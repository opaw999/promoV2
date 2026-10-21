<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Promo_model extends CI_Model
{
    public $db;
    public $tk;
    public $usertype;
    public $userId;
    public $today;
    public $hrCode;
    function __construct()
    {
        parent::__construct();
        $this->db       = $this->load->database('default', TRUE);
        $this->tk       = $this->load->database('timekeeping', TRUE);
        $this->userId   = $_SESSION['emp_id'];
        $cond   = array('emp_id' => $_SESSION['emp_id']);
        $cc     = $this->selectAll_tcR('employee3', $cond)['company_code'];
        $_SESSION['hrCode'] = ($cc != '07') ? 'asc' : 'cebo';
        $this->hrCode       = $_SESSION['hrCode'];
        $this->usertype     = $this->get_usertype();
        $this->today        = date('Y-m-d');
    }

    public function get_usertype()
    {
        $query = $this->db->select('usertype')
            ->get_where('promo_user', array('emp_id' => $this->userId, 'user_status' => 'active'));
        return $query->row_array()['usertype'];
    }

    public function user_info($emp_id)
    {
        $query = $this->db->select('*')
            ->from('applicant')
            ->join('employee3', 'applicant.app_id = employee3.emp_id')
            ->where('employee3.emp_id', $emp_id)
            ->get();
        return $query->row_array();
    }

    public function promo_menu()
    {
        $condition = array($this->usertype => 'yes', 'menu_status' => 'active');
        $query = $this->db->select('*')
            ->order_by('menu_name')
            ->get_where('promoV2_menu', $condition);
        return $query->result_array();
    }

    public function promo_submenu($menu_id)
    {
        $query = $this->db->where('menu_id', $menu_id)
            ->where($this->usertype, 'yes')
            ->where('submenu_status', 'active')
            ->order_by('submenu_name', 'ASC')
            ->get('promoV2_submenu');
        return $query->result_array();
    }

    public function locate_promo_bu($hrd_location = null)
    {
        $this->db->select('*')
            ->from('locate_promo_business_unit');
        if ($hrd_location !== null) {
            $this->db->where('hrd_location', $hrd_location);
        }
        $this->db->where('status', 'active');
        $this->db->order_by('bunit_name');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function empStores($table, $emp_id, $record_no, $field)
    {
        $query = $this->db->select('COUNT(promo_id) AS num')
            ->get_where($table, array('emp_id' => $emp_id, 'record_no' => $record_no, $field => 'T'));
        return $query->row_array()['num'];
    }

    public function selectAll_tcoA($table, $condition, $order = null)
    {
        $this->db->where($condition);
        if ($order !== null) {
            $this->db->order_by($order);
        }
        $query = $this->db->get($table);
        return $query->result_array();
    }

    public function selectAll($table)
    {
        $query = $this->db->get_where($table);
        return $query->result_array();
    }

    public function selectAll_tk($table)
    {
        $query = $this->tk->get_where($table);
        return $query->result_array();
    }

    public function selectAll_tcA($table, $array)
    {
        $query = $this->db->get_where($table, $array);
        return $query->result_array();
    }

    public function selectAll_tcR($table, $array)
    {
        $query = $this->db->get_where($table, $array);
        return $query->row_array();
    }

    public function selectAll_tcA_tk($table, $array)
    {
        $query = $this->tk->get_where($table, $array);
        return $query->result_array();
    }

    public function selectAll_tcR_tk($table, $array)
    {
        $query = $this->tk->get_where($table, $array);
        return $query->row_array();
    }

    public function update_twdA($table, $where, $data)
    {
        return $this->db->where($where)
            ->update($table, $data);
    }

    public function update_twdA_tk($table, $where, $data)
    {
        return $this->tk->where($where)
            ->update($table, $data);
    }

    public function insert_tdA($table, $data)
    {
        $this->db->insert($table, $data);
        return $this->db->insert_id();
    }

    public function insert_tdA_tk($table, $data)
    {
        return $this->tk->insert($table, $data);
    }

    public function delete_tcr_row($table, $array)
    {
        return $this->db->delete($table, $array);
    }

    public function delete_tc_tk($table, $array)
    {
        return $this->tk->delete($table, $array);
    }

    public function queryString($sql)
    {
        return $this->db->query($sql);
    }

    public function join_ttjcoA($select, $table1, $table2, $join, $left = null, $condition = null, $order = null, $limit = null)
    {
        $this->db->select($select)
            ->from($table1 . ' as t1');
        if ($left !== null) {
            $this->db->join($table2 . ' as t2', $join, $left);
        } else {
            $this->db->join($table2 . ' as t2', $join);
        }
        if ($condition !== null) {
            $this->db->where($condition);
        }
        if ($order !== null) {
            list($orderColumn, $orderDirection) = explode('|', $order);
            $this->db->order_by($orderColumn, $orderDirection);
        }
        if ($limit !== null) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function join_ttjcoR($select, $table1, $table2, $join, $left = null, $condition = null)
    {
        $this->db->select($select)
            ->from($table1 . ' as t1');
        if ($left !== null) {
            $this->db->join($table2 . ' as t2', $join, $left);
        } else {
            $this->db->join($table2 . ' as t2', $join);
        }
        if ($condition !== null) {
            $this->db->where($condition);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getSeverData($row, $location, $field)
    {
        if ($location == 'CORPORATE') {
            $database = $this->db;
        } else if ($location == 'al_tal') {
            $database = $this->load->database('talibon', TRUE);
        } else if ($location == 'al_tub') {
            $database = $this->load->database('tubigon', TRUE);
        } else {
            $database = $this->db;
        }
        if ($row == 1) {
            $database->select('*')
                ->from('employee3 as e')
                ->join('promo_record as p', 'e.emp_id=p.emp_id AND e.record_no=p.record_no ')
                ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
                ->where($field, 'T');
            if ($field == 'al_tub') {
                $database->or_where('fr_tubigon', 'T');
            }
            $query = $database->get();
        } else if ($row == 2) {
            $database->select('*')
                ->from('employmentrecord_ as e')
                ->join('promo_history_record as p', 'e.emp_id=p.emp_id AND e.record_no=p.record_no ')
                ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
                ->where($field, 'T');
            if ($field == 'al_tub') {
                $database->or_where('fr_tubigon', 'T');
            }
            $query = $database->get();
        } else if ($row == 3) {
            $database->select('*')
                ->from('employee3 as e')
                ->join('promo_record as p', 'e.emp_id = p.emp_id AND e.record_no = p.record_no')
                ->join('timekeeping.promo_sched_emp as t', 'e.emp_id = t.empId AND e.record_no = t.recordNo')
                ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
                ->where($field, 'T');
            if ($field == 'al_tub') {
                $database->or_where('fr_tubigon', 'T');
            }
            $database->group_by('e.record_no');
            $query = $database->get();
        }

        return $query->result_array();
    }

    public function updateSeverData($query, $field, $data)
    {
        $serverUpdate = false;
        if ($field == 'al_tal') {
            $database = $this->load->database('talibon', TRUE);
        } else if ($field == 'al_tub') {
            $database = $this->load->database('tubigon', TRUE);
        }

        $eFields = array(
            'record_no',
            'emp_id',
            'emp_no',
            'emp_pins',
            'payroll_no',
            'startdate',
            'eocdate',
            'emp_type',
            'current_status',
            'position',
            'sub_status'
        );

        $pFields = array(
            'record_no',
            'emp_id',
            'agency_code',
            'promo_company',
            'promo_department',
            'al_tag',
            'al_tal',
            'icm',
            'pm',
            'al_tub',
            'alta_citta',
            'fr_tubigon',
            'al_panglao',
            'promo_type',
            'type'
        );



        $tFields = array('empId', 'recordNo', 'statCut', 'date_setup');

        if ($data == 'data1') {

            $pFields[] = 'hr_location';

            $eFields[] = ($data == 'data1') ? 'name' : 'names';
            foreach ($query as $row) {

                $data1      = [];
                $data2      = [];
                $empId      = $row['emp_id'];
                $recordNo   = $row['record_no'];
                $cond       = "(emp_id = '$empId' OR record_no = '$recordNo')";
                $delete     = $database->delete('employee3', $cond);
                $delete     = $database->delete('promo_record', $cond);
                foreach ($row as $field => $value) {
                    if (in_array($field, $eFields)) {
                        $data1[$field] = $value;
                    }
                    if (in_array($field, $pFields)) {
                        $data2[$field] = $value;
                    }
                }
                $insert1 = $database->insert('employee3', $data1);
                $insert2 = $database->insert('promo_record', $data2);
            }
            $serverUpdate = true;
        } else if ($data == 'data2') {

            $eFields[] = ($data == 'data1') ? 'name' : 'names';
            foreach ($query as $row) {

                $data1      = [];
                $data2      = [];
                $empId      = $row['emp_id'];
                $recordNo   = $row['record_no'];

                // $cond       = "(emp_id = '$empId' OR record_no = '$recordNo')";
                // $delete     = $database->delete('employmentrecord_', $cond);
                // $delete     = $database->delete('promo_history_record', $cond);
                foreach ($row as $field => $value) {
                    if (in_array($field, $eFields)) {

                        $data1[$field] = $value;
                    }
                    if (in_array($field, $pFields)) {
                        $data2[$field] = $value;
                    }
                }
                $cond = array('emp_id' => $empId, 'record_no' => $recordNo);
                $sql = $database->get_where('employmentrecord_', $cond);
                $chk = $sql->row_array();
                if (!$chk) {
                    $database->insert('employmentrecord_', $data1);
                }

                $cond = array('emp_id' => $empId, 'record_no' => $recordNo);
                $sql = $database->get_where('promo_history_record', $cond);
                $chk = $sql->row_array();
                if (!$chk) {
                    $database->insert('promo_history_record', $data2);
                }
            }
            $serverUpdate = true;
        } else {
            // $cond = "(empId != '' OR empId = '' OR recordNo != '' OR recordNo = '')";
            // $database->delete('timekeeping.promo_sched_emp', $cond);
            foreach ($query as $row) {

                $data1      = [];
                $empId      = $row['empId'];
                $recordNo   = $row['recordNo'];
                foreach ($row as $field => $value) {
                    if (in_array($field, $tFields)) {
                        $data1[$field] = $value;
                    }
                }

                $cond   = array('empId' => $empId, 'recordNo' => $recordNo);
                $sql    = $database->get_where('timekeeping.promo_sched_emp', $cond);
                $chk    = $sql->row_array();
                if ($chk) {
                    $database->where($cond)
                        ->update('timekeeping.promo_sched_emp', $data1);
                } else {
                    $database->insert('timekeeping.promo_sched_emp', $data1);
                }
            }
            $serverUpdate = true;
        }

        return $serverUpdate;
    }

    public function whereIN_stcd($distinct, $selected, $table, $column, $data)
    {
        if (!empty($distinct)) {
            $this->db->distinct();
        }
        $this->db->select($selected)
            ->from($table)
            ->where_in($column, $data);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function selectDistinct($selected, $table, $condition)
    {
        $this->db->distinct()
            ->select($selected)
            ->from($table)
            ->where($condition);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function searchKey_dstd($distinct = null, $selected, $table, $data)
    {
        if ($distinct !== null) {
            $this->db->distinct();
        }
        $this->db->select($selected)
            ->from($table)
            ->like($data, 'both')
            ->order_by($selected)
            ->limit(10);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function positions()
    {
        $query = $this->db->select('*')
            ->from('position_leveling')
            ->where('position_group', 'Promo')
            ->order_by('position_title')
            ->get();
        return $query->result_array();
    }

    public function addressBoholCebu()
    {
        $query = $this->db->select('brgy_name, town_name, prov_name')
            ->from('barangay')
            ->join('town', 'barangay.town_id = town.town_id')
            ->join('province', 'town.prov_id = province.prov_id')
            ->get();
        return $query->result_array();
    }

    public function active_promoStore($field)
    {
        $query = $this->db->select('count(e.record_no) as num')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id = e.emp_id and p.record_no = e.record_no')
            ->where('current_status', 'Active')
            ->where('hr_location', $this->hrCode)
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->where($field, 'T')
            ->get();
        return $query->row_array();
    }

    public function activeTotal()
    {
        $query = $this->db->select('*')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id = e.emp_id and p.record_no = e.record_no')
            ->where('current_status', 'Active')
            ->where('hr_location', $this->hrCode)
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->get();
        return $query->result_array();
    }

    public function newPromo()
    {
        $query = $this->db->select('*')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id = e.emp_id AND p.record_no = e.record_no')
            ->where('startdate >= DATE_SUB(NOW(), INTERVAL 1 MONTH)')
            ->where('startdate <= NOW()')
            ->where('current_status', 'Active')
            ->where('hr_location', $this->hrCode)
            ->where('tag_as', 'new')
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->get();
        return $query->result_array();
    }

    public function failedEpas()
    {
        $this->db->select('*')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
            ->join('appraisal_details as a', 'a.emp_id=e.emp_id and a.record_no=e.record_no')
            ->where('numrate <', 85)
            ->where('hr_location', $this->hrCode)
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->where_in('current_status', array('Active', 'End of Contract', 'Ad-Resigned', 'V-Resigned'))
            ->where('eocdate <=', date('Y-m-d', strtotime('+5 month')))
            ->group_by('e.emp_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function eocToday()
    {
        $query = $this->db->select('*')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id = e.emp_id AND p.record_no = e.record_no')
            ->where('eocdate', $this->today)
            ->where('hr_location', $this->hrCode)
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->get();
        return $query->result_array();
    }

    public function getName_employee3($emp_id)
    {
        $query = $this->db->select('name')
            ->from('employee3')
            ->where('emp_id', $emp_id)
            ->get();
        return ucwords(strtolower($query->row_array()['name']));
    }

    public function birthdayToday($month_day)
    {
        $this->db->select('*')
            ->from('employee3 AS e')
            ->join('promo_record AS p', 'p.emp_id=e.emp_id AND p.record_no=e.record_no')
            ->join('applicant as a', 'a.app_id = e.emp_id')
            ->where_in('e.emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->where('hr_location', $this->hrCode)
            ->where('current_status', 'Active')
            ->like('birthdate', $month_day, 'before');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function searchPromo($str)
    {
        $this->db->select('*')
            ->from('employee3 AS e')
            ->join('promo_record AS p', 'p.emp_id=e.emp_id AND p.record_no=e.record_no')
            ->join('applicant as a', 'a.app_id = e.emp_id')
            ->where('hr_location', $this->hrCode)
            ->where_in('e.emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->group_start()
            ->like('e.name', $str, 'both')
            ->or_like('e.emp_id', $str, 'both')
            ->or_like('a.lastname', $str, 'both')
            ->or_like('a.firstname', $str, 'both')
            ->group_end()
            ->limit(100);
        $query = $this->db->get();
        if ($query->num_rows() != 0) {
            return $query->result_array();
        } else {
            $this->db->select('*')
                ->from('employee3 AS e')
                ->join('applicant as a', 'a.app_id = e.emp_id')
                ->group_start()
                ->like('e.name', $str, 'both')
                ->or_like('e.emp_id', $str, 'both')
                ->or_like('a.lastname', $str, 'both')
                ->or_like('a.firstname', $str, 'both')
                ->group_end();
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    public function generateQbe($input)
    {
        $this->db->select('*')
            ->from('employee3 AS e')
            ->join('promo_record AS p', 'p.emp_id=e.emp_id AND p.record_no=e.record_no')
            ->join('applicant as a', 'a.app_id = e.emp_id')
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'));
        if (isset($input['name']) && $input['name'] != '') {
            $this->db->group_start()
                ->like('firstname', $input['name'], 'both')
                ->or_like('lastname', $input['name'], 'both')
                ->or_like('name', $input['name'], 'both')
                ->group_end();
        }
        if (isset($input['home_address']) && $input['home_address'] != '') {
            $this->db->group_start()
                ->like('home_address', $input['home_address'], 'both')
                ->group_end();
        }
        if (isset($input['gender']) && $input['gender'] != '') {
            $this->db->where('gender', $input['gender']);
        }
        if (isset($input['religion']) && $input['religion'] != '') {
            $this->db->where('religion', $input['religion']);
        }
        if (isset($input['civilstatus']) && $input['civilstatus'] != '') {
            $this->db->where('civilstatus', $input['civilstatus']);
        }
        if (isset($input['school']) && $input['school'] != '') {
            $this->db->where('school', $input['school']);
        }
        if (isset($input['attainment']) && $input['attainment'] != '') {
            $this->db->where('attainment', $input['attainment']);
        }
        if (isset($input['course']) && $input['course'] != '') {
            $this->db->where('course', $input['course']);
        }
        if (isset($input['height']) && $input['height'] != '') {
            $this->db->where('height', $input['height']);
        }
        if (isset($input['weight']) && $input['weight'] != '') {
            $this->db->where('weight', $input['weight']);
        }
        if (isset($input['bloodtype']) && $input['bloodtype'] != '') {
            $this->db->where('bloodtype', $input['bloodtype']);
        }
        if (isset($input['position']) && $input['position'] != '') {
            $this->db->where('position', $input['position']);
        }

        if (isset($input['agency_code']) && $input['agency_code'] != '') {
            $this->db->where('agency_code', $input['agency_code']);
        }
        if (isset($input['promo_company']) && $input['promo_company'] != '') {
            $this->db->where('promo_company', $input['promo_company']);
        }
        if (isset($input['store']) && $input['store'] != '') {
            $field = explode('|', $input['store']);
            $this->db->where($field[1], 'T');
        }
        if (isset($input['promo_department']) && $input['promo_department'] != '') {
            $this->db->where('promo_department', $input['promo_department']);
        }
        if (isset($input['promo_type']) && $input['promo_type'] != '') {
            $this->db->where('promo_type', $input['promo_type']);
        }
        if (isset($input['type']) && $input['type'] != '') {
            $this->db->where('type', $input['type']);
        }
        if (isset($input['emp_type']) && $input['emp_type'] != '') {
            $this->db->where('emp_type', $input['emp_type']);
        }
        if (isset($input['current_status']) && $input['current_status'] != '') {
            $this->db->where('current_status', $input['current_status']);
        }

        $this->db->where('hr_location', $this->hrCode);
        $this->db->order_by('name');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get201_file($input, $emp_id, $page = null, $others = null)
    {
        $this->db->select('*')
            ->from($input['tableName']);
        if ($others !== null) {
            $this->db->where('requirement_name !=', 'Regularization')
                ->where('requirement_name !=', 'Job Transfers');
        } else {
            $this->db->where('requirement_name', $input['requirementName']);
        }
        $this->db->where($input['empField'], $emp_id);
        if ($input['tableName'] == 'applicant_violation') {
            $this->db->order_by('v_no DESC');
        } else {
            $this->db->order_by('no DESC');
        }
        if ($page !== null) {
            $this->db->limit(1, $page);
        }
        $query = $this->db->get();
        return ($page !== null) ? $query->row_array() : $query->result_array();
    }

    public function get201_resignation($emp_id, $page = null)
    {
        $this->db->select('*')
            ->from('termination as scp')
            ->where('emp_id', $emp_id)
            ->order_by('termination_no DESC');
        if ($page !== null) {
            $this->db->limit(1, $page);
        }
        $query = $this->db->get();
        return ($page !== null) ? $query->row_array() : $query->result_array();
    }

    public function addBl($input)
    {
        $query = $this->db->select('*')
            ->from('applicant as a')
            ->join('employee3 AS e', 'e.emp_id=a.app_id', 'left')
            ->where('app_id', $input['emp_id'])
            ->get();
        return $query->row_array();
    }

    public function reportedbyBl($input)
    {
        $query = $this->db->select('employee3.emp_id, name')
            ->from('employee3')
            ->join('users', 'employee3.emp_id = users.emp_id')
            ->group_start()
            ->where('usertype', 'administrator')
            ->or_where('usertype', 'placement1')
            ->or_where('usertype', 'placement2')
            ->or_where('usertype', 'placement3')
            ->or_where('usertype', 'placement4')
            ->or_where('usertype', 'nesco')
            ->or_where('usertype', 'franchise')
            ->group_end()
            ->group_start()
            ->like('name', $input['str'], 'both')
            ->or_like('employee3.emp_id', $input['str'])
            ->group_end()
            ->group_by('users.emp_id')
            ->order_by('name', 'ASC')
            ->limit(10)
            ->get();
        return $query->result_array();
    }

    public function clearanceHistory($emp_id)
    {
        $query = $this->db->select('*')
            ->from('secure_clearance_promo as scp')
            ->join('secure_clearance_promo_details as scpd', 'scp.scpr_id=scpd.scpr_id')
            ->where('scp.emp_id', $emp_id)
            ->order_by('scp.date_added', 'desc')
            ->get();
        return $query->result_array();
    }

    public function promoDetails_wttA($condition, $empTable, $recordTable)
    {
        $query = $this->db->select('*')
            ->from($empTable . ' as e')
            ->join($recordTable . ' as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
            ->where($condition)
            ->order_by('startdate DESC, e.record_no DESC')
            ->get();
        return $query->result_array();
    }

    public function promoDetails_wttA_asc($condition, $empTable, $recordTable)
    {
        $query = $this->db->select('*')
            ->from($empTable . ' as e')
            ->join($recordTable . ' as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
            ->where($condition)
            ->order_by('e.startdate ASC')
            ->get();
        return $query->result_array();
    }

    public function promoDetailsMerged($condition)
    {
        $sql = "SELECT * FROM (
            SELECT e.emp_id as empId,e.record_no as recNo,e.startdate,e.eocdate,e.position,e.current_status,e.name as emp_name,p.*
            FROM employee3 e JOIN promo_record p ON p.emp_id = e.emp_id AND p.record_no = e.record_no WHERE $condition

            UNION ALL

            SELECT e.emp_id as empId,e.record_no as recNo,e.startdate,e.eocdate,e.position,e.current_status,e.names as emp_name,p.* 
            FROM employmentrecord_ e JOIN promo_history_record p ON p.emp_id = e.emp_id AND p.record_no = e.record_no WHERE $condition
        ) AS combined ORDER BY startdate ASC";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function promoDetails_wttR($condition, $empTable, $recordTable)
    {
        $query = $this->db->select('*')
            ->from($empTable . ' as e')
            ->join($recordTable . ' as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
            ->where($condition)
            ->get();
        return $query->row_array();
    }

    public function searchPromoClearance($input)
    {
        if ($input['process'] == 'secure') {
            $query = $this->db->select('*')
                ->from('employee3 as e')
                ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
                ->where('hr_location', $this->hrCode)
                ->where('sub_status !=', 'Cleared')
                ->where('current_status !=', 'blacklisted')
                ->where('current_status !=', 'Deceased')
                ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
                ->group_start()
                ->like('name', $input['str'], 'both')
                ->or_like('e.emp_id', $input['str'])
                ->group_end()
                ->order_by('name')
                ->limit(10)
                ->get();
        } else if ($input['process'] == 'upload') {
            $tables = [
                ['employee3 as e', 'promo_record as p'],
                ['employmentrecord_ as e', 'promo_history_record as p']
            ];

            // foreach ($tables as $tbl) {
            $query = $this->db->select('*')
                ->from('secure_clearance_promo as s')
                ->join('employee3 as e', 'e.emp_id = s.emp_id')
                ->join('promo_record as p', 'p.emp_id = s.emp_id')
                ->where('hr_location', $this->hrCode)
                ->where('status', 'Pending')
                ->where_in('emp_type', ['Promo', 'Promo-NESCO', 'Promo-EasyL'])
                ->group_start()
                ->like('name', $input['str'], 'both')
                ->or_like('e.emp_id', $input['str'])
                ->group_end()
                ->order_by('name')
                ->limit(10)
                ->get();

            // if ($query->num_rows() > 0) {
            // return $query->result_array();
            // }
            // }
            // return [];
        } else {
            $query = $this->db->select('*')
                ->from('secure_clearance_promo as s')
                ->join('employee3 as e', 'e.emp_id=s.emp_id')
                ->join('promo_record as p', 'p.emp_id=s.emp_id')
                ->where('hr_location', $this->hrCode)
                ->where('status', 'Pending')
                ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
                ->group_start()
                ->like('name', $input['str'], 'both')
                ->or_like('e.emp_id', $input['str'])
                ->group_end()
                ->order_by('name')
                ->limit(10)
                ->get();
        }

        return $query->result_array();
    }

    public function searchPromoContract($input)
    {
        $query = $this->db->select('*')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
            ->where('hr_location', $this->hrCode)
            ->where('current_status !=', 'blacklisted')
            ->where('current_status !=', 'Deceased')
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->group_start()
            ->like('name', $input['str'], 'both')
            ->or_like('e.emp_id', $input['str'])
            ->group_end()
            ->order_by('name')
            ->limit(10)
            ->get();

        return $query->result_array();
    }

    public function searchPromoOutlet($input)
    {
        if ($input['process'] == 'addOutlet' || $input['process'] == 'transferOutlet') {
            $query = $this->db->select('*')
                ->from('employee3 as e')
                ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
                ->where('hr_location', $this->hrCode)
                ->where('current_status', 'Active')
                ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
                ->group_start()
                ->like('name', $input['str'], 'both')
                ->or_like('e.emp_id', $input['str'])
                ->group_end()
                ->order_by('name')
                ->limit(10)
                ->get();
        } else if ($input['process'] == 'removeOutlet') {
            $query = $this->db->select('*')
                ->from('employee3 as e')
                ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
                ->where('hr_location', $this->hrCode)
                ->where_in('current_status', array('Active', 'Ad-Resigned', 'V-Resigned'))
                ->where('promo_type', 'ROVING')
                ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
                ->group_start()
                ->like('name', $input['str'], 'both')
                ->or_like('e.emp_id', $input['str'])
                ->group_end()
                ->order_by('name')
                ->limit(10)
                ->get();
        }

        return $query->result_array();
    }

    public function searchPromoExport($input)
    {
        $query = $this->db->select('*')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
            ->where('hr_location', $this->hrCode)
            ->where('current_status !=', 'blacklisted')
            ->where('current_status !=', 'Deceased')
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->group_start()
            ->like('name', $input['str'], 'both')
            ->or_like('e.emp_id', $input['str'])
            ->group_end()
            ->order_by('name')
            ->limit(10)
            ->get();

        return $query->result_array();
    }

    public function searchPromoResignation($input)
    {
        if ($input['process'] == 'addResignation') {
            $query = $this->db->select('*')
                ->from('employee3 as e')
                ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
                ->where('hr_location', $this->hrCode)
                ->where('current_status !=', 'blacklisted')
                ->where('current_status !=', 'Deceased')
                ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
                ->group_start()
                ->like('name', $input['str'], 'both')
                ->or_like('e.emp_id', $input['str'])
                ->group_end()
                ->order_by('name')
                ->limit(10)
                ->get();
        } else {
            $query = $this->db->select('*')
                ->from('users as u')
                ->join('employee3 as e', 'u.emp_id=e.emp_id')
                ->where('usertype', 'supervisor')
                ->group_start()
                ->like('name', $input['str'], 'both')
                ->or_like('e.emp_id', $input['str'])
                ->group_end()
                ->order_by('name')
                ->limit(10)
                ->get();
        }
        return $query->result_array();
    }

    public function searchPromoSetup($input)
    {
        if ($input['process'] == 'setupSubordinate') {
            $query = $this->db->select('*')
                ->from('users as u')
                ->join('employee3 as e', 'u.emp_id=e.emp_id')
                ->where('usertype', 'supervisor')
                ->group_start()
                ->like('name', $input['str'], 'both')
                ->or_like('e.emp_id', $input['str'])
                ->group_end()
                ->order_by('name')
                ->limit(10)
                ->get();
        }
        return $query->result_array();
    }

    public function searchPromoUsers($input)
    {
        if ($input['process'] == 'userAccount') {
            $query = $this->db->select('*')
                ->from('employee3 as e')
                ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
                ->where('hr_location', $this->hrCode)
                ->where('current_status', 'Active')
                ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
                ->group_start()
                ->like('name', $input['str'], 'both')
                ->or_like('e.emp_id', $input['str'])
                ->group_end()
                ->order_by('name')
                ->limit(10)
                ->get();
        } else if ($input['process'] == 'inchargeAccount') {
            $query = $this->db->select('*')
                ->from('users as u')
                ->join('employee3 as e', 'u.emp_id=e.emp_id')
                ->group_start()
                ->where('usertype', 'administrator')
                ->or_where('usertype', 'placement1')
                ->or_where('usertype', 'placement2')
                ->or_where('usertype', 'placement3')
                ->or_where('usertype', 'placement4')
                ->or_where('usertype', 'nesco')
                ->group_end()
                ->group_start()
                ->like('name', $input['str'], 'both')
                ->or_like('e.emp_id', $input['str'])
                ->group_end()
                ->order_by('name')
                ->group_by('e.emp_id')
                ->limit(10)
                ->get();
        }

        return $query->result_array();
    }

    public function searchWitness($string)
    {
        $query = $this->db->select('*')
            ->from('users as u')
            ->join('employee3 as e', 'u.emp_id=e.emp_id')
            ->join('applicant as a', 'a.app_id=e.emp_id')
            ->group_start()
            ->where('usertype', 'administrator')
            ->or_where('usertype', 'placement1')
            ->or_where('usertype', 'placement2')
            ->or_where('usertype', 'placement3')
            ->or_where('usertype', 'placement4')
            ->or_where('usertype', 'nesco')
            ->group_end()
            ->group_start()
            ->like('name', $string, 'both')
            ->or_like('e.emp_id', $string)
            ->group_end()
            ->order_by('name')
            ->group_by('e.emp_id')
            ->limit(10)
            ->get();

        return $query->result_array();
    }


    public function supervisorList($input)
    {
        $list = $this->selectAll_tcA('leveling_subordinates', array('subordinates_rater' => $input['emp_id']));
        $this->db->distinct()
            ->select('name, e.*')
            ->from('employee3 as e')
            ->join('leveling_subordinates as l', 'e.emp_id=l.ratee')
            ->where('current_status', 'Active');
        foreach ($list as $value) {
            $this->db->where('l.ratee !=', $value['ratee']);
        }
        if (isset($input['cc'])) {
            $this->db->where('e.company_code', $input['cc']);
        }
        if (isset($input['bc'])) {
            $this->db->where('e.bunit_code', $input['bc']);
        }
        if (isset($input['dc'])) {
            $this->db->where('e.dept_code', $input['dc']);
        }
        if (isset($input['sc'])) {
            $this->db->where('e.section_code', $input['sc']);
        }
        if (isset($input['ssc'])) {
            $this->db->where('e.sub_section_code', $input['ssc']);
        }
        if (isset($input['uc'])) {
            $this->db->where('e.unit_code', $input['uc']);
        }
        $this->db->order_by('e.name');

        $query = $this->db->get();
        return $query->result_array();
    }


    public function eocApp_p_chk($emp_id, $record_no, $condition)
    {
        $this->db->select('promo_id')
            ->from('promo_history_record')
            ->where('emp_id', $emp_id)
            ->where('record_no', $record_no);
        if (!empty($condition)) {
            $this->db->where($condition);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function promoUserAccounts()
    {
        $this->db->select('*')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
            ->join('users as u', 'u.emp_id=e.emp_id')
            ->where('hr_location', $this->hrCode)
            ->where('usertype', 'employee')
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->order_by('name');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function resignationList()
    {
        $this->db->select('*, t.remarks,t.date_updated,t.added_by')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
            ->join('termination as t', 't.emp_id=e.emp_id')
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->order_by('date DESC');
        $query = $this->db->get();
        return $query->result_array();
    }


    // **************************************** datatables serverside ****************************************
    public function eocList($input)
    {
        $monthplus  = date('Y-m-d', strtotime('+1 month')); //date one month after the current date
        $monthminus = date('Y-m-d', strtotime('-1 month')); //date one month before the current date
        $plus3      = date('Y-m-d', strtotime('+3 month')); //date 3 months after the current date
        $minus3     = date('Y-m-d', strtotime('-3 month')); //date 3 months before the current date
        $today      = date('Y-m-d');                        //current date
        $yesterday  = date("Y-m-d", strtotime('-1 days'));  //yesterdays date
        $last7days  = date('Y-m-d', strtotime('-7 days'));  //7 days before the current date
        $this->db->select('*')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id=e.emp_id and p.record_no=e.record_no')
            ->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'))
            ->where('hr_location', $this->hrCode)
            ->where('current_status !=', 'blacklisted');
        if ($input['store'] != '') {
            $this->db->where($input['store'], 'T');
        }
        if ($input['day'] != '') {
            if ($input['day'] == 'today') {
                $this->db->where('eocdate', $today);
            } else if ($input['day'] == 'yesterday') {
                $this->db->where('eocdate', $yesterday);
            } else if ($input['day'] == 'last7days') {
                $this->db->where("eocdate BETWEEN '$last7days' AND '$today'");
            } else if ($input['day'] == 'last1month') {
                $this->db->where("eocdate BETWEEN '$monthminus' AND '$today'");
            }
        }
        if ($input['day'] == '' && $input['month'] == '' && $input['year'] == '') {
            $this->db->where('eocdate <=', $monthplus);
            $this->db->where('eocdate >=', $minus3);
        }
        if ($input['month'] != '') {
            $this->db->where('MONTH(eocdate)', $input['month']);
        }
        if ($input['year'] != '') {
            $this->db->where('YEAR(eocdate)', $input['year']);
        }
    }

    public function viewBlacklist()
    {
        $this->db->select('b.*')
            ->from('blacklist as b');
    }

    public function checkBlt($input)
    {
        $mn = '';
        if (!empty($input['mn'])) {
            $mn = ' ' . $input['mn'];
        }
        $name1 = $input['ln'] . ' ' . $input['fn'] . $mn;
        $name2 = $input['ln'] . ', ' . $input['fn'] . $mn;
        $name3 = $input['ln'] . ',' . $input['fn'] . $mn;
        $this->db->select('*')
            ->from('blacklist')
            ->group_start()
            ->or_like('name', $name1)
            ->or_like('name', $name2)
            ->or_like('name', $name3)
            ->or_like('name', $input['ln'])
            ->group_end();
    }

    public function checkBl($input)
    {
        $this->db->select('*')
            ->from('applicant as a')
            ->join('employee3 as e', 'e.emp_id = a.app_id', 'left')
            ->where('lastname', $input['ln'])
            ->like('firstname', $input['fn'], 'both')
            ->where('current_status !=', 'blacklisted');
        if (!empty($input['mn'])) {
            $this->db->like('middlename', $input['mn'], 'both');
        }
    }

    public function clearanceList($input)
    {
        $this->db->select('*, s.date_added as dateAdded, s.promo_type')
            ->from('secure_clearance_promo as s')
            ->join('employee3 as e', 'e.emp_id=s.emp_id')
            ->join('promo_record as p', 'p.emp_id=s.emp_id')
            ->where('hr_location', $this->hrCode);
        if (isset($input['month'])) {
            $this->db->where('MONTH(s.date_added)', $input['month']);
        }
        if (isset($input['year'])) {
            $this->db->where('YEAR(s.date_added)', $input['year']);
        }
    }

    public function masterfile($input)
    {
        $this->db->select('*')
            ->from('employee3 as e')
            ->join('promo_record as p', 'p.emp_id = e.emp_id and p.record_no = e.record_no')
            ->where('current_status', 'Active')
            ->where('hr_location', $this->hrCode);
        if (isset($input['store'])) {
            $split = explode('|', $input['store']);
            $this->db->where($split[1], 'T');
        }
        if (isset($input['promo_department']) && $input['promo_department'] != '') {
            $this->db->where('promo_department', $input['promo_department']);
        }
        if (isset($input['promo_type']) && $input['promo_type'] != '') {
            $this->db->where('promo_type', $input['promo_type']);
        }
        if (isset($input['promo_company']) && $input['promo_company'] != '') {
            $this->db->where('promo_company', $input['promo_company']);
        }
        $this->db->where_in('emp_type', array('Promo', 'Promo-NESCO', 'Promo-EasyL'));
    }

    public function make_datatables($functionName, $order, $defaultOrder, $orderType, $input = null)
    {
        $this->$functionName($input);
        $this->filter_data($order, $defaultOrder, $orderType, $input);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function filter_data($order, $defaultOrder, $orderType, $input)
    {
        if (isset($_POST['search']['value']) && $_POST['search']['value'] != '') {
            $searchValue = $_POST['search']['value'];
            $this->db->group_start();
            foreach ($order as $column) {
                if ($column != '' && $column != 'store') {
                    $this->db->or_like($column, $searchValue);
                } else if ($column == 'store') {
                    foreach ($input['lpbu'] as $bus) {

                        if (stripos($bus['bunit_name'], $searchValue) !== false) {
                            $this->db->or_where($bus['bunit_field'], 'T');
                        } else if (stripos($bus['bunit_acronym'], $searchValue) !== false) {
                            $this->db->or_where($bus['bunit_field'], 'T');
                        }
                    }
                }
            }
            $this->db->group_end();
        }
        if (isset($_POST['order'])) {
            $this->db->order_by($order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by($defaultOrder, $orderType);
        }
    }

    public function get_filtered_data($functionName, $order, $defaultOrder, $orderType, $input = null)
    {
        $this->$functionName($input);
        $this->filter_data($order, $defaultOrder, $orderType, $input);
        return $this->db->count_all_results();
    }

    public function get_all_data($functionName, $input = null)
    {
        $this->$functionName($input);
        return $this->db->count_all_results();
    }
    // **************************************** datatables serverside ****************************************

    public function promoCutoff_update($server, $condition, $data)
    {
        if ($server == 'talibon') {
            $database = $this->load->database('talibon', TRUE);
            $database->where($condition)
                ->update('timekeeping.promo_sched_emp', $data);
        } else if ($server == 'tubigon') {
            $database = $this->load->database('tubigon', TRUE);
            $database->where($condition)
                ->update('timekeeping.promo_sched_emp', $data);
        } else if ($server == 'cebu') {
        } else if ($server == 'corporate') {
            return $this->tk->where($condition)
                ->update('promo_sched_emp', $data);
        }
    }

    public function promoCutoff_insert($server, $data)
    {
        if ($server == 'talibon') {
            $database = $this->load->database('talibon', TRUE);
            return $database->insert('timekeeping.promo_sched_emp', $data);
        } else if ($server == 'tubigon') {
            $database = $this->load->database('tubigon', TRUE);
            return $database->insert('timekeeping.promo_sched_emp', $data);
        } else if ($server == 'cebu') {
        } else if ($server == 'corporate') {
            return $this->tk->insert('promo_sched_emp', $data);
        }
    }

    public function promoCutoff_select($server, $condition)
    {
        if ($server == 'talibon') {
            $database = $this->load->database('talibon', TRUE);
            $query = $database->get_where('timekeeping.promo_sched_emp', $condition);
        } else if ($server == 'tubigon') {
            $database = $this->load->database('tubigon', TRUE);
            $query = $database->get_where('timekeeping.promo_sched_emp', $condition);
        } else if ($server == 'cebu') {
        } else if ($server == 'corporate') {
            $query = $this->tk->get_where('promo_sched_emp', $condition);
        }
        return $query->row_array();
    }

    public function months()
    {
        return array(
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        );
    }

    public function field_names()
    {
        return array(
            'payroll_no'                => 'Payroll No',
            'emp_no'                    => 'Employee No',
            'emp_pins'                  => 'Employee PINS',
            'name'                      => 'Full Name',
            'lastname'                  => 'Last Name',
            'firstname'                 => 'First Name',
            'middlename'                => 'Middle Name',
            'suffix'                    => 'Suffix',
            'birthdate'                 => 'Birthday/Age',
            'home_address'              => 'Home Address',
            'city_address'              => 'City Address',
            'religion'                  => 'Religion',
            'civilstatus'               => 'Civil Status',
            'spouse'                    => 'Spouse',
            'noofsiblings'              => 'No of Siblings',
            'siblingOrder'              => 'Sibling Order',
            'gender'                    => 'Gender',
            'school'                    => 'School',
            'attainment'                => 'Attainment',
            'course'                    => 'Course',
            'contactno'                 => 'Contact No',
            'telno'                     => 'Tel No',
            'email'                     => 'Email',
            'facebookAcct'              => 'Facebook Acct',
            'twitterAcct'               => 'Twitter Acct',
            'citizenship'               => 'Citizenship',
            'bloodtype'                 => 'Bloodtype',
            'weight'                    => 'Weight',
            'height'                    => 'Height',
            'mother'                    => 'Mother',
            'father'                    => 'Father',
            'mother_bdate'              => 'Mother Birthday',
            'father_bdate'              => 'Father Birthday',
            'spouse_bdate'              => 'Spouse Birthday',
            'contact_person'            => 'Contact Person',
            'contact_person_address'    => 'Contact Person Address',
            'contact_person_number'     => 'Contact Person Number',
            'guardian'                  => 'Guardian',
            'hobbies'                   => 'Hobbies',
            'specialSkills'             => 'Special Skills'
        );
    }

    public function field_employee()
    {
        return array(
            'startdate'         => 'Startdate',
            'eocdate'           => 'EOCDate',
            'emp_type'          => 'Employee Type',
            'reg_class'         => 'Regular Type',
            'current_status'    => 'CurrentStatus',
            'job_cat'           => 'JobCategory',
            'emp_cat'           => 'EmpCategory',
            'poslevel'          => 'PositionLevel',
            'position'          => 'Position',
            'lodging'           => 'Lodging',
            'comments'          => 'Comments',
            'remarks'           => 'Remarks',
            'pcc'               => 'PCC',
            'aeregular'         => 'Recommended By',
            'username'          => 'Username',
            'date_brief'        => 'Date Briefed',
            'date_hired'        => 'Date Hired/YearsInService'
        );
    }

    public function field_company()
    {
        return array(
            'agency_code'       => 'Agency',
            'promo_company'     => 'Company',
            'promo_department'  => 'Department',
            'bUs'               => 'Store(s)',
            'promo_type'        => 'Promo Type',
            'type'              => 'Contract Type'
        );
    }

    public function field_benefits()
    {
        return array(
            'cedula_no'         => 'Cedula No',
            'sss_no'            => 'SSS No',
            'pagibig_tracking'  => 'Pagibig RTN',
            'pagibig'           => 'Pagibig MID No',
            'philhealth'        => 'Philhealth',
            'tin_no'            => 'TIN No'
        );
    }

    public function hcode()
    {
        return array(
            'colm_contract'         => 1,
            'tal_contract'          => 10,
            'pm_contract'           => 11,
            'icm_contract'          => 12,
            'asc_contract'          => 14,
            'absna_contract'        => 23,
            'absni_contract'        => 23,
            'tub_contract'          => 31,
            'colc_contract'         => 32,
            'alta_contract'         => 33,
            'fr_panglao_contract'   => 36,
            'fr_tubigon_contract'   => 37,
            'al_panglao_contract'   => 38
        );
    }

    public function emp3Fields()
    {
        return array(
            'record_no',
            'tag_as',
            'date_added',
            'added_by',
            'Details',
            'DepCode',
            'Branch',
            'Branchcode',
            'tag_request',
            'sub_status',
            'tag_stats'
        );
    }
}
