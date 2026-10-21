<script>
    $(document).ready(function() {
        $('span.spanTooltip').tooltip();
        $('.datepicker').flatpickr();
        $('.select2').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            tags: true,
        });

        $('.select2clear').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: true
        });

        $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
        $('.form-check-input').change(function() {
            if (!$(this).is(':checked')) {
                $(this).css('border-color', 'rgb(108 117 125)');
            } else {
                $(this).css('border-color', '');
            }
        });

        $('.previousContract :input').prop('disabled', true); // disables all input,select

        <?php
        if ($menu == 'contract' && $file == 'renewContract') {
        ?>
            getIntros()
        <?php
        }
        ?>
        <?php
        if ($emp_id == 'today') {
        ?>
            eocList(store = '', day = 'today', month = '', year = '')
        <?php
        } else {
        ?>
            eocList(store = '', day = '', month = '', year = '')
        <?php
        }
        ?>

        $('div#generatePermitForm').on('hidden.bs.modal', function() {
            var renew = $('input[name="renew"]').val();
            var emp_id = $('input[name="emp_id"]').val();
            var record_no = $('input[name="record_no"]').val();
            if (renew) {
                renewalAlert(emp_id, record_no, 'current')
            }
        });

        $('div#generateContractForm').on('hidden.bs.modal', function() {
            var renew = $('input[name="renew"]').val();
            var emp_id = $('input[name="emp_id"]').val();
            var record_no = $('input[name="record_no"]').val();
            if (renew) {
                renewalAlert(emp_id, record_no, 'current')
            }
        });

        $("div#extendModal").on("hidden.bs.modal", function() {
            $(".dropdown-list").hide()
            $('input[name="name"]').val('');
            $('input[name="emp_id"]').val('');
            $('input[name="record_no"]').val('');
        });

        $("form#generatePermit").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var fields = [];
            let notRequired = ['specialDays', 'specialSched', 'dayOff']
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
                var value = $("input[name='" + field + "'],select[name='" + field + "']").val();

                if (value === '') {
                    if (!errMessage) {
                        alertRequired();
                        errMessage = true;
                    }
                    fieldsComplete = false;
                    $("input[name='" + field + "'],select[name='" + field + "']").css("border-color", "#dd4b39");
                    $("select[name='" + field + "']").next().find(".select2-selection").css("border-color", "#dd4b39");
                }

                $("input[name='" + field + "'],select[name='" + field + "'],.select2-selection").on("focus", function() {
                    $(this).css("border-color", "");
                });
            }
            if (fieldsComplete) {
                var cutOff = $('select[name="cutOff"]').val();
                formData.append('cutOff', cutOff);
                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm generating of work permit?',
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
                            text: 'Generating work permit...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/savePermit') ?>",
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    try {
                                        let response = JSON.parse(data);
                                        if (response.message == 'success') {
                                            var id = response.data;
                                            $.ajax({
                                                type: "POST",
                                                url: "<?= site_url('promo/generatePermit'); ?>",
                                                data: {
                                                    id
                                                },
                                                success: function(data) {
                                                    let response = JSON.parse(data);
                                                    // var id = response.data;
                                                    if (response.message == 'success') {
                                                        Swal.fire({
                                                            title: 'Success!',
                                                            text: 'Work Permit has been generated!',
                                                            icon: 'success',
                                                            iconColor: '#15ca20',
                                                            confirmButtonColor: '#3085d6',
                                                            confirmButtonText: 'Ok',
                                                            allowOutsideClick: false,
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {

                                                                window.open("<?= base_url('promo/viewPdf') ?>?" + response.id);
                                                            }
                                                        });

                                                    } else {
                                                        Swal.close();
                                                        errorAlert(response.message)
                                                    }
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

        $("form#generateContract").submit(function(e) {
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
                var value = $("input[name='" + field + "'],select[name='" + field + "']").val();

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

                var sss_ctc = [];
                $('input[name="sss_ctc[]"]:checked').each(function() {
                    sss_ctc.push($(this).val());
                });

                if (sss_ctc.length == 0) {

                    alertInfo('Please select SSS or Cedula!');
                } else {
                    Swal.fire({
                        title: 'Confirm?',
                        text: 'Confirm generating of contract?',
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
                                text: 'Generating contract...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            setTimeout(() => {
                                $.ajax({
                                    url: "<?= site_url('promo/saveContract') ?>",
                                    type: 'POST',
                                    data: formData,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    success: function(data) {
                                        try {
                                            let response = JSON.parse(data);
                                            if (response.message == 'success') {
                                                var id = response.data;
                                                $.ajax({
                                                    type: "POST",
                                                    url: "<?= site_url('promo/generateContract'); ?>",
                                                    data: {
                                                        id,
                                                    },
                                                    success: function(data) {
                                                        let response = JSON.parse(data);
                                                        if (response.message == 'success') {
                                                            Swal.fire({
                                                                title: 'Success!',
                                                                text: 'Contract has been generated!',
                                                                icon: 'success',
                                                                iconColor: '#15ca20',
                                                                confirmButtonColor: '#3085d6',
                                                                confirmButtonText: 'Ok',
                                                                allowOutsideClick: false,
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    window.open("<?= base_url('promo/viewPdf') ?>?" + response.id);
                                                                }
                                                            });

                                                        } else {
                                                            Swal.close();
                                                            errorAlert(response.message)
                                                        }
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

        $("form#uploadClearanceRenewal").submit(function(e) {
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
                var value = $("input[name='" + field + "']").val().trim();

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
                    title: 'Confirm?',
                    text: 'Confirm uploading of clearance?',
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
                            text: 'Uploading clearance...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/uploadClearanceRenewal') ?>",
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
                                                text: 'Clearance has been uploaded!',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $("div#uploadClearance").modal("hide");
                                                    window.location = "<?= base_url('promo/page/contract/renewContract/') ?>" + response.data;
                                                    //  checkRenewalReq(emp_id, record_no, 'extend')
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

        $("form#contractRenewal").submit(function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Please wait!',
                text: 'Checking form data...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    setTimeout(function() {
                        // var formData = new FormData(this);
                        var formData = new FormData(document.getElementById('contractRenewal'));
                        var fields = [];
                        let notRequired = ['agency_code', 'vendor_code', 'duration', 'w1', 'w2', 'comments', 'remarks']
                        for (let entry of formData.entries()) {

                            if (!notRequired.includes(entry[0])) {

                                fields.push(entry[0]);
                            }
                        }
                        // console.log(fields)
                        var fieldsComplete = true;
                        var errMessage = false;
                        for (var i = 0; i < fields.length; i++) {

                            var field = fields[i];
                            var value = $("input[name='" + field + "'],select[name='" + field + "'],textarea[name='" + field + "']").val();

                            if (value === '') {
                                console.log(field)
                                if (!errMessage) {
                                    alertRequired();
                                    errMessage = true;
                                }
                                fieldsComplete = false;
                                $("input[name='" + field + "'],select[name='" + field + "'],textarea[name='" + field + "']").css("border-color", "#dd4b39");
                                $("select[name='" + field + "']").next().find(".select2-selection").css("border-color", "#dd4b39");
                            }

                            $("input[name='" + field + "'],select[name='" + field + "'],textarea[name='" + field + "'],.select2-selection").on("focus", function() {
                                $(this).css("border-color", "");
                            });
                        }
                        console.log(fieldsComplete)
                        if (fieldsComplete) {
                            var promo_type = $('select[name="promo_type"]').val();
                            var storeCheck = [];
                            $('input[name="stores[]"]:checked').each(function() {
                                storeCheck.push($(this).val());
                            });
                            console.log(storeCheck)
                            console.log(storeCheck.length)
                            if (promo_type == 'ROVING' && storeCheck.length < 2) {

                                alertInfo('Please select atleast 2 store(s) for ROVING!')
                            } else if (promo_type == 'STATION' && storeCheck.length == 0) {

                                alertInfo('Please select a store!')
                            } else {
                                Swal.fire({
                                    title: 'Confirm!',
                                    text: 'Confim renewal of contract?',
                                    icon: 'question',
                                    iconColor: '#ffc107',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes',
                                    allowOutsideClick: false,
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $("button#submit").prop("disabled", true);
                                        Swal.fire({
                                            icon: 'info',
                                            title: 'Please wait!',
                                            text: 'Renewing contract...',
                                            allowOutsideClick: false,
                                            didOpen: () => {
                                                Swal.showLoading();
                                            }
                                        });
                                        setTimeout(() => {
                                            $.ajax({
                                                url: "<?= site_url('promo/renewContract') ?>",
                                                type: 'POST',
                                                data: formData,
                                                cache: false,
                                                contentType: false,
                                                processData: false,
                                                success: function(data) {
                                                    try {
                                                        let response = JSON.parse(data);
                                                        if (response.message == 'success') {
                                                            var emp_id = response.emp_id;
                                                            var record_no = response.record_no;
                                                            var contract = response.contract;
                                                            renewalAlert(emp_id, record_no, contract)
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
                    }, 2000);
                }
            });
        });

        $("form#transferRate").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var emp_id = $('input[name="emp_id"]').val()
            if (emp_id != '') {
                var details_id = [];
                $('input[name="details_id[]"]:checked').each(function() {
                    details_id.push($(this).val());
                });

                if (details_id.length == 0) {

                    alertInfo('Please select a Rate to Transfer!');
                } else {
                    Swal.fire({
                        title: 'Confirm!',
                        text: 'Confirm tranfer of rate?',
                        icon: 'question',
                        iconColor: '#ffc107',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('button.transferButton').prop('disabled', true)
                            Swal.fire({
                                icon: 'info',
                                title: 'Please wait!',
                                text: 'Renewing contract...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            setTimeout(() => {
                                $.ajax({
                                    url: "<?= site_url('promo/transferRateSave') ?>",
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
                                                    text: 'Rate has been transfered.',
                                                    icon: 'success',
                                                    iconColor: '#15ca20',
                                                    confirmButtonColor: '#3085d6',
                                                    confirmButtonText: 'Ok',
                                                    customClass: 'required_alert',
                                                    allowOutsideClick: false,
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location = "<?= base_url('promo/page/contract/transferRate') ?>";
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
            } else {

                alertInfo('The emp_id input is blank!');
            }
        });

        $("form#addBlacklist").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fields = [];
            let notRequired = ['bday', 'address']
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
                var value = $("textarea[name='" + field + "'],input[name='" + field + "']").val().trim();

                if (value === '') {

                    if (!errMessage) {

                        alertRequired()
                        errMessage = true;
                    }
                    fieldsComplete = false;
                    $("textarea[name='" + field + "'], input[name='" + field + "']").each(function() {
                        $(this).css("border-color", "#fd3550");
                    });
                    $("textarea[name='" + field + "'], input[name='" + field + "']").on("focus", function() {
                        $(this).css("border-color", "");
                    });
                }
            }
            if (fieldsComplete) {
                Swal.fire({
                    title: 'Confirm?',
                    text: 'Confirm the adding of blacklist data?',
                    icon: 'question',
                    iconColor: '#ffc107',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Please wait...',
                            text: 'Adding blacklist data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/addBlacklist') ?>",
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    try {
                                        let response = JSON.parse(data); // Attempt to parse JSON
                                        if (response.message === 'success') {
                                            $("div#addBlacklistForm").modal("hide");
                                            Swal.fire({
                                                title: 'Success!',
                                                text: 'Blacklist data has been added.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonText: 'Ok',
                                                confirmButtonColor: '#0d6efd',
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
    });

    let ajaxCallCount = 0;
    let ajaxRequest;

    function searchPromoContract(str) {
        $('div.permits,div.contracts,div.transferRate').html('');
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
            // console.log("AJAX call count: " + ajaxCallCount);

            ajaxRequest = $.ajax({
                type: "POST",
                url: "<?= site_url('promo/searchPromoContract'); ?>",
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
            $('div.permits,div.contracts,div.transferRate').html('');
            $(".dropdown-list").hide();
        });
    }

    function selectPromoContract(name, emp_id, record_no) {
        $(".dropdown-list").hide()
        $('input[name="name"]').val(name);
        var process = $('input[name="process"]').val();
        if (process == 'printPermit') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/selectPromoContract'); ?>",
                data: {
                    emp_id,
                    record_no,
                    process
                },
                beforeSend: function() {
                    loader_circle('permits')
                },
                success: function(data) {
                    $('div.permits').html(data);
                    $('.datepicker').flatpickr();
                    $('span.spanTooltip').tooltip();
                }
            });
        } else if (process == 'printContract') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/selectPromoContract'); ?>",
                data: {
                    emp_id,
                    record_no,
                    process
                },
                beforeSend: function() {
                    loader_circle('contracts')
                },
                success: function(data) {
                    $('div.contracts').html(data);
                    $('.datepicker').flatpickr();
                    $('span.spanTooltip').tooltip();
                }
            });
        } else if (process == 'extendContract') {
            $('input[name="emp_id"]').val(emp_id);
            $('input[name="record_no"]').val(record_no);
        } else if (process == 'transferRate') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/transferRateForm'); ?>",
                data: {
                    emp_id,
                    record_no,
                    process
                },
                beforeSend: function() {
                    loader_circle('transferRate')
                },
                success: function(data) {
                    $('div.transferRate').html(data);
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
        }
    }

    function generatePermitForm(emp_id, record_no, contract) {
        $('div#generatePermitForm').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#generatePermitForm").modal("show");
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/generatePermitForm'); ?>",
            data: {
                emp_id,
                record_no,
                contract
            },
            beforeSend: function() {
                loader_circle('generatePermitForm')
            },
            success: function(data) {
                $('div.generatePermitForm').html(data);
                $('.select2').select2({
                    theme: "bootstrap-5",
                    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                    placeholder: $(this).data('placeholder'),
                    dropdownParent: $('.generatePermitForm'),
                    allowClear: true,
                    matcher: function(params, data) {
                        if ($.trim(params.term) === '') return data;

                        // Clean search input and option text
                        const searchTerms = params.term
                            .toLowerCase()
                            .replace(/am|pm/gi, '')
                            .trim()
                            .split(/\s+/); // e.g. ['8:00', '12:00']

                        const optionText = data.text.toLowerCase().replace(/am|pm/gi, '');

                        // Match if all search terms are in option text
                        const allMatch = searchTerms.every(term => optionText.includes(term));
                        return allMatch ? data : null;
                    }
                });
            }
        });
    }

    function generateContractForm(emp_id, record_no, contract) {
        $('div#generateContractForm').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#generateContractForm").modal("show");
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/generateContractForm'); ?>",
            data: {
                emp_id,
                record_no,
                contract
            },
            beforeSend: function() {
                loader_circle('generateContractForm')
            },
            success: function(data) {
                $('div.generateContractForm').html(data);
                $('.datepicker').flatpickr();
                // code for the calendar year to be editable
                document.addEventListener('focusin', (e) => {
                    if (e.target.closest(".flatpickr-calendar") !== null) {
                        e.stopImmediatePropagation();
                    }
                });
            }
        });
    }

    function eocFilter() {
        var store = $('select[name="store"]').val()
        var day = $('select[name="day"]').val()
        var month = '';
        var year = '';
        if (day == '') {
            month = $('select[name="month"]').val()
            year = $('select[name="year"]').val()
        }

        console.log(store, day, month, year)
        eocList(store, day, month, year)
        $("div#eocFilter").modal("hide");
    }

    function searhContractWitness(str, witness) {
        if (str.trim() != '') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/searhContractWitness'); ?>",
                data: {
                    str,
                    witness
                },
                success: function(data) {

                    $('.' + witness).show();
                    $('.' + witness).html(data);
                }
            });
        } else {
            $('.' + witness).hide();
        }
    }

    function selectContractWitness(name, witness) {
        if (name.trim() != '') {
            $('input[name="' + witness + '"]').val(name);
        }
        $('div.' + witness).hide()
    }

    function showContractElements(value) {
        if (value == 'cedula') {
            $('input[name="ctc"]').show();
            $('div.issuedat').show();
            $('div.issuedon').show();
            $('input[name="sss"]').hide();
            $('input[name="ctc"]').prop('disabled', false);
            $('input[name="sss"]').prop('disabled', true);
            $('input[name="issuedat"]').prop('disabled', false);
            $('input[name="issuedon"]').prop('disabled', false);
        } else {
            $('input[name="sss"]').show();
            $('div.issuedat').show();
            $('div.issuedon').hide();
            $('input[name="ctc"]').hide();
            $('input[name="ctc"]').prop('disabled', true);
            $('input[name="sss"]').prop('disabled', false);
            $('input[name="issuedat"]').prop('disabled', false);
            $('input[name="issuedon"]').prop('disabled', true);
        }
    }

    function eocList(store, day, month, year) {
        var dataTable = $("#eocList").DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "<?= site_url('promo/eocList'); ?>",
                type: "POST",
                data: {
                    store,
                    day,
                    month,
                    year
                }
            },
            "order": [],
            "columnDefs": [{
                "targets": [3, 4, 5],
                "orderable": false,

            }, {
                "targets": [4, 5],
                "className": "text-center",
            }],
            "drawCallback": function(settings) {
                $('span.eocStrEpas').tooltip();
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
                    details_id: details_id,
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

    function addBl(emp_id) {

        $('div#addBlacklistForm').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#addBlacklistForm").modal("show");
        $.ajax({
            url: "<?= site_url('promo/addBlacklistForm') ?>",
            type: 'POST',
            data: {
                emp_id
            },
            beforeSend: function() {
                loader_circle('addBlacklistForm')
            },
            success: function(data) {
                $("div.addBlacklistForm").html(data);
                $(".datepicker").flatpickr();
                //code for the calendar year to be editable
                document.addEventListener('focusin', (e) => {
                    if (e.target.closest(".flatpickr-calendar") !== null) {
                        e.stopImmediatePropagation();
                    }
                });
            }
        });
    }

    function checkRenewalReq(emp_id, record_no, process = 'eoc') {
        Swal.fire({
            icon: 'info',
            title: 'Please wait!',
            text: 'Checking requirements...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        setTimeout(() => {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/checkRenewalReq'); ?>",
                data: {
                    emp_id,
                    record_no,
                    process
                },
                success: function(data) {
                    let response = JSON.parse(data);
                    if (response.message == 'success') {
                        // if (response.secured) {
                        var type = 'Renewal';
                        if (process == 'extend') {
                            type = 'Extension';
                        }
                        Swal.fire({
                            title: 'Success!',
                            text: 'Requirements has been checked. Proceed to Contract ' + type + '?',
                            icon: 'success',
                            iconColor: '#15ca20',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Yes',
                            showCancelButton: true,
                            cancelButtonColor: '#d33',
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "<?= base_url('promo/page/contract/renewContract/') ?>" + response.data;
                            }
                        });
                    } else if (response.message == 'signoff') {
                        alertInfo('Sign off appraisal rating first.')
                    } else if (response.message == 'fail') {
                        errorAlert('Employee have a failed EPAS.')
                    } else {
                        if (response.securedAll) {
                            Swal.fire({
                                title: 'Requirement lacking!',
                                text: 'Requirements has been checked. Proceed to Uploading of Clearance.',
                                icon: 'info',
                                iconColor: '#ffc107',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Yes',
                                showCancelButton: true,
                                cancelButtonColor: '#d33',
                                allowOutsideClick: false,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('div#uploadClearanceForm').modal({
                                        backdrop: 'static',
                                        keyboard: false,
                                    });
                                    $("div#uploadClearanceForm").modal("show");

                                    $.ajax({
                                        type: "POST",
                                        url: "<?= site_url('promo/uploadClearanceForm'); ?>",
                                        data: {
                                            emp_id,
                                            record_no
                                        },
                                        beforeSend: function() {
                                            loader_circle('uploadClearanceForm')
                                        },
                                        success: function(data) {
                                            $('div.uploadClearanceForm').html(data);

                                        }
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Secured clearance lacking!',
                                text: 'Please secure clearance for: ' + response.stores,
                                icon: 'info',
                                iconColor: '#ffc107',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok',
                                allowOutsideClick: false,
                            })
                        }
                    }
                }
            });
        }, 2000);
    }

    function imgPreview(input) {
        var uniqueId = input.id;
        var preview = $('#imagePreview_' + uniqueId);
        var clearButton = $('#clearButton_' + uniqueId);

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(preview).css({
                    'display': 'block',
                    'width': '100%',
                    'height': '200px',
                    // 'object-fit': 'cover' 
                }).attr('src', e.target.result);
                $(clearButton).css('display', 'block');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearImageInput(uniqueId) {
        $('input#' + uniqueId).val('');
        $('#imagePreview_' + uniqueId).css('display', 'none');
        $('#clearButton_' + uniqueId).css('display', 'none');
    }

    function renewalAlert(emp_id, record_no, contract) {
        $('input[name="record_no"]').val(record_no);
        Swal.fire({
            title: 'Success!',
            text: 'Contract has been renewed! You can now generate Contract and Permit.',
            icon: 'success',
            iconColor: '#15ca20',
            showCancelButton: true,
            confirmButtonText: 'Go to Contract',
            cancelButtonText: 'Go to Permit',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#3085d6',
            showCloseButton: true,
            closeButtonAriaLabel: 'Exit',
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                generateContractForm(emp_id, record_no, contract)
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                generatePermitForm(emp_id, record_no, contract)
            } else if (result.dismiss === Swal.DismissReason.close) {
                window.location = "<?= base_url('promo/page/promo/profile/') ?>" + emp_id;
            }
        });
    }

    function getCompany(value) {
        var action = 'getCompany';
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelectRenewal'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {
                $('select[name="promo_company"]').html(data);
            }
        });
    }

    function getProduct(value) {
        var action = 'getProduct';
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelectRenewal'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {
                $('select[name="product[]"]').empty();
                $('select[name="product[]"]').html(data);
            }
        });
    }

    function getStores(value, promo_type) {
        var action = 'getStores';
        var emp_id = $('input[name="emp_id"]').val();
        var record_no = $('input[name="record_no"]').val();
        var contract = 'current';

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelectRenewal'); ?>",
            data: {
                value,
                action,
                promo_type,
                emp_id,
                record_no,
                contract
            },
            success: function(data) {
                $('ul.stores').html(data);
                $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
                $('.form-check-input').change(function() {
                    if (!$(this).is(':checked')) {
                        $(this).css('border-color', 'rgb(108 117 125)');
                    } else {
                        $(this).css('border-color', '');
                    }
                });
                getIntros()
            }
        });
    }

    function getIntros() {
        var action = 'getIntros';
        var value = [];
        $('input[name="stores[]"]:checked').each(function() {
            value.push($(this).val());
        });
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelectRenewal'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {
                $('div.intros').html(data);
            }
        });
    }

    function getDepartment() {
        var action = 'getDepartment';
        var value = [];
        $('input[name="stores[]"]:checked').each(function() {
            value.push($(this).val());
        });
        getIntros()
        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelectRenewal'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {
                $('select[name="promo_department"]').html(data);
            }
        });
    }

    function getVendor(value) {
        var action = 'getVendor';

        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelectRenewal'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {
                $('select[name="vendor_code"]').html(data);
            }
        });
    }

    function getDuration(eocdate) {
        var action = 'getDuration';
        var startdate = $('input[name="startdate"]').val();
        var renewalType = $('input[name="renewalType"]').val();
        console.log(eocdate)
        if (eocdate === 'startdate') {
            $('input[name="eocdate"]').val('');
        } else {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/getSelectRenewal'); ?>",
                data: {
                    eocdate,
                    action,
                    startdate,
                    renewalType
                },
                success: function(data) {
                    let response = JSON.parse(data);
                    if (response.result == 'failed') {
                        $('input[name="eocdate"]').val('');
                        $('input[name="duration"]').val('');
                        alertInfo(response.message)
                    } else {
                        if (renewalType == 'extend') {
                            if (response.months != 0) {
                                $('input[name="eocdate"]').val('');
                                $('input[name="duration"]').val('');
                                alertInfo('The contract duration (' + response.message + ') exceeds the allowed (15 days) for Contract Extension.')
                            } else if (response.months == 0 && response.days > 15) {
                                $('input[name="eocdate"]').val('');
                                $('input[name="duration"]').val('');
                                alertInfo('The contract duration (' + response.message + ') exceeds the allowed (15 days) for Contract Extension.')
                            } else {
                                $('input[name="duration"]').val(response.message);
                            }
                        } else {
                            $('input[name="duration"]').val(response.message);
                        }

                    }
                }
            });
        }
    }

    function getDutydays(value) {
        var action = 'getDutydays';

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelectRenewal'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {
                $('input[name="dutyDays"]').val(data);
            }
        });
    }

    function getCompDuration(value) {
        if (value == 'Seasonal') {
            $('div.getCompDuration').show();
            $('input[name="company_duration"]').prop('disabled', false);
        } else {
            $('div.getCompDuration').hide();
            $('input[name="company_duration"]').prop('disabled', true);
        }
    }

    function proceedExtend() {
        var emp_id = $('input[name="emp_id"]').val();
        var record_no = $('input[name="record_no"]').val();
        var name = $('input[name="extendSearch"]').val();
        if (emp_id != '' && record_no != '' && name != '') {
            Swal.fire({
                title: 'Confirm!',
                text: 'Confirm extention of contract?',
                icon: 'question',
                iconColor: '#ffc107',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    checkRenewalReq(emp_id, record_no, 'extend')
                }
            })
        } else {
            alertRequired()
            $("input[name='extendSearch']").css("border-color", "#dd4b39");
            $("input[name='extendSearch']").on("focus", function() {
                $(this).css("border-color", "");
            });
        }
    }

    function chkStore(emp_id, record_no) {
        var details_id = [];
        $('input[name="details_id[]"]:checked').each(function() {
            details_id.push($(this).val());
        });

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/checkStores'); ?>",
            data: {
                emp_id,
                record_no,
                details_id
            },
            success: function(data) {
                let response = JSON.parse(data);
                if (response.message == 'success') {
                    if (!response.data) {
                        $('#' + response.id).prop('checked', false);
                        alertInfo('The selected Rate Store does not exist in the Current Contract!')
                        $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
                        $('.form-check-input').change(function() {
                            if (!$(this).is(':checked')) {
                                $(this).css('border-color', 'rgb(108 117 125)');
                            } else {
                                $(this).css('border-color', '');
                            }
                        });
                    }

                }
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