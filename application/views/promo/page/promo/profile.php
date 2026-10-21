<style>
    .required_alert {
        width: 300px;
        font-size: 12px;
    }

    .dropdown-list {
        position: absolute;
        z-index: 999;
        max-height: 150px;
        background-color: #565e64;
        width: 47%;
        overflow-y: auto;
    }

    .readonly[readonly] {
        background-color: #e9ecef;
    }

    ul.profile a:hover {
        background-color: #008cff;
        color: #fff;
    }

    .img-preview {
        width: 300px;
        height: 300px;
    }
</style>

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="row">
            <div class="col-md-3" style="padding-left: 0;">
                <div class="card">
                    <div class="card-body">
                        <!-- NOTE: $emp_id is from controller Page.php -->
                        <input type="hidden" name="emp_id" value="<?= $emp_id ?>">
                        <?php
                        $cond   = array('app_id' => $emp_id);
                        $app    = $this->promo_model->selectAll_tcR('applicant', $cond);
                        $cond   = array('e.emp_id' => $emp_id);
                        $row    = $this->promo_model->promoDetails_wttR($cond, 'employee3', 'promo_record');
                        if (!$row) {
                            $cond = array('emp_id' => $emp_id);
                            $row = $this->promo_model->selectAll_tcR('employee3', $cond);
                        }
                        $cutoff     = 'No Cutoff';
                        $cond       = array('empId' => $emp_id, 'recordNo' => $row['record_no']);
                        $statCut    = $this->promo_model->selectAll_tcR_tk('promo_sched_emp', $cond)['statCut'];
                        if ($statCut) {
                            $cond   = array('statCut' => $statCut);
                            $co     = $this->promo_model->selectAll_tcR_tk('promo_schedule', $cond);
                            $endFC  = ($co['endFC'] != '') ? $co['endFC'] : 'Last';
                            $cutoff = '<span class="badge bg-secondary">' . $co['startFC'] . ' - ' . $endFC . ' / ' . $co['startSC'] . ' - ' . $co['endSC'] . '</span>';
                        }

                        $bdayClass = 'text-secondary mb-0';
                        $message = '';
                        if ($app['birthdate'] != '0000-00-00' && $app['birthdate'] != 'NULL' && $app['birthdate'] != '') {
                            $birthdate          = $app['birthdate'];
                            $currentDate        = date('Y-m-d');
                            $age                = floor((strtotime($currentDate) - strtotime($birthdate)) / 31556926);
                            $birthdateArray     = explode('-', $birthdate);
                            $currentDateArray   = explode('-', $currentDate);
                            $isBirthday         = ($currentDateArray[1] == $birthdateArray[1] && $currentDateArray[2] == $birthdateArray[2]);

                            if ($isBirthday) {
                                $bdayClass = 'text-danger mb-0';
                                $message = '<i class="bx bx-cake"></i> ' . $age . ' years old.';
                            } else {
                                $bdayClass = 'text-secondary mb-0';
                                $message = $age . ' years old.';
                            }
                        }

                        $photo = base_url('assets/promo_assets/images/profile.png');
                        if ($app['photo'] != '') {
                            $url   = FCPATH . $app['photo'];
                            $check = @file_get_contents($url);
                            if ($check !== false) {
                                $photo = base_url($app['photo']);
                            }
                        }

                        $mn         = ($app['middlename'] != '') ? substr($app['middlename'], 0, 1) . '.' : '';
                        $name       = ucwords(strtolower($app['lastname'] . ', ' . $app['firstname'] . ' ' . $app['suffix'] . ' ' . $mn));
                        $name       = $row['name'];
                        $position   = $row['position'];
                        $agency     = 'No Agency';

                        $style = '';
                        $sStat  = $row['sub_status'];
                        $status = $row['current_status'];
                        if ($status == 'Active') {
                            $stat = 'success';
                        } else if ($status == 'blacklisted') {
                            $stat = 'danger';
                        } else if ($status == 'Deceased') {
                            $stat = 'secondary';
                            $style = 'style="filter: grayscale(100%);"';
                        } else {
                            $stat = 'warning';
                        }

                        if ($sStat) {
                            $status = $row['current_status'] . ' <i>(' . $sStat . ')</i>';
                        }

                        if ($row['emp_type'] == 'Promo' || $row['emp_type'] == 'Promo-NESCO' || $row['emp_type'] == 'Promo-EasyL') {
                            $company    = @$row['promo_company'];
                            $department = @$row['promo_department'];
                            if (@$row['agency_code'] != 0 && @$row['agency_code'] != '') {
                                $get_agency = $this->promo_model->selectAll_tcR_tk('promo_locate_agency', array('agency_code' => $row['agency_code']));
                                $agency     = $get_agency['agency_name'];
                            }

                            $i          = 0;
                            $stores     = '';
                            $record_no  = $row['record_no'];
                            $bus        = $this->promo_model->locate_promo_bu($this->hrCode);
                            foreach ($bus as $bu) {
                                $bn = $bu['bunit_name'];
                                $ba = $bu['bunit_acronym'];
                                $hasBu = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
                                if ($hasBu) {
                                    $i++;
                                    if ($i == 1) {
                                        $stores = '<span class="spanTooltip badge bg-secondary" title="' . $bn . '" style="font-size: 10px;">' . $ba . '</span>';
                                    } else {
                                        $stores .= ' <span class="spanTooltip badge bg-secondary" title="' . $bn . '" style="font-size: 10px;">' . $ba . '</span>';
                                    }
                                }
                            }
                        } else {
                            $cc = $row['company_code'];
                            $bc = $row['bunit_code'];
                            $dc = $row['dept_code'];
                            $sc = $row['section_code'];
                            $cond       = array('company_code' => $cc);
                            $company    = $this->promo_model->selectAll_tcR('locate_company', $cond)['company'];
                            $cond       = array('company_code' => $cc, 'bunit_code' => $bc);
                            $stores     = $this->promo_model->selectAll_tcR('locate_business_unit', $cond)['business_unit'];
                            $cond       = array('company_code' => $cc, 'bunit_code' => $bc, 'dept_code' => $dc);
                            $department = $this->promo_model->selectAll_tcR('locate_department', $cond)['dept_name'];
                        }

                        $type       = $row['emp_type'] . ' (' . @$row['type'] . ')';
                        $contract   = date('m/d/Y', strtotime($row['startdate']))  . ' - ' . date('m/d/Y', strtotime($row['eocdate']));
                        if ($row['eocdate'] == '' || $row['eocdate'] == '0000-00-00') {
                            $contract   = date('m/d/Y', strtotime($row['startdate']));
                        }


                        $bloodtype_array    = array('A', 'A+', 'A-', 'B', 'B+', 'B-', 'O', 'O+', 'O-', 'AB', 'AB+', 'AB-');
                        $cs_array           = array('Single', 'Married', 'Widowed', 'Separated', 'Anulled', 'Divorced');
                        ?>
                        <div class="d-flex flex-column align-items-center text-center">
                            <a href="javascript:;" id="<?= $photo ?>" onclick="profilePic(this.id)">
                                <img src="<?= $photo ?>" alt="img" class="rounded-circle shadow p-1 border" <?= $style ?> width="150" height="150">
                            </a>
                            <div class="mt-2">
                                <h6 class="mb-0"><?= $name ?></h6>
                                <p class="text-secondary mb-0"><?= $emp_id ?></p>
                                <p class="<?= $bdayClass ?>"><?= $message ?></p>
                                <span class="spanTooltip badge mb-2 bg-<?= $stat ?>"><?= $status ?></span>
                            </div>
                        </div>
                        <hr class="mt-0 mb-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item justify-content-between gap-3 mt-0" style="display: flex;padding: 5px 0px 5px;">
                                <i class="fadeIn animated bx bx-server font-22 text-primary"></i>
                                <span class="text-secondary" style="font-size: x-small;"><?= $row['emp_no'] ?></span>
                            </li>
                            <li class="list-group-item justify-content-between gap-3 mt-0" style="display: flex;padding: 5px 0px 5px;">
                                <i class="fadeIn animated bx bx-user-pin font-22 text-primary"></i>
                                <span class="text-secondary" style="font-size: x-small;"><?= $agency ?></span>
                            </li>
                            <li class="list-group-item justify-content-between gap-3" style="display: flex;padding: 5px 0px 5px;">
                                <i class="fadeIn animated bx bx-buildings font-22 text-primary"></i>
                                <span class="text-secondary" style="font-size: x-small;"><?= $company ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" style="padding: 5px 0px 5px;">
                                <i class="fadeIn animated bx bx-store font-22 text-primary"></i>
                                <span class="text-secondary" style="max-width: 220px;"><?= $stores ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" style="padding: 5px 0px 5px;">
                                <i class="fadeIn animated bx bx-cart-alt font-22 text-primary"></i>
                                <span class="text-secondary"><?= $department ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" style="padding: 5px 0px 5px;">
                                <i class="fadeIn animated bx bx-id-card font-22 text-primary"></i>
                                <span class="text-secondary"><?= $position ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" style="padding: 5px 0px 5px;">
                                <i class="fadeIn animated bx bx-file font-22 text-primary"></i>
                                <span class="text-secondary"><?= $type ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" style="padding: 5px 0px 5px;">
                                <i class="fadeIn animated bx bx-calendar-week font-22 text-primary"></i>
                                <span class="text-secondary"><?= $cutoff ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" style="padding: 5px 0px 5px;">
                                <i class="fadeIn animated bx bx-calendar font-22 text-primary"></i>
                                <span class="text-secondary"><?= $contract ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-9" style="padding-left: 0;">
                <div class="card">
                    <div class="card-header bg-transparent">
                        <div class="d-flex align-items-center">
                            <div class="text-primary font-22">
                                <span class="profileData_title"></span>
                            </div>
                            <div class="ms-auto">
                                <select class="form-select form-select-sm" name="dataSelection" onchange="profileData(this.value)">
                                    <option value="basicInfo">Basic Information</option>
                                    <option value="contactInfo">Contact & Address Information</option>
                                    <option value="fesc">Family, Educational, Skills & Competencies</option>
                                    <option value="benefits">Benefits & Cedula Information</option>
                                    <option value="training">Eligibility/Seminars/Trainings</option>
                                    <option value="characterRef">Character References</option>
                                    <option value="eocAppraisal">EOC Appraisal History</option>
                                    <option value="appHistory">Application History</option>
                                    <option value="contractHistory">Contract History</option>
                                    <option value="empHistory">Employment History</option>
                                    <option value="transferHistory">Job Transfer History</option>
                                    <option value="blacklistHistory">Blacklist History</option>
                                    <option value="clearanceHistory">Clearance Processing History</option>
                                    <option value="201doc">201 Documents</option>
                                    <option value="supervisor">Peer-Subordinate-Supervisor</option>
                                    <option value="remarks">Remarks</option>
                                    <option value="userAccount">User Account</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body profileData"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="modal_form" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update/Add Data </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save_modal_form" autocomplete="off">
                <div class="modal-body modal_form">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="userAccount" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add User Account </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save_userAccount" autocomplete="off">
                <div class="modal-body">
                    <div class="userAccount"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="supervisor_form" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Supervisor </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save_supervisor_form" autocomplete="off">
                <div class="modal-body">
                    <div class="supervisor_form"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewExam_history" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Examination History </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewExam_history"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewExam" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Examination Details </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewExam"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewApp_details" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Application Details </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewApp_details"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewInt_details" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Interview Details </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewInt_details"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewContract" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contract Details </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <div class="viewContract"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editContract" tabindex="-1" style="display: none;" data-bs-focus="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Contract Details </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save_editContract" autocomplete="off">
                <div class="modal-body">
                    <div class="editContract"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadContract" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Scanned Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="save_uploadContract" autocomplete="off">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 mb-2">
                            <select class="form-select" name="filetype" onchange="contractFile(this.value)">
                                <option value="intro">Intro Files</option>
                                <option value="contract">Contract Files</option>
                                <option value="clearance">Clearance Files</option>
                                <option value="epas" disabled>EPAS Files</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3 contractFile"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewAppraisal" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appraisal Details </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewAppraisal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewCertificate" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Certificate </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body viewCertificate">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="view_letter" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Letter </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body view_letter">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewIntro" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Intro </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewIntro"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewClearance" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Clearance </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewClearance"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewContracts" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Contract </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewContracts"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="docFile_view" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title docFile_view">201 Document </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body docFile_view"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="docFile_uploadForm" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title docFile_uploadForm">201 Document Upload</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="docFile_upload" autocomplete="off">
                <!-- <input type="hidden" name="no" value="'">
                <input type="hidden" name="emp_id" value=""> -->
                <div class="modal-body docFile_uploadForm"></div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>