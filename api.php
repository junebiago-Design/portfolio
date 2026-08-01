<?php
/**
 * api.php — MyCMS Backend
 * Handles: posts, categories, settings, session auth
 * Data stored in: data/posts.json, data/settings.json, data/categories.json
 */

session_start();
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

define('DATA_DIR', __DIR__ . '/data/');
define('POSTS_FILE',      DATA_DIR . 'posts.json');
define('SETTINGS_FILE',   DATA_DIR . 'settings.json');
define('CATEGORIES_FILE', DATA_DIR . 'categories.json');
define('MEDIA_FILE',      DATA_DIR . 'media.json');
define('CONTACTS_FILE',   DATA_DIR . 'contacts.json');

function json_response(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function read_json(string $file): array {
    if (!file_exists($file)) return [];
    $raw = file_get_contents($file);
    return json_decode($raw, true) ?: [];
}

function write_json(string $file, $data): bool {
    if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
    return (bool) file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function default_settings(): array {
    return [
        'siteTitle'         => 'MyCMS',
        'tagline'           => 'Just another MyCMS site',
        'homepageHeadline'  => '',
        'faviconUrl'        => '',
        'logoUrl'           => '',
        'username'          => 'admin',
        'passwordHash'      => base64_encode('admin123'),
        'homepageDisplay'   => 'all',
        'siteUrl'           => '',
        'socialFacebook'    => '',
        'socialInstagram'   => '',
        'socialLinkedin'    => '',
        'socialBehance'     => '',
        'socialMail'        => '',
        // Contact form settings
        'contactEnabled'    => true,
        'contactAlertEmail' => '',  // email to notify on new submission
        'contactSubjects'   => ['General Inquiry','Support','Partnership','Feedback','Other'],
        'contactAutoReply'  => true,
        'contactAutoReplyMsg' => "Thank you for reaching out! We've received your message and will get back to you within 1-2 business days.",
        'contactPrivacyUrl' => '',
    ];
}

function get_settings(): array {
    $s = read_json(SETTINGS_FILE);
    return $s ?: default_settings();
}

function is_logged_in(): bool { return !empty($_SESSION['logged_in']); }

function require_auth(): void {
    if (!is_logged_in()) json_response(['error' => 'Unauthorized'], 401);
}

function default_categories(): array {
    return [
        ['id' => 'cat-1', 'name' => 'News',         'slug' => 'news'],
        ['id' => 'cat-2', 'name' => 'Tutorial',      'slug' => 'tutorial'],
        ['id' => 'cat-3', 'name' => 'Opinion',       'slug' => 'opinion'],
        ['id' => 'cat-4', 'name' => 'Ideas',         'slug' => 'ideas'],
        ['id' => 'cat-5', 'name' => 'Uncategorized', 'slug' => 'uncategorized'],
    ];
}

function get_categories(): array {
    if (!file_exists(CATEGORIES_FILE)) {
        $cats = default_categories();
        write_json(CATEGORIES_FILE, $cats);
        return $cats;
    }
    return read_json(CATEGORIES_FILE) ?: default_categories();
}

function get_media(): array {
    return file_exists(MEDIA_FILE) ? (read_json(MEDIA_FILE) ?: []) : [];
}

function register_media_url(string $url): void {
    if (!$url) return;
    $media = get_media();
    if (!in_array($url, $media, true)) {
        array_unshift($media, $url);
        write_json(MEDIA_FILE, array_values($media));
    }
}

function default_posts(): array {
    $now = time();
    return [
        ['id'=>'1','title'=>'Hello World!','slug'=>'hello-world','content'=>'<p>Welcome to <strong>MyCMS</strong>.</p>','excerpt'=>'Welcome to MyCMS.','featuredImage'=>'','category'=>'News','tags'=>['welcome'],'status'=>'published','showOnHome'=>true,'date'=>date('c',$now-86400*5),'author'=>'admin'],
        ['id'=>'2','title'=>'Getting Started with MyCMS','slug'=>'getting-started','content'=>'<p>MyCMS makes it easy to create and manage content without a database.</p>','excerpt'=>'Learn how to create posts.','featuredImage'=>'','category'=>'Tutorial','tags'=>['guide'],'status'=>'published','showOnHome'=>true,'date'=>date('c',$now-86400*2),'author'=>'admin'],
        ['id'=>'3','title'=>'The Future of File-Based CMS','slug'=>'future-file-cms','content'=>'<p>JSON-file-based CMS platforms are fast, portable, and require no database setup.</p>','excerpt'=>'Exploring file-based tools.','featuredImage'=>'','category'=>'Opinion','tags'=>['future'],'status'=>'published','showOnHome'=>true,'date'=>date('c',$now-86400),'author'=>'admin'],
        ['id'=>'4','title'=>'Draft: Advanced Customization','slug'=>'draft-advanced-customization','content'=>'<p>This is a draft post.</p>','excerpt'=>'Work in progress','featuredImage'=>'','category'=>'Ideas','tags'=>['draft'],'status'=>'draft','showOnHome'=>false,'date'=>date('c',$now),'author'=>'admin'],
    ];
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$body = [];
if (in_array($method, ['POST','PUT','DELETE'])) {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true) ?: [];
}

switch ($action) {

    case 'login':
        if ($method !== 'POST') json_response(['error'=>'Method not allowed'],405);
        $s = get_settings();
        $user = trim($body['username'] ?? '');
        $pass = $body['password'] ?? '';
        if ($user === $s['username'] && base64_encode($pass) === $s['passwordHash']) {
            $_SESSION['logged_in'] = true;
            $_SESSION['username']  = $user;
            json_response(['ok'=>true,'username'=>$user]);
        }
        json_response(['error'=>'Invalid username or password'],401);

    case 'logout':
        session_destroy();
        json_response(['ok'=>true]);

    case 'session':
        if (is_logged_in()) json_response(['loggedIn'=>true,'username'=>$_SESSION['username']]);
        json_response(['loggedIn'=>false]);

    case 'posts':
        if ($method === 'GET') {
            if (!file_exists(POSTS_FILE)) write_json(POSTS_FILE, default_posts());
            json_response(['posts'=>read_json(POSTS_FILE)]);
        }
        require_auth();
        if ($method === 'POST') {
            $posts = read_json(POSTS_FILE) ?: default_posts();
            $post  = sanitize_post($body);
            $post['id'] = (string)(time().rand(100,999));
            array_unshift($posts, $post);
            write_json(POSTS_FILE, $posts);
            if ($post['featuredImage']) register_media_url($post['featuredImage']);
            foreach ($post['gallery'] as $u) register_media_url($u);
            json_response(['ok'=>true,'post'=>$post]);
        }
        if ($method === 'PUT') {
            $posts = read_json(POSTS_FILE);
            $id    = $body['id'] ?? '';
            $idx   = array_search($id, array_column($posts,'id'));
            if ($idx === false) json_response(['error'=>'Post not found'],404);
            $posts[$idx] = sanitize_post($body);
            write_json(POSTS_FILE, $posts);
            if ($posts[$idx]['featuredImage']) register_media_url($posts[$idx]['featuredImage']);
            foreach ($posts[$idx]['gallery'] as $u) register_media_url($u);
            json_response(['ok'=>true,'post'=>$posts[$idx]]);
        }
        if ($method === 'DELETE') {
            $posts = read_json(POSTS_FILE);
            $id    = $body['id'] ?? '';
            $posts = array_values(array_filter($posts, fn($p) => $p['id'] !== $id));
            write_json(POSTS_FILE, $posts);
            json_response(['ok'=>true]);
        }
        json_response(['error'=>'Method not allowed'],405);

    case 'categories':
        if ($method === 'GET') json_response(['categories'=>get_categories()]);
        require_auth();
        if ($method === 'POST') {
            $cats = get_categories();
            $name = trim($body['name'] ?? '');
            if (!$name) json_response(['error'=>'Name required'],400);
            foreach ($cats as $c) {
                if (strtolower($c['name']) === strtolower($name))
                    json_response(['error'=>'Category already exists'],409);
            }
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i','-',$name));
            $cat  = ['id'=>'cat-'.time().rand(10,99),'name'=>$name,'slug'=>$slug];
            $cats[] = $cat;
            write_json(CATEGORIES_FILE, $cats);
            json_response(['ok'=>true,'category'=>$cat]);
        }
        if ($method === 'PUT') {
            $cats = get_categories();
            $id   = $body['id'] ?? '';
            $name = trim($body['name'] ?? '');
            if (!$name) json_response(['error'=>'Name required'],400);
            $idx  = array_search($id, array_column($cats,'id'));
            if ($idx === false) json_response(['error'=>'Category not found'],404);
            $oldName = $cats[$idx]['name'];
            $slug    = strtolower(preg_replace('/[^a-z0-9]+/i','-',$name));
            $cats[$idx] = ['id'=>$id,'name'=>$name,'slug'=>$slug];
            write_json(CATEGORIES_FILE, $cats);
            if ($oldName !== $name) {
                $posts = read_json(POSTS_FILE);
                foreach ($posts as &$p) { if ($p['category'] === $oldName) $p['category'] = $name; }
                unset($p);
                write_json(POSTS_FILE, $posts);
            }
            json_response(['ok'=>true,'category'=>$cats[$idx]]);
        }
        if ($method === 'DELETE') {
            $cats = get_categories();
            $id   = $body['id'] ?? '';
            $cats = array_values(array_filter($cats, fn($c) => $c['id'] !== $id));
            write_json(CATEGORIES_FILE, $cats);
            json_response(['ok'=>true]);
        }
        json_response(['error'=>'Method not allowed'],405);

    case 'settings':
        if ($method === 'GET') {
            $s = get_settings(); unset($s['passwordHash']);
            json_response(['settings'=>$s]);
        }
        require_auth();
        if ($method === 'POST') {
            $current = get_settings();
            $current['siteTitle']        = trim($body['siteTitle']        ?? $current['siteTitle']);
            $current['tagline']          = trim($body['tagline']          ?? $current['tagline']);
            $current['homepageHeadline'] = trim($body['homepageHeadline'] ?? ($current['homepageHeadline'] ?? ''));
            $current['faviconUrl']       = trim($body['faviconUrl']       ?? ($current['faviconUrl'] ?? ''));
            $current['logoUrl']          = trim($body['logoUrl']          ?? ($current['logoUrl'] ?? ''));
            $current['username']         = trim($body['username']         ?? $current['username']);
            $current['homepageDisplay']  = in_array($body['homepageDisplay'] ?? '', ['all','selected'])
                                           ? $body['homepageDisplay']
                                           : ($current['homepageDisplay'] ?? 'all');
            if (!empty($body['newPassword'])) $current['passwordHash'] = base64_encode($body['newPassword']);
            // Site URL for social sharing
            if (array_key_exists('siteUrl', $body)) $current['siteUrl'] = rtrim(trim($body['siteUrl'] ?? ''), '/');
            // Social media profile links
            if (array_key_exists('socialFacebook',  $body)) $current['socialFacebook']  = trim($body['socialFacebook']  ?? '');
            if (array_key_exists('socialInstagram',  $body)) $current['socialInstagram']  = trim($body['socialInstagram']  ?? '');
            if (array_key_exists('socialLinkedin',   $body)) $current['socialLinkedin']   = trim($body['socialLinkedin']   ?? '');
            if (array_key_exists('socialBehance',    $body)) $current['socialBehance']    = trim($body['socialBehance']    ?? '');
            if (array_key_exists('socialMail',       $body)) $current['socialMail']       = trim($body['socialMail']       ?? '');
            // About page content and gallery
            if (array_key_exists('aboutContent', $body)) $current['aboutContent'] = $body['aboutContent'] ?? '';
            if (array_key_exists('aboutGallery', $body) && is_array($body['aboutGallery'])) {
                $urls = array_values(array_filter(array_map('trim', $body['aboutGallery'])));
                $current['aboutGallery'] = $urls;
                foreach ($urls as $u) register_media_url($u);
            }
            // Site gallery page
            if (array_key_exists('siteGallery', $body) && is_array($body['siteGallery'])) {
                $urls = array_values(array_filter(array_map('trim', $body['siteGallery'])));
                $current['siteGallery'] = $urls;
                foreach ($urls as $u) register_media_url($u);
            }
            // Contact form quick settings (contactAlertEmail, contactEnabled)
            if (array_key_exists('contactAlertEmail', $body)) $current['contactAlertEmail'] = trim($body['contactAlertEmail'] ?? '');
            if (array_key_exists('contactEnabled',    $body)) $current['contactEnabled']    = (bool)$body['contactEnabled'];
            write_json(SETTINGS_FILE, $current);
            $_SESSION['username'] = $current['username'];
            json_response(['ok'=>true]);
        }
        json_response(['error'=>'Method not allowed'],405);

    case 'media':
        if ($method === 'GET') {
            json_response(['media' => get_media()]);
        }
        require_auth();
        if ($method === 'POST') {
            // Register a URL manually
            $url = trim($body['url'] ?? '');
            if (!$url) json_response(['error' => 'URL required'], 400);
            register_media_url($url);
            json_response(['ok' => true]);
        }
        if ($method === 'DELETE') {
            $url   = $body['url'] ?? '';
            $media = get_media();
            $media = array_values(array_filter($media, fn($u) => $u !== $url));
            write_json(MEDIA_FILE, $media);
            json_response(['ok' => true]);
        }
        json_response(['error' => 'Method not allowed'], 405);

    case 'upload':
        require_auth();
        if ($method !== 'POST') json_response(['error'=>'Method not allowed'],405);
        if (empty($_FILES['image'])) json_response(['error'=>'No file uploaded'],400);
        $file = $_FILES['image'];
        $allowed  = ['image/jpeg','image/png','image/gif','image/webp'];
        $maxBytes = 5*1024*1024;
        if (!in_array($file['type'],$allowed)) json_response(['error'=>'Only JPG, PNG, GIF, WEBP allowed'],400);
        if ($file['size'] > $maxBytes) json_response(['error'=>'File too large (max 5 MB)'],400);
        if ($file['error'] !== UPLOAD_ERR_OK) json_response(['error'=>'Upload error code: '.$file['error']],500);
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = pathinfo($file['name'],PATHINFO_EXTENSION);
        $filename = uniqid('img_',true).'.'.strtolower($ext);
        $dest     = $uploadDir.$filename;
        if (!move_uploaded_file($file['tmp_name'],$dest)) json_response(['error'=>'Failed to save file'],500);
        $scheme = (!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';
        $host   = $_SERVER['HTTP_HOST'];
        $base   = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/\\');
        $url    = $scheme.'://'.$host.$base.'/uploads/'.$filename;
        register_media_url($url);
        json_response(['ok'=>true,'url'=>$url]);

    case 'debug':
        $scheme = (!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';
        $host   = $_SERVER['HTTP_HOST'];
        $base   = rtrim(dirname($_SERVER['SCRIPT_NAME']),'/\\');
        json_response(['scheme'=>$scheme,'host'=>$host,'base'=>$base,'uploads_url'=>$scheme.'://'.$host.$base.'/uploads/','script_name'=>$_SERVER['SCRIPT_NAME']]);

    case 'reset':
        require_auth();
        write_json(POSTS_FILE,      default_posts());
        write_json(SETTINGS_FILE,   default_settings());
        write_json(CATEGORIES_FILE, default_categories());
        write_json(MEDIA_FILE,      []);
        session_destroy();
        json_response(['ok'=>true]);

    // ── Contact: settings (admin only) ───────────────────────────────────────
    case 'contact_settings':
        require_auth();
        $s = get_settings();
        if ($method === 'GET') {
            json_response(['ok'=>true,'settings'=>[
                'contactEnabled'     => $s['contactEnabled']     ?? true,
                'contactAlertEmail'  => $s['contactAlertEmail']  ?? '',
                'contactSubjects'    => $s['contactSubjects']    ?? ['General Inquiry','Support','Partnership','Feedback','Other'],
                'contactAutoReply'   => $s['contactAutoReply']   ?? true,
                'contactAutoReplyMsg'=> $s['contactAutoReplyMsg']?? "Thank you for reaching out! We've received your message and will get back to you within 1-2 business days.",
                'contactPrivacyUrl'  => $s['contactPrivacyUrl']  ?? '',
            ]]);
        }
        if ($method === 'POST') {
            if (array_key_exists('contactEnabled',     $body)) $s['contactEnabled']     = (bool)$body['contactEnabled'];
            if (array_key_exists('contactAlertEmail',  $body)) $s['contactAlertEmail']  = trim($body['contactAlertEmail'] ?? '');
            if (array_key_exists('contactSubjects',    $body) && is_array($body['contactSubjects'])) {
                $s['contactSubjects'] = array_values(array_filter(array_map('trim', $body['contactSubjects'])));
            }
            if (array_key_exists('contactAutoReply',   $body)) $s['contactAutoReply']   = (bool)$body['contactAutoReply'];
            if (array_key_exists('contactAutoReplyMsg',$body)) $s['contactAutoReplyMsg'] = trim($body['contactAutoReplyMsg'] ?? '');
            if (array_key_exists('contactPrivacyUrl',  $body)) $s['contactPrivacyUrl']  = trim($body['contactPrivacyUrl'] ?? '');
            write_json(SETTINGS_FILE, $s);
            json_response(['ok'=>true]);
        }
        json_response(['error'=>'Method not allowed'],405);

    // ── Contact: inbox CRUD ───────────────────────────────────────────────────
    case 'contact':
        if ($method === 'POST') {
            // Public submission — validate honeypot
            if (!empty($body['_gotcha'])) { json_response(['ok'=>true]); } // silent honeypot
            $name    = trim($body['name']    ?? '');
            $email   = trim($body['email']   ?? '');
            $subject = trim($body['subject'] ?? '');
            $phone   = trim($body['phone']   ?? '');
            $orderid = trim($body['orderid'] ?? '');
            $message = trim($body['message'] ?? '');
            $consent = !empty($body['consent']);
            if (!$name || !$email || !$message || !$consent) {
                json_response(['error'=>'Please fill in all required fields.'],400);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                json_response(['error'=>'Invalid email address.'],400);
            }
            if (strlen($message) > 2000) {
                json_response(['error'=>'Message too long (max 2000 characters).'],400);
            }
            $contacts  = read_json(CONTACTS_FILE) ?: [];
            $entry = [
                'id'      => (string)(time().rand(100,999)),
                'name'    => htmlspecialchars($name,    ENT_QUOTES),
                'email'   => $email,
                'subject' => htmlspecialchars($subject, ENT_QUOTES),
                'phone'   => htmlspecialchars($phone,   ENT_QUOTES),
                'orderid' => htmlspecialchars($orderid, ENT_QUOTES),
                'message' => htmlspecialchars($message, ENT_QUOTES),
                'date'    => date('c'),
                'read'    => false,
            ];
            array_unshift($contacts, $entry);
            write_json(CONTACTS_FILE, $contacts);

            $s = get_settings();

            // Email alert to admin
            $alertTo = $s['contactAlertEmail'] ?? '';
            if ($alertTo && filter_var($alertTo, FILTER_VALIDATE_EMAIL)) {
                $siteName = $s['siteTitle'] ?? 'MyCMS';
                $subLine  = $subject ? " [{$subject}]" : '';
                $headers  = "From: noreply@{$_SERVER['HTTP_HOST']}\r\nReply-To: {$email}\r\nContent-Type: text/html; charset=UTF-8\r\n";
                $mailBody = "
<html><body style='font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px'>
<h2 style='color:#2271b1'>New Contact Form Submission — {$siteName}</h2>
<table style='width:100%;border-collapse:collapse'>
<tr><td style='padding:8px;border-bottom:1px solid #eee;font-weight:600;width:140px'>Name</td><td style='padding:8px;border-bottom:1px solid #eee'>{$entry['name']}</td></tr>
<tr><td style='padding:8px;border-bottom:1px solid #eee;font-weight:600'>Email</td><td style='padding:8px;border-bottom:1px solid #eee'><a href='mailto:{$entry['email']}'>{$entry['email']}</a></td></tr>"
.($entry['phone'] ? "<tr><td style='padding:8px;border-bottom:1px solid #eee;font-weight:600'>Phone</td><td style='padding:8px;border-bottom:1px solid #eee'>{$entry['phone']}</td></tr>" : '')
.($entry['subject'] ? "<tr><td style='padding:8px;border-bottom:1px solid #eee;font-weight:600'>Subject</td><td style='padding:8px;border-bottom:1px solid #eee'>{$entry['subject']}</td></tr>" : '')
.($entry['orderid'] ? "<tr><td style='padding:8px;border-bottom:1px solid #eee;font-weight:600'>Order/Account ID</td><td style='padding:8px;border-bottom:1px solid #eee'>{$entry['orderid']}</td></tr>" : '')
."<tr><td style='padding:8px;font-weight:600;vertical-align:top'>Message</td><td style='padding:8px'>" . nl2br($entry['message']) . "</td></tr>
</table>
<p style='margin-top:20px;font-size:12px;color:#888'>Submitted: " . date('r') . "</p>
</body></html>";
                @mail($alertTo, "New Contact Message{$subLine} — {$siteName}", $mailBody, $headers);
            }

            // Auto-reply to sender
            if (!empty($s['contactAutoReply'])) {
                $siteName = $s['siteTitle'] ?? 'MyCMS';
                $replyMsg = $s['contactAutoReplyMsg'] ?? "Thank you for reaching out!";
                $replyHeaders = "From: noreply@{$_SERVER['HTTP_HOST']}\r\nContent-Type: text/html; charset=UTF-8\r\n";
                $replyBody = "
<html><body style='font-family:sans-serif;max-width:600px;margin:0 auto;padding:20px'>
<h2 style='color:#2271b1'>{$siteName}</h2>
<p>Hi {$entry['name']},</p>
<p>" . nl2br(htmlspecialchars($replyMsg, ENT_QUOTES)) . "</p>
<p>For your records, here is a copy of your message:</p>
<blockquote style='border-left:3px solid #2271b1;padding:10px 16px;margin:16px 0;background:#f6f7f7;border-radius:0 6px 6px 0'>" . nl2br($entry['message']) . "</blockquote>
<p style='color:#888;font-size:12px'>This is an automated confirmation. Please do not reply to this email.</p>
</body></html>";
                @mail($email, "We received your message — {$siteName}", $replyBody, $replyHeaders);
            }

            json_response(['ok'=>true]);
        }

        // Admin-only operations below
        require_auth();
        if ($method === 'GET') {
            $contacts = read_json(CONTACTS_FILE) ?: [];
            $unread   = count(array_filter($contacts, fn($c) => !($c['read'] ?? false)));
            json_response(['ok'=>true,'contacts'=>$contacts,'unread'=>$unread]);
        }
        if ($method === 'PUT') {
            // Mark read/unread
            $id = $body['id'] ?? '';
            $contacts = read_json(CONTACTS_FILE) ?: [];
            $idx = array_search($id, array_column($contacts,'id'));
            if ($idx !== false) {
                $contacts[$idx]['read'] = (bool)($body['read'] ?? true);
                write_json(CONTACTS_FILE, $contacts);
            }
            json_response(['ok'=>true]);
        }
        if ($method === 'DELETE') {
            $id = $body['id'] ?? '';
            $contacts = read_json(CONTACTS_FILE) ?: [];
            $contacts = array_values(array_filter($contacts, fn($c) => $c['id'] !== $id));
            write_json(CONTACTS_FILE, $contacts);
            json_response(['ok'=>true]);
        }
        json_response(['error'=>'Method not allowed'],405);

    case 'share':
        // Public endpoint — no auth required. Atomically increments shareCount
        // on a single post and returns the new total.
        if ($method !== 'POST') json_response(['error'=>'Method not allowed'],405);
        $id    = trim($body['id'] ?? '');
        if (!$id) json_response(['error'=>'Post ID required'],400);
        $posts = read_json(POSTS_FILE);
        if (!$posts) json_response(['error'=>'No posts found'],404);
        $idx   = array_search($id, array_column($posts,'id'));
        if ($idx === false) json_response(['error'=>'Post not found'],404);
        $posts[$idx]['shareCount'] = (int)($posts[$idx]['shareCount'] ?? 0) + 1;
        write_json(POSTS_FILE, $posts);
        json_response(['ok'=>true,'shareCount'=>$posts[$idx]['shareCount']]);

    default:
        json_response(['error'=>'Unknown action'],400);
}

function sanitize_post(array $d): array {
    // Sanitize videos: array of {url, label}
    $videos = [];
    if (!empty($d['videos']) && is_array($d['videos'])) {
        foreach ($d['videos'] as $v) {
            $url = trim($v['url'] ?? '');
            if ($url) {
                $videos[] = [
                    'url'   => $url,
                    'label' => trim($v['label'] ?? ''),
                ];
            }
        }
    }
    // Sanitize gallery: array of URL strings
    $gallery = [];
    if (!empty($d['gallery']) && is_array($d['gallery'])) {
        foreach ($d['gallery'] as $g) {
            $url = trim((string)$g);
            if ($url) $gallery[] = $url;
        }
    }
    return [
        'id'            => $d['id']            ?? '',
        'title'         => trim($d['title']         ?? ''),
        'slug'          => trim($d['slug']          ?? ''),
        'content'       => $d['content']       ?? '',
        'excerpt'       => trim($d['excerpt']       ?? ''),
        'featuredImage' => trim($d['featuredImage'] ?? ''),
        'category'      => trim($d['category']      ?? 'Uncategorized'),
        'tags'          => array_values(array_filter(array_map('trim',(array)($d['tags']??[])))),
        'status'        => in_array($d['status']??'',['published','draft'])?$d['status']:'draft',
        'showOnHome'    => isset($d['showOnHome'])?(bool)$d['showOnHome']:true,
        'date'          => $d['date']          ?? date('c'),
        'author'        => trim($d['author']        ?? 'admin'),
        'videos'        => $videos,
        'gallery'       => $gallery,
        'shareCount'    => (int)($d['shareCount'] ?? 0),  // preserve share count through edits
    ];
}