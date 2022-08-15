<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';
?>
    <link rel="stylesheet" href="assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        ?>
        <?php


        ?>
        <main id="main-container">
                    <div class="bg-pattern" style="background-image: url('assets/media/photos/construction14.png');">
                        <div class="content content-top content-full text-center bg-black-op-75">
                            <div class="py-20">
                                <h1 class="h2 font-w700 text-white mb-10">Manage Company</h1>
                            </div>
                        </div>
                    </div>
                <!-- END Hero -->
                <!-- Page Content -->
            <div id="company_table">

            </div>
        </main>
    </div>
    <!-- Add Company Modal -->

     <div class="modal fade" id="add_company_modal" tabindex="-1" role="dialog" aria-labelledby="add_Company_modal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Add New Company Form</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="add_form">
                            <div class="row">
                                <div class="col-12">
                                    <div class="block block-bordered">
                                        <div class="block-header block-header-default">
                                            <h5 class="block-title"> Details</h5>
                                        </div>
                                        <div class="block-content">
                                            <input type="hidden" name="add_company" value="1">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label-sm" for="company">Company name</label>
                                                <div class="col-8">
                                                    <input type="text" class="form-control form-control-sm" id="company" name="company" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    <!-- END Top Modal -->
    <div class="modal fade" id="edit_company_modal" tabindex="-1" role="dialog" aria-labelledby="edit_company_modal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="block block-themed block-transparent mb-0">
                    <div class="block-header bg-primary-dark">
                        <h3 class="block-title">Edit Company Form</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                <i class="si si-close"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content">
                        <form id="edit_form">
                            <div class="row">
                                <div class="col-12">
                                    <div class="block block-bordered">
                                        <div class="block-header block-header-default">
                                            <h5 class="block-title"> Details</h5>
                                        </div>
                                        <div class="block-content">
                                            <input type="hidden" id="edit_company" name="edit_company" value="1">
                                            <div class="form-group row">
                                                <label class="col-4 col-form-label-sm" for="company">Company name</label>
                                                <div class="col-8">
                                                    <input type="text" class="form-control form-control-sm" id="edit_company_name" name="edit_company_name" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                        <i class="fa fa- mr-5"></i> Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- View Company Details Modal -->
    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script src="assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#employee_sidebar').addClass('open');
            load_company_table()
            // this javascript function is for adding employees
            $("#add_form").submit(function (event) {
                event.preventDefault();
                // alert('test')
                $.ajax({
                    type: 'POST',
                    url: 'ajax/company_ajax.php',
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData:false,
                    success: function(response) {
                        alert(response);
                        load_company_table()

                    },
                    error: function() {
                        console.log("Error adding employee function");
                    }
                });
            });
            $("#edit_form").submit(function (event) {
                event.preventDefault();
                if (confirm("Are you sure you want to update this company?")) {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/company_ajax.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData:false,
                        success: function (response) {
                            alert(response);
                            load_company_table()
                            $('#edit_company_modal').modal('hide');
                        },
                        error: function () {
                            console.log("Error adding employee function");
                        }
                    });
                }
            });

        });
        function load_company_table(){
            $.ajax({
                type: 'POST',
                url: 'ajax/company_ajax.php',
                data: {
                    load_company_table: 1
                },
                success: function(response) {
                    $('#company_table').html(response);
                },
                error: function() {
                    console.log("Error adding employee function");
                }
            });

        }
        function delete_company(comapany_id){
            event.preventDefault();
            if (confirm("Are you sure you want to remove this company?")) {
                $.ajax({
                    type: 'POST',
                    url: 'ajax/company_ajax.php',
                    data: {
                        delete_company: 1,
                        company_id: comapany_id
                    },
                    success: function (response) {
                        alert(response);
                        load_company_table()
                    },
                    error: function () {
                        console.log("Error adding employee function");
                    }
                });
            }
        }
        function show_modal(company_name,company_id){
            $('#edit_company_modal').modal('show');
            $('#edit_company_name').val(company_name);
            $('#edit_company').val(company_id);
        }


    </script>
<?php
include 'includes/footer.php';