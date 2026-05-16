<?php
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';
?>
<div class="container py-5">
  <div class="row g-4">
    <div class="col-12 col-lg-7">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h2 class="h4 mb-3">AI Scam Analyzer</h2>
          <p class="text-muted">Paste a suspicious message, UPI request, URL, or call summary. The analyzer gives instant rule-based risk guidance asynchronously.</p>
          <form id="scamAnalyzerForm" class="row g-3">
            <div class="col-12">
              <label for="scamInput" class="form-label">Suspicious content</label>
              <textarea id="scamInput" class="form-control" rows="6" maxlength="2000" placeholder="Example: I got a call asking for OTP to reverse a failed UPI debit..." required></textarea>
            </div>
            <div class="col-12 d-flex gap-2">
              <button id="analyzeBtn" class="btn btn-primary" type="submit">
                <span class="btn-label">Analyze Risk</span>
                <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>
              </button>
              <button type="button" id="clearAnalyzerBtn" class="btn btn-outline-secondary">Clear</button>
            </div>
          </form>
          <div id="analyzerResult" class="alert alert-info mt-3 d-none" role="status"></div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-5">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h3 class="h5">Emergency Steps</h3>
          <ol class="mb-0">
            <li>Immediately call cyber helpline <strong>1930</strong>.</li>
            <li>Report at <a href="https://cybercrime.gov.in" target="_blank" rel="noopener noreferrer">cybercrime.gov.in</a>.</li>
            <li>Inform your bank/payment app and request account freeze/reversal.</li>
            <li>Collect evidence: screenshots, transaction IDs, chat/call logs, URLs.</li>
            <li>Reset passwords and enable 2FA on bank/email/social accounts.</li>
          </ol>
          <div class="alert alert-warning mt-3 mb-0">For monetary fraud, report within the golden hour to improve recovery chances.</div>
        </div>
      </div>
    </div>
  </div>
<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5">
  <h2>AI Cyber Guidance (Rule-Based Assistant)</h2>
  <ol>
    <li>Immediately call cyber helpline <strong>1930</strong>.</li>
    <li>Report at <a href="https://cybercrime.gov.in" target="_blank">cybercrime.gov.in</a>.</li>
    <li>Inform your bank/payment app and request account freeze/reversal.</li>
    <li>Collect evidence: screenshots, transaction IDs, chat/call logs, URLs.</li>
    <li>Reset passwords and enable 2FA on bank/email/social accounts.</li>
  </ol>
  <div class="alert alert-info">For severe monetary fraud, report within the golden hour for better recovery chances.</div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
