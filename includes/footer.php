<footer class="py-4 text-center text-light bg-dark mt-5">
  <div class="container">
    <p class="mb-1">© <?= date('Y') ?> Cyber Sathi - Free Social Service Campaign by <?= FOUNDER_NAME ?></p>
    <small><?= FOUNDER_LOCATION ?> | <?= PRIMARY_EMAIL ?> | <?= CONTACT_NUMBER ?></small>
  </div>
</footer>

<div id="aiChatWidget" class="ai-chat-widget">
  <button id="aiChatToggle" class="btn btn-primary rounded-pill">💬 Cyber AI Help</button>
  <div id="aiChatPanel" class="card d-none">
    <div class="card-header">Cyber Sathi AI Guidance</div>
    <div class="card-body" id="aiChatBody"><div class="small text-muted">Ask: "How to report UPI fraud?"</div></div>
    <div class="card-footer"><input id="aiChatInput" class="form-control form-control-sm" placeholder="Type your question and press Enter"></div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body></html>
