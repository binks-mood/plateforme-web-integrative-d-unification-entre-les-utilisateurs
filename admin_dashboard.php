<?php
// =====================================================
// NexaFlow – Admin Dashboard (V2 Responsive Fix)
// =====================================================
require_once 'includes/functions.php';
requireAuth();

$user = getCurrentUser();
if ($user['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$initials = $user['avatar_initials'] ?? mb_strtoupper(
    mb_substr($user['firstname'], 0, 1, 'UTF-8') .
    mb_substr($user['lastname'],  0, 1, 'UTF-8'),
    'UTF-8'
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NexaFlow Admin – Suivi des Activités</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/dashboard.css" />
  <style>
    /* ── FIX SIDEBAR MOBILE ── */
    @media (max-width: 768px) {
      .sidebar {
        position: fixed !important;
        left: 0 !important;
        top: 0; bottom: 0;
        z-index: 1001;
        width: 260px !important;
        transform: translateX(-101%) !important; /* Force hide using transform to match style.css logic */
        transition: transform .3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 10px 0 30px rgba(0,0,0,0.5);
      }
      .sidebar.open {
        transform: translateX(0) !important;
      }
      .sidebar-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(2,12,27,.8);
        backdrop-filter: blur(4px);
        z-index: 1000;
      }
      .sidebar-overlay.show { display: block; }
      
      .main-content { margin-left: 0 !important; width: 100% !important; }
      .topbar { padding: 0 16px !important; }
      .page-content { padding: 16px !important; }
      
      /* Force filters to stack on mobile */
      .filters-bar { flex-direction: column !important; align-items: stretch !important; }
      .filters-bar input, .filters-bar select { width: 100% !important; }
      
      .mobile-menu-btn { display: flex !important; align-items: center; justify-content: center; background: rgba(255,255,255,.05); border-radius: 8px; padding: 8px; margin-right: 8px; }
      .topbar-actions .btn-secondary { display: none; }
    }

    /* ── UI ENHANCEMENTS ── */
    .admin-stat {
      background: linear-gradient(135deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
      border: 1px solid rgba(59,130,246,.15);
      border-radius: 16px;
      padding: 24px;
      height: 100%;
      transition: all .3s ease;
    }
    .admin-stat:hover { border-color: var(--blue-500); transform: translateY(-3px); }
    
    .table-responsive {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      margin-top: 10px;
      border-radius: 8px;
    }
    
    .activity-table { min-width: 650px; } /* Ensures table doesn't squash too much */

    .role-badge {
      padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;
    }
    .role-badge.admin { background: rgba(59,130,246,.15); color: #60a5fa; }
    .role-badge.manager { background: rgba(139,92,246,.15); color: #a78bfa; }
    .role-badge.member { background: rgba(16,185,129,.15); color: #34d399; }
  </style>
</head>
<body>

<div class="app-layout">
  <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

  <!-- SIDEBAR -->
  <nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-brand">
        <div class="sidebar-brand-icon">⚙️</div>
        <span class="sidebar-brand-name">NexaFlow Admin</span>
      </div>
    </div>
    <div class="sidebar-nav">
      <div class="nav-section">
        <div class="nav-section-label">Administration</div>
        <div class="nav-item active" onclick="showAdminPage('activities')" id="nav-activities">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
          <span class="nav-item-label">Suivi des activités</span>
        </div>
        <div class="nav-item" onclick="showAdminPage('users')" id="nav-users">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <span class="nav-item-label">Utilisateurs</span>
        </div>
      </div>
      <div class="nav-section">
        <div class="nav-section-label">Navigation</div>
        <a href="dashboard.php" class="nav-item">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          <span class="nav-item-label">Retour au Dashboard</span>
        </a>
      </div>
    </div>
    <div class="sidebar-footer">
      <div class="user-info dropdown" onclick="toggleDropdown('userMenu')">
        <div class="avatar"><?= htmlspecialchars($initials) ?></div>
        <div class="user-details">
          <div class="user-name"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></div>
          <div class="user-role">Admin Général</div>
        </div>
      </div>
      <div class="dropdown-menu" id="userMenu" style="bottom:60px;top:auto">
        <a class="dropdown-item danger" href="logout.php">Déconnexion</a>
      </div>
    </div>
  </nav>

  <!-- MAIN -->
  <div class="main-content">
    <header class="topbar">
      <div class="topbar-title" style="display:flex;align-items:center;">
        <button class="mobile-menu-btn" style="display:none;" onclick="openSidebar()">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <h1 id="pageTitle">Suivi des Activités</h1>
      </div>
      <div class="topbar-spacer"></div>
      <div class="topbar-actions">
        <div class="dropdown">
          <button class="topbar-btn" onclick="toggleDropdown('avatarMenu')">
            <div class="avatar avatar-sm"><?= htmlspecialchars($initials) ?></div>
          </button>
          <div class="dropdown-menu" id="avatarMenu">
            <a class="dropdown-item danger" href="logout.php">Déconnexion</a>
          </div>
        </div>
      </div>
    </header>

    <main class="page-content">
      
      <!-- STATS -->
      <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
          <div class="admin-stat">
            <div class="stat-icon blue">📈</div>
            <div>
              <div class="stat-value" id="totalActivities">0</div>
              <div class="stat-label">Actions enregistrées</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="admin-stat">
            <div class="stat-icon purple">👤</div>
            <div>
              <div class="stat-value" id="totalUsers">0</div>
              <div class="stat-label">Utilisateurs inscrits</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="admin-stat">
            <div class="stat-icon green">📂</div>
            <div>
              <div class="stat-value" id="totalProjects">0</div>
              <div class="stat-label">Projets créés</div>
            </div>
          </div>
        </div>
      </div>

      <!-- ACTIVITIES PAGE -->
      <section class="page-section active" id="page-activities">
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">Flux d'activités récent</h2>
            <button onclick="loadAdminActivities()" class="btn btn-secondary btn-sm">Actualiser</button>
          </div>
          
          <div class="filters-bar" style="display:flex;gap:12px;margin-bottom:20px;">
            <input type="text" id="actSearch" placeholder="🔍 Rechercher..." style="flex:1" oninput="filterActivities()">
            <select id="actType" onchange="filterActivities()" style="width:200px">
              <option value="">Tous les types</option>
              <option value="project">Projet</option>
              <option value="task">Tâche</option>
              <option value="user">Utilisateur</option>
            </select>
          </div>

          <div class="table-responsive">
            <table class="activity-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Utilisateur</th>
                  <th>Action</th>
                  <th>Entité</th>
                  <th>Détails</th>
                </tr>
              </thead>
              <tbody id="adminActivitiesList">
                <tr><td colspan="5" style="text-align:center;padding:40px;">Chargement...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- USERS PAGE -->
      <section class="page-section" id="page-users" style="display:none;">
        <div class="card">
          <div class="card-header">
            <h2 class="card-title">Liste des membres</h2>
            <button onclick="loadAdminUsers()" class="btn btn-secondary btn-sm">Actualiser</button>
          </div>
          <div class="table-responsive">
            <table class="activity-table">
              <thead>
                <tr>
                  <th>Utilisateur</th>
                  <th>Email</th>
                  <th>Rôle</th>
                  <th>Inscription</th>
                </tr>
              </thead>
              <tbody id="adminUsersList"></tbody>
            </table>
          </div>
        </div>
      </section>

    </main>
  </div>
</div>

<script>
let allActs = [];

function openSidebar() {
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebarOverlay').classList.add('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('show');
}

function toggleDropdown(id) {
  document.querySelectorAll('.dropdown-menu').forEach(d => { if(d.id !== id) d.classList.remove('show'); });
  document.getElementById(id).classList.toggle('show');
}

function showAdminPage(pageId) {
  document.querySelectorAll('.page-section').forEach(s => s.style.display = 'none');
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  document.getElementById('page-' + pageId).style.display = 'block';
  document.getElementById('nav-' + pageId).classList.add('active');
  document.getElementById('pageTitle').innerText = (pageId === 'activities') ? 'Suivi des Activités' : 'Gestion Utilisateurs';
  if(pageId === 'activities') loadAdminActivities(); else loadAdminUsers();
  closeSidebar();
}

async function loadAdminActivities() {
  try {
    const res = await fetch('api/admin/activities.php');
    allActs = await res.json();
    document.getElementById('totalActivities').innerText = allActs.length;
    renderActivities(allActs);
  } catch(e) { console.error(e); }
}

function renderActivities(list) {
  const tbody = document.getElementById('adminActivitiesList');
  tbody.innerHTML = list.map(a => `
    <tr>
      <td>${new Date(a.created_at).toLocaleString('fr-FR')}</td>
      <td>
        <div style="display:flex;align-items:center;gap:8px;">
          <div class="avatar avatar-sm">${a.avatar_initials || '??'}</div>
          <span>${a.firstname} ${a.lastname}</span>
        </div>
      </td>
      <td><strong>${a.action}</strong></td>
      <td>${a.entity_type || '-'}</td>
      <td>${a.entity_name || ''} ${a.entity_id ? `(#${a.entity_id})` : ''}</td>
    </tr>
  `).join('');
}

function filterActivities() {
  const q = document.getElementById('actSearch').value.toLowerCase();
  const t = document.getElementById('actType').value.toLowerCase();
  const filtered = allActs.filter(a => {
    const mQ = !q || (a.firstname + ' ' + a.lastname + ' ' + a.action).toLowerCase().includes(q);
    const mT = !t || (a.entity_type || '').toLowerCase() === t;
    return mQ && mT;
  });
  renderActivities(filtered);
}

async function loadAdminUsers() {
  try {
    const res = await fetch('api/admin/users.php');
    const users = await res.json();
    document.getElementById('totalUsers').innerText = users.length;
    document.getElementById('adminUsersList').innerHTML = users.map(u => `
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:8px;">
            <div class="avatar avatar-sm">${u.avatar_initials || '??'}</div>
            <span>${u.firstname} ${u.lastname}</span>
          </div>
        </td>
        <td>${u.email}</td>
        <td><span class="role-badge ${u.role}">${u.role}</span></td>
        <td>${new Date(u.created_at).toLocaleDateString('fr-FR')}</td>
      </tr>
    `).join('');
  } catch(e) { console.error(e); }
}

async function loadStats() {
  try {
    // Load Users count
    const resUsers = await fetch('api/admin/users.php');
    const users = await resUsers.json();
    if (Array.isArray(users)) document.getElementById('totalUsers').innerText = users.length;
    
    // Load Projects count
    const resProj = await fetch('api/projects/index.php');
    const dataProj = await resProj.json();
    const projects = dataProj.data || (Array.isArray(dataProj) ? dataProj : []);
    document.getElementById('totalProjects').innerText = projects.length;
  } catch(e) {
    console.error("Error loading stats:", e);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadAdminActivities();
  loadStats();
});
</script>
</body>
</html>
