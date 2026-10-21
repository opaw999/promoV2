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
                    <h5 class="card-title text-primary">Server Update</h5>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <label class="form-label text-secondary">Select Server</label>
                        <select name="recProcess" class="form-select" onchange="getSeverData(this.value)">
                            <option value="">...</option>
                            <option value="al_tal">ALTURAS TALIBON</option>
                            <option value="al_tub">ALTURAS TUBIGON</option>
                        </select>
                    </div>
                </div>
                <div class="row serverData"></div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->