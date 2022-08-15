<?php
require_once '../../colors.php';
include '../../conn.php';
$session_user_id = $_POST['session_user_id'];
$session_sub_id = $_POST['session_sub_id'];

 $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec");
if(isset($_POST['remove_incident'])) {
    $incident_id = $_POST['delete_incident_id'];
    try {
        $delete_incident = $conn->query("UPDATE tbl_first_aid SET is_deleted = 1 WHERE fa_id = $incident_id; ");
//        echo "Deleted Incident Successfully";
    } catch(Exception $e) {
        echo $e;
    }
}
if (isset($_POST['get_injured_info'])){
    try {
        $date = date('Y-m-d');

        $injured_id = $_POST['injured_id'];
        $employee_qry = $conn->query("SELECT TIMESTAMPDIFF(YEAR, `birth_date`, CURDATE())  AS  age,company ,tbl_position.position FROM tbl_employees 
                                               INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id
                                               WHERE employee_id = $injured_id");

        $employee = $employee_qry->fetch();
        $age = $employee['age'];
        $position = $employee['position'];
        $company = $employee['company'];
        $return_arr[] = array("age" => $age,
            "position" => $position,
            "company" => $company);
        echo json_encode($return_arr);
    } catch (Exception $e) {
        echo $e;
    }
}
if (isset($_POST['load_all_incidents'])){
    try {
        $get_incident_qry = $conn->query("SELECT 
                                                        `fa_id`,
                                                        `location`,
                                                        `nature`,
                                                        `injured_id`,
                                                        `engineer_id`,
                                                        `severity`,
                                                        `age`,
                                                        `treatment`,
                                                        `nurse_findings`,
                                                        `lti_injured`,
                                                        `incident_type`,
                                                        `incident`,
                                                        `root_causes`,
                                                        `date_created`,
                                                        `first_aider_id`,
                                                        `cause`,
                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = engineer_id) as engineer_name,
                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = injured_id) as injured_name,
                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = first_aider_id) as first_aider_name
                                                    FROM tbl_first_aid 
                                                    INNER JOIN tbl_incident ON tbl_first_aid.incident_type = tbl_incident.incident_id
                                                    INNER JOIN tbl_root_causes ON tbl_first_aid.root_causes = tbl_root_causes.cause_id
                                                    WHERE tbl_first_aid.is_deleted=  0 AND emp_id = $session_user_id
                                                     ");
            $get_incident = $get_incident_qry->fetchAll();
    } catch ( Exception $e) {
        echo $e;
    }

    ?>
    <table width="2000px" class="table table-bordered table-striped table-vcenter table-sm js-dataTable-full-pagination table-responsive-lg">
        <thead>
        <tr class="text-center text-white bg-primary">
            <td>ID</td>
            <td>Name of the Injured</td>
            <td>Age</td>
            <td>Location</td>
<!--            <td>Nature</td>-->
            <td>Engineer Name</td>
            <td>First Aider Name</td>
<!--            <td>Severity</td>-->
            <td>Nurse Findings</td>
            <td>First Aid Given</td>
<!--            <td>Incident Type</td>-->
<!--            <td>Root Cause</td>-->
<!--            <td>Date of Incident</td>-->
            <td>Action</td>
        </tr>
        </thead>
        <tbody id="incident_body">
        <?php foreach($get_incident as $incident): ?>
            <tr>
                <td><?=$incident['fa_id'] ?></td>
                <td><?=$incident['injured_name'] ?> </td>
                <td class="text-center"><?=$incident['age'] ?></td>
                <td class="text-center"><?=$incident['location'] ?></td>
<!--                <td>--><?//=$incident['nature'] ?><!--</td>-->
                <td><?=$incident['engineer_name'] ?></td>
                <td><?=$incident['first_aider_name'] ?></td>
<!--                <td>--><?//=$incident['severity'] ?><!--</td>-->
                <td><?=$incident['nurse_findings'] ?></td>
                <td><?=$incident['treatment'] ?></td>
<!--                <td>--><?//=$incident['incident'] ?><!--</td>-->
<!--                <td>--><?//=$incident['cause'] ?><!--</td>-->
<!--                <td>--><?//=$incident['date_created'] ?><!--</td>-->
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm" data-toggle="tooltip" title="View Incident" onclick="load_incident_details(<?=$incident['fa_id']?>)">
                            <i class="fa fa-table"></i>
                        </button>
                    </div>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    <?php
}
if (isset($_POST['load_incident'])){
    $fa_id = $_POST['fa_id'];
    try {

        $get_incident_qry = $conn->query("SELECT 
                                                        `sex`,
                                                        `company_name`,
                                                        `Remarks`,
                                                        tbl_position.position,
                                                        `fa_id`,
                                                        `location`,
                                                        tbl_nature.nature,
                                                        `injured_id`,
                                                        `engineer_id`,
                                                        `severity`,
                                                        `leading_indicator`,
                                                        `indicator`,
                                                        `site_name`,
                                                        `mechanism`,
                                                        `mech_description`,
                                                        `age`,
                                                        `nurse_findings`,
                                                        `lti_injured`,
                                                        `incident_type`,
                                                        `root_causes`,
                                                        `incident`,
                                                        `cause`,
                                                        tbl_first_aid.date_created,
                                                        `first_aider_id`,
                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = engineer_id) as engineer_name,
                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = injured_id) as injured_name,                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = injured_id) as injured_name,
                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = first_aider_id) as first_aider_name
                                                    FROM tbl_first_aid 
                                                    INNER JOIN tbl_employees ON tbl_first_aid.emp_id = tbl_employees.employee_id
                                                    INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id
                                                    INNER JOIN users ON tbl_employees.employee_id = users.emp_id                                                      
                                                    INNER JOIN tbl_subscribers ON users.subscriber_id = tbl_subscribers.subscriber_id    
                                                    INNER JOIN tbl_nature ON tbl_first_aid.nature = tbl_nature.nature_id
                                                    INNER JOIN tbl_indicators ON tbl_first_aid.leading_indicator = tbl_indicators.indicator_id
                                                    INNER JOIN tbl_mechanisms ON tbl_first_aid.mechanism = tbl_mechanisms.mech_id
                                                    INNER JOIN tbl_root_causes ON tbl_first_aid.root_causes = tbl_root_causes.cause_id
                                                    INNER JOIN tbl_incident ON tbl_first_aid.incident_type = tbl_incident.incident_id
                                                    WHERE tbl_first_aid.is_deleted=  0 AND tbl_first_aid.emp_id = $session_user_id AND fa_id = $fa_id");
        $get_incident = $get_incident_qry->fetch();

        $get_eng_qry = $conn->query("SELECT *,tbl_position.position AS emp_position FROM tbl_employees 
                                                  INNER JOIN tbl_position ON  tbl_employees.position = tbl_position.position_id
                                                  WHERE tbl_position.position LIKE '%engineer%' AND tbl_employees.is_deleted = 0 ORDER BY firstname  ASC");

        $engineer_list = $get_eng_qry->fetchAll();
        $root_causes = array("Use of Tools Equipment,Materials and Products", "Intentional/Lack of Awareness/Behaviors", "Protective Systems","Integrity of Tools/PLan/Equipment, Material", "Workplace Hazards", "Organizational","Other");
        $incident_type =  array("FAT","LTC", "RWC", "MTC", "FAC", "NM", "PD","TRAF","FIRE","ENV");

        $fa_id = $get_incident['fa_id'];
        $get_body_parts_qry = $conn->query("SELECT tbl_body_parts.part_id, part_name FROM tbl_body_parts_injured 
                                                     INNER JOIN tbl_body_parts ON tbl_body_parts_injured.part_id = tbl_body_parts.part_id WHERE fa_id = $fa_id");
        $get_body_parts = $get_body_parts_qry->fetchAll();

        $get_equipments_qry = $conn->query("SELECT * FROM tbl_equipment INNER JOIN tbl_fa_equipment ON tbl_equipment.equipment_id = tbl_fa_equipment.equip_id WHERE fa_id = $fa_id");
        $get_equipments = $get_equipments_qry->fetchAll();
        $body_parts = '';
        $equipments = '';
        foreach ($get_body_parts as $body_part){
            $body_parts = $body_parts." * ".$body_part['part_name'];
        }
        foreach($get_equipments as $equipment){
            $equipments = $equipments." * ".$equipment['equipment_description'];
        }
        $severity =  array("Low","Medium","High","Extreme");
        $severity_color = array("text-success","text-warning","text-gd-sun","text-danger");
    } catch (Exception $e){
        echo $e;
    }
    ?>
    <div class="content" >
        <div class="row justify-content-center">
            <div class="col-lg-6 col-xl-4 col-md-6">
                <h3>Personal Info</h3>
                <table class="table table-borderless">
                    <tr>
                        <td class="text-left" width="20%">Name of Injured</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['injured_name']?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">Position</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['position']?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">Age</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['age']?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">Company</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['company_name']?>"></td>
                    </tr>
                </table>
                <h3>Injury Details</h3>
                <table class="table table-borderless">
                    <tr>
                        <td width="20%">Body Part</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$body_parts?>"></td>
                    </tr>
                    <tr>
                        <td width="20%">Nature of The Injury</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['nature']?>"></td>
                    </tr>
                    <tr>
                        <td width="20%">Leading Indicator</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['indicator']?>"></td>
                    </tr>
                    <tr>
                        <td width="20%">Mechanism of Injury</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['mech_description']?>"></td>
                    </tr>
                    <tr>
                        <td width="20%">Severity</td>
                        <td><input class="form form-control bg-white <?=$severity_color[$get_incident['severity']-1]?>" readonly type="text" value="<?=$severity[$get_incident['severity']-1]?>"></td>
                    </tr>
                    <tr>
                        <td width="20%">Equiptments Involved</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$equipments?>"></td>
                    </tr>
                </table>
            </div>
            <div class="col-lg-6 col-xl-4 col-md-6">
                <h3 class="text-white">More Details</h3>
                <table class="table table-borderless">
                    <tr>
                        <td class="text-left" width="20%">Date Of Incident</td>
                        <td><input class="form form-control bg-white " readonly type="text" value="<?=$get_incident['date_created']?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">Location</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['location']?>"></td>
                    </tr>
                    <!--                    <tr>-->
                    <!--                        <td class="text-left">Nature</td>-->
                    <!--                        <td><input class="form form-control bg-white" readonly type="text" value="--><?//=$get_incident['nature']?><!--"></td>-->
                    <!--                    </tr>-->
                    <tr>
                        <td class="text-left">Engineer Assigned</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['engineer_name']?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">First Aider</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['first_aider_name']?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">Nurse Findings</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['nurse_findings']?>"></td>
                    </tr>

                    <tr>
                        <td class="text-left">LTI Injured</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['lti_injured']?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">Incident Type</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['incident']?>"></td>
                    </tr>
                    <tr>
                        <td class="text-left">Root Causes</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['cause']?>"></td>
                    </tr>
                    <tr>
                        <td width="20%">Site Name</td>
                        <td><input class="form form-control bg-white" readonly type="text" value="<?=$get_incident['site_name']?>"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php
}
// add incident
if(isset($_POST['injured'])){
    $site_engineer = $_POST['site_engineer'];
    $injured = $_POST['injured'];
    $location = $_POST['location'];
    $age = $_POST['age'];
    $nurse_findings = $_POST['nurse_findings'];
    $treatment = $_POST['treatment'];
    $nature = $_POST['nature'];
    $severity = $_POST['severity'];
    $first_aider = $_POST['first_aider'];
    $lti_injured = $_POST['lti_injured'];
    $incident_type  =$_POST['incident_type'];
    $root_causes = $_POST['root_causes'];
    $date = date('Y-m-d H:i:s');
    $body_parts = $_POST['body_parts'];
    $remarks = $_POST['remarks'];
    $leading_indicator = $_POST['leading_indicator'];
    $mechanism = $_POST['mechanism'];
    $site_name = $_POST['site_name'];
    $equipments = $_POST['equipments'];
    try {
        if ($incident_type == "FAT"){
            $lti_injured = 0;
        }
        $add_incident = $conn->prepare("INSERT INTO `tbl_first_aid` (
                                                                        `treatment`,
                                                                        `site_name`,
                                                                        `mechanism`,
                                                                        `leading_indicator`,
                                                                        `injured_id`,
                                                                        `emp_id`,
                                                                        `engineer_id`,
                                                                       `location`,
                                                                        `age`,
                                                                        `nurse_findings`,
                                                                        `nature`,
                                                                        `severity`,
                                                                         `first_aider_id`,
                                                                        `lti_injured`,
                                                                        `incident_type`,
                                                                        `root_causes`,
                                                                        `date_created`,
                                                                        `is_deleted`,
                                                                        `remarks`)
                                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $add_incident->execute([$treatment,$site_name,$mechanism,$leading_indicator,$injured, $session_user_id, $site_engineer,$location, $age,$nurse_findings,$nature,$severity,$first_aider,$lti_injured,$incident_type,$root_causes,$date,0,$remarks]);
        echo "SUCCESSFULLY REPORTED INCIDENT";
        $fa_id = $conn->lastInsertId();
        foreach ($body_parts as $part){
            $add_body_parts_qry = $conn->prepare("INSERT INTO tbl_body_parts_injured (`fa_id`,`part_id`,`is_deleted`) VALUES (?,?,?)");
            $add_body_parts_qry->execute([$fa_id,$part,0]);
        }
        foreach ($equipments as $equipment){
            $add_equipments_qry = $conn->prepare("INSERT INTO tbl_fa_equipment (`fa_id`,`equip_id`,`is_deleted`) VALUES (?,?,?)");
            $add_equipments_qry->execute([$fa_id,$equipment,0]);
        }
    } catch (Exception $e){
        echo $e;
    }
}
if (isset($_POST['itd_request'])){
     try {
        $year = $_POST['year'];
        $select_month = $_POST['select_month'];
        $date1 = date('Y-m-d', strtotime(date($year."-".$select_month)));
        $get_itds_table_data_qry = $conn->query("
                                            SELECT  COUNT(*), MONTH(date_conducted)  as itd_month,
                                                COUNT('tbtp_id') as totals_days,
                                                SUM(time) as total_itd,
                                                SUM( case when tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = $year AND MONTH(date_conducted) = $select_month then time else 0 end) as total_hours_selected_month,
                                                COUNT( case when tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = $year AND MONTH(date_conducted) = $select_month then tbtp_id else 0 end) as total_participants_per_month
                                            FROM tbl_toolbox_talks 
                                                INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                            WHERE  tbl_toolbox_talks.is_deleted = 0 AND date_format(date_conducted, '%Y-%m')  <=  date_format('$date1', '%Y-%m') 
                                                ORDER BY `date_conducted`");
        $get_itds_table_data = $get_itds_table_data_qry->fetchAll();

        $get_itds_manual_qry2 = $conn->query("SELECT  SUM(total_hours_civils + total_hours_office + total_hours_electricals + total_hours_mechanicals + total_hours_camps) as total_itds
                                            FROM tbl_tds WHERE  itd_year <= $year AND is_deleted = 0");
        $get_itds_manual2 = $get_itds_manual_qry2->fetchAll();
        $get_contractors_qry = $conn->query(" 
                                            SELECT `date_conducted`,
                                                COUNT(case when company_type='main contractor' then tbtp_id else null end) as total_main_contractors,
                                                COUNT(case when company_type='sub contractor' then tbtp_id else null end) as total_sub_contractors
                                            FROM `tbl_toolbox_talks`
                                                INNER JOIN `tbl_toolbox_talks_participants`  ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id           
                                                INNER JOIN  `tbl_employees` ON tbl_toolbox_talks_participants.employee_id = tbl_employees.employee_id
                                            WHERE  YEAR ( tbl_toolbox_talks.date_conducted ) = $year 
                                                AND MONTH ( tbl_toolbox_talks.date_conducted ) = $select_month AND tbl_toolbox_talks.is_deleted = 0");
        $get_contractors = $get_contractors_qry->fetch();
        $lti_injured_qry = $conn->query("SELECT COALESCE(sum(lti_injured), 0) as total_lti_injured FROM tbl_first_aid WHERE date_format(date_created, '%Y-%m')  <=  date_format('$date1', '%Y-%m')  AND is_deleted = 0");
        $lti_injured2 = $lti_injured_qry->fetch();
        // only the selected month and year
        $training_hrs_monthly_qry = $conn->query("SELECT  COALESCE(sum(training_hrs),0) as total_hours
                                                           FROM `tbl_training_trainees` 
                                                               INNER JOIN tbl_trainings ON tbl_training_trainees.training_id = tbl_trainings.training_id
                                                           WHERE YEAR(training_date) = $year AND MONTH (training_date) = $select_month AND tbl_trainings.is_deleted = 0");
        $training_hrs_monthly = $training_hrs_monthly_qry->fetch();
        // accumulated data from start until the selected data
        $training_hrs_itd_qry = $conn->query( "SELECT  COALESCE(sum(training_hrs),0) as total_training_itd
                                                        FROM `tbl_training_trainees`
                                                            INNER JOIN tbl_trainings ON tbl_training_trainees.training_id = tbl_trainings.training_id
                                                        WHERE date_format(training_date, '%Y-%m')  <=  date_format('$date1', '%Y-%m') AND tbl_trainings.is_deleted = 0");
        $training_hrs_itd = $training_hrs_itd_qry->fetch();

        $get_tbt_qry = $conn->query("SELECT COUNT(`tbt_id`) as total_tbt_this_month FROM tbl_toolbox_talks 
                                              WHERE YEAR(date_conducted) = $year AND MONTH (date_conducted) = $select_month AND tbl_toolbox_talks.is_deleted = 0");
        $get_tbt = $get_tbt_qry->fetch();

        $get_tbt_itd_qry = $conn->query("SELECT COUNT(`tbt_id`) as total_tbt_itd FROM tbl_toolbox_talks
                                                  WHERE YEAR(date_conducted) <= $year AND MONTH (date_conducted) <= $select_month AND tbl_toolbox_talks.is_deleted = 0 ");
        $get_tbt_itd = $get_tbt_itd_qry->fetch();

        $monthly_days = cal_days_in_month(CAL_GREGORIAN,$select_month,$year);
     } catch (Exception $e) {
        echo $e;
     }
    ?>
    <div class="row gutters-tiny d-print-none">
        <div class="col-md-6 col-xl-3 col-lg-6"   >
            <a class="block block-transparent" href="javascript:void(0)">
                <div class="block-content block-content-full bg-success" style=" max-height: 280px">
                    <div class="py-20 text-center">
                        <div class="mb-20">
                            <i class="si si-clock fa-4x text-info-light"></i>
                        </div>
                        <div class="font-size-sm font-w200 text-uppercase text-info-light  ">
                            <table class="table table-bordered text-white " style="table-layout:fixed; width:100%;">
                                <tr>
                                    <td colspan="2">Manhours Worked</td>
                                </tr>
                                <tr>
                                    <td>Month</td>
                                    <td>Itd</td>
<!--                                    <td>LTI Free</td>-->
                                </tr>
                                <tr>

                                    <td><?php echo $mp =  number_format($get_itds_table_data[0]['total_hours_selected_month'])?></td>
                                    <td><?php echo number_format($get_itds_table_data[0]['total_itd'] + $get_itds_manual2[0]['total_itds'])?></td>
                                    <?php $lti =  $lti_injured2['total_lti_injured']; ?>
<!--                                    <td>--><?//=(($mp == 0 || $lti == 0)? 0: $ltifr = ($lti* 1000000)  / $mp);?><!--</td>-->
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3 col-lg-6">
            <a class="block block-transparent" href="javascript:void(0)">
                <div class="block-content block-content-full bg-primary " style=" max-height: 280px">
                    <div class="py-20 text-center">
                        <div class="mb-20">
                            <i class="si si-user fa-4x text-info-light"></i>
                        </div>
                        <div class="font-size-sm font-w200 text-uppercase text-info-light ">
                            <table class="table  table-bordered text-white " style="table-layout:fixed; width:100%;">
                                <tr>
                                    <td colspan="2">Manpower</td>
                                </tr>
                                <tr style="word-wrap:break-word;">
                                    <td>Contractor</td>
                                    <td >Subcontractor<span ></span></td>
                                </tr>
                                <tr>
                                    <td><?=(($get_contractors_qry->rowCOUNT())?ceil($get_contractors['total_main_contractors']/$monthly_days) :0)?></td>
                                    <td><?=(($get_contractors_qry->rowCOUNT())?ceil($get_contractors['total_sub_contractors']/$monthly_days) :0)?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3 col-lg-6" >
            <a class="block block-transparent" href="javascript:void(0)" >
                <div class="block-content block-content-full bg-warning" style=" max-height: 280px">
                    <div class="py-20 text-center">
                        <div class="mb-20">
                            <i class="si si-clock fa-4x text-info-light"></i>
                        </div>
                        <div class="font-size-sm font-w200 text-uppercase text-info-light ">
                            <table class="table table table-bordered text-white" style="table-layout:fixed; width:100%;">
                                <tr>
                                    <td colspan="2">HSE training hours</td>
                                </tr>
                                <tr>
                                    <td>This month</td>
                                    <td>ITD</td>
                                </tr>
                                <tr>
                                    <td><?=(($training_hrs_monthly_qry->rowCOUNT())?$training_hrs_monthly['total_hours'] :0)?></td>
                                    <td><?=(($training_hrs_itd_qry->rowCOUNT())?$training_hrs_itd['total_training_itd'] :0)?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6 col-xl-3 col-lg-6">
            <a class="block block-transparent" href="javascript:void(0)">
                <div class="block-content block-content-full bg-danger" style=" max-height: 280px">
                    <div class="py-20 text-center">
                        <div class="mb-20">
                            <i class="fa fa-dropbox fa-4x text-info-light"></i>
                        </div>
                        <div class="font-size-sm font-w200 text-uppercase text-info-light " >
                            <table class="table table table-bordered text-white" >
                                <tr>
                                    <td colspan="2">Toolbox Talk</td>
                                </tr>
                                <tr>
                                    <td>This Month</td>
                                    <td>ITD</td>
                                </tr>
                                <tr>
                                    <td><?=(($get_tbt_qry->rowCOUNT())?$get_tbt['total_tbt_this_month'] :0)?></td>
                                    <td><?=(($get_tbt_itd_qry->rowCOUNT())?$get_tbt_itd['total_tbt_itd'] :0)?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <!-- END Row #5 -->
    </div>
    <?php
}

if (isset($_POST['incident_report'])){
try {
    $year = $_POST['year'];
    $leading_indicators_qry = $conn->query("SELECT `indicator`,
                                                     COALESCE(COUNT(CASE WHEN tbl_first_aid.is_deleted = 0 
                                                                    AND YEAR(tbl_first_aid.date_created) = $year 
                                                                    AND emp_id = $session_user_id
                                                                    then tbl_first_aid.leading_indicator else null end ),0) 
                                                                        as number_per_indicator
                                                     FROM tbl_indicators 
                                                     LEFT JOIN tbl_first_aid ON tbl_indicators.indicator_id = tbl_first_aid.leading_indicator
                                                     WHERE tbl_indicators.is_deleted = 0
                                                     GROUP BY indicator_id ");
    $leading_indicators = $leading_indicators_qry->fetchAll();

    $severity_qry =  $conn->query("SELECT `severity_id`,tbl_severity.severity, 
                                            COALESCE(COUNT(CASE WHEN tbl_first_aid.is_deleted = 0  
                                                           AND YEAR(tbl_first_aid.date_created) = $year 
                                                           AND emp_id = $session_user_id                               
                                                           then tbl_first_aid.severity else null end),0) as number_per_severity
                                            FROM tbl_severity 
                                            LEFT JOIN tbl_first_aid ON tbl_severity.severity_id = tbl_first_aid.severity
                                            WHERE tbl_severity.is_deleted =0
                                            GROUP BY severity_id ");
    $severity = $severity_qry->fetchAll();

    $mechanism_qry =  $conn->query("SELECT `mech_id`,tbl_mechanisms.mech_description, 
                                             COALESCE(COUNT(CASE WHEN tbl_first_aid.is_deleted = 0 
                                                           AND YEAR(tbl_first_aid.date_created) = $year then tbl_first_aid.mechanism else null end ),0) as number_per_mechanism
                                             FROM tbl_mechanisms 
                                             LEFT JOIN tbl_first_aid ON tbl_mechanisms.mech_id = tbl_first_aid.mechanism
                                             WHERE tbl_mechanisms.is_deleted =0
                                             GROUP BY mech_id ");
    $mechanisms = $mechanism_qry->fetchAll();

    $nature_qry =  $conn->query("SELECT tbl_nature.nature, 
                                          COALESCE(COUNT(CASE WHEN tbl_first_aid.is_deleted = 0 
                                                        AND YEAR(tbl_first_aid.date_created) = $year  
                                                        AND emp_id = $session_user_id
                                                        then tbl_first_aid.nature else null end ),0) as number_per_nature
                                          FROM tbl_nature 
                                          LEFT JOIN tbl_first_aid ON tbl_nature.nature_id = tbl_first_aid.nature
                                          WHERE tbl_nature.is_deleted =0 
                                          GROUP BY nature_id ");
    $nature = $nature_qry->fetchAll();

    $get_position_qry = $conn->query("SELECT position_id,tbl_position.position , 
                                               COALESCE(COUNT(CASE WHEN tbl_first_aid.is_deleted = 0 
                                                              AND YEAR(tbl_first_aid.date_created) = $year
                                                              AND emp_id = $session_user_id
                                                              then tbl_first_aid.injured_id else null end ),0) as number_per_position 
                                               FROM tbl_position 
                                               LEFT JOIN tbl_employees ON tbl_position.position_id = tbl_employees.position 
                                               LEFT JOIN tbl_first_aid ON tbl_employees.employee_id = tbl_first_aid.injured_id
                                               WHERE tbl_position.is_deleted = 0 
                                               GROUP BY tbl_position.position_id ");
    $positions = $get_position_qry->fetchAll();


    $get_body_parts_qry = $conn->query("SELECT tbl_body_parts.part_id as part_number,
                                                     (SELECT COUNT(tbl_body_parts_injured.part_id) FROM tbl_body_parts_injured 
                                                     INNER JOIN tbl_first_aid ON tbl_body_parts_injured.fa_id =  tbl_first_aid.fa_id 
                                                     WHERE tbl_body_parts_injured.is_deleted = 0 AND tbl_first_aid.is_deleted=0 AND YEAR(tbl_first_aid.date_created) = $year AND part_id = tbl_body_parts.part_id  ) as number_per_part
                                                    , part_name
                                                 FROM tbl_body_parts 
                                                 LEFT JOIN tbl_body_parts_injured ON tbl_body_parts.part_id = tbl_body_parts_injured.part_id 
                                                 WHERE tbl_body_parts.is_deleted = 0 
                                                 GROUP BY tbl_body_parts.part_id ");
    $body_parts = $get_body_parts_qry->fetchAll();
    $get_equipment_qry = $conn->query("SELECT 
                                                     (SELECT COUNT(tbl_fa_equipment.equip_id) FROM tbl_fa_equipment 
                                                     INNER JOIN tbl_first_aid ON tbl_fa_equipment.fa_id =  tbl_first_aid.fa_id 
                                                     WHERE tbl_fa_equipment.is_deleted = 0 AND tbl_first_aid.is_deleted=0 AND equip_id = tbl_equipment.equipment_id  ) as number_per_equipment, equipment_description
                                                FROM tbl_equipment 
                                                LEFT JOIN tbl_fa_equipment ON tbl_equipment.equipment_id = tbl_fa_equipment.equip_id 
                                                WHERE tbl_equipment.is_deleted = 0 
                                                GROUP BY tbl_equipment.equipment_id ");
    $equipments = $get_equipment_qry->fetchAll();

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


    $root_causes_qry =  $conn->query("SELECT cause_id, cause,
                                               COALESCE(COUNT(CASE WHEN tbl_first_aid.is_deleted = 0 AND YEAR(tbl_first_aid.date_created) = $year 
                                                              AND emp_id = $session_user_id then tbl_first_aid.root_causes else null end ),0) 
                                                              AS number_per_cause
                                               FROM tbl_root_causes 
                                               LEFT JOIN tbl_first_aid ON tbl_root_causes.cause_id = tbl_first_aid.root_causes
                                               WHERE tbl_root_causes.is_deleted =0 
                                               GROUP BY cause_id ");
    $root_causes = $root_causes_qry->fetchAll();

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
<div class="row invisible d-none" data-toggle="appear">
    <div class="col-md-6 col-xl-3">
        <?php
        try {
            $total_employee_qry = $conn->query("SELECT
                                                         COUNT(tbl_employees.employee_id) as total_employee
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
                <div class="font-size-h3 font-w200 text-primary js-COUNT-to-enabled"><?=$total_employee_fetch['total_employee']?></div>
                <div class="font-size-sm font-w200 text-uppercase text-muted">Total Employee</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <?php
        try {
            $total_training_qry = $conn->query("SELECT
                                                                        COUNT(tbl_trainings.training_id) AS total_trainings
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
                <div class="font-size-h3 font-w200 text-earth"><span ><?=$total_training?></span></div>
                <div class="font-size-sm font-w200 text-uppercase text-muted">Total Trainings</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-right mt-15 d-none d-sm-block">
                    <i class="fa fa-wrench fa-2x text-elegance-light"></i>
                </div>
                <div class="font-size-h3 font-w200 text-elegance" data-toggle="COUNTTo" data-speed="1000" data-to="15">0</div>
                <div class="font-size-sm font-w200 text-uppercase text-muted">Total Toolbox Record</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
            <div class="block-content block-content-full clearfix">
                <div class="float-right mt-15 d-none d-sm-block">
                    <i class="fa fa-clipboard fa-2x text-pulse"></i>
                </div>
                <div class="font-size-h3 font-w200 text-pulse" data-toggle="COUNTTo" data-speed="1000" data-to="4252">0</div>
                <div class="font-size-sm font-w200 text-uppercase text-muted">Inventory</div>
            </div>
        </a>
    </div>

    <!-- END Row #1 -->
</div>
<div >
    <div class="row">
        <div class=" col-sm-12 col-lg-12 col-xl-6">
            <div class="block">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3>Incidents Classification 2022</h3>
                    </div>
                    <div class="block-content">
                        <table class="table table-responsive table-bordered  ">
                            <thead class="bg-primary text-center text-white">
                            <th>Month</th>
                            <?php foreach($months as $month):?>
                                <th ><?=$month?></th>
                            <?php endforeach;?>
                            <th class="bg-black-op-5">Total</th>

                            </thead>
                            <tbody class="text-center">
                            <!--                                    monthly incident per type------------------->
                            <?php $i = 1; ?>
                            <?php foreach($incident_type as $type):

                                // get recordable incident
                                $get_monthly_incidents_qry = $conn->query("SELECT COUNT(fa_id) as number_of_incident,
                                                                                    MONTH(date_created)  as incident_month 
                                                                                    FROM tbl_first_aid WHERE incident_type= $i AND YEAR(date_created) = $year  AND emp_id = $session_user_id
                                                                                    GROUP BY MONTH(date_created)" );
                                $monthly_incident = $get_monthly_incidents_qry->fetchAll();
                                $array_monthly_incident = array(0,0,0,0,0,0,0,0,0,0,0,0);
                                foreach ($monthly_incident as $incident){
                                    $array_monthly_incident[$incident['incident_month']-1] = $incident['number_of_incident'];

                                }
                                ?>
                                <tr>
                                    <?php $monthly_total = 0;?>
                                    <td class="<?=(($i < 5)? "bg-danger text-white":"bg-black-op-5")?>"><?=$type?></td>
                                    <?php foreach($array_monthly_incident as $incident_number): ?>
                                        <td class=" <?=(($incident_number==0)?'': 'bg-black text-white') ?>"><?=$incident_number?></td>
                                        <?php $monthly_total = $monthly_total+ $incident_number;?>
                                    <?php endforeach; ?>
                                    <td><?=$monthly_total?></td>
                                </tr>

                                <?php $i++;
                            endforeach;?>

                            <?php
                            $get_monthly_incidents_qry = $conn->query("SELECT COUNT(fa_id) as number_of_incident , 
                                                                                MONTH(date_created)  as incident_month 
                                                                                FROM tbl_first_aid 
                                                                                WHERE YEAR(date_created) = $year  AND emp_id = $session_user_id 
                                                                                GROUP BY MONTH(date_created)" );
                            $monthly_incident = $get_monthly_incidents_qry->fetchAll();
                            $array_monthly_incident = array(0,0,0,0,0,0,0,0,0,0,0,0);
                            foreach ($monthly_incident as $incident){
                                $array_monthly_incident[$incident['incident_month']-1] = $incident['number_of_incident'];
                            }
                            ?>
                            <tr class=" text-black">
                                <td>Total</td>
                                <?php foreach($array_monthly_incident as $monthly_total): ?>
                                    <td ><?=$monthly_total?></td>
                                <?php endforeach; ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12 col-xl-6">
            <div class="block">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3>Root Causes Analysis</h3>
                    </div>
                    <div class="block-content">
                        <table class="table table-bordered  table-responsive">
                            <thead class="bg-primary text-center text-white">
                            <?php foreach($root_causes as $cause):?>
                                <th ><?=$cause['cause']?></th>
                            <?php endforeach;?>
                            </thead>
                            <tbody class="text-center">
                            <tr>
                                <?php $total_cause = 0; ?>
                                <?php foreach($root_causes as $cause):?>
                                    <th ><?=$cause['number_per_cause']?></th>
                                    <?php $total_cause = $total_cause + $cause['number_per_cause']; ?>
                                <?php endforeach;?>
                                <!--                                                <th>--><?//=$total_cause?><!--</th>-->
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3>Leading Indicators</h3>
                    </div>
                    <div class="block-content">
                        <table class="table table-responsive-xl table-bordered ">
                            <thead class="bg-primary text-center text-white">
                            <?php foreach($leading_indicators as $indicator):?>
                                <th ><?=$indicator['indicator']?></th>
                            <?php endforeach;?>
                            </thead>
                            <tbody class="text-center">
                            <!--                                    monthly incident per type------------------->
                            <tr>
                                <?php
                                $i = 1;
                                foreach($leading_indicators as $indicator):
                                    ?>
                                    <td class=" <?=(($indicator['number_per_indicator']==0)?'': 'bg-blue-op 5 text-black') ?>">
                                        <?=$indicator['number_per_indicator'];?>
                                    </td>
                                    <?php $i++; endforeach; ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class=" col-sm-12 col-lg-12 col-xl-6">
            <div class="block">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3>Potential Severity</h3>
                    </div>
                    <div class="block-content">
                        <table class="table  table-bordered  ">
                            <thead class="bg-primary text-center text-white">
                            <?php foreach($severity as $sev):?>
                                <th ><?=$sev['severity']?></th>
                            <?php endforeach;?>
                            <th bgcolor="black">Total</th>
                            </thead>
                            <tbody class="text-center">
                            <tr class=" text-black">
                                <?php $total_sev = 0; ?>
                                <?php foreach($severity as $sev):?>
                                    <th ><?=$sev['number_per_severity']?></th>
                                    <?php $total_sev = $total_sev + $sev['number_per_severity']; ?>
                                <?php endforeach;?>
                                <th><?=$total_sev?></th>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3>Mechanism of Injury</h3>
                    </div>
                    <div class="block-content">
                        <table class="table  table-bordered  ">
                            <thead class="bg-primary text-center text-white">
                            <th>Mechanism</th>
                            <th bgcolor="black">Total </th>
                            </thead>
                            <tbody class="text-center">
                            <?php $total_mech = 0; ?>
                            <?php foreach($mechanisms as $mech):?>
                                <tr >
                                    <th ><?=$mech['mech_description']?></th>
                                    <th ><?=$mech['number_per_mechanism']?></th>
                                    <?php $total_mech = $total_mech + $mech['number_per_mechanism']; ?>
                                </tr>
                            <?php endforeach;?>
                            <tr>
                                <td  class ="text-black bg-black-op-5">Total</td>
                                <td><?=$total_mech?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class=" col-sm-12 col-lg-12 col-xl-6">
            <div class="block">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3>Body Parts Injured</h3>
                    </div>
                    <div class="block-content">
                        <table class="table  table-bordered  ">
                            <thead class="bg-primary text-center text-white">
                            <th>Body Part</th>
                            <th bgcolor="black">Total </th>
                            </thead>
                            <tbody class="text-center">
                            <?php $total_part = 0; ?>
                            <?php foreach($body_parts as $part):?>
                                <tr >
                                    <th ><?=$part['part_name']?></th>
                                    <th ><?=$part['number_per_part']?></th>
                                    <?php $total_part = $total_part + $part['number_per_part']; ?>
                                </tr>
                            <?php endforeach;?>
                            <tr>
                                <td  class ="text-black bg-black-op-5">Total</td>
                                <td><?=$total_part?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class=" col-sm-12 col-lg-12 col-xl-6">
            <div class="block">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3>Nature of Injury</h3>
                    </div>
                    <div class="block-content">
                        <table class="table  table-bordered  ">
                            <thead class="bg-primary text-center text-white">
                            <th>Nature</th>
                            <th bgcolor="black">Total </th>
                            </thead>
                            <tbody class="text-center">
                            <?php $total_nature = 0; ?>
                            <?php foreach($nature as $nat):?>
                                <tr >
                                    <th ><?=$nat['nature']?></th>
                                    <th ><?=$nat['number_per_nature']?></th>
                                    <?php $total_nature = $total_nature + $nat['number_per_nature']; ?>
                                </tr>
                            <?php endforeach;?>
                            <tr>
                                <td  class ="text-black bg-black-op-5">Total</td>
                                <td><?=$total_nature?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3>Equipments Involved</h3>
                    </div>
                    <div class="block-content">
                        <table class="table  table-bordered  ">
                            <thead class="bg-primary text-center text-white">
                            <th>Equipment</th>
                            <th bgcolor="black">Total </th>
                            </thead>
                            <tbody class="text-center">
                            <?php $total_equip = 0; ?>
                            <?php foreach($equipments as $equip):?>
                                <tr>
                                    <th ><?=$equip['equipment_description']?></th>
                                    <th ><?=$equip['number_per_equipment']?></th>
                                    <?php $total_equip = $total_equip + $equip['number_per_equipment']; ?>
                                </tr>
                            <?php endforeach;?>
                            <tr>
                                <td  class ="text-black bg-black-op-5">Total</td>
                                <td><?=$total_equip?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="block">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3>TRIR AND LTIR</h3>
                    </div>
                    <div class="block-content">
                        <table class="table table-responsive table-bordered  ">
                            <thead class="bg-primary text-center text-white">
                            <th>Month</th>
                            <?php foreach($months as $month):?>
                                <th ><?=$month?></th>
                            <?php endforeach;?>
                            <th class="bg-black-op-5">Total</th>
                            </thead>
                            <tbody class="text-center">
                            <!--                                    monthly incident per type------------------->
                            <?php
                            try {
                                $i = 1;
                                $get_monthly_incidents_qry = $conn->query("
                                                                                        SELECT  COUNT(*), MONTH(date_conducted)  as itd_month,date_conducted,
                                                                                        COUNT('tbtp_id') as totals_days,
                                                                                        SUM(time) as total_itd,
                                                                                        SUM( case when tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = $year  then time else 0 end) as total_hours_selected_month,
                                                                                        COUNT( case when tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = $year  then tbtp_id else 0 end) as total_participants_per_month
                                                                                        FROM tbl_toolbox_talks 
                                                                                        INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                                                                        WHERE  tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted)= $year AND user_id = $session_user_id
                                                                                        GROUP BY MONTH (date_conducted)
                                                                                        ORDER BY `date_conducted`");
                                $itds = $get_monthly_incidents_qry->fetchAll();
                                $get_lost_time_qry = $conn->query("SELECT SUM(lti_injured) as lost_time   , MONTH(date_created) as lti_month
                                                                                        FROM tbl_first_aid
                                                                                        WHERE tbl_first_aid.is_deleted = 0  AND YEAR(date_created) = $year AND emp_id = $session_user_id");
                                $lti_injured = $get_lost_time_qry->fetchAll();
                                $recordable_incident_qry = $conn->query("SELECT COUNT(fa_id) as number_per_incident , MONTH(date_created)  as incident_month 
                                                                                  FROM tbl_first_aid 
                                                                                  WHERE  YEAR(date_created) = $year  AND emp_id = $session_user_id
                                                                                  GROUP BY MONTH(date_created)");
                                $recordable_incident = $recordable_incident_qry->fetchAll();

                                $mri = array(0,0,0,0,0,0,0,0,0,0,0,0); // monthly recordable incident
                                foreach ($recordable_incident as $record){
                                    $mri[$record['incident_month']-1] = $record['number_per_incident'];
                                }
                                $monthly_hours_array = array(0,0,0,0,0,0,0,0,0,0,0,0);
                                $monthly_mp_array = array(0,0,0,0,0,0,0,0,0,0,0,0);
                                $monthly_ltir = array(0,0,0,0,0,0,0,0,0,0,0,0);
                                foreach ($itds as $monthly_record){
                                    $monthly_hours_array[$monthly_record['itd_month']-1] = $monthly_record['total_itd']; // total man hours
                                    $monthly_mp_array[$monthly_record['itd_month']-1] = $monthly_record['totals_days']; // total man power

                                }
                                foreach($lti_injured as $lti){
                                    $LTIR =  ($lti['lost_time'] * 1000000) / $monthly_hours_array[$lti['lti_month']-1];
                                    $monthly_ltir[$lti['lti_month']-1] = $LTIR;
                                }
                            } catch (Exception $e) {
                                echo $e;
                            }

                            ?>
                            <tr>
                                <?php $total_man_hours = 0;?>
                                <td >Total Manhours</td>
                                <?php foreach($monthly_hours_array as $monthly_itd): ?>
                                    <td class=" <?=(($monthly_itd==0)?'': 'bg-black text-white') ?>"><?=$monthly_itd?></td>
                                    <?php $total_man_hours = $total_man_hours+ $monthly_itd;?>
                                <?php endforeach; ?>
                                <td><?=$total_man_hours?></td>
                            </tr>
                            <tr>
                                <?php $monthly_total = 0;?>
                                <td >Total Manpower</td>
                                <?php foreach($monthly_mp_array as $monthly_mp): ?>
                                    <td class=" <?=(($monthly_mp==0)?'': 'bg-black text-white') ?>"><?=$monthly_mp?></td>
                                    <?php $monthly_total = $monthly_total+ $monthly_mp;?>
                                <?php endforeach; ?>
                                <td><?=$monthly_total?></td>
                            </tr>
                            <tr>
                                <?php $monthly_total = 0;?>
                                <td >LTIR</td>
                                <?php foreach($monthly_ltir as $ltir): ?>
                                    <td class=" <?=(($ltir==0)?'': 'bg-black text-white') ?>"><?=$ltir?></td>
                                    <?php $monthly_total = $monthly_total+ $ltir;?>
                                <?php endforeach; ?>
                                <td><?=$monthly_total?></td>
                            </tr>
                            <tr>
                                <td >TRIR</td>
                                <?php
                                $monthly_total = 0;
                                $month_index = 0; //
                                foreach($mri as $recordable_incident):
                                    // MRI is the monthly recordable incident
                                    $trir= 0;
                                    if ($recordable_incident >  0){
                                        $total_manhours =  $monthly_hours_array[$month_index] ;
                                        $trir = ($recordable_incident * 1000000) / $total_manhours;
                                        $monthly_total = $monthly_total+ $recordable_incident;
                                    }
                                    $month_index++;
                                    ?>
                                    <td class=" <?=(($trir==0)?'': 'bg-black text-white') ?>"><?=$trir?></td>
                                <?php endforeach; ?>
                                <td><?=$monthly_total?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <div class=" col-sm-12 col-lg-12 col-xl-6">
            <div class="block">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3>Job Description</h3>
                    </div>
                    <div class="block-content">
                        <table class="table  table-bordered  ">
                            <thead class="bg-primary text-center text-white">
                            <th>Job Description</th>
                            <th bgcolor="black">Total </th>
                            </thead>
                            <tbody class="text-center">
                            <?php $total_position = 0; ?>
                            <?php foreach($positions as $pos):?>
                                <tr >
                                    <th ><?=$pos['position']?></th>
                                    <th ><?=$pos['number_per_position']?></th>
                                    <?php $total_position = $total_position + $pos['number_per_position']; ?>
                                </tr>
                            <?php endforeach;?>
                            <tr>
                                <td  class ="text-black bg-black-op-5">Total</td>
                                <td><?=$total_position?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

    <!-- END Main Container -->
</div>
<?php
}
if (isset($_POST['first_aid_chart'])){
    try {
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

    $get_itds_manual_qry2 = $conn->query("SELECT  SUM(total_hours_civils + total_hours_office + total_hours_electricals + total_hours_mechanicals + total_hours_camps) as total_itds
                                        FROM tbl_tds WHERE  itd_year <= $year AND is_deleted = 0");

    $get_itds_manual2 = $get_itds_manual_qry2->fetchAll();


    $get_number_of_people_qry = $conn->query("SELECT  COUNT(DISTINCT tbl_toolbox_talks_participants.employee_id) as number_of_people 
                                        FROM tbl_toolbox_talks 
                                        INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                        WHERE  tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = $year AND MONTH(date_conducted) = $select_month 
                                        ORDER BY `date_conducted`");
    $get_number_of_people = $get_number_of_people_qry->fetchAll();

    $monthly_values_civils = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthly_values_mechanicals = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthly_values_electricals = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthly_values_camps = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthly_values_office = array(0,0,0,0,0,0,0,0,0,0,0,0);
    $monthly_totals_hours = array(0,0,0,0,0,0,0,0,0,0,0,0);

    $merge_result = "";
    if ($year < 2022){
        $get_itds_selected_year_qry = $conn->query("SELECT *, SUM(total_hours_civils + total_hours_office + total_hours_electricals + total_hours_mechanicals + total_hours_camps) 
                                                            as total_itds
                                                            FROM tbl_tds 
                                                            WHERE `itd_year` = $year AND is_deleted = 0");

        $get_itds_selected_year = $get_itds_selected_year_qry->fetchAll();

        $merge_result =  $get_itds_selected_year;

        $monthly_values_civils[11] = $get_itds_selected_year[0]['total_hours_civils'];
        $monthly_values_mechanicals[11] = $get_itds_selected_year[0]['total_hours_mechanicals'];
        $monthly_values_electricals[11] = $get_itds_selected_year[0]['total_hours_electricals'];
        $monthly_values_camps[11] = $get_itds_selected_year[0]['total_hours_camps'];
        $monthly_values_office[11] = $get_itds_selected_year[0]['total_hours_office'];
        $monthly_totals_hours[11] = $get_itds_selected_year[0]['totals_hours'];
    } else {
        $merge_result = array_merge(array_reverse($get_itds_manual),$get_itds_result);
        foreach ($get_month_itds as $result){
            $monthly_values_civils[intval($result['itd_month'])-1] = $result['total_hours_civils'];
            $monthly_values_mechanicals[intval($result['itd_month'])-1] = $result['total_hours_mechanicals'];
            $monthly_values_electricals[intval($result['itd_month'])-1] = $result['total_hours_electricals'];
            $monthly_values_camps[intval($result['itd_month'])-1] = $result['total_hours_camps'];
            $monthly_values_office[intval($result['itd_month'])-1] = $result['total_hours_office'];
            $monthly_totals_hours[intval($result['itd_month'])-1] = $result['totals_hours'];
        }
    }

//=======================================================================================================
//            INCIDENT
//=======================================================================================================
    $get_incident_types= $conn->query("SELECT tbl_incident.incident,incident_id,
                                                COUNT( CASE WHEN tbl_first_aid.is_deleted = 0 AND YEAR(tbl_first_aid.date_created) = $year then incident_type else NULL end) as number_per_incident 
                                                FROM tbl_incident 
                                                LEFT JOIN tbl_first_aid ON tbl_incident.incident_id = tbl_first_aid.incident_type 
                                                WHERE tbl_incident.is_deleted = 0 
                                                GROUP BY tbl_incident.incident_id");
    $incident_types = $get_incident_types->fetchAll();
    $label_incident = array();
    $label_recordable = array();
    $data_incident = array();
    $data_recordable = array();
    foreach ($incident_types as $result){
        array_push($label_incident, $result['incident']);
        $data_incident[] =   intval($result['number_per_incident']);
        if ($result['incident_id'] <= 5 ){
            array_push($label_recordable, $result['incident']);
            $data_recordable[]= intval($result['number_per_incident']);
        }
    }

    $get_indicator_qry= $conn->query("SELECT indicator_id,indicator, 
                                               COUNT(CASE WHEN tbl_first_aid.is_deleted = 0 AND YEAR(tbl_first_aid.date_created) = $year 
                                                     AND emp_id = '$session_user_id'
                                                     then leading_indicator else NULL end) as number_per_indicator 
                                               FROM tbl_indicators 
                                               LEFT JOIN tbl_first_aid ON tbl_indicators.indicator_id = tbl_first_aid.leading_indicator   
                                               GROUP BY indicator_id ");
    $indicators = $get_indicator_qry->fetchAll();

    $get_severity_qry= $conn->query("SELECT severity_id,tbl_severity.severity, 
                                               COUNT(CASE WHEN tbl_first_aid.is_deleted = 0 AND YEAR(tbl_first_aid.date_created) = $year 
                                                     AND emp_id = '$session_user_id'
                                                     then leading_indicator else NULL end) AS number_per_severity  
                                               FROM tbl_severity
                                               LEFT JOIN tbl_first_aid ON tbl_severity.severity_id = tbl_first_aid.severity 
                                               GROUP BY severity_id ");
    $severities= $get_severity_qry->fetchAll();

    $get_causes_qry = $conn->query("SELECT cause_id,tbl_root_causes.cause, 
                                             COUNT(CASE WHEN tbl_first_aid.is_deleted = 0 AND YEAR(tbl_first_aid.date_created) = $year
                                                   AND emp_id = '$session_user_id' 
                                                   then root_causes else NULL end) AS number_per_cause  
                                             FROM tbl_root_causes
                                             LEFT JOIN tbl_first_aid ON tbl_root_causes.cause_id = tbl_first_aid.root_causes 
                                             GROUP BY cause_id ");
    $causes = $get_causes_qry->fetchAll();

    $get_body_parts_qry = $conn->query("SELECT tbl_body_parts.part_id AS part_number,
                                                 COUNT(tbl_body_parts_injured.part_id) AS number_per_part, part_name
                                                 FROM tbl_body_parts 
                                                 LEFT JOIN tbl_body_parts_injured ON tbl_body_parts.part_id = tbl_body_parts_injured.part_id 
                                                 WHERE tbl_body_parts.is_deleted = 0 GROUP BY tbl_body_parts.part_id ");
    $body_parts = $get_body_parts_qry->fetchAll();

    $get_nature_qry = $conn->query("SELECT tbl_nature.nature_id,tbl_nature.nature AS nature_description, 
                                             COUNT(CASE WHEN tbl_first_aid.is_deleted = 0  AND YEAR(tbl_first_aid.date_created) = $year
                                                   AND emp_id = '$session_user_id' then tbl_first_aid.nature else NULL end) as number_per_nature
                                             FROM tbl_nature
                                             LEFT JOIN tbl_first_aid ON tbl_nature.nature_id = tbl_first_aid.nature 
                                             GROUP BY nature_id");
    $natures = $get_nature_qry->fetchAll();

    $get_position_qry = $conn->query("SELECT position_id,tbl_position.position , COUNT(injured_id) as number_of_person 
                                               FROM tbl_position 
                                               LEFT JOIN tbl_employees ON tbl_position.position_id = tbl_employees.position 
                                               LEFT JOIN tbl_first_aid ON tbl_employees.employee_id = tbl_first_aid.injured_id
                                               WHERE tbl_position.is_deleted = 0 GROUP BY tbl_position.position_id ");
    $positions = $get_position_qry->fetchAll();

    $get_mech_qry = $conn->query("SELECT mech_description,
                                           COUNT(CASE WHEN tbl_first_aid.is_deleted = 0 AND YEAR(tbl_first_aid.date_created) = $year 
                                                 AND emp_id = '$session_user_id' 
                                                 then mechanism else NULL end) as number_of_mech FROM tbl_mechanisms
                                           LEFT JOIN `tbl_first_aid` ON tbl_first_aid.mechanism = tbl_mechanisms.mech_id 
                                           GROUP BY mech_id");
    $mechanisms = $get_mech_qry->fetchAll();
    $equipment_qry = $conn->query("SELECT tbl_equipment.equipment_id as equip_id , 
                                            COUNT(CASE WHEN tbl_first_aid.is_deleted = 0  AND YEAR(tbl_first_aid.date_created) = $year 
                                                  AND emp_id = '$session_user_id'
                                                  then tbl_fa_equipment.equip_id else NULL end) as number_of_equipments, equipment_description
                                            FROM tbl_equipment 
                                            LEFT JOIN tbl_fa_equipment ON tbl_equipment.equipment_id = tbl_fa_equipment.equip_id 
                                            LEFT JOIN tbl_first_aid ON tbl_fa_equipment.fa_id = tbl_first_aid.fa_id
                                            WHERE tbl_equipment.is_deleted = 0 
                                            GROUP BY tbl_equipment.equipment_id");
    $equipments = $equipment_qry->fetchAll();
    $incident_arrays = array();
    $company_qry = $conn->query("SELECT  company_name FROM tbl_company WHERE  is_deleted = 0 AND sub_id = $session_sub_id");
    $company = $company_qry->fetchAll();
    $comp = array();
    foreach($company as $com){
        $comp[] = strtoupper($com['company_name']);
    }

    foreach($incident_types as $type){
        $incident_id = $type['incident_id'];
        $incident_per_company = $conn->query("SELECT COALESCE(COUNT(tbl_first_aid.injured_id),0) number_per_company, company_id, company_name FROM tbl_company 
                                                    LEFT JOIN tbl_employees ON tbl_company.company_id = tbl_employees.company
                                                    LEFT JOIN tbl_first_aid ON tbl_employees.employee_id =  tbl_first_aid.injured_id
                                                    WHERE tbl_employees.is_deleted = 0 AND incident_type =    $incident_id
                                                    AND emp_id = '$session_user_id' AND tbl_first_aid.is_deleted = 0
                                                    GROUP BY tbl_employees.company
                                                    ");
        $incident_company = $incident_per_company->fetchAll();
        $data_company = array();

        //GET ALL DATA WHERE ID  EQUAL TO NUMBER PER COMPANY
        $data_per_type = array_column($incident_company, 'number_per_company');
        array_push($incident_arrays, $data_per_type);

    }
    $incident_arrays = json_encode($incident_arrays);

    $incident_arrays_pm = array();
    $incident_ids = array_column($incident_types, 'incident_id');
    foreach ($incident_ids as $id){
        $i = 1; // number of month
        $monthly_data_per_type = [];
        foreach($months as $month){
        $incident_pm_qry= $conn->query("SELECT incident_type, COUNT(fa_id) as number_per_incident 
                                                 FROM tbl_first_aid 
                                                 WHERE 
                                                 tbl_first_aid.is_deleted = 0 
                                                 AND YEAR(tbl_first_aid.date_created) = $year
                                                 AND MONTH(tbl_first_aid.date_created) = $i
                                                 AND tbl_first_aid.incident_type = $id ");
        $incident_pm = $incident_pm_qry->fetch();
        $monthly_data_per_type[] = $incident_pm['number_per_incident'];
        $i++;
        }
        array_push($incident_arrays_pm, $monthly_data_per_type);
    }
    $incident_arrays_pm =  json_encode($incident_arrays_pm);
    //THIS ARRAY WILL BE DISPLAYED AS A JSON FILE IN THE JAVASCRIPT CODE BELOW
//    ================================================================================================
//    ================================================================================================

//    ================================================================================================

    $ind = array();
    $data_indicators = array();
    foreach($indicators as $indicator){
        array_push($ind, $indicator['indicator']);
        $data_indicators[] = intval($indicator['number_per_indicator']);
    }
//    ================================================================================================

    $label_severity = array();
    $data_severity = array();
    foreach($severities as $severity){
        array_push($label_severity,$severity['severity']);
        $data_severity[] = intval($severity['number_per_severity']);
    }
    //assign each sub-array to the newly created array
//    ================================================================================================


    $label_causes = array();
    $data_causes = array();
    foreach($causes as $cause){
        array_push($label_causes,$cause['cause']);
        $data_causes[] = intval($cause['number_per_cause']);
    }
    //assign each sub-array to the newly created array
//    ================================================================================================
    $label_body_parts  = array();
    $data_body = array();
    foreach($body_parts as $body){
        array_push($label_body_parts, $body['part_name']);
        $data_body[] = intval($body['number_per_part']);
    }
    //assign each sub-array to the newly created array
//    ================================================================================================

    $label_nature = array();
    $data_nature = array();
    foreach($natures as $nature){
        array_push($label_nature, $nature['nature_description']);
        $data_nature[] = intval($nature['number_per_nature']);
    }
    //    ================================================================================================
    $pos = array();
    $data_job = array();
    foreach($positions as $position){
        array_push($pos, $position['position']);
        $data_job[] = intval($position['number_of_person']);
    }
    //    ================================================================================================
    $mech = array(); // store the data here to create the labels
    $data_mech = array();
    foreach($mechanisms as $mechanism){
        array_push($mech, $mechanism['mech_description']);
        $data_mech[] = intval($mechanism['number_of_mech']);
    }
    $equip  = array();
    $data_equipments = array();
    foreach($equipments as $equipment){
        array_push($equip, $equipment['equipment_description']);
        $data_equipments[] = intval($equipment['number_of_equipments']);
    }

    } catch (Exception $e) {
        echo $e;
    }

    $div_space = ' <div class="col-sm-12 d-none d-print-block p-200" >
                        <span class=" "></span>
                   </div>';
    ?>

    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Incident Classification</h3>
                    <button type="button" class="btn-block-option d-print-none" onclick="printGraph('myChart10','Incident Classification');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_incident" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Leading Indicators</h3>
                    <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_indicators','Leading Indicators');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_indicators" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 d-none d-print-block p-100" >
        <span class=" "></span>
    </div>
    <div class="col-sm-12 d-none d-print-block p-50" >
        <span class=" "></span>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Potential Severity</h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_severity','Potential Severity');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_severity" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Root Cause Analysis</h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_cause',Root Cause Analysis);">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_cause" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 d-none d-print-block p-150" >
        <span class=" "></span>
    </div>
    <div class="col-sm-12 d-none d-print-block p-30" >
        <span class=" "></span>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Body Parts Analysis</h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_body','Body Parts Analysis');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_body" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Nature of Injury</h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_injury','Nature Of Injury');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_injury" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 d-none d-print-block p-200" >
        <span class=" "></span>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Recordable Incident</h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_recordable','Recordable Incident');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_recordable" height="150"   class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 d-none d-print-block p-200" >
        <span class=" "></span>
    </div>
    <div class="col-sm-12 d-none d-print-block p-50" >
        <span class=" "></span>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Job Description</h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_job','Job Description')">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_job"   height="300" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Mechanism of Injury</h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('myChart18','Mechanism of Jury');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_mechanism"     class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 d-none d-print-block p-200" >
        <span class=" "></span>
    </div>
    <div class="col-sm-12 d-none d-print-block p-50" >
        <span class=" "></span>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Equipment Involved</h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_equipment','Equipment Involved');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_equipment" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>LTRIR and TRIR </h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_equipment','Equipment Involved');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="trir_canvass" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Incident per Company </h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_equipment','Incident Per Month');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="stacked_incident" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-lg-6 dashboard_canvass">
        <div class="block">
            <div class="block block-rounded">
                <div class="block-header">
                    <h3>Incident per Month </h3>   <button type="button" class="btn-block-option d-print-none" onclick="printGraph('chart_equipment','Incident Per Month');">
                        <i class="si si-printer"></i> Print
                    </button>
                </div>
                <div class="block-content">
                    <canvas id="chart_monthly_incident" class="mb-20 mr-10"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <?=$div_space?>
        <?=$div_space?>

        <div class="col-md-12">
            <div class="row">
                <!--        TOTAL MONTHLY HOURS GRAPH        -->
                <div class="col-6 dashboard_canvass">
                    <div class="block block-rounded">
                        <div class="block-content">
                            <canvas id="totals_civils" height="150" class="mb-20 mr-10"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-6 dashboard_canvass">
                    <div class="block block-rounded">
                        <div class="block-content">
                            <canvas id="totals_mechanicals" height="150" class="mb-20 mr-10"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-6 dashboard_canvass">
                    <div class="block block-rounded">
                        <div class="block-content">
                            <canvas id="totals_electricals" height="150" class="mb-20 mr-10"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 d-none d-print-block p-50" >
                    <span class=" "></span>
                </div>
                <div class="col-6 dashboard_canvass">
                    <div class="block block-rounded">
                        <div class="block-content">
                            <canvas id="totals_camps" height="150" class="mb-20 mr-10"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-6 dashboard_canvass">
                    <div class="block block-rounded">
                        <div class="block-content">
                            <canvas id="totals_office" height="150" class="mb-20 mr-10"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-6 dashboard_canvass">
                    <div class="block block-rounded">
                        <div class="block-content">
                            <canvas id="totals_hours" height="150" class="mb-20 mr-10"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        //var labels = <?php // echo json_encode($itd_years);?>//;
        month_label = ["January","Feburary","March","April","May","June","July","August","September","October","November","December"];
        var colors = ([ 'rgba(255, 99, 132, 0.4)',
            'rgba(255, 159, 64, 0.4)',
            'rgba(255, 205, 86, 0.4)',
            'rgba(75, 192, 192, 0.4)',
            'rgba(54, 162, 235, 0.4)',
            'rgba(153, 102, 255, 0.4)',
            'rgba(201, 203, 207, 0.4)',
            'rgba(255, 99, 132, 0.4)',
            'rgba(255, 159, 64, 0.4)',
            'rgba(255, 205, 86, 0.4)',
            'rgba(75, 192, 192, 0.4)',
            'rgba(54, 162, 235, 0.4)',
            'rgba(153, 102, 255, 0.4)',
            'rgba(201, 203, 207, 0.4)'
        ]);
        var border_colors = ([
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)',
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)']);
        x = JSON.stringify(month_label)
        labels_incident =<?= json_encode($label_incident);?>;
        labels_recorable = <?= json_encode($label_recordable);?>;
        labels_indicators = <?= json_encode($ind);?>;
        labels_severity =<?= json_encode($label_severity);?>;
        labels_causes =<?= json_encode($label_causes);?>;
        labels_body_parts = <?= json_encode($label_body_parts);?>;
        labels_job =<?= json_encode($pos);?>;
        label_nature = <?= json_encode($label_nature)?>;
        labels_mechanism =<?= json_encode($mech);?>;
        labels_equipments =<?= json_encode($equip);?>;
        labels_company = <?= json_encode($comp);?>;
        double_dataset_chart();
        var Y_AXIS = 'y';
        var X_AXIS = 'x';
        var BAR_CHART = 'bar';
        var PIE_CHART = 'pie';
        bar_chart(<?= json_encode($data_incident);?>, labels_incident , 'chart_incident',X_AXIS,BAR_CHART,50, 'Incident');
        bar_chart(<?= json_encode($data_indicators);?>, labels_indicators , 'chart_indicators',X_AXIS,BAR_CHART,50,'Indicators');
        bar_chart(<?= json_encode($data_severity);?>, labels_severity , 'chart_severity',Y_AXIS,BAR_CHART,50,'Severity');
        bar_chart(<?= json_encode($data_causes);?>, labels_causes , 'chart_cause',Y_AXIS,BAR_CHART,30,'Root Causes');
        bar_chart(<?= json_encode($data_body);?>, labels_body_parts , 'chart_body',X_AXIS,BAR_CHART,30, 'Body Parts');
        bar_chart(<?= json_encode($data_recordable);?>, labels_recorable , 'chart_recordable', Y_AXIS,PIE_CHART,50,'Recordable Incident');
        bar_chart(<?= json_encode($data_job);?>, labels_job , 'chart_job',Y_AXIS,BAR_CHART,20,'Job Description');
        bar_chart(<?= json_encode($data_nature);?>, label_nature , 'chart_mechanism',Y_AXIS,BAR_CHART,20,'Mechanism of Injury');
        bar_chart(<?= json_encode($data_equipments);?>, labels_equipments , 'chart_equipment',X_AXIS,BAR_CHART,60,'Equipments Involved');
        bar_chart(<?= json_encode($data_equipments);?>, labels_equipments , 'chart_injury',X_AXIS,BAR_CHART,50,'Injury');
        bar_chart(<?= json_encode($monthly_values_civils);?>,month_label,'totals_civils',X_AXIS,BAR_CHART, 50,'Civils Total');
        bar_chart(<?= json_encode($monthly_values_mechanicals);?>,month_label,'totals_mechanicals',X_AXIS,BAR_CHART, 50,'Mechanical Total' );
        bar_chart(<?= json_encode($monthly_values_electricals);?>,month_label,'totals_electricals',X_AXIS,BAR_CHART, 50, 'Electrical Total' );
        bar_chart(<?= json_encode($monthly_values_camps);?>,month_label,'totals_camps',X_AXIS,BAR_CHART, 50,'Camps Total');
        bar_chart(<?= json_encode($monthly_values_office);?>,month_label,'totals_office',X_AXIS,BAR_CHART, 50, 'Office Total');
        bar_chart(<?= json_encode($monthly_totals_hours);?>,month_label,'totals_hours',X_AXIS,BAR_CHART, 50,'Hours Total');
        bar_chart2(<?= ($incident_arrays);?>, labels_company , 'stacked_incident',X_AXIS,BAR_CHART,50,'Incidents Per Company');
        bar_chart2(<?= ($incident_arrays_pm);?>, month_label , 'chart_monthly_incident',X_AXIS,BAR_CHART,50,'Incidents Per Company');

        function bar_chart(data_values,labels,chart_id,axis,chart_type, bar_thickness,table_label ){
            var data = {
                labels:labels,
                datasets: [{
                    label: table_label,
                    barThickness :bar_thickness,
                    maxBarThickness: 200,
                    data: data_values,
                    backgroundColor: colors,
                    borderColor: border_colors,
                    borderWidth: 1,
                    responsive: true
                }]
            }
            var config2= {
                type: chart_type,
                data: data,
                options: {indexAxis:axis }
            };
            chart_canvass = new Chart(document.getElementById(chart_id),config2);
        }
        function bar_chart2(data_values,labels,chart_id,axis,chart_type, bar_thickness ){
            var  data_arr = data_values;
            var chart_label = JSON.stringify(labels);
            var data = {
                labels: JSON.parse(chart_label),
                datasets: [],
                barThickness :50,
                maxBarThickness: 50,
                responsive: true
            }

            var datasets = [];
            for(var i = 0; i < data_arr.length; i++){
                datasets = {
                    label: labels_incident[i],
                    backgroundColor: colors[i],
                    borderColor: border_colors[i],
                    data:JSON.parse(JSON.stringify(data_arr[i])),
                    borderWidth: 1
                };
                data.datasets.push(datasets);
            };
            var config2= {
                type: 'bar',
                data: data,
                options: {
                    plugins: {
                        title: {
                            display: true
                        },
                    },
                    responsive: true,
                    interaction: {
                        intersect: false,
                    },
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true
                        }
                    }
                }
            };

            chart_canvass = new Chart(document.getElementById(chart_id),config2);
        }
        <?php
        $i = 1;
        $get_monthly_incidents_qry = $conn->query("
                                                                                        SELECT  COUNT(*), MONTH(date_conducted)  as itd_month,date_conducted,
                                                                                        COUNT('tbtp_id') as totals_days,
                                                                                        SUM(time) as total_itd,
                                                                                        SUM( case when tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = 2022  then time else 0 end) as total_hours_selected_month,
                                                                                        COUNT( case when tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted) = 2022  then tbtp_id else 0 end) as total_participants_per_month
                                                                                        FROM tbl_toolbox_talks 
                                                                                        INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                                                                        WHERE  tbl_toolbox_talks.is_deleted = 0 AND YEAR(date_conducted)= 2022
                                                                                        GROUP BY MONTH (date_conducted)
                                                                                        ORDER BY `date_conducted`");
        $itds = $get_monthly_incidents_qry->fetchAll();
        $get_lost_time_qry = $conn->query("SELECT SUM(lti_injured) as lost_time   , MONTH(date_created) as lti_month
                                                                                        FROM tbl_first_aid
                                                                                        WHERE tbl_first_aid.is_deleted = 0  AND YEAR(date_created) = 2022");
        $lti_injured = $get_lost_time_qry->fetchAll();
        $recordable_incident_qry = $conn->query("SELECT COUNT(fa_id) as number_per_incident , MONTH(date_created)  as incident_month 
                                                                                          FROM tbl_first_aid WHERE  YEAR(date_created) = 2022 GROUP BY MONTH(date_created)");
        $recordable_incident = $recordable_incident_qry->fetchAll();

        $mri = array(0,0,0,0,0,0,0,0,0,0,0,0); // monthly recordable incident
        foreach ($recordable_incident as $record){
            $mri[$record['incident_month']-1] = $record['number_per_incident'];
        }
        // array has 12 elements representing the months in a year
        $monthly_hours_array = array(0,0,0,0,0,0,0,0,0,0,0,0);
        $monthly_mp_array = array(0,0,0,0,0,0,0,0,0,0,0,0);
        $monthly_ltir = array(0,0,0,0,0,0,0,0,0,0,0,0);
        $monthly_trir = array(0,0,0,0,0,0,0,0,0,0,0,0);

        // store the data to the arrays
        foreach ($itds as $monthly_record){
            $monthly_hours_array[$monthly_record['itd_month']-1] = $monthly_record['total_itd']; // total man hours
            $monthly_mp_array[$monthly_record['itd_month']-1] = $monthly_record['totals_days']; // total man power
        }

        foreach($lti_injured as $lti){
            $LTIR =  ($lti['lost_time'] * 1000000) / $monthly_hours_array[$lti['lti_month']-1];
            $monthly_ltir[$lti['lti_month']-1] = $LTIR;
        }

        $month_index = 0; //
        foreach($mri as $recordable_incident):
            $trir= 0;
            if ($recordable_incident >  0){
                $total_manhours =  $monthly_hours_array[$month_index] ;
                $trir = ($recordable_incident * 1000000) / $total_manhours;
                $monthly_trir[$month_index] = $trir;
                //            $monthly_total = $monthly_total+ $recordable_incident;
            }
            $month_index++;
        endforeach;
        ?>
        function double_dataset_chart(){
            var data = {
                labels: month_label,
                datasets: [{
                    label: 'LTIR',
                    data: <?php   echo json_encode($monthly_ltir);?>,
                    backgroundColor: colors,
                    borderColor: border_colors ,
                    borderWidth: 1
                },{
                    label: 'TRIR',
                    data: <?php   echo json_encode($monthly_trir);?>,
                    backgroundColor: colors[1] ,
                    borderColor:  border_colors ,
                    borderWidth: 1
                }] // dataset
            };

            const multiple_dataset_config = {
                type: 'bar',
                data: data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                },
            };
            var myChart = new Chart(
                document.getElementById('trir_canvass'),
                multiple_dataset_config
            );

        }
    </script>
<?php
}

$conn = null;
