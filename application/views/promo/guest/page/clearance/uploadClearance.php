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
                    <h5 class="card-title text-primary">Upload Clearance</h5>
                </div>
                <hr>
                <div class="row">
                    <div class="col">
                        <form id="uploadClearance" class="row g-3" autocomplete="off">
                            <input type="hidden" class="form-control" name="clearanceProcess" value="upload">
                            <input type="hidden" class="form-control" name="emp_id">
                            <input type="hidden" class="form-control" name="record_no">
                            <input type="hidden" class="form-control" name="scpr_id">
                            <div class="col-md-6 searchContainer">
                                <label class="form-label">Search Employee</label>
                                <input type="search" class="form-control" name="name" placeholder="Search here..." onkeyup="searchPromoClearance(this.value)">
                                <div class="dropdown-list searchList"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Promo Type</label>
                                <input type="text" class="form-control" name="promo_type" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Store(s)</label>
                                <select class="form-select" name="stores" onchange="browseEpas(this.value)">
                                    <option value="">Select Store</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">EPAS</label>
                                <input type="text" class="form-control readonly" name="epas" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Signed Clearance</label>
                                <input type="file" class="form-control" name="clearance">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Succeeding Status <i style="font-size: 10px;color: red;">if all stores are COMPLETED</i></label>
                                <input type="text" class="form-control" name="stat" disabled>
                            </div>
                            <div class="col-md-12 ">
                                <div class="d-md-flex d-grid align-items-center gap-3 reprintButton">
                                    <button type="submit" class="btn btn-primary px-4">Submit</button>
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