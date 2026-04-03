<?php 
    $pageTitle = "Contact Us";
    $pageDescription = "Get in touch with the Society for Community & Communication Development Research (SCCDR). We'd love to hear from you.";
    include 'includes/header.php'; 
?>        <!-- ========================= header end ========================= -->

   <!-- ========================= hero-section start ========================= -->
        <section id="home" class="hero-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-10 text-center offset-1">
                        <div class="hero-content-wrapper">
                            <h1 class="text-white wow fadeInDown" data-wow-delay=".2s">CONTACT US</h1>
                            <p>
                                <a href="/" style="color:#7AD03A;">Home</a> / <span style="color:#ffffff;"> CONTACT US </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!--========================= feature-section start========================= -->
        <section id="feature" class="feature-section pt-80 pb-80">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-8">

                        <div class="row">
                            <div class="col-12 text-center">
                                <h3>Dear Visitor</h3>
                                <p>Please fill form to contact us</p>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-12 col-md-8 offset-md-2 text-left">
                               

                                <form method="POST" action="{{ route('contactform.store') }}">
                                    
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <input id="firstName" 
                                                style="border-radius: 30px !important;" 
                                                placeholder="First Name" 
                                                type="text" 
                                                class="form-control" 
                                                name="firstname" 
                                                autocomplete="firstname" 
                                                autofocus
                                            >
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-12">
                                            <input id="lastName" 
                                                style="border-radius: 30px !important;" 
                                                placeholder="Last Name" 
                                                type="text" 
                                                class="form-control" 
                                                name="lastname" 
                                                autocomplete="lastname" 
                                                autofocus
                                            >
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-12">
                                            <input id="email" 
                                                style="border-radius: 30px !important;" 
                                                placeholder="Email" 
                                                type="email" 
                                                class="form-control" name="email" 
                                                required 
                                                autocomplete="email" autofocus>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-12">
                                            <input id="phone" 
                                                style="border-radius: 30px !important;" 
                                                placeholder="Phone" 
                                                type="text" 
                                                class="form-control" 
                                                name="phone" 
                                                autocomplete="phone" 
                                                autofocus
                                            >
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-12">
                                            <label> Comment</label><br/>
                                            <textarea style="border-radius: 30px !important;" class="form-control" name="comments" rows="10"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0 mt-3">
                                        <div class="col-md-8">
                                        </div>
                                    </div>
                                

                                    <div class="form-group row mt-3">
                                        <div class="col-10 offset-1">
                                            <button type="submit" value="SCCDR | Send" class="theme-btn  wow fadeInUp">
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-12 col-md-10 offset-md-2 text-left">
                               <h5>Society for Community and Communication Development Research</h5>
                               <p class="lead" style="font-size:14px; color:#000000;">
                                   Department of Agricultural Extension and Rural Development <br/>
                                   Michael Okpara University of Agriculture, <br/>
                                   Umudike. PMB 7267 Umuahia Abia<br/>
                                   State, Nigeria. <br/>
                                   Email: <a href="mailto:info@sccdr.org" style="color:#000000;">info@sccdr.org</a><br/>
                                   Phone: <a href="tel:+2348035485064" style="color:#000000;">+2348035485064</a>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>
        <!--========================= feature-section end========================= -->
        <!--========================= Conferences & WorkShops end========================= -->
         
<?php include 'includes/footer.php'; ?>        <!-- ========================= footer end ========================= -->
