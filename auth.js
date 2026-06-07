// =====================================================
// NexaFlow PHP – Auth JS (côté client uniquement)
// La vraie auth est côté serveur PHP
// =====================================================

function switchTab(tab) {
  document.getElementById('loginForm').classList.toggle('active', tab === 'login');
  document.getElementById('registerForm').classList.toggle('active', tab === 'register');
  document.getElementById('loginTab').classList.toggle('active', tab === 'login');
  document.getElementById('registerTab').classList.toggle('active', tab === 'register');
}

function togglePassword(inputId, btn) {
  const input = document.getElementById(inputId);
  if (input.type === 'password') {
    input.type = 'text';
    btn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
  } else {
    input.type = 'password';
    btn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
  }
}

function checkPasswordStrength(val) {
  const fill  = document.getElementById('strengthFill');
  const text  = document.getElementById('strengthText');
  if (!fill || !text) return;
  
  let strength = 0;
  if (val.length >= 8)          strength++;
  if (/[A-Z]/.test(val))        strength++;
  if (/[0-9]/.test(val))        strength++;
  if (/[^A-Za-z0-9]/.test(val)) strength++;

  const levels = [
    { pct: '25%', color: '#ef4444', label: 'Faible',    labelColor: '#fca5a5' },
    { pct: '50%', color: '#f59e0b', label: 'Moyen',     labelColor: '#fcd34d' },
    { pct: '75%', color: '#3b82f6', label: 'Fort',      labelColor: '#93c5fd' },
    { pct: '100%',color: '#10b981', label: 'Très fort', labelColor: '#6ee7b7' },
  ];
  const lvl = levels[Math.min(strength - 1, 3)] || { pct: '0%', color: 'transparent', label: '', labelColor: '' };
  fill.style.width = val.length ? lvl.pct : '0%';
  fill.style.background = lvl.color;
  text.textContent = val.length ? lvl.label : '';
  text.style.color = lvl.labelColor;
}

function showLoader(form) {
  const btn = form.querySelector('[type="submit"]');
  if (!btn) return;
  const textEl   = btn.querySelector('.btn-text');
  const loaderEl = btn.querySelector('.btn-loader');
  if (textEl)   textEl.classList.add('hidden');
  if (loaderEl) loaderEl.classList.remove('hidden');
  setTimeout(() => { btn.disabled = true; }, 10);
}

function showToast(msg, type = 'info') {
  const toast = document.getElementById('toast');
  if (!toast) return;
  const icons = { success: '✅', error: '❌', info: 'ℹ️', warning: '⚠️' };
  toast.innerHTML = `<span>${icons[type] || 'ℹ️'}</span> ${msg}`;
  toast.className = `toast show ${type}`;
  setTimeout(() => toast.classList.remove('show'), 3500);
}
