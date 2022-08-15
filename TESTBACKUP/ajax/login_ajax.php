<?php
require_once '../conn.php';

if (isset($_POST['username'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
}

$conn = null;