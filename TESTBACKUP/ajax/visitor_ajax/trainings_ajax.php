<?php
include '../../conn.php';
date_default_timezone_set('Asia/Manila');

use PHPMailer\PHPMailer\PHPMailer;
require_once '../../phpmailer/Exception.php';
require_once '../../phpmailer/PHPMailer.php';
require_once '../../phpmailer/SMTP.php';

require '../../vendor/autoload.php';

$mail = new PHPMailer(true);
// add_form

if (isset($_POST['title']) && isset($_POST['contract_no'])){
    $test = "";
    try {
        $title = $_POST['title'];
        $location = $_POST['location'];
        $contract_no = $_POST['contract_no'];
        $date = $_POST['date'];
        $date_expired = $_POST['date_expired'];
        $training_hrs = $_POST['training_hrs'];
//    $status = $_POST['status'];
        $status = 'Scheduled';
        $type = $_POST['type'];
//    $remarks = $_POST['remarks'];
        $date_now = date('Y-m-d H:i:s');
        $employee_id = 0;
        $conducted_by = '';

        $return_arr = array();
        $conn->beginTransaction();
        //        IF TRAINING IS IN-HOUSE
        if ($type == '1'){ // IF TRAINING IS INTERNAL
            $employee_id = $_POST['select_trainer'];
            $employee_qry = $conn->query("SELECT tbl_employees.firstname, tbl_employees.lastname FROM tbl_employees WHERE tbl_employees.employee_id = $employee_id");
            $employee = $employee_qry->fetch();
            $employee_name = ucwords(strtolower($employee['firstname'].' '.$employee['lastname']));
            $conducted_by = $employee_name;
        }else if ($type == '2' OR $type == '4'){ // IF TRAINING IS FOR CLIENT
            $conducted_by = $_POST['select_trainer'];
        }else if ($type == '3'){
            $conducted_by = $_POST['select_trainer'];
            $price = $_POST['price'];
            $currency = $_POST['currency'];
            $select_reviewed = $_POST['select_reviewed'];
            $select_approved = $_POST['select_approved'];
        }
        // INSERT IN DATABASE TRAINING TABLE
        $insert_training = $conn->prepare("INSERT INTO `tbl_trainings` (
                                                    `title`,
                                                    `location`,
                                                    `contract_no`,
                                                    `trainer`,
                                                    `training_hrs`,
                                                    `user_id`,
                                                    `employee_id`,
                                                    `status`,
                                                    `type`,
                                                    `training_date`,
                                                    `date_expired`,
                                                    `date_created`,
                                                    `date_updated`,
                                                    `is_deleted` 
                                                )
                                                VALUES
                                                    (
                                                        ?,?,?,?,?,?,?,?,?,?,?,?,?,?
                                                    )");
        $insert_training->execute([$title,$location,$contract_no,$conducted_by,$training_hrs,$session_emp_id,$employee_id,$status,$type,$date,$date_expired,$date_now,$date_now,0]);
        $training_id = $conn->lastInsertId();


        // IF TRAINING IS THIRD PARTY THEN SAVE OTHER DETAILS TO tbl_trainings_external_details
        if ($type == '3'){
//            INSERT EXTERNAL TRAINING DETAILS
            $insert_external_details = $conn->prepare("INSERT INTO `tbl_trainings_external_details` 
                                                                (`currency`, `training_id`, `course_price`, `date_requested`, `requested_by`, `reviewed_by`, `approved_by` )
                                                            VALUES
                                                                ( ?, ?, ?,?, ?, ?, ? )");
            $insert_external_details->execute([$currency,$training_id,$price,$date_now,$session_emp_id,$select_reviewed,$select_approved]);

            // INSERT WORKFLOW FOR REVIEW AND APPROVAL
            $insert_progress = $conn->prepare("INSERT INTO `tbl_trainings_external_progress`(`training_id`, `type`, `status`, `date_created`) 
                                                     VALUES (?,?,?,?)");
            $insert_progress->execute([$training_id,'request',0,$date_now]);

            // SEND EMAIL TO USER ASSIGNED FOR REVIEW
            $reviewer_email_stmt = $conn->prepare("SELECT `email`,`firstname` FROM `tbl_employees` WHERE `employee_id` = ?");
            $reviewer_email_stmt->execute([$select_reviewed]);
            $reviewer_email_fetch = $reviewer_email_stmt->fetch();
            $reviewer_email = $reviewer_email_fetch['email'];
            $reviewer_fname = ucwords($reviewer_email_fetch['firstname']);

            $message = '
                <div style="padding: 20px 0px 0px 0px; background-color: #189AA7;" class="shadow">
                    <img src="https://membersafety-surfers.com/assets/media/photos/safety-surfers-management-logo.png" style="width: 100%;">
                    <table width="100%" border="0" cellspacing="0" cellpadding="20" style="background-color: #47bcde; color: #5a5f61; font-family:verdana;">
                        <tr>
                            <td style="background-color: #fff; border-top: 10px solid #189AA7; border-bottom: 10px solid #189AA7;">
                                <p style="margin-top: -5px;">Hi '.$reviewer_fname.',</p>
                                You are assigned for reviewing for a new External Training entitled '.ucwords($title).', please login to safety surfers and click notification, Thank you!
                            </td>
                        </tr>
                    </table>
                    <div style="text-align: center; padding: 20px 0px; color: #fff; background-color: #189AA7;">
                        Safety Surfers Management System<br>
                        <a href="https://safetysurfers.com/" style="color: white;">https://safetysurfers.com/</a>
                    </div>
                </div>
            ';

//            $mail->isSMTP();
//            $mail->Host = 'smtp.gmail.com';
//            $mail->SMTPAuth = true;
//            $mail->Username = "den21rychu@gmail.com"; // Gmail address which you want to use as SMTP server
//            $mail->Password = "pqapcuzvmtugwggr"; // Gmail address Password
//            $mail->SMTPSecure = 'tls';
//            $mail->Port = '587';
//
//            //$mail->setFrom('test_email@ipasspmt.com'); // Gmail address which you used as SMTP server
//            $mail->setFrom("den21rychu@gmail.com");
//            $mail->addAddress("$reviewer_email"); // Email address where you want to receive emails (you can use any of your gmail address including the gmail address which you used as SMTP server)
//
//            $mail->isHTML(true);
//            $mail->Subject = "Safety Surfers Review Training";
//            $mail->Body = "$message";
//
//            $mail->send();

        }

        // File upload configuration
        $targetDir = "../assets/media/photos/training/";
//        $allowTypes = array('jpg','png','jpeg','gif');
        $allowTypes = array('zip', 'pdf', 'docx', 'xlsx');
        $fileNames = array_filter($_FILES['files']['name']);
        if(!empty($fileNames)){
            foreach($_FILES['files']['name'] as $key=>$val){
                // File upload path
                $fileName = basename($_FILES['files']['name'][$key]);
                $targetFilePath = $targetDir . $fileName;

                // Check whether file type is valid
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
                if(in_array($fileType, $allowTypes)){
                    // Upload file to server
                    if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)){
                        // Image db insert sql
                        $insert = $conn->prepare("INSERT INTO `tbl_trainings_images`(`training_id`, `image`, `date_uploaded`) VALUES (?,?,?)");
                        $insert->execute([$training_id,$fileName,$date_now]);

                    }
                }
            }
        }
        $conn->commit();
        $return_arr[] = array("status" => 'success', "training_id" => $training_id);
    }catch (Exception $e){
        $conn->rollBack();
        $return_arr[] = array("status" => 'failed', "training_id" => 0);
        $test =  'Sorry, Something wen\'t wrong'. $e;
    }
//    echo $test;
    echo json_encode($return_arr);


}

if (isset($_POST['delete_training'])){
    $training_id = $_POST['training_id'];
    try {
        $remove_training = $conn->query("UPDATE `tbl_trainings` SET `is_deleted`= 1 WHERE training_id = $training_id");
        echo 'Training successfully removed';
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}

if (isset($_POST['add_trainee'])){
    $training_id = $_POST['training_id'];
    $employee_id = $_POST['select_employee'];
    $date_now = date('Y-m-d H:i:s');
    try {
        $add_trainee_sql = $conn->prepare ("INSERT INTO `tbl_training_trainees`(`training_id`, `employee_id`, `user_id`, `date_joined`, `is_removed`) VALUES (?,?,?,?,?)");
        $add_trainee_sql->execute([$training_id,$employee_id,$user_id,$date_now,0]);
        echo 'Trainee successfully added';
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}

if (isset($_POST['remove_trainee'])){
    $training_id = $_POST['training_id'];
    $employee_id = $_POST['employee_id'];

    try {
        $remove_trainee = $conn->prepare("UPDATE `tbl_training_trainees` SET `is_removed`= 1 WHERE training_id = ? AND employee_id = ?");
        $remove_trainee->execute([$training_id,$employee_id]);
        echo 'Trainee successfully removed';
    }catch (Exception $e){
        echo $e;
    }
}

if (isset($_POST['submit_request'])){
    $training_id = $_POST['training_id'];
    $type = 'request';
    $status = 1;
    $date_now = date('Y-m-d H:i:s');

    try {
        $conn->beginTransaction();
        $save_submit = $conn->prepare("INSERT INTO `tbl_trainings_external_progress`(`training_id`, `type`, `status`, `date_created`) 
                                            VALUES (?,?,?,?)");
        $save_submit->execute([$training_id,$type,$status,$date_now]);

//        UPDATE STATUS
        $update_status = $conn->query("UPDATE `tbl_trainings` SET `status`='Request submitted',`date_updated`='$date_now' WHERE `training_id` = $training_id");

        $conn->commit();
        echo 'success';
    }catch (Exception $e){
        $conn->rollBack();
        echo $e;
    }
}

if (isset($_POST['submit_review'])){

    $training_id = $_POST['training_id'];
    $type = 'review';
    $status = 1;
    $date_now = date('Y-m-d H:i:s');

    try {
        $conn->beginTransaction();

        $save_submit = $conn->prepare("UPDATE `tbl_trainings_external_progress` SET `review_status`= ?,`date_updated` = ? WHERE training_id = ?");
        $save_submit->execute(['accepted',$date_now,$training_id]);

//        UPDATE STATUS
        $update_status = $conn->query("UPDATE `tbl_trainings` SET `status`='Review Accepted',`date_updated`='$date_now' WHERE `training_id` = $training_id");

//        SEND EMAIL NOTIFICATION TO ASSIGNED USER FOR APPROVAL
        $fetch_user_email = $conn->query("SELECT users.user_id, users.email, users.fname, users.lname, tbl_trainings.title
                                                    FROM users
                                                    INNER JOIN tbl_trainings_external_details ON users.user_id = tbl_trainings_external_details.approved_by
                                                    INNER JOIN tbl_trainings ON tbl_trainings.training_id = tbl_trainings_external_details.training_id
                                                    WHERE tbl_trainings_external_details.training_id = $training_id");
        $user_details = $fetch_user_email->fetch();
        $user_email = $user_details['email'];
        $user_fname = $user_details['fname'];
        $title = $user_details['title'];

        $message = '<div style="padding: 20px 0px 0px 0px; background-color: #189AA7;" class="shadow">
                        <img src="https://membersafety-surfers.com/assets/media/photos/safety-surfers-management-logo.png" style="width: 100%;">
                        <table width="100%" border="0" cellspacing="0" cellpadding="20" style="background-color: #47bcde; color: #5a5f61; font-family:verdana;">
                            <tr>
                                <td style="background-color: #fff; border-top: 10px solid #189AA7; border-bottom: 10px solid #189AA7;">
                                    <p style="margin-top: -5px;">Hi '.$user_fname.',</p>
                                    You are assigned for reviewing for a new External Training entitled '.ucwords($title).', please login to safety surfers and click notification, Thank you!
                                </td>
                            </tr>
                        </table>
                        <div style="text-align: center; padding: 20px 0px; color: #fff; background-color: #189AA7;">
                            Safety Surfers Management System<br>
                            <a href="https://safetysurfers.com/" style="color: white;">https://safetysurfers.com/</a>
                        </div>
                    </div>';

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = "den21rychu@gmail.com"; // Gmail address which you want to use as SMTP server
        $mail->Password = "pqapcuzvmtugwggr"; // Gmail address Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = '587';

        //$mail->setFrom('test_email@ipasspmt.com'); // Gmail address which you used as SMTP server
        $mail->setFrom("den21rychu@gmail.com");
        $mail->addAddress("$user_email"); // Email address where you want to receive emails (you can use any of your gmail address including the gmail address which you used as SMTP server)

        $mail->isHTML(true);
        $mail->Subject = "Safety Surfers Approve Training";
        $mail->Body = "$message";

        $mail->send();

        $conn->commit();
        echo 'success';
    }catch (Exception $e){
        $conn->rollBack();
        echo $e;
    }
}

if (isset($_POST['submit_approve'])){
    $training_id = $_POST['training_id'];
    $type = 'approve';
    $status = 1;
    $date_now = date('Y-m-d H:i:s');

    try {
        $conn->beginTransaction();

        $save_submit = $conn->prepare("UPDATE `tbl_trainings_external_progress` SET `approve_status`= ?,`date_updated` = ? WHERE training_id = ?");
        $save_submit->execute(['approved',$date_now,$training_id]);

//        UPDATE STATUS
        $update_status = $conn->query("UPDATE `tbl_trainings` SET `status`='Approved',`date_updated`='$date_now' WHERE `training_id` = $training_id");

        $conn->commit();
        echo 'success';
    }catch (Exception $e){
        $conn->rollBack();
        echo $e;
    }
}

if (isset($_POST['update_remarks'])){
    $training_id = $_POST['training_id'];
    $remarks = $_POST['remarks'];

    try {
        $update_remarks = $conn->prepare("UPDATE `tbl_trainings` SET `remarks`= ? WHERE `training_id` = ?");
        $update_remarks->execute([$remarks,$training_id]);

        echo 'Successfully Updated Remarks';
    }catch (\Exception $e){
        echo 'Something went wrong';
    }
}

// THIS AJAX CALL FUNCTION IS FOR LOADING SELECT EMPLOYEES IN ADDING PARTICIPANT TO THE NEWLY CREATED TRAINING
if (isset($_POST['modal_select_employees'])){
    $training_id = $_POST['training_id'];
    $trainer_qry = $conn->query("SELECT
                                        tbl_employees.employee_id,
                                        tbl_employees.firstname,
                                        tbl_employees.middlename,
                                        tbl_employees.lastname
                                        FROM
                                        tbl_employees
                                        WHERE
                                        tbl_employees.employee_id 
                                            NOT IN ((SELECT `employee_id` FROM `tbl_training_trainees` WHERE `training_id` = $training_id)) 
                                        AND
                                        tbl_employees.is_deleted = 0
                                        ");
    foreach ($trainer_qry as $trainer){
//                                                $training
        $employee_fullname = ucwords(strtolower($trainer['firstname']." ".$trainer['lastname']));
        ?>
        <option value="<?=$trainer['employee_id']?>"><?=$employee_fullname?></option>
        <?php
    }
}

// FUNCTION FOR ADDING EMPLOYEE TO THE NEWLY CREATED TRAINING
if (isset($_POST['add_trainee2'])){
    $training_id = $_POST['training_id'];
    $employee_id = $_POST['employee_id'];
    $date_now = date('Y-m-d H:i:s');
    try {
        $add_trainee_sql = $conn->prepare ("INSERT INTO `tbl_training_trainees`(`training_id`, `employee_id`, `user_id`, `date_joined`, `is_removed`) VALUES (?,?,?,?,?)");
        $add_trainee_sql->execute([$training_id,$employee_id,$user_id,$date_now,0]);
        echo 'success';
    }catch (Exception $e){
        echo 'failed';
    }
}

// FUNCTION FOR LOADING TABLE OF PARTICIPANTS TO THE MODAL OF NEWLY CREATED TRAINING
if (isset($_POST['load_table_participants'])){

    $training_id = $_POST['training_id'];
    $trainings = $conn->query("SELECT
                                                                    tbl_training_trainees.employee_id,
                                                                    tbl_training_trainees.date_joined,
                                                                    tbl_employees.firstname,
                                                                    tbl_employees.lastname
                                                                    FROM
                                                                    tbl_training_trainees
                                                                    INNER JOIN tbl_employees ON tbl_employees.employee_id = tbl_training_trainees.employee_id
                                                                    WHERE
                                                                    tbl_training_trainees.training_id = $training_id AND tbl_training_trainees.is_removed = 0");
    $count = 1;
    foreach ($trainings as $training) {
        $employee_name = ucwords(strtolower($training['firstname'].' '.$training['lastname']));
        ?>
        <tr>
            <th class="text-center" scope="row"><?=$count++?></th>
            <td class="table-borderless"><?=$employee_name?></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-secondary js-tooltip-enabled" id="delete" onclick="modal_remove_trainee(<?=$training_id?>,<?=$training['employee_id']?>)" data-toggle="tooltip" title="Remove employee from list" data-original-title="Delete">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
        <?php
    }
}

// FUNCTION FOR REMOVING EMPLOYEE IN THE MODAL OF ADDING PARTICIPANT TO THE NEWLY CREATED TRAINING
if (isset($_POST['modal_remove_trainee'])){
    $training_id = $_POST['training_id'];
    $employee_id = $_POST['employee_id'];

    try {
        $remove_trainee = $conn->prepare("UPDATE `tbl_training_trainees` SET `is_removed`= 1 WHERE training_id = ? AND employee_id = ?");
        $remove_trainee->execute([$training_id,$employee_id]);
        echo 'success';
    }catch (Exception $e){
        echo $e;
    }
}

// VIEW TRAINING DETAILS
if (isset($_POST['view_training'])){
    try {
        $training_id = $_POST['training_id'];
        $training_type = $_POST['training_type'];

        $training_sql ="SELECT
                        tbl_trainings.title,
                        tbl_trainings.location,
                        tbl_trainings.contract_no,
                        tbl_trainings.trainer,
                        tbl_trainings.training_hrs,
                        tbl_trainings.user_id,
                        tbl_trainings.employee_id,
                        tbl_trainings.`status`,
                        tbl_trainings.date_created,
                        tbl_trainings.date_expired,
                        tbl_trainings.date_updated,
                        tbl_trainings.training_date,  
                        DATEDIFF(date_expired,training_date) as diff,
                        IF(training_date >= date_expired, 'Expired', status) as exp,    
                        IF(DATEDIFF(date_expired,training_date) <=5  && DATEDIFF(date_expired,training_date) >= 0  , 'Soon to Expire', 'Valid')  as soon_exp 
                        FROM
                        tbl_trainings
                        WHERE
                        tbl_trainings.training_id = $training_id";

        if ($training_type == 'external'){
            $training_sql ="SELECT
                                tbl_trainings.title,
                                tbl_trainings.location,
                                tbl_trainings.contract_no,
                                tbl_trainings.trainer,
                                tbl_trainings.training_hrs,
                                tbl_trainings.user_id,
                                tbl_trainings.employee_id,
                                tbl_trainings.`status`,
                                tbl_trainings.date_created,
                                tbl_trainings.date_expired,
                                tbl_trainings.date_updated,
                                tbl_trainings.training_date,  
                                tbl_trainings.date_expired,
                                DATEDIFF(date_expired,training_date) as diff,
                                IF(training_date >= date_expired, 'Expired', status) as exp,    
                                IF(DATEDIFF(date_expired,training_date) <=5  && DATEDIFF(date_expired,training_date) >= 0  , 'Soon to Expire', 'Valid')  as soon_exp,    
                                tbl_trainings_external_details.course_price,
                                tbl_trainings_external_details.date_requested,
                                tbl_trainings_external_details.requested_by,
                                tbl_trainings_external_details.reviewed_by,
                                tbl_trainings_external_details.approved_by,
                                (SELECT  CONCAT(firstname, ' ', lastname)from tbl_employees WHERE tbl_employees.employee_id = tbl_trainings_external_details.requested_by)   AS requestor_name,
                                (SELECT  CONCAT(firstname, ' ', lastname)from tbl_employees WHERE tbl_employees.employee_id = tbl_trainings_external_details.reviewed_by)   AS reviewer_name,
                                (SELECT  CONCAT(firstname, ' ', lastname)from tbl_employees WHERE tbl_employees.employee_id = tbl_trainings_external_details.approved_by)   AS approval_name
    
                            FROM
                                tbl_trainings
                                INNER JOIN tbl_trainings_external_details ON tbl_trainings.training_id = tbl_trainings_external_details.training_id 
                            WHERE
                                tbl_trainings_external_details.training_id = $training_id";
        }
        $training_qry = $conn->query($training_sql);
        $training_details = $training_qry->fetch();
        $status = $training_details['status'];


        if ($training_details['exp'] == "Expired"){
            $status_td = '<span class="badge badge-danger ">'.$training_details['exp'].'</span>';
        } else {

            if ($training_details['soon_exp'] == "Soon to Expire"){
                $status_td = '<span class="badge bg-gd-sun text-white">'.$training_details['soon_exp'].'</span>';
            } else {
                $status_td = '<span class="badge badge-success">'."Valid".'</span>';
            }
        }
    } catch (Exception $e){

    }

    ?>
    <!-- Page Content -->
    <h2 class="content-heading pt-0">
        <!--        --><?//=ucwords($training_details['title'])?>
        <img class="img pd-l-30" src="assets/media/favicons/Fiafi logo.png" style="height: 60px; !important">
    </h2>
    <table class="table  table-borderless table-sm mt-20">
        <tbody>
        <tr>
            <td class="text-left font-w600" width="25%"> Course Title</td>
            <td class="border-bottom" width="30%"><?=$training_details['title']?></td>
            <td class="text-left font-w600">Date of Training</td>
            <td class="border-bottom"><?=date('F d Y', strtotime($training_details['date_created']))?></td>

        </tr>

        <tr>
            <td class="text-left font-w600">Location</td>
            <td class="border-bottom"><?=$training_details['location']?></td>
            <td class="text-left font-w600">Date Expired</td>
            <td class="border-bottom"><?=date('F d Y', strtotime($training_details['date_expired']))?></td>

        </tr>
        <tr>
            <td class="text-left font-w600">Training Hours</td>
            <td class="border-bottom"><?=$training_details['training_hrs']?></td>
            <td class="text-left font-w600">Contract no.</td>
            <td class="border-bottom"><?=$training_details['contract_no']?></td>
        </tr>

        <tr>
            <td class="text-left font-w600">Status</td>
            <td class="border-bottom"><span class=""><?=$status_td?></span></td>
            <td class="text-left font-w600">Conducted by:</td>
            <td class="border-bottom"><?=$training_details['trainer']?></td>
        </tr>
        </tbody>
    </table>
    <h2 class="content-heading pt-0">
        List of Participants
    </h2>
    <table class="table table-vcenter table-borderless">
        <thead>
        <tr class="table-bordered">
            <th class="text-center" style="width: 5%;">#</th>
            <th class="text-capitalize" style="width: 20%;">Employee name</th>
            <th class="text-center text-capitalize" style="width: 20%;">Position</th>
            <th class="text-center text-capitalize" style="width: 20%;">Signature</th>
        </tr >
        </thead>
        <tbody>
        <?php
        $trainings = $conn->query("SELECT
                                            tbl_training_trainees.employee_id,
                                            tbl_training_trainees.date_joined,
                                            tbl_employees.firstname,
                                            tbl_employees.lastname,
                                            tbl_position.position
                                            
                                            FROM
                                            tbl_training_trainees
                                            INNER JOIN tbl_employees ON tbl_employees.employee_id = tbl_training_trainees.employee_id
                                            INNER JOIN tbl_position ON tbl_position.position_id = tbl_employees.position
                                            WHERE
                                            tbl_training_trainees.training_id = $training_id AND tbl_training_trainees.is_removed = 0");
        $count = 1;
        foreach ($trainings as $training) {
            $employee_name = ucwords(strtolower($training['firstname'].' '.$training['lastname']));
            ?>
            <tr class="table-bordered">
                <th class="text-center" scope="row"><?=$count++?></th>
                <td ><?=$employee_name?></td>
                <td class="d-none d-sm-table-cell text-center">
                    <?=$training['position']?>
                </td>
                <td class="text-center"></td>

            </tr>
            <?php
        }
        if ($training_type == 'external'){
            ?>
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr class="table-borderless" >
                <td class="font-w600 text-right" style="width: 16.66%" >Total Attendees:</td>
                <td class="text-left" ><?=$count?></td>
            </tr>
            <tr class="table-borderless">
                <td class="font-w600 text-right" >Course Price:</td>
                <td class="text-left" >$<?=$training_details['course_price']?></td>
            </tr>
            <tr class="table-borderless">
                <td class="font-w600 text-right" >Total Amount:</td>
                <td class="text-left" >$<?=$training_details['course_price']*$count?></td>
            </tr>
            <tr class="table-borderless">
                <td class="font-w600 text-right" >Date Requested:</td>
                <td class="text-left" ><?=date('F d, Y', strtotime($training_details['date_requested']))?></td>
            </tr>


            <?php
        }
        ?>

        </tbody>
    </table>

    <?php

    if ($training_type == 'external'){ ?>

        <table class="table table-borderless">
            <thead></thead>
            <tbody>
            <tr class="text-center">
                <td>
                    <div class="form-material floating">
                        <input type="email" class="form-control text-center" id="material-email2" name="material-email2" value="<?=$training_details['requestor_name']?>" disabled>
                    </div>
                    <div class="text-center">
                        <label class="text-center" for="remarks">Requested by</label>
                    </div>
                </td>
                <td>
                    <div class="form-material floating">
                        <input type="email" class="form-control text-center" id="material-email2" name="material-email2" value="<?=$training_details['reviewer_name']?>" disabled>
                    </div>
                    <div class="text-center">
                        <label class="text-center" for="remarks">Reviewed by</label>
                    </div>
                </td>
                <td>
                    <div class="form-material floating">
                        <input type="email" class="form-control text-center" id="material-email2" name="material-email2" value="<?=$training_details['approval_name']?>" disabled>
                    </div>
                    <div class="text-center">
                        <label class="text-center" for="remarks">Approved by</label>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td><br></td>
            </tr>
            <tr class="text-muted text-center " style="font-size: 12px;">
                <td>
                    <p>Uncontrolled Copy if Printed</p>
                </td>
                <td>
                    <p>Fiafi Group Comapny, 2021. All Rights Reserved</p>
                </td>
                <td>
                    <p></p>
                </td>
            </tr>
            </tbody>
        </table>
    <?php } ?>
    <br>

    <?php
    $disable = '';
    if (isset($_POST['disable'])){
        $disable = "disabled";
    }
    if ($disable != "disabled"){
        ?>
        <div class="col-lg-6 d-print-none" >
            <label for="remarks">Attachments : </label>
            <br>
            <?php
            $images_qry = $conn->query("SELECT `training_img_id`, `training_id`, `image`, `date_uploaded` FROM `tbl_trainings_images` WHERE `training_id` = $training_id");
            $images = $images_qry->fetchAll();
            foreach ($images as $image) {
                ?>
                <a class="text-black" href="assets/media/photos/training/<?= $image['image'] ?>" alt=""> <?= $image['image'] ?></a><br>
                <?php
            }
            ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-block-option d-print-none" onclick="print_modal()">
                <i class="si si-printer"></i> Print Form
            </button>
            <button type="button" class="btn btn-option d-print-none" data-dismiss="modal">Close</button>
        </div>
        </div>
        <?php
    }
}

// function for uploading image for completion of training
if (isset($_POST['upload_file_training_id'])){
    $training_id = $_POST['upload_file_training_id'];

    // File upload configuration
    $targetDir = "../assets/media/photos/training/completion/";
//    $allowTypes = array('jpg','png','jpeg','gif');
    $allowTypes = array('zip', 'pdf', 'docx', 'xlsx');

    $fileNames = array_filter($_FILES['files']['name']);
    if(!empty($fileNames)){
        foreach($_FILES['files']['name'] as $key=>$val){
            // File upload path
            $fileName = basename($_FILES['files']['name'][$key]);
            $date1 = date("His"); // for unique file name
            $image = $date1.'-'.$fileName;
            $targetFilePath = $targetDir . $image;



            // Check whether file type is valid
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            if(in_array($fileType, $allowTypes)){
                // Upload file to server
                if(move_uploaded_file($_FILES["files"]["tmp_name"][$key], $targetFilePath)){
                    // Image db insert sql
                    try {

                        $conn->beginTransaction();

                        $insert = $conn->prepare("INSERT INTO `tbl_trainings_images`(`training_id`, `type`, `image`) VALUES (?,?,?)");
                        $insert->execute([$training_id,'completion',$image]);

//                        Update training status as valid
                        $update_training_status = $conn->prepare("UPDATE `tbl_trainings` SET `status`=? WHERE `training_id`= ?");
                        $update_training_status->execute(['valid',$training_id]);
                        $conn->commit();

                        echo 'uploaded';
                    }catch (Exception $e){
                        $conn->rollBack();
                        echo 'failed';
                    }

                }
            }
        }
    }else{
        echo "empty";
    }
}
// FUNCTION FOR UPLOADING ATTACHED IMAGES OF TRAINING
if (isset($_POST['attach_photo_training_id'])){
    $training_id = $_POST['attach_photo_training_id'];

    // File upload configuration
    $targetDir = "../assets/media/photos/training/";
//    $allowTypes = array('jpg','png','jpeg','gif');
    $allowTypes = array('zip', 'pdf', 'docx', 'xlsx');

    $fileNames = array_filter($_FILES['photos']['name']);
    if(!empty($fileNames)){
        foreach($_FILES['photos']['name'] as $key=>$val){
            // File upload path
            $fileName = basename($_FILES['photos']['name'][$key]);
            $date1 = date("His"); // for unique file name
            $image = $date1.'-'.$fileName;
            $targetFilePath = $targetDir . $image;



            // Check whether file type is valid
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
            if(in_array($fileType, $allowTypes)){
                // Upload file to server
                if(move_uploaded_file($_FILES["photos"]["tmp_name"][$key], $targetFilePath)){
                    // Image db insert sql
                    try {

                        $conn->beginTransaction();

                        $insert = $conn->prepare("INSERT INTO `tbl_trainings_images`(`training_id`, `type`, `image`) VALUES (?,?,?)");
                        $insert->execute([$training_id,'standard',$image]);

//                        Update training status as valid
                        $update_training_status = $conn->prepare("UPDATE `tbl_trainings` SET `status`=? WHERE `training_id`= ?");
                        $update_training_status->execute(['invalid',$training_id]);
                        $conn->commit();

                        echo 'uploaded';
                    }catch (Exception $e){
                        $conn->rollBack();
                        echo 'failed';
                    }

                }
            }
        }
    }else{
        echo "empty";
    }
}

if (isset($_POST['view_attached_file'])){
    $training_id = $_POST['training_id'];

    $attached_image_qry = $conn->prepare("SELECT `training_img_id`, `type`, `image`, `date_uploaded` FROM `tbl_trainings_images` WHERE `training_id` = ? AND `type` = ?");
    $attached_image_qry->execute([$training_id,'completion']);
    $attached_images = $attached_image_qry->fetchAll();
    foreach ($attached_images as $attached_image){
        $image = $attached_image['image'];
        $type = $attached_image['type'];
        $training_img_id = $attached_image['training_img_id'];
        ?>
        <div class="col-md-6 animated fadeIn mt-10">
            <div class="options-container fx-item-zoom-in fx-overlay-slide-left">
                <a class="text-black" href="assets/media/photos/training/completion/<?=$image?>" class="img-link" target="_blank" >
                    <?=$image?>
                </a>
            </div>
        </div>
        <?php
    }
}


if (isset($_POST['add_new_item'])){

    try {
        $hours_inhouse = $_POST['total_hours_inhouse'];
        $hours_client = $_POST['total_hours_client'];
        $hours_third_party = $_POST['total_hours_third_party'];
        $house_induction = $_POST['total_hours_induction'];
        $trainee_inhouse = $_POST['total_trainee_inhouse'];
        $trainee_client  = $_POST['total_trainee_client'];
        $trainee_third_party  = $_POST['total_trainee_third_party'];
        $trainee_induction= $_POST['total_trainee_induction'];
        $year = $_POST['select_year'];

        $total_trainee = $trainee_inhouse + $trainee_client + $trainee_third_party+ $trainee_induction;
        $total_hours = $hours_inhouse + $hours_client + $hours_third_party + $house_induction;

        $add_itd_qry = $conn->prepare("INSERT INTO 
                                                    tbl_training_metrics
                                                    (`total_hours_inhouse`,
                                                    `total_trainee_inhouse`, 
                                                    `total_hours_client`,
                                                    `total_trainee_client`,
                                                    `total_hours_third_party`,
                                                    `total_trainee_third_party`,
                                                    `total_hours_induction`,
                                                    `total_trainee_induction`,
                                                    `total_trainee`,
                                                    `total_hours`,
                                                    `year`,
                                                    `is_deleted`
                                                    )
                                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $add_itd_qry->execute([
            $hours_inhouse,
            $hours_client,
            $hours_third_party,
            $house_induction,
            $trainee_inhouse,
            $trainee_client,
            $trainee_third_party,
            $trainee_induction,
            $total_trainee,
            $total_hours,
            $year,
            0
        ]);
        echo "Training Metrics Successfully Added";
    } catch (Exception $e){
        echo $e;
    }
    ?>
    <?php
}

$conn = null;

?>
