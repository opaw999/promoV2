<style>
    .required_alert {
        width: 300px;
        font-size: 12px;
    }

    .slider {
        overflow: auto;
        max-height: 400px;
    }

    /* th,
    td {
        font-size: 12px;
    } */
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <div class="">
                    <h5 class="card-title text-primary">Statistics Report</h5>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-sm table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th rowspan="2">Business Unit</th>
                                    <th rowspan="2">Department</th>
                                    <th colspan="2" class="text-center">Employee Type</th>
                                    <th rowspan="2" class="text-center">Total</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Promo</th>
                                    <th class="text-center">Promo-NESCO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $bUs = $this->promo_model->locate_promo_bu($this->hrCode);
                                foreach ($bUs as $bu) {
                                    $select     = 'DISTINCT(t1.emp_id)';
                                    $table1     = 'employee3';
                                    $table2     = 'promo_record';
                                    $join       = 't1.emp_id=t2.emp_id AND t1.record_no=t2.record_no';
                                    $bu_field   = $bu['bunit_field'];
                                    $c1         = "emp_type = 'Promo' AND $bu_field = 'T' AND current_status = 'Active' AND hr_location = '$this->hrCode'";
                                    $c2         = "emp_type = 'Promo-NESCO' AND $bu_field = 'T' AND current_status = 'Active' AND hr_location = '$this->hrCode'";
                                    $promoT     = $this->promo_model->join_ttjcoA($select, $table1, $table2, $join, null, $c1, null, null);
                                    $promoNesT  = $this->promo_model->join_ttjcoA($select, $table1, $table2, $join, null, $c2, null, null);

                                    $c = array('bunit_id' => $bu['bunit_id'], 'status' => 'active');
                                    $dept = $this->promo_model->selectAll_tcA('locate_promo_department', $c);
                                    if (count($promoT) > 0) {
                                        $pT =   '<a href="javascript:;" onclick="promoStat(\'Promo\', \'allDept\', \''  . $bu_field . '\')">
                                                    <span class="badge rounded-pill bg-success">' . count($promoT) . '</span>
                                                </a>';
                                    } else {
                                        $pT = '<span class="badge rounded-pill bg-success">' . count($promoT) . '</span>';
                                    }
                                    if (count($promoNesT) > 0) {
                                        $pnT =  '<a href="javascript:;" onclick="promoStat(\'Promo-NESCO\', \'allDept\', \''  . $bu_field . '\')">
                                                    <span class="badge rounded-pill bg-success">' . count($promoNesT) . '</span>
                                                </a>';
                                    } else {
                                        $pnT = '<span class="badge rounded-pill bg-success">' . count($promoNesT) . '</span>';
                                    }
                                    if ((count($promoT) + count($promoNesT)) > 0) {
                                        $tT =  '<a href="javascript:;" onclick="promoStat(\'allPromo\', \'allDept\', \''  . $bu_field . '\')">
                                                     <span class="badge rounded-pill bg-success">' . (count($promoT) + count($promoNesT)) . '</span>
                                                 </a>';
                                    } else {
                                        $tT = '<span class="badge rounded-pill bg-success">' . (count($promoT) + count($promoNesT)) . '</span>';
                                    }
                                    echo    '<tr>
                                                <td rowspan="' . (count($dept) + 1) . '">' . $bu['bunit_name'] . '</td>
                                                <td></td>
                                                <td class="text-center">' . $pT . '</td>
                                                <td class="text-center">' . $pnT . '</td>
                                                <td class="text-center">' . $tT . '</td>
                                            </tr>';

                                    foreach ($dept as $dep) {

                                        $dep_name   = $dep['dept_name'];
                                        $c1         = "emp_type = 'Promo' AND $bu_field = 'T' AND promo_department = '$dep_name' AND current_status = 'Active' AND hr_location = '$this->hrCode'";
                                        $c2         = "emp_type = 'Promo-NESCO' AND $bu_field = 'T' AND promo_department = '$dep_name' AND current_status = 'Active' AND hr_location = '$this->hrCode'";
                                        $promo      = $this->promo_model->join_ttjcoA($select, $table1, $table2, $join, null, $c1, null, null);
                                        $promoNes   = $this->promo_model->join_ttjcoA($select, $table1, $table2, $join, null, $c2, null, null);
                                        if (count($promo) > 0) {
                                            $p =   '<a href="javascript:;" onclick="promoStat(\'Promo\', \'' . $dep_name . '\', \''  . $bu_field . '\')" class="text-primary">
                                                        ' . count($promo) . '
                                                    </a>';
                                        } else {
                                            $p = count($promo);
                                        }
                                        if (count($promoNes) > 0) {
                                            $pn =   '<a href="javascript:;" onclick="promoStat(\'Promo-NESCO\', \'' . $dep_name . '\', \''  . $bu_field . '\')" class="text-primary">
                                                        ' . count($promoNes) . '
                                                    </a>';
                                        } else {
                                            $pn = count($promoNes);
                                        }
                                        if ((count($promo) + count($promoNes)) > 0) {
                                            $t =    '<a href="javascript:;" onclick="promoStat(\'allPromo\', \'' . $dep_name . '\', \''  . $bu_field . '\')" class="text-primary">
                                                        ' . (count($promo) + count($promoNes)) . '
                                                    </a>';
                                        } else {
                                            $t = (count($promo) + count($promoNes));
                                        }
                                        echo    '<tr>
                                                    <td>' . $dep_name . '</td>
                                                    <td class="text-center">' . $p . '</td>
                                                    <td class="text-center">' . $pn . '</td>
                                                    <td class="text-center">' . $t . '</td>
                                                </tr>';
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->

    <div class="modal fade" id="promoStat" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Statistics Summary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="promoStatData"></div>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Generate Report</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div> -->
            </div>
        </div>
    </div>