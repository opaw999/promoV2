<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-0">
                    <h5 class="card-title text-primary">Lists of Secured Clearance</h5>
                    <span>
                        <span>Filter Month/Year:</span>
                        <span>
                            <select name="month" onchange="filter_month(this.value)"></select>
                        </span>
                        <span>
                            <select name="year" onchange="filter_year(this.value)">
                                <?php
                                $year = isset($_GET['year']) ? $_GET['year'] :  date('Y');
                                $table = 'secure_clearance_promo';
                                $selected = 'YEAR(date_added) AS years';
                                $cond = "YEAR(date_added)!='0' ORDER BY years DESC";
                                $rowY = $this->promo_model->selectDistinct($selected, $table, $cond);
                                foreach ($rowY as $key => $y) {
                                    $sel = ($year == $y['years']) ? 'selected' : '';
                                    echo '<option value="' . $y['years'] . '"' . $sel . '>' . $y['years'] . '</option>';
                                }
                                ?>
                            </select>
                        </span>
                    </span>
                </div>
                <hr class="mt-2">
                <div class="table-responsive">
                    <table id="clearance" class="table table-sm table-hover align-middle" style="width:100%">
                        <thead>
                            <tr>
                                <th class="name">Name</th>
                                <th>Secured</th>
                                <th>Effectivity</th>
                                <th>Current Status</th>
                                <th>Clearance Status</th>
                                <th>Promo Type</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="clearanceDetails" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clearance Details... </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="clearanceDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="clearanceUpdateForm" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clearance Update... </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateClearance" autocomplete="off">
                <div class="modal-body">
                    <div class="clearanceUpdateForm"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary updateClearanceButton" disabled>Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="showLetter" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resignation Letter </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="showLetter"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewAppraisal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appraisal Details </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="viewAppraisal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>