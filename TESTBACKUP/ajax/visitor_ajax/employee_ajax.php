<?php

include '../../conn.php';
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
if (isset($_POST['load_company_table'])){
    $get_company_qry = $conn->query("SELECT * FROM tbl_company WHERE is_deleted = 0");
    $get_company = $get_company_qry->fetchAll();


    ?>

    <div class="row justify-content-center">
        <div class="col-xl-8  " >
            <div class="content">

                <div class="block">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Companies</h3>
                        <div class="block-options">
                            <div class="block-options-item">
                                <!--                                <code>.table</code>-->
                            </div>
                        </div>
                    </div>
                    <div class="block-content">
                        <table class="table table-vcenter" id="Company_table">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th class="text-center">Company</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($get_company as $company):?>
                                <tr>
                                    <th class="text-center" scope="row"><?=$company['company_id']?></th>
                                    <th class="text-center" scope="row"><?=$company['company_name']?></th>

                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}
$conn = null;