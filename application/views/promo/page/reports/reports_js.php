<script>
    $(document).ready(function() {
        $('.form-check-input:not(:checked)').css('border-color', '#aaa');
        $('.form-check-input').change(function() {
            if (!$(this).is(':checked')) {
                $(this).css('border-color', '#aaa');
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

        $('#storeSelect').on('select2:select select2:unselect', function() {
            var selectedValues = $(this).val();

            if ($.inArray('AllBu', selectedValues) !== -1) {
                $(this).find('option').not(':selected').prop('selected', true);
                $(this).find('option[value="AllBu"]').prop('selected', false);

                $(this).trigger('change');
            }
            console.log(selectedValues)
        });

        $("#newPromo").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/newPromo'); ?>",
                type: "POST"
            },
            "order": [
                [5, 'desc']
            ],
            "columnDefs": [{
                "targets": [5],
                "type": "date"
            }, {
                "targets": [1, 2, 3, 4, 6],
                "orderable": false,

            }]
        });

        $('#bdayToday').DataTable({
            "order": [
                [0, 'asc']
            ],
        });

        $("#failedEpas").DataTable({
            "destroy": true,
            "ajax": {
                url: "<?= site_url('promo/failedEpas'); ?>",
                type: "POST"
            },
            "order": [
                [2, 'desc']
            ],
            "columnDefs": [{
                "targets": [2],
                "type": "date"
            }, {
                "targets": [0, 1, 3, 4],
                "orderable": false,

            }, {
                "targets": [4],
                "className": "text-center",
            }],
            "drawCallback": function(settings) {
                $('span.eocStrEpas').tooltip();
                $('a.eocStrEpas').tooltip();
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

    function getDepartment(value) {
        var action = 'getQbeDept';
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

    function generateQbe() {

        var applicant = $('select[name="applicant[]"]').val()
        var employee = $('select[name="employee[]"]').val()
        var company = $('select[name="company[]"]').val()
        var benefits = $('select[name="benefits[]"]').val()
        if (applicant == '' && employee == '' && company == '' && benefits == '') {
            alertRequired('Please select atleast 1(one) Display Field!')
            $("select[name='applicant[]'],select[name='employee[]'],select[name='company[]'],select[name='benefits[]']").next().find(".select2-selection").css("border-color", "#dd4b39");
            $(".select2-selection").on("focus", function() {
                $(".select2-selection").css("border-color", "");
            });
            return false;
        }

    }

    function generatePromoStat() {
        var stores = $('select[name="stores[]"]').val()
        var preparedBy = $('input[name="preparedBy"]').val()
        var submittedTo = $('input[name="submittedTo"]').val()
        console.log(stores, preparedBy, submittedTo)
        if (!stores || stores.length === 0 || preparedBy.trim() === '' || submittedTo.trim() === '') {


            alertRequired('Please fill the required fields!')
            $("input[name='preparedBy'],input[name='submittedTo']").css("border-color", "#dd4b39");
            $("select[name='stores[]']").next().find(".select2-selection").css("border-color", "#dd4b39");
            $("input[name='preparedBy'],input[name='submittedTo'],.select2-selection").on("focus", function() {
                $(this).css("border-color", "");
            });

            if (stores && stores.length != 0) {
                $("select[name='stores[]']").next().find(".select2-selection").css("border-color", "");
            }
            if (preparedBy.trim() != '') {
                $("input[name='preparedBy']").css("border-color", "");
            }
            if (submittedTo.trim() != '') {
                $("input[name='submittedTo']").css("border-color", "");
            }
            return false;
        }

    }

    function generateMonthlyStat() {
        var current_status = $('select[name="current_status"]').val()
        var dateasof = $('input[name="dateasof"]').val()
        if (current_status.trim() === '' || dateasof.trim() === '') {
            alertRequired('Please fill the required fields!')
            $("input[name='dateasof']").css("border-color", "#dd4b39");
            $("select[name='current_status']").next().find(".select2-selection").css("border-color", "#dd4b39");
            $("input[name='dateasof'],.select2-selection").on("focus", function() {
                $(this).css("border-color", "");
            });
            if (current_status != '') {
                $("select[name='current_status']").next().find(".select2-selection").css("border-color", "");
            }
            if (dateasof.trim() != '') {
                $("input[name='dateasof']").css("border-color", "");
            }
            return false;
        }
    }

    function generateAnnualStat() {
        var current_status = $('select[name="current_status"]').val()
        var datefrom = $('input[name="datefrom"]').val()
        var dateto = $('input[name="dateto"]').val()
        var dateto = $('input[name="dateto"]').val()
        if (current_status.trim() === '' || datefrom.trim() === '' || dateto.trim() === '') {
            alertRequired()
            $("input[name='datefrom']").css("border-color", "#dd4b39");
            $("input[name='dateto']").css("border-color", "#dd4b39");
            $("select[name='current_status']").next().find(".select2-selection").css("border-color", "#dd4b39");
            $("input[name='datefrom'],input[name='dateto'],.select2-selection").on("focus", function() {
                $(this).css("border-color", "");
            });
            if (current_status != '') {
                $("select[name='current_status']").next().find(".select2-selection").css("border-color", "");
            }
            if (datefrom.trim() != '') {
                $("input[name='datefrom']").css("border-color", "");
            }
            if (dateto.trim() != '') {
                $("input[name='dateto']").css("border-color", "");
            }
            return false;
        }
    }

    function generateContractMon() {
        var current_status = $('select[name="current_status"]').val()
        var datefrom = $('input[name="datefrom"]').val()
        var dateto = $('input[name="dateto"]').val()
        var upload_status = $('select[name="upload_status"]').val()
        var promo_type = $('select[name="promo_type"]').val()

        if (promo_type.trim() === '' || upload_status.trim() === '' || current_status.trim() === '' || datefrom.trim() === '' || dateto.trim() === '') {
            alertRequired()
            $("input[name='datefrom']").css("border-color", "#dd4b39");
            $("input[name='dateto']").css("border-color", "#dd4b39");
            $("select[name='current_status'],select[name='upload_status'],select[name='promo_type']").next().find(".select2-selection").css("border-color", "#dd4b39");
            $("input[name='datefrom'],input[name='dateto'],.select2-selection").on("focus", function() {
                $(this).css("border-color", "");
            });
            if (current_status != '') {
                $("select[name='current_status']").next().find(".select2-selection").css("border-color", "");
            }
            if (upload_status != '') {
                $("select[name='upload_status']").next().find(".select2-selection").css("border-color", "");
            }
            if (promo_type != '') {
                $("select[name='promo_type']").next().find(".select2-selection").css("border-color", "");
            }
            if (datefrom.trim() != '') {
                $("input[name='datefrom']").css("border-color", "");
            }
            if (dateto.trim() != '') {
                $("input[name='dateto']").css("border-color", "");
            }
            return false;
        }
    }

    function generateDueContracts(type) {
        var form = document.getElementById('dueContractsForm');
        var store = $('select[name="store"]').val()
        if (store.trim() === '') {
            alertRequired('Please fill the required fields!')
            $("select[name='store']").next().find(".select2-selection").css("border-color", "#dd4b39");
            $(".select2-selection").on("focus", function() {
                $(this).css("border-color", "");
            });
            if (store != '') {
                $("select[name='store']").next().find(".select2-selection").css("border-color", "");
            }
            return false;
        } else {
            if (type === 'excel') {
                form.action = "<?= base_url('promo/reports/generateDueContractsExcel') ?>";
            } else if (type === 'pdf') {
                form.action = "<?= base_url('promo/pdf/generateDueContractsPDF') ?>";
            }
            form.submit();
        }
    }

    function generateDutySched(type) {
        var store = $('select[name="store"]').val()
        var promo_department = $('select[name="promo_department"]').val()
        var promo_company = $('select[name="promo_company"]').val()
        var current_status = $('select[name="current_status"]').val()

        if (type === 'excel') {
            if (store.trim() === '') {
                alertRequired('Please fill the required fields!')
                $("select[name='store']").next().find(".select2-selection").css("border-color", "#dd4b39");
                $(".select2-selection").on("focus", function() {
                    $(this).css("border-color", "");
                });
                if (store != '') {
                    $("select[name='store']").next().find(".select2-selection").css("border-color", "");
                }
                return false;
            }
        } else if (type === 'list') {
            if (store.trim() === '') {
                alertRequired('Please fill the required fields!')
                $("select[name='store']").next().find(".select2-selection").css("border-color", "#dd4b39");
                $(".select2-selection").on("focus", function() {
                    $(this).css("border-color", "");
                });
                if (store != '') {
                    $("select[name='store']").next().find(".select2-selection").css("border-color", "");
                }
            } else {
                $('div#dutySchedList').modal({
                    backdrop: 'static',
                    keyboard: false,
                });
                $("div#dutySchedList").modal("show");
                $.ajax({
                    type: "POST",
                    url: "<?= site_url('promo/dutySchedList'); ?>",
                    data: {
                        store,
                        promo_department,
                        promo_company,
                        current_status,
                    },
                    success: function(data) {
                        $('div.modal-body').html(data);
                        var table = $('#dutySched_table').DataTable({
                            ajax: {
                                url: "<?= site_url('promo/dutySchedListData'); ?>",
                                type: "POST",
                                data: {
                                    store,
                                    promo_department,
                                    promo_company,
                                    current_status
                                },
                            },
                            columns: [{
                                    className: 'details-control',
                                    orderable: false,
                                    data: null,
                                    defaultContent: '<a href="javascript:;"><i class="fadeIn animated bx bx-plus-circle text-primary font-22"></i></a>'
                                },
                                {
                                    data: "0"
                                },
                                {
                                    data: "1",
                                    orderable: false
                                },
                                {
                                    data: "2",
                                    orderable: false
                                },
                                {
                                    data: "3",
                                    orderable: false
                                },
                                {
                                    data: "4",
                                    orderable: false
                                }
                            ],
                            order: [
                                [1, 'asc']
                            ],
                            initComplete: function(settings, json) {
                                $('th.act').css('width', '3%');
                                $('th.name').css('width', '25%');
                                $('th.sd').css('width', '22%');
                                $('th.ts').css('width', '30%');
                                $('th.do').css('width', '10%');
                                $('th.co').css('width', '10%');
                            }
                        });

                        $('#dutySched_table tbody').on('click', 'td.details-control', function() {
                            var tr = $(this).closest('tr');
                            var row = table.row(tr);

                            if (row.child.isShown()) {
                                row.child.hide();
                                tr.removeClass('shown');
                                $(this).html('<a href="javascript:;"><i class="fadeIn animated bx bx-plus-circle text-primary font-22"></i></a>');
                            } else {
                                row.child(format(row.data())).show();
                                tr.addClass('shown');
                                $(this).html('<a href="javascript:;"><i class="fadeIn animated bx bx-minus-circle text-danger font-22"></i></a>');
                            }
                        });

                    }
                });
            }
        }


    }

    function format(d) {
        return '<table class="table table-hover table-bordered" width="100%" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">' +
            '<tr>' +
            '<th width="10%">Store(s)</th>' +
            '<td width="50%">' + d[5] + '</td>' +
            '<th width="10%">Position</th>' +
            '<td width="30%">' + d[8] + '</td>' +
            '</tr>' +
            '<tr>' +
            '<th>Department</th>' +
            '<td>' + d[6] + '</td>' +
            '<th>Deployment</th>' +
            '<td>' + d[9] + '</td>' +
            '</tr>' +
            '<tr>' +
            '<th>Company</th>' +
            '<td>' + d[7] + '</td>' +
            '<th>Contract Date</th>' +
            '<td>' + d[10] + '</td>' +
            '</tr>' +
            '</table>';
    }

    function generateTermRep(type) {
        var form = document.getElementById('terReportForm');
        var store = $('select[name="store"]').val()
        var promo_department = $('select[name="promo_department"]').val()
        var promo_company = $('select[name="promo_company"]').val()
        var month = $('select[name="month"]').val()

        if (month.trim() === '') {
            alertRequired('Please fill the required fields!')
            $("select[name='month']").next().find(".select2-selection").css("border-color", "#dd4b39");
            $(".select2-selection").on("focus", function() {
                $(this).css("border-color", "");
            });

            if (month != '') {
                $("select[name='month']").next().find(".select2-selection").css("border-color", "");
            }
            return false;
        } else {
            if (type === 'excel') {
                form.action = "<?= base_url('promo/reports/generateTermRepExcel') ?>";
                form.submit();
            } else if (type === 'pdf') {
                form.action = "<?= base_url('promo/pdf/generateTermRepPdf') ?>";
                form.submit();
            } else if (type === 'list') {
                $('div#termRepList').modal({
                    backdrop: 'static',
                    keyboard: false,
                });
                $("div#termRepList").modal("show");

                var [id, months] = month.split('|')
                var year = months == new Date().getFullYear() + 1 ? months : new Date().getFullYear();
                console.log(id, months)
                var monthName = months == new Date().getFullYear() + 1 ? 'January' : months;
                $('h5.modal-title').html('List of End Contract for ' + monthName + ' ' + year)

                $.ajax({
                    type: "POST",
                    url: "<?= site_url('promo/termRepList'); ?>",
                    data: {
                        store,
                        promo_department,
                        promo_company,
                        month,
                    },
                    beforeSend: function(data) {
                        loader_circle('modal-body')
                    },
                    success: function(data) {
                        $('div.modal-body').html(data);
                        new PerfectScrollbar('.sliders');
                        $('.sliders').hover(function() {
                            new PerfectScrollbar('.sliders');
                        });
                    }
                });
            }
        }
    }

    function termContract(type) {
        var emp_id = [];
        $('input[name="emp_id[]"]:checked').each(function() {
            emp_id.push($(this).val());
        });
        if (emp_id.length == 0) {
            alertRequired('Please select employee!')
        } else {
            var store = $('select[name="store"]').val()
            var promo_department = $('select[name="promo_department"]').val()
            var promo_company = $('select[name="promo_company"]').val()
            var month = $('select[name="month"]').val()
            var ids = emp_id.join('|');
            var link = "?emp_id=" + ids + "&&month=" + month + "&&store=" + store +
                "&&promo_department=" + promo_department +
                "&&promo_company=" + promo_company +
                "&&type=" + type;
            window.open("<?= base_url('promo/generateTermContract') ?>" + link);
        }
    }

    function checkAll() {
        if ($('input[name="checkAll"]').prop('checked')) {
            $('input[name="emp_id[]"]').prop('checked', true).css('border-color', '');
        } else {
            $('input[name="emp_id[]"]').prop('checked', false).css('border-color', 'rgb(108 117 125)');
        }
    }

    function promoStat(emp_type, department, field) {
        $('div#promoStat').modal({
            backdrop: 'static',
            keyboard: false,
        });
        $("div#promoStat").modal("show");
        $.ajax({
            type: "POST",
            url: "<?= site_url('promo/promoStat'); ?>",
            data: {
                emp_type,
                department,
                field
            },
            beforeSend: function(data) {
                loader_circle('promoStatData')
            },
            success: function(data) {
                $(".promoStatData").html(data);
                $('#statisticRep').DataTable({
                    "order": [
                        [1, 'asc']
                    ],
                });
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
                beforeSend: function(data) {
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