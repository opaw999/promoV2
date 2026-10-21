<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends CI_Controller
{
    public $userId;
    public $hrCode;
    public $today;
    public $adminUser;
    public $ppdf;
    function __construct()
    {
        parent::__construct();
        $this->userId   = $_SESSION['emp_id'];
        $this->hrCode   = $_SESSION['hrCode'];
        $this->today    = date('Y-m-d');
        $this->adminUser = array('01186-2023');
        $this->load->library('ppdf');
        if (!isset($this->userId)) {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }
    }

    public function generateQbe()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'generateQbe';

        $this->load->view('promo/page/page_response', $data);
    }

    public function generatePromoStat()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'generatePromoStat';

        $this->load->view('promo/page/page_response', $data);
    }

    public function generateMonthlyStat()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'generateMonthlyStat';

        $this->load->view('promo/page/page_response', $data);
    }

    public function generateAnnualStat()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'generateAnnualStat';

        $this->load->view('promo/page/page_response', $data);
    }

    public function contractUploadMonitoring()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'contractUploadMonitoring';

        $this->load->view('promo/page/page_response', $data);
    }

    public function generateDueContractsExcel()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'generateDueContractsExcel';

        $this->load->view('promo/page/page_response', $data);
    }

    public function generateDutySched()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'generateDutySched';

        $this->load->view('promo/page/page_response', $data);
    }

    public function dutySchedList()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'dutySchedList';

        $this->load->view('promo/page/page_response', $data);
    }

    public function dutySchedListData()
    {
        $input = $this->input->post(NULL, TRUE);
        $where = "(emp_type = 'Promo' OR emp_type = 'Promo-NESCO' OR emp_type = 'Promo-EasyL')";
        if (!empty($input['store'])) {

            $bunit = explode('|', $input['store']);
            $where .= " AND $bunit[1]= 'T'";
        }
        if (!empty($input['promo_department'])) {
            $where .= " AND promo_department = '$input[promo_department]'";
        } else {

            $dept = $this->promo_model->whereIN_stcd('distinct', 'dept_name', 'locate_promo_department', 'bunit_id', $bunit[0]);
            $where .= ' AND (';
            $i = 0;
            foreach ($dept as $key => $value) {
                $i++;
                $where .= ($i == 1) ? "promo_department = '$value[dept_name]'" : " OR promo_department = '$value[dept_name]'";
            }
            $where .= ')';
        }
        if (!empty($input['promo_company'])) {
            $where .= " AND promo_company = '$input[promo_company]'";
        }
        if (!empty($input['current_status'])) {
            $where .= " AND current_status = '$input[current_status]'";
        }
        $data   = [];
        $bus    = $this->promo_model->locate_promo_bu($this->hrCode);
        $sched  = $this->promo_model->selectAll_tcR('locate_promo_business_unit', array('bunit_id' => $bunit[0]));
        $table1 = 'employee3';
        $table2 = 'promo_record';
        $join   = 't1.emp_id=t2.emp_id AND t1.record_no=t2.record_no';
        $order  = 'name|ASC';
        $query  = $this->promo_model->join_ttjcoA('*', $table1, $table2, $join, null, $where, $order, null);
        foreach ($query as $row) {
            $i = 0;
            $stores = '';
            foreach ($bus as $bu) {
                $hasBu = $this->promo_model->empStores('promo_record', $row['emp_id'], $row['record_no'], $bu['bunit_field']);
                if ($hasBu > 0) {
                    $i++;
                    $stores = ($i == 1) ? $bu['bunit_acronym'] : $stores . ', ' . $bu['bunit_acronym'];
                }
            }
            if ($row[$sched['bunit_specialDays']] != '') {

                $dutySched = $row[$sched['bunit_dutySched']] . ' & ' . $row[$sched['bunit_specialSched']];
                $dutyDays = $row[$sched['bunit_dutyDays']] . ' & ' . $row[$sched['bunit_specialDays']];
            } else {

                $dutySched = $row[$sched['bunit_dutySched']];
                $dutyDays = $row[$sched['bunit_dutyDays']];
            }
            $name = ucwords(strtolower($row['name']));
            $link = '<a href="' . base_url('promo/page/promo/profile/' . $row['emp_id']) . '" target="_blank">' . $name . '</a>';

            $column = [];
            $column[] = $link;
            $column[] = strtoupper($dutyDays);
            $column[] = $dutySched;
            $column[] = $row['dayoff'];
            $column[] = $row['cutoff'];
            $column[] = $stores;
            $column[] = $row['promo_department'];
            $column[] = $row['promo_company'];
            $column[] = $row['position'];
            $column[] = $row['promo_type'];
            $column[] = date('m/d/Y', strtotime($row['startdate'])) . " - " . date('m/d/Y', strtotime($row['eocdate']));
            $data[] = $column;
        }
        echo json_encode(array('data' => $data));
    }

    public function generateTermRepExcel()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'generateTermRepExcel';

        $this->load->view('promo/page/page_response', $data);
    }

    public function termRepList()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'termRepList';

        $this->load->view('promo/page/page_response', $data);
    }

    public function promoStat()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'promoStat';

        $this->load->view('promo/page/page_response', $data);
    }

    public function generateStatRep()
    {
        $data['input'] = $this->input->post(NULL, TRUE);
        $data['systemUser'] = $this->userId;
        $data['request'] = 'generateStatRep';

        $this->load->view('promo/page/page_response', $data);
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
            $link = '<a href="' . base_url('promo/page/promo/profile/' . $row['emp_id']) . '" target="_blank">' . $name . '</a>';
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

    public function failedEpas()
    {
        $data = [];
        $bus = $this->promo_model->locate_promo_bu($this->hrCode);
        $query = $this->promo_model->failedEpas();
        foreach ($query as $row) {

            $stores = '';
            foreach ($bus as $bu) {
                $hasBu = $this->promo_model->empStores('promo_record', $row['emp_id'], $row['record_no'], $bu['bunit_field']);
                if ($hasBu) {
                    $cond   = array('emp_id' => $row['emp_id'], 'record_no' => $row['record_no'], 'store' => $bu['bunit_name']);
                    $app    = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
                    if ($app) {

                        if ($app['numrate'] < 85) {
                            $stores .=  '<a href="javascript:;" id="' . $app['details_id'] . '" onclick="viewAppraisal(this.id)">
                                            <span class="badge bg-danger eocStrEpas" title="' . $bu['bunit_name'] . '">' . $bu['bunit_acronym'] . ' | ' . $app['numrate'] . '</span>
                                        </a>';
                        } else {
                            $stores .=  '<a href="javascript:;" id="' . $app['details_id'] . '" onclick="viewAppraisal(this.id)">
                                            <span class="badge bg-success eocStrEpas" title="' . $bu['bunit_name'] . '">' . $bu['bunit_acronym'] . ' | ' . $app['numrate'] . '</span>
                                        </a>';
                        }
                    } else {
                        $stores .=  '<a href="javascript:;">
                                        <span class="badge bg-secondary eocStrEpas" title="' . $bu['bunit_name'] . '">' . $bu['bunit_acronym'] . ' | none</span>
                                    </a>';
                    }
                }
            }

            $name = ucwords(strtolower($row['name']));
            $link = '<a href="' . base_url('promo/page/promo/profile/' . $row['emp_id']) . '" target="_blank">' . $name . '</a>';
            $action = '<a href="javascript:;" id="' . $row['emp_id'] . '" onclick="addBl(this.id)"><span class="badge bg-primary">Blacklist</span></a>';

            $column = [];
            $column[] = $link;
            $column[] = date('m/d/Y', strtotime($row['startdate']));
            $column[] = date('m/d/Y', strtotime($row['eocdate']));
            $column[] = $stores;
            $column[] = $action;
            $data[] = $column;
        }
        echo json_encode(array("data" => $data));
    }
}
