    <link rel="stylesheet" href="assets/js/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
    <?php
    include 'session.php';
    include 'includes/head.php';
    include 'includes/page_layout.php';
    ?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        ?>
        <!-- Main Container -->
        <main id="main-container" style="min-height: 871px;">
            <?php
            if (isset($_GET['user'])){
                $users_user_id = $_GET['user'];
                try {
                    $user_details_qry = $conn->query("SELECT `emp_id`,tbl_employees.firstname, tbl_employees.lastname, users.username, tbl_employees.email, `password`, tbl_employees.date_created, `user_type`, `img_src`
                                                                      
                                                                FROM `tbl_employees` 
                                                                INNER JOIN  `users` ON tbl_employees.employee_id = users.emp_id
                                                                WHERE users.user_id = $users_user_id");
                    $fetch_user_details = $user_details_qry->fetch();
                    $fullname = ucwords(strtolower($fetch_user_details['firstname'].' '.$fetch_user_details['lastname']));
                }catch (Exception $e){
                    echo $e;
                }
            ?>
                    <div class="bg-gd-sun">
                        <div class="bg-pattern" style="background-image: url('assets/media/photos/construction13.jpg'); background-repeat: no-repeat; background-size: 100%; ">
                            <div class="content content-top content-full text-center">
                                <div class="py-20">
                                    <h1 class="h2 font-w700 text-white mb-10">User Access Settings</h1>
                                    <h2 class="h4 font-w400 text-white-op">Change user access to system functionalities.</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END Hero -->
            <div class="content">


                    <div class="row">
                        <div class="col-lg-6">
                            <a class="block block-link-pop bg-success text-center" href="javascript:void(0)">
                                <div class="block-content block-content-full">
                                    <img class="img-avatar img-avatar-thumb" src="assets/media/photos/employee/<?=$fetch_user_details['img_src']?>" alt="">
                                </div>
                                <div class="block-content block-content-full bg-black-op-5">
                                    <input type="hidden" id="users_user_id" value="<?=$users_user_id?>">
                                    <div class="font-w600 text-white mb-5"><?=$fullname?></div>
                                    <div class="font-size-sm text-white-op">User</div>
                                </div>
                                <div class="block-content block-content-full block-content-sm">
                                    <span class="font-w600 font-size-sm text-success-light"><?=$fetch_user_details['email']?></span>
                                </div>
                            </a>
                            <div class="block">
                                <div class="block-header block-header-default">
                                    <h5 class="block-title">User System Access</h5>
                                </div>
                                <div class="block-content">
                                    <table class="js-table-checkable table table-hover table-vcenter">
                                        <tbody>
                                        <?php
                                        try {

                                            $access_types = $conn->query("SELECT
                                                                                    tbl_modules.title,
                                                                                    user_access_type.access_type_id,
                                                                                    user_access_type.access_name,
                                                                                    user_access_type.description
                                                                                    FROM
                                                                                    tbl_modules
                                                                                    INNER JOIN user_access_type ON tbl_modules.module_id = user_access_type.module_id
                                                                                    ");
                                            foreach ($access_types as $access_type){
                                                $access_type_id = $access_type['access_type_id'];
                                                $checked = '';
//                                                Check access
                                                $access = $conn->query("SELECT `access_id`, `access_type_id`, `user_id`, `status` FROM `user_access` 
                                                                                 WHERE `access_type_id` = $access_type_id AND `user_id` = $users_user_id AND `status` = 0");
                                                if ($access->rowCount() > 0) {
                                                    $access = $access->fetch();
                                                    $access_id = $access['access_id'];
                                                    $status = $access['status'];
                                                    $checked = 'checked';
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a class="font-w600" data-toggle="modal" data-target="#modal-message" href="#"><?=$access_type['access_name']?></a>
                                                        <div class="text-muted mt-5"><?=$access_type['description']?></div>
                                                    </td>
                                                    <td class="d-none d-sm-table-cell font-w600" style="width: 140px;"><?=$access_type['title']?> </td>
                                                    <td class="text-center" style="width: 40px;">
                                                        <label class="css-control css-control-primary css-checkbox">
                                                            <input type="checkbox" class="css-control-input" id="checkbox<?=$access_type_id?>" onclick="change_access(<?=$access_type_id?>)" <?=$checked?>>
                                                            <span class="css-control-indicator"></span>
                                                        </label>
                                                    </td>
                                                </tr>
                                                <?php
                                            }

                                        }catch (Exception $e){
                                            echo $e;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6" id="basic_info">
                            <div class="block block-rounded">
                                <div class="block-header block-header-default">
                                    <h3 class="block-title">Basic Info</h3>
                                    <button type="button" class="mr-10 btn btn-m btn- btn-primary float-right  "   onclick="history.back()" ">
                                    <i class="si si-action-undo"> Back</i>
                                    </button>
                                </div>
                                <div class="block-content">
                                    <div class="font-size-lg text-black mb-5"><?=$fullname?>
                                        <address>
                                            Email: <?=$fetch_user_details['email']?><br>
                                            Type: User<br>
                                            Date Created: <?=$fetch_user_details['date_created']?><br>
                                            <button type="button" class="mt-10 mr-10 btn btn-sm  btn-success " data-toggle="modal" data-target="#edit_user_modal">Edit User</button>
                                            <button type="button" class="mt-10 mr-10 btn btn-sm btn- btn-danger  " data-toggle="modal"  data-target="#delete_user_modal">DEACTIVATE USER</button>
                                        </address>
                                    </div>
                                </div>

                            </div>
                            <div class="block">
                                <div class="block-header block-header-default">
                                    <h3 class="block-title">Created Employees by <?=$fullname?></h3>
                                    <div class="block-options">
                                        <div class="block-options-item">
                                            <!--                                <code>.table</code>-->
                                        </div>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <table class="table  table-striped table-vcenter js-dataTable-simple">
                                        <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%;">#</th>
                                            <th>Name</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $employees = $conn->query("SELECT
                                                                tbl_employees.employee_id,
                                                                tbl_employees.firstname,
                                                                tbl_employees.middlename,
                                                                tbl_employees.lastname,
                                                                tbl_employees.phone_no,
                                                                tbl_employees.img_src,
                                                                tbl_position.position,
                                                                tbl_department.department,
                                                                tbl_employees.is_deleted,
                                                                tbl_nationalities.`name`
                                                                FROM
                                                                tbl_employees
                                                                INNER JOIN tbl_department ON tbl_employees.department = tbl_department.department_id
                                                                INNER JOIN tbl_position ON tbl_position.position_id = tbl_employees.position
                                                                INNER JOIN tbl_nationalities ON tbl_employees.nationality = tbl_nationalities.id
                                                                INNER JOIN users ON users.user_id = tbl_employees.user_id
                                                                WHERE
                                                                tbl_employees.user_id = $users_user_id AND
                                                                tbl_employees.is_deleted = 0
                                                                ORDER BY
                                                                tbl_employees.firstname ASC
                                                            ");
                                        $count = 1;
                                        foreach ($employees as $employee) {
                                            $employee_fullname = ucwords(strtolower($employee['firstname']." ".$employee['lastname']));
                                            $img_src = $employee['img_src'];
                                            ?>
                                            <tr>
                                                <th class="text-center" scope="row"><?=$count++?></th>
                                                <td>
                                                    <?php
                                                    if ($img_src == '' || $img_src == null){
                                                        $img = 'assets/media/avatars/avatar5.jpg';
                                                    }else{
                                                        $img = 'assets/media/photos/employee/'.$img_src;
                                                    }
                                                    ?>
                                                    <img class="img-avatar" src="<?=$img?>" alt="">
                                                    <?=$employee_fullname?>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>


                                    </table>
                                </div>
                            </div>
                        </div>

<!--                        <div class="col-lg-6" id="profile_info">-->
<!---->
<!--                        </div>-->
<!--                        <div class="col-lg-6">-->
<!---->
<!--                        </div>-->
                    </div>

                        <!--------------------------------------   EDIT USER MODAL--------------------------------->
                        <div class="modal fade" id="edit_user_modal" tabindex="-1" role="dialog" aria-labelledby="add_attendance_modal" aria-hidden="true">
                            <div class="modal-dialog " role="document">
                                <div class="modal-content">
                                    <div class="block block-themed block-transparent mb-0">
                                        <div class="block-header bg-primary-dark">
                                            <h3 class="block-title">Edit User Form</h3>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                    <i class="si si-close"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content">
                                            <form id="edit_user_form" method="POST">
                                                <input type="number"  hidden name="edit_user_id" value="<?=$users_user_id?>">
                                                <div class="form-group row">
                                                    <div class="col-6">
                                                        <label for="title">First name</label>
                                                        <input type="text" class="form-control form-control-lg" id="edit_fname" name="edit_fname" required value="<?=$fetch_user_details['firstname']?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="contractNo">Last name</label>
                                                        <input type="text" class="form-control form-control-lg" id="edit_lname" name="edit_lname"  required value="<?=$fetch_user_details['lastname']?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-6">
                                                        <label for="location">User name</label>
                                                        <input type="text" class="form-control form-control-lg" id="edit_username" name="edit_username" required value="<?=$fetch_user_details['username']?>">
                                                    </div>
                                                    <div class="col-6">
                                                        <label for="email">Email</label>
                                                        <input type="email" class="form-control form-control-lg" id="edit_email" name="edit_email" required value="<?=$fetch_user_details['email']?>">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="location">Password</label>
                                                        <input type="password" class="form-control form-control-lg" id="edit_password" name="edit_password" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-12">
                                                        <label for="location">Confirm Password</label>
                                                        <input type="password" class="form-control form-control-lg" id="edit_cpassword" name="edit_cpassword" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-12 text-center">
                                                        <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                                            <i class="fa fa-save mr-5"></i> Save
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--------------------------------------   EDIT USER MODAL--------------------------------->
                        <!--------------------------------------   DELETE USER MODAL--------------------------------->
                        <div class="modal fade" id="delete_user_modal" tabindex="-1" role="dialog" aria-labelledby="add_attendance_modal" aria-hidden="true">
                            <div class="modal-dialog " role="document">
                                <div class="modal-content">
                                    <div class="block block-themed block-transparent mb-0">
                                        <div class="block-header bg-primary-dark">
                                            <h3 class="block-title">Are you sure you want to deactivate this User? </h3>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                                    <i class="si si-close"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content">
                                            <form id="delete_user_form" method="POST">
                                                <input type="number"  hidden name="delete_user_id" value="<?=$users_user_id?>">
                                                <div class="form-group row">
                                                    <div class="col-12 text-center">
                                                        <button type="submit" class="btn btn-sm btn-hero btn-alt-danger min-width-175">
                                                            <i class="fa fa-close mr-5"></i> Deactivate
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-hero btn-alt-primary min-width-175" data-dismiss="modal">
                                                            <i class="fa fa-save mr-5"></i> No.
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--------------------------------------   DELETE USER MODAL--------------------------------->
                <?php
            }else{

                ?>
                <!-- Hero -->
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
<!--                        <button type="button" class="btn btn-sm btn-rounded btn-primary d-md-none float-right ml-5" data-toggle="class-toggle" data-target=".js-inbox-nav" data-class="d-none d-md-block">Menu</button>-->
                        <button type="button" class="btn btn-sm btn-rounded btn-success float-right" data-toggle="modal" data-target="#add_new_user_modal">Add New Account</button>
                    </h2>
                    <!-- Team -->
                    <div class="block">
                        <div class="block-content">
                            <div class="row gutters-tiny py-20">
                                <?php

                                $users = $conn->query("SELECT
                                                    users.user_id,
                                                    tbl_employees.firstname,
                                                    tbl_employees.lastname,
                                                    tbl_employees.email,
                                                    users.user_type,
                                                    users.`status`, 
                                                    tbl_employees.lastname,
                                                    tbl_employees.firstname,
                                                    emp_id,
                                                    img_src

                                                    FROM
                                                    users
                                                    INNER JOIN tbl_employees ON users.emp_id =  tbl_employees.employee_id    
                                                    WHERE users.subscriber_id = $subscriber_id AND  deleted = 0");
                                $count = 1;
                                $all_users = $users->fetchAll();
                                foreach ($all_users as $user) {
                                    $users_user_id = $user['emp_id'];
                                    $employee_fullname= "";

                                    $user_type = '';
                                    if ($user['user_type'] == 0 ){
                                        $user_type = 'Admin';
                                        $employee_fullname = ucwords(strtolower($user['firstname']." ".$user['lastname']));

                                    }else{
                                        $user_type = 'User';
                                        $employee_fullname = ucwords(strtolower($user['firstname']." ".$user['lastname']));
                                    }
                                    ?>
                                    <div class="col-md-6 col-xl-3">
                                        <a class="block text-center" href="users.php?user=<?=$users_user_id?>">
                                            <div class="block-content block-content-full bg-gd-sun">
                                                <img class="img-avatar img-avatar-thumb" src="assets/media/photos/employee/<?=$user['img_src']?>" alt="">
                                            </div>
                                            <div class="block-content block-content-full">
                                                <div class="font-w600 mb-5"><?=$employee_fullname?></div>
                                                <div class="font-size-sm text-muted"><?=$user_type?></div>
                                            </div>
                                            <div class="block-content block-content-full block-content-sm bg-body-light">
                                                <span class="font-w600 font-size-sm text-danger"><?=$user['email']?></span>
                                            </div>
                                        </a>
                                    </div>
                                    <?php
                                }
                                ?>
                                <!-- END Team -->
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </main>
        <!-- END Main Container -->
    </div>
    <!--ADD NEW TOOLBOX TALKS MODAL-->
    <div class="modal fade" id="add_new_user_modal"  role="dialog" aria-labelledby="add_attendance_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Add New User Account</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <?php
                        $select_employee_qry = $conn->query("SELECT * FROM tbl_employees WHERE is_deleted=0  AND user_id = $session_emp_id ORDER BY firstname ASC");
                        $select_employee = $select_employee_qry->fetchAll();
                    ?>
                    <div class="block-content">
                        <form id="add_user_form" method="POST">
                            <input type="hidden" name="add_new_user" value="1">
                            <div class="form-group row">
                                <div class="col-6">
                                    <label class="" for="employee_list">Select Employee</label>
                                    <select class="js-select2 form-control form-control-lg" id="employee_list" name="employee_id" style="width: 100%;" data-placeholder="Choose one..">
                                        <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                        <?php foreach ($select_employee as $emp): ?>
                                            <option value="<?=$emp['employee_id'] ?>"><?=$emp['firstname']." ".$emp['middlename']." ".$emp['lastname']?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="" for="user_type">Select Account Type</label>
                                    <select class="form-control form-control-lg" id="user_type" name="user_type" style="width: 100%;" data-placeholder="Choose one..">
                                        <option selected="selected" disabled="disabled">Choose..</option>
                                        <option value="1">User</option>
                                        <option value="0">Admin</option>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="location">User name</label>
                                    <input type="text" class="form-control form-control-lg" id="username" name="username" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="location">Password</label>
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="location">Confirm Password</label>
                                    <input type="password" class="form-control form-control-lg" id="cpassword" name="cpassword" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                        <i class="fa fa-save mr-5"></i> Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="assets/js/codebase.core.min.js"></script>
<script src="assets/js/codebase.app.min.js"></script>
<!-- Page JS Plugins -->
<script src="assets/js/plugins/select2/js/select2.full.min.js"></script>
<script src="assets/js/plugins/jquery-auto-complete/jquery.auto-complete.min.js"></script>
<script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page JS Code -->
<script src="assets/js/pages/be_tables_datatables.min.js"></script>
<!-- Page JS Code -->
<!--<script src="assets/js/pages/be_forms_plugins.min.js"></script>-->

<!-- Page JS Helpers (BS Datepicker + BS Colorpicker + BS Maxlength + Select2 + Masked Input + Range Sliders + Tags Inputs plugins) -->
<script>jQuery(function(){ Codebase.helpers('select2'); });</script>
<script>
    // $.fn.modal.Constructor.prototype.enforceFocus = function() {
    //     $('#employee_list').select2({
    //         dropdownParent: $('#add_new_user_modal')
    //     });
    // };
    $(document).ready(function () {
        $("#add_user_form").submit(function (event) {
            event.preventDefault();
            password = $('#password').val();
            cpassword = $('#cpassword').val();
            if (password == cpassword){
                $.ajax({
                    type: 'POST',
                    url: 'ajax/users_ajax.php',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(response) {
                        if (response.includes('success')){
                            alert('User successfully added');
                            location.reload();
                        }else if (response.includes('username')){
                            alert('Username already exist!')

                        }else{
                            alert('Something went wrong.');
                        }
                    },
                    error: function() {
                        console.log("Error adding employee function");
                    }
                });
            }else{
                alert('Password does not match!');
            }
        });
        $("#edit_user_form").submit(function (event) {
            event.preventDefault();

            edit_password = $('#edit_password').val();
            edit_cpassword = $('#edit_cpassword').val();

            if (edit_password == edit_cpassword){
                $.ajax({
                    type: 'POST',
                    url: 'ajax/users_ajax.php',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(response) {

                        if (response.includes('success')){
                            alert('User successfully added');
                            location.reload();
                        }else if (response.includes('username')){
                            alert('Username already exist!')
                        }else if (response.includes('email')){
                            alert('Email already exist!')
                        }else if (response.includes('both')){
                            alert('Email and username already exist!')
                        }else{
                            alert('Something went wrong.');
                        }
                    },
                    error: function() {
                        console.log("Error adding employee function");
                    }
                });
            }else{
                alert('Password does not match!');
            }
        });
        $("#delete_user_form").submit(function (event) {
            event.preventDefault();
            if (confirm("Confirm Decativate User?")){
                $.ajax({
                    type: 'POST',
                    url: 'ajax/users_ajax.php',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(response) {
                        alert('User Deactivated Successfully')
                        history.back();
                    },
                    error: function() {
                        console.log("Error Deleting User function");
                    }
                });
            }
        });
    });

    function change_access(access_type_id){

        // alert(access_type_id);
        users_user_id = $('#users_user_id').val();
        $.ajax({
            type: 'POST',
            url: 'ajax/users_ajax.php',
            data: {
                users_user_id:users_user_id,
                access_type_id:access_type_id,
                change_access:1
            },
            success: function(response) {
                // alert(response);
                location.reload();
            },
            error: function() {
                console.log("Error adding employee function");
            }
        });

    }
</script>
    <?php
    include 'includes/footer.php';