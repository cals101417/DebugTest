<?php
require_once '../session.php';


if (isset($_POST['load_chart'])){

    $year = $_POST['year'];
    $select_month = $_POST['select_month'];
//    echo $select_month;
    $get_itds_years_qry = $conn->query("SELECT  COUNT(*), Year(training_date) as itd_year,
                                         SUM(case when type='1' then training_hrs else 0 end) as total_hours_inhouse,
                                        SUM(case when type='2' then training_hrs else 0 end) as total_hours_client,
                                        SUM(case when type='3' then training_hrs else 0 end) as total_hours_third_party,
                                        SUM(case when type='4' then training_hrs else 0 end) as total_hours_induction,
                                        COUNT(case when type='1' then training_hrs else null end) as total_trainee_inhouse,
                                        COUNT(case when type='2' then training_hrs else null end) as total_trainee_client,
                                        COUNT(case when type='3' then training_hrs else null end) as total_trainee_third_party,  
                                        COUNT(case when type='4' then training_hrs else null end) as total_trainee_induction,
                                        COUNT('tm_id') as totals_days,
                                        SUM(training_hrs) as totals_hours        
                                        FROM tbl_trainings 
                                        INNER JOIN tbl_training_trainees ON tbl_trainings.training_id = tbl_training_trainees.training_id  
                                        WHERE  tbl_trainings.is_deleted = 0
                                        GROUP BY date_format(training_date, '%Y') ORDER BY `training_date`");
    $get_itds_result = $get_itds_years_qry->fetchAll();

    $get_report_by_year = $conn->query("SELECT *
                                                               
                                                        FROM tbl_tds                                          
                                                        WHERE `is_deleted` = 0
                                                        ORDER BY `itd_year` DESC
                                        ");

    $get_itds_manual = $get_report_by_year->fetchAll();

    $get_itds_month_qry = $conn->query("SELECT  COUNT(*), MONTH(training_date)  as itd_month,
                                        SUM(case when type='1' then training_hrs else 0 end) as total_hours_inhouse,
                                        SUM(case when type='2' then training_hrs else 0 end) as total_hours_client,
                                        SUM(case when type='3' then training_hrs else 0 end) as total_hours_third_party,
                                        SUM(case when type='4' then training_hrs else 0 end) as total_hours_induction,
                                        COUNT(case when type='1' then training_hrs else null end) as total_trainee_inhouse,
                                        COUNT(case when type='2' then training_hrs else null end) as total_trainee_client,
                                        COUNT(case when type='3' then training_hrs else null end) as total_trainee_third_party,  
                                        COUNT(case when type='4' then training_hrs else null end) as total_trainee_induction,
                                        COUNT('tm_id') as totals_days,
                                        SUM(training_hrs) as totals_hours
                                        FROM tbl_trainings 
                                        INNER JOIN tbl_training_trainees ON tbl_trainings.training_id = tbl_training_trainees.training_id  
                                        WHERE  tbl_trainings.is_deleted = 0 AND YEAR(training_date) = $year AND MONTH(training_date) <= $select_month 
                                        GROUP BY date_format(training_date, '%M') ORDER BY `training_date`
                                        ");
    $get_month_itds = $get_itds_month_qry->fetchAll();

    $get_itds_table_data_qry = $conn->query("SELECT  COUNT(*), MONTH(training_date)  as itd_month,
                                        COUNT('tbtp_id') as totals_days,
                                        SUM(training_hrs) as total_itd,
                                        SUM( case when tbl_trainings.is_deleted = 0 AND YEAR(training_date) = $year AND MONTH(training_date) = $select_month then training_hrs else 0 end) as total_hours_selected_month,
                                        COUNT( case when tbl_trainings.is_deleted = 0 AND YEAR(training_date) = $year AND MONTH(training_date) = $select_month then trainee_id else 0 end) as total_participants_per_month
                                        FROM tbl_trainings 
                                        INNER JOIN tbl_training_trainees ON tbl_trainings.training_id = tbl_training_trainees.training_id  
                                        WHERE  tbl_trainings.is_deleted = 0 AND YEAR(training_date) <= $year AND MONTH(training_date) <= $select_month 
                                        ORDER BY `training_date`");
    $get_itds_table_data = $get_itds_table_data_qry->fetchAll();

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
        'label' => "TOTAL IN-HOUSE HOURS",
        'backgroundColor'=> [],
        'borderColor' => 'rgb(111, 99, 132)',
        'data' => []
    );//assign each sub-array to the newly created array

    $arr4[] = array(
        'label' => "TOTAL CLIENTS HOURS",
        'backgroundColor'=> [],
        'borderColor' => 'rgb(111, 99, 132)',
        'data' => []
    );//assign each sub-array to the newly created array
    $arr5[] = array(
        'label' => "TOTAL THIRD_PARTY HOURS",
        'backgroundColor'=> [],
        'borderColor' => 'rgb(111, 99, 132)',
        'data' => []
    );//assign each sub-array to the newly created array
    //induction
    $arr6[] = array(
        'label' => "TOTAL INDUCTION HOURS",
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
    $monthly_values_inhouse = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthly_values_client = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthly_values_third_party = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthly_values_induction = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthly_values_office = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthly_totals_hours = array(0,0,0,0,0,0,0,0,0,0,0,0);

    foreach ($get_month_itds as $result){
        $monthly_values_inhouse[intval($result['itd_month'])-1] = $result['total_hours_inhouse'];
        $monthly_values_client[intval($result['itd_month'])-1] = $result['total_hours_client'];
        $monthly_values_third_party[intval($result['itd_month'])-1] = $result['total_hours_third_party'];
        $monthly_values_induction[intval($result['itd_month'])-1] = $result['total_hours_induction'];
        $monthly_totals_hours[intval($result['itd_month'])-1] = $result['totals_hours'];
    }
    foreach($monthly_values_inhouse as $result){
        $r = rand(1,100);
        $g = rand(50,150);
        $b = rand(100,255);

        $arr3[0]['data'][] = intval($result);
        $arr3[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
    }

    foreach($monthly_values_client as $result){
        $r = rand(1,100);
        $g = rand(50,150);
        $b = rand(100,255);

        $arr4[0]['data'][] = intval($result);
        $arr4[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
    }
    foreach($monthly_values_third_party as $result){
        $r = rand(1,100);
        $g = rand(50,150);
        $b = rand(100,255);

        $arr5[0]['data'][] = intval($result);
        $arr5[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
    }
    foreach($monthly_values_induction as $result){
        $r = rand(1,100);
        $g = rand(50,150);
        $b = rand(100,255);

        $arr6[0]['data'][] = intval($result);
        $arr6[0]['backgroundColor'][] ='rgb('.$r.','.$g.','.$b.')';
    }

    foreach($monthly_values_induction as $result){
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
    <br>
    <br>

    <h3 class="text-center text-muted"> Monthly Training Report (<?=$year?>)</h3>
    <div class="row mx-20">
        <div class="col-sm-12 mb-10">
            <canvas id="myChart8" height="80"></canvas>
            <hr>
        </div>
        <div class="col-6">
            <canvas id="myChart3"></canvas>
            <hr>
        </div>
        <div class="col-6">
            <canvas id="myChart4"></canvas>
            <hr>
        </div>
        <div class="col-6">
            <canvas id="myChart5"></canvas>
            <hr>
        </div>
        <div class="col-6">
            <canvas id="myChart6"></canvas>
            <hr>
        </div>
<!--        <div class="col-sm-12 mb-20">-->
<!--            <canvas id="myChart7" height="100"></canvas>-->
<!--        </div>-->
    </div>
    <script>
        var labels = <?php  echo json_encode($itd_years);?>;

        labels3 = ["January","Feburary","March","April","May","June","July","August","September","October","November","December"]
        var data3 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets3);;?>
        };
        var config3 = {
            type: 'bar',
            data: data3,
            options: {}
        };

        var myChart3 = new Chart(
            document.getElementById('myChart3'),
            config3
        );
        // FOURTH CHART
        var data4 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets4);;?>
        };
        var config4 = {
            type: 'bar',
            data: data4,
            options: {}
        };

        var myChart4 = new Chart(
            document.getElementById('myChart4'),
            config4
        );


        // FIFTH CHART

        var data5 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets5);;?>
        };
        var config5 = {
            type: 'bar',
            data: data5,
            options: {}
        };

        var myChart5 = new Chart(
            document.getElementById('myChart5'),
            config5
        );
        // SIXTH CHART

        var data6 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets6);;?>
        };
        var config6 = {
            type: 'bar',
            data: data6,
            options: {}
        };

        var myChart6 = new Chart(
            document.getElementById('myChart6'),
            config6
        );
        // SEVENTH CHART
        var data7 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets7);;?>
        };
        var config7 = {
            type: 'bar',
            data: data7,
            options: {}
        };

        var myChart7 = new Chart(
            document.getElementById('myChart7'),
            config7
        );
        // EIGHT CHART
        var data8 = {
            labels:labels3,
            datasets: <?php   print_r($data_sets8);;?>
        };
        var config8 = {
            type: 'bar',
            data: data8,
            options: {}
        };

        var myChart8 = new Chart(
            document.getElementById('myChart8'),
            config8
        );
    </script>
    <?php
}
