<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
include 'queries/inventory_stocks_query.php'
?>
    <div id="page-container"
         class="sidebar-o enable-page-overlay side-scroll <?= $header_layout ?> page-header-inverse <?= $main_content . ' ' . $sidebar_layout ?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        include 'includes/disable_access.php';
        ?>
        <!-- Main Container -->
        <main>
            <!-- Page Content -->
            <div class="content">
                <!-- USER ROLE  -->
                <div class="content-heading">
                    Inventory Reporting
                    <div class="row float-right" style="width: 25%">
                        <select class="col-4 form-control" id="select_months" name="select_months">
                            <?php
                            $yr_qry = $conn->query("SELECT YEAR (tbl_inventory.date_created) as yr
                                                                FROM tbl_inventory
                                                                GROUP BY YEAR (tbl_inventory.date_created)");
                            $months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec");
                            for ($i = 0; $i < count($months); $i++) {
                                $month_num = $i + 1;
                                ?>
                                <option value="<?= $month_num ?>"><?= $months[$i] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <select class="col-4 form-control" id="select_yr" name="select_yr">
                            <?php
                            $yr_qry = $conn->query("SELECT YEAR (tbl_inventory.date_created) as yr
                                                                FROM tbl_inventory
                                                                GROUP BY YEAR (tbl_inventory.date_created)");
                            $years = $yr_qry->fetchAll();
                            foreach ($years as $year) {
                                ?>
                                <option value="<?= $year['yr'] ?>"><?= $year['yr'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <button class="col-4 btn btn-alt-success" id="generate_report">Go</button>
                    </div>
                </div>
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h4 id="report_h4" class="h4 text-muted text-uppercase text-center"><span id="report_title"></span> Report</h4>
                        <div class="block-options">
                            <!-- Print Page functionality is initialized in Helpers.print() -->
                            <button type="button" class="btn-block-option" onclick="refresh_data()">
                                <i class="si si-refresh"></i> Refresh
                            </button>
                            <button type="button" class="btn-block-option" onclick="Codebase.helpers('print-page');">
                                <i class="si si-printer"></i> Print Report
                            </button>
                        </div>
                    </div>
                    <div class="block-content block-content-full">

                        <div class="text-right">
                            <button class="btn btn-primary d-print-none" id="btn_forecast" style="display: none"><i class="fa fa-eye"></i> Next Month Forecast</button>
                        </div>
                        <!-- Products Table -->
                        <div class="table-responsive">
                            <table id="tbl_items" class="table table-borderless table-striped table-vcenter table-sm mt-20">
                                <thead class="text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th class="text-capitalize d-md-table-cell">Total Qty Received</th>
                                    <th class="text-capitalize d-md-table-cell">Total Qty Issued</th>
                                    <th class="text-capitalize d-md-table-cell">Total Qty Remaining</th>
                                    <th class="text-capitalize d-md-table-cell">Reorder Level</th>
                                    <th class="text-capitalize d-sm-table-cell">Unit Price</th>
                                    <th class="text-capitalize d-sm-table-cell">Order Qty</th>
                                    <th class="text-capitalize d-sm-table-cell">Min Order Price</th>
                                    <th class="text-capitalize d-sm-table-cell">Max Order Price</th>
                                    <th class="text-capitalize d-md-table-cell">Status</th>
                                    <th class="text-capitalize d-md-table-cell d-print-none">Action</th>
                                </tr>
                                </thead>
                                <tbody class="text-center" id="tbody_reports">

                                </tbody>
                            </table>
                        </div>
                        <!-- END Products Table -->
                    </div>
                </div>
                <!-- END Products -->
            </div>
            <!-- END Page Content -->
        </main>
    </div>
    <!-- END Main Container -->
    <!--EDIT INVENTORY BOUND QTY MODAL-->
    <div class="modal fade" id="edit_stock_in_modal" tabindex="-1" role="dialog" aria-labelledby="modal_view_details"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Edit Quantity</h3>
                        <div class="block-options">
                            <button class="btn-block-option" data-dismiss="modal" aria-label="Close"
                                    onclick="reload_page()">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content" id="">
                        <form id="update_stock_in">
                            <input type="hidden" name="update_stock_in_id" id="update_stock_in_id">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="be-contact-name">Type</label>
                                    <input type="text" class="form-control form-control-lg" id="update_stock_in_type"
                                           name="update_stock_in_type" disabled>
                                </div>
                                <div class="col-12 mt-10">
                                    <label for="be-contact-name">Edit Qty</label>
                                    <input type="text" class="form-control form-control-lg" id="update_stock_in_qty"
                                           name="update_stock_in_qty" value="" required>
                                </div>
                                <div class="col-12 mt-10">
                                    <label for="be-contact-name">Edit Previous Qty</label>
                                    <input type="text" class="form-control form-control-lg"
                                           id="update_stock_in_prev_qty" name="update_stock_in_prev_qty" value=""
                                           required>
                                </div>
                                <div class="col-12 mt-10">
                                    <button type="submit" class="btn btn-block btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--EDIT INVENTORY BOUND QTY MODAL-->
    <div class="modal fade" id="add_qty_order_modal" tabindex="-1" role="dialog" aria-labelledby="modal_view_details"
         aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Enter Quantity Order</h3>
                        <div class="block-options">
                            <button class="btn-block-option" data-dismiss="modal" aria-label="Close" >
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content" id="">
                        <form id="form_add_qty_order">
                            <div class="form-group row">
                                <div class="col-12">
                                    <input type="hidden" class="form-control form-control-lg" id="add_qty_order_inventory_id" name="add_qty_order_inventory_id">
                                    <input type="hidden" class="form-control form-control-lg" id="add_qty_order_month" name="add_qty_order_month">
                                    <input type="hidden" class="form-control form-control-lg" id="add_qty_order_year" name="add_qty_order_year">
                                    <input type="number" class="form-control form-control-lg" id="add_qty_order" name="add_qty_order">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-block btn-primary mt-10"><i class="fa fa-save"></i> Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!--NEXT MONTH FORECAST MODAL-->
    <div class="modal fade" id="forecast_modal" tabindex="-1" role="dialog" aria-labelledby="modal_view_details"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>

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
                    $('#add_qty_order_modal').modal('hide');
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/inventory_ajax.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData:false,
                        success: function (response) {
                            if (response === 'success'){
                                alert("Successfully added Quantity Order.")
                                $("#generate_report").trigger("click");
                            }else{
                                alert("Something went wrong.")
                            }
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
                month_text = $( "#select_months option:selected" ).text()
                window.open(
                    "inventory_monthly_forecast.php?month="+month+"&month_text="+month_text+"&year="+year+"",
                    '_blank' // <- This is what makes it open in a new window.
                );
                // document.location.href = ";
                // $('#forecast_modal').modal('show');
                // $('#month_forecast_div').show();
                // $.ajax({
                //     type: 'POST',
                //     url: 'ajax/inventory_ajax.php',
                //     data: {
                //         month:month,
                //         year:year,
                //         load_forecast_data:1
                //     },
                //     success: function (response) {
                //         // alert(response)
                //     },
                //     error: function () {
                //         console.log("Error Generate Report function");
                //     }
                // });
            })
        })

        function add_qty_order(inv_id,order_qty){
            month = $('#select_months').val();
            year = $('#select_yr').val();
            $('#add_qty_order_modal').modal('show');
            $("#add_qty_order_inventory_id").val(inv_id)
            $('#add_qty_order_month').val(month)
            $('#add_qty_order_year').val(year)
            $('#add_qty_order').val(order_qty)
        }

        function add_to_forecast(inv_id){
            month = $('#select_months').val();
            year = $('#select_yr').val();
            if (confirm("Are you sure?")){
                $.ajax({
                    type: 'POST',
                    url: 'ajax/inventory_ajax.php',
                    data: {
                        month:month,
                        year:year,
                        inv_id:inv_id,
                        add_to_forecast:1
                    },
                    success: function (response) {
                        if (response === 'success'){
                            alert("Successfully added to Monthly Forecast.")
                            $("#generate_report").trigger("click");
                        }else{
                            alert("Something went wrong.")
                        }
                    },
                    error: function () {
                        console.log("Error Generate Report function");
                    }
                });
            }
        }

        function remove_forecast(inv_id){
            month = $('#select_months').val();
            year = $('#select_yr').val();
            if (confirm("Are you sure?")){
                $.ajax({
                    type: 'POST',
                    url: 'ajax/inventory_ajax.php',
                    data: {
                        month:month,
                        year:year,
                        inv_id:inv_id,
                        remove_forecast:1
                    },
                    success: function (response) {
                        if (response === 'success'){
                            alert("Successfully removed from Monthly Forecast.")
                            $("#generate_report").trigger("click");
                        }else{
                            alert("Something went wrong.")
                        }
                    },
                    error: function () {
                        console.log("Error Generate Report function");
                    }
                });
            }
        }

        function refresh_data(){
            $("#generate_report").trigger("click");
        }
    </script>
<?php
include 'includes/footer.php';