let rj_current_page = 1;
let rj_id_reg = null;
let rj_poli_id = null;
let rj_edit_id = null;
let rj_tindakan_select = null;
let rj_no_rawat = null;
$("#rj_loading").fadeIn(150);
$("#rj_loading").fadeOut(150);
function rj_load_poli() {
    $("#rj_filter_poli").html("<option>Loading...</option>");

    $.get("/rawat-jalan/poli", function (rows) {
        let html = '<option value="">Semua Poli</option>';

        rows.forEach(function (poli) {
            html += `<option value="${poli.poli_id}">
                        ${poli.nama_poli}
                     </option>`;
        });

        $("#rj_filter_poli").html(html);
    });
}
function rj_load_pasien() {
    let keyword = $("#rj_filter_rm").val() || $("#rj_filter_nama").val();

    $("#rj_loading").show();
    $("#rj_table_pasien").html("");

    $.get(
        "/rawat-jalan/cari-pasien",
        {
            keyword: keyword,
            id_poli: $("#rj_filter_poli").val(),
            page: rj_current_page,
        },
        function (res) {
            let html = "";
            let nomor = (res.rj_page - 1) * res.rj_limit + 1;

            res.rj_rows.forEach(function (row) {
                let sepStatus = row.noSep
                    ? '<span class="badge bg-success">' + row.noSep + "</span>"
                    : '<span class="badge bg-danger">Belum SEP</span>';

                html += `
            <tr>

            <td>${nomor++}</td>

            <td>${row.no_rm}</td>

            <td>${row.nama_pasien}</td>

            <td>${row.nama_poli ?? ""}</td>

            <td>${row.nama_dokter ?? ""}</td>

            <td>${row.no_antrian}</td>

            <td>${sepStatus}</td>

            <td>
                <button 
                    class="btn btn-success btn-sm rj_btn_pilih"
                    data-rj-id="${row.id_registrasi}"
                    data-poli="${row.poli}"
                    data-no_rawat="${row.no_rawat}"
                >
                Pilih
                </button>
            </td>

            </tr>
            `;
            });

            $("#rj_loading").hide();

            $("#rj_table_pasien").html(html);

            rj_render_pagination(res.rj_total, res.rj_limit, res.rj_page);
        },
    );
}
function rj_render_pagination(total, limit, current) {
    let totalPage = Math.ceil(total / limit);

    let html = '<nav><ul class="pagination">';

    for (let i = 1; i <= totalPage; i++) {
        let active = i === current ? "active" : "";

        html += `
        <li class="page-item ${active}">
            <a class="page-link rj_page_btn" data-rj-page="${i}" href="#">
                ${i}
            </a>
        </li>
        `;
    }

    html += "</ul></nav>";

    $("#rj_pagination_area").html(html);
}
$("#rj_btn_cari").click(function () {
    rj_current_page = 1;

    rj_load_pasien();
    $("#rj_tabs_pemeriksaan").hide();
});
$(document).on("click", ".rj_btn_pilih", function () {
    rj_id_reg = $(this).data("rj-id");
      rj_no_rawat= $(this).data("no_rawat");


    $("#rj_tabs_pemeriksaan").show();

    rj_poli_id = $(this).data("poli");
    $("#rj_card_tindakan").show();
    rj_load_tindakan_pasien();
    $.get("/rawat-jalan/detail/" + rj_id_reg, function (res) {
        let d = res.data;

        $("#rj_detail_pasien").show();

        $("#rj_d_no_rm").text(d.no_rm);
        $("#rj_d_nama").text(d.nama);
        $("#rj_d_umur").text(d.umur);
        $("#rj_d_alamat").text(d.alamat_lengkap);

        $("#rj_d_sep").text(d.noSep ?? "Belum SEP");
        $("#rj_d_bpjs").text(d.no_bpjs ?? "-");

        $("#rj_d_status").text(d.status_pembayaran);

        $("#rj_d_poli").text(d.nama_poli);
        $("#rj_d_dokter").text(d.nama_dokter);

        if (d.foto) {
            $("#rj_d_foto").attr("src", "/uploads/pasien/" + d.foto);
        }
    });
});
function rj_load_tindakan_pasien() {
    if (!rj_id_reg) return;

    $.get("/rawat-jalan/tindakan-pasien/" + rj_id_reg, function (rows) {
        let html = "";
        let totalBilling = 0;
        let no = 1;

        if (rows.length === 0) {
            html = `
            <tr>
            <td colspan="6" class="text-center text-muted">
            Belum ada tindakan
            </td>
            </tr>
            `;
        } else {
            rows.forEach(function (row) {
                totalBilling += parseInt(row.nominal);

                html += `
                <tr>

                <td>${no++}</td>

                <td>${row.nama_tindakan}</td>

                <td>${rj_rupiah(row.tarif)}</td>

                <td>

                <input type="number"
                class="form-control form-control-sm rj_edit_jumlah"
                data-id="${row.id}"
                value="${row.jumlah}">

                </td>

                <td>${row.nominal}</td>

                <td>
                
<button 
class="btn btn-warning rj_edit_tindakan"
data-id="${row.id}"
data-tindakan="${row.id_tindakan}"
data-nama="${row.nama_tindakan}"
data-tarif="${row.tarif}"
data-jumlah="${row.jumlah}">
Edit
</button>


                <button class="btn btn-danger btn-sm rj_hapus_tindakan"
                data-id="${row.id}">
                Hapus
                </button>

                </td>

                </tr>
                `;
            });
        }

        $("#rj_table_tindakan").html(html);

        $("#rj_total_billing").text(totalBilling);
    });
}
$(document).on("click", ".rj_edit_tindakan", function () {
    let id = $(this).data("id");
    let tindakan = $(this).data("tindakan");
    let nama = $(this).data("nama");
    let tarif = $(this).data("tarif");
    let jumlah = $(this).data("jumlah");

    rj_edit_id = id;

    const modal = new bootstrap.Modal(
        document.getElementById("rj_modal_tindakan"),
    );

    modal.show();

    // pastikan tomselect dibuat dulu
    rj_load_tindakan(rj_poli_id);

    setTimeout(function () {
        if (!rj_tindakan_select) return;

        rj_tindakan_select.addOption({
            id_tindakan: tindakan,
            nama_tindakan: nama,
            tarif: tarif,
        });

        rj_tindakan_select.setValue(tindakan);

        $("#rj_tarif_tindakan").val(tarif);
        $("#rj_jumlah_tindakan").val(jumlah);

        rj_hitung_total();
    }, 300);
});
$(document).on("click", ".rj_hapus_tindakan", function () {
    if (!confirm("Hapus tindakan ini ?")) return;

    let id = $(this).data("id");

    $.post(
        "/rawat-jalan/tindakan-delete",
        {
            id: id,

            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        function () {
            rj_load_tindakan_pasien();
        },
    );
});
function rj_rupiah(n) {
    return new Intl.NumberFormat("id-ID").format(n);
}
$(document).on("change", ".rj_edit_jumlah", function () {
    let id = $(this).data("id");
    let jumlah = $(this).val();

    $.post(
        "/rawat-jalan/tindakan-update",
        {
            id: id,
            jumlah: jumlah,

            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        function () {
            rj_load_tindakan_pasien();
        },
    );
});

function rj_hitung_total() {
    let harga = parseInt($("#rj_tarif_tindakan").val()) || 0;
    let jumlah = parseInt($("#rj_jumlah_tindakan").val()) || 0;

    let total = harga * jumlah;

    $("#rj_total_tindakan").val(total);
}

$("#rj_jumlah_tindakan").on("keyup change", function () {
    rj_hitung_total();
});
$("#rj_btn_tambah_tindakan").click(function () {
    let poli = rj_poli_id;

    rj_load_tindakan(poli);

    new bootstrap.Modal(document.getElementById("rj_modal_tindakan")).show();
});

function rj_load_tindakan(poli) {
    if (rj_tindakan_select) {
        rj_tindakan_select.destroy();
    }

    rj_tindakan_select = new TomSelect("#rj_select_tindakan", {
        valueField: "id_tindakan",
        labelField: "nama_tindakan",
        searchField: "nama_tindakan",

        preload: true,

        load: function (query, callback) {
            fetch("/rawat-jalan/tindakan/" + poli)
                .then((response) => response.json())
                .then((json) => {
                    callback(json);
                })
                .catch(() => callback());
        },

        render: {
            option: function (item, escape) {
                return `
                <div>
                    <strong>${escape(item.nama_tindakan)}</strong>
                    <div class="text-muted small">
                        Tarif : ${item.tarif}
                    </div>
                </div>
                `;
            },
        },

        onChange: function (value) {
            let item = this.options[value];

            if (item) {
                $("#rj_tarif_tindakan").val(item.tarif);

                rj_hitung_total();
            }
        },
    });
}

$("#rj_simpan_tindakan").click(function () {
    Swal.fire({
        title: "Menyimpan...",
        text: "Sedang menyimpan tindakan pasien",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
    let url = "/rawat-jalan/tindakan/simpan";

    if (rj_edit_id) {
        url = "/rawat-jalan/tindakan/update";
    }

    $.post(url, {
        id_tindakan: rj_tindakan_select.getValue(),
        id_registrasi: rj_id_reg,

        tarif: $("#rj_tarif_tindakan").val(),
        jumlah: $("#rj_jumlah_tindakan").val(),
        total: $("#rj_total_tindakan").val(),

        _token: $('meta[name="csrf-token"]').attr("content"),
    })
        .done(function (res) {
            Swal.close();

            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Tindakan berhasil ditambahkan",
                timer: 1500,
                showConfirmButton: false,
            });
            rj_edit_id = null;

            // tutup modal
            const modal = bootstrap.Modal.getInstance(
                document.getElementById("rj_modal_tindakan"),
            );
            modal.hide();

            // reload tindakan pasien
            rj_load_tindakan_pasien();
        })
        .fail(function () {
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "Terjadi kesalahan saat menyimpan tindakan",
            });
        });
});
$(document).ready(function () {
    rj_load_poli();

    rj_load_pasien();
});
$(document).on("click", ".rj_page_btn", function (e) {
    e.preventDefault();

    rj_current_page = $(this).data("rj-page");

    rj_load_pasien();
});
function rj_load_diagnosa() {
    if (!rj_id_reg) return;

    $.get("/rawat-jalan/diagnosa/" + rj_id_reg, function (rows) {
        let html = "";

        if (rows.length == 0) {
            html = `
<tr>
<td colspan="4" class="text-center text-muted">
Belum ada diagnosa
</td>
</tr>
`;
        } else {
            rows.forEach(function (row, i) {
                let jenis =
                    row.jenis == "P"
                        ? '<span class="badge bg-primary">Primary</span>'
                        : '<span class="badge bg-secondary">Sekunder</span>';

                html += `
<tr>

<td>${i + 1}</td>

<td>${row.kdDiag}</td>

<td>${row.nmDiag}</td>

<td>${jenis}</td>

<td>

<button class="btn btn-danger btn-sm rj_hapus_diag"
data-id="${row.id}">
Hapus
</button>

</td>

</tr>
`;
            });
        }

        $("#rj_table_diag").html(html);
    });
}
function rj_load_procedure() {
    if (!rj_id_reg) return;

    $.get("/rawat-jalan/procedure/" + rj_id_reg, function (rows) {
        let html = "";

        if (rows.length == 0) {
            html = `
<tr>
<td colspan="3" class="text-center text-muted">
Belum ada procedure
</td>
</tr>
`;
        } else {
            rows.forEach(function (row) {
                html += `
<tr>

<td>${row.icd9_code}</td>

<td>${row.icd9_display}</td>

<td>

<button class="btn btn-danger btn-sm rj_hapus_proc"
data-id="${row.id}">
Hapus
</button>

</td>

</tr>
`;
            });
        }

        $("#rj_table_proc").html(html);
    });
}
$('a[href="#rj_tab_diagnosa"]').on("shown.bs.tab", function () {
    rj_load_diagnosa();
    rj_load_procedure();
});
$(document).on("click", ".rj_hapus_diag", function () {
    let id = $(this).data("id");

    $.post(
        "/rawat-jalan/diagnosa/delete",
        {
            id: id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        function () {
            rj_load_diagnosa();
        },
    );
});
$(document).on("click", ".rj_hapus_proc", function () {
    let id = $(this).data("id");

    $.post(
        "/rawat-jalan/procedure/delete",
        {
            id: id,
            _token: $('meta[name="csrf-token"]').attr("content"),
        },
        function () {
            rj_load_procedure();
        },
    );
});
let rj_diag_select = null;

function rj_load_diag(){

    if(rj_diag_select){
        rj_diag_select.destroy();
    }

    rj_diag_select = new TomSelect("#rj_diag_select",{

        valueField: "kode",
        labelField: "nama",
        searchField: ["kode","nama"],

        load:function(query,callback){

            if(!query.length) return callback();

            fetch(`/bpjs/referensi-diagnosa/${query}`)
            .then(res=>res.json())
            .then(json=>{

                if(!json.metaData || json.metaData.code != 200){
                    callback();
                    return;
                }

                callback(json.response.diagnosa);

            })
            .catch(()=>callback());

        },

        render:{
            option:(item,escape)=>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`,

            item:(item,escape)=>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`
        }

    });

}
let rj_proc_select = null;

function rj_load_proc(){

    if(rj_proc_select){
        rj_proc_select.destroy();
    }

    rj_proc_select = new TomSelect("#rj_proc_select",{

        valueField: "kode",
        labelField: "nama",
        searchField: ["kode","nama"],

        load:function(query,callback){

            if(!query.length) return callback();

            fetch(`/bpjs/referensi-procedure/${query}`)
            .then(res=>res.json())
            .then(json=>{

                if(!json.metaData || json.metaData.code != 200){
                    callback();
                    return;
                }

                callback(json.response.procedure);

            })
            .catch(()=>callback());

        },

        render:{
            option:(item,escape)=>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`,

            item:(item,escape)=>
                `<div>${escape(item.kode)} - ${escape(item.nama)}</div>`
        }

    });

}
$(document).on("click","#rj_btn_tambah_diag",function(){

    if(!rj_id_reg){
        alert("Pilih pasien terlebih dahulu");
        return;
    }

    const modal = new bootstrap.Modal(
        document.getElementById("rj_modal_diag")
    );

    modal.show();

    // reset form
    $("#rj_diag_jenis").val("P");
    $("#rj_diag_ket").val("");

    // load tomselect diagnosa
    rj_load_diag();

});
$(document).on("click","#rj_btn_tambah_proc",function(){

    if(!rj_id_reg){
        alert("Pilih pasien terlebih dahulu");
        return;
    }

    const modal = new bootstrap.Modal(
        document.getElementById("rj_modal_proc")
    );

    modal.show();

    $("#rj_proc_note").val("");

    // load tomselect procedure
    rj_load_proc();

});
$(document).on("click", "#rj_simpan_diag", function () {

    if(!rj_diag_select || !rj_diag_select.getValue()){
        Swal.fire({
            icon: "warning",
            title: "Diagnosa belum dipilih"
        });
        return;
    }

    let kode = rj_diag_select.getValue();
    let nama = rj_diag_select.options[kode].nama;

    Swal.fire({
        title: "Menyimpan...",
        text: "Sedang menyimpan diagnosa",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.post("/rawat-jalan/diagnosa/simpan", {

        id_registrasi: rj_id_reg,

        kode: kode,
        nama: nama,
           no_rawat: rj_no_rawat,


        jenis: $("#rj_diag_jenis").val(),
        ket: $("#rj_diag_ket").val(),

        _token: $('meta[name="csrf-token"]').attr("content")

    })
    .done(function () {

        Swal.close();

        Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: "Diagnosa berhasil ditambahkan",
            timer: 1500,
            showConfirmButton: false
        });

        const modal = bootstrap.Modal.getInstance(
            document.getElementById("rj_modal_diag")
        );

        modal.hide();

        rj_load_diagnosa();

    })
    .fail(function () {

        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Terjadi kesalahan saat menyimpan diagnosa"
        });

    });

});
$(document).on("click", "#rj_simpan_proc", function () {

    if(!rj_proc_select || !rj_proc_select.getValue()){
        Swal.fire({
            icon: "warning",
            title: "Procedure belum dipilih"
        });
        return;
    }

    let kode = rj_proc_select.getValue();
    let nama = rj_proc_select.options[kode].nama;

    Swal.fire({
        title: "Menyimpan...",
        text: "Sedang menyimpan procedure",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.post("/rawat-jalan/procedure/simpan", {

        id_registrasi: rj_id_reg,

        kode: kode,
        nama: nama,
           no_rawat: rj_no_rawat,


        note: $("#rj_proc_note").val(),

        _token: $('meta[name="csrf-token"]').attr("content")

    })
    .done(function () {

        Swal.close();

        Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: "Procedure berhasil ditambahkan",
            timer: 1500,
            showConfirmButton: false
        });

        const modal = bootstrap.Modal.getInstance(
            document.getElementById("rj_modal_proc")
        );

        modal.hide();

        rj_load_procedure();

    })
    .fail(function () {

        Swal.fire({
            icon: "error",
            title: "Gagal",
            text: "Terjadi kesalahan saat menyimpan procedure"
        });

    });

});