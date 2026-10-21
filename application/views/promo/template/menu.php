<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="<?= base_url('assets/promo_assets/images/cart2.png') ?>" class="logo-icon" alt="logo icon" />
        </div>
        <?php
        $homeLink = base_url('promo');
        $cond = array('emp_id' => $this->userId, 'usertype' => 'promo3');
        $chk = $this->promo_model->selectAll_tcR('promo_user', $cond);
        if ($chk) {
            $homeLink = 'javascript:;';
        }
        ?>
        <div>
            <a href="<?= $homeLink; ?>">
                <img src="<?= base_url('assets/promo_assets/images/promo.png') ?>" class="logo-icon2 logo-text" alt="logo icon" width="150px" style="margin-bottom: 0; margin-left:0;" />
            </a>
        </div>
        <div class="toggle-icon ms-auto">
            <i class='bx bx-menu'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li class="menu-label">Main Navigation </li>
        <li>
            <a href="<?= base_url('promo'); ?>">
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">Dashboard </div>
            </a>
        </li>
        <?php

        $m = [];
        if ($access['menu_access']) {
            $m = explode('|', $access['menu_access']);
        }

        $s = [];
        if ($access['submenu_access']) {
            $s = explode('|', $access['submenu_access']);
        }

        $main_menu = $this->promo_model->promo_menu();
        foreach ($main_menu as $key => $menuVal) {
            $mId = $menuVal['menu_id'];

            if (in_array($mId, $m)) {
                echo '<li>';
                echo    '<a href="javascript:;" class="has-arrow">
                            <div class="parent-icon"><i class="' . $menuVal['icon'] . '"></i></div>
                            <div class="menu-title">' . $menuVal['menu_name'] . ' </div>
                        </a>';
                echo    '<ul>';
                $sub_menu = $this->promo_model->promo_submenu($menuVal['menu_id']);
                foreach ($sub_menu as $key => $value) {
                    $sId = $value['submenu_id'];
                    if (in_array($sId, $s)) {
                        echo '<li>
                                <a href="' . base_url('promo/page/' . $menuVal['routeFolder']) . '/' . $value['routeFile'] . '">
                                    <i class="bx bx-radio-circle"></i>' . $value['submenu_name'] . '
                                </a>';
                        echo '</li>';
                    }
                }
                echo    '</ul>';
                echo '</li>';
            }
        } ?>

        <li>
            <a href="<?= base_url('promo/page/dashboard/aboutUs'); ?>">
                <div class="parent-icon"><i class="lni lni-information"></i>
                </div>
                <div class="menu-title">About Us </div>
            </a>
        </li>
        <li>
            <a href="<?= base_url('assets/promo_assets/images/userguide.pdf'); ?>" target="_blank">
                <div class="parent-icon"><i class="bx bx-map-alt"></i>
                </div>
                <div class="menu-title">User Guide </div>
            </a>
        </li>
    </ul>
    <!--end navigation-->
</div>
<!--end sidebar wrapper -->