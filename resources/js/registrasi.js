import $ from "jquery";
window.$ = window.jQuery = $;

$(function () {

    // ==============================
    // TOGGLE RUJUKAN
    // ==============================

    function toggleRujukan() {
        const val = $("#cara_masuk").val();

        const rujukanList = [
            "RUJUKAN DOKTER",
            "RUJUKAN PUSKESMAS",
            "RUJUKAN RUMAH SAKIT",
            "RUJUKAN ONLINE"
        ];

        // jika nanti mau tampilkan field rujukan tinggal buat div id="rujukan_fields"
        if ($("#rujukan_fields").length) {
            $("#rujukan_fields").toggle(rujukanList.includes(val));
        }
    }

    $("#cara_masuk").on("change", toggleRujukan);



    // ==============================
    // TIPE RAWAT
    // ==============================

 $("#tipe_rawat").on("change", function () {

    let tipe = $(this).val();

    // reset jadwal
    $("#jadwal_dokter").html('<option value="">Pilih Jadwal</option>');

    // reset poli
    $("#poli").val("");

    // tampilkan semua dulu
    $("#poli option").show();

    if (!tipe) return;

    // sembunyikan poli yang tidak sesuai tipe
    $("#poli option").each(function () {

        let poliTipe = $(this).data("tipe");

        if (poliTipe && poliTipe !== tipe) {
            $(this).hide();
        }

    });

    // =============================
    // Jika IGD (G)
    // =============================
    if (tipe === "G") {

        $.get("/registrasi/api/dokter-igd", function (data) {

            let opt = '<option value="">Pilih Dokter IGD</option>';

            data.forEach(function (d) {
                opt += `<option value="${d.id}">${d.nama_lengkap}</option>`;
            });

            $("#jadwal_dokter").html(opt);
        });

    }

});



    // ==============================
    // PILIH POLI → LOAD JADWAL
    // ==============================

    $("#poli").on("change", function () {

        let poli_id = $(this).val();
        let tipe = $("#tipe_rawat").val();
        let tanggal = $("input[name='tgl_registrasi']").val();

        if (tipe !== "J" || !poli_id) return;

        $("#jadwal_dokter").html('<option>Loading...</option>');

        $.ajax({
            url: "/registrasi/api/jadwal-dokter",
            type: "POST",
            data: {
                poli_id: poli_id,
                tanggal: tanggal,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {

                let opt = '<option value="">Pilih Jadwal</option>';

                data.forEach(function (jadwal) {
                    opt += `
                        <option value="${jadwal.id}">
                            ${jadwal.hari} 
                            ${jadwal.jam_mulai} - ${jadwal.jam_selesai}
                            | ${jadwal.nama_dokter}
                        </option>
                    `;
                });

                $("#jadwal_dokter").html(opt);
            },
            error: function () {
                $("#jadwal_dokter").html('<option value="">Gagal memuat jadwal</option>');
            }
        });

    });
    $("#btnCekIhs").on("click", function(){

    let nik       = $("#no_identitas").val();
    let pasienId  = $("#pasien_id").val();

    if(!nik){
        alert("NIK wajib diisi");
        return;
    }

    $("#ihsResult").html("Mencari IHS Number...");

    $.post("/registrasi/cek-ihs", {
        _token: $('meta[name="csrf-token"]').attr('content'),
        no_identitas: nik,
        pasien_id: pasienId
    }, function(res){

        if(res.success){

            $("#ihsResult").html(`
                <div style="color:green;font-weight:600;">
                    IHS Number: ${res.ihs_number}
                    <br>
                    <small>${res.message}</small>
                </div>
            `);

        } else {

            $("#ihsResult").html(`
                <div style="color:red;">
                    ${res.message}
                </div>
            `);

        }

    });

});
$("#cara_bayar").on("change", function(){

    if($(this).val() !== "BPJS") return;

    let nik = $("#no_identitas").val();
    let kartu = $("input[name='no_kartu']").val();

    let nomor = kartu ? kartu : nik;
    let jenis = kartu ? "nokartu" : "nik";

    if(!nomor){
        alert("Isi NIK atau No Kartu dulu");
        return;
    }

    $("#ihsResult").html("Mengecek data BPJS...");

    $.post("/registrasi/cek-bpjs", {
        _token: $('meta[name="csrf-token"]').attr('content'),
        nomor: nomor,
        jenis: jenis
    }, function(res){

        if(res.success){

            let p = res.data;

            $("#ihsResult").html(`
                <div style="background:#e6fffa;padding:10px;border-radius:6px;">
                    <b>${p.nama}</b><br>
                    Status: ${p.statusPeserta.keterangan}<br>
                    Kelas: ${p.hakKelas.keterangan}<br>
                    No Kartu: ${p.noKartu}
                </div>
            `);

        } else {

            $("#ihsResult").html(`
                <div style="color:red;">${res.message}</div>
            `);

        }

    });

});
 let existingIhs = "{{ $pasien->ihs_number ?? '' }}";

    if(existingIhs){
        $("#ihsResult").html(`
            <div style="background:#e6fffa;padding:10px;border-radius:6px;color:#065f46;">
                <b>IHS Number:</b> ${existingIhs}
                <br><small>Data sudah tersimpan</small>
            </div>
        `);
    }
   $("#form-reg").on("submit", function(e){

    e.preventDefault();
     window.showLoading();

    let formData = $(this).serialize();

    $.post("/registrasi/simpan", formData, function(res){

        if(res.status === "success"){
              window.hideLoading();

            window.open(
                "/registrasi/cetak-etiket/" + res.no_rawat,
                "_blank"
            );

          if(res.cara_bayar === "BPJS"){
              window.hideLoading();

                Swal.fire({
                    icon: "success",
                    title: "Registrasi Berhasil",
                    text: "Silakan buat SEP"
                });

                $("#panel-sep").slideDown();

                $('html, body').animate({
                    scrollTop: $("#panel-sep").offset().top - 80
                }, 800);

                $("#noKartu").val(res.no_kartu);
                $("#nama_peserta").val(res.nama);
                $("#nik").val(res.nik);
                $("#noMr").val(res.no_rm);
            } else {

                // Non BPJS → redirect setelah 1 detik
                setTimeout(function(){
                    window.location.href = "/registrasi";
                }, 1000);

            }

        } else {

            Swal.fire("Error", res.message, "error");

        }

    });

});
$("#cekNoPeserta").on('click', function(){

    let noPeserta = $("#nomor_peserta").val();
    let tgl_sep = $("#tgl_sep").val();

    $.get("/bpjs/cek-peserta", {
        no_peserta: noPeserta,
        tgl_sep: tgl_sep
    }, function(response){

        if(response.metaData.code != 200){
            Swal.fire("Error", response.metaData.message, "error");
            return;
        }

        let data = response.response.peserta;

        $("#noKartu").val(data.noKartu);
        $("#nama_peserta").val(data.nama);
        $("#nik").val(data.nik);
        $("#noMr").val(data.mr.noMR);
        $("#noTelp").val(data.mr.noTelepon);
        $("#diagAwal").val("");
        $("#asalRujukan").val("2");

    });
});
$(document).on('click', '#search_diagnosa', function(){

    let kode = $("#kode_diagnosa").val();

    $.get("/bpjs/referensi-diagnosa/" + kode, function(response){

        if(response.metaData.code != 200){
            $("#show_data_diagnosa").html("<tr><td colspan='4'>Data tidak ditemukan</td></tr>");
            return;
        }

        let data = response.response.diagnosa;
        let html = '';

        data.forEach((d,i)=>{
            html += `
                <tr>
                    <td>${i+1}</td>
                    <td>${d.kode}</td>
                    <td>${d.nama}</td>
                    <td>
                        <button class="btn btn-sm btn-info pilih_diagnosa" data-kode="${d.kode}">Pilih</button>
                    </td>
                </tr>
            `;
        });

        $("#show_data_diagnosa").html(html);

    });
});
$(document).on('click', '#search_poli', function(){

    let kode = $("#kode_poli").val();

    $.get("/bpjs/referensi-poli/" + kode, function(response){

        if(response.metaData.code != 200){
            return;
        }

        let data = response.response.poli;
        let html = '';

        data.forEach((d,i)=>{
            html += `
                <tr>
                    <td>${i+1}</td>
                    <td>${d.kode}</td>
                    <td>${d.nama}</td>
                    <td>
                        <button class="btn btn-sm btn-info pilih_poli" data-kode="${d.kode}">Pilih</button>
                    </td>
                </tr>
            `;
        });

        $("#show_data_poli").html(html);
    });
});
});
