<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
?>
<div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?= $header_layout ?> page-header-inverse <?= $main_content . ' ' . $sidebar_layout ?>">
    <?php
    include 'includes/sidebar.php';
    include 'includes/header.php';

    // Fetch data and display to table                                   
    $documents_qry = $conn->query("SELECT `document_id`,
                                            `src`, 
                                            `review_status`,                                           
                                            `review_remark`, 
                                            `description`,
                                            tbl_documents.user_id,
                                            `user_id_review`, 
                                            `user_id_approval`, 
                                            `date_uploaded`,
                                            tbl_documents.status,
                                            `approval_remark`,
                                            `profile_pic`,
                                             `fname`,
                                            `lname`
                                        FROM `tbl_documents`
                                        INNER JOIN users ON tbl_documents.user_id_review = users.user_id
                                        WHERE tbl_documents.user_id=$user_id 
                                        ORDER BY document_id DESC");
    $documents = $documents_qry->fetchAll();

    ?>

    <!-- Main Container -->
    <main id="main-container">
        <div class="content">
            <h2 class="content-heading">
                <button type="button" class="btn btn-sm btn-rounded btn-primary d-md-none float-right ml-5" data-toggle="class-toggle" data-target=".js-inbox-nav" data-class="d-none d-md-block">Menu</button>
                <button type="button" class="btn btn-sm btn-rounded btn-dark float-right" data-toggle="modal" data-target="#add_new_file">Add New File</button>
                Document Files
            </h2>
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">My Documents</h3>
                    <div class="block-options">
                        <div class="block-options-item">
                            <!--                                <code>.table</code>-->
                        </div>
                    </div>
                </div>
                <div class="block-content">
                    <div class="row">
                        <!-- Row #1 -->
                        <!---------------------------------------Submitted documents--------------------------------------->
                        <div class="col-md-12">
                            <div class="block">
                                <div class="block-content block-content-full d-flex align-items-center justify-content-between bg-dark">
                                    <div class="mr-5">
                                        <p class="font-size-lg font-w600 text-white mb-0">
                                            Submitted Documents
                                        </p>
                                        <p class="font-size-sm text-uppercase font-w600 text-white-op mb-0">
                                            <?= count($documents) . " " ?> Files
                                        </p>
                                    </div>
                                    <div class="p-20">
                                        <i class="fa fa-refresh fa-2x text-white-op"></i>
                                    </div>
                                </div>
                                <div class="block-content block-content-full">
                                    <table class="table table-borderless table-striped table-hover mb-0">
                                        <tr>
                                            <th>ID</th>
                                            <th>File </th>
                                            <th>Status</th>
                                            <th>Review</th>
                                        </tr>

                                        <tbody>
                                            <?php

                                            foreach ($documents as $document) {
                                            ?>

                                                <tr style="cursor: pointer">
                                                    <td class="text-center" style="width: 40px;"><?= $document['document_id'] ?></td>
                                                    <td>
                                                        <span class="fa fa-file-pdf-o"></span> <?= $document['src'] ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $status = '';
                                                        if ($document['review_status'] == 1) {
                                                            if ($document['status'] == 0) {
                                                                $status = "Pending for Approval";
                                                            } else if ($document['status'] == 1) {
                                                                $status = "Approved";
                                                            } else {
                                                                $status = "Declined";
                                                            }
                                                        } else {
                                                            $status = "Pending for Review";
                                                        }
                                                        echo $status;
                                                        ?>
                                                    </td>
                                                    <td class="text-center" style="width: 40px;">
                                                        <strong class="text-success">
                                                            <button type="button" class="btn btn-sm  js-tooltip-enabled" onclick="show_document_details(<?= $document['document_id'] ?>)" data-toggle="tooltip" title="View Details" data-original-title="View Details">
                                                                <i class="fa fa-eye"></i>
                                                            </button>
                                                        </strong>
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

                        <!---------------------------------------FOR APPROVAL SECTION--------------------------------------->
                        <!-- END Row #1 -->
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- END Main Container -->
</div>
<!-- Add NEW File Modal -->
<div class="modal fade" id="add_new_file" tabindex="-1" role="dialog" aria-labelledby="add_new_file" aria-hidden="true">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Add New File</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <form id="add_file_form">
                        <input type="hidden" name="add_document_file" value="1">
                        <div class="form-group row">
                            <label class="col-12" for="title">Title</label>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="description">Description</label>
                            <div class="col-12">
                                <textarea class="form-control form-control-lg" id="description" name="description" placeholder="" required></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-12" for="file">Upload File</label>
                            <div class="col-12">
                                <input type="file" id="file" name="file">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="select_reviewer">Select Review Personnel</label>
                            <div class="col-12">
                                <select class="form-control" id="select_reviewer" name="select_reviewer">
                                    <?php
                                    $user_sql = "SELECT users.user_id, users.fname, users.lname, users.email
                                            FROM users WHERE users.subscriber_id = ? AND users.deleted = 0 AND users.`status` = 1 AND users.user_id != ?";
                                    $users_qry1 = $conn->prepare($user_sql);
                                    $users_qry1->execute([$subscriber_id, $user_id]);
                                    foreach ($users_qry1 as $user1) {
                                        if ($user1['user_id'] == $user_id) {
                                            $user_fullname = 'You';
                                        } else {
                                            $user_fullname = ucwords(strtolower($user1['fname'] . " " . $user1['lname']));
                                        }
                                    ?>
                                        <option value="<?= $user1['user_id'] ?>"><?= $user_fullname ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="select_approval">Select Approval Personnel</label>
                            <div class="col-12">
                                <select class="form-control" id="select_approval" name="select_approval">
                                    <?php
                                    $user2_sql = "SELECT users.user_id, users.fname, users.lname, users.email
                                            FROM users WHERE users.subscriber_id = ? AND users.deleted = 0 AND users.`status` = 1 AND users.user_id != ?";
                                    $users_qry2 = $conn->prepare($user2_sql);
                                    $users_qry2->execute([$subscriber_id, $user_id]);

                                    foreach ($users_qry2 as $user2) {
                                        if ($user2['user_id'] == $user_id) {
                                            $user_fullname = 'You';
                                        } else {
                                            $user_fullname = ucwords(strtolower($user2['fname'] . " " . $user2['lname']));
                                        }
                                    ?>
                                        <option value="<?= $user2['user_id'] ?>"><?= $user_fullname ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                    <i class="fa fa-save mr-5"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Add File Modal -->

<!-- VIEW REVIEW MODAL -->

<div class="modal fade" id="document_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title" id="exampleModalLongTitle">Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- modal details  -->
                <div class="row items-push">
                    <div class="col-xl-5 mt-0 mb-0" id="profile_info">
                        <div class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
                            <div class="block-content bg-dark">
                                <div class="push">
                                    <img class="img-avatar img-avatar-thumb" src="assets\media\photos\profile\<?= $document['profile_pic']; ?>" alt="">
                                </div>
                                <div class="pull-r-l pull-b py-10 bg-black-op-25">
                                    <div class="font-w600 mb-5 text-white"> Submitted by: <?= $document['fname'] . " " . $document['lname'] ?>
                                        <i class=""></i>
                                    </div>
                                </div>
                            </div>
                            <div class="block-content bg-black-op-10">
                                <div class="row items-push text-center ">

                                    <?php

                                    $date_uploaded = $document['date_uploaded'];
                                    $file_description = $document['description'];
                                    $review_status = $document['review_status'];
                                    $status = $document['status'];
                                    $review_remark = $document['review_remark'];
                                    $approval_remark = $document['approval_remark'];
                                    $display_status = '';
                                    $icon = '';
                                    $class = '';


                                    ?>
                                    <div class="col-6">
                                        <div class="mb-5"><i class="<?= $icon ?>"></i></div>
                                        <div class="<?= $class ?>">
                                            <p><?= $document['src'] ?></p>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <?php echo "<a href=\"assets\media\docs\\" . $document['src'] . "\" " . "target=\"_BLANK\">"; ?>
                                        <i class="text-dark fa fa-download fa-2x"></i> </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7 px-0 mb-0">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-3">
                                    </div>


                                    <div class="row mb-3">
                                        <div class="col-sm-4">

                                            <p class="mb-0">Date Uploaded: </p>
                                        </div>
                                        <div class="col-sm-8 text-secondary">
                                            <p value=""> <?= $date_uploaded ?></p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <p class="mb-0">File Description: </p>
                                        </div>
                                        <div class="col-sm-8 text-secondary">
                                            <p type="text" value=""><?= $file_description ?></p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <p class="mb-0">Review Status: </p>
                                        </div>
                                        <div class="col-sm-8 text-secondary">
                                            <p type="text" value="">
                                                <?php if ($review_status == 0) {
                                                    echo "Pending";
                                                } else if ($review_status == 1) {
                                                    echo "Approved";
                                                } else {
                                                    echo "Declined";
                                                }
                                                ?></p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <p class="mb-0">Approval Status: </p>
                                        </div>
                                        <div class="col-sm-8 text-secondary">
                                            <p type="text" value="">
                                                <?php if ($status == 0) {
                                                    echo "Pending";
                                                } else if ($status == 1) {
                                                    echo "Approved";
                                                } else {
                                                    echo "Declined";
                                                }
                                                ?></p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <p class="mb-0">Review Remarks: </p>
                                        </div>
                                        <div class="col-sm-8 text-secondary">
                                            <p type="text" value=""><?php if ($review_remark == '') {
                                                                        echo "NO REMARKS";
                                                                    } else {
                                                                        echo $review_remark;
                                                                    } ?></p>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-4">
                                            <p class="mb-0">Approval Remarks: </p>
                                        </div>
                                        <div class="col-sm-8 text-secondary">
                                            <p><?php if ($approval_remark == '') {
                                                    echo "NO REMARKS";
                                                } else {
                                                    echo $approval_remark;
                                                } ?></p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- modal content -->
            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- END VIEW REVIEW MODAL -->

<script src="assets/js/codebase.core.min.js"></script>
<script src="assets/js/codebase.app.min.js"></script>
<script>
    $(document).ready(function() {
        $("#add_file_form").submit(function(event) {
            event.preventDefault();
            alert(new FormData(this));
            $.ajax({
                type: 'POST',
                url: 'ajax/document_files_ajax.php',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
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

    function show_document_details(remarks) {

        $('#document_modal').modal('show');
        $('#remarks_id').text(remarks);

    }

    function show_modal() {
        alert('adasdsa')
    }

    function modal_review() {
        alert('adasdsa')
    }
</script>
<?php
include 'includes/footer.php';
