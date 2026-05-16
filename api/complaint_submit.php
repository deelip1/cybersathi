<?php
require_once __DIR__ . '/../config/db.php';

// ❌ OLD: direct die()/exit messages and weak file-type checks.
// ✅ UPDATED: centralized validation, strict MIME verification, optional image compression, safe redirects.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

if (!validate_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(419);
    exit('CSRF Failed');
}

if (!verify_recaptcha($_POST['g-recaptcha-response'] ?? null)) {
    header('Location: /complaint.php?error=' . urlencode('reCAPTCHA verification failed.'));
    exit;
}

$name = trim((string)($_POST['name'] ?? ''));
$mobile = trim((string)($_POST['mobile'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$city = trim((string)($_POST['city'] ?? ''));
$fraudType = trim((string)($_POST['fraud_type'] ?? ''));
$description = trim((string)($_POST['description'] ?? ''));
$suspectedSource = trim((string)($_POST['suspected_source'] ?? ''));

if ($name === '' || $mobile === '' || $email === '' || $city === '' || $fraudType === '' || $description === '') {
    header('Location: /complaint.php?error=' . urlencode('Please fill all required fields.'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /complaint.php?error=' . urlencode('Please enter a valid email address.'));
    exit;
}

if (!preg_match('/^[0-9+\-\s]{8,20}$/', $mobile)) {
    header('Location: /complaint.php?error=' . urlencode('Please enter a valid mobile number.'));
    exit;
}

$uploadPath = null;
$uploadsDir = __DIR__ . '/../uploads';
if (!is_dir($uploadsDir)) {
    @mkdir($uploadsDir, 0755, true);
}

/**
 * ✅ UPDATED: Secure upload processor with MIME verification and image compression.
 */
$processEvidence = static function(array $file, string $uploadsDir): ?string {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE || empty($file['name'])) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('File upload failed.');
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('File too large. Maximum allowed size is 5 MB.');
    }

    $tmp = (string)$file['tmp_name'];
    if (!is_uploaded_file($tmp)) {
        throw new RuntimeException('Invalid upload source.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $detectedMime = (string)$finfo->file($tmp);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'application/pdf' => 'pdf',
    ];

    if (!isset($allowed[$detectedMime])) {
        throw new RuntimeException('Invalid file type. Allowed: JPG, PNG, PDF.');
    }

    $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo((string)$file['name'], PATHINFO_FILENAME));
    $safeBase = $safeBase ?: 'evidence';
    $ext = $allowed[$detectedMime];
    $newName = time() . '_' . bin2hex(random_bytes(6)) . '_' . $safeBase . '.' . $ext;
    $target = rtrim($uploadsDir, '/\\') . DIRECTORY_SEPARATOR . $newName;

    if ($detectedMime === 'image/jpeg' || $detectedMime === 'image/png') {
        // ✅ UPDATED: basic compression optimization for uploaded images.
        if ($detectedMime === 'image/jpeg') {
            $img = @imagecreatefromjpeg($tmp);
            if (!$img || !imagejpeg($img, $target, 78)) {
                if (is_resource($img) || $img instanceof GdImage) { imagedestroy($img); }
                throw new RuntimeException('Failed to optimize JPEG image.');
            }
            if (is_resource($img) || $img instanceof GdImage) { imagedestroy($img); }
        } else {
            $img = @imagecreatefrompng($tmp);
            if (!$img || !imagepng($img, $target, 6)) {
                if (is_resource($img) || $img instanceof GdImage) { imagedestroy($img); }
                throw new RuntimeException('Failed to optimize PNG image.');
            }
            if (is_resource($img) || $img instanceof GdImage) { imagedestroy($img); }
        }
    } else {
        if (!move_uploaded_file($tmp, $target)) {
            throw new RuntimeException('Failed to store uploaded file.');
        }
    }

    return 'uploads/' . $newName;
};

try {
    if (isset($_FILES['evidence'])) {
        $uploadPath = $processEvidence($_FILES['evidence'], $uploadsDir);
    }

    $stateId = null;
    $districtId = null;

    // ✅ UPDATED: jurisdiction mapping when values are provided (backward compatible).
    if (!empty($_POST['state_id']) && ctype_digit((string)$_POST['state_id'])) {
        $stateId = (int)$_POST['state_id'];
    }
    if (!empty($_POST['district_id']) && ctype_digit((string)$_POST['district_id'])) {
        $districtId = (int)$_POST['district_id'];
    }

    $sql = 'INSERT INTO complaints(user_name,mobile,email,city,state_id,district_id,fraud_type,description,suspected_source,evidence_path,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $name,
        $mobile,
        $email,
        $city,
        $stateId,
        $districtId,
        $fraudType,
        $description,
        $suspectedSource,
        $uploadPath,
        'open'
    ]);

    header('Location: /complaint.php?success=1');
    exit;
} catch (Throwable $e) {
    header('Location: /complaint.php?error=' . urlencode('Unable to submit complaint right now. Please try again.'));
    exit;
}
