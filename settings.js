/* ============================================================
   NexaFlow PHP – Settings Logic
   (Intégré à dashboard.js via loadSettings)
   ============================================================ */

function switchSettingsTab(tab, btn) {
  if (btn) {
    document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
  }
  loadSettings(tab);
}

function loadSettings(tab = 'profile') {
  const el = document.getElementById('settingsContent');
  if (!el) return;

  const u = NX_USER;
  const initials = u.initials || (u.firstname[0] + u.lastname[0]).toUpperCase();

  const templates = {
    profile: `
      <div style="display:grid;grid-template-columns:1fr 2fr;gap:28px">
        <div class="card" style="text-align:center">
          <div class="avatar avatar-xl" style="margin:0 auto 16px;width:80px;height:80px;font-size:28px">${initials}</div>
          <div style="font-weight:700;font-size:16px;margin-bottom:4px">${u.firstname} ${u.lastname}</div>
          <div style="color:var(--blue-400);font-size:13px;margin-bottom:4px">${u.role}</div>
          <div style="color:var(--gray-500);font-size:12px;margin-bottom:16px">${u.organisation || '—'}</div>
          <button class="btn btn-secondary btn-sm" style="width:100%">📷 Changer la photo</button>
        </div>
        <div class="card">
          <div class="card-title" style="margin-bottom:20px">Informations personnelles</div>
          <div class="form-row">
            <div class="form-group"><label>Prénom</label><div class="input-wrapper"><input type="text" id="settFirstname" value="${u.firstname}" style="padding-left:14px"/></div></div>
            <div class="form-group"><label>Nom</label><div class="input-wrapper"><input type="text" id="settLastname" value="${u.lastname}" style="padding-left:14px"/></div></div>
          </div>
          <div class="form-group"><label>Email</label><div class="input-wrapper"><input type="email" value="${u.email}" style="padding-left:14px" readonly/></div></div>
          <div class="form-group"><label>Organisation</label><div class="input-wrapper"><input type="text" id="settOrg" value="${u.organisation || ''}" style="padding-left:14px"/></div></div>
          <div class="form-group">
            <label>Langue</label>
            <select style="width:100%;padding:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none">
              <option selected>Français</option><option>English</option><option>Español</option>
            </select>
          </div>
          <button class="btn btn-primary" style="width:auto;padding:10px 24px" onclick="saveProfile()">Sauvegarder les modifications</button>
        </div>
      </div>
    `,

    org: `
      <div class="card">
        <div class="card-title" style="margin-bottom:24px">Paramètres de l'Organisation</div>
        <div class="form-group"><label>Nom de l'organisation</label><div class="input-wrapper"><input type="text" value="${u.organisation || 'Mon Organisation'}" style="padding-left:14px"/></div></div>
        <div class="form-group"><label>Site web</label><div class="input-wrapper"><input type="url" placeholder="https://monentreprise.fr" style="padding-left:14px"/></div></div>
        <div class="form-group"><label>Secteur d'activité</label>
          <select style="width:100%;padding:12px;background:rgba(10,22,40,.6);border:1px solid rgba(59,130,246,.2);border-radius:10px;color:white;outline:none">
            <option>Technologie & Software</option><option>Conseil</option><option>Finance</option><option>Santé</option>
          </select>
        </div>
        <div style="padding:16px;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.2);border-radius:12px;margin-bottom:20px">
          <div style="font-weight:700;margin-bottom:8px">Plan actuel: <span style="color:var(--blue-400)">Pro</span></div>
          <div style="font-size:13px;color:var(--gray-500)">Projets illimités · 50 GB stockage · Intégrations avancées</div>
        </div>
        <button class="btn btn-primary" style="width:auto;padding:10px 24px" onclick="showToast('Paramètres sauvegardés ✓','success')">Sauvegarder</button>
      </div>
    `,

    notifs: `
      <div class="card">
        <div class="card-title" style="margin-bottom:24px">Préférences de Notifications</div>
        ${[
          ['Tâches assignées', 'Recevoir une notification quand une tâche vous est assignée', true],
          ['Commentaires', 'Être notifié des nouveaux commentaires', true],
          ['Mentions', 'Notification quand vous êtes mentionné', true],
          ['Dates d\'échéance', 'Rappel 24h avant une échéance', true],
          ['Activité de l\'équipe', 'Résumé quotidien des activités', false],
          ['Mises à jour de projet', 'Changements de statut de projet', true],
          ['Rapports hebdomadaires', 'Rapport de performance chaque lundi', false],
        ].map(([label, desc, on]) => `
          <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 0;border-bottom:1px solid rgba(59,130,246,.08)">
            <div>
              <div style="font-size:14px;font-weight:600;margin-bottom:3px">${label}</div>
              <div style="font-size:12px;color:var(--gray-500)">${desc}</div>
            </div>
            <div onclick="this.classList.toggle('on');showToast('Préférence mise à jour','success')"
              style="width:40px;height:22px;border-radius:11px;background:${on?'var(--blue-600)':'rgba(255,255,255,.1)'};position:relative;cursor:pointer;transition:all .3s" class="${on?'on':''}">
              <div style="position:absolute;top:2px;${on?'right:2px':'left:2px'};width:18px;height:18px;border-radius:50%;background:white;transition:all .3s"></div>
            </div>
          </div>
        `).join('')}
      </div>
    `,

    security: `
      <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card">
          <div class="card-title" style="margin-bottom:20px">Changer le mot de passe</div>
          <div class="form-group"><label>Mot de passe actuel</label><div class="input-wrapper"><input type="password" id="currPwd" placeholder="••••••••" style="padding-left:14px"/></div></div>
          <div class="form-group"><label>Nouveau mot de passe</label><div class="input-wrapper"><input type="password" id="newPwd" placeholder="••••••••" style="padding-left:14px" minlength="8"/></div></div>
          <div class="form-group"><label>Confirmer le nouveau mot de passe</label><div class="input-wrapper"><input type="password" id="confirmPwd" placeholder="••••••••" style="padding-left:14px"/></div></div>
          <button class="btn btn-primary" style="width:auto;padding:10px 24px" onclick="changePassword()">Mettre à jour le mot de passe</button>
        </div>
        <div class="card">
          <div class="card-title" style="margin-bottom:20px">Authentification à deux facteurs (2FA)</div>
          <div style="display:flex;align-items:center;justify-content:space-between">
            <div>
              <div style="font-size:14px;font-weight:600">Activer la 2FA</div>
              <div style="font-size:12px;color:var(--gray-500)">Sécurisez votre compte avec Google Authenticator</div>
            </div>
            <div onclick="showToast('Configuration 2FA disponible en production','info')"
              style="width:40px;height:22px;border-radius:11px;background:rgba(255,255,255,.1);position:relative;cursor:pointer">
              <div style="position:absolute;top:2px;left:2px;width:18px;height:18px;border-radius:50%;background:white;transition:all .3s"></div>
            </div>
          </div>
        </div>
      </div>
    `,

    billing: `
      <div class="card">
        <div class="card-title" style="margin-bottom:24px">Mon Abonnement</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px">
          ${[
            ['Starter', '0€/mois', ['3 projets', '5 membres', '1 GB'], false],
            ['Pro', '29€/mois', ['Projets illimités', '20 membres', '50 GB', 'Intégrations'], true],
            ['Enterprise', '99€/mois', ['Tout illimité', 'SSO/SAML', 'Support dédié'], false],
          ].map(([plan, price, features, current]) => `
            <div style="padding:20px;border-radius:14px;border:${current?'2px solid var(--blue-500)':'1px solid rgba(59,130,246,.15)'};background:${current?'rgba(59,130,246,.1)':'rgba(13,31,60,.5)'};text-align:center">
              ${current ? '<div style="font-size:11px;font-weight:700;color:var(--blue-400);letter-spacing:.8px;text-transform:uppercase;margin-bottom:8px">PLAN ACTUEL</div>' : ''}
              <div style="font-size:18px;font-weight:800;margin-bottom:4px">${plan}</div>
              <div style="font-size:22px;font-weight:900;color:var(--blue-400);margin-bottom:16px">${price}</div>
              ${features.map(f => `<div style="font-size:12px;color:var(--gray-400);margin-bottom:6px">✓ ${f}</div>`).join('')}
              <button class="btn ${current?'btn-secondary':'btn-primary'} btn-sm" style="width:100%;margin-top:12px"
                onclick="showToast('${current ? 'Plan actuel' : 'Mise à niveau vers '+plan}','${current?'info':'success'}')">
                ${current ? 'Plan actuel' : 'Choisir'}
              </button>
            </div>
          `).join('')}
        </div>
      </div>
    `,
  };

  el.innerHTML = templates[tab] || templates.profile;
}

async function saveProfile() {
  const firstname = document.getElementById('settFirstname')?.value;
  const lastname  = document.getElementById('settLastname')?.value;
  const organisation = document.getElementById('settOrg')?.value;
  if (!firstname || !lastname) { showToast('Prénom et nom requis', 'error'); return; }

  const res = await apiPost(API.team, 'update_profile', { firstname, lastname, organisation });
  if (res.success) showToast(res.message, 'success');
  else showToast(res.message, 'error');
}

async function changePassword() {
  const curr    = document.getElementById('currPwd')?.value;
  const newPwd  = document.getElementById('newPwd')?.value;
  const confirm = document.getElementById('confirmPwd')?.value;
  if (newPwd !== confirm) { showToast('Les mots de passe ne correspondent pas', 'error'); return; }
  if (!curr || !newPwd)   { showToast('Tous les champs sont requis', 'error'); return; }

  const res = await apiPost(API.team, 'change_password', { current_password: curr, new_password: newPwd });
  if (res.success) showToast(res.message, 'success');
  else showToast(res.message, 'error');
}
