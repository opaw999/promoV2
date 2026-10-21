<style>
    p.ipPhones {
        font-size: 12px;
    }
</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">

        <div class="row">
            <h3 class="card-title text-center text-primary mt-3">ABOUT US</h3>
            <hr>
            <div class="col-md-12">
                <blockquote class="blockquote text-center text-dark">
                    <p>
                        <strong>HRMS</strong> is an online system used by the HRD staff in managing the records of all employees.
                        One significant module presented is the Promo Module which comprises namely: Blacklisted Employee's, Contract,
                        Outlet, Promo Employee's Masterfile, Reports, Resignation/Termination, Setups, User Accounts, and Utilities.
                    </p>
                </blockquote>
            </div>
        </div>
        <div class="row mt-4">
            <h5 class="card-title text-secondary"><i>Contact Us</i></h5>
            <hr>
            <div class="row">
                <div class="col-12 col-lg-6 d-flex">
                    <div class="row">
                        <div class="col-12 col-lg-12 d-flex py-1">
                            <div class="card w-100 mb-5 mb-lg-0">
                                <div class="card-header bg-danger py-3">
                                    <h5 class="card-title text-white text-uppercase text-center">HEAD OFFICE</h5>
                                </div>
                                <div class="card-body text-center">

                                    <p>AGC Corporate Center, North Wing ICM Bldg., Dampas District, Tagbilaran City, Bohol</p>

                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-12 d-flex py-1">
                            <div class="card w-100 mb-5 mb-lg-0">
                                <div class="card-header bg-warning py-3">
                                    <h5 class="card-title text-white text-uppercase text-center">EMAIL</h5>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <p>
                                            <a href="mailto:itsysdev@alturasbohol.com">itsysdev@alturasbohol.com</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 d-flex py-1">
                    <div class="card w-100 mb-5 mb-lg-0">
                        <div class="card-header bg-primary py-3">
                            <h5 class="card-title text-white text-uppercase text-center">IP PHONES</h5>
                        </div>
                        <div class="card-body">
                            <p class="ipPhones">
                                1844 - HRMS, TIMEKEEPING, CMS, ALTURUSH, VMS <br>
                                1819 - WMS, MMS, VRS <br>
                                1821 - EBM, GO <br>
                                1847 - FARMS, GLASS SERVICE, AGRI, CWO, INSTITUTIONAL, ATP <br>
                                1822 - PMS/LEASING, BR, HMO, TMS <br>
                                1825 - ARIS, MYNET, EXTRACT, PCOUNT, MEDPLUS COUPONING, PAYPARKING <br>
                                1826 - RMS <br>
                                1953 - GC <br>
                                1951 - FAD, HRMS CLINIC, TSMS <br>
                                1815/1801 - NAVISION
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <h5 class="card-title text-secondary"><i>Our Team</i></h5>
            <hr>
            <div class="row">

                <?php
                $team = array(
                    'head'          => '43864-2013',
                    'supervisor'    => '21114-2013',
                    'current_sa'    => '02897-2023',
                    'current_prog'  => '01186-2023',
                    'previous_sa'   => '01476-2015',
                    'previous_prog' => '06359-2013'
                );
                $i = 0;
                foreach ($team as $key => $emp_id) {
                    $i++;
                    $app    = $this->promo_model->selectAll_tcR('applicant', array('app_id' => $emp_id));
                    $emp3   = $this->promo_model->selectAll_tcR('employee3', array('emp_id' => $emp_id));
                    $photo  = base_url('assets/promo_assets/images/profile.png');
                    if ($app['photo'] != '') {
                        $url   = FCPATH . $app['photo'];
                        $check = @file_get_contents($url);
                        if ($check !== false) {
                            $photo = base_url($app['photo']);
                        }
                    }

                    $name = ucwords(strtolower($emp3['name']));

                    if ($key == 'current_sa' || $key == 'current_prog' || $key == 'supervisor') {
                        $status = '<span class="badge bg-success px-3">Current</span>';
                    } else {
                        $status = '<span class="badge bg-secondary px-3">Previous</span>';
                    }
                    $col        = 'col-md-6';
                    $position   = '<p class="text-secondary mb-1">' . $emp3['position'] . '</p>';
                    if ($key == 'head') {
                        $col        = 'col-md-12';
                        $status     = '<p class="text-secondary mb-1">Corporate Audit Group Managing Director</p>';
                        $position   = '<p class="text-secondary mb-1">CPA, CIA, CSCU, CISA, REB, REA, CICA, CrFA, REC</p>';
                    } else if ($key == 'supervisor') {
                        $col = 'col-md-12';
                    }

                    echo '<div class="' . $col . ' mt-3 mb-3">';
                    echo    '<div class="d-flex flex-column align-items-center text-center">
                                <img src="' . $photo  . '" alt="..." class="rounded-circle shadow p-1 bg-white" width="150" height="150">
                                <div class="mt-3">
                                     <h5 style="margin-bottom: 0px;">' . $name . '</h5>
                                     ' . $position . '
                                     ' . $status . '
                                </div>
                            </div>';
                    echo '</div>';
                }
                ?>

            </div>
        </div>
    </div>
    <!--end page wrapper -->