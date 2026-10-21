<style>
    .required_alert {
        width: 300px;
        font-size: 12px;
    }

    .slider {
        overflow: auto;
        max-height: 400px;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex mb-0">
                    <h5 class="card-title text-primary">New Employees</h5>
                    <?php

                    $lastmonth = date('F d, Y', strtotime('-1 month'));
                    $date = date('F d, Y');
                    ?>
                    <span style="padding-top: 3px;">
                        &nbsp;(tagged for the last 1 month from <?= '<b>' . $lastmonth . '</b> to <b>' . $date . '</b>'; ?>)
                    </span>
                </div>
                <hr class="mt-2">
                <div class="row">
                    <table id="newPromo" class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>EmpType</th>
                                <th>Store(s)</th>
                                <th>Department</th>
                                <th>Startdate</th>
                                <th>EOCdate</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->