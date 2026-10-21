<script>
    $(document).ready(function() {
        $('#filterList').on('hidden.bs.modal', function() {
            $('div.filteredList').html('')
        });
        $('#promoList').on('hidden.bs.modal', function() {
            $('div.promoList').html('')
        });

        $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
        $('.form-check-input').change(function() {
            if (!$(this).is(':checked')) {
                $(this).css('border-color', 'rgb(108 117 125)');
            } else {
                $(this).css('border-color', '');
            }
        });
        $('.datepicker').flatpickr();
        $('.select2').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            // tags: true,
        });
        $('#addForm').on('shown.bs.modal', function() {
            $('input[name="agency_name"]').val('');
            $('input[name="pc_name"]').val('');
            $('input[name="product"]').val('');
            $('input[name="dept_name"]').val('');
            $('div.updateForm').html('')
        });

        $('input[name="setupSubordinate"]').on('input', function() {
            if ($(this).val() === '') {
                console.log('Clear button clicked or input is empty');
                $('input[name="emp_id"]').val('');
                $('div.supDetails').html('');
                $('div.setupSubordinateForm').html('');
                $('div.buttonsSubordinate').html('');
            }
        });

        agencyListTable()
        companyListTable()
        productListTable()
        companyAgencyTable()
        productCompanyTable()
        departmentListTable()
        buListTable()

        $("form#saveBuForm").submit(function(e) {
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
                var value = $("input[name='" + field + "'],select[name='" + field + "']").val().trim();

                if (value === '') {
                    if (!errMessage) {
                        alertRequired();
                        errMessage = true;
                    }
                    fieldsComplete = false;
                    $("input[name='" + field + "'],select[name='" + field + "']").css("border-color", "#dd4b39");
                }

                $("input[name='" + field + "'],select[name='" + field + "']").on("focus", function() {
                    $(this).css("border-color", "");
                });
            }
            if (fieldsComplete) {

                Swal.fire({
                    title: 'Confirm?',
                    text: 'Confirm adding of business unit.',
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
                            text: 'Adding business unit...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/updateBu') ?>",
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    try {
                                        let response = JSON.parse(data);
                                        if (response.message == 'success') {
                                            $("div#addFormBu").modal("hide");
                                            Swal.fire({
                                                title: 'Success!',
                                                text: response.report,
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    buListTable()
                                                }
                                            });
                                        } else if (response.message == 'exist') {
                                            alertInfo('Business unit already exists.')
                                            $('input[name="' + response.report + '"]').css("border-color", "#dd4b39");
                                            $('input[name="' + response.report + '"]').on("focus", function() {
                                                $(this).css("border-color", "");
                                            });
                                            $('button.addFormButton').prop('disabled', false)
                                            $('button.addFormButton').html('Submit')
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

    function updateForm(process, id, table) {
        $('div#updateForm').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#updateForm").modal("show");

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/updateForm'); ?>",
            data: {
                process,
                id,
                table
            },
            beforeSend: function(data) {
                loader_circle('updateForm')
            },
            success: function(data) {
                $('div.updateForm').html(data)
            }
        });
    }

    function agencyListTable() {
        var dataTable = $("#agencyListTable").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/agencyList'); ?>",
                type: "POST"
            },
            "order": [
                [0, 'asc']
            ],
            "columnDefs": [{
                "targets": 1,
                "className": "text-center",
                "orderable": false,
            }],
        });
    }

    function saveAgency(process) {
        var id = $('input[name="agency_code"]').val();
        var agency_name = $('input[name="agency_name"]').val();

        if (process == 'add') {
            var msg1 = 'Confirm adding of agency.'
            var msg2 = 'Adding agency...'
        } else if (process == 'edit') {
            var msg1 = 'Confirm updating of agency.'
            var msg2 = 'Updating agency....'
        }
        if (agency_name.trim() != '') {
            Swal.fire({
                title: 'Confirm!',
                text: msg1,
                icon: 'question',
                iconColor: '#ffc107',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                showLoaderOnConfirm: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Please wait...',
                        text: msg2,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        $.ajax({
                            type: "POST",
                            url: "<?= site_url('promo/updateAgency'); ?>",
                            data: {
                                process,
                                id,
                                agency_name
                            },
                            success: function(data) {
                                try {
                                    let response = JSON.parse(data);
                                    if (response.message == 'success') {
                                        $("div#updateForm").modal("hide");
                                        $("div#addForm").modal("hide");
                                        Swal.fire({
                                            title: 'Success!',
                                            text: response.report,
                                            icon: 'success',
                                            iconColor: '#15ca20',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'Ok',
                                            allowOutsideClick: false,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                agencyListTable()
                                            }
                                        });
                                    } else if (response.message == 'exist') {
                                        Swal.close();
                                        alertInfo(response.report)
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
        } else {
            alertRequired()
            $("input[name='agency_name']").css("border-color", "#dd4b39");
            $("input[name='agency_name']").on("focus", function() {
                $(this).css("border-color", "");
            });
        }
    }

    function updateAgency(process, id) {

        if (process == 'deactivate') {
            var msg1 = 'Confirm deactivating of agency.'
            var msg2 = 'Deactivating agency...'
        } else if (process == 'activate') {
            var msg1 = 'Confirm activating of agency.'
            var msg2 = 'Activating agency...'
        } else if (process == 'delete') {
            var msg1 = 'Confirm deleting of agency.'
            var msg2 = 'Deleting agency...'
        }

        Swal.fire({
            title: 'Confirm!',
            text: msg1,
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
                    text: msg2,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/updateAgency'); ?>",
                        data: {
                            process,
                            id,
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.report,
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        customClass: 'required_alert',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            agencyListTable()
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

    function buListTable() {
        var dataTable = $("#buListTable").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/buList'); ?>",
                type: "POST"
            },
            "order": [
                [0, 'asc']
            ],
            "columnDefs": [{
                "targets": 4,
                "className": "text-center",
                "orderable": false,
            }],
        });
    }

    function saveBu(process) {
        var bunit_id = $('.updateForm input[name="bunit_id"]').val();
        var business_unit = $('.updateForm input[name="business_unit"]').val();
        var bunit_name = $('.updateForm input[name="bunit_name"]').val();
        var bunit_acronym = $('.updateForm input[name="bunit_acronym"]').val();
        var hrd_location = $('.updateForm select[name="hrd_location"]').val();

        if (bunit_id == '' || business_unit == '' || bunit_name == '' || bunit_acronym == '') {
            alertRequired()
            if (business_unit == '') {
                $('input[name="business_unit"]').css("border-color", "#dd4b39");
            }
            if (bunit_name == '') {
                $('input[name="bunit_name"]').css("border-color", "#dd4b39");
            }
            if (bunit_acronym == '') {
                $('input[name="bunit_acronym"]').css("border-color", "#dd4b39");
            }
            $('input[name="business_unit"],input[name="bunit_name"],input[name="bunit_acronym"]').on("focus", function() {
                $(this).css("border-color", "");
            });
        } else {
            Swal.fire({
                title: 'Confirm!',
                text: 'Confirm update of business unit.',
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
                        text: 'Updating business unit...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        $.ajax({
                            type: "POST",
                            url: "<?= site_url('promo/updateBu'); ?>",
                            data: {
                                process,
                                bunit_id,
                                business_unit,
                                bunit_name,
                                bunit_acronym,
                                hrd_location
                            },
                            success: function(data) {
                                try {
                                    let response = JSON.parse(data);
                                    if (response.message == 'success') {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: response.report,
                                            icon: 'success',
                                            iconColor: '#15ca20',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'Ok',
                                            allowOutsideClick: false,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $("div#updateForm").modal("hide");
                                                buListTable()
                                            }
                                        });
                                    } else if (response.message == 'exist') {
                                        alertInfo('Business unit already exists.')
                                        $('input[name="' + response.report + '"]').css("border-color", "#dd4b39");
                                        $('input[name="' + response.report + '"]').on("focus", function() {
                                            $(this).css("border-color", "");
                                        });
                                        $('button.addFormButton').prop('disabled', false)
                                        $('button.addFormButton').html('Submit')
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
            });
        }
    }

    function updateBu(process, column, value) {
        Swal.fire({
            icon: 'info',
            title: 'Please wait...',
            text: 'Updating business unit...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        setTimeout(() => {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/updateBu'); ?>",
                data: {
                    process,
                    column,
                    value
                },
                success: function(data) {
                    try {
                        let response = JSON.parse(data);
                        if (response.message == 'success') {
                            Swal.fire({
                                title: 'Success!',
                                text: response.report,
                                icon: 'success',
                                iconColor: '#15ca20',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok',
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // buListTable()
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

    function deleteBu(process, bunit_id) {
        Swal.fire({
            title: 'Confirm!',
            text: 'Confirm deleting of business unit.',
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
                    text: 'Deleting business unit...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/updateBu'); ?>",
                        data: {
                            process,
                            bunit_id
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.report,
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            buListTable()
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
        });

    }

    function companyAgencyTable() {
        var dataTable = $("#compAgencyTable").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/companyAgency'); ?>",
                type: "POST"
            },
            "order": [
                [0, 'asc']
            ],
            "columnDefs": [{
                "targets": 1,
                "className": "text-center",
                "orderable": false,
            }],
        });
    }

    function agencyComps(agency_code, agency_name) {
        $('div#agencyComps').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#agencyComps").modal("show");
        $("h5.agencyName").html('Agency Name: <b class="text-danger" data-toggle="tooltip" title ="' + agency_name + '">' + agency_name + '</b>');
        $('[data-toggle="tooltip"]').tooltip();
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/agencyComps'); ?>",
            data: {
                agency_code
            },
            beforeSend: function(data) {
                loader_circle('tagComps')
            },
            success: function(data) {
                $('div.tagComps').html(data)
                $('table#agencyComps').dataTable({
                    "destroy": true,
                    "order": [
                        [0, 'asc']
                    ],
                    "columnDefs": [{
                        "targets": 1,
                        "className": "text-center",
                        "orderable": false,
                    }],
                });
            }
        });
    }

    function updateCompanyAgency(process, id, company) {

        if (process == 'delete') {
            var msg1 = 'Confirm untagging of ' + company + '.';
            var msg2 = 'Untagging company...';
        } else if (process == 'add') {
            var msg1 = 'Confirm tagging of ' + company + '.';
            var msg2 = 'Tagging company...';
        }

        Swal.fire({
            title: 'Confirm!',
            text: msg1,
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
                    text: msg2,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/updateCompanyAgency'); ?>",
                        data: {
                            process,
                            id,
                            company,
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.report,
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $.ajax({
                                                type: "POST",
                                                url: "<?= site_url('promo/agencyComps'); ?>",
                                                data: {
                                                    agency_code: response.ac
                                                },
                                                beforeSend: function() {
                                                    loader_circle('tagComps')
                                                },
                                                success: function(data) {
                                                    $('div.tagComps').html(data)
                                                    $('table#agencyComps').dataTable({
                                                        "destroy": true,
                                                        "order": [
                                                            [0, 'asc']
                                                        ],
                                                        "columnDefs": [{
                                                            "targets": 1,
                                                            "className": "text-center",
                                                            "orderable": false,
                                                        }],
                                                    });
                                                }
                                            });
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

    function companyListTable() {
        var dataTable = $("#companyListTable").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/companyList'); ?>",
                type: "POST"
            },
            "order": [
                [0, 'asc']
            ],
            "columnDefs": [{
                "targets": 1,
                "className": "text-center",
                "orderable": false,
            }],
        });
    }

    function saveCompany(process) {
        var id = $('input[name="pc_code"]').val();
        var pc_name = $('input[name="pc_name"]').val();
        if (process == 'add') {
            var msg1 = 'Confirm adding of company.'
            var msg2 = 'Adding company...';
        } else if (process == 'edit') {
            var msg1 = 'Confirm updating of company.'
            var msg2 = 'Updating company...';
        }

        if (pc_name.trim() != '') {
            Swal.fire({
                title: 'Confirm!',
                text: msg1,
                icon: 'question',
                iconColor: '#ffc107',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                showLoaderOnConfirm: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Please wait...',
                        text: msg2,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        $.ajax({
                            type: "POST",
                            url: "<?= site_url('promo/updateCompany'); ?>",
                            data: {
                                process,
                                id,
                                pc_name
                            },
                            success: function(data) {
                                try {
                                    let response = JSON.parse(data);
                                    if (response.message == 'success') {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: response.report,
                                            icon: 'success',
                                            iconColor: '#15ca20',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'Ok',
                                            customClass: 'required_alert',
                                            allowOutsideClick: false,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $("div#updateForm").modal("hide");
                                                $("div#addForm").modal("hide");
                                                companyListTable()
                                            }
                                        });
                                    } else if (response.message == 'exist') {

                                        alertInfo(response.report)
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
        } else {
            alertRequired()
            $("input[name='pc_name']").css("border-color", "#dd4b39");
            $("input[name='pc_name']").on("focus", function() {
                $(this).css("border-color", "");
            });
        }
    }

    function updateCompany(process, id) {

        if (process == 'deactivate') {
            var msg1 = 'Confirm deactivating of company.'
            var msg2 = 'Deactivating company...';
        } else if (process == 'activate') {
            var msg1 = 'Confirm activating of company.'
            var msg2 = 'Activating company...';
        } else if (process == 'delete') {
            var msg1 = 'Confirm deleting of company.'
            var msg2 = 'Deleting company...';
        }
        Swal.fire({
            title: 'Confirm!',
            text: msg1,
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
                    text: msg2,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/updateCompany'); ?>",
                        data: {
                            process,
                            id,
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.report,
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        customClass: 'required_alert',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            companyListTable()
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

    function productCompanyTable() {
        var dataTable = $("#productCompanyTable").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/productCompany'); ?>",
                type: "POST"
            },
            "order": [
                [0, 'asc']
            ],
            "columnDefs": [{
                "targets": 1,
                "className": "text-center",
                "orderable": false,
            }],
        });
    }

    function compProducts(company) {
        $('div#compProducts').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#compProducts").modal("show");
        $("h5.compName").html('Company Name: <b class="text-danger">' + company + '</b>');

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/compProducts'); ?>",
            data: {
                company
            },
            success: function(data) {
                $('div.tagProducts').html(data)
                $('table#compProducts').dataTable({
                    "destroy": true,
                    "order": [
                        [0, 'asc']
                    ],
                    "columnDefs": [{
                        "targets": 1,
                        "className": "text-center",
                        "orderable": false,
                    }],
                });
                $('[data-toggle="tooltip"]').tooltip();
            }

        });
    }

    function updateProductCompany(process, id, company, product) {
        if (process == 'delete') {
            var msg1 = 'Confirm untagging of ' + product + ' on ' + company + '.';
            var msg2 = 'Untagging product...';
        } else if (process == 'add') {
            var msg1 = 'Confirm tagging of ' + product + ' on ' + company + '.';
            var msg2 = 'Tagging product...';
        }
        Swal.fire({
            title: 'Confirm!',
            text: msg1,
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
                    text: msg2,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/updateProductCompany'); ?>",
                        data: {
                            process,
                            id,
                            company,
                            product
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.report,
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        customClass: 'required_alert',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $.ajax({
                                                type: "POST",
                                                url: "<?= site_url('promo/compProducts'); ?>",
                                                data: {
                                                    company
                                                },
                                                success: function(data) {
                                                    $('div.tagProducts').html(data)
                                                    $('table#compProducts').dataTable({
                                                        "destroy": true,
                                                        "order": [
                                                            [0, 'asc']
                                                        ],
                                                        "columnDefs": [{
                                                            "targets": 1,
                                                            "className": "text-center",
                                                            "orderable": false,
                                                        }],
                                                    });
                                                    $('[data-toggle="tooltip"]').tooltip();
                                                }
                                            });
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

    function productListTable() {
        var dataTable = $("#productListTable").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/productList'); ?>",
                type: "POST"
            },
            "order": [
                [0, 'asc']
            ],
            "columnDefs": [{
                "targets": 1,
                "className": "text-center",
                "orderable": false,
            }],
        });
    }

    function saveProduct(process) {
        var id = $('input[name="id"]').val();
        var product = $('input[name="product"]').val();
        if (process == 'add') {
            var msg1 = 'Confirm adding of product.'
            var msg2 = 'Adding product...';
        } else if (process == 'edit') {
            var msg1 = 'Confirm updating of product.'
            var msg2 = 'Updating product...';
        }
        if (product.trim() != '') {
            Swal.fire({
                title: 'Confirm!',
                text: msg1,
                icon: 'question',
                iconColor: '#ffc107',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                showLoaderOnConfirm: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Please wait...',
                        text: msg2,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        $.ajax({
                            type: "POST",
                            url: "<?= site_url('promo/updateProduct'); ?>",
                            data: {
                                process,
                                id,
                                product
                            },
                            success: function(data) {
                                try {
                                    let response = JSON.parse(data);
                                    if (response.message == 'success') {
                                        $("div#updateForm").modal("hide");
                                        $("div#addForm").modal("hide");
                                        Swal.fire({
                                            title: 'Success!',
                                            text: response.report,
                                            icon: 'success',
                                            iconColor: '#15ca20',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'Ok',
                                            allowOutsideClick: false,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                productListTable()
                                            }
                                        });
                                    } else if (response.message == 'exist') {
                                        alertInfo(response.report)
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
        } else {
            alertRequired()
            $("input[name='pc_name']").css("border-color", "#dd4b39");
            $("input[name='pc_name']").on("focus", function() {
                $(this).css("border-color", "");
            });
        }
    }

    function updateProduct(process, id) {
        if (process == 'deactivate') {
            var msg1 = 'Confirm deactivating of product.'
            var msg2 = 'Deactivating product...';
        } else if (process == 'activate') {
            var msg1 = 'Confirm activating of product.'
            var msg2 = 'Activating product...';
        } else if (process == 'delete') {
            var msg1 = 'Confirm deleting of product.'
            var msg2 = 'Deleting product...';
        }
        Swal.fire({
            title: 'Confirm!',
            text: msg1,
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
                    text: msg2,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/updateProduct'); ?>",
                        data: {
                            process,
                            id,
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.report,
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            productListTable()
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

    function departmentListTable() {
        var dataTable = $("#departmentListTable").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/departmentList'); ?>",
                type: "POST"
            },
            "order": [
                [0, 'asc']
            ],

            "columnDefs": [{
                "targets": 1,
                "className": "text-center",
                "orderable": false,
            }],
        });
    }

    function deleteDept(process, dept) {
        Swal.fire({
            title: 'Confirm!',
            text: 'Confirm deleting of department.',
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
                    text: 'Deleting department...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/updateDepartment'); ?>",
                        data: {
                            process,
                            dept
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.report,
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            departmentListTable()
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

    function deptStores(dept) {
        $('div#deptStores').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#deptStores").modal("show");
        $("h5.deptName").html('Department Name: <b class="text-danger">' + dept + '</b>');

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/deptStores'); ?>",
            data: {
                dept
            },
            success: function(data) {
                $('div.tagStores').html(data)
            }
        });
    }

    function saveDepartment(process) {
        console.log(process)
        var dept = $('input[name="dept_name"]').val();
        if (dept.trim() == '') {
            alertRequired()
            if (dept == '') {
                $('input[name="dept_name"]').css("border-color", "#dd4b39");
                $('input[name="dept_name"]').on("focus", function() {
                    $(this).css("border-color", "");
                });
            }
        } else {
            Swal.fire({
                title: 'Confirm!',
                text: 'Confirm adding of department.',
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
                        text: 'Adding department...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        $.ajax({
                            type: "POST",
                            url: "<?= site_url('promo/updateDepartment'); ?>",
                            data: {
                                process,
                                dept
                            },
                            success: function(data) {
                                try {
                                    let response = JSON.parse(data);
                                    if (response.message == 'success') {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: response.report,
                                            icon: 'success',
                                            iconColor: '#15ca20',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'Ok',
                                            allowOutsideClick: false,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $("div#addForm").modal("hide");
                                                deptStores(dept.toUpperCase())
                                                departmentListTable()
                                            }
                                        });
                                    } else if (response.message == 'exist') {
                                        alertInfo(response.report)
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
    }

    function updateDept(process, id, dept, store) {
        if (process == 'deactivate') {
            var msg1 = 'Confirm deactivating of ' + store + ' on ' + dept + '.'
            var msg2 = 'Deactivating store...';
        } else if (process == 'activate') {
            var msg1 = 'Confirm activating of ' + store + ' on ' + dept + '.'
            var msg2 = 'Activating store...';
        } else if (process == 'untag') {
            var msg1 = 'Confirm untagging of ' + store + ' on ' + dept + '.'
            var msg2 = 'Untagging store...';
        } else if (process == 'tag') {
            var msg1 = 'Confirm tagging of ' + store + ' on ' + dept + '.'
            var msg2 = 'Tagging store...';
        }

        Swal.fire({
            title: 'Confirm!',
            text: msg1,
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
                    text: 'Adding department...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/updateDepartment'); ?>",
                        data: {
                            process,
                            id,
                            dept,
                            store
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: response.report,
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $.ajax({
                                                type: "POST",
                                                url: "<?= site_url('promo/deptStores'); ?>",
                                                data: {
                                                    dept
                                                },
                                                success: function(data) {
                                                    $('div.tagStores').html(data)
                                                }
                                            });
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

    function searchPromoSetup(str) {
        var process = $('input[name="process"]').val();
        $('div.supDetails').html('');
        $("input[name='emp_id']").val('');
        $('div.setupSubordinateForm').html('').removeClass('border-start');
        if (str.trim() != '') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/searchPromoSetup'); ?>",
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
            $('div.supDetails').html('');
            $("input[name='emp_id']").val('');
            $('div.setupSubordinateForm').html('').removeClass('border-start');
        });
    }

    function selectPromoSetup(name, emp_id) {
        $(".dropdown-list").hide()
        $("input[name='emp_id']").val(emp_id);
        $("input[name='name']").val(name);
        var process = $('input[name="process"]').val();

        if (process == 'setupSubordinate') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/selectPromoSetup'); ?>",
                data: {
                    emp_id,
                    process,
                },
                beforeSend: function(data) {
                    loader_circle('supDetails')
                },
                success: function(data) {
                    $('div.supDetails').html(data)
                    $('span.spanTooltip').tooltip();
                    $('td.pComp').tooltip();
                    subordinates(emp_id)
                }
            });
        }
    }

    function promoList(emp_id) {
        $('button.addingButton').attr('id', emp_id);
        $('div#promoList').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#promoList").modal("show");
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/promoList'); ?>",
            data: {
                emp_id,
            },
            beforeSend: function(data) {
                loader_circle('promoList')
            },
            success: function(data) {
                $('div.promoList').html(data);
                $("table#promoListTable").DataTable({
                    "destroy": true,
                    "order": [],
                    "columnDefs": [{
                            "targets": [0, 2],
                            "orderable": false,
                        },
                        {
                            "targets": [3],
                            "className": "text-center",
                            "orderable": false,
                        }
                    ],
                    "drawCallback": function(settings) {
                        // Apply border styling
                        $('.form-check-input:not(:checked)').css('border-color', '#aaa');

                        // Rebind change event on each draw
                        $('.form-check-input').off('change').on('change', function() {
                            $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                            if (!$(this).is(':checked')) {
                                $(this).css('border-color', '#aaa');
                            } else {
                                $(this).css('border-color', '');
                            }
                        });
                    }
                });

                $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                $('.form-check-input').change(function() {
                    $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                    if (!$(this).is(':checked')) {
                        $(this).css('border-color', '#aaa');
                    } else {
                        $(this).css('border-color', '');
                    }
                });
            }
        });
    }

    function filterForm(emp_id) {
        $('div#filterList').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#filterList").modal("show");
        $('button.filterList').attr('id', emp_id);
        $('button.addingButton').attr('id', emp_id);
        $('.select2').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: true,
            dropdownParent: $('#filterList')
        });

    }

    function filterList(emp_id) {

        var store = $('select[name="store"]').val();
        var dept = $('select[name="promo_department"]').val();
        var type = $('select[name="promo_type"]').val();
        var comp = $('select[name="promo_company"]').val();
        var agency = $('select[name="agency_code"]').val();
        if (store || dept || type || comp || agency) {

            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/promoList'); ?>",
                data: {
                    emp_id,
                    store,
                    dept,
                    type,
                    comp,
                    agency
                },
                beforeSend: function(data) {
                    loader_circle('filteredList')
                },
                success: function(data) {
                    $('button.addingButton').show();
                    $('div.filteredList').html(data);
                    $("table#promoListTable").DataTable({
                        "destroy": true,
                        "order": [],
                        "columnDefs": [{
                                "targets": [0, 2],
                                "orderable": false,
                            },
                            {
                                "targets": [3],
                                "className": "text-center",
                                "orderable": false,
                            }
                        ],
                        "drawCallback": function(settings) {
                            // Apply border styling
                            $('.form-check-input:not(:checked)').css('border-color', '#aaa');

                            // Rebind change event on each draw
                            $('.form-check-input').off('change').on('change', function() {
                                $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                                if (!$(this).is(':checked')) {
                                    $(this).css('border-color', '#aaa');
                                } else {
                                    $(this).css('border-color', '');
                                }
                            });
                        }
                    });

                    $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                    $('.form-check-input').change(function() {
                        $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                        if (!$(this).is(':checked')) {
                            $(this).css('border-color', '#aaa');
                        } else {
                            $(this).css('border-color', '');
                        }
                    });
                }
            });
        } else {
            alertInfo('Please select atleast 1 filter.')
        }
    }

    function getCompany(value) {
        var action = 'getCompany';
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {

                $('select[name="promo_company"]').html(data);
            }
        });
    }

    function filterDepartment(value) {
        var action = 'filterDepartment';
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {
                $('select[name="promo_department"]').html(data);
            }
        });

    }

    function subordinates(emp_id) {
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/subordinates'); ?>",
            data: {
                emp_id,
            },
            beforeSend: function(data) {
                loader_circle('setupSubordinateForm')
            },
            success: function(data) {
                $('div.setupSubordinateForm').html(data).addClass('border-start');
                var dataTable = $("#setupSubordinateTable").DataTable({
                    "destroy": true,
                    "order": [],
                    "columnDefs": [{
                            "targets": [0, 1, 2, 3],
                            "orderable": false,
                        },
                        {
                            "targets": [4],
                            "className": "text-center",
                            "orderable": false,
                        }
                    ],
                    "drawCallback": function(settings) {
                        // Apply border styling
                        $('.form-check-input:not(:checked)').css('border-color', '#aaa');

                        // Rebind change event on each draw
                        $('.form-check-input').off('change').on('change', function() {
                            $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                            if (!$(this).is(':checked')) {
                                $(this).css('border-color', '#aaa');
                            } else {
                                $(this).css('border-color', '');
                            }
                        });
                    }
                });


                $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                $('.form-check-input').change(function() {
                    $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                    if (!$(this).is(':checked')) {
                        $(this).css('border-color', '#aaa');
                    } else {
                        $(this).css('border-color', '');
                    }
                });
            }
        });
    }

    function addSub(ratee) {
        console.log(ratee)
        var process = 'addMul';
        var msg1 = 'Confirm adding of subordinates.'
        var msg2 = 'Adding subordinates...';
        var msg3 = 'Subordinates has been added.';
        var table = $('#promoListTable').DataTable();
        var empId = [];
        table.rows().nodes().to$().find('input[name="emp_ids[]"]:checked').each(function() {
            empId.push($(this).val());
        });
        console.log(empId)
        if (empId.length == 0) {
            alertInfo('Check atleast 1(one) in the PROMO List.')
        } else {
            Swal.fire({
                title: 'Confirm!',
                text: msg1,
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
                        title: 'Please wait!',
                        text: msg2,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        $.ajax({
                            type: "POST",
                            url: "<?= site_url('promo/updateSubordinates'); ?>",
                            data: {
                                process,
                                ratee,
                                empId
                            },
                            success: function(data) {
                                try {
                                    let response = JSON.parse(data);
                                    if (response.message == 'success') {
                                        $("div#promoList").modal("hide");
                                        subordinates(ratee)
                                        Swal.fire({
                                            title: 'Success!',
                                            text: msg3,
                                            icon: 'success',
                                            iconColor: '#15ca20',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'Ok',
                                            allowOutsideClick: false,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                // subordinates(ratee)
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
                    }, 1000);
                }
            })
        }
    }

    function removeSub(ratee) {
        console.log(ratee)
        var process = 'deleteMul';
        var msg1 = 'Confirm deleting of subordinates.'
        var msg2 = 'Deleting subordinates...';
        var msg3 = 'Subordinates has been deleted.';
        var table = $('#setupSubordinateTable').DataTable();
        var subId = [];
        table.rows().nodes().to$().find('input[name="subId[]"]:checked').each(function() {
            subId.push($(this).val());
        });
        console.log(subId)
        if (subId.length == 0) {
            alertInfo('Check atleast 1(one) in the SUBORDINATES List.')
        } else {
            Swal.fire({
                title: 'Confirm!',
                text: msg1,
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
                        title: 'Please wait!',
                        text: msg2,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        $.ajax({
                            type: "POST",
                            url: "<?= site_url('promo/updateSubordinates'); ?>",
                            data: {
                                process,
                                ratee,
                                subId
                            },
                            success: function(data) {
                                try {
                                    let response = JSON.parse(data);
                                    if (response.message == 'success') {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: msg3,
                                            icon: 'success',
                                            iconColor: '#15ca20',
                                            confirmButtonColor: '#3085d6',
                                            confirmButtonText: 'Ok',
                                            allowOutsideClick: false,
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                subordinates(ratee)
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
                    }, 1000);
                }
            })
        }
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