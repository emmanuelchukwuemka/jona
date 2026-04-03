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
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="toggler-icon"></span>
                        <span class="toggler-icon"></span>
                        <span class="toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                        <ul id="nav" class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a href="/index.php">Home</a>
                            </li>
                            <li class="nav-item"> 
                                <a class="" href="/journals.php">Journals</a>
                            </li>
                            <li class="nav-item"> 
                                <a class="" href="/resources.php">Resources</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    About Us <i class="fas fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu shadow-lg border-0" aria-labelledby="navbarDropdown" style="border-radius: 15px; padding: 15px; min-width: 200px; background: rgba(255,255,255,0.98); backdrop-filter: blur(10px);">
                                    <li><a class="dropdown-item mb-2" style="border-radius: 8px; padding: 10px 15px; transition: var(--transition);" href="/about-sccdr.php">The Story</a></li>
                                    <li><a class="dropdown-item mb-2" style="border-radius: 8px; padding: 10px 15px; transition: var(--transition);" href="/our-values.php">Our Values</a></li>
                                    <li><a class="dropdown-item" style="border-radius: 8px; padding: 10px 15px; transition: var(--transition);" href="/board-members.php">Board</a></li>
                                </ul>
                            </li>
                            <li class="nav-item"> 
                                <a class="" href="/contact-us.php">Contact Us</a>
                            </li>
                            <li class="nav-item"> 
                                <a class="nav-btn" href="/membership.php" style="background: var(--primary); color: white; padding: 8px 20px; border-radius: 30px; margin-left: 15px;">Member Portal</a>
                            </li>
                            <li class="nav-item"> 
                                <a class="nav-btn" href="/admin/index.php" style="background: transparent; color: var(--heading-color); border: 1px solid var(--heading-color); padding: 7px 18px; border-radius: 30px; margin-left: 10px;">Admin Login</a>
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
