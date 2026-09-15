/**
 * NEXUS AUTH & ADMIN SYSTEM
 * - Multi-step Sign Up with OTP verification via mobile simulator toast
 * - Direct Password Login for verified accounts (No OTP needed on login)
 * - Admin Dashboard with live user statistics and Logout handler
 */

document.addEventListener('DOMContentLoaded', () => {

  // ==========================================
  // STATE & LOCAL STORAGE DATABASE
  // ==========================================
  const STORAGE_KEY_USERS = 'nexus_registered_users';
  const STORAGE_KEY_SESSION = 'nexus_active_session';

  let currentGeneratedOTP = null;
  let resendTimerInterval = null;
  let remainingTimerSeconds = 60;
  let signupDataDraft = {
    name: '',
    phone: '',
    password: ''
  };

  // Retrieve stored users or initialize default array
  function getStoredUsers() {
    const raw = localStorage.getItem(STORAGE_KEY_USERS);
    if (!raw) {
      // Pre-seed a sample verified account for instant testing
      const defaultUsers = [
        {
          name: 'Default Admin',
          phone: '01700000000',
          password: 'admin',
          isVerified: true,
          createdAt: new Date().toLocaleDateString()
        }
      ];
      localStorage.setItem(STORAGE_KEY_USERS, JSON.stringify(defaultUsers));
      return defaultUsers;
    }
    return JSON.parse(raw);
  }

  function saveUser(userObj) {
    const users = getStoredUsers();
    // Check if phone exists and replace or append
    const existingIdx = users.findIndex(u => u.phone === userObj.phone);
    if (existingIdx >= 0) {
      users[existingIdx] = userObj;
    } else {
      users.push(userObj);
    }
    localStorage.setItem(STORAGE_KEY_USERS, JSON.stringify(users));
  }

  function getActiveSession() {
    const raw = localStorage.getItem(STORAGE_KEY_SESSION);
    return raw ? JSON.parse(raw) : null;
  }

  function setActiveSession(userObj) {
    const sessionData = {
      user: userObj,
      loginTime: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    };
    localStorage.setItem(STORAGE_KEY_SESSION, JSON.stringify(sessionData));
  }

  function clearActiveSession() {
    localStorage.removeItem(STORAGE_KEY_SESSION);
  }


  // ==========================================
  // DOM ELEMENT SELECTIONS
  // ==========================================
  const authSection = document.getElementById('auth-section');
  const adminSection = document.getElementById('admin-section');

  // Tabs
  const tabLogin = document.getElementById('tab-login');
  const tabSignup = document.getElementById('tab-signup');
  const formLogin = document.getElementById('form-login');
  const formSignup = document.getElementById('form-signup');

  // Login Form
  const inputLoginIdentifier = document.getElementById('login-identifier');
  const inputLoginPassword = document.getElementById('login-password');
  const loginErrorBox = document.getElementById('login-error');
  const loginErrorText = document.getElementById('login-error-text');

  // Signup Steps
  const stepDot1 = document.getElementById('step-dot-1');
  const stepDot2 = document.getElementById('step-dot-2');
  const stepDot3 = document.getElementById('step-dot-3');
  const stepLine1 = document.getElementById('step-line-1');
  const stepLine2 = document.getElementById('step-line-2');

  const signupStep1 = document.getElementById('signup-step-1');
  const signupStep2 = document.getElementById('signup-step-2');
  const signupStep3 = document.getElementById('signup-step-3');

  const inputSignupName = document.getElementById('signup-name');
  const inputSignupPhone = document.getElementById('signup-phone');
  const signupStep1Error = document.getElementById('signup-step1-error');
  const signupStep1ErrorText = document.getElementById('signup-step1-error-text');
  const btnSendOtp = document.getElementById('btn-send-otp');

  // OTP Verification
  const displayTargetPhone = document.getElementById('display-target-phone');
  const otpDigits = document.querySelectorAll('.otp-digit');
  const otpErrorBox = document.getElementById('otp-error');
  const otpErrorText = document.getElementById('otp-error-text');
  const otpTimerDisplay = document.getElementById('otp-timer');
  const btnResendOtp = document.getElementById('btn-resend-otp');
  const btnBackStep1 = document.getElementById('btn-back-step1');
  const btnVerifyOtp = document.getElementById('btn-verify-otp');

  // Signup Password
  const inputSignupPassword = document.getElementById('signup-password');
  const inputSignupConfirmPassword = document.getElementById('signup-confirm-password');
  const signupStep3Error = document.getElementById('signup-step3-error');
  const signupStep3ErrorText = document.getElementById('signup-step3-error-text');
  const btnCompleteSignup = document.getElementById('btn-complete-signup');

  // SMS Toast
  const smsToast = document.getElementById('sms-toast');
  const smsCodeDisplay = document.getElementById('sms-code-display');
  const btnCopyOtp = document.getElementById('btn-copy-otp');

  // Admin Dashboard
  const adminUserName = document.getElementById('admin-user-name');
  const adminUserPhone = document.getElementById('admin-user-phone');
  const userAvatar = document.getElementById('user-avatar');
  const dashboardSessionTime = document.getElementById('dashboard-session-time');
  const infoAccountName = document.getElementById('info-account-name');
  const infoAccountPhone = document.getElementById('info-account-phone');
  const infoAccountDate = document.getElementById('info-account-date');
  const btnLogout = document.getElementById('btn-logout');


  // ==========================================
  // INITIALIZATION & SESSION CHECK
  // ==========================================
  function initApp() {
    const activeSession = getActiveSession();
    if (activeSession && activeSession.user) {
      renderAdminView(activeSession);
    } else {
      renderAuthView();
    }
  }

  function renderAuthView() {
    authSection.classList.remove('hidden');
    adminSection.classList.add('hidden');
  }

  function renderAdminView(sessionData) {
    const user = sessionData.user;
    authSection.classList.add('hidden');
    adminSection.classList.remove('hidden');

    adminUserName.textContent = user.name || 'Admin User';
    adminUserPhone.textContent = formatPhoneDisplay(user.phone);
    userAvatar.textContent = (user.name || 'A').charAt(0).toUpperCase();

    dashboardSessionTime.textContent = sessionData.loginTime || 'Active Now';
    infoAccountName.textContent = user.name || 'N/A';
    infoAccountPhone.textContent = formatPhoneDisplay(user.phone);
    infoAccountDate.textContent = user.createdAt || 'Today';
  }

  function formatPhoneDisplay(phone) {
    if (!phone) return '+880 1700000000';
    if (phone.startsWith('0')) {
      return '+880 ' + phone.substring(1);
    }
    return '+880 ' + phone;
  }


  // ==========================================
  // TAB SWITCHING
  // ==========================================
  tabLogin.addEventListener('click', () => {
    tabLogin.classList.add('active');
    tabSignup.classList.remove('active');
    formLogin.classList.add('active');
    formLogin.classList.remove('hidden');
    formSignup.classList.add('hidden');
    formSignup.classList.remove('active');
    hideAlerts();
  });

  tabSignup.addEventListener('click', () => {
    tabSignup.classList.add('active');
    tabLogin.classList.remove('active');
    formSignup.classList.add('active');
    formSignup.classList.remove('hidden');
    formLogin.classList.add('hidden');
    formLogin.classList.remove('active');
    hideAlerts();
  });

  // Password visibility toggle
  document.querySelectorAll('.btn-toggle-pw').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target');
      const targetInput = document.getElementById(targetId);
      const icon = btn.querySelector('i');

      if (targetInput.type === 'password') {
        targetInput.type = 'text';
        icon.className = 'fa-regular fa-eye-slash';
      } else {
        targetInput.type = 'password';
        icon.className = 'fa-regular fa-eye';
      }
    });
  });

  function hideAlerts() {
    loginErrorBox.classList.add('hidden');
    signupStep1Error.classList.add('hidden');
    otpErrorBox.classList.add('hidden');
    signupStep3Error.classList.add('hidden');
  }


  // ==========================================
  // LOGIN FLOW (No OTP required once verified)
  // ==========================================
  formLogin.addEventListener('submit', (e) => {
    e.preventDefault();
    hideAlerts();

    const identifier = inputLoginIdentifier.value.trim();
    const password = inputLoginPassword.value.trim();

    if (!identifier || !password) {
      showError(loginErrorBox, loginErrorText, 'Please enter both phone/email and password.');
      return;
    }

    const users = getStoredUsers();
    // Normalize phone number search
    const cleanPhone = identifier.replace(/[^0-9]/g, '');

    const matchedUser = users.find(u => 
      (u.phone === identifier || u.phone === cleanPhone || cleanPhone.endsWith(u.phone)) && 
      u.password === password
    );

    if (matchedUser) {
      // Save session & redirect to Admin Panel
      setActiveSession(matchedUser);
      renderAdminView(getActiveSession());
    } else {
      showError(loginErrorBox, loginErrorText, 'Invalid mobile number or password! Please try again or Sign Up.');
    }
  });


  // ==========================================
  // OTP SIGN UP FLOW (Multi-Step)
  // ==========================================

  // STEP 1: Details & Send OTP
  btnSendOtp.addEventListener('click', () => {
    hideAlerts();
    const name = inputSignupName.value.trim();
    let phone = inputSignupPhone.value.trim().replace(/\D/g, '');

    if (!name || name.length < 2) {
      showError(signupStep1Error, signupStep1ErrorText, 'Please enter your full name.');
      return;
    }

    if (!phone || phone.length < 10) {
      showError(signupStep1Error, signupStep1ErrorText, 'Please enter a valid 10-digit mobile number.');
      return;
    }

    if (phone.length === 11 && phone.startsWith('0')) {
      phone = phone.substring(1);
    }

    signupDataDraft.name = name;
    signupDataDraft.phone = phone;

    displayTargetPhone.textContent = `+880 ${phone}`;

    // Generate 6-digit OTP code & show simulation toast
    generateAndSendOTP();

    // Advance UI to Step 2
    switchSignupStep(2);
  });

  // OTP Generation & Simulation Toast
  function generateAndSendOTP() {
    currentGeneratedOTP = Math.floor(100000 + Math.random() * 900000).toString();
    console.log(`[SIMULATED SMS OTP]: ${currentGeneratedOTP}`);

    smsCodeDisplay.textContent = currentGeneratedOTP;
    smsToast.classList.remove('hidden');

    // Start 60s countdown timer
    startResendTimer();
  }

  // Copy OTP button click
  btnCopyOtp.addEventListener('click', () => {
    if (currentGeneratedOTP) {
      navigator.clipboard.writeText(currentGeneratedOTP);
      // Auto fill digit boxes
      fillOtpInputs(currentGeneratedOTP);
      btnCopyOtp.innerHTML = '<i class="fa-solid fa-check"></i>';
      setTimeout(() => {
        btnCopyOtp.innerHTML = '<i class="fa-regular fa-copy"></i>';
      }, 2000);
    }
  });

  function startResendTimer() {
    clearInterval(resendTimerInterval);
    remainingTimerSeconds = 60;
    btnResendOtp.disabled = true;

    resendTimerInterval = setInterval(() => {
      remainingTimerSeconds--;
      const secs = remainingTimerSeconds < 10 ? `0${remainingTimerSeconds}` : remainingTimerSeconds;
      otpTimerDisplay.textContent = `00:${secs}`;

      if (remainingTimerSeconds <= 0) {
        clearInterval(resendTimerInterval);
        btnResendOtp.disabled = false;
        otpTimerDisplay.textContent = '00:00';
      }
    }, 1000);
  }

  btnResendOtp.addEventListener('click', () => {
    if (!btnResendOtp.disabled) {
      generateAndSendOTP();
      hideAlerts();
    }
  });

  // Auto-focus & digit handling for OTP Inputs
  otpDigits.forEach((input, index) => {
    input.addEventListener('input', (e) => {
      const val = e.target.value;
      if (val.length === 1 && index < otpDigits.length - 1) {
        otpDigits[index + 1].focus();
      }
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !input.value && index > 0) {
        otpDigits[index - 1].focus();
      }
    });

    input.addEventListener('paste', (e) => {
      e.preventDefault();
      const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim();
      if (/^\d{6}$/.test(pastedData)) {
        fillOtpInputs(pastedData);
      }
    });
  });

  function fillOtpInputs(codeStr) {
    codeStr.split('').forEach((char, idx) => {
      if (otpDigits[idx]) {
        otpDigits[idx].value = char;
      }
    });
    if (otpDigits[5]) otpDigits[5].focus();
  }

  function getEnteredOTP() {
    let code = '';
    otpDigits.forEach(d => code += d.value);
    return code;
  }

  // STEP 2: Verify OTP
  btnVerifyOtp.addEventListener('click', () => {
    hideAlerts();
    const enteredOTP = getEnteredOTP();

    if (enteredOTP.length < 6) {
      showError(otpErrorBox, otpErrorText, 'Please enter all 6 digits of the OTP.');
      return;
    }

    if (enteredOTP === currentGeneratedOTP) {
      // OTP is valid! Proceed to Step 3 (Set Password)
      switchSignupStep(3);
    } else {
      showError(otpErrorBox, otpErrorText, 'Incorrect OTP code! Click "Resend OTP" or check the message box.');
    }
  });

  btnBackStep1.addEventListener('click', () => {
    switchSignupStep(1);
  });

  // STEP 3: Complete Registration & Save User
  btnCompleteSignup.addEventListener('click', () => {
    hideAlerts();
    const password = inputSignupPassword.value.trim();
    const confirmPw = inputSignupConfirmPassword.value.trim();

    if (!password || password.length < 6) {
      showError(signupStep3Error, signupStep3ErrorText, 'Password must be at least 6 characters long.');
      return;
    }

    if (password !== confirmPw) {
      showError(signupStep3Error, signupStep3ErrorText, 'Passwords do not match.');
      return;
    }

    // Save Account into LocalStorage DB as Verified
    const newAccount = {
      name: signupDataDraft.name,
      phone: signupDataDraft.phone,
      password: password,
      isVerified: true,
      createdAt: new Date().toLocaleDateString()
    };

    saveUser(newAccount);

    // Hide SMS toast
    smsToast.classList.add('hidden');

    // Log user in directly
    setActiveSession(newAccount);
    renderAdminView(getActiveSession());
  });


  // ==========================================
  // STEP SWITCHER HELPER
  // ==========================================
  function switchSignupStep(stepNumber) {
    // Hide all steps
    signupStep1.classList.add('hidden');
    signupStep1.classList.remove('active');
    signupStep2.classList.add('hidden');
    signupStep2.classList.remove('active');
    signupStep3.classList.add('hidden');
    signupStep3.classList.remove('active');

    // Reset step dots
    stepDot1.classList.remove('active');
    stepDot2.classList.remove('active');
    stepDot3.classList.remove('active');
    stepLine1.classList.remove('completed');
    stepLine2.classList.remove('completed');

    if (stepNumber === 1) {
      signupStep1.classList.remove('hidden');
      signupStep1.classList.add('active');
      stepDot1.classList.add('active');
    } else if (stepNumber === 2) {
      signupStep2.classList.remove('hidden');
      signupStep2.classList.add('active');
      stepDot1.classList.add('active');
      stepDot2.classList.add('active');
      stepLine1.classList.add('completed');
      if (otpDigits[0]) otpDigits[0].focus();
    } else if (stepNumber === 3) {
      signupStep3.classList.remove('hidden');
      signupStep3.classList.add('active');
      stepDot1.classList.add('active');
      stepDot2.classList.add('active');
      stepDot3.classList.add('active');
      stepLine1.classList.add('completed');
      stepLine2.classList.add('completed');
    }
  }


  // ==========================================
  // LOGOUT HANDLER
  // ==========================================
  btnLogout.addEventListener('click', () => {
    clearActiveSession();
    renderAuthView();

    // Reset forms & switch to Login tab
    tabLogin.click();
    inputLoginIdentifier.value = '';
    inputLoginPassword.value = '';
    smsToast.classList.add('hidden');
  });

  function showError(boxEl, textEl, message) {
    textEl.textContent = message;
    boxEl.classList.remove('hidden');
  }

  // Run initial check
  initApp();

});
