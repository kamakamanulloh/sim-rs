let diagTom;
let poliTom;
let rj_diag_select;
let rj_proc_select;

document.addEventListener("DOMContentLoaded", function () {
    const laka = document.getElementById("lakaLantas");
    if (!laka) return;

    laka.addEventListener("change", function () {
        const fields = document.querySelectorAll(".bpjs-kll-field");

        if (this.value === "0" || this.value === "") {
            fields.forEach((f) => f.classList.add("d-none"));
        } else {
            fields.forEach((f) => f.classList.remove("d-none"));
        }
    });
    const suplesi = document.getElementById("suplesi");

    if (suplesi) {
        suplesi.addEventListener("change", function () {
            const field = document.getElementById("suplesi_field");
            if (!field) return;

            if (this.value === "1") {
                field.classList.remove("d-none");
            } else {
                field.classList.add("d-none");
            }
        });
    }
    const isSKDP = document.getElementById("isSKDP");

    if (isSKDP) {
        isSKDP.addEventListener("change", function () {
            const fields = document.querySelectorAll(".bpjs-skdp-field");

            if (this.value === "1") {
                fields.forEach((f) => f.classList.remove("d-none"));
            } else {
                fields.forEach((f) => f.classList.add("d-none"));
            }
        });
    }
    document.querySelectorAll(".bpjs-tab").forEach((button) => {
        button.addEventListener("click", function () {
            document
                .querySelectorAll(".bpjs-tab")
                .forEach((btn) => btn.classList.remove("active"));

            document
                .querySelectorAll(".bpjs-tab-pane")
                .forEach((pane) => pane.classList.remove("active"));

            this.classList.add("active");

            let tabId = this.getAttribute("data-tab");
            let target = document.getElementById(tabId);
            if (target) target.classList.add("active");
        });
    });
    diagTom = new TomSelect("#diagAwal", {
        valueField: "kode",
        labelField: "nama",
        searchField: ["kode", "nama"],
        load: function (query, callback) {
            if (!query.length) return callback();

            fetch(`/bpjs/referensi-diagnosa/${query}`)
                .then((res) => res.json())
                .then((json) => {
                    if (!json.metaData || json.metaData.code != 200) {
                        callback();
                        return;
                    }
                    callback(json.response.diagnosa);
                })
                .catch(() => callback());
        },
        render: {
            option: (item, escape) =>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`,
            item: (item, escape) =>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`,
        },
    });

    poliTom = new TomSelect("#tujuan", {
        valueField: "kode",
        labelField: "nama",
        searchField: ["kode", "nama"],
        load: function (query, callback) {
            if (!query.length) return callback();

            fetch(`/bpjs/referensi-poli/${query}`)
                .then((res) => res.json())
                .then((json) => {
                    if (!json.metaData || json.metaData.code != 200) {
                        callback();
                        return;
                    }
                    callback(json.response.poli);
                })
                .catch(() => callback());
        },
        render: {
            option: (item, escape) =>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`,
            item: (item, escape) =>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`,
        },
    });
});
function validate(class_name) {
    var fields = document.getElementsByClassName(class_name);
    var invalid = 0;

    for (var i = 0; i < fields.length; i++) {
        if ($(fields[i]).val() == "" || !$(fields[i]).val()) {
            $(fields[i]).addClass("is-invalid");
            invalid++;
        } else {
            $(fields[i]).removeClass("is-invalid");
        }
    }

    return invalid === 0;
}

// ==========================================
// GET 90 DAYS AGO DATE
// ==========================================

function getNinetyDaysAgoDate() {
    var today = new Date();
    var ninetyDaysAgo = new Date(today);
    ninetyDaysAgo.setDate(today.getDate() - 90);

    return ninetyDaysAgo.toISOString().split("T")[0];
}

// ==========================================
// KIRIM JAWABAN
// ==========================================

function kirim_jawaban(param) {
    window.showLoading();
    $("#modalQuestion").modal("show");

    var noKartu = $("#noKartu").val();
    var tgl_pelayanan = $("#tgl_sep").val();
    var jenis_faskes = $("#jenis_faskes").val();
    var ppkRujukan = $("#ppkRujukan").val();
    var tglLahir = $("#tglLahir").val();

    $.ajax({
        type: "GET",
        url:
            "/bpjs/kirimJawaban?noka=" +
            noKartu +
            "&tglPelayanan=" +
            tgl_pelayanan +
            "&jawaban=" +
            param +
            "&ppkRujukan=" +
            ppkRujukan +
            "&jenis_faskes=" +
            jenis_faskes +
            "&tglLahir=" +
            tglLahir,
        success: function (response) {
            window.hideLoading();

            if (response.metaData.code != 200) {
                Swal.fire("Gagal", response.metaData.message, "error");
                return;
            }

            let data = response.response.rujukan;
            let html = "";

            if (!data || data.length === 0) {
                html = `
            <tr>
                <td colspan="6" class="text-center text-muted">
                    Tidak ada data rujukan
                </td>
            </tr>
        `;
            } else {
                data.forEach((item, index) => {
                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.noKunjungan}</td>
                    <td>${item.peserta.nama}</td>
                    <td>${item.diagnosa?.kode ?? ""}</td>
                    <td>${item.tglKunjungan}</td>
                    <td>
                        <button class="btn btn-sm btn-primary pilih_rujukan_rs"
                            data-nokunjungan="${item.noKunjungan}">
                            Pilih
                        </button>
                    </td>
                </tr>
            `;
                });
            }

            $("#rujukan_rs_list").html(html);

            $("#modalRujukanRS").modal("show");
        },
    });
}

// ==========================================
// CARI SURKON
// ==========================================

function cari_surkon(nomor) {
    $.ajax({
        type: "GET",
        url: "/bpjs/rencanaCariNomorSurat?kode=" + nomor,
        success: function (response) {
            var response = $.parseJSON(response);

            if (response.code == "200") {
                var data = response.respon;
                var diagnosa = data.sep.diagnosa;
                var kode_dx = diagnosa.split(" - ");

                $("#diagAwal").attr("readonly", false);
                $("#diagAwal").val(kode_dx[0]);
            }
        },
    });
}

// ==========================================
// INIT DPJP DROPDOWN
// ==========================================

function init_dpjp(jnsLayanan, tglLayanan, poli_bpjs) {
    var dropdown = $("#dpjpLayan");

    dropdown.empty();
    dropdown.append('<option selected="true" value="">Pilih DPJP</option>');
    dropdown.prop("selectedIndex", 0);

    $.ajax({
        url:
            "/bpjs/dpjp?jenisPelayanan=" +
            jnsLayanan +
            "&tglPelayanan=" +
            tglLayanan +
            "&kodeSpesialis=" +
            poli_bpjs,
        type: "get",
        dataType: "json",
        success: function (rspns) {
            var rspn = rspns.respon;

            $.each(rspn.list, function (key, item) {
                dropdown.append(
                    $("<option></option>")
                        .attr("value", item.kode)
                        .text(item.nama),
                );
            });
        },
    });
}

// ==========================================
// GET PROPINSI
// ==========================================

function getPropinsi() {
    $.ajax({
        type: "GET",
        url: "/bpjs/referensi-propinsi",
        dataType: "json",
        success: function (response) {
            console.log(response);

            // Support format CI lama
            if (response.code == "200") {
                var data = response.respon;
            }
            // Support format VClaim asli
            else if (response.metaData && response.metaData.code == 200) {
                var data = response.response;
            } else {
                console.log("Format response tidak dikenali");
                return;
            }

            var html = '<option value="">Choose</option>';

            for (var i = 0; i < data.list.length; i++) {
                html +=
                    '<option value="' +
                    data.list[i].kode +
                    '">' +
                    data.list[i].nama +
                    "</option>";
            }

            $("#kdPropinsi").html(html);
        },
    });
}

// ==========================================
// DOCUMENT READY INIT
// ==========================================
// ==========================================
// CEK NOMOR PESERTA
// ==========================================

$(document).on("click", "#cekNoPeserta", function () {
    let noPeserta = $("#nomor_peserta").val();
    let tgl_sep = $("#tgl_sep").val();

    $.ajax({
        type: "GET",
        url:
            "/bpjs/cekBpjsNomorPeserta?no_peserta=" +
            noPeserta +
            "&tgl_sep=" +
            tgl_sep,
        success: function (response) {
            var obj = $.parseJSON(response);

            if (obj.code != "200") {
                swal({
                    icon: "error",
                    title: "Oops...",
                    text: obj.message,
                });
            } else {
                let data = obj.respon.peserta;

                if (data.statusPeserta.keterangan == "AKTIF") {
                    $(".alert_status")
                        .addClass("alert-success")
                        .removeClass("alert-warning");
                } else {
                    $(".alert_status")
                        .addClass("alert-warning")
                        .removeClass("alert-success");
                }

                $("#klsRawatHak").val(data.hakKelas.kode);
                $("#noKartu").val(data.noKartu);
                $("#nama_peserta").val(data.nama);
                $("#nik").val(data.nik);
                $("#noMr").val(data.mr.noMR);
                $("#no_hp").val(data.mr.noTelepon);
                $("#kelas").val(data.hakKelas.keterangan);
                $("#kd_faskes").val(data.provUmum.kdProvider);
                $("#nm_faskes").val(data.provUmum.nmProvider);
                $("#dinsos").val(data.informasi.dinsos);
                $("#noSKTM").val(data.informasi.noSKTM);
                $("#prolanisPRB").val(data.informasi.prolanisPRB);
                $("#statusPeserta").text(
                    "Status Peserta : " + data.statusPeserta.keterangan,
                );
                $("#tglLahir").val(data.tglLahir);
                $("#noTelp").val(data.mr.noTelepon);
                $("#sex").val(data.sex);
                $("#umur").val(data.umur.umurSekarang);
                $("#jenisPeserta").val(data.jenisPeserta.keterangan);
                $("#asalRujukan").val("2");
                $("#ppkRujukan").val("0440R001");
                $("#ppkRujukan_txt").val("RSUD Lebong");

                // AUTO LOAD HISTORY 90 HARI
                var no_kartu_txt = $("#noKartu").val();
                var search_tanggal_awal = getNinetyDaysAgoDate();
                var search_tanggal_akhir = new Date()
                    .toISOString()
                    .split("T")[0];

                $.ajax({
                    type: "GET",
                    url:
                        "/Bpjs/carihistory?no_kartu_txt=" +
                        no_kartu_txt +
                        "&search_tanggal_awal=" +
                        search_tanggal_awal +
                        "&search_tanggal_akhir=" +
                        search_tanggal_akhir,
                });
            }
        },
    });
});

// ==========================================
// SEARCH SKDP
// ==========================================

$(document).on("click", "#search_skdp", function () {
    if (validate("validate_skdp")) {
        var bulantahun = $("#skdp_bulantahun").val();
        var nokartu = $("#skdp_nokartu").val();
        var formatfilter = $("#skdp_formatfilter").val();
        var tahun = bulantahun.slice(0, 4);
        var bulan = bulantahun.slice(5);

        $.ajax({
            type: "GET",
            url:
                "/bpjs/cariSpriNoka?bulan=" +
                bulan +
                "&tahun=" +
                tahun +
                "&noKartu=" +
                nokartu +
                "&filter=" +
                formatfilter,
            success: function (response) {
                var response = $.parseJSON(response);

                var html = "";

                if (response.code != "200") {
                    html =
                        '<tr><td style="text-align: center;" class="p-2" colspan="4">' +
                        response.message +
                        "</td></tr>";
                } else {
                    var data = response.respon;

                    for (var i = 0; i < data.list.length; i++) {
                        html +=
                            "<tr>" +
                            "<td>" +
                            (i + 1) +
                            "</td>" +
                            "<td>" +
                            data.list[i].noSuratKontrol +
                            "</td>" +
                            "<td>" +
                            data.list[i].kodeDokter +
                            "</td>" +
                            "<td>" +
                            data.list[i].namaDokter +
                            "</td>" +
                            "<td>" +
                            data.list[i].namaJnsKontrol +
                            "</td>" +
                            "<td>" +
                            data.list[i].tglRencanaKontrol +
                            "</td>" +
                            "<td>" +
                            data.list[i].noSepAsalKontrol +
                            "</td>" +
                            '<td><button class="btn btn-sm btn-info pilih_skdp text-center" data-noSuratKontrol="' +
                            data.list[i].noSuratKontrol +
                            '" data-kodeDokter="' +
                            data.list[i].kodeDokter +
                            '" data-namaDokter="' +
                            data.list[i].namaDokter +
                            '" data-poliTujuan="' +
                            data.list[i].poliTujuan +
                            '" data-sepasal="' +
                            data.list[i].noSepAsalKontrol +
                            '">Pilih</button></td>' +
                            "</td>";
                    }
                }

                $("#show_data_skdp").html(html);
            },
        });
    }
});

// ==========================================
// PILIH SKDP
// ==========================================

$(document).on("click", ".pilih_skdp", function () {
    $("#noSurat").val($(this).attr("data-noSuratKontrol"));
    $("#kodeDPJP").val($(this).attr("data-kodeDokter"));
    $("#kodeDPJP_txt").val($(this).attr("data-namaDokter"));
    $("#tujuan").val($(this).attr("data-poliTujuan"));

    if ($("#noRujukan").val() == "") {
        $("#noRujukan").val($(this).attr("data-sepasal"));
    }

    cari_surkon($(this).attr("data-noSuratKontrol"));

    $("#modalSKDP").modal("hide");
});

// ==========================================
// SEARCH DIAGNOSA
// ==========================================

$(document).on("click", "#search_diagnosa", function () {
    var kode_diagnosa = $("#kode_diagnosa").val();

    $.ajax({
        type: "GET",
        url: "/bpjs/getReferensiDiagnosa/" + kode_diagnosa,
        success: function (response) {
            var response = $.parseJSON(response);
            var html = "";

            if (response.code != "200") {
                html =
                    '<tr><td style="text-align: center;" class="p-2" colspan="4">' +
                    response.message +
                    "</td></tr>";
            } else {
                var data = response.respon;

                for (var i = 0; i < data.diagnosa.length; i++) {
                    html +=
                        "<tr>" +
                        "<td>" +
                        (i + 1) +
                        "</td>" +
                        "<td>" +
                        data.diagnosa[i].kode +
                        "</td>" +
                        "<td>" +
                        data.diagnosa[i].nama +
                        "</td>" +
                        '<td><button class="btn btn-sm btn-info pilih_diagnosa text-center" data-kode="' +
                        data.diagnosa[i].kode +
                        '">Pilih</button></td>' +
                        "</td>";
                }
            }

            $("#show_data_diagnosa").html(html);
        },
    });
});

// ==========================================
// PILIH DIAGNOSA
// ==========================================

$(document).on("click", ".pilih_diagnosa", function () {
    $("#diagAwal").val($(this).attr("data-kode"));
    $("#modalDiagnosa").modal("hide");
});

// ==========================================
// SEARCH POLI
// ==========================================

$(document).on("click", "#search_poli", function () {
    var kode_poli = $("#kode_poli").val();

    $.ajax({
        type: "GET",
        url: "/bpjs/getReferensiPoli/" + kode_poli,
        success: function (response) {
            var response = $.parseJSON(response);
            var html = "";

            if (response.code != "200") {
                html =
                    '<tr><td style="text-align: center;" class="p-2" colspan="4">' +
                    response.message +
                    "</td></tr>";
            } else {
                var data = response.respon;

                for (var i = 0; i < data.poli.length; i++) {
                    html +=
                        "<tr>" +
                        "<td>" +
                        (i + 1) +
                        "</td>" +
                        "<td>" +
                        data.poli[i].kode +
                        "</td>" +
                        "<td>" +
                        data.poli[i].nama +
                        "</td>" +
                        '<td><button class="btn btn-sm btn-info pilih_poli text-center" data-kode="' +
                        data.poli[i].kode +
                        '">Pilih</button></td>' +
                        "</td>";
                }
            }

            $("#show_data_poli").html(html);
        },
    });
});

// ==========================================
// PILIH POLI
// ==========================================

$(document).on("click", ".pilih_poli", function () {
    $("#tujuan").val($(this).attr("data-kode"));
    $("#modalPoli").modal("hide");
});
// ==========================================
// SEARCH DPJP
// ==========================================

$(document).on("click", "#search_dpjp", function () {
    var jenis_pelayanan = $("#jenis_pelayanan").val();
    var tgl_pelayanan = $("#tgl_pelayanan").val();
    var kode_spesialis = $("#kode_spesialis").val();

    $.ajax({
        type: "GET",
        url:
            "/bpjs/dpjp?jenisPelayanan=" +
            jenis_pelayanan +
            "&tglPelayanan=" +
            tgl_pelayanan +
            "&kodeSpesialis=" +
            kode_spesialis,
        success: function (response) {
            var response = $.parseJSON(response);
            var html = "";

            if (response.code != "200") {
                html =
                    '<tr><td style="text-align: center;" class="p-2" colspan="4">' +
                    response.message +
                    "</td></tr>";
            } else {
                var data = response.respon;

                for (var i = 0; i < data.list.length; i++) {
                    html +=
                        "<tr>" +
                        "<td>" +
                        (i + 1) +
                        "</td>" +
                        "<td>" +
                        data.list[i].kode +
                        "</td>" +
                        "<td>" +
                        data.list[i].nama +
                        "</td>" +
                        '<td><button class="btn btn-sm btn-info pilih_dpjp text-center" data-kode="' +
                        data.list[i].kode +
                        '" data-kodePoli="' +
                        kode_spesialis +
                        '">Pilih</button></td>' +
                        "</td>";
                }
            }

            $("#show_data_dpjp").html(html);
        },
    });
});

// ==========================================
// PILIH DPJP
// ==========================================

$(document).on("click", ".pilih_dpjp", function () {
    $("#dpjpLayan").val($(this).attr("data-kode"));
    $("#tujuan").val($(this).attr("data-kodePoli"));
    $("#modalDPJP").modal("hide");
});

// ==========================================
// SEARCH RUJUKAN
// ==========================================

$(document).on("click", "#search_rujukan", function () {
    window.showLoading();

    var no_kartu = $("#no_kartu_txt").val();
    var jenis_pelayanan = $("#jenis_faskes").val();

    $.ajax({
        type: "GET",
        url:
            "/bpjs/getListRujukan?no_kartu=" +
            no_kartu +
            "&jenis_pelayanan=" +
            jenis_pelayanan,
        success: function (response) {
            window.hideLoading();

            var response = $.parseJSON(response);
            var html = "";

            if (response.code != "200") {
                html =
                    '<tr><td style="text-align: center;" class="p-2" colspan="4">' +
                    response.message +
                    "</td></tr>";
            } else {
                var data = response.respon;

                for (var i = 0; i < data.rujukan.length; i++) {
                    html +=
                        "<tr>" +
                        "<td>" +
                        (i + 1) +
                        "</td>" +
                        "<td>" +
                        data.rujukan[i].noKunjungan +
                        "</td>" +
                        "<td>" +
                        data.rujukan[i].peserta.nama +
                        "</td>" +
                        "<td>" +
                        data.rujukan[i].tglKunjungan +
                        "</td>" +
                        "<td>" +
                        data.rujukan[i].provPerujuk.nama +
                        "</td>" +
                        "<td>" +
                        data.rujukan[i].poliRujukan.nama +
                        "</td>" +
                        '<td><button class="btn btn-sm btn-info pilih_rujukan text-center" data-rujukan="' +
                        data.rujukan[i].noKunjungan +
                        '" data-faskes="' +
                        jenis_pelayanan +
                        '">Pilih</button></td>' +
                        "</td>";
                }
            }

            $("#show_data_rujukan").html(html);
        },
    });
});

// ==========================================
// PILIH RUJUKAN
// ==========================================

$(document).on("click", ".pilih_rujukan", function () {
    $("#no_rujukan").val($(this).attr("data-rujukan"));
    $("#faskes_perujuk").val($(this).attr("data-faskes"));

    cariNoRujukan($(this).attr("data-rujukan"), $(this).attr("data-faskes"));

    $("#modalRujukan").modal("hide");
});

// ==========================================
// GET RANDOM QUESTION
// ==========================================

$(document).on("click", "#getQuestion", function () {
    window.showLoading();
    $("#modalQuestion").modal("show");

    var noKartu = $("#noKartu").val();
    var tgl_pelayanan = $("#tgl_sep").val();

    $.ajax({
        type: "GET",
        url:
            "/bpjs/getRandomQuestion?noka=" +
            noKartu +
            "&tglPelayanan=" +
            tgl_pelayanan,
        success: function (response) {
            window.hideLoading();

            var response = $.parseJSON(response);
            var html = "";

            if (response.code != "200") {
                swal({
                    icon: "error",
                    title: "Gagal",
                    text: response.message,
                });
            } else {
                var data = response.respon;

                html += "di mana faskes tingkat pertama anda ?";

                for (var i = 0; i < data.faskes.length; i++) {
                    html +=
                        '<button class="btn btn-sm btn-info pilih_jawaban text-center" style="margin:5px" data-kode="' +
                        data.faskes[i].kode +
                        '">' +
                        data.faskes[i].nama +
                        "</button>";
                }
            }

            $("#show_data_question").html(html);
        },
    });
});

// ==========================================
// PILIH JAWABAN
// ==========================================

$(document).on("click", ".pilih_jawaban", function () {
    kirim_jawaban($(this).attr("data-kode"));
});

// ==========================================
// SAVE SEP
// ==========================================

$(document).on("click", "#save_sep", function () {
    window.showLoading();
    $("#save_sep").attr("disabled", true);

    var data = $("#form-create-sep").serialize();

    if (validate("validate")) {
        $.ajax({
            type: "post",
            url: "/Bpjs/sepInsertDua",
            data: data,
            success: function (response) {
                window.hideLoading();

                var response = $.parseJSON(response);

                if (response.code != "200") {
                    swal({
                        icon: "error",
                        title: "Gagal",
                        text: response.message,
                    });
                } else {
                    var data = response.respon.sep;

                    var dpjp =
                        $("#kodeDPJP").val() != ""
                            ? $("#kodeDPJP").val()
                            : $("#dpjpLayan").val();

                    $("#res-noSep").val(data.noSep);

                    swal({
                        icon: "success",
                        title: "Berhasil",
                        text:
                            "Berhasil didaftarkan dengan No. Sep : " +
                            data.noSep,
                    });

                    window.open("/bpjs/printSep/" + data.noSep);
                }

                $("#save_sep").removeAttr("disabled");
            },
        });
    } else {
        window.hideLoading();
        $("#save_sep").removeAttr("disabled");

        swal({
            icon: "error",
            title: "Gagal",
            text: "Mohon isi data yang diperlukan!",
        });
    }
});

// ==========================================
// SEARCH HISTORY
// ==========================================

$(document).on("click", "#search_history", function () {
    var no_kartu_txt = $("#noKartu").val();
    var search_tanggal_awal = $("#search_tanggal_awal").val();
    var search_tanggal_akhir = $("#search_tanggal_akhir").val();

    $.ajax({
        type: "GET",
        url:
            "/Bpjs/carihistory?no_kartu_txt=" +
            no_kartu_txt +
            "&search_tanggal_awal=" +
            search_tanggal_awal +
            "&search_tanggal_akhir=" +
            search_tanggal_akhir,
        success: function (response) {
            var response = $.parseJSON(response);
            var html = "";

            if (response.code != "200") {
                html =
                    '<tr><td style="text-align: center;" class="p-2" colspan="4">' +
                    response.message +
                    "</td></tr>";
            } else {
                var data = response.respon;

                for (var i = 0; i < data.histori.length; i++) {
                    var jnsPelayananBpjs =
                        data.histori[i].jnsPelayanan == "1"
                            ? "Rawat Inap"
                            : "Rawat Jalan";

                    var tglPulang =
                        data.histori[i].jnsPelayanan == "1"
                            ? '<button class="btn btn-sm btn-info" id="update_tgl_pulang" data-tgl="' +
                              data.histori[i].tglSep +
                              '" data-sep="' +
                              data.histori[i].noSep +
                              '">Update Tanggal Pulang</button>'
                            : "";

                    html +=
                        "<tr>" +
                        "<td>" +
                        (i + 1) +
                        "</td>" +
                        "<td>" +
                        data.histori[i].noSep +
                        "</td>" +
                        "<td>" +
                        data.histori[i].noRujukan +
                        "</td>" +
                        "<td>" +
                        data.histori[i].namaPeserta +
                        "</td>" +
                        "<td>" +
                        data.histori[i].tglSep +
                        "</td>" +
                        "<td>" +
                        data.histori[i].ppkPelayanan +
                        "</td>" +
                        "<td>" +
                        jnsPelayananBpjs +
                        "</td>" +
                        "<td>" +
                        data.histori[i].diagnosa +
                        "</td>" +
                        "<td>" +
                        data.histori[i].poli +
                        "</td>" +
                        "<td>" +
                        tglPulang +
                        "</td>";
                }
            }

            $("#show_data_history").html(html);
        },
    });
});

// ==========================================
// UPDATE TGL PULANG
// ==========================================

$(document).on("click", "#update_tgl_pulang", function () {
    $("#noSep").val($(this).data("sep"));
    $("#tglPulang").val($(this).data("tgl"));
    $("#modalTglPulang").modal("show");
});

$(document).on("click", "#update_tgl_pulang_submit", function () {
    var data = $("#formUpdateTglPulang").serialize();

    $.post("/bpjs/sepUpdateTglPulangDua", data, function (response) {
        var response = $.parseJSON(response);

        if (response.code != "200") {
            swal({
                icon: "error",
                title: "Gagal",
                text: response.message,
            });
        } else {
            swal({
                icon: "success",
                title: "Berhasil",
                text: "Update Tanggal Pulang!",
            });

            $(".modal").modal("hide");
        }
    });
});

// ==========================================
// STATUS PULANG
// ==========================================

$(document).on("change", "#statusPulang", function () {
    if (this.value == "4") {
        $(".status4").show();
    } else {
        $(".status4").hide();
    }
});
$(document).ready(function () {
    $("#tujuan").on("change", function () {
        let kodePoli = $(this).val();
        if (!kodePoli) return;

        let today = new Date().toISOString().split("T")[0];

        $.get(
            `/bpjs/dpjp?jenisPelayanan=2&tglPelayanan=${today}&kodeSpesialis=${kodePoli}`,
            function (res) {
                if (!res.metaData || res.metaData.code != 200) {
                    return;
                }

                let dropdown = $("#dpjpLayan");
                dropdown.empty();
                dropdown.append('<option value="">Pilih DPJP</option>');

                res.response.list.forEach(function (item) {
                    dropdown.append(
                        `<option value="${item.kode}">
                    ${item.kode} - ${item.nama}
                 </option>`,
                    );
                });
            },
        );
    });
    // Load Propinsi
    getPropinsi();
});

$(document).on("click", "#getQuestion", function () {
    // logic buka pertanyaan
});

// ==========================================
// DATA RUJUKAN RS LIST
// ==========================================
$(document).on("click", "#btn_data_rujukan", function () {
    let noKartu = $("#no_kartu_txt").val();
    let jenisFaskes = $("#jenis_faskes").val();

    if (!noKartu) {
        Swal.fire("Oops", "Nomor kartu harus diisi", "warning");
        return;
    }

    if (!jenisFaskes) {
        Swal.fire("Oops", "Pilih jenis faskes", "warning");
        return;
    }

    window.showLoading();

    $.ajax({
        type: "GET",
        url:
            "/bpjs/rujukan-rs-list?no_kartu=" +
            noKartu +
            "&jenis=" +
            jenisFaskes,
        dataType: "json",
        success: function (response) {
            window.hideLoading();

            if (!response.metaData || response.metaData.code != 200) {
                Swal.fire(
                    "Gagal",
                    response.metaData?.message ?? "Error",
                    "error",
                );
                return;
            }

            let data = response.response.rujukan || [];
            let html = "";

            if (data.length === 0) {
                html = `
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Tidak ada data rujukan
                    </td>
                </tr>
            `;
            } else {
                data.forEach((item, index) => {
                    html += `
                    <tr data-detail='${JSON.stringify(item)}'>
                        <td>${index + 1}</td>
                        <td>${item.noKunjungan}</td>
                        <td>${item.peserta?.nama ?? ""}</td>
                        <td>${item.diagnosa?.kode ?? ""}</td>
                        <td>
                            ${item.provPerujuk?.kode ?? ""} - 
                            ${item.provPerujuk?.nama ?? ""}
                        </td>
                        <td>${item.tglKunjungan}</td>
                        <td>
                            <button class="btn btn-sm btn-primary pilih_rujukan_rs">
                                Pilih
                            </button>
                        </td>
                    </tr>
                `;
                });
            }

            $("#rujukan_rs_list").html(html);

            new bootstrap.Modal(
                document.getElementById("modalRujukanRS"),
            ).show();
        },
        error: function (xhr) {
            console.log(xhr.responseText);
            window.hideLoading();
            Swal.fire("Error", "Server tidak merespon", "error");
        },
    });
});
// ==========================================
// PILIH RUJUKAN RS
// ==========================================

$(document).on("click", "#tableRujukanRS tbody tr", function () {
    let detail = $(this).data("detail");
    if (!detail) return;

    let html = `
        <div class="mb-2">
            <strong>Nama:</strong><br>
            ${detail.peserta.nama}
        </div>

        <div class="mb-2">
            <strong>No Kartu:</strong><br>
            ${detail.peserta.noKartu}
        </div>

        <div class="mb-2">
            <strong>NIK:</strong><br>
            ${detail.peserta.nik}
        </div>

        <div class="mb-2">
            <strong>Diagnosa:</strong><br>
            ${detail.diagnosa.kode} - ${detail.diagnosa.nama}
        </div>

        <div class="mb-2">
            <strong>Faskes Perujuk:</strong><br>
            ${detail.provPerujuk.nama}
        </div>

        <div class="mb-2">
            <strong>Tanggal Kunjungan:</strong><br>
            ${detail.tglKunjungan}
        </div>
    `;

    $("#rujukan_detail_preview").html(html);
});
$(document).on("click", ".pilih_rujukan_rs", function (e) {
    e.stopPropagation();

    let row = $(this).closest("tr");
    let detail = row.data("detail");

    if (!detail) return;

    // =========================
    // AUTO FILL DATA PESERTA
    // =========================
    $("#nama_peserta").val(detail.peserta.nama);
    $("#noKartu").val(detail.peserta.noKartu);
    $("#nik").val(detail.peserta.nik);
    $("#noMr").val(detail.peserta.mr?.noMR ?? "");
    $("#noTelp").val(detail.peserta.mr?.noTelepon ?? "");

    // =========================
    // DATA RUJUKAN
    // =========================
    $("#noRujukan").val(detail.noKunjungan);
    $("#ppkRujukan").val(detail.provPerujuk.kode);
    $("#ppkRujukan_txt").val(detail.provPerujuk.nama);

    // =========================
    // AUTO SET FASKES 1
    // =========================
    $("#asalRujukan").val("1"); // 🔥 langsung faskes 1
    // =========================
    // DIAGNOSA OTOMATIS
    // =========================
    if (diagTom) {
        diagTom.clear();
        diagTom.addOption({
            kode: detail.diagnosa.kode,
            nama: detail.diagnosa.nama,
        });
        diagTom.setValue(detail.diagnosa.kode);
    }

    // =========================
    // POLI OTOMATIS
    // =========================
    if (poliTom) {
        poliTom.clear();
        poliTom.addOption({
            kode: detail.poliRujukan.kode,
            nama: detail.poliRujukan.nama,
        });
        poliTom.setValue(detail.poliRujukan.kode);
    }

    // Load DPJP
    loadDpjp(detail.poliRujukan.kode);

    bootstrap.Modal.getInstance(
        document.getElementById("modalRujukanRS"),
    ).hide();

    Swal.fire(
        "Berhasil",
        "Rujukan dipilih & Faskes otomatis Faskes 1",
        "success",
    );
});

// ==============================
// OPEN MODAL CEK BPJS
// ==============================
$(document).on("click", ".btn-cek-bpjs", function () {
    let jenis = $(this).data("jenis");

    $("#jenis_pencarian").val(jenis);
    $("#no_peserta").val("");
    $("#hasilBpjs").html("");

    $("#modalCekBpjs").modal("show");
});

// ==============================
// PROSES CEK
// ==============================
$("#btnProsesCek").on("click", function () {
    $("#hasilBpjs").html(`
        <div class="text-center">
            <div class="spinner-border text-primary"></div>
        </div>
    `);

    $.post(
        "/bpjs/cek-peserta",
        {
            _token: $('meta[name="csrf-token"]').attr("content"),
            jenis: $("#jenis_pencarian").val(),
            no_peserta: $("#no_peserta").val(),
            tgl_sep: $("#tgl_sep").val(),
        },
        function (res) {
            if (res.metaData.code != 200) {
                $("#hasilBpjs").html(`
                <div class="alert alert-danger">
                    ${res.metaData.message}
                </div>
            `);
                return;
            }

            let p = res.response.peserta;

            let statusColor =
                p.statusPeserta.keterangan == "AKTIF" ? "success" : "danger";

            let html = `
            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h5 class="fw-bold">${p.nama}</h5>
                    <hr>

                    <div class="row mb-2">
                        <div class="col-6 text-muted">No Kartu</div>
                        <div class="col-6 fw-bold">${p.noKartu}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6 text-muted">NIK</div>
                        <div class="col-6">${p.nik}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6 text-muted">Kelas</div>
                        <div class="col-6">
                            <span class="badge bg-primary">
                                ${p.hakKelas.keterangan}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6 text-muted">Jenis Peserta</div>
                        <div class="col-6">${p.jenisPeserta.keterangan}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6 text-muted">Faskes</div>
                        <div class="col-6">${p.provUmum.nmProvider}</div>
                    </div>

                    <div class="mt-3">
                        <span class="badge bg-${statusColor}">
                            ${p.statusPeserta.keterangan}
                        </span>
                    </div>

                </div>
            </div>
        `;

            $("#hasilBpjs").html(html);
        },
    ).fail(function () {
        $("#hasilBpjs").html(`
            <div class="alert alert-danger">
                Gagal terhubung ke server.
            </div>
        `);
    });
});
$(document).on("click", "#btn_history_sep", function () {
    let noKartu = $("#no_kartu_txt").val();

    if (!noKartu) {
        Swal.fire("Oops", "Nomor kartu harus diisi", "warning");
        return;
    }

    let tglMulai = moment().subtract(90, "days").format("YYYY-MM-DD");
    let tglAkhir = moment().format("YYYY-MM-DD");

    window.showLoading();

    $.ajax({
        type: "GET",
        url: "/bpjs/history",
        data: {
            no_kartu: noKartu,
            tgl_mulai: tglMulai,
            tgl_akhir: tglAkhir,
        },
        dataType: "json",
        success: function (res) {
            window.hideLoading();

            if (!res.metaData || res.metaData.code != 200) {
                Swal.fire("Gagal", res.metaData?.message ?? "Error", "error");
                return;
            }

            let data = res.response.histori || [];
            let html = "";

            if (data.length === 0) {
                html = `
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            Tidak ada histori pelayanan
                        </td>
                    </tr>
                `;
            } else {
                data.forEach((item, index) => {
                    let jenis =
                        item.jnsPelayanan == "1" ? "Rawat Inap" : "Rawat Jalan";

                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${item.noSep}</td>
                            <td>${item.tglSep}</td>
                            <td>${item.tglPlgSep ?? "-"}</td>
                            <td>${jenis}</td>
                            <td>${item.ppkPelayanan}</td>
                            <td>${item.diagnosa}</td>
                            <td>${item.noRujukan ?? "-"}</td>
                            <td>${item.kelasRawat ?? "-"}</td>
                        </tr>
                    `;
                });
            }

            $("#history_sep_list").html(html);
            new bootstrap.Modal(
                document.getElementById("modalHistorySEP"),
            ).show();
        },
        error: function () {
            window.hideLoading();
            Swal.fire("Error", "Server tidak merespon", "error");
        },
    });
});
$(document).on("click", "#btn_cari_diagnosa", function () {
    let keyword = prompt("Masukkan kode / nama diagnosa");

    if (!keyword) return;

    $.get("/bpjs/referensi-diagnosa/" + keyword, function (res) {
        if (res.metaData.code != 200) {
            Swal.fire("Error", res.metaData.message, "error");
            return;
        }

        let data = res.response.diagnosa;
        let html = "";

        data.forEach((item, i) => {
            html += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${item.kode}</td>
                    <td>${item.nama}</td>
                    <td>
                        <button class="btn btn-sm btn-primary pilih_diagnosa"
                                data-kode="${item.kode}"
                                data-nama="${item.nama}">
                            Pilih
                        </button>
                    </td>
                </tr>
            `;
        });

        $("#diagnosa_list").html(html);
        new bootstrap.Modal(document.getElementById("modalDiagnosa")).show();
    });
});
$(document).on("click", ".pilih_diagnosa", function () {
    $("#diagAwal").val($(this).data("kode"));
    $("#diagAwal_text").val(
        $(this).data("kode") + " - " + $(this).data("nama"),
    );

    bootstrap.Modal.getInstance(
        document.getElementById("modalDiagnosa"),
    ).hide();
});
$(document).on("click", "#btn_cari_poli", function () {
    let keyword = prompt("Masukkan kode / nama poli");

    if (!keyword) return;

    $.get("/bpjs/referensi-poli/" + keyword, function (res) {
        if (res.metaData.code != 200) {
            Swal.fire("Error", res.metaData.message, "error");
            return;
        }

        let data = res.response.poli;
        let html = "";

        data.forEach((item, i) => {
            html += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${item.kode}</td>
                    <td>${item.nama}</td>
                    <td>
                        <button class="btn btn-sm btn-primary pilih_poli"
                                data-kode="${item.kode}"
                                data-nama="${item.nama}">
                            Pilih
                        </button>
                    </td>
                </tr>
            `;
        });

        $("#poli_list").html(html);
        new bootstrap.Modal(document.getElementById("modalPoli")).show();
    });
});
$(document).on("click", ".pilih_poli", function () {
    let kodePoli = $(this).data("kode");
    let namaPoli = $(this).data("nama");

    $("#tujuan").val(kodePoli);
    $("#tujuan_text").val(kodePoli + " - " + namaPoli);

    bootstrap.Modal.getInstance(document.getElementById("modalPoli")).hide();

    loadDpjp(kodePoli);
});
function loadDpjp(kodePoli) {
    let today = new Date().toISOString().split("T")[0];

    $.get(
        `/bpjs/dpjp?jenisPelayanan=2&tglPelayanan=${today}&kodeSpesialis=${kodePoli}`,
        function (res) {
            if (res.metaData.code != 200) {
                Swal.fire("Error", res.metaData.message, "error");
                return;
            }

            let data = res.response.list;
            let dropdown = $("#dpjpLayan");

            dropdown.empty();
            dropdown.append('<option value="">Pilih Dokter</option>');

            data.forEach((item) => {
                dropdown.append(
                    `<option value="${item.kode}">
                    ${item.kode} - ${item.nama}
                 </option>`,
                );
            });
        },
    );
}
$(document).on("click", "#btnCekPeserta", function () {
    let jenis = $("#jenis_peserta").val();
    let nomor = $("#nomor_peserta").val();
    let tgl = $("#tgl_sep").val();

    if (!nomor) {
        Swal.fire("Oops", "Nomor peserta harus diisi", "warning");
        return;
    }

    window.showLoading("Cek Peserta BPJS...");

    $.ajax({
        url: "bpjs/cek-peserta",
        type: "GET",
        data: {
            jenis: jenis,
            no_peserta: nomor,
            tgl_sep: tgl,
        },
        success: function (res) {
            window.hideLoading();

            if (!res.metaData || res.metaData.code != 200) {
                Swal.fire("Error", res.metaData.message, "error");
                return;
            }

            let p = res.response.peserta;

            // auto isi form SEP
            $("#nama_peserta").val(p.nama);
            $("#noKartu").val(p.noKartu);
            $("#nik").val(p.nik);
            $("#noMr").val(p.mr.noMR);
            $("#noTelp").val(p.mr.noTelepon);

            Swal.fire({
                icon: "success",
                title: "Peserta ditemukan",
                text: p.nama,
            });
        },
        error: function () {
            window.hideLoading();
            Swal.fire("Error", "Server tidak merespon", "error");
        },
    });
});

function rj_load_diag() {
    rj_diag_select = new TomSelect("#rj_diag_select", {
        valueField: "kode",
        labelField: "nama",

        searchField: ["kode", "nama"],

        load: function (query, callback) {
            if (!query.length) return callback();

            fetch(`/bpjs/referensi-diagnosa/${query}`)
                .then((res) => res.json())

                .then((json) => {
                    if (json.metaData.code != 200) {
                        callback();

                        return;
                    }

                    callback(json.response.diagnosa);
                });
        },

        render: {
            option: (item, escape) =>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`,

            item: (item, escape) =>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`,
        },
    });
}
function rj_load_proc() {
    rj_proc_select = new TomSelect("#rj_proc_select", {
        valueField: "kode",
        labelField: "nama",

        searchField: ["kode", "nama"],

        load: function (query, callback) {
            if (!query.length) return callback();

            fetch(`/referensi/procedure/${query}`)
                .then((res) => res.json())

                .then((json) => {
                    callback(json.response.procedure);
                });
        },

        render: {
            option: (item, escape) =>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`,

            item: (item, escape) =>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`,
        },
    });
}
