<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5">
  <h2>Live Chat Support (Firebase Realtime)</h2>
  <p>Connect with Cyber Volunteers for guidance in real-time.</p>
  <div id="chatBox" class="chat-box mb-3"></div>
  <form id="chatForm" class="input-group">
    <input type="hidden" id="csrfToken" value="<?= e(csrf_token()) ?>">
    <input id="chatSender" class="form-control" placeholder="Your name" required>
    <input id="chatMessage" class="form-control" placeholder="Type message" required>
    <button class="btn btn-primary">Send</button>
  </form>
  <div id="firebaseConfig"
       data-api-key="<?= e(FIREBASE_API_KEY) ?>"
       data-auth-domain="<?= e(FIREBASE_AUTH_DOMAIN) ?>"
       data-project-id="<?= e(FIREBASE_PROJECT_ID) ?>"></div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
