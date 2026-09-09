<?php
/* Template Name: TOURIM Customer Queries */
if (!defined('ABSPATH')) { exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Customer Queries | TOURIM</title>
  <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri()); ?>/css/query-admin.css?v=20260814-2">
  <?php tourim_wp_template_meta(); wp_head(); ?>
</head>
<body class="query-admin-body">
  <section class="query-login" id="loginScreen">
    <form class="query-login-card" id="loginForm">
      <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" alt="TOURIM">
      <h1>Customer Query Login</h1>
      <p>Sign in to view and manage customer enquiries.</p>
      <input name="login" placeholder="Admin username or email" autocomplete="username" required>
      <div class="password-field-wrap">
        <input name="password" id="loginPassword" type="password" placeholder="Password" autocomplete="current-password" required>
        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Show password" title="Show password">
          <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
          <svg class="eye-off-icon" style="display:none;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
        </button>
      </div>
      <button type="submit">Login</button>
      <div class="query-message" id="loginMessage" role="alert"></div>
    </form>
  </section>

  <main class="query-app" id="queryApp" hidden>
    <header class="query-header">
      <div><span>TOURIM</span><h1>Customer Queries</h1><small class="query-live"><i></i> Live updates every 3 seconds</small></div>
      <div class="query-actions"><button id="refreshBtn">Refresh</button><button id="exportExcelBtn" style="background:#0f766e;">Export Excel (.xlsx)</button><button id="exportBtn">Export CSV</button><a href="<?php echo esc_url(home_url('/')); ?>">View Site</a><button class="danger" id="logoutBtn">Log Out</button></div>
    </header>

    <section class="query-stats" aria-label="Query summary">
      <article><span>Total</span><b id="totalCount">0</b></article>
      <article><span>New</span><b id="newCount">0</b></article>
      <article><span>Pending</span><b id="pendingCount">0</b></article>
      <article><span>Replied</span><b id="repliedCount">0</b></article>
    </section>

    <section class="query-panel">
      <div class="query-filters">
        <input id="querySearch" type="search" placeholder="Search name, phone, package or destination">
        <select id="statusFilter"><option value="">All statuses</option><option>New</option><option>Pending</option><option>Replied</option><option>Follow-up</option><option>Converted</option><option>Cancelled</option></select>
      </div>
      <div class="query-table-wrap"><table><thead><tr><th>Customer</th><th>Contact</th><th>Trip</th><th>Travel</th><th>Status</th><th>Action</th></tr></thead><tbody id="queryRows"></tbody></table></div>
      <p class="query-empty" id="emptyState" hidden>No customer queries found.</p>
    </section>
  </main>

  <dialog id="queryDialog"><form method="dialog" class="query-dialog-card"><button class="dialog-close" value="cancel" aria-label="Close">×</button><div id="queryDetail"></div></form></dialog>
  <div class="query-toast" id="queryToast"></div>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
  <script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/data.js?v=20260814"></script>
  <script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/query-admin.js?v=20260814-4"></script>
  <?php wp_footer(); ?>
</body>
</html>
