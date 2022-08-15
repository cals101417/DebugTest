<?php
include 'includes/head.php';
?>
    <!-- Main Container -->
    <main id="main-container">

        <!-- Hero -->
        <div class="bg-primary">
            <div class="bg-pattern bg-black-op-25" style="background-image: url('assets/media/various/bg-pattern.png');">
                <div class="content content-top text-center">
                    <div class="py-50">
                        <h1 class="font-w700 text-white mb-10">PREMIUM PLAN</h1>
                        <h2 class="h4 font-w400 text-white-op">All managing modules are accessible!</h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Hero -->

        <!-- Contact Form -->
        <!-- Contact Form Validation functionality is initialized in js/pages/be_pages_generic_contact.min.js which was auto compiled from _es6/pages/be_pages_generic_contact.js -->
        <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
        <div class="content content-full">
            <div class="row justify-content-center py-30">
                <div class="col-lg-12 text-center">
                    <label for="be-contact-name">Please fill up the form</label>
                    <hr>
                </div>
                <div class="col-lg-8 col-xl-6">
                    <form id="register_form" method="post">

                        <input type="hidden" class="form-control form-control-lg" id="plan" value="1">
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="be-contact-name">First name</label>
                                <input type="text" class="form-control form-control-lg" id="fname" name="fname" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="be-contact-name">Middle name</label>
                                <input type="text" class="form-control form-control-lg" id="mname" name="mname" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="be-contact-name">Last name</label>
                                <input type="text" class="form-control form-control-lg" id="lname" name="lname" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="be-contact-email">Email</label>
                            <div class="col-12">
                                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Enter your email.." required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-12" for="be-contact-email">Password</label>
                            <div class="col-12">
                                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Enter your password.." required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-hero btn-alt-primary min-width-175">
                                    <i class="fa fa-send mr-5"></i> Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- END Contact Form -->

    </main>
    <!-- END Main Container -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script>
        $(document).ready(function () {
            $("register_form").submit(function (event) {
                event.preventDefault();
                alert('test')
                // fname = $("#fname").val();
                // mname = $("#mname").val();
                // lname = $("#lname").val();
                // email = $("#email").val();
                // password = $("#password").val();
                // plan = $("#plan").val();
                $.ajax({
                    type:"post",
                    url:"ajax/register.php",
                    data:$(this).serialize(),
                    success:function(response){
                        if (response === "success") {
                            document.location = "index.php";
                        } else {
                            alert('Something went wrong');
                            location.reload();
                        }
                    }
                });
            }
        }
    </script>
<?php
include 'includes/footer.php';