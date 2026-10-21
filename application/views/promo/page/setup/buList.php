<style>
    .readonly[readonly] {
        background-color: #e9ecef;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-0">
                    <h4 class="card-title text-primary">Business Unit List</h4>
                    <span>
                        <button type="button" class="btn btn-sm btn-primary px-2" data-bs-toggle="modal" data-bs-target="#addFormBu">Add Business Unit</button>
                    </span>
                </div>
                <hr class="mt-2">
                <div class="table-responsive">
                    <table id="buListTable" class="table table-sm table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Business Unit Name</th>
                                <th>Status</th>
                                <th>TK Status</th>
                                <th>Appraisal Status</th>
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


<div class="modal fade" id="addFormBu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Business Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saveBuForm" autocomplete="off">
                <input type="hidden" name="process" value="add">
                <div class="modal-body addFormBu">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Business Unit</label>
                                <input type="text" name="business_unit" class="form-control" style="text-transform: uppercase;">
                            </div>
                        </div>
                        <div class=" col-md-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Business Unit Name</label>
                                <input type="text" name="bunit_name" class="form-control" style="text-transform: uppercase;">
                            </div>
                        </div>
                        <div class=" col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">Acronym</label>
                                <input type="text" name="bunit_acronym" class="form-control" style="text-transform: uppercase;">
                            </div>
                        </div>
                        <div class=" col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">Column Field</label>
                                <input type="text" name="bunit_field" class="form-control" style="text-transform: lowercase;">
                            </div>
                        </div>
                        <div class=" col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">Location</label>
                                <select class="form-control" name="hrd_location">
                                    <option value="">---</option>
                                    <option value="asc">asc</option>
                                    <option value="cebo">cebo</option>
                                    <option value="nesco">nesco</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary addFormButton">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Business Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body updateForm">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="update" onclick="saveBu(this.id)">Update</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>