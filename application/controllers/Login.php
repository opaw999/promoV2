<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $cond   = array('emp_id' => $_SESSION['emp_id']);
        $cc     = $this->promo_model->selectAll_tcR('employee3', $cond)['company_code'];
        $_SESSION['hrCode'] = ($cc != '07') ? 'asc' : 'cebo';

        $cond = array('emp_id' => $_SESSION['emp_id'], 'user_status' => 'active');
        $promo = $this->promo_model->selectAll_tcR('promo_user', $cond);

        if (!isset($_SESSION['emp_id']) && !$promo) {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }
    }

    public function index()
    {
        redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms/promoV2/promo');
    }
}
