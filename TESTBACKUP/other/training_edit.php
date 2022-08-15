<?php
include 'includes/head.php';
include 'session.php';
?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        if (!isset($_GET['edit'])){
            echo '<script>window.history.back();</script>';
        }
        $training_id = $_GET['edit'];
        $fetch_training = $conn->query("SELECT
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
                                                        tbl_trainings.date_updated
                                                        FROM
                                                        tbl_trainings
                                                        WHERE
                                                        tbl_trainings.training_id = $training_id");
        $training = $fetch_training->fetch();
        $title = ucwords($training['title']);
        $location = ucwords($training['location']);
        $contract_no = ucwords($training['contract_no']);
        $trainer = ucwords($training['trainer']);
        $date_created = ucwords($training['date_created']);
        $date_expired = ucwords($training['date_expired']);
        $training_hrs = ucwords($training['training_hrs']);
        $status = ucwords($training['status']);
        $remarks = ucwords($training['remarks']);

        $count_trainee = $conn->query("SELECT
                                                Count(tbl_training_trainees.trainee_id) as total_trainee
                                                FROM
                                                tbl_training_trainees
                                                WHERE
                                                tbl_training_trainees.training_id = $training_id");
        $count_fetch = $count_trainee->fetch();
        ?>
        <!-- Main Container -->
        <main id="main-container">
            <div class="content">
                <!-- Overview -->
                <h2 class="content-heading">
                    <button class="btn btn-sm btn-alt-primary float-right" onclick="history.back()"><i class="si si-action-undo"></i> Go Back</button>
                    Overview
                </h2>
                <div class="row gutters-tiny">
                    <!-- In Orders -->
                    <div class="col-md-6 col-xl-4">
                        <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full block-sticky-options">
                                <div class="block-options">
                                    <div class="block-options-item">
                                        <i class="fa fa-shopping-basket fa-2x text-info-light"></i>
                                    </div>
                                </div>
                                <div class="py-20 text-center">
                                    <div class="font-size-h2 font-w700 mb-0 text-info js-count-to-enabled" data-toggle="countTo" data-to="39"><?=$count_fetch['total_trainee']?></div>
                                    <div class="font-size-sm font-w600 text-uppercase text-muted">Trainees</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- END In Orders -->

                    <!-- Stock -->
                    <div class="col-md-6 col-xl-4">
                        <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full block-sticky-options">
                                <div class="block-options">
                                    <div class="block-options-item">
                                        <i class="fa fa-check fa-2x text-success-light"></i>
                                    </div>
                                </div>
                                <div class="py-20 text-center">
                                    <div class="font-size-h2 font-w700 mb-0 text-success js-count-to-enabled" data-toggle="countTo" data-to="85">85</div>
                                    <div class="font-size-sm font-w600 text-uppercase text-muted">Stock</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- END Stock -->

                    <!-- Delete Product -->
                    <div class="col-xl-4">
                        <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full block-sticky-options">
                                <div class="block-options">
                                    <div class="block-options-item">
                                        <i class="fa fa-archive fa-2x text-danger-light"></i>
                                    </div>
                                </div>
                                <div class="py-20 text-center">
                                    <div class="font-size-h2 font-w700 mb-0 text-danger">
                                        <i class="fa fa-times"></i>
                                    </div>
                                    <div class="font-size-sm font-w600 text-uppercase text-muted">Delete Training</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- END Delete Product -->
                </div>
                <!-- END Overview -->

                <!-- Update Product -->
                <h2 class="content-heading">
                    Update Training Course
                </h2>
                <div class="row gutters-tiny">
                    <!-- Basic Info -->
                    <div class="col-md-12">
                        <form id="update_training_form" method="post">
                            <div class="block block-rounded block-themed">
                                <div class="block-header bg-gd-primary">
                                    <h3 class="block-title">Basic Info</h3>
                                    <div class="block-options">
<!--                                        <button type="submit" class="btn btn-sm btn-alt-primary">-->
<!--                                            <i class="fa fa-save mr-5"></i>Save-->
<!--                                        </button>-->
                                    </div>
                                </div>
                                <div class="block-content block-content-full">
                                    <div class="form-group row">
                                        <label class="col-12" for="title">Course Title</label>
                                        <div class="col-12">
                                            <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="" value="<?=$title?>" required>
                                            <input type="hidden" id="training_id" name="training_id" value="<?=$training_id?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="location">Training Location</label>
                                        <div class="col-12">
                                            <input type="text" class="form-control form-control-lg" id="location" name="location" placeholder="" value="<?=$location?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="contract_no">Contract No.</label>
                                        <div class="col-12">
                                            <input type="text" class="form-control form-control-lg" id="contract_no" name="contract_no" placeholder="" value="<?=$contract_no?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="example-select">Conducted by:</label>
                                        <div class="col-md-9">
                                            <select class="form-control" id="select_trainer" name="select_trainer">
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
                                            <input type="date" class="form-control form-control-lg" id="date" name="date" placeholder=""  value="<?=date('Y-m-d',strtotime($date_created))?>"required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="date_expired">Date Expired</label>
                                        <div class="col-12">
                                            <input type="date" class="form-control form-control-lg" id="date_expired" name="date_expired" placeholder="" value="<?=date('Y-m-d',strtotime($date_expired))?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="training_hrs">Training Hours</label>
                                        <div class="col-12">
                                            <input type="number" class="form-control form-control-lg" id="training_hrs" name="training_hrs" placeholder="" value="<?=$training_hrs?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="status">Status</label>
                                        <div class="col-12">
                                            <input type="text" class="form-control form-control-lg" id="status" name="status" placeholder="" value="<?=$status?>" required>
                                        </div>
                                    </div>
                                    <input type="hidden" id="type" name="type" value="1">
                                    <input type="hidden" id="edit_training" name="edit_training" value="1">
<!--                                    <div class="form-group row">-->
<!--                                        <label class="col-12" for="file">Upload image file</label>-->
<!--                                        <div class="col-12">-->
<!--                                            <input type="file" name="files[]" multiple >-->
<!--                                        </div>-->
<!--                                    </div>-->
                                    <div class="form-group row">
                                        <label class="col-12" for="remarks">Remarks</label>
                                        <div class="col-12">
                                            <textarea class="form-control form-control-lg" id="remarks" name="remarks" placeholder="" required><?=$remarks?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12 text-center">
                                            <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                                <i class="fa fa-save mr-5"></i> Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- END Basic Info -->
                </div>
                <!-- END Update Product -->
            </div>

        </main>
        <!-- END Main Container -->
    </div>

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#training_sidebar').addClass('open');
            $("#update_training_form").submit(function (event) {
                event.preventDefault();
                // alert(new FormData(this));
                $.ajax({
                    type: 'POST',
                    url: 'ajax/training_edit_ajax.php',
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
            });

            $("#add_trainee").submit(function (event) {
                event.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'ajax/trainings_ajax.php',
                    data: $(this).serialize(),
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
            });
        });

    </script>
<?php
include 'includes/footer.php';