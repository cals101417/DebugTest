<?php
require_once '../session.php';

// CHANGE LAYOUT FUNCTION
if (isset($_POST['change_layout'])){
    $layout = $_POST['layout'];
    try {
//        check if user has custom layout
        $layout_qry = $conn->query("SELECT `layout_id`, `container` FROM `tbl_custom_layout` WHERE `user_id` = $user_id");
        $count = $layout_qry->rowCount();

        if ($count > 0){
            $update_layout = $conn->prepare("UPDATE `tbl_custom_layout` SET `container`= ? WHERE `user_id` = ?");
            $update_layout->execute([$layout,$user_id]);
        }else{
            $insert_new_layout = $conn->prepare("INSERT INTO `tbl_custom_layout`(`user_id`, `header`, `sidebar`, `container`) VALUES (?,?,?,?)");
            $insert_new_layout->execute([$user_id,'','',$layout]);
        }
        echo $layout;
    }catch (Exception $e){
        echo $e;
    }
}

// CHANGE sidebar theme FUNCTION
if (isset($_POST['change_sidebar'])){
    $sidebar = $_POST['sidebar'];
    try {
//        check if user has custom layout
        $layout_qry = $conn->query("SELECT `layout_id`, `container` FROM `tbl_custom_layout` WHERE `user_id` = $user_id");
        $count = $layout_qry->rowCount();

        if ($count > 0){
            $update_layout = $conn->prepare("UPDATE `tbl_custom_layout` SET `sidebar`= ? WHERE `user_id` = ?");
            $update_layout->execute([$sidebar,$user_id]);
        }else{
            $insert_new_layout = $conn->prepare("INSERT INTO `tbl_custom_layout`(`user_id`, `header`, `sidebar`, `container`) VALUES (?,?,?,?)");
            $insert_new_layout->execute([$user_id,'',$sidebar,'']);
        }
        echo $sidebar;
    }catch (Exception $e){
        echo $e;
    }
}

// CHANGE HEADER THEME FUNCTION
if (isset($_POST['change_header'])){
    $header = $_POST['header'];
    try {
//        check if user has custom layout
        $layout_qry = $conn->query("SELECT `layout_id`, `container` FROM `tbl_custom_layout` WHERE `user_id` = $user_id");
        $count = $layout_qry->rowCount();

        if ($count > 0){
            $update_layout = $conn->prepare("UPDATE `tbl_custom_layout` SET `header`= ? WHERE `user_id` = ?");
            $update_layout->execute([$header,$user_id]);
        }else{
            $insert_new_layout = $conn->prepare("INSERT INTO `tbl_custom_layout`(`user_id`, `header`, `sidebar`, `container`) VALUES (?,?,?,?)");
            $insert_new_layout->execute([$user_id,$header,'','']);
        }
        echo $header;
    }catch (Exception $e){
        echo $e;
    }
}

?>