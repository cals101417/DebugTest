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
            <section>
                <div class="main-content">
            <?php
            if (!empty($_GET['view_training'])){
                $training_id = $_GET['view_training'];
                $training_qry = $conn->query("SELECT
                                                            tbl_trainings.title,
                                                            tbl_trainings.location,
                                                            tbl_trainings.contract_no,
                                                            tbl_trainings.remarks,
                                                            tbl_trainings.trainer,
                                                            tbl_trainings.training_hrs,
                                                            tbl_trainings.user_id,
                                                            tbl_trainings.employee_id,
                                                            tbl_trainings.`status`,
                                                            tbl_trainings.date_created,
                                                            tbl_trainings.date_expired,
                                                            tbl_trainings.date_updated,
                                                            tbl_trainings_external_details.course_price,
                                                            tbl_trainings_external_details.date_requested,
                                                            tbl_trainings_external_details.requested_by,
                                                            tbl_trainings_external_details.reviewed_by,
                                                            tbl_trainings_external_details.approved_by 
                                                        FROM
                                                            tbl_trainings
                                                            INNER JOIN tbl_trainings_external_details ON tbl_trainings.training_id = tbl_trainings_external_details.training_id 
                                                        WHERE
                                                            tbl_trainings_external_details.training_id = $training_id");
                $training_details = $training_qry->fetch();

                ?>
                <style>

                    @media print {
                        .items-table input {
                            line-height:1.5em;
                        }

                        input:focus {
                            outline: 0;
                        }

                        .col-4.input-container input {
                            width: 100%;
                        }

                        input:hover, textarea:hover, input:focus, textarea:focus {
                            border: 1px solid #CCC;
                        }
                    }
                </style>
                <!-- Page Content -->
                <div class="content">
                    <h2 class="content-heading">
                        <a href="training_edit.php?edit=<?=$training_id?>" class="btn btn-sm btn-secondary float-right">Edit</a>
                        <button class="btn btn-sm btn-alt-primary float-right" onclick="history.back()"><i class="si si-action-undo"></i> Go Back</button>
                        <?=ucwords($training_details['title'])?>
                    </h2>
                    <table class="table table-striped table-borderless table-sm mt-20">
                        <tbody>
                        <tr>
                            <td class="font-w600"> Title</td>
                            <td><?=$training_details['title']?></td>
                            <td class="font-w600">Date of Training</td>
                            <td><?=date('F d, Y', strtotime($training_details['date_created']))?></td>
                        </tr>
                        <tr>
                            <td class="font-w600">Location</td>
                            <td><?=$training_details['location']?></td>
                            <td class="font-w600">Date Expired</td>
                            <td><?=date('F d, Y', strtotime($training_details['date_expired']))?></td>
                        </tr>
                        <tr>
                            <td class="font-w600">Contract no.</td>
                            <td><?=$training_details['contract_no']?></td>
                            <td class="font-w600">Training Hours</td>
                            <td><?=$training_details['training_hrs']?></td>
                        </tr>
                        <tr>
                            <td class="font-w600">Conducted by:</td>
                            <td><?=$training_details['trainer']?></td>
                            <td class="font-w600">Status</td>
                            <td><span class="badge badge-secondary"><?=$training_details['status']?></span></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <input type="hidden" id="progress_id" value="<?=$progress_id?>">
                                <?php
                                if ($training_details['approved_by'] == $session_emp_id){
                                //  CHECK IF TRAINING IS APPROVED
                                    $approve_check = $conn->prepare("SELECT training_progress FROM tbl_trainings_external_progress WHERE training_id = ? AND (approve_status like ? OR approve_status IS NULL)");
                                    $approve_check->execute([$training_id,'']);
                                    $approve_count = $approve_check->rowCount();
                                    $approve = $approve_check->fetch();
                                    $progress_id = $approve['training_progress'];
                                    if ($approve_count > 0){
                                        echo '<button class="btn btn-sm btn-success float-right" id="btn_approve">Approve</button>';
                                    }else{
                                        echo '<button class="btn btn-sm btn-success float-right" disabled>Approved</button>';
                                    }
                                }
                                if ($training_details['reviewed_by'] == $session_emp_id){
//                                    CHECK IF TRAINING IS REVIEWED
                                    $review_check = $conn->prepare("SELECT training_progress FROM tbl_trainings_external_progress WHERE training_id = ? AND (review_status like ? OR review_status IS NULL)");
                                    $review_check->execute([$training_id,'']);
                                    $review_count = $review_check->rowCount();
                                    $review = $review_check->fetch();
                                    $progress_id = $review['training_progress'];
                                    if ($review_count > 0){
                                        echo '<button class="btn btn-sm btn-alt-danger float-right" id="btn_review">Set as Reviewed</button>';
                                    }else{
                                        echo '<button class="btn btn-sm btn-alt-danger float-right" disabled>Reviewed</button>';
                                    }
                                ?>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="block">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">List of Trainees</h3>
                            <div class="block-options">
                                <div class="block-options-item">
                                    <!--                                <code>.table</code>-->
                                </div>
                            </div>
                        </div>
                        <div class="block-content">
                            <form id="add_trainee" method="post">
                                <div class="form-group row">
                                    <div class="col-lg-5 col-sm-12"></div>
                                    <div class="col-lg-6 col-sm-12 mb-10">
                                        <input type="hidden" name="add_trainee" value="1">
                                        <input type="hidden" id="training_id" name="training_id" value="<?=$training_id?>">
                                        <select class="form-control" id="select_employee" name="select_employee">
                                            <?php
                                            $trainer_qry = $conn->query("SELECT
                                                                                tbl_employees.employee_id,
                                                                                tbl_employees.firstname,
                                                                                tbl_employees.middlename,
                                                                                tbl_employees.lastname
                                                                                FROM
                                                                                tbl_employees
                                                                                WHERE
                                                                                tbl_employees.employee_id 
                                                                                    NOT IN ((SELECT `employee_id` FROM `tbl_training_trainees` WHERE `training_id` = $training_id)) 
                                                                                AND
                                                                                tbl_employees.is_deleted = 0
                                                                                ");
                                            foreach ($trainer_qry as $trainer){
//                                                $training
                                                $employee_fullname = ucwords(strtolower($trainer['firstname']." ".$trainer['lastname']));
                                                ?>
                                                <option value="<?=$trainer['employee_id']?>"><?=$employee_fullname?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-1 col-sm-12">
                                        <button type="submit" class="btn btn-success float-right" id="add_to_training">Add</button>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-vcenter">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th style="width: 20%;">Employee name</th>
                                    <th class="text-center" style="width: 20%;">Date Added</th>
                                    <th class="text-center" style="width: 20%;">Status</th>
                                    <th class="text-center" style="width: 10%;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $trainings = $conn->query("SELECT
                                                                    tbl_training_trainees.employee_id,
                                                                    tbl_training_trainees.date_joined,
                                                                    tbl_employees.firstname,
                                                                    tbl_employees.lastname
                                                                    FROM
                                                                    tbl_training_trainees
                                                                    INNER JOIN tbl_employees ON tbl_employees.employee_id = tbl_training_trainees.employee_id
                                                                    WHERE
                                                                    tbl_training_trainees.training_id = $training_id AND tbl_training_trainees.is_removed = 0");
                                $count = 1;
                                foreach ($trainings as $training) {
                                    $employee_name = ucwords(strtolower($training['firstname'].' '.$training['lastname']));
                                    ?>
                                    <tr>
                                        <th class="text-center" scope="row"><?=$count++?></th>
                                        <td class=""><?=$employee_name?></td>
                                        <td class="text-center"><?=date('F d, Y', strtotime($training['date_joined']))?></td>
                                        <td class="d-none d-sm-table-cell text-center">
                                            <span class="badge badge-secondary">None</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
<!--                                                <a href="training_in_house.php?view_training=--><?//=$training['training_id']?><!--" class="btn btn-sm btn-primary js-tooltip-enabled" data-toggle="tooltip" title="" data-original-title="Edit">-->
<!--                                                    <i class="fa fa-eye"></i>-->
<!--                                                </a>-->
<!--                                                <button type="button" class="btn btn-sm btn-secondary js-tooltip-enabled" data-toggle="tooltip" title="" data-original-title="Edit">-->
<!--                                                    <i class="fa fa-pencil"></i>-->
<!--                                                </button>-->
                                                <button type="button" class="btn btn-sm btn-danger js-tooltip-enabled" id="delete" onclick="remove_trainee(<?=$training_id?>,<?=$training['employee_id']?>)" data-toggle="tooltip" title="Remove employee from list" data-original-title="Delete">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>

                                <tr class="bg-black-op-5">
                                    <td class="font-w600 text-right" colspan="4">Total Attendees:</td>
                                    <td class="text-left" ><?=$count?></td>
                                </tr>
                                <tr class="bg-black-op-5">
                                    <td class="font-w600 text-right" colspan="4">Course Price:</td>
                                    <td class="text-left" >$<?=$training_details['course_price']?></td>
                                </tr>
                                <tr class="bg-black-op-5">
                                    <td class="font-w600 text-right" colspan="4">Total Amount:</td>
                                    <td class="text-left" >$<?=$training_details['course_price']*$count?></td>
                                </tr>
                                <tr class="bg-black-op-5">
                                    <td class="font-w600 text-right" colspan="4">Date Requested:</td>
                                    <td class="text-left" ><?=date('F d, Y', strtotime($training_details['date_requested']))?></td>
                                </tr>
                                </tbody>
                            </table>

                            <div class="form-group row">
                                <div class="col-lg-4">
                                    <div class="form-material floating">
                                        <?php
                                        $requested_by_id = $training_details['requested_by'];
                                        $requested_by_qry = $conn->query("SELECT fname,lname FROM users WHERE users.user_id = $requested_by_id ");
                                        $requested_by = $requested_by_qry->fetch();
                                        $requested_by_name = ucwords(strtolower($requested_by['fname'].' '.$requested_by['lname']))
                                        ?>
                                        <input type="email" class="form-control text-center" id="material-email2" name="material-email2" value="<?=$requested_by_name?>" disabled>
                                    </div>
                                    <div class="text-center">
                                        <label class="text-center" for="remarks">Requested by</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-material floating">
                                        <?php
                                        $reviewed_by_id = $training_details['reviewed_by'];
                                        $reviewed_by_qry = $conn->query("SELECT firstname,lastname FROM tbl_employees WHERE employee_id = $reviewed_by_id");
                                        $reviewed_by = $reviewed_by_qry->fetch();
                                        $reviewed_by_name = ucwords(strtolower($reviewed_by['firstname'].' '.$reviewed_by['lastname']))
                                        ?>
                                        <input type="email" class="form-control text-center" id="material-email2" name="material-email2" value="<?=$reviewed_by_name?>" disabled>
                                    </div>
                                    <div class="text-center">
                                        <label class="text-center" for="remarks">Reviewed by</label>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <?php
                                        $approved_by_id = $training_details['approved_by'];
                                        $approved_by_qry = $conn->query("SELECT firstname,lastname FROM tbl_employees WHERE employee_id = $reviewed_by_id ");
                                        $approved_by = $approved_by_qry->fetch();
                                        $approved_by_name = ucwords(strtolower($approved_by['firstname'].' '.$approved_by['lastname']))
                                    ?>
                                    <div class="form-material floating">
                                        <input type="email" class="form-control text-center" id="material-email2" name="material-email2" value="<?=$approved_by_name?>" disabled>
                                    </div>
                                    <div class="text-center">
                                        <label class="text-center" for="remarks">Approved by</label>
                                    </div>
                                </div>
                            </div>

                            <div class="content-heading"></div>
                            <div class="form-group row">
                                <label class="col-lg-6" for="remarks">Files Attached</label>


                                <div class="col-lg-6">
                                    <div class="row items-push">
                                        <?php
                                        $images_qry = $conn->query("SELECT `training_img_id`, `training_id`, `image`, `date_uploaded` FROM `tbl_trainings_images` WHERE `training_id` = $training_id");
                                        foreach ($images_qry as $image){
                                            ?>
                                        <div class="options-container fx-item-zoom-in fx-overlay-slide-left">
                                            <a class="text-black" href="assets/media/photos/training/<?= $image['image'] ?>" alt=""> <?= $image['image'] ?></a><br>
                                            <div class="options-overlay bg-black-op">
                                                <div class="options-overlay-content">
                                                    <h3 class="h4 text-white mb-5">Image Attached</h3>
                                                    <h4 class="h6 text-white-op mb-15">More Details</h4>
                                                    <button class="btn btn-sm btn-rounded btn-alt-primary min-width-75" href="javascript:void(0)">
                                                        <i class="fa fa-pencil"></i> Edit
                                                    </button>
                                                    <button class="btn btn-sm btn-rounded btn-alt-danger min-width-75" href="javascript:void(0)">
                                                        <i class="fa fa-times"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Page Content -->
                <script src="assets/js/codebase.core.min.js"></script>
                <script src="assets/js/codebase.app.min.js"></script>
                <script src="custom_js/external_training.js"></script>
                <?php
            }else{
                ?>
                <!-- Hero -->
                <div class="bg-gd-sea">
                    <div class="bg-pattern" style="background-image: url('assets/media/photos/construction13.jpg');">
                        <div class="content content-top content-full text-center bg-black-op-75">
                            <div class="py-20">
                                <h1 class="h2 font-w700 text-white mb-10">External Training</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Hero -->
                <!-- Page Content -->
                <div class="content">
                    <h2 class="content-heading">
                        <button type="button" class="btn btn-sm btn-rounded btn-primary d-md-none float-right ml-5" data-toggle="class-toggle" data-target=".js-inbox-nav" data-class="d-none d-md-block">Menu</button>
                        <?php
                        //                    ADD ACCESS TRAINING ENABLE DISABLE BUTTON
                        $add_disable = '';
                        $access = $conn->query("SELECT `status` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 5 ");
                        if ($access->rowCount() > 0){
                            $add_access = $access->fetch();
                            if ($add_access['status'] == 1){
                                $add_disable = 'disabled';
                            }
                        }else{
                            $add_disable = 'disabled';
                        }
                        ?>
                        <button type="button" class="btn btn-sm btn-rounded btn-success float-right" data-toggle="modal" data-target="#create_training_modal" <?=$add_disable?>>Create New Training</button>
                        <span class="font-w700 text-corporate-dark">Third-Party Trainings</span>
                    </h2>
                    <div class="block">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Training List</h3>
                            <div class="block-options">
                                <div class="block-options-item">
                                    <!--                                <code>.table</code>-->
                                </div>
                            </div>
                        </div>
                        <div class="block-content">
                            <table class="table table-vcenter">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th style="width: 20%;">Title</th>
                                    <th class="text-center">No. of Participants</th>
                                    <th class="text-center">Conducted by</th>
                                    <th class="text-center" style="width: 20%;">Date Created</th>
                                    <th class="text-center" style="text-transform: capitalize; !important;">Training Date</th>
                                    <th class="text-center" style="text-transform: capitalize; !important;">Expiration</th>
                                    <th class="text-center" style="width: 20%;">Status</th>
                                    <th class="text-center" style="width: 10%;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                //ENABLE/DISABLE EDIT AND DELETE BUTTON ACCORDING TO USER ACCESS

                                //EDIT ACCESS
                                $edit_disable = '';
                                $edit_access_qry = $conn->query("SELECT `status` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 6");
                                if ($edit_access_qry->rowCount() > 0){
                                    $edit_access = $edit_access_qry->fetch();
                                    $status = $edit_access['status'];
                                    if ($status == 1){
                                        $edit_disable = 'disabled';
                                    }
                                }else{
                                    $edit_disable = 'disabled';
                                }

                                //                            DELETE ACCESS
                                $delete_disable = '';
                                $delete_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 7");
                                if ($delete_access->rowCount() > 0){
                                    $delete_access = $delete_access->fetch();
                                    $delete_status = $delete_access['status'];
                                    if ($delete_status == 1){
                                        $delete_disable = 'disabled';
                                    }
                                }else{
                                    $delete_disable = 'disabled';
                                }

                                $trainings = $conn->query("SELECT
                                                                tbl_trainings.training_id,
                                                                tbl_trainings.title,
                                                                tbl_trainings.trainer,
                                                                tbl_trainings.`status`,
                                                                tbl_trainings.date_created,
                                                                tbl_trainings.training_date,
                                                                tbl_trainings.date_expired,                                                                
                                                                Count(tbl_training_trainees.trainee_id) as count
                                                                FROM
                                                                tbl_trainings
                                                                INNER JOIN tbl_training_trainees ON tbl_trainings.training_id = tbl_training_trainees.training_id
                                                                WHERE
                                                                tbl_trainings.is_deleted = 0 AND tbl_trainings.type = 3
                                                                GROUP BY
                                                                tbl_trainings.training_id");
                                $count = 1;
                                foreach ($trainings as $training) {
                                    $training_id = $training['training_id'];
                                    $status = $training['status'];
                                    $today = new DateTime(date("Y/m/d H:i:s"));
                                    $training_date = new DateTime($training['training_date']);
                                    $expiration = new DateTime($training['date_expired']);
                                    $origin = date_format($training_date, "Y/m/d H:i:s");
                                    $target = date_format($expiration, "Y/m/d H:i:s");
                                    $interval = $today->diff($expiration);
                                    $interval=  $interval->format('%R%a days');
                                    if ($interval >  0){
                                        if ($interval <= 5){
                                            $status_td = '<span class="badge bg-gd-sun  text-white">'."Soon To Expire".'</span>';
                                        } else {
                                            $status_td = '<span class="badge badge-success ">'."Valid".'</span>';
                                        }
                                    } else {
                                        $status_td = '<span class="badge badge-danger">'."Expired".'</span>';
                                    }
                                    ?>
                                    <tr>
                                        <th class="text-center" scope="row"><?=$count++?></th>
                                        <td><?=$training['title']?></td>
                                        <td class="text-center"><?=$training['count']?></td>
                                        <td class="text-center"><?=$training['trainer']?></td>
                                        <td class="text-center"><?=date('F d, Y', strtotime($training['date_created']))?></td>
                                        <td class="text-center"><?=date('F d, Y', strtotime($training['training_date']))?></td>
                                        <td class="text-center"><?=date('F d, Y', strtotime($training['date_expired']))?></td>
                                        <td class="d-none d-sm-table-cell text-center">
                                            <?php echo $status_td;
//                                            if ($training['status'] == 'Approved'){
//                                                echo '<span class="badge badge-success">Approved</span>';
//                                            }elseif ($training['status'] == 'Review Accepted'){
//                                                echo '<span class="badge badge-warning">Reviewed</span>';
//                                            }else{
//                                                echo '<span class="badge badge-secondary">Requested for Review</span>';
//                                            }

                                            ?>

                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-primary js-tooltip-enabled" onclick="view_training(<?=$training_id?>,'external')" data-toggle="tooltip" title="View Details" data-original-title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="<?=(($edit_disable == '')?'training_third_party.php?view_training='.$training['training_id']:'#')?>" class="btn btn-sm btn-secondary js-tooltip-enabled" data-toggle="tooltip" title="Edit Training" data-original-title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-secondary js-tooltip-enabled" id="delete" onclick="delete_training(<?=$training['training_id']?>)" data-toggle="tooltip" title="" data-original-title="Delete" <?=$delete_disable?>>
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
            </div>
            </section>
        </main>
        <!-- END Main Container -->

    </div>
<!--     Add Employee Modal -->
    <div class="modal fade" id="create_training_modal" tabindex="-1" role="dialog" aria-labelledby="create_training_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Create New External Training</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="add_form" method="post">
                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="title">Course Title</label>
                                    <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="" required>
                                </div>
                                <div class="col-6">
                                    <label for="contract_no">Contract No.</label>
                                    <input type="text" class="form-control form-control-lg" id="contract_no" name="contract_no" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6">
                                    <label  for="location">Training Location</label>
                                    <input type="text" class="form-control form-control-lg" id="location" name="location" placeholder="" required>
                                </div>
                                <div class="col-6">
                                    <label class="" for="trainer">Conducted by:</label>
                                    <input type="text" class="form-control form-control-lg" id="trainer" name="select_trainer" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="be-contact-name">Currency</label>
                                    <!--                                                <input type="text" class="form-control" id="currency" name="currency" value="--><?//=$inventory_currency?><!--" required>-->
                                    <select class="form-control form-control-lg "  id="currency" name="currency" required >
                                        <option value="" disabled="disabled" selected = "selected">Select Currenct</option>
                                        <option value="AED">United Arab Emirates dirham</option>
                                        <option value="AFN">Afghan afghani</option>
                                        <option value="ALL">Albanian lek</option>
                                        <option value="AMD">Armenian dram</option>
                                        <option value="AOA">Angolan kwanza</option>
                                        <option value="ARS">Argentine peso</option>
                                        <option value="AUD">Australian dollar</option>
                                        <option value="AWG">Aruban florin</option>
                                        <option value="AZN">Azerbaijani manat</option>
                                        <option value="BAM">Bosnia and Herzegovina convertible mark</option>
                                        <option value="BBD">Barbadian dollar</option>
                                        <option value="BDT">Bangladeshi taka</option>
                                        <option value="BGN">Bulgarian lev</option>
                                        <option value="BHD">Bahraini dinar</option>
                                        <option value="BIF">Burundian franc</option>
                                        <option value="BMD">Bermudian dollar</option>
                                        <option value="BND">Brunei dollar</option>
                                        <option value="BOB">Bolivian boliviano</option>
                                        <option value="BRL">Brazilian real</option>
                                        <option value="BSD">Bahamian dollar</option>
                                        <option value="BTN">Bhutanese ngultrum</option>
                                        <option value="BWP">Botswana pula</option>
                                        <option value="BYR">Belarusian ruble</option>
                                        <option value="BZD">Belize dollar</option>
                                        <option value="CAD">Canadian dollar</option>
                                        <option value="CDF">Congolese franc</option>
                                        <option value="CHF">Swiss franc</option>
                                        <option value="CLP">Chilean peso</option>
                                        <option value="CNY">Chinese yuan</option>
                                        <option value="COP">Colombian peso</option>
                                        <option value="CRC">Costa Rican colón</option>
                                        <option value="CUP">Cuban convertible peso</option>
                                        <option value="CVE">Cape Verdean escudo</option>
                                        <option value="CZK">Czech koruna</option>
                                        <option value="DJF">Djiboutian franc</option>
                                        <option value="DKK">Danish krone</option>
                                        <option value="DOP">Dominican peso</option>
                                        <option value="DZD">Algerian dinar</option>
                                        <option value="EGP">Egyptian pound</option>
                                        <option value="ERN">Eritrean nakfa</option>
                                        <option value="ETB">Ethiopian birr</option>
                                        <option value="EUR">Euro</option>
                                        <option value="FJD">Fijian dollar</option>
                                        <option value="FKP">Falkland Islands pound</option>
                                        <option value="GBP">British pound</option>
                                        <option value="GEL">Georgian lari</option>
                                        <option value="GHS">Ghana cedi</option>
                                        <option value="GMD">Gambian dalasi</option>
                                        <option value="GNF">Guinean franc</option>
                                        <option value="GTQ">Guatemalan quetzal</option>
                                        <option value="GYD">Guyanese dollar</option>
                                        <option value="HKD">Hong Kong dollar</option>
                                        <option value="HNL">Honduran lempira</option>
                                        <option value="HRK">Croatian kuna</option>
                                        <option value="HTG">Haitian gourde</option>
                                        <option value="HUF">Hungarian forint</option>
                                        <option value="IDR">Indonesian rupiah</option>
                                        <option value="ILS">Israeli new shekel</option>
                                        <option value="IMP">Manx pound</option>
                                        <option value="INR">Indian rupee</option>
                                        <option value="IQD">Iraqi dinar</option>
                                        <option value="IRR">Iranian rial</option>
                                        <option value="ISK">Icelandic króna</option>
                                        <option value="JEP">Jersey pound</option>
                                        <option value="JMD">Jamaican dollar</option>
                                        <option value="JOD">Jordanian dinar</option>
                                        <option value="JPY">Japanese yen</option>
                                        <option value="KES">Kenyan shilling</option>
                                        <option value="KGS">Kyrgyzstani som</option>
                                        <option value="KHR">Cambodian riel</option>
                                        <option value="KMF">Comorian franc</option>
                                        <option value="KPW">North Korean won</option>
                                        <option value="KRW">South Korean won</option>
                                        <option value="KWD">Kuwaiti dinar</option>
                                        <option value="KYD">Cayman Islands dollar</option>
                                        <option value="KZT">Kazakhstani tenge</option>
                                        <option value="LAK">Lao kip</option>
                                        <option value="LBP">Lebanese pound</option>
                                        <option value="LKR">Sri Lankan rupee</option>
                                        <option value="LRD">Liberian dollar</option>
                                        <option value="LSL">Lesotho loti</option>
                                        <option value="LTL">Lithuanian litas</option>
                                        <option value="LVL">Latvian lats</option>
                                        <option value="LYD">Libyan dinar</option>
                                        <option value="MAD">Moroccan dirham</option>
                                        <option value="MDL">Moldovan leu</option>
                                        <option value="MGA">Malagasy ariary</option>
                                        <option value="MKD">Macedonian denar</option>
                                        <option value="MMK">Burmese kyat</option>
                                        <option value="MNT">Mongolian tögrög</option>
                                        <option value="MOP">Macanese pataca</option>
                                        <option value="MRO">Mauritanian ouguiya</option>
                                        <option value="MUR">Mauritian rupee</option>
                                        <option value="MVR">Maldivian rufiyaa</option>
                                        <option value="MWK">Malawian kwacha</option>
                                        <option value="MXN">Mexican peso</option>
                                        <option value="MYR">Malaysian ringgit</option>
                                        <option value="MZN">Mozambican metical</option>
                                        <option value="NAD">Namibian dollar</option>
                                        <option value="NGN">Nigerian naira</option>
                                        <option value="NIO">Nicaraguan córdoba</option>
                                        <option value="NOK">Norwegian krone</option>
                                        <option value="NPR">Nepalese rupee</option>
                                        <option value="NZD">New Zealand dollar</option>
                                        <option value="OMR">Omani rial</option>
                                        <option value="PAB">Panamanian balboa</option>
                                        <option value="PEN">Peruvian nuevo sol</option>
                                        <option value="PGK">Papua New Guinean kina</option>
                                        <option value="PHP">Philippine peso</option>
                                        <option value="PKR">Pakistani rupee</option>
                                        <option value="PLN">Polish złoty</option>
                                        <option value="PRB">Transnistrian ruble</option>
                                        <option value="PYG">Paraguayan guaraní</option>
                                        <option value="QAR">Qatari riyal</option>
                                        <option value="RON">Romanian leu</option>
                                        <option value="RSD">Serbian dinar</option>
                                        <option value="RUB">Russian ruble</option>
                                        <option value="RWF">Rwandan franc</option>
                                        <option value="SAR">Saudi riyal</option>
                                        <option value="SBD">Solomon Islands dollar</option>
                                        <option value="SCR">Seychellois rupee</option>
                                        <option value="SDG">Singapore dollar</option>
                                        <option value="SEK">Swedish krona</option>
                                        <option value="SGD">Singapore dollar</option>
                                        <option value="SHP">Saint Helena pound</option>
                                        <option value="SLL">Sierra Leonean leone</option>
                                        <option value="SOS">Somali shilling</option>
                                        <option value="SRD">Surinamese dollar</option>
                                        <option value="SSP">South Sudanese pound</option>
                                        <option value="STD">São Tomé and Príncipe dobra</option>
                                        <option value="SVC">Salvadoran colón</option>
                                        <option value="SYP">Syrian pound</option>
                                        <option value="SZL">Swazi lilangeni</option>
                                        <option value="THB">Thai baht</option>
                                        <option value="TJS">Tajikistani somoni</option>
                                        <option value="TMT">Turkmenistan manat</option>
                                        <option value="TND">Tunisian dinar</option>
                                        <option value="TOP">Tongan paʻanga</option>
                                        <option value="TRY">Turkish lira</option>
                                        <option value="TTD">Trinidad and Tobago dollar</option>
                                        <option value="TWD">New Taiwan dollar</option>
                                        <option value="TZS">Tanzanian shilling</option>
                                        <option value="UAH">Ukrainian hryvnia</option>
                                        <option value="UGX">Ugandan shilling</option>
                                        <option value="USD">United States dollar</option>
                                        <option value="UYU">Uruguayan peso</option>
                                        <option value="UZS">Uzbekistani som</option>
                                        <option value="VEF">Venezuelan bolívar</option>
                                        <option value="VND">Vietnamese đồng</option>
                                        <option value="VUV">Vanuatu vatu</option>
                                        <option value="WST">Samoan tālā</option>
                                        <option value="XAF">Central African CFA franc</option>
                                        <option value="XCD">East Caribbean dollar</option>
                                        <option value="XOF">West African CFA franc</option>
                                        <option value="XPF">CFP franc</option>
                                        <option value="YER">Yemeni rial</option>
                                        <option value="ZAR">South African rand</option>
                                        <option value="ZMW">Zambian kwacha</option>
                                        <option value="ZWL">Zimbabwean dollar</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="" for="price">Course Price</label>
                                    <input type="number" class="form-control form-control-lg" id="price" name="price"  required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="date">Date of Training</label>
                                    <input type="date" class="form-control form-control-lg" id="date" name="date" placeholder="" required>
                                </div>
                                <div class="col-6">
                                    <label for="date_expired">Date Expired</label>
                                    <input type="date" class="form-control form-control-lg" id="date_expired" name="date_expired" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="training_hrs">Training Hours</label>
                                    <input type="number" class="form-control form-control-lg" id="training_hrs" name="training_hrs" placeholder="" required>
                                </div>
                                <div class="col-6 ">
                                    <label for="file">Upload image file</label>
                                    <input type="file" name="files[]" multiple>
                                    <!-- <input type="file" id="files[]" name="files[]" multiple> -->
                                </div>

<!--                                <div class="col-8">-->
<!--                                    <label for="status">Status</label>-->
<!--                                    <input type="text" class="form-control form-control-lg" id="status" name="status" placeholder="" required>-->
<!--                                </div>-->
                            </div>
                            <div class="form-group row">
                                <div class="col-6">
                                    <label for="reviewed">Reviewed by:</label>
                                    <select class="form-control" id="select_reviewed" name="select_reviewed">
                                        <?php
                                        try {
                                            $user2_sql = "SELECT employee_id, users.user_id, firstname, lastname, tbl_employees.email
                                            FROM users
                                            INNER JOIN tbl_employees ON users.emp_id = tbl_employees.employee_id
                                            WHERE users.subscriber_id = $subscriber_id AND users.deleted = 0 AND users.`status` = 1 AND emp_id != $session_emp_id";
                                            $users_qry2 = $conn->query($user2_sql);

                                            foreach ($users_qry2 as $user2){
                                                if ($user2['employee_id'] == $session_emp_id){
                                                    $user_fullname = 'You';
                                                }else{
                                                    $user_fullname = ucwords(strtolower($user2['firstname']." ".$user2['lastname']));
                                                }
                                                ?>
                                                <option value="<?=$user2['employee_id']?>"><?=$user_fullname?></option>
                                                <?php
                                            }

                                        } catch (Exception $e) {
                                            echo $e;
                                        }

                                        ?>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="approved">Approved by:</label>
                                    <select class="form-control" id="select_approved" name="select_approved">
                                        <?php

                                        $users_qry2 = $conn->query($user2_sql);
                                        foreach ($users_qry2 as $user2){
                                            if ($user2['employee_id'] == $session_emp_id){
                                                $user_fullname = 'You';
                                            }else{
                                                $user_fullname = ucwords(strtolower($user2['firstname']." ".$user2['lastname']));
                                            }
                                            ?>
                                            <option value="<?=$user2['employee_id']?>"><?=$user_fullname?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" id="type" name="type" value="3">
<!--                            <div class="form-group row">-->
<!---->
<!--                                <div class="col-8">-->
<!--                                    <label for="remarks">Remarks</label>-->
<!--                                    <textarea class="form-control form-control-lg" id="remarks" name="remarks" placeholder="" required></textarea>-->
<!--                                </div>-->
<!--                            </div>-->
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-block btn-hero btn-success min-width-175">
                                        <i class="fa fa-plus mr-5"></i> Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Top Modal -->

    <!-- Add Employee Modal -->
    <div class="modal fade" id="loader_modal" tabindex="-1" role="dialog" aria-labelledby="loader_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Please wait..</h3>
                        <div class="block-options">
<!--                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">-->
<!--                                <i class="si si-close"></i>-->
<!--                            </button>-->
                        </div>
                    </div>
                    <div class="block-content bg-primary-dark text-center text-white mb-20 pb-20 pt-0" >
                        <i class="fa fa-4x fa-cog fa-spin text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Top Modal -->

    <!--VIEW TRAINING DETAILS MODAL-->
    <div class="modal fade" id="modal_view_details" tabindex="-1" role="dialog" aria-labelledby="modal_view_details" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark d-print-none">
                        <h3 class="block-title">Training Details</h3>
                        <div class="block-options">
                            <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content" id="view_details_content">
                        <div id="view_details_content2">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- ADD PARTICIPANTS MODAL FOR NEWLY CREATED TRAINING -->
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
                                <input type="hidden" name="modal_training_id" id="modal_training_id">
                                <select class="form-control" id="modal_select_employee" name="select_employee">

                                </select>
                            </div>
                            <div class="col-lg-1 col-sm-12">
                                <button type="button" class="btn btn-success float-right" id="add_to_training">Add</button>
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
                    </div>
                    <div class="modal-footer">
                        <div class="block-content block-content-full bg-body-light font-size-sm">
                            <button class="btn btn-primary float-right" onclick="reload_page()">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
include_once 'training_modals.php';
?>
    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script>
        $(document).ready(function () {
            // Capitalize th of tables
            $("th").attr("style","text-transform: capitalize");
            $('#training_sidebar').addClass('open');
        });


    </script>
    <script src="custom_js/training.js"></script>

<?php
include 'includes/footer.php';
