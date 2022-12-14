<?php
include 'session2.php';

?>


<?php if (!isset($_SESSION['user_id'])) : ?>

    <?php
    require_once 'conn.php';
    include 'includes/head.php';
    if (isset($_SESSION['user_id'])) {
        header("location: .php");
    }
    if ($_POST) {
        $username = $_POST['login-user'];
        $password = $_POST['login-password'];
        $date = date('Y-m-d H:i:s');
        try {
            $stmt = $conn->prepare("SELECT * FROM `users` WHERE username = ?");
            $stmt->execute([$username]);
            $count = $stmt->rowCount();
            if ($count > 0) {
                $users = $stmt->fetch();
                if (password_verify($password, $users['password'])) {
                    $_SESSION['login-email'] = $users['email'];
                    $_SESSION['user_id'] = $users['user_id'];
                    $_SESSION['subscriber_id'] = $users['subscriber_id'];
                    $_SESSION['session_emp_id'] = $users['emp_id'];
                    //            echo $_SESSION['login-email'];
                    echo '<script>document.location = "";</script>';
                } else {
                    echo '<script>alert("email and password does not match!");</script>';
                }
            } else {
                echo '<script>alert("User does not exist in the database")</script>';
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    ?>
    <main id="main-container">
        <!-- Page Content -->
        <div class="bg-body-dark bg-pattern" style="background-image: url('assets/media/various/bg-pattern-inverse.png');">
            <div class="row mx-0 justify-content-center">
                <div class="hero-static col-lg-6 col-xl-4">
                    <div class="content content-full overflow-hidden">
                        <div class="py-30 text-center">
                            <a class="font-w700" href="index.php">
                                <img class="img pd-l-30" src="assets/media/photos/safety-surfers-management-logo.png" style="height: 60px; !important">
                            </a>
                            <h1 class="h4 font-w700 mt-30 mb-10">Welcome to Your Dashboard</h1>
                            <h2 class="h5 font-w400 text-muted mb-0">It???s a great day today!</h2>
                        </div>

                        <!-- Sign In Form -->
                        <!-- jQuery Validation functionality is initialized with .js-validation-signin class in js/pages/op_auth_signin.min.js which was auto compiled from _es6/pages/op_auth_signin.js -->
                        <!-- For more examples you can check out https://github.com/jzaefferer/jquery-validation -->
                        <form id="login_form" action="" method="post">
                            <div class="block block-themed block-rounded block-shadow">
                                <div class="block-header bg-gd-dusk">
                                    <h3 class="block-title">Please Sign In</h3>
                                    <div class="block-options">
                                        <button type="button" class="btn-block-option">
                                            <i class="si si-wrench"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="block-content">
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="login-user">Username</label>
                                            <input type="text" class="form-control" id="login-user" name="login-user" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="login-password">Password</label>
                                            <input type="password" class="form-control" id="login-password" name="login-password" required>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <div class="col-sm-6 d-sm-flex align-items-center push">
                                            <div class="custom-control custom-checkbox mr-auto ml-0 mb-0">
                                                <input type="checkbox" class="custom-control-input" id="login-remember-me" name="login-remember-me">
                                                <label class="custom-control-label" for="login-remember-me">Remember
                                                    Me</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 text-sm-right push">
                                            <button type="submit" class="btn btn-alt-primary">
                                                <i class="si si-login mr-10"></i> Sign In
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="block-content bg-body-light">
                                    <div class="form-group text-center">
                                        <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="register.php">
                                            <i class="fa fa-plus mr-5"></i> Create Account
                                        </a>
                                        <a class="link-effect text-muted mr-10 mb-5 d-inline-block" href="op_auth_reminder3.html">
                                            <i class="fa fa-warning mr-5"></i> Forgot Password
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- END Sign In Form -->
                    </div>
                </div>
            </div>
        </div>
        <!-- END Page Content -->

    </main>

<?php endif ?>
<?php if (isset($_SESSION['user_id'])) : ?>

    <?php
    include 'includes/head.php';
    include 'includes/page_layout.php';
    include 'folders_modal.php'
    ?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?= $header_layout ?> page-header-inverse <?= $main_content . ' ' . $sidebar_layout ?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        ?>
        <!--PLAY DOCUMENTS OF THE OPENEED FOLDER -->

        <!-- DISPLAY FOLDERS FROM DATABASE -->
        <!-- Main Container -->
        <?php if (isset($_GET['view'])) : ?>
            <?php

            // USER ROLE ACCESS

            $submit_file_disable = '';
            try {


                $add_stock_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 21 AND `status` = 0");
                if ($add_stock_access->rowCount() > 0) {
                    $add_stock_access = $add_stock_access->fetch();
                    $add_item_status = $add_stock_access['status'];
                    if ($add_stock_access == 1) {
                        $add_stock_disable = 'disabled';
                    }
                } else {
                    $add_stock_disable = 'disabled';
                }


                $folder_id = $_GET['view'];

                $get_submitted_documents = $conn->query("SELECT `document_id`,
                                                `folder_id`,
                                                `title`,                                             
                                                `description`,
                                                tbl_documents.user_id,
                                                `approval_id`, 
                                                `date_uploaded`,
                                                tbl_documents.status,
                                                tbl_employees.employee_id,
                                                `project_code`, `originator`, `originator2`,  
                                                `rev`,`discipline`,`firstname`,`lastname`,`document_type`,`document_zone`,`document_level`,`sequence_no`,`document_level`,
                                                (SELECT document_id
                                                FROM  tbl_document_comments_replies 
                                                WHERE tbl_document_comments_replies.document_id = tbl_documents.document_id LIMIT 1) AS comment_exist
                                            FROM `tbl_documents`
                                            INNER JOIN tbl_employees ON tbl_documents.user_id = tbl_employees.employee_id                                         
                                            WHERE tbl_documents.user_id=$session_emp_id AND
                                            folder_id = $folder_id AND tbl_documents.is_deleted = 0
                                            ORDER BY `document_id` DESC");

                $documents = $get_submitted_documents->fetchAll();
                // get for review
                $get_for_review_documents = $conn->query("SELECT 
                                             tbl_documents.document_id,
                                            tbl_documents.folder_id,
                                            `title`,
                                            `description`,                                           
                                            tbl_documents.user_id,                                    
                                            `approval_id`, 
                                            `date_uploaded`,
                                            `review_status`,
                                            tbl_documents.status,
                                            `firstname`,
                                            `lastname`,
                                            `project_code`, 
                                            `rev`,`originator`, `originator2`,  
                                            `discipline`,`lastname`,`firstname`,`document_type`,`document_zone`,`document_level`,`sequence_no`,`document_level`,
                                              (SELECT tbl_document_comments_replies.document_id
                                                FROM  tbl_document_comments_replies 
                                                WHERE tbl_document_comments_replies.document_id = tbl_documents.document_id LIMIT 1) AS comment_exist
                                           FROM tbl_documents 
                                           INNER JOIN tbl_document_reviewer ON tbl_documents.document_id = tbl_document_reviewer.document_id                                                                          
                                           INNER JOIN tbl_employees ON tbl_documents.user_id = tbl_employees.employee_id AND 
                                           tbl_documents.folder_id = $folder_id
                                           WHERE tbl_document_reviewer.reviewer_id = $session_emp_id AND tbl_documents.is_deleted = 0
                                           ORDER BY `document_id` DESC");
                $documents2 = $get_for_review_documents->fetchAll();

                $session_emp_id = $_SESSION['session_emp_id'];
                $get_for_approval_documents = $conn->query("SELECT 
                                             tbl_documents.document_id, 
                                             tbl_documents.folder_id,
                                            `title`,
                                            `description`,                                           
                                            tbl_documents.user_id,                                    
                                            `approval_id`, 
                                            `date_uploaded`,
                                            tbl_documents.status,
                                            tbl_documents.status,
                                            `rev`,
                                            `firstname`,
                                            `lastname`,
                                            `project_code`, `originator`, `originator2`,                             
                                            `discipline`,`lastname`,`firstname`,`document_type`,`document_zone`,`document_level`,`sequence_no`,`document_level`,
                                            tbl_documents.status,                                            
                                                (SELECT  document_id
                                                FROM  tbl_document_comments_replies                                            
                                                WHERE tbl_document_comments_replies.document_id = tbl_documents.document_id 
                                               LIMIT 1 ) AS comment_exist
                                           FROM tbl_documents                                                                                                                
                                           INNER JOIN tbl_employees ON tbl_documents.user_id = tbl_employees.employee_id AND 
                                           tbl_documents.folder_id = $folder_id
                                           WHERE tbl_documents.approval_id = $session_emp_id AND tbl_documents.is_deleted = 0");
                $documents3 = $get_for_approval_documents->fetchAll();
                //GET ALL DOCUMENTS IN THE SELECTED FOLDER
                $folder_docs = $conn->query("SELECT tbl_documents.document_id,
                                                         `project_code`,
                                                         `originator`,`originator2`,  
                                                         `discipline`,
                                                         `document_type`,
                                                         `document_zone`,
                                                         `document_level`,
                                                         `sequence_no`,
                                                         `document_level`,  
                                                          `rev`,COUNT(tbl_files.file_id) as number_of_attached_files,      
                                                          (SELECT COUNT(response_file_id) from tbl_document_response_files
                                                           WHERE tbl_document_response_files.document_id = tbl_documents.document_id AND tbl_documents.is_deleted = 0  AND response_type = 1
                                                           GROUP BY  tbl_document_response_files.document_id) as number_of_revised_files
                                            FROM `tbl_documents`
                                            INNER JOIN `tbl_files` ON tbl_documents.document_id  = tbl_files.document_id                                  
                                            WHERE tbl_documents.folder_id = $folder_id AND tbl_documents.is_deleted =0
                                            GROUP BY tbl_documents.document_id                                                                         
                                            ORDER BY tbl_documents.document_id DESC");

                $documents5 = $folder_docs->fetchAll(); // document controkl
                //            COUNT DOCUMENTS ROWS FROM TBL_DOCUMENTS
                $count_documents_qry = $conn->query("SELECT *  FROM tbl_documents");
                $count_documents = $count_documents_qry->fetchAll();
                $number_of_documents =  count($documents) + 1;
                $number_of_digits = strlen((string) $number_of_documents);
                $number_of_zeroes =  5 - $number_of_digits;
                $sequence_no_zeros = '';

                for ($i = 0; $i <= $number_of_zeroes; $i++) {
                    $sequence_no_zeros = $sequence_no_zeros . "0";
                }
                $sequence =  $sequence_no_zeros . $number_of_documents;

                //            COUNT DOCUMENTS ROWS FROM TBL_DOCUMENTS

            } catch (Exception $e) {
                echo $e;
            }

            function stitch_file_name($document1, $document2, $document3, $document4, $document5, $document6, $document7)
            {
                try {
                    $file_array = array();
                    //            echo $document1;
                    if (strtolower($document1) != 'n/a') {
                        array_push($file_array, $document1);
                    }
                    if (strtolower($document2) != 'n/a') {
                        array_push($file_array, $document2);
                    }
                    if (strtolower($document3) != 'n/a') {
                        array_push($file_array, $document3);
                    }

                    if (strtolower($document4) != 'n/a') {
                        array_push($file_array, $document4);
                    }
                    if (strtolower($document5) != 'n/a') {
                        array_push($file_array, $document5);
                    }
                    if (strtolower($document6) != 'n/a') {
                        array_push($file_array, $document6);
                    }
                    if (strtolower($document7) != 'n/a') {
                        array_push($file_array, $document7);
                    }
                    $file_new = "";
                    for ($i = 0; $i < count($file_array); $i++) {
                        if ($file_array[$i] != '') {
                            if ($i == 0) {
                                $file_new  = $file_array[$i];
                            } else {
                                $file_new  = $file_new . "-" . $file_array[$i];
                            }
                        }
                    }
                    return strval($file_new);
                } catch (Exception $e) {
                    echo $e;
                }
            }
            function display_status_value($status)
            {
                $result = '';
                switch ($status) {

                    case 'A':
                        $result = "APPROVED W/O COMMENTS";
                        break;
                    case 'B':
                        $result = "APPROVED WITH COMMENT/S";
                        break;
                    case 'C':
                        $result = "FAIL/NOT APPROVED";
                        break;
                    case 'D':
                        $result = "APPROVED WITHOUT COMMENTS";
                        break;
                    case 'E':
                        $result = "NO OBJECTION WITH COMMENTS";
                        break;
                    case 'F':
                        $result = "RESPONDED/REVIEWED/ACTIONED";
                        break;
                    default:
                        $result = "PENDING ACTION";
                        break;
                }
                return $result;
            }

            function select_badge($status)
            {
                $result = '';
                switch ($status) {

                    case 'A':
                        $result = "badge badge-success";
                        break;
                    case 'B':
                        $result = "badge badge-info";
                        break;
                    case 'C':
                        $result = "badge badge-danger";
                        break;
                    case 'D':
                        $result = "badge badge-secondary";
                        break;
                    case 'E':
                        $result = "badge badge-warning";
                        break;
                    case 'F':
                        $result = "bg-warning text-dark";
                        break;
                    default:
                        $result = "badge badge-warning";
                        break;
                }
                echo $result;
            }

            $folder_id =  $_GET['view'];
            $current_folder = $_GET['folder'];
            $folder = explode("-", $current_folder);

            $folder_number = $folder[0];
            $folder_name = "";
            ((count($folder) < 2) ? $folder_name = "" : $folder_name = $folder[1]);
            ?>
            <main id="main-container">
                <!-- Hero -->
                <div class="bg-dark">
                    <div class="bg-image bg-image-middle" style="background-image: url('assets/media/photos/construction4.jpeg');">
                        <div class="content content-top text-center ">
                            <div class="py-50">
                                <h1 class="font-w700 text-white mb-10"><?php echo ucfirst($folder_name) ?></h1>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page Content -->
                <div class="content">
                    <!-- Single Item -->
                    <h2 class="content-heading">
                        <?php echo $folder_number; ?>
                        <button class="btn btn-sm btn-alt-primary btn-rounded float-right" onclick="history.back()"><i class="si si-action-undo"></i> Go Back
                        </button>
                    </h2>
                    <div class="block shadow p-3 mb-5 bg-white border">
                        <div class="block-content">
                            <div id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="block block-bordered block-rounded mb-2">
                                    <div class="block-header bg-gd-primary" role="tab" id="accordion_h1">
                                        <a class="font-w600 text-white" data-toggle="collapse" data-parent="#accordion" href="#accordion_q1" aria-expanded="true" aria-controls="accordion_q1">1.1
                                            Submitted Documents
                                        </a>
                                    </div>
                                    <div id="accordion_q1" class="collapse show" role="tabpanel" aria-labelledby="accordion_h1" data-parent="#accordion">
                                        <div class="block-content pb-20">
                                            <button type="button" class="btn btn-sm btn-success float-right" data-toggle="modal" data-target="#add_new_file">Add New File
                                            </button>
                                            <table class="table table-sm table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th class="text-capitalize">Document</th>
                                                        <th class="text-capitalize">Description</th>
                                                        <th class="text-capitalize text-center">Title</th>
                                                        <th class="text-capitalize text-center">Status</th>
                                                        <th class="text-capitalize">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php
                                                    try {
                                                        $c = 1; ?>
                                                        <?php foreach ($documents as $document) : ?>
                                                            <?php $full_name = $document['firstname'] . ' ' . $document['lastname']; ?>
                                                            <tr>
                                                                <?php $subdoc_status = $document['status']; ?>
                                                                <td><?= $c ?></td>
                                                                <td>
                                                                    <?php
                                                                    $d1 =  $document['project_code'];
                                                                    $d2 =  $document['originator2'];
                                                                    $d3 =  $document['discipline'];
                                                                    $d4 =  $document['document_type'];
                                                                    $d5 =  $document['document_zone'];
                                                                    $d6 =  $document['document_level'];
                                                                    $d7 =  $document['sequence_no'];
                                                                    $file_new =  stitch_file_name($d1, $d2, $d3, $d4, $d5, $d6, $d7);
                                                                    echo $file_new;
                                                                    ?>
                                                                </td>

                                                                <td><?= $document['description'] ?></td>
                                                                <td><?= $document['title'] ?></td>
                                                                <td class="approval_status<?php echo $subdoc_status ?>">
                                                                    <span class="
                                                                    <?php select_badge($subdoc_status); ?>">
                                                                        <?php
                                                                        $dis_status =  display_status_value($subdoc_status);
                                                                        if (!empty($document['comment_exist'] && $dis_status == "PENDING ACTION")) {
                                                                            echo "WITH COMMENTS";
                                                                        } else {
                                                                            echo $dis_status;
                                                                        }
                                                                        $c++; ?>
                                                                    </span>
                                                                </td>
                                                                <td class="text-center" style="width: 40px;">
                                                                    <div class="btn-group">
                                                                        <button type="button" type="submit" class="btn btn-sm btn-primary js-tooltip-enabled" onclick="load_documents_for_review(<?= $document['document_id'] ?>, <?= $document['folder_id'] ?>,0,'<?= $file_new ?>',1)" data-toggle="tooltip" title="View Details" data-original-title="View Details">
                                                                            <i class="fa fa-search"></i>
                                                                        </button>
                                                                        <button type="button" type="submit" class="btn btn-sm btn-success js-tooltip-enabled" onclick="load_uploaded_files(<?= $document['document_id'] ?>, <?= $document['folder_id'] ?>)" data-toggle="tooltip" title="View Files" data-original-title="View Files">
                                                                            <i class="fa fa-table"></i>
                                                                        </button>
                                                                        <button type="button" type="submit" class="btn btn-sm btn-danger js-tooltip-enabled" onclick="delete_submitted_document_modal(<?= $document['document_id'] ?>)" data-toggle="tooltip" title="Delete Documents" data-original-title="Delete Documents">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>

                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                        <!--END HERE-->
                                                    <?php

                                                    } catch (Exception $e) {
                                                        echo $e;
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="block block-bordered block-rounded mb-2">
                                    <div class="block-header bg-gd-primary" role="tab" id="accordion_h2">
                                        <a class="font-w600 text-white" data-toggle="collapse" data-parent="#accordion" href="#accordion_q2" aria-expanded="true" aria-controls="accordion_q2">1.2 For Review Documents</a>
                                    </div>
                                    <div id="accordion_q2" class="collapse" role="tabpanel" aria-labelledby="accordion_h2" data-parent="#accordion">
                                        <div class="block-content">
                                            <div class="block">
                                                <div class="block-header block-header-default">
                                                    <!-- <h3 class="block-title">Dynamic Table <small>Full pagination</small></h3> -->
                                                </div>
                                                <div class="block-content block-content-full">
                                                    <!-- DataTables functionality is initialized with .js-dataTable-full-pagination class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                                                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>File</th>
                                                                <th>Revision No.</th>
                                                                <th>Description</th>
                                                                <th>Title</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $c1 = 1;

                                                            try {
                                                            ?>
                                                                <?php foreach ($documents2 as $document2) : ?>
                                                                    <tr>

                                                                        <td><?= $c1 ?></td>
                                                                        <td>
                                                                            <?php
                                                                            $d1 =  $document2['project_code'];
                                                                            $d2 =  $document2['originator2'];
                                                                            $d3 =  $document2['discipline'];
                                                                            $d4 =  $document2['document_type'];
                                                                            $d5 =  $document2['document_zone'];
                                                                            $d6 =  $document2['document_level'];
                                                                            $d7 =  $document2['sequence_no'];

                                                                            $file_new =  stitch_file_name($d1, $d2, $d3, $d4, $d5, $d6, $d7);
                                                                            echo $file_new;
                                                                            ?>
                                                                        </td>

                                                                        <td><?= $document2['rev'] ?></td>
                                                                        <td><?= $document2['description'] ?></td>
                                                                        <td><?= $document2['title'] ?> </td>
                                                                        <td class="review_status<?= $document2['review_status'] ?>">
                                                                            <span class="<?php select_badge($status); ?>">
                                                                                <?php
                                                                                $dis_status =  display_status_value($document2['review_status']);
                                                                                if (!empty($document2['comment_exist'] && $dis_status == "PENDING ACTION")) {
                                                                                    echo "WITH COMMENTS";
                                                                                } else {
                                                                                    echo $dis_status;
                                                                                }
                                                                                $c++; ?>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn btn-sm btn-primary mr-5 mb-5" onclick="load_documents_for_review(<?= $document2['document_id'] ?>, <?= $document2['folder_id'] ?>,0,'<?= $file_new ?>',2)" data-toggle="tooltip" title="View Details" data-original-title="View Details">
                                                                                    <i class="fa fa-search"></i>
                                                                                </button>
                                                                                <a data-toggle="tooltip" title="Review Action" data-original-title="Review Action" href="document_review_form.php?review=<?= $document2['document_id'] ?>&folder_id=<?= $document2['folder_id'] ?>" class="btn btn-sm btn-success mr-5 mb-5">
                                                                                    <i class="fa fa-pencil"></i>
                                                                                </a>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <?php $c1++; ?>
                                                                <?php endforeach ?>
                                                            <?php
                                                            } catch (Exception $e) {
                                                            } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="block block-bordered block-rounded mb-2">
                                    <div class="block-header bg-gd-primary" role="tab" id="accordion_h3">
                                        <a class="font-w600 text-white" data-toggle="collapse" data-parent="#accordion" href="#accordion_q3" aria-expanded="true" aria-controls="accordion_q3">1.3 For Approval Documents</a>
                                    </div>
                                    <div id="accordion_q3" class="collapse" role="tabpanel" aria-labelledby="accordion_h3" data-parent="#accordion">
                                        <div class="block-content">
                                            <div class="block">
                                                <div class="block-header block-header-default">
                                                </div>
                                                <div class="block-content block-content-full">
                                                    <!-- DataTables functionality is initialized with .js-dataTable-full-pagination class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                                                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>File</th>
                                                                <th>Revision No.</th>
                                                                <th>Title</th>
                                                                <th>Approval Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $c2 = 1; ?>
                                                            <?php foreach ($documents3 as $document3) : ?>
                                                                <tr style="cursor: pointer">
                                                                    <td> <?= $c2 ?></td>
                                                                    <td>
                                                                        <?php
                                                                        $subdoc_status = $document3['status'];
                                                                        $d1 =  $document3['project_code'];
                                                                        $d2 =  $document3['originator2'];
                                                                        $d3 =  $document3['discipline'];
                                                                        $d4 =  $document3['document_type'];
                                                                        $d5 =  $document3['document_zone'];
                                                                        $d6 =  $document3['document_level'];
                                                                        $d7 =  $document3['sequence_no'];
                                                                        $file_new =  stitch_file_name($d1, $d2, $d3, $d4, $d5, $d6, $d7);
                                                                        echo $file_new;
                                                                        ?>
                                                                    </td>
                                                                    <td> <?= $document3['rev'] ?></td>
                                                                    <td> <?= $document3['title'] ?></td>
                                                                    <!-- TO BE EDITED -->
                                                                    <td class="approval_status<?= $document3['status'] ?>">
                                                                        <span class="<?php select_badge($document3['status']) ?>">
                                                                            <?php
                                                                            $dis_status =  display_status_value($subdoc_status);
                                                                            if (!empty($document3['comment_exist'] && $dis_status == "PENDING ACTION")) {
                                                                                echo "WITH COMMENTS";
                                                                            } else {
                                                                                echo $dis_status;
                                                                            }
                                                                            $c++; ?>
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-center" style="width: 40px;">
                                                                        <strong class="text-success">
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn btn-sm btn-primary js-tooltip-enabled" onclick="load_documents_for_review(<?= $document3['document_id'] ?>, <?= $document3['folder_id'] ?>,0,'<?= $file_new ?>',2)" data-toggle="tooltip" title="View Details" data-original-title="View Details">
                                                                                    <i class="fa fa-search "></i>
                                                                                </button>
                                                                                <a data-toggle="tooltip" title="Approval Action" data-original-title="Approval Action" href="document_approval_form.php?review=<?= $document3['document_id'] ?>&folder_id=<?= $document3['folder_id'] ?>" class="btn btn-sm btn-success js-tooltip-enabled">
                                                                                    <i class="fa fa-pencil"></i>
                                                                                </a>
                                                                            </div>
                                                                        </strong>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="block block-bordered block-rounded">
                                    <div class="block-header bg-gd-primary" role="tab" id="accordion_h5">
                                        <a class="font-w600 text-white" data-toggle="collapse" data-parent="#accordion" href="#accordion_q5" aria-expanded="true" aria-controls="accordion_q5">1.4 Documents Control</a>
                                    </div>
                                    <!--                                document control-->
                                    <div id="accordion_q5" class="collapse" role="tabpanel" aria-labelledby="accordion_h5" data-parent="#accordion">
                                        <div class="block-content">
                                            <table class="table table-bordered  table-vcenter js-dataTable-full-pagination">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>#</th>
                                                        <th>File</th>
                                                        <th># of Attached files</th>
                                                        <th>No. of Revision/s</th>
                                                        <th>Attached Files</th>
                                                        <th>Reviewers</th>
                                                        <th>Approval</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-center">
                                                    <?php
                                                    $counter = 1;
                                                    ?>
                                                    <?php foreach ($documents5 as $document5) : ?>
                                                        <?php
                                                        $doc5_id =  $document5['document_id'];
                                                        $get_files_qry = $conn->query("SELECT *
                                                        FROM `tbl_files`
                                                        WHERE document_id =  $doc5_id AND is_deleted = 0                                 
                                                        ORDER BY document_id DESC");
                                                        $files = $get_files_qry->fetchAll();
                                                        ?>
                                                        <tr class="text-center">
                                                            <!--                                                    .'-'. $document5['document_id']-->
                                                            <td style="vertical-align: top"><?= $counter; ?></td>
                                                            <td class="text-left" style="vertical-align: top">
                                                                <?php
                                                                $d1 =  $document5['project_code'];
                                                                $d2 =  $document5['originator2'];
                                                                $d3 =  $document5['discipline'];
                                                                $d4 =  $document5['document_type'];
                                                                $d5 =  $document5['document_zone'];
                                                                $d6 =  $document5['document_level'];
                                                                $d7 =  $document5['sequence_no'];
                                                                $file_new =  stitch_file_name($d1, $d2, $d3, $d4, $d5, $d6, $d7);
                                                                echo $file_new;
                                                                ?>
                                                            </td>

                                                            <td style="vertical-align: top"> <?= $document5['number_of_attached_files'] ?></td>
                                                            <td style="vertical-align: top"> <?= (($document5['number_of_revised_files'] == '') ? '0' : $document5['number_of_revised_files']) ?></td>
                                                            <td style="vertical-align: top">
                                                                <?php
                                                                $file_counter = 1;
                                                                foreach ($files as $file) {
                                                                ?>
                                                                    <a href="assets/media/docs/<?= $file['src']; ?>" target="_blank">File <?= $file_counter ?></a>
                                                                <?php
                                                                    $file_counter++;
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                try {
                                                                    $doc_id =  $document5['document_id'];
                                                                    $get_reviewers_qry = $conn->query("SELECT *,CONCAT(firstname,' ',lastname) as full_name FROM `tbl_document_reviewer` 
                                                                                                        INNER JOIN tbl_employees ON tbl_document_reviewer.reviewer_id =  tbl_employees.employee_id
                                                                                                        WHERE `document_id` = $doc_id");

                                                                    $get_approval_qry = $conn->query("SELECT `firstname`,`lastname`, tbl_documents.status AS doc_status,CONCAT(firstname,' ',lastname) as full_name FROM `tbl_documents` 
                                                                                                        INNER JOIN tbl_employees ON tbl_documents.approval_id =  tbl_employees.employee_id
                                                                                                        WHERE `document_id` = $doc_id");
                                                                    $get_approval = $get_approval_qry->fetch();
                                                                    $get_reviewers = $get_reviewers_qry->fetchAll();
                                                                } catch (Exception $e) {
                                                                    echo $e;
                                                                }
                                                                ?>
                                                                <table class="table-borderless">
                                                                    <?php
                                                                    foreach ($get_reviewers as $rev) {
                                                                        $initials = $rev['firstname'][0] . '. ' . $rev['lastname'][0];
                                                                    ?>
                                                                        <tr>
                                                                            <td class="pl-5 m-0 mt-5"><?= strtoupper($initials); ?></td>
                                                                            <td class="pl-5 m-0 mt-5">
                                                                                <span class=" <?php echo select_badge($rev['review_status']) ?>  " data-toggle="tooltip" title="<?php echo display_status_value($rev['review_status']) ?>" data-original-title="View Details"> <?php echo display_status_value($rev['review_status'])[0] ?> </span>
                                                                            </td>
                                                                        </tr>

                                                                    <?php } ?>
                                                                </table>
                                                            </td>
                                                            <td style="vertical-align:top" class="pt-20">
                                                                <span><?= strtoupper($get_approval['firstname'][0] . " " . $get_approval['lastname'][0]) ?></span>
                                                                <span class="ml-10 <?php echo select_badge($get_approval['doc_status']) ?>" data-toggle="tooltip" title="<?php echo display_status_value($get_approval['doc_status']) ?>" data-original-title="View Details"> <?php echo display_status_value($get_approval['doc_status'])[0] ?></span>
                                                            </td>
                                                            <td>
                                                                <button type="button" type="submit" class="btn btn-sm  js-tooltip-enabled" onclick="load_documents_for_review(<?= $doc5_id ?>, <?= $folder_id ?>,0,'<?= $file_new ?>',2)" data-toggle="tooltip" title="View Details" data-original-title="View Details">
                                                                    <i class="fa fa-search"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <?php $counter++ ?>
                                                    <?php endforeach ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Single Item -->
                </div>
                <!-- END Page Content -->
            </main>
            <?php
            $display_status = '';
            $icon = '';
            $class = '';
            ?>
            <div class="modal fade" id="document_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content modal-xl">
                        <div class="modal-header bg-dark">
                            <h5 class="modal-title text-success" id="exampleModalLongTitle">Details of the Submitted
                                Document</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container" id="submitted_documents_info">
                                <?php echo "<a href=\"assets\media\docs\\" . $document['src'] . "\" " . "target=\"_BLANK\">"; ?>
                                <i class="text-dark fa fa-download fa-2x"></i> </a>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END VIEW REVIEW MODAL -->
            <!--DELETE MODAL-->
            <div class="modal fade" id="delete_document_modal" tabindex="-1" role="dialog" aria-labelledby="add_new_file" aria-hidden="true">
                <div class="modal-dialog modal-xs" role="document">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title"> </h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="si si-close"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content">
                                <form id="delete_file_form">
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            <!--                                        <input type="email" class="form-control" id="manual_delete_cc" name ="manual_delete_cc" placeholder="Email">-->
                                            <input id="delete_document_id" hidden name="delete_document_id" value="">
                                            <input id="delete_folder_name" hidden name="delete_folder_name" value="<?= $current_folder ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">

                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12 text-center">
                                            <button type="submit" id="delete_document_btn" class="btn btn-sm btn-danger min-width-175">
                                                <i class="fa fa-x mr-5"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--DELETE MODAL-->
            <!-- SUBMIT NEW FILE  ADD FILE FORM-->
            <div class="modal fade" id="add_new_file" role="dialog" aria-labelledby="add_new_file" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">Add New File </h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="si si-close"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content">
                                <form id="add_file_form">
                                    <div class="row">
                                        <div class="col">
                                            <input type="text" name="folder_name2" value="<?= $_GET['folder'] ?>" hidden>
                                            <input type="text" name="folder_id" value="<?= $folder_id ?>" hidden>
                                            <input type="hidden" name="add_document_file" value="1">
                                            <div class="form-group row">
                                                <label for="project_code1" class="col-sm-3 col-form-label">Project
                                                    Code</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-lg text-sm-left" id="title" name="project_code1" placeholder="Project Code" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="originator" class="col-sm-3 col-form-label">Originator</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-lg text-sm-left" id="originator" name="originator" placeholder="Originator" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="discipline" class="col-sm-3 col-form-label">Discipline</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-lg text-sm-left" id="discipline" name="discipline" placeholder="Discipline" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="Type" class="col-sm-3 col-form-label">Type</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-lg text-sm-left" id="type" name="type" placeholder="Type" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="rev" class="col-sm-3 col-form-label">Zone</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-lg text-sm-left" id="zone" name="zone" placeholder="Zone" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="rev" class="col-sm-3 col-form-label">Level</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-lg text-sm-left" id="level" name="level" placeholder="Level" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="sequence" class="col-sm-3 col-form-label">Sequence no.</label>
                                                <div class="col-sm-9">
                                                    <input type="text" value="<?= $sequence ?>" readonly class="form-control form-control-lg text-sm-left" id="sequence_no" name="sequence_no" placeholder="Sequence" required>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col">
                                            <div class="form-group row">
                                                <label for="title" class="col-sm-3 col-form-label">Document Title</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control form-control-lg text-sm-left" id="title" name="title" placeholder="Title" required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label for="description" class="col-sm-3 col-form-label">Description</label>
                                                <div class="col-sm-9">
                                                    <textarea type="text" class="form-control t " id="description" name="description" placeholder="Description" required></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="file" class="col-sm-3 col-form-label">Upload File</label>
                                                <div class="col-sm-9">
                                                    <input type="file" name="files[]" id="attached_files" required multiple onchange="set_new_file_button()">
                                                </div>
                                            </div>
                                            <?php
                                            try {
                                                $user_sql = $conn->query("SELECT * FROM users 
                                                     INNER JOIN tbl_employees ON users.emp_id = tbl_employees.employee_id
                                                     WHERE `deleted` = 0 AND emp_id != $session_emp_id AND sub_id = $subscriber_id ORDER BY tbl_employees.firstname ASC");
                                                $user_qry1 = $user_sql->fetchAll();
                                            } catch (Exception $e) {
                                                echo $e;
                                            }
                                            ?>
                                            <div class="form-group row">
                                                <label class="col-sm-3" for="select_reviewer">Review Personnel</label>
                                                <div class="col-sm-9">
                                                    <select class="js-select2 " id="select_reviewer" onchange="test()" name="select_reviewer[]" style="width: 100%;" data-placeholder="Select Review Personnel.." multiple required>
                                                        <?php foreach ($user_qry1 as $emp1) : ?>
                                                            <?php $user_fullname = ucwords(strtolower($emp1['firstname'] . " " . $emp1['lastname'])); ?>

                                                            <option value="<?= $emp1['emp_id'] ?>"><?= $user_fullname ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                    <!--                                                <input type="text" class="form-control" id="reviewer_names">-->
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-3" for="select_approval">Select Approval Personnel</label>
                                                <div class="col-9">
                                                    <select class="js-select2 form-control" id="select_approval" name="select_approval" style="width: 100%;" data-placeholder="Choose one..">
                                                        <option></option><!-- Required for data-placeholder attribute to work with Select2 plugin -->
                                                        <?php foreach ($user_qry1 as $user1) : ?>
                                                            <?php $user_fullname = ucwords(strtolower($user1['firstname'] . "  " . $user1['lastname'])); ?>
                                                            <option value="<?= $user1['emp_id'] ?>"><?= $user_fullname ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-3" for="select_approval">Add CC</label>
                                                <div class="col-sm-9">
                                                    <select class="js-select2 form-control" id="select_cc" name="select_cc[]" style="width: 100%;" data-placeholder="Optional..">

                                                        <option></option>
                                                        <?php foreach ($user_qry1 as $emp1) : ?>
                                                            <?php $user_fullname = ucwords(strtolower($emp1['firstname'] . " " . $emp1['lastname'])); ?>
                                                            <option value="<?= $emp1['emp_id'] ?>"><?= $user_fullname ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3" for="manual_cc">Add CC Manually : </label>
                                                <div class="col-sm-9">
                                                    <input type="email" class="form-control form-control-lg text-sm-left" type="text" id="manual_cc" name="manual_cc" placeholder="Optional..">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-12 text-center">
                                            <button type="submit" id="new_file_btn" class="btn btn-sm btn-hero btn-alt-primary min-width-175" disabled>
                                                <i class="fa fa-save mr-5"></i> Submit new file.
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="document_details_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content modal-xl">
                        <div class="modal-header bg-dark">
                            <h5 class="modal-title text-success" id="exampleModalLongTitle">Documents Details </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="update_review">
                            <div class="modal-body">
                                <div class="container" id="document_info">
                                    <!--                                --><?php //echo "<a href=\"assets\media\docs\\" . $document['src'] . "\" " . "target=\"_BLANK\">"; 
                                                                            ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>

                                <div class="col-12 text-center" id="loading-icon" style="display: none;">
                                    <div class="col-12 text-center" id="document_action_btn">
                                    </div>
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                    <div>
                                        <p>Please Wait...</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- FOR REVIEW MODAL  -->
            <!--  ADD EDIT REPLY MODAL   -->
            <div class="modal fade " id="add_reply_modal" tabindex="-1" role="dialog" aria-labelledby="add_comment_modal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="add_comment_modal">Add/Edit Reply</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="reply_action" class="">
                            <div class="modal-body ">
                                <!-- HIDDEN VARIABLES     -->
                                <input type="number" hidden name="response_id" id="response_id" value="">
                                <input type="number" hidden name="reply_document_id" id="reply_document_id" value="">
                                <input type="number" hidden name="reply_reviewer_id" id="reply_reviewer_id" value="">
                                <input type="text" hidden name="file_new" id="file_new" value="">
                                <input type="text" hidden name="count_revision" id="count_revision" value="">
                                <input type="number" hidden value="1" name="response_type" id="response_type">
                                <input type="text" hidden value="<?= $current_folder ?>" name="folder_name_reply" id="folder_name_reply">
                                <input type="number" hidden value="<?= $folder_id ?>" name="folder_id_reply" id="folder_id_reply">
                                <!-- HIDDEN VARIABLES     -->
                                <div class="form-group row">
                                    <label class="col-sm-3" for="manual_cc2">Add CC Manually : </label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control form-control-lg text-sm-left" type="text" id="manual_cc2" name="manual_cc2" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3" for="select_approval">Add CC</label>
                                    <div class="col-sm-9">
                                        <select class="js-select2 " id="select_cc2" name="select_cc2[]" style="width: 100%;" data-placeholder="Choose at least one.." multiple required>
                                            <?php foreach ($user_qry1 as $emp1) : ?>
                                                <?php $user_fullname = ucwords(strtolower($emp1['firstname'] . " " . $emp1['lastname'])); ?>
                                                <option value="<?= $emp1['user_id'] ?>"><?= $user_fullname ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row" id="comment div">
                                    <label class="col-sm-3" for="reply_code" id="comment_label">Reply Code: </label>
                                    <div class="col-sm-9" id="reply_div">
                                        <select class="form-control" name="reply_code" id="reply_code" aria-label=".form-select-lg example">
                                            <option value="i">i</option>
                                            <option value="ii">ii</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" id="revised_file_div">
                                    <label class="col-sm-3" for="revised_files" id="comment_label">Attach File: </label>
                                    <div class="col-sm-9" id="reply_div">
                                        <input class="form-control form-control-lg text-sm-left" type="file" id="revised_files" rows="4" name="revised_files[]" multiple>
                                    </div>
                                </div>
                                <div class="form-group row" id="comment div">
                                    <label class="col-sm-3" for="reply" id="comment_label">Reply: </label>
                                    <div class="col-sm-9" id="reply_div">
                                        <textarea class="form-control form-control-lg text-sm-left" type="text" id="reply" name="reply" cols="6" rows="4"></textarea>
                                    </div>
                                </div>
                            </div>
                            <!--  set button unclickable if status is set -->
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-alt-success mr-5 mb-5" id="add_html_comment">
                                    <i class="fa fa-plus mr-5"></i>Add Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--    ADD REPLY MODAL-->
            <!-- END VIEW REVIEW MODAL -->

        <?php endif ?>
    <?php endif ?>
    <!-- END View Employee Details Modal -->
    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>

    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page JS Code -->
    <script src="assets/js/pages/be_tables_datatables.min.js"></script>
    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/select2/js/select2.full.min.js"></script>
    <script src="assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="assets/js/plugins/jquery-validation/additional-methods.js"></script>
    <script src="assets/js/plugins/sweetalert2/sweetalert2.min.js"></script>

    <script src="assets/js/pages/be_ui_activity.min.js"></script>
    <!-- Page JS Helpers (Select2 plugin) -->
    <script>
        jQuery(function() {
            Codebase.helpers('select2');
        });
    </script>

    <!-- Page JS Code -->
    <script src="assets/js/pages/be_forms_validation.min.js"></script>
    <script>
        $('.select2-search__field ').keyup(function() {
            $("#display_test").val($('#description').val());
            Codebase.helpers('select2')
        });

        $(document).ready(function() {
            let toast = Swal.mixin({
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-alt-success m-5',
                    cancelButton: 'btn btn-alt-danger m-5',
                    input: 'form-control'
                }
            });
            var loading = function() {
                Swal.fire({
                        title: "Uploading Docs",
                        allowOutsideClick: false,
                        showConfirmButton: false,
                    }),
                    Swal.showLoading()
            }
            var loading2 = function() {
                Swal.fire({
                        title: "Deleting Docs",
                        allowOutsideClick: false,
                        showConfirmButton: false,
                    }),
                    Swal.showLoading()
            }

            // loading.showLoading();

            // loading.showLoading();
            $("#add_file_form").submit(function(event) {
                event.preventDefault();
                if (confirm("Are you sure you want to upload this document?")) {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/document_files_ajax.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function() {
                            $('#add_new_file').modal('hide');
                            loading();
                        },
                        success: function(response) {
                            console.log(response)
                            Swal.close()
                            location.reload();
                        },
                        error: function() {
                            console.log("Error adding employee function");
                        }
                    });
                }
            });

            $("#update_review").submit(function(event) {
                event.preventDefault();
                // document_id = $("#document_id").innerText;
                $("#loading-icon").show();
                $("#document_action_btn").hide();
                if (confirm("Are you sure you want to approve/reject this document?")) {
                    $("#loading-icon").fadeOut(4500);
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/document_files_ajax.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,

                        success: function(response) {
                            $('#loading-icon').hide();
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            console.log("Error adding updating review function");
                        }
                    });
                }
            });

            $("#reply_action").submit(function(event) {
                event.preventDefault();
                var formData = new FormData()
                if (confirm("Are you sure you want to add?")) {
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
                        }
                    });
                }
            });

            $("#delete_file_form").submit(function(event) {
                event.preventDefault();
                var id = $('#delete_document_id').val();
                $('#delete_document_modal').modal('hide');
                toast.fire({
                    title: 'Are you sure you want to delete this??',
                    text: 'This file will be removed from the table!',
                    type: 'warning',
                    showCancelButton: true,
                    customClass: {
                        confirmButton: 'btn btn-alt-danger m-1',
                        cancelButton: 'btn btn-alt-secondary m-1'
                    },
                    confirmButtonText: 'Yes, delete it!',
                    html: false,
                    preConfirm: e => {
                        return new Promise(resolve => {
                            setTimeout(() => {
                                resolve();
                            }, 50);
                        });
                    }
                }).then(result => {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            url: "ajax/document_files_ajax.php",
                            data: new FormData(this),
                            contentType: false,
                            cache: false,
                            processData: false,
                            success: function(response) {
                                console.log(response);
                                swal.fire({
                                    allowOutsideClick: false,
                                    title: "Deleted",
                                    text: "The Document has been removed!",
                                    type: "success"
                                }).then(function() {
                                    location.reload();
                                });
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        toast.fire('Cancelled', 'File Not Deleted :)', 'error');
                    }
                });
            });
        });

        function test() {
            selected = $('#select_reviewer').find(':selected');
            names = [];
            for (var i = 0; i <= selected.length - 1; i++) {
                // console.log(selected[i].text);
                names.push(selected[i].text);

            }
            let text = names.toString();
            $('#reviewer_names').val(text);
        }

        function employee_profile_info(id) {
            $.ajax({
                type: "POST",
                url: "ajax/employees_ajax.php",
                data: {
                    user_id: id,
                    employee_profile_info: 1,
                },
                success: function(data) {
                    $('#profile_info').html(data);
                    location.reload()
                }
            });
        }

        function delete_submitted_document_modal(document_id) {
            $('#delete_document_modal').modal('show');
            $('#delete_document_id').val(document_id);
        }

        function show_approval_details(id) {
            $.ajax({
                type: "POST",
                url: "ajax/document_files_ajax.php",
                data: {
                    document_id: id,
                    show_approval_details: 1,
                },
                success: function(data) {
                    $('#document_info').html(data);
                    // location.reload()
                }
            });
        }

        function set_new_file_button() {
            var x = $('#attached_files').val();
            if (x == "") {
                $('#new_file_btn').prop('disabled', true);
            } else {
                $('#new_file_btn').prop('disabled', false);
            }
        }

        function set_action_id(id) {
            var x = id;
            $('#action_id').val(id);
        }

        function show_submitted_details(remarks) {
            $('#document_modal').modal('show');
            $('#remarks_id').text(remarks);
        }

        function open_file($file_name) {
            if ($file_name != "") {
                if (confirm("Download the file and write your comments then upload here.")) {
                    window.open("assets/media/docs/".concat($file_name));
                }
            }
        }

        function load_submitted_documents_details(document_id, folder_id) {
            $('#document_modal').modal('show');

            $.ajax({
                type: "POST",
                url: "ajax/document_files_ajax.php",
                data: {
                    load_submitted_documents: 1,
                    folder_id,
                    folder_id,
                    document_id,
                    document_id
                },
                success: function(data) {
                    $('#submitted_documents_info').html(data);
                }
            });
        }

        function add_reviewer_comments(document_id, reviewer_id) {

            $.ajax({
                type: "POST",
                url: "ajax/document_files_ajax.php",
                data: {
                    // document_id: document_id,
                    //  document_id, document_id,
                    add_reviewer_comments: 1,
                },
                success: function(response) {
                    $('#document_info').html(data);
                    // location.reload()
                    alert(response);

                }
            });
        }

        function load_documents_for_review(document_id, folder_id, table_appearance, file_new, action_disable) {
            // action_disable =  2 disables action or add reply if loading view for the REVIEWER
            $('#document_details_modal').modal('show');
            $.ajax({
                type: "POST",
                url: "ajax/document_files_ajax.php",
                data: {
                    action_disable: action_disable,
                    table_appearance: table_appearance,
                    file_new: file_new,
                    document_id: document_id,
                    action_disable: action_disable,
                    folder_id,
                    folder_id,
                    load_documents_for_review: 1,
                },
                success: function(data) {
                    $('#document_info').html(data);
                    // location.reload()
                }
            });
        }

        function add_new_reply(response_id, document_id, reply_reviewer_id, file_new, count_revision, attachment_required) {

            if (attachment_required == 0) {

                $('#revised_files').attr("required", "false");
                $('#revised_file_div').attr("hidden", "true");

            }
            $('#revised_files').attr("rows", 4);
            $('#reply_document_id').val(document_id);
            $('#response_id').val(response_id);
            $('#reply_reviewer_id').val(reply_reviewer_id);
            $('#file_new').val(file_new);
            $('#count_revision').val(count_revision);
            $('#add_reply_modal').modal('show');
            $('#document_details_modal').modal('hide');
        }

        function load_uploaded_files(document_id, folder_id) {
            $('#document_details_modal').modal('show');
            $.ajax({
                type: "POST",
                url: "ajax/document_files_ajax.php",
                data: {
                    document_id: document_id,
                    show_files: 1,
                },
                success: function(data) {
                    $('#document_info').html(data);
                }
            });
        }
    </script>
    <?php
    include 'includes/footer.php';
