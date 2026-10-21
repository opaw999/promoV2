<style>
    #logsSearch {
        font-size: 12px;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-0">
                    <h5 class="card-title text-primary">Logs Search</h5>
                    <span>
                        <div class="input-group input-group-sm">
                            <input type="text" name="logsSearch" class="form-control form-control-sm">
                            <button class="btn btn-primary" type="button" onclick="logsSearch()">Search</button>
                        </div>
                    </span>
                </div>
                <hr>
                <table id="logsSearch" class="table table-hover table-sm" style="width:100%">
                    <thead>
                        <tr>
                            <th width="5%">LogNo</th>
                            <th>Activity</th>
                            <th width="5%">Date</th>
                            <th width="8%">Time</th>
                            <th width="8%" class="text-center">User</th>
                            <th width="10%" class="text-center">Username</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->