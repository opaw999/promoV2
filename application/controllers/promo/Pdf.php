<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pdf extends CI_Controller
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
        $this->adminUser    = array('01186-2023');
        $this->load->library('ppdf');
        if (!isset($this->userId)) {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }
    }

    public function randomNo()
    {
        $newstring  = '';
        $length     = 5;
        $list       = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        mt_srand((float)microtime() * 1000000);

        if ($length > 0) {
            while (strlen($newstring) < $length) {
                $newstring .= $list[mt_rand(0, strlen($list) - 1)];
            }
        }
        return $newstring;
    }

    public function generatePermit()
    {
        $input      = $this->input->post(NULL, TRUE);
        $id         = explode('_', $input['id']);
        $emp_id     = $id[0];
        $record_no  = $id[1];
        $contract   = $id[3];

        $cond       = array('bunit_id' => $id[2]);
        $store      = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);
        $name       = $this->promo_model->getName_employee3($emp_id);
        $no         = $this->randomNo() . '-' . date('mdy');
        $table1     = ($contract == 1) ? 'employee3' : 'employmentrecord_';
        $table2     = ($contract == 1) ? 'promo_record' : 'promo_history_record';
        $cond       = array('e.emp_id' => $emp_id, 'e.record_no' => $record_no);
        $row        = $this->promo_model->promoDetails_wttR($cond, $table1, $table2);

        $cond       = array('emp_id' => $emp_id, 'record_no' => $record_no, 'store' => $store['bunit_name']);
        $ps         = $this->promo_model->selectAll_tcR('promo_schedule', $cond);

        $cond       = array('emp_id' => $emp_id, 'usertype' => 'employee');
        $account    = $this->promo_model->selectAll_tcR('users', $cond);
        $copy       = array("Supervisor's Copy", "201 Copy");
        $position   = ($row['type'] == 'Seasonal') ? $row['position'] . ' Seasonal' : $row['position'];
        $duty       = '';
        $special    = '';
        $i          = 0;
        if (!empty($ps['dutySched'])) {
            $duty       = $ps['dutySched'] . '/' . $ps['dutyDays'];
        }
        if (!empty($ps['specialSched'])) {
            $special    =  $ps['specialSched'] . '/' . $ps['specialDays'];
        }

        // Set PDF properties
        $this->ppdf->SetAuthor($this->userId);
        $this->ppdf->SetTitle('Work Permit');
        $this->ppdf->SetSubject('Work Permit');
        $this->ppdf->SetKeywords('work permit');
        $this->ppdf->setPrintHeader(false);
        $this->ppdf->setPrintFooter(false);

        // Add a page
        $this->ppdf->AddPage();

        foreach ($copy as $page) {
            $i++;
            $this->ppdf->SetFont('Times', '', 8);
            $this->ppdf->Ln(8);
            $this->ppdf->Cell(5);
            $this->ppdf->Cell(0, 5, "(" . @$page . ")", 0, 0, 'L');
            $this->ppdf->SetX(160);
            $this->ppdf->Cell(0, 5, 'PERMIT NO. P' . $no);
            $this->ppdf->Ln(5);
            $this->ppdf->SetFont('Times', 'B', 12);
            $this->ppdf->Cell(0, 5, 'NOTICE OF ASSIGNMENT', 0, 0, 'C');
            $this->ppdf->Ln(5);
            $this->ppdf->SetFont('Times', '', 12);
            $this->ppdf->Cell(0, 5, 'For Promodiser - Merchandiser', 0, 0, 'C');
            $this->ppdf->Ln(5);
            $this->ppdf->Cell(0, 5, 'Assigned at ' . @$store['bunit_name'] . '', 0, 0, 'C');

            $this->ppdf->Ln(15);
            $this->ppdf->Cell(7);
            $this->ppdf->SetFont('Times', '', 12);
            $this->ppdf->Cell(5, 7, "NAME:", 0, 0, 'L');
            $this->ppdf->SetX(32);
            $this->ppdf->Cell(0, 7, @$name);
            $this->ppdf->SetX(32);
            $this->ppdf->Cell(0, 7, "__________________________________________");
            $this->ppdf->SetX(126);
            $this->ppdf->Cell(0, 7, "STATUS:");
            $this->ppdf->SetX(144);
            $this->ppdf->Cell(0, 7, @$row['type']);
            $this->ppdf->SetX(144);
            $this->ppdf->Cell(0, 7, "_________________________");

            $this->ppdf->Ln(7);
            $this->ppdf->Cell(7);
            $this->ppdf->Cell(5, 7, "DESIGNATION:", 0, 0, 'L');
            $this->ppdf->SetX(48);
            $this->ppdf->Cell(0, 7, @$position);
            $this->ppdf->SetX(48);
            $this->ppdf->Cell(0, 7, "_______________________");
            $this->ppdf->SetX(99);
            $this->ppdf->Cell(0, 7, "DEPT. ASSIGNED:");
            $this->ppdf->SetX(136);
            $this->ppdf->Cell(0, 7, @$row['promo_department']);
            $this->ppdf->SetX(136);
            $this->ppdf->Cell(0, 7, "____________________________");

            $this->ppdf->Ln(7);
            $this->ppdf->Cell(7);
            $this->ppdf->Cell(5, 7, "COMPANY/AGENCY:", 0, 0, 'L');
            $this->ppdf->SetX(61);
            $this->ppdf->Cell(0, 7, strtoupper(@$row['promo_company']));
            $this->ppdf->SetX(61);
            $this->ppdf->Cell(0, 7, "________________________________________________________________");

            $this->ppdf->Ln(7);
            $this->ppdf->Cell(7);
            $this->ppdf->Cell(5, 7, "INCLUSIVE DATES OF ASSIGNMENT:", 0, 0, 'L');
            $this->ppdf->SetX(90);
            $this->ppdf->Cell(0, 7, "");
            $this->ppdf->SetX(91);
            $this->ppdf->Cell(0, 7, date('F d, Y', strtotime($row['startdate'])) . ' - ' . date('F d, Y', strtotime($row['eocdate'])));
            $this->ppdf->SetX(91);
            $this->ppdf->Cell(0, 7, "__________________________________________________");

            $this->ppdf->Ln(7);
            $this->ppdf->Cell(7);
            $this->ppdf->Cell(5, 7, "DUTY SCHEDULE:", 0, 0, 'L');
            $this->ppdf->SetX(54);
            $this->ppdf->Cell(0, 7, $duty);
            $this->ppdf->SetX(55);
            $this->ppdf->Cell(0, 7, "___________________________________________________________________");
            $this->ppdf->Ln(7);
            $this->ppdf->SetX(54);
            $this->ppdf->Cell(0, 7, $special);
            $this->ppdf->SetX(55);
            $this->ppdf->Cell(0, 7, "___________________________________________________________________");

            $this->ppdf->Ln(7);
            $this->ppdf->Cell(7);
            $this->ppdf->Cell(5, 7, "DAY OFF:", 0, 0, 'L');
            $this->ppdf->SetX(37);
            $this->ppdf->Cell(0, 7, strtoupper($ps['dayoff']));
            $this->ppdf->SetX(37);
            $this->ppdf->Cell(0, 7, "____________________");

            $this->ppdf->Ln(20);
            $this->ppdf->SetFont('Times', 'U', 12);
            $this->ppdf->Cell(180, 7, "MS. BERNADETH M. CASTIL", 0, 0, 'R');
            $this->ppdf->Ln(5);
            $this->ppdf->SetFont('Times', '', 12);
            $this->ppdf->Cell(190, 7, "ASSISTANT CORPORATE HR MANAGER", 0, 0, 'R');

            if ($row['tag_as'] == 'new') {
                $username = '';
                $password = '';
                if (trim($account['username']) != '') {

                    $username = $account['username'];
                    $password = 'Hrms2014';
                }
                $this->ppdf->Ln(5);
                $this->ppdf->SetFont('Times', 'I', 8);
                $this->ppdf->Cell(7);
                $this->ppdf->Cell(12, 7, 'Username : ' . $username, 0, 0, 'L');
                $this->ppdf->Ln(4);
                $this->ppdf->Cell(7);
                $this->ppdf->Cell(12, 7, 'Password : ' . $password, 0, 0, 'L');
                $this->ppdf->Ln(3);
                $this->ppdf->SetFont('Times', '', 12);
            } else {
                $this->ppdf->Ln(10);
            }
            if ($i == '1') {
                $this->ppdf->Ln(15);
                $this->ppdf->Cell(0, 7, '-----------------------------------------------------------------------------------------------------------------------------------------', 0, 0, 'L');
                $this->ppdf->Ln(10);
            }
        }

        // Output the PDF
        $cond           = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $data           = array($store['bunit_permit'] => $no);
        $update         = $this->promo_model->update_twdA($table2, $cond, $data);
        $filename       = 'permittowork_' . $emp_id . '_PermitNo_' . $no . '.pdf';
        $base_path      = FCPATH;
        $relative_path  = '../document/permittowork/';
        $path           = $base_path . $relative_path . $filename;
        $link           = 'id=' . $filename;
        $this->ppdf->Output($path, 'F');
        $activity       = 'Generate Promo Permit to Work of ' . $name . ' (' . $emp_id . ')  Permit No:' . $no;
        $logs           = array(
            'activity'  => $activity,
            'date'      => $this->today,
            'time'      => date('H:i:s'),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $this->promo_model->insert_tdA('logs', $logs);

        if ($row[$store['bunit_permit']] != '') {
            $rLogs      = $this->promo_model->selectAll_tcR('reprint_logs', array('print_no' => $row[$store['bunit_permit']]));
            $rtimes     = ($rLogs['reprint_times'] + 1);
            $logs       = array(
                'reprint_name'      => 'Permit',
                'print_no'          => $no,
                'reprint_times'     => $rtimes,
                'reprint_date'      => $this->today,
                'reprint_time'      => date('H:i:s'),
                'reprint_user'      => $this->userId,
                'reprint_username'  => $_SESSION['username'],
            );
            if ($rLogs) {
                $this->promo_model->update_twdA('reprint_logs', array('print_no' => $row[$store['bunit_permit']]), $logs);
            } else {
                $this->promo_model->insert_tdA('reprint_logs', $logs);
            }
        }

        ob_clean();
        echo json_encode(['message' => 'success', 'id' => $link]);
    }

    public function generateContract()
    {
        $input      = $this->input->post(NULL, TRUE);
        $id         = explode('_', $input['id']);
        $emp_id     = $id[0];
        $record_no  = $id[1];
        $header_no  = $id[2];
        $contract   = $id[3];
        $no         = $this->randomNo() . '-' . date('mdy');
        $witness    = $this->promo_model->selectAll_tcR('employment_witness', array('emp_id' => $emp_id, 'rec_no' => $record_no));
        $date_generated = $this->today;
        if ($witness['date_generated'] != '' && $witness['date_generated'] != '0000-00-00') {
            $date_generated = $witness['date_generated'];
        }
        $header     = $this->promo_model->selectAll_tcR('contract_header', array('ccode_no' => $header_no));
        $headComp   = str_replace('-', ' - ', $header['company']);
        $table1     = ($contract == 1) ? 'employee3' : 'employmentrecord_';
        $table2     = ($contract == 1) ? 'promo_record' : 'promo_history_record';
        $row        = $this->promo_model->promoDetails_wttR(array('e.emp_id' => $emp_id, 'e.record_no' => $record_no), $table1, $table2);
        $app        = $this->promo_model->selectAll_tcR('applicant', array('app_id' => $emp_id));
        $mn         = (!empty($app['middlename'])) ? ' ' . substr($app['middlename'], 0, 1) . '. ' : ' ';
        $suffix     = (!empty($app['suffix'])) ? ' ' . $app['suffix'] . ' ' : '';
        $fullname   = $app['firstname'] . $mn .  $app['lastname'] . $suffix;
        $sss_ctc    = ($witness['sss_ctc'] == 'Cedula') ? 'CTC No.' : 'SSS No.';
        $position   = ($row['type'] == 'Seasonal') ? $row['position'] . ' Seasonal' : $row['position'];
        $when       = date_create($date_generated);
        $month      = date_format($when, 'm');
        $day        = date_format($when, 'jS');
        $year       = date_format($when, 'Y');
        $monthName  = date('F', mktime(0, 0, 0, $month, 10));
        $five       = ($header_no == 23) ? ucwords(strtolower($headComp)) : 'employer';

        $hcode = array(
            1 => 'colm_contract',
            10 => 'tal_contract',
            11 => 'pm_contract',
            12 => 'icm_contract',
            14 => 'asc_contract',
            23 => 'absna_contract',
            23 => 'absni_contract',
            31 => 'tub_contract',
            32 => 'colc_contract',
            33 => 'alta_contract',
            36 => 'fr_panglao_contract',
            37 => 'fr_tubigon_contract',
            38 => 'al_panglao_contract',
        );

        $field = $hcode[$header_no];


        // Set PDF properties
        $this->ppdf->SetAuthor($this->userId);
        $this->ppdf->SetTitle('CONTRACT OF EMPLOYMENT');
        $this->ppdf->SetSubject('contract');
        $this->ppdf->SetKeywords('contract');
        $this->ppdf->setPrintHeader(false);
        $this->ppdf->setPrintFooter(false);

        // Add a page
        $this->ppdf->AddPage('P', 'LEGAL');

        $this->ppdf->Ln(1);
        $this->ppdf->SetFont('Times', 'B', 12);
        $this->ppdf->Cell(0, 0, 'CONTRACT OF EMPLOYMENT FOR A DEFINITE PERIOD', 0, 0, 'C');
        $this->ppdf->Ln(10);

        $this->ppdf->SetFont('Times', '', 12);
        $this->ppdf->Cell(144);
        $this->ppdf->Cell(40, 0, date('F d, Y', strtotime($date_generated)), 0, 0, 'R');
        $this->ppdf->Ln(1);
        $this->ppdf->SetX(158);
        $this->ppdf->Cell(40, 0, '__________________');
        $this->ppdf->Ln(4);
        $this->ppdf->SetX(171);
        $this->ppdf->Cell(40, 7, 'DATE');

        $this->ppdf->Ln(10);
        $this->ppdf->Cell(7);
        $this->ppdf->Cell(20, 7, $header['addresse'], 0, 0, 'L');
        $this->ppdf->Ln(5);
        $this->ppdf->Cell(7);
        $this->ppdf->Cell(32, 7, $headComp, 0, 0, 'L');
        $this->ppdf->Ln(5);
        $this->ppdf->Cell(7);
        $this->ppdf->Cell(32, 7, $header['address'], 0, 0, 'L');

        $this->ppdf->Ln(10);
        $this->ppdf->Cell(7);
        $this->ppdf->Cell(32, 7, 'SIR:', 0, 0, 'L');
        $this->ppdf->Ln(5);
        $this->ppdf->Cell(25);
        $this->ppdf->Cell(7);
        $this->ppdf->SetX(25);
        $this->ppdf->MultiCell(175, 5, 'Anent to my  employment with, assigned in ' . $row['promo_company'] . ', I hereby agree on the following terms and conditions in connection therewith to wit:', 0, 'L');

        $this->ppdf->Ln(4);
        $this->ppdf->Cell(7);
        $this->ppdf->SetX(17);
        $this->ppdf->Cell(5, 7, '1.', 0, 0, 'L');
        $this->ppdf->MultiCell(175, 5, 'To be assigned as ' . $position  . ' in the ' . $row['promo_department'] . ' Department of your establishment as may be determined by my employer from ' . date('F d, Y', strtotime($row['startdate'])) . ' to ' . date('F d, Y', strtotime($row['eocdate'])) . '.');

        $this->ppdf->Ln(4);
        $this->ppdf->Cell(7);
        $this->ppdf->SetX(17);
        $this->ppdf->Cell(5, 7, '2.', 0, 0, 'L');
        $this->ppdf->MultiCell(175, 5, 'Shall be entitled to a monthly compensation from my employer with my full knowledge and understanding that I am not an employee, nor connected in whatever capacity with ' . ucwords(strtolower($headComp)) . '.                           ');

        $this->ppdf->Ln(4);
        $this->ppdf->Cell(7);
        $this->ppdf->SetX(17);
        $this->ppdf->Cell(5, 7, '3.', 0, 0, 'L');
        if ($header_no == 23) {
            $this->ppdf->MultiCell(175, 5, 'To abide by the established working hours prescribed by ' . $row['promo_company'] . ' and while assigned at the Abenson, I will strictly comply with the rules and regulation and policies of the ' . ucwords(strtolower($headComp)) . '.                            ');
        } else {
            $this->ppdf->MultiCell(175, 5, 'To abide by the established working hours and strictly comply with the rules, regulation and policies of the ' . ucwords(strtolower($headComp)) . '.                            ');
        }

        $this->ppdf->Ln(4);
        $this->ppdf->Cell(7);
        $this->ppdf->SetX(17);
        $this->ppdf->Cell(5, 7, '4.', 0, 0, 'L');
        $this->ppdf->MultiCell(175, 5, 'Can be terminated from my assessment upon violation of the rules and regulation of ' . ucwords(strtolower($headComp)) . ' or for any violation of the law, decree or ordinance in connection therewith.                             ');

        $this->ppdf->Ln(4);
        $this->ppdf->Cell(7);
        $this->ppdf->SetX(17);
        $this->ppdf->Cell(5, 7, '5.', 0, 0, 'L');
        $this->ppdf->MultiCell(175, 5, 'All records and documents of ' . $five . ' and all information pertaining to its business affairs are confidential and that no authorized release, disclosure or reproduction of the same will be made at any time during or after my assignment.                            ');

        $this->ppdf->Ln(4);
        $this->ppdf->Cell(20);
        $this->ppdf->MultiCell(167, 3, 'IN WITNESS WHEREOF, I have  hereunto  set my hand  and affixed my signature this ' . $day . ' day');
        $this->ppdf->Cell(11);
        $this->ppdf->Cell(20, 5, ' of ' . strtoupper($monthName) . ', ' . $year . ' at Tagbilaran City, Bohol, Philippines.', 0, 0, 'L');

        $this->ppdf->SetFont('Times', 'U', 12);
        $this->ppdf->Ln(15);
        $this->ppdf->Cell(125);
        $fullname = str_replace('ñ', 'Ñ', strtoupper($fullname));
        $this->ppdf->Cell(60, 7, ucwords(strtoupper($fullname)), 0, 0, 'C');
        $this->ppdf->Ln(5);
        $this->ppdf->Cell(146);
        $this->ppdf->SetFont('Times', '', 12);
        $this->ppdf->Cell(20, 7, 'Employee', 0, 0, 'L');

        $this->ppdf->Ln(10);
        $this->ppdf->Cell(125);
        $this->ppdf->Cell(15, 15, $sss_ctc);
        $this->ppdf->SetFont('Times', 'U', 12);
        $this->ppdf->Cell(45, 15, $witness['sssno_ctcno']);
        $this->ppdf->SetFont('Times', '', 12);
        if ($witness['sss_ctc'] == 'Cedula') {
            $this->ppdf->Ln(5);
            $this->ppdf->Cell(125);
            $this->ppdf->Cell(17, 15, 'issued on ');
            $this->ppdf->SetFont('Times', 'U', 12);
            $this->ppdf->Cell(45, 15, date('F d, Y', strtotime($witness['issuedon'])));
        }
        $this->ppdf->SetFont('Times', '', 12);
        $this->ppdf->Ln(5);
        $this->ppdf->Cell(125);
        $this->ppdf->Cell(5, 15, 'at ');
        $this->ppdf->SetFont('Times', 'U', 12);
        $this->ppdf->Cell(45, 15, $witness['issuedat']);
        $this->ppdf->SetFont('Times', '', 12);

        $this->ppdf->Ln(10);
        $this->ppdf->Cell(11);
        $this->ppdf->Cell(20, 7, 'CONFORME:', 0, 0, 'L');
        $this->ppdf->Ln(10);
        $this->ppdf->Cell(11);
        $this->ppdf->Cell(20, 7, $headComp, 0, 0, 'L');
        $this->ppdf->Ln();
        $this->ppdf->Cell(11);
        $this->ppdf->Cell(20, 7, 'By:', 0, 0, 'L');
        $this->ppdf->Ln(5);

        $this->ppdf->SetFont('Times', 'U', 12);
        $this->ppdf->Cell(25);
        $this->ppdf->Cell(20, 7, 'MS. BERNADETH M. CASTIL', 0, 0, 'L');
        $this->ppdf->Ln(5);
        $this->ppdf->Cell(12);
        $this->ppdf->SetFont('Times', '', 12);
        $this->ppdf->Cell(20, 7, 'ASSISTANT CORPORATE HR MANAGER', 0, 0, 'L');

        $this->ppdf->Ln(10);
        $this->ppdf->Cell(11);
        $this->ppdf->Cell(20, 7, 'SIGNED IN THE PRESENCE OF:', 0, 0, 'L');
        $this->ppdf->Ln(10);
        $this->ppdf->SetFont('Times', 'U', 12);
        $this->ppdf->Cell(12);
        $this->ppdf->Cell(60, 7, $witness['witness1'], 0, 0, 'C');
        $this->ppdf->Cell(15);
        $this->ppdf->SetFont('Times', '', 12);
        $this->ppdf->Cell(20, 7, 'and', 0, 0, 'C');
        $this->ppdf->Cell(13);
        $this->ppdf->SetFont('Times', 'U', 12);
        $this->ppdf->Cell(60, 7, $witness['witness2'], 0, 0, 'C');
        $this->ppdf->Ln(5);
        $this->ppdf->Cell(34);
        $this->ppdf->SetFont('Times', '', 12);
        $this->ppdf->Cell(20, 7, 'Witness', 0, 0, 'L');
        $this->ppdf->Cell(88);
        $this->ppdf->Cell(20, 7, 'Witness', 0, 0, 'L');

        $this->ppdf->Ln(10);
        $this->ppdf->Cell(7);
        $this->ppdf->SetX(17);
        $this->ppdf->MultiCell(178, 6, 'SUBSCRIBE AND SWORN TO before me this ' . $day . ' day of ' . strtoupper($monthName) . ', ' . $year . ' in the City of Tagbilaran, Bohol, Philippines.                     ');

        $this->ppdf->Ln(8);
        $this->ppdf->Cell(120);
        $this->ppdf->Cell(20, 7, '_______________________________', 0, 0, 'L');
        $this->ppdf->Ln();
        $this->ppdf->Cell(126);
        $this->ppdf->Cell(20, 7, 'ADMINISTERING OFFICER', 0, 0, 'L');
        $this->ppdf->Ln();

        $cond           = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $update         = $this->promo_model->update_twdA($table2, $cond, array($field => $no));
        $filename       = 'contract_' . $emp_id . '_random_' . $no . '.pdf';
        $base_path      = FCPATH;
        $relative_path  = '../document/contract/';
        $path           = $base_path . $relative_path . $filename;
        $link           = 'id=' . $filename;
        $this->ppdf->Output($path, 'F');

        $activity   = "Generate Promo Contract of Employment of $fullname ($emp_id) Record No.$record_no Contract No.$no";
        $logs       = array(
            'activity'  => $activity,
            'date'      => $this->today,
            'time'      => date("H:i:s"),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $this->promo_model->insert_tdA('logs', $logs);
        if ($row[$field] != '') {
            $rLogs = $this->promo_model->selectAll_tcR('reprint_logs', array('print_no' => $row[$field]));
            $rtimes = ($rLogs['reprint_times'] + 1);
            $logs           = array(
                'reprint_name'      => 'Contract',
                'print_no'          => $no,
                'reprint_times'     => $rtimes,
                'reprint_date'      => $this->today,
                'reprint_time'      => date('H:i:s'),
                'reprint_user'      => $this->userId,
                'reprint_username'  => $_SESSION['username'],
            );
            if ($rLogs) {
                $this->promo_model->update_twdA('reprint_logs', array('print_no' => $row[$field]), $logs);
            } else {
                $this->promo_model->insert_tdA('reprint_logs', $logs);
            }
        }

        ob_clean();
        echo json_encode(['message' => 'success', 'id' => $link]);
    }

    public function generateDueContractsPDF()
    {
        $input = $this->input->post(NULL, TRUE);

        // Set PDF properties
        $this->ppdf->SetAuthor($this->userId);
        $this->ppdf->SetTitle('DUE CONTRACTS REPORT');
        $this->ppdf->SetSubject('DUE CONTRACTS REPORT');
        $this->ppdf->SetKeywords('DUE CONTRACTS REPORT');
        $this->ppdf->setPrintHeader(false);

        // Add a page
        $this->ppdf->AddPage('L', 'LEGAL');

        $this->ppdf->SetFont('Times', 'B', 12);
        $this->ppdf->Ln();
        $this->ppdf->Cell(145);
        $this->ppdf->Cell(30, 7, "DUE CONTRACTS REPORT", 0, 0, 'C');
        $this->ppdf->Ln();
        $this->ppdf->Cell(145);
        $this->ppdf->Cell(30, 7, "as of " . date('F d, Y'), 0, 0, 'C');
        $this->ppdf->SetFont('Times', 'B', 10);

        $this->ppdf->Ln(10);
        $this->ppdf->SetFont('Times', 'B', 10);
        $this->ppdf->Cell(12, 7, 'NO.', 1);
        $this->ppdf->Cell(55, 7, "NAME", 1);
        $this->ppdf->Cell(27, 7, "EMPTYPE", 1);
        $this->ppdf->Cell(25, 7, "STARTDATE ", 1);
        $this->ppdf->Cell(22, 7, 'EOCDATE', 1);
        $this->ppdf->Cell(45, 7, "POSITION", 1);
        $this->ppdf->Cell(110, 7, "STORE(S", 1);
        $this->ppdf->Cell(40, 7, "DEPARTMENT", 1);
        $this->ppdf->Ln();

        $where = "(emp_type = 'Promo' OR emp_type = 'Promo-NESCO' OR emp_type = 'Promo-EasyL')";
        if (!empty($input['store'])) {
            if ($input['store'] == 'allbu') {
                $query = $this->promo_model->locate_promo_bu($this->hrCode);
                $i = 0;
                $where .= ' AND (';
                foreach ($query as $key => $field) {
                    $i++;
                    $where .= ($i == 1) ? "$field[bunit_field] = 'T'" : " OR $field[bunit_field] = 'T'";
                }
                $where .= ')';
            } else {
                $bunit = explode('|', $input['store']);
                $where .= " AND $bunit[1]= 'T'";
            }
        }
        if (!empty($input['promo_department'])) {
            $where .= " AND promo_department = '$input[promo_department]'";
        } else {
            if ($input['store'] == 'allbu') {
                $query = $this->promo_model->locate_promo_bu($this->hrCode);
                $bunit_id = [];
                foreach ($query as $key => $field) {
                    $bunit_id[] = $field['bunit_id'];
                }
                $dept = $this->promo_model->whereIN_stcd('distinct', 'dept_name', 'locate_promo_department', 'bunit_id', $bunit_id);
                $where .= ' AND (';
                $i = 0;
                foreach ($dept as $key => $value) {
                    $i++;
                    $where .= ($i == 1) ? "promo_department = '$value[dept_name]'" : " OR promo_department = '$value[dept_name]'";
                }
                $where .= ')';
            }
        }
        $where .= " AND current_status = 'Active' AND hr_location = '$this->hrCode'";
        $where .= " AND eocdate < '" . date('Y-m-d') . "'";

        $no = 0;
        $table1 = 'employee3';
        $table2 = 'promo_record';
        $join = 't1.emp_id=t2.emp_id AND t1.record_no=t2.record_no';
        $order = "name|ASC";
        $query = $this->promo_model->join_ttjcoA('*', $table1, $table2, $join, null, $where, $order, null);
        foreach ($query as $row) {
            $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            $no++;
            $i = 0;
            $stores = '';
            foreach ($bus as $bu) {
                $hasBu = $this->promo_model->empStores('promo_record', $row['emp_id'], $row['record_no'], $bu['bunit_field']);
                if ($hasBu) {
                    $i++;
                    $stores = ($i == 1) ? $bu['bunit_acronym'] : $stores . ', ' . $bu['bunit_acronym'];
                }
            }
            $this->ppdf->SetFillColor(200, 220, 255);
            $this->ppdf->Cell(12, 7, $no, 1);
            $this->ppdf->Cell(55, 7, ucwords(strtolower($row['name'])), 1);
            $this->ppdf->Cell(27, 7, $row['emp_type'], 1);
            $this->ppdf->Cell(25, 7, date('m/d/Y', strtotime($row['startdate'])), 1);
            $this->ppdf->Cell(22, 7, date('m/d/Y', strtotime($row['eocdate'])), 1);
            $this->ppdf->Cell(45, 7, ucwords(strtolower($row['position'])), 1);
            $this->ppdf->Cell(110, 7, $stores, 1);
            $this->ppdf->Cell(40, 7, $row['promo_department'], 1);
            $this->ppdf->Ln();
        }

        $this->ppdf->SetFont('Times', 'I', 8);
        $this->ppdf->Cell(0, 10, 'Prepared by: ' . $this->promo_model->getName_employee3($this->userId), 0, 0, 'R');

        ob_clean();
        $this->ppdf->Output();
    }

    public function generateTermRepPdf()
    {
        $input = $this->input->post(NULL, TRUE);

        // Set PDF properties
        $this->ppdf->SetAuthor($this->userId);
        $this->ppdf->SetTitle('TERMINATION OF CONTRACT REPORT');
        $this->ppdf->SetSubject('TERMINATION OF CONTRACT REPORT');
        $this->ppdf->SetKeywords('TERMINATION OF CONTRACT REPORT');
        $this->ppdf->setPrintHeader(false);

        // Add a page
        $this->ppdf->AddPage('P', 'LETTER');

        $where = "(emp_type = 'Promo' OR emp_type = 'Promo-NESCO' OR emp_type = 'Promo-EasyL')";
        if (!empty($input['store'])) {
            $bunit = explode('|', $input['store']);
            $where .= " AND $bunit[1]= 'T'";
        }
        if (!empty($input['promo_department'])) {
            $where .= " AND promo_department = '$input[promo_department]'";
        }
        if (!empty($input['month'])) {
            $month = explode('|', $input['month']);
            $y = date('Y') + 1;
            if ($month[1] == $y) {
                $year = $month[1];
                $date = $year . '-' . $month[0];
            } else {
                $year = date('Y');
                $date = $year . '-' . $month[0];
            }
            $where .= " AND eocdate LIKE '%$date%'";
        }
        if (!empty($input['promo_company'])) {
            $where .= " AND promo_company = '$input[promo_company]'";
        }
        $where .= " AND current_status = 'Active' AND hr_location = '$this->hrCode'";
        $no = 0;
        $table1 = 'employee3';
        $table2 = 'promo_record';
        $join = 't1.emp_id=t2.emp_id AND t1.record_no=t2.record_no';
        $order = "name|ASC";
        $query = $this->promo_model->join_ttjcoA('*', $table1, $table2, $join, null, $where, $order, null);
        foreach ($query as $row) {
            $no++;
            // $bus = $this->promo_model->locate_promo_bu($this->hrCode);
            $cond = array('bunit_field' => $bunit[1]);
            $bus = $this->promo_model->selectAll_tcA('locate_promo_business_unit', $cond);
            foreach ($bus as $bu) {
                $hasBu = $this->promo_model->empStores('promo_record', $row['emp_id'], $row['record_no'], $bu['bunit_field']);
                if ($hasBu) {

                    $this->ppdf->SetFont('Times', 'B', 12);
                    $this->ppdf->Cell(85);

                    $this->ppdf->Ln(5);
                    $this->ppdf->SetFont('Times', 'B', 12);
                    $this->ppdf->Cell(0, 5, 'NOTICE OF TERMINATION', 0, 0, 'C');
                    $this->ppdf->Ln(5);
                    $this->ppdf->SetFont('Times', '', 12);
                    $this->ppdf->Cell(0, 5, 'For Promodiser - Merchandiser', 0, 0, 'C');
                    $this->ppdf->Ln(5);
                    $this->ppdf->Cell(0, 5, 'Assigned at ' . ucwords(strtolower($bu['business_unit'])) . '', 0, 0, 'C');

                    $this->ppdf->Ln(10);
                    $this->ppdf->SetFont('Times', '', 12);
                    $text = '<p style="text-align: right;"><b>Date:</b> <u>' . date('F d, Y') . '</u></p>';
                    $this->ppdf->writeHTML($text, true, false, true, false, '');

                    $this->ppdf->Ln(10);
                    $text = '<p><b>&nbsp;TO' . str_repeat('&nbsp;', 30) . ': <u>' . ucwords(strtolower($row['name'])) . '</u></b></p>';
                    $this->ppdf->writeHTML($text, true, false, true, false, '');
                    $this->ppdf->SetFont('Times', 'B', 11);
                    $this->ppdf->Cell(0, 0, 'COMPANY/AGENCY:');
                    $this->ppdf->SetX(50);
                    $this->ppdf->SetFont('Times', 'BU', 11);
                    $this->ppdf->MultiCell(0, 0, $row['promo_department'] . ' - ' . $row['promo_company'], 0, 'L');



                    $this->ppdf->Ln(8);
                    $this->ppdf->SetFont('Times', '', 12);
                    $text   = '<p style="text-indent: 40px;">Please be reminded that according to the Introductory Letter we received 
                                from your company/agency, your assignment on this establishment will expire on ';
                    $date   = '<b>' . date('l', strtotime($row['eocdate'])) . ' ' . date('F d, Y', strtotime($row['eocdate'])) . '</b>.</p>';
                    $this->ppdf->writeHTML($text . $date, true, false, true, false, '');

                    $this->ppdf->Ln(2);
                    $str    = '<b>' . ucwords(strtolower($bu['business_unit'])) . '</b>';
                    $text   = '<p style="text-indent: 40px;">In connection with this you are advised to yield all 
                    company properties under your care and seek clearance before you leave the 
                    company premises of ' . $str . ' at the close of business hours on such day.</p>';
                    $this->ppdf->writeHTML($text, true, false, true, false, '');

                    $this->ppdf->Ln(2);
                    $text   = '<p style="text-indent: 40px;">Thank you and good luck!.</p>';
                    $this->ppdf->writeHTML($text, true, false, true, false, '');

                    $this->ppdf->Ln(10);
                    $this->ppdf->SetFont('Times', '', 11);
                    $text = '<p><b>MS. BERNADETH M. CASTIL</b><br>ASSISTANT CORPORATE HR MANAGER</p>';
                    $this->ppdf->writeHTML($text, true, false, true, false, '');
                    $this->ppdf->Ln(20);
                }
            }
            $date = date("Y-m-d");
            $time = date("H:i:s");
            $data = array(
                'activity'  => 'Generate Promo Termination of Contract Report of ' . $row['name'],
                'date'      => $date,
                'time'      => $time,
                'user'      => $this->userId,
                'username'  => $_SESSION['username']
            );
            $this->promo_model->insert_tdA('logs', $data);
        }

        ob_clean();
        $this->ppdf->Output();
    }

    public function generateTermContract()
    {
        $input = $this->input->get(NULL, TRUE);

        // Set PDF properties
        $this->ppdf->SetAuthor($this->userId);
        $this->ppdf->SetTitle('TERMINATION OF CONTRACT REPORT');
        $this->ppdf->SetSubject('TERMINATION OF CONTRACT REPORT');
        $this->ppdf->SetKeywords('TERMINATION OF CONTRACT REPORT');
        $this->ppdf->setPrintHeader(false);
        $this->ppdf->AddPage('P', 'LETTER');
        if (isset($input['emp_id'])) {
            $no = 0;
            $ids = explode('|', $input['emp_id']);
            foreach ($ids as $emp_id) {

                $where = "(emp_type = 'Promo' OR emp_type = 'Promo-NESCO' OR emp_type = 'Promo-EasyL')";
                $where .= " AND t1.emp_id ='$emp_id'";
                if (!empty($input['store'])) {
                    $bunit = explode('|', $input['store']);
                    $where .= " AND $bunit[1]= 'T'";
                }
                if (!empty($input['promo_department'])) {
                    $where .= " AND promo_department = '$input[promo_department]'";
                }
                if (!empty($input['month'])) {
                    $month = explode('|', $input['month']);
                    $y = date('Y') + 1;
                    if ($month[1] == $y) {
                        $year = $month[1];
                        $date = $year . '-' . $month[0];
                    } else {
                        $year = date('Y');
                        $date = $year . '-' . $month[0];
                    }
                    $where .= " AND eocdate LIKE '%$date%'";
                }
                if (!empty($input['promo_company'])) {
                    $where .= " AND promo_company = '$input[promo_company]'";
                }
                $where .= " AND current_status = 'Active' AND hr_location = '$this->hrCode'";


                $bus = $this->promo_model->locate_promo_bu($this->hrCode);
                $table1 = 'employee3';
                $table2 = 'promo_record';
                $join = 't1.emp_id=t2.emp_id AND t1.record_no=t2.record_no';
                $order = "name|ASC";
                $query = $this->promo_model->join_ttjcoA('*', $table1, $table2, $join, null, $where, $order, null);

                foreach ($query as $row) {
                    $no++;
                    if ($input['type'] == 'forStore') {
                        foreach ($bus as $bu) {
                            $hasBu = $this->promo_model->empStores('promo_record', $row['emp_id'], $row['record_no'], $bu['bunit_field']);
                            if ($hasBu) {

                                $this->ppdf->SetFont('Times', 'B', 12);
                                $this->ppdf->Cell(85);

                                $this->ppdf->Ln(5);
                                $this->ppdf->SetFont('Times', 'B', 12);
                                $this->ppdf->Cell(0, 5, 'NOTICE OF TERMINATION', 0, 0, 'C');
                                $this->ppdf->Ln(5);
                                $this->ppdf->SetFont('Times', '', 12);
                                $this->ppdf->Cell(0, 5, 'For Promodiser - Merchandiser', 0, 0, 'C');
                                $this->ppdf->Ln(5);
                                $this->ppdf->Cell(0, 5, 'Assigned at ' . ucwords(strtolower($bu['business_unit'])) . '', 0, 0, 'C');

                                $this->ppdf->Ln(10);
                                $this->ppdf->SetFont('Times', '', 12);
                                $text = '<p style="text-align: right;"><b>Date:</b> <u>' . date('F d, Y') . '</u></p>';
                                $this->ppdf->writeHTML($text, true, false, true, false, '');


                                $this->ppdf->Ln(10);
                                $text = '<p><b>&nbsp;TO' . str_repeat('&nbsp;', 30) . ': <u>' . ucwords(strtolower($row['name'])) . '</u></b></p>';
                                $this->ppdf->writeHTML($text, true, false, true, false, '');
                                $this->ppdf->SetFont('Times', 'B', 11);
                                $this->ppdf->Cell(0, 0, 'COMPANY/AGENCY:');
                                $this->ppdf->SetX(50);
                                $this->ppdf->SetFont('Times', 'BU', 11);
                                $this->ppdf->MultiCell(0, 0, $row['promo_department'] . ' - ' . $row['promo_company'], 0, 'L');


                                $this->ppdf->Ln(8);
                                $this->ppdf->SetFont('Times', '', 12);
                                $text   = '<p style="text-indent: 40px;">Please be reminded that according to the Introductory Letter we received 
                                            from your company/agency, your assignment on this establishment will expire on ';
                                $date   = '<b>' . date('l', strtotime($row['eocdate'])) . ' ' . date('F d, Y', strtotime($row['eocdate'])) . '</b>.</p>';
                                $this->ppdf->writeHTML($text . $date, true, false, true, false, '');

                                $this->ppdf->Ln(2);
                                $str    = '<b>' . ucwords(strtolower($bu['business_unit'])) . '</b>';
                                $text   = '<p style="text-indent: 40px;">In connection with this you are advised to yield all 
                                company properties under your care and seek clearance before you leave the 
                                company premises of ' . $str . ' at the close of business hours on such day.</p>';
                                $this->ppdf->writeHTML($text, true, false, true, false, '');

                                $this->ppdf->Ln(2);
                                $text   = '<p style="text-indent: 40px;">Thank you and good luck!.</p>';
                                $this->ppdf->writeHTML($text, true, false, true, false, '');

                                $this->ppdf->Ln(10);
                                $this->ppdf->SetFont('Times', '', 11);
                                $text = '<p><b>MS. BERNADETH M. CASTIL</b><br>ASSISTANT CORPORATE HR MANAGER</p>';
                                $this->ppdf->writeHTML($text, true, false, true, false, '');
                                $this->ppdf->Ln(20);
                            }
                        }
                    } else {
                        $i = 0;
                        $stores = '';
                        foreach ($bus as $bu) {
                            $hasBu = $this->promo_model->empStores('promo_record', $row['emp_id'], $row['record_no'], $bu['bunit_field']);
                            if ($hasBu) {
                                $i++;
                                $stores = ($i == 1) ? $bu['bunit_acronym'] : $stores . ', ' . $bu['bunit_acronym'];
                            }
                        }
                        $hrd = $this->promo_model->getName_employee3($this->userId);
                        $this->ppdf->Image(base_url('assets/promo_assets/images/alturas.png'), 40, 10, 120);
                        $this->ppdf->Ln(19);
                        $this->ppdf->Cell(15);
                        $this->ppdf->SetFont('Times', '', 12);
                        $this->ppdf->Cell(0, 7, date('F d, Y'));

                        $this->ppdf->Ln(11);
                        $this->ppdf->Cell(15);
                        $this->ppdf->SetFont('Times', 'B', 12);
                        $this->ppdf->MultiCell(0, 7,  $row['promo_company'], 0, 'L');
                        $this->ppdf->Ln(8);
                        $this->ppdf->Cell(15);
                        $this->ppdf->Cell(0, 7, 'ATTENTION:	    PERSONNEL DEPARTMENT');

                        $this->ppdf->Ln(11);
                        $this->ppdf->Cell(15);
                        $this->ppdf->SetFont('Times', '', 12);
                        $this->ppdf->Cell(0, 7, "Dear Sir/Ma'am:");

                        $this->ppdf->Ln(14);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, "Please be reminded that based on your Intro Letter, the termination of contract of the " . strtolower($row['position']) . " handling your product, will take effect on the date stated below:", 0, 'L');

                        $this->ppdf->Ln(9);
                        $this->ppdf->Cell(15);
                        $this->ppdf->Cell(45, 7, 'NAME', '1', 0);
                        $getXcomp = $this->ppdf->GetX();
                        $this->ppdf->Cell(63, 7, 'COMPANY', '1', 0);
                        $getXout = $this->ppdf->GetX();
                        $this->ppdf->Cell(45, 7, 'OUTLET', '1', 0);
                        $getXeoc = $this->ppdf->GetX();
                        $this->ppdf->Cell(17, 7, 'EOC', '1', '1');

                        $this->ppdf->SetFont('Times', '', 10);
                        $this->ppdf->Cell(15);
                        $getY = $this->ppdf->GetY();
                        $this->ppdf->MultiCell(45, 15, ucwords(strtolower($row['name'])), '1', 'L');

                        $this->ppdf->SetY($getY);
                        $this->ppdf->SetX($getXcomp);
                        $this->ppdf->MultiCell(63, 15,  $row['promo_company'], '1', 'L');

                        $this->ppdf->SetY($getY);
                        $this->ppdf->SetX($getXout);
                        $this->ppdf->MultiCell(45, 15, $stores, '1', 'L');
                        $this->ppdf->SetY($getY);
                        $this->ppdf->SetX($getXeoc);
                        $this->ppdf->MultiCell(17, 15, date('m/d/y', strtotime($row['eocdate'])), '1', 'L');
                        $this->ppdf->Cell(15);

                        $this->ppdf->SetFont('Times', '', 12);
                        $this->ppdf->Ln(8);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, "In view of the above, if you find his / her performance commendable for renewal, we would like to request your good office to send us an Introductory Letter. Otherwise, please endorse a possible applicant as replacement.", 0, 'L');

                        $this->ppdf->Ln(5);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, "Kindly fax your Introductory Letter in this number (038) 501-9245 or you may email at corporatehrd@alturasbohol.com.", 0, 'L');

                        $this->ppdf->Ln(5);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, "Thank you!", 0, 'L');

                        $this->ppdf->Ln(5);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, "Very respectfully yours,", 0, 'L');

                        $this->ppdf->SetFont('Times', 'B', 12);
                        $this->ppdf->Ln(5);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, "ALTURAS SUPERMARKET CORPORATION", 0, 'L');

                        $this->ppdf->Ln(7);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, ucwords(strtolower($hrd)), 0, 'L');

                        $this->ppdf->SetFont('Times', '', 12);
                        $this->ppdf->Ln(0);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, "HRD - Promo Transaction", 0, 'L');

                        $this->ppdf->SetFont('Times', '', 12);
                        $this->ppdf->Ln(5);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, "Noted By:", 0, 'L');

                        $this->ppdf->SetFont('Times', 'B', 12);
                        $this->ppdf->Ln(8);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, "MS. BERNADETH M. CASTIL", 0, 'L');

                        $this->ppdf->SetFont('Times', '', 12);
                        $this->ppdf->Ln(0);
                        $this->ppdf->Cell(15);
                        $this->ppdf->MultiCell(180, 7, "ASSISTANT CORPORATE HR MANAGER", 0, 'L');
                    }
                }
                $date = date("Y-m-d");
                $time = date("H:i:s");
                $data = array(
                    'activity'  => 'Generate Promo Termination of Contract Report of ' . $emp_id,
                    'date'      => $date,
                    'time'      => $time,
                    'user'      => $this->userId,
                    'username'  => $_SESSION['username']
                );
                // $logs = $this->promo_model->insert_tdA('logs', $data);
            }
        }
        ob_clean();
        $this->ppdf->Output();
    }

    public function viewPdf()
    {
        $input = $this->input->get(NULL, TRUE);
        $id = explode('_', $input['id']);
        $code = $id[0];

        if ($code == 'contract') {
            $pdfPath = '../document/contract/' . $input['id'];
        } else if ($code == 'permittowork') {
            $pdfPath = '../document/permittowork/' . $input['id'];
        } else if ($code == 'duecontracts') {
            $pdfPath = '../document/contract/' . $input['id'];
        } else if ($code == 'clearance') {
            $pdfPath = '../document/generated_clearance/' . $input['id'];
        }
        if (file_exists($pdfPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $input['id'] . '"');
            readfile($pdfPath);
        } else {
            header('HTTP/1.0 404 Not Found');
            echo 'File not found';
        }
    }

    public function generateClearance()
    {
        $input = $this->input->post(NULL, TRUE);

        $emp_id         = $input['emp_id'];
        $scdetails_id   = $input['scdetails_id'];

        $name       = $this->promo_model->getName_employee3($emp_id);
        $cond       = array('emp_id' => $emp_id);
        $company    = $this->promo_model->selectAll_tcR('promo_record', $cond)['promo_company'];
        $dept       = $this->promo_model->selectAll_tcR('promo_record', $cond)['promo_department'];

        $cond       = array('scdetails_id' => $scdetails_id);
        $scd        = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
        $recordno   = $scd['record_no'];
        $scpr_id    = $scd['scpr_id'];
        $store      = $scd['store'];
        $effectivedate  = date('F d, Y', strtotime($scd['date_effectivity']));
        $datesecure     = date('F d, Y', strtotime($scd['date_secure']));

        $cond   = array('scpr_id' => $scpr_id);
        $sc     = $this->promo_model->selectAll_tcR('secure_clearance_promo', $cond);
        $reason = $sc['reason'];

        $cond       = "emp_id='$emp_id' and record_no ='$recordno' and store='$store'";
        $numrate    = $this->promo_model->selectAll_tcR('appraisal_details', $cond)['numrate'];
        // Set PDF properties
        $this->ppdf->SetAuthor($this->userId);
        $this->ppdf->SetSubject('clearance');
        $this->ppdf->SetKeywords('clearance');
        $this->ppdf->setPrintHeader(false);
        $this->ppdf->setPrintFooter(false);

        // Add a page
        $this->ppdf->AddPage('P', 'LEGAL');
        $this->ppdf->SetFont('Helvetica', '', 12);
        $this->ppdf->SetAutoPageBreak('on', 5);
        $this->ppdf->SetTitle('PROMO CLEARANCE');

        $header     = "CLEARANCE FOR PROMO/MERCHANDISER";
        $address    = "ASSIGNED AT $store";

        $this->ppdf->SetFont('Helvetica', 'B', 11);
        $this->ppdf->Cell(85);
        $this->ppdf->Cell(30, 5, $header, 0, 0, 'C');
        $this->ppdf->Ln(5);
        $this->ppdf->SetFont('Helvetica', 'B', 11);
        $this->ppdf->Cell(40);
        $this->ppdf->Cell(120, 4, $address, 0, 0, 'C');
        $this->ppdf->Ln(5);
        $this->ppdf->SetFont('Helvetica', '', 10);

        $this->ppdf->Cell(132);
        $this->ppdf->Cell(24, 7, 'Date Secure:');
        $this->ppdf->Cell(50, 7, $datesecure);
        $this->ppdf->Ln(0);
        $this->ppdf->Cell(154);
        $this->ppdf->Cell(53, 8, '________________');

        $this->ppdf->Ln(5);
        $this->ppdf->Cell(132);
        $this->ppdf->Cell(26, 7, 'Date Effective:');
        $this->ppdf->Cell(50, 7, $effectivedate);
        $this->ppdf->Ln(0);
        $this->ppdf->Cell(156);
        $this->ppdf->Cell(53, 8, '________________');
        $this->ppdf->Ln(7);

        $this->ppdf->Cell(5);
        $this->ppdf->Cell(12, 7, 'Name:');
        $this->ppdf->Cell(110, 7, $name);
        $this->ppdf->Ln(0);
        $this->ppdf->Cell(17);
        $this->ppdf->Cell(115, 8, '__________________________________________________________');

        $this->ppdf->Cell(16, 7, 'Signature:');
        $this->ppdf->Cell(40, 8, '___________________');
        $this->ppdf->Ln(6);
        $this->ppdf->Cell(5);
        $this->ppdf->Cell(30, 7, 'Company/Agency:');
        $this->ppdf->Cell(90, 7, $company);
        $this->ppdf->Ln(0);
        $this->ppdf->Cell(35);
        $this->ppdf->Cell(100, 8, '_____________________________________________________________________________');

        $this->ppdf->Ln(7);
        $this->ppdf->Cell(5);
        $this->ppdf->Cell(33, 7, 'Section/Assignment:');
        $this->ppdf->Cell(90, 6, $dept);
        $this->ppdf->Ln(0);
        $this->ppdf->Cell(38);
        $this->ppdf->Cell(90, 7, '___________________________________________________________________________');

        $this->ppdf->Ln(7);
        $this->ppdf->Cell(5);
        $this->ppdf->Cell(19, 7, 'REASON:');
        $this->ppdf->Cell(90, 7, $reason);
        $this->ppdf->Ln(0);
        $this->ppdf->Cell(22);
        $this->ppdf->Cell(90, 7, '____________________');

        $this->ppdf->Ln(9);
        $this->ppdf->Cell(5);
        $this->ppdf->SetFont('Helvetica', '', 10);
        $this->ppdf->Cell(108, 6, '_________________________ Section Accountability');
        $this->ppdf->Cell(105, 6, '_________________________ ID');
        $this->ppdf->Ln();
        $this->ppdf->Cell(5);
        $this->ppdf->Cell(108, 6, '_________________________ Financial Accountability');
        $this->ppdf->Cell(105, 6, '_________________________ Locker');
        $this->ppdf->Ln();
        $this->ppdf->Cell(5);
        $this->ppdf->Cell(108, 6, '_________________________ SSD');
        $this->ppdf->Cell(105, 6, '_________________________ Dormitory');
        //$this->ppdf->Cell(105 ,6, '____________________________ Uniform');
        $this->ppdf->Ln(7);

        $this->ppdf->Cell(5);
        $this->ppdf->SetFont('Helvetica', 'B', 10);
        $this->ppdf->Cell(30, 10, 'RECOMMENDED FOR APPROVAL:');

        $this->ppdf->Ln(10);
        $this->ppdf->Cell(5);
        $this->ppdf->SetFont('Helvetica', '', 10);
        $this->ppdf->Cell(85, 5, '______________________________________');
        $this->ppdf->Cell(17);
        $this->ppdf->Cell(25, 5, 'EPAS Grade');
        $this->ppdf->Cell(55, 7, $numrate, 1);
        $this->ppdf->Ln(5);
        $this->ppdf->SetFont('Helvetica', '', 10);
        $this->ppdf->Cell(5);
        $this->ppdf->Cell(78, 4, 'Area Sup./ Promo Sup. / Sr. Supervisor', 0, 0, 'C');
        $this->ppdf->Cell(15);
        //$this->ppdf->Cell(85 ,5, 'EPAS Grade',0,0,'C'); 

        $this->ppdf->Ln(8);
        $this->ppdf->Cell(5);
        $this->ppdf->SetFont('Helvetica', '', 10);
        $this->ppdf->Cell(85, 5, '______________________________________');
        $this->ppdf->Cell(18);
        //$this->ppdf->Cell(100 ,5, '________________________________________');
        $this->ppdf->Ln(5);
        $this->ppdf->SetFont('Helvetica', '', 10);
        $this->ppdf->Cell(5);
        $this->ppdf->Cell(80, 4, 'HRD Supervisor', 0, 0, 'C');
        $this->ppdf->Cell(15);
        //$this->ppdf->Cell(92,4, '  HRD Supervisor',0,0,'C'); 

        $this->ppdf->Ln(7);

        $this->ppdf->Cell(5);
        $this->ppdf->SetFont('Helvetica', 'B', 10);
        $this->ppdf->Cell(25, 6, 'Approved by:');

        $this->ppdf->Cell(15);
        $this->ppdf->Cell(150, 10, 'Received by:', 0, 0, 'C');

        $this->ppdf->Ln(10);
        $this->ppdf->SetFont('Helvetica', '', 10);
        $this->ppdf->Cell(6);
        $this->ppdf->Cell(70, 4, '___________________________________', 0, 0, 'C');
        $this->ppdf->Cell(22);
        $this->ppdf->Cell(100, 4, '________________________________________', 0, 0, 'C');
        $this->ppdf->Ln(5);
        $this->ppdf->SetFont('Helvetica', '', 10);
        $this->ppdf->Cell(5);
        $this->ppdf->Cell(70, 4, '  Mall / Store Manager ', 0, 0, 'C');
        $this->ppdf->Cell(40);
        $this->ppdf->Cell(70, 4, 'HRD CORP - Promo Coordinator', 0, 0, 'C');

        $time           = date('H-i-s');
        $filename       = 'generated_clearance_' . $emp_id . '_' . $this->today . '_' . $time . '.pdf';
        // $base_path      = $_SERVER['DOCUMENT_ROOT'];
        // $relative_path  = '/hrmsv2/document/generated_clearance/';
        $base_path      = FCPATH;
        $relative_path  = '../document/generated_clearance/';
        $path           = $base_path . $relative_path . $filename;
        $link           = 'id=' . $filename;

        $cond   = array('scdetails_id' => $scdetails_id);
        $data   = array('generated_clearance' => $filename);
        $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);

        $this->ppdf->Output($path, 'F');
        ob_clean();
        echo json_encode(['message' => 'success', 'id' => $link]);
    }

    public function viewPdfClearance()
    {
        $input      = $this->input->get(NULL, TRUE);
        $filename   = $input['id'];
        $pdfPath    = '../document/generated_clearance/' . $filename;

        if (file_exists($pdfPath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $filename . '"');
            readfile($pdfPath);
        } else {
            header('HTTP/1.0 404 Not Found');
            echo 'File not found';
        }
    }
}
