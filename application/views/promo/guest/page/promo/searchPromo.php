<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div>
                    <h5 class="card-title text-primary">Search Results...</h5>
                </div>
                <hr>
                <div class="row">
                    <?php
                    echo '<table class="table table-sm table-hover" id="searchPromoTable">';
                    echo '<thead style="display: none;"><tr><th></th></tr></thead>';
                    echo '<tbody>';
                    $i      = 0;
                    $str    = $_GET['searchValue'];
                    $cond = array('hrd_location' => $this->hrCode);
                    $bus = $this->promo_model->selectAll_tcoA('locate_promo_business_unit', $cond, 'bunit_name');
                    $list   = $this->promo_model->searchPromo($str);
                    if ($list) {
                        foreach ($list as  $row) {
                            $i++;
                            $empId = $row['app_id'];
                            $photo = base_url('assets/promo_assets/images/profile.png');
                            if ($row['photo'] != '') {
                                $url   = FCPATH . $row['photo'];
                                $check = @file_get_contents($url);
                                if ($check !== false) {
                                    $photo = base_url($row['photo']);
                                }
                            }

                            $remarks = $row['remarks'];
                            $name   = $row['name'];
                            $bday   = date('F d, Y', strtotime($row['birthdate']));

                            $style = 'style="width: 100px;height: 100px;"';
                            $color = '';
                            if ($row['current_status'] == 'Active') {
                                $color = 'text-success';
                            } elseif ($row['current_status'] == 'Deceased') {
                                $color = 'text-secondary';
                                $style = 'style="width: 100px;height: 100px;filter: grayscale(100%);"';
                            } elseif ($row['current_status'] == 'blacklisted') {
                                $color = 'text-danger';
                            } else {
                                $color = 'text-warning';
                            }

                            if ($row['emp_type'] == 'Promo' || $row['emp_type'] == 'Promo-NESCO' || $row['emp_type'] == 'Promo-EasyL') {
                                $link = '<a href="' . base_url('guest/promo/profile/' . $empId) . '">' . $empId . ' ' . $name . '</a>';
                                $company = @$row['promo_company'];
                                $dept = @$row['promo_department'];
                                $pType = @$row['promo_type'];
                                $x = 0;
                                $store = '';
                                foreach ($bus as $bu) {
                                    $cond   = array('emp_id' => $row['emp_id'], $bu['bunit_field'] => 'T');
                                    $hasBu  = $this->promo_model->selectAll_tcR('promo_record', $cond);
                                    if ($hasBu) {
                                        $x++;
                                        $store = ($x == 1) ? $bu['bunit_acronym'] : $store . ', ' . $bu['bunit_acronym'];
                                    }
                                }
                            } else {
                                $link = '<a href="' . base_url('../placement/employee_details.php?com=' . $empId) . '">' . $empId . ' ' . $name . '</a>';
                                $pType = 'not Promo';
                                $cc = $row['company_code'];
                                $bc = $row['bunit_code'];
                                $dc = $row['dept_code'];
                                $sc = $row['section_code'];
                                $cond       = array('company_code' => $cc);
                                $company    = $this->promo_model->selectAll_tcR('locate_company', $cond)['company'];
                                $cond       = array('company_code' => $cc, 'bunit_code' => $bc);
                                $store      = $this->promo_model->selectAll_tcR('locate_business_unit', $cond)['business_unit'];
                                $cond       = array('company_code' => $cc, 'bunit_code' => $bc, 'dept_code' => $dc);
                                $dept       = $this->promo_model->selectAll_tcR('locate_department', $cond)['dept_name'];
                            }

                            echo '<tr>';
                            echo    '<td>';
                            echo    '<div class="d-flex justify-content-left align-items-center flex-wrap gap-3">';
                            echo    '<img class="border rounded" src="' . $photo . '" alt="" ' . $style . '>';
                            echo    '<span style="max-width: 85%;">';
                            echo    '<span class="text-danger">[' . $i . ']</span> ';
                            echo    '<strong>' . $link . '</strong>&nbsp;';
                            echo    '<i class="' . $color . '">' . $row['current_status'] . ' (' . $row['sub_status'] . ')</i>&nbsp;<br>';
                            echo    '<strong>Company: </strong><i>' . @$company . '</i> &nbsp;<br>';
                            echo    '<strong>Department: </strong><i>' . @$dept . '</i> &nbsp;';
                            echo    '<strong>Employee Type: </strong><i>' . $row['emp_type'] . '</i> &nbsp;';
                            echo    '<strong>Promo Type: </strong><i>' . @$pType . '</i> &nbsp;';
                            echo    '<strong>Store(s): </strong><i>' . @$store . '</i> &nbsp;<br>';
                            echo    '<strong>Gender: </strong><i>' . $row['gender'] . '</i> &nbsp;';
                            echo    '<strong>Birthdate: </strong><i>' . $bday . '</i> &nbsp;';
                            echo    '<strong>Home Address: </strong><i>' . $row['home_address'] . '</i><br>';
                            echo    '<strong>Remarks: </strong><i style="font-size:smaller;">' . $remarks . '</i> &nbsp;';
                            echo    '</span>';
                            echo    '</div>';
                            echo    '</td>';
                            echo '</tr>';
                        }
                    }
                    echo '</tbody>';
                    echo '</table>';
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end page wrapper -->