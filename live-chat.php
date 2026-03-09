<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5">
  <h2>Live Chat Support</h2>
  <p>Connect with Cyber Volunteers for guidance.</p>
  <div id="chatBox" class="chat-box mb-3"></div>
  <form id="chatForm" class="input-group">
    <input id="chatSender" class="form-control" placeholder="Your name" required>
    <input id="chatMessage" class="form-control" placeholder="Type message" required>
    <button class="btn btn-primary">Send</button>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
