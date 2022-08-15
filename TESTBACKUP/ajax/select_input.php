
<?php
require_once '../session.php';


 if (isset($_POST['inventory_id'])){
    $inventory_id = $_POST['inventory_id'];
    $return_arr = array();
    try {

        $fetch_item_qry = $conn->query("SELECT * FROM `tbl_inventory` WHERE `inventory_id` = $inventory_id");
        $item = $fetch_item_qry->fetch();
        $item_name = ($item['item']);
        $min_qty = $item['min_qty'];
        $return_arr[] = ["inventory_id" => $inventory_id, "item_name" => $item_name,"min_qty" => $min_qty];
        // echo json_encode($return_arr);
        echo $item['inventory_id'];
    }catch (Exception $e){
        echo $e;
    }

}
 ?>  