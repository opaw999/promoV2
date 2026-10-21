<script>
    $(document).ready(function() {

        var dataTable = $("#blacklist").DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "<?php echo site_url('guest/viewBlacklist'); ?>",
                type: "POST",
            },
            "order": [],
            "columnDefs": [{
                    "targets": [0, 2, 3, 4],
                    "orderable": false,

                }, {
                    "targets": 4,
                    "className": "text-wrap",
                },
                // {
                //     "targets": 5,
                //     "className": "text-center",
                //     "orderable": false,
                // }
            ],
        });

        $("form#updateBlacklist").submit(function(e) {
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
                var value = $("textarea[name='" + field + "'],input[name='" + field + "']").val();

                if (value.trim() === '') {

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
                    title: 'Confirm!',
                    text: 'Confirm the update of blacklist data?',
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
                            text: 'Updating blacklist data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/updateBlacklist') ?>",
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    try {
                                        let response = JSON.parse(data); // Attempt to parse JSON
                                        if (response.message === 'success') {
                                            $("div#updateBlacklistForm").modal("hide");
                                            Swal.fire({
                                                title: 'Success!',
                                                text: 'Blacklist data has been updated.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonText: 'Ok',
                                                confirmButtonColor: '#0d6efd',
                                            }).then(() => {
                                                dataTable.ajax.reload();
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

        $("input[name='lastname'],input[name='firstname'],input[name='middlename']").on('search', function() {
            $("button.addManual").hide();
            $("div.checkBl").hide();
            $("div.checkBlt").hide();
        });
        $("input[name='lastname'],input[name='firstname'],input[name='middlename']").on('keyup', function() {
            $("button.addManual").hide();
            $("div.checkBl").hide();
            $("div.checkBlt").hide();
        });
    });

    function checkBl() {

        var fn = $('input[name="firstname"]').val().trim();
        var ln = $('input[name="lastname"]').val().trim();
        var mn = $('input[name="middlename"]').val().trim();
        if (ln == '' || fn == '') {
            if (ln == '') {
                $('input[name="lastname"]').css("border-color", "#fd3550");
            }
            if (fn == '') {
                $('input[name="firstname"]').css("border-color", "#fd3550");
            }
            $('input[name="lastname"],input[name="firstname"]').on("focus", function() {
                $(this).css("border-color", "");
            });
            alertRequired()
        } else {

            $("button.addManual").show();
            $("div.checkBl").show();
            $("div.checkBlt").show();
            $("div.checkBlt").addClass("border-start");

            $("#checkBl_table").DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "<?php echo site_url('promo/browseBltable1'); ?>",
                    type: "POST",
                    data: {
                        fn,
                        ln,
                        mn,
                    },
                },
                "order": [],
                "columnDefs": [{
                    "targets": [1, 2],
                    "className": "text-center",
                    "orderable": false,
                }],
                lengthChange: false,
                searching: false
            });

            $("#checkBlt_table").DataTable({
                "destroy": true,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "<?php echo site_url('promo/browseBltable2'); ?>",
                    type: "POST",
                    data: {
                        fn,
                        ln,
                        mn,
                    },
                },
                "order": [],
                "columnDefs": [{
                    "targets": [1],
                    "className": "text-center",
                    "orderable": false,
                }],
                lengthChange: false,
                searching: false
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

    function addManual() {
        var fn = $('input[name="firstname"]').val().trim();
        var ln = $('input[name="lastname"]').val().trim();
        var mn = $('input[name="middlename"]').val().trim();
        if (ln == '' || fn == '') {
            if (ln == '') {
                $('input[name="lastname"]').css("border-color", "#fd3550");
            }
            if (fn == '') {
                $('input[name="firstname"]').css("border-color", "#fd3550");
            }
            $('input[name="lastname"],input[name="firstname"]').on("focus", function() {
                $(this).css("border-color", "");
            });
            alertRequired()
        } else {
            $('div#addBlacklistForm').modal({
                backdrop: 'static',
                keyboard: false,
            });
            $("div#addBlacklistForm").modal("show");

            $.ajax({
                url: "<?= site_url('promo/addBlacklistFormManual') ?>",
                type: 'POST',
                data: {
                    fn,
                    ln,
                    mn
                },
                beforeSend: function() {
                    loader_circle('bl_form')
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
    }

    function nameSearch(str) {
        if (str.trim() != '') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/reportedbyBl'); ?>",
                data: {
                    str
                },
                success: function(data) {
                    $(".dropdown-list").show()
                    $(".dropdown-list").html(data);
                }
            });
        } else(
            $(".dropdown-list").hide()
        )
    }

    function getName(name) {

        $("input[name='reportedby']").val(name);
        $(".dropdown-list").hide()
    }

    function updateBlacklistForm(id) {

        $('div#updateBlacklistForm').modal({
            backdrop: 'static',
            keyboard: false,
        });

        $("div#updateBlacklistForm").modal("show");

        $.ajax({
            type: "POST",
            url: "<?php echo site_url('promo/updateBlacklistForm'); ?>",
            data: {
                id,
            },
            beforeSend: function() {
                loader_circle('updateBlacklistForm')
            },
            success: function(data) {
                $("div.updateBlacklistForm").html(data);
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
</script>