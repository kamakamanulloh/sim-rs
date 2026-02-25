@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    {{-- ===================== SUMMARY CARDS ===================== --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h6 class="text-muted">Pasien Hari Ini</h6>
                    <h2 class="fw-bold">{{ $totalHariIni }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h6 class="text-muted">Rawat Jalan</h6>
                    <h2 class="fw-bold text-success">{{ $totalRajal }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h6 class="text-muted">Gawat Darurat</h6>
                    <h2 class="fw-bold text-danger">{{ $totalIgd }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <h6 class="text-muted">Rawat Inap</h6>
                    <h2 class="fw-bold text-primary">{{ $totalRanap }}</h2>
                </div>
            </div>
        </div>

    </div>

    {{-- ===================== TABLE ===================== --}}
    <div class="card shadow-sm border-0 rounded-3">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pasien</h5>

            <form method="GET" class="d-flex">
                <input type="text"
                       name="keyword"
                       value="{{ request('keyword') }}"
                       class="form-control form-control-sm me-2"
                       placeholder="Masukkan No.RM / Nama Pasien">
                <button class="btn btn-success btn-sm">Cari</button>
            </form>
        </div>

        <div class="table-responsive overflow-visible">
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
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    @forelse($data as $row)
                        <tr>
                            <td>{{ $row->pasien->no_rm ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->tanggal_registrasi)->format('d-m-Y') }}</td>
                            <td>{{ $row->pasien->nama ?? '-' }}</td>
                            <td>{{ $row->pasien->jenis_kelamin ?? '-' }}</td>

                            <td>
                                @if($row->tipe_rawat == 'J')
                                    <span class="badge bg-success">Rawat Jalan</span>
                                @elseif($row->tipe_rawat == 'G')
                                    <span class="badge bg-danger">IGD</span>
                                @else
                                    <span class="badge bg-primary">Rawat Inap</span>
                                @endif
                            </td>

                            <td>{{ $row->nama_poli }}</td>
                            <td>{{ $row->nama_dokter }}</td>
                            <td>{{ $row->no_antrian }}</td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    {{ $row->status ?? 'Menunggu' }}
                                </span>
                            </td>
                          <td class="text-center align-middle position-relative">

    <div class="dropdown">
        <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                data-bs-boundary="window"
                data-bs-display="dynamic">
            Aksi
        </button>

        <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <button class="dropdown-item text-danger btn-batal"
                                                data-id="{{ $row->id }}"
                                                data-nama="{{ $row->nama_pasien }}">
                                            ❌ Batal Registrasi
                                        </button>
                                    </li>

                                    {{-- ================= BPJS ================= --}}
                                   @if($row->cara_bayar == 'BPJS')

                                        @if($row->noSep)
                                            <li>
                                                <a class="dropdown-item"
                                                target="_blank"
                                                href="{{ url('/sep/pdf/'.$row->noSep) }}">
                                                📄 Lihat SEP
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item"
                                                href="{{ url('/sep/create/'.$row->no_rawat) }}">
                                                ➕ Buat SEP
                                                </a>
                                            </li>
                                        @endif

                                    @endif

                                    {{-- ================= CETAK ETIKET ================= --}}
                                    <li>
                                        <a class="dropdown-item"
                                        target="_blank"
                                        href="{{ url('/registrasi/cetak-etiket/'.$row->no_rawat) }}">
                                        🖨 Cetak Etiket
                                        </a>
                                    </li>

                                    {{-- ================= HISTORY ================= --}}
                                    <li>
                                        <a class="dropdown-item"
                                        href="{{ url('/registrasi/history/'.$row->id_pasien) }}">
                                        📜 History
                                        </a>
                                    </li>

                                    {{-- ================= MASUK RAWAT INAP ================= --}}
                                    @if(in_array($row->tipe_rawat, ['J','G']))
                                        <li>
                                            <a class="dropdown-item text-success"
                                            href="{{ url('/rawat-inap/masuk/'.$row->no_rawat) }}">
                                            🏥 Masuk Rawat Inap
                                            </a>
                                        </li>
                                    @endif

                                </ul>
                            </div>

                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-3">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white">
            {{ $data->links() }}
        </div>

    </div>

</div>
<!-- Modal Batal Registrasi -->
<div class="modal fade" id="modalBatal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formBatal">
                @csrf

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Batal Registrasi</h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="batal_id" name="id">

                    <div class="mb-3">
                        <label>Nama Pasien</label>
                        <input type="text"
                               id="batal_nama"
                               class="form-control"
                               readonly>
                    </div>

                    <div class="mb-3">
                        <label>Alasan Pembatalan</label>
                        <textarea name="alasan"
                                  class="form-control"
                                  required
                                  rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-danger">
                        Simpan Pembatalan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function(){

    // buka modal
    document.querySelectorAll(".btn-batal").forEach(btn => {
        btn.addEventListener("click", function(){

            document.getElementById("batal_id").value = this.dataset.id;
            document.getElementById("batal_nama").value = this.dataset.nama;

            new bootstrap.Modal(
                document.getElementById("modalBatal")
            ).show();
        });
    });

    // submit pembatalan
    document.getElementById("formBatal")
        .addEventListener("submit", function(e){

        e.preventDefault();

        fetch("/registrasi/batal", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id: document.getElementById("batal_id").value,
                alasan: this.alasan.value
            })
        })
        .then(res => res.json())
        .then(res => {

            if(res.status === "success"){
                location.reload();
            } else {
                alert(res.message);
            }

        });

    });

});
</script>
@endpush
@endsection