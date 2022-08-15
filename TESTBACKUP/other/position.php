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
                <div class="bg-pattern" style="background-image: url('assets/media/photos/construction13.jpg');;">
                    <div class="content content-top content-full text-center">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10">Manage Positions</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->
            <!-- Page Content -->
            <div class="content">
                <?php
                if (isset($_POST['position'])){

                    $position_name =  strtoupper($_POST['position']);
                    try {
//            Add position to db
                        $insert = $conn->prepare("INSERT INTO `tbl_position`(`position`, `user_id`, `is_deleted`) VALUES (?,?,?)");
                        $insert->execute([$position_name,$user_id,0]);
                        echo '
                            <div class="alert alert-success alert-dismissable" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h3 class="alert-heading font-size-h4 font-w400">Success</h3>
                                <p class="mb-0">New position successfully added</p>
                            </div>
                            ';
                    }catch (Exception $e){
                        echo '
                            <div class="alert alert-danger alert-dismissable" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                                <h3 class="alert-heading font-size-h4 font-w400">Error</h3>
                                <p class="mb-0">Something went wrong in adding new position</p>
                            </div>
                            ';
                    }

                }
                ?>
                <h2 class="content-heading">
                    <button type="button" class="btn btn-sm btn-rounded btn-primary d-md-none float-right ml-5" data-toggle="class-toggle" data-target=".js-inbox-nav" data-class="d-none d-md-block">Menu</button>
                    <?php
                    //                    ADD ACCESS EMPLOYEE ENABLE DISABLE BUTTON
                    $add_disable = '';
                    $access = $conn->query("SELECT `status` FROM `user_access` WHERE `user_id` = $user_id AND access_type_id = 1");
                    if ($access->rowCount() > 0){
                        $add_access = $access->fetch();
                        if ($add_access['status'] == 1){
                            $add_disable = 'disabled';
                        }
                    }else{
                        $add_disable = 'disabled';
                    }
                    ?>
                    <button type="button" class="btn btn-sm btn-rounded btn-success float-right" data-toggle="modal" data-target="#add_position_modal" <?=$add_disable?>>Add New Position</button>
                    Positions
                </h2>
                <div class="block">
                    <div class="block-content">
                        <table class="table table-vcenter">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th class="text-center" style="width: 15%;text-transform: capitalize; !important;">Position</th>
                                <th class="text-center" style="width: 15%;text-transform: capitalize; !important;">Date Created</th>
                                <th class="text-center" style="width: 10%;text-transform: capitalize; !important;">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $positions = $conn->query("SELECT
                                                                tbl_position.position_id,
                                                                tbl_position.position,
                                                                tbl_position.date_created as position_date_created
                                                                FROM
                                                                tbl_position
                                                                INNER JOIN users ON users.user_id = tbl_position.user_id
                                                                WHERE
                                                                tbl_position.is_deleted = 0
                                                                ORDER BY
                                                                tbl_position.position ASC
                                                            ");
                            $positions_fetch = $positions->fetchAll();
                            $count = 1;
                            foreach ($positions_fetch as $position) {
                                $position_id = $position['position_id'];
                                $position_name = $position['position'];
                                $date_created = $position['position_date_created'];
                                ?>
                                <tr>
                                    <th class="text-center" scope="row"><?=$count++?></th>
                                    <td class="text-center"><?=ucwords(strtolower($position_name))?></td>
                                    <td class="text-center"><?=$date_created?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-secondary js-tooltip-enabled" id="delete" onclick="edit_position(<?=$position_id?>)" data-toggle="tooltip" title="Edit Position" data-original-title="Edit Position">
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-secondary js-tooltip-enabled" id="delete" onclick="remove_position(<?=$position_id?>)" data-toggle="tooltip" title="Delete Position" data-original-title="Delete Position">
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

    <!-- Add Position Modal -->
    <div class="modal fade" id="add_position_modal" tabindex="-1" role="dialog" aria-labelledby="add_position_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0" id="print_div">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title"><span id="span_employee_name">Employee Details</span></h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="add_form" method="post" action="position.php">
                            <div class="form-group row">
                                <label class="col-12" for="title">Position name</label>
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-lg" id="position" name="position" placeholder="" required>
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
    <!-- END Add Position Modal -->

    <!-- Edit Position Modal -->
    <div class="modal fade" id="edit_position_modal" tabindex="-1" role="dialog" aria-labelledby="add_position_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0" id="print_div">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title"><span id="span_employee_name">Employee Details</span></h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="edit_form" method="post"">
                            <input type="hidden" id="position_id" name="position_id">
                            <input type="hidden" id="update_position" name="update_position" value="1">
                            <div class="form-group row">
                                <label class="col-12" for="title">Position name</label>
                                <div class="col-12">
                                    <input type="text" class="form-control form-control-lg" id="edit_position" name="position" placeholder="" required>
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
    <!-- END Edit Position Modal -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#employee_sidebar').addClass('open');

            $("#edit_form").submit(function (event) {
                event.preventDefault();
                // alert('test')
                $.ajax({
                    type: 'POST',
                    url: 'ajax/position_ajax.php',
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
        // load position details once edit btn is clicked
        function edit_position(position_id){
            $('#position_id').val(position_id);
            $('#edit_position_modal').modal('show');
            $.ajax({
                type: "POST",
                url: "ajax/position_ajax.php",
                data: {
                    position_id: position_id,
                    edit_position: 1,
                },
                success: function(data){
                    $('#edit_position').val(data);
                    // location.reload()
                }
            });
        }

        function remove_position(position_id){
            if (confirm("Are you sure you want to remove this position?")){
                $.ajax({
                    type: "POST",
                    url: "ajax/position_ajax.php",
                    data: {
                        position_id: position_id,
                        remove_position: 1,
                    },
                    success: function(data){
                        if (data.includes('success')){
                            alert("Successfully removed");
                            location.reload();
                        }else{
                            alert("Something went wrong")
                        }
                        // location.reload()
                    }
                });
            }

        }
    </script>
<?php
include 'includes/footer.php';