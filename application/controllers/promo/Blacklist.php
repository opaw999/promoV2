<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Blacklist extends CI_Controller
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

    public function viewBlacklist()
    {
        $input  = $this->input->post(NULL, TRUE);
        $data   = [];
        $order  = array('b.app_id', 'b.name', 'b.reportedBy', 'b.date_blacklisted', 'b.reason');
        $list   = $this->promo_model->make_datatables('viewBlacklist', $order, $order[1], 'ASC', $input);
        foreach ($list as $row) {

            $dateBL = ($row['date_blacklisted'] == '0000-00-00' || $row['date_blacklisted'] == null) ? '' : date('m/d/Y', strtotime($row['date_blacklisted']));
            $action =   '<a href="javascript:;" title="Update Blacklist" id="' . $row['blacklist_no'] . '" onclick="updateBlacklistForm(this.id)">
                            <i class="bx bx-edit me-0" style="font-size: 22px;"></i>
                        </a>';
            $column     = [];
            $column[]   = $row['app_id'];
            $column[]   = $row['name'];
            $column[]   = $row['reportedby'];
            $column[]   = $dateBL;
            $column[]   = $row['reason'];
            $column[]   = $action;
            $data[]     = $column;
        }
        $output = array(
            'draw'              => intval($_POST['draw']),
            'recordsTotal'      => $this->promo_model->get_all_data('viewBlacklist'),
            'recordsFiltered'   => $this->promo_model->get_filtered_data('viewBlacklist', $order, $order[1], 'ASC', $input),
            'data'              => $data
        );
        echo json_encode($output);
    }

    public function updateBlacklistForm()
    {
        $input      = $this->input->post(NULL, TRUE);
        $blacklist  = $this->promo_model->selectAll_tcR('blacklist', array('blacklist_no' => $input['id']));
        echo '<div class="row">
                <div class="col-md-12">
                    <label class="form-label">EmployeeID/Name</label>
                    <input type="text" class="form-control" value="[' . $blacklist['app_id'] . '] ' . $blacklist['name'] . '" disabled>
                </div>';
        echo '</div>';
        echo '<div class="row mt-2">
                <div class="col-md-6">
                    <label class="form-label">Reported By</label>
                    <input type="text" class="form-control" name="reportedby" value="' . $blacklist['reportedby'] . '" onkeyup="nameSearch(this.value)">
                    <div id="dropdown-list" class="dropdown-list"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date Blacklisted</label>
                    <input type="text" class="form-control datepicker" name="date_blacklisted" value="' . $blacklist['date_blacklisted'] . '">
                </div>';
        echo '</div>';
        echo '<div class="row mt-2">
                <div class="col-md-6">
                    <label class="form-label">Birthdate</label>
                    <input type="text" class="form-control datepicker" name="bday" value="' . $blacklist['bday'] . '">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address" value="' . $blacklist['address'] . '">
                </div>';
        echo '</div>';
        echo '<div class="row mt-2">
                <div class="col-md-12">
                    <label for="input1" class="form-label">Reason</label>
                    <textarea class="form-control" name="reason" cols="3" rows="3">' . $blacklist['reason'] . '</textarea>
                </div>';
        echo '</div>';
        echo '<input type="hidden" class="form-control" name="blacklist_no" value="' . $blacklist['blacklist_no'] . '">';
    }

    public function updateBlacklist()
    {
        $input  = $this->input->post(NULL, TRUE);
        $cond   = array('blacklist_no' => $input['blacklist_no']);
        $data   = array(
            'reportedby'        => $input['reportedby'],
            'date_blacklisted'  => $input['date_blacklisted'],
            'bday'              => $input['bday'],
            'address'           => $input['address'],
            'reason'            => $input['reason'],
        );

        $update = $this->promo_model->update_twdA('blacklist', $cond, $data);

        if ($update) {
            $activity = 'Update Blacklist Promo Employee with blacklist_no:' . $input['blacklist_no'];
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date('H:i:s'),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $logs = $this->promo_model->insert_tdA('logs', $data);

            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function reportedbyBl()
    {
        echo '<ul class="list-group">';
        $input  = $this->input->post(NULL, TRUE);
        $query  = $this->promo_model->reportedbyBl($input);
        if ($query) {
            foreach ($query as $row) {
                $name = ucwords(strtolower($row['name']));
                echo '<a href="javascript:;" class="list-group-item" onclick="getName(\'' . $name . '\')">' . $name . '</a>';
            }
        } else {
            echo '<a href="javascript:;" class="list-group-item">No results found!</a>';
        }
        echo '</ul>';
    }

    public function browseBltable1()
    {
        $input  = $this->input->post(NULL, TRUE);
        $data   = [];
        $order  = array('lastname', null, null);
        $list   = $this->promo_model->make_datatables('checkBl', $order, $order[0], 'ASC', $input);
        foreach ($list as $row) {
            $app_id = '';
            $action = '';
            $name   = '';
            if ($row['name'] != '') {
                $name = ucwords(strtolower($row['name']));
            } else {
                $name = ucwords(strtolower($row['lastname'] . ', ' . $row['firstname']));
            }

            if ($row['app_id'] != '') {
                $app_id = '<a href="' . base_url('promo/page/promo/profile/' . $row['app_id']) . '" target="_blank">' . $name . '</a>';
            } else {
                $app_id = $name;
            }

            $check = $this->promo_model->selectAll_tcR('blacklist', array('app_id' => $row['app_id']));
            if (!$check) {
                $action =   '<a href="javascript:;" title="add to Blacklist" id="' . $row['app_id'] . '" onclick="addBl(this.id)">
                                <i class="bx bx-list-plus me-0" style="font-size: 22px;"></i>
                            </a>';
            }

            if ($row['current_status'] == 'blacklisted') {
                $status = '<span class="badge bg-danger">' . $row['current_status'] . '</span>';
            } else if ($row['current_status'] == 'Active') {
                $status = '<span class="badge bg-success">' . $row['current_status'] . '</span>';
            } else {
                $status = '<span class="badge bg-warning">' . $row['current_status'] . '</span>';
            }

            $column     = [];
            $column[]   = $app_id;
            $column[]   = $status;
            $column[]   = $action;
            $data[]     = $column;
        }

        $output = array(
            'draw'              => intval($_POST['draw']),
            'recordsTotal'      => $this->promo_model->get_all_data('checkBl', $input),
            'recordsFiltered'   => $this->promo_model->get_filtered_data('checkBl', $order, $order[0], 'ASC', $input),
            'data'              => $data
        );
        echo json_encode($output);
    }

    public function browseBltable2()
    {
        $input  = $this->input->post(NULL, TRUE);
        $data   = [];
        $order  = array('name', null);
        $list   = $this->promo_model->make_datatables('checkBlt', $order, $order[0], 'ASC', $input);
        foreach ($list as $row) {

            $name   = ucwords(strtolower($row['name']));
            $app_id = $name;

            if ($row['app_id'] != '') {
                $app_id = '<a href="' . base_url('promo/page/promo/profile/' . $row['app_id']) . '" target="_blank">' . $name . '</a>';
            }

            $column     = [];
            $column[]   = $app_id;
            $column[]   = '<span class="badge bg-danger">Blacklisted</span>';
            $data[]     = $column;
        }

        $output = array(
            'draw'              => intval($_POST['draw']),
            'recordsTotal'      => $this->promo_model->get_all_data('checkBlt', $input),
            'recordsFiltered'   => $this->promo_model->get_filtered_data('checkBlt', $order, $order[0], 'ASC', $input),
            'data'              => $data
        );
        echo json_encode($output);
    }

    public function addBlacklistForm()
    {
        $input  = $this->input->post(NULL, TRUE);
        $result = $this->promo_model->addBl($input);

        $name = ucwords(strtolower($result['lastname'] . ', ' . $result['firstname'] . ' ' . $result['middlename']));

        if ($result['birthdate'] == '1900-01-01' || $result['birthdate'] == '0000-00-00') {
            $bdate = '';
        } else {
            $bdate = $result['birthdate'];
        }

        echo '<div class="row">';
        echo '<div class="col-md-12">
                <label for="input1" class="form-label">EmployeeID/Name</label>
                <input type="text" class="form-control" value="[' . $result['app_id'] . '] ' . $name . '" disabled>
                <input type="hidden" name="emp_id" value="' . $result['app_id'] . '">
                <input type="hidden" name="name" value="' . $name . '">
                <input type="hidden" name="process" value="existing">';
        echo '</div>';
        echo '</div>';
        echo '<div class="row mt-2">';
        echo '<div class="col-md-6">
                <label for="input2" class="form-label">Reported By</label>
                <input type="text" class="form-control" name="reportedby" onkeyup="nameSearch(this.value)">
                <div id="dropdown-list" class="dropdown-list"></div>';
        echo '</div>';
        echo '<div class="col-md-6">
                <label for="input2" class="form-label">Date Blacklisted</label>
                <input type="text" class="form-control datepicker" name="date_blacklisted" value="' . date('Y-m-d') . '">';
        echo '</div>';
        echo '</div>';
        echo '<div class="row mt-2">';
        echo '<div class="col-md-6">
                <label for="input2" class="form-label">Birthdate</label>
                <input type="text" class="form-control datepicker" name="bday" value="' . $bdate . '" placeholder="yyyy-mm-dd">';
        echo '</div>';
        echo '<div class="col-md-6">
                <label for="input2" class="form-label">Address</label>
                <input type="text" class="form-control" name="address" value="' . $result['home_address'] . '">';
        echo '</div>';
        echo '</div>';
        echo '<div class="row mt-2">';
        echo '<div class="col-md-12">
                <label for="input1" class="form-label">Reason</label>
                <textarea class="form-control" name="reason" cols="3" rows="3"></textarea>';
        echo '</div>';
        echo '</div>';
    }

    public function addBlacklistFormManual()
    {
        $input  = $this->input->post(NULL, TRUE);
        $fn     = ucwords(strtolower($input['fn']));
        $ln     = ucwords(strtolower($input['ln']));
        $name   = $ln . ', ' . $fn;

        if ($input['mn'] != '') {
            $mn = ucwords(strtolower($input['mn']));
            $name = $ln . ', ' . $fn . ' ' . $mn;
        }

        $reportedBy = $this->promo_model->selectAll_tcR('employee3', array('emp_id' => $this->userId))['name'];

        echo '<div class="row">
                <input type="hidden" class="form-control" name="process" value="manual">
                <div class="col-md-12">
                <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="name" value="' . $name . '" readonly>
                </div>';
        echo '</div>';
        echo '<div class="row mt-2">
                <div class="col-md-6">
                    <label class="form-label">Reported By</label>
                    <input type="text" class="form-control" name="reportedby" value="' . $reportedBy . '" onkeyup="nameSearch(this.value)">
                    <div id="dropdown-list" class="dropdown-list"></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Date Blacklisted</label>
                    <input type="text" class="form-control datepicker" name="date_blacklisted" value="' . date('Y-m-d') . '">
                </div>';
        echo '</div>';
        echo '<div class="row mt-2">
                <div class="col-md-6">
                    <label class="form-label">Birthdate</label>
                    <input type="text" class="form-control datepicker" name="bday" placeholder="yyyy-mm-dd">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address">   
                </div>';
        echo '</div>';
        echo '<div class="row mt-2">
                <div class="col-md-12">
                    <label class="form-label">Reason</label>
                    <textarea class="form-control" name="reason" cols="3" rows="3"></textarea>
                </div>';
        echo '</div>';
    }

    public function addBlacklist()
    {
        $input  = $this->input->post(NULL, TRUE);
        $data   = array(
            'name'              => $input['name'],
            'date_blacklisted'  => $input['date_blacklisted'],
            'date_added'        => $this->today,
            'reportedby'        => $input['reportedby'],
            'reason'            => $input['reason'],
            'status'            => 'blacklisted',
            'staff'             => $this->userId,
            'bday'              => $input['bday'],
            'address'           => $input['address'],
        );

        if ($input['process'] != 'manual') {
            $data['app_id'] = $input['emp_id'];
        }
        $insert = $this->promo_model->insert_tdA('blacklist', $data);

        if ($input['process'] != 'manual') {
            $empID  = $input['emp_id'];
            $cond   = "emp_id = '$empID'";
            $data1  = array('current_status' => 'blacklisted');
            $update = $this->promo_model->update_twdA('employee3', $cond, $data1);
        }

        if ($insert) {

            $activity = "Added Blacklist Promo Employee ($input[name])";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date('H:i:s'),
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
