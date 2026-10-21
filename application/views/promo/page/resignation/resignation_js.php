<script>
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        $('.datepicker').flatpickr();
        $('.select2').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            tags: true,
        });
        var dataTable = $("#resignationList").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/resignationList'); ?>",
                type: "POST"
            },
            "order": [],
            "columnDefs": [{
                    "targets": [1, 2, 3, 4],
                    "orderable": false,

                },
                {
                    "targets": 5,
                    "className": "text-center",
                    "orderable": false,
                }
            ],
        });

        $("form#uploadResignationLetter").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            var fields = [];
            let notRequired = []
            for (let entry of formData.entries()) {
                if (!notRequired.includes(entry[0])) {
                    fields.push(entry[0]);
                }
            }
            console.log(fields)
            var fieldsComplete = true;
            var errMessage = false;
            for (var i = 0; i < fields.length; i++) {

                var field = fields[i];
                var value = $("input[name='" + field + "']").val();

                if (value === '') {
                    if (!errMessage) {
                        alertRequired();
                        errMessage = true;
                    }
                    fieldsComplete = false;
                    $("input[name='" + field + "']").css("border-color", "#dd4b39");
                }

                $("input[name='" + field + "']").on("focus", function() {
                    $(this).css("border-color", "");
                });
            }
            if (fieldsComplete) {
                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm uploading of resignation letter.',
                    icon: 'question',
                    iconColor: '#ffc107',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Please wait...',
                            text: 'Uploading resignation letter...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/uploadLetter') ?>",
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    try {
                                        let response = JSON.parse(data);
                                        if (response.message == 'success') {
                                            Swal.fire({
                                                title: 'Success!',
                                                text: 'Resignation letter has been uploaded.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location = "<?= base_url('promo/page/resignation/resignTermList') ?>";
                                                }
                                            });
                                        } else {
                                            Swal.close();
                                            errorAlert(response.message)
                                        }
                                    } catch (e) {
                                        Swal.close();
                                        console.error("Error:", data);
                                        errorAlert(data)
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    Swal.close();
                                    console.error("AJAX Error:", textStatus, errorThrown);
                                    errorAlert(errorThrown)
                                }
                            });
                        }, 2000);
                    }
                })
            }
        });

        $("form#addResignationForm").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var fields = [];
            // let notRequired = ['asc_clearance', 'tal_clearance', 'icm_clearance', 'pm_clearance', 'tub_clearance', 'alta_clearance', 'al_panlao_clearance', ]
            for (let entry of formData.entries()) {
                // if (!notRequired.includes(entry[0])) {
                if (!entry[0].includes('clearance') && !entry[0].includes('resignation_letter')) {
                    fields.push(entry[0]);
                }
            }
            console.log(fields)
            var fieldsComplete = true;
            var errMessage = false;
            for (var i = 0; i < fields.length; i++) {

                var field = fields[i];
                var value = $("input[name='" + field + "'],select[name='" + field + "'],textarea[name='" + field + "']").val();

                if (value === '') {
                    if (!errMessage) {
                        alertRequired();
                        errMessage = true;
                    }
                    fieldsComplete = false;
                    $("input[name='" + field + "'],select[name='" + field + "'],textarea[name='" + field + "']").css("border-color", "#dd4b39");
                }

                $("input[name='" + field + "'],select[name='" + field + "'],textarea[name='" + field + "']").on("focus", function() {
                    $(this).css("border-color", "");
                });
            }
            if (fieldsComplete) {
                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm adding of Resignation/Termination.',
                    icon: 'question',
                    iconColor: '#ffc107',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Please wait...',
                            text: 'Adding Resignation/Termination data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/addResignation') ?>",
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    try {
                                        let response = JSON.parse(data);
                                        if (response.message == 'success') {
                                            Swal.fire({
                                                title: 'Success!',
                                                text: 'Resignation/Termination has been added.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location = "<?= base_url('promo/page/resignation/addResignTerm') ?>";
                                                }
                                            });
                                        } else {
                                            Swal.close();
                                            errorAlert(response.message)
                                        }
                                    } catch (e) {
                                        Swal.close();
                                        console.error("Error:", data);
                                        errorAlert(data)
                                    }
                                },
                                error: function(jqXHR, textStatus, errorThrown) {
                                    Swal.close();
                                    console.error("AJAX Error:", textStatus, errorThrown);
                                    errorAlert(errorThrown)
                                }
                            });
                        }, 2000);
                    }
                })
            }
        });
    });

    function searchPromoResignation(str) {
        var process = $('input[name="process"]').val();
        if (str.trim() != '') {

            if (process == 'addResignation') {
                $('div.addResignationForm').html('');
                $('input[name="date"]').prop('disabled', true).val('<?= date('Y-m-d') ?>');
                $('select[name="status"]').prop('disabled', true).val('');
                $('textarea[name="remarks"]').prop('disabled', true).val('');
                $('input[name="emp_id"]').val('');
            } else {
                $('div.tagResignationTable').html('');
                $('input[name="emp_id"]').val('');
            }

            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/searchPromoResignation'); ?>",
                data: {
                    str,
                    process,
                },
                success: function(data) {
                    $(".dropdown-list").show()
                    $(".dropdown-list").html(data);
                }
            });
        } else {
            $(".dropdown-list").hide();
        }

        $("input[name='name']").on('search', function() {
            $(".dropdown-list").hide();
            $('div.addResignationForm').html('');
            $('input[name="date"]').prop('disabled', true).val('<?= date('Y-m-d') ?>');
            $('select[name="status"]').prop('disabled', true).val('');
            $('textarea[name="remarks"]').prop('disabled', true).val('');
            $('input[name="emp_id"]').val('');
            $('div.tagResignationTable').html('');
        });
    }

    function selectPromoResignation(name, emp_id, status) {
        $(".dropdown-list").hide()
        var process = $("input[name='process']").val();
        if (process == 'addResignation') {
            // if (status == 'Active' || status == 'End of Contract') {
            if (status == 'Active') {
                Swal.fire({
                    title: status + '!',
                    text: 'Employee status is ' + status + '.',
                    icon: 'success',
                    iconColor: '#15ca20',
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#0d6efd',
                    allowOutsideClick: false,
                });
                $('input[name="date"]').prop('disabled', false);
                $('select[name="status"]').prop('disabled', false);
                $('textarea[name="remarks"]').prop('disabled', false);
                $('input[name="name"]').val(name);
                $('input[name="emp_id"]').val(emp_id);
            } else {
                Swal.fire({
                    title: status + '!',
                    text: 'Employee status is ' + status + '.',
                    icon: 'error',
                    iconColor: '#fd3550',
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#0d6efd',
                    allowOutsideClick: false,
                });
                $('input[name="date"]').prop('disabled', true).val('<?= date('Y-m-d') ?>');
                $('select[name="status"]').prop('disabled', true).val('');
                $('textarea[name="remarks"]').prop('disabled', true).val('');
                $('input[name="name"]').val('');
                $('input[name="emp_id"]').val('');
            }
        } else {
            $('input[name="name"]').val(name);
            $('input[name="emp_id"]').val(emp_id);
            loader_circle('tagResignationTable')
            tagResignationTable(emp_id)
        }

    }

    function addResignation(status) {
        if (status.trim() != '') {
            var emp_id = $('input[name="emp_id"]').val();
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/addResignationForm'); ?>",
                data: {
                    status,
                    emp_id,
                },
                beforeSend: function() {
                    loader_circle('resignation')
                },
                success: function(data) {
                    $('div.resignation').html(data);
                }
            });
        } else {
            $('div.resignation').html('');
        }
    }

    function tagResignationTable(emp_id) {
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/tagResignationTable'); ?>",
            data: {
                emp_id,
            },
            success: function(data) {
                $('div.tagResignationTable').html(data);
                var dataTable = $("#tagResignationTable").DataTable({
                    "destroy": true,
                    "order": [],
                    "columnDefs": [{
                            "targets": [0, 2, 3],
                            "orderable": false,
                        },
                        {
                            "targets": 4,
                            "className": "text-center",
                            "orderable": false,
                        }
                    ],
                    "drawCallback": function(settings) {
                        $('i.resignTooltip').tooltip();
                    }
                });
            }
        });
    }

    function tagResignation(tag_stat, rater_id, ratee_id) {
        if (tag_stat != '' && rater_id != '' && ratee_id != '') {
            Swal.fire({
                title: 'Confirm!',
                text: 'Confirm ' + tag_stat + 'ging of employee.',
                icon: 'question',
                iconColor: '#ffc107',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Please wait...',
                        text: tag_stat + 'ging employee...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        $.ajax({
                            type: "POST",
                            url: "<?= site_url('promo/tagResignation'); ?>",
                            data: {
                                tag_stat,
                                rater_id,
                                ratee_id
                            },
                            success: function(data) {
                                try {
                                    let response = JSON.parse(data);
                                    if (response.message == 'success') {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: 'Employee has been ' + tag_stat + 'ged.',
                                            icon: 'success',
                                            iconColor: '#15ca20',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'Ok',
                                            allowOutsideClick: false,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                tagResignationTable(rater_id)
                                            };
                                        });
                                    } else {
                                        Swal.close();
                                        errorAlert(response.message)
                                    }
                                } catch (e) {
                                    Swal.close();
                                    console.error("Error:", data);
                                    errorAlert(data)
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                Swal.close();
                                console.error("AJAX Error:", textStatus, errorThrown);
                                errorAlert(errorThrown)
                            }
                        });
                    }, 2000);
                };
            });
        } else {
            alertInfo('Data is empty!')
        }
    }

    function view_letter(link) {
        $('div#showLetter').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#showLetter").modal("show");
        console.log(link)
        $("div.showLetter").html('<img src="' + link + '" class="d-block w-100" />');
    }

    function uploadLetterForm(termination_no, emp_id) {
        $('div#uploadLetter').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#uploadLetter").modal("show");
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/uploadLetterForm'); ?>",
            data: {
                termination_no,
                emp_id
            },
            success: function(data) {
                $('div.uploadLetter').html(data);
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