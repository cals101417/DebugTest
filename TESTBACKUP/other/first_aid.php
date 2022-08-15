<?php
include 'session.php';
include 'includes/head.php';
include 'includes/page_layout.php';

$type_id = 1;
?>
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll <?=$header_layout?> page-header-inverse <?=$main_content.' '.$sidebar_layout?>">
        <?php
        include 'includes/sidebar.php';
        include 'includes/header.php';
        ?>

<?php
try {

    $get_emp_qry = $conn->query("SELECT * FROM tbl_employees WHERE is_deleted = 0 ORDER BY firstname  ASC");
    $employee_list = $get_emp_qry->fetchAll();
    $get_eng_qry = $conn->query("SELECT *,tbl_position.position AS emp_position FROM tbl_employees 
                                          INNER JOIN tbl_position ON  tbl_employees.position = tbl_position.position_id
                                          WHERE tbl_position.position LIKE '%engineer%' AND tbl_employees.is_deleted = 0 ORDER BY firstname  ASC");
    $equipment_list_qry = $conn->query("SELECT * FROM tbl_equipment WHERE is_deleted = 0");
    $equipment_list = $equipment_list_qry->fetchAll();
    $engineer_list = $get_eng_qry->fetchAll();
//    $root_causes = array("Use of Tools Equipment,Materials and Products", "Intentional/Lack of Awareness/Behaviors", "Protective Systems","Integrity of Tools/PLan/Equipment, Material", "Workplace Hazards", "Organizational","Other");
    $incident_type_qry = $conn->query("SELECT * FROM tbl_incident WHERE is_deleted= 0");
    $incident_type = $incident_type_qry->fetchAll();
    $body_parts_qry = $conn->query("SELECT * FROM tbl_body_parts WHERE is_deleted = 0");
    $body_parts = $body_parts_qry->fetchAll();
    $mechanisms_qry = $conn->query("SELECT * FROM tbl_mechanisms WHERE is_deleted = 0");
    $mechanisms = $mechanisms_qry->fetchAll();
    $root_causes_qry = $conn->query("SELECT * FROM tbl_root_causes WHERE is_deleted = 0");
    $root_causes = $root_causes_qry->fetchAll();
    $leading_indicators_qry =  $conn->query("SELECT `indicator_id`, `indicator` FROM tbl_indicators WHERE is_deleted = 0");
    $leading_indicators = $leading_indicators_qry->fetchAll();
    $nature_qry = $conn->query("SELECT nature AS nature_description, nature_id FROM tbl_nature WHERE is_deleted = 0");
    $natures = $nature_qry->fetchAll();
} catch ( Exception $e) {
    echo $e;
}

?>
    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="assets/js/plugins/select2/css/select2.css">
        <!-- Main Container -->
        <main id="main-container">
            <!-- Hero -->
            <div class="bg-gd-lake d-print-none">
                <div class="bg-pattern" style="background-image: url('assets/media/photos/construction4.jpeg');">
                    <div class="content content-top content-full text-center">
                        <div class="py-20">
                            <h1 class="h2 font-w700 text-white mb-10">Incident</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->

            <div class="content" >
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Incident  Report</h3>
                        <button type="button" class="btn btn-primary mr-5" id="add_incident_btn" data-toggle="modal" data-target="#add_new_incident_modal""><li class="fa fa-plus"></li> Incident</button>
                        <button type="button" class="btn btn-primary mr-5 d-print-none"  id="back_btn" onclick="load_all_incidents()"><li class="fa fa-return"></li> Back</button>
                        <button type="button" class="btn btn-primary mr-5 d-print-none"  id="print_btn" onclick="Codebase.helpers('print-page');"><li class="fa fa-return"></li> Print</button>
                    </div>
                    <div class=" block-content block-content-full  p-3 mb-5 rounded " id="first_aid_content">
                        <!-- DataTables functionality is initialized with .js-dataTable-full-pagination class in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
                    </div>
                </div>
                <div class="block">
                    <div class="block-content">
                        <div id="canvass"></div>
                    </div>
                </div>
            </div>
        </main>
<!--modals-->
<!--                                                            NEW INCIDENT MODAL-->
    <div class="modal fade" id="add_new_incident_modal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white" id="exampleModalLabel">New Incident</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form  class="js-validation-bootstrap" id="add_new_incident">
                <div class="modal-body">
                    <div class="col-xl-12">
                        <h4>I. First Aid Information</h4>
                    </div>
                    <div class="col-xl-12">
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label" for="val-password">Name of the injured </label>
                            <div class="col-lg-8 ">
                                <select class=" js-select2 form-control form-control-sm add_incident " id="injured" name="injured" style="width: 100%;" data-placeholder="Choose one.." required>
                                    <option value=""></option>
                                <?php foreach ($employee_list as $emp):?>
                                    <option value="<?=$emp['employee_id']?>"><?=$emp['firstname']." ".$emp['lastname']?></option>
                                <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label"  for="injured">Site Engineer e <span class="text-danger">*</span></label>
                            <div class="col-lg-8 ">
                                <select class="js-select2 form-control form-control-sm add_incident"  name="site_engineer" id="site_engineer" data-placeholder="Choose One.." style="width: 100%;" required>
                                    <!--                                    <option selected="selected" disabled="disabled" value="">Select Site Engineer</option>-->
                                    <option value=""></option>
                                    <?php foreach ($engineer_list as $emp2):?>
                                        <option value="<?=$emp2['employee_id']?>"><?=$emp2['firstname']." ".$emp2['lastname']?> </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label"  for="first_aider">First Aider <span class="text-danger">*</span></label>
                            <div class="col-lg-8 ">
                                <select class="js-select2 form-control form-control-sm add_incident"  name="first_aider" id="first_aider" data-placeholder="Choose One... "style="width: 100%" required>
                                    <option></option>
                                    <?php foreach ($employee_list as $emp2):?>
                                        <option value="<?=$emp2['employee_id']?>"><?=$emp2['firstname']." ".$emp2['lastname']?> </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label"  for="age">Age <span class="text-danger">*</span></label>
                            <div class="col-lg-8 ">
                                <input class="form-control form-control-sm bg-white" type="number" id="age" readonly name="age"  placeholder=""  >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label"  for="job_description">Job Description <span class="text-danger">*</span></label>
                            <div class="col-lg-8 ">
                                <input class="form-control form-control-sm bg-white " type="text" id="job_description"  name="job_description" readonly  placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label"  for="location">Location <span class="text-danger">*</span></label>
                            <div class="col-lg-8 ">
                                <input class="form-control form-control-sm add_incident" type="text" id="location"  name="location" placeholder="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label"  for="nurse_findings">Nurse  Findings <span class="text-danger">*</span></label>
                            <div class="col-lg-8">
                                <textarea  class="form-control form-control-sm add_incident" id="nurse_findings" name="nurse_findings" cols="30" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label"  for="treatment">First Aid Given <span class="text-danger">*</span></label>
                            <div class="col-lg-8">
                                <textarea  class="form-control form-control-sm add_incident" id="treatment" name="treatment" cols="30" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12">
                        <h4>II. Incident Information</h4>
                    </div>
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="incident_type">Incident Classification</label>
                                <select class="js-select2 form-control form-control-sm add_incident"  name="incident_type" id="incident_type" data-placeholder="Choose One.." style="width: 100%;" required >
                                    <option></option>
                                    <?php foreach ($incident_type as $incident):?>
                                        <option value="<?=$incident['incident_id']?>"><?=$incident['incident']?> </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nature">Nature of Injury</label>
                                <select class="js-select2 form-control form-control-sm add_incident"  name="nature"  id="nature"  style="width: 100%;" data-placeholder="Choose One.." required>
                                    <option></option>
                                    <?php foreach ($natures as $nature):?>
                                        <option value="<?=$nature['nature_id']?>"><?=$nature['nature_description']?> </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="leading_indicator">Leading Indicator</label>
                                <select class="js-select2 form-control form-control-sm add_incident"  name="leading_indicator" id="leading_indicator" style="width: 100%;" data-placeholder="Choose One.."  required>
<!--                                    <option selected="selected" disabled="disabled" value="">Select Leading Indicator</option>-->
                                    <option ></option>
                                    <?php foreach($leading_indicators as $indicator):?>
                                        <option value="<?=$indicator['indicator_id']?>"><?=$indicator['indicator']?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mechanism">Mechanism of Injury</label>
                                <select class="js-select2 form-control form-control-sm add_incident"  name="mechanism" id="mechanism" data-placeholder="Choose One.."   style="width: 100%;" required>
                                    <option></option>
                                    <?php foreach ($mechanisms as $mechanism):?>
                                        <option value="<?=$mechanism['mech_id']?>"><?=$mechanism['mech_description']?> </option>
                                    <?php endforeach ?>
                                </select>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="injured">Potential Severity</label>

                                <select class="js-select2 form-control form-control-sm add_incident"  name="severity" id="severity" style="width: 100%;" data-placeholder="Choose One.." required>
<!--                                    <option selected="selected" disabled="disabled" value="">Select Severity</option>-->
                                    <option></option>
                                    <option value="1">Low </option>
                                    <option value="2">Medium</option>
                                    <option value="3">High</option>
                                    <option value="4">Extreme</option>
                                </select>
                            </div>

                        </div>
                        <div class="col-xl-6">
                            <div class="form-group">
                                <label for="root_causes">Incidents per company</label>
                                <input class="form-control form-control-sm bg-white"   name="company" id="company" readonly  >
                            </div>
                            <div class="form-group row">
                                <div class="col-xl-6">
                                    <label for="lti">LTI of the Injured</label>
                                    <input type="number" class="form-control form-control-sm add_incident"  name="lti_injured" id="lti_injured" placeholder="Number of Days Loss" required>
                                </div>
                                <div class="col-xl-6">
                                    <label for="lti">Site Name</label>
                                    <input class="form-control form-control-sm add_incident"   name="site_name" id="site_name" placeholder="Site Name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="root_causes">Root Cause</label>
                                <select class= "js-select2 form-control form-control-sm add_incident"  name="root_causes" id="root_causes" data-placeholder="Choose one.." style="width: 100%" required>
                                    <option></option>
                                    <?php foreach ($root_causes as $cause):?>
                                        <option value="<?=$cause['cause_id']?>"><?=$cause['cause']?> </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="equipment">Equipment Involved</label>
                                <select class="js-select2 form-control form-control-sm add_incident"  name="equipments[]"  id="equipments"  data-placeholder="Choose One.."  multiple style="width: 100%;" required>
                                    <?php foreach ($equipment_list as $equipment):?>
                                        <option value="<?=$equipment['equipment_id']?>"><?=$equipment['equipment_description']?> </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mechanism">Body Part Injured</label>
                                <select class="js-select2 form-control form-control-sm add_incident"  name="body_parts[]" multiple id="body_part"  data-placeholder="Choose One.." style="width: 100%;"  required>
                                    <?php foreach ($body_parts as $part):?>
                                        <option value="<?=$part['part_id']?>"><?=$part['part_name']?> </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                        </div>
                    </div>
                        <div class="col-xl-12">
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea class="form-control form-control-sm add_incident"  name="remarks" id="remarks" rows="6" placeholder="Remarks" required> </textarea>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="save_incident_btn">Save Incident</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
    <!--NEW INCIDENT MODAL-->
<!--modals-->
<?php $conn = NULL; ?>
    <script src="assets/js/codebase.core.min.js"></script>

    <script src="assets/js/codebase.app.min.js"></script>
    <script src="assets/js/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="assets/js/codebase.core.min.js"></script>
    <!-- Page JS Plugins -->
    <script src="assets/js/plugins/select2/js/select2.full.min.js"></script>
    <script src="assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="assets/js/plugins/jquery-validation/additional-methods.js"></script>
    <!-- Page JS Helpers (Select2 plugin) -->
    <script>jQuery(function(){ Codebase.helpers('select2'); });</script>
    <!-- Page JS Code -->
    <script src="assets/js/pages/be_forms_validation.min.js"></script>

    <script>
        $(document).ready(function () {

            // $(".js-select2").select2({
            //     theme: "classic"
            // });

            disable_btn()
            load_all_incidents()
            $('#first_aid_sidebar').addClass('open');
            $('#injured').change(function (event){
                let injured_id = $('#injured').find(":selected").val();
                $.ajax({
                    type: 'POST',
                    url: 'ajax/incident_ajax.php',
                    data: {
                        injured_id: injured_id,
                        get_injured_info  : 1
                    },
                    success: function (response) {
                       let employee = JSON.parse(response);
                        $('#age').val(employee[0].age);
                        $('#job_description').val(employee[0].position);
                        $('#company').val(employee[0].company);
                    },
                    error: function () {
                        console.log("Error adding employee function");
                    }
                });
            })
            $("#add_new_incident").submit(function (event) {
                event.preventDefault();
                if (confirm("Are you sure you want to add incident?") ) {
                    $.ajax({
                        type: 'POST',
                        url: 'ajax/incident_ajax.php',
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (response) {
                            // alert(response)
                            load_all_incidents()
                            $('#add_new_incident_modal form :input'). val("");
                            $( ".js-select2" ).each(function( index ) {
                                $(this).val(0).select2();
                                $('select').select2({
                                    placeholder: {
                                        id: '-1', // the value of the option
                                        text: 'Select an option'
                                    }
                                });
                            });
                            disable_btn()
                        },
                        error: function () {
                            console.log("Error adding employee function");
                        }
                    });
                }
            })
        });
        function delete_incident(delete_incident_id){
            // alert(incident_id);
            let toast = Swal.mixin({
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-alt-success m-5',
                    cancelButton: 'btn btn-alt-danger m-5',
                    input: 'form-control'
                }
            });
            toast.fire({
                title: 'Are you sure you want to remove this incident ? ?',
                text: 'This action cannot be reversed',
                type: 'warning',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-alt-warning m-1',
                    cancelButton: 'btn btn-alt-secondary m-1'
                },
                confirmButtonText: 'Yes, remove incident!',
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
                    toast.fire('incident Deleted!', 'The Incident is removed.', 'success');
                    // result.dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                    $.ajax({
                        type: "POST",
                        url: "ajax/incident_ajax.php",
                        data: {
                            delete_incident_id: delete_incident_id,
                            remove_incident: 1,
                        },
                        success: function(data){
                            load_all_incidents()
                        }
                    });
                } else if (result.dismiss === 'cancel') {
                    toast.fire('Cancelled', 'incident not deleted :)', 'error');
                }
            });
        }

        function disable_btn(){
            $("#save_incident_btn").attr('disabled','disabled');
            var button_disabled = '';
            $("#add_new_incident").change(function() {
                var  x= 0;
                $.each($(".add_incident"), function(){
                    var select_val =  $(this).val();
                    if (select_val ==""){
                        button_disabled = 'disabled';
                        return false;
                    } else  {
                        button_disabled = '';
                        return true;
                    }
                });
                if (button_disabled =="disabled" ){
                    $("#save_incident_btn").attr('disabled','disabled');
                } else {
                    $("#save_incident_btn").removeAttr('disabled');
                }
            });
        }
        // get all incidents
        function load_all_incidents(){
            $('#back_btn').hide();
            $('#print_btn').hide();
            $('#add_incident_btn').show();

            $('#first_aid_content').html('');
            $.ajax({
                type: "POST",
                url: "ajax/incident_ajax.php",
                data: {
                    load_all_incidents: 1
                },
                success: function(data){
                    $('#first_aid_content').html(data);
                    // location.reload()
                }
            });
        }
        function load_incident_details(fa_id){
            $('#add_incident_btn').hide();
            $('#back_btn').show();
            $('#print_btn').show();
            $('#first_aid_content').html('');
            $.ajax({
                type: "POST",
                url: "ajax/incident_ajax.php",
                data: {
                    fa_id: fa_id,
                    load_incident: 1
                },
                success: function(data){
                    $('#first_aid_content').html(data);
                    // location.reload()
                }
            });
        }
    </script>
    </body>


    </body>
    </html>
<?php
include 'includes/footer.php';
