@extends('layouts.app')

@section('content')

@vite([
'resources/css/registrasi.css',
'resources/js/registrasi.js'
])

<div class="reg-container">
    <div class="reg-card">

        <h2 class="reg-title">Form Registrasi Pasien</h2>

        <form id="form-reg">
            @csrf
<input type="hidden" id="pasien_id" value="{{ $pasien->id }}">
            <div class="reg-grid">

                {{-- ================= LEFT COLUMN ================= --}}
                <div class="reg-box">

                    <h5>Biodata Pasien</h5>

                    <input type="hidden" name="id" value="{{ $pasien->id }}">

                    {{-- No RM & Nama --}}
                    <div class="reg-row">
                        <div>
                            <label>No. RM</label>
                            <input type="text" name="no_rm"
                                   value="{{ $pasien->no_rm }}"
                                   class="reg-input">
                        </div>
                        <div>
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama"
                                   value="{{ $pasien->nama }}"
                                   class="reg-input">
                        </div>
                    </div>

                    {{-- NIK + Button --}}
                    <div class="reg-row">
                        <div>
                            <label>No. KTP</label>
                            <input type="text"
                                   name="no_identitas"
                                   id="no_identitas"
                                   value="{{ $pasien->nik }}"
                                   class="reg-input">
                        </div>

                        <div>
                            <label>&nbsp;</label>
                            <button type="button"
                                    id="btnCekIhs"
                                    class="reg-btn-primary"
                                    style="width:100%;height:42px;">
                                Cek IHS Number
                            </button>
                        </div>
                    </div>

                   <div id="ihsResult" style="margin-top:10px;">
                        @if($pasien->ihs_number)
                            <div style="background:#e6fffa;padding:10px;border-radius:6px;color:#065f46;">
                                <b>IHS Number:</b> {{ $pasien->ihs_number }}
                                <br>
                                <small>Data sudah tersimpan di database</small>
                            </div>
                        @endif
                    </div>

                    {{-- TTL --}}
                    <div class="reg-row">
                        <div>
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir"
                                   value="{{ $pasien->tempat_lahir }}"
                                   class="reg-input">
                        </div>
                        <div>
                            <label>Tanggal Lahir</label>
                            <input type="text"
                                   name="tgl_lahir"
                                   value="{{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d-m-Y') }}"
                                   class="reg-input">
                        </div>
                    </div>

                    {{-- Umur --}}
                    <div class="reg-row">
                        <div>
                            <label>Umur</label>
                            <input type="text" name="umur"
                                   value="{{ $pasien->umur }}"
                                   class="reg-input">
                        </div>
                        <div></div>
                    </div>

                    {{-- JK & Ibu --}}
                    <div class="reg-row">
                        <div>
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="reg-input">
                                <option value="">Pilih</option>
                                <option value="Laki-laki" {{ $pasien->jenis_kelamin=='Laki-laki'?'selected':'' }}>Laki-laki</option>
                                <option value="Perempuan" {{ $pasien->jenis_kelamin=='Perempuan'?'selected':'' }}>Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label>Nama Ibu</label>
                            <input type="text" name="nama_ibu"
                                   value="{{ $pasien->nama_ibu }}"
                                   class="reg-input">
                        </div>
                    </div>

                    {{-- Gol Darah & Nikah --}}
                    <div class="reg-row">
                        <div>
                            <label>Golongan Darah</label>
                            <select name="gol_darah" class="reg-input">
                                <option value="">Pilih</option>
                                <option value="A" {{ $pasien->gol_darah=='A'?'selected':'' }}>A</option>
                                <option value="B" {{ $pasien->gol_darah=='B'?'selected':'' }}>B</option>
                                <option value="AB" {{ $pasien->gol_darah=='AB'?'selected':'' }}>AB</option>
                                <option value="O" {{ $pasien->gol_darah=='O'?'selected':'' }}>O</option>
                            </select>
                        </div>
                        <div>
                            <label>Status Nikah</label>
                            <select name="status_nikah" class="reg-input">
                                <option value="">Pilih</option>
                                <option value="Kawin" {{ $pasien->status_nikah=='Kawin'?'selected':'' }}>Kawin</option>
                                <option value="Belum Kawin" {{ $pasien->status_nikah=='Belum Kawin'?'selected':'' }}>Belum Kawin</option>
                            </select>
                        </div>
                    </div>

                    {{-- Agama & Pekerjaan --}}
                    <div class="reg-row">
                        <div>
                            <label>Agama</label>
                            <select name="agama" class="reg-input">
                                <option value="">Pilih</option>
                                <option value="Islam" {{ $pasien->agama=='Islam'?'selected':'' }}>Islam</option>
                                <option value="Kristen" {{ $pasien->agama=='Kristen'?'selected':'' }}>Kristen</option>
                                <option value="Hindu" {{ $pasien->agama=='Hindu'?'selected':'' }}>Hindu</option>
                                <option value="Budha" {{ $pasien->agama=='Budha'?'selected':'' }}>Budha</option>
                            </select>
                        </div>
                        <div>
                            <label>Pekerjaan</label>
                            <select name="pekerjaan" class="reg-input">
                                <option value="">Pilih</option>
                                @foreach($pekerjaanList as $item)
                                    <option value="{{ $item->id }}" {{ $pasien->pekerjaan==$item->id?'selected':'' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Pendidikan & PND --}}
                    <div class="reg-row">
                        <div>
                            <label>Pendidikan</label>
                            <select name="pendidikan" class="reg-input">
                                <option value="">Pilih</option>
                                @foreach($pendidikanList as $item)
                                    <option value="{{ $item->id }}" {{ $pasien->pendidikan==$item->id?'selected':'' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>PND</label>
                            <input type="text" name="pnd"
                                   value="{{ $pasien->pnd }}"
                                   class="reg-input">
                        </div>
                    </div>

                    {{-- Penjamin & Kartu --}}
                    <div class="reg-row">
                        <div>
                            <label>Penjamin</label>
                            <select name="penjamin" class="reg-input">
                                <option value="">Pilih</option>
                                @foreach($penjaminList as $item)
                                    <option value="{{ $item->id }}" {{ $pasien->penjamin==$item->id?'selected':'' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>No. Kartu</label>
                            <input type="text" name="no_kartu"
                                   value="{{ $pasien->no_kartu }}"
                                   class="reg-input">
                        </div>
                    </div>

                    {{-- HP & Suku --}}
                    <div class="reg-row">
                        <div>
                            <label>No HP</label>
                            <input type="text" name="no_hp"
                                   value="{{ $pasien->no_hp }}"
                                   class="reg-input">
                        </div>
                        <div>
                            <label>Suku</label>
                            <select name="suku" class="reg-input">
                                <option value="">Pilih</option>
                                @foreach($sukuList as $item)
                                    <option value="{{ $item->id }}" {{ $pasien->suku==$item->id?'selected':'' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat_lengkap"
                              class="reg-input"
                              rows="3">{{ $pasien->alamat_lengkap }}</textarea>

                </div>

                {{-- ================= RIGHT COLUMN ================= --}}
                <div class="reg-box">

                    <h5>Data Registrasi</h5>

                    <div class="reg-row">
                        <div>
                            <label>Tanggal Registrasi</label>
                            <input type="text"
                                   name="tgl_registrasi"
                                   value="{{ now()->format('d-m-Y') }}"
                                   class="reg-input">
                        </div>
                        <div>
                            <label>Tipe Perawatan</label>
                            <select name="tipe_rawat" id="tipe_rawat" class="reg-input">
                                <option value="">Pilih</option>
                                <option value="J">Rawat Jalan</option>
                                <option value="G">Rawat Darurat</option>
                            </select>
                        </div>
                    </div>

                    <div class="reg-row">
                        <div>
                            <label>Pilih Poli</label>
                           <select name="poli" id="poli" class="reg-input">
                                <option value="">Pilih Poli</option>
                                @foreach($list_poli as $p)
                                    <option value="{{ $p->poli_id }}" data-tipe="{{ $p->tipe }}">
                                        {{ $p->nama_poli }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Jadwal Dokter</label>
                            <select name="jadwal_dokter" id="jadwal_dokter" class="reg-input">
                                <option value="">Pilih Jadwal</option>
                            </select>
                        </div>
                    </div>

                    <div class="reg-row">
                        <div>
                            <label>Cara Pembayaran</label>
                            <select name="cara_bayar" class="reg-input">
                                <option value="">Pilih</option>
                                <option value="umum">Umum</option>
                                <option value="BPJS">BPJS</option>
                                <option value="asuransi">Asuransi</option>
                            </select>
                        </div>
                        <div>
                            <label>Cara Masuk</label>
                            <select name="cara_masuk" id="cara_masuk" class="reg-input">
                                <option value="">Pilih</option>
                                <option value="DATANG SENDIRI">Datang Sendiri</option>
                                <option value="RUJUKAN DOKTER">Rujukan Dokter</option>
                                <option value="RUJUKAN PUSKESMAS">Rujukan Puskesmas</option>
                                <option value="RUJUKAN RUMAH SAKIT">Rujukan RS</option>
                                <option value="RUJUKAN ONLINE">Rujukan Online</option>
                            </select>
                        </div>
                    </div>

                    <label>Jenis Kunjungan</label>
                    <select name="jenis_kunjungan" class="reg-input">
                        <option value="">Pilih</option>
                        <option value="baru">Baru</option>
                        <option value="lama">Lama</option>
                    </select>

                </div>

            </div>

            <div class="reg-actions">
                <button type="submit" class="reg-btn-primary">Simpan</button>
                <a href="{{ url('/registrasi') }}" class="reg-btn-danger">Batal</a>
            </div>

        </form>
    </div>
</div>
{{-- PANEL BPJS --}}
<!--  d-none -->
<div id="panel-sep" class="mt-5">
    @include('bpjs.form-sep')
</div>
@endsection
@push('scripts')
@vite(['resources/js/bpjs.js','resources/js/registrasi.js'])
@endpush