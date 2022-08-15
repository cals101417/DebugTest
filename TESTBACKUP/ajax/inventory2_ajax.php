
<?php
require_once '../session.php';


// UPDATE inventory

if (isset($_POST['update_inventory'])){

    $inventory_id = $_POST['update_inventory'];
    $item = $_POST['item'];
    $description = $_POST['description'];
 
  
   
 // Set values for the image directory
   
    $inventory_img_dir = '';
//
    try {
        $inventory_qry = $conn->query("     ELECT img_src FROM tbl_inventory WHERE tbl_inventory.inventory_id = $inventory_id");
        $inventory = $inventory_qry->fetch();
        $inventory_img_src = $inventory['img_src'];
        $inventory_img_dir = '../assets/media/photos/inventory/'.$inventory_img_src;
    }catch (Exception $e){
        echo $e;
    }
    
    if (!empty($_FILES["inventory_img"]["name"])){
        // Allow certain file formats
        // File upload path
        $targetDir = "../assets/media/photos/inventory/";
        $fileName = basename($_FILES["inventory_img"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

        $allowTypes = array('jpg','png','jpeg','gif');
        if(in_array($fileType, $allowTypes)){
            // Upload file to server
            if(move_uploaded_file($_FILES["inventory_img"]["tmp_name"], $targetFilePath)){
                // Update image file name into database
                unlink($inventory_img_dir);
                try {
                    $update = $conn->prepare("UPDATE `tbl_inventory` 
                                        SET `item` = ?,
                                        `img_src` = ?,
                                        `description` = ?
                                        
                                        WHERE
                                            `inventory_id` = ?");
                    $update->execute([$item, $fileName, $description,$inventory_id]);

                    echo 'inventory successfully updated';
                }catch (Exception $e){
                    echo 'Sorry, Something wen\'t wrong'.$e;
                }

            }else{
                $statusMsg = "Sorry, there was an error uploading your file.";
            }
        }else{
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
        }
    }else{
        try {
            $update = $conn->prepare("UPDATE `tbl_inventory` 
            SET `item` = ?,        
            `description` = ?
            
            WHERE
                `inventory_id` = ?");
            $update->execute([$item, $description,$inventory_id]);

            echo 'inventory successfully updated';
        }catch (Exception $e){
            echo 'Sorry, Something went wrong'.$e;
        }
    }
}

// UPDATE INVENTORY END



if (isset($_POST['add_new_item'])){
    $name = $_POST['item_name'];
    $desc = $_POST['desc'];
    $min_qty = $_POST['min_qty'];
    $date = date('Y-m-d H:i:s');
    $statusMsg = "";
    if (!empty($_FILES["item_img"]["name"])){
        // Allow certain file formats
        // File upload path
        $targetDir = "../assets/media/photos/inventory/";
        $fileName = basename($_FILES["item_img"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);

        $allowTypes = array('jpg','png','jpeg','gif');
        if(in_array($fileType, $allowTypes)){
            // Upload file to server
            if(move_uploaded_file($_FILES["item_img"]["tmp_name"], $targetFilePath)){
                // Insert image file name into database
                    try {
                        $insert = $conn->prepare("INSERT INTO `tbl_inventory`(`item`, `description`, `min_qty`,`img_src`, `date_created`, `date_updated`, `user_id`, `is_removed`) VALUES (?,?,?,?,?,?,?,?)");
                        $insert->execute([$name,$desc,$min_qty,$fileName,$date,$date,$user_id,0]);

                        echo 'Item successfuly added';
                    }catch (Exception $e){
                        echo 'Something went wrong '.$e;
                    }

            }else{
                $statusMsg = "Sorry, there was an error uploading your file.";            }
        }else{
            $statusMsg = 'Sorry, only JPG, JPEG, PNG, GIF, & PDF files are allowed to upload.';
        }
    }else{
        try {
            $insert = $conn->prepare("INSERT INTO `tbl_inventory`(`item`, `description`, `min_qty`,`img_src`, `date_created`, `date_updated`, `user_id`, `is_removed`) VALUES (?,?,?,?,?,?,?,?)");
            $insert->execute([$name,$desc,$min_qty,'',$date,$date,$user_id,0]);

            echo 'Item successfuly added';
        }catch (Exception $e){
            echo 'Something went wrong '.$e;
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

if (isset($_POST['fetch_stocks'])){
    $inventory_id = $_POST['inventory_id'];
    $return_arr = array();

    try {

        $fetch_item_qry = $conn->query("SELECT `inventory_id`, `item`, `min_qty`, `date_created`, `date_updated`, `user_id`, `is_removed` FROM `tbl_inventory` WHERE `inventory_id` = $inventory_id SORT" );
        $item = $fetch_item_qry->fetch();
        $item_name = ucwords($item['item']);
        $min_qty = $item['min_qty'];

        $return_arr[] = ["inventory_id" => $inventory_id, "item_name" => $item_name,"min_qty" => $min_qty];

        echo json_encode($return_arr);
    }catch (Exception $e){
        echo $e;
    }

}

// RECORD TO DATABASE THE QUANTITY OF STOCKS REMOVED FROM TBL INVENTORY THEN RECORD IT TO TBL_INVENTORY_BOUND
if (isset($_POST['pull_stocks'])){
    $inventory_id = $_POST['inventory_id'];
    $pull_out_stock_qty = $_POST['pull_out_stock_qty'];
    $current_stock_qty = $_POST['current_stock_qty'];
    $new_stock_qty = abs($current_stock_qty-$pull_out_stock_qty);
    
    $type = 'outbound';
    $date = date('Y-m-d H:i:s');

    try {

        $conn->beginTransaction();
//        INSERT NEW BOUND DATA
        $bound_qry = $conn->prepare("INSERT INTO `tbl_inventory_bound`(`inventory_id`, `qty`, `type`, `date`, `user_id`) VALUES (?,?,?,?,?)");
        $bound_qry->execute([$inventory_id,$pull_out_stock_qty,$type,$date,$user_id]);

//        Update inventory quantity
        $update_inventory_qry = $conn->prepare("UPDATE `tbl_inventory` SET `current_stock_qty` = ? WHERE `inventory_id` = ?");
        $update_inventory_qry->execute([$new_stock_qty,$inventory_id]);

        $conn->commit();
        echo 'Pulled out '.$pull_out_stock_qty.' from the Inventory.'.$new_stock_qty.' remaining';
    }catch (Exception $e){
        $conn->rollBack();
        echo $e;
    }
}

// FUNCTION FOR REMOVING ITEM
if (isset($_POST['remove_item'])){

    $inventory_id = $_POST['inventory_id'];

    try {
        $remove_qry = $conn->prepare("UPDATE `tbl_inventory` SET `is_removed`= ?,`is_removed_user_id`= ?  WHERE `inventory_id`= ?");
        $remove_qry->execute([1,$user_id,$inventory_id]);

        echo 'success';
    }catch (Exception $e){
        echo 'Something went wrong';
    }
}



if (isset($_POST['add_stocks'])){
    $newly_added_stock = $_POST['new_stock_qty'];
    $inventory_id = $_POST['inventory_id'];
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
        $update_new_qty_qry->execute([$new_stock_qty,$inventory_id]);

//        Record adding new stock
        $insert_bound = $conn->prepare("INSERT INTO `tbl_inventory_bound`(`inventory_id`, `qty`, `type`, `date`, `user_id`) VALUES (?,?,?,?,?)");
        $insert_bound->execute([$inventory_id,$newly_added_stock,$type,$date,$user_id]);

        $conn->commit();

        echo ' Successfully added '.$newly_added_stock.' new stock/s. ';
        echo 'The current quantiy of this item is now : '.$new_stock_qty;
    }catch (Exception $e){

        $conn->rollBack();
        echo 'Something went wrong '. $e;
    }
}





// if (isset($_POST['add_stocks'])){
//     $add_min_qty = $_POST['add_min_qty'];
//     $inventory_id = $_POST['inventory_id'];
//     $type = 'inbound';
//     $date = date('Y-m-d H:i:s');

//     try {
//         $conn->beginTransaction();

//     //    fetch remaining quantity
//         $fetch_item_qry = $conn->prepare("SELECT tbl_inventory.min_qty FROM tbl_inventory WHERE tbl_inventory.inventory_id = ?");
//         $fetch_item_qry->execute([$inventory_id]);
//         $item = $fetch_item_qry->fetch();
//         $current_stock_qty = $item['min_qty'];

//         $new_min_qty = $current_stock_qty+$add_min_qty;

//     //    Update stock quantity
//         $update_min_qty_qry = $conn->prepare("UPDATE `tbl_inventory` SET `min_qty`= ? WHERE `inventory_id`=  ?");
//         $update_min_qty_qry->execute([$new_min_qty,$inventory_id]);

// //        Record adding new stock
//         $insert_bound = $conn->prepare("INSERT INTO `tbl_inventory_bound`(`inventory_id`, `min_qty`, `type`, `date`, `user_id`) VALUES (?,?,?,?,?)");
//         $insert_bound->execute([$inventory_id,$add_min_qty,$type,$date,$user_id]);

//         $conn->commit();

//         echo 'Successfully added new stocks';
//     }catch (Exception $e){
//         $conn->rollBack();
//         echo 'Something went wrong '. $e;
//     }
// }

//if (isset($_POST['show_item_table'])){
//    try {
//        $fetch_items_qry = $conn->query("SELECT `inventory_id`, `item`, `min_qty`, `date_created`, `user_id` FROM `tbl_inventory`");
//        $items = $fetch_items_qry->fetchAll();
//        foreach ($items as $item){
//            $item_id = $item['inventory_id'];
//            $item_name = $item['item'];
//            $min_qty = $item['min_qty'];
//            $date_created = $item['date_created'];
//        }
//        echo 'success';
//    }catch (Exception $e){
//        echo 'Something went wrong '.$e;
//    }
//}

$conn = null;
?>