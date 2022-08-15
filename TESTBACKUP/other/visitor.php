<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
?>
    <style>
        @page {
            size: 25cm 35.7cm;
            margin: 5mm 5mm 5mm 5mm; /* change the margins as you want them to be. */
        }
    </style>
<div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
<?php
include 'includes/sidebar.php';
include 'includes/header.php';
?>
    <!-- Main Container -->
<?php
try {
    $get_links_qry = $conn->query("SELECT * FROM tbl_visitor WHERE sub_id = $subscriber_id AND user_id = $session_emp_id ");
    $get_links = $get_links_qry->fetchAll();
} catch (Exception $e ){
    echo $e;
}
?>
    <main id="main-container">
    <!-- Hero -->
        <div class="bg-image bg-image-bottom d-print-none" style="background-image: url('assets/media/photos/construction1.jpg');">
            <div class="bg-primary-dark-op">
                <div class="content content-top text-center overflow-hidden">
                    <div class="pt-50 pb-20 d-print-none">
                        <h1 class="font-w700 text-white mb-10 js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">Links</h1>
                        <h2 class="h4 font-w400 text-white-op js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">HSE Management System</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="block col-lg-6 ">
                <div class="block-header block-header-default">
                </div>
                <div class="content">
                    <table class="table table-bordered table-active">
                        <thead>
                        <th>#</th>
                        <th>Link</th>
                        <th>Date Expire</th>
                        <th>Action</th>
                        </thead>
                        <tbody>
                            <?php foreach ($get_links as $link): ?>
                            <tr>
                                <td><?=$link['link_id']?></td>
                                <td><?=$link['link']?></td>
                                <td><?=$link['date_generated']?></td>
                                <td>
                                    <button class="btn btn-danger"> Deactivate</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </main>
    <!--  END Hero    -->
    <!-- Page Content -->
    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page JS Code -->
    <script src="assets/js/pages/be_tables_datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!--    <script src="assets/js/plugins/sparkline/jquery.sparkline.min.js"></script>-->
    <!-- Page JS Code -->
    <!-- Page JS Helpers (Easy Pie Chart Plugin) -->
    <script>jQuery(function(){ Codebase.helpers('easy-pie-chart'); });</script>
    <canvas id="myChart" width="400" height="400"></canvas>
    <script>
        $(document).ready(function() {
            <?php
            $dateTime = new DateTime();
            $month = $dateTime->format('m')-1;
            ?>
            $( <?php echo '"#0'.$month.'"'; ?>).attr( "selected", true )
            document.body.style.zoom = "90%";
            // load_chart();
            first_aid_chart();
        });
    </script>
<?php
include 'includes/footer.php';
