<script>
    $(document).ready(function() {
        $('.datepicker').flatpickr();
        var dataTable = $("table#logs").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?php echo site_url('promo/logs'); ?>",
                type: "POST"
            },
            "order": [
                [2, 'desc']
            ],
            "columnDefs": [{
                    "targets": [0, 1, 3],
                    "orderable": false,
                },
                {
                    "targets": [4, 5],
                    "className": "text-center",
                    "orderable": false,
                },
            ],
            "initComplete": function(settings, json) {
                $('div.logs').show();
                $('.logs_list').html('');
            }
        });
        logsAdmin('<?= date('Y-m-d') ?>')
        logsSearch()
    });

    function logsAdmin(day) {
        var dataTable = $("#logsAdmin").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?php echo site_url('promo/logsAdmin'); ?>",
                type: "POST",
                data: {
                    day
                }
            },
            "order": [
                [0, 'desc']
            ],
            "columnDefs": [{
                    "targets": [1, 2, 3],
                    "orderable": false,
                },
                {
                    "targets": [4, 5],
                    "className": "text-center",
                    "orderable": false,
                },
            ],
            "initComplete": function(settings, json) {
                // Swal.fire({
                //     icon: 'info',
                //     title: 'Please wait...',
                //     text: 'Loading logs.',
                //     allowOutsideClick: false,
                //     didOpen: () => {
                //         Swal.showLoading();
                //     }
                // });
            }
        });
    }

    function logsSearch() {
        var string = $("input[name='logsSearch']").val();
        var dataTable = $("#logsSearch").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?php echo site_url('promo/logsSearch'); ?>",
                type: "POST",
                data: {
                    string
                }
            },
            "order": [
                [0, 'desc']
            ],
            "columnDefs": [{
                    "targets": [1, 2, 3],
                    "orderable": false,
                },
                {
                    "targets": [4, 5],
                    "className": "text-center",
                    "orderable": false,
                },
            ],
            "initComplete": function(settings, json) {
                // Swal.fire({
                //     icon: 'info',
                //     title: 'Please wait...',
                //     text: 'Loading logs.',
                //     allowOutsideClick: false,
                //     didOpen: () => {
                //         Swal.showLoading();
                //     }
                // });
            }
        });


    }

    function loader_circle(page) {
        $('div.' + page).html(
            '<div style="text-align: center;">' +
            '<img src="<?= base_url('assets/promo_assets/images/giphy1.gif') ?>" style="width: 50%;">' +
            '</div>'
        );
    }

    function alertRequired() {
        Swal.fire({
            title: 'Required!',
            text: 'Please fillup the required fields!',
            icon: 'warning',
            iconColor: '#ffc107',
            confirmButtonText: 'Ok',
            confirmButtonColor: '#0d6efd',
        });
    }

    function errorAlert(message) {
        Swal.fire({
            title: 'Error!',
            text: message,
            icon: 'error',
            iconColor: '#dc3545',
            confirmButtonText: 'Ok',
            confirmButtonColor: '#dc3545',
        })
    }

    function alertInfo(message) {
        Swal.fire({
            title: 'Note!',
            text: message,
            icon: 'info',
            confirmButtonText: 'Ok',
            confirmButtonColor: '#0d6efd',
        });
    }
</script>