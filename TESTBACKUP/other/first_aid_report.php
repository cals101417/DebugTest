<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
?>
<div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
<?php
include 'includes/sidebar.php';
include 'includes/header.php';
?>
    <!-- Main Container -->
<?php
try {

    $generate_report_qry = $conn->query("SELECT  *,COUNT(*), Year(date_conducted) as itd_year,
                                        SUM(case when tbt_type='1' then time else 0 end) as total_hours_civils,
                                        SUM(case when tbt_type='2' then time else 0 end) as total_hours_electricals,
                                        SUM(case when tbt_type='3' then time else 0 end) as total_hours_mechanicals,
                                        SUM(case when tbt_type='4' then time else 0 end) as total_hours_camps,
                                        SUM(case when tbt_type='5' then time else 0 end) as total_hours_office,       
                                        COUNT(case when tbt_type='1' then time else null end) as total_days_civils,
                                        COUNT(case when tbt_type='2' then time else null end) as total_days_electricals,
                                        COUNT(case when tbt_type='3' then time else null end) as total_days_mechanicals,  
                                        COUNT(case when tbt_type='4' then time else null end) as total_days_camps,
                                        COUNT(case when tbt_type='5' then time else null end) as total_days_office,       
                                        COUNT('tbtp_id') as totals_days,
                                        SUM(time) as totals_hours
            
                                        FROM tbl_toolbox_talks 
                                        INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                        WHERE  tbl_toolbox_talks.is_deleted = 0
                                        GROUP BY date_format(date_conducted, '%Y') ORDER BY `date_conducted`");
    $get_result= $generate_report_qry->fetchAll();
    //   GET PREVIOUS YEAR OF THE SELECTED YEAR
//            $previous_year = explode("-",$range2);
//            $prev_year = ((strval($previous_year[0])-1).'-12-31 00:00:00');
    $get_report_by_year = $conn->query("SELECT  *,COUNT(*),Year(date_conducted) as year,
                                            SUM(case when tbt_type='1' then time else 0 end) as total_hours_civils,
                                            SUM(case when tbt_type='2' then time else 0 end) as total_hours_electrical,
                                            SUM(case when tbt_type='3' then time else 0 end) as total_hours_mechanical,
                                            SUM(case when tbt_type='4' then time else 0 end) as total_hours_camps,
                                            SUM(case when tbt_type='5' then time else 0 end) as total_hours_office,       
                                            COUNT(case when tbt_type='1' then time else null end) as total_days_civils,
                                            COUNT(case when tbt_type='2' then time else null end) as total_days_electricals,
                                            COUNT(case when tbt_type='3' then time else null end) as total_days_mechanicals,  
                                            COUNT(case when tbt_type='4' then time else null end) as total_days_camps,
                                            COUNT(case when tbt_type='5' then time else null end) as total_days_office,                                           
                                            COUNT('tbtp_id') as monthly_participant,  
                                            SUM(time) as monthly_hours            
                                            FROM tbl_toolbox_talks 
                                            INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id
                                            GROUP BY date_format(date_conducted, '%Y') ORDER BY `date_conducted` DESC");
    $get_result2= $get_report_by_year->fetchAll();
    $get_manual_records_qry =$conn->query("SELECT * FROM tbl_tds WHERE `is_deleted` = 0 ORDER BY 'itd_year' ASC");
    $get_manual_result = $get_manual_records_qry->fetchAll();
    $array_itds  = array_merge($get_manual_result , $get_result);
    $incident_type =  array("FAT","LTC", "RWC", "MTC", "FAC", "NM", "PD","TRAF","FIRE","ENV");
    $leading_indicators =  array("NM","FAC", "WSA", "STA", "STA");
    $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec");
    $root_causes = array(
        "Not Following Procedures",
        "Use of Tools Equipment,Materials and Products",
        "Intentional/Lack of Awareness/Behaviors",
        "Protective Systems",
        "Integrity of Tools/PLan/Equipment, Material",
        "Workplace Hazards",
        "Organizational",
        "Other");

} catch (Exception $e ){
    echo $e;
}
?>
    <main id="main-container" ">
    <!-- Hero -->
    <div class="bg-image bg-image-bottom" style="background-image: url('assets/media/photos/construction1.jpg');">
        <div class="bg-primary-dark-op">
            <div class="content content-top text-center overflow-hidden">
                <div class="pt-50 pb-20">
                    <h1 class="font-w700 text-white mb-10 js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">Dashboard</h1>
                    <h2 class="h4 font-w400 text-white-op js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">HSE Management System</h2>
                </div>
            </div>
        </div>
    </div>
    <!--  END Hero    -->
    <!-- Page Content -->
<?php
try {
    $yr_qry = $conn->query("SELECT YEAR (tbl_toolbox_talks.date_conducted) as yr
                                                                                        FROM tbl_toolbox_talks
                                                                                        WHERE is_deleted = 0
                                                                                        GROUP BY YEAR (tbl_toolbox_talks.date_conducted)");

    $years = $yr_qry->fetchAll();

    $year_qry2 = $conn->query("SELECT `itd_year`as yr FROM tbl_tds WHERE is_deleted = 0");

    $years2 = $year_qry2->fetchAll();
    $total_years  = array_reverse(array_merge($years2 , $years));

} catch (Exception $e) {
    echo $e;
}
?>
    <div class="content">
        <h2 class="content-heading pt-0">
            Dashboard Reports
        </h2>
        <div class="content">
            <!-- Overview -->
            <h2 class="content-heading">Overview</h2>
            <div class="row invisible d-none" data-toggle="appear">
                <!-- Row #1 -->
                <div class="col-md-6 col-xl-3">
                    <?php
                    try {
                        $total_employee_qry = $conn->query("SELECT
                                                                        Count(tbl_employees.employee_id) as total_employee
                                                                        FROM
                                                                        tbl_employees
                                                                        WHERE
                                                                        tbl_employees.is_deleted = 0");
                        $total_employee_fetch = $total_employee_qry->fetch();
                    }catch (Exception $e){
                        echo $e;
                    }
                    ?>
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full clearfix">
                            <div class="float-right mt-15 d-none d-sm-block">
                                <i class="si si-people fa-2x text-primary-light"></i>
                            </div>
                            <div class="font-size-h3 font-w600 text-primary js-count-to-enabled"><?=$total_employee_fetch['total_employee']?></div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total Employee</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl-3">
                    <?php
                    try {
                        $total_training_qry = $conn->query("SELECT
                                                                        Count(tbl_trainings.training_id) AS total_trainings
                                                                        FROM
                                                                        tbl_trainings
                                                                        WHERE
                                                                        tbl_trainings.is_deleted = 0
                                                                        ");
                        $total_training_fetch = $total_training_qry->fetch();
                        $total_training = $total_training_fetch['total_trainings'];
                    }catch (Exception $e){
                        echo $e;
                    }
                    ?>
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full clearfix">
                            <div class="float-right mt-15 d-none d-sm-block">
                                <i class="si si-energy fa-2x text-earth-light"></i>
                            </div>
                            <div class="font-size-h3 font-w600 text-earth"><span ><?=$total_training?></span></div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total Trainings</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full clearfix">
                            <div class="float-right mt-15 d-none d-sm-block">
                                <i class="fa fa-wrench fa-2x text-elegance-light"></i>
                            </div>
                            <div class="font-size-h3 font-w600 text-elegance" data-toggle="countTo" data-speed="1000" data-to="15">0</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Total Toolbox Record</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full clearfix">
                            <div class="float-right mt-15 d-none d-sm-block">
                                <i class="fa fa-clipboard fa-2x text-pulse"></i>
                            </div>
                            <div class="font-size-h3 font-w600 text-pulse" data-toggle="countTo" data-speed="1000" data-to="4252">0</div>
                            <div class="font-size-sm font-w600 text-uppercase text-muted">Inventory</div>
                        </div>
                    </a>
                </div>
                <!-- END Row #1 -->
            </div>

            <!-- END Page Content -->

            <!--        SQL /-->

            <!-- Hero -->
            <div hidden class="bg-image" style="background-image: url('assets/media/photos/photo13@2x.jpg');">
                <div class="bg-black-op-75">
                    <div class="content content-top content-full text-center">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10"> <?= $year = $_GET['year'];?> ITD REPORT </h1>
                        </div>
                    </div>
                </div>
            </div>
            <!--                CLASSIFICATION INCIDENT-->
            <!--            <div class="col-12">-->
            <!--                <div class="block">-->
            <!--                    <div class="block block-rounded">-->
            <!--                        <div class="block-header">-->
            <!--                            <h3>Incident Classification</h3>-->
            <!--                        </div>-->
            <!--                        <div class="block-content">-->
            <!--                         -->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
<!--            <div id="fa_canvass" class="row">-->
<!---->
<!--            </div>-->
            <div id="sample">
                <div class="row">
                    <div class="col-6">
                        <div class="block">
                            <div class="block block-rounded">
                                <div class="block-header">
                                    <h3>Incidents Classification 2022</h3>
                                </div>
                                <div class="block-content">
                                    <table class="table table-bordered">
                                        <thead class="bg-primary text-center text-white">
                                        <th>Month</th>
                                        <?php foreach($months as $month):?>
                                            <th ><?=$month?></th>
                                        <?php endforeach;?>
                                        </thead>
                                        <tbody class="text-center">
                                        <!--                                    monthly incident per type------------------->
                                        <?php $i = 1; ?>
                                        <?php foreach($incident_type as $type):?>
                                            <?php
                                            $get_monthly_incidents_qry = $conn->query("SELECT COUNT(fa_id) as number_of_incident , MONTH(date_created)  as incident_month FROM tbl_first_aid WHERE incident_type= $i AND YEAR(date_created) = 2022 GROUP BY MONTH(date_created)" );
                                            $monthly_incident = $get_monthly_incidents_qry->fetchAll();
                                            $array_monthly_incident = array(0,0,0,0,0,0,0,0,0,0,0,0);
                                            foreach ($monthly_incident as $incident){
                                                $array_monthly_incident[$incident['incident_month']-1] = $incident['number_of_incident'];
                                            }
                                            ?>
                                            <tr>
                                                <td class="<?=(($i < 5)? "bg-danger text-white":"bg-black-op-5")?>"><?=$type?></td>
                                                <?php foreach($array_monthly_incident as $incident_number): ?>
                                                    <td class=" <?=(($incident_number==0)?'': 'bg-black text-white') ?>"><?=$incident_number?></td>
                                                <?php endforeach; ?>
                                            </tr>

                                            <?php $i++;
                                        endforeach;?>

                                        <?php
                                        $get_monthly_incidents_qry = $conn->query("SELECT COUNT(fa_id) as number_of_incident , MONTH(date_created)  as incident_month FROM tbl_first_aid WHERE YEAR(date_created) = 2022 GROUP BY MONTH(date_created)" );
                                        $monthly_incident = $get_monthly_incidents_qry->fetchAll();
                                        $array_monthly_incident = array(0,0,0,0,0,0,0,0,0,0,0,0);
                                        foreach ($monthly_incident as $incident){
                                            $array_monthly_incident[$incident['incident_month']-1] = $incident['number_of_incident'];
                                        }
                                        ?>
                                        <tr class=" text-black">
                                            <td>Total</td>
                                            <?php foreach($array_monthly_incident as $incident_number): ?>
                                                <td ><?=$incident_number?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--                        ROOT CAUSES ANALYSIS-->
                    <div class="col-12">
                        <div class="block">
                            <div class="block block-rounded">
                                <div class="block-header">
                                    <h3>Root Causes Analysis</h3>
                                </div>
                                <div class="block-content">
                                    <table class="table table-bordered ">
                                        <thead class="bg-primary text-center text-white">
                                        <?php foreach($root_causes as $cause):?>
                                            <th ><?=$cause?></th>
                                        <?php endforeach;?>
                                        </thead>
                                        <tbody class="text-center">
                                        <!--                                    monthly incident per type------------------->
                                        <tr>
                                            <?php
                                            $i = 1;
                                            foreach($root_causes as $cause):

                                                $get_causes = $conn->query("SELECT COUNT(fa_id) as number_of_causes
                                                                                            FROM tbl_first_aid 
                                                                                            WHERE root_causes =  $i AND YEAR(date_created) = 2022 
                                                                                            GROUP BY root_causes" );
                                                $get_causes = $get_causes->fetch();

                                                ?>
                                                <td class=" <?=(($get_causes==0)?'': 'bg-blue-op 5 text-black') ?>">
                                                    <?php
                                                    $cause = 0;
                                                    if (!empty($get_causes)){
                                                        $cause = $get_causes['number_of_causes'];
                                                    }
                                                    echo $cause;
                                                    ?>

                                                </td>
                                                <?php $i++; endforeach; ?>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--                        LEADING INDICATORS-->
                    <div class="col-12">
                        <div class="block">
                            <div class="block block-rounded">
                                <div class="block-header">
                                    <h3>Leading Indicators</h3>
                                </div>
                                <div class="block-content">
                                    <table class="table table-bordered ">
                                        <thead class="bg-primary text-center text-white">
                                        <?php foreach($leading_indicators as $indicator):?>
                                            <th ><?=$indicator?></th>
                                        <?php endforeach;?>
                                        </thead>
                                        <tbody class="text-center">
                                        <!--                                    monthly incident per type------------------->
                                        <tr>
                                            <?php
                                            $i = 1;
                                            foreach($leading_indicators as $indicator):
                                                $get_causes = $conn->query("SELECT COUNT(fa_id) as number_of_causes
                                                                                            FROM tbl_first_aid 
                                                                                            WHERE leading_indicator =  $i AND YEAR(date_created) = 2022 
                                                                                            GROUP BY leading_indicator" );
                                                $get_causes = $get_causes->fetch();
                                                ?>
                                                <td class=" <?=(($get_causes==0)?'': 'bg-blue-op 5 text-black') ?>">
                                                    <?php
                                                    $cause = 0;
                                                    if (!empty($get_causes)){
                                                        $cause = $get_causes['number_of_causes'];
                                                    }
                                                    echo $cause;
                                                    ?>

                                                </td>
                                                <?php $i++; endforeach; ?>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="float-right d-print-none">
                    <form class="block-options" id="generate_reports" action="tbt_reports_pdf.php" method="post">
                        <div class="form-inline">
                            <select class="form-control mr-5" id="select_month" name="select_month" onchange="load_chart()">
                                <option id="01"  value="01">January</option>
                                <option id="02"  value="02">February</option>
                                <option id="03"  value="03" >March</option>
                                <option id="04"  value="04">April</option>
                                <option id="05"  value="05">May</option>
                                <option id="06"  value="06">June</option>
                                <option id="07"  value="07">July</option>
                                <option id="08"  value="08">August</option>
                                <option id="09"  value="09">September</option>
                                <option id="010" value="10">October</option>
                                <option id="011" value="11">November</option>
                                <option id="012" value="12">December</option>
                            </select>
                            <select class="form-control mr-5" onchange="load_chart()" id="year" name="year">
                                <?php
                                foreach ($total_years as $year) {
                                    ?>
                                    <option value="<?= $year['yr'] ?>"><?= $year['yr'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <button type="submit" class="btn btn-primary mr-5"><li class="fa fa-folder"></li> Generate</button>
                            <button type="button" class="btn btn-primary" onclick="Codebase.helpers('print-page');"> Print</button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
        <!--    <div class=" block-content block-content-full shadow p-3 mb-5 bg-white rounded d-print-none">-->
        <!---->
        <!--    </div>-->
        <!--        ADD NEW ITD-->
        <!--            ADD NEW ITD-->
        <div class="modal fade" id="edit_itd_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" >EDIT ITD</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="si si-close"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content">
                            <form id="edit_itd_form">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">ITD </label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" readonly id="edit_itd_id" name="edit_itd_id" placeholder="Total Days" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">Civils</label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" id="total_days_civils" name="total_days_civils" placeholder="Total Days" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img"></label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg"  id="total_hours_civils" placeholder="Total Hours" name="total_hours_civils">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">Mechanicals</label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" id="total_days_mechanicals" name="total_days_mechanicals" placeholder="Total Days" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img"></label>
                                            <div class="col-12">
                                                <input type ="number" class="form-control form-control-lg"  id="total_hours_mechanicals" placeholder="Total Hours" name="total_hours_mechanicals">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">Office</label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" id="total_days_office" name="total_days_office" placeholder="Total Days" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img"></label>
                                            <div class="col-12">
                                                <input type ="number" class="form-control form-control-lg"  id="total_hours_office" placeholder="Total Hours" name="total_hours_office">
                                            </div>
                                        </div>
                                    </div>
                                    <!-----------------FIRST COLUMN---------------------->
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">Electricals</label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" id="total_days_electricals" placeholder="Total Days" name="total_days_electricals"  required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img"></label>
                                            <div class="col-12">
                                                <input type ="number" class="form-control form-control-lg"  id="total_hours_electricals" placeholder="Total Hours" name="total_hours_electricals">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">Camps</label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" id="total_days_camps" name="total_days_camps" placeholder="Total Days" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img"></label>
                                            <div class="col-12">
                                                <input type ="number" class="form-control form-control-lg"  id="total_hours_camps" placeholder="Total Hours" name="total_hours_camps">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img">Select Year</label>
                                            <div class="col-12">
                                                <select class="form-control" id="select_year" name="select_year">
                                                    <option value="2015">2015</option>
                                                    <option value="2016">2016</option>
                                                    <option value="2017">2017</option>
                                                    <option value="2018">2018</option>
                                                    <option value="2019">2019</option>
                                                    <option value="2020">2020</option>
                                                    <option value="2021">2021</option>
                                                </select>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group row">
                                            <div class="col-12 text-center">
                                                <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                                    <i class="fa fa-plus mr-5"></i> EDIT ITD
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </main>
        <!-- END Main Container -->
    </div>

    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page JS Code -->
    <script src="assets/js/pages/be_tables_datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script src="assets/js/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script src="assets/js/plugins/easy-pie-chart/jquery.easypiechart.min.js"></script>

    <!-- Page JS Code -->
    <script src="assets/js/pages/be_blocks_widgets_stats.min.js"></script>

    <!-- Page JS Helpers (Easy Pie Chart Plugin) -->
    <script>jQuery(function(){ Codebase.helpers('easy-pie-chart'); });</script>
    <script>
        $(document).ready(function() {
            <?php
            $dateTime = new DateTime();
            $month = $dateTime->format('m')-1;

            ?>
            $( <?php echo '"#0'.$month.'"'; ?>).attr( "selected", true )
            load_chart();
            first_aid_chart();
        });
        function load_chart(){
            var year =  $("#year").val();
            var select_month =  $("#select_month").val();
            if (year < 2022){
                $('#012').prop('selected', true);
                $('.disable-select').prop('disabled', true);
            } else {
                $('.disable-select').prop('disabled', false);
            }

            $('#generate_itd_report').attr("disabled", false);
            $( "#default_select" ).attr( "disabled", false );
            $.ajax({
                type: 'POST',
                url: 'ajax/toolbox_talks_reports_ajax.php',
                data: {
                    year: year,
                    select_month: select_month,
                    load_chart_annual: 1
                },
                success: function (response) {
                    $('#canvass').html(response);
                    // location.reload();
                },
                error: function () {
                    console.log("Error Generate Report function");
                }
            });
        }
        function first_aid_chart(){
            alert('test');
            $.ajax({
                type: 'POST',
                url: 'ajax/incident_ajax.php',
                data: {
                    first_aid_chart: 1
                },
                success: function (response) {
                    $('#fa_canvass').html(response);
                    // location.reload();
                },
                error: function () {
                    console.log("Error Generate Report function");
                }
            });
        }
        function generate_itd(itd_year){
            location.href = "toolbox_talks_reports_itds.php?year="+itd_year;
        }
    </script>
<?php
include 'includes/footer.php';
