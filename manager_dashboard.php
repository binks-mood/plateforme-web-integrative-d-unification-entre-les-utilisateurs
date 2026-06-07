<?php
// =====================================================
// NexaFlow – Dashboard Principal
// =====================================================
require_once 'includes/functions.php';
requireAuth();

$user = getCurrentUser();
if ($user['role'] !== 'chef_projet') {
    header('Location: dashboard.php');
    exit;
}
$initials = $user['avatar_initials'] ?? strtoupper(substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1));

// Données de notification non lues
$stmtNotif = db()->prepare("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = ? AND is_read = 0");
$stmtNotif->execute([$user['id']]);
$notifCount = $stmtNotif->fetch()['cnt'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NexaFlow – Tableau de Bord</title>
  <meta name="description" content="Tableau de bord NexaFlow – gérez vos projets, tâches et équipes." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="assets/css/dashboard.css" />
</head>
<body>

<div class="app-layout">

  <!-- ─── SIDEBAR ─────────────────────────────────── -->
  <nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
          <svg width="20" height="20" viewBox="0 0 48 48" fill="none">
            <circle cx="14" cy="24" r="4" fill="white"/>
            <circle cx="34" cy="14" r="4" fill="white" opacity=".8"/>
            <circle cx="34" cy="34" r="4" fill="white" opacity=".6"/>
            <line x1="14" y1="24" x2="34" y2="14" stroke="white" stroke-width="2" opacity=".6"/>
            <line x1="14" y1="24" x2="34" y2="34" stroke="white" stroke-width="2" opacity=".6"/>
            <line x1="34" y1="14" x2="34" y2="34" stroke="white" stroke-width="2" opacity=".4"/>
          </svg>
        </div>
        <span class="sidebar-brand-name">NexaFlow</span>
      </div>
      <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()" aria-label="Réduire le menu">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
    </div>

    <div class="sidebar-nav">
      <!-- PRINCIPAL -->
      <div class="nav-section">
      <div class="nav-section">
        <div class="nav-section-label">Principal</div>
        <div class="nav-item active" onclick="showPage('dashboard')" id="nav-dashboard">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
          <span class="nav-item-label">Vue globale</span>
        </div>
        <div class="nav-item" onclick="showPage('projects')" id="nav-projects">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
          <span class="nav-item-label">Projets</span>
          <span class="nav-badge" id="badge-projects">8</span>
        </div>
        <div class="nav-item" onclick="showPage('tasks')" id="nav-tasks">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
          <span class="nav-item-label">Tâches</span>
          <span class="nav-badge" id="badge-tasks">24</span>
        </div>
        <div class="nav-item" onclick="showPage('kanban')" id="nav-kanban">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="5" height="18" rx="1"/><rect x="10" y="3" width="5" height="11" rx="1"/><rect x="17" y="3" width="5" height="15" rx="1"/></svg>
          <span class="nav-item-label">Kanban</span>
        </div>
        <div class="nav-item" onclick="showPage('timeline')" id="nav-timeline">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="17" y1="12" x2="3" y2="12"/><line x1="21" y1="6" x2="3" y2="6"/><line x1="21" y1="18" x2="3" y2="18"/></svg>
          <span class="nav-item-label">Planning Gantt</span>
        </div>
      </div>

      <!-- COLLABORATION -->
      <div class="nav-section">
        <div class="nav-section-label">Collaboration</div>
        <div class="nav-item" onclick="showPage('team')" id="nav-team">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <span class="nav-item-label">Équipe</span>
        </div>
        <div class="nav-item" onclick="showPage('messages')" id="nav-messages">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          <span class="nav-item-label">Messages</span>
          <span class="nav-badge" id="badge-messages">3</span>
        </div>
        <div class="nav-item" onclick="showPage('calendar')" id="nav-calendar">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <span class="nav-item-label">Calendrier</span>
        </div>
      </div>

      <!-- INTÉGRATION & OUTILS -->
      <?php if ($user['role'] === 'admin'): ?>
      <div class="nav-section">
        <div class="nav-section-label">Intégration &amp; Outils</div>
        <div class="nav-item" onclick="showPage('integrations')" id="nav-integrations">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
          <span class="nav-item-label">Intégrations</span>
        </div>
        <div class="nav-item" onclick="showPage('analytics')" id="nav-analytics">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
          <span class="nav-item-label">Analytics</span>
        </div>
        <div class="nav-item" onclick="showPage('automations')" id="nav-automations">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          <span class="nav-item-label">Automatisations</span>
        </div>
      </div>
      <?php endif; ?>

      <!-- SYSTÈME -->
      <div class="nav-section">
        <div class="nav-section-label">Système</div>
        <div class="nav-item" onclick="showPage('settings')" id="nav-settings">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 9 15a1.65 1.65 0 0 0-1.82-.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 15 9a1.65 1.65 0 0 0 1.82.33l.06-.06a2 2 0 0 1 2.83 2.83z"/></svg>
          <span class="nav-item-label">Paramètres</span>
        </div>
      </div>
    </div>

    <div class="sidebar-footer">
      <div class="user-info dropdown" onclick="toggleDropdown('userMenu')">
        <div class="avatar" id="userAvatar"><?= htmlspecialchars($initials) ?></div>
        <div class="user-details">
          <div class="user-name" id="userName"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></div>
          <div class="user-role"><?= htmlspecialchars(getStatusLabel($user['role'] ?? 'developpeur')) ?></div>
        </div>
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-left:auto;color:var(--gray-600)"><polyline points="6 9 12 15 18 9"/></svg>
      </div>
      <div class="dropdown-menu" id="userMenu" style="bottom:60px;top:auto">
        <a class="dropdown-item" href="#" onclick="showPage('settings')">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-.5 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-.5H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 .5-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
          Profil &amp; Paramètres
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item danger" href="logout.php">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Déconnexion
        </a>
      </div>
    </div>
  </nav>

  <!-- ─── MAIN CONTENT ──────────────────────────── -->
  <div class="main-content">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-title">
        <h1 id="pageTitle">Tableau de Bord</h1>
        <p id="pageSubtitle">Bienvenue, <span id="topUserName"><?= htmlspecialchars($user['firstname']) ?></span> 👋 Voici votre aperçu</p>
      </div>
      <div class="topbar-spacer"></div>
      <div class="topbar-search" style="position:relative">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--gray-600)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="globalSearch" placeholder="Rechercher projets, tâches, membres..." oninput="handleSearch(this.value)" autocomplete="off" />
        <div id="searchResults" class="search-results-dropdown"></div>
      </div>
      <div class="topbar-actions">
        <button class="topbar-btn" id="notifBtn" onclick="togglePanel('notifPanel')" data-tooltip="Notifications" aria-label="Notifications">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
          <?php if ($notifCount > 0): ?>
          <span class="topbar-badge"><?= $notifCount ?></span>
          <?php endif; ?>
        </button>
        <button class="topbar-btn" onclick="openNewProjectModal()" data-tooltip="Nouveau projet" aria-label="Créer un projet">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        </button>
        <div class="dropdown">
          <button class="topbar-btn" onclick="toggleDropdown('avatarMenu')" aria-label="Menu utilisateur">
            <div class="avatar avatar-sm" style="width:30px;height:30px;font-size:11px"><?= htmlspecialchars($initials) ?></div>
          </button>
          <div class="dropdown-menu" id="avatarMenu">
            <a class="dropdown-item" href="#" onclick="showPage('settings')">
              <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              Mon profil
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item danger" href="logout.php">
              <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Déconnexion
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content" id="pageContent">

      <!-- ══ PAGE: DASHBOARD ═══════════════════════════ -->
      <section class="page-section active" id="page-dashboard">
        <div class="row g-3 mb-4" id="statsGrid">
          <!-- Chargé via AJAX -->
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card animate-in" style="animation-delay:.05s">
              <div class="stat-icon blue">📁</div>
              <div class="stat-value" id="stat-projects">–</div>
              <div class="stat-label">Projets actifs</div>
              <div class="stat-change up" id="stat-projects-change"></div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card animate-in" style="animation-delay:.1s">
              <div class="stat-icon green">✅</div>
              <div class="stat-value" id="stat-tasks-done">–</div>
              <div class="stat-label">Tâches terminées</div>
              <div class="stat-change up">↑ Ce mois</div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card animate-in" style="animation-delay:.15s">
              <div class="stat-icon yellow">⏳</div>
              <div class="stat-value" id="stat-tasks-progress">–</div>
              <div class="stat-label">Tâches en cours</div>
              <div class="stat-change down" id="stat-tasks-late"></div>
            </div>
          </div>
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="stat-card animate-in" style="animation-delay:.2s">
              <div class="stat-icon purple">👥</div>
              <div class="stat-value" id="stat-members">–</div>
              <div class="stat-label">Membres actifs</div>
              <div class="stat-change up">↑ 3 nouveaux</div>
            </div>
          </div>
        </div>

        <!-- DATA GRID ROW 1 -->
        <div class="row row-cols-1 row-cols-xl-2 g-4 mb-4" style="margin-bottom:24px">
          <div class="card animate-in" style="animation-delay:.25s">
            <div class="card-header">
              <div>
                <div class="card-title">Activité Hebdomadaire</div>
                <div class="card-subtitle">Tâches complétées par jour</div>
              </div>
              <span class="badge badge-blue">Cette semaine</span>
            </div>
            <div class="chart-bars" id="weekChart"></div>
          </div>

          <div class="card animate-in" style="animation-delay:.3s">
            <div class="card-header">
              <div>
                <div class="card-title">Répartition Projets</div>
                <div class="card-subtitle">Statut global</div>
              </div>
            </div>
            <div id="projectsDonut" style="display:flex;align-items:center;gap:28px">
              <div class="donut-wrap">
                <svg width="140" height="140" viewBox="0 0 140 140">
                  <circle cx="70" cy="70" r="54" fill="none" stroke="rgba(59,130,246,.1)" stroke-width="20"/>
                  <circle cx="70" cy="70" r="54" fill="none" stroke="#2563eb" stroke-width="20" stroke-dasharray="135 204" stroke-dashoffset="0" transform="rotate(-90 70 70)" stroke-linecap="round"/>
                  <circle cx="70" cy="70" r="54" fill="none" stroke="#60a5fa" stroke-width="20" stroke-dasharray="76 263" stroke-dashoffset="-135" transform="rotate(-90 70 70)" stroke-linecap="round"/>
                  <circle cx="70" cy="70" r="54" fill="none" stroke="#1e3a8a" stroke-width="20" stroke-dasharray="46 293" stroke-dashoffset="-211" transform="rotate(-90 70 70)" stroke-linecap="round"/>
                  <circle cx="70" cy="70" r="54" fill="none" stroke="#ef4444" stroke-width="20" stroke-dasharray="28 311" stroke-dashoffset="-257" transform="rotate(-90 70 70)" stroke-linecap="round"/>
                </svg>
                <div class="donut-center">
                  <span class="donut-val" id="donut-total">–</span>
                  <span class="donut-lbl">Projets</span>
                </div>
              </div>
              <div class="donut-legend" id="donut-legend">
                <div class="legend-item"><div class="legend-dot" style="background:#2563eb"></div><span>En cours</span></div>
                <div class="legend-item"><div class="legend-dot" style="background:#60a5fa"></div><span>Planifié</span></div>
                <div class="legend-item"><div class="legend-dot" style="background:#1e3a8a"></div><span>Terminé</span></div>
                <div class="legend-item"><div class="legend-dot" style="background:#ef4444"></div><span>En retard</span></div>
              </div>
            </div>
          </div>
        </div>

        <!-- DATA GRID ROW 2 -->
        <div class="row row-cols-1 row-cols-xl-2 g-4 mb-4" style="margin-bottom:24px">
          <div class="card animate-in" style="animation-delay:.35s">
            <div class="card-header">
              <div>
                <div class="card-title">Projets Récents</div>
                <div class="card-subtitle">Vos projets actifs</div>
              </div>
              <button class="btn btn-secondary btn-sm" onclick="showPage('projects')">Voir tout</button>
            </div>
            <div id="recentProjectsList"><div class="loading-pulse"></div></div>
          </div>

          <div class="card animate-in" style="animation-delay:.4s">
            <div class="card-header">
              <div>
                <div class="card-title">Tâches Prioritaires</div>
                <div class="card-subtitle">À faire aujourd'hui</div>
              </div>
              <button class="btn btn-secondary btn-sm" onclick="showPage('tasks')">Voir tout</button>
            </div>
            <div id="priorityTasksList"><div class="loading-pulse"></div></div>
          </div>
        </div>

        <!-- DATA GRID ROW 3 -->
        <div class="row row-cols-1 row-cols-xl-2 g-4 mb-4" style="margin-bottom:24px">
          <div class="card animate-in" style="animation-delay:.45s">
            <div class="card-header">
              <div><div class="card-title">Activité Récente</div></div>
            </div>
            <div id="activityFeed"><div class="loading-pulse"></div></div>
          </div>

          <div style="display:flex;flex-direction:column;gap:20px">
            <div class="card animate-in" style="animation-delay:.5s">
              <div class="card-title" style="margin-bottom:16px">Actions Rapides</div>
              <div class="quick-grid">
                <div class="quick-action" onclick="openNewProjectModal()">
                  <span class="quick-icon">📁</span><span class="quick-label">Nouveau Projet</span>
                </div>
                <div class="quick-action" onclick="openNewTaskModal()">
                  <span class="quick-icon">✏️</span><span class="quick-label">Nouvelle Tâche</span>
                </div>
                <div class="quick-action" onclick="showPage('team')">
                  <span class="quick-icon">👤</span><span class="quick-label">Inviter Membre</span>
                </div>
                <?php if ($user['role'] === 'admin'): ?>
                <div class="quick-action" onclick="showPage('integrations')">
                  <span class="quick-icon">🔗</span><span class="quick-label">Connecter Outil</span>
                </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="card animate-in" style="animation-delay:.55s">
              <div class="card-title" style="margin-bottom:14px">Mini Calendrier</div>
              <div class="calendar-mini" id="calendarMini"></div>
            </div>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: PROJETS ════════════════════════════ -->
      <section class="page-section" id="page-projects">
        <div class="section-header">
          <div>
            <div class="section-title">Gestion des Projets</div>
            <p class="text-sm text-gray mt-2" id="projects-count-label">Chargement...</p>
          </div>
          <button class="btn btn-primary" style="width:auto;padding:10px 20px" onclick="openNewProjectModal()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouveau Projet
          </button>
        </div>
        <div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap" id="projectFilters">
          <button class="btn btn-primary btn-sm" style="width:auto" onclick="filterProjects('all')">Tous</button>
          <button class="btn btn-secondary btn-sm" onclick="filterProjects('active')">En cours</button>
          <button class="btn btn-secondary btn-sm" onclick="filterProjects('planned')">Planifié</button>
          <button class="btn btn-secondary btn-sm" onclick="filterProjects('done')">Terminé</button>
          <button class="btn btn-secondary btn-sm" onclick="filterProjects('late')">En retard</button>
        </div>
        <div id="projectsGrid" class="row g-4">
          <div class="col-12 col-md-6 col-xl-4"><div class="loading-pulse" style="height:200px;border-radius:16px"></div></div>
          <div class="col-12 col-md-6 col-xl-4"><div class="loading-pulse" style="height:200px;border-radius:16px"></div></div>
          <div class="col-12 col-md-6 col-xl-4"><div class="loading-pulse" style="height:200px;border-radius:16px"></div></div>
        </div>
      </section>

      <!-- ══ PAGE: TÂCHES ═════════════════════════════ -->
      <section class="page-section" id="page-tasks">
        <div class="section-header">
          <div>
            <div class="section-title">Gestion des Tâches</div>
            <p class="text-sm text-gray mt-2" id="tasks-count-label">Chargement...</p>
          </div>
          <button class="btn btn-primary" style="width:auto;padding:10px 20px" onclick="openNewTaskModal()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouvelle Tâche
          </button>
        </div>
        <div class="card">
          <div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap" id="taskFilters">
            <button class="btn btn-primary btn-sm" style="width:auto" onclick="filterTasks('all')">Toutes</button>
            <button class="btn btn-secondary btn-sm" onclick="filterTasks('todo')">À faire</button>
            <button class="btn btn-secondary btn-sm" onclick="filterTasks('in_progress')">En cours</button>
            <button class="btn btn-secondary btn-sm" onclick="filterTasks('review')">En révision</button>
            <button class="btn btn-secondary btn-sm" onclick="filterTasks('done')">Terminées</button>
          </div>
          <div style="overflow-x:auto">
            <table class="data-table" id="tasksTable">
              <thead>
                <tr>
                  <th style="width:40px"></th>
                  <th>Tâche</th>
                  <th>Projet</th>
                  <th>Assigné à</th>
                  <th>Priorité</th>
                  <th>Statut</th>
                  <th>Échéance</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="tasksTableBody">
                <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gray-500)">Chargement...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: KANBAN ═════════════════════════════ -->
      <section class="page-section" id="page-kanban">
        <div class="section-header">
          <div><div class="section-title">Tableau Kanban</div></div>
          <button class="btn btn-primary" style="width:auto;padding:10px 20px" onclick="openNewTaskModal()">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Ajouter une carte
          </button>
        </div>
        <div class="kanban-board" id="kanbanBoard"></div>
      </section>

      <!-- ══ PAGE: PLANNING GANTT ═════════════════════ -->
      <section class="page-section" id="page-timeline">
        <div class="section-header">
          <div><div class="section-title">Planning Gantt</div><p class="text-sm text-gray mt-2">Vue chronologique de vos projets</p></div>
        </div>
        <div class="card">
          <div style="overflow-x:auto">
            <div id="ganttChart" style="min-width:600px;padding-top:8px"></div>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: ÉQUIPE ═════════════════════════════ -->
      <section class="page-section" id="page-team">
        <div class="section-header">
          <div><div class="section-title">Gestion des Équipes</div><p class="text-sm text-gray mt-2" id="team-count-label">Chargement...</p></div>
          <button class="btn btn-primary" style="width:auto;padding:10px 20px" onclick="openModal('newTeamModal')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Créer une équipe
          </button>
        </div>
        <div class="row row-cols-1 row-cols-lg-2 g-4">
          <div class="card" style="height: fit-content;">
            <div class="card-title" style="margin-bottom:20px">Équipes actives</div>
            <!-- Liste des équipes créées par l'admin affichées ici -->
            <div id="teamsList"><div class="loading-pulse"></div></div>
          </div>
          
          <div class="card">
            <div class="card-title" style="margin-bottom:16px">Annuaire des Utilisateurs</div>
            <div class="input-wrapper" style="margin-bottom: 16px; position:relative;">
              <span class="input-icon" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              </span>
              <input type="text" id="userSearchInput" placeholder="Rechercher par nom..." oninput="searchUsersForTeam(this.value)" style="width:100%;padding:12px 12px 12px 36px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);color:white;border-radius:10px;outline:none;">
            </div>
            <div id="teamMembersList" style="max-height: 400px; overflow-y:auto; padding-right:8px;">
              <div class="loading-pulse"></div>
            </div>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: MESSAGES ═══════════════════════════ -->
      <section class="page-section" id="page-messages">
        <div class="section-title" style="margin-bottom:20px">Messagerie d'Équipe</div>
        <div class="row g-3" style="height:calc(100vh - 180px)">
          <div class="col-12 col-lg-3" style="height:100%">
            <div class="card" style="overflow-y:auto">
              <div style="font-size:12px;font-weight:700;color:var(--gray-600);letter-spacing:.8px;text-transform:uppercase;margin-bottom:12px">ÉQUIPES</div>
              <input type="text" id="channelSearch" placeholder="Rechercher une équipe..." oninput="filterChannels()" style="width:100%;padding:8px 12px;margin-bottom:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);color:white;border-radius:6px;outline:none;font-size:13px;">
              <div id="channelsList" style="display:flex;flex-direction:column;gap:4px"></div>
            </div>
          </div>
          <div class="col-12 col-md-8 col-xl-9 h-100">
            <div class="card" style="display:flex;flex-direction:column;height:100%" id="chatArea">
              <div id="noChannelSelected" style="flex:1;display:flex;align-items:center;justify-content:center;color:var(--gray-500)">
                Veuillez sélectionner une équipe pour afficher la messagerie.
              </div>
              <div id="activeChatArea" style="display:none;flex-direction:column;height:100%">
                <div style="border-bottom:1px solid rgba(59,130,246,.1);padding-bottom:14px;margin-bottom:14px">
                  <div style="font-weight:700" id="currentChannelName"># général</div>
                  <div style="font-size:12px;color:var(--gray-500)" id="currentChannelDesc">Canal de discussion principal de l'équipe</div>
                </div>
                <div id="chatMessages" style="flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:16px;margin-bottom:16px"></div>
              <div style="display:flex;gap:12px;align-items:center">
                <input type="text" id="chatInput" placeholder="Écrire un message..."
                  onkeydown="handleChatKey(event)"
                  style="flex:1;padding:12px 16px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none" />
                  <button onclick="sendMessage()" class="btn btn-primary" style="width:auto;padding:12px 20px">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: CALENDRIER ═════════════════════════ -->
      <section class="page-section" id="page-calendar">
        <div class="section-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
          <div class="section-title">Calendrier des Projets</div>
          <button class="btn btn-primary" style="width:auto;padding:8px 16px" onclick="openModal('newEventModal')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouvel événement
          </button>
        </div>
        <div class="row g-4">
          <div class="col-12 col-lg-8">
            <div class="card"><div id="bigCalendar"></div></div>
          </div>
          <div class="col-12 col-lg-4">
            <div class="card">
              <div class="card-title" style="margin-bottom:16px">Événements à venir</div>
              <div id="upcomingEvents" style="display:flex;flex-direction:column;gap:12px"></div>
            </div>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: INTÉGRATIONS ════════════════════════ -->
      <section class="page-section" id="page-integrations">
        <div class="section-header">
          <div><div class="section-title">Centre d'Intégrations</div><p class="text-sm text-gray mt-2">Connectez vos outils préférés à NexaFlow</p></div>
        </div>
        <div style="display:flex;gap:10px;margin-bottom:24px;flex-wrap:wrap">
          <button class="btn btn-primary btn-sm" style="width:auto">Tous</button>
          <button class="btn btn-secondary btn-sm">Communication</button>
          <button class="btn btn-secondary btn-sm">Développement</button>
          <button class="btn btn-secondary btn-sm">Stockage</button>
          <button class="btn btn-secondary btn-sm">Analytics</button>
          <button class="btn btn-secondary btn-sm">Design</button>
        </div>
        <div class="row g-3" id="integrationsGrid"></div>
        <div class="card" style="margin-top:28px">
          <div class="card-header">
            <div><div class="card-title">🔄 Hub de Données Unifié</div><div class="card-subtitle">Synchronisation centralisée de toutes vos intégrations</div></div>
            <span class="badge badge-success">Actif</span>
          </div>
          <div id="hubStats" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px"></div>
        </div>
      </section>

      <!-- ══ PAGE: ANALYTICS ══════════════════════════ -->
      <section class="page-section" id="page-analytics">
        <div class="section-title" style="margin-bottom:20px">Analytics &amp; Rapports</div>
        <div class="row g-3 mb-4" style="margin-bottom:24px" id="analyticsStats"></div>
        <div class="row row-cols-1 row-cols-xl-2 g-4 mb-4">
          <div class="card">
            <div class="card-title" style="margin-bottom:16px">Performance mensuelle</div>
            <div class="chart-bars" id="monthChart"></div>
          </div>
          <div class="card">
            <div class="card-title" style="margin-bottom:16px">Top Contributeurs</div>
            <div id="topContributors"></div>
          </div>
        </div>
      </section>

      <!-- ══ PAGE: AUTOMATISATIONS ════════════════════ -->
      <section class="page-section" id="page-automations">
        <div class="section-header">
          <div><div class="section-title">Automatisations</div><p class="text-sm text-gray mt-2">Créez des flux de travail intelligents</p></div>
          <button class="btn btn-primary" style="width:auto;padding:10px 20px" onclick="openModal('newAutoModal')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouvelle automatisation
          </button>
        </div>
        <div id="automationsList" style="display:flex;flex-direction:column;gap:16px"></div>
      </section>

      <!-- ══ PAGE: PARAMÈTRES ═════════════════════════ -->
      <section class="page-section" id="page-settings">
        <div class="section-title" style="margin-bottom:20px">Paramètres</div>
        <div class="settings-tabs">
          <button class="settings-tab active" onclick="switchSettingsTab('profile',this)">Profil</button>
          
          <?php if ($user['role'] === 'admin'): ?>
          <button class="settings-tab" onclick="switchSettingsTab('org',this)">Organisation</button>
          <?php endif; ?>
          
          <button class="settings-tab" onclick="switchSettingsTab('notifs',this)">Notifications</button>
          <button class="settings-tab" onclick="switchSettingsTab('security',this)">Sécurité</button>
          
          <?php if ($user['role'] === 'admin'): ?>
          <button class="settings-tab" onclick="switchSettingsTab('billing',this)">Abonnement</button>
          <?php endif; ?>
        </div>
        <div id="settingsContent"></div>
      </section>

    </main>
  </div>
</div>

<!-- ─── NOTIFICATION PANEL ───────────────────────── -->
<div class="notif-panel" id="notifPanel">
  <div class="notif-header">
    <span class="notif-title">Notifications</span>
    <button class="btn btn-ghost btn-sm" onclick="markAllRead()">Tout lire</button>
  </div>
  <div class="notif-scroll" id="notifList"></div>
</div>

<!-- ─── MODALS ────────────────────────────────────── -->

<!-- New Project Modal -->
<div class="modal-overlay" id="newProjectModal">
  <div class="modal-box">
    <div class="modal-header">
      <span class="modal-title">📁 Nouveau Projet</span>
      <button class="modal-close" onclick="closeModal('newProjectModal')">✕</button>
    </div>
    <form onsubmit="createProject(event)">
      <div class="form-group"><label>Nom du projet *</label><div class="input-wrapper"><input type="text" id="projName" placeholder="Mon super projet" required style="padding-left:14px"/></div></div>
      <div class="form-group"><label>Description</label><textarea id="projDesc" placeholder="Décrivez votre projet..." rows="3" style="width:100%;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;padding:12px 14px;outline:none;resize:vertical"></textarea></div>
      <div class="row row-cols-1 row-cols-md-2 g-3">
        <div class="form-group"><label>Date de début</label><div class="input-wrapper"><input type="date" id="projStart" style="padding-left:14px"/></div></div>
        <div class="form-group"><label>Date de fin</label><div class="input-wrapper"><input type="date" id="projEnd" style="padding-left:14px"/></div></div>
      </div>
      <div class="form-group">
        <label>Priorité</label>
        <select id="projPriority" style="width:100%;padding:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none">
          <option value="haute">Haute</option><option value="moyenne" selected>Moyenne</option><option value="basse">Basse</option>
        </select>
      </div>
      <div class="form-group">
        <label>Équipe associée (Optionnel)</label>
        <select id="projTeam" style="width:100%;padding:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none">
          <option value="">Aucune</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('newProjectModal')">Annuler</button>
        <button type="submit" class="btn btn-primary" style="width:auto;padding:10px 24px">Créer le projet</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Nouvel Événement -->
<div class="modal-overlay" id="newEventModal">
  <div class="modal-box" style="max-width:500px">
    <div class="modal-header">
      <div class="modal-title">Nouvel événement</div>
      <button class="modal-close" onclick="closeModal('newEventModal')">✕</button>
    </div>
    <form onsubmit="createCalendarEvent(event)">
      <div class="form-group">
        <label>Titre de l'événement *</label>
        <div class="input-wrapper"><input type="text" id="eventTitle" placeholder="Ex: Jour férié, Anniversaire..." required style="padding-left:14px"/></div>
      </div>
      <div class="row row-cols-1 row-cols-md-2 g-3">
        <div class="form-group"><label>Date *</label><div class="input-wrapper"><input type="date" id="eventDate" required style="padding-left:14px"/></div></div>
        <div class="form-group"><label>Heure (Optionnel)</label><div class="input-wrapper"><input type="time" id="eventTime" style="padding-left:14px"/></div></div>
      </div>
      <div class="form-group">
        <label>Couleur</label>
        <select id="eventColor" style="width:100%;padding:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none">
          <option value="#6366f1">Indigo (Défaut)</option>
          <option value="#ec4899">Rose (Anniversaire)</option>
          <option value="#8b5cf6">Violet (Férié)</option>
          <option value="#14b8a6">Teal (Réunion)</option>
          <option value="#f59e0b">Orange (Rappel)</option>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('newEventModal')">Annuler</button>
        <button type="submit" class="btn btn-primary" style="width:auto;padding:10px 24px">Créer l'événement</button>
      </div>
    </form>
  </div>
</div>

<!-- New Task Modal -->
<div class="modal-overlay" id="newTaskModal">
  <div class="modal-box">
    <div class="modal-header">
      <span class="modal-title">✏️ Nouvelle Tâche</span>
      <button class="modal-close" onclick="closeModal('newTaskModal')">✕</button>
    </div>
    <form onsubmit="createTask(event)">
      <div class="form-group">
        <label>Assigner à</label>
        <select id="taskAssignee" style="width:100%;padding:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none">
          <option value="">Sélectionner un membre...</option>
        </select>
      </div>
      <div class="form-group"><label>Titre de la tâche *</label><div class="input-wrapper"><input type="text" id="taskTitle" placeholder="Ex: Implémenter l'authentification" required style="padding-left:14px"/></div></div>
      <div class="form-group">
        <label>Projet</label>
        <select id="taskProject" onchange="updateAssigneeOptions(this.value)" style="width:100%;padding:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none"></select>
      </div>
      <div class="row row-cols-1 row-cols-md-2 g-3">
        <div class="form-group">
          <label>Priorité</label>
          <select id="taskPriority" style="width:100%;padding:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none">
            <option value="haute">Haute</option><option value="moyenne" selected>Moyenne</option><option value="basse">Basse</option>
          </select>
        </div>
        <div class="form-group"><label>Échéance</label><div class="input-wrapper"><input type="date" id="taskDue" style="padding-left:14px"/></div></div>
      </div>
      <div class="form-group"><label>Description</label><textarea id="taskDesc" placeholder="Détails de la tâche..." rows="3" style="width:100%;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;padding:12px 14px;outline:none;resize:vertical"></textarea></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="closeModal('newTaskModal')">Annuler</button>
        <button type="submit" class="btn btn-primary" style="width:auto;padding:10px 24px">Créer la tâche</button>
      </div>
    </form>
  </div>
</div>

<!-- Modals Equipes et Utilisateurs -->
<div class="modal-overlay" id="newTeamModal">
  <div class="modal-box">
    <div class="modal-header">
      <span class="modal-title">📁 Nouvelle Équipe</span>
      <button class="modal-close" onclick="closeModal('newTeamModal')">✕</button>
    </div>
    <div class="form-group">
      <label>Nom de l'équipe *</label>
      <div class="input-wrapper">
        <input type="text" id="newTeamName" placeholder="Ex: Développeurs Backend" style="padding-left:14px;width:100%;height:40px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);color:white;border-radius:8px;" />
      </div>
    </div>
    <div class="modal-footer" style="margin-top:20px;">
      <button type="button" class="btn btn-ghost" onclick="closeModal('newTeamModal')">Annuler</button>
      <button type="button" class="btn btn-primary" onclick="createTeam()" style="width:auto;padding:10px 24px">Créer l'équipe</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="projectMembersModal">
  <div class="modal-box" style="max-width: 500px;">
    <div class="modal-header">
      <span class="modal-title">👥 Membres du Projet</span>
      <button class="modal-close" onclick="closeModal('projectMembersModal')">✕</button>
    </div>
    <div style="margin-bottom:12px">
      <h4 id="pmProjectName" style="font-size:16px;font-weight:600">Nom du projet</h4>
      <p style="font-size:12px;color:var(--gray-500)" id="pmMembersCount">Chargement...</p>
    </div>
    <div id="projectMembersList" style="max-height: 350px; overflow-y:auto; display:flex; flex-direction:column; gap:8px; padding-right:8px">
      <div class="loading-pulse"></div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="projectTasksModal">
  <div class="modal-box" style="max-width: 600px;">
    <div class="modal-header">
      <span class="modal-title">📋 Tâches du Projet</span>
      <button class="modal-close" onclick="closeModal('projectTasksModal')">✕</button>
    </div>
    <div style="margin-bottom:12px">
      <h4 id="ptProjectName" style="font-size:16px;font-weight:600">Nom du projet</h4>
      <p style="font-size:12px;color:var(--gray-500)" id="ptTasksCount">Chargement...</p>
    </div>
    <div id="projectTasksList" style="max-height: 400px; overflow-y:auto; display:flex; flex-direction:column; gap:4px; padding-right:8px">
      <div class="loading-pulse"></div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="userProfileModal">
  <div class="modal-box" style="max-width: 400px;">
    <div class="modal-header">
      <span class="modal-title">👤 Profil Utilisateur</span>
      <button class="modal-close" onclick="closeModal('userProfileModal')">✕</button>
    </div>
    <div id="userProfileContent" style="text-align:center; padding: 10px 0;">
      <div class="avatar" id="upAvatar" style="width:64px;height:64px;font-size:24px;margin:0 auto 12px;">?</div>
      <h3 id="upName" style="font-size:18px;font-weight:700;margin-bottom:4px;">John Doe</h3>
      <p id="upEmail" style="color:var(--gray-500);font-size:13px;margin-bottom:6px;">john@doe.com</p>
      <p style="margin-bottom:24px"><span class="badge badge-blue" id="upRole">Développeur</span></p>
      
      <div style="text-align:left; background:rgba(59,130,246,.08); border:1px solid rgba(59,130,246,.2); padding:16px; border-radius:12px;">
        <label style="font-size:13px;font-weight:600;display:block;margin-bottom:8px">Ajouter à une équipe :</label>
        <select id="upTeamSelect" style="width:100%;padding:10px 14px;margin-bottom:12px;border-radius:8px;background:rgba(10,22,40,.8);color:white;border:1px solid rgba(59,130,246,.3);outline:none;">
          <!-- Fetch teams via JS -->
        </select>
        <button class="btn btn-primary" onclick="addUserToTeam()" style="width:100%;display:flex;justify-content:center;gap:8px;">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Ajouter à l'équipe
        </button>
        <input type="hidden" id="upUserId">
      </div>
    </div>
  </div>
</div>

<!-- Auto Modal -->
<div class="modal-overlay" id="newAutoModal">
  <div class="modal-box">
    <div class="modal-header">
      <span class="modal-title">⚡ Nouvelle Automatisation</span>
      <button class="modal-close" onclick="closeModal('newAutoModal')">✕</button>
    </div>
    <div class="form-group"><label>Nom de l'automatisation</label><div class="input-wrapper"><input type="text" id="autoName" placeholder="Ex: Notification tâche terminée" style="padding-left:14px"/></div></div>
    <div class="form-group">
      <label>Déclencheur (Trigger)</label>
      <select id="autoTrigger" style="width:100%;padding:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none">
        <option value="task_done">Tâche terminée</option><option value="task_assigned">Nouvel assignement</option><option value="due_date_exceeded">Date d'échéance atteinte</option><option value="status_changed">Statut modifié</option>
      </select>
    </div>
    <div class="form-group">
      <label>Action</label>
      <select id="autoAction" style="width:100%;padding:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none">
        <option value="send_notification">Envoyer une notification</option><option value="send_email">Envoyer un email</option><option value="create_task">Créer une tâche</option><option value="update_status">Mettre à jour un statut</option>
      </select>
    </div>
    <div class="modal-footer">
      <button class="btn btn-ghost" onclick="closeModal('newAutoModal')">Annuler</button>
      <button class="btn btn-primary" style="width:auto;padding:10px 24px" onclick="createAutomation()">Créer</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<!-- Data PHP → JS -->
<script>
const NX_USER = {
  id: <?= $user['id'] ?>,
  firstname: <?= json_encode($user['firstname']) ?>,
  lastname: <?= json_encode($user['lastname']) ?>,
  email: <?= json_encode($user['email']) ?>,
  role: <?= json_encode($user['role']) ?>,
  initials: <?= json_encode($initials) ?>,
  organisation: <?= json_encode($user['organisation'] ?? '') ?>
};
</script>

<script src="assets/js/dashboard.js?v=<?= filemtime('assets/js/dashboard.js') ?>"></script>
<script src="assets/js/calendar.js?v=<?= filemtime('assets/js/calendar.js') ?>"></script>
<script src="assets/js/settings.js"></script>
<script src="assets/js/messages.js?v=<?= filemtime('assets/js/messages.js') ?>"></script>
</body>
</html>





