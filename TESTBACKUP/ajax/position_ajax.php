<?php
require_once '../session.php';

if (isset($_POST['edit_position'])){
    $position_id = $_POST['position_id'];

    $pos_query = $conn->query("SELECT `position` FROM `tbl_position` WHERE `position_id` = $position_id");
    $pos_fetch = $pos_query->fetch();
    $position_name = $pos_fetch['position'];

}

if (isset($_POST['update_position'])){
    $position_id = $_POST['position_id'];
    $position_name = $_POST['position'];

    try {
        $update = $conn->query("UPDATE `tbl_position` SET `position`='$position_name' WHERE `position_id`=  $position_id");
        echo 'Position Successfully Updated';
    }catch (Exception $e){
        echo 'Something went wrong';
    }

}

if (isset($_POST['remove_position'])){
    $position_id = $_POST['position_id'];
    try {
        $update = $conn->query("UPDATE `tbl_position` SET `is_deleted`= 1 WHERE `position_id`= $position_id");
        echo 'success';
    }catch (Exception $e){
        echo 'error';
    }

}
$conn = null;