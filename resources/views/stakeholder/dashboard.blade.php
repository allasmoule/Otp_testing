@extends('layouts.app')

@section('title', 'Main Stakeholder Management Panel - Botbari')

@section('styles')
<style>
  :root {
    --card-bg: rgba(15, 23, 42, 0.85);
    --card-border: rgba(255, 255, 255, 0.08);
    --gold-color: #f59e0b;
    --bkash-color: #e2136e;
    --nagad-color: #f7931e;
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
    background: linear-gradient(135deg, var(--gold-color), #d97706);
    display: flex; align-items: center; justify-content: center; color: #fff; font-size: 20px;
    box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
  }
  .admin-brand-name { font-family: var(--font-heading); font-size: 20px; font-weight: 700; color: var(--text-main); }
  .admin-brand-tag { font-size: 11px; background: rgba(245, 158, 11, 0.2); color: var(--gold-color); padding: 3px 8px; border-radius: 6px; font-weight: 700; }

  .admin-user-menu { display: flex; align-items: center; gap: 20px; }
  .user-badge {
    display: flex; align-items: center; gap: 12px; padding: 6px 16px;
    background: rgba(255, 255, 255, 0.04); border: 1px solid var(--border-color);
    border-radius: var(--radius-full);
  }
  .avatar-circle {
    width: 36px; height: 36px; border-radius: 50%;
    background: linear-gradient(135deg, var(--gold-color), #b45309);
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
    display: flex; flex-direction: column; gap: 32px;
  }

  /* HERO BANNER */
  .hero-panel {
    padding: 32px 36px; border-radius: var(--radius-lg); position: relative; overflow: hidden;
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95));
    border: 1px solid rgba(245, 158, 11, 0.3); display: flex; justify-content: space-between; align-items: center;
  }
  .hero-title { font-family: var(--font-heading); font-size: 28px; font-weight: 700; margin-bottom: 8px; color: var(--text-main); }
  .hero-subtitle { font-size: 14px; color: var(--text-muted); max-width: 620px; line-height: 1.6; }

  .status-badge-live {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.3);
    color: #fbbf24; padding: 6px 14px; border-radius: var(--radius-full);
    font-size: 12px; font-weight: 700; margin-bottom: 14px;
  }
  .pulse-dot { width: 8px; height: 8px; background: #f59e0b; border-radius: 50%; box-shadow: 0 0 10px #f59e0b; animation: pulse 1.8s infinite; }
  @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(1.2); } }

  /* METRICS GRID */
  .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px; }
  .metric-card {
    padding: 24px; border-radius: var(--radius-lg); background: var(--bg-card);
    border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;
    transition: var(--transition-fast);
  }
  .metric-card:hover { transform: translateY(-3px); border-color: rgba(245, 158, 11, 0.4); box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4); }
  .metric-info { display: flex; flex-direction: column; gap: 4px; }
  .metric-label { font-size: 13px; color: var(--text-muted); font-weight: 500; }
  .metric-value { font-family: var(--font-heading); font-size: 28px; font-weight: 700; color: var(--text-main); }
  .metric-sub { font-size: 11px; color: var(--text-dim); }

  .metric-icon-box {
    width: 52px; height: 52px; border-radius: var(--radius-md); display: flex;
    align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
  }
  .icon-gold { background: rgba(245, 158, 11, 0.15); color: var(--gold-color); border: 1px solid rgba(245, 158, 11, 0.3); }
  .icon-indigo { background: rgba(99, 102, 241, 0.15); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.3); }
  .icon-emerald { background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.3); }
  .icon-pink { background: rgba(226, 19, 110, 0.15); color: var(--bkash-color); border: 1px solid rgba(226, 19, 110, 0.3); }

  /* PANEL CARDS & TABLES */
  .panel-card {
    background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);
    padding: 28px; display: flex; flex-direction: column; gap: 20px;
  }
  .panel-header { display: flex; justify-content: space-between; align-items: center; }
  .panel-title { font-family: var(--font-heading); font-size: 20px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px; }

  .table-responsive { width: 100%; overflow-x: auto; }
  .custom-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
  .custom-table th {
    padding: 14px 18px; background: rgba(255, 255, 255, 0.03); color: var(--text-muted);
    font-weight: 600; border-bottom: 1px solid var(--border-color);
  }
  .custom-table td { padding: 16px 18px; border-bottom: 1px solid rgba(255, 255, 255, 0.04); color: var(--text-main); }
  .custom-table tr:hover td { background: rgba(255, 255, 255, 0.02); }

  .badge-status {
    display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 600;
  }
  .badge-success { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
  .badge-warning { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }

  .method-badge {
    display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
  }
  .method-bkash { background: rgba(226, 19, 110, 0.15); color: #f472b6; border: 1px solid rgba(226, 19, 110, 0.3); }
  .method-nagad { background: rgba(247, 147, 30, 0.15); color: #fb923c; border: 1px solid rgba(247, 147, 30, 0.3); }

  /* NAV TABS */
  .section-tabs { display: flex; gap: 12px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; margin-bottom: 20px; }
  .section-tab-btn {
    background: transparent; border: none; color: var(--text-muted); font-size: 15px; font-weight: 600;
    padding: 8px 16px; border-radius: var(--radius-md); cursor: pointer; display: flex; align-items: center; gap: 8px;
    transition: var(--transition-fast);
  }
  .section-tab-btn.active { background: rgba(245, 158, 11, 0.15); color: var(--gold-color); border: 1px solid rgba(245, 158, 11, 0.3); }
</style>
@endsection

@section('content')
<div class="admin-section">

  <!-- Header Navbar -->
  <header class="admin-header">
    <div class="admin-brand">
      <div class="admin-logo"><i class="fa-solid fa-crown"></i></div>
      <span class="admin-brand-name">Main Stakeholder Panel</span>
      <span class="admin-brand-tag">ID: 110071</span>
    </div>

    <div class="admin-user-menu">
      <div class="user-badge">
        <div class="avatar-circle">S</div>
        <div style="display:flex; flex-direction:column">
          <span style="font-size:13px; font-weight:600">{{ $user->name ?? 'Main Stakeholder' }}</span>
          <span style="font-size:11px; color:var(--gold-color)">Stakeholder ID: 110071</span>
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

    <!-- Hero Welcome Banner -->
    <section class="hero-panel glass-panel">
      <div>
        <div class="status-badge-live">
          <span class="pulse-dot"></span> Stakeholder Executive Control Panel Active (ID: 110071)
        </div>
        <h2 class="hero-title">Welcome, Main Stakeholder!</h2>
        <p class="hero-subtitle">
          Real-time oversight of all user accounts created in the system and complete ledger of bill payments processed through bKash and Nagad gateways.
        </p>
      </div>
      <div style="font-size:75px; color:rgba(245,158,11,0.25); display:flex; align-items:center; justify-content:center">
        <i class="fa-solid fa-chart-pie"></i>
      </div>
    </section>

    <!-- Top Metrics Overview Grid -->
    <section class="metrics-grid">

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Total Accounts Opened</span>
          <span class="metric-value">{{ $totalAccounts }}</span>
          <span class="metric-sub">Registered System Users</span>
        </div>
        <div class="metric-icon-box icon-indigo">
          <i class="fa-solid fa-users"></i>
        </div>
      </div>

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Total Bill Payments</span>
          <span class="metric-value">{{ $totalPaymentsCount }}</span>
          <span class="metric-sub">Completed Checkouts</span>
        </div>
        <div class="metric-icon-box icon-emerald">
          <i class="fa-solid fa-receipt"></i>
        </div>
      </div>

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Total Revenue Collected</span>
          <span class="metric-value">৳ {{ number_format($totalRevenue, 2) }}</span>
          <span class="metric-sub">BDT Currency</span>
        </div>
        <div class="metric-icon-box icon-gold">
          <i class="fa-solid fa-vault"></i>
        </div>
      </div>

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Gateway Payment Volume</span>
          <span class="metric-value" style="font-size:16px; color:var(--text-main); font-weight:600">
            bKash: ৳{{ number_format($bkashRevenue) }} | Nagad: ৳{{ number_format($nagadRevenue) }}
          </span>
          <span class="metric-sub">bKash ({{ $bkashCount }}) / Nagad ({{ $nagadCount }}) Transactions</span>
        </div>
        <div class="metric-icon-box icon-pink">
          <i class="fa-solid fa-building-columns"></i>
        </div>
      </div>

    </section>

    <!-- TAB 1: ALL ACCOUNTS OPENED (koto gulo account khulche sob list) -->
    <section class="panel-card glass-panel">
      <div class="panel-header">
        <h3 class="panel-title">
          <i class="fa-solid fa-users-gear" style="color:var(--primary)"></i>
          All Created Accounts Directory ({{ count($allUsers) }} Accounts)
        </h3>
        <span class="metric-sub">Koto gulo account khulche sob list</span>
      </div>

      <div class="table-responsive">
        <table class="custom-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Mobile Number</th>
              <th>Email / Identifier</th>
              <th>Role</th>
              <th>OTP Status</th>
              <th>Registration Date & Time</th>
            </tr>
          </thead>
          <tbody>
            @forelse($allUsers as $u)
              <tr>
                <td><strong>#{{ $u->id }}</strong></td>
                <td>
                  <div style="display:flex; align-items:center; gap:8px">
                    <div class="avatar-circle" style="width:28px; height:28px; font-size:12px">
                      {{ strtoupper(substr($u->name, 0, 1)) }}
                    </div>
                    <span style="font-weight:600">{{ $u->name }}</span>
                  </div>
                </td>
                <td><strong style="color:var(--primary)">+880 {{ $u->phone }}</strong></td>
                <td>{{ $u->email ?? 'N/A' }}</td>
                <td>
                  @if($u->role === 'stakeholder')
                    <span style="color:var(--gold-color); font-weight:700; font-size:11px; background:rgba(245,158,11,0.15); padding:3px 8px; border-radius:4px; border:1px solid rgba(245,158,11,0.3)">
                      <i class="fa-solid fa-crown"></i> Stakeholder
                    </span>
                  @else
                    <span style="color:var(--text-muted); font-size:12px">Standard User</span>
                  @endif
                </td>
                <td>
                  @if($u->is_otp_verified)
                    <span class="badge-status badge-success"><i class="fa-solid fa-circle-check"></i> OTP Verified</span>
                  @else
                    <span class="badge-status badge-warning"><i class="fa-solid fa-hourglass-half"></i> Pending</span>
                  @endif
                </td>
                <td>{{ $u->created_at ? $u->created_at->format('d M Y, h:i:s A') : 'N/A' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align:center; padding:32px; color:var(--text-dim)">
                  No registered accounts in system database yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <!-- TAB 2: ALL BILL PAYMENTS (koto gulo payment hoiche sob list) -->
    <section class="panel-card glass-panel">
      <div class="panel-header">
        <h3 class="panel-title">
          <i class="fa-solid fa-money-bill-transfer" style="color:var(--accent-emerald)"></i>
          All Bill Payments Ledger ({{ count($allPayments) }} Transactions)
        </h3>
        <span class="metric-sub">Koto gulo payment hoiche sob list</span>
      </div>

      <div class="table-responsive">
        <table class="custom-table">
          <thead>
            <tr>
              <th>Trx ID</th>
              <th>Payer Name</th>
              <th>Bill Category</th>
              <th>Amount Paid</th>
              <th>Payment Gateway</th>
              <th>Wallet Account</th>
              <th>Date & Time</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($allPayments as $p)
              <tr>
                <td><strong style="font-family:monospace; color:var(--primary)">{{ $p->trx_id }}</strong></td>
                <td><span style="font-weight:600">{{ $p->payer_name }}</span></td>
                <td><strong>{{ $p->bill_type }}</strong></td>
                <td><strong style="color:var(--accent-emerald)">৳ {{ number_format($p->amount, 2) }} BDT</strong></td>
                <td>
                  @if(strtolower($p->payment_method) === 'bkash')
                    <span class="method-badge method-bkash"><i class="fa-solid fa-b"></i> bKash</span>
                  @else
                    <span class="method-badge method-nagad"><i class="fa-solid fa-n"></i> Nagad</span>
                  @endif
                </td>
                <td>{{ $p->account_number }}</td>
                <td>{{ $p->created_at ? $p->created_at->format('d M Y, h:i:s A') : 'N/A' }}</td>
                <td>
                  <span style="color:#34d399; font-weight:700; font-size:12px">
                    <i class="fa-solid fa-circle-check"></i> SUCCESS
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" style="text-align:center; padding:32px; color:var(--text-dim)">
                  No bill payments recorded in database yet.
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
