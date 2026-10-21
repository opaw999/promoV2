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
                <form action="<?php echo base_url('promo/reports/generatePromoStat') ?>" method="POST" autocomplete="off" target="_blank" onSubmit="return generatePromoStat()">
                    <h5 class="card-title text-primary">Promo Statistics Report</h5>
                    <hr class="mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="text-secondary mb-1 mt-2">Store(s)</label>
                                    <select name="stores[]" class="form-select select2" id="storeSelect" multiple data-placeholder="Select Store(s)">
                                        <option value="AllBu">All Stores</option>
                                        <?php
                                        $cond = array('hrd_location' => $this->hrCode, 'tk_status' => 'active', 'appraisal_status' => 'active');
                                        $query = $this->promo_model->selectAll_tcA('locate_promo_business_unit', $cond);
                                        foreach ($query as $key => $bu) {
                                            echo '<option value="' . $bu['bunit_field'] . '">' . $bu['bunit_name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="text-secondary mb-1 mt-2">Prepared By</label>
                                    <input type="text" name="preparedBy" class="form-control" style="text-transform: capitalize;">
                                </div>
                                <div class="col-md-12"></div>
                                <div class="col-md-12">
                                    <label class="text-secondary mb-1 mt-2">Submitted To</label>
                                    <input type="text" name="submittedTo" class="form-control" style="text-transform: capitalize;">
                                </div>
                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary pull-right">Generate in Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end page wrapper -->