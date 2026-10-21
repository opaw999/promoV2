<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Promo extends CI_Controller
{
    public $userId;
    public $hrCode;
    public $today;
    public $adminUser;
    public $zip;
    function __construct()
    {
        parent::__construct();
        $this->userId   = $_SESSION['emp_id'];
        $this->hrCode   = $_SESSION['hrCode'];
        $this->today    = date('Y-m-d');
        $this->adminUser    = array('01186-2023');
        $this->load->library('zip');
        if (!isset($this->userId)) {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }
    }

    public function masterfile()
    {
        $input  = $this->input->post(NULL, TRUE);
        $data   = [];
        $bus    = $this->promo_model->locate_promo_bu($this->hrCode);
        $input  = array_merge($input, ['lpbu' => $bus]);
        $order  = array('e.emp_id', 'name', 'promo_company', 'store', 'promo_department', 'position', 'emp_type');
        $list   = $this->promo_model->make_datatables('masterfile', $order, $order[1], 'ASC', $input);
        foreach ($list as $row) {
            $i = 0;
            $stores = '';
            $empId = $row['emp_id'];
            $recordNo = $row['record_no'];
            foreach ($bus as $bu) {
                $field      = $bu['bunit_field'];
                $buName     = $bu['bunit_name'];
                $acronym    = $bu['bunit_acronym'];
                $hasBu = $this->promo_model->empStores('promo_record', $empId, $recordNo, $field);
                if ($hasBu) {
                    $i++;
                    $str    = '<span class="badge bg-secondary spanTooltip" title="' . $buName . '">' . $acronym . '</span>';
                    $stores = ($i == 1) ? $str  : $stores . ' ' . $str;
                }
            }

            $name       = ucwords(strtolower($row['name']));
            $link       = '<a href="' . base_url('promo/page/promo/profile/' . $empId) . '" target="_blank">' . $name . '</a>';
            $status     = '<span class="badge bg-success ">' . $row['current_status'] . '</span>';
            $column     = [];
            $column[]   = $link;
            $column[]   = $row['promo_company'];
            $column[]   = $stores;
            $column[]   = $row['promo_department'];
            $column[]   = $row['position'];
            $column[]   = $row['emp_type'];
            $column[]   = $status;
            $data[]     = $column;
        }

        $output = array(
            'draw'              => intval($_POST['draw']),
            'recordsTotal'      => $this->promo_model->get_all_data('masterfile'),
            'recordsFiltered'   => $this->promo_model->get_filtered_data('masterfile', $order, $order[1], 'ASC', $input),
            'data'              => $data
        );
        echo json_encode($output);
    }

    public function searchApplicant()
    {
        $input = $this->input->post(NULL, TRUE);
        echo '<table class="table table-hover" id="applicantsTable">
                <thead style="display: none;">
                    <tr><th></th></tr>
                </thead>
                <tbody>';
        $cond = array('lastname LIKE' => '%' . $input['lastname'] . '%', 'firstname LIKE' => '%' . $input['firstname'] . '%');
        $list = $this->promo_model->selectAll_tcA('applicant', $cond);
        if ($list) {

            $i = 0;
            foreach ($list as  $row) {

                $i++;
                $appId = $row['app_id'];
                $photo = base_url('assets/promo_assets/images/profile.png');
                if ($row['photo'] != '') {
                    $url   = FCPATH . $row['photo'];
                    $check = @file_get_contents($url);
                    if ($check !== false) {
                        $photo = base_url($row['photo']);
                    }
                }

                $fullname = '';
                if (!empty($row['suffix'])) {
                    $fullname = $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['suffix'] . ' ' . $row['middlename'];
                } else {
                    $fullname = $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'];
                }

                $bday = date('F d, Y', strtotime($row['birthdate']));
                $name = ucwords(strtolower($fullname));
                $link = '<a href="' . base_url('promo/page/promo/profile/' . $appId) . '" target="_blank">' . $name . '</a>';
                echo '<tr>';
                echo '<td>';
                echo '<div class="row">';
                echo '<div class="col-md-1">';
                echo '<img class="border rounded" src="' . $photo . '" alt="" style="width: 100%; height: 80%;">';
                echo '</div>';
                echo '<div class="col-md-11">';
                echo '<p>';
                echo '<span class="text-danger">[' . $i . ']</span> ';
                echo '<strong>' . $appId . ' ' . $link . '</strong>&nbsp;<br>';
                echo '<strong>Gender: </strong><i>' . $row['gender'] . '</i> &nbsp;';
                echo '<strong>Civil Status: </strong><i>' . $row['civilstatus'] . '</i> &nbsp;';
                echo '<strong>Birthdate: </strong><i>' . $bday . '</i> &nbsp;<br>';
                echo '<strong>Home Address: </strong><i>' . $row['home_address'] . '</i>';
                echo '</p>';
                echo '</div>';
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody>';
        echo '</table>';
    }

    public function searchTagtoRecruitment()
    {
        echo '<ul class="list-group">';
        $input  = $this->input->post(NULL, TRUE);
        $str    = $input['str'];
        $select = 'app_id, t1.firstname, t1.lastname, t1.middlename, t1.suffix, t1.status as status1, t2.status as status2, t2.position, t2.app_code';
        $table1 = 'applicant';
        $table2 = 'applicants';
        $join   = 't1.appcode=t2.app_code';
        $left   = 'left';
        $cond   = "app_id != '' AND (t1.firstname like '%$str%' OR t1.lastname like '%$str%' OR app_id = '$str')";
        $order  = 't1.lastname|ASC';
        $limit  = '10';
        $query  = $this->promo_model->join_ttjcoA($select, $table1, $table2, $join, $left, $cond, $order, $limit);
        if ($query) {
            foreach ($query as $row) {

                $emp_id = $row['app_id'];
                $status = '';
                if (!empty($row['status1'])) {
                    $status = $row['status1'];
                } else if (!empty($row['status2'])) {
                    $status = $row['status2'];
                } else {
                    $cond   = array('emp_id' => $emp_id);
                    $status = $this->promo_model->selectAll_tcR('employee3', $cond)['current_status'];
                }

                $cond   = array('emp_id' => $emp_id);
                $cStat = $this->promo_model->selectAll_tcR('employee3', $cond)['current_status'];

                $name = '';
                if (!empty($row['suffix'])) {
                    $name = $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['suffix'] . ', ' . $row['middlename'];
                } else {
                    $name = $row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename'];
                }

                $clk = 'onclick="getPromoDetails(\'' . $name . '\',\'' . $emp_id . '\', \'' . $status . '\', \'' . $cStat . '\')"';
                if (!empty($emp_id)) {
                    echo '<a href="javascript:;" class="list-group-item" ' . $clk . '>' . $name . '</a>';
                }
            }
        } else {
            echo '<a href="javascript:;" class="list-group-item">No Results Found!</a>';
        }
        echo '</ul>';
    }

    public function tagToRecruitment()
    {
        $input      = $this->input->post(NULL, TRUE);
        $status     = $input['current_status'];
        $recProcess = $input['recProcess'];
        $position   = $input['position'];
        $emp_id     = $input['emp_id'];
        $message    = 'error ' . $recProcess . ' | ' . $status;
        $tagged     = false;
        if ($status == 'blacklisted') {
            $message = 'Cannot process request! Applicant/Employee is ' . $status . '.';
        } else if ($status == 'Active') {
            $message = 'Cannot process request! Applicant/Employee is ' . $status . '.';
        } else {

            $select = '*';
            $table1 = 'applicant';
            $table2 = 'applicants';
            $join   = 't1.appcode=t2.app_code';
            $cond   = array('app_id' => $emp_id);
            $query  = $this->promo_model->join_ttjcoR($select, $table1, $table2, $join, null, $cond);

            if ($recProcess == 'exam') {
                $set            = array('status' => 'initialreq completed', 'position' => $position);
                $app_details    = 'Examination';
                $value          = 'initialreq completed';
                $message        = 'Applicant is now in examination process.';
            } else if ($recProcess == 'interview') {
                $set            = array('status' => 'for interview', 'position' => $position);
                $app_details    = 'Interview';
                $value          = 'for interview';
                $message        = 'Applicant is now in interview process.';
            } else if ($recProcess == 'training') {
                $set            = array('status' => 'for training', 'position' => $position);
                $app_details    = 'Training';
                $value          = 'for training';
                $message        = 'Applicant is now in training process.';
            } else if ($recProcess == 'final_completion') {
                $set            = array('status' => 'for final completion', 'position' => $position);
                $app_details    = 'Final Completion';
                $value          = 'for final completion';
                $message        = 'Applicant is now in final completion process.';
            } else if ($recProcess == 'orientation') {
                $set            = array('status' => 'for orientation', 'position' => $position);
                $app_details    = 'Orientation';
                $value          = 'for orientation';
                $message        = 'Applicant is now in orientation process.';
            } else if ($recProcess == 'hiring') {
                $set            = array('status' => 'for hiring', 'position' => $position);
                $app_details    = 'Hiring';
                $value          = 'for hiring';
                $message        = 'Applicant is now in hiring process.';
            } else if ($recProcess == 'initial_completion') {
                $set            = array('status' => 'tagged', 'position' => $position);
                $app_details    = 'Initial Completion';
                $value          = 'tagged';
                $message        = 'Applicant may process his/her initial requirements.';
            }

            if ($query) {
                $cond   = array('app_code' => $query['appcode']);
                $update = $this->promo_model->update_twdA('applicants', $cond, $set);
            } else {
                $cond   = array('app_id' => $emp_id);
                $info   = $this->promo_model->selectAll_tcR('applicant', $cond);

                $data = array(
                    'lastname'      => $info['lastname'],
                    'firstname'     => $info['firstname'],
                    'middlename'    => $info['middlename'],
                    'position'      => $position,
                    'status'        => $value,
                    'date_time'     => $this->today,
                    'suffix'        => $info['suffix'],
                    'entry_by'      => $this->userId,
                    'hr_location'   => $this->hrCode,
                );

                $id     = $this->promo_model->insert_tdA('applicants', $data);

                $data   = array('appcode' => $id);
                $cond   = array('app_id' => $emp_id);
                $update = $this->promo_model->update_twdA('applicant', $cond, $data);
            }

            $cond   = array('app_id' => $emp_id);
            $chk    = $this->promo_model->selectAll_tcR('application_details', $cond);

            if ($chk) {
                $data = array(
                    'application_status'    => $app_details,
                    'position_applied'      => $position,
                    'updatedby'             => $this->userId,
                    'date_updated'          => $this->today,
                );
                $tag = $this->promo_model->update_twdA('application_details', $cond, $data);
            } else {
                $data = array(
                    'app_id'                => $emp_id,
                    'date_applied'          => $this->today,
                    'application_status'    => $app_details,
                    'position_applied'      => $position,
                    'updatedby'             => $this->userId,
                    'date_updated'          => $this->today,
                );
                $tag = $this->promo_model->insert_tdA('application_details', $data);
            }

            if ($tag) {

                $data = array(
                    'detail'    => 'tag ' . $emp_id . ' to ' . $recProcess,
                    'user'      => $this->userId,
                    'date'      => $this->today,
                );

                $logs   = $this->promo_model->insert_tdA('applicant_tagging_logs', $data);
                $tagged = true;
            }
        }

        if ($tagged) {
            echo json_encode(['message' => 'success', 'tagged' => $tagged, 'report' => $message]);
        } else {
            echo json_encode(['message' => 'failed', 'report' => $message]);
        }
    }

    public function getSeverData()
    {
        $input = $this->input->post(NULL, TRUE);
        $i = 0;
        $field = $input['server'];
        $location = ['CORPORATE', $field];
        foreach ($location as $loc) {

            $i++;
            $corp1 = $this->promo_model->getSeverData(1, $loc, $field);
            $corp2 = $this->promo_model->getSeverData(2, $loc, $field);
            $corp3 = $this->promo_model->getSeverData(3, $loc, $field);

            $clk1 = 'id="serverdata" onclick="updateSeverData(\'data1\',\'' . $field . '\')"';
            $clk2 = 'id="serverdata" onclick="updateSeverData(\'data2\',\'' . $field . '\')"';
            $clk3 = 'id="serverdata" onclick="updateSeverData(\'data3\',\'' . $field . '\')"';

            if ($loc == 'CORPORATE') {
                $name = $loc;
            } else {
                $name = ($field == 'al_tal') ? 'TALIBON' : 'TUBIGON';
            }

            echo '<div class="col-md-6">';
            echo '<h4>' . $name . ' Server</h4>';
            echo '<table class="table table-sm table-bordered">
                    <tr>
                        <th>Database Table</th>
                        <th class="text-center" width="25%">No. of Records</th>';
            if ($i == 1) {
                echo '<th class="text-center" width="10%">Action</th>';
            }
            echo    '</tr>';
            echo    '<tr>
                        <td>employee3/promo_record</td>
                        <td class="text-center">
                            <span class="btn btn-sm btn-primary d-block">' . count($corp1) . '</span>
                        </td>';
            if ($i == 1) {
                echo '<td><button class="btn btn-sm btn-outline-success" ' . $clk1 . '>Update</button></td>';
            }
            echo    '</tr>';
            echo    '<tr>
                        <td>employmentrecord_/promo_history_record</td>
                        <td class="text-center">
                            <span class="btn btn-sm btn-primary d-block">' . count($corp2) . '</span>
                        </td>';
            if ($i == 1) {
                echo    '<td><button class="btn btn-sm btn-outline-success" ' . $clk2 . '>Update</button></td>';
            }
            echo    '</tr>';
            echo    '<tr>
                        <td>promo_sched_emp</td>
                        <td class="text-center">
                            <span class="btn btn-sm btn-primary d-block">' . count($corp3) . '</span>
                        </td>';
            if ($i == 1) {
                echo '<td><button class="btn btn-sm btn-outline-success" ' . $clk3 . '>Update</button></td>';
            }
            echo    '</tr>';
            echo '</table>';
            echo '</div>';
        }
        if (in_array($this->userId, $this->adminUser)) {
            $h1 = $this->promo_model->getSeverData(1, 'CORPORATE', $field);
            $h2 = $this->promo_model->getSeverData(1, $field, $field);
            echo '<div class="col-md-6">';
            $hist1 = array_column($h1, 'emp_id');
            $hist2 = array_column($h2, 'emp_id');
            foreach ($hist2 as $row) {
                if (!in_array($row, $hist1)) {
                    // echo $row . ' ';
                }
            }
            echo '</div>';
        }
    }

    public function updateSeverData()
    {
        $input  = $this->input->post(NULL, TRUE);
        $data   = $input['data'];
        $field  = $input['field'];
        $server = ['CORPORATE'];
        foreach ($server as $loc) {
            if ($data == 'data1') {
                $query = $this->promo_model->getSeverData(1, $loc, $field);
            } else if ($data == 'data2') {
                $query = $this->promo_model->getSeverData(2, $loc, $field);
            } else {
                $query = $this->promo_model->getSeverData(3, $loc, $field);
            }
        }
        $update = $this->promo_model->updateSeverData($query, $field, $data);
        if ($update) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function searchPromoExport()
    {
        echo '<ul class="list-group">';
        $input = $this->input->post(NULL, TRUE);
        $query = $this->promo_model->searchPromoExport($input);
        if ($query) {
            foreach ($query as  $row) {
                $name       = ucwords(strtolower($row['name']));
                $emp_id     = $row['emp_id'];
                $onclick    = 'onclick="selectPromoExport(\'' . $name . '\',\'' . $emp_id . '\')"';
                echo '<a href="javascript:;" class="list-group-item" ' . $onclick . '>' . $name . '</a>';
            }
        } else {
            echo '<a href="javascript:;" class="list-group-item">No Results Found!</a>';
        }
        echo '</ul>';
    }

    public function selectPromoExport()
    {
        $input = $this->input->post(NULL, TRUE);
        echo '<table class="table table-sm table-hover" style="width: 100%;">
                <tr>
                    <th>No.</th>
                    <th>record_no</th>
                    <th>Startdate</th>
                    <th>EOCdate</th>
                    <th>Company</th>
                    <th>Store(s)</th>
                    <th class="text-center">Action</th>
                </tr>';
        $emp_id     = $input['emp_id'];
        $bus        = $this->promo_model->locate_promo_bu($this->hrCode);
        $cond       = array('e.emp_id' => $emp_id);
        $current    = $this->promo_model->promoDetails_wttA($cond, 'employee3', 'promo_record');
        $previous   = $this->promo_model->promoDetails_wttA($cond, 'employmentrecord_', 'promo_history_record');
        if ($current || $previous) {
            $contract = ['current' => $current, 'previous' => $previous];
            foreach ($contract as $type => $data) {

                $i = 0;
                $table = ($type == 'current') ? 'promo_record' : 'promo_history_record';
                foreach ($data as $value) {

                    $i++;
                    $c = 0;
                    $stores = '';
                    $record_no = $value['record_no'];
                    $company = $value['promo_company'];
                    foreach ($bus as $bu) {
                        $field  = $bu['bunit_field'];
                        $buName = $bu['bunit_name'];
                        $buAcro = $bu['bunit_acronym'];
                        $hasBu  = $this->promo_model->empStores($table, $emp_id, $record_no, $field);
                        if ($hasBu) {
                            $c++;
                            $store = '<span class="badge bg-secondary spanTooltip" title="' . $buName . '">' . $buAcro . ' </span>';
                            $stores = ($c == 1) ? $store : $stores . ' ' . $store;
                        }
                    }

                    $clk    = 'onclick="exportPromoData(\'' . $emp_id . '\',\'' . $record_no . '\', \'' . $type . '\')"';
                    $action = '<a href="javascript:;" ' . $clk . '><span class="badge bg-success">Export</span></a>';

                    if ($type == 'current') {
                        $no = '<span class="badge bg-success">C</span>';
                    } else {
                        $no = '<span class="badge bg-danger">' . $i . '</span>';
                    }

                    echo '<tr>
                            <td>' . $no . '</td>
                            <td>' . $record_no . '</td>
                            <td>' . date('M. d, Y', strtotime($value['startdate'])) . '</td>
                            <td>' . date('M. d, Y', strtotime($value['eocdate'])) . '</td>
                            <td class="text-truncate pComp" style="max-width: 200px;" title="' . $company . '">' . $company . '</td>
                            <td>' . $stores . '</td>
                            <td class="text-center">' . $action . '</td>';
                    echo '</tr>';
                }
            }
        }
        echo '</table>';
    }

    public function exportPromoData()
    {
        $input      = $this->input->get(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        $contract   = $input['contract'];
        $table1     = ($contract == 'current') ? 'employee3' : 'employmentrecord_';
        $table2     = ($contract == 'current') ? 'promo_record' : 'promo_history_record';
        $filename   = date('ymdHis') . '.zip';
        $path       = FCPATH . '/document/portalExport/' . $filename;

        $i      = 0;
        $store  = '';
        $fields = array('record_no', 'emp_id', 'agency_code', 'promo_company', 'promo_department', 'promo_type', 'type', 'hr_location');
        $bus    = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $hasBu = $this->promo_model->empStores($table2, $emp_id, $record_no, $bu['bunit_field']);
            if ($hasBu) {
                $i++;
                $store = ($i == 1) ? $bu['bunit_field'] : $store . ', ' . $bu['bunit_field'];
                $fields[] = $bu['bunit_field'];
            }
        }

        $values = [];
        $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $query  = $this->promo_model->selectAll_tcR($table2, $cond);
        foreach ($query as $field => $row) {
            if (in_array($field, $fields)) {
                $values[] = "'" . $this->db->escape_str($row) . "'";
            }
        }

        $val    = implode(", ", $values);
        $col    = 'record_no, emp_id, agency_code, promo_company, promo_department, ' . $store . ', promo_type, type, hr_location';
        $data   = "INSERT INTO promo_record ($col) VALUES ($val);";

        $tableName = 'promo_record.sql';
        $this->zip->add_data($tableName, $data);
        $this->zip->archive($path);

        if (file_exists($path)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
            header('Content-Length: ' . filesize($path));

            readfile($path);
        } else {
            show_404();
        }

        exit;
    }
}
