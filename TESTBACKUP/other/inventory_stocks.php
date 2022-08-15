<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
include 'queries/inventory_stocks_query.php'
?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        include 'includes/disable_access.php';
        ?>
        <!-- Main Container -->
        <main>
            <?php
                if (isset($_GET['view'])){
                    $item_id = $_GET['view'];
                    //                This query is for fetching inventory data
                
                $history_query = $conn->query("SELECT tbl_inventory.inventory_id,
                                                        `item`, 
                                                        `current_stock_qty`,
                                                        tbl_inventory.date_created,
                                                        tbl_inventory.date_updated, 
                                                        tbl_inventory.img_src,
                                                        -- tbl_employees.employee_id,
                                                        -- `firstname`,
                                                        -- `middlename`,
                                                        -- `lastname`,
                                                        `is_removed`,                                            
                                                        `type`,
                                                        `date`,
                                                        `qty`,   
                                                        `previous_qty`,                                             
                                                        `inventory_bound_id`,
                                                        `requested_by_employee`,
                                                        `requested_by_location`
                                                        
                                                        FROM tbl_inventory 
                                                        INNER JOIN tbl_inventory_bound
                                                        ON tbl_inventory.inventory_id = tbl_inventory_bound.inventory_id
                                                        -- INNER JOIN tbl_employees
                                                        -- ON tbl_inventory_bound.requested_by_employee = employee_id
                                                        WHERE tbl_inventory.inventory_id = $item_id ORDER by inventory_bound_id DESC");
                $history_items = $history_query->fetchAll();

                $item_query = $conn->query("SELECT item, description, current_stock_qty , date_created, img_src FROM tbl_inventory WHERE tbl_inventory.inventory_id = $item_id");
                $item1 = $item_query->fetch();      


                $edit_item_logs_disable = '';

                 $edit_item_logs_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 22 AND `status` = 0");
                    if ($edit_item_logs_access->rowCount() > 0){
                        $edit_item_logs_access = $edit_item_logs_access->fetch();
                        $edit_item_logs_status = $edit_item_logs_access['status'];
                       
                        if ($delete_status == 1){
                            $edit_item_logs_disable = 'disabled';
                        }
                    }else{
                        $edit_item_logs_disable = 'disabled';
                    }
                
            ?>
                    <!-- Page Content -->
                    <div class="content">
                        <!-- Stocks -->
                        <h5 class="content-heading ">Overview    </h5> 
                        <!-- <button type="button" class="btn btn-outline-primary ">Back</button>           -->
                        <nav class="breadcrumb push mb-0 pl-0 float-right">                           
                            <button class="btn btn-sm btn-alt-primary float-right" onclick="history.back()"><i class="si si-action-undo"></i> Go Back</button>            
                        </nav>
                     
                        <!-- Products -->
                        <div class="row">
                            <div class="col-md-10">
                                <table class="table table-striped table-borderless table-sm mt-20">
                                    <tbody class="text-center">
                                    <tr>
                                        <td class="font-w600">Item Name:</td>
                                        <td><?=$item1['item']?></td>
                                        <td class="font-w600">Date Created:</td>
                                        <td><?=date('F d Y', strtotime($item1['date_created']))?></td>
                                    </tr>
                                    <tr>
                                        <td class="font-w600">Description:</td>
                                        <td><?=$item1['description']?></td>
                                        <td class="font-w600">Quantity:</td>
                                        <td><?=$item1['current_stock_qty']?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-2">
                               
                                <img class ="float-right" width="100" height="100" src=" <?php echo "assets/media/photos/inventory/".$item1['img_src']; ?>">
                            </div>
                        </div>    
                        <h2 class="content-heading">Inbound/Outbound</h2>
                        <div class="block block-rounded">
                            <div class="block-content block-content-full">
                                <!-- Products Table -->
                                <table id="tbl_items" class="table table-borderless text-center table-striped table-vcenter ">
                                    <thead>
                                    <tr>
                                        <th style="width: 100px;">ID</th>
                                        <th>Type</th>
                                        <th class=" d-sm-table-cell">Quantity</th>
                                        <th class=" d-sm-table-cell">Previous Quantity</th>
                                        <th class=" d-md-table-cell">Date</th>
                                        <th class=" d-md-table-cell">Requested by</th>
                                        <th class=" d-md-table-cell">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($history_items as $item){  ?>
                                            <tr>                                           
                                                <td> <?= $item['inventory_bound_id']?> </td>
                                                <td> 
                                                    <?php
                                                        if($item['type'] == 'inbound'){

                                                        echo '<span class="badge badge-success">Added</span>';
                                                        } else { 

                                                        echo '<span class="badge badge-warning">Pulled Out</span>';
                                                        }
                                                    
                                                    ?>
                                            </td>
                                                <td class=" d-md-table-cell"><?=$item['qty']?></td>
                                                <td class=" d-md-table-cell"><?=$item['previous_qty']?></td>
                                                <td class=" d-sm-table-cell"> <?=date('M d, Y', strtotime($item['date']))?> </td>
                                                                                    
                                                <?php if ($item['requested_by_employee'] ==""): ?>
                                                <td class=" d-sm-table-cell"> <?= $item['requested_by_location']?> </td>    
                                                <?php else: ?>
                                                        <!-- get the employee name, because we only have the ID. -->
                                                    <?php    
                                                    $emp_id =  $item['requested_by_employee'];
                                                    $employee_qry  = $conn->query("SELECT `firstname`, `lastname`, `middlename` FROM tbl_employees WHERE `employee_id` = $emp_id");
                                                    $emp = $employee_qry->fetch();
                                                    ?>
                                                  <td class=" d-sm-table-cell"> <?= $emp['firstname']." ".$emp['middlename']." ".$emp['lastname'] ?>  </td>      
                                                <?php endif ?>

                                                <td class=" d-sm-table-cell">
                                                    <button class="btn btn-sm btn-secondary"  <?=$edit_item_logs_disable?> onclick="edit_stock_in(<?=$item['inventory_bound_id']?>,<?=$item['qty']?>,<?=$item['previous_qty']?>,'<?=$item['type']?>')"><i class="fa fa-pencil"></i> Edit</button>
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
            }else{ // ---------------------------------- INVENTORY LIST CONTENT ----------------------------------------
            ?>
            <!-- Hero -->
           
             <!-- Hero -->
            <div class="bg-image" style="background-image: url('assets/media/photos/construction4.jpeg');">
                <div class="bg-black-op-75">
                    <div class="content content-top content-full text-center">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10">Manage Stocks</h1>

                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->

            <!-- Page Content -->
            <div class="content">
                <h2 class="content-heading">Overview</h2>
                <div class="row gutters-tiny">
                    <div class="col-6 col-xl-3">
                        <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-right mt-15  d-sm-block">
                                    <i class="fa fa-star fa-2x text-success-light"></i>
                                </div>
                                <div class="font-size-h3 font-w600 text-success-light" data-toggle="countTo" data-speed="1000" data-to="<?=$total_items_available?>">0</div>
                                <div class="font-size-sm font-w600 text-uppercase "> AVAILABLE ITEMS</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-xl-3">
                        <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                            <div class="block-content block-content-full clearfix">
                                <div class="float-right mt-15  d-sm-block">
                                    <i class="fa fa-warning fa-2x text-danger-light"></i>
                                </div>
                                <div class="font-size-h3 font-w600 text-danger-light" data-toggle="countTo" data-speed="1000" data-to="<?=$total_out_of_stock?>">0</div>
                                <div class="font-size-sm font-w600 text-uppercase text-muted">  OUT OF STOCK</div>
                            </div>
                        </a>
                    </div>
                    <!--  Pull Out Stocks -->
                </div>
                <!-- END Overview -->
                <!-- Items -->
                <!-- USER ROLE  -->
                <?php 
                    $pull_item_disable = '';
                    $pull_item_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 20 AND `status` = 0");
                    if ($pull_item_access->rowCount() > 0){
                        $pull_item_access = $pull_item_access->fetch();
                        $pull_item_status = $pull_item_access['status'];
                        if ($delete_status == 1){
                            $pull_item_disable = 'disabled';
                        }
                    }else{
                        $pull_item_disable = 'disabled';
                    }
                    // USER ROLE ACCESS
                    $add_stock_disable = '';
                    $add_stock_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 21 AND `status` = 0");
                    if ($add_stock_access->rowCount() > 0){
                        $add_stock_access = $add_stock_access->fetch();
                        $add_item_status = $add_stock_access['status'];
                        if ($add_stock_access == 1){
                            $add_stock_disable = 'disabled';
                        }
                    }else{
                        $add_stock_disable = 'disabled';
                    }
                 ?>

                <div class="content-heading"> 
                    Items (<?=$total_items?>)
                    <button  class="btn btn-outline-success  btn-rounded mr-5 mb-5  float-right"  <?=$add_stock_disable ?> data-toggle="modal" data-target="#add_new_item_modal" >
                        <i class="fa fa-plus mr-5"></i>Add New Stocks
                    </button>
                    <button  <?=$pull_item_disable ?> class="btn btn-success  btn-rounded mr-5 mb-5  float-right" data-toggle="modal" data-target="#pull_stock_modal" >
                        <i class="fa fa-minus mr-5"></i>Pull Out Stocks
                    </button>
                   
                </div>
                <div class="block block-rounded">
                    <div class="block-content block-content-full">
                        <!-- Products Table -->
                        <table id="tbl_items" class="table table-borderless table-striped table-vcenter">
                            <thead class="text-center">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class=" d-md-table-cell">Ref. ID</th>
                                    <th>Image</th>                                
                                    <th>Current Quantity</th>                                 
                                    <th class=" d-md-table-cell">Unit</th>
                                    <th class=" d-sm-table-cell">Price</th> 
                                    <th class=" d-md-table-cell">Min Quantity</th>
                                    <th class=" d-sm-table-cell">Date Updated</th>                          
                                    <th class=" d-md-table-cell">Status</th>
                                    <th class=" d-md-table-cell">Action</th>
                                </tr>
                            </thead>
                        <tbody class="text-center">

                            
                            <?php
                            //                            ENABLE/DISABLE EDIT AND DELETE BUTTON ACCORDING TO USER ACCESS

                            //  User Access end.

                            $fetch_items_qry = $conn->query("SELECT 
                                                    `inventory_id`,
                                                    `item`,
                                                    `current_stock_qty`, 
                                                    `min_qty`,
                                                    `img_src`, 
                                                    `date_created`, 
                                                    `user_id` 
                                                    `unit`,
                                                    `item_currency`,
                                                    `item_price`,
                                                    `try`
                                                    FROM `tbl_inventory` WHERE is_removed = 0  ORDER BY `inventory_id` ASC ");
                            $items = $fetch_items_qry->fetchAll();
                            

                            $item_count = 1;
                            foreach ($items as $item){
                                $item_id = $item['inventory_id'];
                                $item_name = $item['item'];
                                $min_qty = $item['min_qty'];
                                $item_unit = $item['try'];
                                $item_price = $item['item_price'];
                                $item_currency  = $item['item_currency'];
                                $current_stock_qty = $item['current_stock_qty'];
                                $img_src = $item['img_src'];
                                $date_created = date('F d, Y', strtotime($item['date_created']));
                                $stock_status = '';
                            ?>
                                <tr>
                                    <td><?=$item_count++?></td>
                                     <td> <a href="<?=(($edit_disable == '')?'inventory_stocks.php?view='.$item_id:'#')?>"> 
                                            <?=$item_name?>
                                            </a>
                                    </td>
                                    <td> <?=$item_id?></td>
                                    <td>
                                        <img class="img pd-l-30" shape="circle" src="assets/media/photos/inventory/<?=$img_src?>" style="height: 60px; !important">
                                
                                    </td>                                   
                                    <td class=" d-sm-table-cell"> <?php echo $current_stock_qty?> </td>
                                    <td class=" d-sm-table-cell"> <?php echo $item_unit ?> </td>
                                    <td class=" d-sm-table-cell"> <?=$item_price?><?=(($item_price != 0)?$item_currency:'')?> </td>
                                    <td class=" d-sm-table-cell"> <?php echo $min_qty?> </td>
                                    <td class=" d-sm-table-cell"> <?php echo $date_created?> </td>
                                    <td class=" d-sm-table-cell">
                                            <?php
                                                //compare current quantity to the minimum quantity
                                                $badge = '';
                                                $status = '';
                                                if($current_stock_qty < $min_qty){
                                                    $status =  "Reorder";
                                                    $badge = "badge badge-danger";
                                                } else  {
                                                    $status = "Full";
                                                    $badge = "badge badge-success";
                                                }
                                            ?> 
                                        <span class="<?=$badge;?>"><?=$status;?> </span>
                                    </td>
                                    <td>
                                        <a href="<?=(($edit_disable == '')?'inventory_stocks.php?view='.$item_id:'#')?>" class="btn btn-warning btn-sm" data-toggle="tooltip" title="View logs"><i class="fa fa-history"></i></a>
                                        <button type="button" class="btn btn-success btn-sm js-tooltip-enabled" <?=$add_stock_disable ?>
                                            onclick="fetch_stocks(<?=$item_id?>,'add',<?=$current_stock_qty?>,'<?=$item_name?>','<?= $min_qty ?>','<?=$img_src?>')"
                                            data-toggle="tooltip" title="Add new stocks">
                                            <span class="fa fa-plus"></span>
                                        </button>                                        
                                         <button type="button" class="btn btn-primary btn-sm js-tooltip-enabled" <?=$pull_item_disable ?>
                                            onclick="fetch_stocks(<?=$item_id?>,'pull',<?=$current_stock_qty?>,'<?=$item_name?>','<?= $min_qty ?>','<?=$img_src?>')"
                                            data-toggle="tooltip" title="Pull stocks from inventory">
                                            <span class="fa fa-minus"></span>
                                        </button>
                                    </td>
                                 </tr>

                            <?php  }  ?>

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
            </div>
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
                     <!-- ATM -->
                    <div class="block-content">
                        <form id="add_stocks_form">                           
                            <div class="form-group row">                                
                                <div class="col-8" >     
                                    <div  class="form-group form-control-md">
                                    <label class="" for="item_img">Item</label>                             
                                        <select onchange = "select_item()"  class = "form-control form-control-lg" name = "mySelect" id= "mySelect">                                              
                                                
                                                <option id="default_option" value="0">Select Item</option>
                                            <?php 
                                                // $items variable is the same variable used in the query above
                                             foreach ($items as $item){?>
                                                <option id =  "selected_item_id" value="<?php echo 
                                                                                    $item['min_qty']." ".
                                                                                    $item['current_stock_qty']." ".
                                                                                    $item['inventory_id']." ".
                                                                                    $item['img_src'];?>" >
                                                    <?php echo $item['item'];?>                                                        
                                                </option>          
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group form-control-md"> 
                                        <label class=""  for="item_img">Current Stock</label>
                                        <input readonly class="form-control form-control-lg"  id="show_current_qty" name="show_current_qty">
                                        <input hidden type="text"  id="inventory_id3" name="inventory_id3">
                                    </div>
                                    <div class="form-group form-control-md">
                                        <label  for="item_img">New Stock</label>
                                        <input required onkeypress="return event.charCode >= 48" min="1" type="number" class="form-control form-control-lg"  id="new_stock_qty" name="new_stock_qty">
                                    </div>
                                    
                                                                  </div>

                                <div class="col-4 result">
                                     <label class="" for="item_image2">File Image</label>                             
                                    <img id ="item_image2" class="img-thumbnail  rounded float-left"" src="" >                                    
                                </div>
                            </div>                          
                           
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button  type="submit" id = "add_button" required class="btn btn-sm btn-hero btn-alt-primary min-width-175">
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
    <!-- END Add New Stocks Modal -->


      <!-- Pull Stocks From Inventory Modal -->
      <div class="modal fade" id="pull_stock_modal" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Pull Stocks Form</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                     <!-- RN-->
                    <div class="block-content">
                        <form id="pull_stocks_form">                           
                            <div class="form-group row">                              
                                <div class="col-8">                     
                                    <label  for="item_img">Item</label>
                                    <select onchange = "select_item_pull_out()"  class = "form-control form-control-lg" id= "mySelect2" name = "mySelect2">
                                          <option id="default" >Select Item</option>                                        
                                                 <?php 
                                            // $items variable is the same variable used in the query above
                                                  foreach ($items as $item){?>
                                            <option id =  "selected_item_pull_id" value="<?php echo $item['current_stock_qty']." ".$item['inventory_id']." ".$item['img_src'];?>" name = "selected_item_pull_id"><?=$item['inventory_id']."-".$item['item']?></option>          
                                              <?php } ?>
                                    </select>
                                    <div class="form-group row">
                                    <label class="col-12"  for="show_current_qty2">Current Stock</label>                                   
                                    <div class="col-12 result">
                                     
                                        <input   readonly type="number" class="form-control form-control-lg"  id="show_current_qty2" name="show_current_qty2">
                                    </div>

                                    <label class="col-12" for="item_img">Pull Out Stock Qty</label>            
                                    <div class="col-12">
                                        <input required onkeypress="return event.charCode >= 48" min="1" type="number" class="form-control form-control-lg"  id="pull_out_stock_qty" name="pull_out_stock_qty">
                                    </div>

                                    <div class="col-12"> 
                                        <label class="col-12" >Requested by</label>                                
                                        <input   hidden type="text" name = "requestor_choice" id="requestor_choice" value="">                                
                                        <div class="dropdown "  id="requestor_id">
                                              <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Select : 
                                              </a>
                                            <div class="dropdown-menu w-200" aria-labelledby="dropdownMenuLink">
                                                <a class="dropdown-item" href="#" onclick="select_requestor(1)">Employee</a>
                                                <a class="dropdown-item" href="#" onclick="select_requestor(2)">Location</a>
                                            </div> 
                                        </div>
                                    </div>


                                    <div class="col-12 result" hidden  id="employee_list">        
                                        <label class="col-12" for="employee_request">Employee List</label>   
                                        <div>                                       
                                         
                                             <select  class="custom-select form" type ="text"  name = "employee_request" id= "employee_request"required>
                                                <option value="">None</option>
                                                
                                                 <?php foreach ($employee_details as $employee_detail) { ?>
                                                    <option value="<?=$employee_detail['employee_id']?>"><?=$employee_detail['firstname']." ".$employee_detail['middlename']." ".$employee_detail['lastname'];?>
                                                    </option>        
                                                <?php } ?>
                                              </select>
                                        </div>
                                    </div>  

                                    <div class="col-12 result" hidden  id="location_input">
                                        <label class="col-12" for="location_request" >Location</label> 
                                        <div >                                             
                                            <input type="text" class="form-control form-control-lg" name = "location_request" id="location_request" >
                                        </div>
                                    </div>
                                    </div>
                                        </div>
                                        <div class="col-4">  
                                          <label class="col-12" for="item_image" >File Image</label>                        
                                            <img id ="item_image" class="img-thumbnail  rounded float-left"" src="" >                 
                                        </div>
                                    </div>                             
                                    <div class="modal-footer ">                                    
                                               
                                           <!--  <button  type="submit" id = "pull_out_button" hidden class="btn btn-sm btn-hero btn-alt-success min-width-175 ">
                                                <i class="fa fa-plus mr-5"></i> Pull Out
                                            </button> -->
                                        <div class="col-12 text-center">
                                         <button type="submit" id ="pull_out_button"   class="btn btn-sm btn-hero btn-alt-success min-width-175">
                                             <i class="fa fa-plus mr-5"></i> Pull Out
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
    <!-- END Pull Stocks From Inventory Modal -->

    <!-- View inventory Details Modal -->
    <div class="modal fade" id="view_inventory_modal" tabindex="-1" role="dialog" aria-labelledby="add_inventory_modal" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0" id="print_div">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title"><span id="span_inventory_name">inventory Details</span></h3>
                        <div class="block-options">
                            <?php
                            //                    ADD ACCESS TRAINING ENABLE DISABLE BUTTON
                            $print_access = $conn->query("SELECT `status` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 4");
                            if ($print_access->rowCount() > 0) {
                                $print_access_fetch = $print_access->fetch();
                                if ($print_access_fetch['status'] == 0){
                                ?>
                                <button type="button" class="btn-block-option" onclick="printDiv('print_div')">
                                    <i class="si si-printer"></i> Print
                                </button>
                                <?php
                                }
                            }
                            ?>
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <div class="row items-push">
                            <div class="col-xl-5 mt-0 mb-0" id="profile_info"></div>
                            <div class="col-xl-7 px-0 mb-0" id="basic_info"></div>
                        </div>
                        <!-- END User Info -->
                        <div class="content">
                            <!-- Cart -->
                            <h2 class="content-heading pt-0">Trainings</h2>
                            <div class="block block-rounded">
                                <!-- Trainings Table -->
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%;">#</th>
                                            <th style="width: 20%;">Title</th>
                                            <th class="text-center">Conducted by</th>
                                            <th class="text-center">Type</th>
                                            <th class="text-center" style="width: 20%;">Date Created</th>
                                            <th class="text-center" style="width: 15%;">Status</th>
                                            <th class="text-center" style="width: 10%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="trainings_info">
                                    </tbody>
                                </table>
                                <!-- END Trainings Table -->
                            </div>
                            <!-- END Cart -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END View inventory Details Modal -->

    <!-- SPECIFIC MODAL -->
    
    <!-- Pull Stockss From Inventory Modal SPECIFIC -->
      <div class="modal fade" id="pull_stocks_modal_specific" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Outbound Item</h3>                        
                    </div>
                     <!-- RN-->
                    <div class="block-content">
                        <!-- form ------------------------------------------------------------>
                        <form id="pull_stocks_form_specific">                           
                            <div class="form-group row">                              
                                <div class="col-8">                 
                                                                      
                                        <table class="table table-sm table-bordered table-striped">
                                            <thead>
                                            <th style="width: 70%;">Item name</th>
                                            <th>Remaining min_qty</th>
                                            </thead>
                                            <tr>
                                                <td><span id="span_item_name" n></span></td>
                                                <td><span id="span_current_qty"></span></td>
                                            </tr>
                                        </table>
                                        <hr>
                                        <input type="hidden" id="pull_minimum_qty" name="pull_minimum_qty">
                                        <input type="hidden" id="pull_item_id" name="pull_item_id">
                                        <input type="hidden" id="show_current_qty3" name="show_current_qty3">
                                            <div class="form-group row">
                                                <label class="col-12" for="add_min_qty">Quantity</label>
                                                <div class="col-12">
                                                    <input type="number" class="form-control form-control-lg" id="pull_out_stock_qty2" name="pull_out_stock_qty2" placeholder="Enter quantity to be added" required>
                                                </div>
                                            </div>   
                                                                        <div class="col-12"> 
                                        <label class="col-12" >Requested by</label>
                                        <input   hidden type="text" name = "requestor_choice3" id="requestor_choice3" value="">                                
                                        <div class="dropdown "  id="requestor_id">
                                              <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Select : 
                                              </a>
                                            <div class="dropdown-menu w-200" aria-labelledby="dropdownMenuLink">
                                                <a class="dropdown-item" href="#" onclick="select_requestor3(1)">Employee</a>
                                                <a class="dropdown-item" href="#" onclick="select_requestor3(2)">Location</a>
                                            </div> 
                                        </div>
                                    </div>      
                                     <div class="col-12 result" hidden  id="employee_list3">        
                                        <label class="col-12" for="employee_request">Employee List</label>   
                                        <div>                                       
                                         
                                             <select  class="custom-select form" type ="text"  name = "employee_request3" id= "employee_request3"required>
                                                <option value="">None</option>                                          
                                                 <?php foreach ($employee_details as $employee_detail) { ?>
                                                    <option value="<?=$employee_detail['employee_id']?>"><?=$employee_detail['firstname']." ".$employee_detail['middlename']." ".$employee_detail['lastname'];?>
                                                    </option>        
                                                <?php } ?>
                                              </select>
                                        </div>
                                    </div>                      

                                    <div class="col-12 result" hidden  id="location_input3">
                                        <label class="col-12" for="location_request3" >Location</label> 
                                        <div >                                             
                                            <input type="text" class="form-control form-control-lg" name = "location_request3" id="location_request3" >
                                        </div>
                                    </div>
                                </div>              
                                <div class="col-4">  
                                    <img id ="item_image4" class="img-thumbnail  rounded float-left"" src="" >                 
                                </div>                                    
                            </div>                                        
                                </div>                            
                                    <div class="modal-footer "> 
                                        <div class="col-12 text-center">
                                         <button type="submit" id ="pull_out_button"   class="btn btn-sm btn-hero btn-alt-success min-width-175">
                                             <i class="fa fa-plus mr-5"></i> Pull Stocks
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
    <!-- END Add Stocks From Inventory Modal -->
         <div class="modal fade" id="add_stocks_modal_specific" tabindex="-1" role="dialog" aria-labelledby="add_employee_modal" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title"> Add Stocks Form</h3>
                      
                    </div>
                     <!-- RN-->
                    <div class="block-content">
                        <!-- form ------------------------------------------------------------>
                        <form id="add_stocks_form_specific">                           
                            <div class="form-group row">                              
                                <div class="col-8">                     
                                   
                                    <div class="block-content">
                                          <div class="block block-themed block-transparent mb-0">
                                        <h3 class="block-title">Inbound Item</h3>
                                        
                                    </div>
                                        <table class="table table-sm table-bordered table-striped">
                                            <thead>
                                            <th style="width: 70%;">Item name</th>
                                            <th>Current Qty</th>
                                            </thead>
                                            <tr>
                                                <td><span id="span_item_name_add"></span></td>
                                                <td><span id="span_remaining_min_qty_add"></span></td>
                                            </tr>
                                        </table>
                                        <hr>
                                        
                                            <input type="hidden" id="add_item_id" name="add_item_id">
                                            <input type="hidden" id="add_minimum_qty" name="add_minimum_qty">
                                            <div class="form-group row">
                                                <label class="col-12" for="add_min_qty">Quantity</label>
                                                <div class="col-12">
                                                    <input type="number" class="form-control form-control-lg" id="add_new_stock_qty" name="add_new_stock_qty" placeholder="Enter quantity to be added" required>
                                                </div>
                                            </div>   
                                        </div>              


                                </div>
                                <div class="col-4">  
                                        <label class="col-12" for="item_image" >File Image</label>
                                        <img id ="item_image3" class="img-thumbnail  rounded float-left"" src="" >                 
                                </div>
                            </div>                                        
                                </div>                            
                                    <div class="modal-footer "> 
                                        <div class="col-12 text-center">
                                         <button type="submit" id ="pull_out_button"   class="btn btn-sm btn-hero btn-alt-success min-width-175">
                                             <i class="fa fa-plus mr-5"></i> Add Stocks
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
    
    <!-- END Pull Stocks From Inventory Modal -->

<!--EDIT INVENTORY BOUND QTY MODAL-->
    <div class="modal fade" id="edit_stock_in_modal" tabindex="-1" role="dialog" aria-labelledby="modal_view_details" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Edit Quantity</h3>
                        <div class="block-options">
                            <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
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
                                    <input type="text" class="form-control form-control-lg" id="update_stock_in_type" name="update_stock_in_type" disabled>
                                </div>
                                <div class="col-12 mt-10">
                                    <label for="be-contact-name">Edit Qty</label>
                                    <input type="text" class="form-control form-control-lg" id="update_stock_in_qty" name="update_stock_in_qty" value="" required>
                                </div>
                                <div class="col-12 mt-10">
                                    <label for="be-contact-name">Edit Previous Qty</label>
                                    <input type="text" class="form-control form-control-lg" id="update_stock_in_prev_qty" name="update_stock_in_prev_qty" value="" required>
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

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>


       <!-- Page JS Plugins -->
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page JS Code -->
    <script src="assets/js/pages/be_tables_datatables.min.js"></script>

    <script>
        $(document).ready(function () {
            $("th").attr("style","text-transform: capitalize");
            // Datatable for item table
            $('#tbl_items').DataTable();
            $('#inventory_sidebar').addClass('open');

            //PULL AND ADD NEW STOCKS END
            $("#add_stocks_form_specific").submit(function (event) {
                event.preventDefault();

                inventory_id = document.getElementById("add_item_id").value;
                min_qty = parseInt(document.getElementById("add_minimum_qty").value);
                current_stock_qty = parseInt(document.getElementById("span_remaining_min_qty_add").innerText);

                new_stock_qty =    parseInt(document.getElementById("add_new_stock_qty").value);
                minimum_stock_required = min_qty - current_stock_qty;

                if (confirm("Are you sure?")){
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
                            console.log("Error adding new stocks function");
                        }
                    });
                }
            });

            //PULL AND ADD NEW STOCKS END
            $("#add_stocks_form").submit(function (event) {
                event.preventDefault();

                //The new added stock should not be below the current minimum required quantity

                var a = document.getElementById("mySelect").value;
                var b = a.split(" ");
                inventory_id = b[2];
                min_qty = parseInt(b[0]);
                current_stock_qty = parseInt(b[1]);

                new_stock_qty =    parseInt(document.getElementById("new_stock_qty").value);
                minimum_stock_required = min_qty - current_stock_qty;

                if (confirm("Are you sure?")){
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
                            console.log("Error adding new stocks function");
                        }
                    });
                }
            });

            $("#pull_stocks_form_specific").submit(function (event) {
                event.preventDefault();
                inventory_id = document.getElementById("pull_item_id").value;
                current_stock_qty = parseInt(document.getElementById("span_current_qty").innerText);
                pull_out_stock_qty =    parseInt(document.getElementById("pull_out_stock_qty2").value)

                if(current_stock_qty < pull_out_stock_qty){

                    alert('The quantity entered exceeded the current stock quantity');
                }else{
                    if (confirm("Are you sure?")){
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
                                console.log("Error Pulling Stocks");
                            }
                        });
                    }
                }
            });

            // PULL STOCKS FROM INVENTORY

            $("#pull_stocks_form").submit(function (event) {
                event.preventDefault();

                var a =  document.getElementById("mySelect2").value;
                var b = a.split(" ");
                inventory_id = b[1];
                current_stock_qty = b[0];
                pull_out_stock_qty = parseInt($('#pull_out_stock_qty').val());

                // alert(pull_out_stock_qty +"asdasdas" +current_stock_qty);


                if(current_stock_qty < pull_out_stock_qty){
                    alert('The quantity entered exceeded the current stock quantity');
                }else{
                    if (confirm("Are you sure?")){
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
                                console.log("Error Pulling Stocks");
                            }
                        });
                    }
                }
            });

            // FUNCTION FOR SUBMITTING UPDATE
            $("#update_form").submit(function (event) {

                event.preventDefault();
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

                        console.log("Error adding inventory function");
                    }
                });
            });


            $("#update_stock_in").submit(function (event) {
                event.preventDefault();
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
                        console.log("Error update function");
                    }
                });

            });
        });

           // ADD NEW STOCKS

        function add_new_stocks(){
              var formdata = new FormData(document.getElementById('add_stocks_form'));

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
                    console.log("Error adding new stocks function");
                }
            });
        }

        //PULL AND ADD NEW STOCKS

        function fetch_stocks(id,type,current_stock_qty,item_name, min_qty, img_src){
            if (type === 'add'){
                $('#add_stocks_modal_specific').modal("show");
                document.getElementById("span_item_name_add").innerText = item_name;
                document.getElementById("span_remaining_min_qty_add").innerText = current_stock_qty;
                document.getElementById("add_item_id").value = id;
                document.getElementById("add_minimum_qty").value = min_qty;
                document.getElementById("item_image3").src = "assets/media/photos/inventory/"+img_src.toString();

            }else{
                $('#pull_stocks_modal_specific').modal("show");
                document.getElementById("span_item_name").innerText = item_name;
                document.getElementById("span_current_qty").innerText = current_stock_qty;
                document.getElementById("show_current_qty3").value = current_stock_qty;
                document.getElementById("pull_item_id").value = id;
                document.getElementById("pull_minimum_qty").value = min_qty;
                document.getElementById("item_image4").src = "assets/media/photos/inventory/"+img_src.toString();
            }            
        }
        function select_item(){

            var x = document.getElementById("mySelect").value;
            var a = x.split(" ");
            // alert(a);
            document.getElementById("show_current_qty").value = a[1];
            document.getElementById("inventory_id3").value = a[2];
            document.getElementById("default_option").hidden = true;
            document.getElementById("item_image2").src = "assets/media/photos/inventory/"+a[3].toString();
            
            
              
        }
        function select_item_pull_out(){

            var x = document.getElementById("mySelect2").value;
            var a = x.split(" ");
            
            // alert(a);
            document.getElementById('default_option').disabled = true;
            document.getElementById("show_current_qty2").value = a[0];
            document.getElementById("item_image").src = "assets/media/photos/inventory/"+a[2].toString();
            document.getElementById("default").hidden = true;
            document.getElementById("pull_out_button").hidden = false;
        }
        function view_details(id){
            $('#view_inventory_modal').modal('show');
            inventory_profile_info(id);
            inventory_basic_info(id);
            inventory_trainings_info(id);
        }     


        function select_requestor(choice){
            if(choice ==1){ //1 =  Employee
                document.getElementById("employee_list").hidden = false;
                document.getElementById("employee_request").required = true;
                document.getElementById("location_request").required = false;
                document.getElementById("location_input").hidden = true;   
                document.getElementById("requestor_choice").value = choice;       
            } else {
                document.getElementById("employee_list").hidden = true;
                document.getElementById("location_input").hidden = false; 
                document.getElementById("requestor_choice").value = choice;   
                document.getElementById("employee_request").required = false;
                document.getElementById("location_request").required = true;          
            }
        }


        function select_requestor2(choice){
              
                var item = document.getElementById("mySelect").value
            if (item != "0" ) {
                if(choice ==1){ //1 =  Employee
                    document.getElementById("employee_list2").hidden = false;
                    document.getElementById("employee_request2").required = true;
                    document.getElementById("location_request2").required = false;
                    document.getElementById("location_input2").hidden = true;   
                    document.getElementById("requestor_choice2").value = choice;     

                } else {
                    document.getElementById("employee_list2").hidden = true;
                    document.getElementById("location_input2").hidden = false; 
                    document.getElementById("requestor_choice2").value = choice;   
                    document.getElementById("employee_request2").required = false;
                    document.getElementById("location_request2").required = true;             
                } 
                document.getElementById("add_button").hidden = false;    
            } else {
                
                 alert("No Items Selected");
            } 
        }
        function select_requestor3(choice){
  
            if(choice ==1){ //1 =  Employee
                document.getElementById("employee_list3").hidden = false;
                document.getElementById("employee_request3").required = true;
                document.getElementById("location_request3").required = false;
                document.getElementById("location_input3").hidden = true;   
                document.getElementById("requestor_choice3").value = choice;         
            } else {
                document.getElementById("employee_list3").hidden = true;
                document.getElementById("location_input3").hidden = false; 
                document.getElementById("requestor_choice3").value = choice;   
                document.getElementById("employee_request3").required = false;
                document.getElementById("location_request3").required = true;            
            }
        }
        function select_requestor4(choice){
  
            if(choice ==1){ //1 =  Employee
                document.getElementById("employee_list4").hidden = false;
                document.getElementById("employee_request4").required = true;
                document.getElementById("location_request4").required = false;
                document.getElementById("location_input4").hidden = true;   
                document.getElementById("requestor_choice4").value = choice;         
            } else {
                document.getElementById("employee_list4").hidden = true;
                document.getElementById("location_input4").hidden = false; 
                document.getElementById("requestor_choice4").value = choice;   
                document.getElementById("employee_request4").required = false;
                document.getElementById("location_request4").required = true;            
            }
        }


        function inventory_basic_info(id){
            $.ajax({
                type: "POST",
                url: "ajax/inventory_ajax.php",
                data: {
                    inventory_id: id,
                    inventory_basic_info: 1,
                },
                success: function(data){
                    $('#basic_info').html(data);
                    // location.reload()
                }
            });
        }

        // END VIEW inventory DETAILS AJAX FUNCTIONS

        function edit_stock_in(inv_bound_id,qty,prev_qty,type){
            $('#update_stock_in_id').val(inv_bound_id);
            $('#update_stock_in_qty').val(qty);
            $('#update_stock_in_type').val(type);
            $('#update_stock_in_prev_qty').val(prev_qty);
            $('#edit_stock_in_modal').modal("show");
            // if (confirm("Are you sure?")){
            //     $.ajax({
            //         type: "POST",
            //         url: "ajax/inventory_ajax.php",
            //         data: {
            //             inv_bound_id: inv_bound_id,
            //             edit_stock_in: 1,
            //         },
            //         success: function(data){
            //             alert(data);
            //         }
            //     });
            // }
        }
       
    
    </script>
<?php
include 'includes/footer.php';