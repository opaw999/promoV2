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
                    <h5 class="card-title text-primary">Tag for Resignation</h5>
                </div>
                <hr>
                <input type="hidden" name="emp_id">
                <input type="hidden" name="process" value="tagResignation">
                <div class="row">
                    <!-- <div class="col-md-5">
                        <div class="col-md-12 mb-2 searchContainer">
                            <label class="form-label text-secondary">Search Supervisor</label>
                            <div class="input-group font-22">
                                <i class="input-group-text fadeIn animated lni lni-users"></i>
                                <input type="text" class="form-control" name="tagResignation" autocomplete="off" placeholder=" Search here..." onkeyup="searhPromo(this.value, this.name)">
                            </div>
                            <div class="dropdown-list tagResignation"></div>
                        </div>
                    </div> -->
                    <div class="col-md-5 mb-2">
                        <label class="form-label text-secondary">Search Supervisor</label>
                        <input type="search" class="form-control" id="searchInput" name="name" placeholder="Search here..." onkeyup="searchPromoResignation(this.value)" autocomplete="off">
                        <div class="dropdown-list"></div>
                    </div>
                    <div class="col-md-7"><br>
                        <div class="d-md-flex d-grid align-items-center gap-1 justify-content-left mt-3">
                            <span class="badge text-secondary"><b>LEGEND:</b></span>
                            <i class="fadeIn animated bx bx-check-circle text-success font-22"></i>
                            <span class="badge text-secondary">- Done EPAS Rating</span>
                            <i class="fadeIn animated bx bx-purchase-tag-alt text-primary font-22"></i>
                            <span class="badge text-secondary">- Tag for Resignation</span>
                            <i class="fadeIn animated bx bx-x-circle text-danger font-22"></i>
                            <span class="badge text-secondary">- Untag Pending Resignation</span>
                        </div>
                    </div>
                </div>
                <div class="row tagResignationTable mt-3"></div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->