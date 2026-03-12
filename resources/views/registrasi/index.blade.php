@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    {{-- ================= SUMMARY ================= --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Pasien Hari Ini</h6>
                    <h2 class="fw-bold" id="totalHariIni">0</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Rawat Jalan</h6>
                    <h2 class="fw-bold text-success" id="totalRajal">0</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">IGD</h6>
                    <h2 class="fw-bold text-danger" id="totalIgd">0</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Rawat Inap</h6>
                    <h2 class="fw-bold text-primary" id="totalRanap">0</h2>
                </div>
            </div>
        </div>

    </div>


    {{-- ================= TABLE ================= --}}
    <div class="card shadow-sm border-0 rounded-3">

        <div class="card-header bg-white d-flex justify-content-between">

            <h5 class="mb-0">Daftar Pasien</h5>

            <div class="d-flex">

                <input type="text"
                    id="keyword"
                    class="form-control form-control-sm me-2"
                    placeholder="Masukkan No.RM / Nama Pasien">

                <button class="btn btn-success btn-sm"
                    onclick="loadData()">
                    Cari
                </button>

            </div>

        </div>


        <div class="reg-table">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>No.RM</th>
                        <th>Tgl Daftar</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>Tipe</th>
                        <th>Poli</th>
                        <th>DPJP</th>
                        <th>No Antrian</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody id="table-data">
                </tbody>

            </table>

        </div>

        <div class="card-footer bg-white">

            <ul class="pagination justify-content-end" id="pagination"></ul>

        </div>

    </div>


</div>


{{-- ================= MODAL BATAL ================= --}}
<div class="modal fade" id="modalBatal">

    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formBatal">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Batal Registrasi</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="batal_id">

                    <div class="mb-3">
                        <label>Nama Pasien</label>
                        <input type="text"
                            id="batal_nama"
                            class="form-control"
                            readonly>
                    </div>

                    <div class="mb-3">
                        <label>Alasan</label>
                        <textarea id="alasan"
                            class="form-control"
                            required></textarea>
                    </div>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button class="btn btn-danger">
                        Simpan
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
<div class="modal fade" id="modalSEP" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">

<div class="modal-header bg-primary text-white">
<h5 class="modal-title">Pembuatan SEP</h5>
<button class="btn-close btn-close-white"
data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

@include('bpjs.form-sep')

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
@endsection


