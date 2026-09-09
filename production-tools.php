<?php
/*
Template Name: TOURIM Production Tools
*/
if (!defined('ABSPATH')) { exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TOURIM Production Tools</title>
  <link rel="icon" type="image/png" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" />
  <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri()); ?>/css/styles.css?v=20260814-v5">
  <?php tourim_wp_template_meta(); ?>
  <?php wp_head(); ?>
</head>
<body class="admin-body prod-tools-body">
  <main class="prod-wrap">
    <header class="prod-hero">
      <div>
        <span class="badge orange">Production Upgrade Center</span>
        <h1>TOURIM Production Tools</h1>
        <p>Advanced modules for 100% business-ready operation: roles, uploads, payments, vouchers, invoices, WhatsApp templates, SEO routes, analytics and backups.</p>
      </div>
      <div class="action-row">
        <a class="btn ghost" href="admin.html">← Back to Admin</a>
        <a class="btn ghost" href="launch-check.html">100% Launch Check</a>
        <a class="btn teal" href="index.html">View Site</a>
        <button class="btn primary" onclick="loadProductionTools()">Refresh Tools</button>
      </div>
    </header>

    <section class="stats-grid prod-stats">
      <div class="stat-card"><span>👥</span><h3 id="prodUsers">0</h3><p>Admin Users</p></div>
      <div class="stat-card"><span>💳</span><h3 id="prodPayments">0</h3><p>Payments</p></div>
      <div class="stat-card"><span>🎫</span><h3 id="prodBookings">0</h3><p>Bookings</p></div>
      <div class="stat-card"><span>📄</span><h3 id="prodDocs">0</h3><p>Invoices / Vouchers</p></div>
      <div class="stat-card"><span>🖼</span><h3 id="prodMedia">0</h3><p>Media Files</p></div>
      <div class="stat-card"><span>📈</span><h3 id="prodEvents">0</h3><p>Tracked Events</p></div>
    </section>

    <section class="prod-grid">
      <div class="panel">
        <h3>Role & User Manager</h3>
        <form class="admin-form" id="prodUserForm">
          <input name="name" placeholder="Team member name" required>
          <input name="email" placeholder="Email">
          <input name="username" placeholder="Username" required>
          <select name="role"><option value="super_admin">Super Admin</option><option value="admin">Admin</option><option value="sales">Sales Executive</option><option value="editor">Content Editor</option><option value="viewer">Viewer</option></select>
          <select name="status"><option value="active">Active</option><option value="disabled">Disabled</option></select>
          <input name="password" type="password" placeholder="New password">
          <button class="btn primary" type="submit">Save User</button>
        </form>
        <div class="mini-list" id="prodUsersList"></div>
      </div>

      <div class="panel">
        <h3>Server Media Upload</h3>
        <form class="admin-form" id="prodUploadForm">
          <input type="file" name="file" accept="image/*" required>
          <input name="alt_text" placeholder="Alt text for SEO">
          <select name="type"><option>package</option><option>destination</option><option>hotel</option><option>blog</option><option>offer</option><option>gallery</option></select>
          <button class="btn teal" type="submit">Upload to Server</button>
        </form>
        <div class="mini-list" id="prodMediaList"></div>
      </div>

      <div class="panel">
        <h3>Payment Tracker</h3>
        <form class="admin-form" id="prodPaymentForm">
          <input name="customer" placeholder="Customer name" required>
          <input name="phone" placeholder="Phone">
          <input name="package" placeholder="Package name" required>
          <input name="amount" type="number" placeholder="Amount" required>
          <select name="mode"><option>UPI</option><option>Cash</option><option>Bank Transfer</option><option>Razorpay</option><option>Card</option></select>
          <input name="transaction_id" placeholder="Transaction ID">
          <select name="status"><option>Received</option><option>Pending</option><option>Failed</option><option>Refunded</option></select>
          <input name="payment_date" type="date">
          <textarea name="note" placeholder="Payment note"></textarea>
          <button class="btn primary" type="submit">Save Payment</button>
        </form>
        <div class="mini-list" id="prodPaymentList"></div>
      </div>

      <div class="panel">
        <h3>Booking, Invoice & Voucher Generator</h3>
        <form class="admin-form" id="prodBookingForm">
          <input name="customer" placeholder="Customer name" required>
          <input name="phone" placeholder="Phone">
          <input name="email" placeholder="Email">
          <input name="package" placeholder="Package" required>
          <input name="destination" placeholder="Destination">
          <input name="travel_date" type="date">
          <input name="pax" placeholder="Guests / Pax">
          <input name="total_amount" type="number" placeholder="Total amount">
          <input name="advance" type="number" placeholder="Advance">
          <input name="balance" type="number" placeholder="Balance">
          <textarea name="hotel_plan" placeholder="Hotel plan"></textarea>
          <textarea name="transport_plan" placeholder="Transport plan"></textarea>
          <button class="btn teal" type="submit">Save Booking</button>
          <div class="action-row"><button class="btn primary" type="button" onclick="generateDoc('invoice')">Generate Invoice</button><button class="btn ghost" type="button" onclick="generateDoc('voucher')">Generate Voucher</button></div>
        </form>
        <div class="mini-list" id="prodBookingList"></div>
        <div class="mini-list" id="prodDocList"></div>
      </div>

      <div class="panel">
        <h3>WhatsApp Business Templates</h3>
        <p class="muted-text">Use variables like {{name}}, {{package}}, {{destination}}, {{balance}}, {{travel_date}}.</p>
        <textarea id="prodWhatsappTemplates" rows="10" placeholder="Templates JSON"></textarea>
        <button class="btn primary" onclick="saveProductionSettings()">Save WhatsApp / SEO Settings</button>
      </div>

      <div class="panel">
        <h3>SEO Route Manager</h3>
        <p class="muted-text">Clean URL rules are active in .htaccess for packages, destinations and blogs.</p>
        <textarea id="prodSeoRoutes" rows="10" placeholder="SEO routes JSON"></textarea>
        <button class="btn teal" onclick="saveProductionSettings()">Save SEO Routes</button>
      </div>

      <div class="panel wide-panel">
        <h3>Advanced Analytics Report</h3>
        <div class="action-row"><button class="btn primary" onclick="loadAnalyticsReport()">Calculate Analytics</button><button class="btn ghost" onclick="createBackup()">Create Backup</button></div>
        <pre class="analytics-pre" id="prodAnalyticsReport">Click Calculate Analytics to view event types, daily activity and top interested items.</pre>
      </div>

      <div class="panel wide-panel">
        <h3>Audit Logs</h3>
        <div class="mini-list" id="prodAuditList"></div>
      </div>
    </section>
  </main>
  <div class="toast"></div>
  <script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/data.js?v=20260624-v3"></script>
  <script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/production-tools.js"></script>
  <?php wp_footer(); ?>
</body>
</html>
