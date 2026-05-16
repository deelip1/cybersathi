<?php
require_once __DIR__ . '/config/db.php';
ensure_user();

// ✅ UPDATED: keep logic at top; supports HTML + JSON mode for async refresh.
$userId = (int)($_SESSION['user_id'] ?? 0);
$userName = (string)($_SESSION['user_name'] ?? 'User');
$results = [];
$errorMessage = '';

try {
    $stmt = $pdo->prepare('SELECT id, score, total_questions, created_at FROM quiz_results WHERE user_id = ? ORDER BY id DESC LIMIT 100');
    $stmt->execute([$userId]);
    $results = $stmt->fetchAll() ?: [];
} catch (Throwable $e) {
    $errorMessage = 'Unable to load quiz results right now. Please try again.';
}

if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'ok' => $errorMessage === '',
        'error' => $errorMessage,
        'results' => array_map(static function(array $r): array {
            return [
                'id' => (int)$r['id'],
                'score' => (int)$r['score'],
                'total_questions' => (int)$r['total_questions'],
                'created_at' => (string)$r['created_at'],
            ];
        }, $results),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

include __DIR__ . '/includes/header.php';
?>
<div class="container py-5">
    <div class="row g-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h2 class="mb-1">User Dashboard</h2>
                    <p class="text-muted mb-0">Welcome, <strong><?= e($userName) ?></strong></p>
                </div>
                <div class="d-flex gap-2">
                    <button id="refreshResultsBtn" class="btn btn-outline-secondary" type="button">
                        <span class="btn-label">Refresh Results</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                    <a href="/logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="card-title mb-3">Your Quiz Results</h4>

                    <?php if ($errorMessage !== ''): ?>
                        <div id="resultsError" class="alert alert-danger mb-3"><?= e($errorMessage) ?></div>
                    <?php else: ?>
                        <div id="resultsError" class="alert alert-danger mb-3 d-none"></div>
                    <?php endif; ?>

                    <div id="resultsEmpty" class="alert alert-info mb-3 <?= $results ? 'd-none' : '' ?>">No quiz attempts yet.</div>

                    <div id="resultsList" class="row g-3">
                        <?php foreach ($results as $r): ?>
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                        <div>
                                            <div class="fw-semibold">Score: <span class="text-primary"><?= (int)$r['score'] ?>/<?= (int)$r['total_questions'] ?></span></div>
                                            <div class="small text-muted">Attempted on: <?= e((string)$r['created_at']) ?></div>
                                        </div>
                                        <a class="btn btn-sm btn-outline-primary" href="/certificate.php?result_id=<?= (int)$r['id'] ?>&download=1">Download Certificate PDF</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    const refreshBtn = document.getElementById('refreshResultsBtn');
    const listEl = document.getElementById('resultsList');
    const emptyEl = document.getElementById('resultsEmpty');
    const errorEl = document.getElementById('resultsError');
    if (!refreshBtn || !listEl || !emptyEl || !errorEl) return;

    const label = refreshBtn.querySelector('.btn-label');
    const spinner = refreshBtn.querySelector('.spinner-border');

    function setLoading(isLoading) {
        refreshBtn.disabled = isLoading;
        if (label) label.textContent = isLoading ? 'Refreshing...' : 'Refresh Results';
        if (spinner) spinner.classList.toggle('d-none', !isLoading);
    }

    function renderRows(rows) {
        listEl.innerHTML = rows.map((row) => {
            const id = Number(row.id) || 0;
            const score = Number(row.score) || 0;
            const total = Number(row.total_questions) || 0;
            const created = String(row.created_at || '');
            return `
                <div class="col-12">
                    <div class="card border">
                        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <div class="fw-semibold">Score: <span class="text-primary">${score}/${total}</span></div>
                                <div class="small text-muted">Attempted on: ${created}</div>
                            </div>
                            <a class="btn btn-sm btn-outline-primary" href="/certificate.php?result_id=${id}&download=1">Download Certificate PDF</a>
                        </div>
                    </div>
                </div>`;
        }).join('');
        emptyEl.classList.toggle('d-none', rows.length > 0);
    }

    refreshBtn.addEventListener('click', async () => {
        setLoading(true);
        errorEl.classList.add('d-none');
        errorEl.textContent = '';
        try {
            const res = await fetch('/user-dashboard.php?format=json', {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('Failed to refresh results');
            const data = await res.json();
            if (!data.ok) throw new Error(data.error || 'Unable to refresh');
            renderRows(Array.isArray(data.results) ? data.results : []);
        } catch (err) {
            errorEl.textContent = 'Failed to refresh results. Please try again.';
            errorEl.classList.remove('d-none');
        } finally {
            setLoading(false);
        }
    });
})();
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
