<?php
include 'includes/head.php';
require_once 'conn.php';
if ($_POST) {
    $fname = $_POST['firstname'];
    $lname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $user_type = $_POST['select_plan'];
    $date = date('Y-m-d H:i:s');
    try {
        $conn->query("INSERT INTO `users`(`firstname`, `lastname`, `email`, `password`, `date_created`, `user_type`, `deleted`, `approved`, `date_updated`) 
                                        VALUES ('$fname','$lname','$email','$password','$date',$user_type,0,0,'$date')");

        echo '<script>document.location = "index.php";</script>';
        //        echo 'success';
    } catch (Exception $e) {
        echo $e;
    }
}
?>
<!-- Main Container -->
<main id="main-container">

    <!-- Hero -->
    <div class="bg-primary">
        <div class="bg-pattern bg-black-op-25" style="background-image: url('assets/media/various/bg-pattern.png');">
            <div class="content content-top text-center">
                <div class="py-50">
                    <h1 class="font-w700 text-white mb-10">PREMIUM PLAN</h1>
                    <h2 class="h4 font-w400 text-white-op">All managing modules are accessible!</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- END Hero -->

    <!-- Contact Form -->
    <!-- Contact Form Validation functionality is initialized in js/pages/be_pages_generic_contact.min.js which was auto compiled from _es6/pages/be_pages_generic_contact.js -->
    <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
    <div class="content content-full">
        <div class="row justify-content-center py-30">
            <div class="col-lg-12 text-center">
                <label for="be-contact-name">Please fill up the form</label>
                <hr>
            </div>
            <div class="col-lg-8 col-xl-6">
                <form id="register_form" action="register.php" method="post">
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="be-contact-name">First name</label>
                            <input type="text" class="form-control form-control-lg" id="fname" name="fname" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <label for="be-contact-name">Last name</label>
                            <input type="text" class="form-control form-control-lg" id="lname" name="lname" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-12" for="example-select">Select Plan</label>
                        <div class="col-md-9">
                            <select class="form-control" id="select_plan" name="select_plan">
                                <option value="0">Standard</option>
                                <option value="1">Premium</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-12" for="be-contact-email">Email</label>
                        <div class="col-12">
                            <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Enter your email.." required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-12" for="be-contact-email">Password</label>
                        <div class="col-12">
                            <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Enter your password.." required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-hero btn-alt-primary min-width-175">
                                <i class="fa fa-send mr-5"></i> Register
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END Contact Form -->

</main>
<!-- END Main Container -->

<script src="assets/js/codebase.core.min.js"></script>
<script src="assets/js/codebase.app.min.js"></script>
<?php
include 'includes/footer.php';
