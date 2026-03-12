<div class="d-flex justify-content-between align-items-center mb-2">

<div class="text-muted">
Total Data: <strong>{{ $data->total() }}</strong>
</div>

<div>
{{ $data->links() }}
</div>

</div>


<table class="table table-hover align-middle">

<thead class="table-light">
<tr>
<th>No RM</th>
<th>Tanggal</th>
<th>Nama</th>
<th>JK</th>
<th>Poli</th>
<th>Dokter</th>
<th>Antrian</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

@forelse($data as $row)

<tr>

<td>{{ $row->no_rm }}</td>

<td>{{ \Carbon\Carbon::parse($row->tanggal_registrasi)->format('d-m-Y') }}</td>

<td>{{ $row->nama_pasien }}</td>

<td>{{ $row->jenis_kelamin }}</td>

<td>{{ $row->nama_poli }}</td>

<td>{{ $row->nama_dokter }}</td>

<td class="fw-bold">{{ $row->no_antrian }}</td>

<td>
@if($row->noSep)
<span class="badge bg-primary">SEP Aktif</span>
@else
<span class="badge bg-secondary">Belum SEP</span>
@endif
</td>

<td>

<div class="dropdown">

<button class="btn btn-sm btn-outline-primary dropdown-toggle"
data-bs-toggle="dropdown"
data-bs-boundary="viewport">

Aksi

</button>

<ul class="dropdown-menu dropdown-menu-end">

<li>
<button class="dropdown-item text-danger btn-batal"
data-id="{{ $row->id_registrasi }}">
❌ Batal Registrasi
</button>
</li>

<li>
<a class="dropdown-item"
target="_blank"
href="/registrasi/cetak-etiket/{{ $row->no_rawat }}">
🖨 Cetak Etiket
</a>
</li>

<li>
<a class="dropdown-item"
href="/registrasi/history/{{ $row->id_pasien }}">
📜 History
</a>
</li>

</ul>

</div>

</td>

</tr>

@empty

<tr>
<td colspan="9" class="text-center text-muted">
Tidak ada data
</td>
</tr>

@endforelse

</tbody>

</table>


<div class="d-flex justify-content-between align-items-center mt-3">

<div>
Menampilkan {{ $data->firstItem() }} -
{{ $data->lastItem() }}
dari {{ $data->total() }} data
</div>

<div>
{{ $data->links() }}
</div>

</div>