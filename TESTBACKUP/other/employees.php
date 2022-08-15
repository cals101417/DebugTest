

<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
?>
<link rel="stylesheet" href="assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
<div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
    <?php
    include 'includes/sidebar.php';
    include 'includes/header.php';
    ?>
    <!--        THIS IS A SAMPLE CHANGES -->
    <!-- Main Container -->
    <main id="main-container">

        <?php
        if (isset($_GET['edit'])){ // ---------------------------------- EDIT EMPLOYEE DETAILS CONTENT ----------------------------------------
            $employee_id = $_GET['edit'];

//                This query is for fetching employee data
            $employee_details = $conn->query("SELECT
                                                            tbl_employees.firstname,
                                                            tbl_employees.middlename,
                                                            tbl_employees.lastname,
                                                            tbl_employees.phone_no,
                                                            tbl_employees.email,
                                                            tbl_employees.`img_src`,
                                                            tbl_company.`company_name`,
                                                            tbl_company.`company_id`,
                                                            tbl_employees.`company_type`,
                                                            tbl_employees.position,
                                                            tbl_employees.department,
                                                            tbl_employees.nationality,
                                                            tbl_employees.is_active,
                                                            tbl_employees.is_deleted,
                                                            tbl_employees.birth_date
                                                            FROM
                                                            tbl_employees
                                                            INNER JOIN tbl_company ON tbl_employees.company = tbl_company.company_id
                                                            WHERE
                                                            tbl_employees.employee_id = $employee_id");
            $employee_fetch = $employee_details->fetch();
            $employee_first = $employee_fetch['firstname'];
            $employee_middle = $employee_fetch['middlename'];
            $employee_last = $employee_fetch['lastname'];
            $phone_no = $employee_fetch['phone_no'];
            $email = $employee_fetch['email'];
            $imgsrc = $employee_fetch['img_src'];
            $company = $employee_fetch['company_name'];
            $company_id = $employee_fetch['company_id'];
            $company_type = $employee_fetch['company_type'];
            $position_id = $employee_fetch['position'];
            $department_id = $employee_fetch['department'];
            $nationality_id = $employee_fetch['nationality'];
            $is_active = $employee_fetch['is_active'];
            $is_deleted = $employee_fetch['is_deleted'];
            $birth_date = $employee_fetch['birth_date'];
            $timestamp = strtotime($birth_date);
            $new_date = date("Y-m-d", $timestamp);

            $employee_fullname = ucwords(strtolower($employee_first.' '.$employee_last));
//                This query is for fetching training count
            $trainings = $conn->query("SELECT
                                                    Count(tbl_training_trainees.trainee_id) as training_count
                                                    FROM
                                                    tbl_training_trainees
                                                    WHERE
                                                    tbl_training_trainees.employee_id = $employee_id AND
                                                    tbl_training_trainees.is_removed = 0 ");
            $training_count_fetch = $trainings->fetch();
            $training_count = $training_count_fetch['training_count'];

            $get_company_qry = $conn->query("SELECT * FROM tbl_company WHERE is_deleted = 0");
            $get_company = $get_company_qry->fetchAll();

            ?>
            <!-- Hero -->
            <div class="bg-success">
                <div class="bg-pattern bg-black-op-25" style="background-image: url('assets/media/photos/construction14.png');">
                    <div class="content content-top text-center bg-black-op-75">
                        <div class="py-50">
                            <h1 class="font-w700 text-white mb-10">Edit Employee Details</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <nav class="breadcrumb push mb-0 pl-0">
                    <a class="breadcrumb-item" href="employees.php">Employee List</a>
                    <span class="breadcrumb-item active">Edit</span>
                </nav>
                <!-- Update Product -->
                <h2 class="content-heading mb-0">
                    <button class="btn btn-sm btn-alt-primary float-right" onclick="history.back()"><i class="si si-action-undo"></i> Go Back</button>
                    Update Employee Details
                </h2>
                <div class="content content-full text-center">
                    <div class="mb-15">
                        <a class="img-link" href="be_pages_generic_profile.html">
                            <img class="img-avatar img-avatar96 img-avatar-thumb" src="assets/media/photos/employee/<?=$imgsrc?>" alt="">
                        </a>
                        <h1 class="h3 text-muted font-w700 mb-10"><?=$employee_fullname?></h1>
                    </div>
                </div>
                <form id="update_form">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="block">
                                <div class="block-content block-content-full">
                                    <input type="hidden" name="update_employee" value="<?=$employee_id?>">
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label for="be-contact-name">First name</label>
                                            <input type="text" class="form-control form-control-lg" id="fname" name="fname" value="<?=$employee_first?>" required>
                                        </div>
                                        <div class="col-6">
                                            <label for="be-contact-name">Middle name</label>
                                            <input type="text" class="form-control form-control-lg" id="mname" name="mname" value="<?=$employee_middle?>" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="be-contact-name">Last name</label>
                                            <input type="text" class="form-control form-control-lg" id="lname" name="lname" value="<?=$employee_last?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label for="be-contact-email">Phone no.</label>
                                            <input type="number" class="form-control form-control-lg" id="phone" name="phone" value="<?=$phone_no?>" placeholder="" required>
                                        </div>
                                        <div class="col-6">
                                            <label for="email">Email.</label>
                                            <input type="email" class="form-control form-control-lg" id="email" name="email" value="<?=$email?>" placeholder="" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-4" for="item_img">Image</label>
                                        <div class="col-8">
                                            <input type="file" id="employee_img" name="employee_img">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-4">
                                            <label for="company">Status</label>
                                        </div>
                                        <div class="col-8">
                                            <select class="form-control form-control-sm" id="select_active" name="select_active">
                                                <?php
                                                $active_selected = '';
                                                $inactive_selected = '';
                                                if ($is_active == '0'){
                                                    $active_selected = 'selected';
                                                }else{
                                                    $inactive_selected = 'selected';
                                                }
                                                ?>
                                                <option value="0" <?=$active_selected?>>Active</option>
                                                <option value="1" <?=$inactive_selected?>>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-4 col-form-label-sm" for="birth_date">Birth Date</label>
                                        <div class="col-8">
                                            <input type="text" class="js-datepicker form-control" id="birth_date" name="birth_date"
                                                   data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy/mm/dd" value="<?=$new_date?>" placeholder="yyyy/mm/dd" max="<?= date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="block">
                                <div class="block-content block-content-full">
                                    <div class="form-group row">
                                        <div class="col-8">
                                            <label for="company">Company</label>
                                            <select class=" form-control form-control-sm" name="company" id="company">
                                                <?php $selected =  ""; ?>
                                                <?php foreach($get_company as $com): ?>
                                                    <?=(($com['company_id'] == $company_id)? $selected =  "selected" : $selected = "")  ?>
                                                    <option value="<?=$com['company_id']?>" <?=$selected?>><?=$com['company_name']?></option>
                                                <?php endforeach; ?>
                                            </select>

                                        </div>
                                        <div class="col-4">
                                            <label for="be-contact-name">Select type</label>
                                            <select class="form-control form-control-sm" id="select_company_type" name="select_company_type">
                                                <?php
                                                $main_selected = '';
                                                $sub_selected = '';
                                                $client_selected = '';
                                                if ($company_type == 'main contractor'){
                                                    $main_selected = 'selected';
                                                }elseif ($company_type == 'sub contractor'){
                                                    $sub_selected = 'selected';
                                                }else{
                                                    $client_selected = 'selected';
                                                }
                                                ?>
                                                <option value="main Contractor" <?=$main_selected?>>Main Contractor</option>
                                                <option value="sub Contractor" <?=$sub_selected?>>Sub Contractor</option>
                                                <option value="client" <?=$client_selected?>>Client</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-12" for="example-select">Select Position</label>
                                        <div class="col-12">
                                            <select class="form-control" id="select_position" name="select_position">
                                                <?php
                                                $position_qry = $conn->query("SELECT `position_id`, `position`, `user_id`, `date_created`, `is_deleted` FROM `tbl_position`");
                                                foreach ($position_qry as $position){
                                                    if ($position['position_id'] == $position_id){
                                                        ?>
                                                        <option value="<?=$position['position_id']?>" selected><?=$position['position']?></option>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <option value="<?=$position['position_id']?>"><?=$position['position']?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-12" for="example-select">Select Department</label>
                                        <div class="col-12">
                                            <select class="form-control" id="select_department" name="select_department">
                                                <?php
                                                $departments = $conn->query("SELECT `department_id`, `department`, `user_id`, `date_created` FROM `tbl_department`");
                                                foreach ($departments as $department){
                                                    if ($department['department_id'] == $department_id){
                                                        ?>
                                                        <option value="<?=$department['department_id']?>" selected><?=$department['department']?></option>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <option value="<?=$department['department_id']?>"><?=$department['department']?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-12" for="example-select">Nationality</label>
                                        <div class="col-12">
                                            <select class="form-control" id="select_nationality" name="select_nationality">
                                                <?php
                                                $nationalities_qry = $conn->query("SELECT `id`, `name` FROM `tbl_nationalities` ORDER BY tbl_nationalities.`name` ASC");
                                                foreach ($nationalities_qry as $nationality){
                                                    if ($nationality['id'] == $nationality_id){
                                                        ?>
                                                        <option value="<?=$nationality['id']?>" selected><?=$nationality['name']?></option>
                                                        <?php
                                                    }else{
                                                        ?>
                                                        <option value="<?=$nationality['id']?>"><?=$nationality['name']?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>
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
                <!-- END Update Product -->
            </div>

            <?php
        }else{ // ---------------------------------- EMPLOYEE LIST CONTENT ----------------------------------------
            ?>
            <!-- Hero -->
            <div class="bg-gd-earth">
                <div class="bg-pattern" style="background-image: url('assets/media/photos/construction14.png');">
                    <div class="content content-top content-full text-center bg-black-op-75">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10">Manage Employees</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->
            <!-- Page Content -->
            <div class="content">
                <div class="row js-appear-enabled animated fadeIn" data-toggle="appear">
                    <!-- Row #1 -->
                    <div class="col-6 col-xl-3">
                        <?php
                        try {
                            $total_employee = 0;
                            $total_active = 0;
                            $total_employee_qry = $conn->query("SELECT
                                                                        Count(tbl_employees.employee_id) as total_employee
                                                                        FROM
                                                                        tbl_employees
                                                                        WHERE
                                                                        tbl_employees.is_deleted = 0");
                            $total_employee_fetch = $total_employee_qry->fetch();
                            $total_employee = $total_employee_fetch['total_employee'];

//                            TOTAL ACTIVE COUNT
                            $total_active_qry = $conn->query("SELECT
                                                                    Count(tbl_employees.employee_id) AS total_active
                                                                    FROM
                                                                    tbl_employees
                                                                    WHERE
                                                                    tbl_employees.is_active = 0 AND
                                                                    tbl_employees.is_deleted = 0
                                                                    ");
                            $total_active_fetch = $total_active_qry->fetch();
                            $total_active = $total_active_fetch['total_active'];
//                            TOTAL INACTIVE COUNT
                            $total_inactive_qry = $conn->query("SELECT
                                                                    Count(tbl_employees.employee_id) AS total_inactive
                                                                    FROM
                                                                    tbl_employees
                                                                    WHERE
                                                                    tbl_employees.is_active = 1 AND
                                                                    tbl_employees.is_deleted = 0
                                                                    ");
                            $total_inactive_fetch = $total_inactive_qry->fetch();
                            $total_inactive = $total_inactive_fetch['total_inactive'];
                        }catch (Exception $e){
                            echo $e;
                        }

                        ?>
                        <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-right mt-15 d-none d-sm-block">
                                    <i class="si si-people fa-2x text-primary-light"></i>
                                </div>
                                <div class="font-size-h3 font-w600 text-primary js-count-to-enabled"><?=$total_employee_fetch['total_employee']?></div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Total Employee</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-xl-3">
                        <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-right mt-15 d-none d-sm-block">
                                    <i class="si si-envelope-open fa-2x text-elegance-light"></i>
                                </div>
                                <div class="font-size-h3 font-w600 text-elegance js-count-to-enabled" data-toggle="countTo" data-speed="1000" data-to="<?=$total_inactive?>"><?=$total_inactive?></div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Inactive</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-xl-3">
                        <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-right mt-15 d-none d-sm-block">
                                    <i class="si si-users fa-2x text-pulse"></i>
                                </div>
                                <div class="font-size-h3 font-w600 text-pulse js-count-to-enabled" data-toggle="countTo" data-speed="<?=$total_active?>" data-to="<?=$total_active?>"><?=$total_active?></div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Active</div>
                            </div>
                        </a>
                    </div>
                    <!-- END Row #1 -->
                </div>
                <h2 class="content-heading">
                    <button type="button" class="btn btn-sm btn-rounded btn-primary d-md-none float-right ml-5" data-toggle="class-toggle" data-target=".js-inbox-nav" data-class="d-none d-md-block">Menu</button>
                    <?php
                    //                    ADD ACCESS EMPLOYEE ENABLE DISABLE BUTTON
                    $add_disable = '';
                    $access = $conn->query("SELECT `status` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 1");
                    if ($access->rowCount() > 0){
                        $add_access = $access->fetch();
                        if ($add_access['status'] == 1){
                            $add_disable = 'disabled';
                        }
                    }else{
                        $add_disable = 'disabled';
                    }
                    ?>
                    <button type="button" class="btn btn-sm btn-rounded btn-success float-right" data-toggle="modal" data-target="#add_employee_modal" <?=$add_disable?>>Add New Employee</button>
                    Employee
                </h2>
                <div class="block">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Your Employees</h3>
                        <div class="block-options">
                            <div class="block-options-item">
                                <!--                                <code>.table</code>-->
                            </div>
                        </div>
                    </div>
                    <div class="block-content">
                        <table class="table table-vcenter js-dataTable-full-pagination" id="employee_table">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th style="text-transform: capitalize; !important;">Profile</th>
                                <th style="text-transform: capitalize; !important;">Name</th>
                                <th class="text-center" style="width: 10%;text-transform: capitalize; !important;">Position</th>
                                <th class="text-center" style="width: 18%;text-transform: capitalize; !important;">Department</th>
                                <th class="text-center" style="width: 10%;text-transform: capitalize; !important;">Nationality</th>
                                <th class="text-center" style="width: 5%;text-transform: capitalize; !important;">Status</th>
                                <th class="text-center" style="width: 10%;text-transform: capitalize; !important;">Phone No.</th>
                                <th class="text-center" style="width: 10%;text-transform: capitalize; !important;">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            //                            ENABLE/DISABLE EDIT AND DELETE BUTTON ACCORDING TO USER ACCESS
                            $edit_disable = '';
                            $edit_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 2 AND `status` = 0");
                            if ($edit_access->rowCount() > 0){
                                $edit_access = $edit_access->fetch();
                                $status = $edit_access['status'];
                                if ($status == 1){
                                    $edit_disable = 'disabled';
                                }
                            }else{
                                $edit_disable = 'disabled';
                            }

                            $delete_disable = '';
                            $delete_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 3 AND `status` = 0");
                            if ($delete_access->rowCount() > 0){
                                $delete_access = $delete_access->fetch();
                                $delete_status = $delete_access['status'];
                                if ($delete_status == 1){
                                    $delete_disable = 'disabled';
                                }
                            }else{
                                $delete_disable = 'disabled';
                            }
//                            employees table, get employees
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
                                                                tbl_employees.is_active,
                                                                tbl_nationalities.`name`
                                                                FROM
                                                                tbl_employees
                                                                INNER JOIN tbl_department ON tbl_employees.department = tbl_department.department_id
                                                                INNER JOIN tbl_position ON tbl_position.position_id = tbl_employees.position
                                                                INNER JOIN tbl_nationalities ON tbl_employees.nationality = tbl_nationalities.id
                                                                INNER JOIN users ON users.user_id = tbl_employees.user_id
                                                                WHERE
                                                                tbl_employees.sub_id = $subscriber_id AND
                                                                tbl_employees.is_deleted = 0
                                                                ORDER BY
                                                                tbl_employees.firstname ASC
                                                            ");
                            $count = 1;
                            foreach ($employees as $employee) {
                                $employee_fullname = ucwords(strtolower($employee['firstname']." ".$employee['lastname']));
                                $img_src = $employee['img_src'];
                                $employee_id = $employee['employee_id'];
                                $is_active = $employee['is_active'];
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
                                    </td>
                                    <td><?=$employee_fullname?></td>
                                    <td class="text-center"><?=ucwords(strtolower($employee['position']))?></td>
                                    <td class="text-center"><?=ucwords(strtolower($employee['department']))?></td>
                                    <!--                                <td class="d-none d-sm-table-cell text-center">-->
                                    <!--                                    <span class="badge badge-info">--><?//=$employee['department']?><!--</span>-->
                                    <!--                                </td>-->
                                    <td class="text-center"><?=ucwords(strtolower($employee['name']))?></td>
                                    <td class="text-center">
                                        <?php
                                        if ($is_active == 0){
                                            echo '<span class="badge badge-success">Active</span>';
                                        }else{
                                            echo '<span class="badge badge-warning">Inactive</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center"><?=$employee['phone_no']?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary js-tooltip-enabled" onclick="view_details(<?=$employee['employee_id']?>)" data-toggle="tooltip" title="View Details" data-original-title="View Details">
                                                <i class="fa fa-clipboard"></i>
                                            </button>
                                            <a href="<?=(($edit_disable == '')?'employees.php?edit='.$employee_id:'#')?>" type="button" class="btn btn-sm btn-success js-tooltip-enabled" data-toggle="tooltip" title="Edit Employee" data-original-title="Edit" >
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger js-tooltip-enabled" id="delete" onclick="delete_employee(<?=$employee['employee_id']?>)" data-toggle="tooltip" title="Delete Employee" data-original-title="Delete" <?=$delete_disable?>>
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END Page Content -->
            <?php
        }
        ?>

    </main>
    <!-- END Main Container -->
</div>
<!-- Add Employee Modal -->
<div class="modal fade" id="add_employee_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Add New Employee Form</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option">
                            <i class="si si-printer"></i> Print
                        </button>
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <form id="add_form">
                        <div class="row">
                            <div class="col-6">
                                <div class="block block-bordered">
                                    <div class="block-header block-header-default">
                                        <h5 class="block-title">Personal Details</h5>
                                    </div>
                                    <div class="block-content">
                                        <input type="hidden" name="add_employee" value="1">
                                        <div class="form-group row">
                                            <label class="col-4 col-form-label-sm" for="be-contact-name">First name</label>
                                            <div class="col-8">
                                                <input type="text" class="form-control form-control-sm" id="fname" name="fname" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-4 col-form-label-sm" for="mname">Middle name.</label>
                                            <div class="col-8">
                                                <input type="text" class="form-control form-control-sm" id="mname" name="mname" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-4 col-form-label-sm" for="lname">Last name.</label>
                                            <div class="col-8">
                                                <input type="text" class="form-control form-control-sm" id="lname" name="lname" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-4 col-form-label-sm" for="birth_date">Birth Date</label>
                                            <div class="col-8">
                                                <input type="text" class="js-datepicker form-control" id="birth_date" name="birth_date"
                                                       data-week-start="1" data-autoclose="true" data-today-highlight="true" data-date-format="yyyy/mm/dd" placeholder="yyyy/mm/dd" max="<?= date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-4 col-form-label-sm" for="be-contact-email">Phone no.</label>
                                            <div class="col-8">
                                                <input type="number" class="form-control form-control-sm" id="phone" name="phone" placeholder="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-4 col-form-label-sm" for="email">Email.</label>
                                            <div class="col-8">
                                                <input type="email" class="form-control form-control-sm" id="email" name="email" placeholder="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-4 col-form-label-sm" for="item_img">Image</label>
                                            <div class="col-8">
                                                <input type="file" id="employee_img" name="employee_img" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-6">
                                <div class="block block-bordered">
                                    <div class="block-header block-header-default">
                                        <h5 class="block-title">Work Details</h5>
                                    </div>
                                    <div class="block-content">
                                        <div class="form-group row">
                                            <div class="col-6">
                                                <label for="company">Company</label>
                                                <select class=" form-control form-control-sm" name="company" id="company">
                                                    <?php
                                                    $get_company_qry = $conn->query("SELECT * FROM tbl_company WHERE sub_id = $subscriber_id AND is_deleted = 0");
                                                    $get_company = $get_company_qry->fetchAll();
                                                    ?>
                                                    <?php foreach($get_company as $company): ?>
                                                        <option value="<?=$company['company_id']?>"><?=$company['company_name']?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label for="be-contact-name">Select type</label>
                                                <select class="form-control form-control-sm" id="select_company_type" name="select_company_type">
                                                    <option value="main Contractor">Main Contractor</option>
                                                    <option value="sub Contractor">Sub Contractor</option>
                                                    <option value="client">Client</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="example-select">Select Position</label>
                                            <div class="col-md-9">
                                                <select class="form-control" id="select_position" name="select_position">
                                                    <?php
                                                    $position_qry = $conn->query("SELECT `position_id`, `position`, `user_id`, `date_created`, `is_deleted` FROM `tbl_position` WHERE is_deleted = 0");
                                                    foreach ($position_qry as $position){
                                                        ?>
                                                        <option value="<?=$position['position_id']?>"><?=$position['position']?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="example-select">Select Department</label>
                                            <div class="col-md-9">
                                                <select class="form-control" id="select_department" name="select_department">
                                                    <?php
                                                    $departments = $conn->query("SELECT `department_id`, `department`, `user_id`, `date_created` FROM `tbl_department`");
                                                    foreach ($departments as $department){
                                                        ?>
                                                        <option value="<?=$department['department_id']?>"><?=$department['department']?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="example-select">Nationality</label>
                                            <div class="col-md-9">
                                                <select class="form-control" id="select_nationality" name="select_nationality">
                                                    <?php
                                                    $nationalities_qry = $conn->query("SELECT `id`, `name` FROM `tbl_nationalities` ORDER BY tbl_nationalities.`name` ASC");
                                                    foreach ($nationalities_qry as $nationality){
                                                        ?>
                                                        <option value="<?=$nationality['id']?>"><?=$nationality['name']?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                    <i class="fa fa-plus mr-5"></i> Add
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--                <div class="modal-footer">-->
            <!--                    <button type="button" class="btn btn-alt-secondary" data-dismiss="modal">Close</button>-->
            <!--                    <button type="button" class="btn btn-alt-success" data-dismiss="modal">-->
            <!--                        <i class="fa fa-check"></i> Perfect-->
            <!--                    </button>-->
            <!--                </div>-->
        </div>
    </div>
</div>
<!-- END Top Modal -->

<!-- View Employee Details Modal -->
<div class="modal fade" id="view_employee_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0" id="print_div">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title"><span id="span_employee_name">Employee Details</span></h3>
                    <div class="block-options">
                        <?php
                        //                    ADD ACCESS TRAINING ENABLE DISABLE BUTTON
                        $print_access = $conn->query("SELECT `status` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 4");
                        if ($print_access->rowCount() > 0) {
                            $print_access_fetch = $print_access->fetch();
                            if ($print_access_fetch['status'] == 0){
                                ?>
                                <button type="button" class="btn-block-option" onclick="printDiv('print_div')">
                                    <i class="si si-printer"></i> Print
                                </button>
                                <?php
                            }
                        }
                        ?>
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <div class="row items-push">
                        <div class="col-xl-5 mt-0 mb-0" id="profile_info"></div>
                        <div class="col-xl-7 px-0 mb-0" id="basic_info"></div>
                    </div>
                    <!-- END User Info -->
                    <div class="content">
                        <!-- Cart -->
                        <h2 class="content-heading pt-0">Trainings</h2>
                        <div class="block block-rounded">
                            <!-- Trainings Table -->
                            <table class="table table-vcenter ">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th style="width: 20%;">Title</th>
                                    <th class="text-center">Conducted by</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center" style="width: 20%;">Date Created</th>
                                    <th class="text-center" style="width: 15%;">Status</th>
                                    <th class="text-center" style="width: 10%;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="trainings_info">
                                </tbody>
                            </table>
                            <!-- END Trainings Table -->
                        </div>
                        <!-- END Cart -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END View Employee Details Modal -->

<script src="assets/js/codebase.core.min.js"></script>
<script src="assets/js/codebase.app.min.js"></script>
<script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
<!-- Page JS Code -->
<script src="assets/js/pages/be_tables_datatables.min.js"></script>

<script src="assets/js/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

<script>
    $(document).ready(function () {
        $('#employee_sidebar').addClass('open');
        Codebase.helpers(['datepicker']);

        // this javascript function is for adding employees
        $("#add_form").submit(function (event) {
            event.preventDefault();
            // alert('test')
            $.ajax({
                type: 'POST',
                url: 'ajax/employees_ajax.php',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    console.log("Error adding employee function");
                }
            });
        });

        $("#update_form").submit(function (event) {
            event.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'ajax/employees_ajax.php',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    console.log("Error adding employee function");
                }
            });
        });
    });

    function view_details(id){
        employee_profile_info(id);
        employee_basic_info(id);
        employee_trainings_info(id);
        $('#view_employee_modal').modal('show');

    }

    // VIEW EMPLOYEE DETAILS AJAX FUNCTIONS
    function employee_profile_info(id){
        $.ajax({
            type: "POST",
            url: "ajax/employees_ajax.php",
            data: {
                employee_id: id,
                employee_profile_info: 1,
            },
            success: function(data){
                $('#profile_info').html(data);
                // location.reload()
            }
        });
    }

    function employee_basic_info(id){
        $.ajax({
            type: "POST",
            url: "ajax/employees_ajax.php",
            data: {
                employee_id: id,
                employee_basic_info: 1,
            },
            success: function(data){
                $('#basic_info').html(data);
                // location.reload()
            }
        });
    }

    function employee_trainings_info(id){
        $.ajax({
            type: "POST",
            url: "ajax/employees_ajax.php",
            data: {
                employee_id: id,
                employee_trainings_info: 1,
            },
            success: function(data){
                $('#trainings_info').html(data);
                // location.reload()
            }
        });
    }
    // END VIEW EMPLOYEE DETAILS AJAX FUNCTIONS

    function delete_employee(id){
        if (confirm("Are you sure you want to remove this employee?")){
            $.ajax({
                type: "POST",
                url: "ajax/employees_ajax.php",
                data: {
                    employee_id: id,
                    delete_employee: 1,
                },
                success: function(data){
                    alert(data);
                    location.href = 'employees.php';
                }
            });
        }

    }

    function printDiv(div){
        var printContents = document.getElementById(div).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    }

</script>
<?php
include 'includes/footer.php';
