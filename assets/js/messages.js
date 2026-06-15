(function(){
  const root = document.getElementById('chat-root');
  if (!root) return;
  const other = root.dataset.other;
  const list = document.getElementById('messageList');
  const form = document.getElementById('chatForm');
  const bodyEl = document.getElementById('messageBody');
  let polling = null;

  function render(messages){
    list.innerHTML = '';
    const thread = document.createElement('div'); thread.className = 'thread chat-container';
    messages.forEach(m => {
      const isMe = (parseInt(m.sender_id,10) === parseInt(window.USER_ID,10));
      const row = document.createElement('div'); row.className = 'chat-row ' + (isMe ? 'outgoing' : 'incoming');

      // avatar for incoming/outgoing
      if (!isMe) {
        const img = document.createElement('img'); img.className = 'chat-avatar';
        img.src = m.sender_pic ? ('/brilliance/uploads/' + m.sender_pic) : '/brilliance/assets/images/logo.png';
        img.alt = m.sender_name || 'User';
        row.appendChild(img);
      }

      const col = document.createElement('div'); col.style.display = 'flex'; col.style.flexDirection = 'column'; col.style.alignItems = isMe ? 'flex-end' : 'flex-start';
      const bubble = document.createElement('div');
      bubble.className = 'bubble ' + (isMe ? 'outgoing' : 'incoming');
      bubble.innerHTML = '<div style="font-size:14px;">' + (m.subject ? '<strong>' + escapeHtml(m.subject) + '</strong><br>' : '') + escapeHtml(m.body) + '</div>';
      const meta = document.createElement('div'); meta.className = 'chat-meta'; meta.textContent = (isMe ? 'You' : (m.sender_name || 'User')) + ' · ' + m.created_at;
      col.appendChild(bubble);
      col.appendChild(meta);
      row.appendChild(col);

      if (isMe) {
        const img = document.createElement('img'); img.className = 'chat-avatar';
        img.src = window.USER_AVATAR ? ('/brilliance/uploads/' + window.USER_AVATAR) : '/brilliance/assets/images/logo.png';
        img.alt = 'You';
        row.appendChild(img);
      }

      thread.appendChild(row);
    });
    list.appendChild(thread);
    // smooth scroll to bottom
    list.scrollTo({ top: list.scrollHeight, behavior: 'smooth' });
  }

  function escapeHtml(str){
    if (!str) return '';
    return String(str).replace(/[&<>\"]/g, function(s){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'})[s]; });
  }

  async function fetchConversation(){
    try {
      const res = await fetch('/brilliance/messages/api/conversation.php?user=' + encodeURIComponent(other));
      if (!res.ok) return;
      const data = await res.json();
      if (data && Array.isArray(data.messages)) render(data.messages);
    } catch (e) { console.error(e); }
  }

  form.addEventListener('submit', async function(e){
    e.preventDefault();
    const fd = new FormData(form);
    try {
      const res = await fetch('/brilliance/processes/send-message-ajax.php', { method:'POST', body: fd });
      if (!res.ok) { alert('Unable to send'); return; }
      const data = await res.json();
      if (data && data.ok) {
        bodyEl.value = '';
        await fetchConversation();
      } else {
        alert('Send failed');
      }
    } catch (err) { console.error(err); alert('Send error'); }
  });

  // initial fetch + polling
  fetchConversation();
  polling = setInterval(fetchConversation, 2500);
})();
