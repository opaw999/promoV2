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
                    <h5 class="card-title text-primary">Printing of Contract</h5>
                </div>
                <hr>
                <form id="generateContract" autocomplete="off">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="hidden" name="process" value="printContract">
                            <label class="form-label">Search Employee</label>
                            <input type="search" class="form-control" name="name" onkeyup="searchPromoContract(this.value)" placeholder="Search here..." autocomplete="off">
                            <div class="dropdown-list"></div>
                        </div>
                    </div>
                    <div class="row contracts"> </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="generateContractForm" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Contract Form...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="generateContract" autocomplete="off">
                <div class="modal-body generateContractForm"></div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Generate Contract</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>