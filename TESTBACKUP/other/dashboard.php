<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
?>
    <style>
        @page {
        @page {
            size: 25cm 35.7cm;
            margin: 5mm 5mm 5mm 5mm; /* change the margins as you want them to be. */
        }
    </style>
<?php

 $header_layout = 'page';
 $main_content = 'header';
 $sidebar_layout = 'glass';

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
            $total_years = array("2022","2021","2020","2019","2018","2017","2016","2015");

        } catch (Exception $e ){
            echo $e;
        }
        ?>
        <main id="main-container" ">
        <!-- Hero -->
        <div class="bg-image bg-image-bottom d-print-none" style="background-image: url('assets/media/photos/construction1.jpg');">
            <div class="bg-primary-dark-op">
                <div class="content content-top text-center overflow-hidden">
                    <div class="pt-50 pb-20 d-print-none">
                        <h1 class="font-w700 text-white mb-10 js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">Dashboard</h1>
                        <h2 class="h4 font-w400 text-white-op js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">HSE Management System</h2>
                    </div>
                </div>
            </div>
        </div>
        <!--  END Hero    -->
        <!-- Page Content -->

        <div class="content">

            <div class="block-header">
                <h2 class="content-heading pt-0 d-print-none">
                    Dashboard Reports
                </h2>
                <div class="float-right d-print-none">
                    <form class="block-options" id="generate_reports" action="tbt_reports_pdf.php" method="post">
                        <div class="form-inline">
                            <select class="form-control mr-5 mb-5" id="select_month" name="select_month" onchange="first_aid_chart()">
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
                            <select class="form-control mr-5  mb-5" onchange="first_aid_chart()" id="year" name="year">
                                <?php
                                foreach ($total_years as $year) {
                                    ?>
                                    <option value="<?= $year ?>"><?= $year ?></option>
                                    <?php
                                }
                                ?>
                            </select>

<!--                            <button type="submit" class="btn btn-primary mr-5  mb-5"><li class="fa fa-folder"></li> Generate</button>-->
<!--                            <button type="button" class="btn btn-primary  mb-5" onclick="print_window()"> Print</button>-->
                            <div class="btn-group mb-5">
                                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Action
                                </button>
                                <div class="dropdown-menu">
                                    <button type="submit" class="dropdown-item "><li class="fa fa-folder"></li> Generate PDF</button>
                                    <button type="button" class="btn " data-toggle="modal" data-target="#link_modal" data-backdrop="false" onclick="generate_link()" >Generate Link</button>
                                    <!--                                    <button type="button" class="dropdown-item" onclick="print_window()"> Print</button>-->
<!--                                    <a class="dropdown-item" href="#">Action</a>-->
<!--                                    <a class="dropdown-item" href="#">Another action</a>-->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
           
            <div class="content">
                <!-- Overview -->
                <h2 class="content-heading">Overview</h2>
                <div id="itd_request" class="block">
                </div>
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
            </div>
            <div hidden class="bg-image" style="background-image: url('assets/media/photos/photo13@2x.jpg');">
                <div class="bg-black-op-75">
                    <div class="content content-top content-full text-center">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10"> <?= $year = $_GET['year'];?> ITD REPORT </h1>
                        </div>
                    </div>
                </div>
            </div>
            <div id="fa_canvass" class="row">
            </div>
            <div id="sample">
            </div>
            <div id="canvass"></div>
            </div>


        </main>
        <!-- END Main Container -->
    </div>


    <div class="modal fade" id="link_modal" tabindex="-1" role="dialog" aria-labelledby="link_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-slideright" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Generate Link</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row">
                            <input class="form-control-lg" type="text" style="border-color: transparent; width: 100%" id="input_link" readonly>
                            <input class="form-control-lg" type="text" style="border-color: transparent; width: 100%" id="input_serial" hidden>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-alt-success" data-dismiss="modal" onclick="save_link()">
                        <i class="fa fa-clipboard"></i> Copy to Clipboard
                    </button>
                </div>
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
<!--    <script src="assets/js/plugins/sparkline/jquery.sparkline.min.js"></script>-->
    <!-- Page JS Code -->
    <!-- Page JS Helpers (Easy Pie Chart Plugin) -->
    <script>jQuery(function(){ Codebase.helpers('easy-pie-chart'); });</script>
    <canvas id="myChart" width="400" height="400"></canvas>
    <script>
        $(document).ready(function() {
            <?php
            $dateTime = new DateTime();
            $month = $dateTime->format('m')-1;
            ?>
            $( <?php echo '"#0'.$month.'"'; ?>).attr( "selected", true )
            document.body.style.zoom = "100%";
            // load_chart();
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
        function itd_request(){
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
                url: 'ajax/incident_ajax.php',
                data: {
                    year: year,
                    select_month: select_month,
                    itd_request: 1
                },
                success: function (response) {
                    $('#itd_request').html(response);
                    // location.reload();
                },
                error: function () {
                    console.log("Error Generate Report function");
                }
            });
        }
        function first_aid_chart(){
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
                url: 'ajax/incident_ajax.php',
                data: {
                    year: year,
                    select_month: select_month,
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
            $.ajax({
                type: 'POST',
                url: 'ajax/incident_ajax.php',
                data: {
                    year: year,
                    select_month: select_month,
                    itd_request: 1
                },
                success: function (response) {
                    $('#itd_request').html('');
                    $('#itd_request').html(response);
                    // location.reload();
                },
                error: function () {
                    console.log("Error Generate Report function");
                }
            });
        }
        function generate_link(){
            var base_url = window.location.origin;
            var random_num = (Math.random() * 1002);
            let serial = (random_num.toString()).replace(".", ""); // remove tuldok
            var pathArray = window.location.pathname.split( '/' );
            console.log(base_url);
            let link = base_url+"/visit.php?page=dashboard&"+"v="+serial;

            $('#input_link').val(link.toString());
            $('#input_serial').val(serial.toString());
        }
        function save_link(){

            let link = $('#input_link').val();
            let serial = $('#input_serial').val();
            navigator.clipboard.writeText(link);
            $.ajax({
                type: 'POST',
                url: 'ajax/visitor_ajax.php',
                data: {
                    add_link: 1,
                    link: link,
                    serial: serial
                },
                success: function (response) {
                    $('#itd_request').html('');
                    $('#itd_request').html(response);
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
        function print_window(){
            var canvas = $('.dashboard_canvass');
            $('.dashboard_canvass').each(function() {
                this.classList.add("col-sm-12");
                this.style.height = "150";
            });
            // canvas.classList.add("col-sm-12");

            window.print("width=100, height=200");
            location.reload();
        }
        function printGraph() {
            // var canvas = document.getElementById("myChart10");

            // win.document.write('<img src="'+image+'"/>');
            // win.print();

            // win.location.reload();

        }
    </script>
<?php
include 'includes/footer.php';
