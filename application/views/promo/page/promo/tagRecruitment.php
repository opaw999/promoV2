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
                    <h5 class="card-title text-primary">Tag to Recruitment</h5>
                </div>
                <hr>
                <form id="tagtoRecruitmentForm" autocomplete="off">
                    <input type="hidden" name="process" value="tagtoRecruitment">
                    <input type="hidden" class="form-control" name="emp_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label for="input1" class="form-label">Search Applicant</label>
                                    <input type="search" class="form-control" name="name" placeholder="Search here..." onkeyup="searchTagtoRecruitment(this.value)">
                                    <div class="dropdown-list"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-secondary">Status</label>
                                    <input type="text" class="form-control" name="status" disabled>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label class="form-label text-secondary">Current Status</label>
                                    <input type="text" class="form-control" name="current_status" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label text-secondary">Recruitment Process</label>
                                    <select name="recProcess" class="form-select" disabled>
                                        <option value="">Select Process</option>
                                        <option value="initial_completion"> Initial Completion </option>
                                        <option value="exam"> For Examination </option>
                                        <option value="interview"> For Interview </option>
                                        <option value="training"> For Training </option>
                                        <option value="final_completion"> For Final Completion </option>
                                        <option value="orientation"> For Orientation </option>
                                        <option value="hiring"> For Hiring </option>
                                        <!-- <option value="deployment"> For Deployment </option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <label class="form-label text-secondary">Position</label>
                                    <select name="position" class="form-select select2" data-placeholder="Select Position" disabled>
                                        <option value="">Select Position</option>
                                        <?php
                                        $c = "tag = 'active' OR tag = ''";
                                        $position = $this->promo_model->selectAll_tcA('positions', $c);
                                        foreach ($position as $value) {
                                            echo '<option value="' . $value['position'] . '">' . $value['position'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <!-- <div class="pull-right"> -->
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <!-- </div> -->
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->