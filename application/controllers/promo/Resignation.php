<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Resignation extends CI_Controller
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

    public function searchPromoResignation()
    {
        echo '<ul class="list-group">';
        $input  = $this->input->post(NULL, TRUE);
        $query  = $this->promo_model->searchPromoResignation($input);
        if ($query) {
            foreach ($query as  $row) {
                $name       = ucwords(strtolower($row['name']));
                $emp_id     = $row['emp_id'];
                $record_no  = $row['record_no'];
                $status     = $row['current_status'];
                $onclick    = 'onclick="selectPromoResignation(\'' . $name . '\',\'' . $emp_id . '\',\'' . $status . '\')"';
                echo '<a href="javascript:;" class="list-group-item" ' . $onclick . '>' . $name . '</a>';
            }
        } else {
            echo '<a href="javascript:;" class="list-group-item">No Results Found!</a>';
        }
        echo '</ul>';
    }

    public function addResignationForm()
    {
        $input  = $this->input->post(NULL, TRUE);
        $emp_id = $input['emp_id'];
        $status = $input['status'];

        echo '<div class="row">';
        $cond = array('emp_id' => $emp_id);
        $row = $this->promo_model->selectAll_tcR('employee3', $cond);
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {

            $hasBu = $this->promo_model->empStores('promo_record', $emp_id, $row['record_no'], $bu['bunit_field']);
            if ($hasBu) {
                echo '<div class="col-sm-6 mt-0 mb-0">';
                echo '<div class="card radius-10 border shadow-none">
                        <div class="card-body">
                            <p class="card-title text-primary">Clearance: ' . $bu['bunit_name'] . '</p>
                            <hr class="mt-0 mb-2">
                            <input type="file" name="' . $bu['bunit_clearance'] . '" class="form-control form-control-sm" accept="image/*">
                        </div>';
                echo '</div>';
                echo '</div>';
            }
        }

        if ($status == 'Resigned') {
            echo '<div class="col-sm-6 mt-0 mb-0">';
            echo '<div class="card radius-10 border shadow-none">
                    <div class="card-body">
                        <p class="card-title text-primary">Resignation Letter</p>
                        <hr class="mt-0 mb-2">
                        <input type="file" name="resignation_letter" class="form-control form-control-sm" accept="image/*">
                    </div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }

    public function addResignation()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $status     = $input['status'];
        $date       = $input['date'];
        $remarks    = $input['remarks'];

        $cond   = array('emp_id' => $emp_id);
        $row    = $this->promo_model->selectAll_tcR('employee3', $cond);
        $bus    = $this->promo_model->locate_promo_bu($this->hrCode);
        foreach ($bus as $bu) {
            $hasBu = $this->promo_model->empStores('promo_record', $emp_id, $row['record_no'], $bu['bunit_field']);
            if ($hasBu) {
                $path = '';
                if (isset($_FILES[$bu['bunit_clearance']]['name'])) {
                    $array          = explode(".", $_FILES[$bu['bunit_clearance']]['name']);
                    $filename       = $emp_id . '=' . date('Y-m-d') . '=' . 'Clearance' . '=' . date('H-i-s-A') . '.' . end($array);
                    $base_path      = FCPATH;
                    $relative_path  = '../document/clearance/';
                    $path           = $base_path . $relative_path . $filename;
                    if (move_uploaded_file($_FILES[$bu['bunit_clearance']]['tmp_name'], $path)) {
                        $data   = array($bu['bunit_clearance'] => $relative_path . $filename);
                        $cond   = array('emp_id' => $emp_id, 'record_no' => $row['record_no']);
                        $update = $this->promo_model->update_twdA('promo_record', $cond, $data);
                    }
                }
            }
        }

        $rLetter = '';
        if ($input['status'] == 'Resigned') {
            $path = '';
            if (isset($_FILES['resignation_letter']['name'])) {
                $array          = explode(".", $_FILES['resignation_letter']['name']);
                $filename       = $emp_id . '=' . date('Y-m-d') . '=' . 'Resignation-Letter' . '=' . date('H-i-s-A') . '.' . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/resignation/';
                $path           = $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES['resignation_letter']['tmp_name'], $path)) {
                    $rLetter = $relative_path . $filename;
                }
            }
        }

        $data = array(
            'emp_id'        => $emp_id,
            'date'          => $date,
            'remarks'       => $remarks,
            'added_by'      => $this->userId,
            'date_updated'  => $this->today
        );

        if ($rLetter != '') {
            $data['resignation_letter'] = $rLetter;
        }

        $insert     = $this->promo_model->insert_tdA('termination', $data);

        $cond       = array('emp_id' => $emp_id, 'record_no' => $row['record_no']);
        $data       = array('current_status' => $status, 'remarks' => $remarks);
        $update1    = $this->promo_model->update_twdA('employee3', $cond, $data);

        $cond       = array('emp_id' => $emp_id);
        $data       = array('user_status' => 'inactive');
        $update2    = $this->promo_model->update_twdA('users', $cond, $data);

        if ($insert && $update1 && $update2) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function tagResignationTable()
    {
        $input  = $this->input->post(NULL, TRUE);
        echo '<div class="table-responsive">';
        echo '<table id="tagResignationTable" class="table table-sm table-hover align-middle" style="width:100%;">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Employee Type</th>
                        <th>Position</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>';
        $sup    = $input['emp_id'];
        $select = '*,t1.record_no';
        $table1 = 'employee3';
        $table2 = 'leveling_subordinates';
        $join   = 't1.emp_id=t2.subordinates_rater';
        $cond   = "ratee = '$sup' AND  current_status IN ('Active','Ad-Resigned','V-Resigned') AND (emp_type = 'Promo' OR emp_type = 'Promo-NESCO' OR emp_type = 'Promo-EasyL')";
        $order  = 'name|ASC';
        $query  = $this->promo_model->join_ttjcoA($select, $table1, $table2, $join, null, $cond, $order, null);
        foreach ($query as $row) {

            $cond   = array('emp_id' => $row['emp_id'], 'record_no' => $row['record_no']);
            $chk    = $this->promo_model->selectAll_tcR('promo_record', $cond);
            if ($chk) {

                $profile    = '<a href="' . base_url('promo/page/promo/profile/' . $row['emp_id']) . '" target="_blank">' . $row['emp_id'] . '</a>';
                $cond       = array('rater_id' => $sup, 'ratee_id' => $row['emp_id']);
                $tag_stat   = $this->promo_model->selectAll_tcR('tag_for_resignation', $cond)['tag_stat'];

                echo '<tr>
                        <td>' . $profile . '</td>
                        <td>' . ucwords(strtolower($row['name'])) . '</td>
                        <td>' . $row['emp_type'] . '</td>
                        <td>' . $row['position'] . '</td>
                        <td>';
                if ($tag_stat == 'Pending') {
                    echo    '<a href="javascript:;" class="text-danger" onclick="tagResignation(\'untag\',\'' . $sup . '\', \'' . $row['emp_id'] . '\')">
                                <i class="resignTooltip bx bx-x-circle font-22" title="Untag for Resignation"></i>
                            </a>';
                } else if ($tag_stat == 'Done') {
                    echo    '<a href="javascript:;" class="text-success">
                                <i class="resignTooltip bx bx-check-circle font-22" title="Done"></i>
                            </a>';
                } else {
                    echo    '<a href="javascript:;" class="text-primary" onclick="tagResignation(\'tag\',\'' . $sup . '\', \'' . $row['emp_id'] . '\')">
                                <i class="resignTooltip bx bx-purchase-tag-alt font-22" title="Tag for Resignation"></i>
                            </a>';
                }
                echo    '</td>';
                echo '</tr>';
            }
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }

    public function tagResignation()
    {
        $input      = $this->input->post(NULL, TRUE);
        $tag_stat   = $input['tag_stat'];

        if ($tag_stat == 'tag') {
            $insert = array(
                'ratee_id'      => $input['ratee_id'],
                'rater_id'      => $input['rater_id'],
                'added_by'      => $this->userId,
                'date_added'    => $this->today,
                'tag_stat'      => 'Pending',
            );
            $tag = $this->promo_model->insert_tdA('tag_for_resignation', $insert);
        } else {
            $cond = array(
                'ratee_id'  => $input['ratee_id'],
                'rater_id'  => $input['rater_id'],
            );
            $tag = $this->promo_model->delete_tcr_row('tag_for_resignation', $cond);
        }

        if ($tag) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function resignationList()
    {
        $data = [];
        $list = $this->promo_model->resignationList();
        foreach ($list as $row) {
            $name = '<a class="text-truncate" href="' . base_url('promo/page/promo/profile/' . $row['emp_id']) . '" target="_blank">' . ucwords(strtolower($row['name'])) . '</a>';
            $addedBy = $this->promo_model->getName_employee3($row['added_by']);
            if (!empty($row['resignation_letter'])) {

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

                $action = '<a href="javascript:;" id="' . $link . '" onclick="view_letter(this.id)"><i class="bx bx-images font-22" title="view"></i></a>';
            } else {
                $action = '<a href="javascript:;" id="' . $row['termination_no'] . '" onclick="uploadLetterForm(this.id,\'' . $row['emp_id'] . '\')"><i class="fadeIn animated bx bx-upload font-22 text-secondary" title="upload"></i></a>';
            }
            $column     = [];
            $column[]   = $name;
            $column[]   = date('m/d/Y', strtotime($row['date']));
            $column[]   = ucwords(strtolower($addedBy));
            $column[]   = date('m/d/Y', strtotime($row['date_updated']));
            $column[]   = $row['remarks'];
            $column[]   = $action;
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }

    public function uploadLetterForm()
    {
        $input = $this->input->post(NULL, TRUE);
        $tn = $input['termination_no'];
        $id = $input['emp_id'];
        echo '<input type="hidden" name="termination_no" value="' . $tn . '">';
        echo '<input type="hidden" name="emp_id" value="' . $id . '">';
        echo '<div class="row">';
        echo '<div class="col-sm-10 mx-auto">
                <div class="card radius-10 border shadow-none">
                    <div class="card-body">
                        <h5 class="card-title text-secondary">Choose Image...</h5>
                        <hr>
                        <div class="d-flex align-items-center gap-2">
                            <input type="file" name="resignation_letter" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>';
        echo '</div>';
        echo '</div>';
    }

    public function uploadLetter()
    {
        $input  = $this->input->post(NULL, TRUE);
        $emp_id = $input['emp_id'];

        $path   = '';
        if (isset($_FILES['resignation_letter']['name'])) {
            $array          = explode(".", $_FILES['resignation_letter']['name']);
            $filename       = $emp_id . '=' . date('Y-m-d') . '=' . 'Resignation-Letter' . '=' . date('H-i-s-A') . '.' . end($array);
            $base_path      = FCPATH;
            $relative_path  = '../document/resignation/';
            $path           = $base_path . $relative_path . $filename;
            if (move_uploaded_file($_FILES['resignation_letter']['tmp_name'], $path)) {
                $cond   = array('termination_no' => $input['termination_no']);
                $data   = array('resignation_letter' => $relative_path . $filename, 'date_updated' => $this->today);
                $update = $this->promo_model->update_twdA('termination', $cond, $data);
            }
        }

        if ($update) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }
}
