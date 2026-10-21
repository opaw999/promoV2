<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Logs extends CI_Controller
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

    public function logs()
    {
        $list = $this->promo_model->selectAll_tcA('logs', array('user' => $this->userId));
        $data = [];
        foreach ($list as $row) {
            $column     = [];
            $column[]   = $row['log_no'];
            $column[]   = $row['activity'];
            $column[]   = date('m/d/Y', strtotime($row['date']));
            $column[]   = date('h:i:s A', strtotime($row['time']));;
            $column[]   = $row['user'];
            $column[]   = $row['username'];
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }

    public function logsAdmin()
    {
        $input  = $this->input->post(NULL, TRUE);
        $data   = [];

        $cond = array('emp_id' => $this->userId);
        $usertype = $this->promo_model->selectAll_tcR('promo_user', $cond)['usertype'];

        $list = $this->promo_model->selectAll_tcA('logs', array('date' => $input['day'], 'user' => $this->userId));
        if ($usertype == 'administrator') {
            $list = $this->promo_model->selectAll_tcA('logs', array('date' => $input['day']));
        }

        foreach ($list as $row) {
            $cond = array('emp_id' => $row['user']);
            $check = $this->promo_model->selectAll_tcR('promo_user', $cond);
            if ($check) {
                $column     = [];
                $column[]   = $row['log_no'];
                $column[]   = $row['activity'];
                $column[]   = date('m/d/Y', strtotime($row['date']));
                $column[]   = date('h:i:s A', strtotime($row['time']));;
                $column[]   = $row['user'];
                $column[]   = $row['username'];
                $data[]     = $column;
            }
        }

        echo json_encode(array('data' => $data));
    }

    public function logsSearch()
    {
        $input  = $this->input->post(NULL, TRUE);
        $str    = trim($input['string']);
        $data   = [];
        $cond = "activity like '%$str%'";
        if ($str == '') {
            $cond = ['date' => date('Y-m-d')];
        }
        $list = $this->promo_model->selectAll_tcA('logs', $cond);
        foreach ($list as $row) {

            $column     = [];
            $column[]   = $row['log_no'];
            $column[]   = $row['activity'];
            $column[]   = date('m/d/Y', strtotime($row['date']));
            $column[]   = date('h:i:s A', strtotime($row['time']));;
            $column[]   = $row['user'];
            $column[]   = $row['username'];
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }
}
