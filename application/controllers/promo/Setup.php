<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setup extends CI_Controller
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

    public function updateForm()
    {
        $input = $this->input->post(NULL, TRUE);
        if ($input['table'] == 'agency') {
            $cond   = array('agency_code' => $input['id']);
            $agency = $this->promo_model->selectAll_tcR_tk('promo_locate_agency', $cond)['agency_name'];
            echo '<input type="hidden" name="agency_code" value="' . $input['id'] . '">';
            echo '<div class="form-group">
                    <label>Agency Name</label>
                    <input type="text" class="form-control" name="agency_name" value="' . $agency . '" autocomplete="off" style="text-transform: uppercase;">';
            echo '</div>';
        } else if ($input['table'] == 'company') {
            $pc_name = $this->promo_model->selectAll_tcR('locate_promo_company', array('pc_code' => $input['id']))['pc_name'];
            echo '<input type="hidden" name="pc_code" value="' . $input['id'] . '">';
            echo '<div class="form-group">
                    <label>Company Name</label>
                    <input type="text" class="form-control" name="pc_name" value="' . $pc_name . '" autocomplete="off">';
            echo '</div>';
        } else if ($input['table'] == 'product') {
            $product = $this->promo_model->selectAll_tcR('locate_promo_product', array('id' => $input['id']))['product'];
            echo '<input type="hidden" name="id" value="' . $input['id'] . '">';
            echo '<div class="form-group">
                    <label>Company Name</label>
                    <input type="text" class="form-control" name="product" value="' . $product . '" autocomplete="off">';
            echo '</div>';
        } else if ($input['table'] == 'bu') {
            $cond   = array('bunit_id' => $input['id']);
            $bu     = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);
            echo '<input type="hidden" name="bunit_id" value="' . $input['id'] . '">';
            echo '<div class="row">
				    <div class="col-md-12 mb-2">
				    	<div class="form-group">
				    		<label class="form-label">Business Unit</label>
				    		<input type="text" name="business_unit" value="' . $bu['business_unit'] . '" class="form-control" style="text-transform: uppercase;" autocomplete="off">
				    	</div>
				    </div>
				    <div class=" col-md-12 mb-2">
				    	<div class="form-group">
				    		<label class="form-label">Business Unit Name</label>
				    		<input type="text" name="bunit_name" value="' . $bu['bunit_name'] . '" class="form-control" style="text-transform: uppercase;" autocomplete="off">
				    	</div>
                    </div>
				    <div class=" col-md-6 mb-2">
				    	<div class="form-group">
				    		<label class="form-label">Acronym</label>
				    		<input type="text" name="bunit_acronym" value="' . $bu['bunit_acronym'] . '" class="form-control" style="text-transform: uppercase;" autocomplete="off">
				    	</div>
                    </div>
                    <div class=" col-md-6 mb-2">
				    	<div class="form-group">
				    		<label class="form-label">Column Field</label>
				    		<input type="text" name="bunit_field" value="' . $bu['bunit_field'] . '" class="form-control" style="text-transform: lowercase;" disabled>
				    	</div>
                    </div>
                    <div class=" col-md-6 mb-2">
                        <div class="form-group">
                            <label class="form-label">Location</label>
                            <select class="form-control" name="hrd_location">';
            echo            '<option value="asc" ' . ($bu['hrd_location'] == 'asc' ? 'selected' : '') . '>asc</option>';
            echo            '<option value="cebo" ' . ($bu['hrd_location'] == 'cebo' ? 'selected' : '') . '>cebo</option>';
            echo            '<option value="nesco" ' . ($bu['hrd_location'] == 'nesco' ? 'selected' : '') . '>nesco</option>';
            echo            '</select>
                        </div>
                    </div>';
            echo '</div>';
        }
    }

    public function agencyList()
    {
        $data   = [];
        $list   = $this->promo_model->selectAll_tk('promo_locate_agency');
        foreach ($list as $row) {

            $ac     = $row['agency_code'];
            $action =   '<a href="javascript:;" title="edit" id="edit" onclick="updateForm(this.id, \'' . $ac . '\', \'agency\')">
                            <i class="bx bx-edit text-primary me-0" style="font-size: 22px;"></i>
                        </a>';
            if ($row['status'] == 1) {
                $action .=   '<a href="javascript:;" title="deactivate" id="deactivate" onclick="updateAgency(this.id, \'' . $ac . '\')">
                                <i class="bx bx-bulb text-warning me-0" style="font-size: 22px;"></i>
                            </a>';
            } else {
                $action .=   '<a href="javascript:;" title="activate" id="activate" onclick="updateAgency(this.id, \'' . $ac . '\')">
                                <i class="bx bx-bulb text-secondary me-0" style="font-size: 22px;"></i>
                            </a>';
            }

            if (in_array($this->userId, $this->adminUser)) {
                $action .=  '<a href="javascript:;" title="delete" id="delete" onclick="updateAgency(this.id, \'' . $ac . '\')">
                                <i class="bx bx-trash text-danger me-0" style="font-size: 22px;"></i>
                            </a>';
            }
            $column     = [];
            $column[]   = $row['agency_name'];
            $column[]   = $action;
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }

    public function updateAgency()
    {
        $input = $this->input->post(NULL, TRUE);
        if ($input['process'] == 'edit') {

            $ac     = $input['id'];
            $an     = strtoupper($input['agency_name']);
            $cond   = array('agency_name' => $an, 'agency_code !=' => $ac);
            $chk    = $this->promo_model->selectAll_tcA_tk('promo_locate_agency', $cond);
            if ($chk) {
                $message = 'Agency name already exists.';
                echo json_encode(['message' => 'exist', 'report' => $message]);
                return;
            }

            $message    = 'Agency has been updated.';
            $data       = array('agency_name' => $an, 'updated_at' => date('Y-m-d H:i:s'));
            $cond       = array('agency_code' => $ac);
            $agency     = $this->promo_model->update_twdA_tk('promo_locate_agency', $cond, $data);
        } else if ($input['process'] == 'add') {

            $an     = strtoupper($input['agency_name']);
            $cond   = array('agency_name' => $an);
            $chk    = $this->promo_model->selectAll_tcA_tk('promo_locate_agency', $cond);
            if ($chk) {
                $message = 'Agency name already exists.';
                echo json_encode(['message' => 'exist', 'report' => $message]);
                return;
            }

            $message    = 'Agency has been added.';
            $data       = array('agency_name' => $an, 'status' => 1, 'created_at' => date('Y-m-d H:i:s'));
            $agency     = $this->promo_model->insert_tdA_tk('promo_locate_agency', $data);
        } else if ($input['process'] == 'activate') {
            $message    = 'Agency has been activated.';
            $cond       = array('agency_code' => $input['id']);
            $data       = array('status' => 1, 'updated_at' => date('Y-m-d H:i:s'));
            $agency     = $this->promo_model->update_twdA_tk('promo_locate_agency', $cond, $data);
        } else if ($input['process'] == 'deactivate') {
            $message    = 'Agency has been deactivated.';
            $cond       = array('agency_code' => $input['id']);
            $data       = array('status' => 0, 'updated_at' => date('Y-m-d H:i:s'));
            $agency     = $this->promo_model->update_twdA_tk('promo_locate_agency', $cond, $data);
        } else if ($input['process'] == 'delete') {
            $message    = 'Agency has been deleted.';
            $cond       = array('agency_code' => $input['id']);
            $agency     = $this->promo_model->delete_tc_tk('promo_locate_agency', $cond);
        }

        if ($agency) {
            echo json_encode(['message' => 'success', 'report' => $message]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function buList()
    {
        $data   = [];
        $list   = $this->promo_model->selectAll('locate_promo_business_unit');
        foreach ($list as $row) {

            $status =  '<select class="form-select form-select-sm" style="width: 100px;" id="updateStatus" name="status" onchange="updateBu(this.id,this.name,this.value)">
                            <option value="active|' . $row['bunit_id'] . '" ' . ($row['status'] == 'active' ? 'selected' : '') . '>Active</option>
                            <option value="inactive|' . $row['bunit_id'] . '" ' . ($row['status'] == 'inactive' ? 'selected' : '') . '>InActive</option>
                        </select>';

            $tk_status =    '<select class="form-select form-select-sm" style="width: 100px;" id="updateStatus" name="tk_status" onchange="updateBu(this.id,this.name,this.value)">
                                <option value="active|' . $row['bunit_id'] . '" ' . ($row['tk_status'] == 'active' ? 'selected' : '') . '>Active</option>
                                <option value="inactive|' . $row['bunit_id'] . '" ' . ($row['tk_status'] == 'inactive' ? 'selected' : '') . '>InActive</option>
                             </select>';

            $app_status =  '<select class="form-select form-select-sm" style="width: 100px;" id="updateStatus" name="appraisal_status" onchange="updateBu(this.id,this.name,this.value)">
                                <option value="active|' . $row['bunit_id'] . '" ' . ($row['appraisal_status'] == 'active' ? 'selected' : '') . '>Active</option>
                                <option value="inactive|' . $row['bunit_id'] . '" ' . ($row['appraisal_status'] == 'inactive' ? 'selected' : '') . '>InActive</option>
                            </select>';

            $action     = '<a href="javascript:;" title="edit" id="update" onclick="updateForm(this.id, \'' . $row['bunit_id'] . '\',\'bu\')">
                            <i class="bx bx-edit text-primary me-0" style="font-size: 22px;"></i>
                          </a>';

            if (in_array($this->userId, $this->adminUser)) {
                $action .=  '<i class="bx bx-trash text-secondary me-0" style="font-size: 22px;"></i>';
                // $action .=  '<a href="javascript:;" title="delete" id="delete" onclick="deleteBu(this.id, \'' . $row['bunit_id'] . '\')">
                //                 <i class="bx bx-trash text-danger me-0" style="font-size: 22px;"></i>
                //             </a>';
            }

            $column     = [];
            $column[]   = $row['bunit_name'];
            $column[]   = $status;
            $column[]   = $tk_status;
            $column[]   = $app_status;
            $column[]   = $action;
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }

    public function updateBu()
    {
        $input = $this->input->post(NULL, TRUE);
        $report = '';
        if ($input['process'] == 'update') {

            $exist  = false;
            $cond   = array('bunit_id !=' => $input['bunit_id']);
            $lbpu   = $this->promo_model->selectAll_tcA('locate_promo_business_unit', $cond);
            foreach ($lbpu as $row) {

                if ($row['business_unit'] == strtoupper($input['business_unit'])) {
                    $report = 'business_unit';
                    $exist = true;
                }

                if ($row['bunit_name'] == strtoupper($input['bunit_name'])) {
                    $report = 'bunit_name';
                    $exist = true;
                }

                if ($row['bunit_acronym'] == strtoupper($input['bunit_acronym'])) {
                    $report = 'bunit_acronym';
                    $exist = true;
                }

                if ($exist) {
                    echo json_encode(['message' => 'exist', 'report' => $report]);
                    return;
                }
            }

            $report = 'Business unit has been updated.';
            $data   = array(
                'business_unit' => strtoupper($input['business_unit']),
                'bunit_name'    => strtoupper($input['bunit_name']),
                'bunit_acronym' => strtoupper($input['bunit_acronym']),
                'hrd_location'  => $input['hrd_location']
            );
            $cond   = array('bunit_id' => $input['bunit_id']);
            $bu     = $this->promo_model->update_twdA('locate_promo_business_unit', $cond, $data);
        } else if ($input['process'] == 'updateStatus') {

            $split  = explode('|', $input['value']);
            $report = 'Business unit status has been updated.';
            $data   = array($input['column'] => $split[0]);
            $cond   = array('bunit_id' => $split[1]);
            $bu     = $this->promo_model->update_twdA('locate_promo_business_unit', $cond, $data);
        } else if ($input['process'] == 'add') {

            $business_unit  = strtoupper($input['business_unit']);
            $bunit_name     = strtoupper($input['bunit_name']);
            $bunit_acronym  = strtoupper($input['bunit_acronym']);
            $bunit_field    = strtolower($input['bunit_field']);
            $hrd_location   = $input['hrd_location'];

            $exist  = false;
            $lbpu   = $this->promo_model->selectAll('locate_promo_business_unit');
            foreach ($lbpu as $row) {

                if ($row['business_unit'] == $business_unit) {
                    $report = 'business_unit';
                    $exist = true;
                }

                if ($row['bunit_name'] == $bunit_name) {
                    $report = 'bunit_name';
                    $exist = true;
                }

                if ($row['bunit_acronym'] == $bunit_acronym) {
                    $report = 'bunit_acronym';
                    $exist = true;
                }

                if ($row['bunit_field'] == $bunit_field) {
                    $report = 'bunit_field';
                    $exist = true;
                }

                if ($exist) {
                    echo json_encode(['message' => 'exist', 'report' => $report]);
                    return;
                }
            }

            $sql1   = "ALTER TABLE `promo_record`
                    ADD COLUMN `{$bunit_field}` VARCHAR(1) NOT NULL AFTER `alta_citta`,
                    ADD COLUMN `{$bunit_field}_epascode` VARCHAR(255) NOT NULL AFTER `alta_special_days`,
                    ADD COLUMN `{$bunit_field}_contract` TEXT NOT NULL AFTER `{$bunit_field}_epascode`,
                    ADD COLUMN `{$bunit_field}_permit` VARCHAR(255) NOT NULL AFTER `{$bunit_field}_contract`,
                    ADD COLUMN `{$bunit_field}_clearance` TEXT NOT NULL AFTER `{$bunit_field}_permit`,
                    ADD COLUMN `{$bunit_field}_intro` VARCHAR(255) NOT NULL AFTER `{$bunit_field}_clearance`,
                    ADD COLUMN `{$bunit_field}_sched` VARCHAR(50) NOT NULL AFTER `{$bunit_field}_intro`,
                    ADD COLUMN `{$bunit_field}_days` VARCHAR(50) NOT NULL AFTER `{$bunit_field}_sched`,
                    ADD COLUMN `{$bunit_field}_special_sched` VARCHAR(50) NOT NULL AFTER `{$bunit_field}_days`,
                    ADD COLUMN `{$bunit_field}_special_days` VARCHAR(50) NOT NULL AFTER `{$bunit_field}_special_sched`";

            $sql2   = "ALTER TABLE `promo_history_record`
                    ADD COLUMN `{$bunit_field}` VARCHAR(1) NOT NULL AFTER `alta_citta`,
                    ADD COLUMN `{$bunit_field}_epascode` VARCHAR(255) NOT NULL AFTER `alta_special_days`,
                    ADD COLUMN `{$bunit_field}_contract` TEXT NOT NULL AFTER `{$bunit_field}_epascode`,
                    ADD COLUMN `{$bunit_field}_permit` VARCHAR(255) NOT NULL AFTER `{$bunit_field}_contract`,
                    ADD COLUMN `{$bunit_field}_clearance` TEXT NOT NULL AFTER `{$bunit_field}_permit`,
                    ADD COLUMN `{$bunit_field}_intro` VARCHAR(255) NOT NULL AFTER `{$bunit_field}_clearance`,
                    ADD COLUMN `{$bunit_field}_sched` VARCHAR(50) NOT NULL AFTER `{$bunit_field}_intro`,
                    ADD COLUMN `{$bunit_field}_days` VARCHAR(50) NOT NULL AFTER `{$bunit_field}_sched`,
                    ADD COLUMN `{$bunit_field}_special_sched` VARCHAR(50) NOT NULL AFTER `{$bunit_field}_days`,
                    ADD COLUMN `{$bunit_field}_special_days` VARCHAR(50) NOT NULL AFTER `{$bunit_field}_special_sched`";

            $sql3   = "ALTER TABLE `promo_applicant_record` ADD COLUMN `{$bunit_field}` VARCHAR(1) NOT NULL AFTER `alta_citta`";

            $data = array(
                'business_unit'         => $business_unit,
                'bunit_name'            => $bunit_name,
                'bunit_acronym'         => $bunit_acronym,
                'bunit_field'           => $bunit_field,
                'bunit_epascode'        => $bunit_field . '_epascode',
                'bunit_contract'        => $bunit_field . '_contract',
                'bunit_permit'          => $bunit_field . '_permit',
                'bunit_clearance'       => $bunit_field . '_clearance',
                'bunit_intro'           => $bunit_field . '_intro',
                'bunit_dutySched'       => $bunit_field . '_sched',
                'bunit_dutyDays'        => $bunit_field . '_days',
                'bunit_specialSched'    => $bunit_field . '_special_sched',
                'bunit_specialDays'     => $bunit_field . '_special_days',
                'hrd_location'          => $hrd_location,
                'status'                => 'active',
                'tk_status'             => 'active',
                'appraisal_status'      => 'active',
            );

            $query1 = $this->promo_model->queryString($sql1);
            if ($query1) {
                $query2 = $this->promo_model->queryString($sql2);
            }

            if ($query2) {
                $query3 = $this->promo_model->queryString($sql3);
            }

            if ($query3) {
                $report = 'Business unit has been added.';
                $bu     = $this->promo_model->insert_tdA('locate_promo_business_unit', $data);
            }
        } else if ($input['process'] == 'delete') {

            $cond   = array('bunit_id' => $input['bunit_id']);
            $row    = $this->promo_model->selectAll_tcR('locate_promo_business_unit', $cond);

            $bunit_special_days     = $row['bunit_specialDays'];
            $bunit_special_sched    = $row['bunit_specialSched'];
            $bunit_days             = $row['bunit_dutyDays'];
            $bunit_sched            = $row['bunit_dutySched'];
            $bunit_intro            = $row['bunit_intro'];
            $bunit_clearance        = $row['bunit_clearance'];
            $bunit_permit           = $row['bunit_permit'];
            $bunit_contract         = $row['bunit_contract'];
            $bunit_epascode         = $row['bunit_epascode'];
            $bunit_field            = $row['bunit_field'];

            $sql1 = "ALTER TABLE `promo_applicant_record` DROP COLUMN `{$bunit_field}`";

            $sql2 = "ALTER TABLE `promo_history_record` 
                    DROP COLUMN `{$bunit_special_days}`,
                    DROP COLUMN `{$bunit_special_sched}`,
                    DROP COLUMN `{$bunit_days}`,
                    DROP COLUMN `{$bunit_sched}`,
                    DROP COLUMN `{$bunit_intro}`,
                    DROP COLUMN `{$bunit_clearance}`,
                    DROP COLUMN `{$bunit_permit}`,
                    DROP COLUMN `{$bunit_contract}`,
                    DROP COLUMN `{$bunit_epascode}`,
                    DROP COLUMN `{$bunit_field}`";

            $sql3 = "ALTER TABLE `promo_record` 
                    DROP COLUMN `{$bunit_special_days}`,
                    DROP COLUMN `{$bunit_special_sched}`,
                    DROP COLUMN `{$bunit_days}`,
                    DROP COLUMN `{$bunit_sched}`,
                    DROP COLUMN `{$bunit_intro}`,
                    DROP COLUMN `{$bunit_clearance}`,
                    DROP COLUMN `{$bunit_permit}`,
                    DROP COLUMN `{$bunit_contract}`,
                    DROP COLUMN `{$bunit_epascode}`,
                    DROP COLUMN `{$bunit_field}`";

            $query1 = $this->promo_model->queryString($sql1);
            if ($query1) {
                $query2 = $this->promo_model->queryString($sql2);
            }

            if ($query2) {
                $query3 = $this->promo_model->queryString($sql3);
            }

            if ($query3) {
                $report = 'Business unit has been deleted.';
                $bu     = $this->promo_model->delete_tcr_row('locate_promo_business_unit', $cond);
            }
        }

        $data = array(
            'activity'  => "Promo Business Unit Setup ($input[process])",
            'date'      => $this->today,
            'time'      => date("H:i:s"),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $insert = $this->promo_model->insert_tdA('logs', $data);

        if ($bu) {
            echo json_encode(['message' => 'success', 'report' => $report]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function companyAgency()
    {
        $cond   = array('status' => '1');
        $list = $this->promo_model->selectAll_tcA_tk('promo_locate_agency', $cond);
        $data = [];
        foreach ($list as $row) {

            $agency_code    = $row['agency_code'];
            $agency_name    = htmlspecialchars($row['agency_name']);
            $button         = '<a href="javascript:;"  onclick="agencyComps(\'' . $agency_code . '\',\'' . addslashes($agency_name) . '\')">
                                <i class="bx bx-grid-alt me-0 font-22" title="View Company Tagging"></i>
                              </a>';
            $column     = [];
            $column[]   = $agency_name;
            $column[]   = $button;
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }

    public function agencyComps()
    {
        $input = $this->input->post(NULL, TRUE);
        echo '<div class="table-responsive">';
        echo '<table id="agencyComps" class="table table-sm table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>Action</th>
                    </tr>
                </thead>';
        $ac     = $input['agency_code'];
        $cond   = array('status' => '1');
        $query  = $this->promo_model->selectAll_tcA('locate_promo_company', $cond);
        foreach ($query as $key => $row) {

            $comp   = $row['pc_name'];
            $cond   = array('agency_code' => $ac, 'company_name' => $comp);
            $plc    = $this->promo_model->selectAll_tcR_tk('promo_locate_company', $cond);

            $action = '';
            if ($plc) {
                $id     = $plc['company_code'];
                $action =  '<a href="javascript:;" id="delete" onclick="updateCompanyAgency(this.id, \'' . $id . '\', \'' . addslashes($comp) . '\')">
                                <i class="bx bx-x-circle text-danger me-0 font-22" title="Untag Company"></i>
                            </a>';
            } else {
                $action =  '<a href="javascript:;" id="add" onclick="updateCompanyAgency(this.id, \'' . $ac . '\', \'' . addslashes($comp) . '\')">
                                <i class="bx bx-purchase-tag-alt text-primary me-0 font-22" title="Tag Company"></i>
                            </a>';
            }

            echo '<tr>
                    <td>' . $comp . '</td>
                    <td>' . $action . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
    }

    public function updateCompanyAgency()
    {
        $input      = $this->input->post(NULL, TRUE);
        $id         = $input['id'];
        $process    = $input['process'];
        $company    = stripslashes($input['company']);

        if ($process == 'delete') {
            $cond   = array('company_code' => $id);
            $ac     = $this->promo_model->selectAll_tcR_tk('promo_locate_company', $cond)['agency_code'];
            $sql    = $this->promo_model->delete_tc_tk('promo_locate_company', $cond);
            $report = $company . ' has been untagged.';
        } else if ($process == 'add') {
            $ac     = $id;
            $data   = array(
                'agency_code'   => $id,
                'company_name'  => $company,
                'created_at'    => date('Y-m-d H:i:s')
            );

            $sql    = $this->promo_model->insert_tdA_tk('promo_locate_company', $data);
            $report = $company . ' has been tagged.';
        }

        $data = array(
            'activity'  => "Promo Company Agency Setup ($process)",
            'date'      => $this->today,
            'time'      => date("H:i:s"),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $insert = $this->promo_model->insert_tdA('logs', $data);

        if ($sql) {
            echo json_encode(['message' => 'success', 'report' => $report, 'ac' => $ac]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function companyList()
    {
        $data   = [];
        $list   = $this->promo_model->selectAll('locate_promo_company');
        foreach ($list as $row) {

            $action =   '<a href="javascript:;" title="edit" id="edit" onclick="updateForm(this.id, \'' . $row['pc_code'] . '\', \'company\')">
                            <i class="bx bx-edit text-primary me-0" style="font-size: 22px;"></i>
                        </a>';
            if ($row['status'] == 1) {
                $action .=  '<a href="javascript:;" title="deactivate" id="deactivate" onclick="updateCompany(this.id, \'' . $row['pc_code'] . '\')">
                                <i class="bx bx-bulb text-warning me-0" style="font-size: 22px;"></i>
                            </a>';
            } else {
                $action .=  '<a href="javascript:;" title="activate" id="activate" onclick="updateCompany(this.id, \'' . $row['pc_code'] . '\')">
                                <i class="bx bx-bulb text-secondary me-0" style="font-size: 22px;"></i>
                            </a>';
            }

            if (in_array($this->userId, $this->adminUser)) {
                $action .=  '<a href="javascript:;" title="delete" id="delete" onclick="updateCompany(this.id, \'' . $row['pc_code'] . '\')">
                                <i class="bx bx-trash text-danger me-0" style="font-size: 22px;"></i>
                            </a>';
            }
            $column     = [];
            $column[]   = $row['pc_name'];
            $column[]   = $action;
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }

    public function updateCompany()
    {
        $input = $this->input->post(NULL, TRUE);
        if ($input['process'] == 'edit') {

            $cond   = array('pc_name' => $input['pc_name'], 'pc_code !=' => $input['id']);
            $chk    = $this->promo_model->selectAll_tcA('locate_promo_company', $cond);
            if ($chk) {
                $message = 'Company name already exists!';
                echo json_encode(['message' => 'exist', 'report' => $message]);
                return;
            }

            $message = 'Company has been updated.';

            $cond   = array('pc_code' => $input['id']);
            $pcName = $this->promo_model->selectAll_tcR('locate_promo_company', $cond)['pc_name'];

            $comp   = $input['pc_name'];
            $data   = array('pc_name' => strtoupper($comp), 'updated_at' => date('Y-m-d H:i:s'));
            $sql    = $this->promo_model->update_twdA('locate_promo_company', $cond, $data);

            $cond   = array('company_name' => $pcName);
            $data   = array('company_name' => strtoupper($comp), 'updated_at' => date('Y-m-d H:i:s'));
            $sql    = $this->promo_model->update_twdA_tk('promo_locate_company', $cond, $data);

            $cond   = array('company' => $pcName);
            $data   = array('company' => strtoupper($comp), 'updated_at' => date('Y-m-d H:i:s'));
            $sql    = $this->promo_model->update_twdA('promo_company_products', $cond, $data);
        } else if ($input['process'] == 'add') {
            $cond   = array('pc_name' => $input['pc_name']);
            $chk    = $this->promo_model->selectAll_tcA('locate_promo_company', $cond);
            if ($chk) {
                $message = 'Company name already exists!';
                echo json_encode(['message' => 'exist', 'report' => $message]);
                return;
            }

            $message = 'Company has been added.';

            $comp = $input['pc_name'];
            $data = array(
                'pc_name'       => strtoupper($comp),
                'status'        => 1,
                'created_at'    => date('Y-m-d H:i:s')
            );
            $sql = $this->promo_model->insert_tdA('locate_promo_company', $data);
        } else if ($input['process'] == 'activate') {

            $message = 'Company has been activated.';

            $cond   = array('pc_code' => $input['id']);
            $data   = array('status' => 1, 'updated_at' => date('Y-m-d H:i:s'));
            $sql    = $this->promo_model->update_twdA('locate_promo_company', $cond, $data);
        } else if ($input['process'] == 'deactivate') {

            $message = 'Company has been deactivated.';

            $cond   = array('pc_code' => $input['id']);
            $data   = array('status' => 0, 'updated_at' => date('Y-m-d H:i:s'));
            $sql    = $this->promo_model->update_twdA('locate_promo_company', $cond, $data);
        } else if ($input['process'] == 'delete') {

            $message = 'Company has been deleted.';

            $cond   = array('pc_code' => $input['id']);
            $sql    = $this->promo_model->delete_tcr_row('locate_promo_company', $cond);
        }

        $activity   = "Changes made for Promo Company ($input[process])";
        $data       = array(
            'activity'  => $activity,
            'date'      => $this->today,
            'time'      => date("H:i:s"),
            'user'      => $this->userId,
            'username'  => $_SESSION['username'],
        );
        $logs = $this->promo_model->insert_tdA('logs', $data);

        if ($sql) {
            echo json_encode(['message' => 'success', 'report' => $message]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function productCompany()
    {
        $data = [];
        $list = $this->promo_model->selectAll('locate_promo_company');
        foreach ($list as $row) {
            $comp   = $row['pc_name'];
            $button = '<a href="javascript:;"  onclick="compProducts(\'' . $comp . '\')">
                        <i class="bx bx-grid-alt me-0 font-22" title="View Product Tagging"></i>
                      </a>';
            $column     = [];
            $column[]   = $comp;
            $column[]   = $button;
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }

    public function compProducts()
    {
        $input = $this->input->post(NULL, TRUE);
        echo '<div class="table-responsive">';
        echo '<table id="compProducts" class="table table-sm table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Action</th>
                    </tr>
                </thead>';
        $comp   = $input['company'];
        $cond   = array('status' => '1');
        $query  = $this->promo_model->selectAll_tcA('locate_promo_product', $cond);
        foreach ($query as $key => $row) {

            $product    = $row['product'];
            $cond       = array('company' => $comp, 'product' => $product);
            $pcp        = $this->promo_model->selectAll_tcR('promo_company_products', $cond);
            $action     = '';
            $id         = '';

            if ($pcp) {
                $id = $pcp['id'];
                $action =  '<a href="javascript:;" id="delete" onclick="updateProductCompany(this.id, \'' . $id . '\', \'' . $comp . '\', \'' . $product . '\')">
                                <i class="bx bx-x-circle text-danger me-0 font-22" title="Untag Product"></i>
                            </a>';
            } else {
                $action =  '<a href="javascript:;" id="add" onclick="updateProductCompany(this.id, \'' . $id . '\', \'' . $comp . '\', \'' . $product . '\')">
                                <i class="bx bx-purchase-tag-alt text-primary me-0 font-22" title="Tag Product"></i>
                            </a>';
            }

            echo '<tr>
                    <td>' . $product . '</td>
                    <td>' . $action . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
    }

    public function updateProductCompany()
    {
        $input      = $this->input->post(NULL, TRUE);
        $id         = $input['id'];
        $process    = $input['process'];
        $product    = $input['product'];
        $company    = $input['company'];

        if ($process == 'delete') {

            $report = 'Product has been untagged.';
            $cond   = array('id' => $id);
            $sql    = $this->promo_model->delete_tcr_row('promo_company_products', $cond);
        } else if ($process == 'add') {

            $report = 'Product has been tagged.';
            $data   = array(
                'company' => $company,
                'product' => $product,
                'created_at' =>  date('Y-m-d H:i:s')
            );

            $sql = $this->promo_model->insert_tdA('promo_company_products', $data);
        }

        if ($sql) {
            echo json_encode(['message' => 'success', 'report' => $report]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function productList()
    {
        $data   = [];
        $list   = $this->promo_model->selectAll('locate_promo_product');
        foreach ($list as $row) {
            $action =   '<a href="javascript:;" title="edit" id="edit" onclick="updateForm(this.id, \'' . $row['id'] . '\', \'product\')">
                            <i class="bx bx-edit text-primary me-0" style="font-size: 22px;"></i>
                        </a>';
            if ($row['status'] == 1) {
                $action .=  '<a href="javascript:;" title="deactivate" id="deactivate" onclick="updateProduct(this.id, \'' . $row['id'] . '\')">
                                <i class="bx bx-bulb text-warning me-0" style="font-size: 22px;"></i>
                            </a>';
            } else {
                $action .=  '<a href="javascript:;" title="activate" id="activate" onclick="updateProduct(this.id, \'' . $row['id'] . '\')">
                                <i class="bx bx-bulb text-secondary me-0" style="font-size: 22px;"></i>
                            </a>';
            }

            if (in_array($this->userId, $this->adminUser)) {
                $action .=  '<a href="javascript:;" title="delete" id="delete" onclick="updateProduct(this.id, \'' . $row['id'] . '\')">
                                <i class="bx bx-trash text-danger me-0" style="font-size: 22px;"></i>
                            </a>';
            }
            $column     = [];
            $column[]   = $row['product'];
            $column[]   = $action;
            $data[]     = $column;
        }

        echo json_encode(array('data' => $data));
    }

    public function updateProduct()
    {
        $input = $this->input->post(NULL, TRUE);

        if ($input['process'] == 'edit') {

            $cond   = array('product' => $input['product'], 'id !=' => $input['id']);
            $chk    = $this->promo_model->selectAll_tcA('locate_promo_product', $cond);
            if ($chk) {
                $message = 'Product name already exists!';
                echo json_encode(['message' => 'exist', 'report' => $message]);
                return;
            }

            $message    = 'Product has been updated.';

            $cond   = array('id' => $input['id']);
            $prod1  = $this->promo_model->selectAll_tcR('locate_promo_product', $cond)['product'];

            $prod2  = $input['product'];
            $data   = array('product' => strtoupper($prod2), 'updated_at' => date('Y-m-d H:i:s'));
            $sql    = $this->promo_model->update_twdA('locate_promo_product', $cond, $data);

            $cond   = array('product' => $prod1);
            $data   = array('product' => strtoupper($prod2), 'updated_at' => date('Y-m-d H:i:s'));
            $sql    = $this->promo_model->update_twdA('promo_products', $cond, $data);
            $sql    = $this->promo_model->update_twdA('promo_company_products', $cond, $data);
        } else if ($input['process'] == 'add') {

            $cond   = array('product' => $input['product']);
            $chk    = $this->promo_model->selectAll_tcA('locate_promo_product', $cond);
            if ($chk) {
                $message = 'Product name already exists!';
                echo json_encode(['message' => 'exist', 'report' => $message]);
                return;
            }

            $message = 'Product has been added.';
            $prod = $input['product'];
            $data = array(
                'product'       => strtoupper($prod),
                'status'        => 1,
                'created_at'    => date('Y-m-d H:i:s')
            );

            $sql = $this->promo_model->insert_tdA('locate_promo_product', $data);
        } else if ($input['process'] == 'activate') {
            $message = 'Product has been activated.';

            $cond   = array('id' => $input['id']);
            $data   = array('status' => 1, 'updated_at' => date('Y-m-d H:i:s'));
            $sql    = $this->promo_model->update_twdA('locate_promo_product', $cond, $data);
        } else if ($input['process'] == 'deactivate') {
            $message = 'Product has been deactivated.';

            $cond   = array('id' => $input['id']);
            $data   = array('status' => 0, 'updated_at' => date('Y-m-d H:i:s'));
            $sql    = $this->promo_model->update_twdA('locate_promo_product', $cond, $data);
        } else if ($input['process'] == 'delete') {
            $message = 'Product has been deleted.';

            $cond   = array('id' => $input['id']);
            $sql    = $this->promo_model->delete_tcr_row('locate_promo_product', $cond);
        }

        if ($sql) {
            echo json_encode(['message' => 'success', 'report' => $message]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function departmentList()
    {
        $data   = [];
        $cond   = array('dept_name !=' => '');
        $list   = $this->promo_model->selectDistinct('dept_name', 'locate_promo_department', $cond);
        foreach ($list as $row) {

            $button     = '<a href="javascript:;" onclick="deptStores(\'' . $row['dept_name'] . '\')">
                            <i class="bx bx-store-alt me-0 font-22" title="View Tagged Store(s)"></i>
                          </a>';
            if (in_array($this->userId, $this->adminUser)) {
                $button     .=  '<a href="javascript:;" onclick="deleteDept(\'deleteDept\',\'' . $row['dept_name'] . '\')">
                                    <i class="bx bx-trash text-danger me-0 font-22" title="Delete Department"></i>
                                </a>';
            }
            $column     = [];
            $column[]   = $row['dept_name'];
            $column[]   = $button;
            $data[]     = $column;
        }
        echo json_encode(array('data' => $data));
    }

    public function deptStores()
    {
        $input = $this->input->post(NULL, TRUE);
        echo '<div class="table-responsive">';
        echo '<table id="" class="table table-sm table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Store ID</th>
                        <th>Store(s) Name</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>';
        $dept   = $input['dept'];
        $query  = $this->promo_model->selectAll('locate_promo_business_unit');
        foreach ($query as $key => $row) {

            $str_id = $row['bunit_id'];
            $store  = $row['bunit_name'];
            $cond   = array('bunit_id' => $str_id, 'dept_name' => $dept);
            $lpt    = $this->promo_model->selectAll_tcR('locate_promo_department', $cond);

            if (count($lpt) != 0) {
                $status = $lpt['status'];
                $id     = $lpt['dept_id'];
                if ($status == 'active') {
                    $action =  '<a href="javascript:;" title="Deactivate" id="deactivate" onclick="updateDept(this.id, \'' . $id . '\',\'' . $dept . '\',\'' . $store . '\')">
                                    <i class="bx bx-bulb text-warning me-0 font-22"></i>
                                </a>';
                } else if ($status == 'inactive') {
                    $action =  '<a href="javascript:;" title="Activate" id="activate" onclick="updateDept(this.id, \'' . $id . '\',\'' . $dept . '\',\'' . $store . '\')">
                                    <i class="bx bx-bulb text-secondary me-0 font-22"></i>
                                </a>';
                }
                if (in_array($this->userId, $this->adminUser)) {
                    $action .=  '<a href="javascript:;" title="Untag store" id="untag" onclick="updateDept(this.id, \'' . $id . '\',\'' . $dept . '\',\'' . $store . '\')">
                                    <i class="bx bx-minus-circle text-danger me-0 font-22"></i>
                                </a>';
                }
            } else {
                $action =  '<a href="javascript:;" title="Tag store" id="tag" onclick="updateDept(this.id, \'' . $str_id . '\', \'' . $dept . '\',\'' . $store . '\')">
                                <i class="bx bx-plus-circle text-primary me-0 font-22"></i>
                            </a>';
            }
            echo '<tr>
                    <td>' . $str_id . '</td>
                    <td>' . $store . '</td>
                    <td class="text-center">' . $action . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
    }

    public function updateDepartment()
    {
        $input = $this->input->post(NULL, TRUE);
        if ($input['process'] == 'deleteDept') {
            $report = 'Department has been deleted.';
            $cond   = array('dept_name' => $input['dept']);
            $sql    = $this->promo_model->delete_tcr_row('locate_promo_department', $cond);
        } else if ($input['process'] == 'add') {
            $dept   = strtoupper($input['dept']);
            $cond   = array('dept_name' => $dept);
            $chk    = $this->promo_model->selectAll_tcR('locate_promo_department', $cond);
            if ($chk) {
                $report = 'Department already exist.';
                echo json_encode(['message' => 'exist', 'report' => $report]);
                return;
            }

            $report = 'Department has been added!';
            $data   = array('dept_name' => $dept, 'status' => 'active');
            $sql    = $this->promo_model->insert_tdA('locate_promo_department', $data);
        } else if ($input['process'] == 'untag') {

            $report = 'Store has been untagged.';
            $cond   =  array('dept_id' => $input['id']);
            $sql    = $this->promo_model->delete_tcr_row('locate_promo_department', $cond);
        } else if ($input['process'] == 'tag') {

            $dept   = strtoupper($input['dept']);
            $report = 'Store has been tagged.';
            $data   = array('bunit_id' => $input['id'], 'dept_name' => $dept, 'status' => 'active');
            $cond   = array('bunit_id' => '0', 'dept_name' => $dept);
            $lpt    = $this->promo_model->selectAll_tcR('locate_promo_department', $cond);
            if ($lpt) {
                $sql = $this->promo_model->update_twdA('locate_promo_department', $cond, $data);
            } else {
                $sql = $this->promo_model->insert_tdA('locate_promo_department', $data);
            }
        } else if ($input['process'] == 'deactivate') {
            $report = 'Store has been deactivated.';
            $data   = array('status' => 'inactive');
            $cond   =  array('dept_id' => $input['id']);
            $sql    = $this->promo_model->update_twdA('locate_promo_department', $cond, $data);
        } else if ($input['process'] == 'activate') {
            $report = 'Store has been activated.';
            $data   = array('status' => 'active');
            $cond   =  array('dept_id' => $input['id']);
            $sql    = $this->promo_model->update_twdA('locate_promo_department', $cond, $data);
        }

        if ($sql) {
            echo json_encode(['message' => 'success', 'report' => $report]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }

    public function searchPromoSetup()
    {
        echo '<ul class="list-group">';
        $input  = $this->input->post(NULL, TRUE);
        $query  = $this->promo_model->searchPromoSetup($input);
        if ($query) {
            foreach ($query as  $row) {
                $name       = ucwords(strtolower($row['name']));
                $emp_id     = $row['emp_id'];
                $onclick    = 'onclick="selectPromoSetup(\'' . $name . '\',\'' . $emp_id . '\')"';

                echo '<a href="javascript:;" class="list-group-item" ' . $onclick . '>' . $name . '</a>';
            }
        } else {
            echo '<a href="javascript:;" class="list-group-item">No Results Found!</a>';
        }
        echo '</ul>';
    }

    public function selectPromoSetup()
    {
        $input      = $this->input->post(NULL, TRUE);
        $process    = $input['process'];
        $emp_id     = $input['emp_id'];

        if ($process == 'setupSubordinate') {

            $cond   = array('emp_id' => $emp_id);
            $row    = $this->promo_model->selectAll_tcR('employee3', $cond);
            $cc     = $row['company_code'];
            $bc     = $row['company_code'] . $row['bunit_code'];
            $dc     = $row['company_code'] . $row['bunit_code'] . $row['dept_code'];
            $sc     = $row['company_code'] . $row['bunit_code'] . $row['dept_code'] . $row['section_code'];

            $style      = 'style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"';
            $company    = $this->promo_model->selectAll_tcR('locate_company', array('company_code' => $cc))['company'];
            $stores     = $this->promo_model->selectAll_tcR('locate_business_unit', array('bcode' => $bc))['business_unit'];

            $dept       = $this->promo_model->selectAll_tcR('locate_department', array('dcode' => $dc))['dept_name'];
            $section    = $this->promo_model->selectAll_tcR('locate_section', array('scode' => $sc))['section_name'];
            $status     = '<font class="text-warning">' . $row['current_status'] . '</font>';
            if ($row['current_status'] == 'Active') {
                $status = '<font class="text-success">' . $row['current_status'] . '</font>';
            }

            echo '<table class="table table-sm">';

            echo '<tr>';
            echo '<th>Employee ID</th>';
            echo '<th>:</th>';
            echo '<td>' . $emp_id . ' (' . $status . ')</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<th>Company</th>';
            echo '<th>:</th>';
            echo '<td class="text-truncate pComp" ' . $style . ' title="' . $company . '">' . $company . '</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<th>Store(s)</th>';
            echo '<th>:</th>';
            echo '<td class="text-truncate pComp" ' . $style . ' title="' . $stores . '">' . $stores . '</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<th>Department</th>';
            echo '<th>:</th>';
            echo '<td>' . $dept . '</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<th>Section</th>';
            echo '<th>:</th>';
            echo '<td>' . $section . '</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<th>Position</th>';
            echo '<th>:</th>';
            echo '<td class="text-truncate pComp" ' . $style . ' title="' . $row['position'] . '">' . $row['position'] . '</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<th>Employee Type</th>';
            echo '<th>:</th>';
            echo '<td>' . $row['emp_type'] . '</td>';
            echo '</tr>';

            echo '</table">';
        }
    }

    public function subordinates()
    {
        $input  = $this->input->post(NULL, TRUE);
        echo '<div class="row">';
        echo '<div class="col-md-12 mb-3 text-end">';
        // if ($this->userId == '01186-2023' || $this->userId == '02897-2023' || $this->userId == '02456-2022') {
        echo '<button type="button" class="btn btn-primary btn-sm me-1" onclick="filterForm(\'' . $input['emp_id'] . '\')">Filter List</button>';
        // }
        echo '<button type="button" class="btn btn-primary btn-sm me-1" onclick="promoList(\'' . $input['emp_id'] . '\')">Add Subordinates</button>';
        echo '<button type="button" class="btn btn-danger btn-sm" onclick="removeSub(\'' . $input['emp_id'] . '\')">Delete Checked</button>';
        echo '</div>';
        echo '<label class="form-label text-secondary">SUBORDINATES LIST</label>';
        echo '<div class="table-responsive">';
        echo '<table id="setupSubordinateTable" class="table table-sm table-hover table-align" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID/Name</th>
                        <th>Position</th>
                        <th>Deprtment</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>';
        $select = '*,t1.record_no,t2.record_no as sub_id';
        $table1 = 'employee3';
        $table2 = 'leveling_subordinates';
        $join   = 't1.emp_id=t2.subordinates_rater';
        $cond   = "ratee = '$input[emp_id]' AND (emp_type = 'Promo' OR emp_type = 'Promo-NESCO' OR emp_type = 'Promo-EasyL')";
        $order  = 'name|ASC';
        $query  = $this->promo_model->join_ttjcoA($select, $table1, $table2, $join, null, $cond, $order, null);
        foreach ($query as $row) {


            $cond   = array('emp_id' => $row['emp_id'], 'record_no' => $row['record_no']);
            $check  = $this->promo_model->selectAll_tcR('promo_record', $cond);
            if ($check) {

                $emp_id = $row['emp_id'];
                $name   = ucwords(strtolower($row['name']));
                $idName = '[' . $emp_id . '] ' . $name;
                $link   = '<a href="' . base_url('promo/page/promo/profile/' . $emp_id) . '" target="_blank">' . $idName . '</a>';

                if ($row['current_status'] == 'Active') {
                    $stat = '<span class="badge bg-success">' . $row['current_status'] . '</span>';
                } else if ($row['current_status'] == 'blacklisted') {
                    $stat = '<span class="badge bg-danger">' . $row['current_status'] . '</span>';
                } else {
                    $stat = '<span class="badge bg-warning">' . $row['current_status'] . '</span>';
                }

                echo '<tr>
                        <td><input class="form-check-input" type="checkbox" name="subId[]" value="' . $row['sub_id'] . '" style=""></td>
                        <td>' . $link . '</td>
                        <td>' . $row['position'] . '</td>
                        <td>' . $check['promo_department'] . '</td>
                        <td>' . $stat . '</td>';
                echo '</tr>';
            }
        }

        echo    '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }

    public function promoList()
    {
        $input = $this->input->post(NULL, TRUE);
        echo '<div class="row">';
        echo '<div class="table-responsive">';
        echo '<table id="promoListTable" class="table table-sm table-hover table-align" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID/Name</th>
                        <th>Position</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>';
        $filter = '';
        if (isset($input['store']) && $input['store'] != '') {
            $str = explode('|', $input['store']);
            $filter = "$str[1]='T' AND ";
            if ($str[1] == 'al_tub') {
                $filter = "($str[1]='T' OR fr_tubigon='T') AND ";
            }
        } else {
            $cond   = array('emp_id' => $input['emp_id']);
            $sup    = $this->promo_model->selectAll_tcR('employee3', $cond);
            $bc     = $sup['company_code'] . $sup['bunit_code'];
            $store  = $this->promo_model->selectAll_tcR('locate_business_unit', array('bcode' => $bc))['business_unit'];
            $bu = $this->promo_model->selectAll_tcR('locate_promo_business_unit', array('bunit_name' => $store))['bunit_field'];
            if ($bu) {
                $filter = "$bu='T' AND ";
                if ($bu == 'al_tub') {
                    $filter = "($bu='T' OR fr_tubigon='T') AND ";
                }
            }
        }

        if (isset($input['dept']) && $input['dept'] != '') {
            $filter .= "promo_department='$input[dept]' AND ";
        }

        if (isset($input['type']) && $input['type'] != '') {
            $filter .= "promo_type='$input[type]' AND ";
        }

        if (isset($input['agency']) && $input['agency'] != '') {
            $filter .= "agency_code='$input[agency]' AND ";
        }

        if (isset($input['comp']) && $input['comp'] != '') {
            $filter .= "promo_company='$input[comp]' AND ";
        }


        $select = '*';
        $table1 = 'employee3';
        $table2 = 'promo_record';
        $order  = 'name|ASC';
        $join   = 't1.emp_id=t2.emp_id AND t1.record_no=t2.record_no';
        $cond   = "$filter current_status != 'blacklisted' AND current_status != 'Deceased' AND sub_status != 'Cleared' AND (emp_type = 'Promo' OR emp_type = 'Promo-NESCO' OR emp_type = 'Promo-EasyL')";
        $query  = $this->promo_model->join_ttjcoA($select, $table1, $table2, $join, null, $cond, $order, null);
        foreach ($query as $key => $row) {
            $cond = array('ratee' => $input['emp_id'], 'subordinates_rater' => $row['emp_id']);
            $check = $this->promo_model->selectAll_tcR('leveling_subordinates', $cond);
            if (!$check) {

                $emp_id = $row['emp_id'];
                $name   = ucwords(strtolower($row['name']));
                $idName = '[' . $emp_id . '] ' . $name;
                $link   = '<a href="' . base_url('promo/page/promo/profile/' . $emp_id) . '" target="_blank">' . $idName . '</a>';

                if ($row['current_status'] == 'Active') {
                    $stat = '<span class="badge bg-success">' . $row['current_status'] . '</span>';
                } else if ($row['current_status'] == 'blacklisted') {
                    $stat = '<span class="badge bg-danger">' . $row['current_status'] . '</span>';
                } else {
                    $stat = '<span class="badge bg-warning">' . $row['current_status'] . '</span>';
                }

                echo '<tr>
                        <td><input class="form-check-input" type="checkbox" name="emp_ids[]" value="' . $row['emp_id'] . '" style=""></td>
                        <td>' . $link . '</td>
                        <td>' . $row['position'] . '</td>
                        <td>' . $stat . '</td>';
                echo '</tr>';
            }
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
    }

    public function updateSubordinates()
    {
        $input = $this->input->post(NULL, TRUE);

        if ($input['process'] == 'delete') {
            $supID  = $this->promo_model->selectAll_tcR('leveling_subordinates', array('record_no' => $input['id']))['ratee'];
            $sql    = $this->promo_model->delete_tcr_row('leveling_subordinates', array('record_no' => $input['id']));
        } else if ($input['process'] == 'add') {
            $supID  = $input['id'];
            $data   = array('ratee' => $input['id'], 'subordinates_rater' => $input['emp_id'], 'date_added' => $this->today);
            $sql    = $this->promo_model->insert_tdA('leveling_subordinates', $data);
        } else if ($input['process'] == 'addMul') {
            $supID  = $input['ratee'];
            foreach ($input['empId'] as $key => $empId) {
                $data = array('ratee' => $input['ratee'], 'subordinates_rater' => $empId, 'date_added' => $this->today);
                $sql = $this->promo_model->insert_tdA('leveling_subordinates', $data);
            }
        } else if ($input['process'] == 'deleteMul') {
            $supID = $input['ratee'];
            foreach ($input['subId'] as $key => $subId) {
                $sql = $this->promo_model->delete_tcr_row('leveling_subordinates', array('record_no' => $subId));
            }
        }

        if ($sql) {
            echo json_encode(['message' => 'success', 'sup_id' => $supID]);
        } else {
            echo json_encode(['message' => 'failed']);
        }
    }
}
