<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Contract extends CI_Controller
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

    public function searchPromoContract()
    {
        echo '<ul class="list-group">';
        $input = $this->input->post(NULL, TRUE);
        $query = $this->promo_model->searchPromoContract($input);
        if ($query) {
            foreach ($query as $row) {
                $name       = ucwords(strtolower($row['name']));
                $emp_id     = $row['emp_id'];
                $record_no  = $row['record_no'];
                $onclick    = 'onclick="selectPromoContract(\'' . $name . '\',\'' . $emp_id . '\',\'' . $record_no . '\')"';
                echo '<a href="javascript:;" class="list-group-item" ' . $onclick . '>' . $name . '</a>';
            }
        } else {
            echo '<a href="javascript:;" class="list-group-item">No Results Found!</a>';
        }
        echo '</ul>';
    }

    public function selectPromoContract()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $process    = $input['process'];
        $cond       = array('e.emp_id' => $emp_id);
        $current    = $this->promo_model->promoDetails_wttA($cond, 'employee3', 'promo_record');
        $previous   = $this->promo_model->promoDetails_wttA($cond, 'employmentrecord_', 'promo_history_record');
        echo '<div class="table-container">';
        echo '<table class="table table-sm table-hover " style="width: 100%;">
                <tr>
                    <th>No.</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Startdate</th>
                    <th>EOCdate</th>
                    <th>Store(s)</th>
                    <th class="text-center">Current Status</th>
                    <th class="text-center">Action</th>
                </tr>';

        if ($current || $previous) {
            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            $contract = ['current' => $current, 'previous' => $previous];
            foreach ($contract as $type => $data) {

                $i      = 0;
                $table  = ($type == 'current') ? 'promo_record' : 'promo_history_record';
                foreach ($data as $value) {

                    $i++;
                    $c = 0;
                    $stores     = '';
                    $record_no  = $value['record_no'];
                    foreach ($bus as $bu) {
                        $hasBu = $this->promo_model->empStores($table, $emp_id, $record_no, $bu['bunit_field']);
                        if ($hasBu) {
                            $c++;
                            $store = '<span class="badge bg-secondary spanTooltip" title="' . $bu['bunit_name'] . '">' . $bu['bunit_acronym'] . ' </span>';
                            $stores = ($c == 1) ? $store : $stores . ' ' . $store;
                        }
                    }

                    if ($process == 'printPermit') {
                        $action =   '<a href="javascript:;" onclick="generatePermitForm(\'' . $emp_id . '\',\'' . $record_no . '\', \'' . $type . '\')">
                                        <span class="badge bg-primary">Select</span>
                                    </a>';
                    } else if ($process == 'printContract') {
                        $action =   '<a href="javascript:;" onclick="generateContractForm(\'' . $emp_id . '\',\'' . $record_no . '\', \'' . $type . '\')">
                                        <span class="badge bg-primary">Select</span>
                                    </a>';
                    } else {
                        $action = '';
                    }

                    if ($type == 'current') {
                        $no = '<span class="badge bg-success">C</span>';
                    } else {
                        $no = '<span class="badge bg-danger">' . $i . '</span>';
                    }

                    if ($value['current_status'] == 'Active') {
                        $stat = '<font class="text-success">' . $value['current_status'] . '</font>';
                    } else if ($value['current_status'] == 'Blacklisted') {
                        $stat = '<font class="text-danger">' . $value['current_status'] . '</font>';
                    } else if ($value['current_status'] == 'Deceased') {
                        $stat = '<font class="text-secondary">' . $value['current_status'] . '</font>';
                    } else {
                        $stat = '<font class="text-warning">' . $value['current_status'] . '</font>';
                    }

                    echo '<tr>';
                    echo    '<td>' . $no . '</td>';
                    echo    '<td>' . $value['promo_department'] . '</td>';
                    echo    '<td>' . $value['position'] . '</td>';
                    echo    '<td>' . date('M. d, Y', strtotime($value['startdate'])) . '</td>';
                    echo    '<td>' . date('M. d, Y', strtotime($value['eocdate'])) . '</td>';
                    echo    '<td>' . $stores . '</td>';
                    echo    '<td class="text-center">' . $stat . '</td>';
                    echo    '<td class="text-center">' . $action . '</td>';
                    echo '</tr>';
                }
            }
        }
        echo '</table>';
        echo '</div>';
    }

    public function generatePermitForm()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        $contract   = $input['contract'];

        $table1 = ($contract == 'current') ? 'employee3' : 'employmentrecord_';
        $table2 = ($contract == 'current') ? 'promo_record' : 'promo_history_record';
        $cond   = array('e.emp_id' => $emp_id, 'e.record_no' => $record_no);
        $name   = $this->promo_model->getName_employee3($emp_id);
        $row    = $this->promo_model->promoDetails_wttR($cond, $table1, $table2);

        echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
        echo '<input type="hidden" name="record_no" value="' . $record_no . '">';
        echo '<input type="hidden" name="contract" value="' . $contract . '">';
        echo '<div class="row">';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Search Promo to Generate Permit</label>
                <div class="input-group font-22">
                    <i class="input-group-text fadeIn animated lni lni-users"></i>
                    <input type="text" class="form-control" name="printPermit" value="' . $name . '" placeholder=" Search here..." autocomplete="off" onkeyup="searhPrint(this.value, this.name)" disabled>';
        echo    '</div>';
        echo    '<div class="dropdown-list printPermit"></div>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Cut Off</label>
                <select class="form-select select2" name="cutOff" data-placeholder="Select Cut Off" disabled>
                <option value="">Select Cut Off</option>';

        $cond   = array('emp_id' => $emp_id);
        $recNo  = $this->promo_model->selectAll_tcR('employee3', $cond)['record_no'];
        $cond   = array('recordNo' => $recNo, 'empId' => $emp_id);
        $statCut = $this->promo_model->selectAll_tcR_tk('promo_sched_emp', $cond)['statCut'];
        $cutoff = $this->promo_model->selectAll_tk('promo_schedule');
        foreach ($cutoff as $value) {
            $endFC = ($value['endFC'] != '') ? $value['endFC'] : 'last';
            echo '<option value="' . $value['statCut'] . '"';
            echo ($value['statCut'] == $statCut) ? 'selected' : '';
            echo '>' . $value['startFC'] . '-' . $endFC . ' / ' . $value['startSC'] . '-' . $value['endSC'] . '</option>';
        }

        echo    '</select>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Store(s)</label>
                <select class="form-select select2" name="store" data-placeholder="Select Store">
                <option value="">Select Store</option>';

        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $hasBu = $this->promo_model->empStores($table2, $row['emp_id'], $row['record_no'], $bu['bunit_field']);
            if ($hasBu > 0) {
                $value = $bu['bunit_id'] . '|' . $bu['bunit_dutySched'] . '|' . $bu['bunit_dutyDays'] . '|' . $bu['bunit_specialSched'] . '|' . $bu['bunit_specialDays'];
                echo '<option value="' . $value . '">' . $bu['bunit_name'] . '</option>';
            }
        }

        echo    '</select>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Day Off</label>
                <select class="form-select select2" name="dayOff[]" data-placeholder="Select Day Off" onchange="getDutydays($(this).val())" multiple>
                <option value="">Select Day Off</option>';

        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'No Day Off');
        foreach ($days as $value) {
            echo '<option value="' . $value . '">' . $value . '</option>';
        }

        echo    '</select>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Duty Schedule</label>
                <select class="form-select select2" name="dutySched" data-placeholder="Select Duty Schedule">
                <option value="">Select Duty Schedule</option>';

        $dutySched = $this->promo_model->selectAll_tk('shiftcodes');
        foreach ($dutySched as $value) {
            $shiftCode  = $value['shiftCode'];
            $In1        = $value['1stIn'];
            $Out1       = $value['1stOut'];
            $In2        = $value['2ndIn'];
            $Out2       = $value['2ndOut'];
            if ($In2 == '') {
                echo "<option value = '$shiftCode'>$shiftCode = $In1-$Out1 </option>";
            } else {
                echo "<option value = '$shiftCode'>$shiftCode = $In1-$Out1, $In2-$Out2</option>";
            }
        }

        echo    '</select>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Duty Days</label>
                <input type="text" class="form-control" name="dutyDays">';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Special Schedule</label>
                <select class="form-select select2" name="specialSched" data-placeholder="Select Special Schedule">
                <option value="">Select Special Schedule</option>';

        $dutySched = $this->promo_model->selectAll_tk('shiftcodes');
        foreach ($dutySched as $value) {
            $shiftCode  = $value['shiftCode'];
            $In1        = $value['1stIn'];
            $Out1       = $value['1stOut'];
            $In2        = $value['2ndIn'];
            $Out2       = $value['2ndOut'];
            if ($In2 == '') {

                echo "<option value = '$shiftCode'>$shiftCode = $In1-$Out1 </option>";
            } else {

                echo "<option value = '$shiftCode'>$shiftCode = $In1-$Out1, $In2-$Out2</option>";
            }
        }

        echo    '</select>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Special Days</label>
                <input type="text" class="form-control" name="specialDays">';
        echo '</div>';
        echo '</div>';
    }

    public function generateContractForm()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        $contract   = $input['contract'];

        $cond       = array('app_id' => $emp_id);
        $sss_ctc    = $this->promo_model->selectAll_tcR('applicant_otherdetails', $cond);
        $cond       = array('emp_id' => $emp_id, 'rec_no' => $record_no);
        $witness    = $this->promo_model->selectAll_tcR('employment_witness', $cond);

        $date_generated = $this->today;
        if ($witness['date_generated'] != '' && $witness['date_generated'] != '0000-00-00') {
            $date_generated = $witness['date_generated'];
        }

        $issuedAt   = !empty($witness['issuedat']) ? $witness['issuedat'] : (!empty($sss_ctc['cedula_place']) ? $sss_ctc['cedula_place'] : '');
        $issuedOn   = !empty($witness['issuedon']) ? $witness['issuedon'] : (!empty($sss_ctc['cedula_date']) ? $sss_ctc['cedula_date'] : '');

        if ($witness['sss_ctc'] == 'Cedula') {
            $cedulaNo   = !empty($witness['sssno_ctcno']) ? $witness['sssno_ctcno'] : (!empty($sss_ctc['cedula_no']) ? $sss_ctc['cedula_no'] : '');
            $sssNo      = !empty($sss_ctc['sss_no']) ? $sss_ctc['sss_no'] : '';
        } else {
            $sssNo      = !empty($witness['sssno_ctcno']) ? $witness['sssno_ctcno'] : (!empty($sss_ctc['sss_no']) ? $sss_ctc['sss_no'] : '');
            $cedulaNo   = !empty($sss_ctc['cedula_no']) ? $sss_ctc['cedula_no'] : '';
        }
        echo '<div class="row">';
        echo '<input type="hidden" class="form-control" name="emp_id" value="' . $emp_id . '">';
        echo '<input type="hidden" class="form-control" name="record_no" value="' . $record_no . '">';
        echo '<input type="hidden" class="form-control" name="contract" value="' . $contract . '">';
        echo '<div class="col-md-12 mb-3">
                <label class="form-label">Contract Header</label>
                <select class="form-select form-select select2" name="contract_header_no" data-placeholder="Select Contract Header">
                    <option value="">Select Contract Header</option>';

        $hcode  = $this->promo_model->hcode();
        $table2 = ($contract == 'current') ? 'promo_record' : 'promo_history_record';
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $key => $bu) {
            $buCode = $bu['bunit_contract'];
            $hasBu = $this->promo_model->empStores($table2, $emp_id, $record_no, $bu['bunit_field']);
            if ($hasBu) {

                $cond = array('ccode_no' => $hcode[$buCode]);
                $row  = $this->promo_model->selectAll_tcR('contract_header', $cond);
                $hearder = $row['company'] . ' ------------- ' . $row['address'];
                echo '<option value="' . $row['ccode_no'] . '">' . $hearder . '</option>';
            }
        }

        echo    '</select>';
        echo '</div>';
        echo '<div class="col-md-12 mb-3">
                <label class="form-label">Please choose either to use SSS No. or Cedula (CTC No.)</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check form-check-success">
                            <input class="form-check-input" type="radio" name="sss_ctc[]" id="flexRadioSuccess2" value="sss" onclick="showContractElements(this.value)">
                            <label class="form-check-label" for="flexRadioSuccess2">SSS No.</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control form-control" name="sss" value="' . $sssNo . '" style="display: none;" disabled placeholder="SSS No.">
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-check-success">
                            <input class="form-check-input" type="radio" name="sss_ctc[]" id="flexRadioSuccess1" value="cedula" onclick="showContractElements(this.value)">
                            <label class="form-check-label" for="flexRadioSuccess1">Cedula (CTC No.)</label>
                        </div>
                    </div>
                    <div class="col-md-6 issuedOn">
                        <input type="text" class="form-control form-control" name="ctc" value="' . $cedulaNo . '" style="display: none;" disabled placeholder="Cedula (CTC No.)">
                    </div>
                    <div class="col-md-6 mt-2 issuedat" style="display: none;" style="display: none;">
                        <label class="form-label">Issued At</label>
                        <input type="text" class="form-control form-control" name="issuedat" value="' . $issuedAt . '" disabled>
                    </div>
                    <div class="col-md-6 mt-2 issuedon" style="display: none;" style="display: none;">
                        <label class="form-label">Issued On</label>
                        <input type="text" class="form-control form-control datepicker" name="issuedon" value="' . $issuedOn . '" placeholder="yyyy/mm/dd" disabled>
                    </div>
                </div>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Witness 1</label>
                <input type="text" class="form-control form-control" name="witness1" value="' . $witness['witness1'] . '" onkeyup="searhContractWitness(this.value, this.name)">
                <div class="dropdown-list witness1"></div>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Witness 2</label>
                <input type="text" class="form-control form-control" name="witness2" value="' . $witness['witness2'] . '" onkeyup="searhContractWitness(this.value, this.name)">
                <div class="dropdown-list witness2"></div>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Date of Signing the Contract</label>
                <input type="text" class="form-control form-control datepicker" name="date" value="' . $date_generated . '" placeholder="yyyy/mm/dd">';
        echo '</div>';
        echo '</div>';
    }

    public function searhContractWitness()
    {
        $input  = $this->input->post(NULL, TRUE);
        $str    = trim($input['str']);

        if ($input['witness'] == 'w1') {
            $witness = 'witness1';
        } else if ($input['witness'] == 'w2') {
            $witness = 'witness2';
        } else {
            $witness = $input['witness'];
        }
        echo '<ul class="list-group witness">';
        $query  = $this->promo_model->searchWitness($str);
        if ($query) {
            foreach ($query as  $row) {

                $name = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
                if ($row['middlename']) {
                    $mi = substr($row['middlename'], 0, 1) . '.';
                    $name = htmlspecialchars($row['firstname'] . ' ' . $mi . ' ' . $row['lastname']);
                }

                echo '<a href="javascript:;" class="list-group-item" onclick="selectContractWitness(\'' . $name . '\', \'' . $input['witness'] . '\')">' . $name . '</a>';
            }
        } else {
            echo '<a href="javascript:;" class="list-group-item">No Results Found!</a>';
        }
        echo '</ul>';
    }

    public function savePermit()
    {
        $input  = $this->input->post(NULL, TRUE);

        $i = 0;
        $dayoff = '';
        foreach ($input['dayOff'] as $key => $do) {
            $i++;
            $dayoff = ($i == 1) ? $do : $dayoff . ', ' . $do;
        }

        $cond   = array('statCut' => $input['cutOff']);
        $getCO  = $this->promo_model->selectAll_tcR_tk('promo_schedule', $cond);
        $endFC  = ($getCO['endFC'] != '') ? $getCO['endFC'] : 'last';
        $cutOff = $getCO['startFC'] . '-' . $endFC . ' / ' . $getCO['startSC'] . '-' . $getCO['endSC'];

        $cond   = array('shiftcode' => $input['dutySched']);
        $dSched = $this->promo_model->selectAll_tcR_tk('shiftcodes', $cond);
        $In1    = $dSched['1stIn'];
        $Out1   = $dSched['1stOut'];
        $In2    = $dSched['2ndIn'];
        $Out2   = $dSched['2ndOut'];

        $promoSched = '';
        if (count($dSched) > 0) {
            if ($In2 == '') {
                $promoSched = "$In1-$Out1";
            } else {
                $promoSched = "$In1-$Out1, $In2-$Out2";
            }
        }

        $cond   = array('shiftcode' => $input['specialSched']);
        $sSched = $this->promo_model->selectAll_tcR_tk('shiftcodes', $cond);
        $In1    = $sSched['1stIn'];
        $Out1   = $sSched['1stOut'];
        $In2    = $sSched['2ndIn'];
        $Out2   = $sSched['2ndOut'];

        $specialSched = '';
        if (count($sSched) > 0) {
            if ($In2 == '') {
                $specialSched = "$In1-$Out1";
            } else {
                $specialSched = "$In1-$Out1, $In2-$Out2";
            }
        }

        $table  = ($input['contract'] === 'current') ? 'promo_record' : 'promo_history_record';
        $store  = explode('|', $input['store']);
        $cond   = array('emp_id' => $input['emp_id'], 'record_no' => $input['record_no'],);
        $data   = array(
            $store[1] => $promoSched,
            $store[2] => $input['dutyDays'],
            $store[3] => $specialSched,
            $store[4] => $input['specialDays'],
            'dayoff'  => $dayoff,
            'cutoff'  => $cutOff,
        );
        $update = $this->promo_model->update_twdA($table, $cond, $data);

        $cond   = array('bunit_id' => $store[0]);
        $buName = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond)['bunit_name'];
        $data  = array(
            'emp_id'        => $input['emp_id'],
            'record_no'     => $input['record_no'],
            'dutySched'     => $promoSched,
            'dutyDays'      => $input['dutyDays'],
            'specialSched'  => $specialSched,
            'specialDays'   => $input['specialDays'],
            'dayoff'        => $dayoff,
            'cutoff'        => $cutOff,
            'store'         => $buName,
        );

        $cond   = array('emp_id' => $input['emp_id'], 'record_no' => $input['record_no'], 'store' => $buName);
        $check  = $this->promo_model->selectAll_tcR('promo_schedule', $cond);
        if ($check) {
            $this->promo_model->update_twdA('promo_schedule', $cond, $data);
        } else {
            $this->promo_model->insert_tdA('promo_schedule', $data);
        }


        $c      = ($input['contract'] === 'current') ? '1' : '0';
        $link   = $input['emp_id'] . '_' . $input['record_no'] . '_' . $store[0] . '_' . $c;

        if ($update) {
            echo json_encode(['message' => 'success', 'data' => $link]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function saveContract()
    {
        $input = $this->input->post(NULL, TRUE);

        foreach ($input['sss_ctc'] as $value) {
            if ($value == 'sss') {
                $data1 = array(
                    'witness1'              => $input['witness1'],
                    'witness2'              => $input['witness2'],
                    'contract_header_no'    => $input['contract_header_no'],
                    'sss_ctc'               => 'SSS',
                    'sssno_ctcno'           => $input['sss'],
                    'issuedat'              => $input['issuedat'],
                    'date_generated'        => $input['date'],
                );
                $data2 = array('sss_no' => $input['sss']);
            } else {
                $data1 = array(
                    'witness1'              => $input['witness1'],
                    'witness2'              => $input['witness2'],
                    'contract_header_no'    => $input['contract_header_no'],
                    'sss_ctc'               => 'Cedula',
                    'sssno_ctcno'           => $input['ctc'],
                    'issuedat'              => $input['issuedat'],
                    'issuedon'              => $input['issuedon'],
                    'date_generated'        => $input['date'],
                );
                $data2 = array(
                    'cedula_no'             => $input['ctc'],
                    'cedula_date'           => $input['issuedon'],
                    'cedula_place'          => $input['issuedat'],
                );
            }

            $cond   = array('rec_no' => $input['record_no'], 'emp_id' => $input['emp_id']);
            $chk    = $this->promo_model->selectAll_tcR('employment_witness', $cond);
            if ($chk) {
                $cond       = array('rec_no' => $input['record_no'], 'emp_id' => $input['emp_id']);
                $witness    = $this->promo_model->update_twdA('employment_witness', $cond, $data1);
            } else {
                $data3 = array(
                    'rec_no'            => $input['record_no'],
                    'emp_id'            => $input['emp_id'],
                    'date_generated'    => $input['date'],
                    'generated_by'      => $_SESSION['emp_id']
                );
                $data1      = array_merge($data1, $data3);
                $witness    = $this->promo_model->insert_tdA('employment_witness', $data1);
            }

            $update = $this->promo_model->update_twdA('applicant_otherdetails', array('app_id' => $input['emp_id']), $data2);
        }

        $c = ($input['contract'] === 'current') ? '1' : '0';
        $link = $input['emp_id'] . '_' . $input['record_no'] . '_' . $input['contract_header_no'] . '_' . $c;

        if ($witness && $update) {
            echo json_encode(['message' => 'success', 'data' => $link]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function eocList()
    {
        $input  = $this->input->post(NULL, TRUE);
        $data   = [];
        $bus    = $this->promo_model->locate_promo_bu($this->hrCode);
        $input  = array_merge($input, ['lpbu' => $bus]);
        $order  = array('name', 'startdate', 'eocdate', 'e.emp_id', 'store');
        $list   = $this->promo_model->make_datatables('eocList', $order, $order[2], 'DESC', $input);
        foreach ($list as $row) {

            $empId  = $row['emp_id'];
            $recNo  = $row['record_no'];
            $name   = ucwords(strtolower($row['name']));
            $link   = '<a href="' . base_url('promo/page/promo/profile/' . $empId) . '" target="_blank">' . $name . '</a>';
            $stores = '';
            $check  = true;
            $fail   = false;
            $i      = 0;
            foreach ($bus as $bU) {
                $buField    = $bU['bunit_field'];
                $buName     = $bU['bunit_name'];
                $buAcro     = $bU['bunit_acronym'];

                $hasBu = $this->promo_model->empStores('promo_record', $empId, $recNo, $buField);
                if ($hasBu) {
                    $i++;
                    $cond = array('emp_id' => $empId, 'record_no' => $recNo, 'store' => $buName);
                    $app = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
                    if ($app) {
                        $comm = '';
                        if ($app['raterSO'] == 0 || $app['rateeSO'] == 0) {
                            $comm = ' - for Sign Off';
                            $check = false;
                        }
                        if ($app['numrate'] >= 85) {
                            $appChk = ($app['raterSO'] == 1 && $app['rateeSO'] == 1) ? 'badge bg-success' : 'badge bg-warning';
                        } else {
                            $appChk = 'badge bg-danger';
                            $comm .= ' - Failed';
                            $fail = true;
                        }
                        $strNumrate = '<a href="javascript:;" onclick="viewAppraisal(\'' . $app['details_id'] . '\')"><span class="' . $appChk . ' eocStrEpas"  title="' . $buName . $comm . '">' . $buAcro . ' | ' . $app['numrate'] . '</span></a>';
                    } else {
                        if ($row['type'] == 'Contractual') {
                            $check = false;
                            $strNumrate = '<span class="badge bg-secondary eocStrEpas"  title="' . $buName . '">' . $buAcro . ' | none</span>';
                        } else {
                            $sdate  = date_create($row['startdate']);
                            $edate  = date_create($row['eocdate']);
                            $diff   = date_diff($sdate, $edate);
                            $days   = $diff->format("%a") + 1;
                            if ($days >= 0 && $days <= 15) {
                                $strNumrate = '<span class="badge bg-success eocStrEpas"  title="' . $buName . '">' . $buAcro . ' | Seasonal</span>';
                            } else {
                                $check = false;
                                $strNumrate = '<span class="badge bg-secondary eocStrEpas"  title="' . $buName . '">' . $buAcro . ' | none</span>';
                            }
                        }
                    }

                    $stores = ($i == 1) ? $strNumrate : $stores . ' ' . $strNumrate;
                }
            }

            $action = '<span class="eocStrEpas" title="Incomplete"><i class="bx bx-dots-horizontal-rounded font-22"></i></span>';
            if ($check) {
                $onclick = 'onclick="checkRenewalReq(\'' . $empId . '\',\'' . $recNo . '\')"';
                $action = '<a href="javascript:;" ' . $onclick . '><span class="badge bg-primary">Renew</span></a>';
            }

            if ($fail) {
                $onclick = 'onclick="addBl(\'' . $empId . '\')"';
                $action = '<a href="javascript:;" ' . $onclick . '><span class="badge bg-danger">Blacklist</span></a>';
            }

            $stat   = $row['current_status'];
            $sStat  = $row['sub_status'];
            $col    = 'badge bg-gradient-blooker text-white shadow-sm';
            if ($stat == 'Active') {
                $col = 'badge bg-gradient-quepal text-white shadow-sm';
            }
            $subStat = '';
            if ($sStat != '') {
                $subStat = 'title="' . $sStat . '"';
            }
            $status = '<span class="' . $col . ' eocStrEpas" ' . $subStat . '>' . $stat . '</span>';

            $column     = [];
            $column[]   = $link;
            $column[]   = date('m/d/Y', strtotime($row['startdate']));
            $column[]   = date('m/d/Y', strtotime($row['eocdate']));
            $column[]   = $stores;
            $column[]   = $status;
            $column[]   = $action;
            $data[]     = $column;
        }

        $output = array(
            'draw'              => intval($_POST['draw']),
            'recordsTotal'      => $this->promo_model->get_all_data('eocList', $input),
            'recordsFiltered'   => $this->promo_model->get_filtered_data('eocList', $order, $order[2], 'DESC', $input),
            'data'              => $data
        );
        echo json_encode($output);
    }

    public function viewAppraisal()
    {
        $input      = $this->input->post(NULL, TRUE);
        $cond       = array('details_id' => $input['details_id']);
        $answer     = $this->promo_model->selectAll_tcA('appraisal_answer', $cond);
        $details    = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
        $date       = date('m/d/Y', strtotime($details['ratingdate']));
        $rater      = $this->promo_model->getName_employee3($details['rater']);
        $ratee      = $this->promo_model->getName_employee3($details['emp_id']);
        $store      = $details['store'];

        $raterSO = '<span class="badge bg-primary">YES</span>';
        if ($details['raterSO'] == 0) {
            $raterSO = '<span class="badge bg-secondary">NO</span>';
        }

        $rateeSO = '<span class="badge bg-primary">YES</span>';
        if ($details['rateeSO'] == 0) {
            $rateeSO = '<span class="badge bg-secondary">NO</span>';
        }

        switch ($details['descrate']) {
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
        echo '<table class="table table-bordered">
                <tr>
                    <th colspan="3">Guide Questions</th>
                    <th class="text-center">Rate</th>
                </tr>';
        $i = 1;
        foreach ($answer as $value) {

            $cond = array('appraisal_id' => $value['appraisal_id']);
            $quest = $this->promo_model->selectAll_tcR('appraisal', $cond);
            echo '<tr>
                    <td colspan="3" style="font-size:80%;">
                        ' . $i++ . '. <b>' . $quest['title'] . '</b></br>' . $quest['description'] . '
                    </td>
                    <td class="text-center"><b>' . $value['rate'] . '</b></td>';
            echo '</tr>';
        }

        echo    '<tr>
                    <td>Descriptive Rating</td>
                    <th>' . $descrate . '</th>
                    <td>Total Rating</td>
                    <th class="text-center" style="font-size:16px;">' . $details['numrate'] . '</th>
                </tr>';
        echo '</table>';

        echo '<table class="table table-bordered">';
        echo    '<tr>
                    <td>Rating Date</td>
                    <th>' . $date . '</th>
                    <td>Store</td>
                    <th>' . $store . '</th>
                </tr>';
        echo    '<tr>
                    <td>Ratee</td>
                    <th>' . $ratee . '</th>
                    <td>Rater</td>
                    <th>' . $rater . '</th>
                </tr>';
        echo    '<tr>
                    <td>Ratee Signoff</td>
                    <th>' . $rateeSO . '</th>
                    <td>Rater Signoff</td>
                    <th>' . $raterSO . '</th>
                </tr>';
        echo '</table>';

        echo '<table class="table table-bordered">';
        echo    '<tr>
                    <td width="20%">Rater\'s Comment</td>
                    <td>
                        <textarea class="form-control readonly" readonly>' . $details['ratercomment'] . '</textarea>
                    </td>
                </tr>
                <tr>
                    <td width="20%">Ratee\'s Comment</td>
                    <td>
                        <textarea class="form-control readonly" readonly>' . $details['rateecomment'] . '</textarea>
                    </td>
                </tr>';
        echo '</table>';
    }

    public function checkRenewalReq()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];

        $cond = ['emp_id' => $emp_id, 'record_no' => $record_no];
        $emp3 = $this->promo_model->selectAll_tcR('employee3', $cond);
        if ($emp3) {

            $oneYearAfter = new DateTime($emp3['eocdate']);
            $oneYearAfter->modify('+1 year +1 day');

            if (new DateTime() > $oneYearAfter) {
                echo json_encode(['message' => 'expired', 'data' => $emp_id]);
                exit;
            }
        }

        $cond = array('emp_id' => $emp_id, 'numrate <' => '85');
        $fail = $this->promo_model->selectAll_tcA('appraisal_details', $cond);
        if ($fail) {
            echo json_encode(['message' => 'fail', 'data' => $emp_id]);
            exit;
        }

        if (isset($input['process']) && $input['process'] == 'extend') {
            $cond = array('emp_id' => $emp_id, 'status' => 'Pending');
            $scps = $this->promo_model->selectAll_tcA('secure_clearance_promo', $cond);
            if (!$scps) {
                $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
                $app    = $this->promo_model->selectAll_tcA('appraisal_details', $cond);
                if ($app) {
                    foreach ($app as $row) {
                        if ($row['raterSO'] != '1' || $row['rateeSO'] != '1') {
                            echo json_encode(['message' => 'signoff', 'data' => $emp_id]);
                            exit;
                        }
                    }
                    $_SESSION['renewID'] = $emp_id;
                    $_SESSION['renewalType'] = 'extend';
                    echo json_encode(['message' => 'success', 'data' => $emp_id]);
                    exit;
                } else {
                    $_SESSION['renewID'] = $emp_id;
                    $_SESSION['renewalType'] = 'extend';
                    echo json_encode(['message' => 'success', 'data' => $emp_id]);
                    exit;
                }
            } else {
                $_SESSION['renewID'] = $emp_id;
                $_SESSION['renewalType'] = 'extend';
                echo json_encode(['message' => 'success', 'data' => $emp_id]);
                exit;
            }
        }

        $chk    = true;
        $cond   = array('emp_id' => $emp_id, 'status' => 'Pending');
        $scp    = $this->promo_model->selectAll_tcA('secure_clearance_promo', $cond);
        if ($scp) {
            $chk = false;
        }

        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no, 'clearance_status' => 'Pending');
        $scpd   = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
        if ($scpd) {
            $chk = false;
        }

        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $scpd   = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
        if (!$scpd) {
            $chk = false;
        }

        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $check  = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);

        $i          = 0;
        $stores     = '';
        $secured    = true;
        $bus        = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $field  = $bu['bunit_field'];
            $hasBu  = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $field);
            if ($hasBu) {
                if (!in_array($bu['bunit_name'], array_column($check, 'store'))) {
                    $i++;
                    $stores = ($i == 1) ? $bu['bunit_name'] : $stores . ', ' . $bu['bunit_name'];
                    $secured = false;
                }
            }
        }

        if ($chk) {
            $_SESSION['renewID'] = $emp_id;
            $_SESSION['renewalType'] = 'eoc';
            echo json_encode(['message' => 'success', 'data' => $emp_id, 'secured' => $secured, 'stores' => $stores]);
        } else {
            echo json_encode(['message' => 'upload', 'securedAll' => $secured, 'stores' => $stores]);
        }
    }

    public function uploadClearanceForm()
    {
        $input = $this->input->post(NULL, TRUE);
        $emp_id = $input['emp_id'];
        $record_no = $input['record_no'];
        echo '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 row-cols-xl-3 justify-content-center">';
        echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
        echo '<input type="hidden" name="record_no" value="' . $record_no . '">';
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $hasBu = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
            if ($hasBu) {
                $clrName = $bu['bunit_clearance'];
                echo '<div class="col">';
                echo '<div class="card radius-10 border shadow-none">';
                echo '<img id="imagePreview_' . $clrName . '" src="#" alt="Image Preview" class="card-img-top" style="display: none;">';
                echo '<div class="card-body">';
                echo    '<h5 class="card-title text-primary">' . $bu['bunit_name'] . '</h5>';
                echo    '<hr>';
                echo    '<div class="d-flex align-items-center gap-2">';
                echo        '<input type="file" id="' . $clrName . '" name="' . $clrName . '" class="form-control form-control-sm" accept="image/*" onchange="imgPreview(this)">';
                echo        '<a href="javascript:;" id="clearButton_' . $clrName . '" class="btn btn-sm btn-secondary" onclick="clearImageInput(\'' . $clrName . '\')" style="display: none;">Clear</a>';
                echo    '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        }
        echo '</div>';
    }

    public function uploadClearanceRenewal()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];

        $curStores  = [];
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $chk = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
            if ($chk) {
                $curStores[] = $bu['bunit_name'];
            }
        }

        $cond =  array('emp_id' => $emp_id, 'status' => 'Pending');
        $scpr_id = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond)['scpr_id'];
        if ($scpr_id) {
            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $bu) {

                $field      = $bu['bunit_field'];
                $clrName    = $bu['bunit_clearance'];
                $stores     = $bu['bunit_name'];
                $hasBu      = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $field);
                if ($hasBu) {

                    $path = '';
                    if (isset($_FILES[$clrName]['name'])) {
                        $array          = explode(".", $_FILES[$clrName]['name']);
                        $filename       = $emp_id . '=' . date('Y-m-d') . '=' . $clrName . '=' . date('H-i-s-A') . '.' . end($array);
                        $base_path      = FCPATH;
                        $relative_path  = '../document/clearance/';
                        $path           = $base_path . $relative_path . $filename;
                        if (move_uploaded_file($_FILES[$clrName]['tmp_name'], $path)) {
                            $clearance = $relative_path . $filename;
                        }
                    }

                    $data = array('clearance_status' => 'Completed', 'date_cleared' => $this->today);
                    $cond = array('emp_id' => $emp_id, 'store' => $stores, 'scpr_id' => $scpr_id, 'clearance_status' => 'Pending');
                    $scpd = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                    if ($scpd) {
                        $cond = array('scpr_id' => $scpr_id, 'scdetails_id' => $scpd['scdetails_id']);
                        $pcm = $this->promo_model->selectAll_tcR('promo_clearance_monitoring', $cond);
                        if ($pcm) {
                            $data['date_effectivity'] = $pcm['date_effectivity'];

                            $data1 = array('status' => 'Done');
                            $c = $this->promo_model->update_twdA('promo_clearance_monitoring', $cond, $data1);
                        }
                    }

                    $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);

                    if (isset($clearance)) {

                        $data   = array($clrName => $clearance);
                        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
                        $update = $this->promo_model->update_twdA('promo_record', $cond, $data);
                    }

                    $cond       = array('scpr_id' => $scpr_id, 'clearance_status' => 'Completed');
                    $clrStores  = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);

                    $cond   = array('scpr_id' => $scpr_id);
                    $reason = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond)['reason'];

                    $activity   = "Uploaded Promo $reason Clearance of ($emp_id) for $stores with scpr_id: $scpr_id";
                    $data       = array(
                        'activity'  => $activity,
                        'date'      => $this->today,
                        'time'      => date("H:i:s"),
                        'user'      => $this->userId,
                        'username'  => $_SESSION['username'],
                    );
                    $logs = $this->promo_model->insert_tdA('logs', $data);

                    $chk = array_diff($curStores, array_column($clrStores, 'store'));
                    if (!$chk) {
                        $data   = array('status' => 'Completed');
                        $cond   = array('scpr_id' => $scpr_id);
                        $scp    = $this->promo_model->update_twdA('secure_clearance_promo', $cond, $data);

                        $cond = array('ratee_id' => $emp_id, 'tag_stat' => 'Pending');
                        $this->promo_model->delete_tcr_row('tag_for_resignation', $cond);

                        $cs     = ($reason == 'Termination') ? 'End of Contract' : $reason;
                        $data   = array('current_status' => $cs, 'sub_status' => 'Cleared');
                        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
                        $emp3   = $this->promo_model->update_twdA('employee3', $cond, $data);

                        $data   = array('user_status' => 'inactive');
                        $cond   = array('emp_id' => $emp_id);
                        $update = $this->promo_model->update_twdA('users', $cond, $data);
                    }
                }
            }
        }

        if ($emp3 && $scp) {
            $_SESSION['renewID'] = $emp_id;
            $_SESSION['renewalType'] = 'eoc';
            echo json_encode(['message' => 'success', 'data' => $emp_id]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function getSelectRenewal()
    {
        $input = $this->input->post(NULL, TRUE);
        if ($input['action'] == 'getCompany') {

            if ($input['value'] != '') {
                $row = $this->promo_model->selectAll_tcA_tk('promo_locate_company', array('agency_code' => $input['value']));
            } else {
                $row = $this->promo_model->selectAll('locate_promo_company');
            }
            $comp = ($input['value'] != '') ? 'company_name' : 'pc_name';

            echo '<option value="">Select Company</option>';
            foreach ($row as $value) {
                echo '<option value="' . $value[$comp] . '">' . $value[$comp] . '</option>';
            }
        } else if ($input['action'] == 'getProduct') {

            $row = $this->promo_model->selectAll_tcA('promo_company_products', array('company' => $input['value']));
            echo '<option value="">Select Product</option>';
            foreach ($row as $value) {
                echo '<option value="' . $value['product'] . '">' . $value['product'] . '</option>';
            }
        } else if ($input['action'] == 'getStores') {

            $type = ($input['value'] == 'ROVING') ? 'checkbox' : 'radio';
            $table2 = ($input['contract'] == 'current') ? 'promo_record' : 'promo_history_record';
            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $bu) {

                $hasBu      = $this->promo_model->empStores($table2, $input['emp_id'], $input['record_no'], $bu['bunit_field']);
                $checked    = ($input['promo_type'] == $input['value'] && $hasBu > 0) ? 'checked' : '';
                $field      = $bu['bunit_id'] . '|' . $bu['bunit_field'];

                echo '<li class="list-group-item">
                        <div class="form-check form-check-success">
                            <input type="' . $type . '" class="form-check-input" name="stores[]" id="' . $field . '"  value="' . $field . '" ' . $checked . ' onclick="getDepartment()">
                            <label class="form-check-label" for="' . $field . '">' . $bu['bunit_name'] . '</label>
                        </div>';
                echo '</li>';
            }
        } else if ($input['action'] == 'getDepartment') {

            if (isset($input['value'])) {
                $id = [];
                foreach ($input['value'] as $value) {
                    $val = explode('|', $value);
                    $id[] = $val['0'];
                }
                echo '<option value="">Select Department</option>';
                $department = $this->promo_model->whereIN_stcd('DISTINCT', 'dept_name', 'locate_promo_department', 'bunit_id', $id);
                foreach ($department as $value) {
                    echo '<option value="' . $value['dept_name'] . '">' . $value['dept_name'] . '</option>';
                }
            } else {
                echo '<option value="">Select Department</option>';
            }
        } else if ($input['action'] == 'getIntros') {
            if (isset($input['value'])) {
                $id         = [];
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
        } else if ($input['action'] == 'getVendor') {
            echo '<option value="">Select Vendor</option>';
            $vendor = $this->promo_model->selectAll_tcA('promo_vendor_lists', array('department' => $input['value']));
            foreach ($vendor as $value) {
                echo '<option value="' . $value['vendor_code'] . '">' . $value['vendor_name'] . '</option>';
            }
        } else if ($input['action'] == 'getDuration') {

            $days       = '';
            $months     = '';
            $date1      = $input['startdate'];
            $date2      = $input['eocdate'];
            $message    = '';
            if ($date1 != '') {
                $timestamp1 = strtotime($date1);
                $timestamp2 = strtotime($date2);
                if ($timestamp1 < $timestamp2) {
                    $startdate  = new DateTime($date1);
                    $eocdate    = new DateTime($date2);
                    $interval   = $startdate->diff($eocdate);
                    $years      = $interval->y;
                    $months     = $interval->m;
                    $days       = $interval->d;
                    if ($input['renewalType'] == 'extend') {
                        $days = $interval->d + 1;
                    }
                    $result     = 'success';
                    if ($years > 0) {
                        $message    .=  "$years year" . ($years > 1 ? "s" : "") . " ";
                    }
                    if ($months > 0) {
                        $message    .= "$months month" . ($months > 1 ? "s" : "") . " ";
                    }
                    if ($days > 0) {
                        $message    .= "$days day" . ($days > 1 ? "s" : "");
                    }
                } else if ($timestamp1 > $timestamp2) {
                    if ($input['eocdate'] == '') {
                        $result = 'success';
                        $message = 'EOCdate is open!';
                    } else {
                        $result = 'failed';
                        $message = 'EOCdate should be later than Startdate!';
                    }
                } else {
                    $result     = 'success';
                    $message    = "1 day";
                }
            } else {
                $result     = 'failed';
                $message    = 'Startdate is blank!';
            }
            echo json_encode(['result' => $result, 'message' => $message, 'months' => $months, 'days' => $days]);
        } else if ($input['action'] == 'getDutydays') {

            $i = 0;
            $dutyDays = '';
            $days = array(
                'Monday' => 'Mon',
                'Tuesday' => 'Tue',
                'Wednesday' => 'Wed',
                'Thursday' => 'Thu',
                'Friday' => 'Fri',
                'Saturday' => 'Sat',
                'Sunday' => 'Sun'
            );
            foreach ($days as $key => $value) {
                // if ($value != substr($input['value'], 0, 3)) {
                //     $i++;
                //     $dutyDays = ($i == 1) ? $value : $dutyDays . ', ' . $value;
                // }

                if (!in_array($key, $input['value'])) {
                    $i++;
                    $dutyDays = ($i == 1) ? $value : $dutyDays . ', ' . $value;
                }
            }
            echo $dutyDays;
        } else if ($input['action'] == 'filterDepartment') {
            if (isset($input['value'])) {
                $val = explode('|', $input['value']);
                $id = $val['0'];
                echo '<option value="">Department...</option>';
                $department = $this->promo_model->selectAll_tcA('locate_promo_department', array('bunit_id' => $id));
                foreach ($department as $value) {
                    echo '<option value="' . $value['dept_name'] . '">' . $value['dept_name'] . '</option>';
                }
            } else {
                echo '<option value="">Department...</option>';
            }
        }
    }

    public function renewContract()
    {
        $input      = $this->input->post(NULL, TRUE);
        $empId      = $input['emp_id'];
        $recordNo   = $input['record_no'];

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
        $data   = [];
        $fields = array('promo_id');
        $promo  = $this->promo_model->selectAll_tcR('promo_record', $cond);
        foreach ($promo as $field => $value) {
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

        // updating of appraisal details table ******************************************************************
        $data   = array('record_no' => $recordNo_);
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $update = $this->promo_model->update_twdA('appraisal_details', $cond, $data);
        // updating of appraisal details table ******************************************************************

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

        // updating of promo_schedule table ******************************************************************
        $data   = array('record_no' => $recordNo_);
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $update = $this->promo_model->update_twdA('promo_schedule', $cond, $data);
        // updating of promo_schedule table ******************************************************************

        // updating of secure_clearance_promo_details table ******************************************************************
        $data   = array('record_no' => $recordNo_);
        $cond   = array('emp_id' => $empId, 'record_no' => $recordNo);
        $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);
        // updating of secure_clearance_promo_details table ******************************************************************

        // inserting of employee3 table ******************************************************************
        $cond = array('position' => $input['position']);
        $poslevel = $this->promo_model->selectAll_tcR('positions', $cond)['positionlevel'];
        $eocdate = (isset($input['eocdate'])) ? $input['eocdate'] : '0000-00-00';

        $data = array(
            'emp_id'            => $empId,
            'name'              => $oldEmp3['name'],
            'startdate'         => $input['startdate'],
            'eocdate'           => $input['eocdate'],
            'emp_type'          => $input['emp_type'],
            'barcodeId'         => $oldEmp3['barcodeId'],
            'bioMetricId'       => $oldEmp3['bioMetricId'],
            'position'          => $input['position'],
            'positionlevel'     => intval($poslevel),
            'comments'          => $input['comments'],
            'remarks'           => $input['remarks'],
            'date_added'        => $this->today,
            'added_by'          => $this->userId,
            'duration'          => $input['duration'],
            'emp_no'            => $oldEmp3['emp_no'],
            'emp_pins'          => $oldEmp3['emp_pins'],
            'payroll_no'        => $oldEmp3['payroll_no'],
            'current_status'    => 'Active',
        );

        $record_no = $this->promo_model->insert_tdA('employee3', $data);
        // inserting of employee3 table ******************************************************************

        // inserting of promo_record table ******************************************************************
        $compDuration = (isset($input['company_duration'])) ? $input['company_duration'] : '0000-00-00';
        $data = array(
            'record_no'         => $record_no,
            'emp_id'            => $empId,
            'agency_code'       => $input['agency_code'],
            'promo_company'     => $input['promo_company'],
            'promo_department'  => $input['promo_department'],
            'vendor_code'       => $input['vendor_code'],
            'company_duration'  => $compDuration,
            'promo_type'        => $input['promo_type'],
            'type'              => $input['type'],
            'hr_location'       => $this->hrCode
        );

        foreach ($input['stores'] as $value) {
            $field = explode('|', $value);
            $data[$field[1]] = 'T';
        }

        $promo_record = $this->promo_model->insert_tdA('promo_record', $data);
        // inserting of promo_record table ******************************************************************

        // uploading of intro ******************************************************************
        $intro = $this->uploadIntro($input, $record_no);
        // uploading of intro ******************************************************************     

        // updating for contract extension table ******************************************************************
        if ($input['renewalType'] == 'extend') {
            if ($oldEmp3['sub_status'] != 'Cleared' && $oldEmp3['sub_status'] != '') {
                $data   = array('record_no' => $record_no);
                $cond   = array('emp_id' => $empId, 'record_no' => $recordNo_);
                $app    = $this->promo_model->update_twdA('appraisal_details', $cond, $data);

                $cond   = array('emp_id' => $empId, 'record_no' => $recordNo_, 'clearance_status' => 'Pending');
                $scpd   = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);

                $data   = array('record_no' => $record_no, 'date_effectivity' => $input['eocdate']);
                $cond   = array('emp_id' => $empId, 'record_no' => $recordNo_, 'clearance_status' => 'Pending');
                $ex     = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);
                if ($ex) {
                    foreach ($scpd as $key => $clr) {

                        $cond = array('scpr_id' => $clr['scpr_id'], 'scdetails_id' => $clr['scdetails_id']);
                        $chk = $this->promo_model->selectAll_tcR('promo_clearance_monitoring', $cond);
                        if (!$chk) {
                            $data = array(
                                'emp_id'            => $empId,
                                'record_no'         => $record_no,
                                'recordNo'          => $recordNo_,
                                'scpr_id'           => $clr['scpr_id'],
                                'scdetails_id'      => $clr['scdetails_id'],
                                'extend_date'       => $input['eocdate'],
                                'date_effectivity'  => $clr['date_effectivity'],
                                'store'             => $clr['store'],
                                'status'            => 'Pending',
                                'date'              => $this->today,
                            );
                            $c = $this->promo_model->insert_tdA('promo_clearance_monitoring', $data);
                        } else {
                            $cond = array('id' => $chk['id']);
                            $data = array('extend_date' => $input['eocdate'], 'record_no' => $record_no);
                            $c = $this->promo_model->update_twdA('promo_clearance_monitoring', $cond, $data);
                        }
                    }
                }
            } else {
                $cond = array('emp_id' => $empId, 'record_no' => $recordNo, 'status' => 'Pending');
                $pcm = $this->promo_model->selectAll_tcA('promo_clearance_monitoring', $cond);
                if ($pcm) {
                    $data   = array('record_no' => $record_no);
                    $cond   = array('emp_id' => $empId, 'record_no' => $recordNo_);
                    $upd    = $this->promo_model->update_twdA('appraisal_details', $cond, $data);

                    $data = array('record_no' => $record_no, 'date_effectivity' => $input['eocdate']);
                    $cond = array('emp_id' => $empId, 'record_no' => $recordNo_, 'clearance_status' => 'Pending');
                    $upd = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);

                    $cond = array('emp_id' => $empId, 'record_no' => $recordNo, 'status' => 'Pending');
                    $data = array('extend_date' => $input['eocdate'], 'record_no' => $record_no);
                    $upd = $this->promo_model->update_twdA('promo_clearance_monitoring', $cond, $data);
                }
            }

            foreach ($input['stores'] as $value) {
                $field  = explode('|', $value);
                $cond   = ['bunit_field' => $field[1]];
                $ecode  = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond)['bunit_epascode'];

                $data   = [$ecode => '1'];
                $cond   = ['emp_id' => $empId, 'record_no' => $record_no];
                $upd    = $this->promo_model->update_twdA('promo_record', $cond, $data);
            }
        }
        // updating for contract extension table ******************************************************************

        // inserting of employment_witness table ******************************************************************
        $data   = array('emp_id' => $empId, 'rec_no' => $record_no, 'witness1' => $input['w1'], 'witness2' => $input['w2']);
        $insert = $this->promo_model->insert_tdA('employment_witness', $data);
        // inserting of employment_witness table ******************************************************************

        // inserting of promo_products table ******************************************************************
        if (isset($input['product'])) {
            $data = array('emp_id' => $input['emp_id'], 'record_no' => $record_no, 'created_at' => date('Y-m-d H:i:s'));

            foreach ($input['product'] as $value) {
                $data['product']  = $value;
                $insert = $this->promo_model->insert_tdA('promo_products', $data);
            }
        }
        // inserting of promo_products table ******************************************************************

        $renewType = ($input['renewalType'] == 'extend') ? 'Extended' : 'Renew';
        $activity = "$renewType Promo Contract of Employment of $oldEmp3[name] ($empId) with Record No:" . $record_no;
        $data = array(
            'activity'  => $activity,
            'date'      => $this->today,
            'time'      => date("H:i:s"),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $insert = $this->promo_model->insert_tdA('logs', $data);

        if ($record_no && $promo_record) {

            $data   = array('user_status' => 'active');
            $cond   = array('emp_id' => $empId);
            $update = $this->promo_model->update_twdA('users', $cond, $data);

            $cond = array('emp_id' => $empId, 'record_no' => $recordNo);
            $del1 = $this->promo_model->delete_tcr_row('employee3', $cond);
            $del2 = $this->promo_model->delete_tcr_row('promo_record', $cond);

            $cond = array('ratee_id' => $empId, 'tag_stat' => 'Pending');
            $del3 = $this->promo_model->delete_tcr_row('tag_for_resignation', $cond);

            // inserting and updating of promo_sched_emp table ******************************************************************
            $servers = ['corporate'];
            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $bu) {
                $field = $bu['bunit_field'];
                $hasBu = $this->promo_model->empStores('promo_record', $empId, $record_no, $field);
                if ($hasBu) {
                    if ($field == 'al_tal') {
                        $servers[]    = 'talibon';
                    } else if ($field == 'al_tub' || $field == 'fr_tubigon') {
                        $servers[]    = 'tubigon';
                    }
                }
            }

            $cond = array('empId' => $empId, 'recordNo' => $recordNo);
            foreach ($servers as $server) {
                $data   = array('recordNo' => $record_no, 'statCut' => $input['statCut'], 'date_setup' => $this->today);
                $update = $this->promo_model->promoCutoff_update($server, $cond, $data);
            }
            // inserting and updating of promo_sched_emp table ******************************************************************

            $data = array('message' => 'success', 'emp_id' => $empId, 'record_no' => $record_no, 'contract' => 'current');
            echo json_encode($data);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function transferRateForm()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
        echo '<input type="hidden" name="record_no" value="' . $record_no . '">';
        echo '<div class="table-responsive">';
        echo '<table class="table table-sm table-hover">
                <tr>
                    <th>No.</th>
                    <th>Store</th>
                    <th>Rate</th>
                    <th>Rater</th>
                    <th>Startdate</th>
                    <th>EOCdate</th>
                </tr>';
        $cond       = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $promo_type = $this->promo_model->selectAll_tcR('promo_record', $cond)['promo_type'];
        $checkbox   = ($promo_type == 'ROVING') ? 'checkbox' : 'radio';

        $cond = array('e.emp_id' => $emp_id);
        $row = $this->promo_model->promoDetails_wttA($cond, 'employmentrecord_', 'promo_history_record');
        if ($row) {
            $no = 0;
            foreach ($row as $value) {
                $no++;
                $empId      = $value['emp_id'];
                $recordNo   = $value['record_no'];
                $cond       = array('emp_id' => $empId, 'record_no' => $recordNo);
                $appDetails = $this->promo_model->selectAll_tcA('appraisal_details', $cond);
                if ($appDetails) {
                    foreach ($appDetails as $appVal) {

                        $id = $appVal['details_id'];
                        $rater = $this->promo_model->getName_employee3($appVal['rater']);
                        $idVal = 'type="' . $checkbox . '" id="' . $id . '" value="' . $id . '"';
                        $cond = array('bunit_name' => $appVal['store']);
                        $bu = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);
                        if ($bu) {
                            $cond       = array('emp_id' => $empId, 'record_no' => $recordNo, $bu['bunit_epascode'] => '1');
                            $chk        = $this->promo_model->selectAll_tcR('promo_history_record', $cond);
                            $disabled   = ($chk) ? '' : 'disabled';
                            $onclick    = $idVal . 'onclick="chkStore(\'' . $empId . '\',\'' . $record_no . '\')"' . $disabled;

                            echo '<tr>
                                    <td><span class="badge bg-danger">' . $no . '</span> | <input class="form-check-input" name="details_id[]" ' . $onclick . '></td>
                                    <td>' . $appVal['store'] . '</td>
                                    <td>' . $appVal['numrate'] . '</td>
                                    <td>' . $rater . '</td>
                                    <td>' . date('m/d/Y', strtotime($value['startdate'])) . '</td>
                                    <td>' . date('m/d/Y', strtotime($value['eocdate'])) . '</td>';
                            echo '</tr>';
                        } else {

                            echo '<tr>
                                    <td><span class="badge bg-danger">' . $no . '</span> | <input class="form-check-input" type="' . $checkbox . '" disabled></td>
                                    <td>' . $appVal['store'] . '</td>
                                    <td>' . $appVal['numrate'] . '</td>
                                    <td>' . $rater . '</td>
                                    <td>' . date('m/d/Y', strtotime($value['startdate'])) . '</td>
                                    <td>' . date('m/d/Y', strtotime($value['eocdate'])) . '</td>';
                            echo '</tr>';
                        }
                    }
                } else {
                    echo '<tr>';
                    echo    '<td class="text-center" colspan="6">No Appraisal Rate for Previous Contract No. <span class="badge bg-danger">' . $no . '</span></td>';
                    echo '</tr>';
                }
            }
        }
        echo '</table>';
        echo '</div>';
        echo '<div class="d-md-flex d-grid align-items-center gap-1 justify-content-between mt-2">
                <ul class="list-unstyled">
                    <li><span class="badge text-secondary"><b>NOTE:&nbsp;</b>Previous Contract No.<span class="badge bg-danger">1</span></span></li>
                    <ul>
                        <li><span class="badge text-secondary">Refers to Contract History on the Profile</span></li>
                        <li><span class="badge text-secondary">Inactive checkbox is either not Previous Contract No. 1 or epascode is blank</span></li>
                    </ul>
                </ul>';
        echo '<button type="submit" class="btn btn-primary transferButton">Transfer Rate</button>';
        echo '</div>';
    }

    public function checkStores()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];

        $stores = [];
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $hasBu = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
            if ($hasBu) {
                $stores[] = $bu['bunit_name'];
            }
        }

        $exist = false;
        if (isset($input['details_id'])) {
            foreach ($input['details_id'] as $details_id) {
                $id         = $details_id;
                $cond       = array('details_id' => $details_id);
                $appraisal  = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
                if ($appraisal) {
                    if (in_array($appraisal['store'], $stores)) {
                        $exist = true;
                    }
                }
            }
        }

        echo json_encode(['message' => 'success', 'data' => $exist, 'id' => $id]);
    }

    public function transferRateSave()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];

        foreach ($input['details_id'] as $details_id) {

            $cond       = array('details_id' => $details_id);
            $appraisal  = $this->promo_model->selectAll_tcR('appraisal_details', $cond);

            $cond   = array('bunit_name' => $appraisal['store']);
            $field  = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond)['bunit_epascode'];

            $cond   = array('emp_id' => $emp_id, 'record_no' => $appraisal['record_no']);
            $data   = array($field => '');
            $hist   = $this->promo_model->update_twdA('promo_history_record', $cond, $data);

            $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
            $data   = array($field => '1');
            $promo  = $this->promo_model->update_twdA('promo_record', $cond, $data);

            $data   = array('record_no' => $record_no);
            $cond   = array('details_id' => $details_id);
            $app    = $this->promo_model->update_twdA('appraisal_details', $cond, $data);

            $activity = "Promo transfer rate of ($emp_id) with Appraisal ID: $details_id";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $insert = $this->promo_model->insert_tdA('logs', $data);
        }

        if ($hist && $promo && $app) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function uploadIntro($input, $record_no)
    {
        $intro = [];
        $empId = $input['emp_id'];
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $introName = $bu['bunit_intro'];
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
                    $data   = array($introName => $filename);
                    $cond   = array('emp_id' => $empId, 'record_no' => $record_no);
                    $update = $this->promo_model->update_twdA('promo_record', $cond, $data);
                    if ($update) {
                        $intro[] = $bu['bunit_name'];
                    }
                }
            }
        }

        return $intro;
    }

    public function contractsDetails()
    {
        $data   = [];
        $bus    = $this->promo_model->locate_promo_bu($this->hrCode);
        $cond   = "emp_type IN ('Promo', 'Promo-NESCO') AND YEAR(date_added)>='2025'";
        $query  = $this->promo_model->promoDetails_wttA($cond, 'employee3', 'promo_record');
        foreach ($query as $key => $row) {
            $empId  = $row['emp_id'];
            $recNo  = $row['record_no'];
            $name   = $row['name'];
            $comp   = $row['promo_company'];
            $dept   = $row['promo_department'];
            $post   = $row['position'];
            $ptype  = $row['promo_type'];
            $sd     = $row['startdate'];
            $ed     = $row['eocdate'];
            $status = $row['current_status'];

            $i      = 0;
            $stores = '';
            $uCont  = '';
            $uBy    = '';
            $uDate  = '';
            foreach ($bus as $bu) {
                $buField    = $bu['bunit_field'];
                $buName     = $bu['bunit_name'];
                $buCont     = $bu['bunit_contract'];
                $buAcro     = $bu['bunit_acronym'];
                $str        = '<span class="badge bg-success">' . $buAcro . '</span>';
                if ($row[$buField] == 'T') {
                    $i++;
                    $contract   = '';
                    $date       = '';
                    $uname      = '';
                    if ($row[$buCont]) {
                        $contract = $row[$buCont];
                        $ex = explode('=', $contract);
                        if (isset($ex[1])) {
                            $date = $ex[1];
                            $cond = "date='$date' AND activity='Uploaded Promo contract of $empId for $buName with record_no: $recNo' LIMIT 1";
                            $logs = $this->promo_model->selectAll_tcR('logs', $cond);
                            if ($logs) {
                                $uname = $this->promo_model->getName_employee3($logs['user']);
                            }
                        }
                    }

                    $stores = ($i == 1) ? $str : $stores . '<br>' . $str;
                    $uCont  = ($i == 1) ? $contract : $uCont . '<br>' . $contract;
                    $uBy    = ($i == 1) ? $uname : $uBy . '<br>' . $uname;
                    $uDate  = ($i == 1) ? $date : $uDate . '<br>' . $date;
                }
            }

            $column     = [];
            $column[]   = $empId;
            $column[]   = $name;
            $column[]   = $comp;
            $column[]   = $dept;
            $column[]   = $post;
            $column[]   = $ptype;
            $column[]   = $sd;
            $column[]   = $ed;
            $column[]   = $status;
            $column[]   = $stores;
            $column[]   = $uCont;
            $column[]   = $uBy;
            $column[]   = $uDate;
            $data[]     = $column;
        }

        $cond   = "emp_type IN ('Promo', 'Promo-NESCO') AND YEAR(startdate)>='2025'";
        $query  = $this->promo_model->promoDetails_wttA($cond, 'employmentrecord_', 'promo_history_record');
        foreach ($query as $key => $row) {
            $empId  = $row['emp_id'];
            $recNo  = $row['record_no'];
            $name   = $row['names'];
            $comp   = $row['promo_company'];
            $dept   = $row['promo_department'];
            $post   = $row['position'];
            $ptype  = $row['promo_type'];
            $sd     = $row['startdate'];
            $ed     = $row['eocdate'];
            $status = $row['current_status'];

            $i      = 0;
            $stores = '';
            $uCont  = '';
            $uBy    = '';
            $uDate  = '';
            foreach ($bus as $bu) {
                $buField    = $bu['bunit_field'];
                $buName     = $bu['bunit_name'];
                $buCont     = $bu['bunit_contract'];
                $buAcro     = $bu['bunit_acronym'];
                $str        = '<span class="badge bg-success">' . $buAcro . '</span>';
                if ($row[$buField] == 'T') {
                    $i++;
                    $contract   = '';
                    $date       = '';
                    $uname      = '';
                    if ($row[$buCont]) {
                        $contract = $row[$buCont];
                        $ex = explode('=', $contract);
                        if (isset($ex[1])) {
                            $date = $ex[1];
                            $cond = "date='$date' AND activity='Uploaded Promo contract of $empId for $buName with record_no: $recNo' LIMIT 1";
                            $logs = $this->promo_model->selectAll_tcR('logs', $cond);
                            if ($logs) {
                                $uname = $this->promo_model->getName_employee3($logs['user']);
                            }
                        }
                    }

                    $stores = ($i == 1) ? $str : $stores . '<br>' . $str;
                    $uCont  = ($i == 1) ? $contract : $uCont . '<br>' . $contract;
                    $uBy    = ($i == 1) ? $uname : $uBy . '<br>' . $uname;
                    $uDate  = ($i == 1) ? $date : $uDate . '<br>' . $date;
                }
            }

            $column     = [];
            $column[]   = $empId;
            $column[]   = $name;
            $column[]   = $comp;
            $column[]   = $dept;
            $column[]   = $post;
            $column[]   = $ptype;
            $column[]   = $sd;
            $column[]   = $ed;
            $column[]   = $status;
            $column[]   = $stores;
            $column[]   = $uCont;
            $column[]   = $uBy;
            $column[]   = $uDate;
            $data[]     = $column;
        }

        echo json_encode(['data' => $data]);
    }
}
