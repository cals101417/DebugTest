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
        <div class="bg-body-dark bg-pattern" style="background-image: url('assets/media/photos/bg-pattern-inverse.png');">
            <div class="row mx-0 justify-content-center">
                <div class="hero-static col-lg-6 col-xl-4">
                    <div class="content content-full overflow-hidden">
                        <div class="py-30 text-center">
                            <a class="font-w700" href="index.php">
                                <img class="img pd-l-30" src="assets/media/photos/safety-surfers-management-logo.png" style="height: 60px; !important">
                            </a>
                            <h1 class="h4 font-w700 mt-30 mb-10">Welcome to Your Dashboard</h1>
                            <h2 class="h5 font-w400 text-muted mb-0">It’s a great day today!</h2>
                        </div>

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
                                                <label class="custom-control-label" for="login-remember-me">Remember Me</label>
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

    ?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?= $header_layout ?> page-header-inverse <?= $main_content . ' ' . $sidebar_layout ?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        ?>
        <?php
        if (isset($_GET['review'])) {
            try {
                //RESPONSE TYPE  1 =  COMMENT 2 = REPLY
                //GET SUBMITTED DOCUMENTS
                $folder_id = $_GET['folder_id'];
                $get_folder_name_qry = $conn->query("SELECT `folder_name` FROM tbl_folders WHERE folder_id = $folder_id");
                $folder_name3 = $get_folder_name_qry->fetch();
                $document_id = $_GET['review'];
                $get_submitted_documents = $conn->query("SELECT `document_id`,
                                                `src`,
                                                `title`,                                             
                                                `description`,
                                                `folder_id`,       
                                                tbl_documents.user_id,
                                                `approval_id`, 
                                                `date_uploaded`,
                                                tbl_documents.status,
                                                `project_code`,
                                                `document_type`,
                                                `document_zone`,
                                                `originator`,
                                                `document_level`,
                                                 tbl_documents.user_id,                                          
                                                `discipline`,
                                                `sequence_no`,            
                                                 `firstname`,
                                                `lastname`,
                                                `email`,
                                                `rev`,
                                                `remarks`,
                                                `originator2`,
                                                (SELECT COUNT(response_file_id) FROM `tbl_document_response_files` WHERE `document_id` = $document_id AND `response_type` = 1 AND `is_deleted` = 0 GROUP BY `document_id`) AS revisions,
                                                (SELECT tbl_position.position from tbl_employees  INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id WHERE tbl_employees.employee_id = tbl_documents.originator)  AS submitter_position,
                                                (SELECT tbl_position.position from tbl_employees  INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id WHERE tbl_employees.employee_id = tbl_documents.approval_id)   AS approval_position,
                                                (SELECT  CONCAT(firstname, ' ', lastname)from tbl_employees WHERE employee_id = tbl_documents.approval_id)AS approval_name
                                            FROM `tbl_documents`                                       
                                            INNER JOIN tbl_employees ON tbl_documents.user_id = tbl_employees.employee_id
                                            INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id                                            
                                            WHERE document_id = $document_id ORDER BY document_id DESC");
                $document =  $get_submitted_documents->fetch();
                $new_document = array();
                foreach ($document as $docu) {
                    $new_document[] = $docu;
                }
                $approver_email_qry = $conn->query("SELECT `email`,`firstname`,`lastname` FROM tbl_documents
                                              INNER JOIN tbl_employees ON tbl_documents.approval_id = tbl_employees.employee_id
                                              WHERE document_id = $document_id");
                $approver_info = $approver_email_qry->fetch();
                $originator_name = $approver_info['firstname'] . " " . $approver_info['lastname'];
                //GET THE FILES UPLOADED BY THE ORIGINATOR
                $originator_id = $document['user_id'];


                //GET REVIEWERS DOCUMENTS - DISPLAY ORIGINAL DOCUMENTS IF REVIEWERS HAS YET TO UPLOAD DOCUMENNTS
                // CHECK FOR ANY DOCUMENTS UPLOADED OTHER THAN THE ORIGINATOR

                $check_check_files = $conn->query("SELECT `document_id` FROM tbl_files WHERE `document_id` = $document_id AND `user_id` != $originator_id");

                $check_files = $check_check_files->fetchAll();
                $row_count =  count($check_files);
                $attached_files = '';
                $file_loaded  = ""; //  NEED TO SELECT WHETHER TO ADD NEW RECORD OR JUST OVER RIDE THE FILE UPLOADED BY OTHER REVIEWERS

                if ($row_count == 0) {
                    $file_loaded = 1;
                    $get_originator_files = $conn->query("SELECT * FROM tbl_files WHERE `document_id` = $document_id AND `user_id` = $originator_id");
                    $get_files = $get_originator_files->fetchAll();
                    $attached_files =   $get_files;
                } else {
                    $file_loaded = 2;
                    $check_reviewer_files = $conn->query("SELECT * FROM tbl_files WHERE `document_id` = $document_id AND `user_id` != $originator_id");
                    $reviewer_files = $check_reviewer_files->fetchAll();
                    $attached_files = $reviewer_files;
                }


                $get_document_reviewers = $conn->query("SELECT 
                                                   `review_document_id`,
                                                   `reviewer_id`,
                                                   `document_id`,
                                                   `review_status`,
                                                   `folder_id`,
                                                   `review_status`,                                           
                                                   `lastname`,
                                                   `firstname`,
                                                   tbl_position.position,
                                                    `date_reviewed`
                                                   FROM tbl_employees
                                                   RIGHT JOIN tbl_document_reviewer
                                                   ON tbl_employees.employee_id = tbl_document_reviewer.reviewer_id
                                                   INNER JOIN  tbl_position ON tbl_employees.position = tbl_position.position_id
                                                   WHERE document_id = $document_id");


                $docs2 = $get_document_reviewers->fetchAll();

                //GET REVIEWERS DOCUMENTS

                //GET APPROVAL STATUS
                $approval_status_qry = $conn->query("SELECT `document_id`,
                                 
                                   `title`,                                             
                                   `description`,
                                   tbl_documents.user_id,
                                   `approval_id`, 
                                   `date_uploaded`,
                                   tbl_documents.status,
                                   `project_code`,
                                   `document_type`,
                                   `document_zone`,
                                   `document_level`,
                                   tbl_documents.user_id,
                                   `discipline`,
                                    `firstname`,                         
                                   tbl_documents.status,
                                   `lastname`
                               FROM `tbl_documents`
                               INNER JOIN tbl_employees ON tbl_documents.approval_id = tbl_employees.employee_id                  
                               WHERE tbl_documents.document_id = $document_id  AND
                               folder_id = $folder_id
                               ORDER BY document_id DESC");

                $docs3 =  $approval_status_qry->fetch();
                //GET APPROVAL STATUS

                //GET REVIEWER COMMENTS

                $reviewer_comments_qry = $conn->query("SELECT *                                                
                                                            FROM `tbl_document_comments_replies`
                                                            INNER JOIN tbl_employees ON tbl_document_comments_replies.reviewer_id = tbl_employees.employee_id                                         
                                                            WHERE tbl_document_comments_replies.document_id = $document_id  
                                                             AND tbl_document_comments_replies.is_deleted = 0 AND `response_id` != 0                                                         
                                                            ORDER BY reviewer_id ASC");

                $rev_comments =  $reviewer_comments_qry->fetchAll();
                // GET REPLIES
                $get_replies_qry = $conn->query("SELECT *                                                
                                                    FROM `tbl_document_comments_replies`
                                                    INNER JOIN tbl_employees ON tbl_document_comments_replies.reviewer_id = tbl_employees.employee_id                                         
                                                    WHERE tbl_document_comments_replies.document_id = $document_id   AND tbl_document_comments_replies.is_deleted = 0 AND`response_id` != 0      
                                                    ORDER BY document_id DESC");
                $rev_replies =  $get_replies_qry->fetchAll();
            } catch (Exception $e) {
                echo $e;
            }
            // STICH EVERYTHING TO MAKE A NEW FILE NAME

            $file_no = $document['project_code'] . "-" .
                $document['originator'] . "-" .
                $document['discipline'] . "-" .
                $document['document_type'] . "-" .
                $document['document_zone'] . "-" .
                $document['document_level'] . "-" .
                $document['sequence_no'];

            $new_file_no = array();
            $pieces = explode("-", $file_no);
            $connector = "-";
            $count = 1;
            foreach ($pieces as $piece) {
                array_push($new_file_no, $piece);
            }
            $file_new = "";
            foreach ($new_file_no as $item) {
                if ($count == count($new_file_no)) {
                    $connector = "";
                }
                if (strtolower($item) != "n/a") {
                    $file_new = $file_new . $item . $connector;
                }
                $count++;
            }
            $i = strlen($file_new);
            //            echo substr_replace($file_new, '', $i - 1, 1);

            // STICH EVERYTHING TO MAKE A NEW FILE NAME
            //GET REVIEWER COMMENTS
            $current_reviewer_status = '';
            $set_status_color = '';
            // SET COLOR  DOCUMENT REVIEW STATUS CODE
            if ($current_reviewer_status == "A") {
                $set_status_color = 'red';
            } elseif ($current_reviewer_status == "B") {
                $set_status_color = 'green';
            } elseif ($current_reviewer_status == "C") {
                $set_status_color = 'orange';
            } elseif ($current_reviewer_status == "D") {
                $set_status_color = 'blue';
            }
            function display_status_value($status)
            {
                $result = '';
                switch ($status) {

                    case 'A':
                        $result = "APPROVED WITHOUT COMMENT";
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
                        $result =  "badge badge-success";
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
                        $result = "badge rounded-pill bg-warning text-light";
                        break;
                }
                echo $result;
            }
        ?>
            <style type="text/css">
                thead,
                th,
                label {
                    font-weight: bolder;
                }

                <?php echo '.' . $current_reviewer_status . '{    
                    }' ?>.bold-label {
                    font-weight: bolder;
                }

                .background {
                    border-color: red;
                }
            </style>
            <main id="main-container">
                <?php $this_document_id = $document['document_id']; ?>
                <input type="text" hidden value="<?= $session_emp_id ?>" name="reviewer_name" id="rev_id">
                <input type="text" hidden value="<?= $document['document_id'] ?>" name="document_name" id="document_id">
                <!-- GLOBAL VARIABLE  -->
                <div class="bg-dark">
                    <div class="bg-pattern bg-black-op-25" style="background-image: url('assets/media/photos/construction6.jpg');">
                        <div class="content content-top text-center">
                            <div class="py-50">
                                <h1 class="font-w700 text-white mb-10">Review Document</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Page Content -->
                <div class="container shadow p-3 mb-5 bg-white border">
                    <div class="content">
                        <h2 class="content-heading"><?php  ?></h2>
                        <!-- DIV BLOCK  -->
                        <div class="block">
                            <div class="block-header block-header-default bg-pattern text-white " style="background-color: #808080">
                                <p>Document No. <?= $this_document = $document['document_id'] ?><?php  ?></p>
                                <button class="float-right btn btn-sm btn-alt-primary btn-rounded" onclick="history.back()"><i class="si si-action-undo"></i> Go Back</button>
                            </div>
                        </div>
                        <!-- DIV BLOCK  -->
                        <div class="container" id="background">
                            <div class="form-group">

                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-striped table-borderless table-sm mt-20  text-center">
                                        <thead>

                                        </thead>
                                        <tbody class="text-left">
                                            <tr>
                                                <th class="bold-label text-right">Proj. Code </th>
                                                <td><?= $document['project_code'] ?></td>
                                            </tr>
                                            <tr>
                                                <th class="bold-label text-right ">Originator</th>
                                                <td><?php echo $document['originator2'];  ?></td>

                                            </tr>
                                            <tr>
                                                <th class="bold-label text-right">Discipline</th>
                                                <td><?= $document['discipline'] ?></td>
                                            </tr>
                                            <tr>
                                                <th class="bold-label text-right">Type</th>
                                                <td><?= $document['document_type'] ?></td>
                                            </tr>
                                            <tr>
                                                <th class="bold-label text-right">Zone</th>
                                                <td><?= $document['document_zone'] ?></td>

                                            </tr>
                                            <tr>
                                                <th class="bold-label text-right">Level</th>
                                                <td><?= $document['document_level'] ?></td>

                                            </tr>
                                            <tr>
                                                <th class="bold-label text-right">Sequence No.</th>
                                                <td><?= $document['sequence_no'] ?></td>

                                            </tr>
                                            <tr>
                                                <th class="bold-label text-right">Rev No.</th>
                                                <td>
                                                    <?php
                                                    if ($document['revisions'] != "") {
                                                        echo $document['revisions'];
                                                    } else {
                                                        echo "0";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th class="bold-label text-right">Document Title: </th>
                                                <td><?= $document['title'] ?></td>
                                            </tr>
                                            <tr>
                                                <th class="bold-label text-right">Submitted by: </th>
                                                <td><?php echo $document['firstname'] . ' ' . $document['lastname'];  ?></td>
                                            </tr>
                                            <tr>
                                                <th class="bold-label text-right">Position: </th>
                                                <td><?= $document['submitter_position'] ?></td>
                                            </tr>
                                            <tr>
                                                <th class="bold-label text-right">Attached File/s: </th>
                                                <td>
                                                    <!-- GET FILED IDS -->
                                                    <?php $file_id = array(); ?>
                                                    <?php foreach ($attached_files as $attached_file) : ?>
                                                        <a href="assets/media/docs/<?= $attached_file['src'] ?>
                                                                " target="_blank"><?= $attached_file['src'] ?></br>
                                                        </a>
                                                        <?php array_push($file_id, intval($attached_file['file_id'])); ?>
                                                    <?php endforeach ?>
                                                    <!-- SET THE FILE UPLOAD LIMIT ACCORDING TO THE FILE UPLOADED BY THE ORIGINATOR -->
                                                    <?php
                                                    $required_upload_number  = count($attached_files);
                                                    $file =  json_encode($file_id);
                                                    ?>
                                                    <input type="text" hidden name="required_upload_number" id="required_upload_number" value="<?php echo $required_upload_number; ?>">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12">
                                    <table class="table table-striped table-borderless table-sm mt-20  text-center">
                                        <thead>
                                            <tr class="text-left text-white" style="background-color: #808080">
                                                <td colspan="8">Reviewer Comment Code Legend : </td>
                                            </tr>
                                            <tr class="text-left" style="background-color: #F6F7F9">
                                                <td colspan="8">1 = action required on this issue 2= advisory comment
                                                    <button class="btn btn-success float-sm-right" id="add_comments_btn" onclick="add_rev_comments()"> Add Comments</button>
                                                </td>
                                            </tr>
                                            <th>No.</th>
                                            <th>Initial</th>
                                            <th>Comment Page</th>
                                            <th>RCC</th>
                                            <th>Reviewers Comments</th>
                                            <th>Date</th>
                                            <th>Attached File/s</th>
                                            <th>Action</th>
                                        </thead>
                                        <?php $comment_status = ''; ?>
                                        <tbody>

                                            <?php $counter = 0;
                                            $reviewer_counter = 0;
                                            $comment_exists = "";
                                            $attachment_required = "required";
                                            ?>
                                            <?php foreach ($rev_comments as $rev_comment) : ?>
                                                <?php
                                                if ($rev_comment['reviewer_id'] == $session_emp_id) {
                                                    $comment_status = "B";
                                                }
                                                //                                                  Current rev is the first reviewer set. We will display all the comments of all the current reviewer.
                                                //                                                 We will increment if the $current_rev is not equal to the reviewer of the current index
                                                $first_name = $rev_comment['firstname'];
                                                $last_name = $rev_comment['lastname'];
                                                ?>
                                                <tr>

                                                    <?php
                                                    //                                                    Check if there's existing comment by the current reviewer so that we can disable input file.
                                                    //    Our logic requires multiple comments but single instance of adding files

                                                    if ($rev_comment['reviewer_id'] == $session_emp_id) {
                                                        $comment_exists = "hidden";
                                                        $attachment_required = "";
                                                    }
                                                    $current_rev = $rev_comments[$counter]['reviewer_id'];
                                                    if ($counter == 0) {
                                                    ?>
                                                        <td><?= $reviewer_counter + 1; ?></td>
                                                        <td><?php echo $first_name[0] . " " . $last_name[0] ?></td>
                                                        <td class="text-left">
                                                            <?php
                                                            $pages = explode(',', $rev_comment['pages']);
                                                            for ($i = 1; $i < count($pages);) {
                                                                echo $pages[$i] . "<br>";
                                                                $i++;
                                                            } ?>
                                                        </td>
                                                        <td><?= $rev_comment['comment_code'] ?></td>
                                                        <td width="300px"><textarea class="form-control form-control-lg text-sm-left" type="text" readonly cols="4" rows="3"> <?= $rev_comment['comment'] ?></textarea></td>
                                                        <td><?= $rev_comment['comment_date'] ?></td>
                                                        <td>
                                                            <?php
                                                            $resp_id =  $rev_comment['response_id'];
                                                            $get_comment_files_qry = $conn->query("SELECT `response_file_src` FROM tbl_document_response_files WHERE response_id = $resp_id AND uploader_id  = $session_emp_id AND response_type = 2");
                                                            $get_comment_files = $get_comment_files_qry->fetchAll();
                                                            ?>
                                                            <?php $file_counter = 1; ?>
                                                            <?php foreach ($get_comment_files as $item) : ?>
                                                                <a data-toggle="tooltip" title="<?= $item['response_file_src'] ?>" data-original-title="<?= $item['response_file_src'] ?>" href="assets/media/docs/<?= $item['response_file_src'] ?>" target="_blank"><?= 'FILE' . $file_counter ?></a><br>
                                                                <?php $file_counter++; ?>
                                                            <?php endforeach ?>
                                                        </td>
                                                        <?php
                                                        $hide_action = "";
                                                        if ($rev_comments[$counter]['reviewer_id'] != $session_emp_id) {
                                                            $hide_action = "display: none;";
                                                        }
                                                        ?>
                                                        <td width="100" style="<?= $hide_action ?>" id="comment_action_column">
                                                            <button class="btn btn-sm btn-circle btn-alt-danger mr-5 mb-5" onclick="delete_comment(<?= $rev_comment['response_id'] ?>)"><i class="fa fa-times text-danger"></i></button>
                                                            <button class="btn btn-sm btn-circle btn-alt-success mr-5 mb-5" onclick="edit_rev_comments(<?= $rev_comment['response_id'] ?>)"><i class="fa fa-pencil text-success"></i></button>
                                                        </td>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-left">
                                                            <?php
                                                            $pages = explode(',', $rev_comment['pages']);
                                                            for ($i = 1; $i < count($pages);) {
                                                                echo $pages[$i] . "<br>";
                                                                $i++;
                                                            } ?>
                                                        </td>
                                                        <td><?= $rev_comment['comment_code'] ?></td>
                                                        <td width="300px"><textarea class="form-control form-control-lg text-sm-left" type="text" readonly cols="4" rows="3"> <?= $rev_comment['comment'] ?></textarea></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>


                                                    <?php
                                                        if ($current_rev != $rev_comments[$counter - 1]['reviewer_id']) {
                                                            $reviewer_counter++;
                                                        }
                                                    }
                                                    ?>


                                                </tr>
                                                <?php $counter++; ?>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-12">
                                    <table class="table table-striped table-borderless table-sm mt-20  text-center">
                                        <thead>
                                            <tr class="text-left text-white" style="background-color: #808080;">
                                                <td colspan="7">Originator Reply Code Legend : </td>
                                            </tr>
                                            <tr class="text-left" style="background-color: #F6F7F9;  ">
                                                <td colspan="7"> i = Incorporated ii= Evaluated and not incorporated for reason stated</td>
                                            </tr>
                                            <th>No.</th>
                                            <th>Reply Code</th>
                                            <th>Reviewer</th>
                                            <th>ORIGINATOR REPLY</th>
                                            <th>Date</th>
                                            <th>Files</th>
                                        </thead>
                                        <tbody>
                                            <?php $counter2 = 0;
                                            $reviewer_counter2 = 0; ?>
                                            <?php foreach ($rev_replies as $rev_rep) : ?>

                                                <?php
                                                $response_id = $rev_rep['response_id'];
                                                $first_name = $rev_rep['firstname'];
                                                $last_name = $rev_rep['lastname'];
                                                if ($rev_comments[$counter2]['reply_code'] != '') {
                                                ?>
                                                    <tr>
                                                        <?php
                                                        $current_rev2 = $rev_replies[$counter2]['reviewer_id'];
                                                        if ($counter2 == 0) {
                                                        ?>
                                                            <td><?= $reviewer_counter2 + 1; ?></td>
                                                            <td>
                                                                <?php
                                                                echo $rev_rep['reply_code'];
                                                                ?>
                                                            </td>
                                                            <td><?= $first_name . " " . $last_name ?></td>
                                                            <td width="300px"><textarea class="form-control form-control-lg text-sm-left" type="text" readonly cols="4" rows="3"> <?= $rev_rep['reply'] ?></textarea></td>
                                                            <td class=""><?= $rev_rep['reply_date'] ?></td>
                                                            <td>
                                                                <?php
                                                                $rev_id = $rev_rep['reviewer_id'];
                                                                $resp_id =  $rev_rep['response_id'];
                                                                $get_reply_files_qry = $conn->query("SELECT `response_file_src` FROM tbl_document_response_files WHERE response_id = $resp_id AND response_type != 2");
                                                                $get_reply_files = $get_reply_files_qry->fetchAll();
                                                                if ($rev_rep['reply'] == '') {
                                                                    $add_comments_disabled = "true";
                                                                }
                                                                ?>
                                                                <?php $file_counter = 1; ?>
                                                                <?php foreach ($get_reply_files as $item) : ?>
                                                                    <a href="assets/media/docs/<?= $item['response_file_src'] ?>" target="_blank"><?= "FILE NO. " . $file_counter ?></a></br>
                                                                    <?php $file_counter++; ?>
                                                                <?php endforeach ?>
                                                            </td>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <td>
                                                            </td>
                                                            <td></td>
                                                            <td></td>
                                                            <td width="300px"><textarea class="form-control form-control-lg text-sm-left" type="text" readonly cols="4" rows="3"> <?= $rev_rep['reply'] ?></textarea></td>
                                                            <td class=""></td>
                                                            <td>
                                                                <?php
                                                                if ($rev_rep['reply'] == '') {
                                                                    $add_comments_disabled = "true";
                                                                }
                                                                ?>
                                                            </td>
                                                        <?php
                                                            if ($current_rev2 != $rev_replies[$counter2 - 1]['reviewer_id']) {
                                                                $reviewer_counter2++;
                                                                //                                                        echo $reviewer_counter2+1;
                                                            }
                                                        }
                                                        ?>


                                                    </tr>
                                                <?php $counter2++;
                                                } ?>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-sm-6">
                                    <table class="table table-striped table-bordered table-sm mt-20">
                                        <tr class="text-white" style="background-color: #808080">
                                            <td class="text-center" colspan="4"> Review Comment Status</td>
                                        </tr>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Position </th>
                                            <th>Status</th>
                                        </tr>
                                        <tbody>
                                            <!-- docs2 GET REVIEWERS -->
                                            <?php
                                            $action_buttons = 'disabled';
                                            $c4 = 1;
                                            $current_reviewer_index = 1;
                                            ?>
                                            <?php foreach ($docs2 as $doc2) : ?>
                                                <?php $full_name =  $doc2['firstname'] . " " . $doc2['lastname']; ?>
                                                <tr class="text-center" colspan>
                                                    <?php
                                                    $color = '';
                                                    // ENABLE BUTTON IF STATUS IS EQUAL TO PENDING FOR ACTION
                                                    try {
                                                        $rev_status =  $doc2['review_status'];
                                                        $doc_status =  $document['status'];
                                                        $rev_id2  = $doc2['reviewer_id'];
                                                        $doc_id = $this_document_id;
                                                        $get_signed_file_qry = $conn->query("SELECT * FROM `tbl_document_reviewer_sign` WHERE `document_id` = $doc_id AND `user_id` = $rev_id2");
                                                        $get_signed_files = $get_signed_file_qry->fetchAll();
                                                        $get_final_doc_qry =  $conn->query("SELECT * FROM `tbl_document_response_files` WHERE `document_id` = $doc_id AND `response_id` = 0");
                                                        $get_final_doc = $get_final_doc_qry->fetchAll();
                                                        if ($doc2['reviewer_id'] == $session_emp_id) {
                                                            $current_reviewer_index = $c4 - 1;
                                                            if ($rev_status == "0") {
                                                                $action_buttons = '';
                                                                //                                                        echo "asdasdasds";
                                                            }
                                                        }
                                                        $badge = $rev_status;
                                                        // GET THE CURRENT REVIEWERS REVIEW STATUS
                                                        // CURRENT REVIEWER IS THE ONE GOING TO ACT ON THE DOCUMENT. WE NEED TO DISTINGUISH BECAUSE THERE ARE MULTIPLE REVIEWER IN ONE DOCUMENT
                                                        // WE NEED TO SET A VARIABLE'S STATUS IN ORDER TO SET THE STATUS COLOR IN THE DOCUMENT REVIEW STATUS CODE
                                                        if ($doc2['reviewer_id'] == $session_emp_id) {
                                                            $current_reviewer_status = $doc2['review_status'];
                                                        }
                                                    } catch (Exception $e) {
                                                        echo $e;
                                                    }
                                                    ?>
                                                    <td><?= $c4 ?></td>
                                                    <td><?= $full_name ?></td>
                                                    <td><?= $doc2['position'] ?></td>
                                                    <td><span class="<?php select_badge($badge); ?>"><?= display_status_value($rev_status) ?></span></td>
                                                    <?php $c4++; ?>
                                                </tr>
                                            <?php endforeach ?>
                                            <tr>
                                                <td class="text-center" colspan="2">Attached File</td>
                                                <td class="text-left" colspan="2">

                                                    <?php $file_counter = 1 ?>
                                                    <?php foreach ($get_signed_files as $signed_file) : ?>
                                                        <a href="assets/media/docs/<?= $signed_file['src'] ?>" data-toggle="tooltip" title="<?= $signed_file['src'] ?>"> <?= "FILE " . $file_counter ?></a><br>
                                                    <?php endforeach ?>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm-6">
                                    <table class="table table-striped table-bordered table-sm mt-20">
                                        <tr class="text-white" style="background-color: #808080">
                                            <td class="text-center" colspan="5"> Approval Comment Status</td>
                                        </tr>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Remarks</th>
                                            <th>STATUS</th>

                                        </tr>
                                        <tbody>
                                            <tr>
                                                <td><?= $document['approval_id'] ?></td>
                                                <td><?= $document['approval_name'] ?></td>
                                                <td><?= $document['approval_position'] ?></td>
                                                <td><?= $document['remarks'] ?></td>
                                                <td><span class="<?php select_badge($doc_status) ?>"><?= display_status_value($doc_status) ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center" colspan="2">Attached File</td>
                                                <td class="text-left" colspan="3">
                                                    <?php $file_counter = 1 ?>
                                                    <?php foreach ($get_final_doc as $final_doc) : ?>
                                                        <a href="assets/media/docs/<?= $final_doc['response_file_src'] ?>" data-toggle="tooltip" title="<?= $final_doc['response_file_src'] ?>"> <?= "FILE " . $file_counter ?></a><br>
                                                        <?php $file_counter = $file_counter + 1 ?>
                                                    <?php endforeach ?>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <?php
                                $previous_reviewer = $current_reviewer_index - 1;
                                if ($current_reviewer_index == "0") {
                                    // ACTION IS ENABLED
                                } else {
                                    if ($docs2[$previous_reviewer]['review_status'] == "0") {
                                        $action_buttons = "disabled";
                                    }
                                }
                                ?>
                                <!--                                DISABLE BUTTON IF COMMENTS ARE STILL UNREPLIED-->
                                <?php
                                $get_comment_status_qry = $conn->query("SELECT `response_status` FROM tbl_document_comments_replies WHERE document_id = $this_document AND reviewer_id = $session_emp_id AND response_status = 0 AND is_deleted = 0");
                                $get_comment_status = $get_comment_status_qry->fetchAll();
                                $get_comment_status_disabled = "";
                                if (!empty($get_comment_status)) { //

                                    $get_comment_status_disabled = "disabled";
                                    $action_buttons = $get_comment_status_disabled;
                                }
                                ?>
                                <!--    APPROVAL -->
                                <div class="col-sm-12">
                                    <table class="table table-borderless" id="action_table">
                                        <thead>
                                            <th class="text-center" colspan="3">Document Review Status Code</th>
                                        </thead>
                                        <tbody>
                                            <!--                                            for testing-->
                                            <?php
                                            $add_comment_button = '';
                                            if ($action_buttons == "disabled") {
                                                $add_comment_button  = "true";
                                            }
                                            //                                            $action_buttons = '' ;
                                            ?>
                                            <tr>
                                                <td class="col-sm-6  A bold-label"><button <?php echo $action_buttons ?> class="btn btn-success btn-block btn_actions col-sm-12" onclick="action_modal_show(1)"> Approve</button></button></td>
                                                <td class="col-sm-6 C bold-label"><button <?php echo $action_buttons ?> class="btn btn-danger btn-block btn_actions" onclick="action_modal_show(3)"> Fail/Not Approve</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>

                <!-- MODAL ACTION ON THE DOCUMENT REVIEW ACTION-->
                <link rel="stylesheet" href="assets/js/plugins/select2/css/select2.css">
                <div class="modal fade" id="add_comment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="add_comment_modal">ACTION</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="review_action">
                                <div class="modal-body">
                                    <!-- HIDDEN VARIABLES     -->
                                    <input type="text" hidden name="file_ids" id="file_ids" value="<?php echo $file; ?>">
                                    <input hidden type="text" value="<?= $file_loaded ?>" name="file_loaded" id="file_loaded">
                                    <input hidden type="text" value="" name="action_type" id="action_type">
                                    <input hidden type="text" value="<?= $originator_name ?>" name="originator_name" id="originator_name">
                                    <input hidden type="text" value="1" name="actor_type" id="actor_type">
                                    <input hidden type="text" value="<?= $folder_name3['folder_name'] ?>" name="review_folder_name" id="review_folder_name">
                                    <!--DOCUMENT DETAILS-->
                                    <input hidden type="text" name="file_new" id="file_new" value="<?= $file_new; ?>">

                                    <input hidden type="text" value="<?= $this_document_id ?>" name="doc_id" id="doc_id">
                                    <input hidden type="text" value="<?= $originator_id ?>" name="originator_id" id="originator_id">
                                    <input hidden type="text" value="<?= $session_emp_id ?>" name="rev_id" id="rev_id">
                                    <input hidden type="text" value="<?= $document['project_code'] ?>" name="project_code" id="project_code">
                                    <input hidden type="text" value="<?= $document['document_level'] ?>" name="document_level" id="document_level">
                                    <input hidden type="text" value="<?= $document['discipline'] ?>" name="discipline" id="discipline">
                                    <input hidden type="text" value="<?= $document['document_type'] ?>" name="document_type" id="document_type">
                                    <input hidden type="text" value="<?= $document['document_zone'] ?>" name="document_zone" id="document_zone">
                                    <input hidden type="text" value="<?= $document['date_uploaded'] ?>" name="date_uploaded" id="date_uploaded">
                                    <input hidden type="text" value="<?= $document['title'] ?>" name="doc_title" id="doc_title">
                                    <input hidden type="text" value="<?= $document['description'] ?>" name="description" id="description">
                                    <input hidden type="text" value="<?= $document['rev'] ?>" name="rev" id="rev">
                                    <input hidden type="text" value="<?= $approver_info['email'] ?>" name="approver_email" id="approver_email">
                                    <input hidden type="text" value="<?= $approver_info['firstname'] . " " . $approver_info['lastname'] ?>" name="approver_name" id="approver_name">
                                    <input hidden type="text" value="<?= $folder_id ?>" name="folder_id" id="folder_id">
                                    <input hidden type="text" value="<?= $comment_status ?>" name="comment_status" id="comment_status">
                                    <!--  ACTOR DEFINES WHETHER THE ACTION IS INITIATED BY REVIEWER OR APPROVAL PERSONNEL -->
                                    <br>
                                    <?php
                                    try {
                                        $user_sql = $conn->query("SELECT `employee_id`, `firstname`, `lastname`,`email`
                                                        FROM tbl_employees WHERE `is_deleted` = 0 ORDER BY firstname ASC");
                                        $user_qry = $user_sql->fetchAll();
                                    ?>
                                        <div class="form-group row">
                                            <label class="col-sm-3" for="select_approval">Add CC</label>
                                            <div class="col-sm-9">
                                                <select class="js-select2 " id="select_cc" name="select_cc[]" style="width: 100%;" data-placeholder="Choose at least one.." multiple>

                                                    <?php foreach ($user_qry as $emp1) : ?>
                                                        <?php $user_fullname = ucwords(strtolower($emp1['firstname'] . " " . $emp1['lastname'])); ?>

                                                        <option value="<?= $emp1['employee_id'] ?>"><?= $user_fullname ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3" for="manual_cc">Add CC Manually : </label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control form-control-lg text-sm-left" type="text" id="manual_cc" name="manual_cc">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3" for="approval_remarks">Add Remarks: </label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control form-control-lg text-sm-left" type="text" id="approval_remarks" name="approval_remarks" required>
                                            </div>
                                        </div>
                                        <div class="form-group row" id="div_signed_file">
                                            <label class="col-sm-3" for="signed_file">Add Signed File: </label>
                                            <div class="col-sm-9" id="signed_file2">

                                            </div>
                                        </div>
                                </div>
                                <!--  set button unclickable if status is set -->
                                <div class="modal-footer">
                                    <div class="col-sm-12 text-center">
                                        <button type="submit" class="btn btn-success action_rev" id="approve_document"> Save </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Modal Add Comment-->
                <!--            add reply modal-->
                <div class="modal fade" id="add_reply_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="add_comment_modal">Add Reply</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="reply_action">
                                <div class="modal-body">
                                    <!-- HIDDEN VARIABLES     -->
                                    <input type="number" name="response_id" hidden id="response_id" value="">

                                    <!-- HIDDEN VARIABLES     -->
                                    <div class="form-group row">
                                        <label class="col-sm-3" for="manual_cc2">Add CC Manually : </label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control form-control-lg text-sm-left" type="text" id="manual_cc" name="manual_cc" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3" for="select_approval">Add CC</label>
                                        <div class="col-sm-9">
                                            <select class="js-select2 " id="select_cc2" name="select_cc2[]" style="width: 100%;" data-placeholder="Choose at least one.." multiple required>
                                                <?php foreach ($user_qry as $emp1) : ?>
                                                    <?php $user_fullname = ucwords(strtolower($emp1['firstname'] . " " . $emp1['lastname'])); ?>
                                                    <option value="<?= $emp1['user_id'] ?>"><?= $user_fullname ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" id="comment div">
                                        <label class="col-sm-3" for="reply_code">Reply Code: </label>
                                        <div class="col-sm-9" id="reply_div">
                                            <select class="form-control" name="reply_code" id="reply_code" aria-label=".form-select-lg example">
                                                <option value="" disabled="disabled" selected="selected">Select Comment Code</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" id="comment div">
                                        <label class="col-sm-3" for="reply">Reply: </label>
                                        <div class="col-sm-9" id="reply_div">
                                            <input class="form-control form-control-lg text-sm-left" type="text" id="reply" name="reply">
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

                <div class="modal fade" id="edit_comment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="add_comment_modal">Edit Comments</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <!--                        edit_response_id  = key -->
                            <form id="edit_comment_action">
                                <div class="modal-body">
                                    <!-- HIDDEN VARIABLES     -->
                                    <input hidden type="text" value="<?= $document_id ?>" name="review_doc_id">
                                    <input hidden type="text" value="<?= $session_emp_id ?>" name="reviewer_id">
                                    <input hidden type="text" value="" name="edit_response_id" id="edit_response_id">
                                    <!-- HIDDEN VARIABLES     -->
                                    <div class="form-group row" id="comment div">
                                        <label class="col-sm-3" for="comment_code">Comment Code: </label>
                                        <div class="col-sm-9" id="comment_div">
                                            <select class="form-control" name="edit_comment_code" id="comment_code" aria-label=".form-select-lg example">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" id=" div">
                                        <label class="col-sm-3" for="reply" id="edit_comment">Comment: </label>
                                        <div class="col-sm-9" id="reply_div">
                                            <input class="form-control form-control-lg text-sm-left" type="text" id="edit_comment" name="edit_comment">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3" for="reply" id="edit_pages">Comment Page: </label>
                                        <div class="col-sm-9" id="reply_div">
                                            <input class="form-control form-control-lg text-sm-left" type="text" id="edit_pages" name="edit_pages">
                                        </div>
                                    </div>
                                </div>
                                <!--  set button unclickable if status is set -->
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-alt-success mr-5 mb-5" id="add_html_comment">
                                        <i class="fa fa-plus mr-5"></i>Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!--            ADD COMMENTS SUBMIT COMMENTS-->
                <div class="modal fade" id="add_comment_modal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="add_comment_modal">Add Comments</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>



                            <!-- key = reviewer_comment-->
                            <form id="comment_action">
                                <div class="modal-body">
                                    <!-- HIDDEN VARIABLES     -->
                                    <input hidden type="text" value="<?= $document_id ?>" name="review_doc_id">
                                    <input hidden type="text" value="<?= $session_emp_id ?>" name="reviewer_id">
                                    <input hidden type="number" name="folder_id4" id="folder_id4" value="<?= $folder_id ?>">
                                    <input hidden type="number" name="response_type" id="response_type" value="2">
                                    <input hidden type="text" name="document_infos[]" id="document_infos" value="">
                                    <input hidden type="number" name="response_type" id="response_type" value="2">
                                    <input hidden type="text" name="originator_email" id="originator_email" value="<?= $document['email'] ?>">
                                    <input hidden type="text" name="originator_name" id="originator_name" value="<?= $document['firstname'] . " " . $document['lastname'] ?>">
                                    <input hidden type="text" name="folder_id_comments" id="folder_id_comments" value="<?= $document['folder_id'] ?>">
                                    <input hidden type="text" name="folder_name_comments" id="folder_name_comments" value="<?= $folder_name3['folder_name'] ?>">
                                    <input hidden type="text" name="select_approval_id" id="select_approval_id" value="<?= $document['approval_id'] ?>">
                                    <input hidden type="text" name="file_new" id="file_new" value="<?= $file_new; ?>">

                                    <!-- HIDDEN VARIABLES     -->
                                    <div class="form-group row" id="comment div">
                                        <label class="col-sm-3" for="comment_code" i>Comment Code: </label>
                                        <div class="col-sm-9" id="comment_div">
                                            <select class="form-control" name="comment_code" onchange="enable_add_comment_button(1)" id="comment_code" aria-label=".form-select-lg example">
                                                <option value="" disabled="disabled" selected="selected">Select Comment Code</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" id=" div">
                                        <label class="col-sm-3" for="reply">Brief Comment: </label>
                                        <div class="col-sm-9" id="reply_div">
                                            <textarea class="form-control form-control-lg text-sm-left" type="text" id="reply" name="reviewer_comment" cols="6" rows="4"></textarea>
                                        </div>
                                    </div>
                                    <!--                                <div class="form-group row"  >-->
                                    <!--                                    <label class="col-sm-3" for="reply" id="comment_label" >Comment Page: </label>-->
                                    <!--                                    <div class="col-sm-9" id="reply_div">-->
                                    <!--                                        <input  class="form-control form-control-lg text-sm-left" type="text" id="pages" name = "pages">-->
                                    <!--                                    </div>-->
                                    <!--                                </div>-->
                                    <div class="form-group row">
                                        <label class="col-sm-3" for="select_approval">Comment Page</label>
                                        <div class="col-sm-9">
                                            <select class="js-select2 " id="pages" name="pages[]" style="width: 100%;" data-placeholder="Choose at least one.." multiple required>
                                                <?php for ($i = 1; $i <= 500; $i++) { ?>
                                                    <option value="<?= "page " . $i ?>"><?= "page " . $i ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" <?= $comment_exists ?>>
                                        <label class="col-sm-3" for="reply" id="comment_label">File with Comments: </label>
                                        <div class="col-sm-9" id="reply_div">
                                            <input class="form-control form-control-lg text-sm-left" multiple type="file" onchange="enable_add_comment_button(2)" id="revised_files" <?= $attachment_required ?> name="revised_files[]">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3" for="select_approval">Add CC</label>
                                        <div class="col-sm-9">
                                            <select class="js-select2 " id="select_cc4" name="select_cc4[]" style="width: 100%;" data-placeholder="Choose at least one.." multiple required>

                                                <?php foreach ($user_qry as $emp1) : ?>
                                                    <?php $user_fullname = ucwords(strtolower($emp1['firstname'] . " " . $emp1['lastname'])); ?>

                                                    <option value="<?= $emp1['employee_id'] ?>"><?= $user_fullname ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3" for="cc_comments_manual">Add CC Manually : </label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control form-control-lg text-sm-left" type="text" id="cc_comments_manual" name="cc_comments_manual" required>
                                        </div>
                                    </div>
                                    <!--  set button unclickable if status is set -->
                                    <div class="modal-footer">
                                        <button id="submit_comment" type="submit" disabled="disabled" class="btn btn-alt-success mr-5 mb-5 text-center">
                                            <i class="fa fa-plus mr-5"></i>Submit Comments
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
                                    } catch (Exception $e) {
                                        echo $e;
                                    }
                                }


        ?>

    </div>
    <!-- Page Content -->
    </main>

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

<!-- Page JS Helpers (Select2 plugin) -->
<script>
    jQuery(function() {
        Codebase.helpers('select2');
    });
</script>
<!-- Page JS Code -->
<script src="assets/js/pages/be_forms_validation.min.js"></script>
<script src="custom_js/document_file.js"></script>
<script src="assets/js/plugins/sweetalert2/sweetalert2.min.js"></script>

<script>
    $(document).ready(function() {
        $('textarea').each(function() {
            var textHeight = $(this).val();
            var text_lenght = textHeight.length + 30;
            this.setAttribute('style', 'height:' + (text_lenght) + 'px;overflow-y:hidden;');
        })

        $('#add_comments_btn').attr("hidden", <?php echo $add_comment_button ?>);
        $('#comment_action_column').attr("hidden", <?php echo $add_comment_button ?>);
        $('#action_table').attr("hidden", <?php echo $add_comment_button ?>);


    });


    var comment_code = 0;
    var revised_file = 0;

    function enable_add_comment_button(status) {
        // alert();
        var file = $("#revised_files").val();
        var comment_exists = "<?= $comment_exists ?>";
        if (status == 1) {
            comment_code = 1;
        } else if (status == 2) {
            if (file != '') {
                revised_file = 1;
            } else {
                revised_file = 0;
            }
        }
        if (comment_exists == "hidden") {
            revised_file = 1;
        }
        if (comment_code == 1 && revised_file == 1) {
            $('#submit_comment').attr("disabled", false);
        } else {
            $('#submit_comment').attr("disabled", true);
        }
    }
</script>


<?php
include 'includes/footer.php';
