<?php
require_once '../session.php';
date_default_timezone_set('Asia/Manila');

if (isset($_POST['edit_training'])){
    $training_id = $_POST['training_id'];
    $title = $_POST['title'];
    $location = $_POST['location'];
    $contract_no = $_POST['contract_no'];
    $date = $_POST['date'];
    $date_expired = $_POST['date_expired'];
    $training_hrs = $_POST['training_hrs'];
    $status = $_POST['status'];
    $type = $_POST['type'];
    $remarks = $_POST['remarks'];
    $date_now = date('Y-m-d H:i:s');
    $employee_id = 0;
    $conducted_by = '';

    try {
        $conn->beginTransaction();
//        IF TRAINING IS IN-HOUSE
        if ($type == '1'){ // IF TRAINING IS INTERNAL
            $employee_id = $_POST['select_trainer'];
            $employee_qry = $conn->query("SELECT tbl_employees.firstname, tbl_employees.lastname FROM tbl_employees WHERE tbl_employees.employee_id = $employee_id");
            $employee = $employee_qry->fetch();
            $employee_name = ucwords(strtolower($employee['firstname'].' '.$employee['lastname']));
            $conducted_by = $employee_name;
        }else if ($type == '2'){ // IF TRAINING IS FOR CLIENT
            $conducted_by = $_POST['trainer'];
        }else if ($type == '3'){
            $conducted_by = $_POST['trainer'];
            $date_requested = $_POST['date_requested'];
            $price = $_POST['price'];
            $select_requested = $_POST['select_requested'];
            $select_reviewed = $_POST['select_reviewed'];
            $select_approved = $_POST['select_approved'];
        }
// UPDATE DATA IN TRAINING TABLE
        $update_training = $conn->prepare("UPDATE `tbl_trainings` 
                                                        SET 
                                                        `title` = ?,
                                                        `location` = ?,
                                                        `contract_no` = ?,
                                                        `remarks` = ?,
                                                        `trainer` = ?,
                                                        `training_hrs` = ?,
                                                        `employee_id` = ?,
                                                        `status` = ?,
                                                        `date_created` = ?,
                                                        `date_expired` = ?,
                                                        `date_updated` = ?
                                                        WHERE `training_id` = ?");
        $update_training->execute([$title,$location,$contract_no,$remarks,$conducted_by,$training_hrs,$employee_id,$status,$date,$date_expired,$date_now,$training_id]);

        // IF TRAINING IS THIRD PARTY THEN SAVE OTHER DETAILS TO tbl_trainings_external_details
        if ($type == '3'){
            $insert_external_details = $conn->prepare("UPDATE `tbl_trainings_external_details` 
                                                                SET
                                                                `course_price` = '',
                                                                `date_requested` = '',
                                                                `requested_by` = '',
                                                                `reviewed_by` = '',
                                                                `approved_by` = '' 
                                                                WHERE
                                                                    `training_id` = $training_id");
            $insert_external_details->execute([$training_id,$price,$date_requested,$select_requested,$select_reviewed,$select_approved]);
        }

        $conn->commit();
        echo 'Training successfully updated';
    }catch (Exception $e){
        $conn->rollBack();
        echo 'Sorry, Something wen\'t wrong'. $e;
    }
}



$conn = null;