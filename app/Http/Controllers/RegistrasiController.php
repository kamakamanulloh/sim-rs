<?php
namespace App\Http\Controllers;

use App\Models\Registrasi;
use App\Models\Pasien;
use App\Models\Poli;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Penjamin;
use App\Models\Suku;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;


class RegistrasiController extends Controller
{

public function index(Request $request)
{
   $query = Registrasi::with('pasien')
    ->leftJoin('jadwal_dokter', 'jadwal_dokter.id', '=', 'registrasi.jadwal_dokter')
    ->leftJoin('pegawai', 'pegawai.id', '=', 'jadwal_dokter.id_pegawai')
    ->leftJoin('master_poli', 'master_poli.poli_id', '=', 'registrasi.poli')
    ->leftJoin('pasien', 'pasien.id', '=', 'registrasi.id_pasien')
 ->leftJoin('sep', function ($join) {
    $join->on(
        DB::raw('sep.no_rawat COLLATE utf8mb4_general_ci'),
        '=',
        DB::raw('registrasi.no_rawat COLLATE utf8mb4_general_ci')
    )
    ->whereNull('sep.deleted_at');
})
    ->select(
        'registrasi.*',
        'pasien.no_rm',
        'pasien.nama as nama_pasien',
        'pasien.jenis_kelamin',
        'pegawai.nama_lengkap as nama_dokter',
        'master_poli.nama_poli',
        'sep.noSep'
    )
    ->when($request->keyword, function ($q) use ($request) {
        $q->where(function ($sub) use ($request) {
            $sub->where('pasien.nama', 'like', '%'.$request->keyword.'%')
                ->orWhere('pasien.no_rm', 'like', '%'.$request->keyword.'%');
        });
    });

    $data = $query
        ->orderByDesc('registrasi.tanggal_registrasi')
        ->paginate(10);

     // summary
    $totalHariIni = Registrasi::whereDate('tanggal_registrasi', now())->count();
    $totalRajal   = Registrasi::where('tipe_rawat','J')->count();
    $totalIgd     = Registrasi::where('tipe_rawat','G')->count();
    $totalRanap   = Registrasi::where('tipe_rawat','I')->count();

    return view('registrasi.index', compact(
        'data',
        'totalHariIni',
        'totalRajal',
        'totalIgd',
        'totalRanap'
    ));
}
public function batal(Request $request)
{
    $request->validate([
        'id' => 'required',
        'alasan' => 'required'
    ]);

    Registrasi::where('id', $request->id)
        ->update([
            'status' => 'Batal',
            'keterangan_pulang' => $request->alasan,
            'cara_pulang' => 'Batal'
        ]);

    return response()->json([
        'status' => 'success'
    ]);
}
public function create($id)
{
    $pasien = Pasien::findOrFail($id);

    return view('registrasi.form',[
        'pasien' => $pasien,
        'pekerjaanList' => Pekerjaan::orderBy('nama')->get(),
        'pendidikanList' => Pendidikan::orderBy('nama')->get(),
        'penjaminList' => Penjamin::orderBy('nama')->get(),
        'sukuList' => Suku::orderBy('nama')->get(),
        'list_poli' => Poli::all()
    ]);
}
public function dokterIgd()
{
    $data = \DB::table('pegawai')
        ->where('id_jabatan', 6)
        ->where('id_unit', 6)
        ->get();

    return response()->json($data);
}
public function jadwalDokter(\Illuminate\Http\Request $request)
{
    date_default_timezone_set('Asia/Jakarta');

    $poli_id = $request->poli_id;

    $tanggal = \Carbon\Carbon::createFromFormat('d-m-Y', $request->tanggal)
        ->format('Y-m-d');

    $hari = \Carbon\Carbon::parse($tanggal)->format('l');

    $hari_map = [
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    ];

    $hari_indo = $hari_map[$hari];

    $jam_now = date('H:i');
    

   $jadwal = \DB::table('jadwal_dokter')
    ->join('pegawai', 'pegawai.id', '=', 'jadwal_dokter.id_pegawai')
    ->select(
        'jadwal_dokter.*',
        'pegawai.nama_lengkap as nama_dokter'
    )
    ->where('jadwal_dokter.id_poli', $poli_id)
    ->where('jadwal_dokter.hari', $hari_indo)
    ->where('jadwal_dokter.status', 'Praktek')
    ->get();
    return response()->json($jadwal);
}


public function store(Request $request)
{
    DB::beginTransaction();

    try {

        $pasien = Pasien::findOrFail($request->id);

        $tgl_registrasi = Carbon::createFromFormat('d-m-Y', $request->tgl_registrasi)
            ->format('Y-m-d');

        // CEK pasien belum pulang
        $cek = Registrasi::where('id_pasien', $request->id)
            ->where('cara_pulang', '-')
            ->where('pasien_lunas', 'N')
            ->first();

        if ($cek) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pasien masih terdaftar dan belum pulang.'
            ], 422);
        }

        // HITUNG ANTRIAN
        if ($request->tipe_rawat === 'J') {
            $jumlahAntrian = Registrasi::where('poli', $request->poli)
                ->where('jadwal_dokter', $request->jadwal_dokter)
                ->where('tanggal_registrasi', $tgl_registrasi)
                ->count();
        } else {
            $jumlahAntrian = Registrasi::where('tipe_rawat', $request->tipe_rawat)
                ->where('tanggal_registrasi', $tgl_registrasi)
                ->count();
        }

        $no_antrian = str_pad($jumlahAntrian + 1, 3, '0', STR_PAD_LEFT);

        $kodePoli = Poli::where('poli_id', $request->poli)
            ->value('kode_bpjs') ?? 'XX';

        $no_rawat = now()->format('dmY') . $kodePoli . $no_antrian;

        Registrasi::create([
            'id_pasien' => $request->id,
            'no_rawat' => $no_rawat,
            'tanggal_registrasi' => $tgl_registrasi,
            'poli' => $request->poli,
            'tipe_rawat' => $request->tipe_rawat,
            'cara_bayar' => $request->cara_bayar,
            'cara_masuk' => $request->cara_masuk,
            'jenis_kunjungan' => $request->jenis_kunjungan,
            'no_antrian' => $no_antrian,
            'cara_pulang' => '-',
            'jadwal_dokter' => $request->jadwal_dokter
        ]);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'no_rawat' => $no_rawat
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}

public function history($id_pasien)
{    $data = Registrasi::select(
            'tanggal_registrasi',
            'tanggal_pulang',
            'poli',
            'cara_pulang'
        )
        ->where('id_pasien', $id_pasien)
        ->orderBy('tanggal_registrasi', 'DESC')
        ->get();

    return response()->json($data);
}

public function datatableRajal(Request $request)
{
    $query = Registrasi::with(['pasien'])
        ->where('tipe_rawat','J');

    if($request->no_rm){
        $query->whereHas('pasien',function($q) use ($request){
            $q->where('no_rm','like','%'.$request->no_rm.'%');
        });
    }

    $total = $query->count();

    $data = $query
        ->offset($request->start)
        ->limit($request->length)
        ->get();

    return response()->json([
        'draw' => intval($request->draw),
        'recordsTotal' => $total,
        'recordsFiltered' => $total,
        'data' => $data
    ]);
}
public function cetakEtiket($no_rawat)
{
    $registrasi = Registrasi::with('pasien')
        ->where('registrasi.no_rawat',$no_rawat)
         ->leftJoin('jadwal_dokter', 'jadwal_dokter.id', '=', 'registrasi.jadwal_dokter')
    ->leftJoin('pegawai', 'pegawai.id', '=', 'jadwal_dokter.id_pegawai')
    ->leftJoin('master_poli', 'master_poli.poli_id', '=', 'registrasi.poli')
    ->leftJoin('pasien', 'pasien.id', '=', 'registrasi.id_pasien')
 ->leftJoin('sep', function ($join) {
    $join->on(
        DB::raw('sep.no_rawat COLLATE utf8mb4_general_ci'),
        '=',
        DB::raw('registrasi.no_rawat COLLATE utf8mb4_general_ci')
    )
    ->whereNull('sep.deleted_at');
})
    ->select(
        'registrasi.*',
        'pasien.no_rm',
        'pasien.nama as nama_pasien',
        'pasien.jenis_kelamin',
        'pegawai.nama_lengkap as nama_dokter',
        'master_poli.nama_poli',
        'sep.noSep'
    )
        ->firstOrFail();

        
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($no_rawat)
        ->size(150)
        ->margin(5)
        ->build();

    $qr = base64_encode($result->getString());

    return view('registrasi.etiket',compact('registrasi','qr'));
}
public function cekIhs(Request $request)
{
    $request->validate([
        'no_identitas' => 'required',
        'pasien_id'    => 'required'
    ]);

    $nik = trim($request->no_identitas);

    $result = satu_patient_by_nik($nik);

    if ($result['success']) {

        $ihs = $result['ihs_number'];

       
        Pasien::where('id', $request->pasien_id)
              ->update(['ihs_number' => $ihs]);

        return response()->json([
            'success'    => true,
            'ihs_number' => $ihs,
            'message'    => 'IHS berhasil ditemukan & disimpan'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => $result['message'] ?? 'Tidak ditemukan'
    ]);
}
public function cekBpjs(Request $request)
{
    $tglSep = now()->format('Y-m-d');

    if ($request->jenis == 'nik') {
        $endpoint = "Peserta/nik/{$request->nomor}/tglSEP/{$tglSep}";
    } else {
        $endpoint = "Peserta/nokartu/{$request->nomor}/tglSEP/{$tglSep}";
    }

    $result = vclaim_get($endpoint);

    if (($result['metaData']['code'] ?? null) == 200) {
        return response()->json([
            'success' => true,
            'data' => $result['response']['peserta']
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => $result['metaData']['message'] ?? 'Gagal'
    ]);
}
}