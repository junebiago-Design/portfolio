<?php
/**
 * MyCMS Installation Wizard
 * Run this script to set up MyCMS for the first time.
 * If the system is already installed, it will display a notice.
 */

// Prevent direct access if data/settings.json already exists (installed)
if (file_exists(__DIR__ . '/data/settings.json')) {
    $alreadyInstalled = true;
} else {
    $alreadyInstalled = false;
}

// Process form submission
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyInstalled) {
    // CSRF-like simple token (optional)
    $siteTitle      = trim($_POST['site_title'] ?? '');
    $tagline        = trim($_POST['tagline'] ?? '');
    $siteUrl        = rtrim(trim($_POST['site_url'] ?? ''), '/');
    $adminUsername  = trim($_POST['admin_username'] ?? '');
    $adminPassword  = $_POST['admin_password'] ?? '';
    $adminPassword2 = $_POST['admin_password2'] ?? '';
    $alertEmail     = trim($_POST['alert_email'] ?? '');
    $contactEnabled = isset($_POST['contact_enabled']) && $_POST['contact_enabled'] === '1';

    // Validation
    if (empty($siteTitle)) {
        $error = 'Site title is required.';
    } elseif (empty($adminUsername)) {
        $error = 'Admin username is required.';
    } elseif (strlen($adminPassword) < 4) {
        $error = 'Admin password must be at least 4 characters.';
    } elseif ($adminPassword !== $adminPassword2) {
        $error = 'Passwords do not match.';
    } elseif (!empty($alertEmail) && !filter_var($alertEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Alert email must be a valid email address (or leave empty).';
    } else {
        // Auto-detect site URL if not provided
        if (empty($siteUrl)) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            $siteUrl = $protocol . '://' . $host . $scriptDir;
        }
        $siteUrl = rtrim($siteUrl, '/');

        // Data directory
        $dataDir = __DIR__ . '/data';
        if (!is_dir($dataDir)) {
            if (!mkdir($dataDir, 0755, true)) {
                $error = 'Cannot create data directory. Please check permissions.';
            }
        }

        if (empty($error)) {
            // Helper to write JSON
            function write_json($file, $data) {
                return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

            // 1. Settings
            $settings = [
                'siteTitle'         => $siteTitle,
                'tagline'           => $tagline,
                'homepageHeadline'  => '',
                'faviconUrl'        => '',
                'logoUrl'           => '',
                'username'          => $adminUsername,
                'passwordHash'      => base64_encode($adminPassword),
                'homepageDisplay'   => 'all',
                'siteUrl'           => $siteUrl,
                'socialFacebook'    => '',
                'socialInstagram'   => '',
                'socialLinkedin'    => '',
                'socialBehance'     => '',
                'socialMail'        => '',
                'contactEnabled'    => $contactEnabled,
                'contactAlertEmail' => $alertEmail,
                'contactSubjects'   => ['General Inquiry', 'Support', 'Partnership', 'Feedback', 'Other'],
                'contactAutoReply'  => true,
                'contactAutoReplyMsg' => "Thank you for reaching out! We've received your message and will get back to you within 1-2 business days.",
                'contactPrivacyUrl' => '',
                'aboutContent'      => '<p>Welcome to <strong>' . htmlspecialchars($siteTitle) . '</strong>. This is your About page. You can edit this content in the admin area.</p>',
                'aboutGallery'      => [],
                'siteGallery'       => []
            ];
            write_json($dataDir . '/settings.json', $settings);

            // 2. Categories
            $categories = [
                ['id' => 'cat-1', 'name' => 'News',         'slug' => 'news'],
                ['id' => 'cat-2', 'name' => 'Tutorial',     'slug' => 'tutorial'],
                ['id' => 'cat-3', 'name' => 'Opinion',      'slug' => 'opinion'],
                ['id' => 'cat-4', 'name' => 'Ideas',        'slug' => 'ideas'],
                ['id' => 'cat-5', 'name' => 'Uncategorized', 'slug' => 'uncategorized']
            ];
            write_json($dataDir . '/categories.json', $categories);

            // 3. Posts
            $now = time();
            $posts = [
                [
                    'id'            => '1',
                    'title'         => 'Hello World!',
                    'slug'          => 'hello-world',
                    'content'       => '<p>Welcome to <strong>' . htmlspecialchars($siteTitle) . '</strong>. This is your first post. You can edit or delete it from the admin dashboard.</p>',
                    'excerpt'       => 'Welcome to ' . htmlspecialchars($siteTitle) . '.',
                    'featuredImage' => '',
                    'category'      => 'News',
                    'tags'          => ['welcome'],
                    'status'        => 'published',
                    'showOnHome'    => true,
                    'date'          => date('c', $now - 86400 * 3),
                    'author'        => $adminUsername,
                    'videos'        => [],
                    'gallery'       => [],
                    'shareCount'    => 0
                ],
                [
                    'id'            => '2',
                    'title'         => 'Getting Started with MyCMS',
                    'slug'          => 'getting-started',
                    'content'       => '<p>MyCMS makes it easy to create and manage content without a database. All data is stored as JSON files.</p><p>You can add videos, galleries, and more. Log in to the admin area to start building your site.</p>',
                    'excerpt'       => 'Learn how to use MyCMS, a flat‑file CMS.',
                    'featuredImage' => '',
                    'category'      => 'Tutorial',
                    'tags'          => ['guide'],
                    'status'        => 'published',
                    'showOnHome'    => true,
                    'date'          => date('c', $now - 86400),
                    'author'        => $adminUsername,
                    'videos'        => [],
                    'gallery'       => [],
                    'shareCount'    => 0
                ]
            ];
            write_json($dataDir . '/posts.json', $posts);

            // 4. Media
            write_json($dataDir . '/media.json', []);

            // 5. Contacts
            write_json($dataDir . '/contacts.json', []);

            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyCMS Installation</title>
    <style>
        :root {
            --wp-blue: #2271b1;
            --wp-blue-dark: #135e96;
            --wp-dark: #1d2327;
            --wp-gray-5: #f0f0f1;
            --wp-gray-10: #e5e5e5;
            --wp-gray-20: #c3c4c7;
            --wp-gray-50: #8c8f94;
            --radius: 6px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 24px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background: #f0f0f1;
            color: #1e1e1e;
        }
        .install-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .install-header {
            background: var(--wp-dark);
            color: #fff;
            padding: 28px 32px;
            text-align: center;
        }
        .install-header h1 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 600;
        }
        .install-header p {
            margin: 0;
            opacity: 0.8;
            font-size: 15px;
        }
        .install-body {
            padding: 32px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 6px;
            color: var(--wp-dark);
        }
        .required:after {
            content: " *";
            color: #d63638;
        }
        input, select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--wp-gray-20);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: inherit;
            transition: 0.15s;
        }
        input:focus {
            outline: none;
            border-color: var(--wp-blue);
            box-shadow: 0 0 0 1px var(--wp-blue);
        }
        .hint {
            font-size: 12px;
            color: var(--wp-gray-50);
            margin-top: 5px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }
        .checkbox-group input {
            width: auto;
            margin: 0;
        }
        .checkbox-group label {
            margin: 0;
            font-weight: normal;
        }
        .error {
            background: #fbdada;
            border-left: 4px solid #d63638;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #b32d2e;
        }
        .success {
            background: #edfaef;
            border-left: 4px solid #1f6b2c;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #1f6b2c;
        }
        .success a {
            color: #1f6b2c;
            font-weight: 600;
        }
        .buttons {
            margin-top: 28px;
        }
        button {
            background: var(--wp-blue);
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 15px;
            font-weight: 600;
            border-radius: var(--radius);
            cursor: pointer;
            transition: 0.15s;
            width: 100%;
        }
        button:hover {
            background: var(--wp-blue-dark);
        }
        hr {
            margin: 24px 0;
            border: none;
            border-top: 1px solid var(--wp-gray-10);
        }
        .already-installed {
            text-align: center;
            padding: 32px;
        }
        .already-installed svg {
            width: 60px;
            height: 60px;
            color: #d63638;
            margin-bottom: 16px;
        }
        .already-installed h2 {
            margin: 0 0 10px;
        }
        .already-installed p {
            color: var(--wp-gray-50);
        }
        .already-installed a {
            display: inline-block;
            margin-top: 20px;
            background: var(--wp-blue);
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: var(--radius);
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="install-container">
    <div class="install-header">
        <h1>MyCMS Setup Wizard</h1>
        <p>Welcome! Let's get your site up and running in minutes.</p>
    </div>
    <div class="install-body">
        <?php if ($alreadyInstalled): ?>
            <div class="already-installed">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <h2>Already Installed</h2>
                <p>MyCMS appears to be already set up. If you need to reinstall, please delete the <code>data/</code> folder first.</p>
                <a href="index.php">Go to your site →</a>
            </div>
        <?php elseif ($success): ?>
            <div class="success">
                ✅ <strong>Installation complete!</strong> Your site is ready.
            </div>
            <p style="margin-top: 10px;">You can now log in to the admin area using the username and password you provided.</p>
            <div class="buttons">
                <a href="index.php#/login" style="display:block; text-align:center; background:var(--wp-blue); color:#fff; text-decoration:none; padding:10px; border-radius:var(--radius); font-weight:600;">Log In →</a>
            </div>
        <?php else: ?>
            <?php if ($error): ?>
                <div class="error">⚠️ <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label class="required">Site Title</label>
                    <input type="text" name="site_title" value="<?php echo isset($_POST['site_title']) ? htmlspecialchars($_POST['site_title']) : 'MyCMS'; ?>" required>
                    <div class="hint">Your site name, appears in browser tab and header.</div>
                </div>

                <div class="form-group">
                    <label>Tagline</label>
                    <input type="text" name="tagline" value="<?php echo isset($_POST['tagline']) ? htmlspecialchars($_POST['tagline']) : 'Just another MyCMS site'; ?>">
                    <div class="hint">A short description of your site (optional).</div>
                </div>

                <div class="form-group">
                    <label>Site URL</label>
                    <input type="url" name="site_url" value="<?php 
                        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
                        $autoUrl = $protocol . '://' . $host . $scriptDir;
                        echo isset($_POST['site_url']) ? htmlspecialchars($_POST['site_url']) : htmlspecialchars($autoUrl);
                    ?>" placeholder="https://example.com/mycms">
                    <div class="hint">Full URL of your installation (no trailing slash). Leave blank to auto-detect.</div>
                </div>

                <hr>

                <div class="form-group">
                    <label class="required">Admin Username</label>
                    <input type="text" name="admin_username" value="<?php echo isset($_POST['admin_username']) ? htmlspecialchars($_POST['admin_username']) : 'admin'; ?>" required>
                    <div class="hint">Username you will use to log into the admin area.</div>
                </div>

                <div class="form-group">
                    <label class="required">Admin Password</label>
                    <input type="password" name="admin_password" required>
                    <div class="hint">At least 4 characters.</div>
                </div>

                <div class="form-group">
                    <label class="required">Confirm Password</label>
                    <input type="password" name="admin_password2" required>
                </div>

                <hr>

                <div class="form-group">
                    <label>Alert Email (for contact form)</label>
                    <input type="email" name="alert_email" value="<?php echo isset($_POST['alert_email']) ? htmlspecialchars($_POST['alert_email']) : ''; ?>" placeholder="admin@example.com">
                    <div class="hint">When someone submits the contact form, a notification will be sent to this email. Leave blank to disable email alerts.</div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="contact_enabled" value="1" id="contact_enabled" <?php echo (!isset($_POST['contact_enabled']) || $_POST['contact_enabled'] == '1') ? 'checked' : ''; ?>>
                    <label for="contact_enabled">Enable contact form on the public site</label>
                </div>

                <div class="buttons">
                    <button type="submit">Install MyCMS</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>