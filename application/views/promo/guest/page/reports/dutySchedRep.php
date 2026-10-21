<style>
    .required_alert {
        width: 300px;
        font-size: 12px;
    }

    .slider {
        overflow: auto;
        max-height: 400px;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <form action="<?= base_url('promo/reports/generateDutySched') ?>" method="POST" target="_blank" onSubmit="return generateDutySched('excel')">
                    <h5 class="card-title text-primary">Duty Schedule Report</h5>
                    <hr class="mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-secondary mb-1 mt-2">Store(s)</label>
                            <select name="store" class="form-select select2" onchange="getDepartment(this.value)" data-placeholder="Select Store">
                                <option value="">Select Store</option>
                                <?php
                                $query = $this->promo_model->locate_promo_bu($this->hrCode);
                                foreach ($query as $key => $bu) {
                                    echo '<option value="' . $bu['bunit_id'] . '|' . $bu['bunit_field'] . '">' . $bu['bunit_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="text-secondary mb-1 mt-2">Department</label>
                            <select name="promo_department" class="form-select select2" data-placeholder="Select Department">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="text-secondary mb-1 mt-2">Company</label>
                            <select name="promo_company" class="form-select select2" data-placeholder="Select Company">
                                <option value="">Select Company</option>
                                <?php
                                $query = $this->promo_model->selectAll('locate_promo_company');
                                foreach ($query as $key => $value) {
                                    echo '<option value="' . $value['pc_name'] . '">' . $value['pc_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="text-secondary mb-1 mt-2">Current Status</label>
                            <select name="current_status" class="form-select select2" data-placeholder="Select Current Status">
                                <?php
                                $condition = array('current_status !=' => '');
                                $query = $this->promo_model->selectDistinct('current_status', 'employee3', $condition);
                                foreach ($query as $key => $value) {
                                    if ($value['current_status'] == 'Active') {
                                        echo '<option value="' . $value['current_status'] . '" selected>' . $value['current_status'] . '</option>';
                                    } else {
                                        echo '<option value="' . $value['current_status'] . '">' . $value['current_status'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-primary pull-right">Generate in Excel</button>
                            <button type="button" class="btn btn-secondary pull-right me-2" onclick="generateDutySched('list')">Generate List</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end page wrapper -->

    <div class="modal fade" id="dutySchedList" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Duty Schedule List</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body table-responsive"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>