<style>
    .required_alert {
        width: 300px;
        font-size: 12px;
    }

    .slider {
        overflow: auto;
        max-height: 500px;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <form action="<?php echo base_url('promo/reports/generateQbe') ?>" method="POST" autocomplete="off" target="_blank" onSubmit="return generateQbe()">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mt-0">
                        <h5 class="card-title text-primary">Query By Example Report</h5>
                        <!-- <button type="submit" class="btn btn-sm btn-primary">Generate in Excel</button> -->
                    </div>
                    <hr class="mt-2">
                    <div class="row">
                        <div class="col-md-4 border-end">
                            <h6 class="text-center">DISPLAY FIELDS</h6>
                            <div class="row slider">
                                <div class="col-md-6 displayFields">
                                    <label class="text-secondary">Applicant</label>
                                    <?php
                                    $query = $this->promo_model->field_names();
                                    foreach ($query as $key => $field) {
                                        echo    '<div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="' . $key . '" name="applicant[]" id="' . $key . '" style="border-color: rgb(170, 170, 170);">
                                                    <label class="form-check-label" for="' . $key . '">' . $field . '</label>
                                                </div>';
                                    }
                                    ?>
                                </div>
                                <div class="col-md-6 displayFields">
                                    <label class="text-secondary">Employee</label>
                                    <?php
                                    $query = $this->promo_model->field_employee();
                                    foreach ($query as $key => $field) {
                                        echo    '<div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="' . $key . '" name="employee[]" id="' . $key . '" style="border-color: rgb(170, 170, 170);">
                                                    <label class="form-check-label" for="' . $key . '">' . $field . '</label>
                                                </div>';
                                    }
                                    ?>

                                    <label class="text-secondary mt-2">Company Details</label>
                                    <?php
                                    $query = $this->promo_model->field_company();
                                    foreach ($query as $key => $field) {
                                        echo    '<div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="' . $key . '" name="company[]" id="' . $key . '" style="border-color: rgb(170, 170, 170);">
                                                    <label class="form-check-label" for="' . $key . '">' . $field . '</label>
                                                </div>';
                                    }
                                    ?>

                                    <label class="text-secondary mt-2">Benefits</label>
                                    <?php
                                    $query = $this->promo_model->field_benefits();
                                    foreach ($query as $key => $field) {
                                        echo    '<div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="' . $key . '" name="benefits[]" id="' . $key . '" style="border-color: rgb(170, 170, 170);">
                                                    <label class="form-check-label" for="' . $key . '">' . $field . '</label>
                                                </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 border-end ">
                            <h6 class="text-center">CONDITIONS</h6>
                            <div class="row slider">
                                <div class="col-md-12">
                                    <label class="text-secondary">Name</label>
                                    <input type="text" name="name" class="form-control form-control-sm">
                                    <label class="text-secondary mt-2">Home Address</label>
                                    <input type="text" name="home_address" class="form-control form-control-sm">
                                    <label class="text-secondary mt-2">Gender</label>
                                    <select name="gender" class="form-select form-select-sm select2" data-placeholder="Select Gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                    <label class="text-secondary mt-2">Religion</label>
                                    <select name="religion" class="form-select form-select-sm select2" data-placeholder="Select Religion">
                                        <option value="">Select Religion</option>
                                        <?php
                                        $query = $this->promo_model->selectAll('religion');
                                        foreach ($query as $key => $value) {
                                            echo '<option value="' . $value['religion'] . '">' . $value['religion'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">Civil Status</label>
                                    <select name="civilstatus" class="form-select form-select-sm select2" data-placeholder="Select Civil Status">
                                        <option value="">Select Civil Status</option>
                                        <?php
                                        $civil_status = array('Single', 'Married', 'Widowed', 'Separated', 'Anulled', 'Divorced');
                                        foreach ($civil_status as $key => $value) {
                                            echo '<option value="' . $value . '">' . $value . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">School</label>
                                    <select name="school" class="form-select form-select-sm select2" data-placeholder="Select School">
                                        <option value="">Select School</option>
                                        <?php
                                        $query = $this->promo_model->selectAll('school');
                                        foreach ($query as $key => $value) {
                                            echo '<option value="' . $value['school_name'] . '">' . $value['school_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">Attainment</label>
                                    <select name="attainment" class="form-select form-select-sm select2" data-placeholder="Select Attainment">
                                        <option value="">Select Attainment</option>
                                        <?php
                                        $query = $this->promo_model->selectAll('attainment');
                                        foreach ($query as $key => $value) {
                                            echo '<option value="' . $value['attainment'] . '">' . $value['attainment'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">Course</label>
                                    <select name="course" class="form-select form-select-sm select2" data-placeholder="Select Course">
                                        <option value="">Select Course</option>
                                        <?php
                                        $query = $this->promo_model->selectAll('course');
                                        foreach ($query as $key => $value) {
                                            echo '<option value="' . $value['course_name'] . '">' . $value['course_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">Height</label>
                                    <select name="height" class="form-select form-select-sm select2" data-placeholder="Select Height">
                                        <option value="">Select Height</option>
                                        <?php
                                        $query = $this->promo_model->selectAll('height');
                                        foreach ($query as $key => $value) {
                                            echo '<option value="' . $value['cm'] . ' / ' . $value['feet'] . '">' . $value['cm'] . ' / ' . $value['feet']  . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">Weight</label>
                                    <select name="weight" class="form-select form-select-sm select2" data-placeholder="Select Weight">
                                        <option value="">Select Weight</option>
                                        <?php
                                        $query = $this->promo_model->selectAll('weight');
                                        foreach ($query as $key => $value) {
                                            echo '<option value="' . $value['kilogram'] . ' / ' . $value['pounds'] . '">' . $value['kilogram'] . ' / ' . $value['pounds']  . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">Blood Type</label>
                                    <select name="bloodtype" class="form-select form-select-sm select2" data-placeholder="Select Blood Type">
                                        <option value="">Select Blood Type</option>
                                        <?php
                                        $bloodtype = array('A', 'A+', 'A-', 'B', 'B+', 'B-', 'O', 'O+', 'O-', 'AB', 'AB+', 'AB-');
                                        foreach ($bloodtype as $key => $value) {
                                            echo '<option value="' . $value . '">' . $value . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">Position</label>
                                    <select name="position" class="form-select form-select-sm select2" data-placeholder="Select Position">
                                        <option value="">Select Position</option>
                                        <?php
                                        $query = $this->promo_model->positions();
                                        foreach ($query as $key => $value) {
                                            echo '<option value="' . $value['position_title'] . '">' . $value['position_title'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 ">
                            <h6 class="text-center">FILTERS</h6>
                            <div class="row slider">
                                <div class="col-md-12">
                                    <label class="text-secondary">Agency</label>
                                    <select name="agency_code" class="form-select form-select-sm select2" data-placeholder="Select Agency">
                                        <option value="">Select Agency</option>
                                        <?php
                                        $query = $this->promo_model->selectAll_tk('promo_locate_agency');
                                        foreach ($query as $key => $value) {
                                            echo '<option value="' . $value['agency_code'] . '">' . $value['agency_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">Company</label>
                                    <select name="promo_company" class="form-select form-select-sm select2" data-placeholder="Select Company">
                                        <option value="">Select Company</option>
                                        <?php
                                        $query = $this->promo_model->selectAll('locate_promo_company');
                                        foreach ($query as $key => $value) {
                                            echo '<option value="' . $value['pc_name'] . '">' . $value['pc_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">Store</label>
                                    <select name="store" class="form-select form-select-sm select2" onchange="getDepartment(this.value)" data-placeholder="Select Store">
                                        <option value="">Select Store</option>
                                        <?php
                                        $query = $this->promo_model->locate_promo_bu($this->hrCode);
                                        foreach ($query as $key => $value) {
                                            echo '<option value="' . $value['bunit_id'] . '|' . $value['bunit_field'] . '">' . $value['bunit_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label class="text-secondary mt-2">Department</label>
                                    <select name="promo_department" class="form-select form-select-sm select2" data-placeholder="Select Department">
                                        <option value="">Select Department</option>
                                    </select>
                                    <label class="text-secondary mt-2">Promo Type</label>
                                    <select name="promo_type" class="form-select form-select-sm select2" data-placeholder="Select Promo Type">
                                        <option value="">Select Promo Type</option>
                                        <option value="STATION">STATION</option>
                                        <option value="ROVING">ROVING</option>
                                    </select>
                                    <label class="text-secondary mt-2">Contract Type</label>
                                    <select name="type" class="form-select form-select-sm select2" data-placeholder="Select Contract Type">
                                        <option value="">Select Contract Type</option>
                                        <option value="Contractual">Contractual</option>
                                        <option value="Seasonal">Seasonal</option>
                                    </select>
                                    <label class="text-secondary mt-2">Employee Type</label>
                                    <select name="emp_type" class="form-select form-select-sm select2" data-placeholder="Select Employee Type">
                                        <option value="">Select Employee Type</option>
                                        <option value="Promo">Promo</option>
                                        <option value="Promo-NESCO">Promo-NESCO</option>
                                    </select>
                                    <label class="text-secondary mt-2">Current Status</label>
                                    <select name="current_status" class="form-select form-select-sm select2" data-placeholder="Select Current Status">
                                        <option value="">Select Current Status</option>
                                        <?php
                                        $d = array('Promo', 'Promo-NESCO');
                                        $query = $this->promo_model->whereIN_stcd('distinct', 'current_status', 'employee3', 'emp_type', $d);
                                        foreach ($query as $key => $value) {
                                            if ($value['current_status'] != '') {
                                                echo '<option value="' . $value['current_status'] . '">' . $value['current_status'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary mt-3">Generate in Excel</button>
                        </div>
                    </div>
                    <!-- <button type="submit" class="btn btn-sm btn-primary mt-3">Generate in Excel</button> -->
                </form>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->