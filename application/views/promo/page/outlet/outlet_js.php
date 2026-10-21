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

        $("#changeOutletHistory").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/changeOutletHistory'); ?>",
                type: "POST"
            },
            "order": [],
            "columnDefs": [{
                "targets": [0, 2, 3, 4],
                "orderable": false,

            }, {
                "targets": [3, 4],
                "className": "text-wrap",
            }, {
                "targets": [0],
                "className": "text-center",
            }],
        });

        $("form#addOutletForm").submit(function(e) {
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

                var stores = [];
                var current_stores = [];
                $('input[name="stores[]"]:checked').each(function() {
                    if (!$(this).is(':disabled')) {
                        stores.push($(this).val());
                    } else {
                        current_stores.push($(this).val());
                    }
                });
                if (stores.length == 0) {

                    alertInfo('Please select a Store.')
                } else {
                    Swal.fire({
                        title: 'Confirm!',
                        text: 'Confirm adding of store(s).',
                        icon: 'question',
                        iconColor: '#ffc107',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('button.outletButton').prop('disabled', true)
                            Swal.fire({
                                icon: 'info',
                                title: 'Please wait!',
                                text: 'Adding outlet...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            setTimeout(() => {
                                $.ajax({
                                    url: "<?= site_url('promo/addOutlet') ?>",
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
                                                    text: 'New outlet(s) has been added.',
                                                    icon: 'success',
                                                    iconColor: '#15ca20',
                                                    confirmButtonColor: '#3085d6',
                                                    confirmButtonText: 'Ok',
                                                    allowOutsideClick: false,
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location = "<?= base_url('promo/page/outlet/addOutlet') ?>";
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
            }
        });

        $("form#removeOutletForm").submit(function(e) {
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
                var storeCount = $('input[name="storeCount"').val()

                var stores = [];
                $('input[name="stores[]"]:checked').each(function() {
                    if ($(this)) {
                        stores.push($(this).val());
                    }
                });

                $('input[name="stores[]"]').each(function() {
                    if (!$(this).is(':checked') || $(this).is(':disabled')) {
                        formData.append('currentStores[]', $(this).val());
                    }
                });

                if (stores.length == 0) {
                    alertInfo('Please select a Store.')
                } else if (stores.length == storeCount) {
                    alertInfo('You can not remove all stores.')
                } else {
                    Swal.fire({
                        title: 'Confirm!',
                        text: 'Confirm removing of store(s).',
                        icon: 'question',
                        iconColor: '#ffc107',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('button.outletButton').prop('disabled', true)
                            Swal.fire({
                                icon: 'info',
                                title: 'Please wait!',
                                text: 'Removing outlet...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            setTimeout(() => {
                                $.ajax({
                                    url: "<?= site_url('promo/removeOutlet') ?>",
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
                                                    text: 'Outlet(s) has been removed.',
                                                    icon: 'success',
                                                    iconColor: '#15ca20',
                                                    confirmButtonColor: '#3085d6',
                                                    confirmButtonText: 'Ok',
                                                    allowOutsideClick: false,
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location = "<?= base_url('promo/page/outlet/removeOutlet') ?>";
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
            }
        });

        $("form#transferOutletForm").submit(function(e) {
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
                var storeCount = $('input[name="storeCount"').val()

                var stores = [];
                $('input[name="stores[]"]:checked').each(function() {
                    if ($(this)) {
                        stores.push($(this).val());
                    }
                });

                var transfer = [];
                $('input[name="transfer[]"]:checked').each(function() {
                    if ($(this)) {
                        transfer.push($(this).val());
                    }
                });

                $('input[name="stores[]"]').each(function() {
                    if (!$(this).is(':checked') || $(this).is(':disabled')) {
                        formData.append('currentStores[]', $(this).val());
                    }
                });

                if (stores.length == 0) {
                    alertInfo('Please select a Store to Transfer From.')
                } else if (transfer.length == 0) {
                    alertInfo('Please select a Store to Transfer To')
                    // } else if (stores.length == storeCount) {
                    //     alertInfo('You can not remove all stores.')
                } else {
                    Swal.fire({
                        title: 'Confirm!',
                        text: 'Confirm transfering of store(s).',
                        icon: 'question',
                        iconColor: '#ffc107',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                        allowOutsideClick: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('button.outletButton').prop('disabled', true)
                            Swal.fire({
                                icon: 'info',
                                title: 'Please wait!',
                                text: 'Transfering outlet...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            setTimeout(() => {
                                $.ajax({
                                    url: "<?= site_url('promo/transferOutlet') ?>",
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
                                                    text: 'Outlet(s) has been transfered.',
                                                    icon: 'success',
                                                    iconColor: '#15ca20',
                                                    confirmButtonColor: '#3085d6',
                                                    confirmButtonText: 'Ok',
                                                    allowOutsideClick: false,
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location = "<?= base_url('promo/page/outlet/transferOutlet') ?>";
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
            }
        });
    });

    let ajaxCallCount = 0;
    let ajaxRequest;

    function searchPromoOutlet(str) {
        $('div.addOutlet,div.removeOutlet,div.transferOutlet,div.promoDetails').html('');
        $('div.addOutlet,div.removeOutlet,div.transferOutlet').removeClass('border-start');
        var process = $('input[name="process"]').val();
        if (str.trim() != '') {

            if (ajaxRequest && ajaxRequest.readyState !== 4) {
                ajaxRequest.abort();
                // console.log("Previous AJAX request aborted");
                // The readyState property represents the state of the AJAX request.
                // 0: Request not initialized
                // 1: Server connection established
                // 2: Request received
                // 3: Processing request
                // 4: Request finished and response is ready (completed)
            }

            ajaxCallCount++;
            // console.log("AJAX call count: 


            ajaxRequest = $.ajax({
                type: "POST",
                url: "<?= site_url('promo/searchPromoOutlet'); ?>",
                data: {
                    str,
                    process,
                },
                beforeSend: function() {
                    $('.dropdown-list').html('<ul class="list-group"><a class="list-group-item">' +
                        '<div class="spinner-border spinner-border-sm" role="status"> <span class="visually-hidden">Loading...</span>' +
                        '</div></a></ul>');
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
            $('div.addOutlet,div.removeOutlet,div.transferOutlet,div.promoDetails').html('');
            $('div.addOutlet,div.removeOutlet,div.transferOutlet').removeClass('border-start');
            $(".dropdown-list").hide();
        });
    }

    function selectPromoOutlet(name, emp_id, record_no) {
        $(".dropdown-list").hide()
        $('input[name="name"]').val(name);
        var process = $('input[name="process"]').val();
        if (process == 'addOutlet') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/selectPromoOutlet'); ?>",
                data: {
                    emp_id,
                    record_no,
                    process
                },
                beforeSend: function() {
                    loader_circle('addOutlet')
                },
                success: function(data) {
                    $('div.addOutlet').html(data);
                    $('div.addOutlet').addClass('border-start');
                    var startdate = $('input[name="startdate"]').val();
                    var eocdate = $('input[name="eocdate"]').val();
                    $('.datepicker').flatpickr({
                        minDate: startdate,
                        maxDate: eocdate
                    });
                    $('span.spanTooltip').tooltip();
                    $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
                    $('.form-check-input').change(function() {
                        if (!$(this).is(':checked')) {
                            $(this).css('border-color', 'rgb(108 117 125)');
                        } else {
                            $(this).css('border-color', '');
                        }
                    });
                    var today = '<?= date('Y-m-d') ?>';
                    getDuration(startdate)
                }
            });
            changeOutletDetails(emp_id, record_no)

        } else if (process == 'removeOutlet') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/selectPromoOutlet'); ?>",
                data: {
                    emp_id,
                    record_no,
                    process
                },
                beforeSend: function() {
                    loader_circle('removeOutlet')
                },
                success: function(data) {
                    $('div.removeOutlet').html(data);
                    $('div.removeOutlet').addClass('border-start');
                    $('input.readonly').css('background-color', '#e9ecef');
                    // $('.datepicker').flatpickr();
                    var startdate = $('input[name="startdate"]').val();
                    var eocdate = $('input[name="eocdate"]').val();
                    $('.datepicker').flatpickr({
                        minDate: startdate,
                        maxDate: eocdate
                    });
                    $('span.spanTooltip').tooltip();
                    $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
                    $('.form-check-input').change(function() {
                        if (!$(this).is(':checked')) {
                            $(this).css('border-color', 'rgb(108 117 125)');
                        } else {
                            $(this).css('border-color', '');
                        }
                    });
                    var today = '<?= date('Y-m-d') ?>';
                    getDuration(startdate)
                }
            });
            changeOutletDetails(emp_id, record_no)
        } else if (process == 'transferOutlet') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/selectPromoOutlet'); ?>",
                data: {
                    emp_id,
                    record_no,
                    process
                },
                beforeSend: function() {
                    loader_circle('transferOutlet')
                },
                success: function(data) {
                    $('div.transferOutlet').html(data);
                    $('div.transferOutlet').addClass('border-start');
                    var setdate = $('input[name="setdate"]').val();
                    var eocdate = $('input[name="eocdate"]').val();
                    $('.datepicker').flatpickr({
                        minDate: setdate,
                        maxDate: eocdate
                    });
                    $('span.spanTooltip').tooltip();
                    $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
                    $('.form-check-input').change(function() {
                        if (!$(this).is(':checked')) {
                            $(this).css('border-color', 'rgb(108 117 125)');
                        } else {
                            $(this).css('border-color', '');
                        }
                    });
                }
            });
            changeOutletDetails(emp_id, record_no)
        }
    }

    function changeOutletDetails(emp_id, record_no) {
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/changeOutletDetails'); ?>",
            data: {
                emp_id,
                record_no,
            },
            beforeSend: function() {
                loader_circle('promoDetails')
            },
            success: function(data) {
                $('div.promoDetails').html(data);

            }
        });
    }

    function viewAppraisal(details_id) {
        if (details_id != '') {
            $('div#viewAppraisal').modal({
                backdrop: 'static',
                keyboard: false,
            });
            $("div#viewAppraisal").modal("show");

            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/viewAppraisal'); ?>",
                data: {
                    details_id,
                },
                beforeSend: function() {
                    loader_circle('viewAppraisal')
                },
                success: function(data) {

                    $('div.viewAppraisal').html(data);
                    $('button.print').prop('id', details_id)
                }
            });
        }
    }

    function getIntros() {
        var process = $('input[name="process"]').val();
        var action = 'getIntros';
        var value = [];
        if (process == 'addOutlet') {
            $('input[name="stores[]"]:checked').each(function() {
                if (!$(this).is(':disabled')) {
                    value.push($(this).val());
                }
            });
        } else {
            $('input[name="transfer[]"]:checked').each(function() {
                if (!$(this).is(':disabled')) {
                    value.push($(this).val());
                }
            });
        }
        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelectOutlet'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {

                $('div.intros').html(data);
                $('div.intros .col-sm-3').removeClass('col-sm-3').addClass('col-sm-4');
            }
        });
    }

    function getClearance() {
        var action = 'getClearance';
        var value = [];
        $('input[name="stores[]"]:checked').each(function() {
            if (!$(this).is(':disabled')) {
                value.push($(this).val());
            }
        });
        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelectOutlet'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {

                $('div.clearance').html(data);
                $('div.clearance .col-sm-3').removeClass('col-sm-3').addClass('col-sm-4');
            }
        });
    }

    function getDuration(startdate) {
        var action = 'getDuration';
        var eocdate = $('input[name="eocdate"]').val();
        console.log(startdate, eocdate)
        if (startdate != '') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/getSelect'); ?>",
                data: {
                    eocdate,
                    action,
                    startdate,
                },
                success: function(data) {
                    let response = JSON.parse(data);
                    if (response.result == 'failed') {
                        Swal.fire({
                            title: 'Required!',
                            text: response.message,
                            icon: 'warning',
                            iconColor: '#fd3550',
                            confirmButtonText: 'Ok',
                            confirmButtonColor: '#0d6efd',
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('input[name="startdate"]').val('');
                            }
                        });
                    } else {
                        $('input[name="duration"]').val(response.message);
                    }
                }
            });
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
            text: 'Please fillup the required fields.',
            icon: 'warning',
            iconColor: '#ffc107',
            confirmButtonText: 'Ok',
            confirmButtonColor: '#0d6efd',
            allowOutsideClick: false,
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
            allowOutsideClick: false,
        })
    }

    function alertInfo(message) {
        Swal.fire({
            title: 'Note!',
            text: message,
            icon: 'info',
            confirmButtonText: 'Ok',
            confirmButtonColor: '#0d6efd',
            allowOutsideClick: false,
        });
    }
</script>