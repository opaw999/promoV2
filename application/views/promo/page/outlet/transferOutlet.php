<style>
    /* .required_alert {
        width: 300px;
        font-size: 12px;
    }

    ul.list-group a:hover {
        color: #fff;
    }

    html.dark-theme a.list-group-item:hover {
        background-color: #6c757d;
    }

    html.light-theme a.list-group-item:hover {
        background-color: #008cff;
    }

    .searchContainer {
        position: relative;
        display: inline-block;
    }

    .transferOutlet {
        position: absolute;
        z-index: 999;
        max-height: 150px;
        width: 100%;
        overflow-y: auto;
    }

    .transferOutletForm {
        display: none;
    } */

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
                    <h5 class="card-title text-primary">Transfer Outlet</h5>
                </div>
                <hr>
                <form id="transferOutletForm" autocomplete="off">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="hidden" name="process" value="transferOutlet">
                            <label class="form-label">Search Employee &nbsp;<font class="notes">(<i>Active employees only.</i>)</font></label>
                            <input type="search" class="form-control" name="name" onkeyup="searchPromoOutlet(this.value)" placeholder="Search here..." autocomplete="off">
                            <div class="dropdown-list"></div>
                            <div class="promoDetails"> </div>
                        </div>
                        <div class="col-md-8 transferOutlet"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="viewAppraisal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appraisal Details </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewAppraisal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary print" onclick="printEpas(this.id)">Print</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>