<?php
require_once '../session.php';

if (isset($_POST['edit_department'])){
    $department_id = $_POST['department_id'];

    $pos_query = $conn->query("SELECT `department` FROM `tbl_department` WHERE `department_id` = $department_id");
    $pos_fetch = $pos_query->fetch();

    $department_name = $pos_fetch['department'];

    echo $department_name;

}

if (isset($_POST['update_department'])){
    $department_id = $_POST['department_id'];
    $department_name = $_POST['department'];

    try {
        $update = $conn->query("UPDATE `tbl_department` SET `department`='$department_name' WHERE `department_id`=  $department_id");
        echo 'department Successfully Updated';
    }catch (Exception $e){
        echo 'Something went wrong';
    }

}

if (isset($_POST['remove_department'])){
    $department_id = $_POST['department_id'];

    try {
        $update = $conn->query("UPDATE `tbl_department` SET `is_deleted`= 1 WHERE `department_id`= $department_id");
        echo 'success';
    }catch (Exception $e){
        echo 'error';
    }

}
$conn = null;