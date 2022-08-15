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

<!--        SQL /-->
        <?php
        try {

            $generate_report_qry = $conn->query("SELECT  *,COUNT(*), Year(date_conducted) as itd_year,
                                        SUM(case when tbt_type='1' then time else 0 end) as total_hours_civils,
                                        SUM(case when tbt_type='2' then time else 0 end) as total_hours_electricals,
                                        SUM(case when tbt_type='3' then time else 0 end) as total_hours_mechanicals,
                                        SUM(case when tbt_type='4' then time else 0 end) as total_hours_camps,
                                        SUM(case when tbt_type='5' then time else 0 end) as total_hours_office,       
                                        COUNT(case when tbt_type='1' then time else null end) as total_days_civils,
                                        COUNT(case when tbt_type='2' then time else null end) as total_days_electricals,
                                        COUNT(case when tbt_type='3' then time else null end) as total_days_mechanicals,  
                                        COUNT(case when tbt_type='4' then time else null end) as total_days_camps,
                                        COUNT(case when tbt_type='5' then time else null end) as total_days_office,       
                                        COUNT('tbtp_id') as totals_days,
                                        SUM(time) as totals_hours
            
                                        FROM tbl_toolbox_talks 
                                        INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id  
                                        WHERE  tbl_toolbox_talks.is_deleted = 0
                                        GROUP BY date_format(date_conducted, '%Y') ORDER BY `date_conducted`");
            $get_result= $generate_report_qry->fetchAll();

            //   GET PREVIOUS YEAR OF THE SELECTED YEAR
//            $previous_year = explode("-",$range2);
//            $prev_year = ((strval($previous_year[0])-1).'-12-31 00:00:00');

            $get_report_by_year = $conn->query("SELECT  *,COUNT(*),Year(date_conducted) as year,
                                            SUM(case when tbt_type='1' then time else 0 end) as total_hours_civils,
                                            SUM(case when tbt_type='2' then time else 0 end) as total_hours_electrical,
                                            SUM(case when tbt_type='3' then time else 0 end) as total_hours_mechanical,
                                            SUM(case when tbt_type='4' then time else 0 end) as total_hours_camps,
                                            SUM(case when tbt_type='5' then time else 0 end) as total_hours_office,       
                                            COUNT(case when tbt_type='1' then time else null end) as total_days_civils,
                                            COUNT(case when tbt_type='2' then time else null end) as total_days_electricals,
                                            COUNT(case when tbt_type='3' then time else null end) as total_days_mechanicals,  
                                            COUNT(case when tbt_type='4' then time else null end) as total_days_camps,
                                            COUNT(case when tbt_type='5' then time else null end) as total_days_office,                                           
                                            COUNT('tbtp_id') as_participant,  
                                            SUM(time) as monthly_hours            
                                            FROM tbl_toolbox_talks 
                                            INNER JOIN tbl_toolbox_talks_participants ON tbl_toolbox_talks.tbt_id = tbl_toolbox_talks_participants.tbt_id
                                            GROUP BY date_format(date_conducted, '%Y') ORDER BY `date_conducted` DESC");
            $get_result2= $get_report_by_year->fetchAll();
            $get_manual_records_qry =$conn->query("SELECT * FROM tbl_tds WHERE `is_deleted` = 0 ORDER BY 'itd_year' ASC");
            $get_manual_result = $get_manual_records_qry->fetchAll();
            $array_itds  = array_merge($get_manual_result , $get_result);

        } catch (Exception $e ){
            echo $e;
        }
        ?>
        <main id="main-container">
            <div class="bg-image bg-image-bottom" style="background-image: url('assets/media/photos/construction3.jpg');">
                <div class="bg-primary-dark-op">
                    <div class="content content-top text-center overflow-hidden">
                        <div class="pt-50 pb-20">
                            <h1 class="font-w700 text-white mb-10 js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">ToolBox Talks Reports</h1>
                            <h2 class="h4 font-w400 text-white-op js-appear-enabled animated fadeInUp" data-toggle="appear" data-class="animated fadeInUp">Manage your TBT Reports and ITDs!</h2>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page Content -->
            <div class="content">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">ITD Reports</h3>
                        <form class="block-options" id="generate_reports" action="tbt_reports_pdf.php" method="post">
                            <div class="form-inline">
                                <select class="form-control mr-5" id="select_month" name="select_month" onchange="load_chart()">
                                    <option id="01"  value="01">January</option>
                                    <option id="02"  value="02">February</option>
                                    <option id="03"  value="03" >March</option>
                                    <option id="04"  value="04">April</option>
                                    <option id="05"  value="05">May</option>
                                    <option id="06"  value="06">June</option>
                                    <option id="07"  value="07">July</option>
                                    <option id="08"  value="08">August</option>
                                    <option id="09"  value="09">September</option>
                                    <option id="010" value="10">October</option>
                                    <option id="011" value="11">November</option>
                                    <option id="012" value="12">December</option>
                                </select>
                                <select class="form-control mr-5" onchange="load_chart()" id="year" name="year">
                                    <?php
                                    $yr_qry = $conn->query("SELECT YEAR (tbl_toolbox_talks.date_conducted) as yr
                                                                                        FROM tbl_toolbox_talks
                                                                                        WHERE is_deleted = 0
                                                                                        GROUP BY YEAR (tbl_toolbox_talks.date_conducted)");
                                    $years = $yr_qry->fetchAll();
                                    foreach ($years as $year) {
                                        ?>
                                        <option value="<?= $year['yr'] ?>"><?= $year['yr'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <button type="submit" class="btn btn-primary mr-5"><li class="fa fa-folder"></li> Generate</button>
                                <button type="button" class="btn btn-primary mr-5" data-toggle="modal" data-target="#add_new_itd""><li class="fa fa-plus text-success"></li> ITD</button>
                                <button type="button" class="btn btn-primary" onclick="Codebase.helpers('print-page');"> Print</button>
                            </div>
                        </form>
                    </div>
                    <div class=" block-content block-content-full shadow p-3 mb-5 rounded d-print-none" >
                        <!-- DataTables functionality is initialized with .js-dataTable-full-pagination class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                        <table width="2000px" class="table table-bordered table-striped table-vcenter table-sm js-dataTable-full-pagination  table-responsive-xl">
                            <thead>
                            <tr class="text-center text-white bg-primary">
                                <th class="text-capitalize">Year</th>
                                <th class="text-capitalize">Civils TD</th>
                                <th class="text-capitalize">Civils TH</th>
                                <th class="text-capitalize">Mechanicals TD</th>
                                <th class="text-capitalize">Mechanicals TH</th>
                                <th class="text-capitalize">Electricals TD</th>
                                <th class="text-capitalize">Electricals TH</th>
                                <th class="text-capitalize">Camps TD</th>
                                <th class="text-capitalize">Camps TH</th>
                                <th class="text-capitalize">Office TD</th>
                                <th class="text-capitalize">Office TH</th>
                                <th class="text-capitalize">Total Days</th>
                                <th class="text-capitalize">Total Hours</th>
                                <th class="text-capitalize">Total ITD</th>
                                <th class="text-capitalize" width="9%">Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            $days_civils = 0;
                            $days_mechanicals = 0;
                            $days_electricals = 0;
                            $days_camps = 0;
                            $days_office = 0;
                            $hours_civils  = 0;
                            $hours_mechanicals  = 0;
                            $hours_electricals  = 0;
                            $hours_camps  = 0;
                            $hours_office  = 0;

                            for ($i = 0; $i < count($array_itds); $i++ ){
//
                                $days_civils =  $days_civils + $array_itds[$i]['total_days_civils'];
                                $days_mechanicals = $days_mechanicals  + $array_itds[$i]['total_days_mechanicals'];
                                $days_electricals = $days_electricals + $array_itds[$i]['total_days_electricals'];
                                $days_camps = $days_camps + $array_itds[$i]['total_days_camps'];
                                $days_office = $days_office + $array_itds[$i]['total_days_office'];
                                $hours_civils  = $hours_civils + $array_itds[$i]['total_hours_civils'];
                                $hours_mechanicals  = $hours_mechanicals + $array_itds[$i]['total_hours_mechanicals'];
                                $hours_electricals  = $hours_electricals + $array_itds[$i]['total_hours_electricals'];
                                $hours_camps  = $hours_camps + $array_itds[$i]['total_hours_camps'];
                                $hours_office  = $hours_office  + $array_itds[$i]['total_hours_office'];
                                $totals_itds = $hours_office + $hours_camps + $hours_electricals+ $hours_mechanicals+$hours_civils;
                                $totals_days  = $array_itds[$i]['total_days_civils'] + $array_itds[$i]['total_days_mechanicals'] + $array_itds[$i]['total_days_electricals']+ $array_itds[$i]['total_days_electricals']+$array_itds[$i]['total_days_office'];
                                $totals_hours = $array_itds[$i]['total_hours_office'] +  $array_itds[$i]['total_hours_camps'] + $array_itds[$i]['total_hours_electricals']  + $array_itds[$i]['total_hours_mechanicals']  +$array_itds[$i]['total_hours_civils']

                                ?>
                                <tr class="text-center" >
                                    <td><?=$array_itds[$i]['itd_year']?></td>
                                    <td><?=number_format($array_itds[$i]['total_days_civils'])?></td>
                                    <td><?=number_format($array_itds[$i]['total_hours_civils']) ?></td>
                                    <td><?=number_format($array_itds[$i]['total_days_mechanicals'])?></td>
                                    <td><?=number_format($array_itds[$i]['total_hours_mechanicals'])?></td>
                                    <td><?=number_format($array_itds[$i]['total_days_electricals'])?></td>
                                    <td><?=number_format($array_itds[$i]['total_hours_electricals'])?></td>
                                    <td><?=number_format($array_itds[$i]['total_days_camps'])?></td>
                                    <td><?=number_format($array_itds[$i]['total_hours_camps'])?></td>
                                    <td><?=number_format($array_itds[$i]['total_days_office'])?></td>
                                    <td><?=number_format($array_itds[$i]['total_hours_office'])?></td>
                                    <td><?=number_format($totals_days)?></td>
                                    <td><?=number_format($totals_hours)?></td>
                                    <td><?=number_format($totals_itds)?></td>
                                    <td>
                                        <?php
                                        $itd_id= "";
                                        if (isset($array_itds[$i]['itd_id'])){
                                            $itd_id =  $array_itds[$i]['itd_id'];
                                            ?>
                                            <button class="btn btn-sm btn-success mr-5 mb-5" onclick="edit_itd(<?=$itd_id?>)" title="Edit"><i class="fa fa-pencil"></i></button>
                                            <button class="btn btn-sm btn-danger mr-5 mb-5" onclick="delete_itd(<?=$itd_id?>)" title="Delete"> <i class="fa fa-trash"></i></button>
                                            <?php
                                        } else {
                                            ?>
                                            <button class="btn btn-circle btn-alt-primary" onclick="generate_itd(<?php echo $array_itds[$i]['itd_year']?>)" title="Generate ITD Report"><i class="fa fa-bar-chart"></i></button>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="block">
                    <div class="block-content">
                        <div id="canvass">
                        </div>
                    </div>
                </div>
            </div>

        </main>

<!--        ADD NEW ITD-->
        <div class="modal fade" id="add_new_itd" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="block block-themed block-transparent mb-0">
                        <div class="block-header bg-primary-dark">
                            <h3 class="block-title" >Add New ITD</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                    <i class="si si-close"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content">
                            <form id="add_new_itd_form">
                                <input type="hidden" name="add_new_item" value="1">
                                <div class="row">
<!--                                                                SECOND COLUMN-->
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">Civils</label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" id="total_days_civils" name="total_days_civils" placeholder="Total Days" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img"></label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg"  id="total_hours_civils" placeholder="Total Hours" name="total_hours_civils">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">Mechanicals</label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" id="total_days_mechanicals" name="total_days_mechanicals" placeholder="Total Days" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img"></label>
                                            <div class="col-12">
                                                <input type ="number" class="form-control form-control-lg"  id="total_hours_mechanicals" placeholder="Total Hours" name="total_hours_mechanicals">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">Office</label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" id="total_days_office" name="total_days_office" placeholder="Total Days" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img"></label>
                                            <div class="col-12">
                                                <input type ="number" class="form-control form-control-lg"  id="total_hours_office" placeholder="Total Hours" name="total_hours_office">
                                            </div>
                                        </div>
                                    </div>
                                            <!-----------------FIRST COLUMN---------------------->
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">Electricals</label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" id="total_days_electricals" placeholder="Total Days" name="total_days_electricals"  required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img"></label>
                                            <div class="col-12">
                                                <input type ="number" class="form-control form-control-lg"  id="total_hours_electricals" placeholder="Total Hours" name="total_hours_electricals">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="min_qty">Camps</label>
                                            <div class="col-12">
                                                <input type="number" class="form-control form-control-lg" id="total_days_camps" name="total_days_camps" placeholder="Total Days" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img"></label>
                                            <div class="col-12">
                                                <input type ="number" class="form-control form-control-lg"  id="total_hours_camps" placeholder="Total Hours" name="total_hours_camps">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-12" for="item_img">Select Year</label>
                                            <div class="col-12">
                                                <select class="form-control" id="select_year" name="select_year">
                                                    <option value="2015">2015</option>
                                                    <option value="2016">2016</option>
                                                    <option value="2017">2017</option>
                                                    <option value="2018">2018</option>
                                                    <option value="2019">2019</option>
                                                    <option value="2020">2020</option>
                                                    <option value="2021">2021</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-12 text-center">
                                                <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                                    <i class="fa fa-plus mr-5"></i> Add New ITD
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!--            ADD NEW ITD-->


    <div class="modal fade" id="edit_itd_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title" >EDIT ITD</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="edit_itd_form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-12" for="min_qty">ITD </label>
                                        <div class="col-12">
                                            <input type="number" class="form-control form-control-lg" readonly id="edit_itd_id" name="edit_itd_id" placeholder="Total Days" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="min_qty">Civils</label>
                                        <div class="col-12">
                                            <input type="number" class="form-control form-control-lg" id="total_days_civils" name="total_days_civils" placeholder="Total Days" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="item_img"></label>
                                        <div class="col-12">
                                            <input type="number" class="form-control form-control-lg"  id="total_hours_civils" placeholder="Total Hours" name="total_hours_civils">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="min_qty">Mechanicals</label>
                                        <div class="col-12">
                                            <input type="number" class="form-control form-control-lg" id="total_days_mechanicals" name="total_days_mechanicals" placeholder="Total Days" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="item_img"></label>
                                        <div class="col-12">
                                            <input type ="number" class="form-control form-control-lg"  id="total_hours_mechanicals" placeholder="Total Hours" name="total_hours_mechanicals">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="min_qty">Office</label>
                                        <div class="col-12">
                                            <input type="number" class="form-control form-control-lg" id="total_days_office" name="total_days_office" placeholder="Total Days" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="item_img"></label>
                                        <div class="col-12">
                                            <input type ="number" class="form-control form-control-lg"  id="total_hours_office" placeholder="Total Hours" name="total_hours_office">
                                        </div>
                                    </div>
                                </div>
                                <!-----------------FIRST COLUMN---------------------->
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-12" for="min_qty">Electricals</label>
                                        <div class="col-12">
                                            <input type="number" class="form-control form-control-lg" id="total_days_electricals" placeholder="Total Days" name="total_days_electricals"  required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="item_img"></label>
                                        <div class="col-12">
                                            <input type ="number" class="form-control form-control-lg"  id="total_hours_electricals" placeholder="Total Hours" name="total_hours_electricals">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="min_qty">Camps</label>
                                        <div class="col-12">
                                            <input type="number" class="form-control form-control-lg" id="total_days_camps" name="total_days_camps" placeholder="Total Days" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="item_img"></label>
                                        <div class="col-12">
                                            <input type ="number" class="form-control form-control-lg"  id="total_hours_camps" placeholder="Total Hours" name="total_hours_camps">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-12" for="item_img">Select Year</label>
                                        <div class="col-12">
                                            <select class="form-control" id="select_year" name="select_year">
                                                <option value="2015">2015</option>
                                                <option value="2016">2016</option>
                                                <option value="2017">2017</option>
                                                <option value="2018">2018</option>
                                                <option value="2019">2019</option>
                                                <option value="2020">2020</option>
                                                <option value="2021">2021</option>
                                            </select>
                                        </div>
                                    </div>

                                    <br>
                                    <div class="form-group row">
                                        <div class="col-12 text-center">
                                            <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                                <i class="fa fa-plus mr-5"></i> EDIT ITD
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                </div>
            </div>
        </div>
    </div>


    <!-- END Pull Stocks From Inventory Modal -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>

    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page JS Code -->
    <script src="assets/js/pages/be_tables_datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function () {
            $(document).ready(function() {
                $('#toolbox_sidebar').addClass('open');

                $('#itd_table').DataTable( {
                    "order": [[ 0, "desc" ]]
                } );
                <?php
                $dateTime = new DateTime();
                $month = $dateTime->format('m')-1;
                ?>
                $( <?php echo '"#0'.$month.'"'; ?>).attr( "selected", true )
                load_chart()
            } );

            $("#add_new_itd_form").submit(function (event) {
                event.preventDefault();
                if (confirm("Are you sure you want to add?")){
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/toolbox_talks_reports_ajax.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData:false,
                        success: function(data) {
                            // alert(data);
                            location.reload();
                        }
                    });
                }
            });
            $("#edit_itd_form").submit(function (event) {
                event.preventDefault();
                if (confirm("Are you sure you want to EDIT ITD?")){
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/toolbox_talks_reports_ajax.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData:false,
                        success: function(data) {
                            // alert(data);
                            location.reload();
                        }
                    });
                }
            });

        });
        function edit_itd(itd) {
            $('#edit_itd_id').val(itd);
            $('#edit_itd_modal').modal('show');
        }
        function delete_itd(year) {

            if (confirm("Are you sure you want to remove this item?")) {
                $.ajax({
                    type: 'POST',
                    url: 'ajax/toolbox_talks_reports_ajax.php',
                    data: {
                        delete_year: year,
                    },
                    success: function (response) {
                        // alert(response);
                        location.reload();
                    }
                });
            }
        }
        function load_chart(){
            var year =  $("#year").val();
            var select_month =  $("#select_month").val();
            // $("#default_select" .attr( "disabled", false);
            $.ajax({
                type: 'POST',
                url: 'ajax/toolbox_talks_reports_ajax.php',
                data: {
                    year: year,
                    select_month:  select_month,
                    load_chart: 1
                },
                success: function (response) {
                    // alert(response);
                    $('#canvass').html(response);
                },
                error: function () {
                    console.log("Error Generate Report function");
                }
            });
        }
        function generate_itd(itd_year){
            location.href = "toolbox_talks_reports_itds.php?year="+itd_year;
        }
    </script>

    <script>


    </script>
<?php
include 'includes/footer.php';