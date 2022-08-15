<?php
require_once '../session.php';
date_default_timezone_set('Asia/Manila');

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';


?>


<?php

// }


//SHOW DOCUMENTS FOR APPROVAL END
//CREATE FOLDER
if (isset($_POST['folder_name'])) {

    $folder_name = $_POST['folder_name'];
    $date = date('Y-m-d H:i:s');
    try {
        $conn->beginTransaction();
        //1 = no 0 =yes
        $add_folder_qry = $conn->prepare("INSERT INTO 
                                            `tbl_folders`                                                
                                            (`folder_name`, 
                                                `date_created`, 
                                                `is_removed`, 
                                                `is_active`) 
                                        VALUES (?,?,?,?)");
        $add_folder_qry->execute([$folder_name, $date, 1, 0]);

        echo "Folder created Successfully";

        $conn->commit();
    } catch (\Exception $e) {
        $conn->rollBack();
        echo $e;
    }
}
/// END CREATE FOLDER

//REMOVE FOLDER
if (isset($_POST['remove_folder'])) {

    $folder_id = $_POST['folder_id'];
    $date = date('Y-m-d H:i:s');
    try {
        $conn->beginTransaction();
        //1 = no 0 =yes
        $remove_folder = $conn->query("UPDATE `tbl_folders` SET `is_removed` = 0   WHERE `folder_id` = $folder_id");

        echo "Folder Edited Successfully";

        $conn->commit();
    } catch (\Exception $e) {
        $conn->rollBack();
        echo $e;
    }
}
/// END REMOVE FOLDER

//EDIT FOLDER
if (isset($_POST['folder_id_edit'])) {

    $folder_id = $_POST['folder_id_edit'];
    $folder_name_edit = $_POST['folder_name_edit'];
    $date = date('Y-m-d H:i:s');
    $conn->beginTransaction();
    try {
        //1 = no 0 =yes
        // $edit_folder = $conn->query("UPDATE `tbl_folders` SET `folder_name` = ".$folder_name_edit."  WHERE `folder_id` = $folder_id");
        $update_folder = $conn->prepare("UPDATE `tbl_folders` SET `folder_name` = ?  WHERE `folder_id` = ?");
        $update_folder->execute([$folder_name_edit, $folder_id]);


        echo "Folder Removed Successfully";

        $conn->commit();
    } catch (\Exception $e) {
        $conn->rollBack();
        echo $e;
    }
}
/// END EDIT FOLDER
//ADD FILE FORM WILL GO HERE
if (isset($_POST['add_document_file'])) {
    try {
        $conn->beginTransaction();
        require_once '../send_mail.php';
        $manual_cc = $_POST['manual_cc'];
        $select_approval = $_POST['select_approval'];
        // get reviewer emails //
        $select_reviewer = $_POST['select_reviewer']; // USER ID
        // get reviewer name
        $reviewer_emails = array();
        $reviewer_assigned = array();
        // store all email in array then pass it to send_mail()
        foreach ($select_reviewer as $reviewer_id) {
            $reviewer = $conn->query("SELECT * FROM tbl_employees  WHERE `is_deleted` = 0 AND employee_id = $reviewer_id");
            $reviewer_info = $reviewer->fetch();
            $email = $reviewer_info['email'];
            $full_name =  $reviewer_info['firstname'] . " " . $reviewer_info['lastname'];
            $reviewer_emails[] = $email;
            $reviewer_assigned[] = $full_name;
        }
        // get reviewer emails //
        //get approval name
        $approver = $conn->query("SELECT * FROM tbl_employees  WHERE `is_deleted` = 0 AND employee_id = $select_approval");
        $approver_info = $approver->fetch();
        $cc_id =  $_POST['select_cc'];
        $cc2_emails = array();
        // store all email in array then pass it to send_mail()
        if ($cc_id != '') {
            foreach ($cc_id as $id) {
                $cc_qry = $conn->query("SELECT *,users.email AS email2 FROM users 
                                                     INNER JOIN tbl_employees ON users.emp_id = tbl_employees.employee_id
                                                     WHERE `deleted` = 0 AND emp_id = $id");
                $cc_info = $cc_qry->fetch();
                $email = $cc_info['email2'];
                $cc2_emails[] = $email;
            }
        }
        $folder_name = $_POST['folder_name2'];
        $project_code = $_POST['project_code1'];
        $discipline = $_POST['discipline'];
        $originator2 = $_POST['originator'];
        $document_type = $_POST['type'];
        $document_zone = $_POST['zone'];
        $document_level = $_POST['level'];
        //        $rev = $_POST['rev'];
        $originator = $_POST['originator'];
        $sequence_no = $_POST['sequence_no'];
        //    $select_approval = $_POST['select_approval']; // USER ID
        $date_now = date('Y-m-d H:i:s');
        $folder_id = $_POST['folder_id'];
        // $to =  $reviewer_emails;
        $cc1_email = $approver_info['email'];
        //CC1 IS APPROVER EMAIL

        $title = $_POST['title'];
        $description = $_POST['description'];
        $date_uploaded = $date_now;

        // STITCH NEW FILE NAME BY COMBINING THE DATA BELOW
        $approval_assigned = $approver_info['firstname'] . " " . $approver_info['lastname'];
        $file_no =  $_POST['project_code1'] . "-" .
            $originator . "-" .
            $discipline . "-" .
            $document_type . "-" .
            $document_zone . "-" .
            $document_level . "-" .
            $sequence_no;

        $new_file_no = array();
        $pieces = explode("-", $file_no);
        $connector = "-";
        $count = 1;
        foreach ($pieces as $piece) {
            array_push($new_file_no, $piece);
        }
        $file_new_name = "";
        foreach ($new_file_no as $item) {
            if ($count == count($new_file_no)) {
                $connector = "";
            }
            if (strtolower($item) != "n/a") {
                $file_new_name = $file_new_name . $item . $connector;
            }
            $count++;
        }
        $i =  strlen($file_new_name);
        $date1 = date("His"); // for unique file name
        $fileNames = $_FILES['files']['name'];

        if (!empty($fileNames)) {
            $add_document_qry = $conn->prepare("INSERT INTO `tbl_documents`( 
                                                                `description`, 
                                                                `user_id`,  
                                                                `approval_id`, 
                                                                `date_uploaded`, 
                                                                `status`,
                                                                `title`,
                                                                `folder_id`,   
                                                                `project_code`,
                                                                 `discipline`,
                                                                `document_type`,
                                                                `document_zone`,
                                                                `document_level`,
                                                                `is_deleted`,
                                                                `originator`,
                                                                `sequence_no`,
                                                                `originator2`
                                                                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $add_document_qry->execute([
                $description,
                $session_emp_id,
                $select_approval,
                $date_now,
                0,
                $title,
                $folder_id,
                $project_code,
                $discipline,
                $document_type,
                $document_zone,
                $document_level,
                0,
                $originator,
                $sequence_no,
                $originator2
            ]);

            $document_id = $conn->lastInsertId();
            $get_files_qry = $conn->query("SELECT `src` FROM `tbl_files` WHERE `document_id` = $document_id");
            $get_file_names = $get_files_qry->fetchAll();
            $new_file_names = array();
            foreach ($_FILES['files']['name'] as $key => $val) {
                $fileName = basename($_FILES['files']['name'][$key]);
                $unique_file_name = $date1 . '-' . $fileName;
                $new_file_names[] = $unique_file_name;
                // File upload configuration
                $targetDir = "../assets/media/docs/" . $unique_file_name;
                // get the file extension
                $extension = pathinfo($unique_file_name, PATHINFO_EXTENSION);
                // the physical file on a temporary uploads directory on the server
                $file = $_FILES['files']['tmp_name'][$key];
                $size = $_FILES['files']['size'];
                if (!in_array($extension, ['zip', 'pdf', 'docx', 'xlsx'])) {
                    echo "your file extension must be .zip, .pdf, .docx, 'xlsx";
                    $conn->rollBack();

                    // } elseif ($size > 1000000) { // file shouldn't be larger than 1Megabyte
                    //     echo "File too large!";
                } else {
                    // move the uploaded (temporary) file to the specified destination
                    if (move_uploaded_file($file, $targetDir)) {
                        $add_file_qry = $conn->prepare("INSERT INTO `tbl_files`( 
                                                                `document_id`, 
                                                                `user_id`,  
                                                                `src`,
                                                                `uploader_type`,
                                                                `folder_id`,
                                                                `is_deleted`                                                            
                                                                
                                                                ) VALUES (?,?,?,?,?,?)");
                        $add_file_qry->execute([$document_id, $session_emp_id, $unique_file_name, 1, $folder_id, 0]);

                        $reviewer_comments_qry = $conn->query("SELECT *
                            FROM `tbl_document_comments_replies`
                            INNER JOIN users ON tbl_document_comments_replies.reviewer_id = users.user_id                                         
                            WHERE tbl_document_comments_replies.document_id = $document_id 
                            ORDER BY document_id DESC");
                        $rev_comments =  $reviewer_comments_qry->fetchAll();

                        foreach ($select_reviewer as $reviewer) {
                            $add_reviewer = $conn->prepare("INSERT INTO `tbl_document_reviewer`(
                                                                    `document_id`,
                                                                    `originator_id`,
                                                                    `reviewer_id`,
                                                                    `review_status`,
                                                                    `folder_id`,
                                                                    `is_deleted`
                                                                    ) 
                                                                VALUES (?,?,?,?,?,?)");
                            $add_reviewer->execute([$document_id, $session_emp_id, $reviewer, 0, $folder_id, 0]);
                        }
                        //SAVE EACH REVIEWER FOR THE DOCUMENT IN THE TBL_DOCUMENT_REVIEWER


                        $get_subscriber_qry = $conn->query("SELECT `employee_id`,`access_name` from tbl_employees INNER JOIN `tbl_subscribers` WHERE `employee_id` = $session_emp_id");
                        $subscriber_qry = $get_subscriber_qry->fetch();
                        $subscriber = $subscriber_qry['access_name'];

                        $status = "PENDING";
                        //                            print_r($reviewer_emails);
                        //                            echo  $cc1_email;
                        //                            print_r($cc2_emails);
                        //                            $reviewer_emails  = ["adrianpajaro12@gmail.com","hse.manager@fiafigroup.com"];
                        //                            $cc1_email = "hse.manager@fiafigroup.com";
                        //                            $cc2_emails =["hse.manager@fiafigroup.com","adrian.pajaro@jmc.edu.ph"];
                        //                            print_r($reviewer_assigned);
                        //                            print_r($reviewer_emails);
                        echo $manual_cc;
                        send_mail(
                            $reviewer_emails,
                            $cc1_email,
                            $cc2_emails,
                            $manual_cc,
                            $folder_name,
                            $title,
                            $file_new_name,
                            $date_uploaded,
                            $reviewer_assigned,
                            $approval_assigned,
                            $status,
                            $folder_id,
                            $subscriber,
                            $new_file_names
                        );

                        $conn->commit();

                        echo "Successfully added a new file for review.";
                        echo "Mail Sent";
                    } else {
                        echo "failed";
                    }
                    //SAVE EACH REVIEWER FOR THE DOCUMENT IN THE TBL_DOCUMENT_REVIEWER
                }
            } //foreach

        } // if empty
    } catch (Exception $e) {
        //        $conn->rollBack();
        echo $e;
    }
}


// SHOW SUBMITTED DOCUMENTS

if (isset($_POST['load_submitted_documents'])) {

    $folder_id = $_POST['folder_id'];
    $document_id = $_POST['document_id'];
    $get_submitted_documents = $conn->query("SELECT `document_id`,
                                            `src`,
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
                                            `sequence_no`,                                            
                                            tbl_documents.status,
                                            `lastname`,
                                            `originator2`,
                                            `remarks`,
                                        FROM `tbl_documents`
                                        INNER JOIN tbl_employees ON tbl_document_reviewer.reviewer_id = tbl_employees.employee_id
                                        INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id
                                        WHERE tbl_documents.user_id=$session_emp_id AND
                                        folder_id = $folder_id
                                        ORDER BY document_id DESC");
    $document =  $get_submitted_documents->fetch();

    $get_document_reviewers = $conn->query("SELECT 
                                                `review_document_id`,
                                                `reviewer_id`,
                                                `document_id`,
                                                `review_status`,
                                                `folder_id`
                                                `firstname`,
                                                `lastname`,
                                                tbl_position.position                                                
                                                FROM tbl_document_reviewer                                                      
                                                INNER JOIN tbl_employees ON tbl_document_reviewer.reviewer_id = tbl_employees.employee_id
                                                INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id
                                                WHERE document_id = $document_id");

    $docs2 = $get_document_reviewers->fetchAll();

    $approval_status_qry = $conn->query("SELECT `document_id`,
                                          `src`,
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
                                          `remarks`,
                                          tbl_documents.status,
                                          `lastname`
                                      FROM `tbl_documents`
                                      INNER JOIN users ON tbl_documents.approval_id = users.user_id
                                      WHERE tbl_documents.document_id = document_id  AND
                                      folder_id = $folder_id
                                        ORDER BY document_id DESC");

    $docs3 =  $approval_status_qry->fetch();
?>

    <style type="text/css">
        thead,
        th,
        label {
            font-weight: bolder;

        }

        <?php echo '.' . $document['status'] . '{
//                          background-color: green;
                        color:  white;
                    }' ?>.bold-label {
            font-weight: bolder;
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered">
                <thead>
                    <!--                                    <th class="bold-label">Project Code</th>-->
                    <th class="bold-label">Project Code</th>
                    <th class="bold-label">Sequence No.</th>
                    <th class="bold-label">Originator</th>
                    <th class="bold-label">Discipline</th>
                    <th class="bold-label">Type</th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <p><?= $document['project_code'] ?>
                            <p>
                        </td>
                        <td>
                            <p><?= $document['Sequence'] ?>
                            <p>
                        </td>
                        <td>
                            <p><?= $document['user_id'] ?>
                            <p>
                        </td>
                        <td>
                            <p><?= $document['discipline'] ?>
                            <p>
                        </td>
                        <td>
                            <p><?= $document['document_type'] ?>
                            <p>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold-label"> Zone </td>
                        <td class="bold-label">Transmittal Date </td>
                        <td class="bold-label"> Document Title </td>
                        <td class="bold-label"> Attached File </td>
                        <td class="bold-label">Final Status </td>
                    </tr>
                    <tr>
                        <td>
                            <p><?= $document['document_zone'] ?>
                            <p>
                        </td>
                        <td>
                            <p><?= $document['date_uploaded'] ?>
                            <p>
                        </td>
                        <td>
                            <p><?= $document['title'] ?>
                            <p>
                        </td>
                        <td>
                            <?php
                            if ($document['src'] == "") {
                            ?>
                                }
                                ?>
                                <?= $document['src'] ?><br>
                        </td>
                        <td>
                            <?php if ($document['status'] == "0") : ?>
                                <p><?= 'PENDING ACTION' ?></p>
                            <?php else : ?>
                                <p><?= $document['status'] ?></p>
                            <?php endif ?>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
        <div class="col-sm-12">
            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <th>No.</th>
                        <th>Initial</th>
                        <th>Page/Section</th>
                        <th>Reviewers Comments</th>
                        <th> </th>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>

                        <?php foreach ($rev_comments as $rev_comments) : ?>
                            <?php
                                    $first_name = $rev_comments['firstname'];
                                    $last_name = $rev_comments['lastname']
                            ?>
                            <tr>
                                <td><?= $counter ?></td>
                                <td><?= $first_name . " " . $last_name ?></td>
                                <td><?= $rev_comments['pages'] ?></td>
                                <td></td>
                                <td><?= $rev_comments['comment'] ?></td>
                            </tr>
                            <?php $counter++; ?>
                        <?php endforeach ?> <?php $counter = 1; ?>
                        <?php foreach ($rev_comments as $rev_comments) : ?>
                            <?php
                                    $first_name = $rev_comments['firstname'];
                                    $last_name = $rev_comments['lastname']
                            ?>
                            <tr>
                                <td><?= $counter ?></td>
                                <td><?= $first_name . " " . $last_name ?></td>
                                <td><?= $rev_comments['pages'] ?></td>
                                <td><?= $rev_comments['response_id'] ?></td>
                                <td><?= $rev_comments['comment'] ?></td>
                            </tr>
                            <?php $counter++; ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <th>Reply Code</th>
                        <th>Originator</th>
                        <th>Page/Section</th>
                        <th>REVIEWER REPLY</th>
                        <th>
                            <!-- <button class="btn-outline-success">
                                                Add
                                            </button> -->
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="2">1</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td rowspan="2">2</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>

                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row">
                <table class="table table-bordered">
                    <thead>
                        <th class="text-center" colspan="3">Document Review Status Code</th>

                    </thead>
                    <tbody>
                        <tr>
                            <td class="A">A. Approved with comments</td>
                            <td class="B">B. SONO</td>
                            <td class="C">C. Fail/Not Approved</td>
                        </tr>
                        <tr>
                            <td class="D">D. Approved with Comments</td>
                            <td class="E">E. NOWC : No Objection with Comments.</td>
                            <td class="F">F. Responded/
                                Reviewed/
                                Actioned
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="col-sm-12">
                <table class="table table-bordered">
                    <thead>
                        <th class="text-center" colspan="4">Contractors Review Comment Status</th>
                        <th class="text-center" colspan="4"> APPROVAL COMMENT STATUS</th>
                    </thead>
                    <thead>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Position </th>
                        <th>Status</th>
                    </thead>
                    <tbody>
                        <?php foreach ($docs2 as $doc2) : ?>
                            <?php $full_name =  $doc2['firstname'] . " " . $doc2['middlename'] . " " . $doc2['lastname'] ?>
                            <tr>
                                <?php
                                    $color = '';
                                    $rev_status =  $doc2['review_status'];
                                    if ($rev_status == "A") {
                                        $color =  "badge badge-success";
                                    } elseif ($rev_status == "B") {
                                        $color =  "badge badge-info";
                                    } elseif ($rev_status == "C") {
                                        $color =  "badge badge-danger";
                                    } elseif ($rev_status == "D") {
                                        $color =  "badge badge-secondary";
                                    } elseif ($rev_status == "E") {
                                        $color =  "badge badge-warning";
                                    } elseif ($rev_status == "F") {
                                        $color =  "bg-warning text-dark";
                                    }
                                ?>
                                <td><?= $doc2['review_document_id'] ?></td>
                                <td><?= $full_name ?></td>
                                <td><?= $doc2['position'] ?></td>
                                <td class="<?= $color ?>"><span class=""><?= $doc2['review_status'] ?></span></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <br>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-bordered">
                <thead>
                    <th class="text-center" colspan="4">Contractors Approval Status</th>
                </thead>
                <thead>
                    <th>Name</th>
                    <th>Position </th>
                    <th>Remarks</th>
                    <th>Status</th>
                </thead>
                <tbody>
                    <tr>
                        <?php
                                $approval_fullname = $docs3['firstname'] . " " . $docs3['middlename'] . " " . $docs3['lastname'];
                                $color2 = '';
                                $approval_status = $docs3['status'];
                                if ($approval_status == "A") {
                                    $color =  "badge badge-success";
                                } elseif ($rev_status == "B") {
                                    $color =  "badge badge-info";
                                } elseif ($rev_status == "C") {
                                    $color =  "badge badge-danger";
                                } elseif ($rev_status == "D") {
                                    $color =  "badge badge-secondary";
                                } elseif ($rev_status == "E") {
                                    $color =  "badge badge-warning";
                                } elseif ($rev_status == "F") {
                                    $color =  "bg-warning text-dark";
                                }
                        ?>
                        <th><?= $approval_fullname ?></th>
                        <td><?php

                                if ($docs3['remarks'] == "") {
                                    echo "NONE";
                                } else {
                                    echo $docs3['remarks'];
                                }
                            ?>

                        <td style="background-color: <?= $color2; ?>"><?= $docs3['status'] ?></td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <div class="row">

    </div>

    <!-- // SHOW SUBMITTED DOCUMENTS -->


    <!-- SHOW DOCUMENTS FOR REVIEW -->

<?php
                            }
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

                        if (isset($_POST['load_documents_for_review'])) {
                            try {
                                $table_appearance = $_POST['table_appearance'];
                                $action_disable = $_POST['action_disable'];   //  2 WILL HIDE THE ACTION( IT IS NOT NEEDED FOR REVIEWAL AND APPROVAL AND DOCUMENT CONTROL)
                                //        $action_display = "";
                                if ($action_disable == 2) {
                                    $action_display = "display:none;";
                                }
                                $file_new = $_POST['file_new'];
                                $folder_id = $_POST['folder_id'];
                                $document_id = $_POST['document_id'];
                                $get_submitted_documents = $conn->query("SELECT `document_id`,
                                            `title`,                                             
                                            `description`,
                                            tbl_documents.user_id,
                                            `approval_id`, 
                                            `date_uploaded`,
                                            tbl_documents.status,
                                            tbl_position.position,
                                            `project_code`,
                                            `document_type`,
                                            `document_zone`,
                                            `document_level`,
                                            tbl_documents.user_id AS submitter_id,                                          
                                            `discipline`,                                          
                                            `sequence_no`,
                                             `firstname`,
                                            `lastname`,
                                            `remarks`,
                                            `originator2`,
                                            `src`,
                                             (SELECT COUNT(response_file_id) FROM `tbl_document_response_files` WHERE `document_id` = $document_id AND `response_type` = 1 AND `is_deleted` = 0 GROUP BY `document_id`) AS revisions,
                                            (SELECT tbl_position.position from tbl_employees  INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id WHERE tbl_employees.employee_id = tbl_documents.approval_id)   AS approval_position,
                                            (SELECT tbl_position.position from tbl_employees  INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id WHERE tbl_employees.employee_id = submitter_id)  AS submitter_position,
                                            (SELECT  CONCAT(firstname, ' ', lastname)from tbl_employees WHERE tbl_employees.employee_id = tbl_documents.approval_id)   AS approval_name    
                                        FROM `tbl_documents`
                                        INNER JOIN tbl_employees ON tbl_documents.user_id = tbl_employees.employee_id
                                        INNER JOIN  tbl_position ON tbl_employees.position = tbl_position.position_id    
                                        WHERE document_id = $document_id
                                        ORDER BY document_id DESC");

                                $document =  $get_submitted_documents->fetch();
                                //GET SUBMITTED DOCUMENTS

                                $originator_id = $document['user_id'];


                                //GET REVIEWERS DOCUMENTS - DISPLAY ORIGINAL DOCUMENTS IF REVIEWERS HAS YET TO UPLOAD DOCUMENNTS
                                // CHECK FOR ANY DOCUMENTS UPLOADED OTHER THAN THE ORIGINATOR
                                $check_check_files = $conn->query("SELECT `document_id` FROM tbl_files WHERE `document_id` = $document_id AND `user_id` != $originator_id");
                                $check_files = $check_check_files->fetchAll();
                                $row_count =  count($check_files);
                                $attached_files = '';
                                $file_loaded  = ""; //  NEED TO SELECT WHETHER TO ADD NEW RECORD OR JUST OVER RIDE THE FILE UPLOADED BY OTHER REVIEWERS
                                $originator_id;
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

                                //GET REVIEWERS DOCUMENTS
                                $get_document_reviewers = $conn->query("SELECT 
                                                `review_document_id`,
                                                `reviewer_id`,
                                                `document_id`,
                                                `review_status`,
                                                `folder_id`,
                                                `review_status`,                                           
                                                `lastname`,
                                                `firstname`,
                                                tbl_position.position
                                                FROM tbl_document_reviewer  
                                                INNER JOIN tbl_employees ON tbl_document_reviewer.reviewer_id = tbl_employees.employee_id
                                                INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id
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
                           INNER JOIN users ON tbl_documents.approval_id = users.emp_id
                           WHERE tbl_documents.document_id = $document_id  AND
                           folder_id = $folder_id
                             ORDER BY document_id DESC");
                                $docs3 =  $approval_status_qry->fetch();
                                //GET APPROVAL STATUS
                                //GET REVIEWER COMMENTS
                                $reviewer_comments_qry = $conn->query("SELECT `document_id`,
                                                        `response_id`,
                                                        `comment`,
                                                        `pages`,
                                                        `reviewer_id`,
                                                        `reply`
                                                        `firstname`,
                                                        `lastname`,           
                                                        `comment_code`                                            
                                                FROM `tbl_document_comments_replies`
                                                INNER JOIN users ON tbl_document_comments_replies.reviewer_id = users.user_id                                         
                                                WHERE tbl_document_comments_replies.document_id = $document_id  AND
                                                `reviewer_id` = $session_emp_id AND is_deleted = 0
                                                
                                                ORDER BY document_id DESC");
                                $rev_comments =  $reviewer_comments_qry->fetchAll();
                                // GET REPLIES
                                $get_replies_qry = $conn->query("SELECT *                                            
                                            FROM `tbl_document_comments_replies`
                                            INNER JOIN users ON tbl_document_comments_replies.reviewer_id = users.emp_id                                         
                                            WHERE tbl_document_comments_replies.document_id = $document_id AND is_deleted = 0
                                            ORDER BY document_id DESC");
                                $rev_replies =  $get_replies_qry->fetchAll();

                                //GET REVIEWER COMMENTS
                                $current_reviewer_status = '';
                                $set_status_color = '';

                                //    COUNT HOW MANY REVISION WAS SUBMITTED BY GETTINGTHE NUMBER OF UNREPLIED COMMENTS
                                $count_revision_qry = $conn->query("SELECT COUNT(*) as number  FROM `tbl_document_comments_replies` WHERE `response_status` = 1 AND `document_id` = $document_id");
                                $count_revision = $count_revision_qry->fetch();
                                $count_rev =  $count_revision['number'] + 1;

                                // SET COLOR  DOCUMENT REVIEW STATUS CODE
                                if ($current_reviewer_status == "A") {
                                    $set_status_color = 'red';
                                } elseif ($current_reviewer_status == "B") {
                                    $set_status_color = 'black';
                                } elseif ($current_reviewer_status == "C") {
                                    $set_status_color = 'blue';
                                } elseif ($current_reviewer_status == "D") {
                                    $set_status_color = 'green';
                                }
?>
    <style type="text/css">
        thead,
        th,
        label {
            font-weight: bolder;

        }

        textarea {
            /*resize: none;*/
            overflow-y: hidden;
        }

        <?php echo '.' . $document['status'] . '{
                         background-color: green;
                        color:  white;
                    }' ?>.bold-label {
            font-weight: bolder;
        }
    </style>
    <div class="container ">
        <div class="content">
            <h2 class="content-heading"><?php  ?></h2>
            <!-- DIV BLOCK  -->
            <div class="block">
                <div class="block-header block-header-default bg-pattern  text-white" style="background-color: #808080">
                    <p>Document No. <?= $this_document = $document['document_id'] ?></p>
                </div>
            </div>
            <!-- DIV BLOCK  -->
            <div class="container" id="background">
                <div class="form-group">
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-striped table-borderless table-sm mt-20  text-center">
                            <tbody class="text-left">
                                <tr>
                                    <td class="bold-label text-right">Proj. Code </td>
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
                                    <td><?php echo $document['submitter_position'] ?></td>
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
                    <!--                    SUBMITTER MODAL-->
                    <div class="col-sm-12">
                        <table class="table table-striped table-borderless table-sm mt-20  text-center">
                            <thead class="text-center">
                                <tr class="text-left text-white">
                                    <td colspan="7" style="background-color: #808080"> Reviewer Comment Code Legend :
                                    </td>
                                </tr>
                                <tr class="text-left" style="background-color: #F6F7F9;">
                                    <td colspan="7">1 = action required on this issue 2= advisory comment</td>
                                </tr>
                                <th>No.</th>
                                <th>Initial</th>
                                <th> Comment Page</th>
                                <th>RCC</th>
                                <th>Reviewer's Comments</th>
                                <th> Date</th>
                                <th>File</th>
                            </thead>

                            </thead>
                            <tbody>
                                <?php $counter = 0;
                                $reviewer_counter = 0; ?>
                                <?php foreach ($rev_replies as $rev_comment) : ?>
                                    <?php
                                    $first_name = $rev_comment['firstname'];
                                    $last_name = $rev_comment['lastname']
                                    ?>
                                    <tr>
                                        <?php
                                        $current_rev = $rev_replies[$counter]['reviewer_id'];
                                        if ($counter == 0) {
                                        ?>
                                            <td>
                                                <?= $reviewer_counter + 1; ?>
                                            </td>
                                            <td class="text-center"><?= $first_name . " " . $last_name ?></td>
                                            <td class="text-left">
                                                <?php
                                                $pages = explode(',', $rev_comment['pages']);
                                                for ($i = 1; $i < count($pages);) {
                                                    echo $pages[$i] . "<br>";
                                                    $i++;
                                                } ?>
                                            </td>
                                            <td class="text-center"><?= $rev_comment['comment_code'] ?></td>
                                            <td class="text-center">
                                                <textarea class="form-control form-control-lg text-sm-left" type="text" readonly>  <?= $rev_comment['comment'] ?></textarea>
                                            </td>
                                            <td class="text-center"><?= $rev_comment['comment_date'] ?></td>
                                            <td>
                                                <?php
                                                $rev_id = $rev_comment['reviewer_id'];
                                                $resp_id =  $rev_comment['response_id'];
                                                $get_comment_files_qry = $conn->query("SELECT `response_file_src` FROM tbl_document_response_files WHERE response_id = $resp_id AND uploader_id  = $rev_id");
                                                $get_comment_files = $get_comment_files_qry->fetchAll();

                                                ?>
                                                <?php $file_counter = 1; ?>
                                                <?php foreach ($get_comment_files as $item) : ?>
                                                    <a href="assets/media/docs/<?= $item['response_file_src'] ?>" target="_blank" data-toggle="tooltip" title="View Files" data-original-title="View Files">
                                                        <?= "FILE " . $file_counter ?></a></br>
                                                    <?php $file_counter++; ?>
                                                <?php endforeach ?>
                                            </td>
                                        <?php
                                        } else {
                                        ?>
                                            <td>
                                            </td>
                                            <td class="text-center"></td>
                                            <td class="text-left">
                                                <?php
                                                $pages = explode(',', $rev_comment['pages']);
                                                for ($i = 1; $i < count($pages);) {
                                                    echo $pages[$i] . "<br>";
                                                    $i++;
                                                } ?>
                                            </td>
                                            <td class="text-center"><?= $rev_comment['comment_code'] ?></td>
                                            <td class="text-center">
                                                <textarea class="form-control form-control-lg text-sm-left" type="text" readonly>  <?= $rev_comment['comment'] ?></textarea>
                                            </td>
                                            <td></td>
                                            <td></td>
                                        <?php
                                            if ($current_rev != $rev_replies[$counter - 1]['reviewer_id']) {
                                                $reviewer_counter++;
                                                //                                                        echo $reviewer_counter+1;
                                            }
                                        }
                                        ?>

                                    </tr>
                                    <?php $counter++; ?>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                                $hide_action = "";
                                if ($document['status'] != "0") {
                                    $hide_action = "hidden";
                                }
                    ?>
                    <div class="col-sm-12">
                        <table class="table table-striped table-borderless table-sm mt-20  text-center">
                            <thead class="text-center">
                                <tr class="text-left text-white" style="background-color: #808080">
                                    <td colspan="8" style="background-color: #808080"> Originator Reply Code Legend :
                                    </td>
                                </tr>
                                <tr class="text-left" style="background-color: #F6F7F9">
                                    <td colspan="8"> i = Incorporated ii= Evaluated and not incorporated for reason stated</td>
                                </tr>
                                <th>No.</th>
                                <th>Reviewer</th>
                                <th>Comment Page</th>
                                <th>OCC</th>
                                <th>Originator REPLY</th>
                                <th> REPLY DATE</th>
                                <!--                                            attached reply files-->
                                <th> Files</th>
                                <th style="<?= $action_display ?>" <?= $hide_action ?>>ACTION</th>
                            </thead>
                            <tbody>

                                <?php $counter2 = 0;
                                $reviewer_counter2 = 0; ?>
                                <?php foreach ($rev_replies as $rev_rep) : ?>
                                    <?php
                                    $first_name = $rev_rep['firstname'];
                                    $last_name = $rev_rep['lastname'];
                                    $response_id  = $rev_rep['response_id'];
                                    $document_id  = $rev_rep['document_id'];
                                    $reply_reviewer_id = $rev_rep['reviewer_id'];

                                    ?>

                                    <tr>
                                        <?php
                                        $reply_exists = "";
                                        $attachment_required = 1; // attachment required
                                        echo ($attachment_required);
                                        if (count($get_comment_files) == 0) {
                                            $reply_exists = "hidden";
                                            $attachment_required = 0; // attachment not required
                                        }

                                        $current_rev2 = $rev_replies[$counter2]['reviewer_id'];
                                        if ($counter2 == 0) { ?>
                                            <td><?= $reviewer_counter2 + 1; ?></td>
                                            <td><?= $first_name . " " . $last_name ?></td>
                                            <td class="text-left">
                                                <?php
                                                $pages = explode(',', $rev_rep['pages']);
                                                for ($i = 1; $i < count($pages);) {
                                                    echo $pages[$i] . "<br>";
                                                    $i++;
                                                }
                                                ?>
                                            </td>
                                            <td><?= $rev_rep['reply_code'] ?></td>
                                            <td>
                                                <?php if ($rev_rep['reply'] != '') {  ?>
                                                    <textarea style="resize:none;" class="form-control form-control-lg text-sm-left" readonly type="text" cols="6" rows="4"><?= $rev_rep['reply'] ?> </textarea>
                                                <?php } ?>
                                            </td>
                                            <td><?= $rev_rep['reply_date'] ?></td>
                                            <?php
                                            $rev_id = $rev_rep['reviewer_id'];
                                            $resp_id =  $rev_rep['response_id'];
                                            $get_comment_files_qry = $conn->query("SELECT `response_file_src` 
                                                                                                FROM tbl_document_response_files
                                                                                                WHERE response_id = $resp_id AND document_id = $document_id AND response_type = 1");
                                            $get_comment_files = $get_comment_files_qry->fetchAll();
                                            ?>
                                            <td class="text-left">
                                                <?php $file_counter = 1 ?>
                                                <?php foreach ($get_comment_files as $item) : ?>
                                                    <a href="assets/media/docs/<?= $item['response_file_src'] ?>" data-toggle="tooltip" title="<?= $item['response_file_src'] ?>"> <?= "FILE " . $file_counter ?></a><br>
                                                    <?php $file_counter = $file_counter + 1 ?>
                                                <?php endforeach ?>
                                            </td>
                                            <td class="text-center" style="<?= $action_display ?>" <?= $hide_action ?>>
                                                <?php
                                                if ($table_appearance == 0) {
                                                    if (is_null($rev_rep['reply'])) {
                                                ?>
                                                        <button type="button" class="btn btn-success" onclick="add_new_reply(<?= $response_id ?>,<?= $document_id ?>,<?= $reply_reviewer_id ?>,'<?= $file_new ?>','<?= $count_rev ?>',<?= $attachment_required ?>)">Add Reply</button>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </td>
                                        <?php

                                        } else {
                                        ?>
                                            <td></td>
                                            <td></td>
                                            <td class="text-left">
                                                <?php
                                                $pages = explode(',', $rev_rep['pages']);
                                                for ($i = 1; $i < count($pages);) {
                                                    echo $pages[$i] . "<br>";
                                                    $i++;
                                                } ?>
                                            </td>
                                            <td><?= $rev_rep['reply_code'] ?></td>
                                            <td>
                                                <?php if ($rev_rep['reply'] != '') {  ?>
                                                    <textarea style="resize:none;" class="form-control form-control-lg text-sm-left" readonly type="text" cols="6" rows="4"><?= $rev_rep['reply'] ?> </textarea>
                                                <?php } ?>
                                            </td>
                                            <td></td>
                                            <td class="text-left"></td>
                                            <td class="text-center" style="<?= $action_display ?>" <?= $hide_action ?>>
                                                <?php
                                                if ($table_appearance == 0) {
                                                    if (is_null($rev_rep['reply'])) {
                                                ?>
                                                        <button type="button" class="btn btn-success" onclick="add_new_reply(<?= $response_id ?>,<?= $document_id ?>,<?= $reply_reviewer_id ?>,'<?= $file_new ?>','<?= $count_rev ?>',1)">Add Reply</button>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </td>
                                        <?php
                                            if ($current_rev2 != $rev_replies[$counter2 - 1]['reviewer_id']) {
                                                $reviewer_counter2++;
                                                echo $reviewer_counter2 + 1;
                                            }
                                        }
                                        ?>
                                    </tr>
                                    <?php $counter2++; ?>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class=" table table-striped table-bordered table-sm mt-20  text-center">
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
                                <?php foreach ($docs2 as $doc2) : ?>
                                    <?php $full_name =  $doc2['firstname'] . " " . $doc2['lastname'] ?>
                                    <tr>
                                        <?php
                                        $rev_status =  $doc2['review_status'];
                                        // GET THE CURRENT REVIEWERS REVIEW STATUS
                                        // CURRENT REVIEWER IS THE ONE GOING TO ACT ON THE DOCUMENT. WE NEED TO DISTINGUISH BECAUSE THERE ARE MULTIPLE REVIEWER IN ONE DOCUMENT
                                        // WE NEED TO SET A VARIABLE'S STATUS IN ORDER TO SET THE STATUS COLOR IN THE DOCUMENT REVIEW STATUS CODE
                                        if ($doc2['reviewer_id'] == $session_emp_id) {
                                            $current_reviewer_status = $doc2['review_status'];
                                        }
                                        ?>
                                        <td><?= $doc2['review_document_id'] ?></td>
                                        <td><?= $full_name ?></td>
                                        <td> <?= $doc2['position'] ?></td>
                                        <td class="review-status">
                                            <span class="<?php select_badge($rev_status); ?>">
                                                <?php echo  display_status_value($rev_status) ?>
                                            </span>
                                        </td>
                                        <?php $full_name =  $document['firstname'] . " " . $document['lastname']; ?>
                                        <!-- docs2 GET REVIEWERS -->
                                        <?php
                                        $action_buttons = 'disabled';
                                        $c4 = 1;
                                        $current_reviewer_index = '';
                                        ?>
                                        <?php
                                        $color = '';
                                        $approval_status =  $document['status'];
                                        // ENABLE BUTTON IF STATUS IS EQUAL TO PENDING FOR ACTION
                                        try {
                                            if ($document['approval_id'] == $session_emp_id) {
                                                $current_reviewer_index = $c4 - 1;
                                                if ($approval_status == "0") {
                                                    $action_buttons = '';
                                                }
                                            }
                                            // GET THE CURRENT REVIEWERS REVIEW STATUS
                                            // CURRENT REVIEWER IS THE ONE GOING TO ACT ON THE DOCUMENT. WE NEED TO DISTINGUISH BECAUSE THERE ARE MULTIPLE REVIEWER IN ONE DOCUMENT
                                            // WE NEED TO SET A VARIABLE'S STATUS IN ORDER TO SET THE STATUS COLOR IN THE DOCUMENT REVIEW STATUS CODE
                                            if ($document['approval_id'] == $session_emp_id) {
                                                $current_reviewer_status = $document['status'];
                                            }
                                        } catch (Exception $e) {
                                            echo $e;
                                        }
                                        ?>
                                        <?php $c4++; ?>
                                    </tr>
                                <?php endforeach ?>
                                <?php
                                $get_signed_file_qry = $conn->query("SELECT * FROM `tbl_document_reviewer_sign` WHERE `signatory_type` = 0 AND `document_id` = $document_id");
                                $get_signed_files = $get_signed_file_qry->fetchAll();

                                $get_final_doc_qry =  $conn->query("SELECT * FROM `tbl_document_response_files` WHERE `document_id` = $document_id AND `response_id` = 0");
                                $get_final_doc = $get_final_doc_qry->fetchAll();
                                ?>
                                <tr>
                                    <td class="text-center" colspan="2">Attached File</td>
                                    <td class="text-left" colspan="2">
                                        <?php $file_counter = 1 ?>
                                        <?php foreach ($get_signed_files as $signed_file) : ?>
                                            <a href="assets/media/docs/<?= $signed_file['src'] ?>" data-toggle="tooltip" title="<?= $signed_file['src'] ?>"> <?= "FILE " . $file_counter ?></a><br>
                                            <?php $file_counter = $file_counter + 1 ?>
                                        <?php endforeach ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <table class=" table table-striped table-bordered table-sm mt-20  text-center">
                            <tr class="text-white" style="background-color: #808080">
                                <td class="text-center" colspan="5"> APPROVAL COMMENT STATUS</td>
                            </tr>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Remarks</th>
                                <th>STATUS</th>
                            </tr>
                            <tbody>
                                <!-- docs2 GET REVIEWERS -->
                                <tr>
                                    <td><?= $document['approval_id'] ?></td>
                                    <td><?= $document['approval_name'] ?></td>
                                    <td><?= $document['approval_position'] ?></td>
                                    <td><?= $document['remarks'] ?></td>
                                    <td><span class="<?php $doc_status = $document['status'];
                                                        select_badge($doc_status) ?>"><?= display_status_value($doc_status) ?></span></td>
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
                </div>
            </div>
            <br>
        </div>
    </div>
<?php
                            } catch (Exception $e) {
                                echo 'Sorry, Something wen\'t wrong' . $e;
                            }
                        }
                        //DELETE FILE FORM DELETE DOCUMENT ID
                        if (isset($_POST['delete_document_id'])) {
                            $document_id = $_POST['delete_document_id'];
                            try {
                                require_once '../send_mail.php';
                                $docs_info_qry = $conn->query("SELECT *
                                                FROM `tbl_documents`
                                                INNER JOIN tbl_employees ON tbl_documents.approval_id = tbl_employees.employee_id
                                                WHERE  `document_id` = $document_id");
                                $docs_qry = $docs_info_qry->fetch();


                                $get_reviewer_names = $conn->query("SELECT `firstname`, `lastname`,`email` FROM `tbl_document_reviewer`
                                                    INNER JOIN tbl_employees ON tbl_document_reviewer.reviewer_id = tbl_employees.employee_id 
                                                    WHERE`document_id` = $document_id");

                                $reviewer_names = $get_reviewer_names->fetchAll();
                                $reviewer_assigned = array();
                                $reviewer_emails = array();
                                foreach ($reviewer_names as $rev) {
                                    $reviewer_name = $rev['firstname'] . ' ' . $rev['lastname'];
                                    $reviewer_email = $rev['email'];
                                    array_push($reviewer_assigned, $reviewer_name);
                                    array_push($reviewer_emails, $reviewer_email);
                                }
                                //        $manual_cc = $_POST['manual_delete_cc'];
                                //        $cc2_emails = $_POST['select_delete_cc'];
                                $cc1_email = $docs_qry['email'];
                                $description = $docs_qry['description'];
                                $title = $docs_qry['title'];
                                $originator = $docs_qry['originator2'];
                                $discipline = $docs_qry['discipline'];
                                $project_code = $docs_qry['project_code'];
                                $document_type = $docs_qry['document_type'];
                                $document_zone = $docs_qry['document_zone'];
                                $document_level = $docs_qry['document_level'];
                                $sequence_no = $docs_qry['sequence_no'];
                                //        $rev = $docs_qry['rev'];
                                $folder_name = $_POST['delete_folder_name'];
                                $date_uploaded = $docs_qry['date_uploaded'];
                                $approval_assigned = $docs_qry['firstname'] . ' ' . $docs_qry['lastname'];
                                $folder_id = $docs_qry['folder_id'];
                                $set_status = "DELETED";
                                $get_files_qry = $conn->query("SELECT `src` FROM `tbl_files` WHERE `document_id` = $document_id");
                                $get_file_names = $get_files_qry->fetchAll();

                                $is_deleted = 1; // ye
                                $delete_doc_qry = $conn->query("UPDATE `tbl_documents` SET tbl_documents.is_deleted = $is_deleted WHERE tbl_documents.document_id =  $document_id");
                                $delete_doc_rev_qry = $conn->query("UPDATE `tbl_document_reviewer` SET tbl_document_reviewer.is_deleted = $is_deleted  WHERE tbl_document_reviewer.document_id = $document_id");
                                $delete_files_qry = $conn->query("UPDATE `tbl_files` SET tbl_files.is_deleted = $is_deleted WHERE tbl_files.document_id = $document_id ");
                                $approval_id = $docs_qry['approval_id'];

                                $approver = $conn->query("SELECT `email`, `firstname` ,`lastname` FROM tbl_documents INNER JOIN tbl_employees ON tbl_documents.approval_id =  tbl_employees.employee_id WHERE tbl_documents.approval_id = $approval_id");
                                $approver_info = $approver->fetch();

                                // STITCH NEW FILE NAME BY COMBINING THE DATA BELOW
                                $approval_assigned = $approver_info['firstname'] . " " . $approver_info['lastname'];
                                $file_no =
                                    $project_code . "-" .
                                    $originator . "-" .
                                    $discipline . "-" .
                                    $document_type . "-" .
                                    $document_zone . "-" .
                                    $document_level . "-" .
                                    $sequence_no;
                                $new_file_no = array();
                                $pieces = explode("-", $file_no);
                                $connector = "-";
                                $count = 1;
                                foreach ($pieces as $piece) {
                                    array_push($new_file_no, $piece);
                                }
                                $file_new_name = "";
                                foreach ($new_file_no as $item) {
                                    if ($count == count($new_file_no)) {
                                        $connector = "";
                                    }
                                    if (strtolower($item) != "n/a") {
                                        $file_new_name = $file_new_name . $item . $connector;
                                    }
                                    $count++;
                                }
                                $i =  strlen($file_new_name);
                                //    echo substr_replace($file_new_name, '', $i-1, 1);
                                $status = "DELETED";
                                echo "Delete Document Successful!";

                                // GET SUBSCRIBER QRY

                                $get_subscriber_qry = $conn->query("SELECT `user_id`,`access_name` from users INNER JOIN `tbl_subscribers` WHERE `user_id` = $session_emp_id");

                                $subscriber_qry = $get_subscriber_qry->fetch();

                                $subscriber  = $subscriber_qry['access_name'];

                                //        send_mail($reviewer_emails,
                                //            $cc1_email,
                                //            $cc2_emails,
                                //            $manual_cc,
                                //            $folder_name,
                                //            $title,
                                //            $file_new_name,
                                //            $date_uploaded,
                                //            $reviewer_assigned,
                                //            $approval_assigned,
                                //            $status,
                                //            $folder_id,
                                //            $subscriber);

                            } catch (Exception $e) {
                                echo $e;
                            }
                        }
                        //REVIEW ACTION

                        if (isset($_POST['action_type'])) {
                            require_once '../send_mail.php';

                            $comment_status = $_POST['comment_status'];
                            $doc_id = $_POST['doc_id'];
                            $rev_id = $_POST['rev_id']; // this id is dynamic, either for REVIEWER or APPROVAL
                            $cc_id = $_POST['select_cc'];
                            $folder_name = $_POST['review_folder_name'];
                            $originator = $_POST['originator_name'];
                            $file_new_name = $_POST['file_new'];
                            $action_type = $_POST['action_type'];
                            // $action_id = $_POST['action_id'];
                            $set_status = '';
                            $actor_type = $_POST['actor_type'];   // 1 =  REVIEWER
                            $originator_id = $_POST['originator_id'];
                            $file_ids = $_POST['file_ids'];
                            $files_loaded = $_POST['file_loaded']; // 1=ADD NEW FILE, 2 OVERRIDE THE EXISTING
                            $manual_cc = $_POST['manual_cc'];
                            // we need to get the size of the POST and then subtract the SIZE OF THE POST NOT BELONGING TO COMMENTS then  we divide by 2
                            //    $comment_length=(sizeof($_POST) -22) /2;
                            //    $comment_code = $_POST['comment_code'];

                            $date1 = date("His"); // for unique file name
                            //    $fileNames = $_FILES['files']['name'];
                            $uploader_type = '';
                            $document_level = $_POST['document_level'];
                            $project_code = $_POST['project_code'];
                            $description = $_POST['description'];
                            $title = $_POST['doc_title'];
                            $discipline = $_POST['discipline'];
                            $document_type = $_POST['document_type'];
                            $date_uploaded = $_POST['date_uploaded'];
                            $document_zone = $_POST['document_zone'];
                            $cc1_email = $_POST['approver_email'];
                            $folder_id = $_POST['folder_id'];
                            //    $rev = $_POST['rev'];
                            $approval_assigned = $_POST['approver_name'];
                            $remarks = $_POST['approval_remarks'];
                            //actor_type, either reviewer or approval
                            // ACTION ID 1 = REJECT 2 = APPROVE
                            //ACTION TYPE = 1 WITH COMMENT , 2 WITHOUT COMMENT
                            // GET FILE NAMES
                            $get_files_qry = $conn->query("SELECT `src` FROM `tbl_files` WHERE `document_id` = $doc_id");
                            $get_file_names = $get_files_qry->fetchAll();

                            $reviewer_emails = array();
                            $reviewer_info  = ""; // reviewer info will be dynamic depending on the actor. We exclude the actor's ID if he is the REVIEWER
                            try {
                                $conn->beginTransaction();
                                $notif_status = "";
                                $set_status = "B";

                                if ($action_type == 1) {
                                    if ($comment_status == '') {
                                        $set_status = "A";
                                        $notif_status = "Approved";
                                    } else {
                                        $set_status = "B";
                                        $notif_status = "Approved";
                                    }
                                } else  if ($action_type == 2) {
                                    $set_status = "B";
                                } else  if ($action_type == 3) {
                                    $set_status = "C";
                                    $notif_status = "Failed";
                                } else  if ($action_type == 4) {
                                    $set_status = "D";
                                } else  if ($action_type == 5) {
                                    $set_status = "E";
                                } else  if ($action_type == 6) {
                                    $set_status = "F";
                                }

                                // WITHOUT COMMENTS
                                // UPDATE STATUS FOR REVIEW OR APPROVAL
                                //
                                if ($actor_type == 1) {

                                    $update_review = $conn->prepare("UPDATE `tbl_document_reviewer` SET 
                                         `review_status` =?,
                                  `date_reviewed` = ?
                                     WHERE 
                                      `document_id` = ? AND 
                                       `reviewer_id` = ?");
                                    $update_review->execute([$set_status, $date_uploaded, $doc_id, $rev_id]);
                                    if ($action_type == 1) {
                                        $fileNames = $_FILES['signed_file']['name'];

                                        $new_file_names = array();
                                        // SAVE DOCUMENT SIGNED BY REVIEWER
                                        if (!empty($fileNames)) {
                                            foreach ($_FILES['signed_file']['name'] as $key => $val) {
                                                $fileName = basename($_FILES['signed_file']['name'][$key]);
                                                $unique_file_name = $date1 . '-' . $fileName;
                                                $new_file_names[] = $unique_file_name;
                                                // File upload configuration
                                                $targetDir = "../assets/media/docs/" . $unique_file_name;
                                                // get the file extension
                                                $extension = pathinfo($unique_file_name, PATHINFO_EXTENSION);
                                                // the physical file on a temporary uploads directory on the server
                                                $file = $_FILES['signed_file']['tmp_name'][$key];
                                                $size = $_FILES['signed_file']['size'];

                                                if (!in_array($extension, ['zip', 'pdf', 'docx', 'xlsx'])) {
                                                    echo "your file extension must be .zip, .pdf, .docx, 'xlsx";
                                                    // } elseif ($size > 1000000) { // file shouldn't be larger than 1Megabyte
                                                    //     echo "File too large!";
                                                } else {
                                                    // move the uploaded (temporary) file to the specified destination
                                                    if (move_uploaded_file($file, $targetDir)) {
                                                        $date = date('Y-m-d H:i:s');
                                                        $add_signed_file_qry = $conn->prepare("INSERT INTO `tbl_document_reviewer_sign`(
                                                                `document_id`,
                                                                `user_id`,
                                                                `src`, 
                                                                `upload_date`,
                                                                `is_deleted`
                                                                ) VALUES (?,?,?,?,?)");
                                                        $is_deleted = 0;
                                                        $add_signed_file_qry->execute([$doc_id, $rev_id, $unique_file_name, $date_uploaded, $is_deleted]);
                                                    } else {
                                                        echo "failed";
                                                    }
                                                    //SAVE EACH REVIEWER FOR THE DOCUMENT IN THE TBL_DOCUMENT_REVIEWER
                                                }
                                            } //foreach
                                        }
                                    }
                                    // SAVE DOCUMENT SIGNED BY REVIEWER

                                } else {

                                    //                SAVE DOCUMENT SIGNED BY APPROVAL
                                    $new_file_names = array();
                                    $cc_id =  $_POST['select_cc'];
                                    if ($set_status == "A") {
                                        $fileNames = $_FILES['signed_file']['name'];
                                        $response_type = 3;
                                        $response_id  = 0;
                                        $date_now = date('Y-m-d H:i:s');
                                        if (!empty($fileNames)) {
                                            // loop
                                            foreach ($_FILES['signed_file']['name'] as $key => $val) {
                                                $fileName = basename($_FILES['signed_file']['name'][$key]);
                                                $unique_file_name = "Final_document_no" . $doc_id . "-" . $date1 . "-" . $fileName;
                                                $new_file_names[] = $unique_file_name;
                                                // File upload configuration
                                                $targetDir = "../assets/media/docs/" . $unique_file_name;
                                                // get the file extension
                                                $extension = pathinfo($fileName, PATHINFO_EXTENSION);

                                                // the physical file on a temporary uploads directory on the server
                                                $file = $_FILES['signed_file']['tmp_name'][$key];
                                                $size = $_FILES['signed_file']['size'];

                                                if (!in_array($extension, ['zip', 'pdf', 'docx', 'xlsx'])) {
                                                    echo "your file extension must be .zip, .pdf, .docx, 'xlsx";
                                                    // } elseif ($size > 1000000) { // file shouldn't be larger than 1Megabyte
                                                    //     echo "File too large!";
                                                } else {
                                                    // move the uploaded (temporary) file to the specified destination

                                                    if (move_uploaded_file($file, $targetDir)) {

                                                        $add_file_qry = $conn->prepare("INSERT INTO `tbl_document_response_files`( 
                                                            `document_id`, 
                                                            `response_id`,  
                                                            `response_type`,  
                                                            `response_file_src`,
                                                            `uploader_id`,
                                                            `date_uploaded`
                                                            ) VALUES (?,?,?,?,?,?)");
                                                        $add_file_qry->execute([$doc_id, $response_id, $response_type, $unique_file_name, $session_emp_id, $date_now]);
                                                    } else {
                                                        echo "failed";
                                                    }
                                                    //SAVE EACH REVIEWER FOR THE DOCUMENT IN THE TBL_DOCUMENT_REVIEWER
                                                }
                                            } //foreach
                                        }
                                    }
                                    $update_approve = $conn->prepare("UPDATE `tbl_documents` SET 
                                  `status` =?,
                                `remarks` = ? 
                                WHERE 
                                 `document_id` = ? ");
                                    $update_approve->execute([$set_status, $remarks, $doc_id]);
                                }

                                // MULTIPLE FILE UPLOAD CONFIGURATION
                                // CHECK RECORD IF THERE IS A FILE UPLOADED EXCLUDING THE ORIGINATOR
                                // $check_qry = $conn->query("SELECT COUNT(file_id) FROM tbl_files WHERE `document_id` = $doc_id AND `user_id` != $originator_id");
                                // $file_counter =  $check_qry->fetch();
                                $file_ids = trim($_POST['file_ids'], "[]");
                                $file_id = explode(",", $file_ids);
                                $file_id_counter =  0;
                                // File upload configuration
                                $reviewer_assigned =  array(); // reviewer names
                                // store all email in array then pass it to send_mail()
                                $reviewer = $conn->query("SELECT `email`, `firstname` ,`lastname`
                                                                FROM tbl_document_reviewer
                                                                INNER JOIN tbl_employees on tbl_document_reviewer.reviewer_id = tbl_employees.employee_id
                                                                WHERE `document_id` = $doc_id");
                                $reviewer_info = $reviewer->fetchAll();

                                foreach ($reviewer_info as $rev) {
                                    $email =  $rev['email'];
                                    $full_name = $rev['firstname'] . " " . $rev['lastname'];
                                    //                    array_push($reviewer_emails, $email);
                                    //                    array_push($reviewer_assigned, $full_name);
                                    $reviewer_emails[] = $email;
                                    $reviewer_assigned[] = $full_name;
                                }
                                $cc2_emails = array();
                                // store all email in array then pass it to send_mail()
                                if (!empty($cc_id)) {
                                    foreach ($cc_id as $id) {
                                        $cc_qry = $conn->query("SELECT `email`,`lastname`,`firstname` from tbl_employees WHERE `employee_id` = $id");
                                        $cc_info = $cc_qry->fetch();
                                        $email = $cc_info['email'];
                                        //                    $full_name = $cc_info['firstname']." ".$cc_info['lastname'];
                                        $cc2_emails[] = $email;
                                    }
                                }

                                // get document info
                                $get_doc_info_qry = $conn->query("SELECT * FROM tbl_documents WHERE document_id = $doc_id");
                                $docs_info = $get_doc_info_qry->fetch();

                                $get_subscriber_qry = $conn->query("SELECT `user_id`,`access_name` from users INNER JOIN `tbl_subscribers` WHERE `emp_id` = $session_emp_id");
                                $subscriber_qry = $get_subscriber_qry->fetch();
                                $subscriber = $subscriber_qry['access_name'];

                                $status = $notif_status;

                                send_mail(
                                    $reviewer_emails,
                                    $cc1_email,
                                    $cc2_emails,
                                    $manual_cc,
                                    $folder_name,
                                    $title,
                                    $file_new_name,
                                    $date_uploaded,
                                    $reviewer_assigned,
                                    $approval_assigned,
                                    $status,
                                    $folder_id,
                                    $subscriber,
                                    $new_file_names
                                );

                                echo "Successfully Acted on the Document! ";

                                $conn->commit();
                            } catch (Exception $e) {
                                $conn->rollBack();
                                echo $e;
                            }
                        }




                        // ADD COMMENT - COMMENT ACTION
                        $new_file_names = array();
                        if (isset($_POST['reviewer_comment'])) {
                            require_once '../send_mail.php';
                            $document_id = $_POST['review_doc_id'];
                            $reviewer_id = $_POST['reviewer_id'];
                            $comment = $_POST['reviewer_comment'];
                            $comment_code = $_POST['comment_code'];
                            $selected_pages = $_POST['pages'];
                            $response_type = $_POST['response_type'];
                            $fileNames = $_FILES['revised_files']['name'];
                            $date1 = date("His"); // for unique file name
                            $date = date('Y-m-d H:i:s'); //
                            $pages  = '';
                            foreach ($selected_pages as $page) {
                                $pages = $pages . ", " . $page;
                            }
                            substr_replace($pages, ',', 0, 1);
                            try {
                                $conn->beginTransaction();
                                // SAVE COMMENTS
                                $add_comment_qry = $conn->prepare("INSERT INTO 
                                                 `tbl_document_comments_replies`                 
                                                 (`document_id`, 
                                                     `reviewer_id`, 
                                                     `comment`, 
                                                     `pages`,
                                                    `comment_code`,
                                                    `comment_date`) 
                                             VALUES (?,?,?,?,?,?)");
                                $add_comment_qry->execute([$document_id, $reviewer_id, $comment, $pages, $comment_code, $date]);

                                // SAVE COMMENTS
                                $response_id = $conn->lastInsertId();
                                //SAVE FILES PER COMMENTS
                                if (!empty($fileNames)) {
                                    foreach ($_FILES['revised_files']['name'] as $key => $val) {
                                        $fileName = basename($_FILES['revised_files']['name'][$key]);
                                        $unique_file_name = $date1 . '-' . $fileName;
                                        $new_file_names[] = $unique_file_name;
                                        // File upload configuration

                                        $targetDir = "../assets/media/docs/" . $unique_file_name;
                                        // get the file extension
                                        $extension = pathinfo($unique_file_name, PATHINFO_EXTENSION);

                                        // the physical file on a temporary uploads directory on the server
                                        $file = $_FILES['revised_files']['tmp_name'][$key];
                                        $size = $_FILES['revised_files']['size'];

                                        if (!in_array($extension, ['zip', 'pdf', 'docx', 'xlsx'])) {
                                            //                    echo "your file extension must be .zip, .pdf, .docx, 'xlsx";
                                            // } elseif ($size > 1000000) { // file shouldn't be larger than 1Megabyte
                                            //     echo "File too large!";
                                        } else {
                                            // move the uploaded (temporary) file to the specified destination
                                            if (move_uploaded_file($file, $targetDir)) {
                                                $date = date('Y-m-d H:i:s');
                                                $add_file_qry = $conn->prepare("INSERT INTO `tbl_document_response_files`( 
                                                            `document_id`, 
                                                            `response_id`,  
                                                            `response_type`,  
                                                            `response_file_src`,
                                                            `uploader_id`,
                                                            `date_uploaded`
                                                            ) VALUES (?,?,?,?,?,?)");
                                                $add_file_qry->execute([$document_id, $response_id, $response_type, $unique_file_name, $session_emp_id, $date]);
                                            } else {
                                                echo "failed";
                                            }
                                            //SAVE EACH REVIEWER FOR THE DOCUMENT IN THE TBL_DOCUMENT_REVIEWER
                                        }
                                    } //foreach
                                }
                                //SAVE FILES PER COMMENTS

                                //        GET REVIEWER IDS.
                                $select_approval  = $_POST['select_approval_id'];
                                $approver = $conn->query("SELECT `email`, `firstname` ,`lastname` FROM tbl_employees WHERE `employee_id` = $select_approval");
                                $approver_info = $approver->fetch();
                                //cc1 originator email
                                $approval_assigned = $_POST['originator_name']; //name
                                $cc1_email = $_POST['originator_email']; // email
                                $cc2_emails = array();
                                $manual_cc = $_POST['cc_comments_manual'];
                                $cc_id = $_POST['select_cc4'];
                                $title = "";
                                $get_subscriber_qry = $conn->query("SELECT access_name from tbl_subscribers");
                                $subscriber_qry = $get_subscriber_qry->fetch();
                                $subscriber = $subscriber_qry['access_name'];
                                $date_uploaded = date('Y-m-d H:i:s');
                                $folder_id = $_POST['folder_id_comments'];
                                $folder_name = $_POST['folder_name_comments'];
                                // $to =  $reviewer_emails;
                                $cc1_email = $approver_info['email'];
                                //        $document_infos = ($_POST['document_infos']);
                                $status = "Pending Review";
                                $file_new_name = $_POST['file_new'];

                                $reviewer_emails = array();
                                $reviewer_assigned = array();
                                $get_reviewer_emails_qry = $conn->query("SELECT * FROM `tbl_document_reviewer` 
                                                                   INNER JOIN users on tbl_document_reviewer.reviewer_id = users.user_id
                                                                   WHERE `document_id` = $document_id");
                                $get_reviewer_emails = $get_reviewer_emails_qry->fetchAll();

                                foreach ($get_reviewer_emails as $rev_items) {
                                    $email = $rev_items['email'];
                                    $full_name = $rev_items['firstname'] . " " . $rev_items['lastname'];
                                    array_push($reviewer_emails, $email);
                                    array_push($reviewer_assigned, $full_name);
                                }

                                // store all email in array then pass it to send_mail()
                                foreach ($cc_id as $id) {
                                    $cc_qry = $conn->query("SELECT `email`, `firstname` ,`lastname` FROM tbl_employees WHERE `employee_id` = $id");
                                    $cc_info = $cc_qry->fetch();
                                    //            echo $id."----";
                                    $email = $cc_info['email'];
                                    array_push($cc2_emails, $email);
                                }
                                //
                                send_mail(
                                    $reviewer_emails,
                                    $cc1_email,
                                    $cc2_emails,
                                    $manual_cc,
                                    $folder_name,
                                    $title,
                                    $file_new_name,
                                    $date_uploaded,
                                    $reviewer_assigned,
                                    $approval_assigned,
                                    $status,
                                    $folder_id,
                                    $subscriber,
                                    $new_file_names
                                );
                                $conn->commit();
                                echo "Comment Successfully Added";
                            } catch (Exception $e) {
                                $conn->rollBack();
                                echo $e;
                            }
                        }


                        if (isset($_POST['delete_response_id'])) {
                            $response_id = $_POST['delete_response_id'];
                            try {
                                $delete_comment_qry = $conn->prepare("UPDATE `tbl_document_comments_replies` SET `is_deleted` = 1  WHERE `response_id` = ?");
                                $delete_comment_qry->execute([$response_id]);
                                echo "Comment Successfully Deleted";
                            } catch (Exception $e) {
                                echo $e;
                            }
                        }



                        //EDIT COMMENTS
                        if (isset($_POST['edit_response_id'])) {

                            try {
                                $response_id = $_POST['edit_response_id'];
                                $edit_comment = $_POST['edit_comment'];
                                $edit_pages = $_POST['edit_pages'];
                                $edit_comment_code = $_POST['edit_comment_code'];
                                $date = date('Y-m-d H:i:s');
                                $edit_comment_qry = $conn->prepare("UPDATE `tbl_document_comments_replies` 
                                                    SET 
                                                        `comment` = $edit_comment,
                                                        `pages` = $edit_pages, 
                                                        `comment_code` = $edit_comment_code,
                                                        `reply_date` = $date
                                                    WHERE `response_id` = ?");
                                $edit_comment_qry->execute([$response_id]);

                                echo "Comment Successfully Deleted";
                            } catch (Exception $e) {
                                echo $e;
                            }
                        }
                        //ADD REPLY EDIT REPLY
                        if (isset($_POST['response_id'])) {
                            require_once '../send_mail.php';
                            $reply_code = $_POST['reply_code'];
                            $reply = $_POST['reply'];
                            $count_rev = $_POST['count_revision'];
                            $cc2_emails  = $_POST['select_cc2'];
                            $document_id = $_POST['reply_document_id'];
                            $manual_cc2 = $_POST['manual_cc2'];
                            $response_id = $_POST['response_id'];
                            $response_type = $_POST['response_type'];
                            $fileNames = $_FILES['revised_files']['name'];
                            $date1 = date("His"); // for unique file name
                            $response_status = 1;
                            $date_now = date('Y-m-d H:i:s');
                            //    COUNT HOW MANY REVISION WAS SUBMITTED GET THE NUMBER OF


                            try {
                                $conn->beginTransaction();
                                // SAVE COMMENTS
                                $add_reply_qry = $conn->query("UPDATE tbl_document_comments_replies      
                                                 SET  `reply`='$reply', `reply_code` = '$reply_code', `response_status` =$response_status,`reply_date` = '$date_now'                                            
                                                 WHERE `response_id` = $response_id");
                                //        $add_reply_qry->execute([$reply,$reply_code,$response_status, $response_id, $date_now]);

                                // SAVE COMMENTS

                                //SAVE FILES PER COMMENTS
                                if (!empty($fileNames)) {
                                    foreach ($_FILES['revised_files']['name'] as $key => $val) {
                                        $fileName = basename($_FILES['revised_files']['name'][$key]);
                                        $unique_file_name = "Rev-No." . $count_rev . "-" . $date1 . $fileName;

                                        // File upload configuration
                                        $targetDir = "../assets/media/docs/" . $unique_file_name;
                                        // get the file extension
                                        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

                                        // the physical file on a temporary uploads directory on the server
                                        $file = $_FILES['revised_files']['tmp_name'][$key];
                                        $size = $_FILES['revised_files']['size'];

                                        if (!in_array($extension, ['zip', 'pdf', 'docx', 'xlsx'])) {
                                            echo "your file extension must be .zip, .pdf, .docx, 'xlsx";
                                            // } elseif ($size > 1000000) { // file shouldn't be larger than 1Megabyte
                                            //     echo "File too large!";
                                        } else {
                                            // move the uploaded (temporary) file to the specified destination
                                            if (move_uploaded_file($file, $targetDir)) {

                                                $add_file_qry = $conn->prepare("INSERT INTO `tbl_document_response_files`( 
                                                            `document_id`, 
                                                            `response_id`,  
                                                            `response_type`,  
                                                            `response_file_src`,
                                                            `uploader_id`,
                                                            `date_uploaded`
                                                            ) VALUES (?,?,?,?,?,?)");
                                                $add_file_qry->execute([$document_id, $response_id, $response_type, $unique_file_name, $session_emp_id, $date_now]);
                                            } else {
                                                echo "failed";
                                            }
                                            //SAVE EACH REVIEWER FOR THE DOCUMENT IN THE TBL_DOCUMENT_REVIEWER
                                        }
                                    } //foreach
                                }
                                //SAVE FILES PER COMMENTS

                                // SEND NOTIFICATIONS
                                //        GET REVIEWER IDS.

                                // GET DOCUMENT INFOS
                                $get_document_infos_qry = $conn->query("SELECT * FROM `tbl_documents` 
                                                        INNER JOIN tbl_employees ON tbl_documents.approval_id  = tbl_employees.employee_id 
                                                        WHERE `document_id` = $document_id");
                                $document_infos = $get_document_infos_qry->fetch();
                                $select_approval  = $document_infos['approval_id'];
                                //        $approver = $conn->query("SELECT `email`, `firstname` ,`lastname` FROM users WHERE `user_id` = $select_approval");
                                //        $approver_info = $approver->fetch();
                                $approval_assigned = $document_infos['firstname'] . " " . $document_infos['lastname'];
                                // get commenter email
                                $reply_reviewer_id = $_POST['reply_reviewer_id'];
                                //            echo $reply_reviewer_id;
                                $get_sender_reviewer_email_qry = $conn->query("SELECT `email`  FROM tbl_employees WHERE `employee_id` = $reply_reviewer_id ");
                                $get_sender_reviewer_email = $get_sender_reviewer_email_qry->fetch();

                                $cc1_email = $get_sender_reviewer_email['email']; // email

                                //        var_dump($get_sender_reviewer_email_qry);

                                // get commenter email
                                $cc2_emails = array();
                                $manual_cc = $_POST['manual_cc2'];
                                $cc_id = $_POST['select_cc2'];
                                $title = "";
                                $get_subscriber_qry = $conn->query("SELECT `user_id`,`access_name` from users INNER JOIN `tbl_subscribers` ON  users.subscriber_id = tbl_subscribers.subscriber_id WHERE `user_id` = $session_emp_id");
                                $subscriber_qry = $get_subscriber_qry->fetch();
                                $subscriber = $subscriber_qry['access_name'];
                                $date_uploaded = date('Y-m-d H:i:s');
                                $folder_id = $_POST['folder_id_reply'];
                                $folder_name = $_POST['folder_name_reply'];
                                // $to =  $reviewer_emails;
                                $cc1_email = $document_infos['email'];
                                //        $document_infos = ($_POST['document_infos']);
                                $status = "Pending Review(Reply Sent)";
                                $file_new_name = $_POST['file_new'];

                                $reviewer_emails = array();
                                $reviewer_assigned = array();
                                $get_reviewer_emails_qry = $conn->query("SELECT * FROM `tbl_document_reviewer` 
                                                                   INNER JOIN users on tbl_document_reviewer.reviewer_id = users.user_id
                                                                   WHERE `document_id` = $document_id");
                                $get_reviewer_emails = $get_reviewer_emails_qry->fetchAll();

                                foreach ($get_reviewer_emails as $rev_items) {
                                    $email = $rev_items['email'];
                                    $full_name = $rev_items['firstname'] . " " . $rev_items['lastname'];
                                    array_push($reviewer_emails, $email);
                                    array_push($reviewer_assigned, $full_name);
                                }

                                // store all email in array then pass it to send_mail()
                                foreach ($cc_id as $id) {
                                    $cc_qry = $conn->query("SELECT `email`, `firstname` ,`lastname` FROM users WHERE `user_id` = $id");
                                    $cc_info = $cc_qry->fetch();

                                    $email = $cc_info['email'];
                                    array_push($cc2_emails, $email);
                                }
                                //
                                send_mail(
                                    $reviewer_emails,
                                    $cc1_email,
                                    $cc2_emails,
                                    $manual_cc,
                                    $folder_name,
                                    $title,
                                    $file_new_name,
                                    $date_uploaded,
                                    $reviewer_assigned,
                                    $approval_assigned,
                                    $status,
                                    $folder_id,
                                    $subscriber,
                                    $new_file_names
                                );
                                //SEND NOTIFICATIONS
                                $conn->commit();
                                echo "Replied Successfully ";
                            } catch (Exception $e) {
                                $conn->rollBack();
                                echo $e;
                            }
                        }
                        if (isset($_POST['show_files'])) {
                            try {

                                $docu_id = $_POST['document_id'];
                                //tbl_files =  originator submitted documents
                                $get_originator_files_qry = $conn->query("SELECT `src`,
                                                           (SELECT  CONCAT(firstname, ' ', lastname)from users WHERE user_id = tbl_files.user_id)   AS originator_name
                                                           FROM `tbl_files` 
                                                           INNER JOIN users ON tbl_files.user_id = users.user_id        
                                                           WHERE document_id = $docu_id");
                                $get_originator_files = $get_originator_files_qry->fetchAll();

                                // get the ids of reviewers who actually commented
                                $count_reviewers_qry = $conn->query("SELECT DISTINCT (reviewer_id) as rev_id,
                                                        (SELECT MAX(response_id) FROM tbl_document_comments_replies WHERE `document_id` = $docu_id AND `reviewer_id` = rev_id) AS latest_response_id
                                                      FROM tbl_document_comments_replies
                                                      WHERE tbl_document_comments_replies.document_id = $docu_id ");
                                $count_reviewers = $count_reviewers_qry->fetchAll();

                                $get_signed_file_qry = $conn->query("SELECT * FROM `tbl_document_reviewer_sign` WHERE `document_id` = $docu_id");
                                $get_signed_files = $get_signed_file_qry->fetchAll();
                                $row_count_orig = count($get_originator_files);
                            } catch (Exception $e) {
                                echo $e;
                            }
?>
<div class="row">
    <div class="col-sm-3">
        <div class="block block-bordered">
            <div class="block-header block-header-default">
                <h3 class="block-title">Submitted by : <?= $get_originator_files[0]['originator_name']; ?></h3>
            </div>
            <div class="block-content">
                <table class="table table-striped table-borderless table-sm mt-20  ">
                    <tbody>
                        <?php $counter = 0; ?>
                        <?php $file_counter = 1; ?>
                        <?php foreach ($get_originator_files as $item2) : ?>
                            <tr>
                                <td>
                                    <?= $file_counter; ?>
                                </td>
                                <td>
                                    <a href="assets/media/docs/<?= $item2['src'] ?>" target="_blank"><?= $item2['src'] ?>
                                    </a>
                                </td>
                            </tr>
                            <?php $file_counter++; ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-5">
        <div class="block block-bordered">
            <div class="block-header block-header-default">
                <h3 class="block-title">Reviewer Comments Files</h3>
            </div>
            <div class="block-content">
                <table class="table table-striped table-borderless table-sm mt-20  ">
                    <tbody>
                        <?php $counter = 0; ?>
                        <?php $file_counter2 = 1;
                            if (count($count_reviewers) == 0) {
                                echo "0 FILE UPLOADED";
                            }
                        ?>
                        <?php foreach ($count_reviewers as $count_rev) : ?>
                            <?php
                                $rev_id = $count_rev['rev_id'];
                                $latest_response_id = $count_rev['latest_response_id'];
                                $get_comment_files_qry = $conn->query("SELECT `response_file_src`,tbl_document_comments_replies.reviewer_id,
                                                        (SELECT  CONCAT(firstname, ' ', lastname)from users WHERE users.user_id = tbl_document_comments_replies.reviewer_id)   AS reviewer_name       
                                                        FROM tbl_document_response_files       
                                                        INNER JOIN tbl_document_comments_replies ON tbl_document_response_files.response_id = tbl_document_comments_replies.response_id     
                                                        INNER JOIN users ON tbl_document_comments_replies.reviewer_id = users.user_id
                                                        WHERE tbl_document_response_files.document_id = $docu_id AND `response_type` = 2  AND tbl_document_response_files.response_id = $latest_response_id AND `reviewer_id` = $rev_id");
                                $get_comment_files = $get_comment_files_qry->fetchAll();
                            ?>
                            <?php $file_counter2 = 1; ?>
                            <?php foreach ($get_comment_files as $item2) : ?>
                                <tr>
                                    <?php if ($file_counter2 == 1) {  ?>
                                        <td><?= $rev_id ?></td>
                                    <?php
                                    } else {  ?>
                                        <td></td>
                                    <?php
                                    }  ?>
                                    <td><?= $item2['reviewer_name'] ?></td>
                                    <td>
                                        <a href="assets/media/docs/<?= $item2['response_file_src'] ?>" target="_blank"><?= $item2['response_file_src'] ?>
                                    </td>
                                </tr>
                                <?php $file_counter2++; ?>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="block block-bordered">
            <div class="block-header block-header-default">
                <h3 class="block-title">Revised Files</h3>
            </div>
            <div class="block-content">
                <table class="table table-striped table-borderless table-sm mt-20">
                    <tbody>
                        <?php foreach ($count_reviewers as $count_rev) : ?>
                            <?php
                                $rev_id = $count_rev['rev_id'];
                                $latest_response_id = $count_rev['latest_response_id'];
                                $get_revised_files_qry = $conn->query("SELECT `response_file_src`
                                                                            FROM tbl_document_response_files 
                                                                            INNER JOIN tbl_document_comments_replies ON tbl_document_response_files.response_id = tbl_document_comments_replies.response_id     
                                                                            INNER JOIN users ON tbl_document_comments_replies.reviewer_id = users.user_id
                                                                            WHERE tbl_document_comments_replies.document_id = $docu_id  AND response_type = 1
                                                                            AND tbl_document_response_files.response_id = $latest_response_id 
                                                                            AND `reviewer_id` = $rev_id");
                                $get_revised_files = $get_revised_files_qry->fetchAll();
                            ?>
                            <?php foreach ($get_revised_files as $item) : ?><?php $file_counter = 1; ?>
                            <tr>
                                <td><?= $file_counter ?></td>
                                <td>
                                    <a href="assets/media/docs/<?= $item['response_file_src'] ?>" target="_blank"><?= $item['response_file_src'] ?>
                                </td>
                            </tr>
                            <?php $file_counter++; ?>
                        <?php endforeach ?>
                    <?php endforeach ?>
                    <?php
                            if (count($get_signed_files) != 0) {
                    ?>
                        <tr>
                            <td class="text-capitalize">No: </td>
                            <td class="text-capitalize">Signed Files</td>
                        </tr>
                        </tr>
                        <?php $file_counter3 = 1; ?>
                        <?php foreach ($get_signed_files as $item3) : ?>
                            <tr>
                                <td><?= $file_counter3 ?></td>
                                <td>
                                    <a href="assets/media/docs/<?= $item3['src'] ?>" target="_blank"><?= $item3['src'] ?>
                                </td>

                            </tr>
                            <?php $file_counter3++; ?>
                        <?php endforeach ?>
                    <?php } ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="col-sm-4">
    </div>
</div>
</div>
</div>
<script>
    $(document).ready(function() {
        $('textarea').each(function() {
            var textHeight = $(this).val();
            var text_lenght = textHeight.length + 30;
            this.setAttribute('style', 'height:' + (text_lenght) + 'px;overflow-y:hidden;');
        })
    });
</script>

<?php }
                        $conn = Null; ?>