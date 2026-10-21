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
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-primary">Export Promo Data (Upload to Portal Admin Contracts)</h5>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="hidden" name="process" value="exportContract">
                        <label class="form-label">Search Employee</label>
                        <input type="search" class="form-control" name="name" onkeyup="searchPromoExport(this.value)" placeholder="Search here..." autocomplete="off">
                        <div class="dropdown-list"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 contracts"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->