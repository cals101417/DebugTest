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
        <link rel="stylesheet" href="assets/js/plugins/select2/css/select2.css">
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
                                        echo '<button class="btn btn-success btn-block btn-lg">valid</button>
                                                <a href="#" class="text-center" onclick="view_attached_file('.$training_id.')"> View attached file</a>';
                                    }else{
                                        echo '<button class="btn btn-warning btn-block btn-lg" onclick="set_complete('.$training_id.')">Mark as valid</button>';
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
                                <h1 class="h2 font-w700 text-white mb-10">Induction Training</h1>
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
                        <button type="button" class="btn btn-sm btn-rounded btn-success float-right" id="btn_new_training" <?=$add_disable?> onclick = "set_type(4)">Create New Training</button>
                        Induction Training
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
                                                                tbl_trainings.type = 4
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

//                                    echo "-From".date_format($training_date, "Y/m/d H:i:s")."-to".date_format($expiration, "Y/m/d H:i:s")."<br>";
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
                                                <button class="btn btn-sm btn-primary js-tooltip-enabled" onclick="view_training(<?=$training_id?>,'induction')" data-toggle="tooltip" title="View Details" data-original-title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="<?=(($edit_disable == '')?'training_client.php?view_training='.$training['training_id']:'#')?>" class="btn btn-sm btn-secondary js-tooltip-enabled" data-toggle="tooltip" title="Edit Training" data-original-title="Edit">
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
<?php
include_once 'training_modals.php';
?>
    <!-- ************************************ END OF PHP FILE FOR TOOLBOX TALKS MODALS ************************************************** -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script src="assets/js/plugins/select2/js/select2.full.min.js"></script>
    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".js-select2").select2({
                theme: "classic"
            });
            $("th").attr("style","text-transform: capitalize");
            $('#tbl_training').DataTable();
            $('#training_sidebar').addClass('open');
        });
    </script>
    <!-- SCRIPT FOR FUNCTION IN MANAGING TRAININGS -->
    <script src="custom_js/training.js"></script>

<?php
include 'includes/footer.php';
