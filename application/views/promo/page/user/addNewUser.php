<style>
    .required_alert {
        width: 300px;
        font-size: 12px;
    }

    ul.list-group a:hover {
        color: #fff;
    }

    html.dark-theme .list-group-item:hover {
        background-color: #008cff;
    }

    html.light-theme .list-group-item:hover {
        background-color: #008cff;
    }

    .searchContainer {
        position: relative;
        display: inline-block;
    }

    .userAccount {
        position: absolute;
        z-index: 999;
        max-height: 150px;
        width: 100%;
        overflow-y: auto;
    }

    .userAccountForm {
        display: none;
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
                    <h5 class="card-title text-primary">Add New User Accounts</h5>
                </div>
                <hr>
                <form id="userAccountForm" autocomplete="off">
                    <input type="hidden" name="emp_id" readonly>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="row">
                                <div class="col-md-12 mb-2 searchContainer">
                                    <label class="form-label text-secondary">Search Promo</label>
                                    <div class="input-group font-22">
                                        <i class="input-group-text fadeIn animated lni lni-users"></i>
                                        <input type="text" class="form-control" name="userAccount" placeholder=" Search here..." onkeyup="searhPromo(this.value, this.name)">
                                    </div>
                                    <div class="dropdown-list userAccount"></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-secondary">User Type</label>
                                    <input type="text" class="form-control readonly" name="usertype" value="employee" readonly style="text-transform: uppercase;">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-secondary">Username</label>
                                    <input type="text" class="form-control readonly" name="username" readonly>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label text-secondary">Password</label>
                                    <input type="password" class="form-control" name="password">
                                </div>

                                <div class="col-sm-12 d-md-flex d-grid align-items-center gap-3 justify-content-left">
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