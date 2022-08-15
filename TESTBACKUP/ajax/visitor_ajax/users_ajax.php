<?php

if (isset($_POST['change_access'])){
    $access_type_id = $_POST['access_type_id'];
    $users_user_id = $_POST['users_user_id'];
    $date_now = date('Y-m-d H:i:s');
    print_r($_POST);
    try {
//    CHECK IF ACCESS ALREADY EXIST
        $check = $conn->query("SELECT `access_id`, `status` FROM `user_access` WHERE `access_type_id` = $access_type_id AND `user_id` = $users_user_id");
        if ($check->rowCount() > 0){
            $access = $check->fetch();
            $access_id = $access['access_id'];
            $status = $access['status'];
            $new_status = 0;
            if ($status == 0){
                $new_status = 1;
            }else{
                $new_status = 0;
            }

            $update_access = $conn->query("UPDATE `user_access` SET `status`=$new_status WHERE `access_id` = $access_id");
        }else{
            $insert = $conn->query("INSERT INTO `user_access`(`access_type_id`, `user_id`, `status`, `date_created`) VALUES ($access_type_id,$users_user_id,0,'$date_now')");

        }
    }catch (Exception $e){
        echo $e;
    }

}

if (isset($_POST['add_new_user'])){
//    $fname = $_POST['fname'];
//    $lname = $_POST['lname'];
    try {

        $user_type = $_POST['user_type'];
        $employee_id = $_POST['employee_id'];
        $username = $_POST['username'];
//        $email = $_POST['email'];
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $get_employee_email_qry = $conn->query("SELECT `email` FROM tbl_employees WHERE employee_id = $employee_id" );
        $get_employee_email = $get_employee_email_qry->fetchAll();
        $employee_email = $get_employee_email[0]['email'];
        //    CHECK IF USERNAME AND EMAIL ALREADY EXISTS
        $check_username = $conn->prepare("SELECT `user_id` FROM `users` WHERE username = ? AND  deleted = 0");
        $check_username->execute([$username]);
        $chech_username_count = $check_username->rowCount();
//        $check_email = $conn->prepare("SELECT `user_id` FROM `users` WHERE email = ? AND  deleted = 0");
//        $check_email->execute([$email]);
//        $chech_email_count = $check_email->rowCount();


        if ($chech_username_count > 0){
            echo 'username';
//        }elseif ($chech_email_count > 0 && $chech_username_count == 0){
//            echo 'email';
//        }elseif ($chech_email_count > 0 && $chech_username_count > 0){
//            echo 'both';
        }else{
            $insert = $conn->prepare("INSERT INTO `users`(`email`,`user_type`,`emp_id`, `username`, `password`, `subscriber_id`, `status`) 
                                            VALUES (?,?,?,?,?,?,?)");
            $insert->execute([$employee_email,$user_type,$employee_id,$username,$hashed_password,$subscriber_id,1]);
            echo 'success';

        }
    }catch (Exception $e){
        echo $e;
    }
}

if (isset($_POST['edit_user_id'])){
    $fname = $_POST['edit_fname'];
    $lname = $_POST['edit_lname'];
    $username = $_POST['edit_username'];
    $email = $_POST['edit_email'];
    $password = $_POST['edit_password'];
    $edit_user_id = $_POST['edit_user_id'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

//    CHECK IF USERNAME AND EMAIL ALREADY EXISTS
    $check_username = $conn->prepare("SELECT `user_id` FROM users WHERE username = ? AND `user_id` != ? AND deleted = 0 ");
    $check_username->execute([$username,$edit_user_id]);
    $chech_username_count = $check_username->rowCount();

    $check_email = $conn->prepare("SELECT `user_id` FROM users WHERE `email` = ? AND `user_id` != ?AND  deleted = 0");
    $check_email->execute([$email, $edit_user_id]);
    $chech_email_count = $check_email->rowCount();


    if ($chech_username_count > 0 && $chech_email_count == 0){
        echo 'username';
    }elseif ($chech_email_count > 0 && $chech_username_count == 0){
        echo 'email';
    }elseif ($chech_email_count > 0 && $chech_username_count > 0){
        echo 'both';
    }else{

        try {
            $update_employee_tbl = $conn->prepare("UPDATE `tbl_employees`  SET 
                                                        `firstname` = ?,                                                         
                                                        `lastname` = ?,
                                                        `email`= ?
                                                        WHERE `employee_id` = ?");

            $update_user_tbl    = $conn->prepare("UPDATE `users`  SET 
                                                        `fname` = ?,                                                         
                                                        `lname` = ?,
                                                        `username` = ?,              
                                                        `email`=?,
                                                        `password` = ?    WHERE `emp_id` = ?");
            $update_user_tbl->execute([$fname,$lname,$username,$email,$hashed_password,$edit_user_id]);
            $update_employee_tbl->execute([$fname,$lname,$email, $edit_user_id]);

            echo 'success';
        }catch (Exception $e){
            echo $e;
        }
    }


}

if (isset($_POST['delete_user_id'])){
    $delete_user_id = $_POST['delete_user_id'];
    try {
        $update_access = $conn->prepare("UPDATE `users`  SET `deleted` = ? WHERE `user_id` = ?");
        $update_access->execute([1,$delete_user_id]);


    }catch (Exception $e){
        echo $e;
    }
}

$conn = null;
?>