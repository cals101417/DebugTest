<?php
include 'session.php';
include 'includes/head.php';
?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-modern main-content-boxed">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        include 'includes/disable_access.php';
        $month = $_GET['month'];
        $month_text = $_GET['month_text'];
        $year = $_GET['year'];
        ?>
        <!-- Main Container -->
        <main id="main-container">
            <!-- Page Content -->
            <div class="content">
                <div class="block mb-0 block-rounded" id="month_forecast_div">
                    <div class="block-header block-header-default">
                        <h4 id="report_h4" class="block-title text-muted"><span id="report_title"></span>Budget Forecast Form</h4>
                        <div class="block-options">
                            <!-- Print Page functionality is initialized in Helpers.print() -->
                            <button type="button" class="btn-block-option" onclick="Codebase.helpers('print-page');">
                                <i class="si si-printer"></i> Print
                            </button>
                        </div>
                    </div>
                    <div class="block-content" id="forecast_content">
                        <h3 class="content-heading text-center">
                            Monthly Budget Forecast - (<?=$month_text?>. <?=$year?>)
                        </h3>
                        <table class="table table-sm mb-50 table-bordered">
                            <thead class="thead-dark">
                            <tr>
                                <th class="text-capitalize">ID</th>
                                <th class="text-capitalize text-center">Description</th>
                                <th class="text-capitalize text-center">Line Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total = 0;
                            $month_forecast_qry = $conn->query("SELECT
                                                                        tbl_inventory.item_price,
                                                                        tbl_inventory_report.qty_order
                                                                        FROM
                                                                        tbl_inventory_report
                                                                        INNER JOIN tbl_inventory ON tbl_inventory.inventory_id = tbl_inventory_report.inventory_id
                                                                        WHERE
                                                                        tbl_inventory_report.`month` = $month AND
                                                                        tbl_inventory_report.`year` = $year AND
                                                                        tbl_inventory_report.is_added_to_forecast = 1");
                            foreach ($month_forecast_qry as $items_forecast){
                                $item_price = $items_forecast['item_price'];
                                $qty_order = $items_forecast['qty_order'];
                                $total += $item_price*$qty_order;
                            }
                            ?>
                            <tr>
                                <td>1</td>
                                <td class="text-center">PPE</td>
                                <td class="text-right"><?=number_format($total)?></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td class="text-center"></td>
                                <td class="text-right"><?=0?></td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td class="text-center"></td>
                                <td class="text-right"><?=0?></td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td class="text-center"></td>
                                <td class="text-right"><?=0?></td>
                            </tr>
                            <tr class="alert-primary">
                                <td colspan="2" class="text-right">USD Subtotal</td>
                                <td class="text-right"></td>
                            </tr>
                            <tr class="alert-primary">
                                <td colspan="2" class="text-right">IRQ Subtotal</td>
                                <td class="text-right"></td>
                            </tr>
                            <tr class="bg-body-light ">
                                <td colspan="2" class="text-right">Amended Items & Amount</td>
                                <td class="text-right"></td>
                            </tr>
                            <tr class="alert-primary">
                                <td colspan="2" class="text-right">USD Subtotal</td>
                                <td class="text-right"></td>
                            </tr>
                            <tr class="alert-primary">
                                <td colspan="2" class="text-right">IRQ Subtotal</td>
                                <td class="text-right"></td>
                            </tr>
                            <tr class="bg-body-light">
                                <td colspan="2" class=" text-right">Approved Amount</td>
                                <td class="text-right"></td>
                            </tr>
                            </tbody>
                        </table>

                        <p class="mb-0">Notes:</p>
                        <textarea style="width: 100%; height: 150px"></textarea>
                        <div class="form-group row mt-50">
                            <div class="col-4">
                                <div class="form-material floating open">
                                    <input type="email" class="form-control text-center" id="material-email2" name="material-email2"  disabled="">
                                </div>
                                <div class="text-center">
                                    <label class="text-center" for="remarks">Requested by</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-material floating open">
                                    <input type="email" class="form-control text-center" id="material-email2" name="material-email2" disabled="">
                                </div>
                                <div class="text-center">
                                    <label class="text-center" for="remarks">Reviewed by</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-material floating open">
                                    <input type="email" class="form-control text-center" id="material-email2" name="material-email2" disabled="">
                                </div>
                                <div class="text-center">
                                    <label class="text-center" for="remarks">Approved by</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Page Content -->
        </main>
    </div>
    <!-- END Main Container -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>

    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page JS Code -->
    <script src="assets/js/pages/be_tables_datatables.min.js"></script>

    <script>
        $(document).ready(function () {
            setTimeout(function() {
                $("#generate_report").trigger("click");
            },10);
            
            $('#inventory_sidebar').addClass('open');

            $('#generate_report').click(function () {
                $('#report_h4').show();
                month = $('#select_months').val();
                year = $('#select_yr').val();
                month_text = $( "#select_months option:selected" ).text()
                year_text = $( "#select_yr option:selected" ).text()
                $('#report_title').html('('+month_text+'-'+year_text+')')

                $.ajax({
                    type: 'POST',
                    url: 'ajax/inventory_ajax.php',
                    data: {
                        month: month,
                        year: year,
                        generate_report: 1
                    },
                    success: function (response) {
                        $('#tbody_reports').html(response);
                        if (!response.includes("No Data Found")){
                            $('#btn_forecast').show();
                        }
                        // location.reload();
                    },
                    error: function () {
                        console.log("Error Generate Report function");
                    }
                });
            })

            // ADD QUANTITY ORDER
            $('#form_add_qty_order').submit(function (event) {
                event.preventDefault();
                if (confirm("Are you sure?")){
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/inventory_ajax.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData:false,
                        success: function (response) {
                            alert(response)
                        },
                        error: function () {
                            console.log("Error Generate Report function");
                        }
                    });
                }
            });

            $('#btn_forecast').click(function (){
                month = $('#select_months').val();
                year = $('#select_yr').val();
                // $('#forecast_modal').modal('show');
                $('#month_forecast_div').show();
                $.ajax({
                    type: 'POST',
                    url: 'ajax/inventory_ajax.php',
                    data: {
                        month:month,
                        year:year,
                        load_forecast_data:1
                    },
                    success: function (response) {
                        // alert(response)
                    },
                    error: function () {
                        console.log("Error Generate Report function");
                    }
                });
            })
        });

    </script>
<?php
include 'includes/footer.php';