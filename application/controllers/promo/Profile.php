<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public $userId;
    public $hrCode;
    public $today;
    public $adminUser;
    function __construct()
    {
        parent::__construct();
        $this->userId       = $_SESSION['emp_id'];
        $cond               = array('emp_id' => $_SESSION['emp_id']);
        $cc                 = $this->promo_model->selectAll_tcR('employee3', $cond)['company_code'];
        $_SESSION['hrCode'] = ($cc != '07') ? 'asc' : 'cebo';
        $this->hrCode       = $_SESSION['hrCode'];
        $this->today        = date('Y-m-d');
        $this->adminUser    = array('01186-2023');

        $cond = array('emp_id' => $_SESSION['emp_id'], 'user_status' => 'active');
        $promo = $this->promo_model->selectAll_tcR('promo_user', $cond);
        if (!isset($this->userId) && !$promo) {
            redirect('http://' . $_SERVER['SERVER_ADDR'] . ":" . $_SERVER['SERVER_PORT'] . '/hrms');
        }
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
            // $data['current']        = $this->promo_model->promoDetails_wttA($cond, 'employee3', 'promo_record');
            // $data['previous']       = $this->promo_model->promoDetails_wttA($cond, 'employmentrecord_', 'promo_history_record');
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

            $this->load->view('promo/page/profile_page_response', $data);
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
            $this->load->view('promo/page/profile_page_response', $data);
        }
    }

    public function profilePic()
    {
        $input  = $this->input->post(NULL, TRUE);

        if (isset($_FILES['file']['name'])) {
            $image_name     = addslashes($_FILES['file']['name']);
            $array          = explode(".", $image_name);
            $filename       = $input['emp_id'] . "=" . date('Y-m-d') . "=" . 'Profile' . "=" . date('H-i-s-A') . "." . end($array);
            $base_path      = FCPATH;
            $relative_path  = '../images/users/';
            $path           =  $base_path . $relative_path . $filename;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
                $link   =  $relative_path  . $filename;
                $cond   = array('app_id' => $input['emp_id']);
                $data   = array('photo' => $link);
                $update = $this->promo_model->update_twdA('applicant', $cond, $data);
            }
        }

        if ($update) {
            $activity = "Updated Promo Profile Picture of $input[emp_id]";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $insert = $this->promo_model->insert_tdA('logs', $data);

            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function basicInfo()
    {
        $input = $this->input->post(NULL, TRUE);

        $name = ucwords(strtolower($input['ln'] . ', ' . $input['fn'] . ' ' . $input['mn']));
        if ($input['suffix'] != '') {
            $name = ucwords(strtolower($input['ln'] . ', ' . $input['fn'] . ' ' . $input['suffix'] . ' ' . $input['mn']));
        }

        $cond1  = array('app_code'  => $input['appcode']);
        $cond2  = array('app_id'    => $input['emp_id']);
        $cond   = array('emp_id'    => $input['emp_id']);
        $data1  = array(
            'lastname'      => ucwords(strtolower($input['ln'])),
            'firstname'     => ucwords(strtolower($input['fn'])),
            'middlename'    => ucwords(strtolower($input['mn'])),
            'suffix'        => ucwords(strtolower($input['suffix'])),
        );
        $data2  = array(
            'lastname'      => ucwords(strtolower($input['ln'])),
            'firstname'     => ucwords(strtolower($input['fn'])),
            'middlename'    => ucwords(strtolower($input['mn'])),
            'suffix'        => ucwords(strtolower($input['suffix'])),
            'birthdate'     => $input['birthdate'],
            'religion'      => $input['religion'],
            'civilstatus'   => $input['civilstatus'],
            'gender'        => $input['gender'],
            'citizenship'   => $input['citizenship'],
            'bloodtype'     => $input['bloodtype'],
            'weight'        => $input['weight'],
            'height'        => $input['height'],
        );

        $data3 = array('name' => $name);
        $data4 = array('names' => $name);

        $update1 = $this->promo_model->update_twdA('applicants', $cond1, $data1);
        $update2 = $this->promo_model->update_twdA('applicant', $cond2, $data2);
        $update3 = $this->promo_model->update_twdA('employee3', $cond, $data3);
        $update4 = $this->promo_model->update_twdA('employmentrecord_', $cond, $data4);

        if ($update1 && $update2 && $update3 && $update4) {

            $activity = "Updated Promo Basic Info of $input[emp_id]";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $insert = $this->promo_model->insert_tdA('logs', $data);

            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function benefits()
    {
        $input = $this->input->post(NULL, TRUE);
        $data = array(
            'philhealth'        => $input['philhealth'],
            'sss_no'            => $input['sss_no'],
            'card_no'           => $input['card_no'],
            'pagibig'           => $input['pagibig'],
            'pagibig_tracking'  => $input['pagibig_tracking'],
            'tin_no'            => $input['tin_no'],
            'cedula_no'         => $input['cedula_no'],
            'cedula_date'       => $input['cedula_date'],
            'cedula_place'      => $input['cedula_place'],
        );
        $cond = array('app_id' => $input['emp_id']);
        $update = $this->promo_model->update_twdA('applicant_otherdetails', $cond, $data);

        if ($update) {
            $activity = "Updated Promo Benefits Details of $input[emp_id]";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $insert = $this->promo_model->insert_tdA('logs', $data);

            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function contactInfo()
    {
        $input = $this->input->post(NULL, TRUE);
        $data = array(
            'home_address'              => $input['home_address'],
            'city_address'              => $input['city_address'],
            'contactno'                 => $input['contactno'],
            'telno'                     => $input['telno'],
            'email'                     => $input['email'],
            'facebookAcct'              => $input['facebookAcct'],
            'twitterAcct'               => $input['twitterAcct'],
            'contact_person'            => ucwords(strtolower($input['contact_person'])),
            'contact_person_address'    => $input['contact_person_address'],
            'contact_person_number'     => $input['contact_person_number'],
        );
        $cond = array('app_id' => $input['emp_id']);
        $update = $this->promo_model->update_twdA('applicant', $cond, $data);

        if ($update) {
            $activity = "Updated Promo Contact Info of $input[emp_id]";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $insert = $this->promo_model->insert_tdA('logs', $data);

            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function famEducBackground()
    {
        $input = $this->input->post(NULL, TRUE);
        $data = array(
            'mother'        => ucwords(strtolower($input['mother'])),
            'father'        => ucwords(strtolower($input['father'])),
            'guardian'      => ucwords(strtolower($input['guardian'])),
            'spouse'        => ucwords(strtolower($input['spouse'])),
            'attainment'    => $input['attainment'],
            'school'        => $input['school'],
            'course'        => $input['course'],
            'hobbies'       => $input['hobbies'],
            'specialSkills' => $input['specialSkills'],
        );
        $cond = array('app_id' => $input['emp_id']);
        $update = $this->promo_model->update_twdA('applicant', $cond, $data);

        if ($update) {
            $activity = "Updated Promo Family and Education Background of $input[emp_id]";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $insert = $this->promo_model->insert_tdA('logs', $data);

            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function modal_form()
    {
        $input  = $this->input->post(NULL, TRUE);
        if ($input['action'] == 'edit') {
            if ($input['page'] == 'training') {
                $value = $this->promo_model->selectAll_tcR('application_seminarsandeligibility', array('no' => $input['no']));
                echo '<div class="row">';
                echo '<input type="hidden" class="form-control" name="app_id" value="' . $value['app_id'] . '">';
                echo '<input type="hidden" class="form-control" name="no" value="' . $value['no'] . '">';
                echo '<input type="hidden" class="form-control" name="page" value="' . $input['page'] . '">';
                echo '<div class="col-lg-12">';
                echo '<div class="row mb-3">
                        <div class="col-sm-6">
                            <label>Certificate Name</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-brain font-22"></i>
                                <input type="text" class="form-control" name="name" value="' . $value['name'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Certificate Date</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-calendar font-22"></i>
                                <input type="text" class="form-control datepicker" name="dates" value="' . $value['dates'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Certificate Location</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-map-pin font-22"></i>
                                <input type="text" class="form-control" name="location" value="' . $value['location'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Certificate</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-upload font-22"></i>
                                <input type="file" class="form-control" name="sem_certificate">
                            </div>
                        </div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } else if ($input['page'] == 'characterRef') {
                $value = $this->promo_model->selectAll_tcR('application_character_ref', array('no' => $input['no']));
                echo '<div class="row">';
                echo '<input type="hidden" class="form-control" name="app_id" value="' . $value['app_id'] . '">';
                echo '<input type="hidden" class="form-control" name="no" value="' . $value['no'] . '">';
                echo '<input type="hidden" class="form-control" name="page" value="' . $input['page'] . '">';
                echo '<div class="col-lg-12">';
                echo '<div class="row mb-3">
                        <div class="col-sm-6">
                            <label>Name</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-user font-22"></i>
                                <input type="text" class="form-control" name="name" value="' . $value['name'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Position</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-briefcase font-22"></i>
                                <input type="text" class="form-control" name="position" value="' . $value['position'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Company/Location</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-map-pin font-22"></i>
                                <input type="text" class="form-control" name="company" value="' . $value['company'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Contact No.</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-phone font-22"></i>
                                <input type="text" class="form-control" name="contactno" value="' . $value['contactno'] . '">
                            </div>
                        </div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } else if ($input['page'] == 'empHistory') {
                $value   = $this->promo_model->selectAll_tcR('application_employment_history', array('no' => $input['no']));
                echo '<div class="row">';
                echo '<input type="hidden" class="form-control" name="app_id" value="' . $value['app_id'] . '">';
                echo '<input type="hidden" class="form-control" name="no" value="' . $value['no'] . '">';
                echo '<input type="hidden" class="form-control" name="page" value="' . $input['page'] . '">';
                echo '<div class="col-lg-12">';
                echo '<div class="row mb-3">
                        <div class="col-sm-6">
                            <label>Company</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-buildings font-22"></i>
                                <input type="text" class="form-control" name="company" value="' . $value['company'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Position</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-briefcase font-22"></i>
                                <input type="text" class="form-control" name="position" value="' . $value['position'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Date Start</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-calendar-star font-22"></i>
                                <input type="text" class="form-control datepicker" name="yr_start" value="' . $value['yr_start'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Date End</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-calendar-exclamation font-22"></i>
                                <input type="text" class="form-control datepicker" name="yr_ends" value="' . $value['yr_ends'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Address/Location</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-map-pin font-22"></i>
                                <input type="text" class="form-control" name="address" value="' . $value['address'] . '">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Certificate</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-upload font-22"></i>
                                <input type="file" class="form-control" name="emp_certificate">
                            </div>
                        </div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else if ($input['action'] == 'add') {
            if ($input['page'] == 'training') {
                echo '<div class="row">';
                echo '<input type="hidden" class="form-control" name="app_id" value="' . $input['no'] . '">';
                echo '<input type="hidden" class="form-control" name="page" value="' . $input['page'] . '">';
                echo '<div class="col-lg-12">';
                echo '<div class="row mb-3">
                        <div class="col-sm-6">
                            <label>Certificate Name</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-brain font-22"></i>
                                <input type="text" class="form-control" name="name">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Certificate Date</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-calendar font-22"></i>
                                <input type="text" class="form-control datepicker" name="dates">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Certificate Location</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-map-pin font-22"></i>
                                <input type="text" class="form-control" name="location">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Certificate</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-upload font-22"></i>
                                <input type="file" class="form-control" name="sem_certificate">
                            </div>
                        </div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } else if ($input['page'] == 'characterRef') {
                echo '<div class="row">';
                echo '<input type="hidden" class="form-control" name="app_id" value="' . $input['no'] . '">';
                echo '<input type="hidden" class="form-control" name="page" value="' . $input['page'] . '">';
                echo '<div class="col-lg-12">';
                echo '<div class="row mb-3">
                        <div class="col-sm-6">
                            <label>Name</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-user font-22"></i>
                                <input type="text" class="form-control" name="name">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Position</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-briefcase font-22"></i>
                                <input type="text" class="form-control" name="position">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Company/Location</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-map-pin font-22"></i>
                                <input type="text" class="form-control" name="company">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Contact No.</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-phone font-22"></i>
                                <input type="text" class="form-control" name="contactno"">
                            </div>
                        </div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } else if ($input['page'] == 'empHistory') {
                echo '<div class="row">';
                echo '<input type="hidden" class="form-control" name="app_id" value="' . $input['no'] . '">';
                echo '<input type="hidden" class="form-control" name="page" value="' . $input['page'] . '">';
                echo '<div class="col-lg-12">';
                echo '<div class="row mb-3">
                        <div class="col-sm-6">
                            <label>Company</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-buildings font-22"></i>
                                <input type="text" class="form-control" name="company">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Position</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-briefcase font-22"></i>
                                <input type="text" class="form-control" name="position">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Date Start</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-calendar-star font-22"></i>
                                <input type="text" class="form-control datepicker" name="yr_start">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Date End</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-calendar-exclamation font-22"></i>
                                <input type="text" class="form-control datepicker" name="yr_ends">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Address/Location</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bxs-map-pin font-22"></i>
                                <input type="text" class="form-control" name="address">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label>Certificate</label>
                            <div class="input-group mb-3">
                                <i class="input-group-text fadeIn animated bx bx-upload font-22"></i>
                                <input type="file" class="form-control" name="emp_certificate">
                            </div>
                        </div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } else if ($input['page'] == 'supervisor') {
                echo '<div class="row">';
                echo '<input type="hidden" class="form-control" name="app_id" value="' . $input['no'] . '">';
                echo '<input type="hidden" class="form-control" name="page" value="' . $input['page'] . '">';
                echo '<div class="col-lg-4 border-end">';
                echo '<div class="text-center">';
                echo ' <label class="text-primary">Filter to tag a Supervisor</label>';
                echo '</div>';
                echo '<div class="row">';
                echo '<div class="col-sm-12">
                        <label>Company</label>
                        <div class="input-group input-group-sm mb-1 font-22">
                            <select class="form-select select2" name="company_code" data-placeholder="Select company" onchange="getSupervisor_bc(this.value)">
                                <option value="">Select company</option>';
                $company = $this->promo_model->selectAll('locate_company');
                foreach ($company as $value) {
                    echo '<option value="' . $value['company_code'] . '">' . $value['company'] . '</option>';
                }
                echo    '</select>';
                echo '</div>';
                echo '</div>';
                echo    '<div class="col-sm-12">
                            <label>Business Unit</label>
                            <div class="input-group input-group-sm mb-1">
                                <select class="form-select select2" name="bunit_code" data-placeholder="Select Business Unit" onchange="getSupervisor_dc(this.value)">
                                    <option value="">Select Business Unit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label>Department</label>
                            <div class="input-group input-group-sm mb-1">
                                <select class="form-select select2" name="dept_code" data-placeholder="Select Department" onchange="getSupervisor_sc(this.value)">
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label>Section</label>
                            <div class="input-group input-group-sm mb-1">
                                <select class="form-select select2" name="section_code" data-placeholder="Select Section" onchange="getSupervisor_ssc(this.value)">
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label>Sub-section</label>
                            <div class="input-group input-group-sm mb-1">
                                <select class="form-select select2" name="sub_section_code" data-placeholder="Select Sub-section" onchange="getSupervisor_uc(this.value)">
                                    <option value="">Select Sub-section</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label>Unit</label>
                            <div class="input-group input-group-sm mb-1">
                                <select class="form-select select2" name="unit_code" data-placeholder="Select Unit" onchange="supervisorList(this.value,\'' . $input['no'] . '\')">
                                    <option value="">Select Unit</option>
                                </select>
                            </div>
                        </div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="col-lg-8 supervisorList"></div>';
                echo '</div>';
            } else if ($input['page'] == 'userAccount') {
                $name = $this->promo_model->getName_employee3($input['no']);
                echo '<div class="row">';
                echo '<input type="hidden" class="form-control" name="emp_id" value="' . $input['no'] . '">';
                echo '<div class="col-lg-12">';
                echo '<div class="row">
                        <div class="col-sm-12">
                            <label>Account</label>
                            <div class="input-group mb-2">
                                <i class="input-group-text fadeIn animated bx bx-id-card font-22"></i>
                                <input type="text" class="form-control readonly" name="account" value="[' . $input['no'] . '] ' . $name . '" readonly>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label>User Type</label>
                            <div class="input-group mb-2">
                                <i class="input-group-text fadeIn animated bx bx-user-pin font-22"></i>
                                <input type="text" class="form-control readonly" name="usertype" value="employee" readonly style="text-transform: uppercase;">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label>Username</label>
                            <div class="input-group mb-2">
                                <i class="input-group-text fadeIn animated bx bx-user font-22"></i>
                                <input type="text" class="form-control readonly" name="username" value="' . $input['no'] . '" readonly>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label>Password</label>
                            <div class="input-group mb-1">
                                <i class="input-group-text fadeIn animated bx bx-key font-22"></i>
                                <input type="password" class="form-control" name="password">
                            </div>
                        </div>
                        <div class="col-sm-12 d-md-flex d-grid align-items-center gap-3 justify-content-left">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="viewPassword" id="flexCheckDefault" onclick="passwordFunc(this.name)">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Show Password
                                </label>
                            </div>
                            <div class="form-check form-switch">
						        <input class="form-check-input" type="checkbox" role="switch" name="setPassword" id="flexSwitchCheckDefault1" onclick="passwordFunc(this.name)">
						        <label class="form-check-label" for="flexSwitchCheckDefault1">
                                    Set default Password : Hrms2014
                                </label>
						    </div>
                        </div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else if ($input['action'] == 'deactivate') {
            $cond   = array('user_no' => $input['no']);
            $data   = array('user_status' => 'inactive', 'date_updated' => date('Y-m-d H:i:s'));
            $update = $this->promo_model->update_twdA('users', $cond, $data);
            if ($update) {
                echo json_encode(['message' => 'success']);
            } else {
                echo json_encode(['message' => 'failed']);
            }
        } else if ($input['action'] == 'activate') {

            $cond   = array('user_no' => $input['no']);
            $data   = array('user_status' => 'active', 'date_updated' => date('Y-m-d H:i:s'));
            $update = $this->promo_model->update_twdA('users', $cond, $data);
            if ($update) {
                echo json_encode(['message' => 'success']);
            } else {
                echo json_encode(['message' => 'failed']);
            }
        } else if ($input['action'] == 'reset') {
            $cond   = array('user_no' => $input['no']);
            $data   = array('password' => md5('Hrms2014'), 'date_updated' => date('Y-m-d H:i:s'));
            $update = $this->promo_model->update_twdA('users', $cond, $data);
            if ($update) {
                echo json_encode(['message' => 'success']);
            } else {
                echo json_encode(['message' => 'failed']);
            }
        } else {
            if ($input['page'] == 'supervisor') {
                $delete = $this->promo_model->delete_tcr_row('leveling_subordinates', array('record_no' => $input['no']));
            } else if ($input['page'] == 'userAccount') {

                $delete = $this->promo_model->delete_tcr_row('users', array('user_no' => $input['no']));
            }
            if ($delete) {
                echo json_encode(['message' => 'success']);
            } else {
                echo json_encode(['message' => 'failed']);
            }
        }
    }

    public function save_modal_form()
    {
        $input = $this->input->post(NULL, TRUE);

        if ($input['page'] == 'training') {
            $cert   = '';
            if (isset($_FILES['sem_certificate']['name'])) {

                $image_name     = addslashes($_FILES['sem_certificate']['name']);
                $array          = explode(".", $image_name);
                $filename       = $input['app_id'] . "=" . date('Y-m-d') . "=" . 'Seminar-Certificate' . "=" . date('H-i-s-A') . "." . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/seminar_certificate/';
                $path           =  $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES['sem_certificate']['tmp_name'], $path)) {
                    $cert = $relative_path . $filename;
                }
            }

            $data1  = array(
                'name'              => $input['name'],
                'dates'             => $input['dates'],
                'location'          => $input['location'],
                'sem_certificate'   => $cert,
            );
            $data2  = array(
                'app_id'            => $input['app_id'],
                'name'              => $input['name'],
                'dates'             => $input['dates'],
                'location'          => $input['location'],
                'sem_certificate'   => $cert,
            );
            if (isset($input['no'])) {
                $cond   = array('no' => $input['no']);
                $update = $this->promo_model->update_twdA('application_seminarsandeligibility', $cond, $data1);
            } else {
                $insert = $this->promo_model->insert_tdA('application_seminarsandeligibility', $data2);
            }

            $activity = "Added/Updated Promo Training Data of $input[app_id]";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $insert = $this->promo_model->insert_tdA('logs', $data);
        } else if ($input['page'] == 'characterRef') {

            $data1  = array(
                'name'      => $input['name'],
                'position'  => $input['position'],
                'company'   => $input['company'],
                'contactno' => $input['contactno'],
            );
            $data2  = array(
                'app_id'    => $input['app_id'],
                'name'      => $input['name'],
                'position'  => $input['position'],
                'company'   => $input['company'],
                'contactno' => $input['contactno'],
            );
            if (isset($input['no'])) {
                $cond   = array('no' => $input['no']);
                $update = $this->promo_model->update_twdA('application_character_ref', $cond, $data1);
            } else {
                $insert = $this->promo_model->insert_tdA('application_character_ref', $data2);
            }
            $activity = "Added/Updated Promo Character Reference of $input[app_id]";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $insert = $this->promo_model->insert_tdA('logs', $data);
        } else if ($input['page'] == 'empHistory') {

            $cert   = '';
            if (isset($_FILES['emp_certificate']['name'])) {

                $image_name     = addslashes($_FILES['emp_certificate']['name']);
                $array          = explode(".", $image_name);
                $filename       = $input['app_id'] . "=" . date('Y-m-d') . "=" . 'Employment_Certificate' . "=" . date('H-i-s-A') . "." . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/employment_certificate/';
                $path           =  $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES['emp_certificate']['tmp_name'], $path)) {
                    $cert = $relative_path . $filename;
                }
            }

            $data1  = array(
                'company'           => $input['company'],
                'position'          => $input['position'],
                'yr_start'          => $input['yr_start'],
                'yr_ends'           => $input['yr_ends'],
                'address'           => $input['address'],
                'emp_certificate'   => $cert,
            );
            $data2  = array(
                'app_id'            => $input['app_id'],
                'company'           => $input['company'],
                'position'          => $input['position'],
                'yr_start'          => $input['yr_start'],
                'yr_ends'           => $input['yr_ends'],
                'address'           => $input['address'],
                'emp_certificate'   => $cert,
            );

            if (isset($input['no'])) {
                $cond   = array('no' => $input['no']);
                $update = $this->promo_model->update_twdA('application_employment_history', $cond, $data1);
            } else {
                $insert = $this->promo_model->insert_tdA('application_employment_history', $data2);
            }
            $activity = "Added/Updated Promo Employment History of $input[app_id]";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $insert = $this->promo_model->insert_tdA('logs', $data);
        }

        $operation = isset($input['no']) ? $update : $insert;
        $message = $operation ? 'success' : 'failed';

        echo json_encode(['message' => $message]);
    }

    public function viewExam_history()
    {
        $input = $this->input->post(NULL, TRUE);
        echo '<table class="table table-hover table-sm">
                <tr>
                    <th>No.</th>
                    <th>Examination Date</th>
                    <th>Applying For</th>
                    <th>Exam Code</th>
                    <th>Exam Type</th>
                    <th>Score</th>
                    <th>Norm</th>
                    <th>Result</th>
                </tr>';
        $cond = array('app_id' => $input['emp_id'], 'phase' => 'Examination', 'status' => 'completed');
        $appHist = $this->promo_model->selectAll_tcA('application_history', $cond);
        if ($appHist) {
            $n = 0;
            $mergedData = [];
            $examDetails = $this->promo_model->selectAll_tcA('application_exams2take', array('app_id' => $input['emp_id']));

            for ($i = 0; $i < count($appHist); $i++) {
                $mergedRow = array_merge($appHist[$i], $examDetails[$i]);
                $mergedData[] = $mergedRow;
            }
            foreach ($mergedData as $value) {
                $n++;
                $exstr = explode(",", $value['description']);
                $excode = explode(" ", $exstr[1]);
                $exam_val = $input['emp_id'] . "|" . $excode[0];
                $input['code'] = $excode[0];
                $exam_ref = $value['no'] . '/' . $input['emp_id'];
                $examDet  = $this->promo_model->selectAll_tcR('application_examdetails', array('exam_ref' => $exam_ref));
                $exam_codename = $this->promo_model->selectAll_tcR('application_examtypes', array('exam_code' => $excode[0]));
                $extype = $examDet['exam_type'];
                if ($extype == "EXB") {
                    $overall = 28;
                } elseif ($extype == "ACCP-A" || $extype == "ACCP-B") {
                    $overall = 10;
                } elseif ($extype == "AIT-A") {
                    $overall = 60;
                } elseif ($extype == "AIT-B") {
                    $overall = 50;
                } elseif ($extype == "FIT") {
                    $overall = 12;
                } elseif ($extype == "NTA" || $extype == "VAT") {
                    $overall = 25;
                } elseif ($extype == "STAR" || $extype == "SACHS") {
                    $overall = 0;
                } else {
                    $overall = 0;
                }
                $norm = 'N/A';
                $examNorms  = $this->promo_model->selectAll_tcA('application_exam_norms', array('n_type' => $extype));
                foreach ($examNorms as $valNorm) {
                    if (intval($examDet['exam_score']) >= $valNorm['n_low'] && intval($examDet['exam_score']) <= $valNorm['n_high']) {
                        $norm = $valNorm['n_desc'];
                    }
                }

                if ($value['result'] == "passed") {
                    $result = '<span class="badge bg-success">Passed</span>';
                } else if ($value['result'] == "assessment") {
                    $result = '<span class="badge bg-primary">For Assessment</span>';
                } else if ($value['result'] == "failed") {
                    $result = '<span class="badge bg-danger">Failed</span>';
                } else {
                    $result = '<span class="badge bg-warning">' . $value['result'] . '</span>';
                }

                echo '<tr>
                        <td>' . $n . '.</td>
                        <td>' . date('F j, Y', strtotime($value['date_time'])) . '</td>
                        <td>' . $value['position'] . '</td>
                        <td>' . $exam_codename['exam_codename'] . '</td>
                        <td>' . $extype . '</td>
                        <td>' . $examDet['exam_score'] . '/' . $overall . '</td>
                        <td>' . $norm . '</td>
                        <td>' . $result . '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
    }

    public function viewApp_details()
    {
        $input = $this->input->post(NULL, TRUE);
        echo '<table class="table table-sm">
                <tr>
                    <th>Date Accomplished</th>
                    <th>Description</th>
                    <th>Applying For</th>
                    <th>Status</th>
                    <th>Phases/Process</th>
                </tr>';
        $query = $this->promo_model->selectAll_tcA('application_history', array('app_id' => $input['emp_id']));
        foreach ($query as $row) {
            echo '<tr>
                    <td>' . date('F j, Y', strtotime($row['date_time'])) . '</td>
                    <td>' . $row['description'] . '</td>
                    <td>' . $row['position'] . '</td>
                    <td>' . $row['status'] . '</td>
                    <td>' . $row['phase'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function viewInt_details()
    {
        $input = $this->input->post(NULL, TRUE);
        echo '<table class="table table-sm">
                <tr>
                    <th>Date Interviewed</th>
                    <th>Interview Code</th>
                    <th>Interviewer</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>';
        $cond = array('interviewee_id' => $input['emp_id']);
        $interview = $this->promo_model->selectAll_tcA('application_interview_details_history', $cond);
        foreach ($interview as $row) {
            echo '<tr>
                    <td>' . date('F j, Y', strtotime($row['date_interviewed'])) . '</td>
                    <td>' . $row['interview_code'] . '</td>
                    <td>' . $this->promo_model->getName_employee3($row['interviewer_id']) . '</td>
                    <td>' . $row['interview_status'] . '</td>
                    <td>' . $row['interviewer_remarks'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function appHistory()
    {
        $input = $this->input->post(NULL, TRUE);
        $data = array(
            'position_applied'  => $input['position_applied'],
            'date_applied'      => $input['date_applied'],
            'date_examined'     => $input['date_examined'],
            'exam_results'      => $input['exam_results'],
            'date_brief'        => $input['date_brief'],
            'date_hired'        => $input['date_hired'],
            'aeregular'         => ucwords(strtolower($input['aeregular'])),
        );

        $cond = array('app_id' => $input['emp_id']);
        $update = $this->promo_model->update_twdA('application_details', $cond, $data);

        if ($update) {
            $activity = "Updated Promo Application History of $input[emp_id]";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $insert = $this->promo_model->insert_tdA('logs', $data);
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function viewContract()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        $table1     = ($input['contract'] == 'current') ? 'employee3' : 'employmentrecord_';
        $table2     = ($input['contract'] == 'current') ? 'promo_record' : 'promo_history_record';

        $cond       = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $row        = $this->promo_model->selectAll_tcR($table1, $cond);

        $eocdate = '';
        if ($row['eocdate'] != '0000-00-00') {
            $eocdate = date('F j, Y', strtotime($row['eocdate']));
        }

        $promo = $this->promo_model->selectAll_tcR($table2, $cond);
        if ($promo) {
            $i          = 0;
            $epas       = [];
            $intro      = [];
            $clearance  = [];
            $contract   = [];
            $permit     = [];
            $str        = [];
            $stores     = '';
            $cond = array('hrd_location' => $this->hrCode);
            $bus = $this->promo_model->selectAll_tcA('locate_promo_business_unit', $cond);
            foreach ($bus as $bu) {
                $hasBu = $this->promo_model->empStores($table2, $emp_id, $record_no, $bu['bunit_field']);
                if ($hasBu) {

                    $i++;
                    $bunitAcro              = $bu['bunit_acronym'];
                    $str[$bunitAcro]        = $bu['bunit_name'];
                    $epas[$bunitAcro]       = $bu['bunit_name'];
                    $intro[$bunitAcro]      = $bu['bunit_intro'];
                    $clearance[$bunitAcro]  = $bu['bunit_clearance'];
                    $contract[$bunitAcro]   = $bu['bunit_contract'];
                    $permit[$bunitAcro]     = $bu['bunit_permit'];

                    $spanStore = '<span class="spanTooltip badge bg-secondary" title="' . $bu['bunit_name'] . '">' . $bunitAcro . '</span>';
                    $stores = ($i == 1) ? $spanStore : $stores . ' ' . $spanStore;
                }
            }

            echo '<table class="table table-sm">
                    <tr>
                        <td>Employee ID</td>
                        <th>:</th>
                        <th>' . $emp_id . '</th>
                        <td>Record No.</td>
                        <th>:</th>
                        <th>' . $record_no . '</th>
                    </tr>';
            if ($promo['agency_code'] != 0 && @$promo['agency_code'] != '') {
                $cond = array('agency_code' => $promo['agency_code']);
                $agency = $this->promo_model->selectAll_tcR_tk('promo_locate_agency', $cond)['agency_name'];
                echo '<tr>
                        <td>Agency</td>
                        <th>:</th>
                        <th colspan="4" class="text-wrap">' . $agency . '</th>';
                echo '</tr>';
            }
            echo    '<tr>
                        <td>Company</td>
                        <th>:</th>
                        <th colspan="4" class="text-wrap">' . $promo['promo_company'] . '</th>
                    </tr>
                    <tr>  
                        <td>Department</td>
                        <th>:</th>
                        <th>' . $promo['promo_department'] . '</th>
                        <td>Store(s)</td>
                        <th>:</th>
                        <th>
                            <div class="align-items-center gap-1 justify-content-right d-flex flex-wrap" style="max-width: 300px;">' . $stores . '</div>
                        </th>
                    </tr>
                    <tr>    
                        <td>Promo Type</td>
                        <th>:</th>
                        <th>' . $promo['promo_type'] . '</th>
                        <td>Contract Type</td>
                        <th>:</th>
                        <th>' . $promo['type'] . '</th>
                    </tr>
                    <tr>    
                        <td>Start Date</td>
                        <th>:</th>
                        <th>' . date('F j, Y', strtotime($row['startdate'])) . '</th>
                        <td>EOC Date</td>
                        <th>:</th>
                        <th>' . $eocdate . '</th>
                    </tr>
                    <tr>
                        <td>Position</td>
                        <th>:</th>
                        <th>' . $row['position'] . '</th>
                        <td>Current Status</td>
                        <th>:</th>
                        <th>' . $row['current_status'] . '</th>
                    </tr>';
            echo    '<tr>
                        <td>EPAS</td>
                        <th>:</th>
                        <th>
                             <div class="align-items-center gap-1 justify-content-right d-flex flex-wrap" style="max-width: 300px;">';
            foreach ($epas as $key => $value) {
                $title  = $str[$key];
                $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no, 'store' => $value);
                $epas   = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
                if ($epas) {
                    $epasId = $epas['details_id'];
                    $badge = 'danger';
                    if ($epas['numrate'] >= 85) {
                        $badge = ($epas['raterSO'] == 1 && $epas['rateeSO'] == 1) ? 'success' : 'warning';
                    }

                    echo '<a href="javascript:;" onclick="viewAppraisal(\'' . $epasId . '\')"><span class="spanTooltip badge bg-' . $badge . '" title="' . $value . '">' . $key . ' | ' . $epas['numrate'] . '</span></a>';
                } else {
                    echo '<span class="spanTooltip badge bg-secondary" title="' . $title . ' - No EPAS">' . $key . '</span>';
                }
            }
            echo            '</div>
                        </th>
                        <td>INTRO</td>
                        <th>:</th>
                        <th>
                            <div class="align-items-center gap-1 justify-content-right d-flex flex-wrap" style="max-width: 300px;">';

            foreach ($intro as $key => $value) {
                $title = $str[$key];
                if ($promo[$value] != '') {
                    $path1  = FCPATH . 'document/final_requirements/others/' . $promo[$value];
                    $path2  = FCPATH . '../promo/' . $promo[$value];
                    if (file_exists($path1)) {
                        $link = base_url('/document/final_requirements/others/') . $promo[$value];
                    } else if (file_exists($path2)) {
                        $link = base_url('../promo/') . $promo[$value];
                    } else {
                        $link = base_url('../document/final_requirements/others/') . $promo[$value];
                    }

                    echo '<a href="javascript:;" onclick="viewIntro(\'' . $link . '\')"><span class="spanTooltip badge bg-primary" title="' . $title . '">' . $key . '</span></a>';
                } else {
                    echo '<span class="spanTooltip badge bg-secondary" title="' . $title . ' - No Intro">' . $key . '</span>';
                }
            }
            echo            '</div>
                        </th>
                    </tr>';
            echo    '<tr>
                        <td>CLEARANCE</td>
                        <th>:</th>
                        <th>
                             <div class="align-items-center gap-1 justify-content-right d-flex flex-wrap" style="max-width: 300px;">';
            foreach ($clearance as $key => $value) {
                $title = $str[$key];
                if ($promo[$value] != '') {
                    $path1  = FCPATH . 'document/clearance/' . $promo[$value];
                    $path2  = FCPATH . '../promo/' . $promo[$value];
                    if (file_exists($path1)) {
                        $link = base_url('/document/clearance/') . $promo[$value];
                    } else if (file_exists($path2)) {
                        $link = base_url('../promo/') . $promo[$value];
                    } else {
                        $link = base_url('../document/clearance/') . $promo[$value];
                    }

                    echo '<a href="javascript:;" onclick="viewClearance(\'' . $link . '\')"><span class="spanTooltip badge bg-primary" title="' . $title . '">' . $key . '</span></a>';
                } else {
                    echo '<span class="spanTooltip badge bg-secondary" title="' . $title . ' - No Clearance">' . $key . '</span>';
                }
            }
            echo            '</div>
                        </th>
                        <td>CONTRACT</td>
                        <th>:</th>
                        <th>
                             <div class="align-items-center gap-1 justify-content-right d-flex flex-wrap" style="max-width: 300px;">';
            foreach ($contract as $key => $value) {
                $title = $str[$key];
                if ($promo[$value] != '') {
                    $pdf    = false;
                    $path1  = FCPATH . '../document/contract/';
                    $path2  = FCPATH . '../promo/';
                    $fn     = 'contract_' . $input['emp_id'] . '_random_' . $promo[$value] . '.pdf';

                    if (file_exists($path1 . $promo[$value])) {
                        $link = base_url('../document/contract/') . $promo[$value];
                    } else if (file_exists($path2 . $promo[$value])) {
                        $link = base_url('../promo/') . $promo[$value];
                    } else if (file_exists($path1 . $fn)) {
                        $link = base_url('../document/contract/') . $fn;
                        $pdf = true;
                    } else if (file_exists($path2 . $fn)) {
                        $link = base_url('../promo/') . $fn;
                        $pdf = true;
                    } else {
                        $link = base_url('../document/contract/') . $promo[$value];
                    }

                    if ($pdf) {
                        echo '<a href="' . $link  . '" target="_blank"><span class="spanTooltip badge bg-warning" title="' . $title . ' - PDF">' . $key . '</span></a>';
                    } else {
                        echo '<a href="javascript:;" onclick="viewContracts(\'' . $link . '\')"><span class="spanTooltip badge bg-primary" title="' . $title . '">' . $key . '</span></a>';
                    }
                } else {
                    echo '<span class="spanTooltip badge bg-secondary" title="' . $title . ' - No Contract">' . $key . '</span>';
                }
            }
            echo        '</div>';
            echo    '</th>';
            echo '</tr>';
        } else {
            $cc = $row['company_code'];
            $bc = $row['bunit_code'];
            $dc = $row['dept_code'];

            $cond       = array('company_code' => $cc);
            $company    = $this->promo_model->selectAll_tcR('locate_company', $cond)['company'];
            $cond       = array('company_code' => $cc, 'bunit_code' => $bc);
            $store      = $this->promo_model->selectAll_tcR('locate_business_unit', $cond)['business_unit'];
            $stores     = '<span class="badge bg-secondary">' . $store . '</span>';
            $cond       = array('company_code' => $cc, 'bunit_code' => $bc, 'dept_code' => $dc);
            $dept       = $this->promo_model->selectAll_tcR('locate_department', $cond)['dept_name'];
            $cond       = array('company_code' => $cc, 'bunit_code' => $bc, 'dept_code' => $dc, 'section_code' => $dc);
            $section    = $this->promo_model->selectAll_tcR('locate_section', $cond)['section_name'];
            echo '<table class="table table-sm">
                    <tr>
                        <td>Employee ID</td>
                        <th>:</th>
                        <th>' . $emp_id . '</th>
                        <td>Record No.</td>
                        <th>:</th>
                        <th>' . $record_no . '</th>
                    </tr>
                    <tr>
                        <td>Company</td>
                        <th>:</th>
                        <th colspan="4">' . $company . '</th>
                    </tr>
                    <tr>  
                        <td>Department</td>
                        <th>:</th>
                        <th>' . $dept . '</th>
                        <td>Section</td>
                        <th>:</th>
                        <th>' . $section . '</th>
                    </tr>
                    <tr>
                        <td>Store(s)</td>
                        <th>:</th>
                        <th>' . $stores . '</th>    
                        <td>Employee Type</td>
                        <th>:</th>
                        <th>' . $row['emp_type'] . '</th>
                    </tr>
                    <tr>    
                        <td>Start Date</td>
                        <th>:</th>
                        <th>' . date('F j, Y', strtotime($row['startdate'])) . '</th>
                        <td>EOC Date</td>
                        <th>:</th>
                        <th>' . $eocdate . '</th>
                    </tr>
                    <tr>
                        <td>Position</td>
                        <th>:</th>
                        <th>' . $row['position'] . '</th>
                        <td>Current Status</td>
                        <th>:</th>
                        <th>' . $row['current_status'] . '</th>
                    </tr>
                    <tr>
                        <td>Contract</td>
                        <th>:</th>
                        <th>';
            if ($row['contract'] != '') {
                $pdf    = false;
                $path1  = FCPATH . '../document/contract/';
                $path2  = FCPATH . '../promo/';
                $fn     = $row['contract'];

                if (file_exists($path1 . $row['contract'])) {
                    $link = base_url('../document/contract/') . $row['contract'];
                } else if (file_exists($path2 . $row['contract'])) {
                    $link = base_url('../promo/') . $row['contract'];
                } else if (file_exists($path1 . $fn)) {
                    $link = base_url('../document/contract/') . $fn;
                    $pdf = true;
                } else if (file_exists($path2 . $fn)) {
                    $link = base_url('../promo/') . $fn;
                    $pdf = true;
                } else {
                    $link = base_url('../document/contract/') . $row['contract'];
                }

                if ($pdf) {
                    echo '<a href="' . $link  . '" target="_blank"><span class="spanTooltip badge bg-warning" title="Contract - PDF">View Contract</span></a>';
                } else {
                    echo '<a href="javascript:;" onclick="viewContracts(\'' . $link . '\')"><span class="spanTooltip badge bg-primary" title="Contract - IMG">View Contract</span></a>';
                }
            } else {
                echo '<span class="badge bg-secondary">No Contract</span>';
            }

            echo    '</th>
                    <td>Clearance</td>
                    <th>:</th>
                    <th>';
            if ($row['clearance'] != '') {
                $path1  = FCPATH . 'document/clearance/' . $row['clearance'];
                $path2  = FCPATH . '../promo/' . $row['clearance'];
                if (file_exists($path1)) {
                    $link = base_url('/document/clearance/') . $row['clearance'];
                } else if (file_exists($path2)) {
                    $link = base_url('../promo/') . $row['clearance'];
                } else {
                    $link = base_url('../document/clearance/') . $row['clearance'];
                }

                echo '<a href="javascript:;" onclick="viewClearance(\'' . $link . '\')"><span class="badge bg-primary">View Clearance</span></a>';
            } else {
                echo '<span class="badge bg-secondary">No Clearance</span>';
            }
            echo    '</th>
                    </tr>';
            echo    '<tr>
                        <td>EPAS</td>
                        <th>:</th>
                        <th colspan="4">';
            $cond = array('emp_id' => $emp_id, 'record_no' => $record_no);
            $epas = $this->promo_model->selectAll_tcR('appraisal_details', $cond);
            if ($epas) {
                $epasId = $epas['details_id'];
                $badge = 'danger';
                if ($epas['numrate'] >= 85) {
                    $badge = ($epas['raterSO'] == 1 && $epas['rateeSO'] == 1) ? 'success' : 'warning';
                }

                echo '<a href="javascript:;" onclick="viewAppraisal(\'' . $epasId . '\')"><span class="spanTooltip badge bg-' . $badge . '" title="View EPAS">' . $epas['numrate'] . '</span></a>';
            } else {
                if ($row['epas_code'] != '') {
                    $path1  = FCPATH . 'document/clearance/' . $row['epas_code'];
                    $path2  = FCPATH . '../promo/' . $row['epas_code'];
                    if (file_exists($path1)) {
                        $link = base_url('/document/clearance/') . $row['epas_code'];
                    } else if (file_exists($path2)) {
                        $link = base_url('../promo/') . $row['epas_code'];
                    } else {
                        $link = base_url('../document/clearance/') . $row['epas_code'];
                    }

                    echo '<a href="javascript:;" onclick="viewClearance(\'' . $link . '\')"><span class="badge bg-primary">View EPAS</span></a>';
                } else {
                    echo '<span class="badge bg-secondary">No EPAS</span>';
                }
            }
            echo    '</th>';
            echo '</tr>';
        }
        echo    '<tr>
                    <td>Remarks</td>
                    <th>:</th>
                    <th colspan="4" class="text-wrap"><i>' . $row['remarks'] . '</i></th>';
        echo    '</tr>';
        echo '</table>';
    }

    public function editContract()
    {
        $input          = $this->input->post(NULL, TRUE);
        $emp_id         = $input['emp_id'];
        $record_no      = $input['record_no'];
        $table1         = ($input['contract'] == 'current') ? 'employee3' : 'employmentrecord_';
        $table2         = ($input['contract'] == 'current') ? 'promo_record' : 'promo_history_record';
        $isDisabled     = ($input['contract'] == 'current') ? '' : 'disabled';
        $promo_type     = ['ROVING', 'STATION'];
        $contract_type  = ['Contractual', 'Seasonal'];
        $bunit_id       = [];
        $cond = array('hrd_location' => $this->hrCode);
        $bus = $this->promo_model->selectAll_tcoA('locate_promo_business_unit', $cond, 'bunit_name');
        foreach ($bus as $bu) {
            $hasBu = $this->promo_model->empStores($table2, $emp_id, $record_no, $bu['bunit_field']);
            if ($hasBu) {
                $bunit_id[] = $bu['bunit_id'];
            }
        }
        $cond = array('e.emp_id' => $emp_id, 'e.record_no' => $record_no);
        $row = $this->promo_model->promoDetails_wttR($cond, $table1, $table2);

        $eocdate = '';
        if ($row['eocdate'] != '0000-00-00') {
            $eocdate = $row['eocdate'];
        }

        $agency     = $this->promo_model->selectAll_tk('promo_locate_agency');
        $comp       = 'pc_name';
        $company    = $this->promo_model->selectAll('locate_promo_company');
        if ($row['agency_code'] != '' && $row['agency_code'] != '0') {
            $comp       = 'company_name';
            $company    = $this->promo_model->selectAll_tcA_tk('promo_locate_company', array('agency_code' => $row['agency_code']));
        }

        // $product = $this->promo_model->selectAll_tcA('promo_products', array('emp_id' => $emp_id, 'record_no' => $record_no));
        $department = [];
        if ($bunit_id) {
            $department = $this->promo_model->whereIN_stcd('DISTINCT', 'dept_name', 'locate_promo_department', 'bunit_id', $bunit_id);
        }
        $cond       = array('department' => $row['promo_department']);
        $vendor     = $this->promo_model->selectAll_tcA('promo_vendor_lists', $cond);
        $position   = $this->promo_model->positions();
        $emp_type   = $this->promo_model->selectAll('employee_type');
        $cutoff     = $this->promo_model->selectAll_tk('promo_schedule');

        $cond       = array('emp_id' => $emp_id);
        $recordNo   = $this->promo_model->selectAll_tcR('employee3', $cond)['record_no'];
        $cond       = array('empId' => $emp_id, 'recordNo' => $recordNo);
        $statCut    = $this->promo_model->selectAll_tcR_tk('promo_sched_emp', $cond);

        echo '<div class="row">'; //div row-1
        echo '<input type="hidden" name="emp_id" value="' . $emp_id . '">';
        echo '<input type="hidden" name="record_no" value="' . $record_no . '">';
        echo '<input type="hidden" name="contract" value="' . $input['contract'] . '">';

        echo '<div class="col-lg-6">'; //div row-1 col-1
        echo '<div class="row">'; //div row-1 col-1 row-2

        echo '<div class="col-sm-12 mb-2">
                <label>Agency</label>
                <select class="form-select form-select-sm select2clear" name="agency_code" data-placeholder="Select Agency" onchange="getCompany(this.value)">
                    <option value="">Select Agency</option>';

        foreach ($agency as $value) {
            echo '<option value="' . $value['agency_code'] . '"';
            echo ($value['agency_code'] == $row['agency_code']) ? 'selected' : '';
            echo '>' . $value['agency_name'] . '</option>';
        }
        echo    '</select>';
        echo '</div>';

        echo '<div class="col-sm-12 mb-2">
                <label>Company</label>
                <select class="form-select form-select-sm select2clear" name="promo_company" data-placeholder="Select Company" onchange="getProduct(this.value)">
                    <option value="">Select Company</option>';
        foreach ($company as $value) {
            echo '<option value="' . $value[$comp] . '"';
            echo ($value[$comp] == $row['promo_company']) ? 'selected' : '';
            echo '>' . $value[$comp] . '</option>';
        }
        echo    '</select>';
        echo '</div>';

        echo '<div class="col-sm-12 mb-2">
                <label>Promo Type</label>
                <select class="form-select form-select-sm select2" name="promo_type" data-placeholder="Select Promo Type" onchange="getStores(this.value,\'' . $row['promo_type'] . '\')">
                    <option value="">Select Promo Type</option>';
        foreach ($promo_type as $value) {
            echo '<option value="' . $value . '"';
            echo ($value == $row['promo_type']) ? 'selected' : '';
            echo '>' . $value . '</option>';
        }
        echo    '</select>';
        echo '</div>';

        echo '<div class="col-sm-12">
                <label>Store(s)</label>
                <ul class="list-group list-group-flush stores">';
        $type = ($row['promo_type'] == 'ROVING') ? 'checkbox' : 'radio';
        foreach ($bus as $bu) {
            $disabled = ($bu['status'] == 'active') ? '' : 'disabled';
            $field = $bu['bunit_id'] . '|' . $bu['bunit_field'];
            $hasBu = $this->promo_model->empStores($table2, $emp_id, $record_no, $bu['bunit_field']);
            if ($hasBu) {
                echo '<li class="list-group-item">
                        <div class="form-check form-check-success">
                            <input type="' . $type . '" class="form-check-input" name="stores[]" id="' . $field . '"  value="' . $field . '" checked onclick="getDepartment()" ' . $disabled . '>
                            <label class="form-check-label" for="' . $field . '">' . $bu['bunit_name'] . '</label>
                        </div>';
                echo '</li>';
            } else {
                if ($bu['status'] == 'active') {
                    echo '<li class="list-group-item">
                        <div class="form-check form-check-success">
                            <input type="' . $type . '" class="form-check-input" name="stores[]" id="' . $field . '"  value="' . $field . '" onclick="getDepartment()">
                            <label class="form-check-label" for="' . $field . '">' . $bu['bunit_name'] . '</label>
                        </div>';
                    echo '</li>';
                }
            }
        }
        echo    '</ul>';
        echo '</div>';

        echo '</div>'; //div row-1 col-1 row-2
        echo '</div>'; //div row-1 col-1

        echo '<div class="col-lg-6">'; //div row-1 col-2
        echo '<div class="row">'; //div row-1 col-2 row-3

        echo '<div class="col-sm-12 mb-2">
                <label>Department</label>
                <select class="form-select form-select-sm select2" name="promo_department" data-placeholder="Select Department" onchange="getVendor(this.value)">
                    <option value="">Select Department</option>';
        foreach ($department as $value) {
            echo '<option value="' . $value['dept_name'] . '"';
            echo ($value['dept_name'] == $row['promo_department']) ? 'selected' : '';
            echo '>' . $value['dept_name'] . '</option>';
        }
        echo    '</select>';
        echo '</div>';

        echo '<div class="col-sm-12 mb-2">
                <label>Vendor</label>
                <select class="form-select form-select-sm select2clear" name="vendor_code" data-placeholder="Select Vendor">
                    <option value="">Select Vendor</option>';
        foreach ($vendor as $value) {
            echo '<option value="' . $value['vendor_code'] . '"';
            echo ($value['vendor_code'] == $row['vendor_code']) ? 'selected' : '';
            echo '>' . $value['vendor_name'] . '</option>';
        }
        echo    '</select>';
        echo '</div>';

        echo '<div class="col-sm-12 mb-2">
                <label>Product</label>
                <select class="form-select form-select-sm select2" name="product[]" multiple data-placeholder="Select Product">
                    <option value="">Select Product</option>';
        // foreach ($product as $value) {
        // echo '<option value="' . $value['product'] . '" selected>' . $value['product'] . '</option>';
        // }

        $pcp = $this->promo_model->selectAll_tcA('promo_company_products', array('company' => $row['promo_company']));
        foreach ($pcp as $value) {

            $cond = ['emp_id' => $emp_id, 'record_no' => $record_no, 'product' => $value['product']];
            $chk = $this->promo_model->selectAll_tcR('promo_products', $cond);
            if ($chk) {
                echo '<option value="' . $value['product'] . '" selected>' . $value['product'] . '</option>';
            } else {
                echo '<option value="' . $value['product'] . '">' . $value['product'] . '</option>';
            }
        }
        echo    '</select>';
        echo '</div>';

        echo '<div class="col-sm-12 mb-2">
                <label>Position</label>
                <select class="form-select form-select-sm select2" name="position" data-placeholder="Select Position">
                    <option value="">Select Position</option>';
        foreach ($position as $value) {
            echo '<option value="' . $value['position_title'] . '"';
            echo ($value['position_title'] == $row['position']) ? 'selected' : '';
            echo '>' . $value['position_title'] . '</option>';
        }
        echo    '</select>';
        echo '</div>';

        echo '<div class="col-sm-12 mb-2">
            <label>Employee Type</label>
            <select class="form-select form-select-sm select2" name="emp_type" data-placeholder="Select Employee Type">
                <option value="">Select Employee Type</option>';

        foreach ($emp_type as $value) {
            echo '<option value="' . $value['emp_type'] . '"';
            echo ($value['emp_type'] == $row['emp_type']) ? 'selected' : '';
            echo '>' . $value['emp_type'] . '</option>';
        }
        echo    '</select>';
        echo '</div>';

        echo '<div class="col-sm-12 mb-2">
                <label>Contract Type</label>
                <select class="form-select form-select-sm select2" name="type" data-placeholder="Select Contract Type">
                    <option value="">Select Contract Type</option>';
        foreach ($contract_type as $value) {
            echo '<option value="' . $value . '"';
            echo ($value == $row['type']) ? 'selected' : '';
            echo '>' . $value . '</option>';
        }
        echo    '</select>';
        echo '</div>';

        echo '<div class="col-sm-12 mb-2">
                <label>Startdate</label>
                <input type="text" class="form-control form-control-sm datepicker" name="startdate" value="' . $row['startdate'] . '" placeholder="yyyy/mm/dd" onchange="getDuration(this.name)">';
        echo '</div>';

        echo '<div class="col-sm-12 mb-2">
                <label>EOCdate</label>
                <input type="text" class="form-control form-control-sm datepicker" name="eocdate" value="' . $eocdate . '" placeholder="yyyy/mm/dd" onchange="getDuration(this.value)">';
        echo '</div>';

        echo '<input type="hidden" name="duration" value="' . $row['duration'] . '">';

        echo '<div class="col-sm-12 mb-2">
                <label>Cutoff</label>
                <select class="form-select form-select-sm select2" name="statCut" data-placeholder="Select Cutoff" ' . $isDisabled . '>
                    <option value="">Select Cutoff</option>';
        foreach ($cutoff as $value) {
            $endFC = ($value['endFC'] != '') ? $value['endFC'] : 'last';
            echo '<option value="' . $value['statCut'] . '"';
            echo ($value['statCut'] == $statCut['statCut']) ? 'selected' : '';
            echo '>' . $value['startFC'] . '-' . $endFC . ' / ' . $value['startSC'] . '-' . $value['endSC'] . '</option>';
        }

        echo    '</select>';
        echo '</div>';

        echo '</div>'; //div row-1 col-2 row-3
        echo '</div>'; //div row-1 col-2 row-3

        echo '<div class="col-sm-12 mb-2 mt-2">
                <label>Remarks</label>
                <textarea class="form-control form-control-sm" name="remarks" rows="3">' . $row['remarks'] . '</textarea>';
        echo '</div>';
        echo '</div>'; //div row-1
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

    public function supervisorList()
    {
        $input = $this->input->post(NULL, TRUE);
        echo '<table class="table table-hover" id="supervisorList">
                <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th width="30%">Position</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>';
        $query = $this->promo_model->supervisorList($input);
        foreach ($query as $value) {
            echo '<tr>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input" name="supervisor[]" value="' . $value['emp_id'] . '">
                    </td>
                    <td>' . $value['emp_id'] . '</td>
                    <td>' . $value['name'] . '</td>
                    <td>' . $value['position'] . '</td>
                    <td class="text-center">' . $value['current_status'] . '</td>';
            echo '</tr>';
        }
        echo    '</tbody>';
        echo '</table>';
    }

    public function save_editContract()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        $table1     = ($input['contract'] == 'current') ? 'employee3' : 'employmentrecord_';
        $table2     = ($input['contract'] == 'current') ? 'promo_record' : 'promo_history_record';
        $updatedby  = ($input['contract'] == 'current') ? 'updated_by' : 'updatedby';
        $eocdate    = ($input['eocdate']) ? $input['eocdate'] : '0000-00-00';

        $name       = $this->promo_model->getName_employee3($emp_id);
        $cond       = array('e.emp_id' => $emp_id, 'e.record_no' => $record_no);
        $promo      = $this->promo_model->promoDetails_wttR($cond, $table1, $table2);
        $activity   = "Updated Promo Contract Details of $name ($emp_id) with Record No: $record_no | ";
        if ($promo['startdate'] != $input['startdate']) {
            $activity .= "startdate: $promo[startdate] to $input[startdate] | ";
        }
        if ($promo['eocdate'] != $eocdate) {
            $activity .= "eocdate: $promo[eocdate] to $input[eocdate] | ";

            $cond = array('emp_id' => $emp_id, 'record_no' => $record_no, 'status' => 'pending');
            $chk1 = $this->promo_model->selectAll_tcR('promo_clearance_monitoring', $cond);

            $cond = array('emp_id' => $emp_id, 'record_no' => $record_no, 'clearance_status' => 'pending');
            $chk2 = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
            if ($chk1 && $chk2) {

                $data = ['extend_date' => $eocdate];
                $cond = array('emp_id' => $emp_id, 'record_no' => $record_no, 'status' => 'pending');
                $update = $this->promo_model->update_twdA('promo_clearance_monitoring', $cond, $data);

                $data = ['date_effectivity' => $eocdate];
                $cond = array('emp_id' => $emp_id, 'record_no' => $record_no, 'clearance_status' => 'pending');
                $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);
            }
        }
        if ($promo['position'] != $input['position']) {
            $activity .= "position: $promo[position] to $input[position] | ";
        }
        if ($promo['emp_type'] != $input['emp_type']) {
            $activity .= "emp_type: $promo[emp_type] to $input[emp_type] | ";
        }
        if ($promo['promo_type'] != $input['promo_type']) {
            $activity .= "promo_type: $promo[promo_type] to $input[promo_type] | ";
        }
        if ($promo['type'] != $input['type']) {
            $activity .= "contract type: $promo[type] to $input[type] | ";
        }
        if ($promo['remarks'] != $input['remarks']) {
            $activity .= "remarks | ";
        }
        if ($input['agency_code'] && $promo['agency_code'] != $input['agency_code']) {
            $activity .= "agency_code: $promo[agency_code] to $input[agency_code] | ";
        }
        if ($promo['promo_company'] != $input['promo_company']) {
            $activity .= "company: $promo[promo_company] to $input[promo_company] | ";
        }
        if ($promo['promo_department'] != $input['promo_department']) {
            $activity .= "department: $promo[promo_department] to $input[promo_department] |";
        }

        $cond = array('emp_id' => $emp_id, 'record_no' => $record_no);
        if (isset($input['product'])) {
            $delete = $this->promo_model->delete_tcr_row('promo_products', $cond);
            $data   = array(
                'emp_id'      => $emp_id,
                'record_no'   => $record_no,
                'created_at'  => date('Y-m-d H:i:s')
            );

            foreach ($input['product'] as $value) {
                $data['product'] = $value;
                $insert = $this->promo_model->insert_tdA('promo_products', $data);
            }
        }

        $stores = [];
        $servers = ['corporate'];
        foreach ($input['stores'] as $value) {
            $str        = explode('|', $value);
            $stores[]   = $str[1];

            if ($str[1] == 'al_tal') {
                $servers[] = 'talibon';
            } else if ($str[1] == 'al_tub' || $str[1] == 'fr_tubigon') {
                $servers[] = 'tubigon';
            }
        }

        $data1 = array(
            'startdate'     => $input['startdate'],
            'eocdate'       => $eocdate,
            'emp_type'      => $input['emp_type'],
            'duration'      => $input['duration'],
            'position'      => $input['position'],
            'date_updated'  => $this->today,
            $updatedby      => $this->userId,
            'remarks'       => $input['remarks'],
        );

        $data2 = array(
            'agency_code'       => $input['agency_code'],
            'promo_company'     => $input['promo_company'],
            'promo_department'  => $input['promo_department'],
            'vendor_code'       => $input['vendor_code'],
            'promo_type'        => $input['promo_type'],
            'type'              => $input['type']
        );

        $oldStores = [];
        $cond = array('hrd_location' => $this->hrCode);
        $bus = $this->promo_model->selectAll_tcoA('locate_promo_business_unit', $cond, 'bunit_name');
        foreach ($bus as $bu) {
            $data2[$bu['bunit_field']] = in_array($bu['bunit_field'], $stores) ? 'T' : '';

            $hasBu = $this->promo_model->empStores($table2, $emp_id, $record_no, $bu['bunit_field']);
            if ($hasBu) {
                $oldStores[] = $bu['bunit_field'];
            }
        }

        if (array_diff($oldStores, $stores) || array_diff($stores, $oldStores)) {
            $f = implode(', ', array_values($oldStores));
            $t = implode(', ', array_values($stores));
            $activity .= "store(s): [$f] to [$t] |";
        }

        $cond = array('emp_id' => $emp_id, 'record_no' => $record_no);
        $update1 = $this->promo_model->update_twdA($table1, $cond, $data1);
        $update2 = $this->promo_model->update_twdA($table2, $cond, $data2);

        // inserting and updating of promo_sched_emp table ******************************************************************
        if (isset($input['statCut'])) {

            $cond = array('emp_id' => $emp_id);
            $recordNo = $this->promo_model->selectAll_tcR('employee3', $cond)['record_no'];
            $cond = array('empId' => $emp_id, 'recordNo' => $recordNo);
            $statCut = $this->promo_model->selectAll_tcR_tk('promo_sched_emp', $cond)['statCut'];
            if ($input['statCut'] != $statCut) {

                $activity .= "cutoff: $statCut to $input[statCut]";
                foreach ($servers as $server) {

                    $cond = array('empId' => $emp_id, 'recordNo' => $recordNo);
                    $chk = $this->promo_model->promoCutoff_select($server, $cond);
                    if ($chk) {
                        $data = array('statCut' => $input['statCut'], 'date_setup' => $this->today);
                        $update = $this->promo_model->promoCutoff_update($server, $cond, $data);
                    } else {
                        $data = array(
                            'statCut'       => $input['statCut'],
                            'empId'         => $emp_id,
                            'recordNo'      => $recordNo,
                            'date_setup'    => $this->today,
                        );
                        $insert = $this->promo_model->promoCutoff_insert($server, $data);
                    }
                }
            } else {
                foreach ($servers as $server) {

                    $cond = array('empId' => $emp_id, 'recordNo' => $recordNo);
                    $chk = $this->promo_model->promoCutoff_select($server, $cond);
                    if ($chk) {
                        $data = array('date_setup' => $this->today);
                        $update = $this->promo_model->promoCutoff_update($server, $cond, $data);
                    } else {
                        $data = array(
                            'statCut'       => $input['statCut'],
                            'empId'         => $emp_id,
                            'recordNo'      => $recordNo,
                            'date_setup'    => $this->today,
                        );
                        $insert = $this->promo_model->promoCutoff_insert($server, $data);
                    }
                }
            }
        }
        // inserting and updating of promo_sched_emp table ******************************************************************

        $data = array(
            'activity'  => $activity,
            'date'      => $this->today,
            'time'      => date("H:i:s"),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $logs = $this->promo_model->insert_tdA('logs', $data);

        if ($update1 && $update2) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function contractFile()
    {
        $input  = $this->input->post(NULL, TRUE);
        $table1 = ($input['contract'] == 'current') ? 'employee3' : 'employmentrecord_';
        $table2 = ($input['contract'] == 'current') ? 'promo_record' : 'promo_history_record';
        $cond   = array('e.emp_id' => $input['emp_id'], 'e.record_no' => $input['record_no']);
        $row    = $this->promo_model->promoDetails_wttR($cond, $table1, $table2);

        $file = 'bunit_' . $input['file'];
        $function = ($input['file'] == 'contract') ? 'viewContracts' : 'view' . ucfirst($input['file']);
        echo '<input type="hidden" name="emp_id" value="' . $input['emp_id'] . '">';
        echo '<input type="hidden" name="record_no" value="' . $input['record_no'] . '">';
        echo '<input type="hidden" name="contract" value="' . $input['contract'] . '">';
        echo '<input type="hidden" name="file" value="' . $input['file'] . '">';

        if ($input['file'] == 'clearance') {
            $path1  = FCPATH . 'document/clearance/';
            $path2  = FCPATH . '../promo/';
        } else if ($input['file'] == 'intro') {
            $path1  = FCPATH . 'document/final_requirements/others/';
            $path2  = FCPATH . '../promo/';
        } else if ($input['file'] == 'contract') {
            $path1  = FCPATH . '../document/contract/';
            $path2  = FCPATH . '../promo/';
        }

        $cond = array('hrd_location' => $this->hrCode);
        $bus = $this->promo_model->selectAll_tcoA('locate_promo_business_unit', $cond, 'bunit_name');
        foreach ($bus as $bu) {
            $hasBu = $this->promo_model->empStores($table2, $input['emp_id'], $input['record_no'], $bu['bunit_field']);
            if ($hasBu) {
                $pdf = false;
                $link = '';
                if ($row[$bu[$file]] != '') {

                    if ($input['file'] == 'clearance') {
                        if (file_exists($path1 . $row[$bu[$file]])) {
                            $link = base_url('/document/clearance/') . $row[$bu[$file]];
                        } else if (file_exists($path2 . $row[$bu[$file]])) {
                            $link = base_url('../promo/') . $row[$bu[$file]];
                        } else {
                            $link = base_url('../document/clearance/') . $row[$bu[$file]];
                        }
                    } else if ($input['file'] == 'intro') {
                        if (file_exists($path1 . $row[$bu[$file]])) {
                            $link = base_url('/document/final_requirements/others/') . $row[$bu[$file]];
                        } else if (file_exists($path2 . $row[$bu[$file]])) {
                            $link = base_url('../promo/') . $row[$bu[$file]];
                        } else {
                            $link = base_url('../document/final_requirements/others/') . $row[$bu[$file]];
                        }
                    } else if ($input['file'] == 'contract') {

                        $fn = 'contract_' . $input['emp_id'] . '_random_' . $row[$bu[$file]] . '.pdf';
                        if (file_exists($path1 . $row[$bu[$file]])) {
                            $link = base_url('/document/contract/') . $row[$bu[$file]];
                        } else if (file_exists($path2 . $row[$bu[$file]])) {
                            $link = base_url('../promo/') . $row[$bu[$file]];
                        } else if (file_exists($path1 . $fn)) {
                            $link = base_url('../document/contract/') . $fn;
                            $pdf = true;
                        } else if (file_exists($path2 . $fn)) {
                            $link = base_url('../promo/') . $fn;
                            $pdf = true;
                        } else {
                            $link = base_url('../document/contract/') . $row[$bu[$file]];
                        }
                    }
                }

                echo '<div class="col-sm-4  mb-3 text-center">';
                echo '<div class="card radius-10 border shadow-none">';
                if ($pdf) {
                    echo '<a href="' . $link  . '" target="_blank">
                            <div class="d-md-flex d-grid align-items-center gap-1 justify-content-center mt-2 mb-1">
                                <i class="fadeIn animated bx bx-info-circle font-18 text-primary">
                                </i><span class="badge text-secondary">Click to view PDF.</span>
                            </div>';
                    echo '</a>';
                } else {
                    if ($link) {
                        echo '<a href="javascript:;" onclick="' . $function . '(\'' . $link . '\')">   
                                <div class="d-md-flex d-grid align-items-center gap-1 justify-content-center mt-2 mb-1">
                                    <i class="fadeIn animated bx bx-info-circle font-18 text-primary">
                                    </i><span class="badge text-secondary">Click to enlarge image.</span>
                                </div>';
                        echo '<img src="' . $link  . '" class="card-img-top p-1" alt="...">';
                        echo '</a>';
                    }
                }

                $extended = '';
                $isDisabled = '';
                if ($input['file'] == 'clearance') {

                    $cond = ['emp_id' => $input['emp_id'], 'record_no' => $input['record_no'], 'store' => $bu['bunit_name']];
                    $chk = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                    if (!$chk) {
                        $isDisabled = 'disabled';
                    }

                    $cond['clearance_status'] = 'pending';
                    $chk = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                    if ($chk) {
                        $isDisabled = 'disabled';
                    }

                    if ($input['contract'] != 'current' && !$link) {
                        $cond = ['emp_id' => $input['emp_id']];
                        $recordNo = $this->promo_model->selectAll_tcR('employee3', $cond)['record_no'];

                        $cond = ['emp_id' => $input['emp_id'], 'record_no' => $recordNo, 'store' => $bu['bunit_name'], 'clearance_status' => 'pending'];
                        $scpd = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);

                        $cond = ['emp_id' => $input['emp_id'], 'record_no' => $recordNo, 'recordNo' => $input['record_no'], 'store' => $bu['bunit_name'], 'status' => 'pending'];
                        $pcm = $this->promo_model->selectAll_tcR('promo_clearance_monitoring', $cond);
                        if ($pcm && $scpd) {
                            $extended = '(Extended)';
                            $isDisabled = '';
                        }
                    }
                }

                echo '<div class="card-body d-flex flex-column justify-content-end" >
                        <p class="card-title text-primary">' . $bu['bunit_name'] . ' </br>' . ucfirst($input['file']) . ' ' . $extended . '</p>
                        <input type="file" class="form-control form-control-sm" name="' . $bu[$file] . '" ' . $isDisabled . '>';
                echo '</div>';

                echo '</div>';
                echo '</div>';
            }
        }
    }

    public function save_uploadContract()
    {
        $input      = $this->input->post(NULL, TRUE);
        $emp_id     = $input['emp_id'];
        $record_no  = $input['record_no'];
        $fileType   = $input['file'];
        $table2     = ($input['contract'] == 'current') ? 'promo_record' : 'promo_history_record';

        $cond = array('hrd_location' => $this->hrCode);
        $bus = $this->promo_model->selectAll_tcoA('locate_promo_business_unit', $cond, 'bunit_name');
        foreach ($bus as $bu) {
            $field = $bu['bunit_field'];
            $hasBu = $this->promo_model->empStores($table2, $emp_id, $record_no, $field);
            if ($hasBu) {
                if ($fileType == 'intro') {
                    $path       = '';
                    $introName  = $bu['bunit_intro'];
                    if (isset($_FILES[$introName]['name'])) {
                        $array          = explode(".", $_FILES[$introName]['name']);
                        $filename       = $emp_id . '=' . date('Y-m-d') . '=' . $introName . '=' . date('H-i-s-A') . '.' . end($array);
                        $base_path      = FCPATH;
                        $relative_path  = '../document/final_requirements/others/';
                        $path           = $base_path . $relative_path . $filename;
                        if (move_uploaded_file($_FILES[$introName]['tmp_name'], $path)) {

                            $data = array(
                                'app_id'                => $emp_id,
                                'requirement_name'      => 'intro',
                                'filename'              => $relative_path . $filename,
                                'date_time'             => $this->today,
                                'requirement_status'    => 'passed',
                                'receiving_staff'       => $this->userId,
                            );

                            $insert = $this->promo_model->insert_tdA('application_otherreq', $data);

                            $data   = array($introName => $relative_path . $filename);
                            $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
                            $update = $this->promo_model->update_twdA($table2, $cond, $data);

                            $activity   = "Uploaded Promo $fileType of $emp_id for $bu[bunit_name] with record_no: $record_no";
                            $data = array(
                                'activity'  => $activity,
                                'date'      => $this->today,
                                'time'      => date("H:i:s"),
                                'user'      => $this->userId,
                                'username'  => $_SESSION['username'],
                            );
                            $insert = $this->promo_model->insert_tdA('logs', $data);
                        }
                    }
                } else {
                    $path = '';
                    $name = ($fileType == 'clearance') ? $bu['bunit_clearance'] : $bu['bunit_contract'];
                    if (isset($_FILES[$name]['name'])) {
                        $array          = explode(".", $_FILES[$name]['name']);
                        $filename       = $emp_id . '=' . date('Y-m-d') . '=' . $name . '=' . date('H-i-s-A') . '.' . end($array);
                        $base_path      = FCPATH;
                        $relative_path  = '../document/' . $fileType . '/';
                        $path           = $base_path . $relative_path . $filename;
                        if (move_uploaded_file($_FILES[$name]['tmp_name'], $path)) {

                            if ($fileType == 'clearance') {
                                $cond = ['emp_id' => $emp_id];
                                $recordNo = $this->promo_model->selectAll_tcR('employee3', $cond)['record_no'];

                                $cond = ['emp_id' => $emp_id, 'record_no' => $recordNo, 'store' => $bu['bunit_name'], 'clearance_status' => 'pending'];
                                $scpd = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);

                                $cond = ['emp_id' => $emp_id, 'record_no' => $recordNo, 'recordNo' => $record_no, 'store' => $bu['bunit_name'], 'status' => 'pending'];
                                $pcm = $this->promo_model->selectAll_tcR('promo_clearance_monitoring', $cond);
                                if ($pcm && $scpd) {

                                    $data   = [$name => $relative_path . $filename];
                                    $cond   = ['emp_id' => $emp_id, 'record_no' => $record_no];
                                    $update = $this->promo_model->update_twdA($table2, $cond, $data);

                                    $data   = ['record_no' => $record_no];
                                    $cond   = ['emp_id' => $emp_id, 'record_no' => $recordNo, 'store' => $bu['bunit_name']];
                                    $update = $this->promo_model->update_twdA('appraisal_details', $cond, $data);

                                    $data   = ['record_no' => $record_no, 'date_effectivity' => $pcm['date_effectivity'], 'date_cleared' => $this->today, 'clearance_status' => 'Completed'];
                                    $cond   = ['emp_id' => $emp_id, 'record_no' => $recordNo, 'store' => $bu['bunit_name'], 'clearance_status' => 'pending'];
                                    $update = $this->promo_model->update_twdA('secure_clearance_promo_details', $cond, $data);

                                    $data   = ['status' => 'Done'];
                                    $cond   = ['emp_id' => $emp_id, 'record_no' => $recordNo, 'recordNo' => $record_no, 'store' => $bu['bunit_name'], 'status' => 'pending'];
                                    $update = $this->promo_model->update_twdA('promo_clearance_monitoring', $cond, $data);

                                    $cond   = ['emp_id' => $emp_id, 'record_no' => $recordNo, 'clearance_status' => 'pending'];
                                    $chk    = $this->promo_model->selectAll_tcR('secure_clearance_promo_details', $cond);
                                    if (!$chk) {
                                        $data   = ['status' => 'Completed'];
                                        $cond   = ['emp_id' => $emp_id, 'scpr_id' => $scpd['scpr_id'], 'status' => 'pending'];
                                        $update = $this->promo_model->update_twdA('secure_clearance_promo', $cond, $data);

                                        $data   = array('current_status' => 'Active', 'sub_status' => '');
                                        $cond   = array('emp_id' => $emp_id, 'record_no' => $recordNo);
                                        $update = $this->promo_model->update_twdA('employee3', $cond, $data);
                                    }

                                    $activity   = "Uploaded Promo Extended Clearance of ($emp_id) for $bu[bunit_name] with scpr_id: $scpd[scpr_id]";
                                    $data       = array(
                                        'activity'  => $activity,
                                        'date'      => $this->today,
                                        'time'      => date("H:i:s"),
                                        'user'      => $this->userId,
                                        'username'  => $_SESSION['username'],
                                    );
                                    $logs = $this->promo_model->insert_tdA('logs', $data);
                                } else {
                                    $data   = array($name => $relative_path . $filename);
                                    $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
                                    $update = $this->promo_model->update_twdA($table2, $cond, $data);
                                }
                            } else {

                                $data   = array($name => $relative_path . $filename);
                                $cond   = array('emp_id' => $emp_id, 'record_no' => $record_no);
                                $update = $this->promo_model->update_twdA($table2, $cond, $data);
                            }

                            $activity   = "Uploaded Promo $fileType of $emp_id for $bu[bunit_name] with record_no: $record_no";
                            $data = array(
                                'activity'  => $activity,
                                'date'      => $this->today,
                                'time'      => date("H:i:s"),
                                'user'      => $this->userId,
                                'username'  => $_SESSION['username'],
                            );
                            $insert = $this->promo_model->insert_tdA('logs', $data);
                        }
                    }
                }
            }
        }

        if ($update) {
            echo json_encode(['message' => 'success', 'id' => $input['file']]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function save_supervisor_form()
    {
        $input = $this->input->post(NULL, TRUE);
        foreach ($input['supervisor'] as $ratee) {

            $data = array(
                'ratee'                 => $ratee,
                'subordinates_rater'    => $input['app_id'],
                'date_added'            => $this->today,
                'added_by'              => $this->userId,
            );
            $add = $this->promo_model->insert_tdA('leveling_subordinates', $data);

            $activity = "Added Promo Supervisor ($ratee) to $input[app_id]";
            $data = array(
                'activity'  => $activity,
                'date'      => $this->today,
                'time'      => date("H:i:s"),
                'user'      => $this->userId,
                'username'  => $_SESSION['username'],
            );
            $logs = $this->promo_model->insert_tdA('logs', $data);
        }

        if ($add) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function remarks()
    {
        $input = $this->input->post(NULL, TRUE);
        $data = array('remarks' => $input['remarks']);
        $cond = array('emp_id' => $input['emp_id']);
        $chk = $this->promo_model->selectAll_tcA('remarks', $cond);
        if ($chk) {
            $remarks = $this->promo_model->update_twdA('remarks', $cond, $data);
        } else {
            $data['emp_id'] = $input['emp_id'];
            $remarks = $this->promo_model->insert_tdA('remarks', $data);
        }

        if ($remarks) {
            $activity = "Updated/Added Promo Remarks of $input[emp_id]";
            $data = array(
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

    public function docFile_view()
    {
        $input  = $this->input->post(NULL, TRUE);
        $page   = (isset($input['page'])) ? $input['page'] : 0;
        if ($input['no'] != 27) {
            $row    = $this->promo_model->selectAll_tcR('201document', array('no' => $input['no']));
            if ($input['no'] == 15) {
                $query  = $this->promo_model->get201_file($row, $input['emp_id'], $page, $input['no']);
                $total  = $this->promo_model->get201_file($row, $input['emp_id'], null, $input['no']);
            } else {
                $query  = $this->promo_model->get201_file($row, $input['emp_id'], $page);
                $total  = $this->promo_model->get201_file($row, $input['emp_id']);
            }
            $name   = $this->promo_model->getName_employee3($query['receiving_staff']);
            if (!$name) {
                $name = $query['receiving_staff'];
            }
            $link   =  base_url('../promo/' . $query['filename']);
            $date   = date('F j, Y', strtotime($query['date_time']));
            $path   = str_replace('../', '', $row['path']);
            $path1  = FCPATH . $row['path'] . $query['filename'];
            $path2  = FCPATH . '../promo/' . $query['filename'];
            if (file_exists($path2)) {
                $link = base_url('../promo/') . $query['filename'];
            } else {
                $link = base_url('/' . $path . '/') . $query['filename'];
            }
        } else {
            $query  = $this->promo_model->get201_resignation($input['emp_id'], $page);
            $total  = $this->promo_model->get201_resignation($input['emp_id']);
            $name   = $this->promo_model->getName_employee3($query['added_by']);
            if (!$name) {
                $name = $query['added_by'];
            }
            $date   = date('F j, Y', strtotime($query['date_updated']));
            $letter = $query['resignation_letter'];
            $path1  = FCPATH . 'document/resignation/' . $letter;
            $path2  = FCPATH . '../promo/' . $letter;
            if (file_exists($path1)) {
                $link = base_url('/document/resignation/') . $letter;
            } else if (file_exists($path2)) {
                $link = base_url('../promo/') . $letter;
            } else {
                $link = base_url('../document/resignation/') . $letter;
            }
        }
        $num = count($total);
        echo '<div class="row">';
        echo '<div class="col-sm-3">';
        echo '<div class="input-group input-group-sm mb-3">
				<label class="input-group-text" for="select1">Page No.</label>
				<select class="form-select form-select-sm" name="page" onchange="page(this.value, ' . $input['no'] . ')" id="select1">';
        for ($i = 0; $i < $num; $i++) {
            $pageNum = $i + 1;
            $selected = ($i == $page) ? 'selected' : '';
            echo '<option value="' . $i . '" ' . $selected . '>' . $pageNum . '</option>';
        }
        echo    '</select>';
        echo '</div>';
        echo '</div>';
        echo '<div class="col-sm-9">
                <p>Date Uploaded: <b>' . $date . '</b> | Uploaded By: <b>' . $name . '</b></p>';
        echo '</div>';
        echo '</div>';
        echo '<img src="' . $link . '" class="d-block w-100" alt="image error...">';
    }

    public function docFile_uploadForm()
    {
        $input = $this->input->post(NULL, TRUE);
        $empId = $input['emp_id'];
        $name = $input['name'];
        $no = $input['no'];

        echo '<input type="hidden" name="emp_id" value="' . $empId . '">';
        echo '<input type="hidden" name="no" value="' . $no . '">';
        if ($no == 13 || $no == 21 || $no == 24 || $no == 25) { // multiple upload for misconduct,showcause,tor,suspension
            echo '<input type="file" class="form-control form-control-sm mb-2" name="' . str_replace(' ', '', $name) . '[]" multiple>';
        } else {
            echo '<input type="file" class="form-control form-control-sm mb-2" name="' . str_replace(' ', '', $name) . '">';
        }
    }

    public function docFile_upload()
    {
        $input  = $this->input->post(NULL, TRUE);
        $cond   = array('no' => $input['no']);
        $row    = $this->promo_model->selectAll_tcR('201document', $cond);
        $name   = str_replace(' ', '', $row['201_name']);
        $path   = '';
        if ($input['no'] == 13 || $input['no'] == 21 || $input['no'] == 24 || $input['no'] == 25) {
            // multiple upload for misconduct,showcause,tor,suspension
            if (isset($_FILES[$name]['name']) && is_array($_FILES[$name]['name'])) {
                foreach ($_FILES[$name]['name'] as $i => $image_name) {
                    if (!empty($image_name)) {
                        $image_name     = addslashes($image_name);
                        $array          = explode(".", $image_name);
                        $extension      = end($array);
                        $filename       = $input['emp_id'] . "=" . date('Y-m-d') . "=" . $row['201_name'] . "=" . date('H-i-s-A') . "_" . $i . "." . $extension;
                        $base_path      = FCPATH;
                        $relative_path  = '../document/' . $row['path'] . '/';
                        $path           = $base_path . $relative_path . $filename;

                        if (move_uploaded_file($_FILES[$name]['tmp_name'][$i], $path)) {
                            $file = $relative_path . $filename;
                            $data = array(
                                $row['empField']        => $input['emp_id'],
                                'requirement_name'      => $row['requirementName'],
                                'filename'              => $file,
                                'date_time'             => date('Y-m-d'),
                                'requirement_status'    => 'passed',
                                'receiving_staff'       => $this->userId,
                            );
                            $upload = $this->promo_model->insert_tdA($row['tableName'], $data);

                            $activity   = "Uploaded Promo  $row[requirementName] of $input[emp_id]";
                            $data = array(
                                'activity'  => $activity,
                                'date'      => $this->today,
                                'time'      => date("H:i:s"),
                                'user'      => $this->userId,
                                'username'  => $_SESSION['username'],
                            );
                            $insert = $this->promo_model->insert_tdA('logs', $data);
                        }
                    }
                }
            }
        } else {
            if (isset($_FILES[$name]['name'])) {
                $image_name     = addslashes($_FILES[$name]['name']);
                $array          = explode(".", $image_name);
                $filename       = $input['emp_id'] . "=" . date('Y-m-d') . "=" . $row['201_name'] . "=" . date('H-i-s-A') . "." . end($array);
                $base_path      = FCPATH;
                $relative_path  = '../document/' . $row['path'] . '/';
                $path           = $base_path . $relative_path . $filename;
                if (move_uploaded_file($_FILES[$name]['tmp_name'], $path)) {
                    $file   = $relative_path . $filename; //temp
                    $data   = array(
                        $row['empField']        => $input['emp_id'],
                        'requirement_name'      => $row['requirementName'],
                        'filename'              => $file,
                        'date_time'             => date('Y-m-d'),
                        'requirement_status'    => 'passed',
                        'receiving_staff'       => $this->userId,
                    );
                    $upload = $this->promo_model->insert_tdA($row['tableName'], $data);

                    $activity   = "Uploaded Promo  $row[requirementName] of $input[emp_id]";
                    $data = array(
                        'activity'  => $activity,
                        'date'      => $this->today,
                        'time'      => date("H:i:s"),
                        'user'      => $this->userId,
                        'username'  => $_SESSION['username'],
                    );
                    $insert = $this->promo_model->insert_tdA('logs', $data);
                }
            }
        }

        if ($upload) {
            echo json_encode(['message' => 'success']);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function addtoExtend()
    {
        $input = $this->input->post(NULL, TRUE);
        $emp_id     = $input['empId'];
        $emp        = $this->promo_model->selectAll_tcR('employee3', array('emp_id' => $emp_id));
        $record_no  = $emp['record_no'];

        $cond = array('emp_id' => $emp_id, 'record_no' => $record_no, 'clearance_status' => 'Pending');
        $scpd = $this->promo_model->selectAll_tcA('secure_clearance_promo_details', $cond);
        foreach ($scpd as $key => $clr) {

            $cond = array('scpr_id' => $clr['scpr_id'], 'scdetails_id' => $clr['scdetails_id']);
            $chk = $this->promo_model->selectAll_tcR('promo_clearance_monitoring', $cond);
            if (!$chk) {
                $data = array(
                    'emp_id'            => $emp_id,
                    'record_no'         => $record_no,
                    'scpr_id'           => $clr['scpr_id'],
                    'scdetails_id'      => $clr['scdetails_id'],
                    'extend_date'       => $clr['date_effectivity'],
                    'date_effectivity'  => $emp['eocdate'],
                    'store'             => $clr['store'],
                    'status'            => 'Pending',
                    'date'              => $this->today,
                );
                $c = $this->promo_model->insert_tdA('promo_clearance_monitoring', $data);
            }
            //  else {
            //     $cond = array('id' => $chk['id']);
            //     $data = array('extend_date' => $input['eocdate']);
            //     $c = $this->promo_model->update_twdA('promo_clearance_monitoring', $cond, $data);
            // }
        }
    }
}
