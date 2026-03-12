import $ from "jquery";
window.$ = window.jQuery = $;
let page = 1;

loadData();
loadSummary();

// auto refresh tiap 10 detik
setInterval(function () {
    loadData(false);
    loadSummary();
}, 10000);

function loadData(showLoader = true) {
    if (showLoader) {
        $("#table-data").html(`
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                    Memuat data pasien...
                </td>
            </tr>
        `);
    }

    fetch(`/registrasi/data?page=${page}&keyword=` + $("#keyword").val())
        .then((res) => res.json())

        .then((res) => {
            let html = "";

            res.data.forEach((row) => {
                let tipe = "";
                let sepBadge = "";
                let pasienBadge = "";
                let menuRanap = "";
                let menuSep = "";

                if (row.tipe_rawat === "J" || row.tipe_rawat === "G") {
                    menuRanap = `
<li>
<a class="dropdown-item text-success"
href="/rawat-inap/masuk/${row.no_rawat}">
🏥 Masuk Rawat Inap
</a>
</li>`;
                }

                if (!row.noSep) {
                    menuSep = `
<li>
<button class="dropdown-item text-primary btn-buat-sep"
data-nokartu="${row.no_bpjs ?? ""}"
data-nama="${row.nama_pasien ?? ""}"
data-nik="${row.nik ?? ""}"
data-norm="${row.no_rm ?? ""}"
data-notelp="${row.no_hp ?? ""}"
data-norawat="${row.no_rawat ?? ""}">
📄 Buat SEP
</button>
</li>`;
                }

                if (row.tipe_rawat == "J") {
                    tipe = `<span class="badge bg-success">Rawat Jalan</span>`;
                } else if (row.tipe_rawat == "G") {
                    tipe = `<span class="badge bg-danger">IGD</span>`;
                } else {
                    tipe = `<span class="badge bg-primary">Rawat Inap</span>`;
                }

                if (row.noSep) {
                    sepBadge = `<span class="badge bg-primary">SEP Aktif</span>`;
                } else {
                    sepBadge = `<span class="badge bg-secondary">Belum SEP</span>`;
                }

                if (row.jumlah_kunjungan <= 1) {
                    pasienBadge = `<span class="badge bg-success">Pasien Baru</span>`;
                } else {
                    pasienBadge = `<span class="badge bg-info">Pasien Lama</span>`;
                }

                let highlight = "";

                if (row.antrian_aktif == row.no_antrian) {
                    highlight = "table-warning";
                }

                html += `
<tr class="${highlight}">

<td>${row.no_rm ?? "-"}</td>

<td>${formatTanggal(row.tanggal_registrasi)}</td>

<td>
${row.nama_pasien}
<br>
${pasienBadge}
</td>

<td>${row.jenis_kelamin ?? "-"}</td>

<td>${tipe}</td>

<td>${row.nama_poli ?? "-"}</td>

<td>${row.nama_dokter ?? "-"}</td>

<td class="fw-bold text-center">
${row.no_antrian ?? "-"}
</td>

<td>${sepBadge}</td>

<td class="text-center position-static">

<div class="dropdown">

<button class="btn btn-sm btn-outline-primary dropdown-toggle"
data-bs-toggle="dropdown"
data-bs-boundary="body"
data-bs-display="static">
Aksi
</button>

<ul class="dropdown-menu dropdown-menu-end">

${menuSep}

<li>
<button class="dropdown-item text-danger btn-batal"
data-id="${row.id_registrasi}"
data-nama="${row.nama_pasien}">
❌ Batal Registrasi
</button>
</li>

<li>
<a class="dropdown-item"
target="_blank"
href="/cetak/${row.no_rawat}">
🖨 Cetak Etiket
</a>
</li>

<li>
<button class="dropdown-item btn-history"
data-id="${row.id_registrasi}">
📜 History
</button>
</li>

${menuRanap}

</ul>

</div>

</td>
</tr>`;
            });

            $("#table-data").html(html);

            buatPagination(res.total, res.limit);
        })

        .catch(() => {
            $("#table-data").html(`
                <tr>
                    <td colspan="10" class="text-center text-danger py-4">
                        Gagal memuat data
                    </td>
                </tr>
            `);
        });
}
   $(document).on('click', '.btn-history', function () {

        let id = $(this).data('id')

        $('#modal-registrasi-body').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary"></div>
                <div class="mt-2 text-muted">Loading riwayat...</div>
            </div>
        `)

        const modal = new bootstrap.Modal(
            document.getElementById('modalRegistrasi')
        )
        modal.show()

        $.get('/registrasi/history/' + id)
            .done(function (data) {

                if (!data || data.length === 0) {
                    $('#modal-registrasi-body').html(
                        '<div class="alert alert-light text-center">Tidak ada riwayat pemeriksaan.</div>'
                    )
                    return
                }

                let html = `
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tgl Registrasi</th>
                                    <th>Tgl Pulang</th>
                                    <th>Poli</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                `

                data.forEach(item => {

                    let tglReg = new Date(item.tanggal_registrasi)
                        .toLocaleDateString('id-ID')

                    let tglPulang = item.tanggal_pulang
                        ? new Date(item.tanggal_pulang).toLocaleDateString('id-ID')
                        : '-'

                    html += `
                        <tr>
                            <td>${tglReg}</td>
                            <td>${tglPulang}</td>
                            <td><span class="badge bg-primary">${item.poli ?? '-'}</span></td>
                            <td>
                                ${
                                    item.tanggal_pulang
                                        ? '<span class="badge bg-success">Selesai</span>'
                                        : '<span class="badge bg-warning text-dark">Masih Dirawat</span>'
                                }
                            </td>
                        </tr>
                    `
                })

                html += `</tbody></table></div>`

                $('#modal-registrasi-body').html(html)
            })
            .fail(function () {
                $('#modal-registrasi-body').html(
                    '<div class="alert alert-danger text-center">Gagal mengambil data.</div>'
                )
            })
    })

/* ================= SUMMARY ================= */

function loadSummary() {
    fetch("/registrasi/summary")
        .then((res) => res.json())

        .then((res) => {
            $("#totalHariIni").text(res.totalHariIni);
            $("#totalRajal").text(res.totalRajal);
            $("#totalIgd").text(res.totalIgd);
            $("#totalRanap").text(res.totalRanap);
        });
}

/* ================= PAGINATION ================= */

function buatPagination(total, limit) {
    let totalPage = Math.ceil(total / limit);

    let html = "";

    for (let i = 1; i <= totalPage; i++) {
        html += `
<li class="page-item ${i == page ? "active" : ""}">
<a class="page-link" href="#"
onclick="gantiPage(${i})">${i}</a>
</li>
`;
    }

    $("#pagination").html(html);
}

window.gantiPage = function (p) {
    page = p;

    loadData();
};

/* ================= FORMAT TANGGAL ================= */

function formatTanggal(tanggal) {
    let d = new Date(tanggal);

    return (
        d.getDate().toString().padStart(2, "0") +
        "-" +
        (d.getMonth() + 1).toString().padStart(2, "0") +
        "-" +
        d.getFullYear()
    );
}
$(document).on("click", ".btn-batal", function () {
    $("#batal_id").val($(this).data("id"));
    $("#batal_nama").val($(this).data("nama"));

    const modal = new bootstrap.Modal(document.getElementById("modalBatal"));

    modal.show();
});
$("#formBatal").on("submit", function (e) {
    e.preventDefault();

    fetch("/registrasi/batal", {
        method: "POST",

        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },

        body: JSON.stringify({
            id: $("#batal_id").val(),
            alasan: $("#alasan").val(),
        }),
    })
        .then((res) => res.json())

        .then((res) => {
            if (res.status === "success") {
                bootstrap.Modal.getInstance(
                    document.getElementById("modalBatal"),
                ).hide();

                loadData();
            } else {
                alert(res.message);
            }
        });
});
$(document).on("click", ".btn-buat-sep", function () {
    let noKartu = $(this).data("nokartu");
    let nama = $(this).data("nama");
    let nik = $(this).data("nik");
    let noMr = $(this).data("norm");
    let telp = $(this).data("notelp");

    $("#no_kartu_txt").val(noKartu);

    $("#nama_peserta").val(nama);
    $("#noKartu").val(noKartu);
    $("#nik").val(nik);
    $("#noMr").val(noMr);
    $("#noTelp").val(telp);

    $("#tglSep").val(new Date().toISOString().split("T")[0]);

    new bootstrap.Modal(document.getElementById("modalSEP")).show();
});
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
            "RUJUKAN ONLINE",
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

        $("#jadwal_dokter").html("<option>Loading...</option>");

        $.ajax({
            url: "/registrasi/api/jadwal-dokter",
            type: "POST",
            data: {
                poli_id: poli_id,
                tanggal: tanggal,
                _token: $('meta[name="csrf-token"]').attr("content"),
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
                $("#jadwal_dokter").html(
                    '<option value="">Gagal memuat jadwal</option>',
                );
            },
        });
    });
    $("#btnCekIhs").on("click", function () {
        let nik = $("#no_identitas").val();
        let pasienId = $("#pasien_id").val();

        if (!nik) {
            alert("NIK wajib diisi");
            return;
        }

        $("#ihsResult").html("Mencari IHS Number...");

        $.post(
            "/registrasi/cek-ihs",
            {
                _token: $('meta[name="csrf-token"]').attr("content"),
                no_identitas: nik,
                pasien_id: pasienId,
            },
            function (res) {
                if (res.success) {
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
            },
        );
    });
    $("#cara_bayar").on("change", function () {
        if ($(this).val() !== "BPJS") return;

        let nik = $("#no_identitas").val();
        let kartu = $("input[name='no_kartu']").val();

        let nomor = kartu ? kartu : nik;
        let jenis = kartu ? "nokartu" : "nik";

        if (!nomor) {
            alert("Isi NIK atau No Kartu dulu");
            return;
        }

        $("#ihsResult").html("Mengecek data BPJS...");

        $.post(
            "/registrasi/cek-bpjs",
            {
                _token: $('meta[name="csrf-token"]').attr("content"),
                nomor: nomor,
                jenis: jenis,
            },
            function (res) {
                if (res.success) {
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
            },
        );
    });
    let existingIhs = "{{ $pasien->ihs_number ?? '' }}";

    if (existingIhs) {
        $("#ihsResult").html(`
            <div style="background:#e6fffa;padding:10px;border-radius:6px;color:#065f46;">
                <b>IHS Number:</b> ${existingIhs}
                <br><small>Data sudah tersimpan</small>
            </div>
        `);
    }
    $("#form-reg").on("submit", function (e) {
        e.preventDefault();
        window.showLoading();

        let formData = $(this).serialize();

        $.post("/registrasi/simpan", formData, function (res) {
            if (res.status === "success") {
                window.hideLoading();

                window.open(
                    "/registrasi/cetak-etiket/" + res.no_rawat,
                    "_blank",
                );

                if (res.cara_bayar === "BPJS") {
                    window.hideLoading();

                    Swal.fire({
                        icon: "success",
                        title: "Registrasi Berhasil",
                        text: "Silakan buat SEP",
                    });

                    $("#panel-sep").slideDown();

                    $("html, body").animate(
                        {
                            scrollTop: $("#panel-sep").offset().top - 80,
                        },
                        800,
                    );

                    $("#noKartu").val(res.no_kartu);
                    $("#nama_peserta").val(res.nama);
                    $("#nik").val(res.nik);
                    $("#noMr").val(res.no_rm);
                } else {
                    // Non BPJS → redirect setelah 1 detik
                    setTimeout(function () {
                        window.location.href = "/registrasi";
                    }, 1000);
                }
            } else {
                Swal.fire("Error", res.message, "error");
            }
        });
    });
    $("#cekNoPeserta").on("click", function () {
        let noPeserta = $("#nomor_peserta").val();
        let tgl_sep = $("#tgl_sep").val();

        $.get(
            "/bpjs/cek-peserta",
            {
                no_peserta: noPeserta,
                tgl_sep: tgl_sep,
            },
            function (response) {
                if (response.metaData.code != 200) {
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
            },
        );
    });
    $(document).on("click", "#search_diagnosa", function () {
        let kode = $("#kode_diagnosa").val();

        $.get("/bpjs/referensi-diagnosa/" + kode, function (response) {
            if (response.metaData.code != 200) {
                $("#show_data_diagnosa").html(
                    "<tr><td colspan='4'>Data tidak ditemukan</td></tr>",
                );
                return;
            }

            let data = response.response.diagnosa;
            let html = "";

            data.forEach((d, i) => {
                html += `
                <tr>
                    <td>${i + 1}</td>
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
    $(document).on("click", "#search_poli", function () {
        let kode = $("#kode_poli").val();

        $.get("/bpjs/referensi-poli/" + kode, function (response) {
            if (response.metaData.code != 200) {
                return;
            }

            let data = response.response.poli;
            let html = "";

            data.forEach((d, i) => {
                html += `
                <tr>
                    <td>${i + 1}</td>
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
