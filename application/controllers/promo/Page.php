<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Page extends CI_Controller
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
        $cond = array('emp_id' => $this->userId, 'user_status' => 'active');
        $promo = $this->promo_model->selectAll_tcR('promo_user', $cond);
        if (!isset($this->userId) && !$promo) {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }

        if ($promo['usertype'] == 'promo3') {
            $segment = $this->uri->segment(4);
            if ($segment === 'profile') {
                $id = $this->uri->segment(5);
                redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms/promoV2/guest/promo/profile/' . $id);
            } else {
                redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms/promoV2/guest');
            }
        }
    }

    public function menu($menu = 'dashboard', $file = 'dashboard', $emp_id = '')
    {
        $cond = ['emp_id' => $this->userId, 'user_status' => 'active'];
        $access = $this->promo_model->selectAll_tcR('promo_user', $cond);
        $data['access'] = $access;
        $m = [];
        if ($access['menu_access']) {
            $m = explode('|', $access['menu_access']);
        }

        $s = [];
        if ($access['submenu_access']) {
            $s = explode('|', $access['submenu_access']);
        }

        // if ($file != '' && $file != 'dashboard' && $file != 'profile') {
        //     $cond = ['routeFile' => $file];
        //     $smId = $this->promo_model->selectAll_tcR('promov2_submenu', $cond)['submenu_id'];
        //     if (!in_array($smId, $s)) {
        //         redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms/promoV2');
        //     }
        // }

        if ($file == 'accessRoles' || $file == 'buList') {
            $cond = ['emp_id' => $this->userId, 'usertype' => 'administrator'];
            $admin = $this->promo_model->selectAll_tcR('promo_user', $cond);
            if (!$admin) {
                redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms/promoV2');
            }
        }

        if (!file_exists(APPPATH . "views/promo/page/$menu/$file.php")) {
            show_404();
        }

        if ($menu == 'dashboard' && $file == 'dashboard') {
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
        } else if ($menu == 'contract' && $file == 'renewContract') {

            if (isset($_SESSION['renewID']) && !empty($_SESSION['renewID'])) {
                unset($_SESSION['renewID']);
            } else {
                header('Location: http://' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . '/hrms/promoV2/promo/page/contract/renewal');
                exit;
            }
        }

        $data['emp_id'] = $emp_id;
        $data['user']   = $this->promo_model->user_info($this->userId);
        $data['menu']   = $menu;
        $data['file']   = $file;
        $this->load->view('promo/template/header', $data);
        $this->load->view('promo/template/menu', $data);
        $this->load->view('promo/page/' . $menu . '/' . $file, $data);
        $this->load->view('promo/template/script');
        $this->load->view('promo/page/' . $menu . '/' . $menu . '_js', $data);
    }

    public function logout()
    {
        $this->nativesession->sess_destroy();
        redirect('http://' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT'] . '/hrms');
    }
}
