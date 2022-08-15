<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
$types = array("CIVILS","ELECTRICALS","MECHANICALS","CAMPS","OFFICE");
$type_id = $_GET['type'];
?>
    <link rel="stylesheet" href="assets/js/plugins/select2/css/select2.css">
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        ?>
        <!-- Main Container -->
        <main id="main-container">
            <?php

            if (!empty($_GET['view_attendance'])){
                try {
                    $tbt_id = $_GET['view_attendance'];

                    $attendance_qry = $conn->query("SELECT
                                                    tbl_toolbox_talks.title,
                                                    tbl_toolbox_talks.contract_no,
                                                    tbl_toolbox_talks.location,
                                                    tbl_toolbox_talks.time_conducted,
                                                    tbl_toolbox_talks.date_conducted,
                                                    tbl_toolbox_talks.conducted_by,
                                                    tbl_toolbox_talks.description,
                                                    tbl_toolbox_talks.`status`,
                                                    Count(tbl_toolbox_talks_participants.tbtp_id) AS count,
                                                    (SELECT CONCAT(`firstname`,' ',`lastname`) FROM tbl_employees WHERE tbl_employees .employee_id = tbl_toolbox_talks.conducted_by) as conductor_name
                                                    FROM
                                                    tbl_toolbox_talks
                                                    LEFT JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id
                                                    WHERE
                                                    tbl_toolbox_talks.tbt_id = $tbt_id AND
                                                    tbl_toolbox_talks.tbt_type = $type_id
                                                    GROUP BY
                                                    tbl_toolbox_talks.tbt_id");
                    $tbt_fetch = $attendance_qry->fetch();
                    $status = $tbt_fetch['status'];
                    $title = $tbt_fetch['title'];
                    $contract_no = $tbt_fetch['contract_no'];
                    $location = $tbt_fetch['location'];
                    $time_conducted = $tbt_fetch['time_conducted'];
                    $date_conducted = $tbt_fetch['date_conducted'];
                    $conducted_by = $tbt_fetch['conductor_name'];
                    $description = $tbt_fetch['description'];
                    $count = $tbt_fetch['count'];
                } catch (Exception $e) {
                    echo $e;
                }
                ?>

                <!-- Page Content -->
                <div class="content">
                    <h2 class="content-heading">
                        <button class="btn btn-sm btn-secondary float-right" onclick="edit_tbt_details(<?=$tbt_id?>)">Edit</button>
                        <button class="btn btn-sm btn-alt-primary float-right" onclick="history.back()"><i class="si si-action-undo"></i> Go Back</button>

                        <?=ucwords($title)?>
                    </h2>
                    <table class="table table-borderless table-sm mt-20">
                        <tbody>
                        <tr>
                            <td style="width: 20%" class="font-w700 text-right">Topic:</td>
                            <td style="width: 75%"><?=$title?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-w700 text-right">Activity:</td>
                            <td><?=$description?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-w700 text-right">Contract No:</td>
                            <td><?=$contract_no?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-w700 text-right">Location:</td>
                            <td><?=$location?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-w700 text-right">Conducted by:</td>
                            <td><?=$conducted_by?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-w700 text-right">Time Conducted:</td>
                            <td><?=$time_conducted?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-w700 text-right">Date Conducted:</td>
                            <td><?=date('F d Y', strtotime($date_conducted))?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-w700 text-right">No. of attendance:  </td>
                            <td><?=$count?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-w700 text-right">Status:</td>
                            <?php
                            $st =  ' 
                         
                               <td>
                                   <span class="badge badge-success">Completed</span>
                               </td>
                           <tr> 
                               <td class="font-w700 text-right"></td>
                               <td>
                                   <a href="#" class="text-center" onclick="view_attached_file('.$tbt_id.')"> View attached file</a>
                               </td>  
                           </tr>';

                            if($status == 0){
                                $st =  '<td><span class="badge badge-warning">Incomplete</span></td>';
                            }
                            echo $st;
                            ?>

                        </tbody>
                    </table>

                    <div class="block">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Attendances</h3>
                            <div class="block-options">
                                <div class="block-options-item">
                                    <!--                                <code>.table</code>-->
                                </div>
                            </div>
                        </div>

                        <div class="block-content">
                            <div class="form-group row">
                                <?php
                                // CHECK IF TRAINING HAS UPLOADED COMPLETION FILE
                                $check_completion = $conn->prepare("SELECT * FROM `tbl_tbt_images` WHERE `tbt_id` = ? AND `img_src` != '' ");
                                $check_completion->execute([$tbt_id]);
                                $check_count = $check_completion->rowCount();
                                $set_time_btn_status = '';
                                $set_input = '';
                                $hide_html = "";
                                if ($check_count > 0){
                                    $set_time_btn_status = "hidden";
                                    $set_input = "readonly";
                                    $hide_html = "hidden";
                                }
                                ?>
                                <div class="col-lg-5 col-sm-12"></div>
                                <div class="col-lg-6 col-sm-12 mb-10">
                                    <input type="hidden" name="add_me" value="1">
                                    <input type="hidden" name="tbt_id" id="tbt_id" value="<?=$tbt_id?>">
                                    <select <?=$hide_html ?> class="form-control" id="select_employee" name="select_employee">
                                        <?php
                                        $employee_qry = $conn->query("
                                                            SELECT `employee_id`, `firstname`, `middlename`, `lastname`,`subscriber_id`,
                                                                   CONCAT(`firstname`,' ',middlename,' ',`lastname`) as full_name
                                                            FROM tbl_employees
                                                            INNER JOIN users ON tbl_employees.user_id = users.user_id
                                                            WHERE  `is_deleted` = 0 AND subscriber_id = $subscriber_id 
                                                            ORDER BY `firstname` ASC");
                                        foreach ($employee_qry as $employee){
                                            $employee_fullname = ucwords(strtolower($employee['full_name']));
                                            ?>
                                            <option value="<?=$employee['employee_id']?>"><?=$employee_fullname?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-lg-1 col-sm-12">
                                    <button type="submit" class="btn btn-success float-right" <?=$hide_html ?>  id="btn_add">Add</button>
                                </div>
                            </div>

                            <table class="table table-vcenter table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th class="text-center" style="width: 5%;">Employee ID</th>
                                    <th style="width: 50%;text-transform: capitalize; !important;">Employee name</th>
                                    <th style="width: 20%;text-transform: capitalize; !important;">Date added</th>
                                    <th style="width: 5%;text-transform: capitalize; !important;">Hours</th>
                                    <th <?=$hide_html ?> class="text-center" style="width: 20%;text-transform: capitalize; !important;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $tbt_participants_query = $conn->query("SELECT
                                                                tbl_toolbox_talks_participants.tbtp_id,
                                                                tbl_employees.employee_id,
                                                                tbl_employees.firstname,
                                                                tbl_employees.lastname,
                                                                tbl_toolbox_talks_participants.date_added,
                                                                tbl_toolbox_talks_participants.time
                                                                FROM
                                                                tbl_toolbox_talks_participants
                                                                INNER JOIN tbl_employees ON tbl_employees.employee_id = tbl_toolbox_talks_participants.employee_id
                                                                WHERE
                                                                tbl_toolbox_talks_participants.tbt_id = $tbt_id AND
                                                                tbl_toolbox_talks_participants.is_removed = 0
                                                                ");
                                $count = 1;
                                foreach ($tbt_participants_query as $participant) {
                                    $emplyee_id = $participant ['employee_id'];
                                    $fullname = ucwords(strtolower($participant['firstname'] .' '.$participant['lastname']));
                                    $tbtp_id = $participant ['tbtp_id'];
                                    $date_added = $participant ['date_added'];
                                    $time = $participant ['time'];
                                    ?>
                                    <tr>
                                        <th class="text-center" scope="row"><?=$count++?></th>
                                        <th class="text-center" scope="row"><?=$emplyee_id?></th>
                                        <td class=""><?=$fullname?></td>
                                        <td class=""><?=date('F d, Y', strtotime($date_added))?></td>
                                        <td>
                                            <div class="input-group" >
                                                <div class="input-group-append">
                                                    <input class="form-control form-control-sm text-center"  maxlength="4"onfocusout="set_time(<?=$tbtp_id?>)"<?=$set_input?> type="number" id="add_time<?=$tbtp_id?>" value="<?=$time?>">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button <?=$hide_html ?> type="button" class="btn btn-sm btn-danger js-tooltip-enabled" id="delete" onclick="remove_attendance(<?=$tbtp_id?>,<?=$tbt_id?>)" data-toggle="tooltip" title="Remove employee from list" data-original-title="Delete">
                                                    <i class="fa fa-trash"></i>
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
                    <div class="block">
                        <div class="block-content pb-20">
                            <?php

                            // $check_completion = $conn->prepare("SELECT * FROM `tbl_tbt_images` WHERE `tbt_id` = ? AND type = 'completion'");
                            // $check_completion->execute([$tbt_id]);
                            // $check_count = $check_completion->rowCount();

                            if ($status == 1){

                                echo '<button class="btn btn-success btn-block btn-lg">Completed</button>';

                            }else{
                                echo '<button class="btn btn-warning btn-block btn-lg" onclick="set_complete('.$tbt_id.')">Mark as Completed</button>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }else{
                ?>
                <!-- Hero -->
                <div class="bg-gd-lake">
                    <div class="bg-pattern" style="background-image: url('assets/media/photos/construction4.jpeg');">
                        <div class="content content-top content-full text-center">
                            <div class="py-20">
                                <h1 class="h2 font-w700 text-white mb-10">Toolbox Talks - <?= $types[$type_id-1]?></h1>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Hero -->
                <?php
                $add_new_tbt_disable = '';
                $add_new_tbt_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 9 AND `status` = 0");
                if ($add_new_tbt_access->rowCount() > 0){
                    $add_new_tbt_access = $add_new_tbt_access->fetch();
                    $add_new_tbt_access_status = $add_new_tbt_access['status'];
                    if ($add_new_tbt_access_status == 1){
                        $add_new_tbt_disable = 'disabled';
                    }
                }else{
                    $add_new_tbt_disable = 'disabled';
                }

                ?>
                <div class="content">
                    <h2 class="content-heading">
                        <button type="button" class="btn btn-sm btn-rounded btn-primary d-md-none float-right ml-5" data-toggle="class-toggle" data-target=".js-inbox-nav" data-class="d-none d-md-block">Menu</button>
                        <button type="button" class="btn btn-sm btn-rounded btn-success float-right "  <?=$add_new_tbt_disable ?> data-toggle="modal" data-target="#add_attendance_modal">Add New TBT</button>
                        <div class="block bg-transparent ">
                            <?= $types[$type_id-1]?>
                        </div>
                        <div class="row justify-content-end mb-10 mr-5">
                            <div class="form-inline mb-10 " width="100%" >
                                <select class="form-control mb-10 " id="select_type" name="select_type" onchange="load_over_view()" width="200px"  >
                                    <option id="tbt_type_selected"  value="<?=$type_id?>"> <?= $types[$type_id-1]?></option>
                                    <option id="all"  value="6">ALL </option>
                                </select>
                                <select class="form-control mb-10 " id="select_month" name="select_month" onchange="load_over_view()" width="200px"  >
                                    <option id="01"  value="01">January</option>
                                    <option id="02"  value="02">February</option>
                                    <option id="03"  value="03" >March</option>
                                    <option id="04"  value="04">April</option>
                                    <option id="05"  value="05">May</option>
                                    <option id="06"  value="06">June</option>
                                    <option id="07"  value="07">July</option>
                                    <option id="08"  value="08">August</option>
                                    <option id="09"  value="09">September</option>
                                    <option id="010" value="10">October</option>
                                    <option id="011" value="11">November</option>
                                    <option id="012" value="12">December</option>
                                </select>
                                <select class="form-control mb-10" onchange="load_over_view()" id="select_year" name="select_year">
                                    <?php
                                    $total_years = array("2022","2021","2020","2019","2018","2017","2016","2015","2010");
                                    foreach ($total_years as $year) {
                                        ?>
                                        <option value="<?= $year ?>"><?= $year ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="" id="tbt_overview">
                        </div>

                    </h2>
                    <div class="block">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Your Attendance</h3>
                            <div class="block-options">
                                <div class="block-options-item">
                                    <!--                                <code>.table</code>-->
                                </div>
                            </div>
                        </div>
                        <div class="block-content">
                            <table class="table table-vcenter table-striped" id="tbt_table">
                                <thead class="thead-light text-center">
                                <tr>
                                    <th class="text-center" style="width: 5%;text-transform: capitalize; !important;">#</th>
                                    <th style="width: 20%;text-transform: capitalize; !important;">Activity</th>
                                    <th style="text-transform: capitalize; !important;">Conducted by</th>
                                    <th style="text-transform: capitalize; !important;">Location</th>
                                    <th class="text-center" style="width: 10%;text-transform: capitalize; !important;">No. of Participants</th>
                                    <th class="text-center" style="text-transform: capitalize; !important;">Date Conducted</th>
                                    <th class="text-center" style="text-transform: capitalize; !important;">Status</th>
                                    <th class="text-center" style="text-transform: capitalize; !important;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="tbt_table_tbody" class="text-center">
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
    <!--VIEW TRAINING DETAILS MODAL-->
    <div class="modal fade" id="modal_view_details" tabindex="-1" role="dialog" aria-labelledby="modal_view_details" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Toolbox Talks Details</h3>
                        <div class="block-options">
                            <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content" id="view_details_content">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--ADD NEW TOOLBOX TALKS MODAL-->
    <div class="modal fade" id="add_attendance_modal" role="dialog" aria-labelledby="add_attendance_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Add New Attendance Form</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="add_toolbox_form" method="POST" class="px-10">
                            <input type="hidden" name="add_new_toolbox_talks" value="1">
                            <input type="hidden" name="tbt_type" value="<?=$type_id ?>">
                            <input type="" hidden id="input_time_array" name="input_time_array" value="">
                            <input type="" hidden id="participant_id_array"name="participant_id_array" value="">
                            <div class="form-group row">
                                <label class="col-3" for="title">Topic</label>
                                <input type="text" class="form-control form-control-sm col-9" id="title" name="title" required>
                            </div>
                            <div class="form-group row">
                                <label class="col-3" for="contractNo">Contract no.</label>
                                <input type="text" class="form-control form-control-sm col-9" id="contract_no" name="contract_no" required>
                            </div>
                            <div class="form-group row">
                                <label class="col-3" for="location">Location</label>
                                <input type="text" class="form-control form-control-sm col-9" id="location" name="location" required>
                            </div>
                            <div class="form-group row">
                                <label class="col-3" for="date">Date</label>
                                <input type="date" class="form-control form-control-sm col-9" id="date" name="date" required>
                            </div>
                            <div class="form-group row">
                                <label class="col-4" for="time_conducted">Time Conducted</label>
                                <input type="time" class="form-control form-control-sm col-8" id="time_conducted" name="time_conducted" required>
                            </div>
                            <?php
                            $select_id = "conducted_by";
                            $select_name = "conducted_by";
//                            include 'select_employee_dropdown.php';
                            ?>
                            <div class="form-group row">
                                <label class="col-4" for="conducted_by">Conducted by </label>
                                <select class="js-select2 form-control form-control-sm col-12" id="conducted_by" style="width: 65%" name="conducted_by"  data-placeholder="Choose one..">
                                    <?php
                                    try {
                                        $employee_sql = $conn->query("
                                                            SELECT `employee_id`, `firstname`, `middlename`, `lastname`,`subscriber_id`,
                                                                   CONCAT(`firstname`,' ',middlename,' ',`lastname`) as full_name
                                                            FROM tbl_employees
                                                            INNER JOIN users ON tbl_employees.user_id = users.user_id
                                                            WHERE  `is_deleted` = 0 AND subscriber_id = $subscriber_id 
                                                            ORDER BY `firstname` ASC  ");
                                        $employees_qry1 = $employee_sql->fetchAll();
                                    } catch (Exception $e){
                                        echo $e;
                                    }
                                    ?>
                                    <?php foreach ($employees_qry1 as $employee1): ?>
                                        <?php $employee_fullname = ucwords(strtolower($employee1['full_name'])); ?>
                                        <option value="<?=$employee1['employee_id'] ?>"><?=$employee_fullname?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <!--                            <input type="hidden" class="form-control form-control-lg" id="description" name="description" required>-->
                            <div class="form-group row">
                                <label class="col-3" for="be-contact-email">Activity</label>
                                <textarea class="form-control form-control-sm col-9" id="description" name="description" placeholder="" required></textarea>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 pr-0" for="file">Attach File</label>
                                <div class="col-9 px-0">
                                    <input type="file" name="files[]" multiple >
                                    <!--                                    <input type="file" id="files[]" name="files[]" multiple>-->
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-11 col-sm-12 mb-10">
                                    <input type="hidden" name="modal_toolbox_id" id="modal_toolbox_id">
                                    <select class="js-select2 form-control" id="select_participant" name="select_participant" style="width: 100%; " data-placeholder="Choose multiple" >
                                        <?php foreach ($employees_qry1 as $employee1): ?>
                                            <?php $employee_fullname = ucwords(strtolower($employee1['firstname']." ".$employee1['middlename']." ".$employee1['lastname'])); ?>
                                            <option value="<?=$employee1['employee_id'] ?>"><?=$employee_fullname?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="col-lg-1 col-sm-12">
                                    <button type="button" class="btn btn-sm btn-success float-right" id="add_to_toolbox" onclick="select_participant_tbt()">Add</button>
                                </div>
                            </div>
                            <table class="table table-vcenter" id="table_partipants">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th style="width: 20%;">Employee name</th>
                                    <th style="width: 20%;">Set Hours</th>
                                    <th class="text-center" style="width: 10%;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="table_participants2">
                                </tbody>
                            </table>
                            <hr>
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                        <i class="fa fa-save mr-5"></i> SAVE
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- working here -->
    <!-- Add participants modal, only shows after saving new toolbox talks -->

    <!--ATTACH IMAGE FOR COMPLETION MODAL-->
    <div class="modal fade" id="set_complete_modal" tabindex="-1" role="dialog" aria-labelledby="set_complete_modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Upload File</h3>
                        <div class="block-options">
                            <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="block">
                            <div class="block-content">
                                <form id="upload_file" method="post">
                                    <input type="hidden" id="upload_file_tbt_id" name="upload_file_tbt_id">
                                    <div class="custom-file">
                                        <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                        <!-- When multiple files are selected, we use the word 'Files'. You can easily change it to your own language by adding the following to the input, eg for DE: data-lang-files="Dateien" -->
                                        <input type="file" class="custom-file-input" id="files" name="files[]" data-toggle="custom-file-input" multiple>
                                        <label class="custom-file-label" for="files">Choose files</label>
                                    </div>
                                    <button type="submit" class="btn btn-block btn-primary mt-20"><i class="fa fa-save"></i> Submit</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--VIEW ATTACHED FILE DETAILS MODAL-->
    <div class="modal fade" id="view_attached_file_modal" tabindex="-1" role="dialog" aria-labelledby="view_attached_file_modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Attached File</h3>
                        <div class="block-options">
                            <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="block">
                            <div class="block-content" >
                                <div class="row items-push" id="view_attached_file_div">

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
include_once 'toolbox_modals.php';
?>
    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page JS Code -->
    <script src="assets/js/pages/be_tables_datatables.min.js"></script>
    <script src="assets/js/plugins/select2/js/select2.full.min.js"></script>
    <script src="assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="assets/js/plugins/jquery-validation/additional-methods.js"></script>

    <!-- Page JS Helpers (Select2 plugin) -->
    <script>jQuery(function(){ Codebase.helpers('select2'); });</script>
    <script src="custom_js/toolbox_talks.js"></script>

    <script>
        $(document).ready(function () {
            <?php
            $dateTime = new DateTime();
            $month = $dateTime->format('m')-0;

             ?>
            $( <?php echo '"#0'.$month.'"'; ?>).attr( "selected", true )
            load_tbt_table(<?=$type_id ?>);
            load_over_view()
            $('#toolbox_sidebar').addClass('open');
        });

        function load_over_view(){
            let select_year = $('#select_year').val();
            let select_month = $('#select_month').val();
            let select_type = $('#select_type').val();
            $.ajax({
                type:'POST',
                url:'ajax/toolbox_talks_ajax.php',
                data:{
                    load_tbt_over_view: <?=$type_id ?>,
                    tbt_type:<?=$type_id ?>,
                    select_month: select_month,
                    select_year: select_year,
                    select_type: select_type
                },
                success: function(data) {
                    $('#tbt_overview').html(data);
                }
            });
        }
    </script>
<?php
include 'includes/footer.php';
