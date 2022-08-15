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
        <main id="main-container" style="min-height: 871px;">
            <?php
            if (isset($_GET['view'])){
                $inventory_id = $_GET['view'];
                ?>
                <!-- Page Content -->
                <div class="content">
                    <!-- Overview -->
                    <h2 class="content-heading">Overview</h2>
                    <!-- Products -->
                    <?php
                    $item_query = $conn->query("SELECT item, description, min_qty, date_created FROM tbl_inventory WHERE tbl_inventory.inventory_id = $inventory_id");
                    $item = $item_query->fetch();
                    ?>
                    <table class="table table-striped table-borderless table-sm mt-20">
                        <tbody>
                        <tr>
                            <td class="font-w600">Item name:</td>
                            <td><?=$item['item']?></td>
                            <td class="font-w600">Date Created:</td>
                            <td><?=date('F d Y', strtotime($item['date_created']))?></td>
                        </tr>
                        <tr>
                            <td class="font-w600">Description:</td>
                            <td><?=$item['description']?></td>
                            <td class="font-w600">Quantity:</td>
                            <td><?=$item['min_qty']?></td>
                        </tr>
                        </tbody>
                    </table>
                    <h2 class="content-heading">Inbound/Outbound</h2>
                    <div class="block block-rounded">
                        <div class="block-content block-content-full">
                            <!-- Products Table -->
                            <table id="tbl_items_bounds" class="table table-borderless table-striped table-vcenter ">
                                <thead>
                                <tr>
                                    <th style="width: 100px;">ID</th>
                                    <th>Type</th>
                                    <th>User</th>
                                    <th class="d-none d-sm-table-cell">Quantity</th>
                                    <th class="d-none d-md-table-cell">Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $items_bound_qry = $conn->query("SELECT
                                                                            tbl_inventory_bound.inventory_bound_id,
                                                                            tbl_inventory_bound.qty,
                                                                            tbl_inventory_bound.type,
                                                                            tbl_inventory_bound.date,
                                                                            tbl_inventory_bound.user_id,
                                                                            users.fname,
                                                                            users.lname
                                                                            FROM
                                                                            tbl_inventory_bound
                                                                            INNER JOIN users ON users.user_id = tbl_inventory_bound.user_id
                                                                            WHERE
                                                                            tbl_inventory_bound.inventory_id = $inventory_id");
                                $items_bound = $items_bound_qry->fetchAll();
                                foreach ($items_bound as $item){
                                    $inventory_bound_id = $item['inventory_bound_id'];
                                    $type = $item['type'];
                                    $qty = $item['qty'];
                                    $date = $item['date'];
                                    $type_status = '';
                                    $user_fullname = ucwords(strtolower($item['fname'].' '.$item['lname']));

                                    if ($type == 'outbound'){
                                        $type_status = '<span class="badge badge-warning">Outbound</span>';
                                    }else{
                                        $type_status = '<span class="badge badge-success">Inbound</span>';
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <?=$inventory_bound_id?>
                                        </td>
                                        <td class="d-none d-sm-table-cell"><?=$type_status?></td>
                                        <td class="d-none d-md-table-cell"><?=$user_fullname?></td>
                                        <td class="d-none d-md-table-cell"><?=$qty?></td>
                                        <td class="d-none d-sm-table-cell">
                                            <?php echo $date?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <!-- END Products Table -->

                        </div>
                    </div>
                    <!-- END Products -->
                </div>
                <!-- END Page Content -->

            <?php
            }else{
            ?>
            <!-- Hero -->
            <div class="bg-image" style="background-image: url('assets/media/photos/photo13@2x.jpg');">
                <div class="bg-black-op-75">
                    <div class="content content-top content-full text-center">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10">INVENTORY</h1>
                            <h2 class="h4 font-w400 text-white-op mb-0">You currently have 4.360 in the catalog!</h2>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->

            <!-- Page Content -->
            <div class="content">
                <?php
//                FETCH TOTAL STOCKS
                    $fetch_total_qry = $conn->query("SELECT count(`inventory_id`) as total_stocks FROM `tbl_inventory` WHERE is_removed = 0");
                    $total_stocks = $fetch_total_qry->fetch();
                    $total_stock = $total_stocks['total_stocks'];

//                FETCH AVAILABLE ITEMS
                    $total_available_qry = $conn->query("SELECT count(`inventory_id`) as total_available FROM `tbl_inventory` WHERE is_removed = 0 AND min_qty != 0");
                    $fetch_total_available = $total_available_qry->fetch();
                    $total_stock = $fetch_total_available['total_available'];

//                 FETCH OUT of stock ITEMS
                $total_unavailable_qry = $conn->query("SELECT count(`inventory_id`) as total_unavailable FROM `tbl_inventory` WHERE is_removed = 0 AND min_qty = 0");
                $fetch_total_unavailable = $total_unavailable_qry->fetch();
                $total_out_of_stock = $fetch_total_unavailable['total_unavailable'];

                ?>
                <!-- Overview -->
                <h2 class="content-heading">Overview</h2>
                <div class="row gutters-tiny">
                    <!-- All Items -->
                    <div class="col-md-6 col-xl-3">
                        <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full block-sticky-options">
                                <div class="block-options">
                                    <div class="block-options-item">
                                        <i class="fa fa-circle-o fa-2x text-info-light"></i>
                                    </div>
                                </div>
                                <div class="py-20 text-center">
                                    <div class="font-size-h2 font-w700 mb-0 text-info" data-toggle="countTo" data-to="<?=$total_stock?>">0</div>
                                    <div class="font-size-sm font-w600 text-uppercase text-muted">All Items</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- END All Items -->

                    <!-- Top Sellers -->
                    <div class="col-md-6 col-xl-3">
                        <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full block-sticky-options">
                                <div class="block-options">
                                    <div class="block-options-item">
                                        <i class="fa fa-star fa-2x text-warning-light"></i>
                                    </div>
                                </div>
                                <div class="py-20 text-center">
                                    <div class="font-size-h2 font-w700 mb-0 text-warning" data-toggle="countTo" data-to="<?=$total_stock?>">0</div>
                                    <div class="font-size-sm font-w600 text-uppercase text-muted">Available Items</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- END Top Sellers -->

                    <!-- Out of Stock -->
                    <div class="col-md-6 col-xl-3">
                        <a class="block block-rounded block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full block-sticky-options">
                                <div class="block-options">
                                    <div class="block-options-item">
                                        <i class="fa fa-warning fa-2x text-danger-light"></i>
                                    </div>
                                </div>
                                <div class="py-20 text-center">
                                    <div class="font-size-h2 font-w700 mb-0 text-danger" data-toggle="countTo" data-to="<?=$total_out_of_stock?>">0</div>
                                    <div class="font-size-sm font-w600 text-uppercase text-muted">Out of Stock</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- END Out of Stock -->

                    <!-- Add Product -->
                    <div class="col-md-6 col-xl-3">
                        <a class="block block-rounded block-link-shadow" data-toggle="modal" data-target="#add_new_item_modal">
                            <div class="block-content block-content-full block-sticky-options">
                                <div class="block-options">
                                    <div class="block-options-item">
                                        <i class="fa fa-archive fa-2x text-success-light"></i>
                                    </div>
                                </div>
                                <div class="py-20 text-center">
                                    <div class="font-size-h2 font-w700 mb-0 text-success">
                                        <i class="fa fa-plus"></i>
                                    </div>
                                    
                                    <div class="font-size-sm font-w600 text-uppercase text-muted">New Item</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- END Add Product -->
                </div>
                <!-- END Overview -->

                <!-- Products -->
                <div class="content-heading">
                    Items (<?=$total_stock?>)
                </div>
                <div class="block block-rounded">
                    <div class="block-content block-content-full">
                        <!-- Products Table -->
                        <table id="tbl_items" class="table table-borderless table-striped table-vcenter ">
                            <thead>
                            <tr>
                                <th style="width: 100px;">ID</th>
                                <th>Product</th>
                                <th>Image</th>
                                <th class="d-none d-sm-table-cell">Status</th>
                                <th class="d-none d-sm-table-cell">Quantity</th>
                                <th class="d-none d-md-table-cell">Date Updated</th>
                                <th class="text-right">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $fetch_items_qry = $conn->query("SELECT `inventory_id`, `item`, `min_qty`,`img_src`, `date_created`, `user_id` FROM `tbl_inventory` WHERE is_removed = 0");
                            $items = $fetch_items_qry->fetchAll();
                            foreach ($items as $item){
                                $item_id = $item['inventory_id'];
                                $item_name = $item['item'];
                                $min_qty = $item['min_qty'];
                                $img_src = $item['img_src'];
                                $date_created = $item['date_created'];
                                $stock_status = '';

                                if ($min_qty <= 0){
                                    $stock_status = '<span class="badge badge-danger">Out of Stock</span>';
                                }else{
                                    $stock_status = '<span class="badge badge-success">Available</span>';
                                }
                            ?>
                                <tr>
                                    <td>
                                        <a class="font-w600" href="inventory.php?view=<?=$item_id?>"><?=$item_id?></a>
                                    </td>
                                    <td>
                                        <a href="inventory.php?view=<?=$item_id?>"><?=$item_name?></a>
                                    </td>
                                    <td>
                                        <img class="img pd-l-30" src="assets/media/photos/inventory/<?=$img_src?>" style="height: 60px; !important">
                                    </td>
                                    <td class="d-none d-sm-table-cell"><?=$stock_status?></td>
                                    <td class="d-none d-md-table-cell"><?=$min_qty?></td>
                                    <td class="d-none d-sm-table-cell">
                                        <?php echo $date_created?>
                                    </td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success btn-sm js-tooltip-enabled" onclick="fetch_stocks(<?=$item_id?>,'add')" data-toggle="tooltip" title="Add new stocks" data-original-title="Add new stocks"><span class="fa fa-plus"></span></button>
                                            <button type="button" class="btn btn-primary btn-sm js-tooltip-enabled" onclick="fetch_stocks(<?=$item_id?>,'pull')" data-toggle="tooltip" title="Pull stocks from inventory" data-original-title="Pull stocks from inventory"><span class="fa fa-minus"></span></button>
                                            <button type="button" class="btn btn-danger btn-sm js-tooltip-enabled" onclick="remove_item(<?=$item_id?>,<?=$min_qty?>)" data-toggle="tooltip" title="Remove item from the list" data-original-title="Remove item from the list">
                                                <span class="si si-trash"></span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                        <!-- END Products Table -->

                    </div>
                </div>
                <!-- END Products -->
            </div>
            <!-- END Page Content -->
            <?php
            }
            ?>

        </main>
        <!-- END Main Container -->
    </div>

    <!-- Add New Item Modal -->
    <div class="modal fade" id="add_new_item_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Add New Item Form</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="add_new_item_form">
                            <input type="hidden" name="add_new_item" value="1">
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="item_name">Item name</label>
                                    <input type="text" class="form-control form-control-lg" id="item_name" name="item_name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="desc">Description</label>
                                <div class="col-12">
                                    <textarea class="form-control form-control-lg" id="desc" name="desc" placeholder="" required></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="min_qty">Quantity</label>
                                <div class="col-12">
                                    <input type="number" class="form-control form-control-lg" id="min_qty" name="min_qty" placeholder="" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-12" for="item_img">Image</label>
                                <div class="col-12">
                                    <input type="file" class="form-control form-control-lg" id="item_img" name="item_img">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                        <i class="fa fa-plus mr-5"></i> Add
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Add New Item Modal -->

    <!-- Add Stocks From Inventory Modal -->
    <div class="modal fade" id="add_stocks_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Inbound Item</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <table class="table table-sm table-bordered table-striped">
                            <thead>
                            <th style="width: 70%;">Item name</th>
                            <th>Remaining min_qty</th>
                            </thead>
                            <tr>
                                <td><span id="span_item_name_add"></span></td>
                                <td><span id="span_remaining_min_qty_add"></span></td>
                            </tr>
                        </table>
                        <hr>
                        <form id="add_stocks_form">
                            <input type="hidden" id="add_item_id" name="add_item_id">
                            <div class="form-group row">
                                <label class="col-12" for="add_min_qty">Quantity</label>
                                <div class="col-12">
                                    <input type="number" class="form-control form-control-lg" id="add_min_qty" name="add_min_qty" placeholder="Enter quantity to be added" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-hero btn-alt-success min-width-175">
                                        <i class="fa fa-plus mr-5"></i> Add
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END Add Stocks From Inventory Modal -->

    <!-- Pull Stocks From Inventory Modal -->
    <div class="modal fade" id="pull_stocks_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Outbound Item</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <table class="table table-sm table-bordered table-striped">
                            <thead>
                                <th style="width: 70%;">Item name</th>
                                <th>Remaining min_qty</th>
                            </thead>
                            <tr>
                                <td><span id="span_item_name"></span></td>
                                <td><span id="span_remaining_min_qty"></span></td>
                            </tr>
                        </table>
                        <hr>
                        <form id="pull_stocks_form">
                            <input type="hidden" id="pull_item_id" name="pull_item_id">
                            <div class="form-group row">
                                <label class="col-12" for="pull_min_qty">Quantity</label>
                                <div class="col-12">
                                    <input type="number" class="form-control form-control-lg" id="pull_min_qty" name="pull_min_qty" placeholder="Enter quantity" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                        <i class="fa fa-minus mr-5"></i> Pull
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
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

    <script>
        $(document).ready(function () {
            // Datatable for item table
            $('#tbl_items').DataTable();
            $('#inventory_sidebar').addClass('open');
            // TRIGGER FUNCTION ON CLICKING PULL STOCKS BUTTON
            $('#btn_pull_stocks').click(function (){
                alert('asdasd');
            });

            $("#add_new_item_form").submit(function (event) {
                event.preventDefault();
                // alert('test')
                $.ajax({
                    type: 'POST',
                    url: 'ajax/inventory_ajax.php',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        console.log("Error adding employee function");
                    }
                });
            });

            // FUNCTION FOR PULLING ITEM AND UPDATING RECORD
            $("#pull_stocks_form").submit(function (event) {
                event.preventDefault();
                // alert('test')
                inventory_id = $('#pull_item_id').val();
                remaining_min_qty = parseInt(document.getElementById("span_remaining_min_qty").innerText);
                pull_min_qty = $('#pull_min_qty').val();
                if(remaining_min_qty < pull_min_qty){
                    alert('Insufficient stock');
                }else{
                    if (confirm("Are you sure?")){
                        $.ajax({
                            type: 'POST',
                            url: 'ajax/inventory_ajax.php',
                            data: {
                                inventory_id:inventory_id,
                                remaining_min_qty:remaining_min_qty,
                                pull_min_qty:pull_min_qty,
                                pull_stocks:1
                            },
                            success: function(response) {
                                alert(response);
                                location.reload();
                            },
                            error: function() {
                                console.log("Error adding employee function");
                            }
                        });
                    }
                }
            });

            // FUNCTION FOR SUBMITTING/ADDING NEW STOCK OF AN ITEM
            $("#add_stocks_form").submit(function (event) {
                event.preventDefault();
                // alert('test')
                inventory_id = $('#add_item_id').val();
                remaining_min_qty = document.getElementById("span_remaining_min_qty_add").innerText;
                add_min_qty = $('#add_min_qty').val();

                $.ajax({
                    type: 'POST',
                    url: 'ajax/inventory_ajax.php',
                    data: {
                        inventory_id:inventory_id,
                        remaining_min_qty:remaining_min_qty,
                        add_min_qty:add_min_qty,
                        add_stocks:1
                    },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        console.log("Error adding employee function");
                    }
                });
            });
        });

        // FUNCTION FOR DISPLAYING DETAILS IN THE PROCESS OF DEDUCTING STOCK/min_qty OF AN ITEM
        function fetch_stocks(id,type){
            if (type === 'add'){
                $('#add_stocks_modal').modal("show");
            }else{
                $('#pull_stocks_modal').modal("show");
            }
            $.ajax({
                type: 'POST',
                url: 'ajax/inventory_ajax.php',
                dataType: 'JSON',
                data: {
                    inventory_id: id,
                    fetch_stocks:1
                },
                success: function(response) {
                    var len = response.length;
                    for(var i=0; i<len; i++){
                        if (type === 'add'){
                            document.getElementById("span_item_name_add").innerText = response[i].item_name;
                            document.getElementById("span_remaining_min_qty_add").innerText = response[i].min_qty;
                            $('#add_item_id').val(response[i].inventory_id);
                        }else{
                            document.getElementById("span_item_name").innerText = response[i].item_name;
                            document.getElementById("span_remaining_min_qty").innerText = response[i].min_qty;
                            $('#pull_item_id').val(response[i].inventory_id);
                        }
                    }
                    console.log(response)
                },
                error: function() {
                    console.log("Error adding employee function");
                }
            });
        }

        function remove_item(id,min_qty){
            // if (min_qty !== '0'){
            //     confirm(""){
            //         alert("response");
            //     }
            // }
            if (confirm("Are you sure you want to remove this item?")){
                $.ajax({
                    type: 'POST',
                    url: 'ajax/inventory_ajax.php',
                    data: {
                        inventory_id: id,
                        remove_item:1
                    },
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        console.log("Error adding employee function");
                    }
                });
            }

        }
    </script>
<?php
include 'includes/footer.php';