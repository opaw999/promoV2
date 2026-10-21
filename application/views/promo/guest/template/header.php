<!doctype html>

<html lang="en">

<head>
    <!-- Original URL: https://codervent.com/rocker/demo/vertical/index.html
		Date Downloaded: 9/14/2023 3:49:51 PM !-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!--favicon-->
    <link rel="icon" href="<?= base_url('assets/promo_assets/images/cart2.png') ?>" type="image/png" />
    <!--plugins-->
    <link href="<?= base_url('assets/promo_assets/plugins/simplebar/css/simplebar.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/plugins/metismenu/css/metisMenu.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/plugins/datatable/css/dataTables.bootstrap5.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/css/flatpickr.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/plugins/sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet" />
    <!-- loader-->
    <link href="<?= base_url('assets/promo_assets/css/pace.min.css') ?>" rel="stylesheet" />
    <script src="<?= base_url('assets/promo_assets/js/pace.min.js') ?>"></script>
    <!-- Bootstrap CSS -->
    <link href="<?= base_url('assets/promo_assets/css/bootstrap.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/css/bootstrap-extended.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/css/css2_864ea98.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/css/app.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/css/icons.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/boxicons-master/css/animations.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/boxicons-master/css/boxicons.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/boxicons-master/css/boxicons.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/boxicons-master/css/transformations.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/font-awesome/css/font-awesome.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/plugins/select2/css/select2.min.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('assets/promo_assets/plugins/select2/css/select2-bootstrap-5-theme.min.css') ?>" rel="stylesheet" />
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/promo_assets/css/dark-theme.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/promo_assets/css/semi-dark.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/promo_assets/css/header-colors.css') ?>" />
    <title>PROMO Module | HRMS </title>
</head>

<style>
    html.dark-theme .logo-icon2 {
        filter: invert(1) grayscale(100%) brightness(200%);
    }
</style>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--start header -->
        <header>
            <div class="topbar d-flex align-items-center">
                <nav class="navbar navbar-expand gap-3">
                    <div class="mobile-toggle-menu">
                        <i class='bx bx-menu'></i>
                    </div>
                    <div class="col-md-4">
                        <div class="position-relative input-icon">
                            <input type="text" class="form-control" name="searchPromo" placeholder="Search Promo Employee here..." autocomplete="off">
                            <span class="position-absolute top-50 translate-middle-y d-flex"><i class="bx bx-search"></i></span>
                        </div>
                    </div>
                    <div class="top-menu ms-auto">
                        <ul class="navbar-nav align-items-center gap-1">
                            <?php
                            // $cond   = array('empId' => $user['emp_id'], 'status' => 'active');
                            // $chk    = $this->promo_model->selectAll_tcR_tk('promo_hr_start', $cond);
                            // if ($chk) {
                            //     $link = 'http://' . $_SERVER['SERVER_ADDR'] . '/timekeeping/others/declare/index.php?id=' . $_SESSION['emp_id'] . '&&status=in&&type=hrAccount&&type2=' . $_SESSION['type'];
                            //     echo '<li class="nav-item d-none d-sm-flex"><a class="nav-link" style="width:auto;border-radius:6px;font-size:18px;" href="' . $link . '">HR Account</a></li>';
                            // }
                            ?>
                            <li class="nav-item d-none d-sm-flex"><a class="nav-link" style="width:auto;border-radius:6px;font-size:18px;" href="<?= base_url('../placement') ?>">Placement</a></li>
                            <li class="nav-item dark-mode d-none d-sm-flex"><a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i></a></li>
                        </ul>
                    </div>
                    <div class="user-box dropdown px-3">
                        <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret" href="javascript:;" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php
                            $photo = base_url('assets/promo_assets/images/profile.png');
                            if ($user['photo'] != '') {
                                $url   = FCPATH . $user['photo'];
                                $check = @file_get_contents($url);
                                if ($check !== false) {
                                    $photo = base_url($user['photo']);
                                }
                            }
                            ?>
                            <img src="<?= $photo ?>" class="user-img" alt="user avatar" />
                            <div class="user-info">
                                <p class="user-name mb-0"><?= ucwords(strtolower($user['name'])); ?></p>
                                <p class="designattion mb-0"><?= $user['position']; ?></p>
                            </div>
                        </a>
                        <!-- <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="<?= base_url('promo/page/user/userProfile'); ?>"><i class="bx bx-user fs-5"></i><span>User Profile </span></a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="<?= base_url('promo/logout'); ?>"><i class="bx bx-log-out-circle fs-5"></i><span>Logout </span></a>
                            </li>
                        </ul> -->
                    </div>
                </nav>
            </div>
        </header>
        <!--end header 