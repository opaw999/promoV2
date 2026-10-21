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
                    <h5 class="card-title text-primary">End of Contract List</h5>
                    <span>
                        <button type="button" class="btn btn-sm btn-primary px-2" data-bs-toggle="modal" data-bs-target="#eocFilter">Filter EOC</button>
                    </span>
                </div>
                <hr class="mt-2">
                <div class="table-responsive">
                    <table id="eocList" class="table table-sm table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Startdate</th>
                                <th>EOCdate</th>
                                <th>Store|EPAS</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="eocFilter" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter EOC List</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <label>Select Store(s)</label>
                <select class="form-select mb-3" name="store">
                    <option value="">All Stores</option>
                    <?php
                    $bUs = $this->promo_model->locate_promo_bu($this->hrCode);
                    foreach ($bUs as $key => $row) {
                        echo ' <option value="' . $row['bunit_field'] . '">' . $row['bunit_name'] . '</option>';
                    }
                    ?>
                </select>
                <label>Select Day(s)</label>
                <select class="form-select mb-3" name="day">
                    <option value="">All Days</option>
                    <option value="today" <?= ($emp_id == 'today' ? 'selected' : '') ?>>Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="last7days">Last 7 days</option>
                    <option value="last1month">Last 1 month</option>
                </select>
                <label>Select Month</label>
                <select class="form-select mb-3" name="month">
                    <option value="" selected>Select month...</option>
                    <?php
                    $months = $this->promo_model->months();
                    $cmonth = date('m');
                    foreach ($months as $key => $row) {

                        $selected = '';
                        // if ($cmonth == $key) {
                        //     $selected = 'selected';
                        // }
                        echo ' <option value="' . $key . '" ' . $selected . '>' . $row . '</option>';
                    }
                    ?>
                </select>
                <label>Select Year</label>
                <select class="form-select mb-3" name="year">
                    <option value="" selected>Select year...</option>
                    <?php
                    $pyear = date('Y', strtotime('-1 year'));
                    $nyear = date('Y', strtotime('+1 year'));
                    $cyear = date('Y');
                    echo ' <option value="' . $nyear . '">' . $nyear . '</option>';
                    echo ' <option value="' . $cyear . '">' . $cyear . '</option>';
                    echo ' <option value="' . $pyear . '">' . $pyear . '</option>';
                    ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="eocFilter()">Filter</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewAppraisal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appraisal Details </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewAppraisal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadClearanceForm" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Clearance Form...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadClearanceRenewal" autocomplete="off">
                <div class="modal-body uploadClearanceForm"></div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addBlacklistForm" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Blacklist form...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBlacklist" autocomplete="off">
                <div class="modal-body">
                    <div class="addBlacklistForm"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>