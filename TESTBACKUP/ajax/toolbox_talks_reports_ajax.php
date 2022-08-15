<?php
require_once '../session.php';


if (isset($_POST['delete_year'])){


    try {
        $update_time = $conn->prepare("UPDATE `tbl_tds` SET `is_deleted`= ? WHERE `itd_id`= ?");
        $update_time->execute([1,$_POST['delete_year']]);
        echo "SUCCESS DELETING";
    } catch (Exception $e ){
        echo $e;
    }
}
if (isset($_POST['edit_itd_id'])){
    $itd_id = $_POST['edit_itd_id'];
    $days_civils = $_POST['total_days_civils'];
    $days_mechanicals = $_POST['total_days_mechanicals'];
    $days_electricals = $_POST['total_days_electricals'];
    $days_camps = $_POST['total_days_camps'];
    $days_office = $_POST['total_days_office'];
    $hours_civils = $_POST['total_hours_civils'];
    $hours_mechanicals = $_POST['total_hours_mechanicals'];
    $hours_electricals = $_POST['total_hours_electricals'];
    $hours_camps = $_POST['total_hours_camps'];
    $hours_office = $_POST['total_hours_office'];
    $year = $_POST['select_year'];
    $totals_days = $days_civils + $days_electricals + $days_mechanicals+ $days_camps + $days_office;
    $totals_hours = $hours_civils + $hours_electricals + $hours_mechanicals + $days_camps + $days_office;

        try {
            $update_time = $conn->prepare("UPDATE `tbl_tds` SET 
                                                `total_hours_civils`= ?,
                                                `total_hours_mechanicals`= ?,
                                                `total_hours_electricals`= ?,
                                                `total_hours_camps`= ?,
                                                `total_hours_office`= ?,                                            
                                                `total_days_civils`= ?,
                                                `total_days_mechanicals`= ?,
                                                `total_days_electricals`= ?,
                                                `total_days_camps`= ?,
                                                `total_days_office`= ?,
                                                `totals_days` = ?,
                                                `totals_hours` = ?,
                                                `itd_year` = ?
                                                WHERE `itd_id`= ?");
        $update_time->execute([
            $days_civils,
           $days_mechanicals,
           $days_electricals,
           $days_camps,
           $days_office,
           $hours_civils,
           $hours_mechanicals,
           $hours_electricals,
           $hours_camps,
           $hours_office,
           $totals_days,
           $totals_hours,
           $year,
           $itd_id
        ]);
        echo "Itd Edited Successfully";
    } catch (Exception $e ){
        echo $e;
    }
}
function  new_date_format($date){
    $new_date = $date[0]."-".$date[1]."-".$date[2]." 00:00:00";
    return $new_date;
}
if (isset($_POST['generate_report2'])) {
    $month = sprintf("%02d",$_POST['month']);
    $year = $_POST['year'];
    $dept = $_POST['dept'];

    try {
        $get_position =  $conn->query("SELECT `position`,`position_id`  FROM `tbl_position` WHERE is_deleted = 0");
        $get_all_position = $get_position->fetchAll();
        $index = 0;

            ?>
            <div class="table-responsive">
                <table class="table table-vcenter">
                    <thead class="thead-light">
                    <tr>
                        <th class="text-center text-capitalize"  style="width: 100px;">#</th>
                        <th class=" text-capitalize">
                            <?php

                            ?>
                        </th>
                        <!--  ECHO DAYS-->
                        <?php
                        for ($i = 1; $i <= 31; $i++) {
                            ?>
                            <th><?= $i ?></th>
                            <?php
                        }
                        ?>
                        <!--                         ECHO DAYS-->
                        <th class=" text-capitalize">Total Days</th>
                        <th class=" text-capitalize">Total Hrs</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php
                    $over_all_total = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0  );
                    $over_all_total_days = 0;
                    $over_all_total_time = 0;
                    $counter = 0;
                    foreach ($get_all_position as $position):
                    $position_id=  $position['position_id'];?>
                    <tr>
                        <th class="text-center" scope="row"><?=$position['position_id']?></th>
                        <td width="200"><?=$position['position']?></td>

                        <?php
                        $position_id = $position['position_id'];
                        $get_records_per_position_qry= '';
                        if ($dept == 6){
                            $get_records_per_position_qry = $conn->query("SELECT `date_conducted`,
                                                                    count(tbl_toolbox_talks_participants.tbt_id) AS day_per_participants,
                                                                    sum(tbl_toolbox_talks_participants.time) AS time_per_participant,       
                                                                    tbl_position.position, tbl_employees.employee_id, 
                                                                    tbl_position.position_id
                                                                    FROM `tbl_toolbox_talks_participants`
                                                                    INNER JOIN `tbl_employees`  ON  tbl_toolbox_talks_participants.employee_id  =  tbl_employees.employee_id  
                                                                    INNER JOIN `tbl_position` ON tbl_employees.position = tbl_position.position_id 
                                                                    INNER JOIN  `tbl_toolbox_talks` ON tbl_toolbox_talks_participants.tbt_id = tbl_toolbox_talks.tbt_id
                                                                    WHERE  tbl_employees.position = $position_id 
                                                                               AND tbl_toolbox_talks.is_deleted = 0 
                                                                               AND YEAR ( tbl_toolbox_talks.date_conducted ) = $year AND  MONTH ( tbl_toolbox_talks.date_conducted ) = $month
                                                                    GROUP BY  DAY(date_conducted) ");
                        } else {
                            $get_records_per_position_qry = $conn->query("SELECT `date_conducted`,
                                                                        count(tbl_toolbox_talks_participants.tbt_id) AS day_per_participants,
                                                                        sum(tbl_toolbox_talks_participants.time) AS time_per_participant,       
                                                                        tbl_position.position, tbl_employees.employee_id, 
                                                                        tbl_position.position_id
                                                                        FROM `tbl_toolbox_talks_participants`
                                                                        INNER JOIN `tbl_employees`  ON  tbl_toolbox_talks_participants.employee_id  =  tbl_employees.employee_id  
                                                                        INNER JOIN `tbl_position` ON tbl_employees.position = tbl_position.position_id 
                                                                        INNER JOIN  `tbl_toolbox_talks` ON tbl_toolbox_talks_participants.tbt_id = tbl_toolbox_talks.tbt_id
                                                                        WHERE  tbl_employees.position = $position_id 
                                                                                   AND tbl_toolbox_talks.is_deleted = 0 
                                                                                   AND tbl_toolbox_talks.tbt_type = $dept  
                                                                                   AND YEAR ( tbl_toolbox_talks.date_conducted ) = $year AND  MONTH ( tbl_toolbox_talks.date_conducted ) = $month
                                                                        GROUP BY  DAY(date_conducted) ");
                        }
                        $get_position_results = $get_records_per_position_qry->fetchAll();
                        //  get all records per position
                        $counter = $counter+1;
                        $total_days = 0;
                        $total_time = 0;
                        for ($i = 1; $i <= 31; $i++) {
                            $td_display = '<td>0</td>';
                            $time_per_day = 0;
                            foreach($get_position_results as $item){
                                $get_day = explode("-",$item['date_conducted']);
                                $get_day = explode(" ",$get_day[2]);
                                $get_calendar_day = $get_day[0];
                                if (sprintf("%02d",$i) == $get_calendar_day){
                                    $td_display = '<td class="bg-danger-light ">'.$item['day_per_participants'].'</td>';
                                    $total_days = $total_days+ $item['day_per_participants'];
                                    $total_time = $total_time+ $item['time_per_participant'];
                                    $time_per_day = $item['time_per_participant'];
                                    break;
                                }
                            }
                            echo $td_display;
                            $over_all_total[$i-1] = $over_all_total[$i-1] + $time_per_day;
                        }
                        $over_all_total_time = $over_all_total_time + $total_time;
                        $over_all_total_days = $over_all_total_days + $total_days;
                        ?>
                        <td class="d-none d-sm-table-cell">
                            <span class="badge badge-danger"><?=$total_days?></span>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <span class="badge badge-danger"><?=$total_time?></span>
                        </td>
                    </tr>

                    <php $index++; ?>

                    <?php
                    endforeach;
                    ?>
                        <tr class="bg-success-light ">
                            <th class="text-center" scope="row"><?=$counter+1?></th>
                            <td>Total Time</td>
                            <?php
                                for ($i = 0; $i < 31; $i++){
                                    echo '<td>'.$over_all_total[$i].'</td>';
                                }
                            ?>
                            <td> <span ><?=$over_all_total_days?></span></td>
                            <td> <span ><?=$over_all_total_time?></span></td>
                        </tr>
                    </tbody class="text-center">
                </table>
            </div>
            <!--          table responsive -->

        </div>
        </div>
  <?php

    } catch (Exception $e) {
        echo $e;
    }
}


if (isset($_POST['add_new_item'])){

    try {
        $days_civils = $_POST['total_days_civils'];
        $days_mechanicals = $_POST['total_days_mechanicals'];
        $days_electricals = $_POST['total_days_electricals'];
        $days_camps = $_POST['total_days_camps'];
        $days_office = $_POST['total_days_office'];
        $hours_civils  = $_POST['total_hours_civils'];
        $hours_mechanicals  = $_POST['total_hours_mechanicals'];
        $hours_electricals  = $_POST['total_hours_electricals'];
        $hours_camps  = $_POST['total_hours_camps'];
        $hours_office  = $_POST['total_hours_office'];
        $year = $_POST['select_year'];

        $totals_days = $days_civils + $days_electricals + $days_mechanicals+ $days_camps + $days_office;
        $totals_hours = $hours_civils + $hours_electricals + $hours_mechanicals + $days_camps + $days_office;
        $add_itd_qry = $conn->prepare("INSERT INTO 
                                                    tbl_tds
                                                    (`total_days_civils`,
                                                    `total_days_mechanicals`, 
                                                    `total_days_electricals`,
                                                    `total_days_camps`,
                                                    `total_days_office`,
                                                    `total_hours_civils`,
                                                    `total_hours_mechanicals`,
                                                    `total_hours_electricals`,
                                                    `total_hours_camps`,
                                                    `total_hours_office`,
                                                    `totals_days`,
                                                    `totals_hours`,
                                                    `itd_year`
                                                    )
                                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $add_itd_qry->execute([$days_civils ,
                                $days_mechanicals,
                                $days_electricals ,
                                $days_camps ,
                                $days_office,
                                $hours_civils  ,
                                $hours_mechanicals,
                                $hours_electricals,
                                $hours_camps,
                                $hours_office,
                                $totals_days,
                                $totals_hours,
                                $year
                                ]);
        echo "ITD Successfully Added";
    } catch (Exception $e){
        echo $e;
    }
    ?>
    <?php
}

if (isset($_POST['generate_report2_chart'])) {
        $month = sprintf("%02d",$_POST['month']);
        $year = $_POST['year'];


    try {
        $get_position =  $conn->query("SELECT `position`,`position_id`  FROM `tbl_position` WHERE is_deleted = 0");
        $get_all_position = $get_position->fetchAll();
        foreach($get_all_position as $data){
            $position[] = $data['position'];
        }

        $arr = [];
        foreach($get_all_position as $position) { // LOOP THROUGH POSITIONS

            $position_id =  $position['position_id'];
            $get_records_per_position_qry = $conn->query("
                                                                    SELECT `date_conducted`,
                                                                    count(tbl_toolbox_talks_participants.tbt_id) AS day_per_participants,
                                                                    sum(tbl_toolbox_talks_participants.time) AS time_per_participant,       
                                                                    tbl_position.position, users.user_id, 
                                                                    tbl_position.position_id
                                                                    FROM `tbl_position`
                                                                    INNER JOIN `users` ON tbl_position.position_id = users.position 
                                                                    INNER JOIN `tbl_toolbox_talks_participants`  ON users.user_id = tbl_toolbox_talks_participants.user_id           
                                                                    INNER JOIN  `tbl_toolbox_talks` ON tbl_toolbox_talks_participants.tbt_id = tbl_toolbox_talks.tbt_id
                                                                    WHERE  users.position = $position_id AND
                                                                    YEAR ( tbl_toolbox_talks.date_conducted ) = $year AND  MONTH ( tbl_toolbox_talks.date_conducted ) = $month
                                                                    GROUP BY  DATE_FORMAT(date_conducted, '%D')");
            $get_position_results = $get_records_per_position_qry->fetchAll();

            $over_all_total_participants = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0  ); // LOOP 31 DAYS THEN GET THE DAY WITH A VALUE
            for ($i = 1; $i <= 31; $i++) {
                $td_display = 0;
                $time_per_day = 0;
                $participants_per_day = 0;
                foreach($get_position_results as $result){
                    $get_day = explode("-",$result['date_conducted']);
                    $get_day = explode(" ",$get_day[2]);
                    $get_calendar_day = $get_day[0];

                    if (sprintf("%02d",$i) == $get_calendar_day){
                        $td_display = '<td class="bg-danger-light ">'.$result['day_per_participants'].'</td>';
//                                $total_days = $total_days+ $result['day_per_participants'];
//                                $total_time = $total_time+ $result['time_per_participant'];
                        $time_per_day = $result['time_per_participant'];
                        $participants_per_day = $result['day_per_participants'];
                        break;
                    }
                }
                $over_all_total_participants[$i-1] = $over_all_total_participants[$i-1] + $participants_per_day;
            }

            $r = rand(1,100);
            $g = rand(50,150);
            $b = rand(100,255);
            $arr[] = array(
                'label' => $position['position'],
                'backgroundColor'=> 'rgb('.$r.','.$g.','.$b.')',
                'borderColor' => 'rgb(111, 99, 132)',
                'barThickness' => 30,
                'maxBarThickness' => 50,
                'minBarLength' => 5,
                'barPercentage' => 0.5,
                'data' => $over_all_total_participants,
            );//assign each sub-array to the newly created array
        }
        // TRIM DOUBLE QUOTES
        $data_sets =  str_replace(['
            "label"',
            '"backgroundColor"',
            '"borderColor"',
            '"barThickness"',
            '"maxBarThickness"',
            '"minBarLength"',
            '"barPercentage"',

            '"data"'],
            ["label",
            "backgroundColor",
            "borderColor",
            "barThickness",
            "maxBarThickness",
            "minBarLength",
            "barPercentage",
            "data",""],json_encode($arr));

        echo json_encode($arr);
?>

<?php
    } catch (Exception $e){
        echo $e;
    }

}

if (isset($_POST['load_chart'])){
        // subject to changes , follow code structure in incident_ajax
    $year = $_POST['year'];
    $select_month = $_POST['select_month'];
//    echo $select_month;
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
                                        WHERE  tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = $year AND MONTH(date_conducted) <= $select_month 
                                        GROUP BY date_format(date_conducted, '%M') ORDER BY `date_conducted`");
    $get_month_itds = $get_itds_month_qry->fetchAll();

    $get_itds_table_data_qry = $conn->query("SELECT  COUNT(*), MONTH(date_conducted)  as itd_month,
                                        COUNT('tbtp_id') as totals_days,
                                        SUM(time) as total_itd,
                                        SUM( case when tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = $year AND MONTH(date_conducted) = $select_month then time else 0 end) as total_hours_selected_month,
                                        COUNT( case when tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = $year AND MONTH(date_conducted) = $select_month then tbtp_id else 0 end) as total_participants_per_month
                                        FROM tbl_toolbox_talks 
                                        INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                        WHERE  tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) <= $year AND MONTH(date_conducted) <= $select_month 
                                        ORDER BY `date_conducted`");
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
        '"data"'],
        ["label",
            "backgroundColor",
            "borderColor",
            "barThickness",
            "maxBarThickness",
            "data",""],json_encode($arr));

    $data_sets2 =  str_replace([
        '"label"',
        '"backgroundColor"',
        '"borderColor"',
        '"barThickness"',
        '"maxBarThickness"',

        '"data"'],
        ["label",
            "backgroundColor",
            "borderColor",
            "barThickness",
            "maxBarThickness",
            "data",""],json_encode($arr2));
    $data_sets3 =  str_replace([
        '"label"',
        '"backgroundColor"',
        '"borderColor"',
        '"data"'],
        ["label",
            "backgroundColor",
            "borderColor",
            "data",""],json_encode($arr3));

    $data_sets4=  str_replace([
        '"label"',
        '"backgroundColor"',
        '"borderColor"',
        '"data"'],
        ["label",
            "backgroundColor",
            "borderColor",
            "data",""],json_encode($arr4));

    $data_sets5=  str_replace([
        '"label"',
        '"backgroundColor"',
        '"borderColor"',
        '"data"'],
        ["label",
            "backgroundColor",
            "borderColor",
            "data",""],json_encode($arr5));

    $data_sets6=  str_replace([
        '"label"',
        '"backgroundColor"',
        '"borderColor"',
        '"data"'],
        ["label",
            "backgroundColor",
            "borderColor",
            "data",""],json_encode($arr6));

    $data_sets7=  str_replace([
        '"label"',
        '"backgroundColor"',
        '"borderColor"',
        '"data"'],
        ["label",
            "backgroundColor",
            "borderColor",
            "data",""],json_encode($arr7));
    $data_sets8=  str_replace([
        '"label"',
        '"backgroundColor"',
        '"borderColor"',

        '"data"'],
        ["label",
            "backgroundColor",
            "borderColor",
            "data",""],json_encode($arr8));

    $div_space = ' <div class="col-sm-12 d-none d-print-block p-200" >
                        <span class=" "></span>
                   </div>';
    ?>
    <br>
    <br>

    <h3 class="text-center text-muted"> Monthly Summary Man Hours Report (<?=$year?>)</h3>
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
        <div class="col-sm-12 mb-20">
            <canvas id="myChart7" height="100"></canvas>
        </div>
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

$conn = null;