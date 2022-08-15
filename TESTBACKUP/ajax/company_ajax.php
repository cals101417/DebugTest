<?php
require_once '../session.php';
if (isset($_POST['add_company'])){
    try {
        $company_name = $_POST['company'];
        $sub_id = $subscriber_id;
        $is_deleted = 0;
        $add_company = $conn->query("INSERT INTO `tbl_company`(`company_name`,`is_deleted`,`sub_id`) 
                                        VALUES ('$company_name',$is_deleted,$sub_id)");

        echo "SUCCESSFULLY CREATED COMPANY";
    } catch (Exception $e) {
        echo $e;
    }
}
if (isset($_POST['load_company_table'])){
    $get_company_qry = $conn->query("SELECT * FROM tbl_company WHERE is_deleted = 0");
    $get_company = $get_company_qry->fetchAll();

    $add_comp_access  = $conn->query("SELECT * FROM user_access WHERE access_type_id = 23 AND user_id = $session_emp_id ");
    $add_comp = $add_comp_access->fetch();
    $disabled = '';
    (($add_comp['status'] == 0 )?$disabled =  '': $disabled = "disabled");
?>

    <div class="row justify-content-center">
        <div class="col-xl-8  " >
            <div class="content">
                <h2 class="content-heading">
                    <button type="button" <?=$disabled?> class="btn btn-sm btn-rounded btn-success float-right" data-toggle="modal" data-target="#add_company_modal" >Add New Company</button>
                </h2>
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
                                <th class="text-center" style="width: 10%;text-transform: capitalize; !important;">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($get_company as $company):?>
                                <tr>
                                    <th class="text-center" scope="row"><?=$company['company_id']?></th>
                                    <th class="text-center" scope="row"><?=$company['company_name']?></th>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" <?=$disabled?> class="btn btn-sm btn-success js-tooltip-enabled" onclick="show_modal('<?=$company['company_name']?>',<?=$company['company_id']?>)" >
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                            <button type="button" <?=$disabled?>  class="btn btn-sm btn-danger js-tooltip-enabled" id="delete" onclick="delete_company(<?=$company['company_id']?>)" data-toggle="tooltip" title="Delete Company" data-original-title="Delete">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
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
if (isset($_POST['delete_company'])){
    $company_id = $_POST['company_id'];
    try {
        $update = $conn->query("UPDATE `tbl_company` SET `is_deleted`= 1  WHERE company_id = $company_id");
        echo 'company successfully removed';
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}
if (isset($_POST['edit_company'])){
    $company_id = $_POST['edit_company'];
    $company_name = $_POST['edit_company_name'];
    try {
        $update = $conn->query("UPDATE `tbl_company` SET `company_name`= '$company_name'  WHERE company_id = $company_id");
        echo 'company successfully Updated';
    }catch (Exception $e){
        echo 'Sorry, Something wen\'t wrong';
    }
}

// AJAX CALL FOR FETCHING company DETAILS


$conn = null;