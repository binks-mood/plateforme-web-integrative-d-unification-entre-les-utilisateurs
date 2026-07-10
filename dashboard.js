// =====================================================
// NexaFlow PHP – Dashboard JavaScript Principal
// Toutes les données viennent de l'API PHP via AJAX
// =====================================================

const API = {
  stats:        'api/dashboard/stats.php',
  projects:     'api/projects/index.php',
  tasks:        'api/tasks/index.php',
  team:         'api/team/index.php',
  messages:     'api/messages/index.php',
  notifications:'api/notifications/index.php',
  integrations: 'api/integrations/index.php',
  automations:  'api/automations/index.php',
  teams:        'api/teams/index.php',
  search:       'api/search/index.php',
  export:       'api/export/index.php',
  comments:     'api/comments/index.php',
};

function exportCSV(type) {
  window.location.href = `${API.export}?type=${type}`;
}

// ─────────────────────────────────────────────────────
// FETCH HELPER
// ─────────────────────────────────────────────────────

async function apiGet(url, params = {}) {
  const qs = new URLSearchParams(params).toString();
  const res = await fetch(`${url}${qs ? '?' + qs : ''}`);
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return res.json();
}

async function apiPost(url, action, data = {}) {
  const res = await fetch(`${url}?action=${action}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  });
  return res.json();
}

// ─────────────────────────────────────────────────────
// NAVIGATION
// ─────────────────────────────────────────────────────

const pageTitles = {
  dashboard:    ['Tableau de Bord',       `Bienvenue, <span id="topUserName">${NX_USER.firstname}</span> 👋 Voici votre aperçu`],
  projects:     ['Projets',               'Gérez vos projets actifs'],
  tasks:        ['Tâches',                'Suivez et organisez vos tâches'],
  kanban:       ['Tableau Kanban',        'Visualisation par statut'],
  timeline:     ['Planning Gantt',        'Vue chronologique de vos projets'],
  team:         ['Équipe',               'Membres & permissions'],
  messages:     ['Messagerie',            'Discutez avec votre équipe'],
  calendar:     ['Calendrier',            'Événements à venir'],
  integrations: ['Intégrations',          'Connectez vos outils préférés'],
  analytics:    ['Analytics',             'Rapports & performance'],
  automations:  ['Automatisations',       'Flux de travail intelligents'],
  settings:     ['Paramètres',            'Gérez votre compte'],
};

let currentPage = 'dashboard';
const pageLoaded = new Set();

function showPage(page) {
  // Sections
  document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
  const sec = document.getElementById(`page-${page}`);
  if (sec) sec.classList.add('active');

  // Nav items
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  const nav = document.getElementById(`nav-${page}`);
  if (nav) nav.classList.add('active');

  // Topbar title
  const [title, sub] = pageTitles[page] || [page, ''];
  document.getElementById('pageTitle').textContent = title;
  document.getElementById('pageSubtitle').innerHTML = sub;

  currentPage = page;
  closeAllDropdowns();

  // Marquer comme "vu" et cacher le badge
  const badge = document.getElementById(`badge-${page}`);
  if (badge) {
    badge.style.display = 'none';
    if (lastStats) {
      if (page === 'projects') localStorage.setItem('seen-projects', lastStats.projects_active);
      if (page === 'tasks')    localStorage.setItem('seen-tasks', lastStats.tasks_progress);
      if (page === 'messages') localStorage.setItem('seen-messages', lastStats.messages_count);
    }
  }

  // Charger les données si pas encore chargées
  if (!pageLoaded.has(page)) {
    pageLoaded.add(page);
    loadPageData(page);
  }
}

function loadPageData(page) {
  switch (page) {
    case 'dashboard':    loadDashboard(); break;
    case 'projects':     loadProjects(); break;
    case 'tasks':        loadTasks(); break;
    case 'kanban':       loadKanban(); break;
    case 'timeline':     loadGantt(); break;
    case 'team':         loadTeam(); break;
    case 'messages':     loadMessages(); break;
    case 'calendar':     loadCalendar(); break;
    case 'integrations': loadIntegrations(); break;
    case 'analytics':    loadAnalytics(); break;
    case 'automations':  loadAutomations(); break;
    case 'settings':     loadSettings('profile'); break;
  }
}

// ─────────────────────────────────────────────────────
// DASHBOARD
// ─────────────────────────────────────────────────────

async function loadDashboard() {
  try {
    const data = await apiGet(API.stats);
    lastStats = data;

    // Stats
    animateCounter('stat-projects', data.projects_active);
    animateCounter('stat-tasks-done', data.tasks_done);
    animateCounter('stat-tasks-progress', data.tasks_progress);
    animateCounter('stat-members', data.members);

    if (data.tasks_late > 0) {
      document.getElementById('stat-tasks-late').textContent = `↓ ${data.tasks_late} en retard`;
      document.getElementById('stat-tasks-late').className = 'stat-change down';
    }

    // Alertes deadlines dans les 48h
    if (data.tasks_due_soon && data.tasks_due_soon.length > 0) {
      const deadlineBar = document.getElementById('deadlineAlertBar');
      if (deadlineBar) {
        const names = data.tasks_due_soon.slice(0, 2).map(t => `"${t.title}"`).join(', ');
        const more  = data.tasks_due_soon.length > 2 ? ` (+${data.tasks_due_soon.length - 2})` : '';
        deadlineBar.style.display = 'flex';
        deadlineBar.innerHTML = `
          <span style="font-size:18px">⏰</span>
          <div style="flex:1">
            <strong>${data.tasks_due_soon.length} tâche${data.tasks_due_soon.length > 1 ? 's' : ''} à rendre dans 48h :</strong>
            ${names}${more}
          </div>
          <button onclick="showPage('tasks')" style="background:rgba(255,255,255,.2);border:none;color:white;padding:4px 12px;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600">Voir</button>
          <button onclick="this.closest('#deadlineAlertBar').style.display='none'" style="background:none;border:none;color:white;cursor:pointer;font-size:16px;padding:0 4px">✕</button>
        `;
      }
    }

    // Donut
    document.getElementById('donut-total').textContent = data.projects_total;

    // Projets récents
    const projList = document.getElementById('recentProjectsList');
    if (data.recent_projects.length === 0) {
      projList.innerHTML = '<p style="color:var(--gray-500);font-size:13px;text-align:center;padding:20px">Aucun projet pour l\'instant</p>';
    } else {
      projList.innerHTML = data.recent_projects.map(p => `
        <div class="project-item" style="display:flex;align-items:center;gap:12px;padding:12px;border-radius:10px;margin-bottom:8px;background:rgba(59,130,246,.05);position:relative;cursor:pointer;flex-direction:column;align-items:stretch" onclick="showPage('projects')">
          <div style="display:flex;align-items:center;gap:12px">
            <div style="width:10px;height:10px;border-radius:50%;background:${p.color};flex-shrink:0"></div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escHtml(p.name)}</div>
              ${(NX_USER.role === 'admin' && p.firstname) ? `<div style="font-size:11px;color:var(--gray-500);margin-bottom:2px">Créé par : <span style="color:var(--blue-300);font-weight:600">${escHtml(p.firstname + ' ' + p.lastname)}</span></div>` : ''}
              <div style="font-size:11px;color:var(--gray-500)">
                <span style="cursor:pointer; color:var(--blue-400); font-weight:600; position:relative; z-index:10;" onclick="event.stopPropagation(); showProjectMembers(${p.id})">👥 ${p.member_count} membres</span> 
                · <span style="cursor:pointer; color:var(--blue-400); font-weight:600; position:relative; z-index:10;" onclick="event.stopPropagation(); showProjectTasks(${p.id})" title="Voir les tâches du projet">📋 ${p.task_count} tâches</span>
              </div>
            </div>
            <div style="text-align:right">
              <div style="font-size:12px;font-weight:700;color:var(--blue-400)">${p.progress}%</div>
              <div style="width:60px;height:4px;background:rgba(255,255,255,.08);border-radius:4px;overflow:hidden;margin-top:4px">
                <div style="height:100%;width:${p.progress}%;background:linear-gradient(90deg,var(--blue-700),var(--blue-400));border-radius:4px"></div>
              </div>
            </div>
          </div>
          ${(NX_USER.role === 'admin') ? `
            <div style="display:flex;justify-content:center;margin-top:10px">
              <button onclick="deleteProject(${p.id}, event)" 
                      style="background:rgba(239, 68, 68, 0.1); color:#ef4444; border:1px solid rgba(239, 68, 68, 0.2); border-radius:6px; padding:4px 12px; font-size:11px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px; transition:all 0.2s;"
                      onmouseover="this.style.background='rgba(239, 68, 68, 0.2)'" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
                Supprimer le projet
              </button>
            </div>
          ` : ''}
        </div>
      `).join('');
    }
    
    // Mise à jour des badges latéraux (Sidebar)
    refreshSidebarBadges(data);

    // Tâches prioritaires
    const taskList = document.getElementById('priorityTasksList');
    taskList.innerHTML = data.priority_tasks.map(t => `
      <div style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:8px;margin-bottom:6px;background:rgba(59,130,246,.04)">
        <div style="width:8px;height:8px;border-radius:50%;background:${t.priority==='haute'?'#ef4444':t.priority==='moyenne'?'#f59e0b':'#10b981'};flex-shrink:0"></div>
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escHtml(t.title)}</div>
          <div style="font-size:11px;color:var(--gray-500)">${escHtml(t.project_name||'—')}</div>
        </div>
        <span class="badge ${statusBadge(t.status)}" style="font-size:9px">${statusLabel(t.status)}</span>
        <button onclick="toggleTaskDone(${t.id}, true)" class="btn btn-success btn-sm btn-icon" style="padding:4px 8px; font-size:10px; height:auto; width:auto;" title="Marquer comme fini">
          Effectuer
        </button>
      </div>
    `).join('');

    // Activité
    const feed = document.getElementById('activityFeed');
    feed.innerHTML = data.activities.map(a => `
      <div class="timeline-item">
        <div class="timeline-dot" style="background:rgba(59,130,246,.15)">
          <div class="avatar" style="width:38px;height:38px;font-size:12px">${escHtml(a.avatar_initials||'?')}</div>
        </div>
        <div class="timeline-content">
          <div class="timeline-title"><strong>${escHtml(a.firstname+' '+a.lastname)}</strong> ${escHtml(a.action)} <strong>${escHtml(a.entity_name||'')}</strong></div>
          <div class="timeline-time">${a.time_ago}</div>
        </div>
      </div>
    `).join('');

    // Graphique barres hebdo
    renderWeekChart(data.weekly_activity);

    // Calendrier mini
    renderMiniCalendar();

    // Notifications
    renderNotifications(data.notifications);

  } catch (e) {
    console.error('Dashboard error:', e);
    showToast('Erreur de chargement du dashboard', 'error');
  }
}

function renderWeekChart(data) {
  const maxVal = Math.max(...data.map(d => d.count), 1);
  document.getElementById('weekChart').innerHTML = data.map(d => `
    <div class="chart-bar-wrap">
      <div class="chart-bar" style="height:${Math.max(8, (d.count / maxVal) * 100)}%" title="${d.count} tâche(s)"></div>
      <span class="chart-label">${d.day}</span>
    </div>
  `).join('');
}

function renderNotifications(notifs) {
  const list = document.getElementById('notifList');
  if (!notifs.length) {
    list.innerHTML = '<div style="padding:20px;text-align:center;color:var(--gray-500);font-size:13px">Aucune notification</div>';
    return;
  }
  list.innerHTML = notifs.map(n => `
    <div class="notif-item ${!n.is_read ? 'unread' : ''}">
      ${!n.is_read ? '<div class="notif-dot"></div>' : '<div style="width:8px;flex-shrink:0"></div>'}
      <div class="notif-content">
        <p><strong>${escHtml(n.title)}</strong>${n.message ? ' – ' + escHtml(n.message) : ''}</p>
        <span>${n.time_ago}</span>
      </div>
    </div>
  `).join('');
}

// ─────────────────────────────────────────────────────
// PROJETS
// ─────────────────────────────────────────────────────

let allProjects = [];

async function loadProjects(status = 'all') {
  try {
    const res = await apiGet(API.projects, { action: 'list', status });
    allProjects = res.data;
    renderProjectsGrid(allProjects);
    populateProjectSelects(allProjects);
    const count = allProjects.length;
    document.getElementById('projects-count-label').textContent = `${count} projet${count > 1 ? 's' : ''} dans votre organisation`;
  } catch (e) {
    console.error(e);
  }
}

function renderProjectsGrid(projects) {
  const grid = document.getElementById('projectsGrid');
  if (!projects.length) {
    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--gray-500)"><div style="font-size:48px;margin-bottom:16px">📁</div><p>Aucun projet. Créez votre premier projet !</p></div>';
    return;
  }
  grid.innerHTML = projects.map(p => {
    const pct = p.progress || 0;
    return `
    <div class="col-12 col-md-6 col-xl-4">
    <div class="project-card" style="position:relative;" onclick="showPage('tasks')">
      <div style="height:3px;background:${escHtml(p.color)};border-radius:3px;margin-bottom:14px"></div>
      
      <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
        <div>
          <div style="font-size:15px;font-weight:700;margin-bottom:4px">${escHtml(p.name)}</div>
          <div style="font-size:12px;color:var(--gray-500)">${escHtml(p.description||'').substring(0,60)}${(p.description||'').length>60?'…':''}</div>
        </div>
        <span class="badge ${statusBadge(p.status)}">${statusLabel(p.status)}</span>
      </div>
      ${(NX_USER.role === 'admin' && p.firstname) ? `<div style="font-size:11px;color:var(--gray-500);margin-bottom:12px">Créé par : <span style="color:var(--blue-300);font-weight:600">${escHtml(p.firstname + ' ' + p.lastname)}</span></div>` : ''}
      <div style="margin-bottom:12px">
        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--gray-500);margin-bottom:6px">
          <span>Progression</span><span style="font-weight:700;color:var(--blue-400)">${pct}%</span>
        </div>
        <div class="progress-bar"><div class="progress-fill" style="width:${pct}%"></div></div>
      </div>
      <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;color:var(--gray-500);margin-bottom:16px">
        <span style="cursor:pointer; color:var(--blue-400); font-weight:600; position:relative; z-index:10;" onclick="event.stopPropagation(); showProjectMembers(${p.id})">👥 ${p.member_count||0} membres</span>
        <span style="cursor:pointer; color:var(--blue-400); font-weight:600; position:relative; z-index:10;" onclick="event.stopPropagation(); showProjectTasks(${p.id})" title="Voir les tâches du projet">📋 ${p.task_count||0} tâches</span>
        <span class="badge ${priorityBadge(p.priority)}">${p.priority}</span>
      </div>

      ${(NX_USER.role === 'admin' || p.owner_id == NX_USER.id) ? `
        <div style="display:flex;justify-content:center;border-top:1px solid rgba(255,255,255,0.05);padding-top:14px">
          <button onclick="deleteProject(${p.id}, event)" 
                  style="background:rgba(239, 68, 68, 0.1); color:#ef4444; border:1px solid rgba(239, 68, 68, 0.2); border-radius:8px; padding:6px 16px; font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px; transition:all 0.2s;"
                  onmouseover="this.style.background='rgba(239, 68, 68, 0.2)'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='rgba(239, 68, 68, 0.1)'; this.style.transform='translateY(0)'">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
            Supprimer le projet
          </button>
        </div>
      ` : ''}
    </div>
    </div>
    `;
  }).join('');
}


async function showProjectMembers(id) {
  let proj = allProjects.find(p => p.id == id);
  if (!proj && lastStats && lastStats.recent_projects) {
    proj = lastStats.recent_projects.find(p => p.id == id);
  }
  const name = proj ? proj.name : 'Projet';

  document.getElementById('pmProjectName').textContent = name;
  const list = document.getElementById('projectMembersList');
  const countSpan = document.getElementById('pmMembersCount');
  
  list.innerHTML = '<div class="loading-pulse"></div>';
  countSpan.textContent = 'Chargement...';
  
  openModal('projectMembersModal');
  
  try {
    const res = await apiGet(API.projects, { action: 'members', id });
    if (res.success) {
      countSpan.textContent = res.data.length + ' membre(s)';
      if (res.data.length === 0) {
        list.innerHTML = '<div style="padding:20px;text-align:center;color:var(--gray-500);font-size:13px">Aucun membre dans ce projet.</div>';
      } else {
        list.innerHTML = res.data.map(m => `
          <div style="display:flex;align-items:center;padding:10px;border-radius:8px;background:rgba(59,130,246,.05);margin-bottom:4px">
            <div class="avatar avatar-sm" style="margin-right:12px">${escHtml(m.avatar_initials||'?')}</div>
            <div style="flex:1">
              <div style="font-weight:600;font-size:14px">${escHtml(m.firstname + ' ' + m.lastname)}</div>
              <div style="font-size:12px;color:var(--gray-500)">${escHtml(m.email)}</div>
            </div>
            <div style="margin-left:10px; display:flex; align-items:center; gap:8px">
              <span class="badge badge-blue">${escHtml(m.role_label)}</span>
              ${(NX_USER.role === 'admin' || NX_USER.role === 'chef_projet') ? `<button class="btn btn-primary btn-sm" style="padding:4px 8px; font-size:11px; height:auto; margin-left:8px" onclick="openAssignTask(${id}, ${m.id}, event)">Assigner tâche</button>` : ''}
            </div>
          </div>
        `).join('');
      }
    } else {
      list.innerHTML = '<div style="padding:20px;text-align:center;color:#ef4444;font-size:13px">Erreur lors du chargement des membres.</div>';
      countSpan.textContent = '';
    }
  } catch(e) {
    list.innerHTML = '<div style="padding:20px;text-align:center;color:#ef4444;font-size:13px">Erreur de connexion.</div>';
    countSpan.textContent = '';
    console.error(e);
  }
}

async function deleteProject(id, e) {
  if (e) e.stopPropagation();
  if (!confirm('Supprimer définitivement ce projet et toutes ses données associées ?')) return;
  
  const res = await apiPost(API.projects, 'delete', { id });
  if (res.success) {
    showToast(res.message, 'success');
    pageLoaded.delete('projects');
    pageLoaded.delete('dashboard');
    if (currentPage === 'projects') loadProjects();
    else if (currentPage === 'dashboard') loadDashboard();
  } else {
    showToast(res.message, 'error');
  }
}

function filterProjects(status) {
  pageLoaded.delete('projects');
  loadProjects(status);
  document.querySelectorAll('#projectFilters .btn').forEach((b, i) => {
    b.className = i === ['all','active','planned','done','late'].indexOf(status)
      ? 'btn btn-primary btn-sm' : 'btn btn-secondary btn-sm';
    b.style.width = 'auto';
  });
}

async function createProject(e) {
  e.preventDefault();
  const teamEl = document.getElementById('projTeam');
  const data = {
    name:        document.getElementById('projName').value,
    description: document.getElementById('projDesc').value,
    priority:    document.getElementById('projPriority').value,
    start_date:  document.getElementById('projStart').value,
    end_date:    document.getElementById('projEnd').value,
    team_id:     teamEl ? teamEl.value : null
  };
  const res = await apiPost(API.projects, 'create', data);
  if (res.success) {
    closeModal('newProjectModal');
    showToast(res.message, 'success');
    pageLoaded.delete('projects');
    pageLoaded.delete('dashboard');
    if (currentPage === 'projects') loadProjects();
    e.target.reset();
  } else {
    showToast(res.message, 'error');
  }
}

// ─────────────────────────────────────────────────────
// TÂCHES
// ─────────────────────────────────────────────────────

let allTasks = [];

async function loadTasks(status = 'all') {
  try {
    const res = await apiGet(API.tasks, { action: 'list', status });
    allTasks = res.data;
    renderTasksTable(allTasks);
    const done = allTasks.filter(t => t.status === 'done').length;
    const prog = allTasks.filter(t => t.status !== 'done').length;
    document.getElementById('tasks-count-label').textContent = `${prog} en cours · ${done} terminées`;

    // Charger les projets pour le select de la modale
    const projRes = await apiGet(API.projects, { action: 'list' });
    const sel = document.getElementById('taskProject');
    sel.innerHTML = projRes.data.map(p => `<option value="${p.id}">${escHtml(p.name)}</option>`).join('');
  } catch (e) { console.error(e); }
}

function renderTasksTable(tasks) {
  const body = document.getElementById('tasksTableBody');
  if (!tasks.length) {
    body.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:40px;color:var(--gray-500)">Aucune tâche trouvée</td></tr>';
    return;
  }
  body.innerHTML = tasks.map(t => `
    <tr>
      <td>
        <input type="checkbox" ${t.status==='done'?'checked':''} onchange="toggleTaskDone(${t.id}, this.checked)"
          style="width:16px;height:16px;accent-color:var(--blue-500);cursor:pointer" />
      </td>
      <td>
        <div style="font-weight:600;font-size:13px${t.status==='done'?';text-decoration:line-through;opacity:.5':''}">${escHtml(t.title)}</div>
        ${t.description ? `<div style="font-size:11px;color:var(--gray-500)">${escHtml(t.description).substring(0,50)}…</div>` : ''}
      </td>
      <td><span style="display:flex;align-items:center;gap:6px"><div style="width:8px;height:8px;border-radius:50%;background:${escHtml(t.project_color||'#3b82f6')}"></div>${escHtml(t.project_name||'—')}</span></td>
      <td>
        ${t.firstname ? `<div style="display:flex;align-items:center;gap:8px"><div class="avatar avatar-sm">${escHtml(t.avatar_initials||'?')}</div>${escHtml(t.firstname+' '+t.lastname)}</div>` : '—'}
      </td>
      <td><span class="badge ${priorityBadge(t.priority)}">${t.priority}</span></td>
      <td>
        <select onchange="updateTaskStatus(${t.id}, this.value)"
          style="background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:6px;color:white;padding:4px 8px;font-size:12px;outline:none">
          ${['todo','in_progress','review','done'].map(s => `<option value="${s}" ${s===t.status?'selected':''}>${statusLabel(s)}</option>`).join('')}
        </select>
      </td>
      <td><span style="color:${t.is_overdue?'#ef4444':'inherit'}">${t.due_formatted}</span></td>
      <td style="display:flex;gap:4px;align-items:center;flex-wrap:wrap">
        <button onclick="openTaskComments(${t.id}, ${JSON.stringify(t.title)})" class="btn btn-secondary btn-sm btn-icon" title="Commentaires" style="padding:4px 8px;font-size:11px;height:auto;width:auto">💬</button>
        ${t.status !== 'done' ? `
          <button onclick="toggleTaskDone(${t.id}, true)" class="btn btn-success btn-sm" style="padding:4px 10px; font-size:11px; height:auto; width:auto;" title="Marquer comme fini">
            Effectuer
          </button>
        ` : ''}
        <button onclick="deleteTask(${t.id})" class="btn btn-danger btn-sm btn-icon" title="Supprimer">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
        </button>
      </td>
    </tr>
  `).join('');
}

async function updateTaskStatus(id, status) {
  const t = allTasks.find(x => x.id == id);
  const res = await apiPost(API.tasks, 'update_status', { id, status, title: t?.title });
  if (res.success) showToast('Statut mis à jour', 'success');
  else showToast(res.message, 'error');
}

async function toggleTaskDone(id, done) {
  await updateTaskStatus(id, done ? 'done' : 'todo');
  pageLoaded.delete('tasks');
  pageLoaded.delete('dashboard');
  pageLoaded.delete('projects');
  refreshSidebarBadges();
  if (currentPage === 'tasks') loadTasks();
  else if (currentPage === 'dashboard') loadDashboard();
  else if (currentPage === 'projects') loadProjects();
}

async function deleteTask(id) {
  if (!confirm('Supprimer cette tâche ?')) return;
  const res = await apiPost(API.tasks, 'delete', { id });
  if (res.success) {
    showToast(res.message, 'success');
    pageLoaded.delete('tasks');
    pageLoaded.delete('dashboard');
    pageLoaded.delete('projects');
    refreshSidebarBadges();
    if (currentPage === 'tasks') loadTasks();
    else if (currentPage === 'dashboard') loadDashboard();
    else if (currentPage === 'projects') loadProjects();
  }
}

function filterTasks(status) {
  pageLoaded.delete('tasks');
  loadTasks(status);
}

async function createTask(e) {
  e.preventDefault();
  const taskAssigneeInput = document.getElementById('taskAssignee');
  const data = {
    title:       document.getElementById('taskTitle').value,
    project_id:  document.getElementById('taskProject').value,
    priority:    document.getElementById('taskPriority').value,
    due_date:    document.getElementById('taskDue').value,
    description: document.getElementById('taskDesc').value,
    assigned_to: taskAssigneeInput ? taskAssigneeInput.value : null
  };
  const res = await apiPost(API.tasks, 'create', data);
  if (res.success) {
    closeModal('newTaskModal');
    showToast(res.message, 'success');
    pageLoaded.delete('tasks');
    pageLoaded.delete('kanban');
    pageLoaded.delete('projects');
    pageLoaded.delete('dashboard');
    refreshSidebarBadges();
    if (currentPage === 'tasks') loadTasks();
    if (currentPage === 'kanban') loadKanban();
    if (currentPage === 'projects') loadProjects();
    if (currentPage === 'dashboard') loadDashboard();
    e.target.reset();
  } else showToast(res.message, 'error');
}

// ─────────────────────────────────────────────────────
// KANBAN
// ─────────────────────────────────────────────────────

async function loadKanban() {
  try {
    const res = await apiGet(API.tasks, { action: 'kanban' });
    const board = document.getElementById('kanbanBoard');
    board.innerHTML = res.data.map(col => `
      <div class="kanban-col">
        <div class="kanban-col-header">
          <span class="kanban-col-name">${escHtml(col.label)}</span>
          <span class="kanban-count">${col.tasks.length}</span>
        </div>
        <div id="kanban-col-${col.status}">
          ${col.tasks.map(t => `
            <div class="kanban-card">
              <div class="kanban-card-title">${escHtml(t.title)}</div>
              <div class="kanban-card-meta">
                <span class="badge ${priorityBadge(t.priority)}" style="font-size:9px">${t.priority}</span>
                ${t.project_name ? `<span style="color:var(--blue-400);margin-left:auto">${escHtml(t.project_name)}</span>` : ''}
              </div>
              ${t.due_date ? `<div style="font-size:11px;color:var(--gray-500);margin-top:8px">📅 ${formatDate(t.due_date)}</div>` : ''}
              <div style="display:flex;justify-content:flex-end;margin-top:8px;gap:6px">
                ${['todo','in_progress','review','done'].filter(s => s !== t.status).map(s =>
                  `<button onclick="updateTaskStatus(${t.id},'${s}');pageLoaded.delete('kanban');setTimeout(()=>loadKanban(),300)"
                     style="font-size:10px;padding:3px 8px;border-radius:6px;background:rgba(59,130,246,.12);border:none;color:var(--blue-300);cursor:pointer">
                     → ${statusLabel(s)}</button>`
                ).join('')}
              </div>
            </div>
          `).join('')}
        </div>
      </div>
    `).join('');
  } catch (e) { console.error(e); }
}

// ─────────────────────────────────────────────────────
// GANTT
// ─────────────────────────────────────────────────────

async function loadGantt() {
  try {
    const res = await apiGet(API.tasks, { action: 'gantt' });
    const container = document.getElementById('ganttChart');
    const projects = res.data;

    if (!projects.length) {
      container.innerHTML = '<p style="color:var(--gray-500);text-align:center;padding:40px">Aucun projet avec des dates définies</p>';
      return;
    }

    // Trouver les bornes temporelles
    const allDates = projects.flatMap(p => [new Date(p.start_date), new Date(p.end_date)]);
    const minDate = new Date(Math.min(...allDates));
    const maxDate = new Date(Math.max(...allDates));
    const totalDays = Math.max(1, (maxDate - minDate) / (1000 * 60 * 60 * 24));

    container.innerHTML = `
      <div style="font-size:12px;color:var(--gray-500);margin-bottom:16px;display:flex;gap:16px;padding-left:176px">
        <span>${minDate.toLocaleDateString('fr')}</span>
        <span style="margin-left:auto">${maxDate.toLocaleDateString('fr')}</span>
      </div>
      ${projects.map(p => {
        const start = new Date(p.start_date);
        const end = new Date(p.end_date);
        const left = Math.max(0, (start - minDate) / (1000 * 60 * 60 * 24)) / totalDays * 100;
        const width = Math.max(2, (end - start) / (1000 * 60 * 60 * 24)) / totalDays * 100;
        return `
          <div class="gantt-row">
            <div class="gantt-label" title="${escHtml(p.name)}">${escHtml(p.name)}</div>
            <div class="gantt-track">
              <div class="gantt-bar" style="left:${left}%;width:${width}%;background:${escHtml(p.color)};opacity:.85">
                ${p.progress}%
              </div>
            </div>
          </div>
        `;
      }).join('')}
    `;
  } catch (e) { console.error(e); }
}

// ─────────────────────────────────────────────────────
// ÉQUIPE
// ─────────────────────────────────────────────────────

let allUsers = [];

async function loadTeam() {
  try {
    const resUsers = await apiGet(API.team, { action: 'list' });
    allUsers = resUsers.data;
    renderUserList(allUsers);
    
    const lbl = document.getElementById('team-count-label');
    if (lbl) lbl.textContent = `${allUsers.length} utilisateurs | Création et ajout : limités à l'admin`;
    
    // Charger les équipes
    const resTeams = await apiGet(API.teams, { action: 'list' });
    const teamsList = document.getElementById('teamsList');
    if (teamsList) {
        if (!resTeams.data.length) {
            teamsList.innerHTML = '<p style="color:var(--gray-500);font-size:13px;text-align:center;padding:20px;">Aucune équipe n\'existe encore.</p>';
        } else {
            teamsList.innerHTML = resTeams.data.map(t => `
            <div style="background:rgba(59,130,246,.05);border:1px solid rgba(59,130,246,.15);border-radius:10px;padding:16px;margin-bottom:12px;">
                <div style="font-weight:700;font-size:15px;margin-bottom:8px;">${escHtml(t.name)}</div>
                <div style="font-size:12px;color:var(--gray-500)">${t.members.length} membres</div>
                ${t.members.length > 0 ? `<div style="display:flex;gap:12px;margin-top:10px;flex-wrap:wrap">
                ${t.members.map(m => `
                    <div style="position:relative;">
                        <div class="avatar avatar-sm" title="${escHtml(m.firstname + ' ' + m.lastname)}" style="width:32px;height:32px;font-size:12px;cursor:help;">${escHtml(m.avatar_initials)}</div>
                        ${NX_USER.role === 'admin' ? `<div onclick="removeUserFromTeam(${t.id}, ${m.id})" style="position:absolute;top:-4px;right:-4px;width:14px;height:14px;background:var(--red-500);color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;cursor:pointer;" title="Retirer de l'équipe">×</div>` : ''}
                    </div>
                `).join('')}
                </div>` : ''}
            </div>
            `).join('');
        }
    }
  } catch (e) { console.error(e); }
}

function renderUserList(users) {
  const list = document.getElementById('teamMembersList');
  if(!list) return;
  if(!users.length) {
      list.innerHTML = '<div style="padding:20px;text-align:center;color:var(--gray-500)">Aucun utilisateur trouvé</div>';
      return;
  }
  list.innerHTML = users.map(m => `
    <div class="member-item" onclick="openUserProfile(${m.id})" style="cursor:pointer; transition:all .2s;" title="Voir le profil et ajouter">
      <div class="avatar">${escHtml(m.avatar_initials||'?')}</div>
      <div style="flex:1">
        <div style="font-size:14px;font-weight:600">${escHtml(m.firstname+' '+m.lastname)}</div>
        <div style="font-size:12px;color:var(--gray-500)">${escHtml(m.email)}</div>
      </div>
      <div style="text-align:right">
        <div style="margin-bottom:4px"><span class="badge badge-blue">${escHtml(m.role_label)}</span></div>
      </div>
    </div>
  `).join('');
}

function searchUsersForTeam(val) {
   if (!val) {
       renderUserList(allUsers);
       return;
   }
   const valLower = val.toLowerCase();
   const filtered = allUsers.filter(u => 
       u.firstname.toLowerCase().includes(valLower) ||
       u.lastname.toLowerCase().includes(valLower) ||
       u.email.toLowerCase().includes(valLower)
   );
   renderUserList(filtered);
}

async function openUserProfile(id, fromSearch = false) {
    let user = allUsers.find(u => u.id === id);
    if (!user) {
        try {
            const res = await apiGet(API.team, { action: 'list' });
            allUsers = res.data;
            user = allUsers.find(u => u.id === id);
        } catch (e) { console.error(e); }
    }
    if(!user) return;
    
    document.getElementById('upName').textContent = user.firstname + ' ' + user.lastname;
    document.getElementById('upEmail').textContent = user.email;
    document.getElementById('upRole').textContent = user.role_label || user.role;
    document.getElementById('upAvatar').textContent = user.avatar_initials || '?';
    
    const upOrg = document.getElementById('upOrg');
    if(upOrg) upOrg.textContent = user.organisation || 'Non renseigné';
    const upWhatsapp = document.getElementById('upWhatsapp');
    const upWhatsappRow = document.getElementById('upWhatsappRow');
    if(upWhatsapp) {
        upWhatsapp.textContent = user.whatsapp || '';
        if (upWhatsappRow) {
            upWhatsappRow.style.display = user.whatsapp ? 'block' : 'none';
        }
    }
    const upGmail = document.getElementById('upGmail');
    const upGmailRow = document.getElementById('upGmailRow');
    if(upGmail) {
        upGmail.textContent = user.gmail || '';
        if (upGmailRow) {
            upGmailRow.style.display = user.gmail ? 'block' : 'none';
        }
    }
    
    const upUserId = document.getElementById('upUserId');
    if(upUserId) upUserId.value = user.id;
    
    const select = document.getElementById('upTeamSelect');
    if(select && NX_USER.role === 'admin') {
        apiGet(API.teams, { action: 'list' }).then(res => {
            if(res.data.length === 0) {
                select.innerHTML = '<option value="">Aucune équipe disponible</option>';
            } else {
                select.innerHTML = res.data.map(t => `<option value="${t.id}">${escHtml(t.name)}</option>`).join('');
            }
        });
    }
    
    const teamSection = document.getElementById('upTeamSection');
    if (teamSection) {
        teamSection.style.display = fromSearch ? 'none' : 'block';
    }
    
    openModal('userProfileModal');
}

async function createTeam() {
    const name = document.getElementById('newTeamName').value;
    if(!name) { showToast('Nom de l\'équipe requis', 'error'); return; }
    
    const res = await apiPost(API.teams, 'create', { name });
    if (res.success) {
        closeModal('newTeamModal');
        showToast(res.message, 'success');
        document.getElementById('newTeamName').value = '';
        pageLoaded.delete('team');
        loadTeam();
    } else {
        showToast(res.message, 'error');
    }
}

async function addUserToTeam() {
    const team_id = document.getElementById('upTeamSelect').value;
    const user_id = document.getElementById('upUserId').value;
    if(!team_id) { showToast('Veuillez sélectionner ou créer une équipe avant.', 'error'); return; }
    
    const res = await apiPost(API.teams, 'add_member', { team_id, user_id });
    if(res.success) {
        closeModal('userProfileModal');
        showToast(res.message, 'success');
        pageLoaded.delete('team');
        loadTeam();
    } else {
        showToast(res.message, 'error');
    }
}

async function removeUserFromTeam(team_id, user_id) {
    if(!confirm("Êtes-vous sûr de vouloir retirer cet utilisateur de l'équipe ?")) return;
    const res = await apiPost(API.teams, 'remove_member', { team_id, user_id });
    if(res.success) {
        showToast(res.message, 'success');
        pageLoaded.delete('team');
        loadTeam();
    } else {
        showToast(res.message, 'error');
    }
}

// ─────────────────────────────────────────────────────
// INTÉGRATIONS
// ─────────────────────────────────────────────────────

let _integrationsData = [];

async function loadIntegrations() {
  try {
    const res = await apiGet(API.integrations, { action: 'list' });
    _integrationsData = res.data;
    renderIntegrationsGrid('all');

    // Hub stats
    const hubRes = await apiGet(API.integrations, { action: 'hub_stats' });
    document.getElementById('hubStats').innerHTML = `
      <div style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-radius:12px;padding:16px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:#6ee7b7">${hubRes.data.connected}</div>
        <div style="font-size:12px;color:var(--gray-500)">Intégrations connectées</div>
      </div>
      <div style="background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.2);border-radius:12px;padding:16px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:var(--blue-300)">${hubRes.data.events_day}</div>
        <div style="font-size:12px;color:var(--gray-500)">Événements synchronisés/jour</div>
      </div>
      <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:12px;padding:16px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:#fcd34d">${hubRes.data.uptime}</div>
        <div style="font-size:12px;color:var(--gray-500)">Disponibilité</div>
      </div>
      <div style="background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.2);border-radius:12px;padding:16px;text-align:center">
        <div style="font-size:28px;font-weight:800;color:var(--blue-300)">${hubRes.data.latency}</div>
        <div style="font-size:12px;color:var(--gray-500)">Latence moyenne</div>
      </div>
    `;
  } catch (e) { console.error(e); }
}

function renderIntegrationsGrid(category) {
  const grid = document.getElementById('integrationsGrid');
  const filtered = category === 'all' ? _integrationsData : _integrationsData.filter(i => i.category === category);

  // Update filter button styles
  document.querySelectorAll('.integration-filter-btn').forEach(btn => {
    btn.classList.remove('btn-primary');
    btn.classList.add('btn-secondary');
  });
  const activeBtn = document.getElementById('filter-' + category);
  if (activeBtn) { activeBtn.classList.add('btn-primary'); activeBtn.classList.remove('btn-secondary'); }

  if (!filtered.length) {
    grid.innerHTML = '<div style="text-align:center;padding:60px;color:var(--gray-500);width:100%">Aucune intégration dans cette catégorie.</div>';
    return;
  }

  grid.innerHTML = filtered.map(int => `
    <div class="col-12 col-sm-6 col-md-4 col-xl-3">
      <div class="integration-card ${int.is_connected ? 'connected' : ''}" style="border-top:3px solid ${int.color || '#3b82f6'}">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
          <div class="integration-logo" style="background:${int.color}22;border-radius:12px;width:48px;height:48px;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0">${int.icon}</div>
          <div>
            <div class="integration-name" style="font-weight:700;font-size:15px">${escHtml(int.name)}</div>
            <div style="font-size:11px;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px">${escHtml(int.category)}</div>
          </div>
        </div>
        <div class="integration-desc" style="font-size:12px;color:var(--gray-400);margin-bottom:14px;min-height:36px">${escHtml(int.desc)}</div>
        ${int.is_connected ? `
          <div style="font-size:11px;color:#6ee7b7;margin-bottom:10px;display:flex;align-items:center;gap:6px">
            <span style="width:8px;height:8px;border-radius:50%;background:#10b981;display:inline-block"></span>
            Connecté${int.credential ? ' · ' + escHtml(int.credential) : ''}
          </div>
          <div style="display:flex;gap:8px">
            <button class="btn btn-sm btn-secondary" style="flex:1" onclick="openConnectModal('${int.service}','${escHtml(int.name)}','${escHtml(int.credential_label||'')}','${escHtml(int.credential_placeholder||'')}',true)">✏️ Modifier</button>
            <button class="btn btn-sm btn-danger" style="flex:1" onclick="disconnectIntegration('${int.service}','${escHtml(int.name)}')">Déconnecter</button>
          </div>
        ` : `
          <button class="btn btn-sm btn-primary" style="width:100%" onclick="openConnectModal('${int.service}','${escHtml(int.name)}','${escHtml(int.credential_label||'')}','${escHtml(int.credential_placeholder||'')}',false)">
            🔗 Connecter
          </button>
        `}
      </div>
    </div>
  `).join('');
}

function filterIntegrations(category) {
  renderIntegrationsGrid(category);
}

function openConnectModal(service, name, credLabel, credPlaceholder, isEdit) {
  document.getElementById('connectModalTitle').textContent = (isEdit ? '✏️ Modifier ' : '🔗 Connecter ') + name;
  document.getElementById('connectModalService').value = service;
  document.getElementById('connectModalName').value = name;
  document.getElementById('connectCredLabel').textContent = credLabel || 'Identifiant de connexion';
  document.getElementById('connectCredInput').placeholder = credPlaceholder || '';
  document.getElementById('connectCredInput').value = '';

  // Show help text specific to service
  const helps = {
    gmail:    'Saisissez votre adresse Gmail. Vous recevrez les notifications NexaFlow dans votre boîte mail.',
    whatsapp: 'Saisissez votre numéro WhatsApp avec l\'indicatif pays (ex: +237 6XX XXX XXX). Un message de bienvenue vous sera envoyé.',
    discord:  'Créez un webhook dans votre serveur Discord : Paramètres du serveur → Intégrations → Webhooks.',
    slack:    'Créez un webhook entrant dans votre espace Slack : api.slack.com → Incoming Webhooks.',
    github:   'Générez un token sur github.com → Settings → Developer settings → Personal access tokens.',
    gitlab:   'Générez un token sur gitlab.com → Préférences → Access Tokens.',
    figma:    'Trouvez votre token sur figma.com → Paramètres du compte → Personal Access Tokens.',
    notion:   'Créez une intégration sur notion.so/my-integrations pour obtenir votre token.',
  };
  document.getElementById('connectModalHelp').textContent = helps[service] || '';

  openModal('connectIntegrationModal');
}

async function submitConnectIntegration() {
  const service    = document.getElementById('connectModalService').value;
  const name       = document.getElementById('connectModalName').value;
  const credential = document.getElementById('connectCredInput').value.trim();

  if (!credential) {
    showToast('Veuillez renseigner le champ requis.', 'error');
    return;
  }

  const btn = document.getElementById('connectModalSubmitBtn');
  btn.disabled = true;
  btn.textContent = 'Connexion...';

  const res = await apiPost(API.integrations, 'toggle', { service, name, credential });
  btn.disabled = false;
  btn.textContent = 'Connecter';

  if (res.success) {
    showToast(res.message, 'success');
    closeModal('connectIntegrationModal');
    pageLoaded.delete('integrations');
    loadIntegrations();
    if (service === 'whatsapp' || service === 'gmail') {
      NX_USER[service] = credential;
      pageLoaded.delete('team');
      loadTeam();
    }
  } else {
    showToast(res.message || 'Erreur lors de la connexion.', 'error');
  }
}

async function disconnectIntegration(service, name) {
  if (!confirm(`Déconnecter ${name} ?`)) return;
  const res = await apiPost(API.integrations, 'toggle', { service, name, credential: '' });
  if (res.success) {
    showToast(res.message, 'success');
    pageLoaded.delete('integrations');
    loadIntegrations();
    if (service === 'whatsapp' || service === 'gmail') {
      NX_USER[service] = null;
      pageLoaded.delete('team');
      loadTeam();
    }
  }
}

async function toggleIntegration(service, name) {
  const res = await apiPost(API.integrations, 'toggle', { service, name });
  if (res.success) {
    showToast(res.message, 'success');
    pageLoaded.delete('integrations');
    loadIntegrations();
    if (service === 'whatsapp' || service === 'gmail') {
      pageLoaded.delete('team');
      loadTeam().then(() => {
        const me = allUsers.find(u => u.id === NX_USER.id);
        if (me) {
          NX_USER.whatsapp = me.whatsapp;
          NX_USER.gmail = me.gmail;
        }
      });
    }
  }
}



// ─────────────────────────────────────────────────────
// AUTOMATISATIONS
// ─────────────────────────────────────────────────────

async function loadAutomations() {
  try {
    const res = await apiGet(API.automations, { action: 'list' });
    const list = document.getElementById('automationsList');
    if (!res.data.length) {
      list.innerHTML = '<div style="text-align:center;padding:60px;color:var(--gray-500)"><div style="font-size:48px;margin-bottom:16px">⚡</div><p>Aucune automatisation créée</p></div>';
      return;
    }
    list.innerHTML = res.data.map(a => `
      <div class="card" style="display:flex;align-items:center;gap:20px">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(59,130,246,.15);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0">⚡</div>
        <div style="flex:1">
          <div style="font-size:15px;font-weight:700;margin-bottom:4px">${escHtml(a.name)}</div>
          <div style="font-size:12px;color:var(--gray-500)">SI: <strong>${escHtml(a.trigger_label)}</strong> → ALORS: <strong>${escHtml(a.action_label)}</strong></div>
          <div style="font-size:11px;color:var(--gray-600);margin-top:4px">Exécutée ${a.runs_count} fois · Créée le ${a.created_formatted}</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px">
          <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
            <div onclick="toggleAutomation(${a.id})" style="width:40px;height:22px;border-radius:11px;background:${a.is_active?'var(--blue-600)':'rgba(255,255,255,.1)'};position:relative;cursor:pointer;transition:all .3s">
              <div style="position:absolute;top:2px;${a.is_active?'right:2px':'left:2px'};width:18px;height:18px;border-radius:50%;background:white;transition:all .3s"></div>
            </div>
            <span style="font-size:12px;color:${a.is_active?'var(--blue-400)':'var(--gray-500)'}">${a.is_active?'Actif':'Inactif'}</span>
          </label>
          <button onclick="deleteAutomation(${a.id})" class="btn btn-danger btn-sm btn-icon">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
          </button>
        </div>
      </div>
    `).join('');
  } catch (e) { console.error(e); }
}

async function toggleAutomation(id) {
  const res = await apiPost(API.automations, 'toggle', { id });
  if (res.success) { pageLoaded.delete('automations'); loadAutomations(); }
}

async function deleteAutomation(id) {
  if (!confirm('Supprimer cette automatisation ?')) return;
  const res = await apiPost(API.automations, 'delete', { id });
  if (res.success) { showToast(res.message, 'success'); pageLoaded.delete('automations'); loadAutomations(); }
}

async function createAutomation() {
  const name   = document.getElementById('autoName').value;
  const trigger= document.getElementById('autoTrigger').value;
  const action = document.getElementById('autoAction').value;
  if (!name) { showToast('Nom requis', 'error'); return; }
  const res = await apiPost(API.automations, 'create', { name, trigger, action });
  if (res.success) {
    closeModal('newAutoModal');
    showToast(res.message, 'success');
    pageLoaded.delete('automations');
    loadAutomations();
    document.getElementById('autoName').value = '';
  }
}

// ─────────────────────────────────────────────────────
// ANALYTICS
// ─────────────────────────────────────────────────────

async function loadAnalytics() {
  try {
    const data = await apiGet(API.stats);
    const total = (data.tasks_done || 0) + (data.tasks_progress || 0);
    const completionRate = total > 0 ? Math.round((data.tasks_done / total) * 100) : 0;
    const onTimeRate = data.tasks_with_due > 0 ? Math.round((data.tasks_on_time / data.tasks_with_due) * 100) : 100;
    const avgDays = data.tasks_avg_days > 0 ? data.tasks_avg_days + 'j' : '—';
    document.getElementById('analyticsStats').innerHTML = `
      <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon blue">📈</div><div class="stat-value">${completionRate}%</div><div class="stat-label">Taux de complétion</div><div class="stat-change up">${data.tasks_done} tâches terminées</div></div></div>
      <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon green">⚡</div><div class="stat-value">${avgDays}</div><div class="stat-label">Temps moyen/tâche</div><div class="stat-change up">Durée moyenne réelle</div></div></div>
      <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon yellow">🎯</div><div class="stat-value">${onTimeRate}%</div><div class="stat-label">Dans les délais</div><div class="stat-change ${data.tasks_late > 0 ? 'down' : 'up'}">${data.tasks_late} en retard</div></div></div>
      <div class="col-12 col-sm-6 col-lg-3"><div class="stat-card"><div class="stat-icon purple">💬</div><div class="stat-value">${data.messages_count || 0}</div><div class="stat-label">Messages échangés</div><div class="stat-change up">Total</div></div></div>
    `;

    // Graphique mensuel (données simulées)
    const months = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
    const vals   = [12,18,24,20,28,32,25,30,35,28,38,data.tasks_done];
    const max    = Math.max(...vals, 1);
    document.getElementById('monthChart').innerHTML = months.map((m, i) => `
      <div class="chart-bar-wrap">
        <div class="chart-bar" style="height:${(vals[i]/max)*100}%" title="${vals[i]}"></div>
        <span class="chart-label">${m}</span>
      </div>
    `).join('');

    // Top contributeurs
    const teamRes = await apiGet(API.team, { action: 'list' });
    document.getElementById('topContributors').innerHTML = teamRes.data
      .sort((a,b) => b.tasks_count - a.tasks_count)
      .slice(0, 5)
      .map((m, i) => `
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
          <div style="font-size:16px;font-weight:700;color:var(--gray-600);width:20px">#${i+1}</div>
          <div class="avatar">${escHtml(m.avatar_initials||'?')}</div>
          <div style="flex:1">
            <div style="font-size:13px;font-weight:600">${escHtml(m.firstname+' '+m.lastname)}</div>
            <div style="height:4px;background:rgba(255,255,255,.08);border-radius:4px;margin-top:6px;overflow:hidden">
              <div style="height:100%;width:${Math.min(100,(m.tasks_count/(teamRes.data[0]?.tasks_count||1))*100)}%;background:linear-gradient(90deg,var(--blue-700),var(--blue-400));border-radius:4px"></div>
            </div>
          </div>
          <div style="font-size:13px;font-weight:700;color:var(--blue-400)">${m.tasks_count} tâches</div>
        </div>
      `).join('');
  } catch (e) { console.error(e); }
}

// ─────────────────────────────────────────────────────
// NOTIFICATIONS
// ─────────────────────────────────────────────────────

async function markAllRead() {
  await apiPost(API.notifications, 'mark_read', {});
  showToast('Toutes les notifications ont été lues', 'success');
  document.querySelectorAll('.notif-item').forEach(n => n.classList.remove('unread'));
  const badge = document.querySelector('.topbar-badge');
  if (badge) badge.style.display = 'none';
}

function togglePanel(id) {
  const panel = document.getElementById(id);
  if (!panel) return;
  const isOpen = panel.classList.contains('open');
  closeAllPanels();
  if (!isOpen) {
    panel.classList.add('open');
    if (id === 'notifPanel') {
      apiPost(API.notifications, 'mark_read', {}).then(() => {
        document.querySelectorAll('.notif-item.unread').forEach(n => {
           n.classList.remove('unread');
           const dot = n.querySelector('.notif-dot');
           if (dot) {
               const placeholder = document.createElement('div');
               placeholder.style.width = '8px';
               placeholder.style.flexShrink = '0';
               dot.parentNode.replaceChild(placeholder, dot);
           }
        });
        const notifBtn = document.getElementById('notifBtn');
        if (notifBtn) {
            const badge = notifBtn.querySelector('.topbar-badge');
            if (badge) badge.style.display = 'none';
        }
      }).catch(e => console.error(e));
    }
  }
}

function closeAllPanels() {
  document.querySelectorAll('.notif-panel').forEach(p => p.classList.remove('open'));
}

// ─────────────────────────────────────────────────────
// RECHERCHE GLOBALE
// ─────────────────────────────────────────────────────

// ─────────────────────────────────────────────────────
// UI HELPERS
// ─────────────────────────────────────────────────────

function openModal(id)  { document.getElementById(id).classList.add('open'); }

async function openNewProjectModal() {
  const select = document.getElementById('projTeam');
  if (select) {
    select.innerHTML = '<option value="">Chargement...</option>';
    try {
      const res = await apiGet(API.teams, { action: 'list' });
      if (res.data && res.data.length > 0) {
        select.innerHTML = '<option value="">Aucune</option>' + res.data.map(t => `<option value="${t.id}">${escHtml(t.name)}</option>`).join('');
      } else {
        select.innerHTML = '<option value="">Aucune équipe disponible</option>';
      }
    } catch (e) {
      console.error(e);
      select.innerHTML = '<option value="">Erreur de chargement</option>';
    }
  }
  openModal('newProjectModal');
}

async function openNewTaskModal() {
  const select = document.getElementById('taskProject');
  select.innerHTML = '<option value="">Chargement...</option>';
  
  const taskAssigneeSelect = document.getElementById('taskAssignee');
  if (taskAssigneeSelect) {
    taskAssigneeSelect.innerHTML = '<option value="">Sélectionnez un projet d\'abord...</option>';
  }
  
  openModal('newTaskModal');
  
  try {
    const res = await apiGet(API.projects, { action: 'list' });
    allProjects = res.data;
    populateProjectSelects(allProjects);
    
    if (allProjects.length > 0) {
      await updateAssigneeOptions(allProjects[0].id);
    }
  } catch (e) {
    console.error(e);
    select.innerHTML = '<option value="">Erreur de chargement</option>';
  }
}

async function openAssignTask(projectId, userId, e) {
  if (e) e.stopPropagation();
  closeModal('projectMembersModal');
  
  const select = document.getElementById('taskProject');
  select.innerHTML = '<option value="">Chargement...</option>';
  
  const taskAssigneeSelect = document.getElementById('taskAssignee');
  if (taskAssigneeSelect) {
    taskAssigneeSelect.innerHTML = '<option value="">Chargement...</option>';
  }
  
  openModal('newTaskModal');
  
  try {
    const res = await apiGet(API.projects, { action: 'list' });
    allProjects = res.data;
    populateProjectSelects(allProjects);
    document.getElementById('taskProject').value = projectId;
    
    await updateAssigneeOptions(projectId, userId);
  } catch (err) {
    console.error(err);
    select.innerHTML = '<option value="">Erreur de chargement</option>';
  }
}

async function showProjectTasks(projectId, e) {
  if (e) e.stopPropagation();
  
  let proj = allProjects.find(p => p.id == projectId);
  if (!proj && lastStats && lastStats.recent_projects) {
    proj = lastStats.recent_projects.find(p => p.id == projectId);
  }
  const name = proj ? proj.name : 'Projet';

  document.getElementById('ptProjectName').textContent = name;
  const list = document.getElementById('projectTasksList');
  const countSpan = document.getElementById('ptTasksCount');
  
  list.innerHTML = '<div class="loading-pulse"></div>';
  countSpan.textContent = 'Chargement...';
  
  openModal('projectTasksModal');
  
  try {
    const res = await apiGet(API.tasks, { action: 'list', project_id: projectId });
    if (res.success) {
      const tasks = res.data;
      countSpan.textContent = tasks.length + ' tâche(s)';
      if (tasks.length === 0) {
        list.innerHTML = '<div style="padding:30px;text-align:center;color:var(--gray-500);font-size:13px"><div style="font-size:32px;margin-bottom:8px">📭</div>Aucune tâche dans ce projet.</div>';
      } else {
        list.innerHTML = tasks.map(t => {
          const sLabel = statusLabel(t.status);
          const sBadge = statusBadge(t.status);
          const pBadge = priorityBadge(t.priority);
          const assignee = t.firstname ? (t.firstname + ' ' + t.lastname) : 'Non assigné';
          const initials = t.avatar_initials || '?';
          const overdue = t.is_overdue ? 'color:#ef4444;font-weight:600' : '';
          return `
            <div style="display:flex;align-items:center;padding:12px;border-radius:10px;background:rgba(59,130,246,.05);margin-bottom:6px;gap:12px;border-left:3px solid ${t.status === 'done' ? 'var(--green-400,#22c55e)' : t.status === 'in_progress' ? 'var(--blue-400,#3b82f6)' : t.status === 'review' ? 'var(--yellow-400,#eab308)' : 'var(--gray-400,#9ca3af)'}">
              <div class="avatar avatar-sm" style="flex-shrink:0">${escHtml(initials)}</div>
              <div style="flex:1;min-width:0">
                <div style="font-weight:600;font-size:13px;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escHtml(t.title)}</div>
                <div style="font-size:11px;color:var(--gray-500);display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                  <span>👤 ${escHtml(assignee)}</span>
                  <span style="${overdue}">📅 ${t.due_formatted}</span>
                </div>
              </div>
              <div style="display:flex;gap:6px;align-items:center;flex-shrink:0">
                <span class="badge ${sBadge}" style="font-size:10px">${sLabel}</span>
                <span class="badge ${pBadge}" style="font-size:10px">${t.priority}</span>
              </div>
            </div>`;
        }).join('');
      }
    } else {
      list.innerHTML = '<div style="padding:20px;text-align:center;color:#ef4444;font-size:13px">Erreur lors du chargement des tâches.</div>';
      countSpan.textContent = '';
    }
  } catch(err) {
    list.innerHTML = '<div style="padding:20px;text-align:center;color:#ef4444;font-size:13px">Erreur de connexion.</div>';
    countSpan.textContent = '';
    console.error(err);
  }
}

async function updateAssigneeOptions(projectId, selectedUserId = null) {
  const taskAssigneeSelect = document.getElementById('taskAssignee');
  if (!taskAssigneeSelect) return;
  
  if (!projectId) {
    taskAssigneeSelect.innerHTML = '<option value="">Sélectionnez un projet...</option>';
    return;
  }
  
  try {
    const res = await apiGet(API.projects, { action: 'members', id: projectId });
    if (res.success && res.data) {
      if (res.data.length === 0) {
        taskAssigneeSelect.innerHTML = '<option value="">Aucun membre dans ce projet</option>';
      } else {
        taskAssigneeSelect.innerHTML = '<option value="">Non assigné</option>' + res.data.map(m => 
          `<option value="${m.id}" ${selectedUserId && selectedUserId == m.id ? 'selected' : ''}>${escHtml(m.firstname + ' ' + m.lastname)} (${escHtml(m.role_label)})</option>`
        ).join('');
      }
    } else {
      taskAssigneeSelect.innerHTML = '<option value="">Erreur lors du chargement des membres</option>';
    }
  } catch (err) {
    console.error(err);
    taskAssigneeSelect.innerHTML = '<option value="">Erreur de chargement</option>';
  }
}

function closeModal(id) { document.getElementById(id).classList.remove('open'); }

function toggleDropdown(id) {
  const menu = document.getElementById(id);
  if (!menu) return;
  const isOpen = menu.classList.contains('open');
  closeAllDropdowns();
  if (!isOpen) menu.classList.add('open');
}

function closeAllDropdowns() {
  document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('open'));
}

function toggleSidebar() {
  if (window.innerWidth <= 768) {
    // On mobile, open the sidebar as overlay
    openSidebar();
  } else {
    // Desktop behavior: toggle collapsed class
    document.getElementById('sidebar').classList.toggle('collapsed');
  }
}

function openSidebar() {
  document.getElementById('sidebar').classList.add('mobile-open');
  const overlay = document.getElementById('sidebarOverlay');
  if (overlay) overlay.classList.add('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('mobile-open');
  const overlay = document.getElementById('sidebarOverlay');
  if (overlay) overlay.classList.remove('show');
}

function showToast(msg, type = 'info') {
  const t = document.getElementById('toast');
  const icons = { success: '✅', error: '❌', info: 'ℹ️', warning: '⚠️' };
  t.className = `toast ${type}`;
  t.innerHTML = `<span>${icons[type]||'ℹ️'}</span> ${escHtml(msg)}`;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3500);
}

function animateCounter(id, target) {
  const el = document.getElementById(id);
  if (!el) return;
  let current = 0;
  const increment = Math.ceil(target / 30);
  const timer = setInterval(() => {
    current = Math.min(current + increment, target);
    el.textContent = current;
    if (current >= target) clearInterval(timer);
  }, 40);
}

function escHtml(str) {
  if (str === null || str === undefined) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function formatDate(d) {
  if (!d) return '—';
  return new Date(d).toLocaleDateString('fr-FR');
}

function statusLabel(s) {
  return { active:'En cours', planned:'Planifié', done:'Terminé', late:'En retard', todo:'À faire', in_progress:'En cours', review:'En révision' }[s] || s;
}

function statusBadge(s) {
  return { active:'badge-blue', planned:'badge-gray', done:'badge-success', late:'badge-danger', todo:'badge-gray', in_progress:'badge-blue', review:'badge-warning' }[s] || 'badge-gray';
}

function priorityBadge(s) {
  return { haute:'badge-danger', moyenne:'badge-warning', basse:'badge-success' }[s] || 'badge-gray';
}

let searchTimeout = null;
async function handleSearch(q) {
  const resultsDiv = document.getElementById('searchResults');
  if (!q || q.length < 2) {
    resultsDiv.classList.remove('open');
    return;
  }

  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(async () => {
    try {
      const res = await apiGet(API.search, { q });
      if (res.success) {
        renderSearchResults(res.data);
      }
    } catch (e) { console.error(e); }
  }, 300);
}

function renderSearchResults(data) {
  const resultsDiv = document.getElementById('searchResults');
  const hasResults = data.projects.length || data.tasks.length || data.users.length;

  if (!hasResults) {
    resultsDiv.innerHTML = '<div class="search-no-results">Aucun résultat trouvé pour votre recherche.</div>';
  } else {
    let html = '';

    if (data.projects.length) {
      html += `<div class="search-group"><div class="search-group-title">Projets</div>`;
      html += data.projects.map(p => `
        <div class="search-item" onclick="viewSearchResult('projects', ${p.id})">
          <div class="search-item-icon" style="background:rgba(59,130,246,0.1);color:${p.color}">📁</div>
          <div class="search-item-info">
            <div class="search-item-title">${escHtml(p.name)}</div>
            <div class="search-item-sub">${statusLabel(p.status)}</div>
          </div>
        </div>
      `).join('');
      html += `</div>`;
    }

    if (data.tasks.length) {
      html += `<div class="search-group"><div class="search-group-title">Tâches</div>`;
      html += data.tasks.map(t => `
        <div class="search-item" onclick="viewSearchResult('tasks', ${t.id})">
          <div class="search-item-icon">✏️</div>
          <div class="search-item-info">
            <div class="search-item-title">${escHtml(t.title)}</div>
            <div class="search-item-sub">Statut: ${statusLabel(t.status)}</div>
          </div>
        </div>
      `).join('');
      html += `</div>`;
    }

    if (data.users.length) {
      html += `<div class="search-group"><div class="search-group-title">Membres</div>`;
      html += data.users.map(u => `
        <div class="search-item" onclick="viewSearchResult('team', ${u.id})">
          <div class="search-item-icon" style="background:var(--blue-600);color:white;font-size:10px">${u.avatar_initials}</div>
          <div class="search-item-info">
            <div class="search-item-title">${escHtml(u.firstname + ' ' + u.lastname)}</div>
            <div class="search-item-sub">${u.role} · ${u.email}</div>
          </div>
        </div>
      `).join('');
      html += `</div>`;
    }

    resultsDiv.innerHTML = html;
  }

  resultsDiv.classList.add('open');
}

function viewSearchResult(page, id) {
  document.getElementById('searchResults').classList.remove('open');
  document.getElementById('globalSearch').value = '';
  showPage(page);
  if (page === 'team') {
     setTimeout(() => {
        openUserProfile(id, true);
     }, 350);
  }
}

// Fermer les dropdowns et modales en cliquant en dehors
document.addEventListener('click', (e) => {
  if (!e.target.closest('.dropdown')) closeAllDropdowns();
  if (!e.target.closest('.notif-panel') && !e.target.closest('#notifBtn')) closeAllPanels();
  if (e.target.classList.contains('modal-overlay')) {
    document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
  }
  if (!e.target.closest('.topbar-search')) {
    document.getElementById('searchResults')?.classList.remove('open');
  }
});


let lastStats = null;
async function refreshSidebarBadges(providedData = null) {
  try {
    const data = providedData || await apiGet(API.stats);
    lastStats = data;
    const badgeProj = document.getElementById('badge-projects');
    if (badgeProj) {
      const lastSeen = parseInt(localStorage.getItem('seen-projects') || 0);
      if (data.projects_active > lastSeen) {
        badgeProj.textContent = data.projects_active;
        badgeProj.style.display = 'inline-flex';
      } else {
        badgeProj.style.display = 'none';
      }
    }
    
    const badgeTask = document.getElementById('badge-tasks');
    if (badgeTask) {
      const lastSeen = parseInt(localStorage.getItem('seen-tasks') || 0);
      if (data.tasks_progress > lastSeen) {
        badgeTask.textContent = data.tasks_progress;
        badgeTask.style.display = 'inline-flex';
      } else {
        badgeTask.style.display = 'none';
      }
    }
    
    const badgeMsg = document.getElementById('badge-messages');
    if (badgeMsg) {
      const lastSeen = parseInt(localStorage.getItem('seen-messages') || 0);
      if (data.messages_count > lastSeen) {
        badgeMsg.textContent = data.messages_count;
        badgeMsg.style.display = 'inline-flex';
      } else {
        badgeMsg.style.display = 'none';
      }
    }

    // Mise à jour du badge de notification (Topbar)
    const notifBtn = document.getElementById('notifBtn');
    if (notifBtn) {
      let notifBadge = notifBtn.querySelector('.topbar-badge');
      const unreadCount = data.notifications ? data.notifications.filter(n => !n.is_read).length : 0;
      
      if (unreadCount > 0) {
        if (!notifBadge) {
          notifBadge = document.createElement('span');
          notifBadge.className = 'topbar-badge';
          notifBtn.appendChild(notifBadge);
        }
        notifBadge.style.display = 'block';
        notifBadge.textContent = unreadCount > 9 ? '9+' : unreadCount;
      } else if (notifBadge) {
        notifBadge.style.display = 'none';
      }
    }

    // Toujours rendre les notifications pour que le panel soit rempli peu importe la page
    if (data.notifications && typeof renderNotifications === 'function') {
      renderNotifications(data.notifications);
    }

    // Si on est déjà sur une page, on marque comme vu pour éviter que le badge reste
    if (currentPage === 'projects') localStorage.setItem('seen-projects', data.projects_active);
    if (currentPage === 'tasks')    localStorage.setItem('seen-tasks', data.tasks_progress);
    if (currentPage === 'messages') localStorage.setItem('seen-messages', data.messages_count);

  } catch (e) {
    console.error("Erreur badges:", e);
  }
}

// ─────────────────────────────────────────────────────
// INITIALISATION
// ─────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', async () => {
  await refreshSidebarBadges();

  if (NX_USER.role === 'admin') {
    showPage('dashboard');
  } else {
    showPage('projects');
  }

  // Polling notifications toutes les 45 secondes
  setInterval(async () => {
    try {
      const data = await apiGet(API.stats);
      await refreshSidebarBadges(data);
      const unread = data.notifications ? data.notifications.filter(n => !n.is_read).length : 0;
      if (unread > 0 && document.visibilityState === 'visible') {
        const existing = document.querySelector('.notif-poll-toast');
        if (!existing) showToast(`${unread} notification${unread > 1 ? 's' : ''} non lue${unread > 1 ? 's' : ''}`, 'info');
      }
    } catch (e) { /* silencieux */ }
  }, 45000);
});
function populateProjectSelects(projects) {
  const select = document.getElementById('taskProject');
  if (!select) return;
  const currentVal = select.value;
  select.innerHTML = '<option value="">Aucun projet</option>' + projects.map(p => `
    <option value="${p.id}">${escHtml(p.name)}</option>
  `).join('');
  if (currentVal) select.value = currentVal;
}

// ─────────────────────────────────────────────────────
// COMMENTAIRES DE TÂCHE
// ─────────────────────────────────────────────────────

let _activeTaskId = null;

async function openTaskComments(taskId, taskTitle) {
  _activeTaskId = taskId;
  const modal = document.getElementById('taskCommentsModal');
  if (!modal) return;
  document.getElementById('taskCommentTitle').textContent = taskTitle || 'Commentaires';
  document.getElementById('commentsContainer').innerHTML = '<div style="color:var(--gray-500);font-size:13px;padding:20px 0;text-align:center">Chargement…</div>';
  modal.classList.add('open');
  await loadComments(taskId);
}

async function loadComments(taskId) {
  try {
    const res = await apiGet(API.comments, { action: 'list', task_id: taskId });
    renderComments(res.data || []);
  } catch (e) { console.error(e); }
}

function renderComments(comments) {
  const el = document.getElementById('commentsContainer');
  if (!el) return;
  if (!comments.length) {
    el.innerHTML = '<div style="color:var(--gray-500);font-size:13px;padding:20px 0;text-align:center">Aucun commentaire. Soyez le premier !</div>';
    return;
  }
  el.innerHTML = comments.map(c => `
    <div style="display:flex;gap:10px;margin-bottom:14px;align-items:flex-start">
      <div class="avatar" style="flex-shrink:0;width:32px;height:32px;font-size:12px">${escHtml(c.avatar_initials || '?')}</div>
      <div style="flex:1">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
          <span style="font-size:13px;font-weight:700">${escHtml(c.firstname + ' ' + c.lastname)}</span>
          <span style="font-size:11px;color:var(--gray-500)">${escHtml(c.time_ago)}</span>
          ${c.is_mine ? `<button onclick="deleteComment(${c.id})" style="margin-left:auto;background:none;border:none;color:var(--gray-600);cursor:pointer;font-size:12px;padding:2px 6px;border-radius:4px" title="Supprimer">✕</button>` : ''}
        </div>
        <div style="background:rgba(13,31,60,.6);border:1px solid rgba(59,130,246,.1);border-radius:10px;padding:10px 14px;font-size:13px;line-height:1.5">${escHtml(c.content)}</div>
      </div>
    </div>
  `).join('');
}

async function submitComment() {
  const input = document.getElementById('commentInput');
  const content = input?.value?.trim();
  if (!content || !_activeTaskId) return;
  input.value = '';
  try {
    const res = await apiPost(API.comments, 'create', { task_id: _activeTaskId, content });
    if (res.success) {
      await loadComments(_activeTaskId);
    } else {
      showToast(res.message, 'error');
    }
  } catch (e) { showToast('Erreur réseau', 'error'); }
}

async function deleteComment(id) {
  const res = await apiPost(API.comments, 'delete', { id });
  if (res.success) await loadComments(_activeTaskId);
  else showToast(res.message, 'error');
}

function handleCommentKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); submitComment(); }
}

// ─────────────────────────────────────────────────────
// THEME TOGGLE
// ─────────────────────────────────────────────────────
function toggleTheme() {
  const isLight = document.body.classList.toggle('light-mode');
  localStorage.setItem('nx-theme', isLight ? 'light' : 'dark');
}



