@extends('layouts.app')

@section('title', 'Admin Dashboard - ZendSMS Laravel')

@section('styles')
<style>
  .admin-header {
    height: 72px; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(16px);
    border-bottom: 1px solid var(--border-color); padding: 0 36px;
    display: flex; align-items: center; justify-content: space-between;
    position: sticky; top: 0; z-index: 100;
  }
  .admin-brand { display: flex; align-items: center; gap: 12px; }
  .admin-logo {
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, var(--primary), var(--accent-violet));
    display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px;
  }
  .admin-brand-name { font-family: var(--font-heading); font-size: 18px; font-weight: 700; }

  .admin-user-menu { display: flex; align-items: center; gap: 24px; }
  .user-badge {
    display: flex; align-items: center; gap: 12px; padding: 6px 14px;
    background: rgba(255, 255, 255, 0.04); border: 1px solid var(--border-color);
    border-radius: var(--radius-full);
  }
  .avatar-circle {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-violet), var(--accent-cyan));
    display: flex; align-items: center; justify-content: center; font-weight: 700; color: #fff;
  }

  .btn-logout {
    background: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.3);
    color: #fca5a5; padding: 8px 18px; border-radius: var(--radius-md); font-size: 13px;
    font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;
    transition: var(--transition-fast);
  }
  .btn-logout:hover {
    background: var(--accent-rose); color: #fff; border-color: var(--accent-rose);
    box-shadow: 0 4px 15px rgba(244, 63, 94, 0.4);
  }

  .admin-container {
    max-width: 1100px; width: 100%; margin: 36px auto; padding: 0 24px;
    display: flex; flex-direction: column; gap: 28px;
  }

  .welcome-card {
    padding: 36px; border-radius: var(--radius-lg); display: flex;
    justify-content: space-between; align-items: center;
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9));
  }
  .welcome-title { font-family: var(--font-heading); font-size: 32px; font-weight: 700; margin-bottom: 10px; }
  .welcome-desc { font-size: 15px; color: var(--text-muted); line-height: 1.6; }

  .status-pill {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3);
    color: var(--accent-emerald); padding: 6px 14px; border-radius: var(--radius-full);
    font-size: 12px; font-weight: 600; margin-bottom: 16px;
  }

  .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
  .dashboard-card { padding: 24px; border-radius: var(--radius-md); display: flex; align-items: center; gap: 20px; }
  .card-icon {
    width: 54px; height: 54px; border-radius: 14px; display: flex;
    align-items: center; justify-content: center; font-size: 22px;
  }
  .card-icon.blue { background: rgba(99, 102, 241, 0.15); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.3); }
  .card-icon.purple { background: rgba(139, 92, 246, 0.15); color: var(--accent-violet); border: 1px solid rgba(139, 92, 246, 0.3); }
  .card-icon.emerald { background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.3); }

  .info-panel { border-radius: var(--radius-lg); padding: 28px; }
  .info-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px dashed rgba(255, 255, 255, 0.05); }
</style>
@endsection

@section('content')
<div class="admin-section">
  
  <!-- Header Navbar -->
  <header class="admin-header">
    <div class="admin-brand">
      <div class="admin-logo"><i class="fa-solid fa-shield-halved"></i></div>
      <span class="admin-brand-name">Nexus Admin Panel</span>
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
    
    <!-- Welcome Hero -->
    <section class="welcome-card glass-panel">
      <div>
        <div class="status-pill">
          <i class="fa-solid fa-circle-check"></i> Account Verified via ZendSMS OTP
        </div>
        <h2 class="welcome-title">Welcome to Admin Panel!</h2>
        <p class="welcome-desc">You are logged into the administrator dashboard. Mobile number verification and password authentication have been successfully confirmed.</p>
      </div>
      <div style="font-size:70px; color:rgba(99,102,241,0.2)">
        <i class="fa-solid fa-user-shield"></i>
      </div>
    </section>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
      <div class="dashboard-card glass-panel">
        <div class="card-icon blue"><i class="fa-solid fa-mobile-retro"></i></div>
        <div>
          <h3 style="font-size:13px; color:var(--text-muted)">OTP Verification Engine</h3>
          <p style="font-size:18px; font-weight:700; color:var(--accent-emerald)">ZendSMS Active</p>
          <span style="font-size:12px; color:var(--text-dim)">No OTP needed for subsequent logins</span>
        </div>
      </div>

      <div class="dashboard-card glass-panel">
        <div class="card-icon purple"><i class="fa-solid fa-clock"></i></div>
        <div>
          <h3 style="font-size:13px; color:var(--text-muted)">Current Session Time</h3>
          <p style="font-size:18px; font-weight:700">{{ $sessionTime }}</p>
          <span style="font-size:12px; color:var(--text-dim)">Encrypted Laravel Session</span>
        </div>
      </div>

      <div class="dashboard-card glass-panel">
        <div class="card-icon emerald"><i class="fa-solid fa-database"></i></div>
        <div>
          <h3 style="font-size:13px; color:var(--text-muted)">Database Storage</h3>
          <p style="font-size:18px; font-weight:700">SQLite Database</p>
          <span style="font-size:12px; color:var(--text-dim)">User ID #{{ $user->id }}</span>
        </div>
      </div>
    </div>

    <!-- Info Panel -->
    <section class="info-panel glass-panel">
      <h3 style="font-size:16px; margin-bottom:16px"><i class="fa-solid fa-circle-info"></i> Account Details</h3>
      <div class="info-row">
        <span style="color:var(--text-muted)">Account Holder:</span>
        <span style="font-weight:600">{{ $user->name }}</span>
      </div>
      <div class="info-row">
        <span style="color:var(--text-muted)">Registered Mobile:</span>
        <span style="font-weight:600">+880 {{ $user->phone }}</span>
      </div>
      <div class="info-row">
        <span style="color:var(--text-muted)">OTP Verification Status:</span>
        <span style="color:var(--accent-emerald); font-weight:600"><i class="fa-solid fa-shield"></i> Verified (ZendSMS OTP)</span>
      </div>
      <div class="info-row">
        <span style="color:var(--text-muted)">Account Created:</span>
        <span style="font-weight:600">{{ $verifiedAt }}</span>
      </div>
    </section>

  </main>

</div>
@endsection
