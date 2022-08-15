<?php
require_once '../session.php';
date_default_timezone_set('Asia/Manila');

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;
// use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';

// Retrieving Remarks for Review 
// $mail = new PHPMailer(true);

if (isset($_POST['document_info'])){
    $document_id = $_POST['document_id'];
    $remark = $conn->query("SELECT review_remark FROM tbl_documents WHERE document_id = $document_id");
    $data = $remark->fetch();
?>
    
<?php
}
if (isset($_POST['document_id_review_approve'])){
require_once '../send_mail.php';
   try {
         //ACTION ID HAS A VALUE OF 1,2,3,4 - 1 and 2 is for review status, 3 and 4 is for approval status

      
    // $document_details = $document_details->fetch();
        // File upload configuration

        $date1 = date("His"); // for unique file name  
        $fileName = $_FILES['file']['name'];
        $unique_file_name = $date1.'-'.$fileName;      
        $targetDir = "../assets/media/docs/".$unique_file_name;
        $extension = pathinfo($unique_file_name, PATHINFO_EXTENSION);
        $file = $_FILES['file']['tmp_name'];
        $size = $_FILES['file']['size'];
        $folder_id = $_POST['folder_id2'];
        $column = '';
        $document_src_column = '';
        $email_status = '';


        $document_id = $_POST['document_id_review_approve'];           
        $remarks = $_POST['remarks_textarea'];



        // RETRIEVE EMAILS

       

        $originator_email_qry = $conn->query("SELECT `email`, `img_src`,`firstname` ,`lastname`,`title`, `description`, `date_uploaded` FROM tbl_employees 
                                                INNER JOIN tbl_documents ON tbl_documents.user_id = tbl_employees.employee_id          
                                                WHERE document_id = $document_id");        
        $originator_email = $originator_email_qry->fetch();
        $reviewer_email_qry = $conn->query("SELECT `email`, `img_src`,`firstname` ,`lastname` FROM 
                                                tbl_employees
                                                INNER JOIN tbl_documents ON tbl_documents.user_id_review = tbl_employees.employee_id           
                                                WHERE document_id = $document_id");
        $reviewer_email = $reviewer_email_qry->fetch();
        $approval_email_qry = $conn->query("SELECT `email`, `img_src`,`firstname` ,`lastname` FROM users                                                                           
                                                INNER JOIN tbl_documents ON tbl_documents.user_id_approval = tbl_employees.employee_id           
                                                WHERE document_id = $document_id");

        $approval_email = $approval_email_qry->fetch(); //retrieved
        // RETRIEVE EMAILS   
        $originator_fullname = $originator_email['fname']." ".$originator_email['lname'];    
        $email_content = "The document submitted by ".$originator_fullname." ".$email_status;
        $recipient = "adrian.pajaro@jmc.edu.ph";

        
        $title = $originator_email['title'];
        $description = $originator_email['description'];
        $date_uploaded = $originator_email['date_uploaded'];
        $to = '';
        $cc1 = $_POST['cc1'];
        $cc2 = $_POST['cc2'];
        $manual_cc = $_POST['manual_cc'];

        if($_POST['action_id']==1){   //REVIEW APPROVED
            $status = 1;
            $column =  'review_status';
            $remark = "review_remark";
            $document_src_column = "reviewed_document_src";
            $email_status = "REVIEWED";
            $to = $approval_email['email'];

        } else if($_POST['action_id'] ==2){ //REVIEW DENIED
            $status = 2;
            $column =  'review_status';
            $remark = "review_remark";
            $document_src_column = "reviewed_document_src";
            $email_status = "REJECTED on REVIEW";
            $to = $approval_email['email'];
           
        } else if($_POST['action_id'] ==3){ //APPROVED
            $status = 1;
            $column =  'status';
            $remark = "approval_remark";
            $document_src_column = "final_document_src";
            $email_status = "APPROVED";
            $to = $originator_email['email'];
         
        } else if($_POST['action_id'] ==4){ //DENIED
            $status = 2;
            $column =  'status';
            $remark = "approval_remark";
            $document_src_column = "final_document_src";
            $email_status = "REJECTED";
            $to = $originator_email['email'];
        
        }

            $reviewer_assigned = $reviewer_email['fname']." ".$reviewer_email['lname']; 
            $approval_assigned = $approval_email['fname']." ".$approval_email['lname']; ;

        $update_review = $conn->prepare("UPDATE `tbl_documents` SET $remark = ? , $column =?  WHERE `document_id` = ?");

        if (!in_array($extension, ['zip', 'pdf', 'docx'])) {
            echo "Your file extension must be .zip, .pdf or .docx";
        } elseif ($size > 10000000) { // file shouldn't be larger than 10 Megabyte
            echo "File too large!";
        } else {
            // move the uploaded (temporary) file to the specified destination
            if (move_uploaded_file($file, $targetDir)) {
                $update_review = $conn->prepare("UPDATE `tbl_documents` SET $remark = ? , $column =?, $document_src_column = ? WHERE `document_id` = ?");
                $update_review->execute([$remarks, $status, $unique_file_name, $document_id]);   

                    echo "File uploaded successfully \n";

                send_mail(  $to,
                            $cc1,
                            $cc2,
                            $manual_cc, 
                            $unique_file_name,
                            $title, 
                            $description, 
                            $date_uploaded, 
                            $reviewer_assigned,
                            $approval_assigned, 
                            $email_status,
                            $folder_id );           
               
                
                      
            } else {
                echo "Failed to upload file.";
            }
        }

            // echo " Successfully marked the document";
   } catch (Exception $e) {
       //throw $th;
       $conn->rollBack();
       echo $e;
   }   
?>
    
<?php
    
}

//APPROVAL MODAL
//SHOW DOCUMENTS FOR APPROVAL
if (isset($_POST['show_approval_details'])){
    require_once '../send_mail.php';
    //cc emails
    $cc_email_query = $conn->query("SELECT `email`, `fname` ,`lname` FROM users");
    $cc_emails = $cc_email_query->fetchAll();

    $document_id = $_POST['document_id'];
    $user_id;
    $document_details = $conn->query("SELECT `document_id`,
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
                                            `reviewed_document_src`,
                                             `fname`,
                                            `lname`
                                            FROM tbl_documents                                                                           
                                            INNER JOIN users ON tbl_documents.user_id_review = users.user_id                                                                          
                                            WHERE document_id = $document_id");

    $document_details = $document_details->fetch();
    $requestor = ucwords(strtolower($document_details['fname'].' '.$document_details['lname']));
    $approval_status = $document_details['status'];
    ?>
    
    <div class="col-xl-5 mt-0 mb-0" id="profile_info">
        <div class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
            <div class="block-content bg-primary"> 
               <div class="push">
                    <img class="img-avatar img-avatar-thumb" src="assets\media\photos\profile\<?=$document_details['profile_pic']; ?>" alt="">
                </div>
                <div class="pull-r-l pull-b py-10 bg-black-op-25">
                    <div class="font-w600 mb-5 text-white"> Submitted by: 
                        <?=$requestor?> <i class=""></i>
                    </div>                  
                </div>
            </div>
            <div class="block-content bg-black-op-10">            
                <div class="row items-push text-center ">
                    <div class="col-6">
                        <a href="javascript:void(0);" onclick="open_file('<?=$document_details['reviewed_document_src'];?>')" >
                            <div class="mb-5"><i class="fa fa-arrow-circle-down fa-2x"></i></div>    
                            <div class="font-size-sm text-primary"><?=$document_details['reviewed_document_src'];?></div>
                        </a>
                    </div>
                    <?php
                        $status = $document_details['review_status'];
                        $display_status = '';
                        $icon ='';
                        $class = '';
                        if($status == 0){
                         $display_status = "Pending";
                         $icon = "fa fa-spinner fa-2x"; 
                         $class = 'font-size-sm text-primary';
                        } elseif($status == 1) {
                         $display_status=  "Reviewed";
                         $icon = "fa fa-check fa-2x"; 
                         $class = 'font-size-sm text-success';
                        } else {
                         $display_status = "Declined";
                         $icon = "fa fa-frown-o fa-2x"; 
                         $class = 'font-size-sm text-muted';
                        }
                    ?>
                    <div class="col-6">                        
                        <div class="mb-5"><i class="<?=$icon?>"></i></div>                            
                         <div class="<?=$class ?>"><?=$display_status;?></div> 
                    </div>
                </div>
            </div>        
        </div>            
    </div>

<div class="col-xl-7 px-0 mb-0">
    <div class="col-lg-12">
		<div class="card">
			<div class="card-body">			
				<!-- <div class="row mb-3">					
				</div> -->
				
				<div class="row mb-3">
					<div class="col-sm-4">
						<!-- <p class="mb-0">Doc. ID</p> -->
					</div>
					<div class="col-sm-8 text-secondary">
						<input hidden = "true" type="text"  name = "document_id_review_approve" id = "document_id_review_approve" value="<?=$document_details['document_id']?>">
					</div>
				</div>
                <div class="row mb-3">
					<div class="col-sm-4">
                  
						<p class="mb-0">Date Uploaded: </p>
					</div>
					<div class="col-sm-8 text-secondary">
						<p  type="text"  value=" "><?=$document_details['date_uploaded']?></p>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-sm-4">
						<p class="mb-0">File Description: </p>
					</div>
					<div class="col-sm-8 text-secondary">
						<p  type="text"  value=""><?=$document_details['description']?></p>
                    </div>
				</div>
                <div class="row mb-3" id = "remarks_paragraph" >
                        <div class="col-sm-4">
                            <p class="mb-0">Review Status: </p>
                        </div>
                        <div class="col-sm-8 text-secondary">
                            <p  type="text"  value=""><?=$display_status?></p>
                        </div>
                </div>  
                <div class="row mb-3" id = "remarks_paragraph" >
                    <div class="col-sm-4">
                        <p class="mb-0">Review Remarks: </p>
                    </div>
                    <div class="col-sm-8 text-secondary">
                       <p  type="text"  value=""><?=$document_details['review_remark']?></p>
                    </div>
                </div>        
                <?php 
                // $status = review_status, $
                if($status == 1 && $approval_status == 0 ){ // if pending
                 
                 ?>
                <div class="row mb-10">
                    <div class="col-sm-12">
                        <textarea required class="form-control" id="remarks_textarea" name = "remarks_textarea" rows="3" placeholder="Approval Remarks"></textarea>
                    </div>                   
                </div>  
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label" for="cc1">First CC target <span class="text-danger"></span></label>
                    <div class="col-lg-8">
                        <select class="js-select2 form-control" id="cc1" name="cc1" style="width: 100%;"  required data-placeholder="Choose one..">
                              <option selected>Select CC target:</option>                         
                        <?php foreach ($cc_emails as $cc_email): ?>
                            <option value="<?=$cc_email['email'] ?>"><?=$cc_email['fname'] ?> <?=$cc_email['lname'] ?> </option>
                        <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label" for="cc2">Second CC target <span class="text-danger"></span></label>
                    <div class="col-lg-8">
                        <select class="js-select2 form-control" id="cc2" name="cc2" style="width: 100%;"  required data-placeholder="Choose one..">
                              <option selected>Select 2nd CC target:</option>                         
                        <?php foreach ($cc_emails as $cc_email): ?>
                            <option value="<?=$cc_email['email'] ?>"><?=$cc_email['fname'] ?> <?=$cc_email['lname'] ?> </option>
                        <?php endforeach ?>
                        </select>
                    </div>
                </div>                 
                <div >
               <div class="row mb-3" id = "upload_comment" >
                        <div class="col-sm-4">
                            <p class="mb-0">Upload comment: </p>
                        </div>
                        <div class="col-sm-8 text-secondary">
                           <input type="file" name="file" required>
                        </div>
                    </div>    
                </div>                       
                <?php } else {?>
                    <div class="row mb-3" id = "remarks_paragraph" >
                        <div class="col-sm-4">
                            <p class="mb-0">Remarks: </p>
                        </div>
                        <div class="col-sm-8 text-secondary">
                            <p  type="text"  value=""><?=$document_details['approval_remark']?></p>
                        </div>
                    </div>  
                <?php }?>
			</div>
		</div>
    </div>
</div> 
<?php
}

//SHOW DOCUMENTS FOR APPROVAL END




//CREATE FOLDER 
if(isset($_POST['folder_name'])){

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
        $add_folder_qry->execute([$folder_name,$date,1, 0]);

        echo "Folder created Successfully";

        $conn->commit();
    }catch (\Exception $e){
        $conn->rollBack();
        echo $e;
    }
}
/// END CREATE FOLDER 

//REMOVE FOLDER 
if(isset($_POST['remove_folder'])){

    $folder_id = $_POST['folder_id'];
    $date = date('Y-m-d H:i:s');
    try {
    $conn->beginTransaction();
        //1 = no 0 =yes
    $remove_folder = $conn->query("UPDATE `tbl_folders` SET `is_removed` = 0   WHERE `folder_id` = $folder_id");
  
        echo "Folder Edited Successfully";

        $conn->commit();
    }catch (\Exception $e){
        $conn->rollBack();
        echo $e;
    }
}
/// END REMOVE FOLDER 

//EDIT FOLDER 
if(isset($_POST['folder_id_edit'])){

    $folder_id = $_POST['folder_id_edit'];
    $folder_name_edit = $_POST['folder_name_edit'];
    $date = date('Y-m-d H:i:s');
    try {
    $conn->beginTransaction();
        //1 = no 0 =yes
    // $edit_folder = $conn->query("UPDATE `tbl_folders` SET `folder_name` = ".$folder_name_edit."  WHERE `folder_id` = $folder_id");
    $update_folder = $conn->prepare("UPDATE `tbl_folders` SET `folder_name` = ?  WHERE `folder_id` = ?");
    $update_folder->execute([$folder_name_edit, $folder_id]);   

  
        echo "Folder Removed Successfully";

        $conn->commit();
    }catch (\Exception $e){
        $conn->rollBack();
        echo $e;
    }
}
/// END EDIT FOLDER 



//ADD FILE FORM WILL GO HERE

if (isset($_POST['add_document_file'])){
      
    require_once '../send_mail.php';  
    $manual_cc = $_POST['manual_cc'];
    $select_approval = $_POST['select_approval'];


    // get reviewer emails //
    $select_reviewer = $_POST['select_reviewer']; // USER ID
    // get reviewer name
    $reviewer_emails = array();
    // store all email in array then pass it to send_mail()

     foreach ($select_reviewer as $reviewer_id) {         
            
        $reviewer = $conn->query("SELECT `email`, `fname` ,`lname` FROM users WHERE `user_id` = $reviewer_id");
        $reviewer_info = $reviewer->fetch();
        $email = $reviewer_info['email'];
        array_push($reviewer_emails, $email);

     }
      // get reviewer emails //


    //get approval name 
    $approver = $conn->query("SELECT `email`, `fname` ,`lname` FROM users WHERE `user_id` = $select_approval"); 

    $approver_info = $approver->fetch();

    $cc_id =  $_POST['select_cc'];    

    $cc2_emails= array();
    // store all email in array then pass it to send_mail()
        foreach ($cc_id as $id) {
            
            $cc_qry = $conn->query("SELECT `email`, `fname` ,`lname` FROM users WHERE `user_id` = $id"); 
            $cc_info = $cc_qry->fetch();
            $email = $cc_info['email'];

            array_push($cc2_emails, $email);

        }

    // print_r($cc2_emails);
    
    $project_code = $_POST['project_code'];
    $originator = $user_id;
    $discipline = $_POST['discipline'];
    $document_type = $_POST['type'];
    $document_zone = $_POST['zone'];
    $document_level = $_POST['level'];
    $select_approval = $_POST['select_approval']; // USER ID
    $date_now = date('Y-m-d H:i:s');
    $folder_id = $_POST['folder_id'];
    // $to =  $reviewer_emails;
    $cc1_email = $approver_info['email'];
    
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date_uploaded = $date_now;
    
    $reviewer_assigned = $reviewer_info['fname']." ".$reviewer_info['lname'];
    $approval_assigned = $approver_info['fname']." ".$approver_info['lname'];


    $email_status =  "FOR REVIEW";

    try {
        $conn->beginTransaction();
        // name of the uploaded file

        $date1 = date("His"); // for unique file name  
        $fileName = $_FILES['file']['name'];
        $unique_file_name = $date1.'-'.$fileName;

        // File upload configuration
        $targetDir = "../assets/media/docs/".$unique_file_name;

        // get the file extension
        $extension = pathinfo($unique_file_name, PATHINFO_EXTENSION);

        // the physical file on a temporary uploads directory on the server
        $file = $_FILES['file']['tmp_name']; 
        $size = $_FILES['file']['size'];

        if (!in_array($extension, ['zip', 'pdf', 'docx', 'xlsx'])) {
            echo "You file extension must be .zip, .pdf, .docx, 'xlsx";
        } elseif ($size > 1000000) { // file shouldn't be larger than 1Megabyte
            echo "File too large!";
        } else {

            // move the uploaded (temporary) file to the specified destination
            if (move_uploaded_file($file, $targetDir)) {
                $add_document_qry = $conn->prepare("INSERT INTO `tbl_documents`( 
                                                                            `src`, 
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
                                                                             `document_level`
                                                                            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $add_document_qry->execute([
                                         $unique_file_name,
                                         $description,
                                         $user_id,
                                         $select_approval
                                         ,$date_now,
                                         0,
                                         $title,
                                         $folder_id,                                         
                                        $project_code,                                     
                                        $discipline,
                                        $document_type,
                                        $document_zone,
                                        $document_level
                                     ]);


                $doc_id = $conn->lastInsertId();


                //SAVE EACH REVIEWER FOR THE DOCUMENT IN THE TBL_DOCUMENT_REVIEWER
                foreach ($select_reviewer as $reviewer) {
                     $add_reviewer = $conn->prepare("INSERT INTO `tbl_document_reviewer`(
                                                                    `document_id`,
                                                                    `originator_id`,
                                                                    `reviewer_id`,
                                                                    `review_status`,
                                                                    `folder_id`) 
                                                                VALUES (?,?,?,?,?)");

                    $add_reviewer->execute([$doc_id,$user_id,$reviewer, 0,$folder_id]); 
                }
                //SAVE EACH REVIEWER FOR THE DOCUMENT IN THE TBL_DOCUMENT_REVIEWER

                echo "Successfully added a new file for review.";

                send_mail(  $reviewer_emails,
                            $cc1_email,
                            $cc2_emails,
                            $manual_cc,
                            $unique_file_name,
                            $title, 
                            $description, 
                            $date_uploaded, 
                            $reviewer_assigned,
                            $approval_assigned, 
                            $email_status,
                            $folder_id);  

            } else {
                echo "failed";
            }
        }

        $conn->commit();
    }catch (\Exception $e){
        $conn->rollBack();
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
                                            `middlename`,
                                            tbl_documents.status,
                                            `lastname`
                                        FROM `tbl_documents`
                                        INNER JOIN tbl_employees ON tbl_documents.user_id = tbl_employees.employee_id
                                        WHERE tbl_documents.user_id=$user_id AND
                                        folder_id = $folder_id
                                        ORDER BY document_id DESC" );
        $document =  $get_submitted_documents->fetch();  

        $get_document_reviewers = $conn->query("SELECT 
                                                `review_document_id`,
                                                `reviewer_id`,
                                                `document_id`,
                                                `review_status`,
                                                `folder_id`
                                                `firstname`,
                                                `lastname`,
                                                `middlename`,
                                                `position`
                                                                                 

                                                FROM tbl_document_reviewer  

                                                INNER JOIN tbl_employees ON tbl_document_reviewer.reviewer_id = tbl_employees.employee_id
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
                                          `middlename`,
                                          tbl_position.position,
                                          `remarks`,
                                          tbl_documents.status,
                                          `lastname`
                                      FROM `tbl_documents`
                                      INNER JOIN tbl_employees ON tbl_documents.approval_id = tbl_employees.employee_id
                                      INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id
                                      WHERE tbl_documents.document_id = document_id  AND
                                      folder_id = $folder_id
                                        ORDER BY document_id DESC" );

        $docs3 =  $approval_status_qry->fetch();  

 

       
      ?>            
                <style type="text/css">
                    thead,th,label{
                        font-weight: bolder;
              
                    }
                    <?php echo '.'.$document['status'].'{
//                          background-color: green;
                        color:  white;

                    }' ?>
                    .bold-label{
                        font-weight: bolder;
                    }
                
                </style>
                  <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <thead>
                                    <th class="bold-label">Project Code</th>
                                    <th class="bold-label">Sequence No.</th>
                                    <th class="bold-label">Originator</th>
                                    <th class="bold-label">Discipline</th>
                                    <th class="bold-label">Type</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>                      
                                            <p><?=$document['project_code'] ?><p> 
                                        </td>
                                        <td>                      
                                            <p><?=$document['document_id'] ?><p> 
                                        </td>
                                        <td>                      
                                            <p><?=$document['user_id'] ?><p> 
                                        </td>
                                        <td>                      
                                            <p><?=$document['discipline'] ?><p> 
                                        </td>
                                        <td>                      
                                            <p><?=$document['document_type'] ?><p> 
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
                                            <p><?=$document['document_zone'] ?><p> 
                                        </td>
                                        <td>                      
                                            <p><?=$document['date_uploaded'] ?><p> 
                                        </td>
                                        <td>                      
                                            <p><?=$document['title'] ?><p> 
                                        </td>
                                        <td>                      
                                            <p><?=$document['src'] ?><p> 
                                        </td>
                                        <td>                      
                                            <p><?=$document['status'] ?><p> 
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
                                            <td ></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
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
                                            <td ></td>
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
                                    <thead >
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
                                    <thead >
                                      <th class="text-center" colspan="4">Contractors Review Status</th>
                                    </thead>
                                    <thead>
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>Position </th>
                                        <th>Status</th>
                                    </thead>
                                    <tbody>
                             
                                        <?php foreach ($docs2 as $doc2): ?>
                                            <?php $full_name =  $doc2['firstname']." ".$doc2['middlename']." ".$doc2['lastname'] ?>
                                            <tr> 
                                                <?php 
                                                    $color = '';
                                                    $rev_status =  $doc2['review_status'];
                                                    if ($rev_status == "A") {
                                                        $color =  "green";
                                                    } elseif ($rev_status == "B") {
                                                        $color =  "ORANGE";
                                                    } elseif ($rev_status == "C") {
                                                        $color =  "pink";
                                                    } elseif ($rev_status == "D") {
                                                        $color =  "red";
                                                    } elseif ($rev_status == "E") {
                                                        $color =  "yellow";
                                                    } elseif ($rev_status == "F") {
                                                        $color =  "grey";
                                                    }
                                                ?>
                                                <td><?=$doc2['review_document_id']?></td>
                                                <td><?=$full_name ?></td>
                                                <td><?=$doc2['position']?></td>
                                                <td class="review-status" style="background-color: <?=$color; ?>"><?=$doc2['review_status']?></td>                                           
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
                                        <thead >
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
                                                $approval_fullname = $docs3['firstname']." ".$docs3['middlename']." ".$docs3['lastname'];

                                                    $color2 = '';
                                                   $app_status = $docs3['status'] ;
                                                    if ($app_status== "A") {
                                                        $color2 =  "green";
                                                    } elseif ($app_status == "B") {
                                                        $color2 =  "ORANGE";
                                                    } elseif ($app_status == "C") {
                                                        $color2 =  "pink";
                                                    } elseif ($app_status == "D") {
                                                        $color2 =  "red";
                                                    } elseif ($app_status == "E") {
                                                        $color2 =  "yellow";
                                                    } elseif ($app_status == "F") {
                                                        $color2 =  "grey";
                                                    }                                            

                                                 ?>
                                                <th><?=$approval_fullname ?></th>
                                                <td><?=$docs3['position']?></td>
                                                <td><?php

                                                        if ($docs3['remarks'] == "" ) {
                                                                echo "NONE";                  
                                                        } else  {
                                                            echo $docs3['remarks'];
                                                        }                                       
                                                     ?>

                                                <td style="background-color: <?=$color2; ?>"><?=$docs3['status']?></td>                                                      
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

if (isset($_POST['load_documents_for_review'])) {
        
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
                                            `profile_pic`,
                                            `discipline`,
                                             `fname`,
                                            `lname`
                                        FROM `tbl_documents`
                                        INNER JOIN users ON tbl_documents.user_id = users.user_id
                                        WHERE tbl_documents.user_id=$user_id AND
                                        document_id = $document_id
                                        ORDER BY document_id DESC" );


        $document =  $get_submitted_documents->fetch();  
    //GET SUBMITTED DOCUMENTS  
    
    //GET REVIEWERS DOCUMENTS

        $get_document_reviewers = $conn->query("SELECT 
                                                `review_document_id`,
                                                `reviewer_id`,
                                                `document_id`,
                                                `review_status`,
                                                `folder_id`,
                                                `review_status`,                                           
                                                `lname`,
                                                `fname`,
                                                `position`                                                                                 

                                                FROM tbl_document_reviewer  

                                                INNER JOIN users ON tbl_document_reviewer.reviewer_id = users.user_id
                                                WHERE document_id = $document_id");

        $docs2 = $get_document_reviewers->fetchAll();
    //GET REVIEWERS DOCUMENTS

    //GET APPROVAL STATUS
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
                                `fname`,
                               
                               tbl_position.position,
                               `remarks`,
                               tbl_documents.status,
                               `lname`
                           FROM `tbl_documents`
                           INNER JOIN users ON tbl_documents.approval_id = users.user_id
                           INNER JOIN tbl_position ON users.position = tbl_position.position_id
                           WHERE tbl_documents.document_id = $document_id  AND
                           folder_id = $folder_id
                             ORDER BY document_id DESC" );

        $docs3 =  $approval_status_qry->fetch();  
    //GET APPROVAL STATUS

    //GET REVIEWER COMMENTS

      $reviewer_comments_qry = $conn->query("SELECT `document_id`,
                                                    `response_id`,
                                                    `comment`,
                                                    `pages`,
                                                    `reviewer_id`,
                                                    `reply`
                                                    `fname`,
                                                    `lname`
                                            
                                            FROM `tbl_document_comments_replies`
                                            INNER JOIN users ON tbl_document_comments_replies.reviewer_id = users.user_id                                         
                                            WHERE tbl_document_comments_replies.document_id = $document_id  AND
                                            `reviewer_id` = $user_id
                                            
                                            ORDER BY document_id DESC" );

        $rev_comments =  $reviewer_comments_qry->fetchAll();
        // GET REPLIES
        $get_replies_qry = $conn->query("SELECT `document_id`,
                                                    `response_id`,
                                                    `comment`,
                                                    `pages`,
                                                    `reviewer_id`,
                                                    `reply`
                                                    `fname`,
                                                    `lname`
                                            
                                            FROM `tbl_document_comments_replies`
                                            INNER JOIN users ON tbl_document_comments_replies.reviewer_id = users.user_id                                         
                                            WHERE tbl_document_comments_replies.document_id = $document_id  AND
                                            `reviewer_id` = $user_id
                                            
                                            ORDER BY document_id DESC" );

        $rev_replies =  $get_replies_qry->fetchAll();
        
         //GET REVIEWER COMMENTS
        $current_reviewer_status= '';
        $set_status_color = '';

        // SET COLOR  DOCUMENT REVIEW STATUS CODE
        if($current_reviewer_status == "A"){
                $set_status_color = 'red';

        } elseif($current_reviewer_status == "B") { 
            $set_status_color = 'black';

        } elseif($current_reviewer_status == "C") { 
            $set_status_color = 'blue';
        } elseif($current_reviewer_status == "D") { 
            $set_status_color = 'green';
        }
    
       
      ?>            
                <style type="text/css">
                    thead,th,label{
                        font-weight: bolder;
              
                    }
                    <?php echo '.'.$document['status'].'{
                         background-color: green;
                        color:  white;

                    }' ?>
                    .bold-label{
                        font-weight: bolder;
                    }
                
                </style>
                             <div class="container " >
                    <div class="content">
                        <h2 class="content-heading"><?php  ?></h2>
                        <!-- DIV BLOCK  -->
                        <div class="block">
                          <!--   <div class="block-header block-header-default bg-pattern bg-black-op-25">
                                <button type="button" class="btn btn-sm btn-alt-primary btn-rounded float-right" onclick="action_modal_show()">Action </button>
                                    <button class="btn btn-sm btn-alt-primary btn-rounded float-right" onclick="history.back()"><i class="si si-action-undo"></i> Go Back</button>
                            </div> -->
                        </div>
                        <!-- DIV BLOCK  -->  
                    <div class="container" id ="background">
                        <div class="form-group" >
                          
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered">
                                    <thead>
                                        <th class="bold-label">Project Code</th>
                                        <th class="bold-label">Sequence No.</th>
                                        <th class="bold-label">Originator</th>
                                        <th class="bold-label">Discipline</th>
                                        <th class="bold-label">Type</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>                      
                                                <p><?=$document['project_code'] ?><p> 
                                            </td>
                                            <td>                      
                                                <p id="document_id"><?=$document['document_id'] ?><p> 
                                            </td>
                                            <td>                      
                                                <p><?=$document['user_id'] ?><p> 
                                            </td>
                                            <td>                      
                                                <p><?=$document['discipline'] ?><p> 
                                            </td>
                                            <td>                      
                                                <p><?=$document['document_type'] ?><p> 
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
                                                <p><?=$document['document_zone'] ?><p> 
                                            </td>
                                            <td>                      
                                                <p><?=$document['date_uploaded'] ?><p> 
                                            </td>
                                            <td>                      
                                                <p><?=$document['title'] ?><p> 
                                            </td>
                                            <td>                      
                                                <p><?=$document['src'] ?><p> 
                                            </td>
                                            <td>                      
                                                <p><?=$document['status'] ?><p> 
                                            </td>
                                        </tr>                                        
                                    </tbody>
                                </table>
                            </div>                        
                            <div class="col-sm-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <th>No.</th>
                                            <th>Initial</th>
                                            <th>Page/Section</th>
                                            <th>Comment Code</th>
                                            <th>Reviewers Comments</th>
                                           
                                        </thead>
                                        <tbody>
                                            <?php $counter = 1; ?>
                                            <?php foreach ($rev_comments as $rev_comments): ?>
                                               <?php 
                                                $first_name = $rev_comments['fname'];
                                                $last_name = $rev_comments['lname']
                                             ?> 
                                             <tr>
                                                <td><?=$counter?></td>
                                                <td><?=$first_name." ".$last_name ?></td>
                                                <td><?=$rev_comments['pages']?></td>
                                                <td><?=$rev_comments['response_id']?></td>
                                                <td><?=$rev_comments['comment']?></td>
                                             </tr>
                                            
                                            <?php $counter++; ?>
                                            <?php endforeach ?>
                                       
                                     
                                        </tbody>
                                    </table>  
                                </div>                            
                                <div class="col-sm-12">
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


                                            <?php $counter2 = 1; ?>

                                           
                                            <?php foreach ($rev_replies as $rev_rep): ?>
                                               <?php 
                                                $first_name = $rev_rep['fname'];
                                                $last_name = $rev_rep['lname']

                                             ?> 
                                             <tr>
                                                <td><?=$counter2?></td>
                                                <td><?=$first_name." ".$last_name ?></td>
                                                <td><?=$rev_rep['pages']?></td>
                                                <td><?=$rev_rep['response_id']?></td>
                                                <td><?=$rev_rep['comment']?></td>
                                             </tr>
                                            
                                            <?php $counter2++; ?>
                                            <?php endforeach ?>                                       
                                     
                                        </tbody>
                                    </table>
                                </div>                            
                                <div class="col-sm-12">
                                    <table class="table table-bordered">
                                        <thead >
                                        <th class="text-center" colspan="3">Document Review Status Code</th>
                                        
                                        </thead>
                                        <tbody>
                                            <tr>                                           
                                                <td class="A">A. Approved with comments</td>
                                                <td class="B">B. SONO</td>
                                                <td class="C">C. Fail/Not Approved</td>                                           
                                            </tr>
                                            <tr>
                                                <td class="D">D. Approved without Comments</td>
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
                                        <thead >
                                        <th class="text-center" colspan="4">Contractors Review Status</th>
                                        </thead>
                                        <thead>
                                            <th>No.</th>
                                            <th>Name</th>
                                            <th>Position </th>
                                            <th>Status</th>
                                        </thead>
                                        <tbody>
                                        <!-- docs2 GET REVIEWERS -->
                                            <?php foreach ($docs2 as $doc2): ?>
                                                <?php $full_name =  $doc2['fname']." ".$doc2['lname'] ?>
                                                <tr> 
                                                    <?php 
                                                        $color = '';
                                                        $rev_status =  $doc2['review_status'];
                                                        if ($rev_status == "A") {
                                                            $color =  "green";
                                                        } elseif ($rev_status == "B") {
                                                            $color =  "ORANGE";
                                                        } elseif ($rev_status == "C") {
                                                            $color =  "pink";
                                                        } elseif ($rev_status == "D") {
                                                            $color =  "red";
                                                        } elseif ($rev_status == "E") {
                                                            $color =  "yellow";
                                                        } elseif ($rev_status == "F") {
                                                            $color =  "grey";
                                                        }                                                

                                                        // GET THE CURRENT REVIEWERS REVIEW STATUS
                                                        // CURRENT REVIEWER IS THE ONE GOING TO ACT ON THE DOCUMENT. WE NEED TO DISTINGUISH BECAUSE THERE ARE MULTIPLE REVIEWER IN ONE DOCUMENT
                                                        // WE NEED TO SET A VARIABLE'S STATUS IN ORDER TO SET THE STATUS COLOR IN THE DOCUMENT REVIEW STATUS CODE
                                                        if ($doc2['reviewer_id'] == $user_id) {
                                                            $current_reviewer_status = $doc2['review_status'];
                                                        }
                                                    ?>


                                                    <td><?=$doc2['review_document_id']?></td>
                                                    <td><?=$full_name ?></td>
                                                    <td><?=$doc2['position']?></td>
                                                    <td class="review-status" style="background-color: <?=$color; ?>"><?=$doc2['review_status']?></td>                                           
                                                </tr>
                                            <?php endforeach ?>              
                                        </tbody>
                                    </table>
                                </div>                           
                            </div> 
                        </div>
                        <br>
                    </div>
                </div>
<?php 

}


if (isset($_POST['action_type'])) {

    $doc_id = $_POST['doc_id'];
    $rev_id = $_POST['rev_id']; // this id is dynamic, either for REVIEWER or APPROVAL
    $cc_id = array();
    $action_type = $_POST['action_type'];
    $action_id = $_POST['action_id'];
    $set_status = '';
    $actor_type = $_POST['actor_type'];
    //actor_type, either reviewer or approval
    // ACTION ID 1 = REJECT 2 = APPROVE
    //ACTION TYPE = 1 WITH COMMENT , 2 WITHOUT COMMENT

    

try {
        $conn->beginTransaction();
    if ($action_type == 1) { // WITH COMMENT
        
            $set_status = "B";
        if($action_id == 1){
            $set_status = "A";
        }

            // SAVE COMMENTS
            $comment_length=(sizeof($_POST) -7) /2;

                    for ($i=0; $i <  $comment_length; $i++) { 
                    $page = "pages".$i;
                    $page2 = $_POST[$page];
                    $comment = "comments".$i;
                    $comment2 = $_POST[$comment];
                    $add_comment_qry = $conn->prepare("INSERT INTO 
                                                        `tbl_document_comments_replies`                 
                                                        (`document_id`, 
                                                            `reviewer_id`, 
                                                            `comment`, 
                                                            `pages`) 
                                                    VALUES (?,?,?,?)");
                    $add_comment_qry->execute([$doc_id,$rev_id,$comment2, $page2]);
                }
                // SAVE COMMENTS
                // name of the uploaded file

                $date1 = date("His"); // for unique file name  
                $fileName = $_FILES['review_file']['name'];
                $unique_file_name = $date1.'-'.$fileName;

                // File upload configuration
                $targetDir = "../assets/media/docs/".$unique_file_name;
                // get the file extension
                $extension = pathinfo($unique_file_name, PATHINFO_EXTENSION);
                // the physical file on a temporary uploads directory on the server
                $file = $_FILES['review_file']['tmp_name']; 
                $size = $_FILES['review_file']['size'];

                if ($actor_type ==1 ) {
                    $document_src_column = 'review_document_src';
                } else {
                    $document_src_column = 'approve  _document_src';
                }

                if (!in_array($extension, ['zip', 'pdf', 'docx', 'xlsx'])) {
                    echo "You file extension must be .zip, .pdf, .docx, 'xlsx";
                } elseif ($size > 1000000) { // file shouldn't be larger than 1Megabyte
                    echo "File too large!";
                } else {

                    // move the uploaded (temporary) file to the specified destination
                    if (move_uploaded_file($file, $targetDir)) {
                    // INSERT FILE TO TBL_DOCUMENTS FOR THE REVIEWAL/ COMMENT                  
                    $update_file = $conn->prepare("UPDATE `tbl_documents` SET 
                    $document_src_column =?  WHERE 
                    `document_id` = ?");

                     $update_file->execute([$unique_file_name, $doc_id]); 

                        echo "Successfully Acted on the Document";

                    } else {
                        echo "Failed Action on Document";
                    }
                }
        } else {
            $set_status = "C";
            if($action_id == 2){
                $set_status="D";
            }
        }
            // WITHOUT COMMENTS
            // UPDATE STATUS FOR REVIEW OR APPROVAL
            if ($actor_type == 1) {
                $update_review = $conn->prepare("UPDATE `tbl_document_reviewer` SET 
                        `review_status` =?  WHERE 
                        `document_id` = ? AND 
                        `reviewer_id` = ?");

                $update_review->execute([$set_status, $doc_id, $rev_id]); 
            } else {
                 $update_review = $conn->prepare("UPDATE `tbl_documents` SET 
                        `status` =?  WHERE 
                        `document_id` = ? AND 
                        `approval_id` = ?");

                $update_review->execute([$set_status, $doc_id, $rev_id]); 
            }
           
            echo "Successfully Acted on the Document! ";
         $conn->commit();
    }catch (Exception $e){
        $conn->rollBack();
        echo $e;
    }  
}
?>