<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Manila');
class Login extends CI_Controller {

    function __construct(){
        parent::__construct();
       // $this->checkEmployeeLogin();
        $this->load->library('form_validation');
        $this->load->model('DB_model','dbmodel');
        $this->load->model('PlacementLogin_model','Plmodel');
    }

    // private function checkEmployeeLogin(){
    //     if($this->session->userdata('hrmsEmployeeSession')){
    //         redirect($this->session->userdata('sessionType') . '/dashboard');
    //     }
    // }

    public function index($msg = null)
	{
        $data['title']  = "Login";
        $data['msg'] = $msg;
		$this->load->view('login', $data);
	}

    public function process()
        {
            $this->form_validation->set_rules('username', 'Username', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');
            
            if ($this->form_validation->run() === FALSE)
            {
                $this->index();
            }
            else
            {
                $result = $this->Plmodel->check_for_validation();
                if (!$result)
                {
                    $msg = '<div class="alert alert-danger text-center">You have entered an invalid username or password.</div>';
                    $this->index($msg);
                }
                else
                {
                    $this->Plmodel->insert_logs();
                    redirect(base_url().'dashboard/index');
                }
            } 
        }
    

    // public function check()
    // {
    //     $this->form_validation->set_rules(
    //         'username',
    //         'Username',
    //         'required|trim',
    //         array('required' => 'You must provide a %s.')
    //     );
    //     $this->form_validation->set_rules(
    //         'password',
    //         'Password',
    //         'required|trim',
    //         array('required' => 'You must provide a %s.')
    //     );

    //     if ($this->form_validation->run() == FALSE){
    //         $errors = validation_errors('<li>', '</li>');
    //         echo json_encode([
    //             'status' => 401,
    //             'response' => 'error',
    //             'response_message' =>$errors
    //         ]);
    //     }else{
    //         $username = $this->input->post('username');
    //         $password = md5($this->input->post('password'));

    //         $result = $this->dbmodel
    //                         ->get_row(
    //                             'users',
    //                             'emp_id, username, password, usertype, user_status , user_no',
    //                             array(
    //                                 'field1' => 'username',
    //                                 'field2' => 'password'
    //                             ),
    //                             array(
    //                                 $username,
    //                                 $password
    //                             )
    //                         );
            
    //         if($result){
    //             if($result->user_status === "active"){
    //                 $employee = $this->dbmodel
    //                                 ->get_row(
    //                                     'employee3',
    //                                     'name, record_no, position, emp_type, company_code, bunit_code, dept_code, section_code, startdate, eocdate',
    //                                     array('field1' => 'emp_id'),
    //                                     array($result->emp_id)
    //                                 );

    //                 //ADD SCRIPT FOR ACCESS EMPLOYEE 06212023 miri                
    //                 $employee_access = $this->dbmodel
    //                             ->get_row(
    //                                 'access_allowed_employee',
    //                                 '*',
    //                                 array('field1' => 'emp_id','field2' => 'status'),
    //                                 array($result->emp_id, 1)
    //                             );

    //                 $app_photo = $this->dbmodel
    //                             ->get_row(
    //                                 'applicant',
    //                                 'photo',
    //                                 array('field1' => 'app_id'),
    //                                 array($result->emp_id)
    //                             );           
                    
                                    
    //                 $datas = array(
    //                     "emp_id"        => $result->emp_id,
    //                     "record_no"     => $employee->record_no,
    //                     "username"      => $result->username,
    //                     "password"      => $result->password,
    //                     "userstatus"    => $result->user_status,
    //                     "usertype"      => $result->usertype,
    //                     "employee_name" => $employee->name,
    //                     "position"      => $employee->position,
    //                     "emp_type"      => $employee->emp_type,
    //                     "company"       => $employee->company_code,
    //                     "bunit"         => $employee->bunit_code,
    //                     "department"    => $employee->dept_code,
    //                     "section"       => $employee->section_code,
    //                     "startdate"      => $employee->startdate,
    //                     "eocdate"       => $employee->eocdate,
    //                     "sessionType"   => $result->usertype,
    //                     "userno"        => $result->user_no,
    //                     "accessid"      => @$employee_access->access_id,
    //                     "photo"         => @$app_photo->photo,
    //                     "hrmsEmployeeSession" => true
    //                 );

    //                 $this->session->set_userdata($datas);
    //                 echo json_encode([
    //                     'status' => 200,
    //                     'redirect' => $result->usertype. '/dashboard',
    //                     'response' => "success",
    //                     'response_message' =>'You have successfully logged in.<br> Redirecting to the dashboard.'
    //                 ]);
    //             }else{
    //                 echo json_encode([
    //                     'status' => 401,
    //                     'response'	=> "error",
    //                     'response_message' => "User Account is inactive! Please contact your system administrator to activate your account."
    //                 ]);
    //             }
    //         }else{
    //             echo json_encode([
    //                 'status' => 401,
    //                 'response' => "error",
    //                 'clear_text' => 'true',
    //                 'response_message' =>'Username or Password is incorrect.'
    //             ]);
    //         }
    //     }
    // }
}
