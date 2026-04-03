<?php 
    $pageTitle = "Member Portal";
    $pageDescription = "Sign in or join the Society for Community & Communication Development Research (SCCDR).";
    // For a split-screen immersive page, we might want a different header/footer, 
    // but we'll include the standard header and adapt the layout below it.
    include 'includes/header.php'; 
?>

<!-- Premium Immersive Auth Section -->
<section class="auth-split-screen d-flex flex-wrap" style="min-height: 100vh; background: #fdfdfd; margin-top: -80px; padding-top: 80px;">
    
    <!-- Left Side: Visual / Value Proposition -->
    <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-center" style="position: relative; overflow: hidden; background-image: url('/assets/img/kegvmlpzijjuuixh07nb.jpg'); background-size: cover; background-position: center;">
        
        <!-- Premium Gradient Overlay -->
        <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: linear-gradient(135deg, rgba(8, 30, 15, 0.9) 0%, rgba(122, 208, 58, 0.85) 100%); z-index: 1;"></div>
        
        <!-- Content -->
        <div style="position: relative; z-index: 2; padding: 60px; color: #fff;">
            <div style="display: flex; align-items: center; margin-bottom: 40px;">
                <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-right: 15px;">
                    <i class="fas fa-globe-africa"></i>
                </div>
                <h4 style="margin:0; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; font-size: 14px;">Global Network</h4>
            </div>
            
            <h1 style="font-size: 3.5rem; font-weight: 800; line-height: 1.1; margin-bottom: 25px; text-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                Advance the<br>Frontier of<br>Research.
            </h1>
            <p style="font-size: 1.1rem; line-height: 1.8; color: #fff; opacity: 1; max-width: 400px; margin-bottom: 50px;">
                Join thousands of scholars and practitioners driving sustainable community development through communication and collaboration.
            </p>
            
            <!-- Mini Review / Trust Badge -->
            <div style="display: flex; align-items: center; background: rgba(255,255,255,0.1); backdrop-filter: blur(15px); padding: 20px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.2); max-width: 400px;">
                <div style="display: flex; margin-right: 15px;">
                    <img src="/assets/img/team/IKE2.png" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #fff; margin-right: -15px;">
                    <img src="/assets/img/team/ekanem.png" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #fff; margin-right: -15px;">
                    <img src="/assets/img/team/yinka3.png" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #fff;">
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 14px;">5,000+ Members</div>
                    <div style="font-size: 12px; opacity: 0.8;">Working globally</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Forms -->
    <div class="col-lg-7 d-flex align-items-center justify-content-center" style="padding: 60px 40px;">
        
        <div style="width: 100%; max-width: 550px;">
            
            <!-- Header & Tab Toggle -->
            <div class="text-center mb-40">
                <h2 style="font-weight: 800; color: var(--heading-color); margin-bottom: 10px; font-size: 32px;">Welcome to SCCDR</h2>
                <p style="color: #64748b; font-size: 15px;">Please authenticate to access your professional dashboard.</p>
                
                <!-- Clear Button Switcher -->
                <div class="d-flex justify-content-center mb-40">
                    <div style="background: #f8fafc; padding: 5px; border-radius: 50px; border: 1px solid #e2e8f0; display: inline-flex;">
                        <button id="btn-login" onclick="switchTab('login')" type="button" class="btn" style="border-radius: 50px; padding: 12px 35px; font-weight: 700; background: var(--primary-color); color: #fff; box-shadow: 0 4px 10px rgba(122,208,58,0.2);">Sign In</button>
                        <button id="btn-register" onclick="switchTab('register')" type="button" class="btn" style="border-radius: 50px; padding: 12px 35px; font-weight: 600; background: transparent; color: #64748b;">Sign Up</button>
                    </div>
                </div>
            </div>

            <!-- Login Form -->
            <div id="form-login" class="auth-pane active-pane">
                <div id="login-alert" class="alert d-none mt-2 mb-3" role="alert"></div>
                <form id="loginForm" onsubmit="handleLogin(event)">
                    <div class="form-floating mb-4">
                        <label style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px;">Email Address</label>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="loginEmail" class="form-control modern-input" placeholder="name@institution.edu" required>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin:0;">Password</label>
                            <a href="#" style="font-size: 12px; color: var(--primary-color); font-weight: 700; text-decoration: none;">Forgot Password?</a>
                        </div>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="loginPassword" class="form-control modern-input" placeholder="••••••••" required>
                            <i class="fas fa-eye input-icon-right" style="cursor: pointer; color: #94a3b8;" onclick="togglePassword('loginPassword')"></i>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-40">
                        <label class="custom-checkbox-wrapper">
                            <input type="checkbox" id="rememberMe">
                            <span class="checkmark"></span>
                        </label>
                        <span style="font-size: 14px; color: #64748b; font-weight: 500; margin-left: 10px; cursor: pointer;" onclick="document.getElementById('rememberMe').click();">Keep me signed in</span>
                    </div>
                    
                    <button type="submit" id="btnLoginSubmit" class="btn-auth-primary" style="background: #7AD03A; color: #fff;">
                        Sign In <i class="fas fa-arrow-right ml-10"></i>
                    </button>
                </form>
            </div>

            <!-- Registration Form -->
            <div id="form-register" class="auth-pane">
                <div id="register-alert" class="alert d-none mt-2 mb-3" role="alert"></div>
                <form id="registerForm" onsubmit="handleRegister(event)" enctype="multipart/form-data">
                    <!-- Profile Picture Picker -->
                    <div class="mb-4" style="text-align:center;">
                        <label style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 12px;">Profile Picture <span style="color:#94a3b8; font-weight:400; text-transform:none; letter-spacing:0;">(optional)</span></label>
                        <div id="avatarDropZone" onclick="document.getElementById('regProfilePic').click()"
                             ondragover="avatarDragOver(event)" ondragleave="avatarDragLeave(event)" ondrop="avatarDrop(event)"
                             style="width:110px; height:110px; border-radius:50%; border:2.5px dashed #d1d5db; background:#f8fafc; display:flex; align-items:center; justify-content:center; cursor:pointer; margin:0 auto; overflow:hidden; transition:border-color 0.2s, box-shadow 0.2s; position:relative;">
                            <div id="avatarPlaceholder" style="text-align:center; pointer-events:none;">
                                <i class="fas fa-camera" style="font-size:26px; color:#cbd5e1; display:block; margin-bottom:4px;"></i>
                                <span style="font-size:10px; color:#94a3b8; font-weight:600;">Upload photo</span>
                            </div>
                            <img id="avatarPreview" src="" alt="Preview" style="display:none; width:100%; height:100%; object-fit:cover; border-radius:50%;">
                        </div>
                        <input type="file" id="regProfilePic" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;" onchange="avatarPreview(event)">
                        <p style="font-size:11px; color:#94a3b8; margin-top:8px;">JPG, PNG or WebP — max 2MB</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px;">First Name</label>
                            <div class="input-modern-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="regFirstName" class="form-control modern-input" placeholder="Jane" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px;">Last Name</label>
                            <div class="input-modern-wrapper">
                                <input type="text" id="regLastName" class="form-control modern-input" placeholder="Doe" style="padding-left: 20px;" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px;">Email Address</label>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="regEmail" class="form-control modern-input" placeholder="name@institution.edu" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px;">Institution / Organization</label>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-university input-icon"></i>
                            <input type="text" id="regInstitution" class="form-control modern-input" placeholder="e.g. University of Lagos">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px;">Membership Category</label>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-medal input-icon"></i>
                            <select id="regCategory" class="form-control modern-input" style="appearance: none; cursor: pointer;">
                                <option value="Student Member">Student Member</option>
                                <option value="Professional Member">Professional Member</option>
                                <option value="Institutional Member">Institutional Member</option>
                                <option value="Fellow (FSCCDR)">Fellow (FSCCDR)</option>
                            </select>
                            <i class="fas fa-chevron-down input-icon-right" style="pointer-events: none; color: #94a3b8;"></i>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px;">Password</label>
                            <div class="input-modern-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="regPassword" class="form-control modern-input" placeholder="••••••••" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label style="font-weight: 700; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px;">Confirm</label>
                            <div class="input-modern-wrapper">
                                <input type="password" id="regPasswordConfirm" class="form-control modern-input" placeholder="••••••••" style="padding-left: 20px;" required>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" id="btnRegSubmit" class="btn-auth-primary" style="background: #144525; color: #fff;">
                        Sign Up <i class="fas fa-check-circle ml-10"></i>
                    </button>
                    
                    <p class="text-center mt-3" style="font-size: 12px; color: #94a3b8;">
                        By creating an account, you agree to our <a href="#" style="color: var(--primary-color);">Terms of Service</a>.
                    </p>
                </form>
            </div>

        </div>
    </div>
</section>

<style>
    /* Premium Authentication Component Styles */
    
    .auth-pane {
        display: none;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .auth-pane.active-pane {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }
    
    .input-modern-wrapper {
        position: relative;
    }
    
    .modern-input {
        background: #f8fafc !important;
        border: 2px solid transparent !important;
        border-radius: 16px !important;
        padding: 16px 20px 16px 50px !important;
        font-size: 15px !important;
        font-weight: 500 !important;
        color: var(--heading-color) !important;
        transition: all 0.3s ease !important;
        height: auto !important;
        box-shadow: none !important;
    }
    
    .modern-input:focus {
        background: #fff !important;
        border-color: var(--primary-color) !important;
        box-shadow: 0 10px 25px rgba(122, 208, 58, 0.15) !important;
    }
    
    .input-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 18px;
        transition: color 0.3s;
        z-index: 2;
    }
    
    .input-icon-right {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
        z-index: 2;
    }
    
    .modern-input:focus ~ .input-icon {
        color: var(--primary-color);
    }
    
    .btn-auth-primary {
        width: 100%;
        background: linear-gradient(135deg, var(--primary-color) 0%, rgba(102, 188, 38, 1) 100%);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 18px 20px;
        font-size: 16px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 10px 30px rgba(122, 208, 58, 0.3);
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        cursor: pointer;
    }
    
    .btn-auth-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(122, 208, 58, 0.4);
    }
    .btn-auth-primary:active {
        transform: translateY(1px);
    }
    
    /* Custom Checkbox */
    .custom-checkbox-wrapper {
        display: block;
        position: relative;
        cursor: pointer;
        font-size: 22px;
        user-select: none;
        width: 24px;
        height: 24px;
    }
    
    .custom-checkbox-wrapper input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 24px;
        width: 24px;
        background-color: #f1f5f9;
        border-radius: 6px;
        border: 2px solid #e2e8f0;
        transition: all 0.2s;
    }
    
    .custom-checkbox-wrapper:hover input ~ .checkmark {
        background-color: #e2e8f0;
    }
    
    .custom-checkbox-wrapper input:checked ~ .checkmark {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }
    
    .custom-checkbox-wrapper input:checked ~ .checkmark:after {
        display: block;
    }
    
    .custom-checkbox-wrapper .checkmark:after {
        left: 7px;
        top: 3px;
        width: 6px;
        height: 12px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    
    /* Responsive Adjustments */
    @media (max-width: 991px) {
        .auth-split-screen {
            padding-top: 120px;
        }
    }
</style>

<script>
    function switchTab(tab) {
        const btnLogin = document.getElementById('btn-login');
        const btnRegister = document.getElementById('btn-register');
        const formLogin = document.getElementById('form-login');
        const formRegister = document.getElementById('form-register');
        
        // Remove active class to trigger exit animation
        formLogin.classList.remove('active-pane');
        formRegister.classList.remove('active-pane');
        
        setTimeout(() => {
            if (tab === 'login') {
                btnLogin.style.background = 'var(--primary-color)';
                btnLogin.style.color = '#fff';
                btnLogin.style.fontWeight = '700';
                btnLogin.style.boxShadow = '0 4px 10px rgba(122,208,58,0.2)';
                
                btnRegister.style.background = 'transparent';
                btnRegister.style.color = '#64748b';
                btnRegister.style.fontWeight = '600';
                btnRegister.style.boxShadow = 'none';
                
                formLogin.style.display = 'block';
                formRegister.style.display = 'none';
                
                setTimeout(() => formLogin.classList.add('active-pane'), 10);
                
            } else {
                btnRegister.style.background = 'var(--primary-color)';
                btnRegister.style.color = '#fff';
                btnRegister.style.fontWeight = '700';
                btnRegister.style.boxShadow = '0 4px 10px rgba(122,208,58,0.2)';
                
                btnLogin.style.background = 'transparent';
                btnLogin.style.color = '#64748b';
                btnLogin.style.fontWeight = '600';
                btnLogin.style.boxShadow = 'none';
                
                formLogin.style.display = 'none';
                formRegister.style.display = 'block';
                
                setTimeout(() => formRegister.classList.add('active-pane'), 10);
            }
        }, 150);
    }
    
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }

    async function handleLogin(e) {
        e.preventDefault();
        const alertBox = document.getElementById('login-alert');
        const btn = document.getElementById('btnLoginSubmit');
        
        const formData = new FormData();
        formData.append('action', 'login');
        formData.append('email', document.getElementById('loginEmail').value);
        formData.append('password', document.getElementById('loginPassword').value);

        btn.disabled = true;
        btn.innerHTML = 'Signing In...';

        try {
            const response = await fetch('actions/auth.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            alertBox.classList.remove('d-none', 'alert-danger', 'alert-success');
            if (data.status === 'success') {
                alertBox.classList.add('alert-success');
                alertBox.innerHTML = data.message;
                setTimeout(() => { window.location.href = data.redirect || 'dashboard.php'; }, 1000);
            } else {
                alertBox.classList.add('alert-danger');
                alertBox.innerHTML = data.message;
                btn.disabled = false;
                btn.innerHTML = 'Sign In <i class="fas fa-arrow-right ml-10"></i>';
            }
        } catch (error) {
            alertBox.classList.remove('d-none');
            alertBox.classList.add('alert-danger');
            alertBox.innerHTML = 'A network error occurred. Please try again.';
            btn.disabled = false;
            btn.innerHTML = 'Sign In <i class="fas fa-arrow-right ml-10"></i>';
        }
    }

    async function handleRegister(e) {
        e.preventDefault();
        const alertBox = document.getElementById('register-alert');
        const btn = document.getElementById('btnRegSubmit');
        
        const password = document.getElementById('regPassword').value;
        const confirm = document.getElementById('regPasswordConfirm').value;
        
        if(password !== confirm) {
            alertBox.classList.remove('d-none', 'alert-success');
            alertBox.classList.add('alert-danger');
            alertBox.innerHTML = 'Passwords do not match.';
            return;
        }

        const picInput = document.getElementById('regProfilePic');
        const formData = new FormData();
        formData.append('action', 'register');
        formData.append('first_name', document.getElementById('regFirstName').value);
        formData.append('last_name', document.getElementById('regLastName').value);
        formData.append('email', document.getElementById('regEmail').value);
        formData.append('institution', document.getElementById('regInstitution').value);
        formData.append('category', document.getElementById('regCategory').value);
        formData.append('password', password);
        formData.append('password_confirm', confirm);
        if (picInput.files[0]) formData.append('profile_picture', picInput.files[0]);

        btn.disabled = true;
        btn.innerHTML = 'Creating Account...';

        try {
            const response = await fetch('actions/auth.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            alertBox.classList.remove('d-none', 'alert-danger', 'alert-success');
            if (data.status === 'success') {
                alertBox.classList.add('alert-success');
                alertBox.innerHTML = data.message;
                setTimeout(() => { window.location.href = data.redirect || 'dashboard.php'; }, 1000);
            } else {
                alertBox.classList.add('alert-danger');
                alertBox.innerHTML = data.message;
                btn.disabled = false;
                btn.innerHTML = 'Sign Up <i class="fas fa-check-circle ml-10"></i>';
            }
        } catch (error) {
            alertBox.classList.remove('d-none');
            alertBox.classList.add('alert-danger');
            alertBox.innerHTML = 'A network error occurred. Please try again.';
            btn.disabled = false;
            btn.innerHTML = 'Sign Up <i class="fas fa-check-circle ml-10"></i>';
        }
    }

    // ── Profile picture picker helpers ──────────────────────────────────────
    function avatarPreview(e) {
        const file = e.target.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('Image must be under 2MB.');
            e.target.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = ev => {
            document.getElementById('avatarPlaceholder').style.display = 'none';
            const img = document.getElementById('avatarPreview');
            img.src = ev.target.result;
            img.style.display = 'block';
            document.getElementById('avatarDropZone').style.borderColor = 'var(--primary-color)';
            document.getElementById('avatarDropZone').style.boxShadow = '0 0 0 4px rgba(122,208,58,0.12)';
        };
        reader.readAsDataURL(file);
    }

    function avatarDragOver(e) {
        e.preventDefault();
        document.getElementById('avatarDropZone').style.borderColor = 'var(--primary-color)';
        document.getElementById('avatarDropZone').style.background = 'rgba(122,208,58,0.05)';
    }

    function avatarDragLeave(e) {
        document.getElementById('avatarDropZone').style.background = '#f8fafc';
    }

    function avatarDrop(e) {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const input = document.getElementById('regProfilePic');
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            avatarPreview({ target: input });
        }
    }
</script>

<?php include 'includes/footer.php'; ?>

