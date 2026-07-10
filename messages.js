// =====================================================
// NexaFlow PHP – Messages JS (API réelle)
// =====================================================

let activeChannel = null;
let messagesPolling = null;
let allChannels = [];

async function loadMessages() {
  try {
    const chRes = await apiGet(API.messages, { action: 'channels' });
    allChannels = chRes.data;
    renderChannels(allChannels);
    
    if (activeChannel) {
      await loadChannelMessages(activeChannel);
    }
  } catch (e) { console.error(e); }
}

function filterChannels() {
  const searchInput = document.getElementById('channelSearch');
  if (!searchInput) return;
  const q = searchInput.value.toLowerCase();
  
  const filtered = allChannels.filter(ch => ch.name.toLowerCase().includes(q));
  renderChannels(filtered);
}

function renderChannels(channels) {
  const list = document.getElementById('channelsList');
  if (!list) return;
  
  if (channels.length === 0) {
    list.innerHTML = '<div style="padding:10px;text-align:center;color:var(--gray-500);font-size:13px">Aucune équipe trouvée</div>';
    return;
  }
  
  list.innerHTML = channels.map(ch => {
    const lastSeen = parseInt(localStorage.getItem(`seen-ch-${ch.id}`) || 0);
    const hasNew = ch.count > lastSeen;
    
    return `
      <div onclick="setChannel('${ch.id}', ${ch.count})" style="
        display:flex; align-items:center; gap:10px; padding:10px 12px;
        border-radius:10px; cursor:pointer; font-size:14px;
        color:${activeChannel === ch.id ? 'var(--blue-300)' : 'var(--gray-400)'};
        background:${activeChannel === ch.id ? 'rgba(59,130,246,.12)' : 'transparent'};
        transition: all 0.2s;
        position: relative;
      ">
        <span style="color:var(--blue-600)">${ch.icon}</span>
        ${ch.name}
        ${hasNew ? `
          <span style="margin-left:auto;background:var(--danger);color:white;font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;box-shadow:0 0 8px rgba(239, 68, 68, 0.3)">
            Nouveau
          </span>` : ''}
      </div>
    `;
  }).join('');
}

async function loadChannelMessages(channel) {
  try {
    const res = await apiGet(API.messages, { action: 'list', channel });
    renderChatMessages(res.data);
    
    const currentCh = allChannels.find(c => c.id === channel);
    if (currentCh) {
      const elName = document.getElementById('currentChannelName');
      const elDesc = document.getElementById('currentChannelDesc');
      if (elName) elName.textContent = `# ${currentCh.name}`;
      if (elDesc) elDesc.textContent = currentCh.desc || `Discussions de l'équipe ${currentCh.name}`;
    }
  } catch (e) { console.error(e); }
}

function renderChatMessages(messages) {
  const chatEl = document.getElementById('chatMessages');
  if (!chatEl) return;

  if (!messages.length) {
    chatEl.innerHTML = '<div style="text-align:center;color:var(--gray-600);font-size:14px;margin:auto">Aucun message dans ce canal. Soyez le premier ! 👋</div>';
    return;
  }

  chatEl.innerHTML = messages.map(m => `
    <div style="display:flex;gap:12px;align-items:flex-start;${m.is_mine ? 'flex-direction:row-reverse' : ''}">
      <div class="avatar" style="${m.is_mine ? 'background:linear-gradient(135deg,#1d4ed8,#60a5fa)' : ''}">${m.avatar_initials || '?'}</div>
      <div style="max-width:70%">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;${m.is_mine ? 'flex-direction:row-reverse' : ''}">
          <span style="font-size:13px;font-weight:700">${m.firstname || ''} ${m.lastname || ''}</span>
          <span style="font-size:11px;color:var(--gray-600)">${m.time}</span>
        </div>
        <div style="
          padding:10px 14px; border-radius:12px; font-size:14px;
          background:${m.is_mine ? 'linear-gradient(135deg,var(--blue-800),var(--blue-600))' : 'rgba(13,31,60,.8)'};
          border:1px solid ${m.is_mine ? 'rgba(59,130,246,.4)' : 'rgba(59,130,246,.12)'};
          ${m.is_mine ? 'border-bottom-right-radius:4px' : 'border-bottom-left-radius:4px'};
          color:var(--white); line-height:1.5;
        ">${m.content}</div>
      </div>
    </div>
  `).join('');

  chatEl.scrollTop = chatEl.scrollHeight;
}

function setChannel(channelId, currentCount) {
  activeChannel = channelId;
  if (currentCount !== undefined) {
    localStorage.setItem(`seen-ch-${channelId}`, currentCount);
  }
  
  const noChannelEl = document.getElementById('noChannelSelected');
  const chatAreaEl = document.getElementById('activeChatArea');
  
  if (noChannelEl && chatAreaEl) {
    noChannelEl.style.display = 'none';
    chatAreaEl.style.display = 'flex';
  }
  
  loadMessages();
}

async function sendMessage() {
  const input = document.getElementById('chatInput');
  const content = input.value.trim();
  if (!content) return;

  input.value = '';
  try {
    const res = await apiPost(API.messages, 'send', { channel: activeChannel, content });
    if (res.success) {
      // Marquer comme vu pour l'envoyeur
      const chRes = await apiGet(API.messages, { action: 'channels' });
      const currentCh = chRes.data.find(c => c.id === activeChannel);
      if (currentCh) {
        localStorage.setItem(`seen-ch-${activeChannel}`, currentCh.count);
      }
      // Recharger les messages du canal
      await loadChannelMessages(activeChannel);
      renderChannels(chRes.data); // Mettre à jour la liste des canaux (badges)
    } else {
      showToast(res.message || 'Erreur d\'envoi', 'error');
    }
  } catch (e) {
    showToast('Erreur réseau', 'error');
  }
}

function handleChatKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
}
