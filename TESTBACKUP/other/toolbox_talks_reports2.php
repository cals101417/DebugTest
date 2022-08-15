<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
?>
    <div id="page-container"
         class="sidebar-o enable-page-overlay side-scroll <?= $header_layout ?> page-header-inverse <?= $main_content . ' ' . $sidebar_layout ?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        ?>
        <!-- Main Container -->
        <main id="main-container" style="min-height: 871px;">
            <!-- Hero -->
            <div class="bg-image" style="background-image: url('assets/media/photos/photo13@2x.jpg');">
                <div class="bg-black-op-75">
                    <div class="content content-top content-full text-center">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10">TOOL BOX REPORT </h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->

            <!-- Page Content -->
            <div class="content bg-white"  id="div_content">
                <div class="content-heading">
                    Tool Box Report
                    <div class="block-header block-header-default bg-dark">
                        <div class="col-sm-3">
                            <div class="btn-group" role="group" aria-label="Third group">
                                <button type="button" class="btn btn-secondary dropdown-toggle" id="toolbarDrop"   onclick="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
                                <div class="dropdown-menu" aria-labelledby="toolbarDrop">
                                    <h6 class="dropdown-header">Option</h6>
                                    <button class="dropdown-item btn btn-danger" onclick="generate_report2_pdf()"">
                                        <i class="fa fa-fw fa-file-pdf-o mr-5"></i>GENERATE PDF
                                    </button>
<!--                                    <button class="dropdown-item" onclick="generate_report2_chart()">-->
<!--                                        <i class="fa fa-fw fa-envelope-o mr-5"></i>CHART-->
<!--                                    </button>-->
                                </div>
                            </div>
                        </div>
                        <div class="row ">
                            <select class=" form-control-sm " id="select_dept" name="select_dept" onchange="generate_report2()">
                                <option value="6">All</option>
                                <option value="1">Civils</option>
                                <option value="2">Electricals</option>
                                <option value="3">Mechanicals</option>
                                <option value="4">Camps</option>
                                <option value="5">Offices</option>
                            </select>
                            <select class=" form-control-sm " id="select_months" name="select_months" onchange="generate_report2()">
                                <?php

                                $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec");
                                for ($i = 0; $i < count($months); $i++) {
                                    $month_num = $i + 1;
                                    ?>
                                    <option value="<?= $month_num ?>"><?= $months[$i] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                            <select class=" form-control-sm " id="select_yr" name="select_yr" onchange="generate_report2()">
                                <?php
                                $yr_qry = $conn->query("SELECT YEAR (tbl_toolbox_talks.date_conducted) as yr
                                                                        FROM tbl_toolbox_talks
                                                                        WHERE tbl_toolbox_talks.is_deleted= 0
                                                                        GROUP BY YEAR (tbl_toolbox_talks.date_conducted)");
                                $years = $yr_qry->fetchAll();
                                foreach ($years as $year) {
                                ?>
                                    <option value="<?= $year['yr'] ?>"><?= $year['yr'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
<!--                            <button class=" form-control form-control-sm  btn btn-alt-success" onclick="generate_report2()" id="`generate_report2`">Go</button>-->
                        </div>
                    </div>
                </div>

                <?php
                $get_position =  $conn->query("SELECT `position`,`position_id`  FROM `tbl_position` WHERE is_deleted = 0");
                $get_all_position = $get_position->fetchAll();
                foreach($get_all_position as $data){
                    $position[] = $data['position'];
                }

                $arr = [];
                foreach($get_all_position as $position) { // LOOP THROUGH POSITIONS
                    $position_id=  $position['position_id'];
                    $get_records_per_position_qry = $conn->query("
                                                                    SELECT `date_conducted`,
                                                                    count(tbl_toolbox_talks_participants.tbt_id) AS day_per_participants,
                                                                    sum(tbl_toolbox_talks_participants.time) AS time_per_participant,       
                                                                    tbl_position.position, users.user_id, 
                                                                    tbl_position.position_id
                                                                    FROM `tbl_position`
                                                                    INNER JOIN `users` ON tbl_position.position_id = users.position 
                                                                    INNER JOIN `tbl_toolbox_talks_participants`  ON users.user_id = tbl_toolbox_talks_participants.user_id           
                                                                    INNER JOIN  `tbl_toolbox_talks` ON tbl_toolbox_talks_participants.tbt_id = tbl_toolbox_talks.tbt_id
                                                                    WHERE  users.position = $position_id AND
                                                                    YEAR ( tbl_toolbox_talks.date_conducted ) = 2022 AND  MONTH ( tbl_toolbox_talks.date_conducted ) = 11
                                                                    GROUP BY  DATE_FORMAT(date_conducted, '%D')");
                    $get_position_results = $get_records_per_position_qry->fetchAll();
                    $over_all_total_participants = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0  ); // LOOP 31 DAYS THEN GET THE DAY WITH A VALUE
                    for ($i = 1; $i <= 31; $i++) {
                        $td_display = 0;
                        $time_per_day = 0;
                        $participants_per_day = 0;
                        foreach($get_position_results as $result){
                            $get_day = explode("-",$result['date_conducted']);
                            $get_day = explode(" ",$get_day[2]);
                            $get_calendar_day = $get_day[0];

                            if (sprintf("%02d",$i) == $get_calendar_day){
                                $td_display = '<td class="bg-danger-light ">'.$result['day_per_participants'].'</td>';
//                                $total_days = $total_days+ $result['day_per_participants'];
//                                $total_time = $total_time+ $result['time_per_participant'];
                                $time_per_day = $result['time_per_participant'];
                                $participants_per_day = $result['day_per_participants'];
                                break;
                            }
                        }

                        $over_all_total_participants[$i-1] = $over_all_total_participants[$i-1] + $participants_per_day;
                    }

                    $r = rand(1,100);
                    $g = rand(50,150);
                    $b = rand(100,255);
                    $arr[] = array(
                            'label' => $position['position'],
                            'backgroundColor'=> 'rgb('.$r.','.$g.','.$b.')',
                            'borderColor' => 'rgb(111, 99, 132)',
                            'barThickness' => 10,
                            'maxBarThickness' => 8,
                            'data' => $over_all_total_participants,
                            );//assign each sub-array to the newly created array
                }

                // TRIM DOUBLE QUOTES
                $data_sets =  str_replace(['"label"','"backgroundColor"','"borderColor"','"barThickness"','"maxBarThickness"','"data"'],
                    ["label", "backgroundColor","borderColor","barThickness","maxBarThickness","data",""],json_encode($arr));

                ?>


                <div class="block block-themed block-transparent mb-0" id="div_reports2">
                </div>

                <div>
                    <canvas id="myChart"></canvas>
                </div>

    </div>
    </div>
    <!-- END Products -->
    </div>
    <!-- END Page Content -->
    </main>
    <!-- END Main Container -->

    <!-- END Pull Stocks From Inventory Modal -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function () {
            generate_report2();
            $('#toolbox_sidebar').addClass('open');

        });

        function generate_report2(){
            dept = $('#select_dept').val();
            month = $('#select_months').val();
            year = $('#select_yr').val();
            $.ajax({
                type: 'POST',
                url: 'ajax/toolbox_talks_reports_ajax.php',
                data: {
                    month:month,
                    year:year,
                    dept:dept,
                    generate_report2:1
                },
                success: function(response) {
                    $('#div_reports2').html(response);
                    // location.reload();

                },
                error: function() {
                    console.log("Error Generate Report function");
                }
            });
        }
        function generate_report2_pdf(){
            month = $('#select_months').val();
            year = $('#select_yr').val()
            dept = $('#select_dept').val();
            window.open("toolbox_talks_reports_pdf.php?month="+month+"&year="+year+"&dept="+dept);
            // window.location.href = "toolbox_talks_reports_pdf.php?month="+month+"&year="+year;
        }


        function generate_report2_chart(){
            // Example from the docs
            flag = false;
            month = $('#select_months').val();
            year = $('#select_yr').val();
            const labels = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];
            try {
                $.ajax({
                type: 'POST',
                dataType: "json",
                url: 'ajax/toolbox_talks_reports_ajax.php',
                data: {
                    month:month,
                    year:year,
                    generate_report2_chart:1
                },
                success: function(data2) {
                    // alert(String(data2))
                    const data = {
                        labels: labels,
                        datasets: data2
                    };
                    const config = {
                        type: 'bar',
                        data: data,
                        options: {}
                    };
                    if (flag){
                        myChart.destroy();
                        flag = !flag;
                    }
                    const myChart = new Chart(
                        document.getElementById('myChart'),
                        config

                    );
                },
                    error: function() {
                        console.log("Error Generate Report function");
                    }
                });
            }
            catch(err) {
                alert(err.message);
            }
        }

        function render_chart(config, is_destroyed){
            const myChart = new Chart(
                document.getElementById('myChart'),
                config
            );
        }
    </script>

<?php
include 'includes/footer.php';