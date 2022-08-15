// GLOBAL VARIABLES
var comment_counter = 0;
var y = 0;

// var limit = 2;
$(document).ready(function () {
  let toast = Swal.mixin({
    buttonsStyling: false,
    customClass: {
      confirmButton: "btn btn-alt-success m-5",
      cancelButton: "btn btn-alt-danger m-5",
      input: "form-control",
    },
  });
  var loading = function () {
    Swal.fire({
      title: "Uploading Docs",
      allowOutsideClick: false,
      showConfirmButton: false,
    }),
      Swal.showLoading();
  };

  $("#files").change(function () {
    var files = $(this)[0].files;
    var required_upload_number = $("#required_upload_number").val();
    // var limit =5 ;
    var x = files.length;
    if (x != required_upload_number) {
      alert(
        "You need to select the exact number of file which is :  " +
          required_upload_number +
          "."
      );
      $("#files").val("");
      return false;
    } else {
      return true;
    }
  });

  $("#review_action").submit(function (event) {
    event.preventDefault();
    // var formData = new FormData();
    $(".action_rev").attr("disabled", true);
    if (confirm("Are you sure you want to submit action?")) {
      $.ajax({
        type: "POST",
        url: "ajax/document_files_ajax.php",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function () {
          $("#add_comment_modal").modal("hide");
          loading();
        },
        success: function (data) {
          console.log(data);
          // location.reload()
          // alert(data);
          //
        },
      });
    }
  });
  $("#reply_action").submit(function (event) {
    event.preventDefault();
    var formData = new FormData();
    if (confirm("Are you sure you want to add?")) {
      $.ajax({
        type: "POST",
        url: "ajax/document_files_ajax.php",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
          alert(response);
        },
      });
    }
  });
  // key =  edit_response_id
  $("#comment_action").submit(function (event) {
    event.preventDefault();
    $("#add_comment_modal1").modal("toggle");
    var formData = new FormData();
    toast
      .fire({
        title: "Are you sure you want to add this comment?",
        text: "This comment will be added to the table!",
        type: "warning",
        showCancelButton: true,
        customClass: {
          confirmButton: "btn btn-alt-danger m-1",
          cancelButton: "btn btn-alt-secondary m-1",
        },
        confirmButtonText: "Yes, Add Comment!",
        html: false,
        preConfirm: (e) => {
          return new Promise((resolve) => {
            setTimeout(() => {
              resolve();
            }, 50);
          });
        },
      })
      .then((result) => {
        if (result.value) {
          $.ajax({
            type: "POST",
            url: "ajax/document_files_ajax.php",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (response) {
              alert(response);
              history.back();
            },
          });
        } else if (result.dismiss === "cancel") {
          toast.fire("Cancelled", "File Not Deleted :)", "error");
        }
      });
  });
  $("#edit_comment_action").submit(function (event) {
    event.preventDefault();

    var formData = new FormData();
    if (confirm("Are you sure you want to Edit Comment?")) {
      $.ajax({
        type: "POST",
        url: "ajax/document_files_ajax.php",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
          alert(response);
          history.back();
        },
      });
    }
  });
});
function delete_comment(delete_response_id) {
  if (confirm("Are you sure you want to delete Comment?")) {
    $.ajax({
      type: "POST",
      url: "ajax/document_files_ajax.php",
      data: {
        delete_response_id: delete_response_id,
      },
      success: function (response) {
        alert(response);
      },
    });
  }
}

function edit_rev_comments(response_id) {
  $("#edit_comment_modal").modal("show");
  $("#edit_response_id").val(response_id);
}
function add_rev_comments() {
  $("#add_comment_modal1").modal("show");
}
function add_new_comment() {
  if (comment_counter != 4) {
    var document_id = $("#document_id").text(); //set value of document_id
    var reviewer_id = $("#reviewer_id").val();
    comment_counter = comment_counter + 1;
    display_input();
  } else {
    alert("Exceeded Number of Comments Allowed");
  }
}
function display_input() {
  var content = "";
  for (var i = comment_counter; i >= 0; i--) {
    content =
      content +
      `<div class="form-group">
                        <label for="pages">Pages</label> 
                        <input type="text" class="form-control" id="pages" name= "pages` +
      i +
      `"  placeholder="Pages" >
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Comments</span>
                        </div>
                        <textarea class="form-control" name="comments` +
      i +
      `" aria-label="With textarea" ></textarea>
                    </div>`;
    content2 =
      `<div class="form-group">
            <label for="pages">Pages</label> 
            <input type="text" class="form-control" id="pages" name= "pages` +
      i +
      `"  placeholder="Pages" >
        </div>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Comments</span>
            </div>
            <textarea class="form-control" name="comments` +
      i +
      `" aria-label="With textarea" ></textarea>
        </div>`;
  }

  $("#div_form_comment").html(content);
}
// function add_new_reply(response_id,document_id,reply_reviewer_id){
//     alert(reply_reviewer_id);
//     $('#reply_document_id').val(document_id);
//     $('#response_id').val(response_id);
//     $('#reply_reviewer_id').val(reply_reviewer_id);
//     $('#add_reply_modal').modal('show');
// }
function action_modal_show(choice) {
  $("#add_comment_modal").modal("show");
  $("#div_signed_file").show();
  var input_file = ` <input  type="file" class="form-control form-control-lg text-sm-left"  type="text"  id="signed_file" name="signed_file[]" multiple required >`;

  if (choice == 1) {
    // APPROVED WITH COMMENTS    //    //
    $("#action_type").val(1);
    $("#div_signed_file").show();
    $("#signed_file2").html(input_file);
  } else if (choice == 3) {
    // FAILED/ NOT APPROVED
    $("#add_comment_modal").modal("show");
    $("#action_type").val(3);
    $("#signed_file2").html("");
    $("#div_signed_file").hide();
  }
}
function count_attached_file(choice) {
  var x = document.getElementById("div_form_comment");
  x;
}
function set_action_id(id) {
  $("#action_id").val(id);
}
