<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap mt-0">
                    <h5 class="card-title text-primary">Department List</h5>
                    <span>
                        <button type="button" class="btn btn-sm btn-primary px-2" data-bs-toggle="modal" data-bs-target="#addForm">Add Department</button>
                    </span>
                </div>
                <hr class="mt-2">
                <div class="table-responsive">
                    <table id="departmentListTable" class="table table-sm table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Tagged Store(s)</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->

<div class="modal fade" id="addForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add New Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Department Name</label>
                    <input type="text" class="form-control" name="dept_name" autocomplete="off" style="text-transform: uppercase;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="add" onclick="saveDepartment(this.id)">Submit</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deptStores" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title deptName"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body tagStores"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>