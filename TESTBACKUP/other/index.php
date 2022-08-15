<?php
session_start();
require_once 'conn.php';
include 'includes/head.php';
if (isset($_SESSION['user_id'])){
    header("location: dashboard.php");
}
if ($_POST){
    $username = $_POST['login-user'];
    $password = $_POST['login-password'];
    $date = date('Y-m-d H:i:s');
    try {
        $stmt = $conn->prepare ("SELECT * FROM `users` WHERE users.username = ? AND deleted = 0 ");

        $stmt->execute([$username]);
        $count = $stmt->rowCount();
        if($count > 0)
        {
            $users = $stmt->fetch();
            if (password_verify($password,$users['password'])){
                $_SESSION['login-email'] = $users['email'];
                $_SESSION['user_id'] = $users['user_id'];
                $_SESSION['subscriber_id'] = $users['subscriber_id'];
                $_SESSION['session_emp_id'] = $users['emp_id'];
//            echo $_SESSION['login-email'];
                echo '<script>document.location = "dashboard.php";</script>';
            }else{
                echo '<script>alert("email and password does not match!");</script>';
            }
        }
        else
        {
            echo '<script>alert("User does not exist in the database")</script>';
        }
    }catch (Exception $e){
        echo $e;
    }
}
?>
    <!-- Main Container -->
    <main id="main-container">

        <!-- Page Content -->
        <div class="bg-body-dark bg-pattern" style="background-image: url('assets/media/various/bg-pattern-inverse.png');">
            <div class="row mx-0 justify-content-center">
                <div class="hero-static col-lg-6 col-xl-4">
                    <div class="content content-full overflow-hidden">
                        <div class="py-30 text-center">
                            <a class="font-w700" href="index.php">
                                <img class="img pd-l-30" src="assets/media/favicons/Fiafi logo.png" style="height: 60px; !important">
                            </a>
                            <h1 class="h4 font-w700 mt-30 mb-10">Welcome to Your Dashboard</h1>
                            <h2 class="h5 font-w400 text-muted mb-0">It’s a great day today!</h2>
                        </div>

                        <!-- Sign In Form -->
                        <!-- jQuery Validation functionality is initialized with .js-validation-signin class in js/pages/op_auth_signin.min.js which was auto compiled from _es6/pages/op_auth_signin.js -->
                        <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
                        <form id="login_form" action="index.php" method="post">
                            <div class="block block-themed block-rounded block-shadow">
                                <div class="block-header bg-gd-dusk">
                                    <h3 class="block-title">Please Sign In</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option">
                                            <i class="si si-wrench"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="login-user">Username</label>
                                            <input type="text" class="form-control" id="login-user" name="login-user" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="login-password">Password</label>
                                            <input type="password" class="form-control" id="login-password" name="login-password" required>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-sm-6 d-sm-flex align-items-center push">
                                            <div class="custom-control custom-checkbox mr-auto ml-0 mb-0">
                                                <input type="checkbox" class="custom-control-input" id="login-remember-me" name="login-remember-me">
                                                <label class="custom-control-label" for="login-remember-me">Remember Me</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 text-sm-right push">
                                            <button type="submit" class="btn btn-alt-primary">
                                                <i class="si si-login mr-10"></i> Sign In
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="block-content bg-body-light">
                                    <div class="form-group text-center">
                                        <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="register.php">
                                            <i class="fa fa-plus mr-5"></i> Create Account
                                        </a>
                                        <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="op_auth_reminder3.html">
                                            <i class="fa fa-warning mr-5"></i> Forgot Password
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- END Sign In Form -->
                    </div>
                </div>
            </div>
        </div>
        <!-- END Page Content -->

    </main>
    <!-- END Main Container -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script>
        // $(document).ready(function () {
        //     $("#login_form").submit(function (event) {
        //         event.preventDefault();
        //
        //         username = $("#login-username").val();
        //         password = $("#login-password").val();
        //
        //         $.ajax({
        //             method: "POST",
        //             url: "ajax/login_ajax.php",
        //             data: {
        //                 username: username,
        //                 password: password
        //             },
        //             success: function (response){
        //                 alert(response);
        //             }
        //         })
        //     });
        // });
    </script>
<?php
include 'includes/footer.php';