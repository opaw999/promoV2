<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Clearance extends CI_Controller
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
        $this->adminUser = array('01186-2023');
        if (!isset($this->userId)) {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }
    }

    public function clearanceList()
    {
        $input = $this->input->post(NULL, TRUE);
        $order = array(
            'e.name',
            's.date_added',
            's.emp_id',
            'e.current_status',
            'e.sub_status',
            's.status',
            's.reason',
            's.promo_type',
        );
        $data = [];
        $list = $this->promo_model->make_datatables('clearanceList', $order, $order[1], 'DESC', $input);
        foreach ($list as $row) {

            $cStat = $row['current_status'];
            $sStat = $row['sub_status'];

            if ($cStat == 'Active') {
                $span = 'badge bg-success';
            } else if ($cStat == 'blacklisted') {
                $span = 'badge bg-danger';
            } else {
                $span = ($row['status'] == 'Completed' || $sStat != 'Uncleared') ? 'badge bg-warning' : 'badge bg-danger';
            }

            $current_status = '<span class="' . $span . '">' . $cStat  . '</span>';
            if ($sStat != '') {
                $current_status = '<span class="' . $span . '">' . $cStat  . ' (' . $sStat . ')</span>';
            }

            $clearStat = '<font class="text-warning">' . $row['status'] . '</font>';
            if ($row['status'] == 'Completed') {
                $clearStat = '<font class="text-success">' . $row['status'] . '</font>';
            }

            $name = ucwords(strtolower($row['name']));
            $cond = array('emp_id' => $row['emp_id'], 'scpr_id' => $row['scpr_id']);
            $eDate = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);

            $onclick = 'onclick="clearanceDetails(\'' . $row['reason']  . '\',\'' . $row['scpr_id'] . '\')"';
            $action = '<a href="javascript:;" ' . $onclick . '><i class="bx bx-folder-open me-0 font-22" title="View Clearance"></i></a>';
            if ($row['status'] != 'Completed') {
                $onclick = 'onclick="clearanceUpdateForm(\'' . $row['scpr_id'] . '\')"';
                $action .= '<a href="javascript:;" ' . $onclick . '><i class="bx bx-edit me-0 font-22" title="Update Clearance"></i></a>';
            } else {
                $action .= '<i class="text-secondary bx bx-edit me-0 font-22"></i>';
            }

            $column     = [];
            $column[]   = '<a href="' . base_url('promo/page/promo/profile/' . $row['emp_id']) . '" target="_blank">' . $name . '</a>';
            $column[]   = date('m/d/Y', strtotime($row['dateAdded']));
            $column[]   = date('m/d/Y', strtotime($eDate['date_effectivity']));
            $column[]   = $current_status;
            $column[]   = $clearStat;
            $column[]   = $row['promo_type'];
            $column[]   = $row['reason'];
            $column[]   = $action;
            $data[]     = $column;
        }

        $output = array(
            'draw'              => intval($_POST['draw']),
            'recordsTotal'      => $this->promo_model->get_all_data('clearanceList', $input),
            'recordsFiltered'   => $this->promo_model->get_filtered_data('clearanceList', $order, $order[1], 'DESC', $input),
            'data'              => $data
        );
        echo json_encode($output);
    }

    public function filterYear()
    {
        $input  = $this->input->post(NULL, TRUE);
        $month  = $input['month'];
        $year   = $input['year'];
        $table  = 'secure_clearance_promo';
        $sel    = 'MONTH(date_added) AS months';
        $cond   = "YEAR(date_added)='$year' ORDER BY months DESC;";
        $rowM   = $this->promo_model->selectDistinct($sel, $table, $cond);

        $arr = array_column($rowM, 'months');
        if (!in_array($month, $arr)) {
            $rowM[] = ['months' => $month];
        }

        foreach ($rowM as $key => $m) {
            $sel = ($month == $m['months']) ? 'selected' : '';
            echo '<option value="' . $m['months'] . '" ' . $sel . '>' . $m['months'] . '</option>';
        }
    }

    public function clearanceDetails()
    {
        $input  = $this->input->post(NULL, TRUE);
        $reason = $input['reason'];
        echo '<div class="table-responsive">';
        echo '<table class="table table-sm mb-0 table-hover" style="width:100%;">
                <tr>
                    <th>Store(s)</th>
                    <th class="text-center">EPAS</th>
                    <th>Date Secured</th>
                    <th>Date Effective</th>
                    <th>Date Cleared</th>
                    <th>Status</th>
                    <th>Added By</th>';
        if ($reason === 'Deceased') {
            echo    '<th class="text-center">Death Cert.</th>';
        } elseif ($reason !== 'Termination') {
            echo    '<th class="text-center">Letter</th>';
        }
        echo        '<th class="text-center">Print</th>';
        echo    '</tr>';
        $cond = array('scpr_id' => $input['scpr_id']);
        $query = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
        foreach ($query as $row) {

            $emp_id = $row['emp_id'];
            $cId    = $row['scdetails_id'];
            $cond   = array('emp_id' => $emp_id, 'record_no' => $row['record_no'], 'store' => $row['store']);
            $epas   = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
            if ($epas) {
                $epasId = $epas['details_id'];
                $badge = 'danger';
                if ($epas['numrate'] >= 85) {
                    $badge = ($epas['raterSO'] == 1 && $epas['rateeSO'] == 1) ? 'success' : 'warning';
                }
                $onclick = 'onclick="viewAppraisal(\'' . $epasId . '\')"';
                $app = '<a href="javascript:;" ' . $onclick . '><span class="badge bg-' . $badge . '" >' . $epas['numrate'] . '</span></a>';
            } else {
                $app = '<span class="spanTooltip badge bg-secondary" >No EPAS</span>';
            }

            $addedBy = $this->promo_model->getName_employee3($row['added_by']);

            $dateCleared = '';
            if ($row['date_cleared'] != '0000-00-00') {
                $dateCleared = date('m/d/Y', strtotime($row['date_cleared']));
            }

            $status = ($row['clearance_status'] == 'Completed') ? 'text-success' : 'text-warning';

            echo '<tr>
                    <td>' . $row['store'] . '</td>
                    <td class="text-center">' . $app . '</td>
                    <td>' . date('m/d/Y', strtotime($row['date_secure'])) . '</td>
                    <td>' . date('m/d/Y', strtotime($row['date_effectivity'])) . '</td>
                    <td>' . $dateCleared . '</td>
                    <td class="' . $status . '">' . $row['clearance_status'] . '</td>
                    <td>' . ucwords(strtolower($addedBy)) . '</td>';
            $letter = $row['resignation_letter'];
            $path1  = FCPATH . 'document/resignation/' . $letter;
            $path2  = FCPATH . '../promo/' . $letter;
            if (file_exists($path1)) {
                $link = base_url('/document/resignation/') . $letter;
            } else if (file_exists($path2)) {
                $link = base_url('../promo/') . $letter;
            } else {
                $link = base_url('../document/resignation/') . $letter;
            }

            if ($reason != 'Termination') {
                echo '<td class="text-center">';
                echo    '<a href="javascript:;" title="view Letter" onclick=view_letter(\'' . $link . '\')><i class="bx bx-image-alt me-0 font-22"></i></a>';
                echo '</td>';
            }
            echo    '<td class="text-center">';
            $onclick = 'onclick="generateClearance(\'' . $reason . '\',\'' . $emp_id . '\',\'' . $cId . '\')"';
            $print = '<a href="javascript:;" title="print Clearance" ' . $onclick . '><i class="bx bx-printer me-0 font-22"></i></a>';
            echo ($row['generated_clearance'] == '') ? $print : '<i class="bx bx-printer me-0 text-secondary font-22"></i>';
            echo    '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
    }

    public function clearanceUpdateForm()
    {
        $input      = $this->input->post(NULL, TRUE);
        $scpr_id    = $input['scpr_id'];
        $cond       = array('scpr_id' => $scpr_id);
        $row        = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond);
        $empId      = $row['emp_id'];
        $name       = $this->promo_model->getName_employee3($empId);
        $dateAdded  = date('Y-m-d', strtotime($row['date_added']));
        $status     = $row['status'];
        $reason     = $row['reason'];

        $dateEffect = '';
        $cond = array('scpr_id' => $scpr_id);
        $scpd = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
        if ($scpd) {
            $dateEffect = $scpd['date_effectivity'];
        }

        echo '<div class="row">';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Clearance ID</label>
                <input type="text" class="form-control" value="' . $scpr_id . '" disabled>
                <input type="hidden" name="scpr_id" value="' . $scpr_id . '">';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Date Added</label>
                <input type="text" class="form-control" value="' . $dateAdded . '" disabled>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Employee ID</label>
                <input type="text" class="form-control" value="' . $empId . '" disabled>
                <input type="hidden" name="emp_id" value="' . $empId . '">';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" value="' . ucwords(strtolower($name)) . '" disabled>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Clearance Status</label>
                <select class="form-select" disabled>';
        $cond = array('status !=' => '');
        $sql1 = $this->promo_model->selectDistinct('status', 'secure_clearance_promo', $cond);
        foreach ($sql1 as $row1) {
            $stat   = $row1['status'];
            $sel    = ($status == $stat) ? 'selected' : '';
            echo '<option value="' . $stat . '" ' . $sel . '>' . $stat . '</option>';
        }
        echo    '</select>';
        echo '</div>';
        echo '<div class="col-md-6 mb-3">
                <label class="form-label">Date Effectivity</label>
                <input type="text" class="form-control datepicker" value="' . $dateEffect . '" name="date_effectivity" placeholder="yyyy-mm-dd">';
        echo '</div>';
        if ($reason == 'V-Resigned' || $reason == 'Ad-Resigned') {
            $cond = array('scpr_id' => $scpr_id);
            $query = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
            foreach ($query as $s) {
                $id = $s['scdetails_id'];
                $store = $s['store'];
                echo '<div class="col-md-6 mb-3">';
                echo    '<label class="form-label">' . $store . '</label>';
                echo    '<input type="file" class="form-control" name="resignation_letter_' . $id . '" accept="image/*">';
                echo '</div>';
            }
        }
        echo '<div class="col-md-6 mb-3">';
        echo    '<label class="form-label">Reason</label>';
        echo    '<select class="form-select" name="reason" onchange="changeReason(this.value,\'' . $reason . '\',\'' . $scpr_id . '\')">';
        $cond = array('status !=' => '');
        $sql2 = $this->promo_model->selectDistinct('reason', 'secure_clearance_promo', $cond);
        foreach ($sql2 as $row2) {
            $res    = $row2['reason'];
            $sel    = ($reason == $res) ? 'selected' : '';
            echo '<option value="' . $res . '" ' . $sel . '>' . $res . '</option>';
        }
        echo    '</select>';
        echo '</div>';
        echo '</div>';
        echo '<div class="row additionalReq"></div>';
    }

    public function changeReason()
    {
        $input      = $this->input->post(NULL, TRUE);
        $reason     = $input['reason'];
        $curReason  = $input['curReason'];
        $scpr_id    = $input['scpr_id'];
        $cond       = array('scpr_id' => $scpr_id);
        $empId      = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond)['emp_id'];

        if (($reason == 'Ad-Resigned' || $reason == 'V-Resigned') && ($curReason != 'Ad-Resigned' && $curReason != 'V-Resigned')) {

            echo '<div class="col-md-12 mb-3">';
            echo '<i class="text-danger mb-2">Additional requirements for <b>' . $reason . '</b>:</i>';
            echo '</div>';

            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $row) {

                $field  = $row['bunit_field'];
                $store  = $row['bunit_name'];

                $cond   = array('scpr_id' => $scpr_id, 'emp_id' => $empId, 'store' => $store);
                $chk    = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                $cond   = array('emp_id' => $empId, $field => 'T');
                $chk1   = $this->promo_model->selectAll_tcR('promo_record', $cond);
                if ($chk && $chk1) {

                    echo '<div class="col-md-6 mb-3">';
                    echo    '<label class="form-label">Resignation Letter (' . $store . ')</label>';
                    echo    '<input type="file" class="form-control" name="' . $field . '" accept="image/*">';
                    echo '</div>';
                }
            }
        }

        if ($reason == 'Deceased') {

            echo '<div class="col-md-12 mb-3">';
            echo '<i class="text-danger mb-2">Additional requirements for <b>' . $reason . '</b>:</i>';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Date of Death</label>';
            echo '<input type="text" class="form-control datepicker" name="dateofdeath" placeholder="yyyy-mm-dd" autocomplete="off">';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Cause of Death</label>';
            echo '<input type="text" class="form-control" name="causeofdeath" autocomplete="off">';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Name of Claimant</label>';
            echo '<input type="text" class="form-control" name="claimant" autocomplete="off">';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Relationship to the Deceased</label>';
            echo '<select class="form-select" name="relation">';
            echo '<option value="">Select Relationship</option>';
            echo '<option value="Father">Father</option>';
            echo '<option value="Mother">Mother</option>';
            echo '<option value="Spouse">Spouse</option>';
            echo '<option value="Son">Son</option>';
            echo '<option value="Daughter">Daughter</option>';
            echo '<option value="Sister/Brother">Sister/Brother</option>';
            echo '<option value="Sibling/Relative">Sibling/Relative</option>';
            echo '</select>';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Death Certificate</label>';
            echo '<input type="file" class="form-control" name="death_certificate">';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Authorization Letter</label>';
            echo '<input type="file" class="form-control" name="authorization_letter">';
            echo '</div>';
        }
    }

    public function clearanceRequirement()
    {
        $input      = $this->input->post(NULL, TRUE);
        $reason     = $input['reason'];
        $eocdate    = $input['eocdate'];
        if ($reason == 'Ad-Resigned' || $reason == 'V-Resigned') {
            echo '<i class="text-danger mb-2">Additional requirements for <b>' . $reason . '</b>:</i>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Date of Resignation</label>';
            echo '<input type="text" class="form-control datepicker" value="' . $eocdate . '" name="date_effectivity" placeholder="yyyy-mm-dd">';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Resignation Letter</label>';
            echo '<input type="file" class="form-control" name="resignation_letter">';
            echo '</div>';
        } else if ($reason == 'Termination') {
            echo '<i class="text-danger mb-2">Additional requirements for <b>' . $reason . '</b>:</i>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">EOC Date</label>';
            echo '<input type="text" class="form-control datepicker" name="date_effectivity" value="' . $eocdate . '" placeholder="yyyy-mm-dd">';
            echo '</div>';
        } else if ($reason == 'Deceased') {
            echo '<i class="text-danger mb-2">Additional requirements for <b>' . $reason . '</b>:</i>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Date of Death</label>';
            echo '<input type="text" class="form-control datepicker" name="dateofdeath" placeholder="yyyy-mm-dd">';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Cause of Death</label>';
            echo '<input type="text" class="form-control" name="causeofdeath">';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Name of Claimant</label>';
            echo '<input type="text" class="form-control" name="claimant">';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Relationship to the Deceased</label>';
            echo '<select class="form-select" name="relation">';
            echo '<option value="">Select Relationship</option>';
            echo '<option value="Father">Father</option>';
            echo '<option value="Mother">Mother</option>';
            echo '<option value="Spouse">Spouse</option>';
            echo '<option value="Son">Son</option>';
            echo '<option value="Daughter">Daughter</option>';
            echo '<option value="Sister/Brother">Sister/Brother</option>';
            echo '<option value="Sibling/Relative">Sibling/Relative</option>';
            echo '</select>';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Death Certificate</label>';
            echo '<input type="file" class="form-control" name="death_certificate">';
            echo '</div>';
            echo '<div class="col-md-6 mb-3">';
            echo '<label class="form-label">Authorization Letter</label>';
            echo '<input type="file" class="form-control" name="authorization_letter">';
            echo '</div>';
        }
    }

    public function clearanceUpdate()
    {
        $input      = $this->input->post(NULL, TRUE);
        $scprId     = $input['scpr_id'];
        $empId      = $input['emp_id'];
        $reason     = $input['reason'];
        $dateEffect = $input['date_effectivity'];

        $cond   = ['scpr_id' => $scprId];
        $query  = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
        foreach ($query as $s) {
            $id     = $s['scdetails_id'];
            $str    = $s['store'];
            $de     = $s['date_effectivity'];

            if (isset($_FILES['resignation_letter_' . $id]['name']) && !empty($_FILES['resignation_letter_' . $id]['name'])) {

                $array          = explode(".", $_FILES['resignation_letter_' . $id]["name"]);
                $filename       = trim($empId) . '=' . date('Y-m-d') . '=' . 'Resignation-Letter' . '=' . date('H-i-s-A') . '.' . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/resignation/';
                $path           = $base_path . $relative_path . $filename;

                if (move_uploaded_file($_FILES['resignation_letter_' . $id]["tmp_name"], @$path)) {
                    $cond   = array('scpr_id' => $scprId, 'store' => $str);
                    $data   = array('resignation_letter' => $relative_path . $filename);
                    $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);

                    $cond   = array('emp_id' => $empId, 'date' => $de, 'remarks' => "Resignation letter for $str");
                    $data   = array('resignation_letter' => $relative_path . $filename, 'date' => $dateEffect, 'date_updated' => $this->today);
                    $update = $this->promo_model->update_twdA('termination', $cond, $data);
                }
            }
        }

        $cond   = ['scpr_id' => $scprId];
        $data   = ['date_effectivity' => $dateEffect];
        $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);

        if ($reason == 'V-Resigned' || $reason == 'Ad-Resigned') {

            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $row) {

                $rlName = $row['bunit_field'];
                $store  = $row['bunit_name'];
                if (isset($_FILES[$rlName]['name'])) {

                    $array          = explode(".", $_FILES[$rlName]["name"]);
                    $filename       = trim($empId) . '=' . date('Y-m-d') . '=' . 'Resignation-Letter' . '=' . date('H-i-s-A') . '.' . $array[1];
                    $base_path      = FCPATH;
                    $relative_path  = '../document/resignation/';
                    $path           = $base_path . $relative_path . $filename;

                    if (move_uploaded_file($_FILES[$rlName]["tmp_name"], @$path)) {
                        $cond   = array('scpr_id' => $scprId, 'store' => $store);
                        $data   = array('resignation_letter' => $relative_path . $filename, 'generated_clearance' => '');
                        $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);

                        $scpd  = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                        $dateEffect = $scpd['date_effectivity'];
                        $data = array(
                            'emp_id'        => $empId,
                            'date'          => $dateEffect,
                            'remarks'       => 'Resignation letter for ' . $store,
                            'added_by'      => $this->userId,
                            'date_updated'  => $this->today,
                            'resignation_letter' => $relative_path . $filename
                        );
                        $insert = $this->promo_model->insert_tdA('termination', $data);
                    }
                }
            }

            // if (!$update && !$insert) {
            //     echo json_encode(['message' => 'failed']);
            //     exit;
            // }

            $cond   = array('subordinates_rater' => $empId);
            $query  = $this->promo_model->selectAll_tcA('leveling_subordinates', $cond);
            foreach ($query as $row) {

                $data = array('ratee_id' => $row['subordinates_rater'], 'rater_id' => $row['ratee'], 'tag_stat' => 'Pending');
                $chk  = $this->promo_model->selectAll_tcR('tag_for_resignation', $data);
                if (!$chk) {

                    $data['added_by']   = $this->userId;
                    $data['date_added'] = $this->today;
                    $insert = $this->promo_model->insert_tdA('tag_for_resignation', $data);
                }
            }
        }

        if ($reason == 'Termination') {
            $cond   = array('scpr_id' => $scprId);
            $data   = array('generated_clearance' => '');
            $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);
        }

        $cond   = array('scpr_id' => $scprId);
        $query  = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond);
        if ($query['reason'] == 'V-Resigned' || $query['reason']  == 'Ad-Resigned') {
            if ($reason == 'Termination' || $reason == 'Deceased') {
                $cond = array('ratee_id' => $empId, 'tag_stat' => 'Pending');
                $delete = $this->promo_model->delete_tcr_row('tag_for_resignation', $cond);
            }
        }

        if ($reason == 'Deceased') {

            $path   = '';
            if (isset($_FILES['death_certificate']['name'])) {
                $array          = explode(".", $_FILES['death_certificate']['name']);
                $filename       = $empId . '=' . date('Y-m-d') . '=' . 'Death-Certificate' . '=' . date('H-i-s-A') . '.' . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/resignation/';
                $path           = $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES['death_certificate']['tmp_name'], $path)) {
                    $deathCert  = $relative_path . $filename;
                }
            }

            $path   = '';
            if (isset($_FILES['authorization_letter']['name'])) {
                $array          = explode(".", $_FILES['authorization_letter']['name']);
                $filename       = $empId . '=' . date('Y-m-d') . '=' . 'Authorization-Letter' . '=' . date('H-i-s-A') . '.' . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/authorizationletter/';
                $path           = $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES['authorization_letter']['tmp_name'], $path)) {
                    $authLeter  = $relative_path . $filename;
                }
            }

            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $bu) {

                $field  = $bu['bunit_field'];
                $store  = $bu['bunit_name'];
                $cond   = array($field => 'T', 'emp_id' => $empId);
                $chk    = $this->promo_model->selectAll_tcR('promo_record', $cond);
                if ($chk) {

                    $cond   = array('scpr_id' => $scprId, 'emp_id' => $empId, 'store' => $store);
                    $scpd   = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                    if ($scpd) {

                        $data = array('date_cleared' => $this->today, 'clearance_status' => 'Completed');
                        $cond = array('scdetails_id' => $scpd['scdetails_id']);
                        $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);
                    } else {

                        $data = array(
                            'scpr_id'           => $scprId,
                            'emp_id'            => $empId,
                            'record_no'         => $chk['record_no'],
                            'store'             => $store,
                            'date_secure'       => $this->today,
                            'date_effectivity'  => $this->today,
                            'date_cleared'      => $this->today,
                            'added_by'          => $this->userId,
                            'clearance_status'  => 'Completed'
                        );
                        if (isset($deathCert)) {
                            $data['resignation_letter'] = $deathCert;
                        }
                        $insert = $this->promo_model->insert_tdA('secure_clearance_promo_details', $data);
                    }
                }
            }

            $cod        = $input['causeofdeath'];
            $dod        = $input['dateofdeath'];
            $claimant   = $input['claimant'];
            $relation   = $input['relation'];

            $data = array(
                'sc_id'                     => $scprId,
                'emp_id'                    => $empId,
                'claimant'                  => $claimant,
                'relation'                  => $relation,
                'dateofdeath'               => $dod,
                'causeofdeath'              => $cod,
                'date_secured_clearance'    => $this->today
            );

            $data['death_certificate'] = '';
            if (isset($deathCert)) {
                $data['death_certificate'] = $deathCert;
            }
            $data['authorization_letter'] = '';
            if (isset($authLeter)) {
                $data['authorization_letter'] = $authLeter;
            }

            $insert = $this->promo_model->insert_tdA('secure_clearance_deceased', $data);
        }

        $cond = array('scpr_id' => $scprId);
        $data = array('reason' => $reason);
        if ($reason == 'Deceased') {
            $data['status'] = 'Completed';
        }
        $scp = $this->promo_model->update_twdA('secure_clearance_promo', $cond, $data);

        $cond   = array('emp_id' => $empId);
        $cStat  = $this->promo_model->selectAll_tcR('employee3', $cond)['current_status'];
        if ($reason == 'Termination') {
            $data = ($cStat != 'Active')
                ? ['current_status' => 'End of Contract']
                : ['sub_status' => 'For End of Contract'];
        } elseif ($reason == 'V-Resigned' || $reason == 'Ad-Resigned') {
            $data = ($cStat != 'Active')
                ? ['current_status' => $reason]
                : ['sub_status' => 'For Resignation'];
        } elseif ($reason == 'Deceased') {
            $data = ['current_status' => $reason, 'sub_status' => 'Cleared'];
        }

        $emp3 = $this->promo_model->update_twdA('employee3', $cond, $data);

        $cond = ['emp_id' => $empId];
        $sql = $this->promo_model->selectAll_tcR('employee3', $cond);
        $eocdate = $sql['eocdate'];
        if ($eocdate != '0000-00-00' && $eocdate != '') {
            if (strtotime($dateEffect) >= strtotime($eocdate)) {
                if ($reason == 'Ad-Resigned' || $reason == 'V-Resigned') {
                    $data = ['current_status' => 'Active', 'sub_status' => 'For Resignation'];
                } else if ($reason == 'Termination') {
                    $data = ['current_status' => 'Active', 'sub_status' => 'For End of Contract'];
                } else if ($reason == 'Deceased') {
                    $data = ['current_status' => 'Deceased', 'sub_status' => 'Cleared'];
                }
                $cond = ['emp_id' => $empId];
                $update = $this->promo_model->update_twdA('employee3', $cond, $data);
            }
        }

        $activity = 'Update Promo Clearance of (' . $empId . ') with scpr_id:' . $scprId;
        $activity .= ', reason:' . $query['reason'] . ' to ' . $reason;
        $data = array(
            'activity'  => $activity,
            'date'      => $this->today,
            'time'      => date('H:i:s'),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $logs = $this->promo_model->insert_tdA('logs', $data);

        if ($scp && $emp3 && $logs) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function searchPromoClearance()
    {
        echo '<ul class="list-group">';
        $input = $this->input->post(NULL, TRUE);
        $query = $this->promo_model->searchPromoClearance($input);
        if ($query) {
            foreach ($query as  $row) {
                $name       = ucwords(strtolower($row['name']));
                $emp_id     = $row['emp_id'];
                $record_no  = $row['record_no'];
                $onclick    = 'onclick="selectPromoClearance(\'' . $name . '\',\'' . $emp_id . '\',\'' . $record_no . '\')"';
                echo '<a href="javascript:;" class="list-group-item" ' . $onclick . '>' . $name . '</a>';
            }
        } else {
            echo '<a href="javascript:;" class="list-group-item">No Results Found!</a>';
        }
        echo '</ul>';
    }

    public function selectPromoClearance()
    {
        $input      = $this->input->post(NULL, TRUE);
        $empId      = $input['emp_id'];
        $recordNo   = $input['record_no'];
        $process    = $input['process'];

        $stores = [];
        $promo  = $this->promo_model->promoDetails_wttR(array('e.emp_id' => $empId), 'employee3', 'promo_record');

        $eocdate = ($promo['eocdate']) ? $promo['eocdate'] : date('Y-m-d');

        $bus    = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $chk = $this->promo_model->empStores('promo_record', $empId, $recordNo, $bu['bunit_field']);
            if ($chk) {
                $stores[] = $bu['bunit_name'];
            }
        }

        if ($process == 'secure') {

            $cond   = array('emp_id' => $empId, 'status' => 'Pending');
            $scp    = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond);
            if ($scp) {

                $data   = '<option value="">Select Store</option>';
                $cond   = array('scpr_id' => $scp['scpr_id']);
                $scpd   = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);

                foreach ($stores as $value) {
                    if (in_array($value, array_column($scpd, 'store'))) {
                        $data .= '<option value="" disabled>' . $value . ' - SECURED</option>';
                    } else {
                        $data .= '<option value="' . $value . '">' . $value . '</option>';
                    }
                }

                $year   = date('Y', strtotime($scp['date_added']));
                $month  = date('m', strtotime($scp['date_added']));
                $missing_values = array_diff($stores, array_column($scpd, 'store'));
                $secured = (empty($missing_values)) ? true : false;

                $dateEffect = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond)['date_effectivity'];

                echo json_encode([
                    'message'       => 'success',
                    'secured'       => true,
                    'reason'        => $scp['reason'],
                    'promo_type'    => $scp['promo_type'],
                    'stores'        => $data,
                    'chk'           => $secured,
                    'year'          => $year,
                    'month'         => $month,
                    'eocdate'       => $dateEffect,
                ]);
            } else {

                $data = '<option value="">Select Store</option>';
                foreach ($stores as $value) {
                    if ($promo['promo_type'] == 'STATION') {
                        $data .= '<option value="' . $value . '" selected>' . $value . '</option>';
                    } else {
                        $data .= '<option value="' . $value . '">' . $value . '</option>';
                    }
                }
                echo json_encode([
                    'message'       => 'success',
                    'secured'       => false,
                    'promo_type'    => $promo['promo_type'],
                    'stores'        => $data,
                    'eocdate'       => $eocdate,
                ]);
            }
        } else if ($input['process'] == 'reprint') {

            $cond   = array('emp_id' => $empId, 'status' => 'Pending');
            $query  = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond);

            $cond   = array('scpr_id' => $query['scpr_id']);
            $store  = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);

            $data   = '<option value="">Select Store</option>';
            foreach ($store as $value) {
                if ($value['clearance_status'] == 'Pending') {
                    $data .= '<option value="' . $value['scdetails_id'] . '">' . $value['store'] . '</option>';
                } else {
                    $data .= '<option value="' . $value['scdetails_id'] . '" disabled>' . $value['store'] . ' - Completed</option>';
                }
            }

            echo json_encode([
                'message'       => 'success',
                'reason'        => $query['reason'],
                'promo_type'    => $query['promo_type'],
                'stores'        => $data
            ]);
        } else {
            $cond = array('emp_id' => $empId, 'status' => 'Pending');
            $sql  = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond);
            if ($sql) {

                $data   = '<option value="">Select Store</option>';
                $cond   = array('scpr_id' => $sql['scpr_id']);
                $scpd   = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
                foreach ($scpd as $value) {

                    if ($value['clearance_status'] == 'Pending') {
                        $data .= '<option value="' . $value['store'] . '">' . $value['store'] . '</option>';
                    } else {
                        $data .= '<option value="" disabled>' . $value['store'] . ' - UPLOAD COMPLETED</option>';
                    }
                }

                foreach ($stores as $val) {
                    if (!in_array($val, array_column($scpd, 'store'))) {
                        $data .= '<option value="" disabled>' . $val . ' - NOT SECURED</option>';
                    }
                }

                $stat = ($sql['reason'] == 'Termination') ? 'End of Contract (Cleared)' : $sql['reason'] . ' (Cleared)';
                echo json_encode([
                    'message'       => 'success',
                    'promo_type'    => $sql['promo_type'],
                    'stores'        => $data,
                    'stat'          => $stat,
                    'scpr_id'       => $sql['scpr_id'],
                ]);
            }
        }
    }

    public function reprintClearance()
    {
        $input          = $this->input->post(NULL, TRUE);
        $emp_id         = $input['emp_id'];
        $reason         = $input['reason'];
        $scdetails_id   = $input['stores'];

        $data = array(
            'sc_id'       => $input['stores'],
            'reason'      => $input['reasonReprint'],
            'date'        => date('Y-m-d H:i:s'),
            'generatedby' => $this->userId,
        );
        $insert = $this->promo_model->insert_tdA('secure_clearance_reprint', $data);
        if ($insert) {
            $activity   = "Reprinted Promo $reason Clearance of ($emp_id) with scdetails_id : $scdetails_id";
            $data       = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $logs = $this->promo_model->insert_tdA('logs', $data);
            echo json_encode([
                'message'       => 'print',
                'reason'        => $reason,
                'emp_id'        => $emp_id,
                'scdetails_id'  => $scdetails_id
            ]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function secureClearance()
    {
        $input      = $this->input->post(NULL, TRUE);
        $reason     = $input['reason'];
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        $promo_type = $input['promo_type'];

        $cond       = array('emp_id' => $emp_id, 'status' => 'Pending');
        $scpr_id    = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond)['scpr_id'];

        if ($reason == 'Ad-Resigned' || $reason == 'V-Resigned' || $reason == 'Termination') {

            $path   = '';
            if (isset($_FILES['resignation_letter']['name'])) {
                // $base_path = $_SERVER['DOCUMENT_ROOT'];
                $array          = explode(".", $_FILES['resignation_letter']['name']);
                $filename       = $emp_id . '=' . date('Y-m-d') . '=' . 'Resignation-Letter' . '=' . date('H-i-s-A') . '.' . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/resignation/';
                $path           = $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES['resignation_letter']['tmp_name'], $path)) {
                    $rLetter = $relative_path . $filename;
                }
            }
            $stores     = $input['stores'];
            $dateEffect = $input['date_effectivity'];
            if ($this->today <= $dateEffect) {
                $current_status         = 'Active';
                $substatus              = ($reason == 'Termination') ? 'For End of Contract' : 'For Resignation';
                $dateforactiveresign    = $this->today;
                $dateuncleared          = '';
            } else {
                $current_status         = ($reason == 'Termination') ? 'End of Contract' : $reason;
                $substatus              = 'Uncleared';
                $dateforactiveresign    = '';
                $dateuncleared          = $this->today;
            }

            $data   = array('current_status' => $current_status, 'sub_status' => $substatus);
            $update = $this->promo_model->update_twdA('employee3', array('emp_id' => $emp_id), $data);

            if (!$scpr_id) {
                $data = array(
                    'emp_id'      => $emp_id,
                    'promo_type'  => $promo_type,
                    'reason'      => $reason,
                    'date_added'  => date('Y-m-d H:i:s'),
                    'added_by'    => $this->userId,
                    'status'      => 'Pending'
                );
                $scpr_id = $this->promo_model->insert_tdA('secure_clearance_promo', $data);

                $cond = array('ratee_id' => $emp_id, 'tag_stat' => 'Pending');
                $this->promo_model->delete_tcr_row('tag_for_resignation', $cond);
            }

            if ($reason != 'Termination') {
                $cond = array('subordinates_rater' => $emp_id);
                $supervisor = $this->promo_model->selectAll_tcA('leveling_subordinates', $cond);
                foreach ($supervisor as $row) {

                    $cond   = array('ratee_id' => $emp_id, 'rater_id' => $row['ratee'], 'tag_stat' => 'Pending');
                    $chk    = $this->promo_model->selectAll_tcR('tag_for_resignation', $cond);
                    if (!$chk) {
                        $data = array(
                            'ratee_id'   => $emp_id,
                            'rater_id'   => $row['ratee'],
                            'added_by'   => $this->userId,
                            'date_added' => $this->today,
                            'tag_stat'   => 'Pending',
                        );
                        $this->promo_model->insert_tdA('tag_for_resignation', $data);
                    }
                }

                $data = array(
                    'emp_id'        => $emp_id,
                    'date'          => $dateEffect,
                    'remarks'       => 'Resignation letter for ' . $stores,
                    'added_by'      => $this->userId,
                    'date_updated'  => $this->today,
                );

                if (isset($rLetter)) {
                    $data['resignation_letter'] = $rLetter;
                }

                $insert = $this->promo_model->insert_tdA('termination', $data);
            }

            $data = array(
                'scpr_id'                => $scpr_id,
                'record_no'              => $record_no,
                'emp_id'                 => $emp_id,
                'store'                  => $stores,
                'date_activefor_resign'  => $dateforactiveresign,
                'date_secure'            => $this->today,
                'date_effectivity'       => $dateEffect,
                'date_uncleared'         => $dateuncleared,
                'added_by'               => $this->userId,
                'clearance_status'       => 'Pending'
            );

            if (isset($rLetter)) {
                $data['resignation_letter'] = $rLetter;
            }

            $scdetails_id = $this->promo_model->insert_tdA('secure_clearance_promo_details', $data);
        } else if ($reason == 'Deceased') {

            $path   = '';
            if (isset($_FILES['death_certificate']['name'])) {
                $array          = explode(".", $_FILES['death_certificate']['name']);
                $filename       = $emp_id . '=' . date('Y-m-d') . '=' . 'Death-Certificate' . '=' . date('H-i-s-A') . '.' . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/resignation/';
                $path           = $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES['death_certificate']['tmp_name'], $path)) {
                    $deathCert  = $relative_path . $filename;
                }
            }

            $path   = '';
            if (isset($_FILES['authorization_letter']['name'])) {
                $array          = explode(".", $_FILES['authorization_letter']['name']);
                $filename       = $emp_id . '=' . date('Y-m-d') . '=' . 'Authorization-Letter' . '=' . date('H-i-s-A') . '.' . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/authorizationletter/';
                $path           = $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES['authorization_letter']['tmp_name'], $path)) {
                    $authLeter  = $relative_path . $filename;
                }
            }

            $dateofdeath            = $input['dateofdeath'];
            $causeofdeath           = $input['causeofdeath'];
            $claimant               = $input['claimant'];
            $relation               = $input['relation'];
            $current_status         = $reason;
            $substatus              = 'Cleared';
            $dateforactiveresign    = $dateofdeath;
            $dateuncleared          = '';

            $data   = array('current_status' => $current_status, 'sub_status' => $substatus);
            $update = $this->promo_model->update_twdA('employee3', array('emp_id' => $emp_id), $data);

            $stores = [];
            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $bu) {
                $chk = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
                if ($chk) {
                    $stores[] = $bu['bunit_name'];
                }
            }

            if (!$scpr_id) {
                $data = array(
                    'emp_id'      => $emp_id,
                    'promo_type'  => $promo_type,
                    'reason'      => $reason,
                    'date_added'  => date('Y-m-d H:i:s'),
                    'added_by'    => $this->userId,
                    'status'      => 'Completed'
                );
                $scpr_id = $this->promo_model->insert_tdA('secure_clearance_promo', $data);

                $data = array(
                    'scpr_id'                => $scpr_id,
                    'record_no'              => $record_no,
                    'emp_id'                 => $emp_id,
                    'date_activefor_resign'  => $dateforactiveresign,
                    'date_secure'            => $this->today,
                    'date_effectivity'       => $dateofdeath,
                    'date_uncleared'         => $dateuncleared,
                    'added_by'               => $this->userId,
                    'clearance_status'       => 'Completed'
                );

                if (isset($deathCert)) {
                    $data['resignation_letter'] = $deathCert;
                }

                foreach ($stores as $str) {
                    $data['store']  = $str;
                    $scdetails_id   = $this->promo_model->insert_tdA('secure_clearance_promo_details', $data);
                }
            } else {

                $cond   = array('scpr_id' => $scpr_id);
                $scpd   = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);

                foreach ($stores as $str) {
                    if (in_array($str, array_column($scpd, 'store'))) {

                        $cond   = array('scdetails_id' => $scpd['scdetails_id']);
                        $data   = array('date_cleared' => $this->today, 'clearance_status'  => 'Completed');
                        $scdetails_id = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);
                    } else {
                        $data = array(
                            'scpr_id'                => $scpr_id,
                            'record_no'              => $record_no,
                            'emp_id'                 => $emp_id,
                            'date_activefor_resign'  => $dateforactiveresign,
                            'date_secure'            => $this->today,
                            'date_effectivity'       => $dateofdeath,
                            'date_uncleared'         => $dateuncleared,
                            'date_cleared'           => $this->today,
                            'added_by'               => $this->userId,
                            'clearance_status'       => 'Completed'
                        );
                        $data['store']  = $str;
                        $scdetails_id   = $this->promo_model->insert_tdA('secure_clearance_promo_details', $data);
                    }
                }
            }

            $cond   = array('ratee_id' => $emp_id, 'tag_stat' => 'Pending');
            $delete = $this->promo_model->delete_tcr_row('tag_for_resignation', $cond);

            $data = array(
                'sc_id'                  => $scpr_id,
                'emp_id'                 => $emp_id,
                'claimant'               => $claimant,
                'relation'               => $relation,
                'dateofdeath'            => $dateofdeath,
                'causeofdeath'           => $causeofdeath,
                'date_secured_clearance' => $this->today,
            );
            $data['death_certificate'] = '';
            if (isset($deathCert)) {
                $data['death_certificate'] = $deathCert;
            }
            $data['authorization_letter'] = '';
            if (isset($authLeter)) {
                $data['authorization_letter'] = $authLeter;
            }

            $insert = $this->promo_model->insert_tdA('secure_clearance_deceased', $data);
        }

        if ($update && $scpr_id && $scdetails_id) {
            $activity = "Secured Promo $reason Clearance of ($emp_id) for $stores with scdetails_id: $scdetails_id";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $logs = $this->promo_model->insert_tdA('logs', $data);

            echo json_encode([
                'message'       => 'success',
                'reason'        => $reason,
                'emp_id'        => $emp_id,
                'scdetails_id'  => $scdetails_id,
            ]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function browseEpas()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        $stores     = $input['stores'];

        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no, 'store' => $stores);
        $epas   = $this->promo_model->selectAll_tcR('appraisal_details', $cond);

        $cond   = array('e.emp_id' => $emp_id, 'e.record_no' => $record_no, 'type' => 'Seasonal');
        $emp    = $this->promo_model->promoDetails_wttR($cond, 'employee3', 'promo_record');

        function check_epas($epas, $chk)
        {
            if ($epas) {
                if ($epas['raterSO'] == '1' && $epas['rateeSO'] == '1') {
                    return json_encode(['message' => 'success', 'numrate' => $epas['numrate'], 'descrate' => $epas['descrate']]);
                } else if ($epas['raterSO'] == '1' && $epas['rateeSO'] == '0') {
                    return json_encode(['message' => 'failed', 'alert' => 'signoff', 'text' => 'Please signoff EPAS (employee\'s comment)!']);
                } else if ($epas['raterSO'] == '0' && $epas['rateeSO'] == '1') {
                    return json_encode(['message' => 'failed', 'alert' => 'signoff', 'text' => 'Please signoff EPAS (rater\'s comment)!']);
                } else {
                    return json_encode(['message' => 'failed', 'alert' => 'signoff', 'text' => 'Please signoff EPAS!']);
                }
            } else {
                if ($chk == 'seasonal') {
                    $text = 'Contract is more than 15 days. Please secure EPAS!';
                } else {
                    $text = 'Please secure EPAS!';
                }
                return json_encode(['message' => 'failed', 'alert' => 'noepas', 'text' => $text]);
            }
        }

        if ($emp) {
            $sdate  = date_create($emp['startdate']);
            $edate  = date_create($emp['eocdate']);
            $diff   = date_diff($sdate, $edate);
            $days   = $diff->format("%a") + 1;
            if ($days >= 0 && $days <= 15) {
                if ($epas) {
                    echo check_epas($epas, 'seasonal'); //if naay grado need ma signoff
                } else {
                    echo json_encode(['message' => 'seasonal', 'text' => 'Seasonal', 'days' => $days]);
                }
            } else {
                echo check_epas($epas, 'seasonal'); //15 days up need ug grado
            }
        } else {
            echo check_epas($epas, 'notSeasonal');
        }
    }

    public function uploadClearance()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        $stores     = $input['stores'];
        $scpr_id    = $input['scpr_id'];

        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no, 'store' => $stores);
        $epas   = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
        if ($epas) {
            if ($epas['raterSO'] != '1' || $epas['rateeSO'] != '1') {
                echo json_encode(['message' => 'failed']);
                exit;
            }
        }

        $path = '';
        if (isset($_FILES['clearance']['name'])) {
            $array          = explode(".", $_FILES['clearance']['name']);
            $filename       = $emp_id . '=' . date('Y-m-d') . '=' . 'Clearance' . '=' . date('H-i-s-A') . '.' . end($array);
            $base_path      = FCPATH;
            $relative_path  = '../document/clearance/';
            $path           = $base_path . $relative_path . $filename;
            if (move_uploaded_file($_FILES['clearance']['tmp_name'], $path)) {
                $clearance =  $relative_path . $filename;
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
            $cond   = array('bunit_name' => $stores);
            $field  = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond)['bunit_clearance'];
            $data   = array($field => $clearance);
            $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
            $update = $this->promo_model->update_twdA('promo_record', $cond, $data);
            if (!$update) {
                $update = $this->promo_model->update_twdA('promo_history_record', $cond, $data);
            }
        }

        $curStores  = [];
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $chk = $this->promo_model->empStores('promo_record', $emp_id, $record_no, $bu['bunit_field']);
            if ($chk) {
                $curStores[] = $bu['bunit_name'];
            }
        }

        $cond       = array('scpr_id' => $scpr_id, 'clearance_status' => 'Completed');
        $clrStores  = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);

        $cond   = array('scpr_id' => $scpr_id);
        $reason = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond)['reason'];

        $chk = array_diff($curStores, array_column($clrStores, 'store'));
        if (!$chk) {
            $data   = array('status' => 'Completed');
            $cond   = array('scpr_id' => $scpr_id);
            $update = $this->promo_model->update_twdA('secure_clearance_promo', $cond, $data);

            $cond = array('ratee_id' => $emp_id, 'tag_stat' => 'Pending');
            $this->promo_model->delete_tcr_row('tag_for_resignation', $cond);

            $cs     = ($reason == 'Termination') ? 'End of Contract' : $reason;
            $data   = array('current_status' => $cs, 'sub_status' => 'Cleared');
            $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
            $update = $this->promo_model->update_twdA('employee3', $cond, $data);
            if (!$update) {
                $cond   = array('emp_id' => $emp_id);
                $update = $this->promo_model->update_twdA('employee3', $cond, $data);
            }

            $data   = array('user_status' => 'inactive');
            $cond   = array('emp_id' => $emp_id);
            $update = $this->promo_model->update_twdA('users', $cond, $data);
        }

        if (isset($clearance) && $update) {
            $activity   = "Uploaded Promo $reason Clearance of ($emp_id) for $stores with scpr_id: $scpr_id";
            $data       = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $logs = $this->promo_model->insert_tdA('logs', $data);

            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }
}
