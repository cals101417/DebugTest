<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
include 'folders_modal.php'
?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        ?>
        <!-- DISPLAY FOLDERS FROM DATABASE -->
        <?php 
            $get_folders_qry = $conn->query("SELECT * FROM tbl_folders  WHERE is_removed = 1 AND sub_id = $subscriber_id ORDER BY folder_id DESC  ");
            $folders =  $get_folders_qry->fetchAll();
         ?>
         <link rel="stylesheet" href="assets/js/plugins/datatables/dataTables.bootstrap4.css">
        <!-- DISPLAY FOLDERS FROM DATABASE -->
        <!-- Main Container -->
        <main id="main-container">
            <!-- Hero -->
            <div class="bg-dark">
                <div class="bg-pattern bg-black-op-25" style="background-image: url('assets/media/photos/construction4.jpeg');">
                    <div class="content content-top text-center">
                        <div class="py-50">
                            <h1 class="font-w700 text-white mb-10">Manage Files</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <h2 class="content-heading">
                    Folders
                    <button class="btn btn-sm btn-primary float-right" data-toggle="modal" <?=$create_folder_disable ?>  data-target="#add_folder_modal" ><i class="fa  fa-plus" > </i> Create New Folder </button>
                </h2>
                   <!-- Dynamic Table Full Pagination -->
                <div class="block block-rounded">
                    <div class="block-content pb-20">
                        <!-- DataTables functionality is initialized with .js-dataTable-full-pagination class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                        <table class="table table-striped table-sm table-vcenter js-dataTable-full-pagination mb-20" >
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-capitalize text-center">Name</th>
                                    <th class="text-capitalize text-center">Date Created</th>
                                    <th class="text-capitalize text-center" style="width: 15%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($folders as $folder): ?>
                                    <tr>
                                        <td class="text-center"><?=$folder['folder_id'] ?></td>
                                        <td class="font-w600 text-center">
                                            <a  href="document_files_specific.php?view=<?=$folder['folder_id'] ?>&folder=<?=$folder['folder_name'] ?>">
                                                <i class="fa fa-folder fa-4x text-warning" aria-hidden="true"></i>
                                                <div class="font-w600"><?=$folder['folder_name'] ?></div>
                                            </a>
                                        </td>
                                        <td class="text-center"><?=date('F d, Y',strtotime($folder['date_created']))?></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="tooltip" title="Edit" <?=$edit_folder_disable?> onclick="show_edit_folder_modal(<?=$folder['folder_id']?>,'<?=$folder['folder_name']?>')">
                                                <i class="fa fa-pencil"></i>
                                            </button>

                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" title="Remove Folder" <?=$delete_disable?>  onclick="delete_folder(<?=$folder['folder_id']?>)">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                        <td class="d-none d-sm-table-cell">

                                        </td>
                                    </tr>
                                <?php endforeach ?>   <!-- ROW #1 -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- END Dynamic Table Full Pagination -->
            </div>
        </main> 

    <!-- END View Employee Details Modal -->

        <script src="assets/js/codebase.core.min.js"></script>

        <script src="assets/js/codebase.app.min.js"></script>

        <!-- Page JS Plugins -->
        <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>

        <!-- Page JS Code -->
        <script src="assets/js/pages/be_tables_datatables.min.js"></script>
        <script src="assets/js/plugins/sweetalert2/sweetalert2.min.js"></script>

        <script>
         $(document).ready(function () {
             let toast = Swal.mixin({
                 buttonsStyling: false,
                 customClass: {
                     confirmButton: 'btn btn-alt-success m-5',
                     cancelButton: 'btn btn-alt-danger m-5',
                     input: 'form-control'
                 }
             });

             $("#create_folder_form").submit(function (event) {
                 event.preventDefault();
                 $('#add_folder_modal').modal('hide');
                 toast.fire({
                     title: 'Are you sure you want to create this folder?',
                     text: 'This will be added to the folder list!',
                     type: 'warning',
                     showCancelButton: true,
                     customClass: {
                         confirmButton: 'btn btn-alt-success m-1',
                         cancelButton: 'btn btn-alt-secondary m-1'
                     },
                     confirmButtonText: 'Yes, Create Folder!',
                     html: false,
                     preConfirm: e => {
                         return new Promise(resolve => {
                             setTimeout(() => {
                                 resolve();
                             }, 50);
                         });
                     }
                 }).then(result => {
                     if (result.value) {
                         toast.fire('Folder Created!', 'New Folder Created.', 'success');
                         // result.dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                         $.ajax({
                             type: 'POST',
                             url: 'ajax/document_files_ajax.php',
                             data:new FormData(this),
                             contentType: false,
                             cache: false,
                             processData:false,
                             success: function(response) {
                                 location.reload();
                             },
                             error: function() {
                                 console.log("Error adding new folder function");
                             }
                         });
                     } else if (result.dismiss === 'cancel') {
                         toast.fire('Cancelled', 'FOlder Not Created :)', 'error');
                     }
                 });
             });
             $("#edit_folder_form").submit(function (event) {
                event.preventDefault();
                 let toast = Swal.mixin({
                     buttonsStyling: false,
                     customClass: {
                         confirmButton: 'btn btn-alt-success m-5',
                         cancelButton: 'btn btn-alt-danger m-5',
                         input: 'form-control'
                     }
                 });
                 $('#edit_folder').modal('hide');
                 toast.fire({
                     title: 'Are you sure you want to edit this folder?',
                     text: 'This action cannot be reversed',
                     type: 'warning',
                     showCancelButton: true,
                     customClass: {
                         confirmButton: 'btn btn-alt-success m-1',
                         cancelButton: 'btn btn-alt-secondary m-1'
                     },
                     confirmButtonText: 'Yes, Create Folder!',
                     html: false,
                     preConfirm: e => {
                         return new Promise(resolve => {
                             setTimeout(() => {
                                 resolve();
                             }, 50);
                         });
                     }
                 }).then(result => {
                     if (result.value) {
                         toast.fire('Folder Deleted!', 'The folder is removed.', 'success');
                         // result.dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                         $.ajax({
                             type: 'POST',
                             url: 'ajax/document_files_ajax.php',
                             data:new FormData(this),
                             contentType: false,
                             cache: false,
                             processData:false,
                             success: function(response) {
                                 location.reload();
                             },
                             error: function() {
                                 console.log("Error edit file function");
                             }
                         });

                     } else if (result.dismiss === 'cancel') {
                         toast.fire('Cancelled', 'Folder no deleted :)', 'error');
                     }
                 });
            });
        })
        function show_edit_folder_modal(folder_id, folder_name){
            $('#edit_folder').modal('show');
            $('#folder_name_edit').val(folder_name);
            $('#folder_id_edit').val(folder_id);
        }

         function delete_folder(folder_id){
             // alert(folder_id);
             let toast = Swal.mixin({
                 buttonsStyling: false,
                 customClass: {
                     confirmButton: 'btn btn-alt-success m-5',
                     cancelButton: 'btn btn-alt-danger m-5',
                     input: 'form-control'
                 }
             });

             toast.fire({
                 title: 'Are you sure you want to remove this folder ? ?',
                 text: 'This action cannot be reversed',
                 type: 'warning',
                 showCancelButton: true,
                 customClass: {
                     confirmButton: 'btn btn-alt-warning m-1',
                     cancelButton: 'btn btn-alt-secondary m-1'
                 },
                 confirmButtonText: 'Yes, remove Folder!',
                 html: false,
                 preConfirm: e => {
                     return new Promise(resolve => {
                         setTimeout(() => {
                             resolve();
                         }, 50);
                     });
                 }
             }).then(result => {
                 if (result.value) {
                     toast.fire('Folder Deleted!', 'The folder is removed.', 'success');
                     // result.dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                     $.ajax({
                         type: "POST",
                         url: "ajax/document_files_ajax.php",
                         data: {
                             folder_id: folder_id,
                             remove_folder: 1,
                         },
                         success: function(data){
                             // alert(data);
                             location.reload();
                         }
                     });
                 } else if (result.dismiss === 'cancel') {
                     toast.fire('Cancelled', 'Folder not deleted :)', 'error');
                 }
             });
         }
    </script>
<?php
include 'includes/footer.php';

