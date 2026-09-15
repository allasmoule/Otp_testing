<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Nexus Admin Portal - Laravel')</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <style>
    :root {
      --bg-dark: #0b0f19;
      --bg-card: rgba(18, 24, 38, 0.8);
      --bg-input: rgba(30, 41, 59, 0.6);
      --border-color: rgba(255, 255, 255, 0.1);
      --border-hover: rgba(99, 102, 241, 0.5);
      
      --primary: #6366f1;
      --primary-hover: #4f46e5;
      --primary-glow: rgba(99, 102, 241, 0.35);
      
      --accent-violet: #8b5cf6;
      --accent-cyan: #06b6d4;
      --accent-emerald: #10b981;
      --accent-rose: #f43f5e;

      --text-main: #f8fafc;
      --text-muted: #94a3b8;
      --text-dim: #64748b;

      --font-heading: 'Outfit', sans-serif;
      --font-body: 'Plus Jakarta Sans', sans-serif;

      --radius-sm: 8px;
      --radius-md: 14px;
      --radius-lg: 20px;
      --radius-full: 9999px;

      --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: var(--font-body); }

    body {
      background-color: var(--bg-dark);
      color: var(--text-main);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
      position: relative;
    }

    /* BACKGROUND GLOW ORBS */
    .bg-gradient-wrapper {
      position: fixed;
      top: 0; left: 0; width: 100vw; height: 100vh;
      z-index: -1; overflow: hidden; pointer-events: none;
    }

    .glow-orb {
      position: absolute; border-radius: 50%; filter: blur(120px); opacity: 0.45;
    }
    .orb-1 { width: 450px; height: 450px; background: radial-gradient(circle, var(--primary), var(--accent-violet)); top: -100px; left: -100px; }
    .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, var(--accent-cyan), var(--primary)); bottom: -150px; right: -100px; }

    .glass-panel {
      background: var(--bg-card);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid var(--border-color);
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    /* SMS SIMULATOR TOAST */
    .sms-toast {
      position: fixed; top: 24px; right: 24px; z-index: 9999;
      width: 380px; background: rgba(15, 23, 42, 0.95);
      border: 1px solid var(--primary-glow);
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6), 0 0 20px var(--primary-glow);
      border-radius: var(--radius-md); padding: 16px;
      display: flex; align-items: center; gap: 14px;
      backdrop-filter: blur(12px);
      animation: slideInDown 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .sms-toast.hidden { display: none; }
    .sms-icon {
      width: 44px; height: 44px; border-radius: 12px;
      background: linear-gradient(135deg, var(--primary), var(--accent-violet));
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; color: #fff; flex-shrink: 0;
    }
    .sms-content { flex: 1; }
    .sms-header { display: flex; justify-content: space-between; margin-bottom: 4px; }
    .sms-sender { font-weight: 600; font-size: 13px; color: var(--primary); }
    .sms-time { font-size: 11px; color: var(--text-dim); }
    .sms-body { font-size: 13px; color: var(--text-main); line-height: 1.4; }
    .sms-body strong { color: var(--accent-emerald); font-family: monospace; font-size: 15px; letter-spacing: 1px; }

    .btn-copy-otp {
      background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color);
      color: var(--text-muted); width: 34px; height: 34px; border-radius: var(--radius-sm);
      cursor: pointer; display: flex; align-items: center; justify-content: center;
    }
    .btn-copy-otp:hover { background: var(--primary); color: #fff; }

    @keyframes slideInDown {
      from { opacity: 0; transform: translateY(-30px) scale(0.95); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
  </style>
  @yield('styles')
</head>
<body>

  <div class="bg-gradient-wrapper">
    <div class="glow-orb orb-1"></div>
    <div class="glow-orb orb-2"></div>
  </div>

  <!-- Simulated SMS Notification Toast -->
  <div id="sms-toast" class="sms-toast hidden">
    <div class="sms-icon"><i class="fa-solid fa-comment-sms"></i></div>
    <div class="sms-content">
      <div class="sms-header">
        <span class="sms-sender">💬 ZendSMS Notification</span>
        <span class="sms-time">Just now</span>
      </div>
      <p class="sms-body">Your Verification Code is: <strong id="sms-code-display">123456</strong>. Valid for 5 min.</p>
    </div>
    <button id="btn-copy-otp" class="btn-copy-otp" title="Copy OTP">
      <i class="fa-regular fa-copy"></i>
    </button>
  </div>

  <main>
    @yield('content')
  </main>

  <script>
    // Global CSRF Token Setup for Fetch API
    window.csrfToken = "{{ csrf_token() }}";
  </script>
  @yield('scripts')
</body>
</html>
