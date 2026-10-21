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

    table {
        font-size: 13px;
    }
</style>

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-primary">Add Blacklist</h5>
                </div>
                <hr>
                <h6 class="card-title">Search employee records...</h6>
                <div class="row align-items-center mb-3">
                    <div class="col-md-3">
                        <input type="search" class="form-control" name="lastname" autocomplete="off" placeholder="Lastname">
                    </div>
                    <div class="col-md-3">
                        <input type="search" class="form-control" name="firstname" autocomplete="off" placeholder="Firstname">
                    </div>
                    <div class="col-md-3">
                        <input type="search" class="form-control" name="middlename" autocomplete="off" placeholder="Middlename">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-sm btn-primary px-3" onclick="checkBl()">Browse</button>
                        <button type="button" class="btn btn-sm btn-success px-3 addManual" onclick="addManual()" style="display: none;">Add Manually</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 checkBl" style="display: none;">
                        <div class="text-center">
                            <p class="mb-0"><strong>Records Match on applicant and employee3 tables.</strong></p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover" style="width:100%" id="checkBl_table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Current Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6 checkBlt" style="display: none;">
                        <div class="text-center">
                            <p class="mb-0"><strong>Records Match on blacklist table.</strong></p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover" style="width:100%" id="checkBlt_table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Current Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

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