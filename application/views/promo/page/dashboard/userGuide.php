<style>

</style>
<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <div class="card radius-10">
            <div class="card-body">
                <h5 class="card-title">User Guide</h5>
                <hr>
                <div class="row">
                    <div class="col">
                        <?php
                        $company_empId = "";
                        $con_empId = "";
                        $promoEmpId = "";
                        $emp = '00001-2022|00001-2021';
                        if (!empty($emp)) {
                            $convert = explode("|", $emp);

                            for ($x = 0; $x < count($convert); $x++) {
                                if ($x == 0) {
                                    $company_empId .= " WHERE (( empId = '$convert[$x]' )";
                                    $promoEmpId .= " AND (( employee3.emp_id = '$convert[$x]' )";
                                }
                                if ($x != 0) {
                                    $company_empId .= " OR ( empId = '$convert[$x]' )";
                                    $promoEmpId .= " OR ( employee3.emp_id = '$convert[$x]' )";
                                }
                            }
                            $company_empId .= ")";
                            $promoEmpId .= ")";
                        }
                        echo $promoEmpId;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->