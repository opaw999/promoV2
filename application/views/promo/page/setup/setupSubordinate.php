<style>
    .required_alert {
        width: 300px;
        font-size: 12px;
    }

    .list-group-item {
        color: black;
        text-decoration: none;
        display: block;
        padding: 10px;
    }

    html.dark-theme .list-group-item {
        color: #acafb3;
    }

    html.dark-theme .list-group-item:hover {
        background-color: #303337;
        color: white;
    }

    .list-group-item:hover {
        background-color: #007bff;
        color: white;
    }

    .dropdown-list {
        position: relative;
    }

    .list-group {
        position: absolute;
        z-index: 999;
        max-height: 200px;
        width: 100%;
        overflow-y: auto;
    }

    .readonly[readonly] {
        background-color: #e9ecef;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-primary">Setup Subordinates to Supervisor</h5>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="row">
                            <input type="hidden" name="process" value="setupSubordinate">
                            <input type="hidden" name="emp_id">
                            <div class="col-md-12 mb-3">
                                <label class="form-label text-secondary">Search Supervisor</label>
                                <input type="search" class="form-control" id="searchInput" name="name" placeholder="Search here..." onkeyup="searchPromoSetup(this.value)" autocomplete="off">
                                <div class="dropdown-list"></div>
                            </div>
                            <div class="supDetails"></div>
                            <!-- <div class="d-md-flex d-grid align-items-center gap-1">
                                <span class="badge text-secondary"><b>LEGEND:</b></span>
                                <i class="bx bx-plus-circle font-18 text-primary"></i><span class="badge text-secondary">Add Subordinate</span>
                                <i class="bx bx-x-circle font-18 text-danger"></i><span class="badge text-secondary">Delete Subordinate</span>
                            </div> -->
                        </div>
                    </div>
                    <div class="col-md-8 setupSubordinateForm"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="promoList">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PROMO LIST</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body promoList"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary addingButton" id="" onclick="addSub(this.id)">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="filterList">
    <div class="modal-dialog modal-xl" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PROMO LIST</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="text-primary">Filter here...</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Agency</label>
                                <select name="agency_code" class="form-select select2" onchange="getCompany(this.value)" data-placeholder="Select Agency...">
                                    <option value="">Select Agency...</option>
                                    <?php
                                    $agency = $this->promo_model->selectAll_tk('promo_locate_agency');
                                    foreach ($agency as $value) {
                                        echo '<option value="' . $value['agency_code'] . '">' . $value['agency_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label class="form-label">Company</label>
                                <select name="promo_company" class="form-select select2" data-placeholder="Select Company...">
                                    <option value="">Select Company...</option>
                                    <?php
                                    $company = $this->promo_model->selectAll_tcA('locate_promo_company', array('pc_name !=' => ''));
                                    foreach ($company as $value) {
                                        echo '<option value="' . $value['pc_name'] . '">' . $value['pc_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label class="form-label">Store(s)</label>
                                <select name="store" class="form-select" onchange="filterDepartment(this.value)">
                                    <option value="">Select Store...</option>
                                    <?php
                                    $bus    = $this->promo_model->locate_promo_bu($this->hrCode);
                                    foreach ($bus as $bu) {
                                        $val = $bu['bunit_id'] . '|' . $bu['bunit_field'];
                                        echo '<option value="' . $val . '">' . $bu['bunit_name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label class="form-label">Department</label>
                                <select name="promo_department" class="form-select">
                                    <option value="">Select Department...</option>
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label class="form-label">Promo Type</label>
                                <select name="promo_type" class="form-select">
                                    <option value="">Select Promo Type...</option>
                                    <option value="ROVING">ROVING</option>
                                    <option value="STATION">STATION</option>
                                </select>
                            </div>
                            <div class="col-md-12 mt-2">
                                <button type="button" class="btn btn-info px-4 filterList" id="" onclick="filterList(this.id)">Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 filteredList"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary addingButton" id="" onclick="addSub(this.id)" style="display: none;">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>