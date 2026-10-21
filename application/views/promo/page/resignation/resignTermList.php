<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-primary">Resignation/Termination List</h5>
                </div>
                <hr>
                <div class="row">
                    <div class="table-responsive">
                        <table id="resignationList" class="table table-sm table-hover align-middle" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Effectivity</th>
                                    <th>Added By</th>
                                    <th>Date Added</th>
                                    <th>Remarks</th>
                                    <th>Letter</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="showLetter" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resignation Letter </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body showLetter"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadLetter" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Resignation Letter </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadResignationLetter" autocomplete="off">
                <div class="modal-body uploadLetter"></div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>