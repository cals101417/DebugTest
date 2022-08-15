

<?php
include 'includes/head.php';
include 'conn.php';
?>
<div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
    <?php
    session_start();
    $visit_page =  $_GET['page'];

    $serial= $_GET['v'];
    $get_link_qry = $conn->query("SELECT * FROM tbl_visitor WHERE serial = '$serial'");
    $get_link = $get_link_qry->fetch();
    $date_generated = $get_link['date_generated'];
    $_SESSION["session_user_id"] = $get_link['user_id'];
    $_SESSION["session_sub_id"] = $get_link['sub_id'];
    $session_user_id = $_SESSION["session_user_id"];
    $session_sub_id = $_SESSION["session_sub_id"];
    $date_expired =  date('Y-m-d H:i:s', strtotime('3 day', strtotime($date_generated)));
    $date_now =  date("Y-m-d h:i:s");
    $date_now=date_create($date_now);
    $date_expired=date_create($date_expired);
//    echo $date_expired->getTimestamp();
//    $time_left = $date_expired->getTimestamp() - $date_now->getTimestamp(); // in seconds
    $interval = $date_now->diff($date_expired);
    $date_diff =  $interval->format('%d-%H-%i-%s');
//    echo $date_diff;
    $time =  explode("-", ($date_diff));
    $hours_per_day = 24;
    $minutes_per_hour = 60;
    $seconds_per_minute = 60;
    //convert to seconds
    $days =  (($time[0]* $hours_per_day) * $minutes_per_hour)*$seconds_per_minute; //get s
    $hours =  (($time[1]*$minutes_per_hour)*$seconds_per_minute);
    $minutes =  $time[2];

    $time_left = $days+$hours+$minutes+$time[3]; // total seconds



    if(!empty($get_link)){

        include 'includes/sidebar_visitor.php';
        switch ($visit_page) {
            case "dashboard":
//                include 'includes/header_visitor.php';
                include "visitor_pages/visitor_dashboard.php";
                break;
            case "employee":
                include "visitor_pages/visitor_employees.php";
                break;
            case "position":
                include "visitor_pages/visitor_position.php";
                break;
            case "inventory":
                include "visitor_pages/vistor_inventory_masterlist.php";
                break;
            case "department":
                include "visitor_pages/visitor_departments.php";
                break;
            case "company":
                include "visitor_pages/visitor_company.php";
                break;
            case "users":
                include "visitor_pages/visitor_users.php";
                break;
            case "folders":
                include "visitor_pages/visitor_folders.php";
                break;
            case "documents":
                include "visitor_pages/visitor_documents.php";
                break;
            case "toolbox":
                include "visitor_pages/visitor_toolbox_talks.php";
                break;
            case "first_aid":
                include "visitor_pages/visitor_first_aid.php";
                break;

            case "training/client":
                include "visitor_pages/visitor_training_client.php";
                break;
            case "training/inhouse":
                include "visitor_pages/visitor_training_inhouse.php";
                break;
            case "training/external":
                include "visitor_pages/visitor_training_external.php";
                break;
            case "training/induction":
                include "visitor_pages/visitor_training_induction.php";
                break;
            case "mandays":
                include "visitor_pages/visitor_manday_reports.php";
                break;
            case "manhours":
                include "visitor_pages/visitor_manhours_reports.php";
                break;
            default:
        }
    }

    ?>

    <!--        THIS IS A SAMPLE CHANGES -->
    <!-- Main Container -->
</div>
<script>
    $(document).ready(function() {
        name_link()
        setTimeout(redirect, <?=$time_left?> *1000) //1second = 1000 milliseconds second parameter is milliseconds.

    });

    function name_link(){
        var base_url = window.location.origin;
        var serial  = "<?php echo $serial?>";
        var pathArray = window.location.pathname.split( '/' );
        let dashboard_link = base_url+"/"+pathArray[1]+ "https://membersafety-surfers.com/visit.php?page=dashboard&v=5673973602942267?page=dashboard&"+"v="+serial;
        let employee_link = base_url+"/"+pathArray[1]+ "/visit.php?page=employee&"+"v="+serial;
        let inventory_link = base_url+"/"+pathArray[1]+ "/visit.php?page=inventory&"+"v="+serial;
        let department_link = base_url+"/"+pathArray[1]+ "/visit.php?page=department&"+"v="+serial;
        let position_link = base_url+"/"+pathArray[1]+ "/visit.php?page=position&"+"v="+serial;
        let company_link = base_url+"/"+pathArray[1]+ "/visit.php?page=company&"+"v="+serial;
        let user_link = base_url+"/"+pathArray[1]+ "/visit.php?page=users&"+"v="+serial;
        let folder_link = base_url+"/"+pathArray[1]+ "/visit.php?page=folders&"+"v="+serial;
        // let document_link = base_url+"/"+pathArray[1]+ "/visit.php?page=documents&"+"v="+serial;
        let civil_link = base_url+"/"+pathArray[1]+ "/visit.php?page=toolbox&"+"v="+serial+"&type=1";
        let electrical_link = base_url+"/"+pathArray[1]+ "/visit.php?page=toolbox&"+"v="+serial+"&type=2";
        let mechanical_link = base_url+"/"+pathArray[1]+ "/visit.php?page=toolbox&"+"v="+serial+"&type=3";
        let camp_link = base_url+"/"+pathArray[1]+ "/visit.php?page=toolbox&"+"v="+serial+"&type=4";
        let office_link = base_url+"/"+pathArray[1]+ "/visit.php?page=toolbox&"+"v="+serial+"&type=5";
        let first_aid_link = base_url+"/"+pathArray[1]+ "/visit.php?page=first_aid&"+"v="+serial;
        let client_link = base_url+"/"+pathArray[1]+ "/visit.php?page=training/client&"+"v="+serial;
        let external_link = base_url+"/"+pathArray[1]+ "/visit.php?page=training/external&"+"v="+serial;
        let inhouse_link = base_url+"/"+pathArray[1]+ "/visit.php?page=training/inhouse&"+"v="+serial;
        let induction_link = base_url+"/"+pathArray[1]+ "/visit.php?page=training/induction&"+"v="+serial;
        let mandays_link = base_url+"/"+pathArray[1]+ "/visit.php?page=mandays&"+"v="+serial;
        let manhours_link= base_url+"/"+pathArray[1]+ "/visit.php?page=manhours&"+"v="+serial;

        $('#sidebar_dashboard').attr('href', dashboard_link);
        $('#all_employee_sidebar').attr('href', employee_link);
        $('#sidebar_inventory').attr('href',inventory_link);
        $('#sidebar_department').attr('href',department_link);
        $('#sidebar_company').attr('href',company_link);
        $('#sidebar_users').attr('href',user_link);
        $('#sidebar_folders').attr('href',folder_link);
        $('#sidebar_civils').attr('href',civil_link);
        $('#sidebar_electricals').attr('href',electrical_link);
        $('#sidebar_mechanicals').attr('href',mechanical_link);
        $('#sidebar_camps').attr('href',camp_link);
        $('#sidebar_office').attr('href',office_link);
        $('#sidebar_training').attr('href', window.location.href);
        $('#sidebar_tbt').attr('href', window.location.href);
        $('#sidebar_files').attr('href', window.location.href);
        $('#sidebar_first_aid').attr('href',first_aid_link);
        $('#sidebar_position').attr('href',position_link);

        $('#sidebar_training_client').attr('href', client_link);
        $('#sidebar_training_external').attr('href', external_link);
        $('#sidebar_training_inhouse').attr('href', inhouse_link);
        $('#sidebar_training_induction').attr('href', induction_link);
        $('#sidebar_manhours').attr('href', manhours_link);
        $('#sidebar_mandays').attr('href',mandays_link);


    }
    function redirect(){
        let base_url = window.location.origin;
        let pathArray = window.location.pathname.split( '/' );
        let link = base_url+"/"+pathArray[1]+ "/redirect.php";
        window.location = link;
    }

</script>

<?php

$conn = NULL;
include 'includes/footer.php';
