<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php echo isset($pageTitle) ? $pageTitle . " | SCCDR" : "SCCDR - Society for Community & Communication Development Research"; ?></title>
        <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : "A society providing research based solutions that will ensure sustainable community development."; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="canonical" href="<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle : "SCCDR - Society for Community & Communication Development Research"; ?>" />
        <meta property="og:description" content="<?php echo isset($pageDescription) ? $pageDescription : "A society providing research based solutions that will ensure sustainable community development."; ?>" />
        <meta property="og:url" content="<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" />
        <meta property="og:site_name" content="SCCDR - Society for Community & Communication Development Research" />
        <meta property="og:image" content="/assets/img/logo.png" />
        
        <link rel="shortcut icon" type="image/x-icon" href="/assets/img/favicon.png">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        
		<!-- ========================= CSS here ========================= -->
		<link rel="stylesheet" href="/assets/css/bootstrap-5.0.0-alpha-1.min.css">
        <link rel="stylesheet" href="/assets/css/LineIcons.2.0.css">
        <link rel="stylesheet" href="/assets/css/animate.css">
        <link rel="stylesheet" href="/assets/css/main.css">
        <link rel="stylesheet" href="/assets/css/redesign.css">
    </head>
    <body>
        <!-- ========================= preloader start ========================= -->
            <div class="preloader d-none">
                <div class="loader">
                    <div class="ytp-spinner">
                        <div class="ytp-spinner-container">
                            <div class="ytp-spinner-rotator">
                                <div class="ytp-spinner-left">
                                    <div class="ytp-spinner-circle"></div>
                                </div>
                                <div class="ytp-spinner-right">
                                    <div class="ytp-spinner-circle"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- preloader end -->

        <!-- ========================= header start ========================= -->
  <header class="header navbar-area sticky">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <nav class="navbar navbar-expand-lg">
                    <a class="navbar-brand" href="/">
                        <img src="/assets/img/logo.png" alt="Logo">
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="toggler-icon"></span>
                        <span class="toggler-icon"></span>
                        <span class="toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                        <ul id="nav" class="navbar-nav ml-auto">
                             <li class="nav-item">
                                <a href="/">Home</a>
                            </li>
                            <li class="nav-item"> 
                                <a class="" href="/journals">Journals</a>
                            </li>
                            <li class="nav-item"> 
                                <a class="" href="/resources">Resources</a>
                            </li>
                            <li class="nav-item">
                                <div class="dropdown show">
                                    <a class="" href="/about-sccdr" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                                        About Us <i class="fas fa-angle-down"></i>
                                    </a>

                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                        <a class="dropdown-item" style="color:black; padding-left:10px;" href="/about-sccdr">The Story</a>
                                        <a class="dropdown-item" style="color:black; padding-left:10px;" href="/our-values">Our Values</a>
                                        <a class="dropdown-item" style="color:black; padding-left:10px;" href="/board-members">Board</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item"> 
                                <a class="" href="/contact-us">Contact Us</a>
                            </li>
                            <li class="nav-item"> 
                                <a class="nav-btn" href="/admin" style="background: var(--primary); color: white; padding: 8px 20px; border-radius: 30px; margin-left: 15px;">Admin Login</a>
                            </li>
                        </ul>
                    </div> <!-- navbar collapse -->
                </nav> <!-- navbar -->
            </div>
        </div> <!-- row -->
    </div> <!-- container -->

</header>
<script>
    window.addEventListener('scroll', function() {
        const header = document.querySelector('.header.navbar-area');
        if (window.scrollY > 50) {
            header.classList.add('sticky');
        } else {
            header.classList.remove('sticky');
        }
    });
</script>
<!-- ========================= header end ========================= -->
