<?php
$bloodtype = array('A', 'A+', 'A-', 'B', 'B+', 'B-', 'O', 'O+', 'O-', 'AB', 'AB+', 'AB-');
$civilstat = array('Single', 'Married', 'Widowed', 'Separated', 'Anulled', 'Divorced');
if ($request == 'basicInfo') { ?>
    <form id="basicInfo" autocomplete="off">
        <input type="hidden" name="appcode" value="<?= $app['appcode'] ?>">
        <input type="hidden" name="emp_id" value="<?= $emp_id ?>">
        <div class="row mb-3">
            <div class="col-sm-6">
                <label>Firstname</label>
                <input type="text" class="form-control" name="fn" value="<?= $app['firstname'] ?>" style="text-transform: capitalize;" disabled>
            </div>
            <div class="col-sm-6">
                <label>Lastname</label>
                <input type="text" class="form-control" name="ln" value="<?= $app['lastname'] ?>" style="text-transform: capitalize;" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-6">
                <label>Middlename</label>
                <input type="text" class="form-control" name="mn" value="<?= $app['middlename'] ?>" style="text-transform: capitalize;" disabled>
            </div>
            <div class="col-sm-6">
                <label>Suffix</label>
                <input type="text" class="form-control" name="suffix" value="<?= $app['suffix'] ?>" style="text-transform: capitalize;" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-6">
                <label>Birthdate</label>
                <div class="input-group">
                    <i class="input-group-text fadeIn animated bx bxs-cake font-22"></i>
                    <input type="date" class="form-control datepicker" name="birthdate" value="<?= $app['birthdate'] ?>" disabled>
                </div>
            </div>
            <div class="col-sm-6">
                <label>Citizenship</label>
                <div class="input-group">
                    <i class="input-group-text fadeIn animated bx bxs-flag-alt font-22"></i>
                    <input type="text" class="form-control" name="citizenship" value="<?= $app['citizenship'] ?>" disabled>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-6">
                <label>Gender</label>
                <div class="input-group">
                    <?php $genderIcon = ($app['gender'] == 'Male') ? 'male' : 'female'; ?>
                    <i class="input-group-text fadeIn animated bx bx-<?= $genderIcon ?> font-22"></i>
                    <select class="form-select select2" name="gender" data-placeholder="Select Gender" disabled>
                        <option value="">Select Gender</option>
                        <option value="Male" <?= ($app['gender'] == "Male") ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= ($app['gender'] == "Female") ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <label>Civil Status</label>
                <div class="input-group">
                    <i class="input-group-text fadeIn animated bx bx-male-female font-22"></i>
                    <select class="form-select select2" name="civilstatus" data-placeholder="Select Civil Status" disabled>
                        <option value="">Select Civil Status</option>
                        <?php
                        foreach ($civilstat as $value) {
                            echo '<option value="' . $value . '"';
                            echo ($value == $app['civilstatus']) ? 'selected' : '';
                            echo '>' . $value . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-6">
                <label>Religion</label>
                <div class="input-group">
                    <i class="input-group-text fadeIn animated bx bxs-church font-22"></i>
                    <select class="form-select select2" name="religion" data-placeholder="Select Religion" disabled>
                        <option value="">Select Religion</option>
                        <?php
                        foreach ($religion as $value) {
                            echo '<option value="' . $value['religion'] . '"';
                            echo ($value['religion'] == $app['religion']) ? 'selected' : '';
                            echo '>' . $value['religion'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <label>Bloodtype</label>
                <div class="input-group">
                    <i class="input-group-text fadeIn animated bx bxs-droplet font-22"></i>
                    <select class="form-select select2" name="bloodtype" data-placeholder="Select Bloodtype" disabled>
                        <option value="">Select Bloodtype</option>
                        <?php
                        foreach ($bloodtype as $value) {
                            echo '<option value="' . $value . '"';
                            echo ($value == $app['bloodtype']) ? 'selected' : '';
                            echo '>' . $value . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-sm-6">
                <label>Weight in kilogram</label>
                <div class="input-group">
                    <i class="input-group-text fadeIn animated bx bxs-tachometer font-22"></i>
                    <select class="form-select select2" name="weight" data-placeholder="Select Weight" disabled>
                        <option value="">Select Weight</option>
                        <?php
                        foreach ($weight as $value) {
                            echo '<option value="' . $value['kilogram'] . ' / ' . $value['pounds'] . '"';
                            echo ($value['kilogram'] . ' / ' . $value['pounds'] == $app['weight']) ? 'selected' : '';
                            echo '>' . $value['kilogram'] . ' / ' . $value['pounds'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <label>Height in centimeter</label>
                <div class="input-group">
                    <i class="input-group-text fadeIn animated bx bxs-ruler font-22"></i>
                    <select class="form-select select2" name="height" data-placeholder="Select Height" disabled>
                        <option value="">Select Height</option>
                        <?php
                        foreach ($height as $value) {
                            echo '<option value="' . $value['cm'] . ' / ' . $value['feet'] . '"';
                            echo ($value['cm'] . ' / ' . $value['feet'] == $app['height']) ? 'selected' : '';
                            echo '>' . $value['cm'] . ' / ' . $value['feet'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="d-md-flex d-grid align-items-center gap-2 justify-content-end">
                    <button type="button" class="btn btn-primary" id="edit" onclick="profileData_edit(this.id, '<?= $request ?>')">Edit</button>
                    <button type="submit" class="btn btn-primary" style="display: none;" id="submit">Update</button>
                    <button type="button" class="btn btn-secondary" style="display: none;" id="cancel" onclick="profileData_edit(this.id, '<?= $request ?>')">Cancel</button>
                </div>
            </div>
        </div>
    </form>
<?php
} else if ($request == 'contactInfo') { ?>
    <form id="contactInfo" autocomplete="off">
        <input type="hidden" name="emp_id" value="<?= $emp_id ?>">
        <div class="row">
            <div class="col-sm-6">
                <label>Home Address</label>
                <div class="input-group mb-3">
                    <i class="input-group-text fadeIn animated bx bxs-home font-22"></i>
                    <select class="form-select select2" name="home_address" data-placeholder="Select Home Address" disabled>
                        <option value="">Select Home Address</option>
                        <option value="<?= $app['home_address'] ?>" selected><?= $app['home_address'] ?></option>
                        <?php
                        foreach ($address as $value) {
                            echo '<option value="' . $value['brgy_name'] . ', ' . $value['town_name'] . ', ' . $value['prov_name'] . '"';
                            echo ($value['brgy_name'] . ', ' . $value['town_name'] . ', ' . $value['prov_name']   == $app['home_address']) ? 'selected' : '';
                            echo '>' . $value['brgy_name'] . ', ' . $value['town_name'] . ', ' . $value['prov_name'] .  '</option>';
                        } ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <label>City Address</label>
                <div class="input-group mb-3">
                    <i class="input-group-text fadeIn animated bx bxs-buildings font-22"></i>
                    <select class="form-select select2" name="city_address" data-placeholder="Select City Address" disabled>
                        <option value="">Select City Address</option>
                        <option value="<?= $app['city_address'] ?>" selected><?= $app['city_address'] ?></option>
                        <?php
                        foreach ($address as $value) {
                            echo '<option value="' . $value['brgy_name'] . ', ' . $value['town_name'] . ', ' . $value['prov_name'] . '"';
                            echo ($value['brgy_name'] . ', ' . $value['town_name'] . ', ' . $value['prov_name']   == $app['city_address']) ? 'selected' : '';
                            echo '>' . $value['brgy_name'] . ', ' . $value['town_name'] . ', ' . $value['prov_name'] .  '</option>';
                        } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <label>Cellphone Number</label>
                <div class="input-group mb-3">
                    <i class="input-group-text fadeIn animated bx bx-mobile font-22"></i>
                    <input type="text" class="form-control" name="contactno" value="<?= $app['contactno'] ?>" disabled>
                </div>
            </div>
            <div class="col-sm-6">
                <label>Telephone Number</label>
                <div class="input-group mb-3">
                    <i class="input-group-text fadeIn animated bx bxs-phone font-22"></i>
                    <input type="text" class="form-control" name="telno" value="<?= $app['telno'] ?>" disabled>
                </div>
            </div>
            <div class="col-sm-6">
                <label>Email Address</label>
                <div class="input-group mb-3">
                    <i class="input-group-text fadeIn animated bx bxs-envelope font-22"></i>
                    <input type="text" class="form-control" name="email" value="<?= $app['email'] ?>" disabled>
                </div>
            </div>
            <div class="col-sm-6">
                <label>Facebook Account</label>
                <div class="input-group mb-3">
                    <i class="input-group-text fadeIn animated bx bxl-facebook font-22"></i>
                    <input type="text" class="form-control" name="facebookAcct" value="<?= $app['facebookAcct'] ?>" disabled>
                </div>
            </div>
            <div class="col-sm-6">
                <label>Twitter Account</label>
                <div class="input-group">
                    <i class="input-group-text fadeIn animated bx bxl-twitter font-22"></i>
                    <input type="text" class="form-control" name="twitterAcct" value="<?= $app['twitterAcct'] ?>" disabled>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <label>Contact Person</label>
                <div class="input-group mb-3">
                    <i class="input-group-text fadeIn animated bx bxs-contact font-22"></i>
                    <input type="text" class="form-control" name="contact_person" value="<?= $app['contact_person'] ?>" style="text-transform: capitalize;" disabled>
                </div>
            </div>
            <div class="col-sm-6">
                <label>Contact Person Cellphone Number</label>
                <div class="input-group">
                    <i class="input-group-text fadeIn animated bx bx-mobile font-22"></i>
                    <input type="text" class="form-control" name="contact_person_number" value="<?= $app['contact_person_number'] ?>" disabled>
                </div>
            </div>
            <div class="col-sm-6">
                <label>Contact Person Address</label>
                <div class="input-group mb-3">
                    <i class="input-group-text fadeIn animated bx bxs-home font-22"></i>
                    <select class="form-select select2" name="contact_person_address" data-placeholder="Select Home Address" disabled>
                        <option value="">Select Home Address</option>
                        <option value="<?= $app['contact_person_address'] ?>" selected><?= $app['contact_person_address'] ?></option>
                        <?php
                        foreach ($address as $value) {
                            echo '<option value="' . $value['brgy_name'] . ', ' . $value['town_name'] . ', ' . $value['prov_name'] . '"';
                            echo ($value['brgy_name'] . ', ' . $value['town_name'] . ', ' . $value['prov_name']   == $app['contact_person_address']) ? 'selected' : '';
                            echo '>' . $value['brgy_name'] . ', ' . $value['town_name'] . ', ' . $value['prov_name'] .  '</option>';
                        } ?>
                    </select>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="d-md-flex d-grid align-items-center gap-2 justify-content-end">
                    <button type="button" class="btn btn-primary" id="edit" onclick="profileData_edit(this.id, '<?= $request ?>')">Edit</button>
                    <button type="submit" class="btn btn-primary" style="display: none;" id="submit">Update</button>
                    <button type="button" class="btn btn-secondary" style="display: none;" id="cancel" onclick="profileData_edit(this.id, '<?= $request ?>')">Cancel</button>
                </div>
            </div>
        </div>
    </form>
<?php
} else if ($request == 'fesc') { ?>
    <form id="fesc" autocomplete="off">
        <div class="row">
            <input type="hidden" name="emp_id" value="<?= $emp_id ?>">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-sm-6">
                        <label>Mother</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-female font-22"></i>
                            <input type="text" class="form-control" name="mother" value="<?= $app['mother'] ?>" style="text-transform: capitalize;" disabled>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Father</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-male font-22"></i>
                            <input type="text" class="form-control" name="father" value="<?= $app['father'] ?>" style="text-transform: capitalize;" disabled>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label>Guardian</label>
                        <div class="input-group">
                            <i class="input-group-text fadeIn animated bx bxs-user-voice font-22"></i>
                            <input type="text" class="form-control" name="guardian" value="<?= $app['guardian'] ?>" style="text-transform: capitalize;" disabled>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Spouse</label>
                        <div class="input-group">
                            <i class="input-group-text fadeIn animated bx bxs-heart font-22"></i>
                            <input type="text" class="form-control" name="spouse" value="<?= $app['spouse'] ?>" style="text-transform: capitalize;" disabled>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <label>Hobbies</label>
                        <div class="input-group">
                            <i class="input-group-text bx bx-cycling font-22"></i>
                            <input type="text" class="form-control" name="hobbies" value="<?= $app['hobbies'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Special Skills/Talents</label>
                        <div class="input-group">
                            <i class="input-group-text bx bx-glasses-alt font-22"></i>
                            <input type="text" class="form-control" name="specialSkills" value="<?= $app['specialSkills'] ?>" disabled>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <label>Educational Attainment</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text bx bxs-graduation font-22"></i>
                            <select class="form-select select2" name="attainment" data-placeholder="Select Educational Attainment" disabled>
                                <option value="">Select Educational Attainment</option>
                                <?php
                                foreach ($attainment as $value) {
                                    echo '<option value="' . $value['attainment'] . '"';
                                    echo ($value['attainment']   == $app['attainment']) ? 'selected' : '';
                                    echo '>' . $value['attainment'] .  '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>School</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bxs-school font-22"></i>
                            <select class="form-select select2" name="school" data-placeholder="Select School" disabled>
                                <option value="">Select School</option>
                                <?php
                                foreach ($school as $value) {
                                    echo '<option value="' . $value['school_name'] . '"';
                                    echo ($value['school_name']   == $app['school']) ? 'selected' : '';
                                    echo '>' . $value['school_name'] .  '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Course</label>
                        <div class="input-group font-22">
                            <i class="input-group-text lni lni-thought"></i>
                            <select class="form-select select2" name="course" data-placeholder="Select Course" disabled>
                                <option value="">Select Course</option>
                                <?php
                                foreach ($course as $value) {
                                    echo '<option value="' . $value['course_name'] . '"';
                                    echo ($value['course_name']   == $app['course']) ? 'selected' : '';
                                    echo '>' . $value['course_name'] .  '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-md-flex d-grid align-items-center gap-2 justify-content-end">
                            <button type="button" class="btn btn-primary" id="edit" onclick="profileData_edit(this.id, '<?= $request ?>')">Edit</button>
                            <button type="submit" class="btn btn-primary" style="display: none;" id="submit">Update</button>
                            <button type="button" class="btn btn-secondary" style="display: none;" id="cancel" onclick="profileData_edit(this.id, '<?= $request ?>')">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php
} else if ($request == 'training') { ?>
    <div class="row">
        <input type="hidden" name="emp_id" value="<?= $emp_id ?>">
        <div class="col-lg-12 mb-1">
            <table id="training" class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th class="text-center">Certificate</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($training as $value) {
                        echo    '<tr>
                                    <td>' . $value['name'] . '</td>
                                    <td>' . $value['dates'] . '</td>
                                    <td>' . $value['location'] . '</td>
                                    <td class="text-center">
                                        <a href="javascript:;" title="view Certificate" onclick=viewCertificate("' . $value['sem_certificate'] . '")>
                                            <i class="bx bx-image-alt me-0 font-22"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:;" title="edit" id="edit" onclick="modal_form(this.id, ' . $value['no'] . ', \'' . $request . '\')">
                                            <i class="bx bx-edit me-0 font-22"></i>
                                        </a>
                                    </td>
                                </tr>';
                    } ?>
                </tbody>
            </table>
        </div>
        <div><a href="javascript:;" class="btn btn-primary pull-right" id="add" onclick="modal_form(this.id,'<?= $emp_id ?>','<?= $request ?>')">Add</a></div>
    </div>
<?php
} else if ($request == 'characterRef') {
    echo '<div class="row">';
    echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
    echo '<div class="col-lg-12 mb-1">';
    echo '<table id="characterRef" class="table table-sm table-hover">
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Contact No.</th>
                <th>Company/Location</th>
                <th class="text-center">Action</th>
            </tr>';

    foreach ($character as $value) {
        $clk = 'onclick="modal_form(this.id, \'' . $value['no'] . '\', \'' . $request . '\')"';
        echo '<tr>
                <td>' . $value['name'] . '</td>
                <td>' . $value['position'] . '</td>
                <td>' . $value['contactno'] . '</td>
                <td>' . $value['company'] . '</td>
                <td class="text-center">
                    <a href="javascript:;" title="edit" id="edit" ' . $clk . '><i class="bx bx-edit me-0 font-22"></i></a>
                </td>';
        echo '</tr>';
    }
    $clk = 'onclick="modal_form(this.id,\'' . $emp_id . '\',\'' . $request . '\')"';
    echo '</table>';
    echo '</div>';
    echo '<div>';
    echo    '<a href="javascript:;" class="btn btn-primary pull-right" id="add" ' . $clk . '>Add</a>';
    echo '</div>';
    echo '</div>';
} else if ($request == 'eocAppraisal') {
    echo '<div class="row">';
    echo '<div class="col-lg-12 ">';
    echo '<table id="eocAppraisal" class="table table-sm table-hover">
            <tr>
                <th>Startdate</th>
                <th>EOCdate</th>
                <th>Rater</th>
                <th class="text-center">Rate</th>
                <th class="text-center">Description</th>
                <th>Store</th>
                <th class="text-center">Action</th>
            </tr>';
    $bus = $this->promo_model->selectAll('locate_promo_business_unit');
    $cond = array('emp_id' => $emp_id);
    $current = $this->promo_model->selectAll_tcA('employee3', $cond);
    foreach ($current as $key => $row) {
        $record_no = $row['record_no'];
        $cond = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $promo = $this->promo_model->selectAll_tcR('promo_record', $cond);
        if ($promo) {
            $store  = '';
            foreach ($bus as $bu) {
                $hasBu = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
                if ($hasBu) {

                    $store = $bu['bunit_name'];
                    if ($promo[$bu['bunit_epascode']] == 1) {
                        $cond = array('emp_id' => $emp_id, 'record_no' => $record_no, 'store' => $store);
                        $app  = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
                        if ($app) {

                            switch ($app['descrate']) {
                                case "E":
                                    $descrate = "Excellent";
                                    break;
                                case "VS":
                                    $descrate = "Very Satisfactory";
                                    break;
                                case "S":
                                    $descrate = "Satisfactory";
                                    break;
                                case "US":
                                    $descrate = "Unsatisfactory";
                                    break;
                                case "VU":
                                    $descrate = "Very Unsatisfactory";
                                    break;
                            }

                            $rate = '<span class="badge bg-success">' . $app['numrate'] . '</span>';
                            if ($app['numrate'] < 85) {
                                $rate = '<span class="badge bg-danger">' . $app['numrate'] . '</span>';
                            }

                            echo '<tr>';
                            echo    '<td>' . date('m/d/Y', strtotime($row['startdate'])) . '</td>';
                            echo    '<td>' . date('m/d/Y', strtotime($row['eocdate'])) . '</td>';
                            echo    '<td>' . ucwords(strtolower($this->promo_model->getName_employee3($app['rater']))) . '</td>';
                            echo    '<td class="text-center">' . $rate . '</td>';
                            echo    '<td class="text-center">' . $descrate . '</td>';
                            echo    '<td>' . $store . '</td>';
                            echo    '<td class="text-center">';
                            echo        '<a href="javascript:;" title="View Appraisal Details" id="' . $app['details_id'] . '" onclick="viewAppraisal(this.id)">';
                            echo            '<i class="bx bx-folder-open me-0 font-22"></i>';
                            echo        '</a>';
                            echo    '</td>';
                            echo '</tr>';
                        }
                    }
                }
            }
        }
    }

    $order = 'startdate DESC, record_no DESC';
    $cond = array('emp_id' => $emp_id);
    $previous = $this->promo_model->selectAll_tcoA('employmentrecord_', $cond, $order);
    foreach ($previous as $key => $row) {
        $record_no  = $row['record_no'];
        $cond       = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $promo      = $this->promo_model->selectAll_tcR('promo_history_record', $cond);
        if ($promo) {
            $store  = '';
            foreach ($bus as $bu) {
                $hasBu = $this->promo_model->empStores('promo_history_record', $emp_id, $record_no, $bu['bunit_field']);
                if ($hasBu) {

                    $store = $bu['bunit_name'];
                    if ($promo[$bu['bunit_epascode']] == 1) {
                        $cond = array('emp_id' => $emp_id, 'record_no' => $record_no, 'store' => $store);
                        $app  = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
                        if ($app) {

                            switch ($app['descrate']) {
                                case "E":
                                    $descrate = "Excellent";
                                    break;
                                case "VS":
                                    $descrate = "Very Satisfactory";
                                    break;
                                case "S":
                                    $descrate = "Satisfactory";
                                    break;
                                case "US":
                                    $descrate = "Unsatisfactory";
                                    break;
                                case "VU":
                                    $descrate = "Very Unsatisfactory";
                                    break;
                            }

                            $rate = '<span class="badge bg-success">' . $app['numrate'] . '</span>';
                            if ($app['numrate'] < 85) {
                                $rate = '<span class="badge bg-danger">' . $app['numrate'] . '</span>';
                            }
                            echo '<tr>';
                            echo    '<td>' . date('m/d/Y', strtotime($row['startdate'])) . '</td>';
                            echo    '<td>' . date('m/d/Y', strtotime($row['eocdate'])) . '</td>';
                            echo    '<td>' . ucwords(strtolower($this->promo_model->getName_employee3($app['rater']))) . '</td>';
                            echo    '<td class="text-center">' . $rate . '</td>';
                            echo    '<td class="text-center">' . $descrate . '</td>';
                            echo    '<td>' . $store . '</td>';
                            echo    '<td class="text-center">';
                            echo        '<a href="javascript:;" title="View Appraisal Details" id="' . $app['details_id'] . '" onclick="viewAppraisal(this.id)">';
                            echo            '<i class="bx bx-folder-open me-0 font-22"></i>';
                            echo        '</a>';
                            echo    '</td>';
                            echo '</tr>';
                        }
                    }
                }
            }
        }
    }
    echo '</table>';
    echo '</div>';
    echo '</div>';
} else if ($request == 'appHistory') { ?>
    <form id="appHistory" autocomplete="off">
        <div class="row">
            <input type="hidden" name="emp_id" value="<?= $emp_id ?>">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-sm-6">
                        <label>Position Applied</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-chair font-22"></i>
                            <select class="form-select select2" name="position_applied" data-placeholder="Select Position Applied" disabled>
                                <option value="">Select Position Applied</option>
                                <?php
                                foreach ($positions as $value) {
                                    echo '<option value="' . $value['position_title'] . '"';
                                    echo ($application['position_applied'] == $value['position_title']) ? 'selected' : '';
                                    echo '>' . $value['position_title'] .  '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Date Applied</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-calendar-event font-22"></i>
                            <input type="text" class="form-control datepicker" name="date_applied" value="<?= $application['date_applied'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label>Date of Exam</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-calendar-edit font-22"></i>
                            <input type="text" class="form-control datepicker" name="date_examined" value="<?= $application['date_examined'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Exam Result</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-news font-22"></i>
                            <input type="text" class="form-control" name="exam_results" value="<?= $application['exam_results'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Date Briefed</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text bx bx-calendar font-22"></i>
                            <input type="text" class="form-control datepicker" name="date_brief" value="<?= $application['date_brief'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Date Hired</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text bx bx-calendar-check font-22"></i>
                            <input type="text" class="form-control datepicker" name="date_hired" value="<?= $application['date_hired'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label>Recommended by (Alturas Employee)</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text bx bx-user-check font-22"></i>
                            <input type="text" class="form-control" name="aeregular" value="<?= $application['aeregular'] ?>" style="text-transform: capitalize;" disabled>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <br>
                        <div class="d-md-flex d-grid align-items-center gap-2 justify-content-end mt-2">
                            <button type="button" class="btn btn-primary" id="edit" onclick="profileData_edit(this.id, '<?= $request ?>')">
                                Edit
                            </button>
                            <button type="submit" class="btn btn-primary" style="display: none;" id="submit">Update</button>
                            <button type="button" class="btn btn-secondary" style="display: none;" id="cancel" onclick="profileData_edit(this.id, '<?= $request ?>')">
                                Cancel
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="col-sm-12">
                        <div class="d-md-flex d-grid align-items-center gap-2 justify-content-center">
                            <button type="button" class="btn btn-sm btn-primary" id="<?= $emp_id ?>" onclick="viewExam_history(this.id)">View Examination History</button>
                            <button type="button" class="btn btn-sm btn-primary" id="<?= $emp_id ?>" onclick="viewApp_details(this.id)">View Application Details</button>
                            <button type="button" class="btn btn-sm btn-primary" id="<?= $emp_id ?>" onclick="viewInt_details(this.id)">View Interview Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php
} else if ($request == 'contractHistory') {
    echo '<h6>CURRENT CONTRACT</h6>';
    echo '<table class="table table-sm table-hover align-middle" width="100%">
            <tr>
                <th>No.</th>
                <th>Position</th>
                <th>Company</th>
                <th>Store(s)</th>
                <th>Department</th>
                <th>Status</th>
                <th>Startdate</th>
                <th>EOCdate</th>
                <th class="text-center">Action</th>
            </tr>';
    $cond       = array('emp_id' => $emp_id);
    $current    = $this->promo_model->selectAll_tcA('employee3', $cond);
    foreach ($current as $key => $row) {

        $record_no = $row['record_no'];

        $id     = 'id="current|' . $emp_id . '|' . $record_no . '"';
        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $promo  = $this->promo_model->selectAll_tcR('promo_record', $cond);
        if ($promo) {
            $i      = 0;
            $stores = '';
            $cond = array('hrd_location' => $this->hrCode);
            $bus = $this->promo_model->selectAll_tcoA('locate_promo_business_unit', $cond, 'bunit_name');
            foreach ($bus as $bu) {
                $hasBu = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
                if ($hasBu) {
                    $i++;
                    $buName = $bu['bunit_name'];
                    $buAcro = $bu['bunit_acronym'];
                    $spanStore = '<span class="spanTooltip badge bg-secondary" title="' . $buName . '">' . $buAcro . '</span>';
                    $stores = ($i == 1) ? $spanStore : $stores . ' ' . $spanStore;
                }
            }

            $company    = $promo['promo_company'];
            $dept       = $promo['promo_department'];
            $action     =   '<a href="javascript:;" ' . $id . ' onclick="editContract(this.id)"><i class="fadeIn animated bx bx-edit font-18"></i></a>
                            <a href="javascript:;" ' . $id . ' onclick="uploadContract(this.id)"><i class="fadeIn animated bx bx-upload font-18"></i></a>';
        } else {

            $cc = $row['company_code'];
            $bc = $row['bunit_code'];
            $dc = $row['dept_code'];
            $sc = $row['section_code'];

            $cond       = array('company_code' => $cc);
            $company    = $this->promo_model->selectAll_tcR('locate_company', $cond)['company'];
            $cond       = array('company_code' => $cc, 'bunit_code' => $bc);
            $store      = $this->promo_model->selectAll_tcR('locate_business_unit', $cond)['business_unit'];
            $stores     = '<span class="badge bg-secondary">' . $store . '</span>';
            $cond       = array('company_code' => $cc, 'bunit_code' => $bc, 'dept_code' => $dc);
            $dept       = $this->promo_model->selectAll_tcR('locate_department', $cond)['dept_name'];
            $action     =   '<a href="javascript:;"><i class="text-secondary bx bx-edit font-18"></i></a>
                            <a href="javascript:;"><i class="text-secondary bx bx-upload font-18"></i></a>';
        }
        $eocdate = date('m/d/Y', strtotime($row['eocdate']));
        if ($row['eocdate'] == '0000-00-00') {
            $eocdate = '';
        }

        echo '<tr>
                <td style="font-size: smaller;"><span class="badge bg-success">C</span></td>
                <td>' . $row['position'] . '</td>
                <td style="font-size: smaller;">' . $company . '</td>
                <td style="font-size: smaller;">' . $stores . '</td>
                <td style="font-size: smaller;">' . $dept . '</td>
                <td style="font-size: smaller;">' . $row['current_status'] . '</td>
                <td style="font-size: smaller;">' . date('m/d/Y', strtotime($row['startdate'])) . '</td>
                <td style="font-size: smaller;">' . $eocdate . '</td>
                <td class="text-center">
                    <div class="d-md-flex d-grid align-items-center gap-1 justify-content-center">
                        <a href="javascript:;" ' . $id . ' onclick="viewContract(this.id)"><i class="fadeIn animated bx bx-info-circle font-18"></i></a>';
        echo            $action;
        echo        '</div>';
        echo    '</td>';
        echo '</tr>';
    }
    echo '</table>';

    $x          = 0;
    $order      = 'startdate DESC, record_no DESC';
    $cond       = array('emp_id' => $emp_id);
    $previous   = $this->promo_model->selectAll_tcoA('employmentrecord_', $cond, $order);
    if ($previous) {
        echo '<br>';
        echo '<h6>PREVIOUS CONTRACTS</h6>';
        echo '<table class="table table-sm table-hover align-middle" width="100%">
                <tr>
                    <th>No.</th>
                    <th>Position</th>
                    <th>Company</th>
                    <th>Store(s)</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Startdate</th>
                    <th>EOCdate</th>
                    <th class="text-center">Action</th>
                </tr>';

        foreach ($previous as $key => $row) {
            $x++;
            $record_no = $row['record_no'];
            $eocdate = date('m/d/Y', strtotime($row['eocdate']));
            if ($row['eocdate'] == '0000-00-00') {
                $eocdate = '';
            }
            $id     = 'id="previous|' . $emp_id . '|' . $record_no . '"';
            $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
            $promo  = $this->promo_model->selectAll_tcR('promo_history_record', $cond);
            if ($promo) {
                $i      = 0;
                $stores = '';
                $cond   = array('hrd_location' => $this->hrCode);
                $bus = $this->promo_model->selectAll_tcoA('locate_promo_business_unit', $cond, 'bunit_name');
                foreach ($bus as $bu) {
                    $hasBu = $this->promo_model->empStores('promo_history_record', $emp_id, $record_no, $bu['bunit_field']);
                    if ($hasBu) {
                        $i++;
                        $buName = $bu['bunit_name'];
                        $buAcro = $bu['bunit_acronym'];
                        $spanStore = '<span class="spanTooltip badge bg-secondary" title="' . $buName . '">' . $buAcro . '</span>';
                        $stores = ($i == 1) ? $spanStore : $stores . ' ' . $spanStore;
                    }
                }
                $company    = $promo['promo_company'];
                $dept       = $promo['promo_department'];
                $action     = '<a href="javascript:;" ' . $id . ' onclick="editContract(this.id)"><i class="fadeIn animated bx bx-edit font-18"></i></a><a href="javascript:;" ' . $id . ' onclick="uploadContract(this.id)"><i class="fadeIn animated bx bx-upload font-18"></i></a>';
            } else {
                $cc = $row['company_code'];
                $bc = $row['bunit_code'];
                $dc = $row['dept_code'];
                $sc = $row['section_code'];

                $cond       = array('company_code' => $cc);
                $company    = $this->promo_model->selectAll_tcR('locate_company', $cond)['company'];
                $cond       = array('company_code' => $cc, 'bunit_code' => $bc);
                $store      = $this->promo_model->selectAll_tcR('locate_business_unit', $cond)['business_unit'];
                $stores     = '<span class="badge bg-secondary spanTooltip text-truncate" style="max-width:80px;" title="' . $store . '">' . $store . '</span>';
                $cond       = array('company_code' => $cc, 'bunit_code' => $bc, 'dept_code' => $dc);
                $dept       = $this->promo_model->selectAll_tcR('locate_department', $cond)['dept_name'];
                $action     = '<a href="javascript:;"><i class="text-secondary bx bx-edit font-18"></i></a><a href="javascript:;"><i class="text-secondary bx bx-upload font-18"></i></a>';
            }

            echo '<tr>
                    <td style="font-size: smaller;"><span class="badge bg-danger">' . $x . '</span></td>
                    <td style="font-size: smaller;">' . $row['position'] . '</td>
                    <td style="font-size: smaller;">' . $company . '</td>
                    <td style="font-size: smaller;">' . $stores . '</td>
                    <td style="font-size: smaller;">' . $dept . '</td>
                    <td style="font-size: smaller;">' . $row['current_status'] . '</td>
                    <td style="font-size: smaller;">' . date('m/d/Y', strtotime($row['startdate'])) . '</td>
                    <td style="font-size: smaller;">' . $eocdate . '</td>
                    <td class="text-center">
                        <div class="d-md-flex d-grid align-items-center gap-1 justify-content-center">
                            <a href="javascript:;" ' . $id . ' onclick="viewContract(this.id)"><i class="fadeIn animated bx bx-info-circle font-18"></i></a>';
            echo                $action;
            echo        '</div>';
            echo    '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    echo '<div class="d-md-flex d-grid align-items-center gap-1 justify-content-left mt-3">
            <span class="badge text-secondary"><b>LEGEND:</b></span>
            <span class="badge bg-success">C</span><span class="badge text-secondary">current</span>
            <span class="badge bg-danger">1</span><span class="badge text-secondary">previous</span>
            <i class="fadeIn animated bx bx-info-circle font-18 text-primary"></i><span class="badge text-secondary">view</span>
            <i class="fadeIn animated bx bx-edit font-18 text-primary"></i><span class="badge text-secondary">edit</span>
            <i class="fadeIn animated bx bx-upload font-18 text-primary"></i><span class="badge text-secondary">upload</span>';
    echo '</div>';
} else if ($request == 'empHistory') {
    echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
    echo '<table id="empHistory" class="table table-sm table-hover">
            <tr>
                <th>Company</th>
                <th>Position</th>
                <th>Date Start</th>
                <th>Date End</th>
                <th>Address/Location</th>
                <th class="text-center">Certificate</th>
                <th class="text-center">Action</th>
            </tr>';
    foreach ($empHistory as $value) {

        $path1  = FCPATH . 'document/employment_certificate/' . $value['emp_certificate'];
        $path2  = FCPATH . '../promo/' . $value['emp_certificate'];
        if (file_exists($path1)) {
            $link = base_url('/document/employment_certificate/') . $value['emp_certificate'];
        } else if (file_exists($path2)) {
            $link = base_url('../promo/') . $value['emp_certificate'];
        } else {
            $link = base_url('../document/employment_certificate/') . $value['emp_certificate'];
        }

        $cert   = 'onclick=viewCertificate(\'' . $link  . '\')';
        $modal  = 'onclick="modal_form(this.id, \'' . $value['no'] . '\', \'' . $request . '\')"';
        echo '<tr>
                <td>' . $value['company'] . '</td>
                <td>' . $value['position'] . '</td>
                <td>' . $value['yr_start'] . '</td>
                <td>' . $value['yr_ends'] . '</td>
                <td>' . $value['address'] . '</td>
                <td class="text-center">
                    <a href="javascript:;" title="view Certificate" ' . $cert . '><i class="bx bx-image-alt me-0 font-22"></i></a>
                </td>
                <td class="text-center">
                    <a href="javascript:;" title="edit Certificate" id="edit" ' . $modal . '><i class="bx bx-edit me-0 font-22"></i></a>
                </td>';
        echo '</tr>';
    }
    echo '</table>';
    $modal = 'onclick="modal_form(this.id, \'' . $emp_id . '\', \'' . $request . '\')"';
    echo '<a href="javascript:;" class="btn btn-primary pull-right" id="add" ' . $modal . '>Add</a>';
} else if ($request == 'transferHistory') {
    echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
    echo '<table id="clearanceHistory" class="table table-sm table-hover">
            <tr>
                <th>No.</th>
                <th>Effectivity</th>
                <th>TransferFrom</th>
                <th>TransferTo</th>
                <th>OldPosition</th>
                <th>NewPosition</th>
                <th>DirectSupervisor</th>
                <th>JobTrans</th>
            </tr>';

    $i = 0;
    foreach ($transfer as $value) {
        echo '<tr>
                <td>' . $i++ . '.</td>
                <td>' . $value['effectiveon'] . '</td>
                <td>' . $value['old_location'] . '</td>
                <td>' . $value['new_location'] . '</td>
                <td>' . $value['old_position'] . '</td>
                <td>' . $value['position'] . '</td>
                <td>' . $value['supervision'] . '</td>
                <td></td>';
        echo '<tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else if ($request == 'blacklistHistory') {
    echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
    echo '<table id="blacklistHistory" class="table table-sm table-hover">
            <tr>
                <th>Date Blacklisted</th>
                <th>Reported By</th>
                <th>Reason</th>
            </tr>';

    foreach ($blacklist as $value) {
        echo '<tr>
                <td>' . $value['date_blacklisted'] . '</td>
                <td>' . $value['reportedby'] . '</td>
                <td>' . $value['reason'] . '</td>';
        echo '<tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else if ($request == 'clearanceHistory') {
    echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
    echo '<table id="clearanceHistory" class="table table-sm table-hover">
            <tr>
                <th>Reason</th>
                <th>Store</th>
                <th>Date Secured</th>
                <th>Date Effective</th>
                <th class="text-center">Resignation Letter</th>
                <th class="text-center">Status</th>
                <th>Added By</th>
            </tr>';

    foreach ($clearance as $value) {
        $name   = $this->promo_model->getName_employee3($value['added_by']);
        $color  = ($value['clearance_status'] == 'Completed') ? 'bg-success' : 'bg-warning';
        $status = '<span class="badge ' . $color . '">' . $value['clearance_status'] . '</span>';

        $letter = $value['resignation_letter'];
        $path1  = FCPATH . 'document/resignation/' . $letter;
        $path2  = FCPATH . '../promo/' . $letter;
        if (file_exists($path1)) {
            $link = base_url('/document/resignation/') . $letter;
        } else if (file_exists($path2)) {
            $link = base_url('../promo/') . $letter;
        } else {
            $link = base_url('../document/resignation/') . $letter;
        }

        if ($value['reason'] != 'Termination') {
            $vLetter = '<a href="javascript:;" title="view Letter" onclick=view_letter(\'' . $link . '\')><i class="bx bx-file me-0 font-22"></i></a>';
        } else {
            $vLetter = '<i class="bx bx-file me-0 font-22 text-secondary"></i>';
        }

        echo '<tr>
                <td>' . $value['reason'] . '</td>
                <td>' . $value['store'] . '</td>
                <td>' . $value['date_secure'] . '</td>
                <td>' . $value['date_effectivity'] . '</td>
                <td class="text-center">' . $vLetter . '</td>
                <td class="text-center">' . $status . '</td>
                <td>' . ucwords(strtolower($name)) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';

    if (in_array($this->userId, $this->adminUser)) {
        $cond = array('emp_id' => $emp_id, 'status' => 'Pending');
        $query = $this->promo_model->selectAll_tcA('promo_clearance_monitoring', $cond);
        if ($query) {
            echo '<table class="table table-sm table-hover">
                    <tr>
                        <th>SCPRID</th>
                        <th>SCDETAILSID</th>
                        <th>RECORDNO</th>
                        <th>DATEEFFECTIVE</th>
                        <th>STORE</th>
                    </tr>';
            foreach ($query as $key => $row) {
                echo    '<tr>
                            <td>' . $row['scpr_id'] . '</td>
                            <td>' . $row['scdetails_id'] . '</td>
                            <td>' . $row['record_no'] . '</td>
                            <td>' . $row['date_effectivity'] . '</td>
                            <td>' . $row['store'] . '</td>
                        </tr>';
            }
            echo '</table>';
        } else {
            echo '<button type="button" onclick="addtoExtend(\'' . $emp_id . '\')" class="btn btn-primary">Add to Extend</button>';
        }
    }
} else if ($request == 'benefits') { ?>
    <form id="benefits" autocomplete="off">
        <div class="row">
            <input type="hidden" name="emp_id" value="<?= $emp_id ?>">
            <div class="col-lg-12">
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label>Philhealth No.</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-id-card font-22"></i>
                            <input type="text" class="form-control" name="philhealth" value="<?= $benefits['philhealth'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label>SSS No.</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-id-card font-22"></i>
                            <input type="text" class="form-control" name="sss_no" value="<?= $benefits['sss_no'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label>SSS Card No.</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-id-card font-22"></i>
                            <input type="text" class="form-control" name="card_no" value="<?= $benefits['card_no'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label>Pag-ibig No.</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-id-card font-22"></i>
                            <input type="text" class="form-control" name="pagibig" value="<?= $benefits['pagibig'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label>Pag-ibig RTN</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-id-card font-22"></i>
                            <input type="text" class="form-control" name="pagibig_tracking" value="<?= $benefits['pagibig_tracking'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label>TIN No.</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-id-card font-22"></i>
                            <input type="text" class="form-control" name="tin_no" value="<?= $benefits['tin_no'] ?>" disabled>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <label>Cedula No.</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-id-card font-22"></i>
                            <input type="text" class="form-control" name="cedula_no" value="<?= $benefits['cedula_no'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label>Cedula Date</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-calendar font-22"></i>
                            <input type="text" class="form-control datepicker" name="cedula_date" value="<?= $benefits['cedula_date'] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <label>Cedula Address</label>
                        <div class="input-group mb-3">
                            <i class="input-group-text fadeIn animated bx bx-id-card font-22"></i>
                            <input type="text" class="form-control" name="cedula_place" value="<?= $benefits['cedula_place'] ?>" disabled>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-md-flex d-grid align-items-center gap-2 justify-content-end">
                            <button type="button" class="btn btn-primary" id="edit" onclick="profileData_edit(this.id, '<?= $request ?>')">Edit</button>
                            <button type="submit" class="btn btn-primary" style="display: none;" id="submit">Update</button>
                            <button type="button" class="btn btn-secondary" style="display: none;" id="cancel" onclick="profileData_edit(this.id, '<?= $request ?>')">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php
} else if ($request == 'supervisor') {
    echo '<ul class="list-unstyled">';
    foreach ($supervisor as $value) {
        $row    = $this->promo_model->selectAll_tcR('employee3', array('emp_id' => $value['ratee']));
        $app    = $this->promo_model->selectAll_tcR('applicant', array('app_id' => $value['ratee']));
        $name   = ucwords(strtolower($this->promo_model->getName_employee3($value['ratee'])));
        $empId  = $value['ratee'];
        $stat   = $row['current_status'];
        $color  = ($stat == 'Active') ? 'bg-success' : 'bg-warning';
        $photo  = base_url('assets/promo_assets/images/profile.png');
        if ($app['photo'] != '') {
            $url   = FCPATH . $app['photo'];
            $check = @file_get_contents($url);
            if ($check !== false) {
                $photo = base_url($app['photo']);
            }
        }

        $pos    = $row['position'];
        $bc     = $row['bunit_code'];
        $cc     = $row['company_code'];
        $cond   = array('bunit_code' => $bc, 'company_code' => $cc);
        $store  = $this->promo_model->selectAll_tcR('locate_business_unit', $cond)['business_unit'];
        $onclk  = 'onclick="modal_form(this.id, \'' . $value['record_no'] . '\', \'' . $request . '\')"';
        echo '<li class="d-flex align-items-center border-bottom pb-2 my-2">';
        echo    '<img src="' . $photo . '" class="rounded-circle p-1 border" width="90" height="90" alt="...">';
        echo    '<div class="flex-grow-1 ms-3">';
        echo        '<h5 class="mt-0 mb-1">' . $empId . ' | ' . $name . '</h5>';
        echo        '<p class="mb-1"><i>' . $pos . '  [ ' . $store . ' ]</i></p>';
        echo        '<span class="badge ' . $color . ' rounded-pill">' . $stat . '</span>';
        echo    '</div>';
        echo    '<div class="d-grid">';
        echo        '<a href="javascript:;" class="btn btn-danger" id="remove" ' . $onclk . '">Remove</a>';
        echo    '</div>';
        echo '</li>';
    }
    echo '</ul>';
    $onclk = 'onclick="modal_form(this.id, \'' . $emp_id . '\', \'' . $request . '\')"';
    echo '<div class="row mt-1">';
    echo    '<div><a href="javascript:;" class="btn btn-primary pull-left px-4" id="add" ' . $onclk . '>Add</a></div>';
    echo '</div>';
} else if ($request == 'remarks') {
    echo '<form id="remarks" autocomplete="off">';
    echo '<div class="row">';
    echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
    echo '<div class="col-lg-12">
            <div class="row mb-1">
                <div class="col-sm-12">
                    <div class="input-group mb-1">
                        <textarea name="remarks" class="form-control" cols="30" rows="13" disabled>' . $remarks['remarks'] . '</textarea>
                    </div>
                </div>';
    echo    '</div>';
    echo    '<div class="row">
                <div class="col-md-12">
                    <div class="d-md-flex d-grid align-items-center gap-2 justify-content-end">
                        <button type="button" class="btn btn-primary" id="edit" onclick="profileData_edit(this.id, \'' . $request . '\')">Edit</button>
                        <button type="submit" class="btn btn-primary" style="display: none;" id="submit">Update</button>
                        <button type="button" class="btn btn-secondary" style="display: none;" id="cancel" onclick="profileData_edit(this.id, \'' . $request . '\')">Cancel</button>
                    </div>
                </div>';
    echo    '</div>';
    echo '</div>';
    echo '</div>';
    echo '</form>';
} else if ($request == 'userAccount') {
    echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
    echo '<table id="empHistory" class="table table-hover">
            <tr>
                <th>Username</th>
                <th>User Type</th>
                <th class="text-center">isLogIn</th>
                <th>Date Created</th>
                <th class="text-center">Account Status</th>
                <th class="text-center">Action</th>
            </tr>';

    foreach ($userAccount as $value) {

        $loginBadge     = ($value['login'] == 'yes') ? 'badge bg-success' : 'badge bg-danger';
        $statusBadge    = ($value['user_status'] == 'active') ? 'badge bg-success' : 'badge bg-danger';
        $update         = ($value['user_status'] == 'active') ? 'deactivate' : 'activate';
        $bulbColor      = ($value['user_status'] == 'active') ? 'warning' : 'secondary';
        $userNo         = $value['user_no'];

        $clk = 'onclick="userAccount(this.id, \'' . $userNo . '\', \'' . $request . '\')"';
        echo '<tr>
                <td>' . $value['username'] . '</td>
                <td>' . strtoupper($value['usertype']) . '</td>
                <td class="text-center"><span class="' . $loginBadge . '">' . $value['login'] . '</span></td>
                <td>' . date('F j, Y', strtotime($value['date_created'])) . '</td>
                <td class="text-center"><span class="' . $statusBadge . '">' . $value['user_status'] . '</span></td>
                <td class="text-center">
                    <a href="javascript:;" data-toggle="tooltip" title="' . $update . ' account" id="' . $update . '" ' . $clk . '>
                        <i class="bx bx-bulb me-0 font-22 text-' . $bulbColor . '"></i>
                    </a>
                    <a href="javascript:;" data-toggle="tooltip" title="reset account" id="reset" ' . $clk . '>
                        <i class="fadeIn animated bx bx-reset font-22"></i>
                    </a>';
        if (in_array($this->userId, $this->adminUser)) {
            echo    '<a href="javascript:;" data-toggle="tooltip" title="delete account" id="remove" ' . $clk . '>
                        <i class="fadeIn animated bx bx-trash font-22 text-danger"></i>
                    </a>';
        }
        echo    '</td>';
        echo '</tr>';
    }
    echo '</table>';
    if (!$userAccount) {
        $clk = 'onclick="userAccount(this.id, \'' . $emp_id . '\', \'' . $request . '\')"';
        echo '<div>
                <a href="javascript:;" class="btn btn-primary pull-left" id="add" ' . $clk . '>Add</a>';
        echo '</div>';
    }
} else if ($request == '201doc') {
    echo '<div class="row row-cols-xl-4">';
    echo '<style>
            .alert-counts {
                position: absolute;
                top: -50px;
                right: 15px;
                background-color: red;
                color: white;
                font-size: 12px;
                width: 25px;
                height: 25px;
                padding: 2px 6px;
                border-radius: 50%;
                transform: translate(50%, -50%);
                font-weight: 500;
                display: flex;
                align-items: center;
                justify-content: center;
            }';
    echo '</style>';
    foreach ($docFile as $value) {

        $total      = 0;
        $name201    = $value['201_name'];
        $no         = $value['no'];

        if ($no == 15) {
            $query = $this->promo_model->get201_file($value, $emp_id, null, $no);
        } else if ($no == 27) {
            $query = $this->promo_model->get201_resignation($emp_id);
        } else {
            $query = $this->promo_model->get201_file($value, $emp_id);
        }

        $total = count($query);
        echo '<div class="col">';
        echo '<div class="card border shadow-none radius-10">';
        echo '<div class="card-body">
                <p class="card-title text-center mb-0 fw-bold">' . $name201 . '</p>
                <hr class="mt-1 mb-2">
        		<div class="text-center">
                    <i class="bx bxs-folder text-warning" style="font-size: 70px;"></i>
        		    <div style="position: relative;display: inline-block;">
                        <span class="alert-counts">' . $total . '</span>
                    </div>
                    <div class="d-flex justify-content-center align-items-center ms-auto font-13 gap-2" >';

        $uploadbtn = '<span class="border px-1  rounded"><i class="bx bxs-circle me-1 text-secondary"></i>Upload</span>';
        if ($no != 27) {
            $clk = 'onclick="docFile_uploadForm(\'' . $name201 . '\',\'' . $emp_id . '\', \'' . $no . '\')"';
            $uploadbtn = '<span class="border px-1 rounded cursor-pointer" ' . $clk . '><i class="bx bxs-circle me-1 text-primary"></i>Upload</span>';
        }

        $viewbtn = '<span class="border px-1  rounded"><i class="bx bxs-circle me-1 text-secondary"></i>View</span>';
        if ($total != 0) {
            $clk = 'onclick="docFile_view(\'' . $name201 . '\',\'' . $emp_id . '\', \'' . $no . '\')"';
            $viewbtn = '<span class="border px-1  rounded cursor-pointer" ' . $clk . '><i class="bx bxs-circle me-1 text-success"></i>View</span>';
        }

        echo $uploadbtn;
        echo $viewbtn;

        echo        '</div>';
        echo    '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
}
?>

<script>
    $(document).ready(function() {

        $('[data-toggle="tooltip"]').tooltip();
        $('span.spanTooltip').tooltip();
        $('td.pComp').tooltip();
        $('.datepicker').flatpickr();
        // $('.select2').select2({
        //     theme: "bootstrap-5",
        //     // width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        //     placeholder: $(this).data('placeholder'),
        //     tags: true,
        // });
        $('.select2').select2({
            theme: "bootstrap-5",
            placeholder: $(this).data('placeholder'),
            tags: true,
        }).on('select2:open', function(e) {
            $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style'
        });

        $('#editContract').on('shown.bs.modal', function() {
            $(".datepicker").flatpickr();
            $('.select2').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: $(this).data('placeholder'),
                dropdownParent: $('.editContract'),
            });

            $('.select2clear').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: $(this).data('placeholder'),
                dropdownParent: $('.editContract'),
                allowClear: true,
            });

            $('.select2clear').each(function() {
                $(this).nextAll('.select2.select2-container.select2-container--bootstrap-5:eq(1)').remove();
            });

            $('.form-check-input:not(:checked)').css('border-color', '#aaa');
            $('.form-check-input').change(function() {
                if (!$(this).is(':checked')) {
                    $(this).css('border-color', '#aaa');
                } else {
                    $(this).css('border-color', '');
                }
            });
        });

        $('#modal_form').on('shown.bs.modal', function() {
            $(".datepicker").flatpickr();
            $('.select2').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: $(this).data('placeholder'),
                dropdownParent: $('.modal_form'),
            });
        });

        $('#supervisor_form').on('shown.bs.modal', function() {
            $(".datepicker").flatpickr();
            $('.select2').select2({
                theme: "bootstrap-5",
                width: '100%',
                placeholder: $(this).data('placeholder'),
                dropdownParent: $('.supervisor_form'),
            });
        });

        $("form#basicInfo").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fields = [];
            let notRequired = ['suffix', 'mn', 'bloodtype']
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
                    text: 'Update basic information data.',
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
                            text: 'Updating data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/basicInfo') ?>",
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
                                                text: 'Basic information data has been updated.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    profileData('basicInfo')
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

        $("form#contactInfo").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fields = [];
            let notRequired = ['telno', 'email', 'facebookAcct', 'twitterAcct', 'contact_person', 'contact_person_number', 'contact_person_address']
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
                    text: 'Confirm update of contact and address information.',
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
                            text: 'Updating data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/contactInfo') ?>",
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
                                                text: 'Contact and address information has been updated.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    profileData('contactInfo')
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

        $("form#fesc").submit(function(e) {
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
                    text: 'Confirm update of family/educational background and skills/competencies?',
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
                            text: 'Updating data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/famEducBackground') ?>",
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
                                                text: 'Family/educational background and skills/competencies has been updated.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    profileData('fesc')
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

        $("form#appHistory").submit(function(e) {
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
                    text: 'Confirm update of application history.',
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
                            text: 'Updating data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/appHistory') ?>",
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
                                                text: 'Application history has been updated.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    profileData('appHistory')
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

        $("form#benefits").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var fields = [];
            let notRequired = ['philhealth', 'sss_no', 'card_no', 'pagibig', 'pagibig_tracking', 'tin_no', 'cedula_no', 'cedula_date', 'cedula_place']
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
                    title: 'Confirm!',
                    text: 'Confirm udpate of benefits information.',
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
                            text: 'Updating data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/benefits') ?>",
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
                                                text: 'Benefits information has been updated.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    profileData('benefits')
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

        $("form#docFile_upload").submit(function(e) {
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
                    title: 'Confirm!',
                    text: 'Confirm upload of 201 document.',
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
                            text: 'Updating data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/docFile_upload') ?>",
                                type: 'POST',
                                data: formData,
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    try {
                                        let response = JSON.parse(data);
                                        if (response.message == 'success') {
                                            $("div#docFile_uploadForm").modal("hide");
                                            Swal.fire({
                                                title: 'Success!',
                                                text: '201 Document has been uploaded.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    profileData('201doc')
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

        $("form#remarks").submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var remarks = $('textarea[name="remarks"]').val().trim();
            if (remarks == '') {
                $("textarea[name='remarks']").css("border-color", "#dd4b39");
                $("textarea[name='remarks']").on("focus", function() {
                    $(this).css("border-color", "");
                });
                alertRequired()
            } else {
                Swal.fire({
                    title: 'Confirm!',
                    text: 'Confirm update of remarks.',
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
                            text: 'Updating data...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        setTimeout(() => {
                            $.ajax({
                                url: "<?= site_url('promo/remarks') ?>",
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
                                                text: 'Remarks has been updated.',
                                                icon: 'success',
                                                iconColor: '#15ca20',
                                                confirmButtonColor: '#3085d6',
                                                confirmButtonText: 'Ok',
                                                allowOutsideClick: false,
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    profileData('remarks')
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

        $("form#save_userAccount").submit(function(e) {
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
                    title: 'Confirm!',
                    text: 'Confirm adding of user account.',
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
                            text: 'Adding user account...',
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
                                            if (response.checkUser) {
                                                alertInfo('Username not available!')
                                                $('input[name="username"]').css('border-color', '#dd4b39');
                                                $('input[name="username"]').prop('readonly', false);
                                            } else {
                                                Swal.fire({
                                                    title: 'Success!',
                                                    text: 'User account has been added!',
                                                    icon: 'success',
                                                    iconColor: '#15ca20',
                                                    confirmButtonColor: '#3085d6',
                                                    confirmButtonText: 'Ok',
                                                    allowOutsideClick: false,
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $("div#userAccount").modal("hide");
                                                        profileData('userAccount')
                                                    }
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
    })
</script>