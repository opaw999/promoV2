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
                    <h5 class="card-title text-primary">Add Resignation/Termination</h5>
                </div>
                <hr>
                <form id="addResignationForm" autocomplete="off">

                    <div class="row">
                        <div class="col-md-5">
                            <div class="row">
                                <input type="hidden" name="emp_id">
                                <input type="hidden" name="process" value="addResignation">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label text-secondary">Search Employee</label>
                                    <input type="search" class="form-control" id="searchInput" name="name" placeholder="Search here..." onkeyup="searchPromoResignation(this.value)">
                                    <div class="dropdown-list"></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-secondary">Date Effective</label>
                                    <input type="text" class="form-control datepicker" name="date" value="<?= date('Y-m-d') ?>" disabled>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-secondary">Status</label>
                                    <select name="status" class="form-select" onchange="addResignation(this.value)" disabled>
                                        <option value="">Select Status</option>
                                        <option value="End of Contract">End of Contract</option>
                                        <option value="Resigned">Resigned</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label text-secondary">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="3" disabled></textarea>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 resignation"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->