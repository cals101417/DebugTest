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
            <!-- Page Content -->
            <!-- Hero -->
            <div class="bg-primary">
                <div class="bg-black-op-75">
                    <div class="content content-top content-full text-center">
                        <div class="py-15">
                            <h1 class="h2 font-w700 text-white mb-10">Create New In-House Training</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->
            <div class="content">
                <div class="mx-50">
                    <a href="training_in_house.php" class="btn btn-sm btn-alt-primary mb-10"><i class="si si-action-undo"></i> Go Back</a>
                    <div class="js-wizard-simple block ">
                        <!-- Wizard Progress Bar -->
                        <div class="progress rounded-0" data-wizard="progress" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 34.3333%;" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <!-- END Wizard Progress Bar -->

                        <!-- Step Tabs -->
                        <ul class="nav nav-tabs nav-tabs-alt nav-fill" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#wizard-progress2-step1" data-toggle="tab">1. Details</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#wizard-progress2-step3" data-toggle="tab">2. Participants</a>
                            </li>
                        </ul>
                        <!-- END Step Tabs -->

                        <!-- Form -->
                        <form action="be_forms_wizard.html" method="post">
                            <!-- Steps Content -->
                            <div class="block-content block-content-full tab-content" style="min-height: 274px;">
                                <!-- Step 1 -->
                                <div class="tab-pane active" id="wizard-progress2-step1" role="tabpanel">
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
                                        <label class="col-4" for="date">Date of Training</label>
                                        <label class="col-4" for="date_expired">Date Expired</label>
                                        <label class="col-4" for="training_hrs">Training Hours</label>
                                        <div class="col-4">
                                            <input type="date" class="form-control form-control-lg" id="date" name="date" placeholder="" required>
                                        </div>
                                        <div class="col-4">
                                            <input type="date" class="form-control form-control-lg" id="date_expired" name="date_expired" placeholder="" required>
                                        </div>
                                        <div class="col-4">
                                            <input type="number" class="form-control form-control-lg" id="training_hrs" name="training_hrs" placeholder="" required>
                                        </div>
                                    </div>
                                    <!--                            <div class="form-group row">-->
                                    <!--                                <label class="col-12" for="status">Status</label>-->
                                    <!--                                <div class="col-12">-->
                                    <!--                                    <input type="text" class="form-control form-control-lg" id="status" name="status" placeholder="" required>-->
                                    <!--                                </div>-->
                                    <!--                            </div>-->
                                    <input type="hidden" id="type" name="type" value="1">
                                    <div class="form-group row">
                                        <label class="col-12" for="file">Upload image file</label>
                                        <div class="col-12">
                                            <input type="file" name="files[]" multiple >
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="remarks">Remarks</label>
                                        <div class="col-12">
                                            <textarea class="form-control form-control-lg" id="remarks" name="remarks" placeholder="" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- END Step 1 -->

                                <!-- Step 3 -->
                                <div class="tab-pane" id="wizard-progress2-step3" role="tabpanel">
                                    <div class="form-group">
                                        <div class="form-material floating">
                                            <input class="form-control" type="text" id="wizard-progress2-location" name="wizard-simple2-location">
                                            <label for="wizard-simple2-location">Location</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-material floating">
                                            <select class="form-control" id="wizard-progress2-skills" name="wizard-progress2-skills" size="1">
                                                <option></option><!-- Empty value for demostrating material select box -->
                                                <option value="1">Photoshop</option>
                                                <option value="2">HTML</option>
                                                <option value="3">CSS</option>
                                                <option value="4">JavaScript</option>
                                            </select>
                                            <label for="wizard-progress2-skills">Skills</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="css-control css-control-primary css-switch" for="wizard-progress2-terms">
                                            <input type="checkbox" class="css-control-input" id="wizard-progress2-terms" name="wizard-progress2-terms">
                                            <span class="css-control-indicator"></span> Agree with the terms
                                        </label>
                                    </div>
                                </div>
                                <!-- END Step 3 -->
                            </div>
                            <!-- END Steps Content -->

                            <!-- Steps Navigation -->
                            <div class="block-content block-content-sm block-content-full bg-body-light">
                                <div class="row">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-alt-secondary disabled" data-wizard="prev">
                                            <i class="fa fa-angle-left mr-5"></i> Previous
                                        </button>
                                    </div>
                                    <div class="col-6 text-right">
                                        <button type="button" class="btn btn-alt-secondary" data-wizard="next">
                                            Next <i class="fa fa-angle-right ml-5"></i>
                                        </button>
                                        <button type="submit" class="btn btn-alt-primary d-none" data-wizard="finish">
                                            <i class="fa fa-check mr-5"></i> Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- END Steps Navigation -->
                        </form>
                        <!-- END Form -->
                    </div>
                </div>
            </div>
            <!-- END Page Content -->
        </main>
        <!-- END Main Container -->
    </div>

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>

    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/bootstrap-wizard/jquery.bootstrap.wizard.js"></script>
    <script src="assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="assets/js/plugins/jquery-validation/additional-methods.js"></script>

    <!-- Page JS Code -->
    <script src="assets/js/pages/be_forms_wizard.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#add_form").submit(function (event) {
                event.preventDefault();
                alert(new FormData(this));
                $.ajax({
                    type: 'POST',
                    url: 'ajax/trainings_ajax.php',
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

            $("#btn_new_training").click(function (){
                $("#create_training_modal").modal('show');
                $.ajax({
                    type: 'POST',
                    url: 'ajax/trainings_ajax.php',
                    data: {
                        progress_add:1
                    },
                    success: function(response) {
                        // if (response === "success"){
                        //     alert('Successfully added employee')
                        //     location.reload();
                        // }else{
                        //     alert('Something went wrong');
                        //     console.log(response)
                        // }
                        $('#progress_add').html(response);
                    },
                    error: function() {
                        console.log("Error adding employee function");
                    }
                });
            });
        });

        function delete_training(id){
            if (confirm("Are you sure you want to remove this training?")){
                $.ajax({
                    type: "POST",
                    url: "ajax/trainings_ajax.php",
                    data: {
                        training_id: id,
                        delete_training: 1,
                    },
                    success: function(data){
                        alert(data);
                        location.reload()
                    }
                });
            }

        }

        function remove_trainee(training_id,employee_id){
            if (confirm("Are you sure you want to remove this trainee from the list?")){
                $.ajax({
                    type: "POST",
                    url: "ajax/trainings_ajax.php",
                    data: {
                        training_id: training_id,
                        employee_id: employee_id,
                        remove_trainee: 1,
                    },
                    success: function(data){
                        alert(data);
                        location.reload()
                    }
                });
            }
        }

        function update_remarks(training_id){
            remarks = $('#fetched_remarks').val();
            $.ajax({
                type: "POST",
                url: "ajax/trainings_ajax.php",
                data: {
                    training_id: training_id,
                    remarks: remarks,
                    update_remarks: 1,
                },
                success: function(data){
                    alert(data);
                    location.reload()
                }
            });
        }

    </script>
<?php
include 'includes/footer.php';