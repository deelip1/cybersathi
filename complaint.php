<?php require_once __DIR__ . '/config/db.php'; include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5">
  <h2>Cyber Fraud Help</h2>
  <?php if(isset($_GET["success"])): ?><div class="alert alert-success">Complaint submitted successfully.</div><?php endif; ?>
  <?php if(isset($_GET["error"])): ?><div class="alert alert-danger"><?= e($_GET["error"]) ?></div><?php endif; ?>
  <form method="post" action="/api/complaint_submit.php" enctype="multipart/form-data" class="row g-3">
    <?= csrf_input() ?>
    <div class="col-md-6"><input name="name" class="form-control" required placeholder="Name"></div>
    <div class="col-md-6"><input name="mobile" class="form-control" required placeholder="Mobile"></div>
    <div class="col-md-6"><input name="email" type="email" class="form-control" required placeholder="Email"></div>
    <div class="col-md-6"><input name="city" class="form-control" required placeholder="City"></div>
    <div class="col-md-6"><input name="fraud_type" class="form-control" required placeholder="Fraud Type"></div>
    <div class="col-md-6"><input name="suspected_source" class="form-control" placeholder="Suspected Phone/Website/Social"></div>
    <div class="col-12"><textarea name="description" class="form-control" rows="4" required placeholder="Fraud Description"></textarea></div>
    <div class="col-12"><input type="file" name="evidence" class="form-control" accept="image/*,.pdf"></div>
    <?php if (RECAPTCHA_SITE_KEY !== ""): ?><div class="col-12"><div class="g-recaptcha" data-sitekey="<?= e(RECAPTCHA_SITE_KEY) ?>"></div></div><?php endif; ?>
    <div class="col-12"><button class="btn btn-danger">Submit Complaint</button></div>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
