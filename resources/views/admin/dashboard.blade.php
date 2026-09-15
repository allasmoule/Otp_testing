@extends('layouts.app')

@section('title', 'DianaHost Admin Control Panel')

@section('styles')
<style>
  :root {
    --card-bg: rgba(15, 23, 42, 0.85);
    --card-border: rgba(255, 255, 255, 0.08);
  }

  .admin-header {
    height: 72px; background: rgba(11, 15, 25, 0.9); backdrop-filter: blur(16px);
    border-bottom: 1px solid var(--border-color); padding: 0 32px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 100;
  }
  .admin-brand { display: flex; align-items: center; gap: 14px; }
  .admin-logo {
    width: 42px; height: 42px; border-radius: 12px;
    background: linear-gradient(135deg, var(--primary), var(--accent-violet));
    display: flex; align-items: center; justify-content: center; color: #fff; font-size: 20px;
    box-shadow: 0 6px 18px var(--primary-glow);
  }
  .admin-brand-name { font-family: var(--font-heading); font-size: 20px; font-weight: 700; color: var(--text-main); }
  .admin-brand-tag { font-size: 11px; background: rgba(99, 102, 241, 0.2); color: var(--primary); padding: 3px 8px; border-radius: 6px; font-weight: 600; }

  .admin-user-menu { display: flex; align-items: center; gap: 20px; }
  .user-badge {
    display: flex; align-items: center; gap: 12px; padding: 6px 16px;
    background: rgba(255, 255, 255, 0.04); border: 1px solid var(--border-color);
    border-radius: var(--radius-full);
  }
  .avatar-circle {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-violet), var(--accent-cyan));
    display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff; font-size: 15px;
  }

  .btn-logout {
    background: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.3);
    color: #fca5a5; padding: 9px 20px; border-radius: var(--radius-md); font-size: 13px;
    font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;
    transition: var(--transition-fast);
  }
  .btn-logout:hover {
    background: var(--accent-rose); color: #fff; border-color: var(--accent-rose);
    box-shadow: 0 4px 15px rgba(244, 63, 94, 0.4);
  }

  .admin-container {
    max-width: 1280px; width: 100%; margin: 32px auto; padding: 0 24px;
    display: flex; flex-direction: column; gap: 28px;
  }

  /* ALERT BANNERS */
  .flash-banner {
    padding: 14px 20px; border-radius: var(--radius-md); font-size: 14px;
    display: flex; align-items: center; justify-content: space-between;
    animation: fadeIn 0.3s ease-out;
  }
  .flash-success { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; }
  .flash-danger { background: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.3); color: #fca5a5; }

  /* HERO BANNER */
  .hero-panel {
    padding: 32px 36px; border-radius: var(--radius-lg); position: relative; overflow: hidden;
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95));
    border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;
  }
  .hero-panel::before {
    content: ''; position: absolute; top: -50%; right: -10%; width: 400px; height: 400px;
    background: radial-gradient(circle, var(--primary-glow), transparent 70%); pointer-events: none;
  }
  .hero-title { font-family: var(--font-heading); font-size: 28px; font-weight: 700; margin-bottom: 8px; color: var(--text-main); }
  .hero-subtitle { font-size: 14px; color: var(--text-muted); max-width: 620px; line-height: 1.6; }

  .status-badge-live {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3);
    color: #34d399; padding: 6px 14px; border-radius: var(--radius-full);
    font-size: 12px; font-weight: 600; margin-bottom: 14px;
  }
  .pulse-dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 10px #10b981; animation: pulse 1.8s infinite; }
  @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(1.2); } }

  /* METRICS GRID */
  .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
  .metric-card {
    padding: 24px; border-radius: var(--radius-lg); background: var(--bg-card);
    border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;
    transition: var(--transition-fast);
  }
  .metric-card:hover { transform: translateY(-3px); border-color: var(--primary-glow); box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4); }
  .metric-info { display: flex; flex-direction: column; gap: 4px; }
  .metric-label { font-size: 13px; color: var(--text-muted); font-weight: 500; }
  .metric-value { font-family: var(--font-heading); font-size: 30px; font-weight: 700; color: var(--text-main); }
  .metric-sub { font-size: 11px; color: var(--text-dim); }

  .metric-icon-box {
    width: 52px; height: 52px; border-radius: var(--radius-md); display: flex;
    align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
  }
  .icon-indigo { background: rgba(99, 102, 241, 0.15); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.3); }
  .icon-violet { background: rgba(139, 92, 246, 0.15); color: var(--accent-violet); border: 1px solid rgba(139, 92, 246, 0.3); }
  .icon-emerald { background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.3); }
  .icon-cyan { background: rgba(6, 182, 212, 0.15); color: var(--accent-cyan); border: 1px solid rgba(6, 182, 212, 0.3); }

  /* CONTENT TWO-COLUMN GRID */
  .content-grid { display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 24px; }
  @media (max-width: 992px) { .content-grid { grid-template-columns: 1fr; } }

  .panel-card {
    background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);
    padding: 24px; display: flex; flex-direction: column; gap: 20px;
  }
  .panel-header { display: flex; justify-content: space-between; align-items: center; }
  .panel-title { font-family: var(--font-heading); font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 10px; }

  /* TABLES */
  .table-responsive { width: 100%; overflow-x: auto; }
  .custom-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
  .custom-table th {
    padding: 12px 16px; background: rgba(255, 255, 255, 0.03); color: var(--text-muted);
    font-weight: 600; border-bottom: 1px solid var(--border-color);
  }
  .custom-table td { padding: 14px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.04); color: var(--text-main); }
  .custom-table tr:hover td { background: rgba(255, 255, 255, 0.02); }

  .badge-status {
    display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 600;
  }
  .badge-success { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
  .badge-warning { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
  .badge-danger { background: rgba(244, 63, 94, 0.15); color: #fca5a5; border: 1px solid rgba(244, 63, 94, 0.3); }

  /* SMS QUICK TEST WIDGET */
  .sms-widget-form { display: flex; flex-direction: column; gap: 14px; }
  .widget-input-group { display: flex; flex-direction: column; gap: 6px; }
  .widget-input-group label { font-size: 12px; font-weight: 600; color: var(--text-muted); }
  .widget-input-group input, .widget-input-group textarea {
    width: 100%; background: var(--bg-input); border: 1px solid var(--border-color);
    border-radius: var(--radius-sm); padding: 10px 14px; color: var(--text-main);
    font-size: 13px; outline: none; transition: var(--transition-fast);
  }
  .widget-input-group input:focus, .widget-input-group textarea:focus {
    border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-glow);
  }

  .btn-sm-primary {
    padding: 11px 18px; border-radius: var(--radius-md); border: none;
    background: linear-gradient(135deg, var(--primary), var(--accent-violet));
    color: #fff; font-weight: 600; font-size: 13px; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 4px 15px var(--primary-glow); transition: var(--transition-fast);
  }
  .btn-sm-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px var(--primary-glow); }

  .btn-sm-outline {
    padding: 8px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);
    background: rgba(255, 255, 255, 0.05); color: var(--text-muted); font-weight: 600;
    font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
  }
  .btn-sm-outline:hover { background: rgba(255, 255, 255, 0.1); color: #fff; }
</style>
@endsection

@section('content')
<div class="admin-section">

  <!-- Header Navbar -->
  <header class="admin-header">
    <div class="admin-brand">
      <div class="admin-logo"><i class="fa-solid fa-shield-halved"></i></div>
      <span class="admin-brand-name">Botbari Admin</span>
      <span class="admin-brand-tag">v2.0 Control</span>
    </div>

    <div class="admin-user-menu">
      <div class="user-badge">
        <div class="avatar-circle">{{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}</div>
        <div style="display:flex; flex-direction:column">
          <span style="font-size:13px; font-weight:600">{{ $user->name }}</span>
          <span style="font-size:11px; color:var(--text-muted)">+880 {{ $user->phone }}</span>
        </div>
      </div>

      <!-- LOGOUT BUTTON -->
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn-logout" title="Log Out">
          <i class="fa-solid fa-power-off"></i>
          <span>Logout</span>
        </button>
      </form>
    </div>
  </header>

  <!-- Main Container -->
  <main class="admin-container">

    <!-- Flash Status Notifications -->
    @if(session('status'))
      <div class="flash-banner flash-success">
        <div><i class="fa-solid fa-circle-check"></i> {{ session('status') }}</div>
      </div>
    @endif

    @if(session('error'))
      <div class="flash-banner flash-danger">
        <div><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
      </div>
    @endif

    <!-- Hero Welcome Banner -->
    <section class="hero-panel glass-panel">
      <div>
        <div class="status-badge-live">
          <span class="pulse-dot"></span> Botbari OTP Gateway Active & Live (Sender ID: {{ $senderId }})
        </div>
        <h2 class="hero-title">Welcome back, {{ $user->name }}!</h2>
        <p class="hero-subtitle">
          Manage your verified mobile user database, monitor real-time Botbari OTP dispatch metrics, and audit system security activity from this central dashboard.
        </p>
      </div>
      <div style="font-size:75px; color:rgba(99,102,241,0.25); display:flex; align-items:center; justify-content:center">
        <i class="fa-solid fa-chart-line"></i>
      </div>
    </section>

    <!-- Top Metrics Summary Cards -->
    <section class="metrics-grid">

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Total Registered Users</span>
          <span class="metric-value">{{ $totalUsers }}</span>
          <span class="metric-sub">OTP Verified Accounts</span>
        </div>
        <div class="metric-icon-box icon-indigo">
          <i class="fa-solid fa-users"></i>
        </div>
      </div>

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Total OTPs Sent</span>
          <span class="metric-value">{{ $totalOtpsSent }}</span>
          <span class="metric-sub">Dispatched via DianaHost API</span>
        </div>
        <div class="metric-icon-box icon-violet">
          <i class="fa-solid fa-paper-plane"></i>
        </div>
      </div>

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Verified OTP Sessions</span>
          <span class="metric-value">{{ $verifiedOtpsCount }}</span>
          <span class="metric-sub">Successfully Authenticated</span>
        </div>
        <div class="metric-icon-box icon-emerald">
          <i class="fa-solid fa-circle-check"></i>
        </div>
      </div>

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Active / Pending OTPs</span>
          <span class="metric-value">{{ $pendingOtpsCount }}</span>
          <span class="metric-sub">Within 5-min Expiry Window</span>
        </div>
        <div class="metric-icon-box icon-cyan">
          <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
      </div>

    </section>

    <!-- Main Content Two-Column Grid -->
    <div class="content-grid">

      <!-- Column 1: Registered Accounts Table -->
      <div class="panel-card glass-panel">
        <div class="panel-header">
          <h3 class="panel-title">
            <i class="fa-solid fa-address-book" style="color:var(--primary)"></i>
            Registered Users Directory
          </h3>
          <span class="metric-sub">Latest {{ count($recentUsers) }} Accounts</span>
        </div>

        <div class="table-responsive">
          <table class="custom-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Mobile Number</th>
                <th>Status</th>
                <th>Registered At</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentUsers as $u)
                <tr>
                  <td><strong>#{{ $u->id }}</strong></td>
                  <td>
                    <div style="display:flex; align-items:center; gap:8px">
                      <div class="avatar-circle" style="width:26px; height:26px; font-size:11px">
                        {{ strtoupper(substr($u->name, 0, 1)) }}
                      </div>
                      <span style="font-weight:600">{{ $u->name }}</span>
                    </div>
                  </td>
                  <td>+880 {{ $u->phone }}</td>
                  <td>
                    <span class="badge-status badge-success">
                      <i class="fa-solid fa-check"></i> Verified
                    </span>
                  </td>
                  <td>{{ $u->created_at ? $u->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" style="text-align:center; padding:24px; color:var(--text-dim)">
                    No registered users in system yet.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Column 2: Live SMS Gateway Test Widget & System Info -->
      <div style="display:flex; flex-direction:column; gap:24px">

        <!-- Live SMS Testing Panel -->
        <div class="panel-card glass-panel">
          <div class="panel-header">
            <h3 class="panel-title">
              <i class="fa-solid fa-comment-sms" style="color:var(--accent-violet)"></i>
              Live DianaHost SMS Test
            </h3>
          </div>

          <form action="{{ route('admin.test-sms') }}" method="POST" class="sms-widget-form">
            @csrf
            <div class="widget-input-group">
              <label for="test-phone">Recipient Mobile Number</label>
              <input type="tel" id="test-phone" name="phone" placeholder="e.g. 01712345678" required>
            </div>

            <div class="widget-input-group">
              <label for="test-message">SMS Text Message</label>
              <textarea id="test-message" name="message" rows="3" required>Test SMS from DianaHost Admin Panel. Gateway connection verified!</textarea>
            </div>

            <button type="submit" class="btn-sm-primary">
              <span>Send Live SMS via DianaHost</span>
              <i class="fa-solid fa-paper-plane"></i>
            </button>
          </form>
        </div>

        <!-- DianaHost Gateway Connection Info -->
        <div class="panel-card glass-panel" style="gap:14px">
          <h3 class="panel-title" style="font-size:15px">
            <i class="fa-solid fa-server" style="color:var(--accent-emerald)"></i>
            Gateway Configuration
          </h3>
          <div style="font-size:12px; display:flex; flex-direction:column; gap:8px">
            <div style="display:flex; justify-content:space-between">
              <span style="color:var(--text-muted)">API Key Status:</span>
              <span style="color:var(--accent-emerald); font-weight:600">Configured (.env)</span>
            </div>
            <div style="display:flex; justify-content:space-between">
              <span style="color:var(--text-muted)">Sender ID:</span>
              <span style="font-weight:600">{{ $senderId }}</span>
            </div>
            <div style="display:flex; justify-content:space-between">
              <span style="color:var(--text-muted)">API Endpoint:</span>
              <span style="font-family:monospace">zendsms.com/api/v1/send-sms</span>
            </div>
            <div style="display:flex; justify-content:space-between">
              <span style="color:var(--text-muted)">SSL Verification:</span>
              <span>Bypassed (Local Dev)</span>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- OTP Audit Logs Table -->
    <section class="panel-card glass-panel">
      <div class="panel-header">
        <h3 class="panel-title">
          <i class="fa-solid fa-list-check" style="color:var(--accent-cyan)"></i>
          Recent OTP Dispatch & Verification Audit Log
        </h3>
        
        <form action="{{ route('admin.purge-otps') }}" method="POST">
          @csrf
          <button type="submit" class="btn-sm-outline" onclick="return confirm('Clean up all expired OTP records from database?')">
            <i class="fa-solid fa-broom"></i>
            <span>Purge Expired Logs</span>
          </button>
        </form>
      </div>

      <div class="table-responsive">
        <table class="custom-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Phone Number</th>
              <th>Attempts</th>
              <th>Sent At</th>
              <th>Expiry</th>
              <th>Verification Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentOtps as $otp)
              <tr>
                <td><strong>#{{ $otp->id }}</strong></td>
                <td>{{ $otp->phone }}</td>
                <td>{{ $otp->attempts }} / 5</td>
                <td>{{ $otp->last_sent_at ? $otp->last_sent_at->format('d M, h:i:s A') : 'N/A' }}</td>
                <td>
                  @if($otp->isExpired())
                    <span style="color:var(--text-dim)">Expired</span>
                  @else
                    <span style="color:var(--primary)">Expires {{ $otp->expires_at->diffForHumans() }}</span>
                  @endif
                </td>
                <td>
                  @if($otp->verified_at)
                    <span class="badge-status badge-success"><i class="fa-solid fa-check-double"></i> Verified</span>
                  @elseif($otp->isExpired())
                    <span class="badge-status badge-danger"><i class="fa-solid fa-circle-xmark"></i> Expired</span>
                  @else
                    <span class="badge-status badge-warning"><i class="fa-solid fa-hourglass-half"></i> Pending</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align:center; padding:24px; color:var(--text-dim)">
                  No OTP logs available in database.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

  </main>

</div>
@endsection
