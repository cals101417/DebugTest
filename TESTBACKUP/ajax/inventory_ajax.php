<?php
require_once '../session.php';
// UPDATE inventory
if (isset($_POST['update_inventory'])) {

    $inventory_id = $_POST['update_inventory'];
    $item = $_POST['item'];
    $description = $_POST['description'];
    $min_qty = $_POST['min_qty'];
    $currency = $_POST['currency'];

    // Set values for the image directory

    $inventory_img_dir = '';
//
    try {
        $inventory_qry = $conn->query("SELECT img_src FROM tbl_inventory WHERE tbl_inventory.inventory_id = $inventory_id");
        $inventory = $inventory_qry->fetch();
        $inventory_img_src = $inventory['img_src'];
        $inventory_img_dir = '../assets/media/photos/inventory/' . $inventory_img_src;
    } catch (Exception $e) {
        echo $e;
    }

    if (!empty($_FILES["inventory_img"]["name"])) {
        // Allow certain file formats
        // File upload path
        $targetDir = "../assets/media/photos/inventory/";
        $fileName = basename($_FILES["inventory_img"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES["inventory_img"]["tmp_name"], $targetFilePath)) {
                // Update image file name into database
                unlink($inventory_img_dir);
                try {
                    $update = $conn->prepare("UPDATE `tbl_inventory` 
                                        SET `item` = ?,
                                        `img_src` = ?,
                                        `description` = ?,
                                        `min_qty` = ?,`item_currency` = ?
                                        WHERE
                                            `inventory_id` = ?");
                    $update->execute([$item, $fileName, $description, $min_qty,$currency, $inventory_id]);

                    echo 'inventory successfully updated';
                } catch (Exception $e) {
                    echo 'Sorry, Something wen\'t wrong' . $e;
                }

            } else {
                $statusMsg = "Sorry, there was an error uploading your file.";
            }
        } else {
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
        }
    } else {
        try {
            $update = $conn->prepare("UPDATE `tbl_inventory` 
            SET `item` = ?,        
            `description` = ?,
            `min_qty` = ?,`item_currency` =?
            WHERE
                `inventory_id` = ?");
            $update->execute([$item, $description, $min_qty,$currency, $inventory_id]);

            echo 'inventory successfully updated';
        } catch (Exception $e) {
            echo 'Sorry, Something went wrong' . $e;
        }
    }
}

// UPDATE INVENTORY END


if (isset($_POST['add_new_item'])) {

    $name = $_POST['item_name'];
    $item_unit = $_POST['item_unit'];
    $item_price = $_POST['item_price'];
    $desc = $_POST['desc'];
    $min_qty = $_POST['min_qty'];
    $currency = $_POST['currency'];
    $date = date('Y-m-d H:i:s');
    $statusMsg = "";
    if (!empty($_FILES["item_img"]["name"])) {
        // Allow certain file formats
        // File upload path
        $targetDir = "../assets/media/photos/inventory/";
        $fileName = basename($_FILES["item_img"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES["item_img"]["tmp_name"], $targetFilePath)) {
                // Insert image file name into database
                try {
                    $insert = $conn->prepare("INSERT INTO `tbl_inventory`(`item`, `description`, `min_qty`,`img_src`, `date_created`, `date_updated`, `user_id`, `is_removed`,`current_stock_qty`,`try`, `item_price`,`item_currency`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                    $insert->execute([$name, $desc, $min_qty, $fileName, $date, $date, $user_id, 0, 0, $item_unit, $item_price,$currency]);

                    echo 'Item successfuly added';
                } catch (Exception $e) {
                    echo 'Something went wrong ' . $e;
                }

            } else {
                $statusMsg = "Sorry, there was an error uploading your file.";
            }
        } else {
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
        }
    } else {
        try {
            $insert = $conn->prepare("INSERT INTO `tbl_inventory`(`item`, `description`, `min_qty`,`img_src`, `date_created`, `date_updated`, `user_id`, `is_removed`,`currency`) VALUES (?,?,?,?,?,?,?,?,?)");
            $insert->execute([$name, $desc, $min_qty, '', $date, $date, $user_id, 0,$currency]);

            echo 'Item successfuly added';
        } catch (Exception $e) {
            echo 'Something went wrong ' . $e;
        }
    }
//    try {
//        $insert = $conn->prepare("INSERT INTO `tbl_inventory`(`item`, `description`, `min_qty`, `date_created`, `date_updated`, `user_id`, `is_removed`) VALUES (?,?,?,?,?,?,?)");
//        $insert->execute([$name,$desc,$min_qty,$date,$date,$user_id,0]);
//
//        echo 'Item successfuly added';
//    }catch (Exception $e){
//        echo 'Something went wrong '.$e;
//    }
}

if (isset($_POST['fetch_stocks'])) {
    $inventory_id = $_POST['inventory_id'];
    $return_arr = array();

    try {

        $fetch_item_qry = $conn->query("SELECT `inventory_id`, `item`, `min_qty`, `date_created`, `date_updated`, `user_id`, `is_removed` FROM `tbl_inventory` WHERE `inventory_id` = $inventory_id SORT");
        $item = $fetch_item_qry->fetch();
        $item_name = ucwords($item['item']);
        $min_qty = $item['min_qty'];

        $return_arr[] = ["inventory_id" => $inventory_id, "item_name" => $item_name, "min_qty" => $min_qty];

        echo json_encode($return_arr);
    } catch (Exception $e) {
        echo $e;
    }

}

function check_requestor($requestor)
{
    $type = '';
    $column = '';

    if ($requestor == 1) {
        $type = "requested_by_employee";
    } else if ($requestor == 2) {
        $type = "requested_by_location";
    }

    return $type;
}

// RECORD TO DATABASE THE QUANTITY OF  STOCKS REMOVED FROM TBL INVENTORY THEN RECORD IT TO TBL_INVENTORY_BOUND
if (isset($_POST['pull_item_id'])) {


    $inventory_id = $_POST['pull_item_id'];
    // echo $inventory_id;
    $pull_out_stock_qty = $_POST['pull_out_stock_qty2'];

    $current_stock_qty = $_POST['show_current_qty3'];
    $new_stock_qty = intval($current_stock_qty) - intval($pull_out_stock_qty);
    $rc = $_POST['requestor_choice3'];
    $type = 'outbound';
    $date = date('Y-m-d H:i:s');
    $col = check_requestor($rc); // selects which column where the data will be saved
    $emp_req = $_POST['employee_request3'];
    $loc_req = $_POST['location_request3'];
    $choice = '';

    if ($rc == 1) {   // selects which input will be saved
        $choice = $emp_req;
    } else {
        $choice = $loc_req;
    }


    try {
        $conn->beginTransaction();
//        INSERT NEW BOUND DATA
        $bound_qry = $conn->prepare("INSERT INTO `tbl_inventory_bound`(`inventory_id`, `qty`,`previous_qty`, `type`, `date`, `user_id`, $col) VALUES (?,?,?,?,?,?,?)");
        $bound_qry->execute([$inventory_id, $pull_out_stock_qty, $current_stock_qty, $type, $date, $user_id, $choice]);
        //        Update inventory quantity
        $update_inventory_qry = $conn->prepare("UPDATE `tbl_inventory` SET `current_stock_qty` = ? WHERE `inventory_id` = ?");
        $update_inventory_qry->execute([$new_stock_qty, $inventory_id]);

        $conn->commit();
        // echo $requestor_choice." CHOICE";
        echo 'Pulled out ' . $pull_out_stock_qty . ' from the Inventory.' . $new_stock_qty . ' remaining';
    } catch (Exception $e) {
        $conn->rollBack();
        echo $e;
    }
}


// RECORD TO DATABASE THE QUANTITY OF  STOCKS REMOVED FROM TBL INVENTORY THEN RECORD IT TO TBL_INVENTORY_BOUND
if (isset($_POST['pull_out_stock_qty'])) {

    $a = explode(" ", $_POST['mySelect2']);
    $inventory_id = $a[1];
    // echo $inventory_id;
    $pull_out_stock_qty = $_POST['pull_out_stock_qty'];

    $current_stock_qty = $_POST['show_current_qty2'];
    $new_stock_qty = intval($current_stock_qty) - intval($pull_out_stock_qty);
    $rc = $_POST['requestor_choice'];
    $type = 'outbound';
    $date = date('Y-m-d H:i:s');
    $col = check_requestor($rc); // selects which column where the data will be saved
    $emp_req = $_POST['employee_request'];
    $loc_req = $_POST['location_request'];
    $choice = '';

    if ($rc == 1) {   // selects which input will be saved
        $choice = $emp_req;
    } else {
        $choice = $loc_req;
    }


    try {
        $conn->beginTransaction();
//        INSERT NEW BOUND DATA
        $bound_qry = $conn->prepare("INSERT INTO `tbl_inventory_bound`(`inventory_id`, `qty`,`previous_qty`, `type`, `date`, `user_id`, $col) VALUES (?,?,?,?,?,?,?)");
        $bound_qry->execute([$inventory_id, $pull_out_stock_qty, $current_stock_qty, $type, $date, $user_id, $choice]);

//        Update inventory quantity
        $update_inventory_qry = $conn->prepare("UPDATE `tbl_inventory` SET `current_stock_qty` = ? WHERE `inventory_id` = ?");
        $update_inventory_qry->execute([$new_stock_qty, $inventory_id]);

        $conn->commit();
        // echo $requestor_choice." CHOICE";
        echo 'Pulled out ' . $pull_out_stock_qty . ' from the Inventory.' . $new_stock_qty . ' remaining';
    } catch (Exception $e) {
        $conn->rollBack();
        echo $e;
    }
}

// FUNCTION FOR REMOVING ITEM
if (isset($_POST['remove_item'])) {

    $inventory_id = $_POST['inventory_id'];

    try {
        $remove_qry = $conn->prepare("UPDATE `tbl_inventory` SET `is_removed`= ?,`is_removed_user_id`= ?  WHERE `inventory_id`= ?");
        $remove_qry->execute([1, $user_id, $inventory_id]);

        echo 'success';
    } catch (Exception $e) {
        echo 'Something went wrong';
    }
}


// ADD NEW STOCKS 
if (isset($_POST['add_new_stock_qty'])) {

    $newly_added_stock = $_POST['add_new_stock_qty'];
    $inventory_id = $_POST['add_item_id'];
    $type = 'inbound';
    $date = date('Y-m-d H:i:s');


    try {
        $conn->beginTransaction();

        //    fetch remaining quantity
        $fetch_item_qry = $conn->prepare("SELECT tbl_inventory.current_stock_qty FROM tbl_inventory WHERE tbl_inventory.inventory_id = ?");
        $fetch_item_qry->execute([$inventory_id]);
        $item = $fetch_item_qry->fetch();
        $current_stock_qty = $item['current_stock_qty'];

        $new_stock_qty = $current_stock_qty + $newly_added_stock;

        //    Update stock quantity
        $update_new_qty_qry = $conn->prepare("UPDATE `tbl_inventory` SET `current_stock_qty`= ? WHERE `inventory_id`=  ?");
        $update_new_qty_qry->execute([$new_stock_qty, $inventory_id]);

//        Record adding new stock
        $insert_bound = $conn->prepare("INSERT INTO `tbl_inventory_bound`(`inventory_id`, `qty`, `previous_qty`, `type`, `date`, `user_id`, `requested_by_employee`) VALUES (?,?,?,?,?,?,?)");
        $insert_bound->execute([$inventory_id, $newly_added_stock, $current_stock_qty, $type, $date, $user_id, $user_id]);

        $conn->commit();
        echo ' Successfully added ' . $newly_added_stock . ' new stock/s. ';
        echo 'The total quantity of this item is now : ' . $new_stock_qty;
    } catch (Exception $e) {

        $conn->rollBack();
        echo 'Something went wrong ' . $e;
    }

}
if (isset($_POST['new_stock_qty'])) {


    $newly_added_stock = $_POST['new_stock_qty'];
    $inventory_id = $_POST['inventory_id3'];
    $type = 'inbound';
    $date = date('Y-m-d H:i:s');

    try {
        $conn->beginTransaction();

        //    fetch remaining quantity
        $fetch_item_qry = $conn->prepare("SELECT tbl_inventory.current_stock_qty FROM tbl_inventory WHERE tbl_inventory.inventory_id = ?");
        $fetch_item_qry->execute([$inventory_id]);
        $item = $fetch_item_qry->fetch();
        $current_stock_qty = $item['current_stock_qty'];

        $new_stock_qty = $current_stock_qty + $newly_added_stock;

        //    Update stock quantity
        $update_new_qty_qry = $conn->prepare("UPDATE `tbl_inventory` SET `current_stock_qty`= ? WHERE `inventory_id`=  ?");
        $update_new_qty_qry->execute([$new_stock_qty, $inventory_id]);

//        Record adding new stock
        $insert_bound = $conn->prepare("INSERT INTO `tbl_inventory_bound`(`inventory_id`, `qty`, `previous_qty`, `type`, `date`, `user_id`, `requested_by_employee`) VALUES (?,?,?,?,?,?,?)");
        $insert_bound->execute([$inventory_id, $newly_added_stock, $current_stock_qty, $type, $date, $user_id, $user_id]);

        $conn->commit();

        echo ' Successfully added ' . $newly_added_stock . ' new stock/s. ';
        echo 'The total quantity of this item is now : ' . $new_stock_qty;
    } catch (Exception $e) {

        $conn->rollBack();
        echo 'Something went wrong ' . $e;
    }
}

if (isset($_POST['update_stock_in_id'])) {
    $inv_bound_id = $_POST['update_stock_in_id'];
    $qty = $_POST['update_stock_in_qty'];
    $prev_qty = $_POST['update_stock_in_prev_qty'];

    try {

        $update = $conn->prepare("UPDATE `tbl_inventory_bound` SET `qty`= ?,`previous_qty`= ? WHERE `inventory_bound_id`= ?");
        $update->execute([$qty, $prev_qty, $inv_bound_id]);

        echo 'success';
    } catch (Exception $e) {
        echo 'failed';
    }
}
if (isset($_POST['generate_report'])) {
    $month = sprintf("%02d",$_POST['month']);
    $year = $_POST['year'];

    try {
        $report_qry = $conn->query("SELECT
                                            tbl_inventory.inventory_id,
                                            tbl_inventory.item,
                                            Sum(tbl_inventory_bound.qty),
                                            tbl_inventory.min_qty,
                                            tbl_inventory.item_price,
                                            item_currency
                                            FROM
                                            tbl_inventory_bound
                                            INNER JOIN tbl_inventory ON tbl_inventory.inventory_id = tbl_inventory_bound.inventory_id
                                            WHERE
                                            tbl_inventory.is_removed = 0 AND YEAR (tbl_inventory_bound.date) = '$year' AND MONTH (tbl_inventory_bound.date) = '$month'
                                            GROUP BY
                                            tbl_inventory.inventory_id");
        $count = $report_qry->rowCount();
        $item_counter = 1;

        if ($count > 0){
            $reports = $report_qry->fetchAll();
            $total_maximum_order_price = 0;
            $total_minimum_order_price = 0;
            foreach ($reports as $report_data) {
                $inventory_id = $report_data['inventory_id'];
                $min_qty = $report_data['min_qty'];
                $item_price = $report_data['item_price'];

                $inbound = 0;
                $outbound = 0;
                $fetch_total_qty_received_qry = $conn->query("SELECT
                                                                        tbl_inventory.item,
                                                                        tbl_inventory_bound.qty,
                                                                        tbl_inventory_bound.type
                                                                        FROM
                                                                        tbl_inventory_bound
                                                                        INNER JOIN tbl_inventory ON tbl_inventory.inventory_id = tbl_inventory_bound.inventory_id
                                                                        WHERE
                                                                        tbl_inventory.is_removed = 0 AND YEAR (tbl_inventory_bound.date) = '$year' AND MONTH (tbl_inventory_bound.date) = '$month' AND
                                                                        tbl_inventory.inventory_id = $inventory_id");
                $inventory_record_fetch = $fetch_total_qty_received_qry->fetchAll();
                foreach ($inventory_record_fetch as $inventory_record){
                    if ($inventory_record['type'] == 'inbound'){
                        $inbound += $inventory_record['qty'];
                    }else{
                        $outbound += $inventory_record['qty'];
                    }
                }
                $total_remaining = $inbound-$outbound;
//                $temp_data =
                $temp_qty = $min_qty-$total_remaining;
                $order_qty = 0;
                $minimum_order = 0;
                $minimum_order_price = 0;
                $maximum_order = 0;
                $maximum_order_price = 0;

                $forecast_count = 0;

//                CHECK IF THIS ITEM REPORT HAS ORDER QTY
                $check_order_qty = $conn->query("SELECT `inventory_report_id`, `inventory_id`, `qty_order`, `is_added_to_forecast`, `month`, `year` FROM `tbl_inventory_report` 
                                                            WHERE `inventory_id` = $inventory_id AND `month` = $month AND `year` = $year");
                $count_order_qty = $check_order_qty->rowCount();

                if ($count_order_qty > 0){
                    $fetch_order_qty = $check_order_qty->fetch();
//                    CHECK IF ORDER QTY IS BELOW OR ABOVE required qty to reach minimum qty.
                    $order_qty = $fetch_order_qty['qty_order'];
                    if ($temp_qty < $order_qty){
                        $maximum_order = $order_qty;
                        $maximum_order_price = $item_price*$maximum_order;
                        $total_maximum_order_price +=$maximum_order_price;
                    }else{
                        $minimum_order = $order_qty;
                        $minimum_order_price = $item_price*$minimum_order;
                        $total_minimum_order_price +=$minimum_order_price;
                    }

//                    CHECK IF ITEM IS ALREADY ADDED IN THE FORECAST
                    $forecast_count = $fetch_order_qty['is_added_to_forecast'];
                }
                    $min_order_price = number_format($minimum_order*$item_price);
                    $max_order_price = number_format($maximum_order*$item_price);
                    $unit_price = number_format($report_data['item_price']);
                if ($total_remaining < $report_data['min_qty'] ){
                    $disable_qty = '';
                    (($max_order_price != 0)?$disable_qty ='disabled':'');
                    ?>
                <tr>
                    <td class="text-left"><?=$item_counter; ?></td>
                    <td class="text-left"><?=$report_data['item']?></td>
                    <td><?=$inbound?></td>
                    <td><?=$outbound?></td>
                    <td><?=(($total_remaining >= 0)?$total_remaining:0)?></td>
                    <td><?=$min_qty?></td>
                    <td class=" d-sm-table-cell text-right"><?=$unit_price?>  <?=(($unit_price != 0)?$report_data['item_currency']:'')?></td>
                    <td class=" d-sm-table-cell text-center"><?=$order_qty?></td>
                    <td class=" d-sm-table-cell text-right"><?=$min_order_price?> <?=(($min_order_price != 0)?$report_data['item_currency']:'')?> </td>
                    <td class=" d-sm-table-cell text-right"><?=$max_order_price?> <?=(($max_order_price != 0)?$report_data['item_currency']:'')?></td>
                    <td class=" d-sm-table-cell">
                        <?=(($total_remaining < $report_data['min_qty'])?(($total_remaining == 0)?'<span class="badge badge-danger">Out of Stock</span>':'<span class="badge badge-secondary">Reorder</span>'):'<span class="badge badge-success">In Stock</span>')?>
                    </td>
                    <td class=" d-sm-table-cell d-print-none">
                        <button class="btn btn-primary btn-sm" <?=$disable_qty?> onclick="add_qty_order(<?= $inventory_id ?>,<?= $order_qty ?>)"><i class="fa fa-plus text-success"></i> Qty Order </button>
                        <?php
                        if ($forecast_count == 1) {
                            ?>
                            <button class="btn btn-danger btn-sm" onclick="remove_forecast(<?= $inventory_id ?>)"><i
                                        class="fa fa-minus text-warning"></i> Forecast
                            </button>
                            <?php
                        }else{
                        ?>
                            <button class="btn btn-info btn-sm" onclick="add_to_forecast(<?= $inventory_id ?>)"><i
                                        class="fa fa-arrow-right text-success"></i> Forecast
                            </button>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
                    $item_counter= $item_counter+1;

                }
            }

            ?>
            <tr>
                <td colspan="8" class="text-right alert-primary">Total</td>
                <td class="text-right alert-primary"><?=number_format($total_minimum_order_price)?> <?=(($total_minimum_order_price != 0)?$report_data['item_currency']:'')?></td>
                <td class="text-right alert-primary"><?=number_format($total_maximum_order_price)?> <?=(($total_maximum_order_price != 0)?$report_data['item_currency']:'')?></td>
                <td colspan="2"class="text-right alert-primary"></td>
            </tr>
            <?php

        }else{
            ?>
            <tr>
                <td colspan="12" class="text-center">No Data Found..</td>
            </tr>
<?php
        }

    } catch (Exception $e) {
        echo $e;
    }
}

if (isset($_POST['add_qty_order_inventory_id'])){
    $inventory_id = $_POST['add_qty_order_inventory_id'];
    $qty_order = $_POST['add_qty_order'];
    $month = $_POST['add_qty_order_month'];
    $year = $_POST['add_qty_order_year'];

    try {

//        check if item has order qty already
        $check = $conn->query("SELECT `inventory_report_id`, `inventory_id`, `qty_order`, `is_added_to_forecast`, `month`, `year` FROM `tbl_inventory_report` 
                                        WHERE `inventory_id` = $inventory_id AND `month` = $month AND `year` = $year");
        $count = $check->rowCount();
        if ($count > 0){
            $report_data = $check->fetch();
            $report_id = $report_data['inventory_report_id'];
            $update = $conn->prepare("UPDATE `tbl_inventory_report` SET `qty_order`=? WHERE `inventory_report_id` = ?");
            $update->execute([$qty_order,$report_id]);
        }else{
            $insert_report = $conn->prepare("INSERT INTO `tbl_inventory_report`(`inventory_id`, `qty_order`,`month`,`year`) VALUES (?,?,?,?)");
            $insert_report->execute([$inventory_id,$qty_order,$month,$year]);
        }
        echo 'success';
    }catch (Exception $e){
        echo $e;
    }
}

if (isset($_POST['add_to_forecast'])){
    $inv_id = $_POST['inv_id'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    try {
//        check if item has order qty already
        $check = $conn->query("SELECT `inventory_report_id`, `inventory_id`, `qty_order`, `is_added_to_forecast`, `month`, `year` FROM `tbl_inventory_report` 
                                        WHERE `inventory_id` = $inv_id AND `month` = $month AND `year` = $year");
        $count = $check->rowCount();
        if ($count > 0){
            $report_data = $check->fetch();
            $report_id = $report_data['inventory_report_id'];
            $update = $conn->prepare("UPDATE `tbl_inventory_report` SET `is_added_to_forecast`=? WHERE `inventory_report_id` = ?");
            $update->execute([1,$report_id]);
            echo 'success';
        }else{
            echo 'fail';
        }
    }catch (Exception $e){
        echo $e;
    }

}

if (isset($_POST['remove_forecast'])){
    $inv_id = $_POST['inv_id'];
    $month = $_POST['month'];
    $year = $_POST['year'];

    try {
//        check if item has order qty already
        $check = $conn->query("SELECT `inventory_report_id`, `inventory_id`, `qty_order`, `is_added_to_forecast`, `month`, `year` FROM `tbl_inventory_report` 
                                        WHERE `inventory_id` = $inv_id AND `month` = $month AND `year` = $year");
        $count = $check->rowCount();
        if ($count > 0){
            $report_data = $check->fetch();
            $report_id = $report_data['inventory_report_id'];
            $update = $conn->prepare("UPDATE `tbl_inventory_report` SET `is_added_to_forecast`=? WHERE `inventory_report_id` = ?");
            $update->execute([0,$report_id]);
            echo 'success';
        }else{
            echo 'fail';
        }
    }catch (Exception $e){
        echo $e;
    }

}


$conn = null;
?>