import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';

import $ from 'jquery';
window.$ = window.jQuery = $;

import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.bootstrap5.css";

window.TomSelect = TomSelect;

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import Swal from 'sweetalert2';
window.Swal = Swal;

import moment from 'moment';
window.moment = moment;
// GLOBAL FUNCTIONS HARUS DIBUAT DULU
// =============================
$(document).on('hidden.bs.modal', function () {
    $('body').removeClass('modal-open');
    $('.modal-backdrop').remove();
});
window.showLoading = function (message = 'Memproses...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
};

window.hideLoading = function () {
    Swal.close();
};

window.showSuccess = function (title, text) {
    Swal.fire({
        icon: 'success',
        title: title,
        text: text,
    });
};

window.showError = function (title, text) {
    Swal.fire({
        icon: 'error',
        title: title,
        text: text,
    });
};

// =============================
// BARU IMPORT FILE LAIN
// =============================


import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

document.addEventListener("DOMContentLoaded", function () {
    flatpickr(".datepicker", {
        dateFormat: "d-m-Y"
    });
});


import './dashboard'
import './registrasi'
import './bpjs'
