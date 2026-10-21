<style>
    #logsAdmin {
        font-size: 12px;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-0">
                    <h5 class="card-title text-primary">Logs (Admin)</h5>
                    <span>
                        <div class="input-group input-group-sm">
                            <input type="text" name="day" onchange="logsAdmin(this.value)" value="<?= date('Y-m-d') ?>" class="form-control form-control-sm datepicker">
                            <button class="btn btn-secondary" type="button" disabled>Filter Date</button>
                        </div>
                    </span>
                </div>
                <hr>
                <table id="logsAdmin" class="table table-hover table-sm" style="width:100%">
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