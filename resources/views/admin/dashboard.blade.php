@extends('layouts.app')

@section('title', 'Botbari Bill Payment & Admin Portal')

@section('styles')
<style>
  :root {
    --card-bg: rgba(15, 23, 42, 0.85);
    --card-border: rgba(255, 255, 255, 0.08);
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
    display: flex; flex-direction: column; gap: 32px;
  }

  /* HERO BANNER */
  .hero-panel {
    padding: 32px 36px; border-radius: var(--radius-lg); position: relative; overflow: hidden;
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.95));
    border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;
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
  .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
  .metric-card {
    padding: 24px; border-radius: var(--radius-lg); background: var(--bg-card);
    border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;
  }
  .metric-info { display: flex; flex-direction: column; gap: 4px; }
  .metric-label { font-size: 13px; color: var(--text-muted); font-weight: 500; }
  .metric-value { font-family: var(--font-heading); font-size: 28px; font-weight: 700; color: var(--text-main); }
  .metric-sub { font-size: 11px; color: var(--text-dim); }

  .metric-icon-box {
    width: 52px; height: 52px; border-radius: var(--radius-md); display: flex;
    align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
  }
  .icon-indigo { background: rgba(99, 102, 241, 0.15); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.3); }
  .icon-emerald { background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.3); }
  .icon-pink { background: rgba(226, 19, 110, 0.15); color: var(--bkash-color); border: 1px solid rgba(226, 19, 110, 0.3); }

  /* SECTION TITLE */
  .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
  .section-title { font-family: var(--font-heading); font-size: 22px; font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 10px; }

  /* BILL CARDS GRID */
  .bills-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; }
  .bill-card {
    background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);
    padding: 28px; display: flex; flex-direction: column; justify-content: space-between; gap: 20px;
    position: relative; overflow: hidden; transition: var(--transition-fast);
  }
  .bill-card:hover { transform: translateY(-4px); border-color: var(--primary); box-shadow: 0 14px 35px rgba(99, 102, 241, 0.2); }
  .bill-card-top { display: flex; align-items: flex-start; justify-content: space-between; }
  .bill-icon-wrapper {
    width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center;
    font-size: 24px; color: #fff; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
  }
  .bg-edu { background: linear-gradient(135deg, #6366f1, #4f46e5); }
  .bg-elec { background: linear-gradient(135deg, #f59e0b, #d97706); }
  .bg-net { background: linear-gradient(135deg, #06b6d4, #0891b2); }
  .bg-water { background: linear-gradient(135deg, #10b981, #059669); }
  .bg-gas { background: linear-gradient(135deg, #f43f5e, #e11d48); }
  .bg-tv { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

  .bill-badge-price {
    background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);
    padding: 6px 14px; border-radius: var(--radius-full); font-family: var(--font-heading);
    font-weight: 700; font-size: 16px; color: var(--text-main); display: flex; align-items: center; gap: 4px;
  }
  .bill-badge-price span { color: var(--primary); font-size: 14px; }

  .bill-details h3 { font-family: var(--font-heading); font-size: 20px; font-weight: 700; color: var(--text-main); margin-bottom: 6px; }
  .bill-details p { font-size: 13px; color: var(--text-muted); line-height: 1.5; }

  .btn-pay-bill {
    width: 100%; padding: 13px; border: none; border-radius: var(--radius-md);
    background: linear-gradient(135deg, var(--primary), var(--accent-violet));
    color: #fff; font-weight: 600; font-size: 14px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    box-shadow: 0 6px 18px var(--primary-glow); transition: var(--transition-fast);
  }
  .btn-pay-bill:hover { transform: translateY(-2px); box-shadow: 0 10px 25px var(--primary-glow); }

  /* PAID BILLS HISTORY TABLE */
  .panel-card {
    background: var(--bg-card); border-radius: var(--radius-lg); border: 1px solid var(--border-color);
    padding: 28px; display: flex; flex-direction: column; gap: 20px;
  }
  .table-responsive { width: 100%; overflow-x: auto; }
  .custom-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
  .custom-table th {
    padding: 14px 18px; background: rgba(255, 255, 255, 0.03); color: var(--text-muted);
    font-weight: 600; border-bottom: 1px solid var(--border-color);
  }
  .custom-table td { padding: 16px 18px; border-bottom: 1px solid rgba(255, 255, 255, 0.04); color: var(--text-main); }
  .custom-table tr:hover td { background: rgba(255, 255, 255, 0.02); }

  .method-badge {
    display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
  }
  .method-bkash { background: rgba(226, 19, 110, 0.15); color: #f472b6; border: 1px solid rgba(226, 19, 110, 0.3); }
  .method-nagad { background: rgba(247, 147, 30, 0.15); color: #fb923c; border: 1px solid rgba(247, 147, 30, 0.3); }

  /* MODAL / POPUP OVERLAYS */
  .modal-overlay {
    position: fixed; inset: 0; background: rgba(5, 8, 15, 0.85); backdrop-filter: blur(12px);
    display: flex; align-items: center; justify-content: center; padding: 20px;
    z-index: 1000; opacity: 0; pointer-events: none; transition: opacity 0.3s ease-out;
  }
  .modal-overlay.active { opacity: 1; pointer-events: auto; }

  .modal-box {
    width: 100%; max-width: 480px; background: #0f172a; border: 1px solid var(--border-color);
    border-radius: var(--radius-lg); padding: 32px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
    transform: scale(0.95); transition: transform 0.3s ease-out; position: relative;
  }
  .modal-overlay.active .modal-box { transform: scale(1); }

  .modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
  .modal-title { font-family: var(--font-heading); font-size: 20px; font-weight: 700; color: var(--text-main); }
  .btn-close-modal { background: transparent; border: none; color: var(--text-muted); font-size: 18px; cursor: pointer; }
  .btn-close-modal:hover { color: #fff; }

  /* PAYMENT METHOD SELECTION CARDS (bKash & Nagad) */
  .payment-methods-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 10px; }
  .method-card {
    border: 2px solid var(--border-color); border-radius: var(--radius-md); padding: 18px 14px;
    background: rgba(255, 255, 255, 0.02); cursor: pointer; display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 10px; transition: var(--transition-fast); position: relative;
  }
  .method-card:hover { border-color: rgba(255, 255, 255, 0.2); }
  
  .method-card.selected-bkash {
    border-color: var(--bkash-color); background: rgba(226, 19, 110, 0.1);
    box-shadow: 0 0 15px rgba(226, 19, 110, 0.25);
  }
  .method-card.selected-nagad {
    border-color: var(--nagad-color); background: rgba(247, 147, 30, 0.1);
    box-shadow: 0 0 15px rgba(247, 147, 30, 0.25);
  }

  .method-badge-icon {
    width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center;
    justify-content: center; font-weight: 800; font-size: 18px; color: #fff;
  }
  .icon-bkash-bg { background: var(--bkash-color); box-shadow: 0 4px 12px rgba(226, 19, 110, 0.4); }
  .icon-nagad-bg { background: var(--nagad-color); box-shadow: 0 4px 12px rgba(247, 147, 30, 0.4); }

  .method-name { font-weight: 700; font-size: 14px; color: var(--text-main); }
  .check-icon { position: absolute; top: 10px; right: 10px; font-size: 14px; display: none; }
  .selected-bkash .check-icon { display: block; color: var(--bkash-color); }
  .selected-nagad .check-icon { display: block; color: var(--nagad-color); }

  /* MERCHANT CHECKOUT THEMED MODAL */
  .merchant-header {
    margin: -32px -32px 24px -32px; padding: 24px 32px; border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    display: flex; align-items: center; justify-content: space-between; color: #fff;
  }
  .merchant-bkash { background: linear-gradient(135deg, #e2136e, #b00e54); }
  .merchant-nagad { background: linear-gradient(135deg, #f7931e, #d87708); }

  .merchant-logo { font-family: var(--font-heading); font-size: 22px; font-weight: 800; display: flex; align-items: center; gap: 8px; }
  .merchant-amount-tag { background: rgba(0, 0, 0, 0.2); padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 15px; }

  /* SUCCESS MODAL ANIMATION */
  .success-checkmark-box {
    width: 80px; height: 80px; border-radius: 50%; background: rgba(16, 185, 129, 0.15);
    border: 2px solid #10b981; display: flex; align-items: center; justify-content: center;
    font-size: 36px; color: #34d399; margin: 0 auto 20px; animation: bounceIn 0.5s ease-out;
  }
  @keyframes bounceIn { 0% { transform: scale(0.3); opacity: 0; } 50% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }

  .receipt-box {
    background: rgba(255, 255, 255, 0.03); border: 1px dashed var(--border-color);
    border-radius: var(--radius-md); padding: 18px; margin: 20px 0; display: flex;
    flex-direction: column; gap: 10px; font-size: 13px;
  }
  .receipt-row { display: flex; justify-content: space-between; align-items: center; }
  .receipt-row span { color: var(--text-muted); }
  .receipt-row strong { color: var(--text-main); }
</style>
@endsection

@section('content')
<div class="admin-section">

  <!-- Header Navbar -->
  <header class="admin-header">
    <div class="admin-brand">
      <div class="admin-logo"><i class="fa-solid fa-receipt"></i></div>
      <span class="admin-brand-name">Botbari Bill Pay</span>
      <span class="admin-brand-tag">v2.0 Gateway</span>
    </div>

    <div class="admin-user-menu">
      <div class="user-badge">
        <div class="avatar-circle">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
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

    <!-- Hero Welcome Banner -->
    <section class="hero-panel glass-panel">
      <div>
        <div class="status-badge-live">
          <span class="pulse-dot"></span> bKash & Nagad Merchant Checkout Gateway Online
        </div>
        <h2 class="hero-title">Welcome, {{ $user->name }}!</h2>
        <p class="hero-subtitle">
          Pay your monthly utility bills instantly using bKash or Nagad. Fast, secure 1-click merchant checkout system.
        </p>
      </div>
      <div style="font-size:75px; color:rgba(99,102,241,0.25); display:flex; align-items:center; justify-content:center">
        <i class="fa-solid fa-wallet"></i>
      </div>
    </section>

    <!-- Top Metrics Cards -->
    <section class="metrics-grid">

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Total Bills Paid</span>
          <span class="metric-value" id="metric-bills-count">{{ $totalBillsPaidCount }}</span>
          <span class="metric-sub">Successful Transactions</span>
        </div>
        <div class="metric-icon-box icon-indigo">
          <i class="fa-solid fa-check-double"></i>
        </div>
      </div>

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Total Amount Paid</span>
          <span class="metric-value">৳ <span id="metric-amount-sum">{{ number_format($totalAmountCollected, 2) }}</span></span>
          <span class="metric-sub">BDT Currency</span>
        </div>
        <div class="metric-icon-box icon-emerald">
          <i class="fa-solid fa-money-bill-wave"></i>
        </div>
      </div>

      <div class="metric-card glass-panel">
        <div class="metric-info">
          <span class="metric-label">Payment Methods</span>
          <span class="metric-value" style="font-size:18px; color:var(--text-main)">bKash & Nagad</span>
          <span class="metric-sub">Merchant API Integration</span>
        </div>
        <div class="metric-icon-box icon-pink">
          <i class="fa-solid fa-building-columns"></i>
        </div>
      </div>

    </section>

    <!-- BILL PAYMENT SERVICES CARDS SECTION -->
    <section>
      <div class="section-header">
        <h2 class="section-title">
          <i class="fa-solid fa-bolt-lightning" style="color:var(--primary)"></i>
          Utility & Service Bills
        </h2>
      </div>
      <p style="font-size:13px; color:var(--text-muted); margin-bottom:20px;">
        Select a bill service below to initiate instant bKash or Nagad payment.
      </p>

      <div class="bills-grid">

        <!-- 1. Education Bill (10 Taka) -->
        <div class="bill-card glass-panel">
          <div class="bill-card-top">
            <div class="bill-icon-wrapper bg-edu"><i class="fa-solid fa-graduation-cap"></i></div>
            <div class="bill-badge-price"><span>৳</span> 10.00</div>
          </div>
          <div class="bill-details">
            <h3>Education Bill</h3>
            <p>School, College & Tuition fees payment. Fast fee clearing service.</p>
          </div>
          <button class="btn-pay-bill" onclick="openBillPayModal('Education Bill', 10)">
            <span>Pay Education Bill</span>
            <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>

        <!-- 2. Electricity Bill (5 Taka) -->
        <div class="bill-card glass-panel">
          <div class="bill-card-top">
            <div class="bill-icon-wrapper bg-elec"><i class="fa-solid fa-bolt"></i></div>
            <div class="bill-badge-price"><span>৳</span> 5.00</div>
          </div>
          <div class="bill-details">
            <h3>Electricity Bill</h3>
            <p>Prepaid and Postpaid electricity bill pay for DESCO / NESCO / BPDB.</p>
          </div>
          <button class="btn-pay-bill" onclick="openBillPayModal('Electricity Bill', 5)">
            <span>Pay Electricity Bill</span>
            <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>

        <!-- 3. Internet Bill (15 Taka) -->
        <div class="bill-card glass-panel">
          <div class="bill-card-top">
            <div class="bill-icon-wrapper bg-net"><i class="fa-solid fa-wifi"></i></div>
            <div class="bill-badge-price"><span>৳</span> 15.00</div>
          </div>
          <div class="bill-details">
            <h3>Internet Bill</h3>
            <p>Broadband ISP, Wi-Fi & Fiber internet monthly package subscription bill.</p>
          </div>
          <button class="btn-pay-bill" onclick="openBillPayModal('Internet Bill', 15)">
            <span>Pay Internet Bill</span>
            <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>

        <!-- 4. Water Bill (8 Taka) -->
        <div class="bill-card glass-panel">
          <div class="bill-card-top">
            <div class="bill-icon-wrapper bg-water"><i class="fa-solid fa-droplet"></i></div>
            <div class="bill-badge-price"><span>৳</span> 8.00</div>
          </div>
          <div class="bill-details">
            <h3>Water Utility Bill</h3>
            <p>WASA municipal water supply and sewer bill payment system.</p>
          </div>
          <button class="btn-pay-bill" onclick="openBillPayModal('Water Bill', 8)">
            <span>Pay Water Bill</span>
            <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>

        <!-- 5. Gas Bill (12 Taka) -->
        <div class="bill-card glass-panel">
          <div class="bill-card-top">
            <div class="bill-icon-wrapper bg-gas"><i class="fa-solid fa-fire"></i></div>
            <div class="bill-badge-price"><span>৳</span> 12.00</div>
          </div>
          <div class="bill-details">
            <h3>Gas Utility Bill</h3>
            <p>Titas & Karnaphuli gas supply connection bill clearance.</p>
          </div>
          <button class="btn-pay-bill" onclick="openBillPayModal('Gas Bill', 12)">
            <span>Pay Gas Bill</span>
            <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>

        <!-- 6. Cable TV / DTH Bill (20 Taka) -->
        <div class="bill-card glass-panel">
          <div class="bill-card-top">
            <div class="bill-icon-wrapper bg-tv"><i class="fa-solid fa-tv"></i></div>
            <div class="bill-badge-price"><span>৳</span> 20.00</div>
          </div>
          <div class="bill-details">
            <h3>Cable TV & DTH</h3>
            <p>Akash DTH & Local Cable Dish network monthly subscription payment.</p>
          </div>
          <button class="btn-pay-bill" onclick="openBillPayModal('Cable TV Bill', 20)">
            <span>Pay Cable Bill</span>
            <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>

      </div>
    </section>

    <!-- PAID BILLS HISTORY TABLE -->
    <section class="panel-card glass-panel">
      <div style="display:flex; justify-content:space-between; align-items:center">
        <h3 class="section-title" style="font-size:18px">
          <i class="fa-solid fa-clock-rotate-left" style="color:var(--accent-cyan)"></i>
          Recent Paid Bills History
        </h3>
        <span class="metric-sub" id="history-count-tag">Latest {{ count($recentPayments) }} Transactions</span>
      </div>

      <div class="table-responsive">
        <table class="custom-table">
          <thead>
            <tr>
              <th>Trx ID</th>
              <th>Payer Name</th>
              <th>Bill Category</th>
              <th>Amount (BDT)</th>
              <th>Payment Method</th>
              <th>Account Number</th>
              <th>Date & Time</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="history-table-body">
            @forelse($recentPayments as $p)
              <tr>
                <td><strong style="font-family:monospace; color:var(--primary)">{{ $p->trx_id }}</strong></td>
                <td><span style="font-weight:600">{{ $p->payer_name }}</span></td>
                <td>{{ $p->bill_type }}</td>
                <td><strong>৳ {{ number_format($p->amount, 2) }}</strong></td>
                <td>
                  @if(strtolower($p->payment_method) === 'bkash')
                    <span class="method-badge method-bkash"><i class="fa-solid fa-b"></i> bKash</span>
                  @else
                    <span class="method-badge method-nagad"><i class="fa-solid fa-n"></i> Nagad</span>
                  @endif
                </td>
                <td>{{ $p->account_number }}</td>
                <td>{{ $p->created_at ? $p->created_at->format('d M Y, h:i A') : 'N/A' }}</td>
                <td>
                  <span style="color:#34d399; font-weight:700; font-size:12px">
                    <i class="fa-solid fa-circle-check"></i> SUCCESS
                  </span>
                </td>
              </tr>
            @empty
              <tr id="empty-history-row">
                <td colspan="8" style="text-align:center; padding:32px; color:var(--text-dim)">
                  No bill payment records yet. Pay a bill above to test!
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

  </main>
</div>

<!-- ========================================== -->
<!-- STEP 1 MODAL: BILL PAY & GATEWAY SELECT -->
<!-- ========================================== -->
<div id="modal-bill-pay" class="modal-overlay">
  <div class="modal-box glass-panel">
    <div class="modal-header">
      <div>
        <h3 class="modal-title" id="modal-bill-title">Pay Bill</h3>
        <p style="font-size:12px; color:var(--text-muted); margin-top:2px;">Enter details & choose payment gateway</p>
      </div>
      <button class="btn-close-modal" onclick="closeModal('modal-bill-pay')"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <!-- Bill Summary Card -->
    <div style="background:rgba(255,255,255,0.03); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:14px 18px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center">
      <div>
        <span style="font-size:11px; color:var(--text-dim); text-transform:uppercase; font-weight:700">Bill Service</span>
        <h4 id="summary-bill-name" style="font-size:16px; color:var(--text-main); font-weight:700">Education Bill</h4>
      </div>
      <div style="text-align:right">
        <span style="font-size:11px; color:var(--text-dim); text-transform:uppercase; font-weight:700">Total Payable</span>
        <h4 id="summary-bill-price" style="font-size:20px; color:var(--primary); font-weight:800">৳ 10.00</h4>
      </div>
    </div>

    <!-- Form Controls -->
    <div style="display:flex; flex-direction:column; gap:16px">
      <div class="form-group">
        <label for="payer-name-input" style="font-size:13px; font-weight:600; color:var(--text-muted)">Enter Payer Name</label>
        <div class="input-wrapper">
          <i class="fa-regular fa-user input-icon" style="position:absolute; left:14px; top:14px; color:var(--text-dim)"></i>
          <input type="text" id="payer-name-input" value="{{ $user->name }}" placeholder="Enter full name" style="width:100%; background:var(--bg-input); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:12px 14px 12px 42px; color:var(--text-main); outline:none;" required>
        </div>
      </div>

      <div class="form-group">
        <label style="font-size:13px; font-weight:600; color:var(--text-muted)">Select Payment Method (bKash or Nagad)</label>
        
        <div class="payment-methods-grid">
          <!-- bKash Option (Default Selected) -->
          <div id="method-card-bkash" class="method-card selected-bkash" onclick="selectPaymentMethod('bkash')">
            <i class="fa-solid fa-circle-check check-icon"></i>
            <div class="method-badge-icon icon-bkash-bg"><i class="fa-solid fa-b"></i></div>
            <span class="method-name">bKash Pay</span>
            <span style="font-size:10px; color:var(--text-dim)">Instant Merchant API</span>
          </div>

          <!-- Nagad Option -->
          <div id="method-card-nagad" class="method-card" onclick="selectPaymentMethod('nagad')">
            <i class="fa-solid fa-circle-check check-icon"></i>
            <div class="method-badge-icon icon-nagad-bg"><i class="fa-solid fa-n"></i></div>
            <span class="method-name">Nagad Pay</span>
            <span style="font-size:10px; color:var(--text-dim)">Digital Wallet</span>
          </div>
        </div>
      </div>

      <div id="modal-step1-error" style="display:none; color:#fca5a5; font-size:13px; background:rgba(244,63,94,0.15); border:1px solid rgba(244,63,94,0.3); padding:10px 14px; border-radius:6px;">
        <i class="fa-solid fa-triangle-exclamation"></i> <span id="modal-step1-error-text">Please enter name</span>
      </div>

      <button type="button" class="btn-primary" style="width:100%; padding:14px; margin-top:8px" onclick="proceedToMerchantCheckout()">
        <span>Continue Payment</span>
        <i class="fa-solid fa-arrow-right"></i>
      </button>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- STEP 2 MODAL: MERCHANT GATEWAY CHECKOUT -->
<!-- ========================================== -->
<div id="modal-merchant-checkout" class="modal-overlay">
  <div class="modal-box glass-panel" style="padding-top:0; overflow:hidden">
    
    <!-- Dynamic Merchant Header (bKash pink or Nagad orange) -->
    <div id="merchant-header-banner" class="merchant-header merchant-bkash">
      <div class="merchant-logo">
        <i id="merchant-header-icon" class="fa-solid fa-b"></i>
        <span id="merchant-header-title">bKash Merchant Pay</span>
      </div>
      <div class="merchant-amount-tag" id="merchant-header-amount">৳ 10.00</div>
    </div>

    <p style="font-size:12px; color:var(--text-muted); margin-bottom:18px;">
      Payee Merchant: <strong style="color:var(--text-main)">Botbari Utility Merchant Services</strong>
    </p>

    <div style="display:flex; flex-direction:column; gap:16px">
      
      <div class="form-group">
        <label for="merchant-phone-input" style="font-size:12px; font-weight:600; color:var(--text-muted)">
          <span id="merchant-phone-label">bKash Mobile Account Number</span>
        </label>
        <div class="input-wrapper">
          <i class="fa-solid fa-mobile-screen-button input-icon" style="position:absolute; left:14px; top:14px; color:var(--text-dim)"></i>
          <input type="tel" id="merchant-phone-input" value="{{ $user->phone }}" placeholder="017XXXXXXXX" style="width:100%; background:var(--bg-input); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:12px 14px 12px 42px; color:var(--text-main); outline:none;" required>
        </div>
      </div>

      <div class="form-group">
        <label for="merchant-pin-input" style="font-size:12px; font-weight:600; color:var(--text-muted)">Enter 4-Digit Wallet PIN</label>
        <div class="input-wrapper">
          <i class="fa-solid fa-key input-icon" style="position:absolute; left:14px; top:14px; color:var(--text-dim)"></i>
          <input type="password" id="merchant-pin-input" placeholder="••••" maxlength="6" style="width:100%; background:var(--bg-input); border:1px solid var(--border-color); border-radius:var(--radius-md); padding:12px 14px 12px 42px; color:var(--text-main); outline:none;" required>
        </div>
      </div>

      <div id="merchant-error-box" style="display:none; color:#fca5a5; font-size:13px; background:rgba(244,63,94,0.15); border:1px solid rgba(244,63,94,0.3); padding:10px 14px; border-radius:6px;">
        <i class="fa-solid fa-triangle-exclamation"></i> <span id="merchant-error-text">Error processing payment</span>
      </div>

      <div style="display:flex; gap:12px; margin-top:8px">
        <button type="button" class="btn-secondary" style="flex:1" onclick="closeModal('modal-merchant-checkout')">Cancel</button>
        <button type="button" id="btn-confirm-merchant-pay" class="btn-primary" style="flex:2" onclick="submitMerchantPayment()">
          <span id="btn-confirm-text">Confirm & Pay</span>
          <i class="fa-solid fa-circle-check"></i>
        </button>
      </div>

    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- STEP 3 MODAL: PAYMENT CONFIRMATION RECEIPT -->
<!-- ========================================== -->
<div id="modal-payment-success" class="modal-overlay">
  <div class="modal-box glass-panel" style="text-align:center">
    
    <div class="success-checkmark-box">
      <i class="fa-solid fa-check"></i>
    </div>

    <h2 style="font-family:var(--font-heading); font-size:24px; font-weight:800; color:var(--text-main); margin-bottom:4px">
      Payment Confirmed!
    </h2>
    <p style="font-size:13px; color:#34d399; font-weight:600">Your bill payment has been successfully processed</p>

    <!-- Receipt Details -->
    <div class="receipt-box">
      <div class="receipt-row">
        <span>Transaction ID:</span>
        <strong id="rcpt-trx-id" style="font-family:monospace; color:var(--primary)">BKS98472918</strong>
      </div>
      <div class="receipt-row">
        <span>Service Category:</span>
        <strong id="rcpt-bill-name">Education Bill</strong>
      </div>
      <div class="receipt-row">
        <span>Amount Paid:</span>
        <strong id="rcpt-amount" style="font-size:16px; color:#34d399">৳ 10.00 BDT</strong>
      </div>
      <div class="receipt-row">
        <span>Payment Method:</span>
        <strong id="rcpt-method">bKash Pay</strong>
      </div>
      <div class="receipt-row">
        <span>Payer Name:</span>
        <strong id="rcpt-payer">Tanvir Hasan</strong>
      </div>
      <div class="receipt-row">
        <span>Date & Time:</span>
        <strong id="rcpt-date">16 Sep 2026, 04:30 AM</strong>
      </div>
    </div>

    <button class="btn-primary" style="width:100%; padding:14px" onclick="closeModal('modal-payment-success')">
      <span>Done & Close</span>
      <i class="fa-solid fa-check-double"></i>
    </button>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let currentBillName = '';
  let currentBillAmount = 0;
  let selectedMethod = 'bkash'; // Default selected: bkash

  function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
  }

  function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
  }

  function openBillPayModal(billName, billPrice) {
    currentBillName = billName;
    currentBillAmount = billPrice;

    document.getElementById('modal-bill-title').textContent = 'Pay ' + billName;
    document.getElementById('summary-bill-name').textContent = billName;
    document.getElementById('summary-bill-price').textContent = '৳ ' + Number(billPrice).toFixed(2);
    document.getElementById('modal-step1-error').style.display = 'none';

    // Reset default payment method selection to bKash
    selectPaymentMethod('bkash');

    openModal('modal-bill-pay');
  }

  function selectPaymentMethod(method) {
    selectedMethod = method;
    const bkashCard = document.getElementById('method-card-bkash');
    const nagadCard = document.getElementById('method-card-nagad');

    if (method === 'bkash') {
      bkashCard.className = 'method-card selected-bkash';
      nagadCard.className = 'method-card';
    } else {
      nagadCard.className = 'method-card selected-nagad';
      bkashCard.className = 'method-card';
    }
  }

  function proceedToMerchantCheckout() {
    const payerName = document.getElementById('payer-name-input').value.trim();
    const errBox = document.getElementById('modal-step1-error');
    const errText = document.getElementById('modal-step1-error-text');

    if (!payerName) {
      errText.textContent = 'Please enter Payer Name';
      errBox.style.display = 'block';
      return;
    }

    errBox.style.display = 'none';
    closeModal('modal-bill-pay');

    // Update Merchant Header depending on chosen payment method (bKash or Nagad)
    const headerBanner = document.getElementById('merchant-header-banner');
    const headerIcon = document.getElementById('merchant-header-icon');
    const headerTitle = document.getElementById('merchant-header-title');
    const phoneLabel = document.getElementById('merchant-phone-label');
    const btnConfirmText = document.getElementById('btn-confirm-text');

    document.getElementById('merchant-header-amount').textContent = '৳ ' + Number(currentBillAmount).toFixed(2);
    document.getElementById('merchant-error-box').style.display = 'none';

    if (selectedMethod === 'bkash') {
      headerBanner.className = 'merchant-header merchant-bkash';
      headerIcon.className = 'fa-solid fa-b';
      headerTitle.textContent = 'bKash Merchant Pay';
      phoneLabel.textContent = 'bKash Mobile Wallet Account';
      btnConfirmText.textContent = 'Confirm bKash Pay ৳ ' + currentBillAmount;
    } else {
      headerBanner.className = 'merchant-header merchant-nagad';
      headerIcon.className = 'fa-solid fa-n';
      headerTitle.textContent = 'Nagad Merchant Pay';
      phoneLabel.textContent = 'Nagad Mobile Wallet Account';
      btnConfirmText.textContent = 'Confirm Nagad Pay ৳ ' + currentBillAmount;
    }

    openModal('modal-merchant-checkout');
  }

  async function submitMerchantPayment() {
    const payerName = document.getElementById('payer-name-input').value.trim();
    const phone = document.getElementById('merchant-phone-input').value.trim();
    const pin = document.getElementById('merchant-pin-input').value.trim();
    const errBox = document.getElementById('merchant-error-box');
    const errText = document.getElementById('merchant-error-text');
    const btnConfirm = document.getElementById('btn-confirm-merchant-pay');

    if (!phone || phone.length < 10) {
      errText.textContent = 'Please enter a valid mobile wallet number.';
      errBox.style.display = 'block';
      return;
    }
    if (!pin || pin.length < 4) {
      errText.textContent = 'Please enter your 4-digit PIN.';
      errBox.style.display = 'block';
      return;
    }

    errBox.style.display = 'none';
    btnConfirm.disabled = true;
    const origHtml = btnConfirm.innerHTML;
    btnConfirm.innerHTML = '<span>Processing Payment...</span> <i class="fa-solid fa-spinner fa-spin"></i>';

    try {
      const res = await fetch("{{ route('bill.pay') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify({
          payer_name: payerName,
          bill_type: currentBillName,
          amount: currentBillAmount,
          payment_method: selectedMethod,
          account_number: phone,
          pin: pin
        })
      });

      const data = await res.json();

      if (data.success) {
        closeModal('modal-merchant-checkout');
        
        // Populate Receipt Modal
        const p = data.payment;
        document.getElementById('rcpt-trx-id').textContent = p.trx_id;
        document.getElementById('rcpt-bill-name').textContent = p.bill_type;
        document.getElementById('rcpt-amount').textContent = '৳ ' + p.amount + ' BDT';
        document.getElementById('rcpt-method').textContent = p.payment_method + ' Pay';
        document.getElementById('rcpt-payer').textContent = p.payer_name;
        document.getElementById('rcpt-date').textContent = p.date;

        openModal('modal-payment-success');

        // Refresh History Table & Top Metrics
        refreshHistoryTable();
      } else {
        errText.textContent = data.message || 'Payment failed.';
        errBox.style.display = 'block';
      }
    } catch (err) {
      console.error('Payment Error:', err);
      errText.textContent = 'Server error processing payment.';
      errBox.style.display = 'block';
    } finally {
      btnConfirm.disabled = false;
      btnConfirm.innerHTML = origHtml;
    }
  }

  async function refreshHistoryTable() {
    try {
      const res = await fetch("{{ route('bill.history') }}", {
        headers: { 'Accept': 'application/json' }
      });
      const data = await res.json();

      if (data.success && data.payments) {
        const tbody = document.getElementById('history-table-body');
        tbody.innerHTML = '';

        let totalAmount = 0;
        data.payments.forEach(p => {
          totalAmount += parseFloat(p.amount.replace(/,/g, ''));
          const isBkash = p.payment_method.toLowerCase() === 'bkash';
          const badgeClass = isBkash ? 'method-badge method-bkash' : 'method-badge method-nagad';
          const badgeIcon = isBkash ? '<i class="fa-solid fa-b"></i> bKash' : '<i class="fa-solid fa-n"></i> Nagad';

          const row = `
            <tr>
              <td><strong style="font-family:monospace; color:var(--primary)">${p.trx_id}</strong></td>
              <td><span style="font-weight:600">${p.payer_name}</span></td>
              <td>${p.bill_type}</td>
              <td><strong>৳ ${p.amount}</strong></td>
              <td><span class="${badgeClass}">${badgeIcon}</span></td>
              <td>${p.account_number}</td>
              <td>${p.date}</td>
              <td><span style="color:#34d399; font-weight:700; font-size:12px"><i class="fa-solid fa-circle-check"></i> SUCCESS</span></td>
            </tr>
          `;
          tbody.innerHTML += row;
        });

        document.getElementById('metric-bills-count').textContent = data.payments.length;
        document.getElementById('metric-amount-sum').textContent = totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2 });
        document.getElementById('history-count-tag').textContent = 'Latest ' + data.payments.length + ' Transactions';
      }
    } catch (e) {
      console.error('Error refreshing history:', e);
    }
  }
</script>
@endsection
