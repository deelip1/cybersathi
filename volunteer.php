<?php include __DIR__ . '/includes/header.php'; ?>
<div class="container py-5">
  <h2>Join Cyber Pioneer Volunteer</h2>
  <?php if(isset($_GET["success"])): ?><div class="alert alert-success">Application submitted successfully.</div><?php endif; ?>
  <form method="post" action="/api/volunteer_apply.php" class="row g-3">
    <?= csrf_input() ?>
    <div class="col-md-6"><input name="name" class="form-control" required placeholder="Name"></div>
    <div class="col-md-6"><input name="email" type="email" class="form-control" required placeholder="Email"></div>
    <div class="col-md-6"><input name="mobile" class="form-control" required placeholder="Mobile"></div>
    <div class="col-md-6"><input name="city" class="form-control" required placeholder="City"></div>
    <div class="col-md-6"><input name="skills" class="form-control" required placeholder="Skills"></div>
    <div class="col-md-6"><input name="occupation" class="form-control" required placeholder="Occupation"></div>
    <div class="col-md-6"><input name="password" type="password" class="form-control" required placeholder="Create Password"></div>
    <?php if (RECAPTCHA_SITE_KEY !== ""): ?><div class="col-12"><div class="g-recaptcha" data-sitekey="<?= e(RECAPTCHA_SITE_KEY) ?>"></div></div><?php endif; ?>
    <div class="col-12"><button class="btn btn-success">Apply as Volunteer</button></div>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
