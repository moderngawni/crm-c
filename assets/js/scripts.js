/* assets/js/scripts.js - السكربتات المخصصة */
document.addEventListener('DOMContentLoaded', function () {
    // تهيئة DataTables
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.data-table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json"
            },
            "pageLength": 10,
            "responsive": true
        });
    }

    // تهيئة Flatpickr لاختيار التاريخ
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.flatpickr', {
            dateFormat: "Y-m-d",
            locale: "ar"
        });
    }

    // تهيئة Select2
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap-5',
            language: 'ar',
            dir: 'rtl'
        });
    }
});