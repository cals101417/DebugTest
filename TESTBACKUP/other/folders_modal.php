<style type="text/css">
  input {
  text-align: center;
}
</style>

<?php   
        // ENABLE/DISABLE EDIT AND DELETE BUTTON ACCORDING TO USER ACCESS
        $create_folder_disable = '';

        $create_folder_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 13 AND `status` = 0");
        if ($create_folder_access->rowCount() > 0){

            $create_folder_access = $create_folder_access->fetch();

            $status = $create_folder_access['status'];
            if ($status == 1){
                $create_folder_disable = 'disabled';
            }

        }else{
            $create_folder_disable = 'disabled';
        }
        
        $edit_folder_disable = '';

        $edit_folder_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 14 AND `status` = 0");
        if ($edit_folder_access->rowCount() > 0){
            $edit_folder_access = $edit_folder_access->fetch();
            $status = $edit_folder_access['status'];
            
            if ($status == 1){
                $edit_folder_disable = 'disabled';
            }
            
        }else{
            $edit_folder_disable = 'disabled';
        }     


        $delete_disable = '';
        $delete_access = $conn->query("SELECT `access_id`, `access_type_id`, `status`, `date_created` FROM `user_access` WHERE `user_id` = $session_emp_id AND access_type_id = 15 AND `status` = 0");
        if ($delete_access->rowCount() > 0){
            $delete_access = $delete_access->fetch();
            $delete_status = $delete_access['status'];
            if ($delete_status == 1){
                $delete_disable = 'disabled';
            }
        }else{
            $delete_disable = 'disabled';
        }
 ?>

<div class="modal fade" id="add_folder_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New Folder</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="create_folder_form">
        <div class="modal-body">       
            <div class="form-group">
              <label for="folder_name" class="col-form-label">Folder name:</label>
              <input type="text" class="form-control" id="folder_name"  name="folder_name" required maxlength="40">
            </div>  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" > Create Folder</button>
        </div>
      </form>
    </div>
  </div>
</div>    


<div class="modal fade" id="edit_folder" tabindex="-2" role="dialog" aria-labelledby="delete_modal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModal">Edit Folder Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="edit_folder_form">
        <div class="modal-body">       
            <div class="form-group">
              <label for="folder_name" class="col-form-label">Folder Name:</label>
              <input type="" hidden id="folder_id_edit" name="folder_id_edit">
              <input type="text" class="form-control" id="folder_name_edit" onfocus="this.value=''"   name="folder_name_edit" maxlength="40">
            </div>  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" <?=$edit_folder_disable ?> >Edit Folder</button>
        </div>
      </form>
    </div>
  </div>
</div>    

