<!-- Add Training Modal -->
<div class="modal fade" id="create_training_modal" tabindex="-1" role="dialog" aria-labelledby="create_training_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Create New Training</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <!--                        <div id="progress_add"></div>-->
                    <form id="add_form" method="post">

                        <input type="hidden" class="form-control form-control-lg" id="add_tbt" name="add_tbt" placeholder="" required>
                        <div class="form-group row">
                            <label class="col-12" for="title">Course Title</label>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-lg" id="title" name="title" placeholder="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="location">Training Location</label>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-lg" id="location" name="location" placeholder="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="contract_no">Contract No.</label>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-lg" id="contract_no" name="contract_no" placeholder="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="example-select">Conducted by:</label>
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-lg" id="select_trainer" name="select_trainer" placeholder="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="date">Date of Training</label>
                            <div class="col-12">
                                <input type="date" class="form-control form-control-lg" id="date" name="date" placeholder="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="date_expired">Date Expired</label>
                            <div class="col-12">
                                <input type="date" class="form-control form-control-lg" id="date_expired" name="date_expired" placeholder="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="training_hrs">Training Hours</label>
                            <div class="col-12">
                                <input type="number" class="form-control form-control-lg" id="training_hrs" name="training_hrs" placeholder="" required>
                            </div>
                        </div>
                        <input type="hidden" id="type" name="type" value="">
                        <div class="form-group row">
                            <label class="col-12" for="file">Upload image file</label>
                            <div class="col-12">
                                <input type="file" name="files[]" multiple >
                                <!--                                    <input type="file" id="files[]" name="files[]" multiple>-->
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-sm btn-hero btn-alt-primary min-width-175">
                                    <i class="fa fa-plus mr-5"></i> Save
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

<!--VIEW TRAINING DETAILS MODAL-->
<div class="modal fade" id="modal_view_details" tabindex="-1" role="dialog" aria-labelledby="modal_view_details" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-top" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Training Details</h3>
                    <div class="block-options">
                        <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content" id="view_details_content">
                    <div class="block-content" id="view_details_content2">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add participants modal -->
<div class="modal fade" id="modal_add_participants" tabindex="-1" role="dialog" aria-labelledby="modal_add_participants" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Add Participating Employees</h3>
                    <div class="block-options">
                        <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <div class="form-group row">
                        <div class="col-lg-11 col-sm-12 mb-10">
                            <input type="hidden" name="modal_training_id" id="modal_training_id">
                            <select class="js-select2 form-control" id="modal_select_employee" name="select_employee" style="width: 100%">

                            </select>
                        </div>
                        <div class="col-lg-1 col-sm-12">
                            <button type="button" class="btn btn-success float-right" id="add_to_training">Add</button>
                        </div>
                    </div>
                    <table class="table table-vcenter">
                        <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th style="width: 20%;">Employee name</th>
                            <th class="text-center" style="width: 10%;">Action</th>
                        </tr>
                        </thead>
                        <tbody id="modal_table_participants">

                        </tbody>
                    </table>
                </div>
                <div class="block-content block-content-full bg-body-light font-size-sm">
                    <button class="btn btn-primary float-right" onclick="reload_page()">Submit</button>
                </div>
            </div>
        </div>

    </div>
</div>

<!--ATTACH IMAGE FOR COMPLETION MODAL-->
<div class="modal fade" id="set_complete_modal" tabindex="-1" role="dialog" aria-labelledby="set_complete_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Upload File</h3>
                    <div class="block-options">
                        <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <div class="block">
                        <div class="block-content">
                            <form id="upload_file" method="post">
                                <input type="hidden" id="upload_file_training_id" name="upload_file_training_id">
                                <div class="custom-file">
                                    <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                    <!-- When multiple files are selected, we use the word 'Files'. You can easily change it to your own language by adding the following to the input, eg for DE: data-lang-files="Dateien" -->
                                    <input type="file" class="custom-file-input" id="files" name="files[]" data-toggle="custom-file-input" multiple>
                                    <label class="custom-file-label" for="files">Choose files</label>
                                </div>
                                <button type="submit" class="btn btn-block btn-primary mt-20"><i class="fa fa-save"></i> Submit</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!--VIEW ATTACHED FILE DETAILS MODAL-->
<div class="modal fade" id="view_attached_file_modal" tabindex="-1" role="dialog" aria-labelledby="view_attached_file_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Attached File</h3>
                    <div class="block-options">
                        <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <div class="block">
                        <div class="block-content" >
                            <div class="row items-push" id="view_attached_file_div">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- ATTACHED PHOTOS  MODAL-->
<div class="modal fade" id="attach_photos_modal" tabindex="-1" role="dialog" aria-labelledby="attach_photos_modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="block block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title">Attached Photos</h3>
                    <div class="block-options">
                        <button class="btn-block-option" data-dismiss="modal" aria-label="Close" onclick="reload_page()">
                            <i class="si si-close"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <div class="block">
                        <div class="block-content" >
                            <form id="attach_photo_form" method="post">
                                <input type="hidden" id="attach_photo_training_id" name="attach_photo_training_id">
                                <div class="custom-file">
                                    <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                    <!-- When multiple files are selected, we use the word 'Files'. You can easily change it to your own language by adding the following to the input, eg for DE: data-lang-files="Dateien" -->
                                    <input type="file" class="custom-file-input" id="photos" name="photos[]" data-toggle="custom-file-input" multiple>
                                    <label class="custom-file-label" for="files">Choose files</label>
                                </div>
                                <button type="submit" class="btn btn-block btn-primary mt-20"><i class="fa fa-save"></i> Submit</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>