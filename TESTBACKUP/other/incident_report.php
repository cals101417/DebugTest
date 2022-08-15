<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
?>
<div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
<?php
include 'includes/sidebar.php';
include 'includes/header.php';
?>
    <!-- Main Container -->

    <main id="main-container" ">
    <!-- Hero -->
        <div class="bg-image bg-image-bottom" style="background-image: url('assets/media/photos/construction1.jpg');">
            <div class="bg-primary-dark-op">
                <div class="content content-top text-center overflow-hidden">
                    <div class="pt-50 pb-20">
                        <h1 class="font-w700 text-white mb-10 js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">Incident</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <h2 class="content-heading">Overview</h2>
            <div class="col-6">
            <div class="form-group row">
                <label class="col-lg-1 col-form-label" for="year">Year <span class="text-danger"></span></label>
                <div class="col-lg-6">
                    <select class="form-control form-control-lg" name="year" id="year" onchange="load_incident_reports()">
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                        <option value="2020">2020</option>
                        <option value="2019">2019</option>
                        <option value="2018">2018</option>
                    </select>
                </div>
            </div>
            </div>
        </div>

        <div id="incident_reports">

        </div>
    </main>

    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/pages/be_tables_datatables.min.js"></script>
    <!-- Page JS Code -->
    <!-- Page JS Helpers (Easy Pie Chart Plugin) -->
    <script>jQuery(function(){ Codebase.helpers('easy-pie-chart'); });

        $(document).ready(function () {
            load_incident_reports();
        });

        function load_incident_reports(){

            var year = $('#year').val();
            $.ajax({
                type: 'POST',
                url: 'ajax/incident_ajax.php',
                data: {
                    year: year,
                    incident_report  : 1
                },
                success: function (response) {
                    $('#incident_reports').html(response);
                },
                error: function () {
                    console.log("Error adding employee function");
                }
            });
        }
    </script>


<?php
include 'includes/footer.php';
