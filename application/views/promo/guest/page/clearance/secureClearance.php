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
                    <h5 class="card-title text-primary">Secure Clearance</h5>
                </div>
                <hr>
                <form id="secureClearance" autocomplete="off">

                    <input type="hidden" class="form-control" name="clearanceProcess" value="secure">
                    <input type="hidden" class="form-control" name="emp_id">
                    <input type="hidden" class="form-control" name="record_no">
                    <input type="hidden" class="form-control" name="eocdate">
                    <div class="row">
                        <div class="col-md-6 mb-3 searchContainer">
                            <label class="form-label">Search Employee</label>
                            <input type="search" class="form-control" id="searchInput" name="name" placeholder="Search here..." onkeyup="searchPromoClearance(this.value)">
                            <div class="dropdown-list"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Promo Type</label>
                            <input type="text" class="form-control readonly" name="promo_type" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reason for Securing Clearance</label>
                            <select class="form-select" name="reason" onchange="selectReason(this.value)" data-placeholder="Select Reason" disabled>
                                <option value="">Select Reason</option>
                                <option value="Ad-Resigned">Advised to Resign</option>
                                <option value="V-Resigned">Voluntary Resignation</option>
                                <option value="Termination">Termination of Contract</option>
                                <option value="Deceased">Deceased</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Store(s)</label>
                            <select class="form-select" name="stores" data-placeholder="Select Store">
                                <option value="">Select Store</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3 requirement"></div>
                    <div class="row">
                        <div class="col-md-12 ">
                            <div class="d-md-flex d-grid align-items-center gap-3">
                                <button type="submit" class="btn btn-primary px-4">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->