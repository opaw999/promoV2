<script>
    $(document).ready(function() {

        var fileValue = '<?= $file; ?>';
        if (fileValue == 'listClearance') {
            var month = '<?= isset($_GET['month']) ? $_GET['month'] :  date('m'); ?>';
            var year = '<?= isset($_GET['year']) ? $_GET['year'] :  date('Y'); ?>';
            filter_year(year, month)
        }

        $("form#updateClearance").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fields = [];
            let notRequired = ['claimant', 'relation', 'death_certificate', 'authorization_letter']
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
                var value = $("select[name='" + field + "'],input[name='" + field + "']").val();

                if (value.trim() === '') {

                    if (!errMessage) {

                        alertRequired()
                        errMessage = true;
                    }
                    fieldsComplete = false;
                    $("select[name='" + field + "'], input[name='" + field + "']").each(function() {
                        $(this).css("border-color", "#fd3550");
                    });
                    $("select[name='" + field + "'], input[name='" + field + "']").on("focus", function() {
                        $(this).css("border-color", "");
                    });
                }
            }
            if (fieldsComplete) {

                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm the update of clearance data?',
                    icon: 'question',
                    iconColor: '#ffc107',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {

                        Swal.fire({
                            icon: 'info',
                            title: 'Please wait...',
                            text: 'Updating clearance data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/clearanceUpdate') ?>",
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    try {
                                        let response = JSON.parse(data); // Attempt to parse JSON
                                        if (response.message === 'success') {
                                            $("div#clearanceUpdateForm").modal("hide");
                                            Swal.fire({
                                                title: 'Success!',
                                                text: 'Clearance data has been updated.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonText: 'Ok',
                                                confirmButtonColor: '#0d6efd',
                                                allowOutsideClick: false,
                                            }).then(() => {
                                                location.reload();
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

        $("form#reprintClearance").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fields = [];
            for (let entry of formData.entries()) {

                fields.push(entry[0]);
            }
            console.log(fields)
            var fieldsComplete = true;
            var errMessage = false;
            for (var i = 0; i < fields.length; i++) {

                var field = fields[i];
                var value = $("textarea[name='" + field + "'],input[name='" + field + "'],select[name='" + field + "']").val().trim();

                if (value === '') {
                    if (!errMessage) {
                        alertRequired();
                        errMessage = true;
                    }
                    fieldsComplete = false;
                    $("textarea[name='" + field + "'], input[name='" + field + "'],select[name='" + field + "']").css("border-color", "#dd4b39");
                } else {
                    $("textarea[name='" + field + "'], input[name='" + field + "'],select[name='" + field + "']").css("border-color", "");
                }

                $("textarea[name='" + field + "'], input[name='" + field + "'],select[name='" + field + "']").on("focus", function() {
                    $(this).css("border-color", "");
                });
            }
            if (fieldsComplete) {

                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm the reprint of clearance?',
                    icon: 'question',
                    iconColor: '#ffc107',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Please wait...',
                            text: 'Generating clearance data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/reprintClearance') ?>",
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    try {
                                        let response = JSON.parse(data);
                                        if (response.message == 'success') {

                                            showAlert();
                                            var alertCounter = 0;

                                            function showAlert() {
                                                Swal.fire({
                                                    title: 'Success!',
                                                    text: "You can now print the Clearance.",
                                                    icon: 'success',
                                                    iconColor: '#15ca20',
                                                    showCancelButton: (alertCounter > 0) ? true : false,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: (alertCounter > 0) ? 'Print Again' : 'Print',
                                                    cancelButtonText: 'Close',
                                                    allowOutsideClick: false
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        console.log(response.data)
                                                        window.open("<?= base_url('promo/viewPdfClearance') ?>?" + response.id);
                                                        alertCounter++;
                                                        showAlert();
                                                    } else {
                                                        location.reload();
                                                    }
                                                });
                                            }
                                        } else if (response.message == 'print') {
                                            Swal.close();
                                            generateClearance(response.reason, response.emp_id, response.scdetails_id)
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

        $("form#secureClearance").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fields = [];
            let notRequired = ['claimant', 'relation', 'death_certificate', 'authorization_letter']

            var reasonField = $("select[name='reason']");
            if (reasonField.prop("disabled")) {
                formData.append("reason", reasonField.val());
            }

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
                    $("input[name='" + field + "'], select[name='" + field + "']").css("border-color", "#dd4b39");
                } else {
                    $("input[name='" + field + "'], select[name='" + field + "']").css("border-color", "");
                }
                $("input[name='" + field + "'], select[name='" + field + "']").on("focus", function() {
                    $(this).css("border-color", "");
                });
            }
            if (fieldsComplete) {

                Swal.fire({
                    title: 'Confirm!',
                    icon: 'question',
                    iconColor: '#ffc107',
                    text: 'Confirm securing of clearance?',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Please wait...',
                            text: 'Securing clearance...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/secureClearance') ?>",
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    try {
                                        let response = JSON.parse(data); // Attempt to parse JSON
                                        if (response.message === 'success') {
                                            if (response.reason != 'Deceased') {
                                                showAlert();
                                                var alertCounter = 0;

                                                function showAlert() {
                                                    Swal.fire({
                                                        title: 'Success!',
                                                        text: 'Clearance has been secured.',
                                                        icon: 'success',
                                                        iconColor: '#15ca20',
                                                        showCancelButton: (alertCounter > 0) ? true : false,
                                                        confirmButtonColor: '#3085d6',
                                                        cancelButtonColor: '#d33',
                                                        confirmButtonText: (alertCounter > 0) ? 'Print Again' : 'Print Clearance',
                                                        cancelButtonText: 'Close',
                                                        allowOutsideClick: false
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            generateClearance(response.reason, response.emp_id, response.scdetails_id)
                                                            alertCounter++;
                                                            showAlert();
                                                        } else {
                                                            location.reload();
                                                        }
                                                    });
                                                }
                                            } else {
                                                Swal.fire({
                                                    title: 'Success!',
                                                    text: 'Clearance data has been secured.',
                                                    icon: 'success',
                                                    iconColor: '#15ca20',
                                                    confirmButtonText: 'Ok',
                                                    confirmButtonColor: '#0d6efd',
                                                    allowOutsideClick: false,
                                                }).then(() => {
                                                    location.reload();
                                                });
                                            }
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

        $("form#uploadClearance").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fields = [];

            for (let entry of formData.entries()) {
                fields.push(entry[0]);
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
                    $("input[name='" + field + "'], select[name='" + field + "']").css("border-color", "#dd4b39");
                } else {
                    $("input[name='" + field + "'], select[name='" + field + "']").css("border-color", "");
                }
                $("input[name='" + field + "'], select[name='" + field + "']").on("focus", function() {
                    $(this).css("border-color", "");
                });
            }
            if (fieldsComplete) {

                Swal.fire({
                    title: 'Confirm!',
                    icon: 'question',
                    iconColor: '#ffc107',
                    text: 'Confirm upload of clearance?',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Please wait...',
                            text: 'Uploading clearance...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/uploadClearance') ?>",
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
                                                text: 'Clearance has been uploaded.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonText: 'Ok',
                                                confirmButtonColor: '#0d6efd',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    location.reload();
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

    function clearanceList(year, month) {
        var dataTable = $("#clearance").DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "<?= site_url('guest/clearanceList'); ?>",
                type: "POST",
                data: {
                    month,
                    year
                }
            },
            "order": [],
            "columnDefs": [{
                "targets": [0, 2, 3, 4, 5, 6, 7],
                "orderable": false,

            }, {
                "targets": [7],
                "className": "text-center",
            }],
        });
    }

    function filter_month(month) {
        var year = $("select[name='year']").val();
        clearanceList(year, month)
    }

    function filter_year(year, month) {
        if (!month) {
            var month = $("select[name='month']").val();
        }
        $.ajax({
            url: "<?= site_url('guest/filterYear') ?>",
            type: 'POST',
            data: {
                month,
                year
            },
            success: function(data) {
                $('select[name="month"]').html(data);
                clearanceList(year, month)
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
                url: "<?= site_url('guest/viewAppraisal'); ?>",
                data: {
                    details_id
                },
                beforeSend: function() {
                    loader_circle('viewAppraisal')
                },
                success: function(data) {
                    $('div.viewAppraisal').html(data);
                }
            });
        }
    }

    function clearanceUpdateForm(scpr_id) {
        $('div#clearanceUpdateForm').modal({
            backdrop: 'static',
            keyboard: false,
        });

        $("div#clearanceUpdateForm").modal("show");
        $('button.updateClearanceButton').prop('disabled', true);
        $.ajax({
            url: "<?= site_url('promo/clearanceUpdateForm') ?>",
            type: 'POST',
            data: {
                scpr_id
            },
            beforeSend: function() {
                loader_circle('clearanceUpdateForm')
            },
            success: function(data) {
                $("div.clearanceUpdateForm").html(data);
                $('.datepicker').flatpickr();
                //code for the calendar year to be editable
                document.addEventListener('focusin', (e) => {
                    if (e.target.closest(".flatpickr-calendar") !== null) {
                        e.stopImmediatePropagation();
                    }
                });
            }
        })
    }

    function clearanceDetails(reason, scpr_id) {
        $('div#clearanceDetails').modal({
            backdrop: 'static',
            keyboard: false,
        });

        $("div#clearanceDetails").modal("show");

        $.ajax({
            url: "<?= site_url('guest/clearanceDetails') ?>",
            type: 'POST',
            data: {
                scpr_id,
                reason
            },
            beforeSend: function() {
                loader_circle('clearanceDetails')
            },
            success: function(data) {
                $("div.clearanceDetails").html(data);
            }
        });
    }

    function changeReason(reason, curReason, scpr_id) {
        if (reason == curReason) {
            alertInfo('Select a different reason to update.');
            $('div.additionalReq').html('');
            $('select[name="reason"]').val(curReason);
            $('button.updateClearanceButton').prop('disabled', true);
        } else {
            $('button.updateClearanceButton').prop('disabled', false);
            $.ajax({
                url: "<?= site_url('promo/changeReason') ?>",
                type: 'POST',
                data: {
                    reason,
                    curReason,
                    scpr_id
                },
                beforeSend: function() {
                    loader_circle('additionalReq')
                },
                success: function(data) {
                    $('div.additionalReq').html(data);
                    $('.datepicker').flatpickr();
                    //code for the calendar year to be editable
                    document.addEventListener('focusin', (e) => {
                        if (e.target.closest(".flatpickr-calendar") !== null) {
                            e.stopImmediatePropagation();
                        }
                    });
                }
            });
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

    function generateClearance(reason, emp_id, scdetails_id) {
        $.ajax({
            url: "<?= site_url('promo/generateClearance') ?>",
            type: 'POST',
            data: {
                reason,
                emp_id,
                scdetails_id
            },
            success: function(data) {
                let response = JSON.parse(data);
                if (response.message == 'success') {
                    window.open("<?= base_url('promo/viewPdfClearance') ?>?" + response.id);
                }
            }
        });
    }

    let ajaxCallCount = 0;
    let ajaxRequest;

    function searchPromoClearance(str) {
        var process = $('input[name="clearanceProcess"]').val();
        $('select[name="reason"]').val('').prop('disabled', true);
        $("input[name='reason']").val('');
        $("input[name='emp_id']").val('');
        $("input[name='record_no']").val('');
        $("input[name='promo_type']").val('');
        $("input[name='eocdate']").val('');
        $("input[name='scpr_id']").val('');
        $("input[name='stat']").val('');
        $("input[name='epas']").val('');
        $("select[name='stores']").html('<option value="">Select Store</option>');
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
            // console.log("AJAX call count: " + ajaxCallCount);

            ajaxRequest = $.ajax({
                type: "POST",
                url: "<?= site_url('promo/searchPromoClearance'); ?>",
                data: {
                    str,
                    process
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
            $(".dropdown-list").hide();
            $('.requirement').html('');
            $('select[name="reason"]').val('').prop('disabled', true);
            $("input[name='reason']").val('');
            $("input[name='emp_id']").val('');
            $("input[name='record_no']").val('');
            $("input[name='promo_type']").val('');
            $("input[name='eocdate']").val('');
            $("input[name='scpr_id']").val('');
            $("input[name='stat']").val('');
            $("select[name='stores']").html('<option value="">Select Store</option>');
        });
    }

    function selectPromoClearance(name, emp_id, record_no) {
        $(".dropdown-list").hide()
        $("select[name='reason']").val('').prop('disabled', true);
        var process = $("input[name='clearanceProcess']").val();
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/selectPromoClearance'); ?>",
            data: {
                emp_id,
                record_no,
                process
            },
            success: function(data) {
                let response = JSON.parse(data);
                if (response.message == 'success') {

                    if (process == 'secure') {
                        if (response.secured) {
                            if (response.chk) {

                                var year = response.year;
                                var month = response.month;
                                Swal.fire({
                                    title: 'Note!',
                                    text: name + ' have secured all clearance!',
                                    icon: 'info',
                                    confirmButtonText: 'View Clearance',
                                    showCancelButton: true,
                                    confirmButtonColor: '#0d6efd',
                                    allowOutsideClick: false
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.open("<?= base_url('promo/page/clearance/listClearance') ?>?year=" + year + "&month=" + month, '_blank');
                                    }
                                    $("input[name='promo_type']").val('');
                                    $("input[name='name']").val('');
                                    $("input[name='emp_id']").val('');
                                    $("input[name='record_no']").val('');
                                });
                            } else {
                                $("select[name='reason']").val(response.reason).prop('disabled', true);
                                $("select[name='stores']").html(response.stores);
                                $("input[name='eocdate']").val(response.eocdate);
                                selectReason(response.reason)
                            }
                        } else {
                            $("select[name='reason']").val('').prop('disabled', false);
                            $("select[name='stores']").html(response.stores)
                            $("input[name='eocdate']").val(response.eocdate);
                        }

                        $("input[name='promo_type']").val(response.promo_type);
                        $("input[name='name']").val(name);
                        $("input[name='emp_id']").val(emp_id);
                        $("input[name='record_no']").val(record_no);
                        $(".dropdown-list").hide()
                    } else if (process == 'reprint') {
                        $("select[name='stores']").html(response.stores);
                        $("input[name='reason']").val(response.reason);
                        $("input[name='promo_type']").val(response.promo_type);
                        $("input[name='name']").val(name);
                        $("input[name='emp_id']").val(emp_id);
                        $("input[name='record_no']").val(record_no);
                        $(".dropdown-list").hide()
                    } else {
                        $("input[name='stat']").val(response.stat);
                        $("select[name='stores']").html(response.stores)
                        $("input[name='promo_type']").val(response.promo_type);
                        $("input[name='scpr_id']").val(response.scpr_id);
                        $("input[name='name']").val(name);
                        $("input[name='emp_id']").val(emp_id);
                        $("input[name='record_no']").val(record_no);
                        $(".dropdown-list").hide()
                    }
                } else {
                    alert(data);
                }
            }
        });
    }

    function selectReason(reason) {
        var eocdate = $("input[name='eocdate']").val();
        $.ajax({
            url: "<?= site_url('promo/clearanceRequirement') ?>",
            type: 'POST',
            data: {
                reason,
                eocdate
            },
            beforeSend: function() {
                loader_circle('requirement')
            },
            success: function(data) {
                $("div.requirement").html(data);
                $('.datepicker').flatpickr();
                if (reason == 'Deceased') {
                    $("select[name='stores']").prop('disabled', true);
                } else {
                    $("select[name='stores']").prop('disabled', false);
                }
            }
        });
    }

    function browseEpas(stores) {

        var emp_id = $("input[name='emp_id']").val();
        var record_no = $("input[name='record_no']").val();
        $("input[name='epas']").val('');
        if (stores != '') {

            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/browseEpas'); ?>",
                data: {
                    stores,
                    emp_id,
                    record_no,
                },
                success: function(data) {

                    let response = JSON.parse(data);
                    if (response.message == 'success') {

                        $("input[name='epas']").val(response.numrate);
                    } else if (response.message == 'seasonal') {
                        $("input[name='epas']").val(response.text);
                    } else {
                        $("select[name='stores']").val('')
                        $("input[name='epas']").val('');
                        Swal.fire({
                            title: response.alert === 'noepas' ? 'No EPAS!' : 'Sign Off!',
                            text: response.text,
                            icon: response.alert === 'noepas' ? 'error' : 'warning',
                            iconColor: response.alert === 'noepas' ? '#dc3545' : '#ffc107',
                            confirmButtonText: 'Ok',
                            confirmButtonColor: '#0d6efd',
                            allowOutsideClick: false,
                        })
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
            text: 'Please fillup the required fields!',
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