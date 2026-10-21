<script>
    $(document).ready(function() {
        $("#searchPromoTable").DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": true,
            "autoWidth": false,
        });
        masterfileDatatables(false)
        $('span.spanTooltip').tooltip();
        $('.select2').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });

        $('.select2clear').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: true
        });

        $('.select2ClearModal').select2({
            theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: true,
            dropdownParent: $('#masterfileFilter')
        });

        var fileValue = '<?= $file; ?>';
        if (fileValue === 'profile') {
            profileData('basicInfo');
        } else if (fileValue === 'searchApplicant') {
            $('input[name="lastname"],input[name="firstname"]').keypress(function(e) {
                var code = e.keyCode || e.which;
                if (code == 13) {
                    searchApplicant()
                }
            });
        }

        $('#uploadContract').on('shown.bs.modal', function() {
            $('select[name="filetype"]').val('intro');
        });

        $("form#save_modal_form").submit(function(e) {
            e.preventDefault();
            var pageRef = $('input[name="page"]').val();
            console.log(pageRef)
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
                    $("select[name='" + field + "']").next().find(".select2-selection").css("border-color", "#dd4b39");
                }

                $("input[name='" + field + "'],select[name='" + field + "'],.select2-selection").on("focus", function() {
                    $(this).css("border-color", "");
                });
            }

            if (fieldsComplete) {
                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm updating/adding data.',
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
                            title: 'Please wait!',
                            text: 'Updating/adding data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/save_modal_form') ?>",
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
                                                text: 'Data has been updated/added.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $("div#modal_form").modal("hide");
                                                    profileData(pageRef)
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

        $("form#save_editContract").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fields = [];
            let notRequired = ['agency_code', 'remarks', 'vendor_code', 'duration', 'eocdate']
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
                var value = $("input[name='" + field + "'],select[name='" + field + "'],textarea[name='" + field + "']").val();

                if (value === '') {
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
            if (fieldsComplete) {
                var promo_type = $('select[name="promo_type"]').val();
                var storeCheck = [];
                $('input[name="stores[]"]:checked').each(function() {
                    storeCheck.push($(this).val());
                });
                if (promo_type == 'ROVING' && storeCheck.length < 2) {
                    alertInfo('Please select atleast 2 stores!');
                } else if (promo_type == 'STATION' && storeCheck.length == 0) {
                    alertInfo('Please select a store.');
                } else {
                    Swal.fire({
                        title: 'Confirm!',
                        text: 'Confirm update of contract data.',
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
                                title: 'Please wait!',
                                text: 'Updating contract details...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            setTimeout(() => {
                                $.ajax({
                                    url: "<?= site_url('promo/save_editContract') ?>",
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
                                                    text: 'Contract details has been updated.',
                                                    icon: 'success',
                                                    iconColor: '#15ca20',
                                                    confirmButtonColor: '#3085d6',
                                                    confirmButtonText: 'Ok',
                                                    allowOutsideClick: false,
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $("div#editContract").modal("hide");
                                                        profileData('contractHistory')
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

        $("form#save_uploadContract").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fields = [];
            $('[name]').each(function() {
                var inputElement = $(this);

                if (inputElement.attr('type') === 'file') {
                    fields.push(inputElement.attr('name'));
                }
            });
            console.log(fields)
            var fieldsComplete = false;
            var errMessage = false;
            for (var i = 0; i < fields.length; i++) {

                var field = fields[i];
                var value = $("input[name='" + field + "']").val();
                if (value !== '') {
                    fieldsComplete = true;
                    $("input").css("border-color", "");
                }

                if (value === '') {
                    if (!errMessage) {

                        alertInfo('Please upload atleast 1(one) of the required fields.');
                        errMessage = true;
                    }
                    if (!fieldsComplete) {
                        $("input[name='" + field + "']").css("border-color", "#dd4b39");
                    }
                }

                $("input").on("focus", function() {
                    $("input").css("border-color", "");
                });
            }

            if (fieldsComplete) {
                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm the upload of file documents.',
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
                            title: 'Please wait!',
                            text: 'Uploading file documents...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/save_uploadContract') ?>",
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
                                                text: 'File document has been uploaded.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    contractFile(response.id)
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

        $("form#save_supervisor_form").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var supervisor = [];
            $('input[name="supervisor[]"]:checked').each(function() {
                supervisor.push($(this).val());
            });
            if (supervisor.length == 0) {
                alertInfo('Please select a supervisor.');
            } else {

                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm adding of Supervisor.',
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
                            title: 'Please wait!',
                            text: 'Adding supervisor...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/save_supervisor_form') ?>",
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
                                                text: 'Supervisor has been added.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $("div#supervisor_form").modal("hide");
                                                    profileData('supervisor')
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
        });

        $("form#tagtoRecruitmentForm").submit(function(e) {
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
                    $("select[name='" + field + "']").next().find(".select2-selection").css("border-color", "#dd4b39");
                }

                $("input[name='" + field + "'],select[name='" + field + "'],.select2-selection").on("focus", function() {
                    $(this).css("border-color", "");
                });
            }
            if (fieldsComplete) {

                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm tag to recruitment?',
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
                            title: 'Please wait!',
                            text: 'Tagging to recruitment...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/tagToRecruitment') ?>",
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
                                                    window.location = "<?= base_url('promo/page/promo/tagRecruitment') ?>";
                                                }
                                            });
                                        } else {
                                            Swal.close();
                                            errorAlert(response.report)
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
    })

    function masterfileDatatables(filter) {

        var ajaxData = {
            url: "<?= site_url('guest/masterfile'); ?>",
            type: "POST"
        };

        if (filter) {
            var store = $('select[name="store"]').val();
            var promo_department = $('select[name="promo_department"]').val();
            var promo_type = $('select[name="promo_type"]').val();
            var promo_company = $('select[name="promo_company"]').val();

            if (store != '') {
                ajaxData.data = {
                    store,
                    promo_department,
                    promo_type,
                    promo_company
                };
                $("#masterfileFilter").modal("hide");
            } else {
                alertRequired();
                $("select[name='store']").css("border-color", "#dd4b39");
                $("select[name='store'],.select2-selection").on("focus", function() {
                    $(this).css("border-color", "");
                });
                return; // Exit the function if the store filter is not set
            }
        }

        $("#masterfile").DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "ajax": ajaxData,
            "order": [],
            "columnDefs": [{
                    "targets": [1, 2, 3, 4, 5, 6],
                    "orderable": false,
                },
                {
                    "targets": [1],
                    "className": "text-wrap",
                },
                {
                    "targets": 6,
                    "className": "text-center",
                },
            ],
            "drawCallback": function() {
                $('span.spanTooltip').tooltip();
            }
        });
    }

    function profilePic(pic) {

        var emp_id = $('input[name="emp_id"]').val();
        Swal.fire({
            html: '<img id="profile-pic" src="' + pic + '" alt="img" class="rounded-circle shadow p-1 border" width="300" height="300">' +
                '<label class="mt-2">Change profile picture?</label>',
            confirmButtonText: 'Yes',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            cancelButtonText: 'No',
        }).then(async (result) => {
            if (result.isConfirmed) {
                const {
                    value: file
                } = await Swal.fire({
                    title: 'Select image',
                    input: 'file',
                    inputAttributes: {
                        accept: 'image/*',
                        'aria-label': 'Upload your profile picture',
                    },
                    confirmButtonText: 'Sumbit',
                    showCancelButton: true,
                    cancelButtonText: 'No',
                })
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        Swal.fire({
                            title: 'Your uploaded picture',
                            imageUrl: e.target.result,
                            imageAlt: 'The uploaded picture',
                            confirmButtonText: 'Save',
                            showCancelButton: true,
                            cancelButtonText: 'Cancel',
                            customClass: {
                                image: 'rounded-circle shadow p-1 border img-preview',
                            },
                        }).then(async (result) => {
                            if (result.isConfirmed) {
                                var formData = new FormData();
                                formData.append('file', file);
                                formData.append('emp_id', emp_id);
                                $.ajax({
                                    type: 'POST',
                                    url: '<?= site_url('promo/profilePic'); ?>',
                                    data: formData,
                                    contentType: false,
                                    processData: false,
                                    success: function(data) {
                                        let response = JSON.parse(data);
                                        if (response.message == 'success') {
                                            Swal.fire({
                                                title: 'Success!',
                                                text: 'Profile picture has been changed.',
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

                                            alert(data)
                                        }
                                    }
                                });
                            }
                        });
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    }

    function profileData(id) {

        var emp_id = $('input[name="emp_id"]').val();
        var text = $('select[name="dataSelection"] option[value="' + id + '"]').text();
        if (id != '' && emp_id != '') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('guest/profileData'); ?>",
                data: {
                    id,
                    emp_id
                },
                beforeSend: function() {
                    loader_circle('profileData');
                },
                success: function(data) {
                    $('div.profileData').html(data);
                    $('span.profileData_title').html(text);
                    if (id == 'contractHistory') {
                        // new PerfectScrollbar('.sliders');
                    }
                }
            });
        }
    }

    function profileData_edit(action, page) {
        if (action == 'edit') {
            $('#' + page + ' input:disabled, #' + page + ' select:disabled, #' + page + ' textarea:disabled').prop('disabled', false);
            $('button#submit, button#cancel').show();
            $('button#edit').hide();
            $('i.input-group-text').addClass('text-primary');
        } else {
            profileData(page)
        }
    }

    function modal_form(action, no, page) {
        if (action !== 'remove') {
            if (page === 'supervisor') {
                $('div#supervisor_form').modal({
                    backdrop: 'static',
                    keyboard: false,
                });
                $("div#supervisor_form").modal("show");
            } else {
                $('div#modal_form').modal({
                    backdrop: 'static',
                    keyboard: false,
                });
                $("div#modal_form").modal("show");
            }
        }


        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/modal_form'); ?>",
            data: {
                action,
                no,
                page
            },
            success: function(data) {
                if (action !== 'remove') {
                    if (page === 'supervisor') {
                        $('div.supervisor_form').html(data);
                    } else {
                        $('div.modal_form').html(data);
                    }
                } else {

                    let response = JSON.parse(data);
                    if (response.message == 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Data has been deleted.',
                            icon: 'success',
                            iconColor: '#15ca20',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok',
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                profileData(page)
                            }
                        });
                    } else {
                        alert(data);
                    }
                }
            }
        });
    }

    function userAccount(action, no, page) {
        if (action === 'add') {
            $('div#userAccount').modal({
                backdrop: 'static',
                keyboard: false,
            });
            $("div#userAccount").modal("show");
            userAccountUpdate(action, no, page)

        } else {
            Swal.fire({
                title: 'Confirm!',
                text: 'Confirm ' + action + ' account.',
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
                        title: 'Please wait!',
                        text: 'Updating data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(() => {
                        userAccountUpdate(action, no, page)
                    }, 2000);
                }
            })
        }
    }

    function userAccountUpdate(action, no, page) {
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/modal_form'); ?>",
            data: {
                action,
                no,
                page,
            },
            success: function(data) {
                if (action === 'add') {
                    $('.userAccount').html(data);
                    $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                    $('.form-check-input').change(function() {
                        if (!$(this).is(':checked')) {
                            $(this).css('border-color', '#aaa');
                        } else {
                            $(this).css('border-color', '');
                        }
                    });
                } else {
                    let response = JSON.parse(data);
                    if (response.message == 'success') {
                        if (action == 'activate') {
                            var mes = 'activated'
                        } else if (action == 'deactivate') {
                            var mes = 'deactivated'
                        } else {
                            var mes = action
                        }
                        Swal.fire({
                            title: 'Success!',
                            text: 'Account has been ' + mes + '.',
                            icon: 'success',
                            iconColor: '#15ca20',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok',
                            allowOutsideClick: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                profileData(page)
                            }
                        });
                    } else {
                        alert(data);
                    }
                }
            }
        });
    }

    function viewCertificate(link) {
        console.log(link)
        $('div#viewCertificate').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#viewCertificate").modal("show");
        $("div.viewCertificate").html('<img src="' + link + '" class="d-block w-100" />');
    }

    function view_letter(link) {
        console.log(link)
        $('div#view_letter').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#view_letter").modal("show");
        $("div.view_letter").html('<img src="' + link + '" class="d-block w-100" />');
    }

    function viewIntro(link) {
        console.log(link)
        $('div#viewIntro').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#viewIntro").modal("show");
        $("div.viewIntro").html('<img src="' + link + '" class="d-block w-100"  alt="...">');
    }

    function viewClearance(link) {
        console.log(link)
        $('div#viewClearance').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#viewClearance").modal("show");
        $("div.viewClearance").html('<img src="' + link + '" class="d-block w-100"  alt="...">');
    }

    function viewContracts(link) {
        console.log(link)
        $('div#viewContracts').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#viewContracts").modal("show");
        $("div.viewContracts").html('<img src="' + link + '" class="d-block w-100"  alt="...">');
    }

    function viewAppraisal(details_id) {

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
                loader_circle('viewAppraisal');
            },
            success: function(data) {

                $('div.viewAppraisal').html(data);
            }
        });
    }

    function viewExam_history(emp_id) {

        $('div#viewExam_history').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#viewExam_history").modal("show");

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/viewExam_history'); ?>",
            data: {
                emp_id
            },
            beforeSend: function() {
                loader_circle('viewExam_history');
            },
            success: function(data) {

                $('div.viewExam_history').html(data);
            }
        });
    }

    function viewApp_details(emp_id) {

        $('div#viewApp_details').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#viewApp_details").modal("show");

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/viewApp_details'); ?>",
            data: {
                emp_id
            },
            beforeSend: function() {
                loader_circle('viewApp_details');
            },
            success: function(data) {

                $('div.viewApp_details').html(data);
            }
        });
    }

    function viewInt_details(emp_id) {

        $('div#viewInt_details').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#viewInt_details").modal("show");

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/viewInt_details'); ?>",
            data: {
                emp_id
            },
            beforeSend: function() {
                loader_circle('viewInt_details');
            },
            success: function(data) {
                $('div.viewInt_details').html(data);
            }
        });
    }

    function viewContract(id) {

        let [contract, emp_id, record_no] = id.split('|')
        if (contract != '' && emp_id != '' && record_no != '') {
            $('div#viewContract').modal({
                backdrop: 'static',
                keyboard: false,
            });
            $("div#viewContract").modal("show");
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/viewContract'); ?>",
                data: {
                    contract,
                    emp_id,
                    record_no
                },
                beforeSend: function() {
                    loader_circle('viewContract');
                },
                success: function(data) {
                    $('div.viewContract').html(data);
                    $('span.spanTooltip').tooltip();
                }
            });
        }
    }

    function editContract(id) {

        let [contract, emp_id, record_no] = id.split('|')
        if (contract != '' && emp_id != '' && record_no != '') {
            $('div#editContract').modal({
                backdrop: 'static',
                keyboard: false,
            });
            $("div#editContract").modal("show");
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/editContract'); ?>",
                data: {
                    contract,
                    emp_id,
                    record_no
                },
                beforeSend: function() {
                    loader_circle('editContract');
                },
                success: function(data) {
                    $('div.editContract').html(data);
                    // $(".datepicker").flatpickr();
                    $(".datepicker").flatpickr({
                        allowInput: true
                    });
                    $('.form-check-input:not(:checked)').css('border-color', 'rgb(108 117 125)');
                    $('.form-check-input').change(function() {
                        if (!$(this).is(':checked')) {
                            $(this).css('border-color', 'rgb(108 117 125)');
                        } else {
                            $(this).css('border-color', '');
                        }
                    });

                    $('.select2').select2({
                        theme: "bootstrap-5",
                        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                        placeholder: $(this).data('placeholder'),
                    });

                    $('.select2clear').select2({
                        theme: "bootstrap-5",
                        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                        placeholder: $(this).data('placeholder'),
                        allowClear: true
                    });
                }
            });
        }
    }

    function uploadContract(id) {

        let [contract, emp_id, record_no] = id.split('|')
        if (contract != '' && emp_id != '' && record_no != '') {

            $('div#uploadContract').modal({
                backdrop: 'static',
                keyboard: false,
            });
            $("div#uploadContract").modal("show");

            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/contractFile'); ?>",
                data: {
                    contract,
                    emp_id,
                    record_no,
                    file: 'intro'
                },
                beforeSend: function() {
                    loader_circle('contractFile');
                },
                success: function(data) {
                    $('div.contractFile').html(data);
                }
            });
        }
    }

    function contractFile(file) {
        var emp_id = $('input[name="emp_id"]').val();
        var record_no = $('input[name="record_no"]').val();
        var contract = $('input[name="contract"]').val();
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/contractFile'); ?>",
            data: {
                contract,
                emp_id,
                record_no,
                file
            },
            beforeSend: function() {
                loader_circle('contractFile');
            },
            success: function(data) {
                $('div.contractFile').html(data);
            }
        });
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

    function getProduct(value) {
        var action = 'getProduct';
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
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
        var contract = $('input[name="contract"]').val();
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
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
            }
        });
    }

    function getDepartment() {
        var action = 'getDepartment';
        var value = [];
        $('input[name="stores[]"]:checked').each(function() {
            value.push($(this).val());
        });
        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
            data: {
                value,
                action,
            },
            success: function(data) {
                $('select[name="promo_department"]').html(data);
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

    function getVendor(value) {
        var action = 'getVendor';

        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
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
        console.log(eocdate)
        if (eocdate === 'startdate') {
            $('input[name="eocdate"]').val('');
        } else {
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
                                $('input[name="eocdate"]').val('');
                                $('input[name="duration"]').val('');
                            }
                        });
                    } else {
                        $('input[name="duration"]').val(response.message);
                    }
                }
            });
        }
    }

    function getSupervisor_bc(value) {
        var action = 'getSupervisor_bc';
        var emp_id = $('input[name="emp_id"]').val();
        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
            data: {
                value,
                action,
                emp_id,
            },
            success: function(data) {

                $('select[name="bunit_code"]').html(data);
                supervisorList(value, emp_id)
            }
        });
    }

    function getSupervisor_dc(value) {
        var action = 'getSupervisor_dc';
        var emp_id = $('input[name="emp_id"]').val();
        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
            data: {
                value,
                action,
                emp_id,
            },
            success: function(data) {

                $('select[name="dept_code"]').html(data);
                supervisorList(value, emp_id)
            }
        });
    }

    function getSupervisor_sc(value) {
        var action = 'getSupervisor_sc';
        var emp_id = $('input[name="emp_id"]').val();
        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
            data: {
                value,
                action,
                emp_id,
            },
            success: function(data) {

                $('select[name="section_code"]').html(data);
                supervisorList(value, emp_id)
            }
        });
    }

    function getSupervisor_ssc(value) {
        var action = 'getSupervisor_ssc';
        var emp_id = $('input[name="emp_id"]').val();
        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
            data: {
                value,
                action,
                emp_id,
            },
            success: function(data) {

                $('select[name="sub_section_code"]').html(data);
                supervisorList(value, emp_id)
            }
        });
    }

    function getSupervisor_uc(value) {
        var action = 'getSupervisor_uc';
        var emp_id = $('input[name="emp_id"]').val();
        console.log(value)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/getSelect'); ?>",
            data: {
                value,
                action,
                emp_id,
            },
            success: function(data) {

                $('select[name="unit_code"]').html(data);
                supervisorList(value, emp_id)
            }
        });
    }

    function supervisorList(value, emp_id) {
        var [cc, bc, dc, sc, ssc, uc] = value.split('|')

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/supervisorList'); ?>",
            data: {
                cc,
                bc,
                dc,
                sc,
                ssc,
                uc,
                emp_id
            },
            beforeSend: function() {
                loader_circle('supervisorList');
            },
            success: function(data) {
                $('div.supervisorList').html(data);
                $("#supervisorList").DataTable({
                    scrollCollapse: true,
                    scrollY: '300px',
                    ordering: false,
                    lengthChange: false,
                    paging: false,
                    info: false,
                })
                $('.form-check-input:not(:checked)').css('border-color', '#aaa');
                $('.form-check-input').change(function() {
                    if (!$(this).is(':checked')) {
                        $(this).css('border-color', '#aaa');
                    } else {
                        $(this).css('border-color', '');
                    }
                });
            }
        });
    }

    function docFile_view(name, emp_id, no) {
        $('div#docFile_view').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#docFile_view").modal("show");
        $('h5.docFile_view').html(name);

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/docFile_view'); ?>",
            data: {
                emp_id,
                no,
            },
            beforeSend: function() {
                loader_circle('docFile_view');
            },
            success: function(data) {

                $('div.docFile_view').html(data);
            }
        });
    }

    function page(page, no) {
        var emp_id = $('input[name="emp_id"]').val();
        console.log(page, no, emp_id)
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/docFile_view'); ?>",
            data: {
                page,
                emp_id,
                no,
            },
            beforeSend: function() {
                loader_circle('docFile_view');
            },
            success: function(data) {

                $('div.docFile_view').html(data);
            }
        });
    }

    function docFile_uploadForm(name, emp_id, no) {
        $('div#docFile_uploadForm').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#docFile_uploadForm").modal("show");
        $('h5.docFile_uploadForm').html(name + ' Uploading...');

        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/docFile_uploadForm'); ?>",
            data: {
                name,
                emp_id,
                no,
            },
            beforeSend: function() {
                loader_circle('docFile_uploadForm');
            },
            success: function(data) {

                $('div.docFile_uploadForm').html(data);
            }
        });
    }

    function passwordFunc(action) {
        if (action == 'viewPassword') {
            if ($('input[name="' + action + '"]').prop('checked')) {
                $('input[name="password"]').attr('type', 'text');
            } else {
                $('input[name="password"]').attr('type', 'password');
            }
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

    function filterDepartment(value) {
        var action = 'filterDepartment';
        if (value == '') {
            window.location = "<?= base_url('guest/masterfile') ?>";
        } else {
            $.ajax({
                type: "POST",
                url: "<?= site_url('guest/getSelect'); ?>",
                data: {
                    value,
                    action,
                },
                success: function(data) {
                    $('select[name="promo_department"]').html(data);
                }
            });
        }
    }

    function searchApplicant() {
        var firstname = $('input[name="firstname"]').val();
        var lastname = $('input[name="lastname"]').val();
        if (lastname.trim() != '') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/searchApplicant'); ?>",
                data: {
                    firstname,
                    lastname,
                },
                beforeSend: function() {
                    loader_circle('applicants');
                },
                success: function(data) {
                    $('div.applicants').html(data);
                    $("#applicantsTable").DataTable({
                        "paging": true,
                        "lengthChange": false,
                        "searching": false,
                        "ordering": false,
                        "info": true,
                        "autoWidth": false
                    });
                }
            });
        } else {
            alertRequired()
            $("input[name='lastname']").css("border-color", "#dd4b39");
            $("input[name='lastname']").on("focus", function() {
                $(this).css("border-color", "");
            });
        }
    }

    function searchPromoExport(str) {
        var process = $('input[name="process"]').val();
        $('.contracts').html('');
        if (str.trim() != '') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/searchPromoExport'); ?>",
                data: {
                    str,
                    process
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
            $('.contracts').html('');
        });
    }

    function selectPromoExport(name, emp_id) {
        $('input[name="name"]').val(name);
        $(".dropdown-list").hide();
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/selectPromoExport'); ?>",
            data: {
                emp_id
            },
            beforeSend: function() {
                loader_circle('contracts');
            },
            success: function(data) {
                $('.contracts').html(data);
                $('span.spanTooltip').tooltip();
            }
        });
    }

    function exportPromoData(emp_id, record_no, contract) {
        console.log(emp_id, record_no, contract)
        Swal.fire({
            title: 'Confirm!',
            text: 'Confirm export of promo data.',
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
                    title: 'Please wait!',
                    text: 'Exporting promo data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    Swal.close();
                    window.open("<?= base_url('promo/exportPromoData') ?>?contract=" + contract + "&emp_id=" + emp_id + "&record_no=" + record_no, "_blank");
                }, 2000);
            }
        });
    }

    function searchTagtoRecruitment(str) {
        var process = $('input[name="process"]').val();
        $('select[name="recProcess"],select[name="position"]').val('').prop('disabled', true);
        $('input[name="status"]').val('');
        $('input[name="current_status"]').val('');
        $('input[name="emp_id"]').val('');
        if (str.trim() != '') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/searchTagtoRecruitment'); ?>",
                data: {
                    str,
                    process
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
            $('select[name="recProcess"],select[name="position"]').val('').prop('disabled', true);
            $('input[name="status"]').val('');
            $('input[name="current_status"]').val('');
            $('input[name="emp_id"]').val('');
        });
    }

    function getPromoDetails(name, emp_id, status, current_status) {
        if (name.trim() != '') {
            $('select[name="recProcess"],select[name="position"]').prop('disabled', false);
            $('input[name="name"]').val(name);
            $('input[name="status"]').val(status);
            $('input[name="current_status"]').val(current_status);
            $('input[name="emp_id"]').val(emp_id);
        }
        $(".dropdown-list").hide();
    }

    function getSeverData(server) {
        if (server != '') {
            $.ajax({
                type: "POST",
                url: "<?= site_url('promo/getSeverData'); ?>",
                data: {
                    server
                },
                beforeSend: function() {
                    Swal.fire({
                        icon: 'info',
                        title: 'Please wait!',
                        text: 'Generating server data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(data) {
                    setTimeout(() => {
                        Swal.close()
                        $('.serverData').html(data);
                    }, 2000);
                }
            });
        } else {
            $('.serverData').html('');
        }
    }

    function updateSeverData(data, field) {
        console.log(data, field)
        Swal.fire({
            title: 'Confirm!',
            text: 'Confirm update of server data.',
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
                    title: 'Please wait!',
                    text: 'Updating server data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                setTimeout(() => {
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('promo/updateSeverData'); ?>",
                        data: {
                            data,
                            field
                        },
                        beforeSend: function() {
                            $('button#serverdata').prop('disabled', true);
                        },
                        success: function(data) {
                            try {
                                let response = JSON.parse(data);
                                if (response.message == 'success') {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Server data has been updated.',
                                        icon: 'success',
                                        iconColor: '#15ca20',
                                        confirmButtonColor: '#3085d6',
                                        confirmButtonText: 'Ok',
                                        allowOutsideClick: false,
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            getSeverData(field)
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

    function addtoExtend(empId) {
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/addtoExtend'); ?>",
            data: {
                empId
            },
            beforeSend: function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Please wait!',
                    text: 'Updating data...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(data) {
                Swal.close()
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