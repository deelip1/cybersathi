(() => {
  const timerEl = document.getElementById('timer');
  if (timerEl) {
    let time = 300;
    setInterval(() => {
      time--;
      timerEl.textContent = `Timer: ${time}s`;
      if (time <= 0) document.getElementById('quizForm')?.submit();
    }, 1000);
  }

  const form = document.getElementById('chatForm');
  const box = document.getElementById('chatBox');
  const cfgEl = document.getElementById('firebaseConfig');
  if (!form || !box || !cfgEl) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || document.getElementById('csrfToken')?.value || '';
  const firebaseConfig = {
    apiKey: cfgEl.dataset.apiKey,
    authDomain: cfgEl.dataset.authDomain,
    projectId: cfgEl.dataset.projectId,
  };

  const ready = firebaseConfig.apiKey && firebaseConfig.authDomain && firebaseConfig.projectId;

  async function sendLocalFallback(sender, message) {
    await fetch('/api/chat_send.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
      body: JSON.stringify({ sender, message })
    });
  }

  if (ready) {
    import('https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js').then(async ({ initializeApp }) => {
      const firestore = await import('https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js');
      const app = initializeApp(firebaseConfig);
      const db = firestore.getFirestore(app);
      const messagesRef = firestore.collection(db, 'chat_messages');

      firestore.onSnapshot(firestore.query(messagesRef, firestore.orderBy('createdAt', 'asc')), (snapshot) => {
        box.innerHTML = '';
        snapshot.forEach((doc) => {
          const r = doc.data();
          box.innerHTML += `<div><strong>${r.sender_name}</strong>: ${r.message}</div>`;
        });
        box.scrollTop = box.scrollHeight;
      });

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const sender = document.getElementById('chatSender').value;
        const message = document.getElementById('chatMessage').value;
        await firestore.addDoc(messagesRef, { sender_name: sender, message, createdAt: new Date() });
        await sendLocalFallback(sender, message);
        document.getElementById('chatMessage').value = '';
      });
    }).catch(() => {
      box.innerHTML = '<div class="text-muted">Firebase not configured. Using API fallback.</div>';
    });
  }

  if (!ready) {
    box.innerHTML = '<div class="text-muted">Firebase config missing. Using API fallback polling.</div>';
    async function fetchChat() {
      const res = await fetch('/api/chat_fetch.php');
      const rows = await res.json();
      box.innerHTML = rows.map(r => `<div><strong>${r.sender_name}</strong>: ${r.message} <small class="text-muted">${r.created_at}</small></div>`).join('');
      box.scrollTop = box.scrollHeight;
    }
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const sender = document.getElementById('chatSender').value;
      const message = document.getElementById('chatMessage').value;
      await sendLocalFallback(sender, message);
      document.getElementById('chatMessage').value = '';
      fetchChat();
    });
    setInterval(fetchChat, 3000);
    fetchChat();
  }
})();

(() => {
  const toggle = document.getElementById('aiChatToggle');
  const panel = document.getElementById('aiChatPanel');
  const body = document.getElementById('aiChatBody');
  const input = document.getElementById('aiChatInput');
  if (!toggle || !panel || !body || !input) return;

  const answer = (q) => {
    const t = q.toLowerCase();
    if (t.includes('upi') || t.includes('payment')) return 'Immediately contact your bank, block UPI, and file complaint on cybercrime.gov.in and 1930 helpline.';
    if (t.includes('otp')) return 'Never share OTP with anyone. Banks never ask OTP by call, SMS, or WhatsApp.';
    if (t.includes('report')) return 'Collect evidence (screenshots, transaction ID, phone numbers) and report at cybercrime.gov.in.';
    if (t.includes('loan') || t.includes('investment')) return 'Avoid unknown loan/investment links. Verify app ratings, company registration, and RBI warning lists.';
    return 'Stay alert: do not share OTP/PIN, verify links, and report fraud at cybercrime.gov.in or helpline 1930.';
  };

  toggle.addEventListener('click', () => panel.classList.toggle('d-none'));
  input.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    const q = input.value.trim();
    if (!q) return;
    body.innerHTML += `<div><strong>You:</strong> ${q}</div>`;
    body.innerHTML += `<div class="text-primary"><strong>Cyber AI:</strong> ${answer(q)}</div>`;
    input.value = '';
    body.scrollTop = body.scrollHeight;
  });
})();
