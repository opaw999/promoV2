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

    .notes {
        color: #fd3550;
        font-size: .875em;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-primary">Add Outlet</h5>
                </div>
                <hr>
                <form id="addOutletForm" autocomplete="off">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="hidden" name="process" value="addOutlet">
                            <label class="form-label">Search Employee <font class="notes">(<i>Active employees only.</i>)</font>
                            </label>
                            <input type="search" class="form-control" name="name" onkeyup="searchPromoOutlet(this.value)" placeholder="Search here..." autocomplete="off">
                            <div class="dropdown-list"></div>
                            <div class="promoDetails"> </div>
                        </div>
                        <div class="col-md-8 addOutlet"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->