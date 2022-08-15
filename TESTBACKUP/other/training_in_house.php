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
                                                        tbl_trainings.trainer,
                                                        tbl_trainings.training_hrs,
                                                        tbl_trainings.user_id,
                                                        tbl_trainings.employee_id,
                                                        tbl_trainings.`status`,
                                                        tbl_trainings.date_created,
                                                        tbl_trainings.date_expired,
                                                        tbl_trainings.date_updated
                                                        FROM
                                                        tbl_trainings
                                                        WHERE
                                                        tbl_trainings.training_id = $training_id");
                $training_details = $training_qry->fetch();
                ?>
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
                            <td class="font-w600">Title</td>
                            <td><?=$training_details['title']?></td>
                            <td class="font-w600">Date of Training</td>
                            <td><?=date('F d Y', strtotime($training_details['date_created']))?></td>
                        </tr>
                        <tr>
                            <td class="font-w600">Location</td>
                            <td><?=$training_details['location']?></td>
                            <td class="font-w600">Date Expired</td>
                            <td><?=date('F d Y', strtotime($training_details['date_expired']))?></td>
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
                                        <input type="hidden" name="training_id" value="<?=$training_id?>">
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
                                                                                ORDER BY firstname ASC
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
                                    <th class="text-center" style="width: 5%;text-transform: capitalize; !important;">#</th>
                                    <th style="width: 20%;text-transform: capitalize; !important;">Employee name</th>
                                    <th class="text-center" style="width: 20%;text-transform: capitalize; !important;">Date Added</th>
                                    <th class="text-center" style="width: 20%;text-transform: capitalize; !important;">Status</th>
                                    <th class="text-center" style="width: 10%;text-transform: capitalize; !important;">Action</th>
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
                                        <td class="text-center"><?=date('F d Y', strtotime($training['date_joined']))?></td>
                                        <td class="d-none d-sm-table-cell text-center">
                                            <span class="badge badge-secondary">None</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-danger js-tooltip-enabled" id="delete" onclick="remove_trainee(<?=$training_id?>,<?=$training['employee_id']?>)" data-toggle="tooltip" title="Remove employee from list" data-original-title="Delete">
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
                            <div class="content-heading"></div>
                            <div class="form-group row">

                                <div class="col-lg-6">
                                    <div class="row items-push">
                                        <?php
                                        $images_qry = $conn->query("SELECT `training_img_id`, `training_id`, `image`, `date_uploaded` FROM `tbl_trainings_images` WHERE `training_id` = $training_id");
                                        foreach ($images_qry as $image){
                                            ?>
                                            <div class="col-md-4 animated fadeIn mt-10">
                                                <div class="options-container fx-item-zoom-in fx-overlay-slide-left">
                                                    <img class="img-thumbnail options-item" src="assets/media/photos/training/<?=$image['image']?>" alt="">
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
                                            </div>
                                            <?php
                                        }
                                        ?>

                                    </div>
                                </div>
                                <div class="col-lg-12 mt-20 text-center">

                                    <?php
                                    //                                    CHECK IF TRAINING HAS UPLOADED COMPLETION FILE
                                    $check_completion = $conn->prepare("SELECT `training_id` FROM `tbl_trainings_images` WHERE `training_id`= ? AND `type` = 'completion'");
                                    $check_completion->execute([$training_id]);
                                    $check_count = $check_completion->rowCount();

                                    if ($check_count > 0){
                                        echo '<button class="btn btn-success btn-block btn-lg">Valid</button>
                                                <a href="#" class="text-center" onclick="view_attached_file('.$training_id.')"> View attached file</a>';
                                    }else{
                                        echo '<button class="btn btn-warning btn-block btn-lg" onclick="set_complete('.$training_id.')">Mark as Valid</button>';
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Page Content -->
                <?php
            }else{
                ?>
                <!-- Page Content -->
                <!-- Hero -->
                <div class="bg-gd-sea">
                    <div class="bg-pattern" style="background-image: url('assets/media/photos/construction13.jpg');">
                        <div class="content content-top content-full text-center bg-black-op-75">
                            <div class="py-20">
                                <h1 class="h2 font-w700 text-white mb-10">In-House Training</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Hero -->
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
                        <button type="button" class="btn btn-sm btn-rounded btn-success float-right" id="btn_new_training" <?=$add_disable?> onclick = "set_type(1)">Create New Training</button>
                        In-house Training
                    </h2>
                    <div class="block">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Training List</h3>
                            <div class="block-options">
                                <div class="block-options-item">
                                </div>
                            </div>
                        </div>
                        <div class="block-content">
                            <table id="tbl_training" class="table table-vcenter">
                                <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th style="width: 15%;text-transform: capitalize;">Title</th>
                                    <th class="text-center" style="width: 20%;text-transform: capitalize; !important;">No. of Participants</th>
                                    <th class="text-center" style="width: 20%;text-transform: capitalize; !important;">Conducted by</th>
                                    <th class="text-center" style="text-transform: capitalize; !important;">Date Created</th>
                                    <th class="text-center" style="text-transform: capitalize; !important;">Training Date</th>
                                    <th class="text-center" style="text-transform: capitalize; !important;">Expiration</th>
                                    <th class="text-center" style="text-transform: capitalize; !important;">Status</th>
                                    <th class="text-center" style="width: 10%;text-transform: capitalize; !important;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                //                            ENABLE/DISABLE EDIT AND DELETE BUTTON ACCORDING TO USER ACCESS

                                //                            EDIT ACCESS
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
                                                                tbl_trainings.status,
                                                                tbl_trainings.date_created,
                                                                tbl_trainings.training_date,  
                                                                tbl_trainings.date_expired,
                                                                DATEDIFF(date_expired,training_date) as diff,
                                                                IF(training_date >= date_expired, 'Expired', status) as exp,    
                                                                IF(DATEDIFF(date_expired,training_date) <=5  && DATEDIFF(date_expired,training_date) >= 0  , 'Soon to Expire', 'Valid')  as soon_exp,    
                                                                Count(tbl_training_trainees.trainee_id) as count
                                                                FROM
                                                                tbl_trainings
                                                                INNER JOIN tbl_training_trainees ON tbl_trainings.training_id = tbl_training_trainees.training_id
                                                                WHERE
                                                                tbl_trainings.is_deleted = 0 AND
                                                                tbl_trainings.type = 1
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
                                            <?=$status_td?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-primary js-tooltip-enabled" onclick="view_training(<?=$training_id?>,'inhouse')" data-toggle="tooltip" title="View Details" data-original-title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="<?=(($edit_disable == '')?'training_in_house.php?view_training='.$training['training_id']:'#')?>" class="btn btn-sm btn-secondary js-tooltip-enabled" data-toggle="tooltip" title="Edit Training" data-original-title="Edit">
                                                    <i class="fa fa-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-secondary js-tooltip-enabled" id="delete" onclick="delete_training(<?=$training['training_id']?>)" data-toggle="tooltip" title="Delete Training" data-original-title="Delete Training" <?=$delete_disable?>>
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

    <!-- **************************************** PHP FILE FOR TOOLBOX TALKS MODALS ************************************************** -->
    <!-- Add Training Modal -->
    <div class="modal fade" id="create_training_modal" tabindex="-1" role="dialog" aria-labelledby="create_training_modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Create New Training</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <!--                        <div id="progress_add"></div>-->
                        <form id="add_form" method="post">
                            <input type="hidden" class="form-control form-control-lg" id="add_tbt" name="add_tbt" placeholder="" required>
                            <div class="form-group row">
                                <label class="col-12" for="title">Course Title</label>
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="location">Training Location</label>
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-lg" id="location" name="location" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="contract_no">Contract No.</label>
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-lg" id="contract_no" name="contract_no" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="example-select">Conducted by:</label>
                                <div class="col-md-12">
                                    <select class="form-control" id="select_trainer" name="select_trainer">sti
                                        <?php
                                        $trainer_qry = $conn->query("SELECT
                                                                                tbl_employees.employee_id,
                                                                                tbl_employees.firstname,
                                                                                tbl_employees.middlename,
                                                                                tbl_employees.lastname
                                                                                FROM
                                                                                tbl_employees 
                                                                                WHERE tbl_employees.is_deleted = 0
                                                                                ORDER BY firstname ");
                                        foreach ($trainer_qry as $trainer){
                                            $employee_fullname = ucwords(strtolower($trainer['firstname']." ".$trainer['lastname']));
                                            ?>
                                            <option value="<?=$trainer['employee_id']?>"><?=$employee_fullname?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="date">Date of Training</label>
                                <div class="col-12">
                                    <input type="date" class="form-control form-control-lg" id="date" name="date" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="date_expired">Date Expired</label>
                                <div class="col-12">
                                    <input type="date" class="form-control form-control-lg" id="date_expired" name="date_expired" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="training_hrs">Training Hours</label>
                                <div class="col-12">
                                    <input type="number" class="form-control form-control-lg" id="training_hrs" name="training_hrs" placeholder="" required>
                                </div>
                            </div>
                            <input type="hidden" id="type" name="type" value="">
                            <div class="form-group row">
                                <label class="col-12" for="file">Upload image file</label>
                                <div class="col-12">
                                    <input type="file" name="files[]" multiple >
                                    <!--                                    <input type="file" id="files[]" name="files[]" multiple>-->
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
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

    <!--VIEW TRAINING DETAILS MODAL-->
    <div class="modal fade" id="modal_view_details" tabindex="-1" role="dialog" aria-labelledby="modal_view_details" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-top" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Training Details</h3>
                        <div class="block-options">
                            <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content" id="view_details_content">
                        <div class="block-content" id="view_details_content2">s
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add participants modal -->
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
                    <div class="block-content block-content-full bg-body-light font-size-sm">
                        <button class="btn btn-primary float-right" onclick="reload_page()">Submit</button>
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
                                    <input type="hidden" id="upload_file_training_id" name="upload_file_training_id">
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

    <!-- ATTACHED PHOTOS  MODAL-->
    <div class="modal fade" id="attach_photos_modal" tabindex="-1" role="dialog" aria-labelledby="attach_photos_modal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Attached Photos</h3>
                        <div class="block-options">
                            <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="block">
                            <div class="block-content" >
                                <form id="attach_photo_form" method="post">
                                    <input type="hidden" id="attach_photo_training_id" name="attach_photo_training_id">
                                    <div class="custom-file">
                                        <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                        <!-- When multiple files are selected, we use the word 'Files'. You can easily change it to your own language by adding the following to the input, eg for DE: data-lang-files="Dateien" -->
                                        <input type="file" class="custom-file-input" id="photos" name="photos[]" data-toggle="custom-file-input" multiple>
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
    <!-- ************************************ END OF PHP FILE FOR TOOLBOX TALKS MODALS ************************************************** -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>

    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $("th").attr("style","text-transform: capitalize");
            $('#tbl_training').DataTable();
            $('#training_sidebar').addClass('open');

        });
    </script>
    <!-- SCRIPT FOR FUNCTION IN MANAGING TRAININGS -->
    <script src="custom_js/training.js"></script>

<?php
include 'includes/footer.php';
