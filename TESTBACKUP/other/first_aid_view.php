<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';

$type_id = 1;
?>
<div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
<?php
include 'includes/sidebar.php';
include 'includes/header.php';
?>

<?php
$get_emp_qry = $conn->query("SELECT * FROM tbl_employees WHERE is_deleted = 0 ORDER BY firstname  ASC");
$employee_list = $get_emp_qry->fetchAll();

$fa_id = $_GET['fa_id'];
if (isset($_GET['fa_id'])){
    try {

        $get_incident_qry = $conn->query("SELECT 
                                                        `sex`,
                                                        `company_name`,
                                                        `Remarks`,
                                                        tbl_position.position,
                                                        `fa_id`,
                                                        `Location`,
                                                        `nature`,
                                                        `injured_id`,
                                                        `engineer_id`,
                                                        `severity`,
                                                        `leading_indicator`,
                                                        `site_name`,
                                                        `age`,
                                                        `possibility`,
                                                        `treatment`, 
                                                        `lti_injured`,
                                                        `incident_type`,
                                                        `root_causes`,
                                                        tbl_first_aid.date_created,
                                                        `first_aider_id`,
                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = engineer_id) as engineer_name,
                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = injured_id) as injured_name,                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = injured_id) as injured_name,
                                                        (SELECT CONCAT(firstname,' ',lastname) FROM tbl_employees WHERE employee_id = first_aider_id) as first_aider_name
                                                    FROM tbl_first_aid 
                                                    INNER JOIN tbl_employees ON tbl_first_aid.emp_id = tbl_employees.employee_id
                                                    INNER JOIN tbl_position ON tbl_employees.position = tbl_position.position_id
                                                    INNER JOIN users ON tbl_employees.employee_id = users.emp_id                                                      
                                                    INNER JOIN tbl_subscribers ON users.subscriber_id = tbl_subscribers.subscriber_id                                            
                                                    WHERE tbl_first_aid.is_deleted=  0 AND tbl_first_aid.emp_id = $session_emp_id AND fa_id = $fa_id");
        $get_incident = $get_incident_qry->fetchAll();

        $get_eng_qry = $conn->query("SELECT *,tbl_position.position AS emp_position FROM tbl_employees 
                                                  INNER JOIN tbl_position ON  tbl_employees.position = tbl_position.position_id
                                                  WHERE tbl_position.position LIKE '%engineer%' AND tbl_employees.is_deleted = 0 ORDER BY firstname  ASC");

        $engineer_list = $get_eng_qry->fetchAll();
        $root_causes = array("Use of Tools Equipment,Materials and Products", "Intentional/Lack of Awareness/Behaviors", "Protective Systems","Integrity of Tools/PLan/Equipment, Material", "Workplace Hazards", "Organizational","Other");
        $incident_type =  array("FAT","LTC", "RWC", "MTC", "FAC", "NM", "PD","TRAF","FIRE","ENV");

        $fa_id = $get_incident[0]['fa_id'];
        $get_body_parts_qry = $conn->query("SELECT tbl_body_parts.part_id, part_name FROM tbl_body_parts_injured 
                                                     INNER JOIN tbl_body_parts ON tbl_body_parts_injured.part_id = tbl_body_parts.part_id WHERE fa_id = $fa_id");
        $get_body_parts = $get_body_parts_qry->fetchAll();
        
        $get_equipments_qry = $conn->query("SELECT * FROM tbl_equipment INNER JOIN tbl_fa_equipment ON tbl_equipment.equipment_id = tbl_fa_equipment.equip_id WHERE fa_id = $fa_id");
        $get_equipments = $get_equipments_qry->fetchAll();
        $body_parts = '';
        $equipments = '';
        foreach ($get_body_parts as $body_part){
            $body_parts = $body_parts." * ".$body_part['part_name'];
        }
        foreach($get_equipments as $equipment){
            $equipments = $equipments." * ".$equipment['equipment_description'];
        }

    } catch (Exception $e){
        echo $e;
    }
}
?>
    <style>
        input {
            width: 100%;
        }
    </style>
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="assets/js/plugins/select2/css/select2.css">

    <!-- Main Container -->
    <main id="main-container">
        <!-- Hero -->
        <div class="bg-gd-lake">
            <div class="bg-pattern" style="background-image: url('assets/media/photos/construction4.jpeg');">
                <div class="content content-top content-full text-center  d-print-none">
                    <div class="py-20">
                        <h1 class="h2 font-w700 text-white mb-10">Incident</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Hero -->
        <div class="row ">
            <div class="col-2"></div>
            <div class="col-8">
                <div class="content">
                    <div class="block block-rounded">
                    </div>
                    <div class="block">
                        <div class="block-content" >
                            <div class="block-header block-header-default">
                                <h3 class="block-title">Incident Report</h3>

                            </div>
                            <div class="content" id="canvass">
                                <div class="row">
                                    <div class="col-6">
                                        <input class="form form-control" readonly type="text" value="<?=$get_incident[0]['injured_name']?>">
                                    </div>
                                    <div class="col-6">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/codebase.core.min.js"></script>

    <script src="assets/js/codebase.app.min.js"></script>

    <script>
        $(document).ready(function () {

        });
    </script>
    </body>
<?php
include 'includes/footer.php';
