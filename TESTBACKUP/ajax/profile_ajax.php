<?php
require_once '../session.php';

if (isset($_POST['update_profile'])){
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $type = $_POST['select_user_type'];
    $email = $_POST['email'];
    $date_now = date('Y-m-d H:i:s');

    if (isset($_POST['password']) && $_POST['password'] != ''){
        $new_password = password_hash($_POST['password'],PASSWORD_DEFAULT);
        $update_sql = "UPDATE `tbl_employees` SET `firstname`='$fname',`lastname`='$lname',`email`='$email',`password`='$new_password',`img_src` = '',`date_updated` = '$date_now' WHERE `employee_id` = $session_emp_id";

        $update_sql2 = "UPDATE `users` SET `fname` = $fname, `lname` = $lname, `password`='$new_password',`user_type`=$type,`date_updated` = '$date_now' ,`profile_pic` = '' WHERE `emp_id` = $session_emp_id";
    }else{
        if (!empty($_FILES["profile_img"]["name"])){
            // File upload path
            $targetDir = "../assets/media/photos/employee/";
            $fileName = basename($_FILES["profile_img"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

            $allowTypes = array('jpg','png','jpeg','gif');
            if(in_array($fileType, $allowTypes)){
                // Upload file to server
                if(move_uploaded_file($_FILES["profile_img"]["tmp_name"], $targetFilePath)){
                    // Insert image file name into database

                    $update_sql = "UPDATE `tbl_employees` SET `firstname`='$fname',
                                                               `lastname`='$lname',
                                                               `email`='$email',
                                                               `img_src` = '$fileName',
                                                               `date_updated` = '$date_now' 
                                                                WHERE `employee_id` = $session_emp_id";

                    $update_sql2 = "UPDATE `users` SET `fname`='$fname',
                                                      `lname`='$lname',
                                                      `email`='$email',
                                                      `user_type`=$type,
                                                      `profile_pic` = '$fileName',
                                                      `date_updated` = '$date_now'
                                                       WHERE `emp_id` = $session_emp_id";

                }else{
                    $statusMsg = "Sorry, there was an error uploading your file.";
                }
            }else{
                $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
            }
        }else{
            $update_sql  = "UPDATE `tbl_employees` SET `firstname`='$fname',`lastname`='$lname',`email`='$email',`img_src` = '',`date_updated` = '$date_now' WHERE `employee_id` = $session_emp_id";
            $update_sql2 = "UPDATE `users` SET `fname`='$fname',`lname`='$lname',`email`='$email',`user_type`=$type,`profile_pic` = '',`date_updated` = '$date_now' WHERE `emp_id` = $session_emp_id";
        }
    }
    try {

        $update_profile = $conn->query($update_sql);
        $update_profile2 = $conn->query($update_sql2);
        echo 'success';
//        echo $update_sql;
    }catch (Exception $e){
        echo $e;
    }

}

$conn = null;
?>