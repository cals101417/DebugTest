<?php

include '../../conn.php';
if (isset($_POST['delete_attendance'])){
    $attendance_id = $_POST['attendance_id'];
    try {
        $update = $conn->query("UPDATE `tbl_toolbox_talks` SET `is_deleted`= 1 WHERE `tbt_id`= $attendance_id");
        echo 'Successfully removed';
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}

if (isset($_POST['remove_attendance'])){
    $tbtp_id = $_POST['tbtp_id'];
    try {
        $remove_attendance = $conn->prepare("UPDATE `tbl_toolbox_talks_participants` SET `is_removed`= 1 WHERE `tbtp_id` = ?");
        $remove_attendance->execute([$tbtp_id]);
        echo 'Trainee successfully removed';
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}
if (isset($_POST['active_inactive_user'])){
    $attendance_id = $_POST['attendance_id'];
    $val = $_POST['val'];

    try {
        $active_inactive_user = $conn->prepare("UPDATE `tbl_attendance` SET `Status` = ? WHERE attendance_id = ?");
        $active_inactive_user->execute([$val,$attendance_id]);
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}
if (isset($_POST['load_tbt_over_view'])) {
    $tbt_type = $_POST['tbt_type'];
    $select_year = $_POST['select_year'];
    $select_type = $_POST['select_type'];
    $select_month = $_POST['select_month'];
    $comparator = '';
    (($select_type == 6) ? $comparator = "<" : $comparator = "=" );
    $date = date('Y-m-d', strtotime(date($select_year."-".$select_month)));
    $count_tbt_qry = $conn->query("SELECT (COUNT(tbt_id))as total_tbt, COUNT(DISTINCT(location)) AS total_location  FROM tbl_toolbox_talks 
                                            WHERE tbt_type"."$comparator"."$select_type AND is_deleted = 0
                                            AND  date_format(date_conducted, '%Y-%m')  =  date_format('$date', '%Y-%m')");
    $count =  $count_tbt_qry->fetch();
    $total_man_power_qry = $conn->query("SELECT COUNT(employee_id) as total_man_power, COALESCE(SUM(time),0) as total_man_hours  FROM tbl_toolbox_talks 
                                                  INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id
                                                  WHERE tbt_type "."$comparator"."$select_type AND tbl_toolbox_talks.is_deleted = 0
                                                    AND  date_format(date_conducted, '%Y-%m')  =  date_format('$date', '%Y-%m')");
    $total_man_power = $total_man_power_qry->fetch();
    $average_manpower_qry = $conn->query("SELECT COUNT(employee_id) as total_monthly_manpower ,date_conducted  FROM tbl_toolbox_talks 
                                                   INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id
                                                   WHERE tbt_type "."$comparator"."$select_type AND tbl_toolbox_talks.is_deleted = 0            
                                                        AND  date_format(date_conducted, '%Y-%m')  =  date_format('$date', '%Y-%m')
                                                   GROUP BY MONTH(date_conducted)+'-'+YEAR(date_conducted)");
    $average_manpower = $average_manpower_qry->fetchAll();
    $mp= 0;
    foreach ($average_manpower as $manpower){
        $mp = $mp + $manpower['total_monthly_manpower'];
    }
    if($mp != 0){
        $mp =  $mp/ count($average_manpower);
        $mp =  number_format((float)$mp, 2, '.', '');
    }
    ?>
    <div class="row gutters-tiny">
        <div class="col-12 col-lg-3 col-md-6 ">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="fa fa-dropbox fa-2x "></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-elegance" data-toggle="countTo" data-speed="1000" data-to="<?=$count['total_tbt']?>"><?=$count['total_tbt']?></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Total TBT</div>
                </div>
            </a>
        </div>
        <div class="col-12 col-lg-3 col-md-6">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="fa fa-universal-access fa-2x text-elegance-light"></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-elegance" data-toggle="countTo" data-speed="1000" data-to="<?=$mp?>"><?=$mp?></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">Total Manpower </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-lg-3 col-md-6">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="si si-clock fa-2x "></i>
                    </div>
                    <div class="font-size-h3 font-w600 text-warning" data-toggle="countTo" data-speed="1000" data-to="<?=$total_man_power['total_man_hours']?>"><?=$total_man_power['total_man_hours']?></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">  Total Man Hours</div>
                </div>
            </a>
        </div>

        <div class="col-12 col-lg-3 col-md-6">
            <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                <div class="block-content block-content-full clearfix">
                    <div class="float-right mt-15 d-none d-sm-block">
                        <i class="fa fa-map-marker fa-2x "></i>
                    </div>
                    <div class="font-size-h3 font-w600 " data-toggle="countTo" data-speed="1000" data-to="<?=$count['total_location']?>"><?=$count['total_location']?></div>
                    <div class="font-size-sm font-w600 text-uppercase text-muted">  Total Location</div>
                </div>
            </a>
        </div>
    </div>
    <?php
}
if (isset($_POST['view_toolbox_details'])){
    $tbt_id = $_POST['tbt_id'];
    $type = $_POST['toolbox_type'];

    $tbt_qry = $conn->query("SELECT
                                                tbl_toolbox_talks.title,
                                                tbl_toolbox_talks.contract_no,
                                                tbl_toolbox_talks.location,
                                                tbl_toolbox_talks.time_conducted,
                                                tbl_toolbox_talks.date_conducted,
                                                tbl_toolbox_talks.conducted_by,
                                                tbl_toolbox_talks.description,
                                                tbl_toolbox_talks.`status`,
                                                (SELECT CONCAT(`firstname`,' ',`lastname`) FROM tbl_employees WHERE tbl_employees.employee_id = tbl_toolbox_talks.conducted_by) as conductor_name
                                                FROM tbl_toolbox_talks
                                                WHERE tbl_toolbox_talks.tbt_id = $tbt_id AND tbl_toolbox_talks.tbt_type = $type 
                                                ");


    $tbt_fetch = $tbt_qry->fetch();
    $title = $tbt_fetch['title'];
    $contract_no = $tbt_fetch['contract_no'];
    $location = $tbt_fetch['location'];
    $time_conducted = $tbt_fetch['time_conducted'];
    $date_conducted = $tbt_fetch['date_conducted'];
    $conducted_by = $tbt_fetch['conductor_name'];
    $description = $tbt_fetch['description'];
    $status = $tbt_fetch['status'];

    ?>

    <!-- Page Content -->
    <div class="content pt-0">
        <table class="table table-striped table-borderless table-sm mt-20">
            <tbody>
            <tr>
                <td style="width: 25%" class="font-w700 text-right">Topic:</td>
                <td style="width: 75%"><?=$title?></td>
            </tr>
            <tr>
                <td class="font-w700 text-right">Activity:</td>
                <td><?=$tbt_fetch['description']?></td>
            </tr>
            <tr>
                <td class="font-w700 text-right">Contract No:</td>
                <td><?=$contract_no?></td>
            </tr>
            <tr>
                <td class="font-w700 text-right">Location:</td>
                <td><?=$location?></td>
            </tr>
            <tr>
                <td class="font-w700 text-right">Conducted by:</td>
                <td><?=$conducted_by?></td>
            </tr>
            <tr>
                <td class="font-w700 text-right">No. of attendance:</td>
                <td><?=$location?></td>
            </tr>
            <tr>
                <td class="font-w700 text-right">Status:</td>
                <td>
                    <?php
                    if($status == 0){
                        echo 'Incomplete';
                    }else{
                        echo 'Complete';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td class="font-w700 text-right">Time Conducted:</td>
                <td><?=date('h:i A',strtotime($time_conducted))?></td>
            </tr>
            <tr>
                <td class="font-w700 text-right">Date Conducted:</td>
                <td><?=date('F d, Y', strtotime($date_conducted))?></td>
            </tr>
            </tbody>
        </table>
        <div class="block">
            <div class="block-header block-header-default">
                <h3 class="block-title">List of Participants</h3>
                <div class="block-options">
                    <div class="block-options-item">
                        <!--                                <code>.table</code>-->
                    </div>
                </div>
            </div>
            <div class="block-content">
                <table class="table table-vcenter">
                    <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th class="text-center">Employee name</th>
                        <th class="text-center" style="width: 30%;">Date Added</th>
                        <th class="text-center" style="width: 30%;">Hours</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $tbt_participants_query = $conn->query("SELECT
                                                                tbl_toolbox_talks_participants.tbtp_id,
                                                                tbl_employees.employee_id,
                                                                tbl_employees.firstname,
                                                                tbl_employees.lastname,
                                                                tbl_toolbox_talks_participants.date_added,
                                                                tbl_toolbox_talks_participants.time
                                                                FROM
                                                                tbl_toolbox_talks_participants
                                                                INNER JOIN tbl_employees ON tbl_employees.employee_id =tbl_toolbox_talks_participants.employee_id
                                                                WHERE
                                                                tbl_toolbox_talks_participants.tbt_id = $tbt_id AND
                                                                tbl_toolbox_talks_participants.is_removed = 0
                                                                ");
                    $count = 1;
                    foreach ($tbt_participants_query as $participant) {
                        $emplyee_id = $participant ['employee_id'];
                        $fullname = ucwords(strtolower($participant['firstname'] .' '.$participant['lastname']));
                        $tbtp_id = $participant ['tbtp_id'];
                        $date_added = $participant ['date_added'];
                        $hrs = $participant ['time'];

                        ?>
                        <tr>
                            <th class="text-center" scope="row"><?=$emplyee_id?></th>
                            <td class="text-center"><?=$fullname?></td>
                            <td class="text-center"><?=date('F d, Y', strtotime($date_added))?></td>
                            <td class="text-center"><?=$hrs?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}
if (isset($_POST['add_new_toolbox_talks'])){

    $title = $_POST['title'];
    $tbt_type = $_POST['tbt_type'];
    $contract_no = $_POST['contract_no'];
    $location = $_POST['location'];
    $time_conducted = $_POST['time_conducted'];
    $date = $_POST['date'];
    $conducted_by = $_POST['conducted_by'];
    $description = $_POST['description'];
    $date_now = date('Y-m-d H:i:s');
    $return_arr = array();
    $status = 0;
    $fileNames = array_filter($_FILES['files']['name']);

    $input_time_array = explode("," ,$_POST['input_time_array']);

    $participant_id = explode(",", $_POST['participant_id_array']);
    $error_message = '';
    $status = 0; // completion status

    $targetDir = "../assets/media/photos/toolbox_talks/";
    $allowTypes = array('jpg','png','jpeg','gif','pdf','xlsx');
    // if (!empty($fileNames)) {
    //
    // }

    // check if there's a valid to be uploaded, at least 1

    foreach($_FILES['files']['name'] as $key=>$val){
        $fileName = basename($_FILES['files']['name'][$key]);
        $targetFilePath = $targetDir . $fileName;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        if(in_array($fileType, $allowTypes)){

            $status = 1;

        }
    }

    // check if there's a valid to be uploaded, at least 1

    $stmt = $conn->prepare("INSERT INTO `tbl_toolbox_talks`(`title`, `contract_no`, `location`, `time_conducted`, `date_conducted`, `conducted_by`, `description`, `tbt_type`, `status`, `user_id`, `is_deleted`) 
                              VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$title,$contract_no,$location,$time_conducted,$date,$conducted_by,$description,$tbt_type,$status ,$user_id,0]);

    $tbt_id = $conn->lastInsertId();


    try {

        // File upload configurationva

        $unique_file_name = "";

        if(!empty($fileNames)){

            foreach($_FILES['files']['name'] as $key=>$val){
                // File upload path
                $fileName = basename($_FILES['files']['name'][$key]);
                $date1 = date("His"); // for unique file name
                $unique_file_name = $date1.'-'.$fileName;
                $targetFilePath = $targetDir . $unique_file_name;


                // Check whether file type is valid
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                if(in_array($fileType, $allowTypes)){
                    // Upload file to server
                    if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)){
                        // unique_file_name db insert sql
                        $insert = $conn->prepare("INSERT INTO `tbl_tbt_images`(`img_src`, `date_uploaded`, `tbt_id`) VALUES (?,?,?)");
                        $insert->execute([$unique_file_name,$date_now,$tbt_id]);

                        // completed
                    }

                } else  {

                    $error_message = $error_message."\n".$unique_file_name." "." FILE TYPE NOT ALLOWED!"."\n";
                }

            }
        } else {
            $status = 0; //incomplete
            $error_message = $error_message."\n "."NO FILE UPLOADED";
        }

        //================SAVE NEW TOOL BOOX TALKS ==================//


        //================== SAVE PARTICIPANTS =====================//
        for ($i=0; $i < count($participant_id); $i++) {

            $tbt_time = $input_time_array[$i];
            $employee_id =  $participant_id[$i];

            $date_now = date('Y-m-d H:i:s');
            try {
                $add_trainee_sql = $conn->prepare ("INSERT INTO `tbl_toolbox_talks_participants`(`employee_id`, `time`,`user_id`, `tbt_id`, `date_added`) 
                                                            VALUES (?,?,?,?,?)");
                $add_trainee_sql->execute([$employee_id,$tbt_time, $user_id,$tbt_id,$date_now]);
                echo 'success';
            }catch (Exception $e){
                echo 'failed';
            }
        }
        //================== SAVE PARTICIPANTS =====================//

        echo $error_message;
        // $return_arr[] = array("status" => 'success', "toolbox_id" => $tbt_id);

    }catch (Exception $e){
        echo $e;
        echo $error_message;

        // $return_arr[] = array("status" => 'failed', "toolbox_id" => 0);
    }


    echo json_encode($return_arr);
}

// fetch list of employees
if (isset($_POST['modal_select_employees'])){
    $toolbox_id = $_POST['toolbox_id'];
    $toolbox_qry = $conn->query("SELECT
                                                tbl_employees.employee_id,
                                                tbl_employees.firstname,
                                                tbl_employees.lastname 
                                            FROM
                                                tbl_employees 
                                            WHERE
                                                tbl_employees.employee_id NOT IN ((SELECT tbl_toolbox_talks_participants.employee_id FROM tbl_toolbox_talks_participants 
                                                                                    WHERE tbl_toolbox_talks_participants.tbt_id = $toolbox_id)) 
                                                AND tbl_employees.is_deleted = 0");
    foreach ($toolbox_qry as $toolbox){
//                                                $training
        $employee_fullname = ucwords(strtolower($toolbox['firstname']." ".$toolbox['lastname']));
        ?>
        <option value="<?=$toolbox['employee_id']?>"><?=$employee_fullname?></option>
        <?php
    }
}

if (isset($_POST['add_employee_to_toolbox'])){
    $tbt_id = $_POST['tbt_id'];
    $employee_id = $_POST['employee_id'];
    $date_now = date('Y-m-d H:i:s');
    try {
        $add_trainee_sql = $conn->prepare ("INSERT INTO `tbl_toolbox_talks_participants`(`employee_id`, `user_id`, `tbt_id`, `date_added`) 
                                                    VALUES (?,?,?,?)");
        $add_trainee_sql->execute([$employee_id,$user_id,$tbt_id,$date_now]);
        echo 'success';
    }catch (Exception $e){
        echo 'failed';
    }
}

// FUNCTION FOR LOADING TABLE OF PARTICIPANTS TO THE MODAL OF NEWLY CREATED TRAINING
if (isset($_POST['load_table_participants'])){
    $toolbox_id = $_POST['toolbox_id'];
    $tbt_stmt = $conn->prepare("SELECT
                                            tbl_employees.firstname,
                                            tbl_employees.lastname,
                                            tbl_employees.employee_id,
                                            tbl_toolbox_talks_participants.tbtp_id,
                                            tbl_toolbox_talks_participants.time
                                            FROM
                                            tbl_toolbox_talks_participants
                                            INNER JOIN tbl_employees ON tbl_employees.employee_id = tbl_toolbox_talks_participants.employee_id
                                            WHERE
                                            tbl_toolbox_talks_participants.tbt_id = ? AND
                                            tbl_toolbox_talks_participants.is_removed = 0");
    $tbt_stmt->execute([$toolbox_id]);
    $fetch_tbt = $tbt_stmt->fetchAll();
    foreach ($fetch_tbt as $tbt) {
        $emplyee_id = $tbt ['employee_id'];
        $employee = ucwords(strtolower($tbt['firstname'].' '.$tbt['lastname']));
        $tbtp_id = $tbt ['tbtp_id'];

        ?>
        <tr>
            <th class="text-center" scope="row"><?=$emplyee_id?></th>
            <td class=""><?=$employee?></td>
            <!-- <td>
                    <div class="input-group">
                    <input class="form-control form-control-sm w-25"   onfocusout="set_time(<?=$tbtp_id?>)"  onfocus="this.value=''"  min="1" max="12" type="number" id="add_time<?=$tbtp_id?>" value="<?=$tbt['time']?>">
                        <div class="input-group-append">

                            <!-- <button class="btn btn-sm btn-success" onclick="set_time(<?=$tbtp_id?>)">Set</button> -->
            </div>
            </div>
            </td>  -->
            <td class="text-center">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-secondary js-tooltip-enabled" id="delete" onclick="modal_remove_participant(<?=$tbtp_id?>,<?=$toolbox_id?>)" data-toggle="tooltip" title="Remove employee from attendance" data-original-title="Delete">
                        <i class="fa fa-times"></i>
                    </button>
                    <p><?=$toolbox_id?><?=$tbtp_id?></p>
                </div>
            </td>
        </tr>
        <?php
    }
}
// FUNCTION FOR REMOVING EMPLOYEE ON THE ADDING PARTICIPANT MODAL
// if (isset($_POST['modal_remove_participant'])){
//     $tbtp_id = $_POST['tbtp_id'];

//     try {
//         $remove = $conn->prepare("DELETE FROM `tbl_toolbox_talks_participants` WHERE tbtp_id = ?");
//         $remove->execute([$tbtp_id]);
//         echo 'success';
//     }catch (Exception $e){
//         echo $e;
//     }
// }

if (isset($_POST['add_time'])){
    $tbtp_id = $_POST['tbtp_id'];
    $time = $_POST['time'];

    try {
        $update_time = $conn->prepare("UPDATE `tbl_toolbox_talks_participants` SET `time`= ? WHERE `tbtp_id`= ?");
        $update_time->execute([$time,$tbtp_id]);

        echo 'success';
    }catch (PDOException $e){
        echo $e;
    }

}

if (isset($_POST['load_tbt_table'])){
    $type = $_POST['tbt_type'];
    $href = 'toolboxtalks.php';
    // USER ROLE ACCESS

    // END USER ROLE ACCESS
    $tbt_qry = $conn->query("SELECT
                                    tbl_toolbox_talks.tbt_id,
                                    tbl_toolbox_talks.tbt_type,
                                    tbl_toolbox_talks.title,
                                    tbl_toolbox_talks.contract_no,
                                    tbl_toolbox_talks.location,
                                    tbl_toolbox_talks.time_conducted,
                                    tbl_toolbox_talks.time_conducted,
                                    tbl_toolbox_talks.date_conducted,
                                    tbl_toolbox_talks.conducted_by,
                                    tbl_toolbox_talks.description,
                                    tbl_toolbox_talks.date_created,
                                    tbl_toolbox_talks.`status`,
                                    (SELECT CONCAT(`firstname`,' ',`lastname`) FROM tbl_employees 
                                    WHERE tbl_employees.employee_id = tbl_toolbox_talks.conducted_by ) as conductor_name,
                                    Count(tbl_toolbox_talks_participants.tbtp_id) AS count
                                    FROM
                                    tbl_toolbox_talks
                                    LEFT JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id
                                    WHERE tbl_toolbox_talks.tbt_type = $type AND
                                    tbl_toolbox_talks.is_deleted = 0
                                    GROUP BY tbl_toolbox_talks.tbt_id
                                    ORDER BY date_conducted DESC
                                    ");
    $count = 1;
    foreach ($tbt_qry as $toolbox) {
        $title = $toolbox["title"];
        $tbt_id = $toolbox["tbt_id"];
        $contract_no = $toolbox["contract_no"];
        $date_conducted = $toolbox["date_conducted"];
        $conducted_by = $toolbox["conductor_name"];
        $description = $toolbox["description"];
        $status = $toolbox["status"];
        $location = $toolbox["location"];
        $no = $toolbox["count"];
        ?>
        <tr>
            <th class="text-center" scope="row"><?=$count++?></th>
            <td class="text-center font-w700"><?=$description?></td>
            <td class="text-center"><?=$conducted_by?></td>
            <td class="text-center"><?=$location?></td>
            <td class="text-center"><?=$no?></td>
            <td class="text-center"><?=date('F d, Y', strtotime($date_conducted))?></td>
            <td class="text-center">
                <?php
                $st =  '<span class="badge badge-success">Completed</span>';

                if($status == 0){
                    $st =  '<span class="badge badge-warning">Incomplete</span>';
                }
                echo $st;
                ?>
            </td>
            <td class="text-center">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success js-tooltip-enabled" onclick="view_toolbox_details(<?=$tbt_id?>,<?=$type?>)" data-toggle="tooltip" title="View Details" data-original-title="View Details">
                        <i class="fa fa-list-alt"></i>
                    </button>
                </div>
            </td>
        </tr>
        <?php
    }
}
// function for uploading image for completion of training

if (isset($_POST['view_attached_file'])){
    $tbt_id = $_POST['tbt_id'];

    $attached_image_qry = $conn->prepare("SELECT `tbt_img_id`, `img_src`, `date_uploaded`, `tbt_id`, `type` FROM `tbl_tbt_images` WHERE `tbt_id` = ? ");
    $attached_image_qry->execute([$tbt_id]);
    $attached_images = $attached_image_qry->fetchAll();

    ?>
    <div class="block">
        <div class="block-header block-header-default">
        </div>
        <div class="block-content">
            <table class="table table-sm table-vcenter">
                <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Name</th>
                </tr>
                </thead>
                <tbody>

                <?php
                foreach ($attached_images as $attached_image){
                    $image = $attached_image['img_src'];
                    $type = $attached_image['type'];
                    $tbt_img_id = $attached_image['tbt_img_id'];
                    ?>
                    <tr>
                        <th class="text-center" scope="row"><?=$tbt_img_id ?></th>

                        <td class="d-none d-sm-table-cell">

                            <div class="col-md-12 ">
                                <!--  <div class="options-container fx-item-zoom-in fx-overlay-slide-left"> -->
                                <a href="assets/media/photos/toolbox_talks/<?=$image?>" class="img-link" target="_blank" >


                                    <?php $fileType = pathinfo($image, PATHINFO_EXTENSION);?>
                                    <!-- <p> <?=$fileType ?></p> -->

                                    <?php if ($fileType == "jpg"): ?>
                                        <img class="img img-thumbnail options-item" src="assets/media/photos/toolbox_talks/<?=$image?>" width="150" height= "150" alt="">

                                    <?php endif ?>
                                    <?php if ($fileType == "jpeg"): ?>
                                        <img class="img img-thumbnail options-item" src="assets/media/photos/toolbox_talks/<?=$image?>"  width="150" height= "150"alt="">

                                    <?php endif ?>

                                    <?php if ($fileType == "pdf"): ?>
                                        <span><i class="fa fa-file-o fa-2x"></i><?=$image?></span>

                                    <?php endif ?>
                                </a>
                                <!-- </div> -->
                            </div>
                            </span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php

}

if (isset($_POST['load_tbt_details_form'])){
    $tbt_id = $_POST['tbt_id'];

    $attendance_qry = $conn->query("SELECT
                                                tbl_toolbox_talks.title,
                                                tbl_toolbox_talks.contract_no,
                                                tbl_toolbox_talks.location,
                                                tbl_toolbox_talks.time_conducted,
                                                tbl_toolbox_talks.date_conducted,
                                                tbl_toolbox_talks.conducted_by,
                                                tbl_toolbox_talks.description,
                                                tbl_toolbox_talks.`status`,
                                                (SELECT CONCAT(`firstname`,' ',`lastname`) FROM tbl_employees WHERE conductor_name.employee_id = tbl_toolbox_talks.conducted_by) as conductor_name
                                                Count(tbl_toolbox_talks_participants.tbtp_id) AS count
                                                FROM
                                                tbl_toolbox_talks
                                                LEFT JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id
                                                WHERE
                                                tbl_toolbox_talks.tbt_id = $tbt_id");

    $tbt_fetch = $attendance_qry->fetch();
    $status = $tbt_fetch['status'];
    $title = $tbt_fetch['title'];
    $contract_no = $tbt_fetch['contract_no'];
    $location = $tbt_fetch['location'];
    $time_conducted = $tbt_fetch['time_conducted'];
    $date_conducted = $tbt_fetch['date_conducted'];
    $conducted_by = $tbt_fetch['conductor_name'];
    $description = $tbt_fetch['description'];
    $count = $tbt_fetch['count'];

    ?>
    <input type="hidden" id="details_tbt_id" name="details_tbt_id" value="<?=$tbt_id?>">
    <div class="form-group row">
        <label class="col-4" for="topic">Topic</label>
        <input type="text" class="form-control form-control-sm col-8" id="topic" name="topic" value="<?=$title?>" required>
    </div>
    <div class="form-group row">
        <label class="col-4" for="activity">Activity</label>
        <input type="text" class="form-control form-control-sm col-8" id="activity" name="activity" value="<?=$description?>" required>
    </div>
    <div class="form-group row">
        <label class="col-4" for="contract_no">Contract No.</label>
        <input type="text" class="form-control form-control-sm col-8" id="contract_no" name="contract_no" value="<?=$contract_no?>" required>
    </div>
    <div class="form-group row">
        <label class="col-4" for="location">Location</label>
        <input type="text" class="form-control form-control-sm col-8" id="location" name="location" value="<?=$location?>" required>
    </div>
    <div class="form-group row">
        <label class="col-4" for="conducted_by">Conducted by:</label>
        <input type="text" class="form-control form-control-sm col-8" id="conducted_by" name="conducted_by" value="<?=$conducted_by?>" required>
    </div>
    <div class="form-group row">
        <label class="col-4" for="time_conducted">Time Conducted:</label>
        <input type="time" class="form-control form-control-sm col-8" id="time_conducted" name="time_conducted" value="<?=date("H:i", strtotime($time_conducted))?>" required>
    </div>
    <div class="form-group row">
        <label class="col-4" for="date_conducted">Date Conducted:</label>
        <input type="date" class="form-control form-control-sm col-8" id="date_conducted" name="date_conducted" value="<?=date("Y-m-d", strtotime($date_conducted))?>" required>
    </div>
    <button type="submit" class="btn btn-block btn-primary mt-20"><i class="fa fa-save"></i> Submit</button>
    <?php

}

if (isset($_POST['details_tbt_id'])){
    $topic = $_POST['topic'];
    $activity = $_POST['activity'];
    $contract_no = $_POST['contract_no'];
    $location = $_POST['location'];
    $conducted_by = $_POST['conducted_by'];
    $time_conducted = $_POST['time_conducted'];
    $date_conducted = $_POST['date_conducted'];
    $tbt_id = $_POST['details_tbt_id'];

    try {
        $update = $conn->prepare("UPDATE `tbl_toolbox_talks` SET `title`=?,`contract_no`=?,`location`=?,`time_conducted`=?,`date_conducted`=?,`conducted_by`=?,`description`=? WHERE `tbt_id`=?");
        $update->execute([$topic,$contract_no,$location,$time_conducted,$date_conducted,$conducted_by,$activity,$tbt_id]);

        echo 'success';
    }catch (Exception $e){
        echo 'failed';
    }
}

function  new_date_format($date){
    $new_date = $date[0]."-".$date[1]."-".$date[2]." 00:00:00";
    return $new_date;
}

if (isset($_POST['date_range1'])){

    $range1 = $_POST['date_range1'];
    $range2 = $_POST['date_range2'];
    $date1 = explode("-",$range1);
    $date2 = explode("-",$range2);

    $range1 =  new_date_format($date1);
    $range2 =  new_date_format($date2);

//    echo $range1;
//    echo $range2;

    $generate_report_qry2 = $conn->query("SELECT  *,COUNT(*),
                                SUM(case when tbt_type='1' then time else null end) as sum2,
	                            SUM(case when tbt_type='2' then time else null end) as sum3,
	                            SUM(case when tbt_type='3' then time else null end) as sum4
                                    FROM tbl_toolbox_talks 
                                    INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                    WHERE (`date_conducted`  BETWEEN '$range1' AND '$range2')
                                      GROUP BY `tbt_type`");

    $get_result = $generate_report_qry2->fetchAll();

//    var_dump($get_result);
    foreach($get_result as $result){
        echo "<p>".$result['tbt_id']."-".$result['date_conducted']."----------TYPE: ".$result['sum2']."----------TYPE: ".$result['sum3']."</p>";
    }
    ?>

    <?php
}


$conn = null;