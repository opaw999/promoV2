<style>
    .required_alert {
        width: 300px;
        font-size: 12px;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-primary">Search Applicant</h5>
                </div>
                <hr>
                <div class="row mb-3 mx-auto">
                    <i class="text-danger">Note: Lastname should be in full. </i>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="lastname" placeholder="Lastname..." autocomplete="off">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="firstname" placeholder="Firstname..." autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-primary searchApplicantButton px-4" onclick="searchApplicant()">
                            <i class="bx bx-search-alt"></i>Search
                        </button>
                    </div>
                </div>
                <div class="row applicants"></div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->