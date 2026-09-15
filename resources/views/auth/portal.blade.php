@extends('layouts.app')

@section('title', 'Mobile OTP Verification System - DianaHost')

@section('styles')
<style>
  .auth-section {
    min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px;
  }
  .auth-card {
    width: 100%; max-width: 480px; border-radius: var(--radius-lg); padding: 36px;
    animation: fadeInScale 0.4s ease-out;
  }
  @keyframes fadeInScale { from { opacity: 0; transform: scale(0.96); } to { opacity: 1; transform: scale(1); } }

  .brand-header { text-align: center; margin-bottom: 28px; }
  .brand-logo {
    width: 60px; height: 60px; margin: 0 auto 16px;
    background: linear-gradient(135deg, var(--primary), var(--accent-violet));
    border-radius: 18px; display: flex; align-items: center; justify-content: center;
    font-size: 26px; color: #fff; box-shadow: 0 10px 25px var(--primary-glow);
  }
  .brand-title { font-family: var(--font-heading); font-size: 26px; font-weight: 700; color: var(--text-main); }
  .brand-subtitle { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

  .auth-tabs {
    display: flex; background: rgba(15, 23, 42, 0.7); padding: 5px;
    border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 28px;
  }
  .tab-btn {
    flex: 1; padding: 12px; border: none; background: transparent; color: var(--text-muted);
    font-weight: 600; font-size: 14px; border-radius: 10px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px; transition: var(--transition-fast);
  }
  .tab-btn.active { background: var(--primary); color: #fff; box-shadow: 0 4px 15px var(--primary-glow); }

  .auth-form { display: flex; flex-direction: column; gap: 20px; }
  .auth-form.hidden { display: none; }

  .form-group { display: flex; flex-direction: column; gap: 8px; }
  .form-group label { font-size: 13px; font-weight: 600; color: var(--text-muted); }

  .input-wrapper { position: relative; display: flex; align-items: center; }
  .input-icon { position: absolute; left: 16px; color: var(--text-dim); font-size: 15px; pointer-events: none; }
  .country-code { position: absolute; left: 16px; font-weight: 600; font-size: 14px; color: var(--primary); }

  .input-wrapper input {
    width: 100%; background: var(--bg-input); border: 1px solid var(--border-color);
    border-radius: var(--radius-md); padding: 14px 16px 14px 44px; color: var(--text-main);
    font-size: 14px; outline: none; transition: var(--transition-fast);
  }
  #signup-phone { padding-left: 54px; }
  .input-wrapper input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow); }

  .btn-primary {
    width: 100%; padding: 14px; border: none; border-radius: var(--radius-md);
    background: linear-gradient(135deg, var(--primary), var(--accent-violet));
    color: #fff; font-weight: 600; font-size: 15px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    box-shadow: 0 8px 20px var(--primary-glow); transition: var(--transition-fast);
  }
  .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 25px var(--primary-glow); }

  .btn-secondary {
    padding: 14px 20px; border: 1px solid var(--border-color); border-radius: var(--radius-md);
    background: rgba(255, 255, 255, 0.05); color: var(--text-muted); font-weight: 600;
    font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px;
  }

  .alert-box {
    padding: 12px 16px; border-radius: var(--radius-sm); font-size: 13px;
    display: flex; flex-direction: column; gap: 6px;
  }
  .alert-box.hidden { display: none; }
  .alert-error { background: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.3); color: #fca5a5; }
  .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; }

  .link-enter-otp {
    color: var(--primary); font-weight: 600; text-decoration: underline; cursor: pointer; font-size: 12px; margin-top: 4px;
  }

  /* STEP INDICATOR */
  .step-indicator { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
  .step-item { display: flex; flex-direction: column; align-items: center; gap: 4px; }
  .step-number {
    width: 32px; height: 32px; border-radius: 50%; background: var(--bg-input);
    border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 13px; color: var(--text-dim);
  }
  .step-label { font-size: 11px; color: var(--text-dim); font-weight: 500; }
  .step-item.active .step-number { background: var(--primary); border-color: var(--primary); color: #fff; }
  .step-item.active .step-label { color: var(--primary); font-weight: 600; }
  .step-line { flex: 1; height: 2px; background: var(--border-color); margin: 0 8px 16px 8px; }
  .step-line.completed { background: var(--primary); }

  .signup-step { display: flex; flex-direction: column; gap: 20px; }
  .signup-step.hidden { display: none; }

  .otp-digit {
    width: 48px; height: 56px; background: var(--bg-input); border: 1.5px solid var(--border-color);
    border-radius: var(--radius-md); text-align: center; font-size: 22px; font-weight: 700;
    color: var(--primary); outline: none; transition: var(--transition-fast);
  }
  .otp-digit:focus { border-color: var(--primary); box-shadow: 0 0 15px var(--primary-glow); }
  .otp-digit:-webkit-autofill,
  .otp-digit:-webkit-autofill:hover,
  .otp-digit:-webkit-autofill:focus {
    -webkit-text-fill-color: var(--primary) !important;
    -webkit-box-shadow: 0 0 0px 1000px #1e293b inset !important;
    transition: background-color 5000s ease-in-out 0s;
  }

  .timer-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 13px; color: var(--text-muted); margin-top: 4px;
  }
  .btn-resend {
    background: transparent; border: none; color: var(--primary); font-weight: 600;
    cursor: pointer; font-size: 13px;
  }
  .btn-resend:disabled { color: var(--text-dim); cursor: not-allowed; }
</style>
@endsection

@section('content')
<div class="auth-section">
  <div class="auth-card glass-panel">
    
    <!-- Brand Header -->
    <div class="brand-header">
      <div class="brand-logo"><i class="fa-solid fa-shield-halved"></i></div>
      <h1 class="brand-title">DianaHost OTP Portal</h1>
      <p class="brand-subtitle">Secure Mobile Phone Authentication System</p>
    </div>

    <!-- Navigation Tabs -->
    <div class="auth-tabs">
      <button id="tab-login" class="tab-btn active">
        <i class="fa-solid fa-right-to-bracket"></i> Login
      </button>
      <button id="tab-signup" class="tab-btn">
        <i class="fa-solid fa-user-plus"></i> Sign Up (OTP)
      </button>
    </div>

    <!-- LOGIN FORM -->
    <form id="form-login" class="auth-form active">
      <div id="login-success-banner" class="alert-box alert-success hidden">
        <div style="display:flex; align-items:center; gap:8px">
          <i class="fa-solid fa-circle-check"></i>
          <span id="login-success-text">Account created! Please log in.</span>
        </div>
      </div>

      <div class="form-group">
        <label for="login-identifier">Mobile Number or Email</label>
        <div class="input-wrapper">
          <i class="fa-solid fa-phone-volume input-icon"></i>
          <input type="text" id="login-identifier" placeholder="01712345678 or admin@example.com" required>
        </div>
      </div>

      <div class="form-group">
        <label for="login-password">Password</label>
        <div class="input-wrapper">
          <i class="fa-solid fa-lock input-icon"></i>
          <input type="password" id="login-password" placeholder="Enter password" required>
        </div>
      </div>

      <div id="login-error" class="alert-box alert-error hidden">
        <div style="display:flex; align-items:center; gap:8px">
          <i class="fa-solid fa-circle-exclamation"></i>
          <span id="login-error-text">Invalid credentials</span>
        </div>
      </div>

      <button type="submit" id="btn-login-submit" class="btn-primary">
        <span>Log In to Admin Panel</span>
        <i class="fa-solid fa-arrow-right"></i>
      </button>
    </form>

    <!-- SIGN UP MULTI-STEP FORM WITH DIANAHOST OTP -->
    <div id="form-signup" class="auth-form hidden">
      
      <!-- Step Progress Indicator -->
      <div class="step-indicator">
        <div class="step-item active" id="step-dot-1">
          <div class="step-number">1</div>
          <span class="step-label">Mobile</span>
        </div>
        <div class="step-line" id="step-line-1"></div>
        <div class="step-item" id="step-dot-2">
          <div class="step-number">2</div>
          <span class="step-label">OTP Verify</span>
        </div>
        <div class="step-line" id="step-line-2"></div>
        <div class="step-item" id="step-dot-3">
          <div class="step-number">3</div>
          <span class="step-label">Password</span>
        </div>
      </div>

      <!-- STEP 1: Mobile Input -->
      <div id="signup-step-1" class="signup-step active">
        <div class="form-group">
          <label for="signup-name">Full Name</label>
          <div class="input-wrapper">
            <i class="fa-regular fa-user input-icon"></i>
            <input type="text" id="signup-name" placeholder="e.g. Tanvir Hasan" required>
          </div>
        </div>

        <div class="form-group">
          <label for="signup-phone">Bangladesh Mobile Number</label>
          <div class="input-wrapper">
            <span class="country-code">+88</span>
            <input type="tel" id="signup-phone" placeholder="01712345678" maxlength="11" required>
          </div>
          <small style="font-size:12px; color:var(--text-dim)">Please enter 11-digit mobile number starting with 0 (e.g. 01712345678 or 01540700286)</small>
        </div>

        <div id="signup-step1-error" class="alert-box alert-error hidden">
          <div style="display:flex; align-items:center; gap:8px">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span id="signup-step1-error-text">Please enter a valid phone number</span>
          </div>
          <a id="btn-goto-step2" class="link-enter-otp hidden">⚡ Already received OTP? Click here to enter code</a>
        </div>

        <button type="button" id="btn-send-otp" class="btn-primary">
          <span>Send DianaHost OTP</span>
          <i class="fa-solid fa-paper-plane"></i>
        </button>
      </div>

      <!-- STEP 2: OTP Verification -->
      <div id="signup-step-2" class="signup-step hidden">
        <div style="text-align:center">
          <h3>Verify Mobile Number</h3>
          <p style="font-size:13px; color:var(--text-muted); margin-top:4px;">
            OTP sent to <strong id="display-target-phone">+880 17XXXXXXX</strong>
          </p>
        </div>

        <div class="otp-inputs-container">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit" data-index="0" autofocus autocomplete="one-time-code">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit" data-index="1" autocomplete="one-time-code">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit" data-index="2" autocomplete="one-time-code">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit" data-index="3" autocomplete="one-time-code">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit" data-index="4" autocomplete="one-time-code">
          <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]*" class="otp-digit" data-index="5" autocomplete="one-time-code">
        </div>

        <div id="otp-error" class="alert-box alert-error hidden">
          <div style="display:flex; align-items:center; gap:8px">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span id="otp-error-text">Incorrect OTP code</span>
          </div>
        </div>

        <div class="timer-row">
          <span>Resend code in: <strong id="resend-countdown">60s</strong></span>
          <button id="btn-resend-otp" class="btn-resend" disabled>Resend OTP</button>
        </div>

        <div style="display:flex; gap:12px; margin-top:8px">
          <button type="button" id="btn-back-step1" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
          </button>
          <button type="button" id="btn-verify-otp" class="btn-primary">
            <span>Verify OTP</span>
            <i class="fa-solid fa-check-double"></i>
          </button>
        </div>
      </div>

      <!-- STEP 3: Create Password -->
      <div id="signup-step-3" class="signup-step hidden">
        <div class="form-group">
          <label for="signup-password">Create Password</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-lock input-icon"></i>
            <input type="password" id="signup-password" placeholder="At least 6 characters" required>
          </div>
        </div>

        <div class="form-group">
          <label for="signup-confirm-password">Confirm Password</label>
          <div class="input-wrapper">
            <i class="fa-solid fa-shield-cat input-icon"></i>
            <input type="password" id="signup-confirm-password" placeholder="Re-enter password" required>
          </div>
        </div>

        <div id="signup-step3-error" class="alert-box alert-error hidden">
          <div style="display:flex; align-items:center; gap:8px">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span id="signup-step3-error-text">Passwords do not match</span>
          </div>
        </div>

        <button type="button" id="btn-complete-signup" class="btn-primary">
          <span>Create Verified Account</span>
          <i class="fa-solid fa-circle-check"></i>
        </button>
      </div>

    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const tabLogin = document.getElementById('tab-login');
  const tabSignup = document.getElementById('tab-signup');
  const formLogin = document.getElementById('form-login');
  const formSignup = document.getElementById('form-signup');

  // Resend Timer State
  let countdownSeconds = 60;
  let timerInterval = null;
  let currentPhone = '';
  let currentName = '';

  // Tabs
  tabLogin.addEventListener('click', () => {
    tabLogin.classList.add('active'); tabSignup.classList.remove('active');
    formLogin.classList.remove('hidden'); formSignup.classList.add('hidden');
  });
  tabSignup.addEventListener('click', () => {
    tabSignup.classList.add('active'); tabLogin.classList.remove('active');
    formSignup.classList.remove('hidden'); formLogin.classList.add('hidden');
  });

  // Login Submit -> Redirects automatically into Admin Panel
  formLogin.addEventListener('submit', async (e) => {
    e.preventDefault();
    const identifier = document.getElementById('login-identifier').value.trim();
    const password = document.getElementById('login-password').value.trim();
    const errorBox = document.getElementById('login-error');
    const errorText = document.getElementById('login-error-text');
    const successBanner = document.getElementById('login-success-banner');

    errorBox.classList.add('hidden');
    if (successBanner) successBanner.classList.add('hidden');

    try {
      const res = await fetch("{{ route('login.post') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({ login_identifier: identifier, password: password })
      });
      const data = await res.json();
      if (data.success) {
        window.location.href = data.redirect;
      } else {
        errorText.textContent = data.message || 'Invalid mobile number or password.';
        errorBox.classList.remove('hidden');
      }
    } catch (err) {
      errorText.textContent = 'Server error occurred.';
      errorBox.classList.remove('hidden');
    }
  });

  function formatPhoneDisplay(phoneStr) {
    if (!phoneStr) return '';
    const digits = phoneStr.replace(/\D/g, '');
    if (digits.length >= 10) {
      const core = digits.slice(-10);
      return '+880 ' + core;
    }
    return phoneStr;
  }

  function goToStep2() {
    const rawPhone = document.getElementById('signup-phone').value.trim();
    if (rawPhone) currentPhone = rawPhone;
    document.getElementById('display-target-phone').textContent = formatPhoneDisplay(currentPhone) || 'your mobile number';

    clearOtpInputs();

    document.getElementById('signup-step-1').classList.add('hidden');
    document.getElementById('signup-step-2').classList.remove('hidden');
    document.getElementById('step-dot-2').classList.add('active');
    document.getElementById('step-line-1').classList.add('completed');

    setTimeout(() => {
      if (otpDigits[0]) otpDigits[0].focus();
    }, 50);
  }

  document.getElementById('btn-goto-step2').addEventListener('click', goToStep2);

  // STEP 1: Send OTP via /api/otp/send
  document.getElementById('btn-send-otp').addEventListener('click', async () => {
    const name = document.getElementById('signup-name').value.trim();
    const phone = document.getElementById('signup-phone').value.trim();
    const errBox = document.getElementById('signup-step1-error');
    const errText = document.getElementById('signup-step1-error-text');
    const gotoLink = document.getElementById('btn-goto-step2');

    errBox.classList.add('hidden');
    gotoLink.classList.add('hidden');

    if (!name || phone.length < 10) {
      errText.textContent = 'Please enter your name and a valid mobile number.';
      errBox.classList.remove('hidden');
      return;
    }

    currentName = name;
    currentPhone = phone;

    try {
      const res = await fetch("/api/otp/send", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({ phone: phone })
      });
      const data = await res.json();

      if (data.success) {
        goToStep2();
        startResendTimer();
      } else {
        errText.textContent = data.message || 'Failed to send OTP.';
        errBox.classList.remove('hidden');
        if (data.message && (data.message.includes('wait') || data.message.includes('sent'))) {
          gotoLink.classList.remove('hidden');
        }
      }
    } catch (err) {
      errText.textContent = 'Network or server error occurred.';
      errBox.classList.remove('hidden');
    }
  });

  // 60-Second Resend Countdown
  function startResendTimer() {
    clearInterval(timerInterval);
    countdownSeconds = 60;
    const countdownEl = document.getElementById('resend-countdown');
    const btnResend = document.getElementById('btn-resend-otp');
    btnResend.disabled = true;

    timerInterval = setInterval(() => {
      countdownSeconds--;
      countdownEl.textContent = countdownSeconds + 's';

      if (countdownSeconds <= 0) {
        clearInterval(timerInterval);
        btnResend.disabled = false;
        countdownEl.textContent = '0s';
      }
    }, 1000);
  }

  // Resend OTP Click
  document.getElementById('btn-resend-otp').addEventListener('click', async () => {
    const errBox = document.getElementById('otp-error');
    const errText = document.getElementById('otp-error-text');
    errBox.classList.add('hidden');

    try {
      const res = await fetch("/api/otp/resend", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({ phone: currentPhone || document.getElementById('signup-phone').value })
      });
      const data = await res.json();

      if (data.success) {
        clearOtpInputs();
        if (otpDigits[0]) otpDigits[0].focus();
        startResendTimer();
      } else {
        errText.textContent = data.message || 'Failed to resend OTP.';
        errBox.classList.remove('hidden');
      }
    } catch (err) {
      errText.textContent = 'Server error occurred.';
      errBox.classList.remove('hidden');
    }
  });

  // OTP Digits Navigation & Input Sanitization
  const otpDigits = document.querySelectorAll('.otp-digit');
  otpDigits.forEach((input, index) => {
    input.addEventListener('input', (e) => {
      // Strip any non-digit character (e.g. 'R', letters, symbols)
      const clean = e.target.value.replace(/[^0-9]/g, '');
      e.target.value = clean;

      if (clean.length === 1 && index < 5) {
        otpDigits[index + 1].focus();
      }
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace') {
        if (!input.value && index > 0) {
          otpDigits[index - 1].value = '';
          otpDigits[index - 1].focus();
        }
      }
    });

    // Handle copying/pasting full OTP code into inputs
    input.addEventListener('paste', (e) => {
      e.preventDefault();
      const rawText = (e.clipboardData || window.clipboardData).getData('text') || '';
      const digitsOnly = rawText.replace(/[^0-9]/g, '').slice(0, 6);

      if (digitsOnly.length > 0) {
        clearOtpInputs();
        digitsOnly.split('').forEach((char, i) => {
          if (otpDigits[i]) otpDigits[i].value = char;
        });
        const focusIndex = Math.min(digitsOnly.length, 5);
        otpDigits[focusIndex].focus();
      }
    });
  });

  // STEP 2: Verify OTP via /api/otp/verify
  document.getElementById('btn-verify-otp').addEventListener('click', async () => {
    let enteredCode = '';
    otpDigits.forEach(d => enteredCode += d.value);
    const errBox = document.getElementById('otp-error');
    const errText = document.getElementById('otp-error-text');
    errBox.classList.add('hidden');

    if (enteredCode.length < 6) {
      errText.textContent = 'Please enter all 6 digits of the OTP.';
      errBox.classList.remove('hidden');
      return;
    }

    const phoneToVerify = currentPhone || document.getElementById('signup-phone').value;

    try {
      const res = await fetch("/api/otp/verify", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({ phone: phoneToVerify, otp: enteredCode })
      });
      const data = await res.json();

      if (data.success) {
        document.getElementById('signup-step-2').classList.add('hidden');
        document.getElementById('signup-step-3').classList.remove('hidden');
        document.getElementById('step-dot-3').classList.add('active');
        document.getElementById('step-line-2').classList.add('completed');
      } else {
        errText.textContent = data.message || 'Invalid or expired OTP.';
        errBox.classList.remove('hidden');
      }
    } catch (err) {
      errText.textContent = 'Server error during OTP verification.';
      errBox.classList.remove('hidden');
    }
  });

  document.getElementById('btn-back-step1').addEventListener('click', () => {
    document.getElementById('signup-step-2').classList.add('hidden');
    document.getElementById('signup-step-1').classList.remove('hidden');
  });

  // STEP 3: Complete Registration & Switch to Login Tab with Phone Auto-Filled
  document.getElementById('btn-complete-signup').addEventListener('click', async () => {
    const password = document.getElementById('signup-password').value;
    const confirmPw = document.getElementById('signup-confirm-password').value;
    const name = currentName || document.getElementById('signup-name').value;
    const phone = currentPhone || document.getElementById('signup-phone').value;

    const errBox = document.getElementById('signup-step3-error');
    const errText = document.getElementById('signup-step3-error-text');
    errBox.classList.add('hidden');

    if (password.length < 6 || password !== confirmPw) {
      errText.textContent = 'Passwords must match and be at least 6 characters.';
      errBox.classList.remove('hidden');
      return;
    }

    try {
      const res = await fetch("{{ route('register') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({ 
          name: name,
          phone: phone,
          password: password, 
          password_confirmation: confirmPw 
        })
      });
      const data = await res.json();

      if (data.success) {
        // Automatically switch to Login Tab & Auto-fill mobile number!
        tabLogin.click();
        
        const loginIdentifier = document.getElementById('login-identifier');
        if (loginIdentifier) {
          loginIdentifier.value = data.phone || phone;
        }

        const successBanner = document.getElementById('login-success-banner');
        const successText = document.getElementById('login-success-text');
        if (successBanner && successText) {
          successText.textContent = '✅ Account created & verified! Please enter your password to log in.';
          successBanner.classList.remove('hidden');
        }

        const loginPasswordInput = document.getElementById('login-password');
        if (loginPasswordInput) loginPasswordInput.focus();

      } else {
        errText.textContent = data.message || 'Registration failed.';
        errBox.classList.remove('hidden');
      }
    } catch (err) {
      errText.textContent = 'Registration server error.';
      errBox.classList.remove('hidden');
    }
  });

});
</script>
@endsection
