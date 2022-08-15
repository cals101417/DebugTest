<?php
require_once '../session.php';

if (isset($_POST['add_employee'])){
    $user_id = $_SESSION['user_id'];
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $company = $_POST['company'];
    $select_company_type = $_POST['select_company_type'];
    $position = $_POST['select_position'];
    $department = $_POST['select_department'];
    $nationality = $_POST['select_nationality'];
    $date = date('Y-m-d H:i:s');
    $birth_date = $_POST['birth_date'];

    if (!empty($_FILES["employee_img"]["name"])){
        // Allow certain file formats
        // File upload path
        $targetDir = "../assets/media/photos/employee/";
        $fileName = basename($_FILES["employee_img"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

        $allowTypes = array('jpg','png','jpeg','gif');
        if(in_array($fileType, $allowTypes)){
            // Upload file to server
            if(move_uploaded_file($_FILES["employee_img"]["tmp_name"], $targetFilePath)){
                // Insert image file name into database
                try {
                    $insert = $conn->query("INSERT INTO `tbl_employees`(`sub_id`,`birth_date`,`firstname`, `middlename`, `lastname`, `phone_no`,`email`,`img_src`, `company`, `company_type`, `position`, `department`, `nationality`, `is_active`, `is_deleted`, `date_created`, `user_id`) 
                                        VALUES ('$subscriber_id','$birth_date','$fname','$mname','$lname','$phone','$email','$fileName','$company','$select_company_type',$position,$department,$nationality,0,0,'$date',$user_id)");
                    echo 'Employee successfully added';
                }catch (Exception $e){
                    echo 'Sorry, Something wen\'t wrong';
                }

            }else{
                $statusMsg = "Sorry, there was an error uploading your file.";
            }
        }else{
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
        }
    }else{
        try {
            $insert = $conn->query("INSERT INTO `tbl_employees`(`sub_id`,`birth_date`,`firstname`, `middlename`, `lastname`, `phone_no`,`email`, `position`, `department`, `nationality`, `is_active`, `is_deleted`, `date_created`, `user_id`,`company_type`) 
                                        VALUES ('$subscriber_id','$birth_date','$fname','$mname','$lname','$phone','$email',$position,$department,$nationality,0,0,'$date',$user_id,$select_company_type)");
            echo 'Employee successfully added';
        }catch (Exception $e){
            echo 'Sorry, Something wen\'t wrong';
        }
    }
}

if (isset($_POST['update_employee'])){
    $employee_id = $_POST['update_employee'];
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $company = $_POST['company'];
    $select_company_type = $_POST['select_company_type'];
    $position = $_POST['select_position'];
    $department = $_POST['select_department'];
    $nationality = $_POST['select_nationality'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $date_now = date('Y-m-d H:i:s');
    $is_active = $_POST['select_active'];
    $employee_img_dir = '';
    $birth_date = $_POST['birth_date'];
//
    try {
        $employee_qry = $conn->query("SELECT img_src FROM tbl_employees WHERE tbl_employees.employee_id = $employee_id");
        $employee = $employee_qry->fetch();
        $employee_img_src = $employee['img_src'];
        $employee_img_dir = '../assets/media/photos/employee/'.$employee_img_src;
    }catch (Exception $e){
        echo $e;
    }

    if (!empty($_FILES["employee_img"]["name"])){
        // Allow certain file formats
        // File upload path
        $targetDir = "../assets/media/photos/employee/";
        $fileName = basename($_FILES["employee_img"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

        $allowTypes = array('jpg','png','jpeg','gif');
        if(in_array($fileType, $allowTypes)){
            // Upload file to server
            if(move_uploaded_file($_FILES["employee_img"]["tmp_name"], $targetFilePath)){
                // Update image file name into database
                unlink($employee_img_dir);
                try {
                    $update = $conn->prepare("UPDATE `tbl_employees` 
                                        SET `firstname` = ?,
                                        `middlename` = ?,
                                        `lastname` = ?,
                                        `birth_date` = ?,
                                        `phone_no` = ?,
                                        `email` = ?,
                                        `img_src` = ?,
                                        `company` = ?,
                                        `company_type` = ?,
                                        `position` = ?,
                                        `department` = ?,
                                        `nationality` = ?,
                                        `is_active` = ?,
                                        `date_updated` = ?
                                        WHERE
                                            `employee_id` = ?");
                    $update->execute([$fname,$mname,$lname,$birth_date,$phone,$email,$fileName,$company,$select_company_type,$position,$department,$nationality,$is_active,$date_now,$employee_id]);

                    echo 'Employee successfully updated';
                }catch (Exception $e){
                    echo 'Sorry, Something wen\'t wrong'.$e;
                }

            }else{
                $statusMsg = "Sorry, there was an error uploading your file.";
            }
        }else{
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
        }
    }else{
        try {
            $update = $conn->prepare("UPDATE `tbl_employees` 
                                            SET `firstname` = ?,
                                            `middlename` = ?,
                                            `lastname` = ?,
                                            `birth_date` = ?,
                                            `phone_no` = ?,
                                            `email` = ?,
                                            `company` = ?,
                                            `company_type` = ?,
                                            `position` = ?,
                                            `department` = ?,
                                            `nationality` = ?,
                                            `is_active` = ?,
                                            `date_updated` = ?
                                            WHERE
                                            `employee_id` = ?");
            $update->execute([$fname,$mname,$lname,$birth_date,$phone,$email,$company,$select_company_type,$position,$department,$nationality,$is_active,$date_now,$employee_id]);

            echo 'Employee successfully updated';
        }catch (Exception $e){
            echo 'Sorry, Something wen\'t wrong'.$e;
        }
    }
}

if (isset($_POST['delete_employee'])){
    $employee_id = $_POST['employee_id'];
    try {
        $update = $conn->query("UPDATE `tbl_employees` SET `is_deleted`= 1  WHERE employee_id = $employee_id");
        echo 'Employee successfully removed';
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}

// AJAX CALL FOR FETCHING EMPLOYEE DETAILS
if (isset($_POST['employee_profile_info'])){
    $employee_id = $_POST['employee_id'];

    $employee_details = $conn->query("SELECT
                                                tbl_employees.firstname,
                                                tbl_employees.middlename,
                                                tbl_employees.lastname,
                                                tbl_employees.img_src,
                                                tbl_position.position
                                                FROM
                                                tbl_employees
                                                INNER JOIN tbl_position ON tbl_position.position_id = tbl_employees.position
                                                WHERE
                                                tbl_employees.employee_id = $employee_id");
    $employee_details = $employee_details->fetch();
    $employee_fullname = ucwords(strtolower($employee_details['firstname'].' '.$employee_details['lastname']));
    $img_src = $employee_details['img_src'];
    if ($employee_details['img_src'] == null || $employee_details['img_src'] == ''){
        $img = 'assets/media/avatars/avatar0.jpg';
    }else{
        $img = 'assets/media/photos/employee/'.$img_src;
    }
    ?>
    <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
        <div class="block-content bg-gd-dusk">
            <div class="push">
                <img class="img-avatar img-avatar-thumb" src="<?=$img?>" alt="">
            </div>
            <div class="pull-r-l pull-b py-10 bg-black-op-25">
                <div class="font-w600 mb-5 text-white">
                    <?=$employee_fullname?> <i class="fa fa-star text-warning"></i>
                </div>
                <div class="font-size-sm text-white-op"><?=$employee_details['position']?></div>
            </div>
        </div>
        <div class="block-content bg-black-op-10">
            <div class="row items-push text-center">
                <div class="col-6">
                    <div class="mb-5"><i class="fa fa-clipboard fa-2x"></i></div>
                    <div class="font-size-sm text-muted">0 Trainings</div>
                </div>
                <div class="col-6">
                    <div class="mb-5"><i class="fa fa-check fa-2x"></i></div>
                    <div class="font-size-sm text-muted">15 Finished</div>
                </div>
            </div>
        </div>
    </a>
    <?php
}

if (isset($_POST['employee_basic_info'])){
    $employee_id = $_POST['employee_id'];

    $employee_details = $conn->query("SELECT
                                                tbl_position.position,
                                                tbl_employees.phone_no,
                                                tbl_employees.firstname,
                                                tbl_employees.lastname,
                                                tbl_employees.email,
                                                tbl_employees.company,
                                                tbl_employees.company_type,
                                                tbl_employees.nationality,
                                                tbl_department.department,
                                                tbl_nationalities.`name`
                                                FROM
                                                tbl_employees
                                                INNER JOIN tbl_position ON tbl_position.position_id = tbl_employees.position
                                                INNER JOIN tbl_department ON tbl_department.department_id = tbl_employees.department
                                                INNER JOIN tbl_nationalities ON tbl_nationalities.id = tbl_employees.nationality
                                                WHERE
                                                tbl_employees.employee_id = $employee_id");
    $employee_details = $employee_details->fetch();
    $employee_fullname = ucwords(strtolower($employee_details['firstname'].' '.$employee_details['lastname']));
    if ($employee_details['company_type'] == null || $employee_details['company_type'] == ''){
        $company_type = '';
    }else{
        $company_type = "(".$employee_details['company_type'].")";
    }
    ?>
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Basic Info</h3>
        </div>
        <div class="block-content">
            <div class="font-size-lg text-black mb-5"><?=$employee_fullname?></div>
            <address>
                Phone <i class="fa fa-phone mr-5"></i>: <?=$employee_details['phone_no']?><br>
                Email: <?=$employee_details['email']?><br>
                Job Title: <?=$employee_details['position']?><br>
                Company: <?=$employee_details['company']?> <i><?=$company_type?></i><br>
                Department: <?=$employee_details['department']?><br>
                Nationality: <?=$employee_details['name']?><br><br>
            </address>
        </div>
    </div>
    <?php
}

if (isset($_POST['employee_trainings_info'])){
    $employee_id = $_POST['employee_id'];

    $trainings = $conn->query("SELECT
                                        tbl_trainings.training_id,
                                        tbl_trainings.title,
                                        tbl_trainings.trainer,
                                        tbl_trainings.`status`,
                                        tbl_trainings.date_created,
                                        tbl_trainings_type.title AS type
                                        FROM
                                        tbl_training_trainees
                                        INNER JOIN tbl_trainings ON tbl_trainings.training_id = tbl_training_trainees.training_id
                                        INNER JOIN tbl_trainings_type ON tbl_trainings_type.training_type_id = tbl_trainings.type
                                        WHERE
                                        tbl_trainings.is_deleted = 0 AND tbl_training_trainees.is_removed = 0 AND
                                        tbl_training_trainees.employee_id = $employee_id");
    $count = 1;
    foreach ($trainings as $training) {
    ?>
        <tr>
            <th class="text-center" scope="row">5</th>
            <td><?=$training['title']?></td>
            <td class="text-center"><?=$training['trainer']?></td>
            <td class="text-center"><?=$training['type']?></td>
            <td class="text-center"><?=date('M. d, Y', strtotime($training['date_created']))?></td>
            <td class="d-none d-sm-table-cell text-center">
                <span class="badge badge-info">Scheduled</span>
            </td>
            <td class="text-center">
                <a href="training_in_house.php?view_training=<?=$training['training_id']?>" target="_blank" class="btn btn-sm btn-primary js-tooltip-enabled" data-toggle="tooltip" title="View training details" data-original-title="Edit">
                    <i class="si si-action-redo"></i>
                </a>
            </td>
        </tr>
    <?php
    }
}
$conn = null;