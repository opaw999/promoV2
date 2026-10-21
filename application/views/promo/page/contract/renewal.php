<style>
    .required_alert {
        width: 300px;
        font-size: 12px;
    }

    html.dark-theme .list-group-item {
        color: #acafb3;
    }

    html.dark-theme .list-group-item:hover {
        background-color: #303337;
        color: white;
    }

    .list-group-item {
        color: black;
        text-decoration: none;
        display: block;
        padding: 10px;
    }

    .list-group-item:hover {
        background-color: #007bff;
        color: white;
    }

    .list-group {
        position: absolute;
        z-index: 999;
        max-height: 200px;
        width: 100%;
        overflow-y: auto;
    }

    .dropdown-list {
        position: relative;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-primary">Contract Renewal</h5>
                </div>
                <hr>
                <div class="row row-cols-3">
                    <div class="col">
                        <div class="card radius-10 border shadow-none">
                            <div class="card-body">
                                <div>
                                    <h5 class="card-title">End of Contract Process</h5>
                                </div>
                                <p class="card-text">List of Employees for renewal.</p>
                                <a href="<?= base_url('promo/page/contract/eoclist'); ?>" class="btn btn-primary">Renewal of Contract</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card radius-10 border shadow-none">
                            <div class="card-body">
                                <div>
                                    <h5 class="card-title">Extending of Contract Process</h5>
                                </div>
                                <p class="card-text">Extending of Contract for all employees.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#extendModal">Extend Contract</button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card radius-10 border shadow-none">
                            <div class="card-body">
                                <div>
                                    <h5 class="card-title">Manual Process</h5>
                                </div>
                                <p class="card-text">This is for ABENSON OUTLET only!</p>
                                <a href="javascript:;" class="btn btn-secondary">Manual Renewal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="extendModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Extend Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" name="process" value="extendContract">
                        <input type="hidden" class="form-control" name="emp_id">
                        <input type="hidden" class="form-control" name="record_no">
                        <label class="form-label">Search Promo to Extend Contract</label>
                        <input type="search" class="form-control" name="name" onkeyup="searchPromoContract(this.value)" placeholder="Search here..." autocomplete="off">
                        <div class="dropdown-list"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="proceedExtend()">Extend</button>
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