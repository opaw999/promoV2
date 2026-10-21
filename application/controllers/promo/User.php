<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
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

    public function searchPromoUsers()
    {
        echo '<ul class="list-group">';
        $input  = $this->input->post(NULL, TRUE);
        $query  = $this->promo_model->searchPromoUsers($input);
        if ($query) {
            foreach ($query as  $row) {
                $name       = ucwords(strtolower($row['name']));
                $emp_id     = $row['emp_id'];
                $record_no  = $row['record_no'];
                $onclick    = 'onclick="selectPromoUsers(\'' . $name . '\',\'' . $emp_id . '\',\'' . $record_no . '\')"';
                echo '<a href="javascript:;" class="list-group-item" ' . $onclick . '>' . $name . '</a>';
            }
        } else {
            echo '<a href="javascript:;" class="list-group-item">No Results Found!</a>';
        }
        echo '</ul>';
    }

    public function selectPromoUsers()
    {
        $input = $this->input->post(NULL, TRUE);
        if ($input['process'] == 'userAccount') {

            $cond   = array('emp_id' => $input['emp_id'], 'usertype' => 'employee');
            $chk    = $this->promo_model->selectAll_tcR('users', $cond);
            if ($chk) {
                echo json_encode(['message' => 'exist',]);
            } else {
                echo json_encode(['message' => 'success']);
            }
        } else if ($input['process'] == 'inchargeAccount') {

            $cond   = array('emp_id' => $input['emp_id']);
            $chk    = $this->promo_model->selectAll_tcR('promo_user', $cond);
            if ($chk) {
                echo json_encode(['message' => 'exist',]);
            } else {
                echo json_encode(['message' => 'success']);
            }
        }
    }

    public function save_userAccount()
    {
        $input  = $this->input->post(NULL, TRUE);
        $cond   = array('username' => $input['username']);
        $chk    = $this->promo_model->selectAll_tcR('users', $cond);
        if ($chk) {
            $message = 'User name already exists.';
            echo json_encode(['message' => 'exist', 'report' => $message]);
            return;
        }

        $data = array(
            'emp_id'        => $input['emp_id'],
            'username'      => $input['username'],
            'password'      => md5($input['password']),
            'usertype'      => 'employee',
            'user_status'   => 'inactive',
            'login'         => 'no',
            'date_created'  => date('Y-m-d H:i:s'),
            'user_id'       => '4',
        );

        $sql = $this->promo_model->insert_tdA('users', $data);
        if ($sql) {
            $activity   = "Addded Promo User account of ($input[emp_id])";
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

    public function addPromoInchargeAccount()
    {
        $input  = $this->input->post(NULL, TRUE);
        $data   = array(
            'emp_id'        => $input['emp_id'],
            'usertype'      => $input['usertype'],
            'user_status'   => $input['user_status'],
            'date_created'  => date('Y-m-d H:i:s'),
        );

        $sql = $this->promo_model->insert_tdA('promo_user', $data);
        if ($sql) {

            $activity   = "Addded Promo Incharge account of ($input[emp_id])";
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

    public function managePromoUserAccounts()
    {
        $data   = [];
        $query  = $this->promo_model->promoUserAccounts();
        foreach ($query as $row) {

            $empId  = $row['emp_id'];
            $name   =   '<a href="' . base_url('promo/page/promo/profile/' . $empId) . '" target="_blank">' . ucwords(strtolower($row['name'])) . '</a>';

            $userStat   = $row['user_status'];
            $userNo     = $row['user_no'];

            $class  = ($userStat == 'active') ? 'class="badge bg-success"' : 'class="badge bg-secondary"';
            $id     = ($userStat == 'active') ? 'id="deactivate"' : 'id="activate"';
            $title  = ($userStat == 'active') ? 'title="Deactivate Account"' : 'title="Activate Account"';
            $iClass = ($userStat == 'active') ? 'text-warning' : 'text-secondary';

            $status =   '<span ' . $class . '>' . $userStat . '</span>';
            $action =   '<a href="javascript:;" ' . $id . ' onclick="userAccount(this.id, \'' . $userNo . '\')" ' . $title . '>
                            <i class="bx bxs-bulb me-0 font-22 ' . $iClass . '"></i>
                        </a>';

            $action .=  '<a href="javascript:;" id="reset" onclick="userAccount(this.id, \'' . $userNo . '\')" title="Reset Password">
                            <i class="fadeIn animated bx bx-reset font-22"></i>
                        </a>';
            if (in_array($this->userId, $this->adminUser)) {
                $action .=  '<a href="javascript:;" id="delete" onclick="userAccount(this.id, \'' . $userNo . '\')" title="Delete Account">
                                <i class="fadeIn animated bx bx-trash font-22 text-danger"></i>
                            </a>';
            }

            $column     = [];
            $column[]   = $name;
            $column[]   = $row['username'];
            $column[]   = $row['usertype'];
            $column[]   = $row['login'];
            $column[]   = $status;
            $column[]   = $action;
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }

    public function userAccount()
    {
        $input  = $this->input->post(NULL, TRUE);
        $cond   = array('user_no' => $input['id']);

        if ($input['process'] == 'deactivate') {
            $data = array('user_status' => 'inactive', 'date_updated' => date('Y-m-d H:i:s'));
            $sql = $this->promo_model->update_twdA('users', $cond, $data);
        } else if ($input['process'] == 'activate') {
            $data = array('user_status' => 'active', 'date_updated' => date('Y-m-d H:i:s'));
            $sql = $this->promo_model->update_twdA('users', $cond, $data);
        } else if ($input['process'] == 'reset') {
            $data = array('password' => md5('Hrms2014'), 'date_updated' => date('Y-m-d H:i:s'));
            $sql = $this->promo_model->update_twdA('users', $cond, $data);
        } else if ($input['process'] == 'delete') {
            $sql = $this->promo_model->delete_tcr_row('users', $cond);
        }

        if ($sql) {
            $activity   = "Updated Promo User account with user_no : $input[id]";
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

    public function managePromoInchargeAccounts()
    {
        $cond   = ['user_status' => 'active'];
        $query  = $this->promo_model->selectAll_tcA('promo_user', $cond);
        foreach ($query as $row) {

            $cond = ['emp_id' => $row['emp_id']];
            $emp = $this->promo_model->selectAll_tcR('employee3', $cond);
            if ($emp['current_status'] != 'Active') {
                $data = ['user_status' => 'inactive', 'date_updated' => date('Y-m-d H:i:s')];
                $update = $this->promo_model->update_twdA('promo_user', $cond, $data);
            }
        }

        $cond = ['emp_id' => $this->userId, 'usertype' => 'administrator'];
        $admin = $this->promo_model->selectAll_tcR('promo_user', $cond);

        $data   = [];
        $select = '*,t1.date_updated';
        $table1 = 'promo_user';
        $table2 = 'employee3';
        $join   = 't1.emp_id=t2.emp_id';
        $cond   = ['usertype !=' => ''];
        $order = 't2.name|ASC';
        $query = $this->promo_model->join_ttjcoA($select, $table1, $table2, $join, null, $cond, $order);
        foreach ($query as $row) {

            $userNo = $row['user_no'];
            $link = '<a href="' . base_url('../placement/employee_details.php?com=' . $row['emp_id']) . '" target="_blank">' . $row['emp_id']  . '</a>';
            $dateU = '';
            if (!empty($row['date_updated']) && $row['date_updated'] != '0000-00-00') {
                $dateU = date("m/d/Y", strtotime($row['date_updated']));
            }

            $userType = '<select class="form-select form-select-sm" id="ut' . $userNo . '" name="usertype" onchange="updatePromoInchargeAccounts(this.name,this.value)">';
            $userType .= '<option value="promo1|' . $userNo . '" ' . ($row['usertype'] == 'promo1' ? 'selected' : '') . '>Promo Incharge</option>';
            $userType .= '<option value="promo2|' . $userNo . '" ' . ($row['usertype'] == 'promo2' ? 'selected' : '') . '>Encoder</option>';
            $userType .= '<option value="promo3|' . $userNo . '" ' . ($row['usertype'] == 'promo3' ? 'selected' : '') . '>Guest</option>';
            if ($admin) {
                $userType .= '<option value="administrator|' . $userNo . '" ' . ($row['usertype'] == 'administrator' ? 'selected' : '') . '>Administrator</option>';
            }
            $userType .= '</select>';

            $status = '<select class="form-select form-select-sm" id="us' . $userNo . '"  name="user_status" onchange="updatePromoInchargeAccounts(this.name,this.value)">';
            $status .= '<option value="active|' . $userNo . '" ' . ($row['user_status'] == 'active' ? 'selected' : '') . '>Active</option>';
            $status .= '<option value="inactive|' . $userNo . '" ' . ($row['user_status'] == 'inactive' ? 'selected' : '') . '>InActive</option>';
            $status .= '</select>';

            $access = '<i class="text-secondary bx bx-lock font-22"></i>';
            if ($admin) {
                $access = '<a href="javascript:;" onclick="formUserAccess(\'' . $userNo . '\')">';
                $access .= '<i class="text-primary bx bx-lock font-22"></i>';
                $access .= '</a>';
            }

            $column     = [];
            $column[]   = $link;
            $column[]   = $row['name'];
            $column[]   = $userType;
            $column[]   = $status;
            $column[]   = date("m/d/Y", strtotime($row['date_created']));
            $column[]   = $dateU;
            $column[]   = $access;
            $data[]     = $column;
        }

        echo json_encode(['data' => $data]);
    }

    public function formUserAccess()
    {
        $input  = $this->input->post(NULL, TRUE);
        $userNo = $input['userNo'];
        $cond   = ['user_no' => $userNo];
        $user   = $this->promo_model->selectAll_tcR('promo_user', $cond);
        $empId  = $user['emp_id'];
        $ut     = $user['usertype'];
        $name   = $this->promo_model->getName_employee3($empId);

        $m = [];
        if ($user['menu_access']) {
            $m = explode('|', $user['menu_access']);
        }

        $s = [];
        if ($user['submenu_access']) {
            $s = explode('|', $user['submenu_access']);
        }

        echo '<h4>[' . $empId . '] ' . $name . '</h4>';
        echo '<table class="table table-sm table-bordered align-middle">';
        echo '<tr>';
        echo    '<th width="15%">Main Menu</th>';
        echo    '<th>Sub Menu</th>';
        echo '</tr>';
        echo '<input type="hidden" name="empId" value="' . $empId . '">';
        $cond = ['menu_status' => 'active', $ut => 'yes'];
        $query = $this->promo_model->selectAll_tcA('promov2_menu', $cond);
        foreach ($query as $key => $mm) {
            $mmId = $mm['menu_id'];
            $mmName = $mm['menu_name'];

            $checked = '';
            if (in_array($mmId, $m)) {
                $checked = 'checked';
            }

            $mmM =  '<div class="form-check">
						<input class="form-check-input mm' . $mmId . '" type="checkbox" value="' . $mmId . '" id="mm' . $mmId . '" onclick="updateMainMenu(\'' . $empId . '\',\'' . $mmId . '\')" ' . $checked . '>
						<label class="form-check-label" for="mm' . $mmId . '">' . $mmName . '</label>
					</div>';

            echo '<tr>';
            echo    '<td>' . $mmM . '</td>';
            echo    '<td>';

            $cond = ['menu_id' => $mmId, 'submenu_status' => 'active', $ut => 'yes'];
            $query = $this->promo_model->selectAll_tcA('promov2_submenu', $cond);
            foreach ($query as $key => $sm) {
                $smId = $sm['submenu_id'];
                $smName = $sm['submenu_name'];

                $checked = '';
                if (in_array($smId, $s)) {
                    $checked = 'checked';
                }

                echo    '<div class="form-check form-check-inline">
                            <input class="form-check-input sm' . $mmId . '" type="checkbox" value="' . $smId . '" id="sm' . $smId . '" onclick="updateSubMenu(\'' . $empId . '\',\'' . $mmId . '\',\'' . $smId . '\')" ' . $checked . '>
                            <label class="form-check-label" for="sm' . $smId . '">' . $smName . '</label>
                        </div>';
            }
            echo    '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function  updateMainMenu()
    {
        $input      = $this->input->post(NULL, TRUE);
        $empId      = $input['empId'];
        $mmId       = $input['mmId'];
        $process    = $input['process'];

        $cond   = ['emp_id' => $empId];
        $pm     = $this->promo_model->selectAll_tcR('promo_user', $cond);
        $ut     = $pm['usertype'];

        $cond   = ['menu_id' => $mmId, 'submenu_status' => 'active', $ut => 'yes'];
        $psm    = $this->promo_model->selectAll_tcA('promov2_submenu', $cond);
        $s1     = array_column($psm, 'submenu_id');


        $m = [];
        if ($pm['menu_access']) {
            $m = explode('|', $pm['menu_access']);
        }

        $s2 = [];
        if ($pm['submenu_access']) {
            $s2 = explode('|', $pm['submenu_access']);
        }

        if ($process == 'add') {
            if (!in_array($mmId, $m)) {
                $m[] = $mmId;
            }
            $s = array_merge($s2, $s1);
        } else {
            $m = array_diff($m, [$mmId]);
            $s = array_diff($s2, $s1);
        }

        $menu = implode('|', $m);
        $submenu = implode('|', $s);

        $data   = ['menu_access' => $menu, 'submenu_access' => $submenu];
        $cond   = ['emp_id' => $empId];
        $update = $this->promo_model->update_twdA('promo_user', $cond, $data);
    }

    public function  updateSubMenu()
    {
        $input      = $this->input->post(NULL, TRUE);
        $empId      = $input['empId'];
        $mmId       = $input['mmId'];
        $smId       = $input['smId'];
        $process    = $input['process'];

        $cond   = ['emp_id' => $empId];
        $pm     = $this->promo_model->selectAll_tcR('promo_user', $cond);
        $ut     = $pm['usertype'];

        $cond   = ['menu_id' => $mmId, 'submenu_status' => 'active', $ut => 'yes'];
        $psm    = $this->promo_model->selectAll_tcA('promov2_submenu', $cond);
        $s1     = array_column($psm, 'submenu_id');

        $m = [];
        if ($pm['menu_access']) {
            $m = explode('|', $pm['menu_access']);
        }

        $s = [];
        if ($pm['submenu_access']) {
            $s = explode('|', $pm['submenu_access']);
        }

        if ($process == 'add') {
            if (!in_array($mmId, $m)) {
                $m[] = $mmId;
            }

            if (!in_array($smId, $s)) {
                $s[] = $smId;
            }
        } else {

            $s = array_diff($s, [$smId]);

            $count = 0;
            foreach ($s as $key => $row) {
                if (in_array($row, $s1)) {
                    $count++;
                }
            }
            if (!$count) {
                $m = array_diff($m, [$mmId]);
            }
        }

        $menu = implode('|', $m);
        $submenu = implode('|', $s);

        $data   = ['menu_access' => $menu, 'submenu_access' => $submenu];
        $cond   = ['emp_id' => $empId];
        $update = $this->promo_model->update_twdA('promo_user', $cond, $data);
    }


    public function updatePromoInchargeAccounts()
    {
        $input  = $this->input->post(NULL, TRUE);
        $split  = explode('|', $input['value']);
        $cond   = array('user_no' => $split[1]);
        $data   = array($input['column'] => $split[0], 'date_updated' => date("Y-m-d H:i:s"));
        $sql    = $this->promo_model->update_twdA('promo_user', $cond, $data);

        if ($sql) {
            $activity   = "Updated Promo Incharge account with user_no : $split[1]";
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

    public function promoUserAccessRoles()
    {
        $data = [];
        $list = $this->promo_model->selectAll('promoV2_submenu');
        foreach ($list as $row) {

            $subId  = $row['submenu_id'];
            $p1     = 'id="p1' . $subId . '" style="width: 70px;" name="promo1"';
            $p2     = 'id="p2' . $subId . '" style="width: 70px;" name="promo2"';
            $p3     = 'id="p3' . $subId . '" style="width: 70px;" name="promo3"';
            $stat   = 'id="p3' . $subId . '" style="width: 100px;" name="submenu_status"';

            $admin  = '<span class="badge bg-success px-2 py-1">' . $row['administrator'] . '</span>';
            $menu   = $this->promo_model->selectAll_tcR('promoV2_menu', array('menu_id' => $row['menu_id']))['menu_name'];
            $promo1 =   '<select class="form-select form-select-sm mx-auto" ' . $p1 . ' onchange="updatePromoUserAccessRoles(this.name,this.value)">
                            <option value="yes|' . $subId . '" ' . ($row['promo1'] == 'yes' ? 'selected' : '') . '>yes</option>
                            <option value="no|' . $subId . '" ' . ($row['promo1'] == 'no' ? 'selected' : '') . '>no</option>
                         </select>';
            $promo2 =  '<select class="form-select form-select-sm mx-auto" ' . $p2 . ' onchange="updatePromoUserAccessRoles(this.name,this.value)">
                            <option value="yes|' . $subId . '" ' . ($row['promo2'] == 'yes' ? 'selected' : '') . '>yes</option>
                            <option value="no|' . $subId . '" ' . ($row['promo2'] == 'no' ? 'selected' : '') . '>no</option>
                        </select>';
            $promo3 =  '<select class="form-select form-select-sm mx-auto" ' . $p3 . ' onchange="updatePromoUserAccessRoles(this.name,this.value)">
                            <option value="yes|' . $subId . '" ' . ($row['promo3'] == 'yes' ? 'selected' : '') . '>yes</option>
                            <option value="no|' . $subId . '" ' . ($row['promo3'] == 'no' ? 'selected' : '') . '>no</option>
                        </select>';
            $status =  '<select class="form-select form-select-sm mx-auto" ' . $stat . ' onchange="updatePromoUserAccessRoles(this.name,this.value)">
                            <option value="active|' . $subId . '" ' . ($row['submenu_status'] == 'active' ? 'selected' : '') . '>active</option>
                            <option value="inactive|' . $subId . '" ' . ($row['submenu_status'] == 'inactive' ? 'selected' : '') . '>inactive</option>
                        </select>';

            $column     = [];
            $column[]   = $menu;
            $column[]   = $row['submenu_name'];
            $column[]   = $admin;
            $column[]   = $promo1;
            $column[]   = $promo2;
            $column[]   = $promo3;
            $column[]   = $status;
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }

    public function accessRoles()
    {
        $input  = $this->input->post(NULL, TRUE);
        $split  = explode('|', $input['value']);
        $cond   = array('submenu_id' => $split[1]);
        $menuId = $this->promo_model->selectAll_tcR('promoV2_submenu', $cond)['menu_id'];
        $data   = array($input['column'] => $split[0]);
        $sql    = $this->promo_model->update_twdA('promoV2_submenu', $cond, $data);
        if ($sql) {

            if ($input['column'] != 'submenu_status') {
                $cond = array('menu_id' => $menuId, $input['column'] => 'yes');
                $chk = $this->promo_model->selectAll_tcR('promoV2_submenu', $cond);
                if ($chk) {
                    $data = array($input['column'] => 'yes');
                } else {
                    $data = array($input['column'] => 'no');
                }
                $cond = array('menu_id' => $menuId);
                $sql = $this->promo_model->update_twdA('promoV2_menu', $cond, $data);
            }
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function updateUserAccount()
    {
        $input  = $this->input->post(NULL, TRUE);
        if ($input['process'] == 'username') {

            $cond  = array('username' => $input['username1'], 'emp_id !=' => $input['emp_id']);
            $chk   = $this->promo_model->selectAll_tcR('users', $cond);
            if ($chk) {
                $report = 'Username already exists.';
                echo json_encode(['message' => 'exist', 'report' => $report]);
                return;
            }

            $report = 'Username has been updated. You will be logged out.';
            $data   = array('username' => $input['username1']);
            $cond   = array('username' => $input['username'], 'emp_id' => $input['emp_id']);
            $sql    = $this->promo_model->update_twdA('users', $cond, $data);
        } else {

            $cond   = array('emp_id' => $input['emp_id'], 'username' => $input['username'], 'password' => md5($input['password']));
            $chk    = $this->promo_model->selectAll_tcR('users', $cond)['password'];
            if (!$chk) {
                $report = 'Wrong current password.';
                echo json_encode(['message' => 'mismatch', 'report' => $report]);
                return;
            }

            $report = 'Password has been updated. You will be logged out.';
            $data   = array('password' => md5($input['password1']));
            $cond   = array('emp_id' => $input['emp_id'], 'username' => $input['username'],);
            $sql    = $this->promo_model->update_twdA('users', $cond, $data);
        }

        if ($sql) {
            echo json_encode(['message' => 'success', 'report' => $report]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }
}
