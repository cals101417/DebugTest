<?php
require_once '../session.php';
if (isset($_POST['add_link'])){
    try {
        $link = $_POST['link'];
        $is_active=  0;
        $serial = $_POST['serial'];
        $date = date('Y-m-d H:i:s');
        $add_link = $conn->query("INSERT INTO `tbl_visitor`(`serial`,`sub_id`,`user_id`,`link`,`is_active`,`date_generated`) 
                                        VALUES ('$serial','$subscriber_id','$session_emp_id','$link',$is_active,'$date')");
        echo "SUCCESSFULLY CREATED LINK";
    } catch (Exception $e) {
        echo $e;
    }
}

$conn = null;
