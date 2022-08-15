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

            <!-- Hero -->
            <div class="bg-gd-earth">
                <div class="bg-pattern" style="background-image: url('assets/media/photos/construction14.png');">
                    <div class="content content-top content-full text-center bg-black-op-75">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10">Manage Departments</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->
            <!-- Page Content -->
            <div class="content">
                <?php
                if (isset($_POST['department'])){
                    $department_name = $_POST['department'];

                    try {
//            Add department to db
                        $insert = $conn->prepare("INSERT INTO `tbl_department`(`department`, `user_id`) VALUES (?,?)");
                        $insert->execute([$department_name,$session_emp_id]);
                        echo '
                            <div class="alert alert-success alert-dismissable" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h3 class="alert-heading font-size-h4 font-w400">Success</h3>
                                <p class="mb-0">New department successfully added</p>
                            </div>
                            ';
                    }catch (Exception $e){
                        echo '
                            <div class="alert alert-danger alert-dismissable" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h3 class="alert-heading font-size-h4 font-w400">Error</h3>
                                <p class="mb-0">Something went wrong in adding new department</p>
                            </div>
                            ';
                    }

                }
                ?>
                <h2 class="content-heading">
                    <button type="button" class="btn btn-sm btn-rounded btn-primary d-md-none float-right ml-5" data-toggle="class-toggle" data-target=".js-inbox-nav" data-class="d-none d-md-block">Menu</button>
                    <?php
                    //                    ADD ACCESS EMPLOYEE ENABLE DISABLE BUTTON
                    $add_comp_access  = $conn->query("SELECT * FROM user_access WHERE access_type_id = 23 AND user_id = $session_emp_id ");
                    $add_comp = $add_comp_access->fetch();
                    $add_disable = '';
                    (($add_comp['status'] == 0 )?$add_disable =  '': $add_disable = "disabled");
                    ?>
                    <button type="button" class="btn btn-sm btn-rounded btn-success float-right" data-toggle="modal" data-target="#add_department_modal" <?=$add_disable?>>Add New department</button>
                    Departments List
                </h2>
                <div class="block">
                    <div class="block-content">
                        <table class="table table-vcenter">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th class="text-center" style="width: 15%;text-transform: capitalize; !important;">department</th>
                                <th class="text-center" style="width: 15%;text-transform: capitalize; !important;">Date Created</th>
                                <th class="text-center" style="width: 10%;text-transform: capitalize; !important;">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $departments = $conn->query("SELECT
                                                                tbl_department.department_id,
                                                                tbl_department.department,
                                                                tbl_department.date_created
                                                                FROM
                                                                tbl_department
                                                                WHERE
                                                                tbl_department.is_deleted = 0 AND sub_id = $subscriber_id
                                                            ");
                            $count = 1;
                            foreach ($departments as $department) {
                                $department_id = $department['department_id'];
                                $department_name = $department['department'];
                                $date_created = $department['date_created'];
                                ?>
                                <tr>
                                    <th class="text-center" scope="row"><?=$count++?></th>
                                    <td class="text-center"><?=ucwords(strtolower($department_name))?></td>
                                    <td class="text-center"><?=date('F d, Y', strtotime($date_created))?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-secondary js-tooltip-enabled" id="delete" onclick="edit_department(<?=$department_id?>)" data-toggle="tooltip" title="Edit department" data-original-title="Edit department">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary js-tooltip-enabled" id="delete" onclick="remove_department(<?=$department_id?>)" data-toggle="tooltip" title="Delete department" data-original-title="Delete department">
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

        </main>
        <!-- END Main Container -->
    </div>

    <!-- Add department Modal -->
    <div class="modal fade" id="add_department_modal" tabindex="-1" role="dialog" aria-labelledby="add_department_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0" id="print_div">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title"><span id="span_employee_name">Department</span></h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="add_form" method="post" action="department.php">
                            <div class="form-group row">
                                <label class="col-12" for="title">Department name</label>
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-lg" id="department" name="department" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Add department Modal -->

    <!-- Edit department Modal -->
    <div class="modal fade" id="edit_department_modal" tabindex="-1" role="dialog" aria-labelledby="add_department_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0" id="print_div">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title"><span id="span_employee_name">Department</span></h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="edit_form" method="post"">
                        <input type="hidden" id="department_id" name="department_id">
                        <input type="hidden" id="update_department" name="update_department" value="1">
                        <div class="form-group row">
                            <label class="col-12" for="title">department name</label>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-lg" id="edit_department" name="department" placeholder="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                    Submit
                                </button>
                            </div>
                        </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Edit department Modal -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#edit_form").submit(function (event) {
                event.preventDefault();
                // alert('test')
                $.ajax({
                    type: 'POST',
                    url: 'ajax/department_ajax.php',
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

        // load department details once edit btn is clicked
        function edit_department(department_id){
            $('#department_id').val(department_id);
            $('#edit_department_modal').modal('show');
            $('#training_sidebar').addClass('open');

            $.ajax({
                type: "POST",
                url: "ajax/department_ajax.php",
                data: {
                    department_id: department_id,
                    edit_department: 1,
                },
                success: function(data){
                    $('#edit_department').val(data);
                    // location.reload()
                }
            });
        }
    </script>
<?php
include 'includes/footer.php';