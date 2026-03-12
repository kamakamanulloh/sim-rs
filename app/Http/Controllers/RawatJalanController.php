<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Registrasi;
use App\Models\Poli;

class RawatJalanController extends Controller
{
    public function index()
    {
        return view('rawat_jalan.index', [
            'title' => 'Pemeriksaan Poli Rawat Jalan'
        ]);
    }

    // =========================
    // CARI PASIEN
    // =========================
    public function cariPasien(Request $request)
    {
        $page  = $request->page ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $today = now()->toDateString();

        $query = Registrasi::query()

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
                'pasien.no_bpjs',
                'pegawai.nama_lengkap as nama_dokter',
                'master_poli.nama_poli',
                'sep.noSep'
            )

            // =====================
            // FILTER HARI INI
            // =====================
            ->whereDate('registrasi.tanggal_registrasi', $today)

            // =====================
            // RAWAT JALAN
            // =====================
            ->where('registrasi.tipe_rawat', 'J')
            ->where('registrasi.poli',  $request->id_poli)

            // =====================
            // FILTER POLI
            // =====================
            ->when($request->id_poli, function ($q) use ($request) {
                $q->where('registrasi.poli', $request->id_poli);
            })

            // =====================
            // SEARCH
            // =====================
            ->when($request->keyword, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('pasien.nama', 'like', '%' . $request->keyword . '%')
                        ->orWhere('pasien.no_rm', 'like', '%' . $request->keyword . '%')
                        ->orWhere('registrasi.no_rawat', 'like', '%' . $request->keyword . '%');
                });
            });

        $total = $query->count();

        $data = $query
            ->orderByDesc('registrasi.tanggal_registrasi')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'rj_rows'  => $data,
            'rj_total' => $total,
            'rj_limit' => $limit,
            'rj_page'  => $page
        ]);
    }
    public function getPoliRJ()
    {
        $data = Poli::where('status_poli', 'Aktif')
            ->where('tipe', 'J')
            ->orderBy('nama_poli')
            ->get(['poli_id', 'nama_poli']);

        return response()->json($data);
    }
    public function detailPasien($id)
    {
        $data = DB::table('registrasi')
            ->leftJoin('pasien', 'pasien.id', '=', 'registrasi.id_pasien')
            ->leftJoin('master_poli', 'master_poli.poli_id', '=', 'registrasi.poli')
            ->leftJoin('jadwal_dokter', 'jadwal_dokter.id', '=', 'registrasi.jadwal_dokter')
            ->leftJoin('pegawai', 'pegawai.id', '=', 'jadwal_dokter.id_pegawai')

            ->leftJoin('sep', function ($join) {
                $join->on(
                    DB::raw('sep.no_rawat COLLATE utf8mb4_general_ci'),
                    '=',
                    DB::raw('registrasi.no_rawat COLLATE utf8mb4_general_ci')
                );
            })

            ->select(
                'registrasi.*',
                'pasien.foto',
                'pasien.no_rm',
                'pasien.nama',
                'pasien.tgl_lahir',
                'pasien.alamat_lengkap',
                'pasien.no_bpjs',
                'pasien.id as pasien_id',
                'master_poli.nama_poli',
                'pegawai.nama_lengkap as nama_dokter',

                'sep.noSep'
            )

            ->where('registrasi.id_registrasi', $id)
            ->first();



        if (!$data) {
            return response()->json(['status' => 'error']);
        }
        if ($data->pasien_lunas == 'N') {
            $data->status_pembayaran = 'Belum Pembayaran';
        } else {
            $data->status_pembayaran = 'Sudah Pembayaran';
        }

        // =====================
        // HITUNG UMUR
        // =====================

        $umur = \Carbon\Carbon::parse($data->tgl_lahir)
            ->diff(\Carbon\Carbon::parse($data->tanggal_registrasi));

        $data->umur = $umur->y . ' Th ' . $umur->m . ' B ' . $umur->d . ' Hr';

        return response()->json([
            'status' => 'success',
            'data'   => $data
        ]);
    }
    public function historyPasien($rm)
    {
        $data = DB::table('registrasi')
            ->leftJoin('master_poli', 'master_poli.poli_id', '=', 'registrasi.poli')
            ->where('registrasi.no_rm', $rm)
            ->orderByDesc('registrasi.tanggal_registrasi')
            ->limit(10)
            ->get([
                'registrasi.no_rawat',
                'registrasi.tanggal_registrasi',
                'master_poli.nama_poli'
            ]);

        return response()->json($data);
    }
    public function getTindakan($poli)
    {
        $data = DB::table('tindakan')
            ->where('poli', $poli)
            ->get([
                'id_tindakan',
                'nama_tindakan',
                'tarif'
            ]);

        return response()->json($data);
    }
    public function simpanTindakan(Request $request)
    {

        // ambil data registrasi
        $reg = DB::table('registrasi')
            ->where('id_registrasi', $request->id_registrasi)
            ->first();

        if (!$reg) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registrasi tidak ditemukan'
            ]);
        }



        // generate nomor pembayaran
        $noPembayaran = 'BYR-'  . $reg->no_rawat;

        DB::table('tindakan_pasien')->insert([

            'id_tindakan' => $request->id_tindakan,
            'id_registrasi' => $request->id_registrasi,

            'no_pembayaran' => $noPembayaran,

            'tarif' => $request->tarif,
            'jumlah' => $request->jumlah,

            'nominal' => $request->tarif * $request->jumlah,



            'tgl_input' => now(),

            'created_at' => now(),
            'updated_at' => now()

        ]);

        return response()->json([
            'status' => 'success',
            'no_pembayaran' => $noPembayaran
        ]);
    }
    public function updateTindakan(Request $request)
    {

        DB::table('tindakan_pasien')
            ->where('id', $request->id)
            ->update([

                'id_tindakan' => $request->id_tindakan,
                'tarif' => $request->tarif,
                'jumlah' => $request->jumlah,
                'nominal' => $request->total,

                'updated_at' => now()

            ]);

        return response()->json(['status' => 'success']);
    }
    public function tindakanPasien($id)
    {

        $data = DB::table('tindakan_pasien')
            ->join('tindakan', 'tindakan.id_tindakan', '=', 'tindakan_pasien.id_tindakan')
            ->where('tindakan_pasien.id_registrasi', $id)
            ->get([
                'tindakan.nama_tindakan',
                'tindakan_pasien.tarif',
                'tindakan_pasien.jumlah',
                'tindakan_pasien.nominal'
            ]);

        return response()->json($data);
    }

    public function deleteTindakan(Request $request)
    {

        DB::table('tindakan_pasien')
            ->where('id', $request->id)
            ->delete();

        return response()->json(['status' => 'deleted']);
    }
    public function simpanDiagnosa(Request $r)
    {

        $idReg = $r->id_registrasi;

        // ======================
        // VALIDASI PRIMARY
        // ======================

        if ($r->jenis == 'P') {

            DB::table('pcare_diagnosa')
                ->where('id_registrasi', $idReg)
                ->where('jenis', 'P')
                ->update([
                    'jenis' => 'S'
                ]);
        }

        // ======================
        // HITUNG URUTAN
        // ======================

        $urutan = DB::table('pcare_diagnosa')
            ->where('id_registrasi', $idReg)
            ->max('urutan');

        $urutan = $urutan ? $urutan + 1 : 1;


        // ======================
        // SIMPAN DIAGNOSA
        // ======================

        DB::table('pcare_diagnosa')->insert([

            'id_registrasi' => $idReg,

            'no_rawat' => $r->no_rawat,

            'kdDiag' => $r->kode,
            'nmDiag' => $r->nama,

            'jenis' => $r->jenis,

            'keterangan' => $r->ket,

            'urutan' => $urutan,

            'created_at' => now(),
            'updated_at' => now()

        ]);

        return response()->json(['status' => 'ok']);
    }
    public function updateDiagnosa(Request $r)
    {

        $idReg = $r->id_registrasi;

        if ($r->jenis == 'P') {

            DB::table('pcare_diagnosa')
                ->where('id_registrasi', $idReg)
                ->where('jenis', 'P')
                ->update([
                    'jenis' => 'S'
                ]);
        }

        DB::table('pcare_diagnosa')
            ->where('id', $r->id)
            ->update([

                'kdDiag' => $r->kode,
                'nmDiag' => $r->nama,

                'jenis' => $r->jenis,
                'keterangan' => $r->ket,

                'updated_at' => now()

            ]);

        return response()->json(['status' => 'ok']);
    }
    public function simpanProcedure(Request $r)
    {

        DB::table('pcare_procedure')->insert([

            'id_registrasi' => $r->id_registrasi,

            'no_rawat' => $r->no_rawat,

            'icd9_code' => $r->kode,

            'icd9_display' => $r->nama,

            'note' => $r->note,

            'performed_start' => now(),
            'performed_end' => now(),

            'created_at' => now(),
            'updated_at' => now()

        ]);

        return response()->json(['status' => 'ok']);
    }
    public function getDiagnosa($id)
    {

        $data = DB::table('pcare_diagnosa')
            ->where('id_registrasi', $id)
            ->orderBy('urutan')
            ->get();

        return response()->json($data);
    }
    public function getProcedure($id)
    {

        $data = DB::table('pcare_procedure')
            ->where('id_registrasi', $id)
            ->orderBy('id')
            ->get();

        return response()->json($data);
    }
    public function hapusDiagnosa(Request $r)
    {

        DB::table('pcare_diagnosa')
            ->where('id', $r->id)
            ->delete();

        return response()->json(['status' => 'ok']);
    }
    public function hapusProcedure(Request $r)
    {

        DB::table('pcare_procedure')
            ->where('id', $r->id)
            ->delete();

        return response()->json(['status' => 'ok']);
    }
    // =========================
    // LOAD PCARE PENDAFTARAN
    // =========================
    public function ByRegistrasi($id)
    {
        $row = DB::table('pcare_pendaftaran')
            ->where('id_registrasi', $id)
            ->first();

        if ($row) {
            return response()->json([
                'status' => 'success',
                'data'   => $row
            ]);
        }

        return response()->json([
            'status' => 'empty',
            'data'   => null
        ]);
    }

    // =========================
    // SIMPAN PCARE
    // =========================
    public function Save(Request $request)
    {
        $idReg = $request->id_registrasi;

        if (!$idReg) {
            return response()->json([
                'status' => 'error',
                'message' => 'ID Registrasi wajib'
            ], 422);
        }

        $data = [
            'keluhan'       => $request->keluhan,
            'sistole'       => $request->sistole,
            'diastole'      => $request->diastole,
            'berat_badan'   => $request->berat_badan,
            'tinggi_badan'  => $request->tinggi_badan,
            'resp_rate'     => $request->resp_rate,
            'lingkar_perut' => $request->lingkar_perut,
            'heart_rate'    => $request->heart_rate,
            'rujuk_balik'   => $request->rujuk_balik,
            'updated_at'    => now()
        ];

        $exists = DB::table('pcare_pendaftaran')
            ->where('id_registrasi', $idReg)
            ->first();

        if ($exists) {
            DB::table('pcare_pendaftaran')
                ->where('id', $exists->id)
                ->update($data);
        } else {
            $data['id_registrasi'] = $idReg;
            $data['created_at'] = now();
            DB::table('pcare_pendaftaran')->insert($data);
        }

        return response()->json(['status' => 'success']);
    }
}
