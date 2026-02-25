<div class="bpjs-container mt-5">

    <!-- HEADER -->
    <div class="bpjs-header-box">
        <h4>Pembuatan SEP Rawat Jalan</h4>
        <small>Lengkapi data untuk pembuatan SEP BPJS</small>
    </div>

 <div class="bpjs-section">

    <div class="bpjs-form-row">

        <div class="bpjs-col-4">
            <label>Masukkan No Kartu BPJS</label>
        <input type="text"
       id="no_kartu_txt"
       class="bpjs-control"
       placeholder="Masukkan nomor kartu BPJS">

        </div>

        <div class="bpjs-col-3">
            <label>Jenis Faskes</label>
            <select id="jenis_faskes" class="bpjs-control">
                <option selected disabled>Pilih</option>
                <option value="1">Faskes 1</option>
                <option value="2">Faskes 2 / RS</option>
            </select>
        </div>

        <div class="bpjs-col-3 bpjs-btn-group">
            
            <button type="button"
                    id="btn_data_rujukan"
                    class="bpjs-btn-primary">
                Data Rujukan
            </button>

            <button id="btn_history_sep" class="bpjs-btn-outline">
                History SEP
            </button>
        </div>

    </div>

</div>


<div class="bpjs-section">

    <div class="bpjs-form-row">

        <div class="bpjs-col-4">
            <label>Nomor Peserta BPJS</label>
            <input type="text"
                   class="bpjs-control"
                   placeholder="Masukkan nomor kartu BPJS">
        </div>

        <div class="bpjs-col-3">
            <label>Tanggal SEP</label>
            <input type="date"
                   class="bpjs-control"
                   value="{{ now()->format('Y-m-d') }}">
        </div>

        <div class="bpjs-col-2 bpjs-btn-group">
            <button class="bpjs-btn-success">
                Cek Peserta
            </button>
        </div>

    </div>

</div>
<div class="bpjs-tabs">
    <button class="bpjs-tab active" data-tab="tab1">1. Data Peserta</button>
    <button class="bpjs-tab" data-tab="tab2">2. Tujuan Kunjungan</button>
    <button class="bpjs-tab" data-tab="tab3">3. Lainnya</button>
        <button class="bpjs-tab" data-tab="tab4">4. COB</button>
    <button class="bpjs-tab" data-tab="tab5">5. KLL</button>
    <button class="bpjs-tab" data-tab="tab6">6. SKDP</button>
</div>
  <form id="form-create-sep">

        <div class="bpjs-tab-content">

            <!-- TAB 1 -->
            <div class="bpjs-tab-pane active" id="tab1">
                 <div class="bpjs-grid">

            <!-- Nama Peserta -->
            <div class="bpjs-col-12">
                <label>Nama Peserta <span class="req">*</span></label>
                <input type="text" id="nama_peserta"
                       class="bpjs-control" readonly>
            </div>

            <!-- Kunjungan -->
            <div class="bpjs-col-4">
                <label>Kunjungan Ke <span class="req">*</span></label>
                <select name="kunjunganKe"
                        id="kunjunganKe"
                        class="bpjs-control">
                    <option value="1">Kunjungan Ke-1 / Pertama</option>
                    <option value="2">Kunjungan Ke-2 atau Lebih</option>
                </select>
            </div>

            <!-- No Kartu -->
            <div class="bpjs-col-4">
                <label>No. Kartu BPJS <span class="req">*</span></label>
                <input type="text"
                       name="noKartu"
                       id="noKartu"
                       class="bpjs-control"
                       readonly>
            </div>

            <!-- NIK -->
            <div class="bpjs-col-4">
                <label>NIK <span class="req">*</span></label>
                <input type="text"
                       name="nik"
                       id="nik"
                       class="bpjs-control"
                       readonly>
            </div>

            <!-- Tanggal SEP -->
            <div class="bpjs-col-4">
                <label>Tanggal SEP <span class="req">*</span></label>
                <input type="date"
                       name="tglSep"
                       id="tglSep"
                       class="bpjs-control"
                       value="{{ now()->format('Y-m-d') }}">
            </div>

            <!-- Jenis Pelayanan -->
            <div class="bpjs-col-4">
                <label>Jenis Pelayanan <span class="req">*</span></label>
                <select name="jnsPelayanan"
                        id="jnsPelayanan"
                        class="bpjs-control" readonly>
                    <option value="2" selected>Rawat Jalan</option>
                </select>
            </div>

            <!-- No RM -->
            <div class="bpjs-col-4">
                <label>No. Rekam Medis <span class="req">*</span></label>
                <input type="text"
                       name="noMr"
                       id="noMr"
                       class="bpjs-control"
                       readonly>
            </div>

            <!-- Asal Rujukan -->
            <div class="bpjs-col-3">
                <label>Asal Rujukan <span class="req">*</span></label>
                <select name="asalRujukan"
                        id="asalRujukan"
                        class="bpjs-control"
                        readonly>
                    <option value="1">Faskes 1</option>
                    <option value="2">Faskes 2 / RS</option>
                </select>
            </div>

            <!-- No Rujukan -->
            <div class="bpjs-col-4">
                <label>No. Rujukan <span class="req">*</span></label>
                <input type="text"
                       name="noRujukan"
                       id="noRujukan"
                       class="bpjs-control"
                       readonly>
            </div>

            <!-- Kode Faskes -->
            <div class="bpjs-col-3">
                <label>Kode Faskes Rujukan <span class="req">*</span></label>
                <input type="text"
                       name="ppkRujukan"
                       id="ppkRujukan"
                       class="bpjs-control"
                       readonly>
            </div>

            <!-- Nama Faskes -->
            <div class="bpjs-col-3">
                <label>Faskes Perujuk <span class="req">*</span></label>
                <input type="text"
                       name="ppkRujukan_txt"
                       id="ppkRujukan_txt"
                       class="bpjs-control"
                       readonly>
            </div>

            <!-- Catatan -->
            <div class="bpjs-col-12">
                <label>Catatan</label>
              
                       <textarea    name="catatan"
                       id="catatan"
                       class="bpjs-control"></textarea>
            </div>

            <!-- Telepon -->
            <div class="bpjs-col-6">
                <label>No. Telepon <span class="req">*</span></label>
                <input type="text"
                       name="noTelp"
                       id="noTelp"
                       class="bpjs-control">
                       
            </div>

            <!-- User -->
            <div class="bpjs-col-6">
                <label>User <span class="req">*</span></label>
                <input type="text"
                       name="user"
                       id="user"
                       class="bpjs-control"
                       readonly>
            </div>

        </div>

            </div>

            <!-- TAB 2 -->
            <div class="bpjs-tab-pane" id="tab2">
                <div class="bpjs-grid">

        <!-- Tujuan Kunjungan -->
        <div class="bpjs-col-3">
            <label>Tujuan Kunjungan <span class="req">*</span></label>
            <select name="tujuanKunj"
                    id="tujuanKunj"
                    class="bpjs-control">
                <option value="">Pilih</option>
                <option value="0" selected>Normal</option>
                <option value="1">Prosedur</option>
                <option value="2">Konsul Dokter</option>
            </select>
        </div>

        <!-- Flag Procedure -->
        <div class="bpjs-col-3">
            <label>Flag Procedure</label>
            <select name="flagProcedure"
                    id="flagProcedure"
                    class="bpjs-control">
                <option value="">Pilih</option>
                <option value="0">Prosedur Tidak Berkelanjutan</option>
                <option value="1">Prosedur & Terapi Berkelanjutan</option>
            </select>
            <small class="bpjs-help">
                Kosongkan jika Tujuan Kunjungan = Normal
            </small>
        </div>

        <!-- Penunjang -->
        <div class="bpjs-col-3">
            <label>Penunjang</label>
            <select name="kdPenunjang"
                    id="kdPenunjang"
                    class="bpjs-control">
                <option value="">Pilih</option>
                <option value="1">Radioterapi</option>
                <option value="2">Kemoterapi</option>
                <option value="3">Rehabilitasi Medik</option>
                <option value="4">Rehabilitasi Psikososial</option>
                <option value="5">Transfusi Darah</option>
                <option value="6">Pelayanan Gigi</option>
                <option value="7">Laboratorium</option>
                <option value="8">USG</option>
                <option value="9">Farmasi</option>
                <option value="10">Lain-lain</option>
                <option value="11">MRI</option>
                <option value="12">Hemodialisa</option>
            </select>
            <small class="bpjs-help">
                Kosongkan jika Tujuan Kunjungan = Normal
            </small>
        </div>

        <!-- Assesment Pelayanan -->
        <div class="bpjs-col-3">
            <label>Assesment Pelayanan</label>
            <select name="assesmentPel"
                    id="assesmentPel"
                    class="bpjs-control">
                <option value="">Pilih</option>
                <option value="1">Poli tidak tersedia sebelumnya</option>
                <option value="2">Jam poli telah berakhir</option>
                <option value="3">Dokter tidak praktek sebelumnya</option>
                <option value="4">Atas instruksi RS</option>
                <option value="5">Tujuan Kontrol</option>
            </select>
            <small class="bpjs-help">
                Wajib jika Tujuan = Normal / Konsul
            </small>
        </div>

    </div>
            </div>

            <!-- TAB 3 -->
           <!-- TAB 3 -->
<div class="bpjs-tab-pane" id="tab3">
    <div class="bpjs-grid">

        <!-- ===================== -->
        <!-- ROW 1 -->
        <!-- ===================== -->

        <!-- Diagnosa Awal -->
        <div class="bpjs-col-6">
            <label>Diagnosa Awal <span class="req">*</span></label>

            <select name="diagAwal"
                    id="diagAwal"
                    class="bpjs-control"
                    placeholder="Cari kode / nama diagnosa">
            </select>
        </div>

        <!-- Poli Tujuan -->
        <div class="bpjs-col-3">
            <label>Poli Tujuan <span class="req">*</span></label>

            <select name="tujuan"
                    id="tujuan"
                    class="bpjs-control"
                    placeholder="Cari poli">
            </select>

            <small class="bpjs-help">
                Tidak bisa diubah pada kunjungan pertama
            </small>
        </div>

        <!-- Poli Eksekutif -->
        <div class="bpjs-col-3">
            <label>Poli Eksekutif</label>

            <select name="eksekutif"
                    id="eksekutif"
                    class="bpjs-control">
                <option value="0" selected>Tidak</option>
                <option value="1">Ya</option>
            </select>
        </div>

        <!-- ===================== -->
        <!-- ROW 2 -->
        <!-- ===================== -->

        <!-- DPJP -->
        <div class="bpjs-col-12">
            <label>DPJP Pelayanan <span class="req">*</span></label>

            <select name="dpjpLayan"
                    id="dpjpLayan"
                    class="bpjs-control">
                <option value="">Pilih Dokter</option>
            </select>
        </div>

    </div>
</div>
            <div class="bpjs-tab-pane" id="tab4">
                <div class="bpjs-grid">

    <!-- COB -->
    <div class="bpjs-col-6">
        <label>
            COB (Coordination of Benefit)
        </label>

        <select name="cob"
                id="cob"
                class="bpjs-control">
            <option value="">Pilih</option>
            <option value="0" selected>Tidak</option>
            <option value="1">Ya</option>
        </select>

        <small class="bpjs-help">
            Pilih "Ya" jika pasien memiliki asuransi tambahan
        </small>
    </div>
<div class="bpjs-col-12 d-none" id="cob_extra">
    <label>Nama Asuransi Tambahan</label>
    <input type="text"
           name="asuransiTambahan"
           class="bpjs-control">
</div>
    <!-- Katarak -->
    <div class="bpjs-col-6">
        <label>
            Katarak <span class="req">*</span>
        </label>

        <select name="katarak"
                id="katarak"
                class="bpjs-control">
            <option value="">Pilih</option>
            <option value="0" selected>Tidak</option>
            <option value="1">Ya</option>
        </select>

        <small class="bpjs-help">
            Wajib diisi sesuai kondisi diagnosa pasien
        </small>
    </div>

</div>
            </div>
           <div class="bpjs-tab-pane" id="tab5">

    <div class="bpjs-grid">

        <!-- KLL -->
        <div class="bpjs-col-6">
            <label>
                Kecelakaan Lalu Lintas <span class="req">*</span>
            </label>

            <select name="lakaLantas"
                    id="lakaLantas"
                    class="bpjs-control">
                <option value="">Pilih</option>
                <option value="0" selected>
                    Bukan KLL [BKKL]
                </option>
                <option value="1">
                    KLL & Bukan Kecelakaan Kerja [BKK]
                </option>
                <option value="2">
                    KLL & KK
                </option>
                <option value="3">
                    Kecelakaan Kerja [KK]
                </option>
            </select>
        </div>

        <!-- No LP -->
        <div class="bpjs-col-6 bpjs-kll-field d-none">
            <label>No. Laporan Polisi</label>
            <input type="text"
                   name="noLP"
                   id="noLP"
                   class="bpjs-control"
                   placeholder="Masukkan No. Laporan Polisi">
        </div>

        <!-- Tanggal Kejadian -->
        <div class="bpjs-col-6 bpjs-kll-field d-none">
            <label>Tanggal Kejadian</label>
            <input type="date"
                   name="tglKejadian"
                   id="tglKejadian"
                   class="bpjs-control">

            <small class="bpjs-help">
                Tanggal kejadian kecelakaan
            </small>
        </div>

        <!-- Keterangan -->
        <div class="bpjs-col-6 bpjs-kll-field d-none">
            <label>Keterangan</label>
            <input type="text"
                   name="keterangan"
                   id="keterangan"
                   class="bpjs-control"
                   placeholder="Masukkan keterangan">
        </div>

        <!-- Suplesi -->
        <div class="bpjs-col-6 bpjs-kll-field d-none">
            <label>Suplesi</label>
            <select name="suplesi"
                    id="suplesi"
                    class="bpjs-control">
                <option value="">Pilih</option>
                <option value="0">Tidak</option>
                <option value="1">Ya</option>
            </select>
        </div>

        <!-- No SEP Suplesi -->
        <div class="bpjs-col-6 d-none" id="suplesi_field">
            <label>No. SEP Suplesi</label>
            <input type="text"
                   name="noSepSuplesi"
                   id="noSepSuplesi"
                   class="bpjs-control"
                   placeholder="Masukkan No. SEP Suplesi">

            <small class="bpjs-help">
                Wajib jika Suplesi = Ya
            </small>
        </div>

        <!-- Lokasi -->
        <div class="bpjs-col-4 bpjs-kll-field d-none">
            <label>Provinsi</label>
            <select name="kdPropinsi"
                    id="kdPropinsi"
                    class="bpjs-control">
                <option value="">Pilih</option>
            </select>
        </div>

        <div class="bpjs-col-4 bpjs-kll-field d-none">
            <label>Kabupaten</label>
            <select name="kdKabupaten"
                    id="kdKabupaten"
                    class="bpjs-control">
                <option value="">Pilih</option>
            </select>
        </div>

        <div class="bpjs-col-4 bpjs-kll-field d-none">
            <label>Kecamatan</label>
            <select name="kdKecamatan"
                    id="kdKecamatan"
                    class="bpjs-control">
                <option value="">Pilih</option>
            </select>
        </div>

    </div>

    <!-- ACTION BUTTONS -->
    <div class="bpjs-action-bar">

        <button id="getQuestion"
                type="button"
                class="bpjs-btn-success">
            Buka Pertanyaan
        </button>

        <button id="save_sep"
                type="button"
                class="bpjs-btn-primary">
            Save SEP
        </button>

    </div>

</div>
<div class="bpjs-tab-pane" id="tab6">

    <div class="bpjs-grid">

        <!-- Pakai SKDP -->
        <div class="bpjs-col-6">
            <label>Gunakan SKDP / Surat Kontrol</label>

            <select name="isSKDP"
                    id="isSKDP"
                    class="bpjs-control">
                <option value="0" selected>Tidak</option>
                <option value="1">Ya</option>
            </select>

            <small class="bpjs-help">
                Pilih "Ya" jika pasien memiliki surat kontrol
            </small>
        </div>

        <!-- No Surat Kontrol -->
        <div class="bpjs-col-6 bpjs-skdp-field d-none">
            <label>No. Surat Kontrol</label>

            <div class="bpjs-input-group">
                <input type="text"
                       name="noSurat"
                       id="noSurat"
                       class="bpjs-control"
                       placeholder="Masukkan No. Surat Kontrol">

                <button type="button"
                        id="search_skdp"
                        class="bpjs-btn-outline-sm">
                    Cari
                </button>
            </div>
        </div>

        <!-- Tanggal Rencana Kontrol -->
        <div class="bpjs-col-6 bpjs-skdp-field d-none">
            <label>Tanggal Rencana Kontrol</label>

            <input type="date"
                   name="tglRencanaKontrol"
                   id="tglRencanaKontrol"
                   class="bpjs-control">
        </div>

        <!-- DPJP Kontrol -->
        <div class="bpjs-col-6 bpjs-skdp-field d-none">
            <label>DPJP Kontrol</label>

            <div class="bpjs-input-group">
                <input type="text"
                       name="kodeDPJP"
                       id="kodeDPJP"
                       class="bpjs-control"
                       placeholder="Pilih DPJP"
                       readonly>

                <button type="button"
                        id="search_dpjp"
                        class="bpjs-btn-outline-sm">
                    Cari
                </button>
            </div>
        </div>

        <!-- Poli Tujuan Kontrol -->
        <div class="bpjs-col-6 bpjs-skdp-field d-none">
            <label>Poli Tujuan Kontrol</label>

            <input type="text"
                   name="tujuanKontrol"
                   id="tujuanKontrol"
                   class="bpjs-control"
                   readonly>
        </div>

    </div>

</div>
        </div>

       <div class="bpjs-footer-actions">

    <button id="getQuestion"
            type="button"
            class="bpjs-btn-success">
        Buka Pertanyaan
    </button>

    <button id="save_sep"
            type="button"
            class="bpjs-btn-primary">
        Simpan SEP
    </button>

</div>

    </form>

</div>
<div class="modal fade" id="modalRujukanRS">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Daftar Rujukan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>No</th>
                <th>No Kunjungan</th>
                <th>Nama</th>
                <th>Diagnosa</th>
                <th>Tgl</th>
                 <th>Faskes Perujuk</th> 
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="rujukan_rs_list"></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="modalHistorySEP">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">History SEP</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>No</th>
                <th>No SEP</th>
                <th>Tgl SEP</th>
                <th>Tgl Pulang</th>
                <th>Jenis</th>
                <th>PPK Pelayanan</th>
                <th>Diagnosa</th>
                <th>No Rujukan</th>
                <th>Kelas</th>
              </tr>
            </thead>
            <tbody id="history_sep_list"></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>