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
                <form id="dueContractsForm" method="POST" target="_blank">
                    <h5 class="card-title text-primary">Due Contracts Report</h5>
                    <hr class="mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="text-secondary mb-1">Store(s)</label>
                            <select name="store" class="form-select select2" onchange="getDepartment(this.value)" data-placeholder="Select Store">
                                <option value="">Select Store</option>
                                <option value="allbu">All Store(s)</option>
                                <?php
                                $query = $this->promo_model->locate_promo_bu($this->hrCode);
                                foreach ($query as $key => $bu) {
                                    echo '<option value="' . $bu['bunit_id'] . '|' . $bu['bunit_field'] . '">' . $bu['bunit_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="text-secondary mb-1">Department</label>
                            <select name="promo_department" class="form-select select2" data-placeholder="Select Department">
                                <option value="">Select Department</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="button" class="btn btn-primary pull-right" onclick="generateDueContracts('excel')">Generate in Excel</button>
                            <button type="button" class="btn btn-primary pull-right me-2" onclick="generateDueContracts('pdf')">Generate in PDF</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end page wrapper -->