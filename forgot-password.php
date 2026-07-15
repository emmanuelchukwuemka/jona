<?php
$pageTitle       = "Forgot Password";
$pageDescription = "Reset your SCCDR member account password.";
include 'includes/header.php';
?>

<!-- ═══════════════════════════════════════════════════════════════════
     FORGOT PASSWORD — split-screen matching membership.php design
════════════════════════════════════════════════════════════════════ -->
<section class="auth-split-screen d-flex flex-wrap" style="min-height:100vh; background:#fdfdfd; margin-top:-80px; padding-top:80px;">

    <!-- Left: Visual panel -->
    <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-center"
         style="position:relative; overflow:hidden; background-image:url('/assets/img/kegvmlpzijjuuixh07nb.jpg'); background-size:cover; background-position:center;">
        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(135deg,rgba(8,30,15,0.9) 0%,rgba(122,208,58,0.85) 100%);z-index:1;"></div>
        <div style="position:relative;z-index:2;padding:60px;color:#fff;">
            <div style="display:flex;align-items:center;margin-bottom:40px;">
                <div style="width:50px;height:50px;background:rgba(255,255,255,0.2);backdrop-filter:blur(10px);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:15px;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4 style="margin:0;font-weight:700;letter-spacing:2px;text-transform:uppercase;font-size:14px;">Account Security</h4>
            </div>
            <h1 style="font-size:3.2rem;font-weight:800;line-height:1.15;margin-bottom:25px;text-shadow:0 10px 30px rgba(0,0,0,0.2);">
                Secure &amp;<br>Easy Account<br>Recovery.
            </h1>
            <p style="font-size:1.05rem;line-height:1.8;opacity:1;max-width:400px;margin-bottom:50px;">
                Enter your registered email and we'll send a 6-digit verification code to reset your password safely.
            </p>
            <!-- Steps guide -->
            <div style="display:flex;flex-direction:column;gap:16px;max-width:360px;">
                <?php foreach([
                    ['1','Enter your email address','fa-envelope'],
                    ['2','Check your inbox for the code','fa-inbox'],
                    ['3','Enter the code & new password','fa-key'],
                ] as [$n,$txt,$ico]): ?>
                <div style="display:flex;align-items:center;gap:14px;background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);padding:14px 18px;border-radius:14px;border:1px solid rgba(255,255,255,0.15);">
                    <div style="width:34px;height:34px;background:#7AD03A;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;flex-shrink:0;"><?= $n ?></div>
                    <span style="font-size:14px;font-weight:500;"><i class="fas <?= $ico ?>" style="margin-right:8px;opacity:.7;"></i><?= $txt ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Right: Forms -->
    <div class="col-lg-7 d-flex align-items-center justify-content-center" style="padding:60px 40px;">
        <div style="width:100%;max-width:520px;">

            <div class="text-center mb-40">
                <div style="width:64px;height:64px;background:linear-gradient(135deg,#7AD03A,#144525);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;box-shadow:0 10px 30px rgba(122,208,58,0.3);">
                    <i class="fas fa-lock" style="font-size:26px;color:#fff;"></i>
                </div>
                <h2 style="font-weight:800;color:var(--heading-color);margin-bottom:8px;font-size:28px;">Reset Your Password</h2>
                <p style="color:#64748b;font-size:15px;">We'll send a 6-digit code to your email.</p>
            </div>

            <!-- ── STEP 1: Request OTP ── -->
            <div id="step-request">
                <div id="req-alert" class="alert d-none mb-3" role="alert"></div>
                <form id="requestForm" onsubmit="sendOTP(event)">

                    <div class="mb-4">
                        <label class="fp-label">Email Address</label>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="fpEmail" class="form-control modern-input"
                                   placeholder="name@institution.edu" required
                                   value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
                        </div>
                    </div>

                    <button type="submit" id="btnSendCode" class="btn-auth-primary" style="background:#7AD03A;color:#fff;">
                        Send Reset Code &nbsp;<i class="fas fa-paper-plane"></i>
                    </button>

                    <p class="text-center mt-4" style="font-size:14px;color:#64748b;">
                        Remembered it? <a href="/membership.php" style="color:var(--primary-color);font-weight:700;text-decoration:none;">Back to Sign In</a>
                    </p>
                </form>
            </div>

            <!-- ── STEP 2: Enter OTP + New Password ── -->
            <div id="step-reset" style="display:none;">
                <div id="rst-alert" class="alert d-none mb-3" role="alert"></div>

                <!-- OTP digit boxes -->
                <div class="text-center mb-4">
                    <p style="color:#64748b;font-size:14px;margin-bottom:18px;">
                        Enter the 6-digit code sent to <strong id="sentToEmail"></strong>
                    </p>
                    <div id="otpBoxes" style="display:flex;justify-content:center;gap:10px;">
                        <?php for($i=0;$i<6;$i++): ?>
                        <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                               class="otp-box"
                               id="otp<?= $i ?>"
                               oninput="otpInput(this,<?= $i ?>)"
                               onkeydown="otpBack(event,<?= $i ?>)"
                               onpaste="otpPaste(event)">
                        <?php endfor; ?>
                    </div>
                    <p style="margin-top:14px;font-size:13px;color:#94a3b8;">
                        Didn't get it?
                        <button type="button" id="btnResend" onclick="resendCode()" style="background:none;border:none;color:var(--primary-color);font-weight:700;cursor:pointer;font-size:13px;padding:0;">
                            Resend code
                        </button>
                        <span id="resendTimer" style="color:#94a3b8;font-size:12px;display:none;"></span>
                    </p>
                </div>

                <form id="resetForm" onsubmit="resetPassword(event)">
                    <input type="hidden" id="resetEmail">

                    <div class="mb-4">
                        <label class="fp-label">New Password</label>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="newPassword" class="form-control modern-input"
                                   placeholder="At least 6 characters" required minlength="6">
                            <i class="fas fa-eye input-icon-right" style="cursor:pointer;color:#94a3b8;"
                               onclick="togglePwd('newPassword')"></i>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="fp-label">Confirm New Password</label>
                        <div class="input-modern-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" id="confirmPassword" class="form-control modern-input"
                                   placeholder="Repeat password" required minlength="6">
                            <i class="fas fa-eye input-icon-right" style="cursor:pointer;color:#94a3b8;"
                               onclick="togglePwd('confirmPassword')"></i>
                        </div>
                    </div>

                    <!-- Strength meter -->
                    <div id="strengthWrap" style="margin:-8px 0 18px;display:none;">
                        <div style="height:4px;background:#e2e8f0;border-radius:4px;overflow:hidden;">
                            <div id="strengthBar" style="height:100%;width:0;transition:width .3s,background .3s;border-radius:4px;"></div>
                        </div>
                        <p id="strengthLabel" style="font-size:11px;margin-top:4px;font-weight:600;"></p>
                    </div>

                    <button type="submit" id="btnReset" class="btn-auth-primary" style="background:#144525;color:#fff;">
                        Set New Password &nbsp;<i class="fas fa-check-circle"></i>
                    </button>

                    <p class="text-center mt-3" style="font-size:13px;color:#94a3b8;">
                        <a href="#" onclick="showStep1()" style="color:var(--primary-color);font-weight:700;text-decoration:none;">
                            &larr; Use a different email
                        </a>
                    </p>
                </form>
            </div>

        </div>
    </div>
</section>

<!-- ═══════════════ CSS ═══════════════ -->
<style>
/* Re-use membership.php design tokens */
.fp-label {
    font-weight: 700;
    color: #475569;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    margin-bottom: 8px;
}

/* OTP boxes */
.otp-box {
    width: 52px;
    height: 60px;
    text-align: center;
    font-size: 22px;
    font-weight: 800;
    color: #1e293b;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    outline: none;
    transition: border-color .2s, box-shadow .2s, transform .15s;
    caret-color: #7AD03A;
}
.otp-box:focus {
    border-color: #7AD03A;
    box-shadow: 0 0 0 4px rgba(122,208,58,0.15);
    transform: translateY(-2px);
    background: #fff;
}
.otp-box.filled {
    border-color: #7AD03A;
    background: rgba(122,208,58,0.06);
}

/* Inherit these from membership.php (already global) */
.input-modern-wrapper { position: relative; }
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
    box-shadow: 0 10px 25px rgba(122,208,58,.15) !important;
}
.input-icon {
    position: absolute; left: 20px; top: 50%;
    transform: translateY(-50%); color: #94a3b8;
    font-size: 18px; transition: color .3s; z-index: 2;
}
.input-icon-right {
    position: absolute; right: 20px; top: 50%;
    transform: translateY(-50%); color: #94a3b8;
    font-size: 16px; z-index: 2;
}
.btn-auth-primary {
    width: 100%;
    border: none;
    border-radius: 16px;
    padding: 18px 20px;
    font-size: 16px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 10px 30px rgba(122,208,58,.3);
    transition: all .3s cubic-bezier(.165,.84,.44,1);
    cursor: pointer;
}
.btn-auth-primary:hover  { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(122,208,58,.4); }
.btn-auth-primary:active { transform: translateY(1px); }

@media (max-width: 420px) {
    .otp-box { width: 42px; height: 52px; font-size: 18px; }
}
</style>

<!-- ═══════════════ JS ═══════════════ -->
<script>
let resendCountdown;

/* ── Show step 1 ── */
function showStep1() {
    document.getElementById('step-reset').style.display = 'none';
    document.getElementById('step-request').style.display = 'block';
}

/* ── Show step 2 ── */
function showStep2(email) {
    document.getElementById('sentToEmail').textContent = email;
    document.getElementById('resetEmail').value = email;
    document.getElementById('step-request').style.display = 'none';
    const s2 = document.getElementById('step-reset');
    s2.style.display = 'block';
    document.getElementById('otp0').focus();
    startResendTimer(60);
}

/* ── Send OTP ── */
async function sendOTP(e) {
    e.preventDefault();
    const alertBox = document.getElementById('req-alert');
    const btn      = document.getElementById('btnSendCode');
    const email    = document.getElementById('fpEmail').value.trim();

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>&nbsp; Sending…';

    const fd = new FormData();
    fd.append('step', 'request');
    fd.append('email', email);

    try {
        const res  = await fetch('/actions/forgot_password.php', { method: 'POST', body: fd });
        const data = await res.json();
        alertBox.classList.remove('d-none','alert-danger','alert-success');

        if (data.status === 'success') {
            alertBox.classList.add('alert-success');
            alertBox.innerHTML = data.message;
            setTimeout(() => showStep2(email), 900);
        } else {
            alertBox.classList.add('alert-danger');
            alertBox.innerHTML = data.message;
            btn.disabled = false;
            btn.innerHTML = 'Send Reset Code &nbsp;<i class="fas fa-paper-plane"></i>';
        }
    } catch {
        alertBox.classList.remove('d-none');
        alertBox.classList.add('alert-danger');
        alertBox.innerHTML = 'Network error. Please try again.';
        btn.disabled = false;
        btn.innerHTML = 'Send Reset Code &nbsp;<i class="fas fa-paper-plane"></i>';
    }
}

/* ── Resend Code ── */
async function resendCode() {
    const email = document.getElementById('resetEmail').value || document.getElementById('fpEmail').value;
    document.getElementById('btnResend').disabled = true;

    const fd = new FormData();
    fd.append('step', 'request');
    fd.append('email', email);

    const res  = await fetch('/actions/forgot_password.php', { method: 'POST', body: fd });
    const data = await res.json();
    const alertBox = document.getElementById('rst-alert');
    alertBox.classList.remove('d-none','alert-danger','alert-success');
    alertBox.classList.add(data.status === 'success' ? 'alert-success' : 'alert-danger');
    alertBox.innerHTML = data.message;

    if (data.status === 'success') {
        clearOTP();
        startResendTimer(60);
    } else {
        document.getElementById('btnResend').disabled = false;
    }
}

/* ── Reset Password ── */
async function resetPassword(e) {
    e.preventDefault();
    const alertBox = document.getElementById('rst-alert');
    const btn      = document.getElementById('btnReset');
    const email    = document.getElementById('resetEmail').value;
    const code     = getOTPValue();
    const password = document.getElementById('newPassword').value;
    const confirm  = document.getElementById('confirmPassword').value;

    if (code.length < 6) {
        alertBox.classList.remove('d-none','alert-success');
        alertBox.classList.add('alert-danger');
        alertBox.innerHTML = 'Please enter the complete 6-digit code.';
        return;
    }
    if (password !== confirm) {
        alertBox.classList.remove('d-none','alert-success');
        alertBox.classList.add('alert-danger');
        alertBox.innerHTML = 'Passwords do not match.';
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>&nbsp; Resetting…';

    const fd = new FormData();
    fd.append('step', 'reset');
    fd.append('email', email);
    fd.append('code', code);
    fd.append('password', password);
    fd.append('confirm', confirm);

    try {
        const res  = await fetch('/actions/forgot_password.php', { method: 'POST', body: fd });
        const data = await res.json();
        alertBox.classList.remove('d-none','alert-danger','alert-success');

        if (data.status === 'success') {
            alertBox.classList.add('alert-success');
            alertBox.innerHTML = '<i class="fas fa-check-circle"></i>&nbsp; ' + data.message;
            btn.innerHTML = '<i class="fas fa-check-circle"></i>&nbsp; Done!';
            setTimeout(() => { window.location.href = data.redirect || '/membership.php'; }, 1800);
        } else {
            alertBox.classList.add('alert-danger');
            alertBox.innerHTML = data.message;
            btn.disabled = false;
            btn.innerHTML = 'Set New Password &nbsp;<i class="fas fa-check-circle"></i>';
            // Shake the OTP boxes on wrong code
            document.getElementById('otpBoxes').style.animation = 'shake .4s';
            setTimeout(() => document.getElementById('otpBoxes').style.animation = '', 400);
        }
    } catch {
        alertBox.classList.remove('d-none');
        alertBox.classList.add('alert-danger');
        alertBox.innerHTML = 'Network error. Please try again.';
        btn.disabled = false;
        btn.innerHTML = 'Set New Password &nbsp;<i class="fas fa-check-circle"></i>';
    }
}

/* ── OTP box helpers ── */
function otpInput(el, idx) {
    el.value = el.value.replace(/\D/g,'').slice(-1);
    el.classList.toggle('filled', el.value !== '');
    if (el.value && idx < 5) document.getElementById('otp' + (idx+1)).focus();
}
function otpBack(e, idx) {
    if (e.key === 'Backspace' && !e.target.value && idx > 0) {
        document.getElementById('otp' + (idx-1)).focus();
    }
}
function otpPaste(e) {
    e.preventDefault();
    const digits = (e.clipboardData.getData('text') || '').replace(/\D/g,'').slice(0,6).split('');
    digits.forEach((d, i) => {
        const box = document.getElementById('otp'+i);
        if (box) { box.value = d; box.classList.add('filled'); }
    });
    const last = digits.length < 6 ? digits.length : 5;
    document.getElementById('otp'+last)?.focus();
}
function getOTPValue() {
    return Array.from({length:6}, (_,i) => (document.getElementById('otp'+i)?.value||'')).join('');
}
function clearOTP() {
    for (let i=0;i<6;i++) {
        const b = document.getElementById('otp'+i);
        if (b) { b.value=''; b.classList.remove('filled'); }
    }
    document.getElementById('otp0').focus();
}

/* ── Resend timer ── */
function startResendTimer(secs) {
    const btn   = document.getElementById('btnResend');
    const timer = document.getElementById('resendTimer');
    btn.style.display   = 'none';
    timer.style.display = 'inline';
    clearInterval(resendCountdown);
    let s = secs;
    timer.textContent = `(resend in ${s}s)`;
    resendCountdown = setInterval(() => {
        s--;
        if (s <= 0) {
            clearInterval(resendCountdown);
            timer.style.display = 'none';
            btn.style.display   = 'inline';
            btn.disabled        = false;
        } else {
            timer.textContent = `(resend in ${s}s)`;
        }
    }, 1000);
}

/* ── Password strength ── */
document.getElementById('newPassword').addEventListener('input', function() {
    const v = this.value;
    const wrap  = document.getElementById('strengthWrap');
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    if (!v) { wrap.style.display='none'; return; }
    wrap.style.display = 'block';

    let score = 0;
    if (v.length >= 8)               score++;
    if (/[A-Z]/.test(v))             score++;
    if (/[0-9]/.test(v))             score++;
    if (/[^A-Za-z0-9]/.test(v))      score++;

    const levels = [
        { width:'25%', bg:'#ef4444', text:'Weak',      color:'#ef4444' },
        { width:'50%', bg:'#f59e0b', text:'Fair',      color:'#f59e0b' },
        { width:'75%', bg:'#3b82f6', text:'Good',      color:'#3b82f6' },
        { width:'100%',bg:'#10b981', text:'Strong ✓',  color:'#10b981' },
    ];
    const lvl = levels[Math.max(0, score-1)];
    bar.style.width      = lvl.width;
    bar.style.background = lvl.bg;
    label.textContent    = lvl.text;
    label.style.color    = lvl.color;
});

function togglePwd(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}

/* ── Shake keyframes ── */
const shakeStyle = document.createElement('style');
shakeStyle.textContent = `@keyframes shake {
  0%,100%{transform:translateX(0)}
  20%{transform:translateX(-8px)}
  40%{transform:translateX(8px)}
  60%{transform:translateX(-6px)}
  80%{transform:translateX(6px)}
}`;
document.head.appendChild(shakeStyle);
</script>

<?php include 'includes/footer.php'; ?>
