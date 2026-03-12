<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegistrasiController;
use App\Http\Controllers\MasterPasienController;
use App\Http\Controllers\BpjsController;
use App\Http\Controllers\EmrController;
use App\Http\Controllers\RawatJalanController;
use App\Http\Controllers\AjaxProcedureController;
use App\Http\Controllers\TindakanController;
use App\Http\Controllers\DiagnosaController;
use App\Http\Controllers\SatusehatEncounterController;


Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::prefix('registrasi')->group(function () {

        Route::get('/', [RegistrasiController::class, 'index'])->name('registrasi.index');
        Route::get('/history/{id}', [RegistrasiController::class, 'history'])
            ->name('registrasi.history');


        Route::get('/data', [RegistrasiController::class, 'data'])->name('registrasi.data');

        Route::get('/summary', [RegistrasiController::class, 'summary']);
        Route::get('/history/{id}', [RegistrasiController::class, 'history']);
        Route::get('/datatable-rajal', [RegistrasiController::class, 'datatableRajal']);
        Route::get('/cetak/{no_rawat}', [RegistrasiController::class, 'cetakEtiket']);
        Route::get('/create/{id}', [RegistrasiController::class, 'create'])->name('registrasi.create');
        Route::post('/store', [RegistrasiController::class, 'store'])->name('registrasi.store');
        Route::post('/batal', [RegistrasiController::class, 'batal']);


        Route::get('/api/dokter-igd', [RegistrasiController::class, 'dokterIgd']);
        Route::post('/api/jadwal-dokter', [RegistrasiController::class, 'jadwalDokter']);



        Route::post('/simpan', [RegistrasiController::class, 'store'])
            ->name('registrasi.simpan');
    });

    Route::prefix('master-pasien')->group(function () {
        Route::get('/', [MasterPasienController::class, 'index']);
        Route::get('/datatable', [MasterPasienController::class, 'datatable']);
        Route::post('/store', [MasterPasienController::class, 'store']);
        Route::get('/{id}', [MasterPasienController::class, 'show']);
        Route::post('/nonaktif/{id}', [MasterPasienController::class, 'destroy']);
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/registrasi/cek-ihs', [RegistrasiController::class, 'cekIhs']);
    Route::post('/registrasi/cek-bpjs', [RegistrasiController::class, 'cekBpjs']);
    Route::get(
        '/registrasi/cetak-etiket/{no_rawat}',
        [RegistrasiController::class, 'cetakEtiket']
    );

    Route::prefix('bpjs')->group(function () {

        Route::get('/cek-peserta', [BpjsController::class, 'cekPeserta']);
        Route::get('/history', [BpjsController::class, 'history']);
        Route::get('/dpjp', [BpjsController::class, 'getDpjp']);

        Route::get('/rujukan-list', [BpjsController::class, 'rujukanList']);
        Route::get('/rujukan-detail', [BpjsController::class, 'rujukanDetail']);

        Route::get('/referensi-diagnosa/{kode}', [BpjsController::class, 'referensiDiagnosa']);
         Route::get('/referensi-procedure/{kode}', [BpjsController::class, 'referensiProcedure']);
        Route::get('/referensi-poli/{kode}', [BpjsController::class, 'referensiPoli']);

        Route::get('/dpjp', [BpjsController::class, 'getDpjp']);

        Route::get('/cari-spri', [BpjsController::class, 'cariSpriNoka']);
        Route::get('/surat-kontrol', [BpjsController::class, 'rencanaCariNomorSurat']);

        Route::get('/question', [BpjsController::class, 'getRandomQuestion']);
        Route::get('/kirim-jawaban', [BpjsController::class, 'kirimJawaban']);

        Route::post('/insert-sep', [BpjsController::class, 'sepInsertDua']);
        Route::put('/update-tgl-pulang', [BpjsController::class, 'sepUpdateTglPulangDua']);

        Route::get('/print/{noSep}', [BpjsController::class, 'printSep']);
        Route::get('/referensi-propinsi', [BpjsController::class, 'referensiPropinsi']);
        Route::get('/rujukan-rs-list', [BpjsController::class, 'rujukanRsList']);
        Route::post('/cek-peserta', [BpjsController::class, 'cekPeserta']);
    });
    Route::prefix('rawat-jalan')->group(function () {

        Route::get('/', [RawatJalanController::class, 'index']);
        Route::get('/cari-pasien', [RawatJalanController::class, 'cariPasien']);

        Route::get('/by-registrasi/{id}', [RawatJalanController::class, 'ByRegistrasi']);
        Route::post('/save', [RawatJalanController::class, 'Save']);
        Route::get('/poli', [RawatJalanController::class, 'getPoliRJ']);
        Route::get('/detail/{id}', [RawatJalanController::class, 'detailPasien']);
        Route::get('/history/{rm}', [RawatJalanController::class, 'historyPasien']);
        Route::get('/tindakan/{poli}', [RawatJalanController::class, 'getTindakan']);
        Route::post('/tindakan/simpan', [RawatJalanController::class, 'simpanTindakan']);
        Route::get('/tindakan-pasien/{id}', [RawatJalanController::class, 'tindakanPasien']);
        Route::post('/tindakan/update', [RawatJalanController::class, 'updateTindakan']);
        Route::post('/tindakan-delete', [RawatJalanController::class, 'deleteTindakan']);
        Route::get('/diagnosa/{id}', [RawatJalanController::class, 'getDiagnosa']);
        Route::post('/diagnosa/simpan', [RawatJalanController::class, 'simpanDiagnosa']);
        Route::post('/diagnosa/delete', [RawatJalanController::class, 'hapusDiagnosa']);

        Route::get('/procedure/{id}', [RawatJalanController::class, 'getProcedure']);
        Route::post('/procedure/simpan', [RawatJalanController::class, 'simpanProcedure']);
        Route::post('/procedure/delete', [RawatJalanController::class, 'hapusProcedure']);
        Route::post('/satusehat/encounter/start', [SatusehatEncounterController::class, 'start']);
    });


    Route::prefix('diagnosa')->group(function () {
        Route::get('/select2', [DiagnosaController::class, 'select2']);
        Route::get('/list/{id}', [DiagnosaController::class, 'list']);
        Route::post('/simpan', [DiagnosaController::class, 'simpan']);
        Route::post('/hapus/{id}', [DiagnosaController::class, 'hapus']);
    });


    Route::prefix('procedure')->group(function () {
        Route::get('/icd9/select2', [AjaxProcedureController::class, 'icd9Select2']);
        Route::get('/list/{id}', [AjaxProcedureController::class, 'list']);
        Route::post('/simpan', [AjaxProcedureController::class, 'simpan']);
        Route::post('/hapus/{id}', [AjaxProcedureController::class, 'hapus']);
    });


    Route::prefix('tindakan')->group(function () {
        Route::post('/simpan', [TindakanController::class, 'simpan']);
        Route::get('/list_/{id}', [TindakanController::class, 'list']);
    });

    Route::get('/emr/{id}', [EmrController::class, 'show'])
        ->name('emr.show');

    Route::get('/emr/{id}/pdf', [EmrController::class, 'exportPdf'])
        ->name('emr.pdf');
});


Route::get('/', function () {
    return redirect()->route('login');
});
