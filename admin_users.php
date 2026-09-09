<?php
/* Template Name: TOURIM Users Admin */
if (!defined('ABSPATH')) { exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sub Admins | TOURIM</title>
  <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri()); ?>/css/query-admin.css?v=20260814-2">
  <?php tourim_wp_template_meta(); wp_head(); ?>
  <style>
      .user-panel { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin: 20px 0; }
      .user-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
      .user-table th, .user-table td { border-bottom: 1px solid #ddd; padding: 12px; text-align: left; }
      .user-table th { background: #f9f9f9; }
      .form-group { margin-bottom: 15px; }
      .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
      .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
  </style>
</head>
<body class="query-admin-body">
  <main class="query-app" id="userApp">
    <header class="query-header">
      <div><span>TOURIM</span><h1>Sub Admins</h1></div>
      <div class="query-actions"><a href="<?php echo esc_url(home_url('/admin.php')); ?>">Back to Queries</a><button class="danger" id="logoutBtn">Log Out</button></div>
    </header>

    <section class="user-panel">
        <h2>Add / Edit Sub Admin</h2>
        <form id="userForm">
            <input type="hidden" id="userId" name="id">
            <div class="form-group">
                <label>Name</label>
                <input type="text" id="userName" name="name" required>
            </div>
            <div class="form-group">
                <label>Email/Username</label>
                <input type="text" id="userEmail" name="email" required>
            </div>
            <div class="form-group">
                <label>Password (leave blank to keep current)</label>
                <input type="password" id="userPassword" name="password">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select id="userRole" name="role">
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="sales">Sales</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select id="userStatus" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn primary">Save User</button>
            <button type="button" class="btn ghost" id="resetFormBtn">Cancel</button>
        </form>
    </section>

    <section class="user-panel">
      <h2>Existing Sub Admins</h2>
      <div class="query-table-wrap">
          <table class="user-table">
              <thead><tr><th>Name</th><th>Email/Username</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
              <tbody id="userRows"></tbody>
          </table>
      </div>
    </section>
  </main>

  <div class="query-toast" id="queryToast"></div>
  <script>
      const api = '<?php echo esc_url(get_template_directory_uri()); ?>/api/db.php';
      
      async function loadUsers() {
          try {
              const res = await fetch(api + '?action=get&private=1');
              const data = await res.json();
              if (data.ok) {
                  renderUsers(data.db.users || []);
              } else {
                  if (data.error === 'Admin login required') {
                      window.location.href = '<?php echo esc_url(home_url('/admin.php')); ?>';
                  }
              }
          } catch(e) { console.error(e); }
      }

      function renderUsers(users) {
          const tbody = document.getElementById('userRows');
          tbody.innerHTML = '';
          users.forEach(u => {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                  <td>${u.name}</td>
                  <td>${u.email || u.username}</td>
                  <td>${u.role}</td>
                  <td>${u.status}</td>
                  <td>
                      <button onclick='editUser(${JSON.stringify(u)})' class="btn ghost btn-sm">Edit</button>
                      <button onclick='deleteUser("${u.id}")' class="btn danger btn-sm">Delete</button>
                  </td>
              `;
              tbody.appendChild(tr);
          });
      }
      
      window.editUser = function(user) {
          document.getElementById('userId').value = user.id;
          document.getElementById('userName').value = user.name;
          document.getElementById('userEmail').value = user.email || user.username;
          document.getElementById('userRole').value = user.role;
          document.getElementById('userStatus').value = user.status;
          document.getElementById('userPassword').value = '';
      };
      
      document.getElementById('resetFormBtn').onclick = () => {
          document.getElementById('userForm').reset();
          document.getElementById('userId').value = '';
      };
      
      document.getElementById('userForm').onsubmit = async (e) => {
          e.preventDefault();
          const id = document.getElementById('userId').value;
          const user = {
              id: id || 'user_' + Date.now(),
              name: document.getElementById('userName').value,
              email: document.getElementById('userEmail').value,
              role: document.getElementById('userRole').value,
              status: document.getElementById('userStatus').value
          };
          const pwd = document.getElementById('userPassword').value;
          if (pwd) user.password = pwd;
          
          try {
              const res = await fetch(api, {
                  method: 'POST',
                  headers: {'Content-Type': 'application/json'},
                  body: JSON.stringify({action: 'save_user', user})
              });
              const data = await res.json();
              if (data.ok) {
                  loadUsers();
                  document.getElementById('resetFormBtn').click();
                  showToast('User saved');
              } else {
                  alert(data.error);
              }
          } catch(e) { console.error(e); }
      };

      window.deleteUser = async function(id) {
          if(!confirm('Are you sure you want to delete this user?')) return;
          try {
              const res = await fetch(api, {
                  method: 'POST',
                  headers: {'Content-Type': 'application/json'},
                  body: JSON.stringify({action: 'delete_user', id})
              });
              const data = await res.json();
              if (data.ok) {
                  loadUsers();
                  showToast('User deleted');
              } else {
                  alert(data.error);
              }
          } catch(e) { console.error(e); }
      };
      
      document.getElementById('logoutBtn').onclick = async () => {
        await fetch(api, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ action: 'logout' }) });
        location.href = '<?php echo esc_url(home_url('/admin.php')); ?>';
      };

      function showToast(msg) {
          const t = document.getElementById('queryToast');
          t.textContent = msg;
          t.className = 'query-toast show';
          setTimeout(() => t.className = 'query-toast', 3000);
      }
      
      loadUsers();
  </script>
</body>
</html>
