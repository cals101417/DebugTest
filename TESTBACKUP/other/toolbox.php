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
        <main id="main-container">
            <?php
            if (!empty($_GET['view_attendance'])){
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
                                                Count(tbl_toolbox_talks_participants.tbtp_id) AS count
                                                FROM
                                                tbl_toolbox_talks
                                                LEFT JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id
                                                WHERE
                                                tbl_toolbox_talks.tbt_id = $tbt_id AND
                                                tbl_toolbox_talks.tbt_type = 3
                                                GROUP BY
                                                tbl_toolbox_talks.tbt_id");
                $tbt_fetch = $attendance_qry->fetch();
                $status = $tbt_fetch['status'];
                $title = $tbt_fetch['title'];
                $contract_no = $tbt_fetch['contract_no'];
                $location = $tbt_fetch['location'];
                $time_conducted = $tbt_fetch['time_conducted'];
                $date_conducted = $tbt_fetch['date_conducted'];
                $conducted_by = $tbt_fetch['conducted_by'];
                $description = $tbt_fetch['description'];
                $count = $tbt_fetch['count'];
                ?>

                <!-- Page Content -->
                <div class="content">
                    <h2 class="content-heading">
                        <a href="attendance_edit.php?edit=<?=$tbt_id?>" class="btn btn-sm btn-secondary float-right">Edit</a>
                        <button class="btn btn-sm btn-alt-primary float-right" onclick="history.back()"><i class="si si-action-undo"></i> Go Back</button>
                        <?=ucwords($title)?>
                    </h2>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mt-20">
                                <tbody>
                                <tr>
                                    <td class="font-w600">Topic</td>
                                    <td><?=$title?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="font-w600">Activity</td>
                                    <td><?=$description?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="font-w600">Contract No</td>
                                    <td><?=$contract_no?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="font-w600">Location</td>
                                    <td><?=$location?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="font-w600">Conducted by</td>
                                    <td><?=$conducted_by?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="font-w600">Time Conducted</td>
                                    <td><?=$time_conducted?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="font-w600">Date Conducted</td>
                                    <td><?=date('F d Y', strtotime($date_conducted))?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td class="font-w600">No. of attendance</td>
                                    <td><?=$count?></td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td class="font-w600">Status</td>
                                    <td>
                                        <?php
                                        if($status == 0){
                                            echo 'Active';
                                        }else{
                                            echo 'Inactive';
                                        }
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6"></div>
                    </div>

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
                                <div class="col-lg-5 col-sm-12"></div>
                                <div class="col-lg-6 col-sm-12 mb-10">
                                    <input type="hidden" name="add_me" value="1">
                                    <input type="hidden" name="tbt_id" id="tbt_id" value="<?=$tbt_id?>">
                                    <select class="form-control" id="select_employee" name="select_employee">
                                        <?php
                                        $employee_qry = $conn->query("SELECT
                                                                                            tbl_employees.employee_id,
                                                                                            tbl_employees.firstname,
                                                                                            tbl_employees.lastname 
                                                                                        FROM
                                                                                            tbl_employees 
                                                                                        WHERE
                                                                                            tbl_employees.employee_id NOT IN ((SELECT tbl_toolbox_talks_participants.employee_id FROM tbl_toolbox_talks_participants 
                                                                                                                                WHERE tbl_toolbox_talks_participants.tbt_id = $tbt_id)) 
                                                                                            AND tbl_employees.is_deleted = 0");
                                        foreach ($employee_qry as $employee){
                                            $employee_fullname = ucwords(strtolower($employee['firstname']." ".$employee['lastname']));
                                            ?>
                                            <option value="<?=$employee['employee_id']?>"><?=$employee_fullname?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-lg-1 col-sm-12">
                                    <button type="submit" class="btn btn-success float-right" id="btn_add">Add</button>
                                </div>
                            </div>

                            <table class="table table-vcenter table-hover">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th style="width: 50%;text-transform: capitalize; !important;">Employee name</th>
                                    <th style="width: 20%;text-transform: capitalize; !important;">Date added</th>
                                    <th style="width: 5%;text-transform: capitalize; !important;">Time</th>
                                    <th class="text-center" style="width: 20%;text-transform: capitalize; !important;">Action</th>
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
                                        <th class="text-center" scope="row"><?=$emplyee_id?></th>
                                        <td class=""><?=$fullname?></td>
                                        <td class=""><?=date('F d, Y', strtotime($date_added))?></td>
                                        <td>
                                            <div class="input-group">
                                                <input class="form-control form-control-sm w-25" type="number" id="add_time<?=$tbtp_id?>" value="<?=$time?>">
                                                <div class="input-group-append">
                                                    <button class="btn btn-sm btn-success" onclick="set_time(<?=$tbtp_id?>)">Set</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-danger js-tooltip-enabled" id="delete" onclick="remove_attendance(<?=$tbtp_id?>,<?=$tbt_id?>)" data-toggle="tooltip" title="Remove employee from list" data-original-title="Delete">
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
                            //                                    CHECK IF TRAINING HAS UPLOADED COMPLETION FILE
                            $check_completion = $conn->prepare("SELECT * FROM `tbl_tbt_images` WHERE `tbt_id` = ? AND type = 'completion'");
                            $check_completion->execute([$tbt_id]);
                            $check_count = $check_completion->rowCount();

                            if ($check_count > 0){
                                echo '<button class="btn btn-success btn-block btn-lg">Completed</button>
                                  <a href="#" class="text-center" onclick="view_attached_file('.$tbt_id.')"> View attached file</a>';
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
                    <div class="bg-pattern" style="background-image: url('assets/media/various/bg-pattern.png');">
                        <div class="content content-top content-full text-center">
                            <div class="py-20">
                                <h1 class="h2 font-w700 text-white mb-10">Toolbox Talks - Electrical</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Hero -->
                <div class="content">
                    <h2 class="content-heading">
                        <button type="button" class="btn btn-sm btn-rounded btn-primary d-md-none float-right ml-5" data-toggle="class-toggle" data-target=".js-inbox-nav" data-class="d-none d-md-block">Menu</button>
                        <button type="button" class="btn btn-sm btn-rounded btn-success float-right" data-toggle="modal" data-target="#add_attendance_modal">Add New TBT</button>
                        Civil
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
                                    <th class="text-center" style="text-transform: capitalize; !important;">Date Created</th>
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
    <div class="modal fade" id="add_attendance_modal" tabindex="-1" role="dialog" aria-labelledby="add_attendance_modal" aria-hidden="true">
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
                            <input type="hidden" name="tbt_type" value="1">
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
                            <div class="form-group row">
                                <label class="col-4" for="conducted_by">Conducted by</label>
                                <input type="text" class="form-control form-control-sm col-8" id="conducted_by" name="conducted_by" required>
                            </div>

                            <!--                            <input type="hidden" class="form-control form-control-lg" id="description" name="description" required>-->
                            <div class="form-group row">
                                <label class="col-3" for="be-contact-email">Activity</label>
                                <textarea class="form-control form-control-sm col-9" id="description" name="description" placeholder="" required></textarea>
                            </div>
                            <div class="form-group row">
                                <label class="col-3 pr-0" for="file">Attach image</label>
                                <div class="col-9 px-0">
                                    <input type="file" name="files[]" multiple >
                                    <!--                                    <input type="file" id="files[]" name="files[]" multiple>-->
                                </div>
                            </div>
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

    <!-- Add participants modal, only shows after saving new toolbox talks -->
    <div class="modal fade" id="modal_add_participants" tabindex="-1" role="dialog" aria-labelledby="modal_add_participants" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Add Participating Employees</h3>
                        <div class="block-options">
                            <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="form-group row">
                            <div class="col-lg-11 col-sm-12 mb-10">
                                <input type="hidden" name="modal_toolbox_id" id="modal_toolbox_id">
                                <select class="form-control" id="modal_select_employee" name="modal_select_employee">

                                </select>
                            </div>
                            <div class="col-lg-1 col-sm-12">
                                <button type="button" class="btn btn-success float-right" id="add_to_toolbox">Add</button>
                            </div>
                        </div>
                        <table class="table table-vcenter">
                            <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th style="width: 20%;">Employee name</th>
                                <th class="text-center" style="width: 10%;">Action</th>
                            </tr>
                            </thead>
                            <tbody id="modal_table_participants">

                            </tbody>
                        </table>

                        <button class="btn btn-primary float-right" onclick="reload_page()">Submit</button>
                    </div>
                    <div class="block-content block-content-full bg-body-light font-size-sm">
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page JS Code -->
    <script src="assets/js/pages/be_tables_datatables.min.js"></script>

    <script src="custom_js/toolbox_talks.js"></script>
    <script>
        $(document).ready(function () {
            load_tbt_table(3);
        });
    </script>
<?php
include 'includes/footer.php';
