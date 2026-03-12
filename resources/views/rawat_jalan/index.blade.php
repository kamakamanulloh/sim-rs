@extends('layouts.app')

@section('content')
<div class="card shadow-sm">

    <div class="card-header bg-primary text-white">
        Pemeriksaan Poli Rawat Jalan
    </div>

    <div class="card-body">

        <div class="row mb-3">

            <div class="col-md-3">
                <input type="text" id="rj_filter_rm" class="form-control" placeholder="No RM">
            </div>

            <div class="col-md-3">
                <input type="text" id="rj_filter_nama" class="form-control" placeholder="Nama Pasien">
            </div>

            <div class="col-md-3">
                <select id="rj_filter_poli" class="form-control">
                    <option value="">Semua Poli</option>
                </select>
            </div>

            <div class="col-md-3">
                <button class="btn btn-primary w-100" id="rj_btn_cari">
                    Cari
                </button>
            </div>

        </div>
        <div id="rj_detail_pasien" class="card shadow-sm mb-3" style="display:none">

            <div class="card-header bg-info text-white">
                Detail Pasien
            </div>

            <div class="card-body">

                <div class="row">

                    <!-- FOTO -->
                    <div class="col-md-2 text-center">

                        <img id="rj_d_foto"
                            src="/assets/img/pasien_default.png"
                            class="img-thumbnail mb-2"
                            style="width:110px;height:130px;object-fit:cover">

                        <br>

                        <button class="btn btn-sm btn-primary btn-history w-100" id="rj_btn_history">
                            Riwayat Kunjungan
                        </button>

                    </div>


                    <!-- DATA PASIEN -->
                    <div class="col-md-10">

                        <div class="row g-3">

                            <!-- ROW 1 -->
                            <div class="col-md-3">
                                <label class="text-muted small">No RM</label>
                                <div class="fw-semibold" id="rj_d_no_rm"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="text-muted small">Nama Pasien</label>
                                <div class="fw-semibold" id="rj_d_nama"></div>
                            </div>

                            <div class="col-md-3">
                                <label class="text-muted small">Umur</label>
                                <div class="fw-semibold" id="rj_d_umur"></div>
                            </div>


                            <!-- ROW 2 -->
                            <div class="col-md-6">
                                <label class="text-muted small">Alamat</label>
                                <div class="fw-semibold" id="rj_d_alamat"></div>
                            </div>

                            <div class="col-md-3">
                                <label class="text-muted small">Status Pembayaran</label>
                                <div class="fw-semibold" id="rj_d_status"></div>
                            </div>

                            <div class="col-md-3">
                                <label class="text-muted small">No SEP</label>
                                <div class="fw-semibold" id="rj_d_sep"></div>
                            </div>


                            <!-- ROW 3 -->
                            <div class="col-md-4">
                                <label class="text-muted small">No Kartu BPJS</label>
                                <div class="fw-semibold" id="rj_d_bpjs"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="text-muted small">Poli</label>
                                <div class="fw-semibold" id="rj_d_poli"></div>
                            </div>

                            <div class="col-md-4">
                                <label class="text-muted small">Dokter</label>
                                <div class="fw-semibold" id="rj_d_dokter"></div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

        <div class="table-responsive">
            <div id="rj_loading" class="text-center py-4" style="display:none;">

                <div class="spinner-border text-primary"></div>

                <div class="mt-2 text-muted">
                    Memuat data...
                </div>

            </div>


            <table class="table table-bordered table-sm">

                <thead class="table-light">

                    <tr>
                        <th>No</th>
                        <th>No RM</th>
                        <th>Nama</th>
                        <th>Poli</th>
                        <th>Dokter</th>
                        <th>Antrian</th>
                        <th>Status SEP</th>
                        <th width="90">Aksi</th>
                    </tr>

                </thead>

                <tbody id="rj_table_pasien"></tbody>

            </table>


        </div>

        <div id="rj_pagination_area"></div>

    </div>
</div>
<div id="rj_tabs_pemeriksaan" style="display:none">

    <ul class="nav nav-tabs" id="rj_tabs_menu">

        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#rj_tab_tindakan">
                Tindakan
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#rj_tab_diagnosa">
                Diagnosa & Procedure
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#rj_tab_lab">
                Laboratorium
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#rj_tab_radiologi">
                Radiologi
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#rj_tab_farmasi">
                Farmasi
            </a>
        </li>

    </ul>

    <div class="tab-content mt-3">

        <div class="tab-pane fade show active" id="rj_tab_tindakan">

            <!-- ISI TINDAKAN -->
            <div class="card mt-3" id="rj_card_tindakan">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <span>Tindakan Pasien</span>

                    <button class="btn btn-primary btn-sm" id="rj_btn_tambah_tindakan">
                        Tambah Tindakan
                    </button>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-sm table-bordered">

                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tindakan</th>
                                    <th>Tarif</th>
                                    <th width="90">Jumlah</th>
                                    <th>Total</th>
                                    <th width="80">Aksi</th>
                                </tr>
                            </thead>

                            <tbody id="rj_table_tindakan"></tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">
                                        Total Biaya
                                    </th>

                                    <th id="rj_total_billing">0</th>

                                    <th></th>
                                </tr>
                            </tfoot>

                        </table>

                    </div>

                </div>

            </div>

        </div>


        <div class="tab-pane fade" id="rj_tab_diagnosa">

            <div class="alert alert-light">
                Modul Diagnosa & Procedure
            </div>
            <div class="row">

                <!-- DIAGNOSA -->
                <div class="col-md-6">

                    <div class="card">

                        <div class="card-header d-flex justify-content-between">

                            <span>Diagnosa</span>

                            <button class="btn btn-primary btn-sm" id="rj_btn_tambah_diag">
                                Tambah Diagnosa
                            </button>

                        </div>

                        <div class="card-body">

                            <table class="table table-sm table-bordered">

                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Diagnosa</th>
                                        <th>Jenis</th>
                                        <th width="70">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="rj_table_diag"></tbody>

                            </table>

                        </div>

                    </div>

                </div>


                <!-- PROCEDURE -->
                <div class="col-md-6">

                    <div class="card">

                        <div class="card-header d-flex justify-content-between">

                            <span>Procedure</span>

                            <button class="btn btn-primary btn-sm" id="rj_btn_tambah_proc">
                                Tambah Procedure
                            </button>

                        </div>

                        <div class="card-body">

                            <table class="table table-sm table-bordered">

                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Procedure</th>
                                        <th width="70">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody id="rj_table_proc"></tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="tab-pane fade" id="rj_tab_lab">

            <div class="alert alert-light">
                Modul Laboratorium
            </div>

        </div>


        <div class="tab-pane fade" id="rj_tab_radiologi">

            <div class="alert alert-light">
                Modul Radiologi
            </div>

        </div>


        <div class="tab-pane fade" id="rj_tab_farmasi">

            <div class="alert alert-light">
                Modul Farmasi
            </div>

        </div>

    </div>

</div>
<div class="modal fade" id="modalRegistrasi">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Riwayat Registrasi Pasien</h5>
                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="modal-registrasi-body">
                Loading...
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="rj_modal_tindakan">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5>Tambah Tindakan</h5>
            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label>Tindakan</label>

                    <select id="rj_select_tindakan" class="form-control"></select>

                </div>

                <div class="mb-3">

                    <label>Harga</label>

                    <input type="text" id="rj_tarif_tindakan" class="form-control" readonly>

                </div>

                <div class="mb-3">

                    <label>Jumlah</label>

                    <input type="number" id="rj_jumlah_tindakan" class="form-control" value="1">

                </div>

                <div class="mb-3">

                    <label>Total Biaya</label>

                    <input type="text" id="rj_total_tindakan" class="form-control" readonly>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <button class="btn btn-success" id="rj_simpan_tindakan">
                    Simpan
                </button>

            </div>

        </div>

    </div>

</div>
<div class="modal fade" id="rj_modal_diag">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5>Tambah Diagnosa</h5>
            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label>Diagnosa</label>

                    <select id="rj_diag_select"></select>

                </div>

                <div class="mb-3">

                    <label>Jenis Diagnosa</label>

                    <select id="rj_diag_jenis" class="form-control">

                        <option value="P">Primary</option>
                        <option value="S">Sekunder</option>

                    </select>

                </div>

                <div class="mb-3">

                    <label>Keterangan</label>

                    <textarea id="rj_diag_ket" class="form-control"></textarea>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <button class="btn btn-success" id="rj_simpan_diag">
                    Simpan
                </button>

            </div>

        </div>

    </div>

</div>
<div class="modal fade" id="rj_modal_proc">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5>Tambah Procedure</h5>
            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label>Procedure</label>

                    <select id="rj_proc_select"></select>

                </div>

                <div class="mb-3">

                    <label>Catatan</label>

                    <textarea id="rj_proc_note" class="form-control"></textarea>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>

                <button class="btn btn-success" id="rj_simpan_proc">
                    Simpan
                </button>

            </div>

        </div>

    </div>

</div>
@endsection