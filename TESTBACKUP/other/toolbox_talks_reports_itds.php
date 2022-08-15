<?php

include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
?>
    <div id="page-container"
         class="sidebar-o enable-page-overlay side-scroll <?= $header_layout ?> page-header-inverse <?= $main_content . ' ' . $sidebar_layout ?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        ?>
        <!-- Main Container -->
        <main id="main-container" style="min-height: 871px;">
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
            <?php
            $get_itds_years_qry = $conn->query("SELECT  COUNT(*), Year(date_conducted) as itd_year,
                                        COUNT('tbtp_id') as totals_days,
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
                                        SUM(time) as totals_hours            
                                        FROM tbl_toolbox_talks 
                                        INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                        WHERE  tbl_toolbox_talks.is_deleted = 0
                                        GROUP BY date_format(date_conducted, '%Y') ORDER BY `date_conducted`");




            $get_itds_result = $get_itds_years_qry->fetchAll();

            $get_report_by_year = $conn->query("SELECT *
                                                               
                                                        FROM tbl_tds                                          
                                                        WHERE `is_deleted` = 0
                                                        ORDER BY `itd_year` DESC
                                        ");

            $get_itds_manual = $get_report_by_year->fetchAll();
            $get_itds_month_qry = $conn->query("SELECT  COUNT(*), MONTH(date_conducted)  as itd_month,
                                        COUNT('tbtp_id') as totals_days,
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
                                        SUM(time) as totals_hours            
                                        FROM tbl_toolbox_talks 
                                        INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                        WHERE  tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = $year
                                        GROUP BY date_format(date_conducted, '%M') ORDER BY `date_conducted`");
            $get_month_itds = $get_itds_month_qry->fetchAll();

            $merge_result = array_merge(array_reverse($get_itds_manual),$get_itds_result);

            $arr[] = array(
                 'label' => "TOTAL HOURS",
                'backgroundColor'=> [],
                'barThickness' => '25',
                'maxBarThickness' =>'25',
                'borderColor' => 'rgb(111, 99, 132)',
                'data' => []
            );//assign each sub-array to the newly created array

            $arr2[] = array(
                'label' => "TOTAL PARTICIPANTS",
                'backgroundColor'=> [],
                'barThickness' => '25',
                'maxBarThickness' =>'25',
                'borderColor' => 'rgb(111, 99, 132)',
                'data' => []
            ); //assign each sub-array to the newly created array

            $arr3[] = array(
                'label' => "TOTAL CIVILS HOURS",
                'backgroundColor'=> [],
                'borderColor' => 'rgb(111, 99, 132)',
                'data' => []
            );//assign each sub-array to the newly created array

            $arr4[] = array(
                'label' => "TOTAL MECHANICALS HOURS",
                'backgroundColor'=> [],
                'borderColor' => 'rgb(111, 99, 132)',
                'data' => []
            );//assign each sub-array to the newly created array
            $arr5[] = array(
                'label' => "TOTAL ELECTRICALS HOURS",
                'backgroundColor'=> [],
                'borderColor' => 'rgb(111, 99, 132)',
                'data' => []
            );//assign each sub-array to the newly created array
            //camps
            $arr6[] = array(
                'label' => "TOTAL CAMPS HOURS",
                'backgroundColor'=> [],
                'borderColor' => 'rgb(111, 99, 132)',
                'data' => []
            );//assign each sub-array to the newly created array
            //office
            $arr7[] = array(
                'label' => "TOTAL OFFICE HOURS",
                'backgroundColor'=> [],
                'borderColor' => 'rgb(111, 99, 132)',
                'data' => []
            );
            $arr8[] = array(
                'label' => "TOTAL MONTHLY HOURS",
                'backgroundColor'=> [],
                'borderColor' => 'rgb(111, 99, 132)',
                'data' => []
            );


            foreach ($merge_result as $result){
                $r = rand(1,100);
                $g = rand(50,150);
                $b = rand(100,255);

                $arr[0]['data'][] = intval($result['totals_hours']);
                $arr[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
                $itd_years[] = $result['itd_year'];

                $r2 = rand(55,255);
                $g2 = rand(111,150);
                $b2 = rand(222,50);

                $arr2[0]['data'][] = intval($result['totals_days']);
                $arr2[0]['backgroundColor'][] ='rgb('.$r2.','.$g2.','.$b2.')';
                $itd_years2[] = $result['itd_year'];
            }

            //THIRD CHART
            $monthly_values_civils = array(0,0,0,0,0,0,0,0,0,0,0,0);
            $monthly_values_mechanical = array(0,0,0,0,0,0,0,0,0,0,0,0);
            $monthly_values_electricals = array(0,0,0,0,0,0,0,0,0,0,0,0);
            $monthly_values_camps = array(0,0,0,0,0,0,0,0,0,0,0,0);
            $monthly_values_office = array(0,0,0,0,0,0,0,0,0,0,0,0);
            $monthly_totals_hours = array(0,0,0,0,0,0,0,0,0,0,0,0);

            foreach ($get_month_itds as $result){
                $monthly_values_civils[intval($result['itd_month'])-1] = $result['total_hours_civils'];
                $monthly_values_mechanical[intval($result['itd_month'])-1] = $result['total_hours_mechanicals'];
                $monthly_values_electricals[intval($result['itd_month'])-1] = $result['total_hours_electricals'];
                $monthly_values_camps[intval($result['itd_month'])-1] = $result['total_hours_camps'];
                $monthly_values_office[intval($result['itd_month'])-1] = $result['total_hours_office'];
                $monthly_totals_hours[intval($result['itd_month'])-1] = $result['totals_hours'];


            }



            foreach($monthly_values_civils as $result){
                $r = rand(1,100);
                $g = rand(50,150);
                $b = rand(100,255);

                $arr3[0]['data'][] = intval($result);
                $arr3[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
            }

            foreach($monthly_values_mechanical as $result){
                $r = rand(1,100);
                $g = rand(50,150);
                $b = rand(100,255);

                $arr4[0]['data'][] = intval($result);
                $arr4[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
            }
            foreach($monthly_values_electricals as $result){
                $r = rand(1,100);
                $g = rand(50,150);
                $b = rand(100,255);

                $arr5[0]['data'][] = intval($result);
                $arr5[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
            }
            foreach($monthly_values_camps as $result){
                $r = rand(1,100);
                $g = rand(50,150);
                $b = rand(100,255);

                $arr6[0]['data'][] = intval($result);
                $arr6[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
            }

            foreach($monthly_values_camps as $result){
                $r = rand(1,100);
                $g = rand(50,150);
                $b = rand(100,255);

                $arr7[0]['data'][] = intval($result);
                $arr7[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
            }
            foreach($monthly_totals_hours as $result){
                $r = rand(1,100);
                $g = rand(50,150);
                $b = rand(100,255);

                $arr8[0]['data'][] = intval($result);
                $arr8[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
            }


            $data_sets =  str_replace([
                '"label"',
                '"backgroundColor"',
                '"borderColor"',
                '"barThickness"',
                '"maxBarThickness"',
//                '"minBarLength"',
//                '"barPercentage"',
                '"data"'],
                ["label",
                    "backgroundColor",
                    "borderColor",
                    "barThickness",
                    "maxBarThickness",
//                    "minBarLength",
//                    "barPercentage",
                    "data",""],json_encode($arr));

            $data_sets2 =  str_replace([
                '"label"',
                '"backgroundColor"',
                '"borderColor"',
                '"barThickness"',
                '"maxBarThickness"',
//                '"minBarLength"',
//                '"barPercentage"',
                '"data"'],
                ["label",
                    "backgroundColor",
                    "borderColor",
                    "barThickness",
                    "maxBarThickness",
//                    "minBarLength",
//                    "barPercentage",
                    "data",""],json_encode($arr2));
            $data_sets3 =  str_replace([
                '"label"',
                '"backgroundColor"',
                '"borderColor"',
//                '"barThickness"',
//                '"maxBarThickness"',
//                '"minBarLength"',
//                '"barPercentage"',
                '"data"'],
                ["label",
                    "backgroundColor",
                    "borderColor",
//                    "barThickness",
//                    "maxBarThickness",
//                    "minBarLength",
//                    "barPercentage",
                    "data",""],json_encode($arr3));

            $data_sets4=  str_replace([
                '"label"',
                '"backgroundColor"',
                '"borderColor"',
//                '"barThickness"',
//                '"maxBarThickness"',
//                '"minBarLength"',
//                '"barPercentage"',
                '"data"'],
                ["label",
                    "backgroundColor",
                    "borderColor",
//                    "barThickness",
//                    "maxBarThickness",
//                    "minBarLength",
//                    "barPercentage",
                    "data",""],json_encode($arr4));

            $data_sets5=  str_replace([
                '"label"',
                '"backgroundColor"',
                '"borderColor"',
//                '"barThickness"',
//                '"maxBarThickness"',
//                '"minBarLength"',
//                '"barPercentage"',
                '"data"'],
                ["label",
                    "backgroundColor",
                    "borderColor",
//                    "barThickness",
//                    "maxBarThickness",
//                    "minBarLength",
//                    "barPercentage",
                    "data",""],json_encode($arr5));

            $data_sets6=  str_replace([
                '"label"',
                '"backgroundColor"',
                '"borderColor"',
//                '"barThickness"',
//                '"maxBarThickness"',
//                '"minBarLength"',
//                '"barPercentage"',
                '"data"'],
                ["label",
                    "backgroundColor",
                    "borderColor",
//                    "barThickness",
//                    "maxBarThickness",
//                    "minBarLength",
//                    "barPercentage",
                    "data",""],json_encode($arr6));

            $data_sets7=  str_replace([
                '"label"',
                '"backgroundColor"',
                '"borderColor"',
//                '"barThickness"',
//                '"maxBarThickness"',
//                '"minBarLength"',
//                '"barPercentage"',
                '"data"'],
                ["label",
                    "backgroundColor",
                    "borderColor",
//                    "barThickness",
//                    "maxBarThickness",
//                    "minBarLength",
//                    "barPercentage",
                    "data",""],json_encode($arr7));
            $data_sets8=  str_replace([
                '"label"',
                '"backgroundColor"',
                '"borderColor"',
//                '"barThickness"',
//                '"maxBarThickness"',
//                '"minBarLength"',
//                '"barPercentage"',
                '"data"'],
                ["label",
                    "backgroundColor",
                    "borderColor",
//                    "barThickness",
//                    "maxBarThickness",
//                    "minBarLength",
//                    "barPercentage",
                    "data",""],json_encode($arr8));

            ?>
            <div class="bg-image bg-image-bottom" style="background-image: url('assets/media/photos/construction3.jpg');">
                <div class="bg-primary-dark-op">
                    <div class="content content-top text-center overflow-hidden">
                        <div class="pt-50 pb-20">
                            <h1 class="font-w700 text-white mb-10 js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">ToolBox Talks Reports</h1>
                            <h2 class="h4 font-w400 text-white-op js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">Manage your TBT Reports and ITDs!</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container bg-light">

<!--                <div class="row">-->
<!--                    <div class="block-options float-right">-->
                        <!-- Print Page functionality is initialized in Helpers.print() -->
<!--                        <button type="button" class="btn-block-option" onclick="Codebase.helpers('print-page');">-->
<!--                            <i class="si si-printer"></i> Print Report-->
<!--                        </button>-->
<!--                    </div>-->
<!--                    <div class="col-sm-12">-->
<!--                        <div>-->
<!--                            <h3   style="text-align: center;">--><?//=$year?><!-- ANNUAL ITD REPORTS</h3>-->
<!--                        </div>-->
<!--                        <button class="btn btn-primary float-right" onclick="history.back()">Back</button>-->
<!--                    </div>-->
<!--                    <div class="col-sm-6">-->
<!--                        <canvas id="myChart" style=" width:100%;max-width:1000px"></canvas>-->
<!--                    </div>-->
<!--                    <div class="col-sm-6">-->
<!--                        <canvas id="myChart2" style="width:100%;max-width:1000px"></canvas>-->
<!--                    </div>-->
<!--                </div>-->

                <div class="row">
                    <div class="col-sm-12">  <h3  style="text-align: center;"><?=$year?> MONTHLY ITD REPORTS</h3></div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <canvas id="myChart8" style="height:150%; width:100%;max-width:1000px"></canvas>
                    </div>
                    <div class="col-sm-6">
                        <canvas id="myChart3" style="width:100%;max-width:1000px"></canvas>
                    </div>
                    <div class="col-sm-6">
                        <canvas id="myChart4" style="width:100%;max-width:1000px"></canvas>
                    </div>
                    <div class="col-sm-6">
                        <canvas id="myChart5" style="width:100%;max-width:1000px"></canvas>
                    </div>
                    <div class="col-sm-6">
                        <canvas id="myChart6" style="width:100%;max-width:1000px"></canvas>
                    </div>
                    <div class="col-sm-12">
                        <canvas id="myChart7" style="height:150%; width:100%;max-width:1000px"></canvas>
                    </div>

                </div>

            </div>
        </main>

        <!-- END Main Container -->
    </div>


    <!-- END Pull Stocks From Inventory Modal -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function () {
            $('#toolbox_sidebar').addClass('open');
        });
        function generate_itd(){}
    </script>
    <script>

        const labels = <?php  echo json_encode($itd_years);?>;
        labels3 = ["January","Feburary","March","April","May","June","July","August","September","October","November","December"]
        const data3 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets3);;?>
        };
        const config3 = {
            type: 'bar',
            data: data3,
            options: {}
        };

        const myChart3 = new Chart(
            document.getElementById('myChart3'),
            config3
        );

        // FOURTH CHART

        const data4 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets4);;?>
        };
        const config4 = {
            type: 'bar',
            data: data4,
            options: {}
        };

        const myChart4 = new Chart(
            document.getElementById('myChart4'),
            config4
        );


        // FIFTH CHART

        const data5 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets5);;?>
        };
        const config5 = {
            type: 'bar',
            data: data5,
            options: {}
        };

        const myChart5 = new Chart(
            document.getElementById('myChart5'),
            config5
        );
        // SIXTH CHART

        const data6 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets6);;?>
        };
        const config6 = {
            type: 'bar',
            data: data6,
            options: {}
        };

        const myChart6 = new Chart(
            document.getElementById('myChart6'),
            config6
        );
        // SEVENTH CHART
        const data7 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets7);;?>
        };
        const config7 = {
            type: 'bar',
            data: data7,
            options: {}
        };

        const myChart7 = new Chart(
            document.getElementById('myChart7'),
            config7
        );
        // EIGHT CHART
        const data8 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets8);;?>
        };
        const config8 = {
            type: 'bar',
            data: data8,
            options: {}
        };

        const myChart8 = new Chart(
            document.getElementById('myChart8'),
            config8
        );

    </script>




<?php
include 'includes/footer.php';