<?php
include 'session.php';
include 'includes/head.php';
?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">
    <?php
    include 'includes/sidebar.php';
    include 'includes/header.php';
    ?>
    <!-- Main Container -->
    <main id="main-container">

        <?php
        try {
            $profile_stmt = $conn->prepare("SELECT 
                                                        `firstname`, 
                                                        `lastname`, 
                                                        tbl_employees.email, 
                                                        `user_type`, 
                                                        `subscriber_id`, 
                                                        `img_src` 
                                                FROM `tbl_employees`
                                                INNER JOIN users ON tbl_employees.employee_id =  users.emp_id
                                                WHERE `emp_id` = ?");
            $profile_stmt->execute([$session_emp_id]);

            $fetch_profile = $profile_stmt->fetch();
            $fetch_firstname = $fetch_profile['firstname'];
            $fetch_lastname = $fetch_profile['lastname'];
            $fullastname = ucwords(strtolower($fetch_firstname.' '.$fetch_lastname));
            $fetch_email = $fetch_profile['email'];
            if ($fetch_profile['user_type'] == 0){
                $user_type = 'Admin';
            }else{
                $user_type = 'User';
            }
            $fetch_img_src = $fetch_profile['img_src'];
            $img_src = '';
            if ($fetch_img_src != '' || $fetch_img_src != null){
                $img_src = 'assets/media/photos/employee/'.$fetch_img_src;
            }else{
                $img_src = 'assets/media/avatars/avatar15.jpg';
            }
        }catch (Exception $e){
            echo $e;
        }
        ?>
        <!-- Page Content -->
        <!-- User Info -->
        <div class="bg-image bg-image-bottom" style="background-image: url('assets/media/photos/photo13@2x.jpg');">
            <div class="bg-primary-dark-op py-30">
                <div class="content content-full text-center">
                    <!-- Avatar -->
                    <div class="mb-15">
                        <a class="img-link" href="be_pages_generic_profile.html">
                            <img class="img-avatar img-avatar96 img-avatar-thumb" src="<?=$img_src?>" alt="">
                        </a>
                    </div>
                    <!-- END Avatar -->

                    <!-- Personal -->
                    <h1 class="h3 text-white font-w700 mb-10"><?=$fullastname?></h1>
                    <h2 class="h5 text-white-op">
                        <?=$user_type?>
                    </h2>
                    <!-- END Personal -->

                    <!-- Actions -->
<!--                    <button type="button" class="btn btn-rounded btn-hero btn-sm btn-alt-success mb-5">-->
<!--                        <i class="fa fa-plus mr-5"></i> Add Friend-->
<!--                    </button>-->
<!--                    <button type="button" class="btn btn-rounded btn-hero btn-sm btn-alt-primary mb-5">-->
<!--                        <i class="fa fa-envelope-o mr-5"></i> Message-->
<!--                    </button>-->
                    <!-- END Actions -->
                </div>
            </div>
        </div>
        <!-- END User Info -->

        <div class="content content-full">
            <h2 class="content-heading">Update Profile</h2>
            <form id="update_profile_form" method="post">
                <div class="row gutters-tiny">
                    <!-- Basic Info -->
                    <div class="col-md-7">
                        <div class="block block-rounded block-themed">
                            <div class="block-header bg-gd-primary">
                                <h3 class="block-title">Basic Info</h3>
                                <div class="block-options">
<!--                                    <button type="submit" class="btn btn-sm btn-alt-primary">-->
<!--                                        <i class="fa fa-save mr-5"></i>Save-->
<!--                                    </button>-->
                                </div>
                            </div>
                            <div class="block-content block-content-full">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="be-contact-name">First name</label>
                                        <input type="text" class="form-control form-control-lg" id="firstname" name="firstname" value="<?=$fetch_firstname?>" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="be-contact-name">Last name</label>
                                        <input type="text" class="form-control form-control-lg" id="lastname" name="lastname" value="<?=$fetch_lastname?>" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12">
                                        <label for="select_user_type">User Type</label>
                                        <select class="form-control" id="select_user_type" name="select_user_type">
                                            <?php
                                            $selected_admin = '';
                                            $selected_user = '';
                                            if ($user_type == 'Admin'){
                                                $selected_admin = 'selected';
                                            }else{
                                                $selected_user = 'selected';
                                            }
                                            ?>
                                            <option value="0" <?=$selected_admin?>>Admin</option>
                                            <option value="1" <?=$selected_user?>>User</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-12" for="be-contact-email">Email</label>
                                    <div class="col-12">
                                        <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?=$fetch_email?>" placeholder="Enter your email.." required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-12" for="profile_img">Image</label>
                                    <div class="col-12">
                                        <input type="file" class="" id="profile_img" name="profile_img">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-hero btn-alt-primary min-width-175">
                                            <i class="fa fa-save mr-5"></i> Update Profile
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Basic Info -->

                    <!-- Change Password -->
                    <div class="col-md-5">
                        <!-- Status -->
                        <div class="block block-rounded block-themed">
                            <div class="block-header bg-gd-primary">
                                <h3 class="block-title">Change Password</h3>
                                <div class="block-options">

                                </div>
                            </div>
                            <div class="block-content block-content-full">
                                <div class="form-group row">
                                    <label class="col-12" for="be-contact-email">New Password</label>
                                    <div class="col-12">
                                        <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Enter your password.." >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-12" for="confirm_password">Confirm Password</label>
                                    <div class="col-12">
                                        <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" placeholder="Re-enter password.." >
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-sm btn-alt-primary">
                                    <i class="fa fa-save mr-5"></i>Change Password
                                </button>
                            </div>
                        </div>
                        <!-- END Status -->
                    </div>
                    <!-- END More Options -->
                </div>

            </form>
        </div>
        <!-- END Contact Form -->

    </main>
    <!-- END Main Container -->
    </div>

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#update_profile_form").submit(function (event) {
                event.preventDefault();
                // alert(new FormData(this));
                password = $('#password').val();
                conf_password = $('#confirm_password').val();
                if (password !== conf_password){
                    alert('Password does not match!');
                }else{
                    if (confirm("Are you sure?")){
                        $.ajax({
                            type: 'POST',
                            url: 'ajax/profile_ajax.php',
                            data: new FormData(this),
                            contentType: false,
                            cache: false,
                            processData:false,
                            success: function(response) {
                                // if (response === "success"){
                                //     alert('Successfully added employee')
                                //     location.reload();
                                // }else{
                                //     alert('Something went wrong');
                                //     console.log(response)
                                // }
                                alert(response);
                                location.reload();
                            },
                            error: function() {
                                console.log("Error adding employee function");
                            }
                        });
                    }
                }
            });
        });
    </script>
<?php
include 'includes/footer.php';