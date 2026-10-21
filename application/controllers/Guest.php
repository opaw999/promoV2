<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Guest extends CI_Controller
{
    public $userId;
    public $hrCode;
    public $today;
    public $adminUser;

    function __construct()
    {
        parent::__construct();
        $this->userId = $_SESSION['emp_id'];
        $this->today = date('Y-m-d');
        $this->adminUser = array('01186-2023');

        if (!isset($this->hrCode)) {
            $cond = array('emp_id' => $_SESSION['emp_id']);
            $cc = $this->promo_model->selectAll_tcR('employee3', $cond)['company_code'];
            $_SESSION['hrCode'] = ($cc != '07') ? 'asc' : 'cebo';
            $this->hrCode = $_SESSION['hrCode'];
        }

        if (!isset($this->userId)) {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }

        $cond = array('emp_id' => $this->userId, 'user_status' => 'active', 'usertype' => 'promo3');
        $chk = $this->promo_model->selectAll_tcR('promo_user', $cond);
        if (!$chk) {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }

        $segment = $this->uri->segment(1);
        if ($segment === 'promo') {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }
    }

    public function menu($menu = 'dashboard', $file = 'dashboard', $emp_id = '')
    {

        if (!file_exists(APPPATH . "views/promo/guest/page/$menu/$file.php")) {
            show_404();
        }

        if ($menu == 'dashboard') {
            $i      = 0;
            $stores = '';
            $num    = '';
            $md     = date('m-d');
            $new    = $this->promo_model->newPromo();
            $total  = $this->promo_model->activeTotal();
            $eoc    = $this->promo_model->eocToday();
            $failed = $this->promo_model->failedEpas();
            $bday   = $this->promo_model->birthdayToday($md);
            $color  = [];
            function random_color_part()
            {
                return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
            }

            function random_color()
            {
                return '#' . random_color_part() . random_color_part() . random_color_part();
            }

            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $value) {
                $i++;
                $stores = ($i == 1) ? "'" . $value['bunit_name'] . "'" : $stores . ", '" . $value['bunit_name'] . "'";
                $count  = $this->promo_model->active_promoStore($value['bunit_field']);
                $num    = ($i == 1) ? $count['num'] : $num . ', ' . $count['num'];
                $color[] = random_color();
            }

            $data['color']      = $color;
            $data['stores']     = $stores;
            $data['num']        = $num;
            $data['newPromo']   = $new;
            $data['failedEpas'] = $failed;
            $data['bday']       = $bday;
            $data['total']      = $total;
            $data['eoc']        = $eoc;
        }

        $data['emp_id'] = $emp_id;
        $data['user']   = $this->promo_model->user_info($this->userId);
        $data['menu']   = $menu;
        $data['file']   = $file;
        $this->load->view('promo/guest/template/header', $data);
        $this->load->view('promo/guest/template/menu', $data);
        $this->load->view('promo/guest/page/' . $menu . '/' . $file, $data);
        $this->load->view('promo/guest/template/script');
        $this->load->view('promo/guest/page/' . $menu . '/' . $menu . '_js', $data);
    }

    public function profileData()
    {
        $input  = $this->input->post(NULL, TRUE);
        $emp_id = $input['emp_id'];
        $cond   = array('e.emp_id' => $emp_id);
        $row    = $this->promo_model->promoDetails_wttR($cond, 'employee3', 'promo_record');
        if ($row) {
            $record_no              = $row['record_no'];
            $cond1                  = array('app_id' => $emp_id);
            $cond2                  = array('emp_id' => $emp_id);
            $data['emp_id']         = $emp_id;
            $data['record_no']      = $record_no;
            $data['religion']       = $this->promo_model->selectAll('religion');
            $data['weight']         = $this->promo_model->selectAll('weight');
            $data['height']         = $this->promo_model->selectAll('height');
            $data['attainment']     = $this->promo_model->selectAll('attainment');
            $data['school']         = $this->promo_model->selectAll('school');
            $data['course']         = $this->promo_model->selectAll('course');
            $data['training']       = $this->promo_model->selectAll_tcA('application_seminarsandeligibility', $cond1);
            $data['character']      = $this->promo_model->selectAll_tcA('application_character_ref', $cond1);
            $data['appraisal']      = $this->promo_model->selectAll_tcA('appraisal_details', $cond2);
            $data['application']    = $this->promo_model->selectAll_tcR('application_details', $cond1);
            $data['appHist']        = $this->promo_model->selectAll_tcA('application_history', array('app_id' => $emp_id, 'phase' => 'Examination', 'status' => 'completed'));
            $data['positions']      = $this->promo_model->positions();
            $data['app']            = $this->promo_model->selectAll_tcR('applicant', $cond1);
            $data['address']        = $this->promo_model->addressBoholCebu();
            $data['row']            = $this->promo_model->promoDetails_wttR($cond, 'employee3', 'promo_record');
            $data['empHistory']     = $this->promo_model->selectAll_tcA('application_employment_history', $cond1);
            $data['benefits']       = $this->promo_model->selectAll_tcR('applicant_otherdetails', $cond1);
            $data['supervisor']     = $this->promo_model->selectAll_tcA('leveling_subordinates', array('subordinates_rater' => $emp_id));
            $data['docFile']        = $this->promo_model->selectAll_tcoA('201document', array('promo' => 'yes'), '201_name');
            $data['remarks']        = $this->promo_model->selectAll_tcR('remarks', $cond2);
            $data['clearance']      = $this->promo_model->clearanceHistory($emp_id);
            $data['transfer']       = $this->promo_model->selectAll_tcA('employee_transfer_details', $cond2);
            $data['blacklist']      = $this->promo_model->selectAll_tcA('blacklist', $cond1);
            $data['userAccount']    = $this->promo_model->selectAll_tcA('users', array('emp_id' => $emp_id, 'usertype' => 'employee'));
            $data['request']        = $input['id'];

            $this->load->view('promo/guest/page/profile_page_response', $data);
        } else {
            $cond1                  = array('app_id' => $emp_id);
            $cond2                  = array('emp_id' => $emp_id);
            $data['emp_id']         = $emp_id;
            $data['religion']       = $this->promo_model->selectAll('religion');
            $data['weight']         = $this->promo_model->selectAll('weight');
            $data['height']         = $this->promo_model->selectAll('height');
            $data['attainment']     = $this->promo_model->selectAll('attainment');
            $data['school']         = $this->promo_model->selectAll('school');
            $data['course']         = $this->promo_model->selectAll('course');
            $data['training']       = $this->promo_model->selectAll_tcA('application_seminarsandeligibility', $cond1);
            $data['character']      = $this->promo_model->selectAll_tcA('application_character_ref', $cond1);
            $data['appraisal']      = $this->promo_model->selectAll_tcA('appraisal_details', $cond2);
            $data['application']    = $this->promo_model->selectAll_tcR('application_details', $cond1);
            $data['appHist']        = $this->promo_model->selectAll_tcA('application_history', array('app_id' => $emp_id, 'phase' => 'Examination', 'status' => 'completed'));
            $data['positions']      = $this->promo_model->positions();
            $data['app']            = $this->promo_model->selectAll_tcR('applicant', $cond1);
            $data['address']        = $this->promo_model->addressBoholCebu();
            $data['row']            = $this->promo_model->promoDetails_wttR($cond, 'employee3', 'promo_record');
            $data['empHistory']     = $this->promo_model->selectAll_tcA('application_employment_history', $cond1);
            $data['benefits']       = $this->promo_model->selectAll_tcR('applicant_otherdetails', $cond1);
            $data['supervisor']     = $this->promo_model->selectAll_tcA('leveling_subordinates', array('subordinates_rater' => $emp_id));
            $data['docFile']        = $this->promo_model->selectAll_tcoA('201document', array('promo' => 'yes'), '201_name');
            $data['remarks']        = $this->promo_model->selectAll_tcR('remarks', $cond2);
            $data['clearance']      = $this->promo_model->clearanceHistory($emp_id);
            $data['transfer']       = $this->promo_model->selectAll_tcA('employee_transfer_details', $cond2);
            $data['blacklist']      = $this->promo_model->selectAll_tcA('blacklist', $cond1);
            $data['userAccount']    = $this->promo_model->selectAll_tcA('users', array('emp_id' => $emp_id, 'usertype' => 'employee'));
            $data['request']        = $input['id'];

            $this->load->view('promo/guest/page/profile_page_response', $data);
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
            $link       = '<a href="' . base_url('guest/promo/profile/' . $empId) . '" target="_blank">' . $name . '</a>';
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

    public function getSelect()
    {
        $input = $this->input->post(NULL, TRUE);
        if ($input['action'] == 'getCompany') {
            if ($input['value'] != '') {
                $row = $this->promo_model->selectAll_tcA_tk('promo_locate_company', array('agency_code' => $input['value']));
                $comp = 'company_name';
            } else {
                $row = $this->promo_model->selectAll('locate_promo_company');
                $comp = 'pc_name';
            }

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

            $type   = ($input['value'] == 'ROVING') ? 'checkbox' : 'radio';
            $table  = ($input['contract'] == 'current') ? 'promo_record' : 'promo_history_record';
            $bus    = $this->promo_model->locate_promo_bu($this->hrCode);
            foreach ($bus as $bu) {

                $hasBu      = $this->promo_model->empStores($table, $input['emp_id'], $input['record_no'], $bu['bunit_field']);
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
                $id         = [];
                foreach ($input['value'] as $value) {
                    $val    = explode('|', $value);
                    $id[]   = $val['0'];
                }
                $department = $this->promo_model->whereIN_stcd('DISTINCT', 'dept_name', 'locate_promo_department', 'bunit_id', $id);
                echo '<option value="">Select Department</option>';
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

            $vendor = $this->promo_model->selectAll_tcA('promo_vendor_lists', array('department' => $input['value']));
            echo '<option value="">Select Vendor</option>';
            foreach ($vendor as $value) {
                echo '<option value="' . $value['vendor_code'] . '">' . $value['vendor_name'] . '</option>';
            }
        } else if ($input['action'] == 'getDuration') {

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
                    $result = 'success';
                    $message = "1 day";
                }
            } else {
                $result = 'failed';
                $message = 'Startdate is blank!';
            }
            echo json_encode(['result' => $result, 'message' => $message]);
        } else if ($input['action'] == 'getSupervisor_bc') {
            $bc = $this->promo_model->selectAll_tcA('locate_business_unit', array('company_code' => $input['value']));
            echo '<option value="">Select Business Unit</option>';
            foreach ($bc as $value) {
                echo '<option value="' . $input['value'] . '|' . $value['bunit_code'] . '">' . $value['business_unit'] . '</option>';
            }
        } else if ($input['action'] == 'getSupervisor_dc') {
            $val = explode('|', $input['value']);
            $cc = $val[0];
            $bc = $val[1];
            $query = $this->promo_model->selectAll_tcA('locate_department', array('company_code' => $cc));
            echo '<option value="">Select Department</option>';
            foreach ($query as $value) {
                if ($value['bunit_code'] == $bc) {
                    echo '<option value="' . $input['value'] . '|' . $value['dept_code'] . '">' . $value['dept_name'] . '</option>';
                }
            }
        } else if ($input['action'] == 'getSupervisor_sc') {
            $val = explode('|', $input['value']);
            $cc = $val[0];
            $bc = $val[1];
            $dc = $val[2];
            $query = $this->promo_model->selectAll_tcA('locate_section', array('company_code' => $cc));
            echo '<option value="">Select Section</option>';
            foreach ($query as $value) {
                if ($value['bunit_code'] == $bc && $value['dept_code'] == $dc) {
                    echo '<option value="' . $input['value'] . '|' . $value['section_code'] . '">' . $value['section_name'] . '</option>';
                }
            }
        } else if ($input['action'] == 'getSupervisor_ssc') {
            $val = explode('|', $input['value']);
            $cc = $val[0];
            $bc = $val[1];
            $dc = $val[2];
            $sc = $val[3];
            echo '<option value="">Select Sub-Section</option>';
            $query = $this->promo_model->selectAll_tcA('locate_sub_section', array('company_code' => $cc));
            foreach ($query as $value) {
                if ($value['bunit_code'] == $bc && $value['dept_code'] == $dc && $value['section_code'] == $sc) {
                    echo '<option value="' . $input['value'] . '|' . $value['sub_section_code'] . '">' . $value['sub_section_name'] . '</option>';
                }
            }
        } else if ($input['action'] == 'getSupervisor_uc') {
            $val    = explode('|', $input['value']);
            $cc     = $val[0];
            $bc     = $val[1];
            $dc     = $val[2];
            $sc     = $val[3];
            $ssc    = $val[4];
            echo '<option value="">Select Sub-Section</option>';
            $query  = $this->promo_model->selectAll_tcA('locate_unit', array('company_code' => $cc));
            foreach ($query as $value) {
                if ($value['bunit_code'] == $bc && $value['dept_code'] == $dc && $value['section_code'] == $sc && $value['sub_section_code'] == $ssc) {
                    echo '<option value="' . $input['value'] . '|' . $value['unit_code'] . '">' . $value['unit_name'] . '</option>';
                }
            }
        } else if ($input['action'] == 'getDutydays') {
            $i = 0;
            $dutyDays = '';
            $days = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
            foreach ($days as $value) {
                if ($value != substr($input['value'], 0, 3)) {
                    $i++;
                    $dutyDays = ($i == 1) ? $value : $dutyDays . ', ' . $value;
                }
            }
            echo $dutyDays;
        } else if ($input['action'] == 'filterDepartment') {
            if (isset($input['value'])) {
                $val    = explode('|', $input['value']);
                $id     = $val['0'];
                echo '<option value="">Department...</option>';
                $query = $this->promo_model->selectAll_tcA('locate_promo_department', array('bunit_id' => $id));
                foreach ($query as $value) {
                    echo '<option value="' . $value['dept_name'] . '">' . $value['dept_name'] . '</option>';
                }
            } else {
                echo '<option value="">Department...</option>';
            }
        } else if ($input['action'] == 'getQbeDept') {
            if (isset($input['value'])) {
                $val    = explode('|', $input['value']);
                $id     = $val['0'];
                echo '<option value="">Select Department</option>';
                $query = $this->promo_model->selectAll_tcA('locate_promo_department', array('bunit_id' => $id));
                foreach ($query as $value) {
                    echo '<option value="' . $value['dept_name'] . '">' . $value['dept_name'] . '</option>';
                }
            } else {
                echo '<option value="">Select Department</option>';
            }
        }
    }

    public function newPromo()
    {
        $data = [];
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        $query = $this->promo_model->newPromo();
        foreach ($query as $row) {
            $i = 0;
            $stores = '';
            foreach ($bus as $bu) {
                $hasBu = $this->promo_model->empStores('promo_record', $row['emp_id'], $row['record_no'], $bu['bunit_field']);
                if ($hasBu) {
                    $i++;
                    $stores = ($i == 1) ? $bu['bunit_acronym'] : $stores . ', ' . $bu['bunit_acronym'];
                }
            }
            $name = ucwords(strtolower($row['name']));
            $link = '<a href="' . base_url('guest/promo/profile/' . $row['emp_id']) . '" target="_blank">' . $name . '</a>';
            $column = [];
            $column[] = $link;
            $column[] = $row['position'];
            $column[] = $row['emp_type'];
            $column[] = $stores;
            $column[] = $row['promo_department'];
            $column[] = date('m/d/Y', strtotime($row['startdate']));
            $column[] = date('m/d/Y', strtotime($row['eocdate']));
            $data[] = $column;
        }
        echo json_encode(array("data" => $data));
    }

    public function viewBlacklist()
    {
        $input  = $this->input->post(NULL, TRUE);
        $data   = [];
        $order  = array('b.app_id', 'b.name', 'b.reportedBy', 'b.date_blacklisted', 'b.reason');
        $list   = $this->promo_model->make_datatables('viewBlacklist', $order, $order[1], 'ASC', $input);
        foreach ($list as $row) {

            $dateBL = '';
            if ($row['date_blacklisted'] != '0000-00-00' || $row['date_blacklisted'] != null || $row['date_blacklisted'] != '') {
                $dateBL = date('m/d/Y', strtotime($row['date_blacklisted']));
            }
            $column     = [];
            $column[]   = $row['app_id'];
            $column[]   = $row['name'];
            $column[]   = $row['reportedby'];
            $column[]   = $dateBL;
            $column[]   = $row['reason'];
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
            $column     = [];
            $column[]   = '<a href="' . base_url('guest/promo/profile/' . $row['emp_id']) . '" target="_blank">' . $name . '</a>';
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
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
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
}
