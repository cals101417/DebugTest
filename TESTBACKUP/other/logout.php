<?php
//logout.php
session_start();
session_destroy();
if (isset($_SESSION['super_admin_id'])){
    header("location:super_admin.php");
} else {
    header("location:index.php");
}


?>