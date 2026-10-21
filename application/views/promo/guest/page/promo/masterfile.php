<style>
    .required_alert {
        width: 300px;
        font-size: 12px;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-0">
                    <h5 class="card-title text-primary">Masterfile</h5>
                    <span>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#masterfileFilter">Filter</button>
                    </span>
                </div>
                <hr class="mt-2">
                <div class="table-responsive">
                    <table id="masterfile" class="table table-sm table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Store(s)</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>EmpType</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="masterfileFilter" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Masterfile </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php
                $bUs = $this->promo_model->locate_promo_bu($this->hrCode);
                $company = $this->promo_model->selectAll_tcA('locate_promo_company', array('pc_name !=' => ''));
                ?>
                <div class="row">
                    <div class="col-md-6">

                        <select name="store" class="form-select form-select-sm" onchange="filterDepartment(this.value)">
                            <option value="">Select Store...</option>
                            <?php
                            foreach ($bUs as $bu) {
                                $val = $bu['bunit_id'] . '|' . $bu['bunit_field'];
                                echo '<option value="' . $val . '">' . $bu['bunit_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select name="promo_department" class="form-select form-select-sm">
                            <option value="">Select Department...</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-3">
                        <select name="promo_type" class="form-select form-select-sm">
                            <option value="">Select Promo Type...</option>
                            <option value="ROVING">ROVING</option>
                            <option value="STATION">STATION</option>
                        </select>
                    </div>
                    <div class="col-md-6 mt-3">
                        <select name="promo_company" class="form-select form-select-sm select2ClearModal" data-placeholder="Select Company...">
                            <option value="">Select Company...</option>
                            <?php
                            foreach ($company as $value) {
                                echo '<option value="' . $value['pc_name'] . '">' . $value['pc_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="masterfileDatatables(true)" id="add">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>