<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-primary">Company (Products)</h5>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="productCompanyTable" class="table table-sm table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Tagged Product(s)</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="compProducts" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title compName"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body tagProducts"></div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div class="d-md-flex d-grid align-items-center gap-1">
                    <span class="badge text-secondary"><b>LEGEND:</b></span>
                    <i class="bx bx-purchase-tag-alt font-18 text-primary"></i><span class="badge text-secondary">Tag Company</span>
                    <i class="bx bx-x-circle font-18 text-danger"></i><span class="badge text-secondary">Untag Company</span>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>