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

    .form-control[readonly] {
        background-color: #e9ecef;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-primary">Reprint Clearance</h5>
                </div>
                <hr>
                <div class="row">
                    <div class="col">
                        <form id="reprintClearance" class="row g-3" autocomplete="off">
                            <input type="hidden" class="form-control" name="clearanceProcess" value="reprint">
                            <input type="hidden" class="form-control" name="emp_id">
                            <input type="hidden" class="form-control" name="record_no">
                            <div class="col-md-6 searchContainer">
                                <label for="input1" class="form-label">Search Employee</label>
                                <input type="search" class="form-control" name="name" placeholder="Search here..." onkeyup="searchPromoClearance(this.value)">
                                <div class="dropdown-list searchList"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Promo Type</label>
                                <input type="text" class="form-control" name="promo_type" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reason</label>
                                <input type="text" class="form-control" name="reason" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="input2" class="form-label">Store(s)</label>
                                <select class="form-control" name="stores">
                                    <option value="">Select Store</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="input11" class="form-label">Reason of Reprinting</label>
                                <textarea class="form-control" name="reasonReprint" placeholder="Reason ..." rows="3"></textarea>
                            </div>
                            <div class="col-md-12">
                                <div class="d-md-flex d-grid align-items-center gap-3 reprintButton">
                                    <button type="submit" class="btn btn-primary px-4">Generate</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->