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
                <div class="">
                    <h5 class="card-title text-primary">Birthdays Today (<?= date('F j, Y') ?>)</h5>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <table id="bdayToday" class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Gender</th>
                                    <th>Birthdate</th>
                                    <th>Store(s)</th>
                                    <th>Department</th>
                                    <th>PromoType</th>
                                    <th>Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $md     = date('m-d');
                                $query  = $this->promo_model->birthdayToday($md);
                                foreach ($query as $row) {

                                    $bUs = $this->promo_model->locate_promo_bu($this->hrCode);
                                    $i = 0;
                                    $stores = '';
                                    foreach ($bUs as $bu) {
                                        $hasBu = $this->promo_model->empStores('promo_record', $row['emp_id'], $row['record_no'], $bu['bunit_field']);
                                        if ($hasBu > 0) {
                                            $i++;
                                            $stores = ($i == 1) ? $bu['bunit_acronym'] : $stores . ', ' . $bu['bunit_acronym'];
                                        }
                                    }
                                    $name = '<a href="' . base_url('promo/page/promo/profile/' . $row['emp_id']) . '" target="_blank">' . ucwords(strtolower($row['name'])) . '</a>';
                                    echo    '<tr>
                                                <td>' . $name . '</td>
                                                <td>' . $row['gender'] . '</td>
                                                <td>' . date('m/d/Y', strtotime($row['birthdate'])) . '</td>
                                                <td>' . $stores . '</td>
                                                <td>' . $row['promo_department'] . '</td>
                                                <td>' . $row['promo_type'] . '</td>
                                                <td>' . $row['position'] . '</td>
                                            </tr>';
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