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
    messages.forEach(m => {
      const card = document.createElement('div'); card.className='message-card';
      const left = document.createElement('div'); left.className='message-meta';
      const who = document.createElement('div'); who.style.fontWeight='700';
      // show 'You' for current user's messages, otherwise show the sender's full name if available
      who.textContent = (m.sender_id == window.USER_ID ? 'You' : (m.sender_name || 'User'));
      const subject = document.createElement('div'); subject.className='message-subject'; subject.textContent = (m.subject || '');
      const snippet = document.createElement('div'); snippet.className='message-snippet'; snippet.textContent = m.body;
      left.appendChild(who); if (m.subject) left.appendChild(subject); left.appendChild(snippet);
      const right = document.createElement('div'); right.style.textAlign='right';
      const date = document.createElement('div'); date.className='message-date'; date.textContent = m.created_at;
      right.appendChild(date);
      card.appendChild(left); card.appendChild(right);
      list.appendChild(card);
    });
    // scroll to bottom
    list.scrollTop = list.scrollHeight;
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
