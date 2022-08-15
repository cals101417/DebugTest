<?php
require_once '../session.php';

if (isset($_POST['delete_attendance'])){
    $attendance_id = $_POST['attendance_id'];
    try {
        $update = $conn->query("UPDATE `tbl_attendance` SET `is_deleted`= 1  WHERE attendance_id = $attendance_id");
        echo 'Attendant successfully removed';
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}

if (isset($_POST['remove_attendance'])){
    $attendances_id = $_POST['attendances_id'];
    try {
        $remove_attendance = $conn->prepare("UPDATE `tbl_attendanc_atterndances` SET `is_removed` = 1 WHERE attendances_id = ?");
        $remove_attendance->execute([$attendances_id]);
        echo 'Trainee successfully removed';
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}
if (isset($_POST['active_inactive_user'])){
    $attendance_id = $_POST['attendance_id'];
    $val = $_POST['val'];

    try {
        $active_inactive_user = $conn->prepare("UPDATE `tbl_attendance` SET `Status` = ? WHERE attendance_id = ?");
        $active_inactive_user->execute([$val,$attendance_id]);
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}

if (isset($_POST['view_toolbox_details'])){
    $toolbox_id = $_POST['view_toolbox_details'];
    $type = $_POST['toolbox_type'];

    if ($type == 'civil'){
        $attendance_qry = $conn->query("SELECT `attendance_id`,
                                                `Name`,
                                                `Description`,
                                                `Status`,
                                                `Date_Created`,
                                                `User_ID`,
                                                `Is_Deleted`
                                                FROM `tbl_attendance`
                                                WHERE attendance_id = $toolbox_id");
        $attendance_details = $attendance_qry->fetch();
        $status = $attendance_details['Status'];

        ?>

        <!-- Page Content -->
        <div class="content">
            <h2 class="content-heading pt-0">
                <?=ucwords($attendance_details['Name'])?>
            </h2>
            <table class="table table-striped table-borderless table-sm mt-20">
                <tbody>
                <tr>
                    <td class="font-w600">Name</td>
                    <td><?=$attendance_details['Name']?></td>
                </tr>
                <tr>
                    <td class="font-w600">Date of Training</td>
                    <td><?=date('F d Y', strtotime($attendance_details['Date_Created']))?></td>
                </tr>
                <tr>
                    <td class="font-w600">Description</td>
                    <td><?=$attendance_details['Description']?></td>
                </tr>
                <tr>
                    <td class="font-w600">Status</td>
                    <td>
                        <?php
                        if($status == 0){
                            echo 'Active';
                        }else{
                            echo 'Inactive';
                        }
                        ?>
                    </td>
                </tr>

                </tbody>
            </table>
            <div class="block">
                <div class="block-header block-header-default">
                    <h3 class="block-title">List of Participants</h3>
                    <div class="block-options">
                        <div class="block-options-item">
                            <!--                                <code>.table</code>-->
                        </div>
                    </div>
                </div>
                <div class="block-content">
                    <table class="table table-vcenter">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th class="text-center">Employee name</th>
                            <th class="text-center" style="width: 30%;">Date Added</th>
                            <th class="text-center" style="width: 20%;">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $attendances = $conn->query("SELECT
                                                                tbl_attendanc_atterndances.attendances_id,
                                                                tbl_employees.employee_id,
                                                                tbl_employees.firstname,
                                                                tbl_attendanc_atterndances.attendance_id AS is_removed,
                                                                tbl_attendanc_atterndances.date_created,
                                                                tbl_employees.lastname
                                                                FROM
                                                                tbl_attendanc_atterndances
                                                                INNER JOIN tbl_employees ON tbl_attendanc_atterndances.employee_id = tbl_employees.employee_id
                                                                WHERE
                                                                tbl_attendanc_atterndances.attendance_id = $toolbox_id AND
                                                                tbl_attendanc_atterndances.is_removed = 0
                                                                ");
                        $count = 1;
                        foreach ($attendances as $attendance) {
                            $emplyee_id = $attendance ['employee_id'];
                            $fullname = ucwords(strtolower($attendance['firstname'] .' '.$attendance['lastname']));
                            $attendances_id = $attendance ['attendances_id'];
                            $date_added = $attendance ['date_created'];

                            ?>
                            <tr>
                                <th class="text-center" scope="row"><?=$emplyee_id?></th>
                                <td class="text-center"><?=$fullname?></td>
                                <td class="text-center"><?=date('F d, Y', strtotime($date_added))?></td>
                                <td class="d-none d-sm-table-cell text-center">
                                    <span class="badge badge-secondary">N/A</span>
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
    <?php
    }
}

if (isset($_POST['add_new_toolbox_talks'])){
    $name = $_POST['name'];
    $description = $_POST['description'];
    $date = date('Y-m-d H:i:s');
    try {
        $stmt = $conn->prepare("INSERT INTO `tbl_attendance`( `Name`, `Description`, `Status`, `Course`, `Date_Created`, `User_ID`, `Is_Deleted`) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$name,$description,0,1,$date,$user_id,0]);
        echo 'success';
    }catch (Exception $e){
        echo $e;
    }
}

$conn = null;