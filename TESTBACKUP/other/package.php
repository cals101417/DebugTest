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
                            <h1 class="font-w700 text-white mb-10">Access Plan</h1>
                            <h2 class="h4 font-w400 text-white-op">Step up your game with a better plan.</h2>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->

            <!-- Pricing Tables -->
            <div class="content">
                <div class="row py-30">
                    <div class="col-md-6 col-xl-3">
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <!-- Startup Plan -->
                        <div class="block block-link-pop block-rounded block-bordered text-center">
                            <div class="block-header">
                                <h3 class="block-title">Standard Plan</h3>
                            </div>
                            <div class="block-content bg-body-light">
                                <div class="h1 font-w700 mb-10">$39</div>
                                <div class="h5 text-muted">per month</div>
                            </div>
                            <div class="block-content">
                                <p><strong>10</strong> Projects</p>
                                <p><strong>30GB</strong> Storage</p>
                                <p><strong>100</strong> Clients</p>
                                <p><strong>FULL</strong> Support</p>
                            </div>
                            <div class="block-content block-content-full">
                                <a href="standard_plan.php" id="btn_standard_plan" class="btn btn-hero btn-sm btn-rounded btn-noborder btn-alt-primary">Current Plan</a>
                            </div>
                        </div>
                        <!-- END Startup Plan -->
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <!-- Developer Plan -->
                        <div class="block block-link-pop block-rounded block-bordered text-center">
                            <div class="block-header">
                                <h3 class="block-title font-w600">
                                    <i class="fa fa-check"></i> Premium Plan
                                </h3>
                            </div>
                            <div class="block-content bg-body-light">
                                <div class="h1 font-w700 text-primary mb-10">$19</div>
                                <div class="h5 text-muted">per month</div>
                            </div>
                            <div class="block-content">
                                <p><strong>2</strong> Projects</p>
                                <p><strong>10GB</strong> Storage</p>
                                <p><strong>15</strong> Clients</p>
                                <p><strong>Email</strong> Support</p>
                            </div>
                            <div class="block-content block-content-full">
                                <a href="premium_plan.php" id="btn_premium_plan" class="btn btn-hero btn-sm btn-rounded btn-noborder btn-primary">
                                    <i class="fa fa-arrow-up mr-5"></i> Create Now!
                                </a>
                            </div>
                        </div>
                        <!-- END Developer Plan -->
                    </div>
                    <div class="col-md-6 col-xl-3">
                    </div>
                </div>
            </div>
            <!-- END Pricing Tables -->

            <!-- Call to Action -->
<!--            <div class="bg-body-dark">-->
<!--                <div class="content">-->
<!--                    <div class="py-50 nice-copy text-center">-->
<!--                        <h3 class="font-w700 mb-10">Why Upgrade?</h3>-->
<!--                        <p>We value our customers and our goal is to help them achieve their dreams in any way we can. We work hard to save you time and get you up and running as soon as possible. By upgrading you will get tons of new features that can help you expand your business.</p>-->
<!--                        <a class="btn btn-hero btn-noborder btn-lg btn-rounded btn-primary" href="javascript:void(0)">Try a better plan</a>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
            <!-- END Call to Action -->

        </main>
        <!-- END Main Container -->

    <script src="assets/js/codebase.core.min.js"></script>
    <script src="assets/js/codebase.app.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#btn_standard_plan').click(function (){
                alert('standard');
            });
        }
    </script>
<?php
include 'includes/footer.php';