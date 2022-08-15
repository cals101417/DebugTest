<?php
session_start();
include 'conn.php';
if(!isset($_SESSION['login-email']))
{
    header("location:index.php");
}
$user_id = $_SESSION['user_id'];
$subscriber_id = $_SESSION['subscriber_id'];
$session_emp_id =  $_SESSION['session_emp_id'];
