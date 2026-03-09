(() => {
  const form = document.getElementById('chatForm');
  const box = document.getElementById('chatBox');
  if (!form || !box) return;

  async function fetchChat() {
    const res = await fetch('/api/chat_fetch.php');
    const rows = await res.json();
    box.innerHTML = rows.map(r => `<div><strong>${r.sender_name}</strong>: ${r.message} <small class="text-muted">${r.created_at}</small></div>`).join('');
    box.scrollTop = box.scrollHeight;
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    await fetch('/api/chat_send.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ sender: document.getElementById('chatSender').value, message: document.getElementById('chatMessage').value })
    });
    document.getElementById('chatMessage').value = '';
    fetchChat();
  });

  setInterval(fetchChat, 3000);
  fetchChat();

  const timerEl = document.getElementById('timer');
  if (timerEl) {
    let time = 300;
    setInterval(() => {
      time--;
      timerEl.textContent = `Timer: ${time}s`;
      if (time <= 0) document.getElementById('quizForm')?.submit();
    }, 1000);
  }
})();
