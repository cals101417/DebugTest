<?php
require_once '../session_super_admin.php';

if (isset($_POST['add_new_subscriber'])){

    $user_id = $_SESSION['user_id'];
    $company_name = $_POST['company_name'];
    $company_contact  = $_POST['company_contact'];
    $company_email = $_POST['company_email'];
    $company_address = $_POST['company_address'];
    $city = $_POST['city'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $country = $_POST['country'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $date = date('Y-m-d H:i:s');
    $user_type = 2;
    $logo_src = '';
    $statusMsg = '';
    $check_username = $conn->prepare("SELECT `user_id` FROM `users` WHERE username = ? AND  deleted = 0");
    $check_username->execute([$username]);
    $check_email = $conn->prepare("SELECT `user_id` FROM `users` WHERE email = ? AND  deleted = 0");
    $check_email->execute([$company_email]);
    $chech_email_count = $check_email->rowCount();
    $chech_username_count = $check_username->rowCount();

    try {
        if ($chech_username_count > 0){
            echo 'username';
        }elseif ($chech_email_count > 0 && $chech_username_count == 0){
            echo 'email';
        }elseif ($chech_email_count > 0 && $chech_username_count > 0){
            echo 'both';
        } else {
            $fileName = basename($_FILES["company_logo"]["name"]);

            if (!empty($_FILES["company_logo"]["name"])){
                // Allow certain file formats
                // File upload path
                $targetDir = "../assets/media/photos/company/";
                $targetFilePath = $targetDir . $fileName;
                $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

                $allowTypes = array('jpg','png','jpeg','gif');
                if(in_array($fileType, $allowTypes)){
                    // Upload file to server
                    if(move_uploaded_file($_FILES["company_logo"]["tmp_name"], $targetFilePath)){
                        // Insert image file name into database
                        $logo_src = $fileName;
                        $statusMsg = "success";
                    }else{
                        $statusMsg = "Sorry, there was an error uploading your file.";
                    }
                }else{
                    $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
                }
            }else{
                $logo_src = "NONE";
            }
            $insert_subscribers = $conn->prepare("INSERT INTO `tbl_subscribers`(`country`,`city`,`email`,`access_name`,`company_name`, `type_of_access`, `date_created`, `contact_number`, `address`,`logo_src`) 
                                            VALUES (?,?,?,?,?,?,?,?,?,?)");
            $insert_subscribers->execute([$country, $city,$company_email,"test",$company_name ,"personal",$date,$company_contact,$company_address,$logo_src]);

            $subscriber_id = $conn->lastInsertId();
            $insert_user = $conn->prepare("INSERT INTO `users`(`email`,`user_type`,`emp_id`, `username`, `password`, `subscriber_id`, `status`,`lname`,`fname`) 
                                            VALUES (?,?,?,?,?,?,?,?,?)");
            $insert_user->execute([$company_email,$user_type,null ,$username,$hashed_password,$subscriber_id,1,$last_name,$first_name]);
//            echo $statusMsg;

        }
    } catch (Exception $e){
        echo $e;
    }

}
//deactivate-activate

if (isset($_POST['action_subscriber_id'])){
    $subscriber_id = $_POST['action_subscriber_id'];
    $action_type = $_POST['action_type'];
    $is_deleted = "";
    if($action_type == "Deactivate"){
        $is_deleted = 1;
    } else {
        $is_deleted = 0;
    }
    echo $is_deleted;

    try {
            $deactivate_subscriber_qry = $conn->query("UPDATE tbl_subscribers SET is_deleted = $is_deleted WHERE subscriber_id = $subscriber_id");
            echo "Succesfully Updated";
//            echo $statusMsg;

    } catch (Exception $e){
        echo $e;
    }
}
if($_POST['edit_admin']){
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];

    $update_qry =  $conn->prepare("UPDATE tbl_admin 
                                            SET `first_name` = ?, 
                                            `last_name` = ?, 
                                            `password` = ?, 
                                            `username`= ?,
                                            `phone` = ?,
                                            `email`=?
                                         WHERE admin_id = ?");
    $update_qry->execute([$first_name,$last_name,$password,$username,$contact_number,$email,$super_admin_id]);
    echo "Succcessfully updated your Account";

?>

<?php
}
$conn = null;