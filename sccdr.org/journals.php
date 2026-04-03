<?php 
    $pageTitle = "Research Journals";
    $pageDescription = "Explore the Journal of Community & Communication Research (JCCR) and other academic publications by SCCDR.";
    include 'includes/header.php'; 
?>        <!-- ========================= header end ========================= -->

      <section id="home" class="hero-section" style="background-image: url('/assets/img/slideImg2.jpeg'); background-size: cover; background-position: center;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-10 offset-lg-1 text-center">
                        <div class="hero-content-wrapper">
                            <h1 class="text-white wow fadeInDown" data-wow-delay=".2s">Research Journals</h1>
                            <div class="breadcrumb-wrapper wow fadeInUp" data-wow-delay=".4s">
                                <a href="/" style="color:var(--white); opacity: 0.8;">Home</a>
                                <span style="color:var(--white); opacity: 0.5; margin: 0 10px;">/</span>
                                <span style="color:var(--white);">Journals</span>
                            </div>
                            <div class="mt-40 wow fadeInUp" data-wow-delay=".6s">
                                <a href="https://jccr.sccdr.org/index.php/jccr/login" target="_blank" class="theme-btn-modern">Publish with JCCR</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!--========================= feature-section start========================= -->
        <section id="feature" class="feature-section pt-100 pb-100">
            <div class="container">
                <div class="row align-items-center mb-80">
                    <div class="col-lg-6 mb-50 mb-lg-0 text-center">
                        <div class="journal-image-wrapper wow zoomIn" data-wow-delay=".2s">
                            <img src="/assets/img/jccrImg.png" alt="JCCR Logo" class="img-fluid" style="border-radius: 25px; box-shadow: 0 30px 60px rgba(0,0,0,0.15); width: 100%; max-width: 450px; transition: var(--transition);">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="journal-content-wrapper wow fadeInRight" data-wow-delay=".4s" style="padding-left: 30px;">
                            <div class="section-modern-title text-left mb-30" style="text-align: left;">
                                <h4 style="text-transform: uppercase; letter-spacing: 3px; font-size: 14px; color: var(--primary); margin-bottom: 15px;">Academic Publications</h4>
                                <h1 style="font-size: 48px; margin-bottom: 25px;">JCCR Journal</h1>
                            </div>
                            <h3 class="mb-25" style="color: var(--secondary); font-size: 26px; font-weight: 700;">Journal of Community & Communication Research (JCCR)</h3>
                            <p class="mb-35" style="font-size: 17px; line-height: 1.8; color: var(--text-main); opacity: 0.9;">
                                The Journal of Community and Communication Research (JCCR) is a biannual open-access scholarly peer-reviewed journal 
                                that publishes original and empirically based research, reviews, editorials, and research notes. 
                                The JCCR is indexed by many leading services and has bright prospects for a high impact factor.
                            </p>
                            
                            <div class="row">
                                <div class="col-md-12 mb-30">
                                    <div class="feature-box p-4 border-radius-15" style="background: rgba(122, 208, 58, 0.05); border-radius: 15px; border-left: 5px solid var(--primary);">
                                        <h5 class="mb-10" style="font-size: 20px; color: var(--secondary);"><i class="fas fa-check-circle mr-10" style="color: var(--primary);"></i> Scope of Focus</h5>
                                        <p style="font-size: 15px; color: var(--text-muted);">Agriculture, Agricultural Extension, Rural Development, Communication, Education, Nutrition, Food Security, Climate Change, and ICT-in-Agriculture.</p>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-30">
                                    <div class="feature-box p-4 border-radius-15" style="background: rgba(58, 66, 78, 0.05); border-radius: 15px; border-left: 5px solid var(--secondary);">
                                        <h5 class="mb-10" style="font-size: 20px; color: var(--secondary);"><i class="fas fa-calendar-alt mr-10" style="color: var(--primary);"></i> Publication Cycle</h5>
                                        <p style="font-size: 15px; color: var(--text-muted);">Published bi-annually in the months of June and December.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<!-- 
                        <div class="row">
                            <div class="col-12">
                             <h4 class="mb-20 wow fadeInDown text-center" style="color:#000000 !important;" data-wow-delay=".2s">At Present, JCCR Has</h4>
                                <div class="row text-center">
                                    <div class="col-6 col-md-2 offset-md-1 text-center">
                                        <p><i class="fas fa-book" style="font-size:60px; color:#7AD03A;"></i></p>
                                        <h3 class="mt-2" style="color:#000000 !important;">{{$fetchAllSettings->articles}}</h3>
                                        <p>Articles</p>
                                    </div>

                                    <div class="col-6 col-md-2 text-center">
                                        <p><i class="fas fa-user-graduate" style="font-size:60px; color:#7AD03A;"></i></p>
                                        <h3 class="mt-2" style="color:#000000 !important;">{{$fetchAllSettings->authors}}</h3>
                                        <p>Authors</p>
                                    </div>

                                    <div class="col-6 col-md-2 text-center">
                                        <p><i class="fab fa-affiliatetheme" style="font-size:60px; color:#7AD03A;"></i></p>
                                        <h3 class="mt-2" style="color:#000000 !important;">{{$fetchAllSettings->volumes}}</h3>
                                        <p>Volumes</p>
                                    </div>

                                    <div class="col-6 col-md-2 text-center">
                                        <p><i class="fas fa-folder-open" style="font-size:60px; color:#7AD03A;"></i></p>
                                        <h3 class="mt-2" style="color:#000000 !important;">{{$fetchAllSettings->issues}}</h3>
                                        <p>Issues</p>
                                    </div>

                                    <div class="col-6 col-md-2 text-center">
                                        <p><i class="fas fa-user-edit" style="font-size:60px; color:#7AD03A;"></i></p>
                                        <h3 class="mt-2" style="color:#000000 !important;">{{$fetchAllSettings->reviewers}}</h3>
                                        <p>Reviewers</p>
                                    </div>

                                </div>
                            </div>
                        </div> -->

                <div class="row mt-60 text-center">
                    <div class="col-lg-4 col-md-6 mb-30">
                        <a href="https://jccr.sccdr.org/index.php/jccr/index" target="_blank" class="theme-btn-modern w-100 wow fadeInUp" data-wow-delay=".2s">
                            <i class="fas fa-file-upload mr-10"></i> Publish with JCCR
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-30">
                        <a href="tel:+2348060790069" class="theme-btn-modern w-100 wow fadeInUp" data-wow-delay=".4s" style="background: var(--secondary);">
                            <i class="fas fa-phone-alt mr-10"></i> Call JCCR
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-30">
                        <a href="mailto:infojccr@gmail.com" class="theme-btn-modern w-100 wow fadeInUp" data-wow-delay=".6s" style="background: var(--secondary);">
                            <i class="fas fa-envelope mr-10"></i> Email JCCR
                        </a>
                    </div>
                </div>
            </div>
        </section>

            </div>
        </section>
        <!--========================= feature-section end========================= -->

     
         
<?php include 'includes/footer.php'; ?>        <!-- ========================= footer end ========================= -->
