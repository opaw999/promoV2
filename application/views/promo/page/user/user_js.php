<script>
    $(document).ready(function() {
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
            tags: true,
        });

        promoUserAccessRoles()
        promoUserAccounts()
        promoInchargeAccounts()

        $("form#userAccountForm").submit(function(e) {
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
                    text: 'Confirm adding new user account.',
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
                            text: 'Adding user account.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/save_userAccount') ?>",
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
                                                text: 'User account has been added.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location = "<?= base_url('promo/page/user/addPromoUser') ?>";
                                                }
                                            });
                                        } else if (response.message == 'exist') {
                                            $('input[name="username"]').css('border-color', '#dd4b39');
                                            $('input[name="username"]').prop('readonly', false);
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
        });

        $("form#inchargeAccountForm").submit(function(e) {
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
                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm adding promo incharge account.',
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
                            text: 'Adding promo incharge account.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/addPromoInchargeAccount') ?>",
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
                                                text: 'Promo incharge account has been added.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location = "<?= base_url('promo/page/user/addPromoIncharge') ?>";
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

        $("form#updateUsername").submit(function(e) {
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
                var u1 = $("input[name='username1']").val();
                var u2 = $("input[name='username2']").val();
                if (u1 != u2) {
                    $("input[name='username1']").css("border-color", "#dd4b39");
                    $("input[name='username2']").css("border-color", "#dd4b39");
                    alertInfo('New username does not match.')
                } else {
                    Swal.fire({
                        title: 'Confirm!',
                        text: 'Confirm updating of username.',
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
                                text: 'Updating username.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            setTimeout(() => {
                                $.ajax({
                                    url: "<?= site_url('promo/updateUserAccount') ?>",
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
                                                    text: response.report,
                                                    icon: 'success',
                                                    iconColor: '#15ca20',
                                                    confirmButtonColor: '#3085d6',
                                                    confirmButtonText: 'Ok',
                                                    allowOutsideClick: false,
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location = "<?= base_url('promo/logout') ?>";
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
        });

        $("form#updatePassword").submit(function(e) {
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
                var u1 = $("input[name='password1']").val();
                var u2 = $("input[name='password2']").val();
                if (u1 != u2) {
                    $("input[name='password1']").css("border-color", "#dd4b39");
                    $("input[name='password2']").css("border-color", "#dd4b39");
                    alertInfo('New password does not match.')
                } else {
                    Swal.fire({
                        title: 'Confirm!',
                        text: 'Confirm updating of password.',
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
                                text: 'Updating password.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            setTimeout(() => {
                                $.ajax({
                                    url: "<?= site_url('promo/updateUserAccount') ?>",
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
                                                    text: response.report,
                                                    icon: 'success',
                                                    iconColor: '#15ca20',
                                                    confirmButtonColor: '#3085d6',
                                                    confirmButtonText: 'Ok',
                                                    allowOutsideClick: false,
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        window.location = "<?= base_url('promo/logout') ?>";
                                                    }
                                                });
                                            } else if (response.message == 'mismatch') {
                                                $("input[name='password']").css("border-color", "#dd4b39");
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
        });
    });

    function searchPromoUsers(str) {
        var process = $('input[name="process"]').val();
        $("input[name='emp_id']").val('');
        $("input[name='username']").val('').prop('readonly', true);
        $("input[name='password']").val('').prop('readonly', true).attr('type', 'password');
        $('input[name="viewPassword"],input[name="setPassword"]').prop('checked', false);
        $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
        $('select[name="usertype"]').val('');
        $('select[name="user_status"]').val('');
        if (str.trim() != '') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/searchPromoUsers'); ?>",
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
            $("input[name='emp_id']").val('');
            $("input[name='username']").val('').prop('readonly', true);
            $("input[name='password']").val('').prop('readonly', true).attr('type', 'password');
            $('input[name="viewPassword"],input[name="setPassword"]').prop('checked', false);
            $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
            $('select[name="usertype"]').val('');
            $('select[name="user_status"]').val('');
        });
    }

    function selectPromoUsers(name, emp_id, record_no) {
        $(".dropdown-list").hide();
        var process = $('input[name="process"]').val();
        Swal.fire({
            icon: 'info',
            title: 'Please wait...',
            text: 'Checking for existing accounts.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        setTimeout(() => {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/selectPromoUsers'); ?>",
                data: {
                    emp_id,
                    process
                },
                success: function(data) {
                    try {
                        let response = JSON.parse(data);
                        if (response.message == 'success') {
                            Swal.close();
                            $('input[name="name"]').val(name);
                            $('input[name="emp_id"]').val(emp_id);
                            $('input[name="username"]').val(emp_id).prop('readonly', false);
                            $("input[name='password']").prop('readonly', false);
                        } else {
                            Swal.close();
                            alertInfo('Employee have an existing Account.')
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

    function passwordFunc(action) {
        console.log(action)
        if (action == 'viewPassword') {

            let passwordFields = $('input[name="password"], input[name="password1"], input[name="password2"]');
            let type = $('input[name="' + action + '"]').prop('checked') ? 'text' : 'password';
            passwordFields.attr('type', type);

        } else {
            if ($('input[name="' + action + '"]').prop('checked')) {
                $('input[name="password"]').val('Hrms2014');
                $('input[name="password"]').prop('readonly', true)
                $('input[name="password"]').addClass('readonly')
                $('input[name="password"]').css('border-color', '');
            } else {
                $('input[name="password"]').val('');
                $('input[name="password"]').prop('readonly', false)
                $('input[name="password"]').removeClass('readonly')
            }
        }
    }

    function promoUserAccounts() {
        var dataTable = $("#promoUserAccounts").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/managePromoUserAccounts'); ?>",
                type: "POST"
            },
            "order": [],
            "columnDefs": [{
                    "targets": [1, 2],
                    "orderable": false,
                },
                {
                    "targets": [3, 4, 5],
                    "className": "text-center",
                    "orderable": false,
                }
            ],
        });
    }

    function userAccount(process, id) {
        if (process == 'activate') {
            var msg1 = 'Confirm activating of promo user account.';
            var msg2 = 'Activating promo user account.';
            var msg3 = 'Promo user account has been activated.';
        } else if (process == 'deactivate') {
            var msg1 = 'Confirm deactivating of promo user account.';
            var msg2 = 'Deactivating promo user account.';
            var msg3 = 'Promo user account has been deactivated';
        } else if (process == 'reset') {
            var msg1 = 'Confirm resetting of promo user account password. Default Password: Hrms2014';
            var msg2 = 'Resetting promo user account password.';
            var msg3 = 'Promo user account password has been reset.';
        } else if (process == 'delete') {
            var msg1 = 'Confirm deleting of promo user account.';
            var msg2 = 'Deleting promo user account.';
            var msg3 = 'Promo user account has been deleted.';
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
                        url: "<?= site_url('promo/userAccount'); ?>",
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
                                        text: msg3,
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location = "<?= base_url('promo/page/user/promoUsers') ?>";
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

    function promoInchargeAccounts() {
        var dataTable = $("#promoInchargeAccounts").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/managePromoInchargeAccounts'); ?>",
                type: "POST"
            },
            "order": [],
            "columnDefs": [{
                    "targets": [0, 4, 5],
                    "orderable": false,
                },
                {
                    "targets": [2, 3, 6],
                    "className": "text-center",
                    "orderable": false,
                }
            ],
        });
    }

    function updatePromoInchargeAccounts(column, value) {
        Swal.fire({
            title: 'Confirm!',
            text: 'Confirm update of promo incharge account.',
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
                    text: 'Updating promo incharge account.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/updatePromoInchargeAccounts'); ?>",
                        data: {
                            column,
                            value,
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Promo incharge account has been updated.',
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location = "<?= base_url('promo/page/user/promoIncharge') ?>";
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
            } else {
                var un = value.split('|')
                if (column == 'usertype') {
                    var select = $('select#ut' + un[1] + '[name="' + column + '"]');
                } else {
                    var select = $('select#us' + un[1] + '[name="' + column + '"]');
                }

                console.log(column, value, un[1], select)
                select.find('option').each(function() {
                    if ($(this).val() != value) {
                        select.val($(this).val());
                        return false;
                    }
                });
            }
        })
    }

    function promoUserAccessRoles() {
        var dataTable = $("#promoUserAccessRoles").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/promoUserAccessRoles'); ?>",
                type: "POST"
            },
            "order": [
                [0, "asc"]
            ],
            "columnDefs": [{
                    "targets": [2, 3, 4, 5, 6],
                    "className": "text-center",
                    "orderable": false,
                },
                {
                    "targets": [1],
                    "orderable": false,
                }
            ],
        });

    }

    function updatePromoUserAccessRoles(column, value) {
        Swal.fire({
            title: 'Confirm!',
            text: 'Confirm update of promo user access.',
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
                    text: 'Updating promo user access.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/accessRoles'); ?>",
                        data: {
                            column,
                            value,
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Promo user access has been updated.',
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location = "<?= base_url('promo/page/user/accessRoles') ?>";
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
            } else {
                var split = value.split('|')

                if (column == 'promo1') {
                    var select = $('select#p1' + split[1] + '[name="' + column + '"]');
                } else if (column == 'promo2') {
                    var select = $('select#p2' + split[1] + '[name="' + column + '"]');
                } else {
                    var select = $('select#p3' + split[1] + '[name="' + column + '"]');
                }

                console.log(column, value, split[1], select)
                select.find('option').each(function() {
                    if ($(this).val() != value) {
                        select.val($(this).val());
                        return false;
                    }
                });
            }
        })
    }

    function formUserAccess(userNo) {
        // console.log(userNo)

        $('div#formUserAccess').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#formUserAccess").modal("show");
        $.ajax({
            url: "<?= site_url('promo/formUserAccess') ?>",
            type: 'POST',
            data: {
                userNo
            },
            beforeSend: function() {
                loader_circle('formUserAccess')
            },
            success: function(data) {
                $("div.formUserAccess").html(data);
                $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
                $('.form-check-input').on('change', function() {
                    if ($(this).is(':checked')) {
                        $(this).css('border-color', '');
                    } else {
                        $(this).css('border-color', 'rgb(108 117 125)');
                    }
                });
            }
        });
    }

    function updateMainMenu(empId, mmId) {
        console.log(empId, mmId)
        if ($('#mm' + mmId).is(':checked')) {
            var process = 'add';
            $('.sm' + mmId).prop('checked', true).css('border-color', '');
        } else {
            var process = 'remove';
            $('.sm' + mmId).prop('checked', false).css('border-color', 'rgb(108 117 125)');
        }

        $.ajax({
            url: "<?= site_url('promo/updateMainMenu') ?>",
            type: 'POST',
            data: {
                empId,
                mmId,
                process
            },
            success: function(data) {}
        });
    }

    function updateSubMenu(empId, mmId, smId) {
        console.log(empId, mmId, smId)
        if ($('#sm' + smId).is(':checked')) {
            var process = 'add';
        } else {
            var process = 'remove';
            var count = 0;
            $('.sm' + mmId).each(function() {
                if ($(this).is(':checked')) {
                    count++;
                }
            });

            if (count == 0) {
                $('#mm' + mmId).prop('checked', false).css('border-color', 'rgb(108 117 125)');
            }
        }

        $.ajax({
            url: "<?= site_url('promo/updateSubMenu') ?>",
            type: 'POST',
            data: {
                empId,
                mmId,
                smId,
                process
            },
            success: function(data) {}
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