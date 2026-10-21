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
                    <h5 class="card-title text-primary">Add Promo Incharge Account</h5>
                </div>
                <hr>
                <form id="inchargeAccountForm" autocomplete="off">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="row">
                                <input type="hidden" name="process" value="inchargeAccount">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label text-secondary">Search HR Staff</label>
                                    <input type="search" class="form-control" id="searchInput" name="name" placeholder="Search here..." onkeyup="searchPromoUsers(this.value)" autocomplete="off">
                                    <div class="dropdown-list"></div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <label class="form-label text-secondary">Employee ID</label>
                                    <input type="text" class="form-control readonly" name="emp_id" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-secondary">User Type</label>
                                    <select name="usertype" class="form-select">
                                        <option value="">Select User Type</option>
                                        <option value="promo1">Promo Incharge</option>
                                        <option value="promo2">Encoder</option>
                                        <option value="promo3">Guest</option>
                                        <?php
                                        $ut = $this->promo_model->get_usertype();
                                        if ($ut == 'administrator') {
                                            echo '<option value="administrator">Administrator</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-secondary">User Status</label>
                                    <select name="user_status" class="form-select">
                                        <option value="">Select User Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">InActive</option>
                                    </select>
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