<link rel="stylesheet" href="assets/js/plugins/select2/css/select2.min.css">
<link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
<?php
include 'includes/head.php';

include "session_super_admin.php";

if ($_POST){
    $username = $_POST['login-user'];
    $password = $_POST['login-password'];
    $date = date('Y-m-d H:i:s');
    try {
        $stmt = $conn->prepare ("SELECT * FROM `tbl_admin` WHERE username = ? AND is_deleted = 0 ");

        $stmt->execute([$username]);
        $count = $stmt->rowCount();
        if($count > 0)
        {
            $users = $stmt->fetch();
            if (password_verify($password,$users['password'])){
                $_SESSION['super_admin_email'] = $users['email'];
                $_SESSION['super_admin_id'] = $users['admin_id'];

                echo '<script>document.location = "super_admin.php";</script>';
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
if (!isset($_SESSION['super_admin_id'])){
    ?>
    <!-- Page Content -->
    <div class="bg-body-dark bg-pattern" style="background-image: url('assets/media/various/bg-pattern-inverse.png');">
        <div class="row mx-0 justify-content-center">
            <div class="hero-static col-lg-6 col-xl-4">
                <div class="content content-full overflow-hidden">
                    <div class="py-30 text-center">
                        <a class="font-w700" href="super_admin.php.php">
                            <!--                                <img class="img pd-l-30" src="assets/media/favicons/Fiafi logo.png" style="height: 60px; !important">-->
                        </a>
                        <h1 class="h4 font-w700 mt-30 mb-10">Welcome to Your Dashboard</h1>
                        <h2 class="h5 font-w400 text-muted mb-0">It’s a great day today!</h2>
                    </div>
                    <form id="login_form" action="super_admin.php" method="post">
                        <div class="block block-themed block-rounded block-shadow">
                            <div class="block-header bg-gd-emerald">
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
    <?php
} else {
    ?>

    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
        <?php
        include 'includes/sidebar_admin.php';
        include 'includes/header.php';

        ?>
        <!-- Main Container -->

        <main id="main-container" style="min-height: 871px;">
            <div class="bg-gd-sun">
                <div class="bg-pattern" style="background-image: url('assets/media/photos/construction13.jpg'); background-repeat: no-repeat; background-size: 100%; ">
                    <div class="content content-top content-full text-center">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10">Manage Admin</h1>
                            <h2 class="h4 font-w400 text-white-op">Get to know your passionate team.</h2>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->
            <!-- Page Content -->
            <div class="content">
                <h2 class="content-heading">
                    <button type="button" class="btn btn-sm btn-rounded btn-primary d-md-none float-right ml-5" data-toggle="class-toggle" data-target=".js-inbox-nav" data-class="d-none d-md-block">Menu</button>
                    <button type="button" class="btn btn-sm btn-rounded btn-success float-right" data-toggle="modal" data-target="#add_new_user_modal">Add New Subscriber</button>
                    Subscribers
                </h2>
                <!-- Team -->
                <div class="block">
                    <div class="block-content" id="subscriber_content">
                        <div class="row gutters-tiny py-20" id="display_div">
                            <?php
                            try {

                                $subribers = $conn->query("SELECT `fname`,
                                                                                `lname`, 
                                                                                `company_name`,
                                                                                `country`,
                                                                                `address`,
                                                                                `city`,
                                                                                tbl_subscribers.subscriber_id,
                                                                                tbl_subscribers.email,
                                                                                users.user_type,
                                                                                `logo_src`,
                                                                                `contact_number`,
                                                                                `is_deleted`
                                                                    FROM `tbl_subscribers` 
                                                                    LEFT JOIN  `users` ON tbl_subscribers.subscriber_id = users.subscriber_id WHERE user_type = 2");
                                $count = 1;
                                $all_subscribers = $subribers->fetchAll();
                            } catch (Exception $e) {
                                echo $e;
                            }

                            foreach ($all_subscribers as $subscriber) {
//                                    $users_user_id = $subscriber['emp_id'];
                                $employee_fullname = ucwords(strtolower($subscriber['fname']." ".$subscriber['lname']));
                                $edit_subscribers = json_encode($subscriber);
                                ?>
                                <div class="col-md-6 col-xl-3 shadow-sm">
                                    <div class="content">
                                        <div class="block-content block-content-full bg-gd-dusk">
                                            <img class="img-avatar img-avatar-thumb" src="assets/media/photos/company/<?=$subscriber['logo_src']?>" alt="">
                                        </div>
                                        <div class="block-content block-content-full ">
                                            <?php
                                            $address =$subscriber['address'];
                                            $city = $subscriber['city'];
                                            $country = $subscriber['country'];
                                            $company_name = $subscriber['company_name'];
                                            $email = $subscriber['email'];
                                            $contact = $subscriber['contact_number'];
                                            $subscriber_id = $subscriber['subscriber_id'];
                                            ?>
                                            <div class="font-w600 mb-5"><?=$company_name?></div>
                                            <address>
                                                <?=$address?><br>
                                                <?=$city?><br>
                                                <?=$country?><br>
                                                <span class="font-w600 font-size-sm text-danger"><?=$subscriber['email']?></span>
                                            </address>
                                        </div>
                                        <div class="btn-group float-right mb-10 mr-10" id="activate_btn<?=$subscriber_id?>"  >
                                            <?php if ($subscriber['is_deleted'] == 0){?>
                                                <button class="btn btn-sm btn-danger" onclick="deactivate_subscriber(<?=$subscriber_id?>,'Deactivate')">Deactivate</button>
                                                <?php
                                            } else {
                                                ?>
                                                <button class="btn btn-sm btn-success"  onclick="deactivate_subscriber(<?=$subscriber_id?>,'Activate')">Activate</button>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div  class="col-lg-12" id="edit_div" >
                            <div class="block ">
                                <div class="block-header ">
                                    <h2 class="block-title">Edit Super Admin Information</h2>
                                </div>
                                <div class="block-content">
                                    <div class="row justify-content-center py-20">
                                        <div class="col-xl-12">
                                            <form id="edit_admin_form"  onsubmit="return false;" >
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label" for="username">Username <span class="text-danger">*</span></label>
                                                            <div class="col-lg-8">
                                                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter a username.." required>
                                                                <input type="text" name="edit_admin" value="1">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label" for="email">Email <span class="text-danger">*</span></label>
                                                            <div class="col-lg-8">
                                                                <input type="email" class="form-control" id="email" name="email" placeholder="Your valid email.." required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label" for="password">Password <span class="text-danger">*</span></label>
                                                            <div class="col-lg-8">
                                                                <input type="password" class="form-control" id="password" name="password" placeholder="Choose a safe one.." required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label" for="cpassword">Confirm Password <span class="text-danger">*</span></label >
                                                            <div class="col-lg-8">
                                                                <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="..and confirm it!" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label" for="contact_number">Contact Number <span class="text-danger">*</span></label>
                                                            <div class="col-lg-8">
                                                                <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="Enter a username.." required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label" for="first_name">First Name  <span class="text-danger">*</span></label>
                                                            <div class="col-lg-8">
                                                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Your  First Name.." required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label" for="last_name">Last Name  <span class="text-danger">*</span></label>
                                                            <div class="col-lg-8">
                                                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Your  Last Name.." required >
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-lg-4 col-form-label" for="example-file-input">Display Picture</label>
                                                            <div class="col-lg-8">
                                                                <input type="file" id="example-file-input" name="example-file-input" required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group ">
                                                            <div class=" float-right">
                                                                <button type="submit" class="btn btn-alt-primary ">Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- END Main Container -->
    </div>
    <!--ADD NEW TOOLBOX TALKS MODAL-->
    <div class="modal fade" id="add_new_user_modal" role="dialog" aria-labelledby="add_attendance_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Add New Subscriber</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="add_subscribers" method="POST" class="js-validation-bootstrap">
                            <input type="hidden" name="add_new_subscriber" value="1">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <label class="col-lg-4" for="company_name">Company Name <span class="text-danger">*</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control form-control-sm" id="company_name" name="company_name" required>
                                        </div>
                                        <label  class="col-lg-4" for="company_contact">Contact <span class="text-danger">* </label>
                                        <div class="col-lg-8">

                                            <input type="text" class="form-control form-control-sm" id="company_contact" name="company_contact" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label  class="col-lg-4" for="company_email"> Email<span class="text-danger">*</span></label>
                                        <div class="col-lg-8">
                                            <input type="email" class="form-control form-control-sm" id="company_email" name="company_email" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label  class="col-lg-4" for="company_address">Office Address</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control form-control-sm" id="company_address" name="company_address" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label  class="col-lg-4" for="city">City</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control form-control-sm" id="city" name="city" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label  class="col-lg-4"col-form-label" for="country">Select Country</label>
                                        <div class="col-lg-8">
                                            <select class="js-select2 "id="country" name = "country"
                                                    style="width: 100%;" data-placeholder="Choose One.."
                                                    required>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group row">
                                        <label  class="col-lg-4" for="first_name">First name</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control form-control-sm" id="first_name" name="first_name" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label  class="col-lg-4" for="last_name">Last name</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control form-control-sm" id="last_name" name="last_name" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label  class="col-lg-4" for="location">User name</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control form-control-sm" id="username" name="username" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label  class="col-lg-4" for="val-password">Password</label>
                                        <div class="col-lg-8">
                                            <input type="password" autocomplete="on"  class="form-control form-control-sm" id="val-password" name="val-password" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label  class="col-lg-4" for="val-confirm-password">Confirm Password</label>
                                        <div class="col-lg-8">
                                            <input type="password"  autocomplete="on" class="form-control form-control-sm" id="confirm-password" name="val-confirm-password" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label  class="col-lg-4" >Company Logo</label>
                                        <!--                                        <div class="custom-file">-->
                                        <div class="col-lg-8">
                                            <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                            <input type="file" class="custom-file-input" id="company_logo" name="company_logo" data-toggle="custom-file-input">
                                            <label class="custom-file-label" for="company_logo">Choose file</label>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button  type="submit" class="form form-control btn btn-sm btn-hero btn-alt-primary">
                                        <i class="fa fa-save mr-5"></i> Save
                                    </button>
                                </div>
                            </div>

                    </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>


<?php } ?>



<script src="assets/js/codebase.core.min.js"></script>
<script src="assets/js/codebase.app.min.js"></script>

<!-- Page JS Plugins -->
<script src="assets/js/plugins/select2/js/select2.full.min.js"></script>
<script src="assets/js/plugins/jquery-auto-complete/jquery.auto-complete.min.js"></script>
<script src="assets/js/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="assets/js/pages/be_forms_validation.min.js"></script>


<!-- Page JS Code -->
<!--<script src="assets/js/pages/be_forms_plugins.min.js"></script>-->

<!-- Page JS Helpers (BS Datepicker + BS Colorpicker + BS Maxlength + Select2 + Masked Input + Range Sliders + Tags Inputs plugins) -->
<script>jQuery(function(){ Codebase.helpers('select2'); });</script>
<script>
    $(document).ready(function () {
        $('#edit_div').hide();
        $.ajax({
            type: 'GET',
            url: 'https://restcountries.com/v3.1/all',
            dataType: 'json',
            success: function(data) {
                let data1 = JSON.stringify(data);
                let data2 = JSON.parse(data1);
                for (let i = 0; i < data2.length; i++){

                    $('#country').append($('<option>', {
                        value: data2[i].name.common,
                        text : data2[i].name.common
                    }));
                    $('#edit_country').append($('<option>', {
                        value: data2[i].name.common,
                        text : data2[i].name.common
                    }));
                }
            },
            error: function() {
                console.log("Error adding employee function");
            }
        });

        $('#edit_admin_form').submit(function (event) {
            event.preventDefault();
            password = $('#password').val();
            c_password = $('#cpassword').val();
            if (password == c_password) {
                $.ajax({
                    type: 'POST',
                    url: 'ajax/super_admin_ajax.php',
                    data:$(this).serialize(),
                    success: function (data) {
                        // location.reload();
                        alert(data);
                    }

                });
            } else {
                alert("Password Mismatch");
            }
        });
        $("#add_subscribers").submit(function (event) {
            event.preventDefault();
            password = $('#val-password').val();
            c_password = $('#val-confirm-password').val();

            if (password == c_password){
                if (confirm("Are you sure you want to add?")) {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/super_admin_ajax.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (data) {
                            location.reload();
                        }
                    });
                }
            } else{
                alert("Password Do Not Match");
            }
        })
    });

    function edit_subscriber(data){
        $('#edit_subscriber').modal('show');
    }

    function deactivate_subscriber(subscriber_id,action){

        let activate_btn = 'activate_btn'+(subscriber_id);
        let new_button_status = 'Activate';
        let btn_color = 'success';
        if(action == 'Activate'){
            new_button_status = 'Deactivate';
            btn_color = 'danger';
        }
        let toast = Swal.mixin({
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-alt-success m-5',
                cancelButton: 'btn btn-alt-danger m-5',
                input: 'form-control'
            }
        });
        toast.fire({
            title: 'Are you sure you want to '+action+' subscriber?',
            text: '',
            type: 'warning',
            showCancelButton: true,
            customClass: {
                confirmButton: 'btn btn-alt-warning m-1',
                cancelButton: 'btn btn-alt-secondary m-1'
            },
            confirmButtonText: 'Yes!',
            html: false,
            preConfirm: e => {
                return new Promise(resolve => {
                    setTimeout(() => {
                        resolve();
                    }, 50);
                });
            }
        }).then(result => {
            if (result.value) {
                toast.fire('Subscriber '+action+'d !', 'This subscriber can no longer access the system.', 'success');
                $.ajax({
                    type: "POST",
                    url: "ajax/super_admin_ajax.php",
                    data: {
                        action_subscriber_id: subscriber_id,
                        action_type:  action
                    },
                    success: function(data){
                        // location.reload();
                        // alert(data)
                        $("#"+activate_btn).html('');
                        $("#"+activate_btn).append($('<button>', {
                            value: 'test',
                            text : new_button_status,
                            class: 'btn btn-sm btn-'+btn_color,
                            onclick: "deactivate_subscriber("+ subscriber_id+ ",'"+new_button_status +"')"
                        }));
                    }
                });
            } else if (result.dismiss === 'cancel') {
                toast.fire('Cancelled', 'Subscriber not Updated :)', 'error');
            }
        });
    }
    function edit_super_admin(){
        $('#edit_div').show();
        $('#display_div').hide();
        // $('#subscriber_content').html('');
        $.ajax({
            type: 'POST',
            url: 'ajax/super_admin_ajax.php',
            data: {
                edit_super_admin: 1
            },
            success: function (data){
                // $('#subscriber_content').html(data);
            }
        })
    }

</script>
<?php
include 'includes/footer.php';