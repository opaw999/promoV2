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
                    <h5 class="card-title text-primary">Add Promo User Account</h5>
                </div>
                <hr>
                <form id="userAccountForm" autocomplete="off">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="row">
                                <input type="hidden" name="emp_id">
                                <input type="hidden" name="process" value="userAccount">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label text-secondary">Search Promo</label>
                                    <input type="search" class="form-control" id="searchInput" name="name" placeholder="Search here..." onkeyup="searchPromoUsers(this.value)" autocomplete="off">
                                    <div class="dropdown-list"></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-secondary">User Type</label>
                                    <input type="text" class="form-control" value="employee" disabled style="text-transform: uppercase;">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-secondary">Username</label>
                                    <input type="text" class="form-control readonly" name="username" readonly>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label text-secondary">Password</label>
                                    <input type="password" class="form-control readonly" name="password" readonly>
                                </div>
                                <div class="col-sm-12 d-md-flex d-grid align-items-center gap-3 justify-content-left mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="viewPassword" id="flexCheckDefault" onclick="passwordFunc(this.name)">
                                        <label class="form-check-label text-secondary" for="flexCheckDefault">
                                            Show Password
                                        </label>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" name="setPassword" id="flexSwitchCheckDefault1" onclick="passwordFunc(this.name)">
                                        <label class="form-check-label text-secondary" for="flexSwitchCheckDefault1">
                                            Set default Password : Hrms2014
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->