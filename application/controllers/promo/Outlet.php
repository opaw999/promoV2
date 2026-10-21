<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Outlet extends CI_Controller
{
    public $userId;
    public $hrCode;
    public $today;
    public $adminUser;
    function __construct()
    {
        parent::__construct();
        $this->userId   = $_SESSION['emp_id'];
        $this->hrCode   = $_SESSION['hrCode'];
        $this->today    = date('Y-m-d');
        $this->adminUser    = array('01186-2023');
        if (!isset($this->userId)) {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }
    }

    public function changeOutletHistory()
    {
        $cond = "1='1' ORDER BY change_no DESC";
        $list = $this->promo_model->selectAll_tcA('change_outlet_record', $cond);
        foreach ($list as $row) {
            $name       = $this->promo_model->getName_employee3($row['emp_id']);
            $column     = [];
            $column[]   = '<a href="' . base_url('promo/page/promo/profile/' . $row['emp_id']) . '" target="_blank">' . $row['emp_id'] . '</a>';
            $column[]   = ucwords(strtolower($name));
            $column[]   = date('m/d/Y', strtotime($row['effectiveon']));
            $column[]   = $row['changefrom'];
            $column[]   = $row['changeto'];
            $data[]     = $column;
        }
        echo json_encode(array('data' => $data));
    }

    public function searchPromoOutlet()
    {
        echo '<ul class="list-group">';
        $input  = $this->input->post(NULL, TRUE);
        $query  = $this->promo_model->searchPromoOutlet($input);
        if ($query) {
            foreach ($query as  $row) {
                $name       = ucwords(strtolower($row['name']));
                $emp_id     = $row['emp_id'];
                $record_no  = $row['record_no'];
                $onclick    = 'onclick="selectPromoOutlet(\'' . $name . '\',\'' . $emp_id . '\',\'' . $record_no . '\')"';
                echo '<a href="javascript:;" class="list-group-item" ' . $onclick . '>' . $name . '</a>';
            }
        } else {
            echo '<a href="javascript:;" class="list-group-item">No Results Found!</a>';
        }
        echo '</ul>';
    }

    public function selectPromoOutlet()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        $process    = $input['process'];

        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $row    = $this->promo_model->selectAll_tcR('employee3', $cond);

        if ($process == 'addOutlet') {
            echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
            echo '<input type="hidden" name="record_no" value="' . $record_no . '">';
            echo '<input type="hidden" name="duration" value="' . $row['duration'] . '">';
            echo '<input type="hidden" name="eocdate" value="' . $row['eocdate'] . '" disabled>';
            echo '<input type="hidden" name="setdate" value="' . $row['startdate'] . '" disabled>';
            echo '<div class="text-center text-secondary mb-2">';
            echo '<h6><strong>Check Store(s) to Add</strong></h6>';
            echo '</div>';
            echo '<div class="row">';

            $i      = 0;
            $stores = '';
            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $bu) {

                $hasBu = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
                if ($hasBu) {
                    $i++;
                    $stores = ($i == 1) ? $bu['bunit_name'] : $stores . ', ' . $bu['bunit_name'];
                }

                $checked    = ($hasBu) ? 'checked disabled' : '';
                $color      = ($hasBu) ? 'secondary' : 'primary';
                $field      = $bu['bunit_id'] . '|' . $bu['bunit_field'];

                echo '<div class="col-12 col-sm-4 mb-1">
                        <div class="form-check form-check-' . $color . '">
                            <input class="form-check-input" type="checkbox" name="stores[]" value="' . $field . '" ' . $checked . ' id="' . $bu['bunit_id'] . '" onclick="getIntros()">
                            <label class="form-check-label" for="' . $bu['bunit_id'] . '">
                                | <span class="badge bg-' . $color . '" style="width:150px">' . $bu['bunit_name'] . '</span>
                            </label>
                        </div>';
                echo '</div>';
            }
            echo '<input type="hidden" name="previous_stores" value="' . $stores . '">';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="intros"></div>';
            echo '</div>';
            echo '<div class="row">
                    <div class="col-sm-4 mt-2">
                        <label class="form-label text-secondary">Effective On</label>
                        <div class="input-group">
                            <i class="input-group-text fadeIn animated bx bx-calendar font-22 text-primary"></i>
                            <input type="text" class="form-control form-control-sm datepicker" name="startdate" value="' . date('Y-m-d') . '" placeholder="yyyy/mm/dd" onchange="getDuration(this.value)">
                        </div>
                    </div>';
            echo '</div>';
            echo '<div class="row">
                    <div class="col-sm-4 mt-3">
                        <button type="submit" class="btn btn-primary outletButton">Submit</button>
                    </div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="d-md-flex d-grid align-items-center gap-1 justify-content-between mt-2">
                    <ul class="list-unstyled">
                        <li>
                            <span class="badge text-secondary">
                                <b>NOTE:&nbsp;</b> <span class="badge bg-secondary"><i class="fa fa-check"></i></span>&nbsp;|&nbsp;STORE NAME
                            </span>
                        </li>
                        <ul>
                            <li><span class="badge text-secondary">Refers to the Current Contract Stores</span></li>
                            <li><span class="badge text-secondary">Current Contract Stores cannot be selected</span></li>
                        </ul>
                    </ul>';
            echo '</div>';
            echo '</div>';
        } else if ($process == 'removeOutlet') {
            echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
            echo '<input type="hidden" name="record_no" value="' . $record_no . '">';
            echo '<input type="hidden" name="duration" value="' . $row['duration'] . '">';
            echo '<input type="hidden" name="eocdate" value="' . $row['eocdate'] . '" disabled>';
            echo '<div class="text-center text-secondary mb-3">';
            echo '<h6><strong>Check Store(s) to Remove</strong></h6>';
            echo '</div>';
            echo '<div class="row">';

            $i = 0;
            $stores = '';
            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $bu) {

                $buId = $bu['bunit_id'];
                $buField = $bu['bunit_field'];
                $buName = $bu['bunit_name'];

                $hasBu = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $buField);
                if ($hasBu) {

                    $i++;
                    $onclick    = '';
                    $secured    = false;
                    $value      = $buId . '|' . $buField;
                    $stores     = ($i == 1) ? $buName : $stores . ', ' . $buName;
                    $clearance  = '| <span class="badge bg-secondary">Not Secured</span>';

                    $cond = array('emp_id' => $emp_id, 'promo_type' => 'ROVING', 'status' => 'Pending');
                    $scpr_id = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond)['scpr_id'];
                    if ($scpr_id) {
                        $cond = array('scpr_id' => $scpr_id, 'store' => $buName);
                        $chk = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                        if ($chk) {

                            $secured = true;
                            if ($chk['clearance_status'] == 'Pending') {
                                $onclick = 'onclick="getClearance()"';
                                $clearance = '| <span class="badge bg-info">Secured</span>';
                            } else {
                                $clearance = '| <span class="badge bg-info">Completed</span>';
                            }
                        }
                    } else {
                        $cond = array('emp_id' => $emp_id, 'record_no' => $record_no, 'store' => $buName);
                        $chk = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                        if ($chk) {
                            $clearance = '| <span class="badge bg-info">Completed</span>';
                            $secured = true;
                        }
                    }

                    $signOff    = false;
                    $grade      = false;

                    $epas   = '| <span class="badge bg-secondary">No EPAS</span>';
                    $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no, 'store' => $buName);
                    $appDet = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
                    if ($appDet) {
                        $appId      = $appDet['details_id'];
                        $signOff    = ($appDet['raterSO'] == 1 && $appDet['rateeSO'] == 1) ? true : false;
                        $grade      = ($appDet['numrate'] >= 85 && $appDet['numrate'] <= 100) ? true : false;

                        if ($signOff && $grade) {
                            $color = 'success';
                        } else if (!$signOff && $grade) {
                            $color = 'warning';
                        } else {
                            $color = 'danger';
                        }

                        $rate = $appDet['numrate'] . ' ' . $appDet['descrate'];
                        $epas = ' | <span class="badge bg-' . $color . '"><a href="javascript:;" class="text-white" id="' . $appId . '" onclick="viewAppraisal(this.id)">' . $rate . '</a></span>';
                    }

                    $checkbox = '<input name="stores[]" value="' . $value . '" class="form-check-input" type="checkbox" disabled> | <span class="badge bg-secondary" style="width:130px;">' . $buName . '</span>';

                    if ($signOff && $grade && $secured) {

                        $checkbox = '<div class="form-check form-check-primary">
                                    <input class="form-check-input" type="checkbox" name="stores[]" value="' . $value . '" id="' . $buId . '" ' . $onclick . '>
                                    <label class="form-check-label" for="' . $buId . '">
                                         | <span class="badge bg-primary" style="width:130px;">' . $buName . '</span>
                                    </label>
                                    </div>';
                    }
                    echo '<div class="col-12 col-sm-6 mb-1">
                            <div class="d-md-flex d-grid align-items-center gap-1 justify-content-left">
                            ' . $checkbox . ' ' . $epas . ' ' . $clearance . '
                            </div>';
                    echo '</div>';
                }
            }

            $cond = array('scpr_id' => $scpr_id);
            $sDate = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond)['date_effectivity'];
            if (!$sDate) {
                $sDate = date('Y-m-d');
            }

            echo '<input type="hidden" name="previous_stores" value="' . $stores . '">';
            echo '<input type="hidden" name="storeCount" value="' . $i . '">';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="clearance"></div>';
            echo '</div>';
            echo '<div class="row">
                    <div class="col-sm-4 mt-2">
                        <label class="form-label text-secondary">Effective On</label>
                        <div class="input-group">
                            <i class="input-group-text fadeIn animated bx bx-calendar font-22 text-primary"></i>
                            <input type="text" class="form-control form-control-sm datepicker" name="startdate" value="' . $sDate . '" placeholder="yyyy/mm/dd" onchange="getDuration(this.value)">
                        </div>
                    </div>';
            echo '</div>';
            echo '<div class="row">
                    <div class="col-sm-4 mt-3">
                     <button type="submit" class="btn btn-primary outletButton">Submit</button>
                    </div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="d-md-flex d-grid align-items-center gap-1 justify-content-between mt-2">
                    <ul class="list-unstyled">
                        <li>
                            <span class="badge text-secondary">
                                <b>NOTE:&nbsp;</b> <span class="badge bg-primary"><i class="fa fa-check"></i></span>
                                &nbsp;|&nbsp;STORE NAME&nbsp;|&nbsp;EPAS RATE&nbsp;|&nbsp;CLEARANCE
                            </span>
                        </li>
                        <ul>
                            <li><span class="badge text-secondary">Refers to the Current Contract Stores</span></li>
                            <li><span class="badge text-secondary">Requires Passing EPAS Rate and Signed Off</span></li>
                            <li><span class="badge text-secondary">Requires Uploaded Clearance</span></li>
                        </ul>
                    </ul>';
            echo '</div>';
            echo '</div>';
        } else if ($process == 'transferOutlet') {

            echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
            echo '<input type="hidden" name="record_no" value="' . $record_no . '">';
            echo '<input type="hidden" name="duration" value="' . $row['duration'] . '">';
            echo '<input type="hidden" name="eocdate" value="' . $row['eocdate'] . '" disabled>';
            echo '<input type="hidden" name="setdate" value="' . $row['startdate'] . '" disabled>';
            echo '<div class="text-center text-secondary mb-2">';
            echo '<h6><strong>Check Store(s) to Transfer From</strong></h6>';
            echo '</div>';
            echo '<div class="row">';
            $i = 0;
            $stores = '';
            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $bu) {

                $buId       = $bu['bunit_id'];
                $buField    = $bu['bunit_field'];
                $buName     = $bu['bunit_name'];

                $hasBu  = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $buField);
                $value  = $buId . '|' . $buField;
                if ($hasBu) {

                    $i++;
                    $stores         = ($i == 1) ? $buName : $stores . ', ' . $buName;

                    $secured        = false;
                    $clearance      = '| <span class="badge bg-secondary">Not Secured</span>';
                    $cond           = array('emp_id' => $emp_id, 'status' => 'Pending');
                    $scpr_id        = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond)['scpr_id'];
                    if ($scpr_id) {
                        $cond   = array('scpr_id' => $scpr_id, 'store' => $buName, 'clearance_status' => 'Pending');
                        $chk    = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                        if ($chk) {
                            $clearance  = '| <span class="badge bg-info">Secured</span>';
                            $secured    = true;
                        }
                    } else {
                        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no, 'store' => $buName);
                        $chk    = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                        if ($chk) {
                            $clearance  = '| <span class="badge bg-info">Completed</span>';
                            $secured    = true;
                        }
                    }

                    $signOff    = false;
                    $grade      = false;
                    $epas       = '| <span class="badge bg-secondary">No EPAS</span>';
                    $cond       = array('emp_id' => $emp_id, 'record_no' => $record_no, 'store' => $buName);
                    $appDetails = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
                    if ($appDetails) {

                        $signOff    = ($appDetails['raterSO'] == 1 && $appDetails['rateeSO'] == 1) ? true : false;
                        $grade      = ($appDetails['numrate'] >= 85 && $appDetails['numrate'] <= 100) ? true : false;

                        if ($signOff && $grade) {
                            $color = 'success';
                        } else if (!$signOff && $grade) {
                            $color = 'warning';
                        } else {
                            $color = 'danger';
                        }

                        $rate = $appDetails['numrate'] . ' ' . $appDetails['descrate'];

                        $epas = ' | <span class="badge bg-' . $color . '">
                                <a href="javascript:;" class="text-white" id="' . $appDetails['details_id'] . '" onclick="viewAppraisal(this.id)">
                                ' . $rate . '
                                </a>
                                </span>';
                    }

                    $checkbox = '<input name="stores[]" value="' . $value . '" class="form-check-input" type="checkbox" disabled> 
                                | <span class="badge bg-secondary" style="width:150px;">' . $buName . '</span>';
                    if ($signOff && $grade && $secured) {
                        $checkbox = '<div class="form-check form-check-primary">
                                        <input class="form-check-input" type="checkbox" name="stores[]" value="' . $value . '" id="' . $buId . '" onclick="getClearance()">
                                        <label class="form-check-label" for="' . $buId . '">
                                        | <span class="badge bg-primary" style="width:150px;">' . $buName . '</span>
                                        </label>
                                    </div>';
                    }

                    echo '<div class="col-12 col-sm-6 mb-1">
                            <div class="d-md-flex d-grid align-items-center gap-1 justify-content-left">
                            ' . $checkbox . '' . $epas . '' . $clearance . '
                            </div>';
                    echo '</div>';
                }
            }
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="clearance"></div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="text-center text-secondary mb-2"><hr>';
            echo '<h6><strong>Check Store(s) to Transfer To</strong></h6>';
            echo '</div>';

            foreach ($bus as $bu) {

                $hasBu  = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
                $value  = $bu['bunit_id'] . '|' . $bu['bunit_field'];
                if ($hasBu == 0) {
                    echo '<div class="col-12 col-sm-4 mb-1">
                            <div class="d-md-flex d-grid align-items-center gap-1 justify-content-left">
                                <div class="form-check form-check-primary">
                                    <input class="form-check-input" type="checkbox" name="transfer[]" value="' . $value . '" id="' . $bu['bunit_id'] . '" onclick="getIntros()">
                                    <label class="form-check-label" for="' . $bu['bunit_id'] . '">
                                    | <span class="badge bg-gradient-blues" style="width:150px;">' . $bu['bunit_name'] . '</span>
                                    </label>
                                </div>
                            </div>';
                    echo '</div>';
                }
            }

            echo '<input type="hidden" name="previous_stores" value="' . $stores . '">';
            echo '<input type="hidden" name="storeCount" value="' . $i . '">';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="intros"></div>';
            echo '</div>';
            echo '<div class="row">
                    <div class="col-sm-4 mt-2">
                    <label class="form-label text-secondary">Effective On</label>
                    <div class="input-group">
                        <i class="input-group-text fadeIn animated bx bx-calendar font-22 text-primary"></i>
                        <input type="text" class="form-control form-control-sm datepicker" name="startdate" placeholder="yyyy/mm/dd" onchange="getDuration(this.value)">
                    </div>
                    </div>';
            echo '</div>';
            echo '<div class="row">
                    <div class="col-sm-4 mt-3">
                    <button type="submit" class="btn btn-primary outletButton">Submit</button>
                    </div>';
            echo '</div>';
            echo '<div class="d-md-flex d-grid align-items-center gap-1 justify-content-between mt-2">
                    <ul class="list-unstyled">
                        <li>
                            <span class="badge text-secondary">
                                <b>NOTE:&nbsp;</b> <span class="badge bg-primary"><i class="fa fa-check"></i></span>
                                &nbsp;|&nbsp;STORE NAME&nbsp;|&nbsp;EPAS RATE&nbsp;|&nbsp;CLEARANCE
                            </span>
                        </li>
                        <ul>
                            <li><span class="badge text-secondary">Transfer From refers to the Current Contract Stores</span></li>
                            <li><span class="badge text-secondary">Transfer To refers to the New Store(s)</span></li>
                            <li><span class="badge text-secondary">Requires Passing EPAS Rate and Signed Off</span></li>
                            <li><span class="badge text-secondary">Requires Uploading of Clearance</span></li>
                        </ul>
                    </ul>';
            echo '</div>';
            echo '</div>';
        }
    }

    public function changeOutletDetails()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];

        $cond   = array('e.emp_id' => $emp_id, 'e.record_no' => $record_no);
        $row    = $this->promo_model->promoDetails_wttR($cond, 'employee3', 'promo_record');

        $i      = 0;
        $stores = '';
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {

            $str = '<span class="badge bg-secondary">' . $bu['bunit_acronym'] . '</span>';
            $hasBu = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
            if ($hasBu) {
                $i++;
                $stores = ($i == 1) ? $str : $stores . ' ' . $str;
            }
        }
        $status = '<font class="text-warning">' . $row['current_status'] . '</font>';
        if ($row['current_status'] == 'Active') {
            $status = '<font class="text-success">' . $row['current_status'] . '</font>';
        }

        echo '<div class="table-responsive">';
        echo '<table class="table table-sm mt-2" style="width:100%;">';

        echo '<tr>';
        echo '<th>Employee ID</th>';
        echo '<th>:</th>';
        echo '<td>' . $emp_id . ' (' . $status . ')</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th>Store(s)</th>';
        echo '<th>:</th>';
        echo '<td>' . $stores . '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th>Department</th>';
        echo '<th>:</th>';
        echo '<td>' . $row['promo_department'] . '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th>Promo Type</th>';
        echo '<th>:</th>';
        echo '<td>' . $row['promo_type'] . '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th>Contract Type</th>';
        echo '<th>:</th>';
        echo '<td>' . $row['type'] . '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th>Position</th>';
        echo '<th>:</th>';
        echo '<td>' . $row['position'] . '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th>Start Date</th>';
        echo '<th>:</th>';
        echo '<td>' . $row['startdate'] . '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th>EOC Date</th>';
        echo '<th>:</th>';
        echo '<td>' . $row['eocdate'] . '</td>';
        echo '</tr>';

        echo '</table">';
        echo '</div">';
    }

    public function getSelectOutlet()
    {
        $input = $this->input->post(NULL, TRUE);
        if ($input['action'] == 'getIntros') {
            if (isset($input['value'])) {
                $id = [];
                echo '<div class="row mt-2">
                        <strong class="mb-1">Upload Intros</strong>';
                foreach ($input['value'] as $value) {
                    $val    = explode('|', $value);
                    $id[]   = $val[0];
                    $intro = $this->promo_model->selectAll_tcR('locate_promo_business_unit', array('bunit_id' => $val[0]));
                    echo '<div class="col-sm-3 mb-2">
                            <label>' . $intro['bunit_name'] . '</label>
                            <input type="file" class="form-control form-control-sm" name="' . $intro['bunit_intro'] . '">';
                    echo '</div>';
                }
                echo '</div>';
            }
        } else  if ($input['action'] == 'getClearance') {
            if (isset($input['value'])) {
                $id         = [];
                echo '<div class="row mt-2">
                        <strong class="mb-1">Upload Clearance</strong>';
                foreach ($input['value'] as $value) {
                    $val    = explode('|', $value);
                    $id[]   = $val[0];
                    $intro = $this->promo_model->selectAll_tcR('locate_promo_business_unit', array('bunit_id' => $val[0]));
                    echo '<div class="col-sm-3 mb-2">
                            <label>' . $intro['bunit_name'] . '</label>
                            <input type="file" class="form-control form-control-sm" name="' . $intro['bunit_clearance'] . '">';
                    echo '</div>';
                }
                echo '</div>';
            }
        }
    }

    public function addOutlet()
    {
        $input      = $this->input->post(NULL, TRUE);
        $empId      = $input['emp_id'];
        $recordNo   = $input['record_no'];

        $i              = 0;
        $addedStores    = '';
        foreach ($input['stores'] as $value) {
            $str    = explode('|', $value);
            $cond   = array('bunit_id' => $str[0]);
            $bu     = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);
            if ($bu) {
                $i++;
                $addedStores = ($i == 1) ? $bu['bunit_name'] : $addedStores . ', ' . $bu['bunit_name'];
            }
        }

        // inserting of employmentrecord_ table ******************************************************************
        $data       = [];
        $fields     = $this->promo_model->emp3Fields();
        $cond       = array('emp_id' => $empId, 'record_no' => $recordNo);
        $oldEmp3    = $this->promo_model->selectAll_tcR('employee3', $cond);
        foreach ($oldEmp3 as $field => $value) {
            if (!in_array($field, $fields)) {
                if ($field == 'name') {
                    $field = 'names';
                } else if ($field == 'position_desc') {
                    $field = 'pos_desc';
                } else if ($field == 'updated_by') {
                    $field = 'updatedby';
                } else if ($field == 'current_status') {
                    $value = 'End of Contract';
                }
                $data[$field] = $value;
            }
        }

        $recordNo_ = $this->promo_model->insert_tdA('employmentrecord_', $data);
        // inserting of employmentrecord_ table ******************************************************************

        // inserting of promo_history_record table ******************************************************************
        $promo  = $this->promo_model->selectAll_tcR('promo_record', $cond);
        $data   = [];
        foreach ($promo as $field => $value) {
            $fields = array('promo_id');
            if (!in_array($field, $fields)) {

                if ($field == 'record_no') {
                    $data[$field] = $recordNo_;
                } else if ($field == 'promo_company') {
                    // $data[$field] = $this->db->escape_str($value);
                    $data[$field] = $value;
                } else {
                    $data[$field] = $value;
                }
            }
        }

        $promo_hist = $this->promo_model->insert_tdA('promo_history_record', $data);
        // inserting of promo_history_record table ******************************************************************       

        // inserting of employee3 table ******************************************************************
        $data   = [];
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $query  = $this->promo_model->selectAll_tcR('employee3', $cond);
        foreach ($query as $field => $value) {
            $fields = array('record_no');
            if (!in_array($field, $fields)) {
                if ($field == 'startdate') {
                    $value = $input['startdate'];
                } else if ($field == 'duration') {
                    $value = $input['duration'];
                } else if ($field == 'tag_as') {
                    $value = '';
                } else if ($field == 'remarks') {
                    $value = 'Added Store(s): ' . $addedStores;
                } else if ($field == 'date_added') {
                    $value = $this->today;
                } else if ($field == 'added_by') {
                    $value = $this->userId;
                }

                $data[$field] = $value;
            }
        }

        $record_no = $this->promo_model->insert_tdA('employee3', $data);
        // inserting of employee3 table ******************************************************************

        // inserting of promo_record table ******************************************************************
        $data   = [];
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $promo  = $this->promo_model->selectAll_tcR('promo_record', $cond);
        foreach ($promo as $field => $value) {
            $fields = array('promo_id');
            if (!in_array($field, $fields)) {

                if ($field == 'record_no') {
                    $value = $record_no;
                } else if ($field == 'promo_company') {
                    // $value = $this->db->escape_str($value);
                } else if ($field == 'promo_type') {
                    $value = 'ROVING';
                } else if ($field == 'transferOn') {
                    $value = $input['startdate'];
                }

                $data[$field] = $value;
            }
        }

        foreach ($input['stores'] as $value) {
            $field = explode('|', $value);
            $data[$field[1]] = 'T';
        }

        $promo_record = $this->promo_model->insert_tdA('promo_record', $data);
        // inserting of promo_record table ******************************************************************

        // uploading of intro ******************************************************************
        $intro  = $this->uploadIntro($input, $record_no);
        // uploading of intro ******************************************************************

        // updating of appraisal details table ******************************************************************
        $data   = array('record_no' => $record_no);
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $update = $this->promo_model->update_twdA('appraisal_details', $cond, $data);
        // updating of appraisal details table ******************************************************************

        // updating of employment_witness table ******************************************************************
        $data   = array('rec_no' => $record_no);
        $cond   = array('emp_id' => $empId, 'rec_no' => $recordNo);
        $update = $this->promo_model->update_twdA('employment_witness', $cond, $data);
        // updating of employment_witness table ******************************************************************

        // updating of promo_products table ******************************************************************
        $data   = array('record_no' => $record_no);
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $update = $this->promo_model->update_twdA('promo_products', $cond, $data);
        // updating of promo_products table ******************************************************************

        // updating of promo_schedule table ******************************************************************
        $data   = array('record_no' => $record_no);
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $update = $this->promo_model->update_twdA('promo_schedule', $cond, $data);
        // updating of promo_schedule table ******************************************************************

        // inserting and updating of promo_sched_emp table ******************************************************************
        $i          = 0;
        $changeto   = '';
        $servers    = ['corporate'];
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $field = $bu['bunit_field'];
            $hasBu = $this->promo_model->empStores('promo_record', $empId, $record_no, $field);
            if ($hasBu) {
                $i++;
                $changeto = ($i == 1) ? $bu['bunit_name'] : $changeto . ', ' . $bu['bunit_name'];
                if ($field == 'al_tal') {
                    $servers[] = 'talibon';
                } else if ($field == 'al_tub' || $field == 'fr_tubigon') {
                    $servers[] = 'tubigon';
                }
            }
        }

        $data = array(
            'emp_id'        => $empId,
            'changefrom'    => $input['previous_stores'],
            'changeto'      => $changeto,
            'effectiveon'   => $input['startdate'],
        );
        $changeOutlet = $this->db->insert('change_outlet_record', $data);

        $cond       = array('empId' => $empId, 'recordNo' => $recordNo);
        $statCut    = $this->promo_model->selectAll_tcR_tk('promo_sched_emp', $cond)['statCut'];

        foreach ($servers as $server) {

            $data   = array('recordNo' => $record_no, 'date_setup' => $this->today);
            $update = $this->promo_model->promoCutoff_update($server, $cond, $data);
        }
        // inserting and updating of promo_sched_emp table ******************************************************************

        $activity   = "Added Promo Store(s) to $oldEmp3[name] ($empId) with Record No: $record_no";
        $data       = array(
            'activity'  => $activity,
            'date'      => $this->today,
            'time'      => date("H:i:s"),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $logs = $this->promo_model->insert_tdA('logs', $data);

        if ($record_no && $promo_record && $changeOutlet) {

            $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
            $delete = $this->promo_model->delete_tcr_row('employee3', $cond);
            $delete = $this->promo_model->delete_tcr_row('promo_record', $cond);

            $cond = array('ratee_id' => $empId, 'tag_stat' => 'Pending');
            $delete = $this->promo_model->delete_tcr_row('tag_for_resignation', $cond);

            $data = array(
                'message'   => 'success',
                'emp_id'    => $empId,
                'record_no' => $record_no,
                'contract'  => 'current'
            );
            echo json_encode($data);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function removeOutlet()
    {
        $input      = $this->input->post(NULL, TRUE);
        $empId      = $input['emp_id'];
        $recordNo   = $input['record_no'];
        $clearance  = $this->uploadClearance($input);

        if ($clearance) {
            $i = 0;
            $removedStores = '';
            foreach ($clearance as $key => $store) {
                $i++;
                $removedStores = ($i == 1) ? $store : $removedStores . ', ' . $store;
            }
        } else {
            $i = 0;
            $removedStores = '';
            $clearance = [];
            foreach ($input['stores'] as $value) {
                $str    = explode('|', $value);
                $cond   = array('bunit_id' => $str[0]);
                $bu     = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);
                if ($bu) {
                    $i++;
                    $clearance[] = $bu['bunit_name'];
                    $store = $bu['bunit_name'];
                    $removedStores = ($i == 1) ? $store : $removedStores . ', ' . $store;
                }
            }
        }

        // inserting of employmentrecord_ table ******************************************************************
        $data       = [];
        $fields     = $this->promo_model->emp3Fields();
        $cond       = array('emp_id' => $empId, 'record_no' => $recordNo);
        $oldEmp3    = $this->promo_model->selectAll_tcR('employee3', $cond);
        foreach ($oldEmp3 as $field => $value) {
            if (!in_array($field, $fields)) {
                if ($field == 'name') {
                    $field = 'names';
                } else if ($field == 'position_desc') {
                    $field = 'pos_desc';
                } else if ($field == 'updated_by') {
                    $field = 'updatedby';
                } else if ($field == 'current_status') {
                    if ($value == 'Active') {
                        $value = 'End of Contract';
                    }
                }
                $data[$field] = $value;
            }
        }

        $recordNo_ = $this->promo_model->insert_tdA('employmentrecord_', $data);
        // inserting of employmentrecord_ table ******************************************************************

        // inserting of promo_history_record table ******************************************************************
        $promo  = $this->promo_model->selectAll_tcR('promo_record', $cond);
        $data   = [];
        foreach ($promo as $field => $value) {
            $fields = array('promo_id');
            if (!in_array($field, $fields)) {

                if ($field == 'record_no') {
                    $data[$field] = $recordNo_;
                } else if ($field == 'promo_company') {
                    // $data[$field] = $this->db->escape_str($value);
                    $data[$field] = $value;
                } else {
                    $data[$field] = $value;
                }
            }
        }

        $promo_hist = $this->promo_model->insert_tdA('promo_history_record', $data);
        // inserting of promo_history_record table ******************************************************************

        // updating of employment_witness table ******************************************************************
        $data   = array('rec_no' => $recordNo_);
        $cond   = array('emp_id' => $empId, 'rec_no' => $recordNo);
        $update = $this->promo_model->update_twdA('employment_witness', $cond, $data);
        // updating of employment_witness table ******************************************************************

        // updating of promo_products table ******************************************************************
        $data   = array('record_no' => $recordNo_);
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $update = $this->promo_model->update_twdA('promo_products', $cond, $data);
        // updating of promo_products table ******************************************************************

        // updating of appraisal details table ******************************************************************
        $data   = array('record_no' => $recordNo_);
        foreach ($clearance as $key => $store) {
            $cond   = array('emp_id' => $empId, 'record_no' => $recordNo, 'store' => $store);
            $update = $this->promo_model->update_twdA('appraisal_details', $cond, $data);

            // updating of promo_schedule table ******************************************************************
            $update = $this->promo_model->update_twdA('promo_schedule', $cond, $data);
            // updating of promo_schedule table ******************************************************************
        }
        // updating of appraisal details table ******************************************************************

        // updating the promo clearance tables ******************************************************************
        $cond       = array('emp_id' => $empId, 'status' => 'Pending');
        $scpr_id    = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond)['scpr_id'];
        if ($scpr_id) {

            $data = array(
                'record_no'         => $recordNo_,
                'clearance_status'  => 'Completed',
                'date_cleared'      => $this->today
            );
            foreach ($clearance as $key => $store) {
                $cond = array(
                    'emp_id'            => $empId,
                    'record_no'         => $recordNo,
                    'store'             => $store,
                    'scpr_id'           => $scpr_id,
                    'clearance_status'  => 'Pending'
                );
                $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);
            }

            $cond   = array('scpr_id' => $scpr_id, 'clearance_status' => 'Pending');
            $chk    = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
            if (!$chk) {
                $data   = array('status' => 'Completed');
                $cond   = array('scpr_id' => $scpr_id);
                $update = $this->promo_model->update_twdA('secure_clearance_promo', $cond, $data);

                $data   = array('current_status' => 'Active', 'sub_status' => '');
                $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
                $update = $this->promo_model->update_twdA('employee3', $cond, $data);
            }
        }
        // updating the promo clearance tables ******************************************************************	

        // inserting of employee3 table ******************************************************************
        $data   = [];
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $query  = $this->promo_model->selectAll_tcR('employee3', $cond);
        foreach ($query as $field => $value) {
            $fields = array('record_no');
            if (!in_array($field, $fields)) {
                if ($field == 'startdate') {
                    $value = $input['startdate'];
                } else if ($field == 'duration') {
                    $value = $input['duration'];
                } else if ($field == 'tag_as') {
                    $value = '';
                } else if ($field == 'remarks') {
                    $value = 'Removed Store(s): ' . $removedStores;
                } else if ($field == 'date_added') {
                    $value = $this->today;
                } else if ($field == 'added_by') {
                    $value = $this->userId;
                } else if ($field == 'date_by') {
                    $value = $this->userId;
                } else if ($field == 'date_updated') {
                    $value = $this->today;
                }

                $data[$field] = $value;
            }
        }

        $record_no = $this->promo_model->insert_tdA('employee3', $data);
        // inserting of employee3 table ******************************************************************

        // inserting of promo_record table ******************************************************************
        $fields = array('promo_id');
        foreach ($input['stores'] as $value) {
            $str    = explode('|', $value);
            $cond   = array('bunit_id' => $str[0]);
            $bu     = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);
            if ($bu) {
                $fields[] = $bu['bunit_field'];
                $fields[] = $bu['bunit_epascode'];
                $fields[] = $bu['bunit_contract'];
                $fields[] = $bu['bunit_permit'];
                $fields[] = $bu['bunit_clearance'];
                $fields[] = $bu['bunit_intro'];
                $fields[] = $bu['bunit_dutySched'];
                $fields[] = $bu['bunit_dutyDays'];
                $fields[] = $bu['bunit_specialSched'];
                $fields[] = $bu['bunit_specialDays'];
            }
        }

        $data   = [];
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $promo  = $this->promo_model->selectAll_tcR('promo_record', $cond);
        foreach ($promo as $field => $value) {
            if (!in_array($field, $fields)) {

                if ($field == 'record_no') {
                    $value = $record_no;
                } else if ($field == 'promo_company') {
                    // $value = $this->db->escape_str($value);
                } else if ($field == 'promo_type') {
                    $value = (count($input['currentStores']) == 1) ? 'STATION' : 'ROVING';
                } else if ($field == 'transferOn') {
                    $value = $input['startdate'];
                }

                $data[$field] = $value;
            }
        }

        $promo_record = $this->promo_model->insert_tdA('promo_record', $data);
        // inserting of promo_record table ******************************************************************

        // updating of appraisal table ******************************************************************
        foreach ($input['currentStores'] as $value) {
            $str    = explode('|', $value);
            $cond   = array('bunit_id' => $str[0]);
            $bu     = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);
            if ($bu) {
                $store  = $bu['bunit_name'];
                $data   = array('record_no' => $record_no);
                $cond   = array('emp_id' => $empId, 'record_no' => $recordNo, 'store' => $store);
                $update = $this->promo_model->update_twdA('appraisal_details', $cond, $data);

                // updating of promo_schedule table ******************************************************************
                $update = $this->promo_model->update_twdA('promo_schedule', $cond, $data);
                // updating of promo_schedule table ******************************************************************
            }
        }
        // updating of appraisal table ******************************************************************

        // updating of clearance table ******************************************************************
        $cond   = array('scpr_id' => $scpr_id, 'emp_id' => $empId, 'clearance_status' => 'Pending');
        $query  = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
        if ($query) {
            foreach ($query as $key => $row) {
                $store  = $row['store'];
                $data   = array('record_no' => $record_no);
                $cond   = array('scdetails_id' => $row['scdetails_id']);
                $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);
            }
        }
        // updating of clearance table ******************************************************************

        // inserting and updating of promo_sched_emp table ******************************************************************
        $i          = 0;
        $changeto   = '';
        $servers    = ['corporate'];
        $bus        = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $field = $bu['bunit_field'];
            $hasBu = $this->promo_model->empStores('promo_record', $empId, $record_no, $field);
            if ($hasBu) {
                $i++;
                $changeto = ($i == 1) ? $bu['bunit_name'] : $changeto . ', ' . $bu['bunit_name'];
                if ($field == 'al_tal') {
                    $servers[]    = 'talibon';
                } else if ($field == 'al_tub' || $field == 'fr_tubigon') {
                    $servers[]    = 'tubigon';
                }
            }
        }

        $data = array(
            'emp_id'        => $empId,
            'changefrom'    => $input['previous_stores'],
            'changeto'      => $changeto,
            'effectiveon'   => $input['startdate'],
        );
        $changeOutlet = $this->db->insert('change_outlet_record', $data);

        $cond       = array('empId' => $empId, 'recordNo' => $recordNo);
        $statCut    = $this->promo_model->selectAll_tcR_tk('promo_sched_emp', $cond)['statCut'];

        foreach ($servers as $server) {

            $data   = array('recordNo' => $record_no, 'date_setup' => $this->today);
            $update = $this->promo_model->promoCutoff_update($server, $cond, $data);
        }
        // inserting and updating of promo_sched_emp table ******************************************************************

        $activity   = "Removed Promo Store(s) of $oldEmp3[name] with Record No: $record_no";
        $data       = array(
            'activity'  => $activity,
            'date'      => $this->today,
            'time'      => date("H:i:s"),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $logs = $this->promo_model->insert_tdA('logs', $data);

        if ($record_no && $promo_record && $changeOutlet) {

            $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
            $delete = $this->promo_model->delete_tcr_row('employee3', $cond);
            $delete = $this->promo_model->delete_tcr_row('promo_record', $cond);

            $cond = array('ratee_id' => $empId, 'tag_stat' => 'Pending');
            $delete = $this->promo_model->delete_tcr_row('tag_for_resignation', $cond);

            $data = array(
                'message'   => 'success',
                'emp_id'    => $empId,
                'record_no' => $record_no,
                'contract'  => 'current'
            );
            echo json_encode($data);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function transferOutlet()
    {
        $input      = $this->input->post(NULL, TRUE);
        $empId      = $input['emp_id'];
        $recordNo   = $input['record_no'];
        $clearance  = $this->uploadClearance($input);

        $i = 0;
        $from = '';
        if ($clearance) {
            foreach ($clearance as $store) {
                $i++;
                $from = ($i == 1) ? $store : $from . ', ' . $store;
            }
        } else {
            $clearance = [];
            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $bu) {
                $chk = $this->promo_model->empStores('promo_record', $empId, $recordNo, $bu['bunit_field']);
                if ($chk) {
                    $i++;
                    $store = $bu['bunit_name'];
                    $clearance[] = $bu['bunit_name'];
                    $from = ($i == 1) ? $store : $from . ', ' . $store;
                }
            }
        }

        $i = 0;
        $to = '';
        foreach ($input['transfer'] as $value) {
            $str    = explode('|', $value);
            $cond   = array('bunit_id' => $str[0]);
            $bu     = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);
            if ($bu) {
                $i++;
                $to = ($i == 1) ? $bu['bunit_name'] : $to . ', ' . $bu['bunit_name'];
            }
        }

        $remarks = 'Transfer Store(s) from [' . $from . '] to [' . $to . ']';

        // inserting of employmentrecord_ table ******************************************************************
        $data       = [];
        $fields     = $this->promo_model->emp3Fields();
        $cond       = array('emp_id' => $empId, 'record_no' => $recordNo);
        $oldEmp3    = $this->promo_model->selectAll_tcR('employee3', $cond);
        foreach ($oldEmp3 as $field => $value) {
            if (!in_array($field, $fields)) {
                if ($field == 'name') {
                    $field = 'names';
                } else if ($field == 'position_desc') {
                    $field = 'pos_desc';
                } else if ($field == 'updated_by') {
                    $field = 'updatedby';
                } else if ($field == 'current_status') {
                    $value = 'End of Contract';
                }
                $data[$field] = $value;
            }
        }

        $recordNo_ = $this->promo_model->insert_tdA('employmentrecord_', $data);
        // inserting of employmentrecord_ table ******************************************************************

        // inserting of promo_history_record table ******************************************************************
        $promo  = $this->promo_model->selectAll_tcR('promo_record', $cond);
        $data   = [];
        foreach ($promo as $field => $value) {
            $fields = array('promo_id');
            if (!in_array($field, $fields)) {

                if ($field == 'record_no') {
                    $data[$field] = $recordNo_;
                } else if ($field == 'promo_company') {
                    // $data[$field] = $this->db->escape_str($value);
                    $data[$field] = $value;
                } else {
                    $data[$field] = $value;
                }
            }
        }

        $promo_hist = $this->promo_model->insert_tdA('promo_history_record', $data);
        // inserting of promo_history_record table ******************************************************************

        // updating of employment_witness table ******************************************************************
        $data   = array('rec_no' => $recordNo_);
        $cond   = array('emp_id' => $empId, 'rec_no' => $recordNo);
        $update = $this->promo_model->update_twdA('employment_witness', $cond, $data);
        // updating of employment_witness table ******************************************************************

        // updating of promo_products table ******************************************************************
        $data   = array('record_no' => $recordNo_);
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $update = $this->promo_model->update_twdA('promo_products', $cond, $data);
        // updating of promo_products table ******************************************************************

        // updating of appraisal details table ******************************************************************
        $data   = array('record_no' => $recordNo_);
        foreach ($clearance as $key => $store) {
            $cond   = array('emp_id' => $empId, 'record_no' => $recordNo, 'store' => $store);
            $update = $this->promo_model->update_twdA('appraisal_details', $cond, $data);
        }
        // updating of appraisal details table ******************************************************************

        // updating the promo clearance tables ******************************************************************
        $cond       = array('emp_id' => $empId, 'status' => 'Pending');
        $scpr_id    = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond)['scpr_id'];
        if ($scpr_id) {

            $data = array(
                'record_no'         => $recordNo_,
                'clearance_status'  => 'Completed',
                'date_cleared'      => $this->today
            );
            foreach ($clearance as $key => $store) {
                $cond = array(
                    'emp_id'            => $empId,
                    'record_no'         => $recordNo,
                    'store'             => $store,
                    'scpr_id'           => $scpr_id,
                    'clearance_status'  => 'Pending'
                );
                $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);
            }

            $cond   = array('scpr_id' => $scpr_id, 'clearance_status' => 'Pending');
            $chk    = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
            if (!$chk) {
                $data   = array('status' => 'Completed');
                $cond   = array('scpr_id' => $scpr_id);
                $update = $this->promo_model->update_twdA('secure_clearance_promo', $cond, $data);

                $data   = array('current_status' => 'Active', 'sub_status' => '');
                $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
                $update = $this->promo_model->update_twdA('employee3', $cond, $data);
            }
        }
        // updating the promo clearance tables ******************************************************************	

        // inserting of employee3 table ******************************************************************
        $data   = [];
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $query  = $this->promo_model->selectAll_tcR('employee3', $cond);
        foreach ($query as $field => $value) {
            $fields = array('record_no');
            if (!in_array($field, $fields)) {
                if ($field == 'startdate') {
                    $value = $input['startdate'];
                } else if ($field == 'duration') {
                    $value = $input['duration'];
                } else if ($field == 'tag_as') {
                    $value = '';
                } else if ($field == 'remarks') {
                    $value = $remarks;
                } else if ($field == 'date_added') {
                    $value = $this->today;
                } else if ($field == 'added_by') {
                    $value = $this->userId;
                }

                $data[$field] = $value;
            }
        }

        $record_no = $this->promo_model->insert_tdA('employee3', $data);
        // inserting of employee3 table ******************************************************************

        // inserting of promo_record table ******************************************************************
        $fields = array('promo_id');
        foreach ($input['stores'] as $value) {
            $str    = explode('|', $value);
            $cond   = array('bunit_id' => $str[0]);
            $bu     = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);
            if ($bu) {
                $fields[] = $bu['bunit_field'];
                $fields[] = $bu['bunit_epascode'];
                $fields[] = $bu['bunit_contract'];
                $fields[] = $bu['bunit_permit'];
                $fields[] = $bu['bunit_clearance'];
                $fields[] = $bu['bunit_intro'];
                $fields[] = $bu['bunit_dutySched'];
                $fields[] = $bu['bunit_dutyDays'];
                $fields[] = $bu['bunit_specialSched'];
                $fields[] = $bu['bunit_specialDays'];
            }
        }

        $cStores    = [];
        $chkStores  = isset($input['currentStores']) ? $input['currentStores'] : [];
        $cStores    = array_merge($chkStores, $input['transfer']);

        $data   = [];
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $promo  = $this->promo_model->selectAll_tcR('promo_record', $cond);
        foreach ($promo as $field => $value) {
            if (!in_array($field, $fields)) {

                if ($field == 'record_no') {
                    $value = $record_no;
                } else if ($field == 'promo_company') {
                    // $value = $this->db->escape_str($value);
                } else if ($field == 'promo_type') {
                    $value = (count($cStores) == 1) ? 'STATION' : 'ROVING';
                } else if ($field == 'transferOn') {
                    $value = $input['startdate'];
                }

                $data[$field] = $value;
            }
        }

        foreach ($input['transfer'] as $value) {
            $str = explode('|', $value);
            $data[$str[1]] = 'T';
        }

        $promo_record = $this->promo_model->insert_tdA('promo_record', $data);
        // inserting of promo_record table ******************************************************************

        // uploading of intro ******************************************************************
        $intro  = $this->uploadIntro($input, $record_no);
        // uploading of intro ******************************************************************

        // updating of appraisal table ******************************************************************
        if (isset($input['currentStores'])) {
            foreach ($input['currentStores'] as $value) {
                $str    = explode('|', $value);
                $cond   = array('bunit_id' => $str[0]);
                $bu     = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);
                if ($bu) {
                    $store  = $bu['bunit_name'];
                    $data   = array('record_no' => $record_no);
                    $cond   = array('emp_id' => $empId, 'record_no' => $recordNo, 'store' => $store);
                    $update = $this->promo_model->update_twdA('appraisal_details', $cond, $data);
                }
            }
        }
        // updating of appraisal table ******************************************************************

        // updating of clearance table ******************************************************************
        $cond   = array('scpr_id' => $scpr_id, 'emp_id' => $empId, 'clearance_status' => 'Pending');
        $query  = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
        if ($query) {
            foreach ($query as $key => $row) {
                $store  = $row['store'];
                $data   = array('record_no' => $record_no);
                $cond   = array('scdetails_id' => $row['scdetails_id']);
                $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);
            }
        }
        // updating of clearance table ******************************************************************

        // inserting and updating of promo_sched_emp table ******************************************************************
        $i          = 0;
        $changeto   = '';
        $servers    = ['corporate'];
        $bus        = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $field = $bu['bunit_field'];
            $hasBu = $this->promo_model->empStores('promo_record', $empId, $record_no, $field);
            if ($hasBu) {
                $i++;
                $changeto = ($i == 1) ? $bu['bunit_name'] : $changeto . ', ' . $bu['bunit_name'];
                if ($field == 'al_tal') {
                    $servers[]    = 'talibon';
                } else if ($field == 'al_tub' || $field == 'fr_tubigon') {
                    $servers[]    = 'tubigon';
                }
            }
        }

        $data = array(
            'emp_id'        => $empId,
            'changefrom'    => $input['previous_stores'],
            'changeto'      => $changeto,
            'effectiveon'   => $input['startdate'],
        );
        $changeOutlet = $this->db->insert('change_outlet_record', $data);

        $cond       = array('empId' => $empId, 'recordNo' => $recordNo);
        $statCut    = $this->promo_model->selectAll_tcR_tk('promo_sched_emp', $cond)['statCut'];

        foreach ($servers as $server) {

            $data   = array('recordNo' => $record_no, 'date_setup' => $this->today);
            $update = $this->promo_model->promoCutoff_update($server, $cond, $data);
        }
        // inserting and updating of promo_sched_emp table ******************************************************************

        $activity   = "Transfered Promo Store(s) of $oldEmp3[name] with Record No: $record_no";
        $data       = array(
            'activity'  => $activity,
            'date'      => $this->today,
            'time'      => date("H:i:s"),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $logs = $this->promo_model->insert_tdA('logs', $data);

        if ($record_no && $promo_record && $changeOutlet) {

            $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
            $delete = $this->promo_model->delete_tcr_row('employee3', $cond);
            $delete = $this->promo_model->delete_tcr_row('promo_record', $cond);

            $cond = array('ratee_id' => $empId, 'tag_stat' => 'Pending');
            $delete = $this->promo_model->delete_tcr_row('tag_for_resignation', $cond);

            $data = array(
                'message'   => 'success',
                'emp_id'    => $empId,
                'record_no' => $record_no,
                'contract'  => 'current'
            );
            echo json_encode($data);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function uploadClearance($input)
    {
        $empId      = $input['emp_id'];
        $recordNo   = $input['record_no'];
        $clearance  = [];
        $bus        = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $clearanceName  = $bu['bunit_clearance'];
            $path = '';
            if (isset($_FILES[$clearanceName]['name'])) {
                $array          = explode(".", $_FILES[$clearanceName]['name']);
                $filename       = $empId . '=' . date('Y-m-d') . '=' . $clearanceName . '=' . date('H-i-s-A') . '.' . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/clearance/';
                $path           = $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES[$clearanceName]['tmp_name'], $path)) {

                    $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
                    $data   = array($clearanceName => $relative_path . $filename);
                    $update = $this->promo_model->update_twdA('promo_record', $cond, $data);
                    if ($update) {
                        $clearance[] = $bu['bunit_name'];
                    }
                }
            }
        }

        return $clearance;
    }

    public function uploadIntro($input, $record_no)
    {
        $empId = $input['emp_id'];
        $intro = [];
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $introName  = $bu['bunit_intro'];
            $path = '';
            if (isset($_FILES[$introName]['name'])) {
                $array          = explode(".", $_FILES[$introName]['name']);
                $filename       = $empId . '=' . date('Y-m-d') . '=' . $introName . '=' . date('H-i-s-A') . '.' . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/final_requirements/others/';
                $path           = $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES[$introName]['tmp_name'], $path)) {

                    $data = array(
                        'app_id'                => $empId,
                        'requirement_name'      => 'intro',
                        'filename'              => $relative_path . $filename,
                        'date_time'             => $this->today,
                        'requirement_status'    => 'passed',
                        'receiving_staff'       => $this->userId,
                    );

                    $insert = $this->promo_model->insert_tdA('application_otherreq', $data);

                    $cond   = array('emp_id' => $empId, 'record_no' => $record_no);
                    $data   = array($introName => $filename);
                    $update = $this->promo_model->update_twdA('promo_record', $cond, $data);
                    if ($update) {
                        $intro[] = $bu['bunit_name'];
                    }
                }
            }
        }

        return $intro;
    }
}
