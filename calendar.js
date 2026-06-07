/* ============================================================
   NexaFlow PHP – Calendar Logic (via API)
   ============================================================ */

let calendarDate = new Date();
let currentEvents = [];

// ─── MINI CALENDAR (Dashboard) ─────────────────────────────
async function renderMiniCalendar() {
  const el = document.getElementById('calendarMini');
  if (!el) return;

  const year  = calendarDate.getFullYear();
  const month = calendarDate.getMonth();
  const today = new Date();

  const monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
  const dayNames   = ['Lu','Ma','Me','Je','Ve','Sa','Di'];

  const first   = new Date(year, month, 1);
  const lastDay = new Date(year, month + 1, 0).getDate();
  let startDay  = first.getDay() - 1; if (startDay < 0) startDay = 6;

  // Récupérer les événements du mois
  try {
    const res = await apiGet(API.automations, { action: 'events', month: month + 1, year });
    currentEvents = res.data || [];
  } catch (e) { console.error(e); }

  const eventDates = currentEvents.map(e => e.event_date);

  let html = `
    <div class="cal-header">
      <button class="cal-nav" style="background:none;color:var(--gray-400);cursor:pointer;padding:4px" onclick="changeMiniMonth(-1)">‹</button>
      <span class="cal-month">${monthNames[month]} ${year}</span>
      <button class="cal-nav" style="background:none;color:var(--gray-400);cursor:pointer;padding:4px" onclick="changeMiniMonth(1)">›</button>
    </div>
    <div class="cal-grid">
      ${dayNames.map(d => `<div class="cal-day-name">${d}</div>`).join('')}
  `;

  for (let i = 0; i < startDay; i++) {
    html += `<div class="cal-day other-month"></div>`;
  }

  for (let d = 1; d <= lastDay; d++) {
    const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const isToday = dateStr === `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
    const hasEv   = eventDates.includes(dateStr);
    html += `<div class="cal-day ${isToday ? 'today' : ''} ${hasEv ? 'has-event' : ''}" onclick="selectDate('${dateStr}')">${d}</div>`;
  }

  html += `</div>`;
  el.innerHTML = html;
}

function changeMiniMonth(dir) {
  calendarDate.setMonth(calendarDate.getMonth() + dir);
  renderMiniCalendar();
}

function selectDate(dateStr) {
  const evs = currentEvents.filter(e => e.event_date === dateStr);
  if (evs.length) {
    showToast(`📅 ${evs[0].title} – ${evs[0].project_name || 'Personnel'}`, 'info');
  }
}

// ─── BIG CALENDAR (Page Calendrier) ────────────────────────
async function loadCalendar() {
  const el = document.getElementById('bigCalendar');
  if (!el) return;

  const year   = calendarDate.getFullYear();
  const month  = calendarDate.getMonth();
  const today  = new Date();
  
  const monthNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
  const dayNames   = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
  
  const first      = new Date(year, month, 1);
  const lastDay    = new Date(year, month + 1, 0).getDate();
  let startDay     = first.getDay() - 1; if (startDay < 0) startDay = 6;

  try {
    const res = await apiGet(API.automations, { action: 'events', month: month + 1, year });
    currentEvents = res.data || [];
  } catch (e) { console.error(e); }

  const eventMap = {};
  currentEvents.forEach(e => {
    const d = e.event_date.split('-')[2];
    if (!eventMap[d]) eventMap[d] = [];
    eventMap[d].push(e);
  });

  let html = `
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
      <span style="font-family:var(--font-display);font-size:20px;font-weight:700">${monthNames[month]} ${year}</span>
      <div style="display:flex;gap:8px">
        <button class="btn btn-secondary btn-sm" onclick="changeMonth(-1)">‹ Mois préc.</button>
        <button class="btn btn-secondary btn-sm" onclick="changeMonth(1)">Mois suiv. ›</button>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px">
      ${dayNames.map(d => `<div style="text-align:center;font-size:11px;font-weight:700;color:var(--gray-600);padding:8px 0;text-transform:uppercase">${d}</div>`).join('')}
  `;

  for (let i = 0; i < startDay; i++) html += `<div style="min-height:80px"></div>`;

  for (let d = 1; d <= lastDay; d++) {
    const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const isToday = dateStr === `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
    const events  = eventMap[String(d).padStart(2,'0')] || [];
    
    html += `
      <div style="min-height:80px;padding:6px;border:1px solid rgba(59,130,246,.08);border-radius:6px;background:${isToday ? 'rgba(59,130,246,.12)' : 'rgba(10,22,40,.3)'}">
        <div style="font-size:13px;font-weight:${isToday?'700':'400'};color:${isToday?'var(--blue-400)':'var(--gray-400)'};margin-bottom:4px">${d}</div>
        ${events.map(e => `
          <div style="font-size:10px;background:${escHtml(e.project_color||'#3b82f6')}33;border-left:2px solid ${escHtml(e.project_color||'#3b82f6')};padding:2px 5px;border-radius:3px;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:white" title="${escHtml(e.title)}">
            ${escHtml(e.time_formatted)} ${escHtml(e.title)}
          </div>
        `).join('')}
      </div>
    `;
  }

  html += `</div>`;
  el.innerHTML = html;

  // Render upcoming events
  try {
    const upRes = await apiGet(API.automations, { action: 'upcoming' });
    const upcomingEl = document.getElementById('upcomingEvents');
    if (upcomingEl) {
      if (!upRes.data.length) {
        upcomingEl.innerHTML = '<div style="color:var(--gray-500);font-size:13px;padding:10px 0">Aucun événement à venir.</div>';
      } else {
        upcomingEl.innerHTML = upRes.data.map(e => `
          <div style="display:flex;gap:12px;padding:12px;border-radius:10px;background:rgba(13,31,60,.5);border:1px solid rgba(59,130,246,.12);margin-bottom:8px">
            <div style="width:4px;border-radius:4px;background:${escHtml(e.project_color||'#3b82f6')};flex-shrink:0"></div>
            <div>
              <div style="font-size:13px;font-weight:600;margin-bottom:3px">${escHtml(e.title)}</div>
              <div style="font-size:11px;color:var(--gray-500)">${escHtml(e.date_formatted)} ${e.time_formatted?'à '+e.time_formatted:''} · ${escHtml(e.project_name||'Personnel')}</div>
            </div>
          </div>
        `).join('');
      }
    }
  } catch (e) {}
}

function changeMonth(dir) {
  calendarDate.setMonth(calendarDate.getMonth() + dir);
  loadCalendar();
}

async function createCalendarEvent(e) {
  e.preventDefault();
  const title = document.getElementById('eventTitle').value;
  const date = document.getElementById('eventDate').value;
  const time = document.getElementById('eventTime').value;
  const color = document.getElementById('eventColor').value;

  try {
    const res = await apiPost(API.automations, 'create_event', {
      title,
      event_date: date,
      event_time: time,
      color
    });

    if (res.success) {
      showToast(res.message, 'success');
      closeModal('newEventModal');
      e.target.reset();
      loadCalendar();
      renderMiniCalendar();
    } else {
      showToast(res.message, 'error');
    }
  } catch (err) {
    console.error(err);
    showToast('Erreur de connexion', 'error');
  }
}
