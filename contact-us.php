<?php 
    $pageTitle = "Contact Us";
    $pageDescription = "Get in touch with the Society for Community & Communication Development Research (SCCDR). We'd love to hear from you.";
    include 'includes/header.php'; 
?>        <!-- ========================= header end ========================= -->

    <!-- ========================= hero-section start ========================= -->
    <section id="home" class="hero-section" style="background-image: linear-gradient(rgba(11, 29, 18, 0.7), rgba(11, 29, 18, 0.6)), url('/assets/img/common-bg.jpg'); background-size: cover; background-position: center;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-10 offset-lg-1 text-center">
                    <div class="hero-content-wrapper">
                        <h1 class="text-white wow fadeInDown" data-wow-delay=".2s" style="font-size: 60px; text-shadow: 0 4px 10px rgba(0,0,0,0.3);">Get In Touch</h1>
                        <div class="breadcrumb-wrapper wow fadeInUp" data-wow-delay=".4s">
                            <a href="/index.php" style="color: var(--primary); font-weight: 600;">Home</a> 
                            <span class="mx-3 text-white">/</span> 
                            <span class="text-white opacity-70">Contact Us</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--========================= Contact section start========================= -->
    <section id="contact" class="contact-section pt-100 pb-100">
        <div class="container">
            <div class="row">
                <!-- Contact Info -->
                <div class="col-lg-4">
                    <div class="contact-info-wrapper">
                        <div class="section-modern-title text-left mb-50" style="text-align: left;">
                            <h4 class="wow fadeInUp" data-wow-delay=".2s">Contact Information</h4>
                            <h2 class="wow fadeInUp" data-wow-delay=".4s" style="font-size: 32px; color: var(--secondary);">Reach Out to Us</h2>
                        </div>

                        <div class="contact-card wow fadeInUp" data-wow-delay=".2s" style="background: #fff; padding: 30px; border-radius: 20px; box-shadow: var(--shadow-md); margin-bottom: 30px; border-left: 5px solid var(--primary);">
                            <div class="d-flex align-items-center">
                                <div class="icon-box mr-20" style="width: 50px; height: 50px; background: rgba(122, 208, 58, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h5 style="font-size: 18px; margin-bottom: 5px;">Our Location</h5>
                                    <p style="font-size: 14px; color: var(--text-muted);">MOUAU Extension Centre (MEC), Umudike.</p>
                                </div>
                            </div>
                        </div>

                        <div class="contact-card wow fadeInUp" data-wow-delay=".4s" style="background: #fff; padding: 30px; border-radius: 20px; box-shadow: var(--shadow-md); margin-bottom: 30px; border-left: 5px solid var(--secondary);">
                            <div class="d-flex align-items-center">
                                <div class="icon-box mr-20" style="width: 50px; height: 50px; background: rgba(58, 66, 78, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--secondary);">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h5 style="font-size: 18px; margin-bottom: 5px;">Email Us</h5>
                                    <p style="font-size: 14px; color: var(--text-muted);">info@sccdr.org.ng</p>
                                </div>
                            </div>
                        </div>

                        <div class="contact-card wow fadeInUp" data-wow-delay=".6s" style="background: #fff; padding: 30px; border-radius: 20px; box-shadow: var(--shadow-md); border-left: 5px solid var(--primary);">
                            <div class="d-flex align-items-center">
                                <div class="icon-box mr-20" style="width: 50px; height: 50px; background: rgba(122, 208, 58, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div>
                                    <h5 style="font-size: 18px; margin-bottom: 5px;">Call Support</h5>
                                    <p style="font-size: 14px; color: var(--text-muted);">+234 803 548 5064</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="contact-form-wrapper wow fadeInRight" data-wow-delay=".4s" style="background: #fff; padding: 50px; border-radius: 30px; box-shadow: var(--shadow-lg);">
                        <div class="section-modern-title text-left mb-40" style="text-align: left;">
                            <h2 style="font-size: 28px; color: var(--secondary);">Send a Message</h2>
                        </div>
                        
                        <div id="contact-alert" class="alert d-none mb-3" role="alert" style="border-radius: 15px; padding: 15px 25px;"></div>
                        <form action="#" method="POST" id="contact-form" onsubmit="handleContactForm(event)">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <input type="text" name="firstname" placeholder="First Name" style="width: 100%; padding: 15px 25px; border-radius: 30px; border: 1px solid #eee; background: #fdfdfd;" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <input type="text" name="lastname" placeholder="Last Name" style="width: 100%; padding: 15px 25px; border-radius: 30px; border: 1px solid #eee; background: #fdfdfd;" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <input type="email" name="email" placeholder="Email Address" style="width: 100%; padding: 15px 25px; border-radius: 30px; border: 1px solid #eee; background: #fdfdfd;" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-25">
                                        <input type="text" name="phone" placeholder="Phone Number" style="width: 100%; padding: 15px 25px; border-radius: 30px; border: 1px solid #eee; background: #fdfdfd;">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-40">
                                        <textarea name="comments" placeholder="Your Message" rows="6" style="width: 100%; padding: 20px 25px; border-radius: 20px; border: 1px solid #eee; background: #fdfdfd;"></textarea>
                                    </div>
                                </div>
                                <div class="col-12 text-right">
                                    <button type="submit" class="theme-btn-modern">
                                        <i class="fas fa-paper-plane mr-10"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--========================= Contact section end========================= -->
        <!--========================= feature-section end========================= -->
        <!--========================= Conferences & WorkShops end========================= -->
         
<?php include 'includes/footer.php'; ?>

<script>
async function handleContactForm(e) {
    e.preventDefault();
    const alert = document.getElementById('contact-alert');
    const btn = e.target.querySelector('button[type="submit"]');
    const form = document.getElementById('contact-form');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-10"></i> Sending...';

    const formData = new FormData(form);
    try {
        const res = await fetch('actions/contact.php', { method: 'POST', body: formData });
        const data = await res.json();

        alert.classList.remove('d-none', 'alert-success', 'alert-danger');
        alert.classList.add(data.status === 'success' ? 'alert-success' : 'alert-danger');
        alert.innerHTML = data.message;

        if (data.status === 'success') {
            form.reset();
        }
    } catch (err) {
        alert.classList.remove('d-none');
        alert.classList.add('alert-danger');
        alert.innerHTML = 'A network error occurred. Please try again.';
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-paper-plane mr-10"></i> Send Message';
}
</script>

