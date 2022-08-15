<?php
session_start();
include 'conn.php';
if (isset($_SESSION['super_admin_id'])) {
    $super_admin_email = $_SESSION['super_admin_email'];
    $super_admin_id = $_SESSION['super_admin_id'];
}
//header("location:super_admin.php");


