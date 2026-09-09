<?php
/*
Template Name: TOURIM Launch Check
*/
if (!defined('ABSPATH')) { exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>TOURIM 100% Launch Readiness</title>
  <link rel="icon" type="image/png" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/tourim-logo-clean.png" />
  <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri()); ?>/css/styles.css?v=20260814-v5">
  <?php tourim_wp_template_meta(); ?>
  <?php wp_head(); ?>
</head>
<body class="admin-body prod-tools-body">
  <main class="prod-wrap launch-wrap">
    <header class="prod-hero">
      <div>
        <span class="badge orange">100% Completion Center</span>
        <h1>TOURIM Live Launch Readiness</h1>
        <p>Final launch wizard for hosting, domain, security, database, email, payments, WhatsApp, SEO, backups and production readiness.</p>
      </div>
      <div class="action-row">
        <a class="btn ghost" href="admin.html">← Admin</a>
        <a class="btn ghost" href="production-tools.html">Production Tools</a>
        <a class="btn teal" href="index.html">View Site</a>
        <button class="btn primary" onclick="loadLaunchReadiness()">Recheck</button>
      </div>
    </header>

    <section class="stats-grid prod-stats">
      <div class="stat-card"><span>✅</span><h3 id="launchScore">0%</h3><p>Readiness Score</p></div>
      <div class="stat-card"><span>🧩</span><h3 id="softwareComplete">No</h3><p>Software Complete</p></div>
      <div class="stat-card"><span>🗄️</span><h3 id="storageMode">JSON</h3><p>Storage Mode</p></div>
      <div class="stat-card"><span>🌐</span><h3 id="baseUrl">Local</h3><p>Base URL</p></div>
    </section>

    <section class="prod-grid">
      <div class="panel">
        <h3>Domain, Sitemap & App Secret</h3>
        <p class="muted-text">After hosting upload, set the final domain here. This updates APP_URL, robots.txt and sitemap.xml.</p>
        <form class="admin-form" id="domainForm">
          <input name="app_url" placeholder="https://tourim.in" required>
          <input name="app_secret" placeholder="App secret; leave blank to auto-generate">
          <button class="btn primary" type="submit">Save Domain Settings</button>
        </form>
      </div>

      <div class="panel">
        <h3>External Account Keys</h3>
        <p class="muted-text">Use this only on your private live server. Do not add real keys before sharing the ZIP.</p>
        <form class="admin-form" id="externalForm">
          <input name="SMTP_PASSWORD" placeholder="Gmail SMTP app password">
          <input name="RAZORPAY_KEY_ID" placeholder="Razorpay Key ID">
          <input name="RAZORPAY_KEY_SECRET" placeholder="Razorpay Secret">
          <input name="WHATSAPP_BUSINESS_PHONE_ID" placeholder="WhatsApp Phone Number ID">
          <input name="WHATSAPP_BUSINESS_TOKEN" placeholder="WhatsApp Access Token">
          <input name="UPI_ID" placeholder="UPI ID, e.g. tourim@upi">
          <button class="btn teal" type="submit">Save External Settings</button>
        </form>
      </div>

      <div class="panel wide-panel">
        <h3>Readiness Checklist</h3>
        <div id="readinessList" class="mini-list"></div>
      </div>

      <div class="panel wide-panel">
        <h3>External Items Still Needed</h3>
        <p class="muted-text">These are not missing software code. They depend on your real live accounts.</p>
        <div id="externalList" class="mini-list"></div>
      </div>
    </section>
  </main>
  <div class="toast"></div>
  <script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/data.js?v=20260624-v3"></script>
  <script defer src="<?php echo esc_url(get_template_directory_uri()); ?>/js/launch-check.js"></script>
  <?php wp_footer(); ?>
</body>
</html>
