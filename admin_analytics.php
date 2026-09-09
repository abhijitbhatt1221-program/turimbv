<?php
/* Template Name: TOURIM Analytics */
if (!defined('ABSPATH')) { exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Analytics | TOURIM</title>
  <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri()); ?>/css/query-admin.css?v=20260814-2">
  <?php tourim_wp_template_meta(); wp_head(); ?>
  <style>
      .analytics-panel { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin: 20px 0; }
      .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px; }
      .stat-card { background: #f8fafc; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0; text-align: center; }
      .stat-card h3 { margin: 0 0 10px 0; color: #475569; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; }
      .stat-card .value { font-size: 24px; font-weight: bold; color: #0f172a; }
  </style>
</head>
<body class="query-admin-body">
  <main class="query-app" id="analyticsApp">
    <header class="query-header">
      <div><span>TOURIM</span><h1>Page Visitors Analytics</h1></div>
      <div class="query-actions">
          <a href="<?php echo esc_url(home_url('/admin.php')); ?>">Back to Queries</a>
          <button class="danger" id="logoutBtn">Log Out</button>
      </div>
    </header>

    <section class="analytics-panel">
        <h2>Real-time Visitor Statistics</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Last 24 Hours</h3>
                <div class="value" id="visits1Day">0</div>
            </div>
            <div class="stat-card">
                <h3>Last 7 Days</h3>
                <div class="value" id="visits7Days">0</div>
            </div>
            <div class="stat-card">
                <h3>Last 1 Month</h3>
                <div class="value" id="visits1Month">0</div>
            </div>
            <div class="stat-card">
                <h3>Last 1 Year</h3>
                <div class="value" id="visits1Year">0</div>
            </div>
            <div class="stat-card">
                <h3>All Time Total</h3>
                <div class="value" id="visitsTotal">0</div>
            </div>
        </div>
    </section>
  </main>

  <div class="query-toast" id="queryToast"></div>
  <script>
      const api = '<?php echo esc_url(get_template_directory_uri()); ?>/api/db.php';
      
      async function loadAnalytics() {
          try {
              const res = await fetch(api + '?action=get&private=1');
              const data = await res.json();
              if (data.ok) {
                  processAnalytics(data.db.events || [], data.db.analytics || {});
              } else {
                  if (data.error === 'Admin login required') {
                      window.location.href = '<?php echo esc_url(home_url('/admin.php')); ?>';
                  }
              }
          } catch(e) { console.error(e); }
      }

      function processAnalytics(events, analytics) {
          const now = new Date();
          let v1 = 0, v7 = 0, v30 = 0, v365 = 0;
          
          events.forEach(ev => {
              if (ev.event_type === 'page_view') {
                  const evDate = new Date(ev.created_at);
                  const diffTime = Math.abs(now - evDate);
                  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                  
                  if (diffDays <= 1) v1++;
                  if (diffDays <= 7) v7++;
                  if (diffDays <= 30) v30++;
                  if (diffDays <= 365) v365++;
              }
          });
          
          document.getElementById('visits1Day').textContent = v1;
          document.getElementById('visits7Days').textContent = v7;
          document.getElementById('visits1Month').textContent = v30;
          document.getElementById('visits1Year').textContent = v365;
          document.getElementById('visitsTotal').textContent = analytics.visitors || events.filter(e => e.event_type === 'page_view').length;
      }
      
      document.getElementById('logoutBtn').onclick = async () => {
        await fetch(api, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'logout' }) });
        location.href = '<?php echo esc_url(home_url('/admin.php')); ?>';
      };

      // Real-time update every 10 seconds
      loadAnalytics();
      setInterval(loadAnalytics, 10000);
  </script>
</body>
</html>
