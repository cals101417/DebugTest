<?php
require_once '../conn.php';

if (isset($_POST['register'])){
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $user_type = $_POST['plan'];
    $date = date('Y-m-d H:i:s');
    try {
        $conn->query("INSERT INTO `users`(`fname`, `mname`, `lname`, `email`, `password`, `date_created`, `user_type`, `deleted`, `approved`, `date_updated`) 
                                        VALUES ('$fname','$mname','$lname','$email','$password','$date',$user_type,0,0,'$date')");

        echo 'success';
    }catch (Exception $e){
        echo 'error';
    }
}

$conn = null;