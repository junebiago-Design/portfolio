<?php
/**
 * index.php — MyCMS Front-end
 * All data operations use fetch() → api.php (JSON files, no localStorage)
 *
 * Social sharing: share links use ?post=SLUG so PHP can inject Open Graph
 * meta tags server-side (hash fragments are never sent to the server).
 * When the page loads with ?post=SLUG the JS router is redirected to
 * #/post/SLUG automatically.
 */

// ── Installation check ─────────────────────────────────────────────
// If data/settings.json does not exist, redirect to install.php
// (unless we are already on install.php)
if (!file_exists(__DIR__ . '/data/settings.json') && basename($_SERVER['SCRIPT_NAME']) !== 'install.php') {
    header('Location: install.php');
    exit;
}

// ── Server-side Open Graph injection ─────────────────────────────────────────
$og = [];
$slug = isset($_GET['post']) ? preg_replace('/[^a-z0-9\-_]/i', '', $_GET['post']) : '';
if ($slug) {
    $postsFile    = __DIR__ . '/data/posts.json';
    $settingsFile = __DIR__ . '/data/settings.json';
    $posts        = file_exists($postsFile)    ? (json_decode(file_get_contents($postsFile),    true) ?: []) : [];
    $settings     = file_exists($settingsFile) ? (json_decode(file_get_contents($settingsFile), true) ?: []) : [];
    foreach ($posts as $p) {
        if (($p['slug'] ?? '') === $slug && ($p['status'] ?? '') === 'published') {
            $siteUrl   = rtrim($settings['siteUrl'] ?? '', '/');
            if (!$siteUrl) {
                $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $siteUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
                         . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            }
            $og['url']         = htmlspecialchars($siteUrl . '/?post=' . $slug,         ENT_QUOTES);
            $og['title']       = htmlspecialchars($p['title']   ?? '',                  ENT_QUOTES);
            $og['description'] = htmlspecialchars($p['excerpt']  ?? $p['title'] ?? '',  ENT_QUOTES);
            $og['image']       = htmlspecialchars($p['featuredImage'] ?? '',             ENT_QUOTES);
            $og['site_name']   = htmlspecialchars($settings['siteTitle'] ?? 'MyCMS',    ENT_QUOTES);
            break;
        }
    }
}
?><!DOCTYPE html>

<?php
/**
 * index.php — MyCMS Front-end
 * All data operations use fetch() → api.php (JSON files, no localStorage)
 *
 * Social sharing: share links use ?post=SLUG so PHP can inject Open Graph
 * meta tags server-side (hash fragments are never sent to the server).
 * When the page loads with ?post=SLUG the JS router is redirected to
 * #/post/SLUG automatically.
 */

// ── Server-side Open Graph injection ─────────────────────────────────────────
$og = [];
$slug = isset($_GET['post']) ? preg_replace('/[^a-z0-9\-_]/i', '', $_GET['post']) : '';
if ($slug) {
    $postsFile    = __DIR__ . '/data/posts.json';
    $settingsFile = __DIR__ . '/data/settings.json';
    $posts        = file_exists($postsFile)    ? (json_decode(file_get_contents($postsFile),    true) ?: []) : [];
    $settings     = file_exists($settingsFile) ? (json_decode(file_get_contents($settingsFile), true) ?: []) : [];
    foreach ($posts as $p) {
        if (($p['slug'] ?? '') === $slug && ($p['status'] ?? '') === 'published') {
            $siteUrl   = rtrim($settings['siteUrl'] ?? '', '/');
            if (!$siteUrl) {
                $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $siteUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
                         . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            }
            $og['url']         = htmlspecialchars($siteUrl . '/?post=' . $slug,         ENT_QUOTES);
            $og['title']       = htmlspecialchars($p['title']   ?? '',                  ENT_QUOTES);
            $og['description'] = htmlspecialchars($p['excerpt']  ?? $p['title'] ?? '',  ENT_QUOTES);
            $og['image']       = htmlspecialchars($p['featuredImage'] ?? '',             ENT_QUOTES);
            $og['site_name']   = htmlspecialchars($settings['siteTitle'] ?? 'MyCMS',    ENT_QUOTES);
            break;
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if (!empty($og)): ?>
<!-- Open Graph / social share meta tags (server-rendered for ?post=SLUG URLs) -->
<meta property="og:type"        content="article">
<meta property="og:title"       content="<?= $og['title'] ?>">
<meta property="og:description" content="<?= $og['description'] ?>">
<meta property="og:url"         content="<?= $og['url'] ?>">
<?php if ($og['image']): ?><meta property="og:image" content="<?= $og['image'] ?>"><?php endif; ?>
<meta property="og:site_name"   content="<?= $og['site_name'] ?>">
<!-- Twitter Card -->
<meta name="twitter:card"        content="<?= $og['image'] ? 'summary_large_image' : 'summary' ?>">
<meta name="twitter:title"       content="<?= $og['title'] ?>">
<meta name="twitter:description" content="<?= $og['description'] ?>">
<?php if ($og['image']): ?><meta name="twitter:image" content="<?= $og['image'] ?>"><?php endif; ?>
<?php endif; ?>
<title>MyCMS</title>
<style>
:root{
  --wp-blue:#2271b1;
  --wp-blue-dark:#135e96;
  --wp-dark:#1d2327;
  --wp-darker:#101417;
  --wp-gray-0:#f6f7f7;
  --wp-gray-5:#f0f0f1;
  --wp-gray-10:#e5e5e5;
  --wp-gray-20:#c3c4c7;
  --wp-gray-50:#8c8f94;
  --wp-gray-80:#2c3338;
  --radius:8px;
  --shadow:0 1px 2px rgba(0,0,0,.05);
}
*{box-sizing:border-box}
html,body{height:100%}
html{overflow-y:scroll;scrollbar-gutter:stable}
body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;color:#1e1e1e;background:#fff;-webkit-font-smoothing:antialiased}
a{color:var(--wp-blue);text-decoration:none}
a:hover{color:var(--wp-blue-dark)}
img{max-width:100%;height:auto}

/* ── Page loading overlay ──────────────────────────────────────────── */
#pageLoader{
  position:fixed;inset:0;
  background:#fff;
  z-index:99999;
  display:flex;align-items:center;justify-content:center;
  flex-direction:column;gap:18px;
  pointer-events:none;
  opacity:0;
  transition:opacity .18s ease;
}
#pageLoader.loader-visible{
  opacity:1;
  pointer-events:auto;
}
#pageLoader.loader-hiding{
  opacity:0;
  transition:opacity .28s ease;
}
/* ── Page-content fade ──────────────────────────────────────────────── */
#page-content{
  opacity:1;
  transition:opacity 150ms ease;
  will-change:opacity;
}
#page-content.page-fade-out{
  opacity:0;
  transition:opacity 150ms ease;
  pointer-events:none;
}
#page-content.page-fade-in{
  opacity:0;
}
#page-content.page-fade-in.page-visible{
  opacity:1;
  transition:opacity 180ms ease;
}

/* ── Transition logo ────────────────────────────────────────────────── */
#transition-logo-container{
  position:fixed;inset:0;
  display:flex;align-items:center;justify-content:center;
  z-index:9999;
  pointer-events:none;
  opacity:0;
  transition:opacity 120ms ease;
}
#transition-logo-container.active{
  opacity:1;
}
#transition-logo-container .tl-inner{
  display:flex;align-items:center;gap:12px;
  animation:tlPulse 1.1s cubic-bezier(.45,0,.55,1) infinite;
  transform-origin:center;
}
@keyframes tlPulse{
  0%,100%{transform:scale(1);   opacity:.72}
  50%     {transform:scale(1.07);opacity:1}
}
/* Image logo variant */
#transition-logo-container .tl-img{
  height:250px;width:auto;max-width:560px;object-fit:contain;
  display:block;
}
/* Text logo variant (dot + name) */
#transition-logo-container .tl-dot{
  width:10px;height:10px;border-radius:50%;
  background:var(--wp-blue,#2271b1);
  flex-shrink:0;
}
#transition-logo-container .tl-name{
  font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
  font-size:20px;font-weight:700;letter-spacing:-.3px;
  color:var(--wp-dark,#1e1e1e);
}

/* Spinner ring */
.loader-ring{
  width:44px;height:44px;
  border:3.5px solid var(--wp-gray-10);
  border-top-color:var(--wp-blue);
  border-radius:50%;
  animation:loaderSpin .7s linear infinite;
}
@keyframes loaderSpin{to{transform:rotate(360deg)}}
/* Subtle pulsing label */
.loader-label{
  font-size:13px;font-weight:500;
  color:var(--wp-gray-50);
  letter-spacing:.3px;
  animation:loaderPulse 1.4s ease-in-out infinite;
}
@keyframes loaderPulse{0%,100%{opacity:.5}50%{opacity:1}}
/* Focus outline: only show on keyboard nav, not programmatic focus */
.js-focus-target:focus{outline:none}
.js-focus-target:focus-visible{outline:2px solid var(--wp-blue);outline-offset:3px;border-radius:3px}
/* Suppress blue border/outline on any element focused programmatically (not by keyboard) */
:focus:not(:focus-visible){outline:none!important;box-shadow:none!important}

/* Public */
.site-header{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(255,255,255,.92);backdrop-filter:saturate(180%) blur(10px);border-bottom:1px solid var(--wp-gray-10)}
.container{max-width:1200px;margin:0 auto;padding:0 24px}
.header-inner{display:flex;align-items:center;gap:28px;height:80px}
.logo{font-size:24px;font-weight:800;letter-spacing:-.5px;color:var(--wp-dark);display:flex;align-items:center;gap:8px}
.logo-dot{width:10px;height:10px;border-radius:50%;background:var(--wp-blue);display:inline-block}
.logo-img{height:52px;width:auto;max-width:200px;object-fit:contain;display:block}
.logo-site-title{font-size:34px;font-weight:800;letter-spacing:-.3px;color:var(--wp-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px}
.nav{display:flex;gap:24px}
.nav a{color:#50575e;font-weight:500;font-size:15px;padding:8px 0;position:relative}
.nav a.active,.nav a:hover{color:var(--wp-dark)}
.nav a.active::after{content:'';position:absolute;left:0;right:0;bottom:-2px;height:2px;background:var(--wp-dark);border-radius:2px}
.search-box{margin-left:auto;position:relative;z-index:200}
.search-box input{width:220px;padding:9px 36px 9px 14px;border:1px solid var(--wp-gray-20);border-radius:24px;background:#fff;font-size:14px;transition:width .2s}
.search-box input:focus{outline:none;border-color:var(--wp-blue);box-shadow:0 0 0 1px var(--wp-blue);width:280px}
.search-box button{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;padding:4px;cursor:pointer;color:var(--wp-gray-50)}
/* ── Autocomplete dropdown ───────────────────────────────────────── */
.search-ac-wrap{
  position:fixed;          /* fixed so it's never clipped by overflow:hidden ancestors */
  background:#fff;
  border:1px solid var(--wp-gray-10);
  border-radius:12px;
  box-shadow:0 8px 32px rgba(0,0,0,.13),0 2px 8px rgba(0,0,0,.07);
  overflow-y:auto;
  max-height:min(420px,70vh);
  z-index:9900;
  display:none;
  width:360px;             /* fixed width; JS clamps to viewport */
}
@keyframes acIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.search-ac-wrap.ac-open{display:block;animation:acIn .15s cubic-bezier(.22,1,.36,1)}
.ac-item{display:flex;align-items:flex-start;gap:10px;padding:10px 14px;cursor:pointer;border-bottom:1px solid var(--wp-gray-5);transition:background .1s;text-decoration:none;color:inherit}
.ac-item:last-child{border-bottom:none}
.ac-item:hover,.ac-item.ac-active{background:var(--wp-gray-0)}
.ac-item-thumb{width:38px;height:38px;border-radius:6px;object-fit:cover;flex-shrink:0;background:var(--wp-gray-10)}
.ac-item-thumb-placeholder{width:38px;height:38px;border-radius:6px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:16px}
.ac-item-body{flex:1;min-width:0}
.ac-item-title{font-size:13.5px;font-weight:600;color:var(--wp-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.3}
.ac-item-title mark{background:none;color:var(--wp-blue);font-weight:700;padding:0}
.ac-item-meta{font-size:11.5px;color:var(--wp-gray-50);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ac-item-snippet{font-size:12px;color:#50575e;margin-top:3px;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.ac-item-snippet mark{background:rgba(34,113,177,.12);color:var(--wp-blue);border-radius:2px;padding:0 1px;font-weight:600}
.ac-footer{padding:9px 14px;font-size:12.5px;color:var(--wp-blue);font-weight:600;cursor:pointer;text-align:center;background:var(--wp-gray-0);border-top:1px solid var(--wp-gray-10);transition:background .1s}
.ac-footer:hover{background:var(--wp-gray-5)}
.ac-empty{padding:18px 14px;text-align:center;font-size:13px;color:var(--wp-gray-50)}
/* Mobile autocomplete dropdown */
.mobile-ac-wrap{
  position:fixed;
  left:12px;right:12px;
  background:#1a2330;
  border:1px solid rgba(255,255,255,.10);
  border-radius:14px;
  box-shadow:0 12px 40px rgba(0,0,0,.45),0 3px 10px rgba(0,0,0,.3);
  overflow-y:auto;
  max-height:min(380px,55vh);  /* respects landscape keyboard squeeze */
  z-index:9999;
  display:none;
  animation:acIn .18s cubic-bezier(.22,1,.36,1);
  -webkit-overflow-scrolling:touch;
}
.mobile-ac-wrap.ac-open{display:block}
.mobile-ac-wrap .ac-item{border-bottom:1px solid rgba(255,255,255,.06);color:#e8eaed}
.mobile-ac-wrap .ac-item:last-child{border-bottom:none}
.mobile-ac-wrap .ac-item:hover,
.mobile-ac-wrap .ac-item.ac-active{background:rgba(255,255,255,.07)}
.mobile-ac-wrap .ac-item-title{color:#f1f3f4}
.mobile-ac-wrap .ac-item-title mark{color:#7ab8f5;background:none}
.mobile-ac-wrap .ac-item-meta{color:rgba(255,255,255,.42)}
.mobile-ac-wrap .ac-item-snippet{color:rgba(255,255,255,.58)}
.mobile-ac-wrap .ac-item-snippet mark{background:rgba(122,184,245,.18);color:#7ab8f5}
.mobile-ac-wrap .ac-footer{background:rgba(255,255,255,.05);border-top:1px solid rgba(255,255,255,.08);color:#7ab8f5}
.mobile-ac-wrap .ac-footer:hover{background:rgba(255,255,255,.10)}
.mobile-ac-wrap .ac-empty{color:rgba(255,255,255,.42)}
.mobile-toggle{display:none;background:none;border:none;font-size:24px;cursor:pointer;color:var(--wp-dark);padding:4px 8px;border-radius:4px;transition:background .15s;text-shadow:0 0 0.6px currentColor,0 0 0.6px currentColor;-webkit-text-stroke:0.5px currentColor}
.mobile-toggle{transition:background .18s,transform .15s,box-shadow .15s}
.mobile-toggle:hover{background:var(--wp-gray-10);transform:scale(1.13);box-shadow:0 2px 8px rgba(0,0,0,.10)}
.mobile-toggle:active{transform:scale(.92)}

/* ── Mobile search icon button ───────────────────────────────── */
.mobile-search-toggle{
  display:none;
  background:#000;
  border:none;border-radius:50%;
  width:38px;height:38px;
  align-items:center;justify-content:center;
  cursor:pointer;color:#fff;flex-shrink:0;
  box-shadow:0 2px 10px rgba(0,0,0,.45);
  transition:background .15s,transform .12s,box-shadow .15s;
}
.mobile-search-toggle:hover{background:#222;box-shadow:0 3px 14px rgba(0,0,0,.55)}
.mobile-search-toggle:active{transform:scale(.9)}

/* ── Mobile search expandable bar ───────────────────────────── */
@keyframes mobileSearchIn{
  from{opacity:0}
  to  {opacity:1}
}
@keyframes mobileSearchOut{
  from{opacity:1}
  to  {opacity:0}
}
.mobile-search-wrap{
  display:none;
  flex:1;min-width:0;overflow:visible;
  margin-right:4px;
  position:relative;z-index:200;
}
.mobile-search-wrap.ms-open{
  animation:mobileSearchIn .5s ease forwards;
}
.mobile-search-wrap.ms-closing{
  animation:mobileSearchOut .5s ease forwards;
  pointer-events:none;
}
.mobile-search-form{
  display:flex;align-items:center;gap:4px;
  background:var(--wp-dark);
  border-radius:24px;
  padding:0 8px 0 14px;
  height:40px;width:100%;
}
.mobile-search-input{
  flex:1;background:transparent;border:none;
  color:#fff;font-size:15px;font-weight:400;
  outline:none;min-width:0;padding:0;
  caret-color:#fff;
}
.mobile-search-input::placeholder{color:rgba(255,255,255,.42);font-weight:400}
.mobile-search-input::-webkit-search-cancel-button{-webkit-appearance:none}
.mobile-search-clear{
  background:none;border:none;
  color:rgba(255,255,255,.5);
  font-size:15px;cursor:pointer;
  padding:6px;line-height:1;flex-shrink:0;
  transition:color .15s;border-radius:50%;
}
.mobile-search-clear:hover{color:#fff;background:rgba(255,255,255,.1)}
/* Mobile drawer */
.mobile-drawer{display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:999}
.mobile-drawer.open,.mobile-drawer.closing{display:block}

/* ── Keyframes ───────────────────────────────────────────── */
/* Panel: slide right→left with opacity 10%→100% */
@keyframes drawerPanelIn{
  from{transform:translateX(100%);opacity:.1}
  to  {transform:translateX(0);   opacity:1}
}
@keyframes drawerPanelOut{
  from{transform:translateX(0);   opacity:1}
  to  {transform:translateX(100%);opacity:0}
}
/* Overlay fade */
@keyframes drawerOverlayIn{
  from{opacity:0}to{opacity:1}
}
@keyframes drawerOverlayOut{
  from{opacity:1}to{opacity:0}
}
/* Header slide down */
@keyframes drawerHeaderIn{
  from{opacity:0;transform:translateY(-12px)}
  to  {opacity:1;transform:translateY(0)}
}
/* Nav items slide up */
@keyframes drawerNavItemUp{
  from{opacity:0;transform:translateY(20px)}
  to  {opacity:1;transform:translateY(0)}
}

/* ── Overlay ─────────────────────────────────────────────── */
.mobile-drawer-overlay{
  position:absolute;inset:0;background:rgba(0,0,0,.45);
  opacity:0;
}
.mobile-drawer.open    .mobile-drawer-overlay{animation:drawerOverlayIn  .3s ease forwards}
.mobile-drawer.closing .mobile-drawer-overlay{animation:drawerOverlayOut .28s ease forwards}

/* ── Panel ───────────────────────────────────────────────── */
.mobile-drawer-panel{
  position:absolute;top:0;right:0;bottom:0;width:280px;
  background:#1d2327;box-shadow:-4px 0 24px rgba(0,0,0,.45);
  display:flex;flex-direction:column;
  transform:translateX(100%);opacity:.1;
}
.mobile-drawer.open    .mobile-drawer-panel{animation:drawerPanelIn  .38s cubic-bezier(.4,0,.2,1) forwards}
.mobile-drawer.closing .mobile-drawer-panel{animation:drawerPanelOut .3s  cubic-bezier(.4,0,.2,1) forwards}

/* ── Drawer header ───────────────────────────────────────── */
.mobile-drawer.open .mobile-drawer-header{
  opacity:0;
  animation:drawerHeaderIn .32s cubic-bezier(.22,1,.36,1) .12s forwards;
}

/* ── Nav items staggered slide-up ────────────────────────── */
.mobile-drawer.open .mobile-drawer-nav > *{
  opacity:0;
  animation:drawerNavItemUp .45s cubic-bezier(.22,1,.36,1) forwards;
}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(1) {animation-delay:.18s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(2) {animation-delay:.23s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(3) {animation-delay:.28s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(4) {animation-delay:.33s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(5) {animation-delay:.38s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(6) {animation-delay:.43s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(7) {animation-delay:.48s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(8) {animation-delay:.53s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(9) {animation-delay:.58s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(10){animation-delay:.63s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(11){animation-delay:.68s}
.mobile-drawer.open .mobile-drawer-nav > *:nth-child(12){animation-delay:.73s}
.mobile-drawer-header{display:flex;align-items:center;justify-content:flex-end;padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.10)}
.mobile-drawer-close{background:none;border:none;font-size:22px;cursor:pointer;color:rgba(255,255,255,.55);padding:6px 8px;line-height:1;border-radius:4px;transition:background .15s,color .15s}
.mobile-drawer-close:hover{background:rgba(255,255,255,.10);color:#fff}
.mobile-drawer-nav{flex:1;overflow-y:auto;padding:12px 0}
.mobile-drawer-nav a{display:flex;align-items:center;padding:16px 24px;font-size:18px;font-weight:600;color:#fff;border-left:3px solid transparent;transition:background .18s,color .18s,border-color .18s,transform .15s;letter-spacing:-.3px}
.mobile-drawer-nav a:hover,.mobile-drawer-nav a.active{color:#fff;background:rgba(255,255,255,.10);border-left-color:rgba(255,255,255,.7);transform:translateX(3px)}
.mobile-drawer-sep{height:1px;background:rgba(255,255,255,.10);margin:8px 20px}
.mobile-drawer-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:rgba(255,255,255,.38);padding:12px 24px 4px}

.main{padding:128px 0 80px;min-height:70vh}
.page-head{max-width:760px;margin:0 auto 40px;text-align:center}
.page-title{font-size:48px;line-height:1.1;margin:0 0 12px;letter-spacing:-1px}
.page-desc{font-size:18px;color:#50575e;margin:0}

.post-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:28px}
.post-card{background:#fff;border:1px solid var(--wp-gray-10);border-radius:12px;overflow:hidden;cursor:pointer;transition:transform .15s,box-shadow .15s;box-shadow:0 1px 3px rgba(0,0,0,.06)}
.post-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.1)}
@keyframes cardReveal{from{opacity:0;transform:translateY(36px)}to{opacity:1;transform:translateY(0)}}
.post-card.card-reveal{opacity:0;animation:cardReveal .55s cubic-bezier(.22,1,.36,1) forwards}
.post-card-img-wrap{height:200px;overflow:hidden;background:var(--wp-gray-5)}
.post-card-img{width:100%;height:100%;object-fit:cover}
.post-card-body{padding:20px}
.post-card-cat{font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--wp-blue);margin-bottom:8px;cursor:pointer}
.post-card-cat:hover{color:var(--wp-blue-dark)}
.post-card-title{font-size:20px;font-weight:700;line-height:1.3;margin:0 0 10px;color:#1e1e1e}
.post-card-excerpt{font-size:14px;color:#50575e;margin:0 0 14px;line-height:1.6;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.post-card-meta{font-size:13px;color:var(--wp-gray-50);display:flex;gap:8px;align-items:center}

.article-wrap{max-width:760px;margin:0 auto}
.article-hero{width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:12px;margin-bottom:28px}
.article-cat{font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--wp-blue);display:block;margin-bottom:12px}
.article-title{font-size:42px;line-height:1.15;margin:0 0 16px;letter-spacing:-.5px}
.article-meta{font-size:14px;color:var(--wp-gray-50);margin-bottom:32px;display:flex;gap:8px;align-items:center;padding-bottom:24px;border-bottom:1px solid var(--wp-gray-10)}
.article-content{font-size:17px;line-height:1.8;color:#2c3338}
.article-content h2{font-size:26px;margin:32px 0 12px}
.article-content h3{font-size:20px;margin:24px 0 10px}
.article-content blockquote{border-left:4px solid var(--wp-blue);margin:24px 0;padding:12px 20px;background:var(--wp-gray-0);color:#50575e;font-style:italic;border-radius:0 4px 4px 0}
.article-content img{border-radius:8px;margin:16px 0}
.article-content a{color:var(--wp-blue)}
.cat-social{display:flex;justify-content:center;gap:14px;margin:14px 0 0}
.cat-social a{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:6px;transition:opacity .18s,transform .15s;text-decoration:none}
.cat-social a:hover{opacity:.8;transform:translateY(-2px)}
.cat-social-fb{background:#1877f2}
.cat-social-ig{background:radial-gradient(circle at 30% 107%,#fdf497 0%,#fdf497 5%,#fd5949 45%,#d6249f 60%,#285aeb 90%)}
.cat-social-li{background:#0a66c2}
.cat-social-mail{background:#ea4335}
.cat-social-be{background:#1769ff}
.article-content a.inline-link-btn{display:inline-flex;align-items:center;gap:5px;background:var(--wp-blue);color:#fff!important;padding:3px 10px 3px 8px;border-radius:4px;font-size:.88em;font-weight:600;text-decoration:none!important;transition:background .15s}
.article-content a.inline-link-btn:hover{background:var(--wp-blue-dark)}
.article-content code{background:var(--wp-gray-5);padding:2px 6px;border-radius:3px;font-size:.9em}

/* ── Share buttons ──────────────────────────────────────────────── */
.share-bar{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:32px;padding:20px 0;border-top:1px solid var(--wp-gray-10)}
.share-bar-label{font-size:13px;font-weight:600;color:var(--wp-gray-50);text-transform:uppercase;letter-spacing:.4px;margin-right:4px;flex-shrink:0}
.share-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:24px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:filter .15s,transform .12s,box-shadow .15s;white-space:nowrap;line-height:1}
.share-btn:hover{filter:brightness(1.08);transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,.18)}
.share-btn:active{transform:scale(.95)}
.share-btn svg{flex-shrink:0}
.share-btn-facebook{background:#1877f2;color:#fff}
.share-btn-twitter{background:#000;color:#fff}
.share-btn-whatsapp{background:#25d366;color:#fff}
.share-btn-messenger{background:#0084ff;color:#fff}
.share-counter{display:inline-flex;align-items:center;gap:6px;margin-left:auto;font-size:13px;font-weight:600;color:var(--wp-gray-50);flex-shrink:0;white-space:nowrap}
.share-counter svg{flex-shrink:0}
.share-counter-num{color:var(--wp-dark);font-size:15px;font-weight:700;min-width:1ch;transition:transform .2s cubic-bezier(.34,1.9,.64,1),color .2s}
.share-counter-num.bump{transform:scale(1.4);color:var(--wp-blue)}
@media(max-width:600px){
  .share-bar{gap:8px}
  .share-btn{padding:8px 13px;font-size:12px}
  .share-counter{margin-left:0;width:100%;justify-content:flex-end}
}

/* ── Article scroll-reveal ──────────────────────────────────── */
/* Base: hidden, shifted down */
.scroll-reveal{
  opacity:0;
  transform:translateY(36px);
  transition:opacity .6s cubic-bezier(.22,1,.36,1),
             transform .6s cubic-bezier(.22,1,.36,1);
  will-change:opacity,transform;
}
/* Triggered: fully visible */
.scroll-reveal.is-visible{
  opacity:1;
  transform:translateY(0);
}
/* Slight left-indent variant for content blocks */
.scroll-reveal.sr-content{
  transform:translateY(28px);
}
/* Image/media variant: also scales up slightly */
.scroll-reveal.sr-media{
  transform:translateY(24px) scale(.98);
  transition:opacity .65s cubic-bezier(.22,1,.36,1),
             transform .65s cubic-bezier(.22,1,.36,1);
}
.scroll-reveal.sr-media.is-visible{
  transform:translateY(0) scale(1);
}

/* Video embeds */
.post-video-hero{margin-bottom:32px}
.post-video-hero .video-item-label{font-size:13px;font-weight:600;color:#50575e;margin-bottom:8px;display:flex;align-items:center;gap:6px}
.post-video-hero .video-item-label::before{content:'▶';font-size:10px;color:var(--wp-blue)}
.post-videos{margin:32px 0}
.post-videos h3{font-size:20px;font-weight:700;margin:0 0 16px;color:#1e1e1e}
.video-item{margin-bottom:28px}
.video-item-label{font-size:13px;font-weight:600;color:#50575e;margin-bottom:8px;display:flex;align-items:center;gap:6px}
.video-item-label::before{content:'▶';font-size:10px;color:var(--wp-blue)}
.video-embed-wrap{position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:12px;background:#0d0d0d;box-shadow:0 4px 20px rgba(0,0,0,.18)}
.video-embed-wrap iframe,.video-embed-wrap video{position:absolute;top:0;left:0;width:100%;height:100%;border:none;border-radius:12px}
/* Custom player shell for unknown URLs */
.video-player-shell{border-radius:12px;overflow:hidden;background:#0d0d0d;box-shadow:0 4px 20px rgba(0,0,0,.18)}
.video-player-toolbar{display:flex;align-items:center;gap:10px;padding:8px 14px;background:rgba(255,255,255,.05);border-bottom:1px solid rgba(255,255,255,.07)}
.video-player-badge{font-size:11px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:rgba(255,255,255,.45);background:rgba(255,255,255,.08);padding:3px 8px;border-radius:20px}
.video-player-url{font-size:12px;color:rgba(255,255,255,.35);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1}
.video-player-openlink{font-size:11px;font-weight:600;color:var(--wp-blue);opacity:.8;flex-shrink:0;text-decoration:none}
.video-player-openlink:hover{opacity:1}
.video-player-frame-wrap{position:relative;padding-bottom:56.25%;height:0;overflow:hidden}
.video-player-frame-wrap iframe{position:absolute;top:0;left:0;width:100%;height:100%;border:none}
/* Logo + title overlay — top-left corner of the custom player */
.vp-overlay{
  position:absolute;top:12px;left:14px;
  display:flex;align-items:center;gap:8px;
  pointer-events:none;z-index:2147483647;
  background:rgba(0,0,0,.38);
  backdrop-filter:blur(6px);
  -webkit-backdrop-filter:blur(6px);
  padding:5px 10px 5px 7px;
  border-radius:20px;
  border:1px solid rgba(255,255,255,.10);
  max-width:calc(100% - 28px);
  transition:opacity .3s ease;
}
.vp-overlay-logo{
  height:22px;width:auto;max-width:80px;
  object-fit:contain;display:block;flex-shrink:0;
  filter:drop-shadow(0 1px 2px rgba(0,0,0,.5));
}
.vp-overlay-title{
  font-size:12px;font-weight:700;
  color:rgba(255,255,255,.90);
  letter-spacing:.2px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  text-shadow:0 1px 3px rgba(0,0,0,.6);
}
/* ── Fullscreen: keep overlay visible when frame-wrap enters fullscreen ── */
.video-player-frame-wrap:fullscreen,
.video-player-frame-wrap:-webkit-full-screen,
.video-player-frame-wrap:-moz-full-screen {
  background:#000;padding-bottom:0;height:100%;width:100%;
}
.video-player-frame-wrap:fullscreen .vp-overlay,
.video-player-frame-wrap:-webkit-full-screen .vp-overlay,
.video-player-frame-wrap:-moz-full-screen .vp-overlay {
  position:fixed;top:16px;left:18px;
}
/* JS applies this class when iframe itself is the fullscreen element */
.vp-overlay.vp-fullscreen-active {
  position:fixed;top:16px;left:18px;
}
.video-link-fallback{display:flex;align-items:center;gap:10px;padding:14px 16px;background:var(--wp-gray-0);border:1px solid var(--wp-gray-10);border-radius:8px;color:var(--wp-blue);font-size:14px;font-weight:500}
.video-link-fallback svg{flex-shrink:0}

/* Gallery */
.post-gallery{margin:32px 0}
.post-gallery h3{font-size:20px;font-weight:700;margin:0 0 16px;color:#1e1e1e}
.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;overflow:visible}

/* Odd image count layout */
/* Desktop: wrapper divs are invisible to the grid — images flow normally */
.gallery-grid--odd .gallery-first-row,
.gallery-grid--odd .gallery-rest{display:contents}

/* Mobile only: force 2-col grid, and odd layout special treatment */
@media(max-width:600px){
  /* Even count: force 2-col instead of 1-col from minmax(200px) */
  .gallery-grid{grid-template-columns:repeat(2,1fr)}

  /* Odd count: first image centred, rest in 2-col grid */
  .gallery-grid--odd{display:flex;flex-direction:column;gap:12px}
  .gallery-grid--odd .gallery-first-row{display:flex;justify-content:center}
  .gallery-grid--odd .gallery-first-row .gallery-img{
    width:70%;max-width:300px;aspect-ratio:1;
  }
  .gallery-grid--odd .gallery-rest{
    display:grid;grid-template-columns:repeat(2,1fr);gap:12px;
  }
}

.gallery-img{
  width:100%;aspect-ratio:1;object-fit:cover;border-radius:8px;cursor:pointer;
  transform:scale(1);
  transition:transform .22s ease, box-shadow .22s ease, filter .22s ease;
  will-change:transform,filter;
  position:relative;z-index:1;
}
.gallery-img:hover{
  transform:scale(1.05);
  box-shadow:0 8px 24px rgba(0,0,0,.18);
  filter:brightness(1.04) saturate(1.08);
  z-index:2;
}

/* Lightbox backdrop */
@keyframes lbBackdropIn{from{opacity:0}to{opacity:1}}
.gallery-lightbox{
  position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;
  display:flex;align-items:center;justify-content:center;
  animation:lbBackdropIn .22s ease forwards;outline:none;
}

/* ── Lightbox stage: clips the sliding images ────────────────────────────── */
.gallery-lightbox-stage{
  position:relative;
  width:90vw;max-width:90vw;
  height:82vh;
  overflow:hidden;
  display:flex;align-items:center;justify-content:center;
  z-index:10000;
  flex-shrink:0;
}

/* ── Mobile: full-width stage, hide nav arrows (swipe only) ── */
@media(max-width:768px){
  .gallery-lightbox-stage{
    width:100vw;
    max-width:100vw;
    height:100dvh;          /* full viewport on mobile */
    touch-action:pan-y;     /* allow vertical scroll, intercept horizontal */
  }
  /* Hide prev/next arrows on mobile — swipe handles navigation */
  .gallery-lightbox-nav{ display:none !important; }
  /* Full-screen backdrop on mobile */
  .gallery-lightbox{ background:rgba(0,0,0,.97); }
  /* Counter pill: move up slightly from safe area */
  .gallery-lightbox-counter{
    bottom:max(24px, env(safe-area-inset-bottom, 20px));
  }
}

/* ── Swipe track: the horizontal strip that holds current + adjacent images ── */
/* Used ONLY during mobile touch; JS creates/destroys it dynamically           */
.lb-swipe-track{
  position:absolute;inset:0;
  display:flex;
  align-items:center;
  will-change:transform;
  /* JS drives translateX directly — no CSS transition here during drag */
}
.lb-swipe-track.lb-track-snap{
  transition:transform 300ms cubic-bezier(.25,.46,.45,.94);
}
.lb-swipe-track.lb-track-spring{
  /* Spring-back on cancel */
  transition:transform 320ms cubic-bezier(.34,1.3,.64,1);
}
/* Each slide cell inside the track */
.lb-swipe-cell{
  flex:0 0 100%;
  width:100%;
  height:100%;
  display:flex;
  align-items:center;
  justify-content:center;
  position:relative;
}
/* Images inside cells — no absolute positioning, let flexbox center them */
.lb-swipe-cell .gallery-lightbox-img{
  position:relative;
  left:auto;top:auto;
  translate:none;
  max-width:100%;
  max-height:100%;
  animation:none; /* entrance animation handled separately */
}

/* ── Lightbox image animations ───────────────────────────────────────────── */
/* First open: fade up from below */
@keyframes lbImgReveal{
  from{opacity:0;transform:translateY(40px) scale(.97)}
  to  {opacity:1;transform:translateY(0)    scale(1)}
}
/* ── Carousel: incoming slides ── */
/* Next (→): new image enters from right */
@keyframes lbCarouselInRight{
  from{opacity:0;transform:translateX(100%)}
  to  {opacity:1;transform:translateX(0)}
}
/* Prev (←): new image enters from left */
@keyframes lbCarouselInLeft{
  from{opacity:0;transform:translateX(-100%)}
  to  {opacity:1;transform:translateX(0)}
}
/* ── Carousel: outgoing slides ── */
/* Next (→): current image exits to left */
@keyframes lbCarouselOutLeft{
  from{opacity:1;transform:translateX(0)}
  to  {opacity:0;transform:translateX(-100%)}
}
/* Prev (←): current image exits to right */
@keyframes lbCarouselOutRight{
  from{opacity:1;transform:translateX(0)}
  to  {opacity:0;transform:translateX(100%)}
}
/* ── Shared image styles ── */
.gallery-lightbox-img{
  max-width:90vw;max-height:82vh;object-fit:contain;border-radius:8px;display:block;
  animation:lbImgReveal .55s cubic-bezier(.22,1,.36,1) forwards;
  user-select:none;-webkit-user-drag:none;
  position:absolute;z-index:10000;
  will-change:transform,opacity;
  left:50%;top:50%;translate:-50% -50%;
}
/* Enter animations */
.gallery-lightbox-img.lb-slide-next{animation:lbCarouselInRight .4s cubic-bezier(.25,.46,.45,.94) forwards}
.gallery-lightbox-img.lb-slide-prev{animation:lbCarouselInLeft  .4s cubic-bezier(.25,.46,.45,.94) forwards}
/* Exit animations — identical duration so both finish together */
.gallery-lightbox-img.lb-exit-left {animation:lbCarouselOutLeft  .4s cubic-bezier(.55,0,1,.8) forwards}
.gallery-lightbox-img.lb-exit-right{animation:lbCarouselOutRight .4s cubic-bezier(.55,0,1,.8) forwards}
/* Live drag — no animation, finger controls position directly */
.gallery-lightbox-img.lb-dragging  {animation:none!important;transition:none!important}

/* Close button */
.gallery-lightbox-close{
  position:absolute;top:16px;right:20px;color:#fff;font-size:22px;line-height:1;
  background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);
  border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;
  cursor:pointer;opacity:.85;transition:opacity .15s,background .15s;
}
.gallery-lightbox-close:hover{opacity:1;background:rgba(255,255,255,.2)}

/* Prev / Next arrows */
.gallery-lightbox-nav{
  position:absolute;top:50%;transform:translateY(-50%);color:#fff;font-size:36px;line-height:1;
  background:rgba(255,255,255,.18);border:2px solid rgba(255,255,255,.45);
  border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center;
  cursor:pointer;opacity:.82;transition:opacity .18s,background .18s,border-color .18s,box-shadow .18s,transform .15s;
  user-select:none;text-shadow:0 1px 4px rgba(0,0,0,.6);
  z-index:10001;
}
.gallery-lightbox-nav:hover{
  opacity:1;
  background:rgba(255,255,255,.38);
  border-color:rgba(255,255,255,.9);
  box-shadow:0 0 0 3px rgba(255,255,255,.18),0 4px 20px rgba(0,0,0,.55);
  transform:translateY(-50%) scale(1.08);
}
.gallery-lightbox-nav:active{transform:translateY(-50%) scale(.95)}
.gallery-lightbox-prev{left:16px}
.gallery-lightbox-next{right:16px}
.gallery-lightbox-nav[hidden]{display:none}
/* Mobile nav arrows hidden via .gallery-lightbox-stage media query above */

/* Counter pill */
.gallery-lightbox-counter{
  position:absolute;bottom:20px;left:50%;transform:translateX(-50%);
  color:rgba(255,255,255,.75);font-size:13px;font-weight:600;
  background:rgba(0,0,0,.45);padding:4px 14px;border-radius:20px;
  letter-spacing:.4px;pointer-events:none;
}

/* Video list editor (admin) */
.video-list-editor{display:flex;flex-direction:column;gap:10px}
.video-entry{display:flex;flex-direction:column;gap:6px;background:var(--wp-gray-0);border:1px solid var(--wp-gray-10);border-radius:6px;padding:10px}
.video-entry-row{display:flex;gap:6px;align-items:center}
.video-entry .btn-danger{padding:4px 8px;font-size:12px}

/* Gallery editor (admin) */
.gallery-editor-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:10px;margin-bottom:10px}
.gallery-editor-item{position:relative;aspect-ratio:1;border-radius:4px;overflow:hidden;border:1px solid var(--wp-gray-10);cursor:grab;user-select:none;transition:opacity .15s,box-shadow .15s}
.gallery-editor-item:active{cursor:grabbing}
.gallery-editor-item.drag-over{box-shadow:0 0 0 3px var(--wp-blue);opacity:1}
.gallery-editor-item.dragging{opacity:.35;cursor:grabbing}
.gallery-editor-item img{width:100%;height:100%;object-fit:cover;pointer-events:none}
.gallery-editor-item .remove-gallery-img{position:absolute;top:4px;right:4px;background:rgba(0,0,0,.6);color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:12px;cursor:pointer;line-height:1;display:flex;align-items:center;justify-content:center}
.gallery-drag-hint{font-size:11px;color:var(--wp-gray-50);display:flex;align-items:center;gap:4px;margin-bottom:6px}
.gallery-add-row{display:flex;gap:8px;align-items:center;flex-wrap:wrap}

.empty-state{text-align:center;padding:60px 20px;color:var(--wp-gray-50)}
.empty-state h3{font-size:22px;color:var(--wp-dark);margin-bottom:8px}

/* ── Contact form ──────────────────────────────────────────────────── */
.contact-wrap{max-width:680px;margin:0 auto;padding:40px 0 60px}
.contact-wrap h1{font-size:42px;font-weight:800;letter-spacing:-.5px;color:var(--wp-dark);margin:0 0 8px}
.contact-subtitle{font-size:17px;color:var(--wp-gray-50);margin:0 0 36px;line-height:1.5}
.contact-card{background:#fff;border:1px solid var(--wp-gray-20);border-radius:12px;padding:36px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
.contact-row{margin-bottom:22px}
.contact-row-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:22px}
.contact-label{display:flex;align-items:center;gap:6px;font-size:14px;font-weight:600;color:var(--wp-dark);margin-bottom:7px}
.contact-label .req{color:#b32d2e;font-size:12px}
.contact-label .opt{color:var(--wp-gray-50);font-size:12px;font-weight:400}
.contact-input{width:100%;padding:10px 14px;border:1px solid var(--wp-gray-20);border-radius:8px;font-size:15px;color:var(--wp-dark);background:#fff;transition:border-color .15s,box-shadow .15s;font-family:inherit}
.contact-input:focus{outline:none;border-color:var(--wp-blue);box-shadow:0 0 0 2px rgba(34,113,177,.15)}
.contact-input::placeholder{color:var(--wp-gray-50)}
.contact-select{width:100%;padding:10px 14px;border:1px solid var(--wp-gray-20);border-radius:8px;font-size:15px;color:var(--wp-dark);background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238c8f94' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 14px center;-webkit-appearance:none;appearance:none;transition:border-color .15s;font-family:inherit}
.contact-select:focus{outline:none;border-color:var(--wp-blue);box-shadow:0 0 0 2px rgba(34,113,177,.15)}
.contact-textarea{width:100%;padding:10px 14px;border:1px solid var(--wp-gray-20);border-radius:8px;font-size:15px;color:var(--wp-dark);background:#fff;resize:vertical;min-height:140px;font-family:inherit;transition:border-color .15s,box-shadow .15s}
.contact-textarea:focus{outline:none;border-color:var(--wp-blue);box-shadow:0 0 0 2px rgba(34,113,177,.15)}
.char-counter{text-align:right;font-size:12px;color:var(--wp-gray-50);margin-top:5px;transition:color .15s}
.char-counter.warn{color:#d63638}
.contact-consent{display:flex;align-items:flex-start;gap:10px;padding:14px 16px;background:var(--wp-gray-0);border:1px solid var(--wp-gray-10);border-radius:8px}
.contact-consent input[type=checkbox]{margin-top:2px;width:16px;height:16px;flex-shrink:0;accent-color:var(--wp-blue);cursor:pointer}
.contact-consent label{font-size:13.5px;color:#50575e;line-height:1.5;cursor:pointer}
.contact-consent a{color:var(--wp-blue);text-decoration:underline}
.contact-submit{width:100%;padding:13px;background:var(--wp-blue);color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:700;cursor:pointer;transition:background .15s,transform .1s,box-shadow .15s;letter-spacing:.1px;margin-top:6px;font-family:inherit}
.contact-submit:hover{background:var(--wp-blue-dark);box-shadow:0 4px 14px rgba(34,113,177,.3)}
.contact-submit:active{transform:scale(.98)}
.contact-submit:disabled{opacity:.6;cursor:not-allowed;transform:none;box-shadow:none}
.contact-field-err{border-color:#d63638!important;box-shadow:0 0 0 2px rgba(214,54,56,.12)!important}
.contact-alert{padding:14px 18px;border-radius:8px;font-size:14px;font-weight:500;margin-bottom:20px;display:none}
.contact-alert.success{background:#edfaef;border:1px solid #a6e7b3;color:#1f6b2c}
.contact-alert.error{background:#fbdada;border:1px solid #f0a9a9;color:#b32d2e}
.contact-honeypot{display:none!important;visibility:hidden!important;height:0!important;overflow:hidden!important}
.contact-thank-you{text-align:center;padding:48px 20px}
.contact-thank-you .ty-icon{font-size:64px;margin-bottom:16px;animation:tyBounce .5s cubic-bezier(.34,1.56,.64,1) both}
@keyframes tyBounce{from{transform:scale(0);opacity:0}to{transform:scale(1);opacity:1}}
.contact-thank-you h2{font-size:28px;font-weight:800;color:var(--wp-dark);margin:0 0 12px}
.contact-thank-you p{color:var(--wp-gray-50);font-size:16px;margin:0 0 24px;max-width:440px;line-height:1.6}
@media(max-width:600px){
  .contact-wrap{padding:24px 0 40px}
  .contact-card{padding:24px 18px}
  .contact-row-2{grid-template-columns:1fr}
  .contact-wrap h1{font-size:30px}
}

/* ── Admin inbox ──────────────────────────────────────────────────── */
.contact-msg-row{display:flex;gap:16px;align-items:flex-start;padding:16px 20px;border-bottom:1px solid var(--wp-gray-10);cursor:pointer;transition:background .1s;position:relative}
.contact-msg-row:hover{background:var(--wp-gray-0)}
.contact-msg-row.unread{background:#f0f6ff}
.contact-msg-row.unread::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--wp-blue);border-radius:0 3px 3px 0}
.contact-msg-meta{flex:1;min-width:0}
.contact-msg-from{font-size:14px;font-weight:700;color:var(--wp-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.contact-msg-row.unread .contact-msg-from{color:var(--wp-blue)}
.contact-msg-subject{font-size:13px;color:#50575e;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px}
.contact-msg-snippet{font-size:12px;color:var(--wp-gray-50);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:3px}
.contact-msg-date{font-size:12px;color:var(--wp-gray-50);white-space:nowrap;flex-shrink:0;margin-top:2px}
.contact-msg-detail{padding:24px;background:#fff;border:1px solid var(--wp-gray-20);border-radius:8px;margin-top:16px}
.contact-msg-detail h3{margin:0 0 16px;font-size:18px;font-weight:700;color:var(--wp-dark)}
.contact-msg-field{display:flex;gap:10px;margin-bottom:10px;font-size:14px}
.contact-msg-field strong{min-width:120px;color:var(--wp-gray-80);flex-shrink:0}
.contact-msg-body{margin-top:20px;padding:16px;background:var(--wp-gray-0);border-radius:6px;font-size:15px;line-height:1.6;white-space:pre-wrap;color:var(--wp-dark)}
.unread-badge{display:inline-flex;align-items:center;justify-content:center;background:var(--wp-blue);color:#fff;border-radius:20px;font-size:11px;font-weight:700;padding:1px 7px;margin-left:6px;vertical-align:middle;min-width:20px}

.site-footer{background:var(--wp-gray-5);border-top:1px solid var(--wp-gray-10);padding:24px 0;font-size:14px;color:var(--wp-gray-50)}

/* Login */
.login-body{background:var(--wp-gray-5);display:flex;align-items:center;justify-content:center}
.login-wrap{width:320px;padding:40px 0}
.login-logo{font-size:28px;font-weight:800;text-align:center;margin:0 0 28px;color:var(--wp-dark);display:flex;align-items:center;justify-content:center;gap:8px}
.login-card{background:#fff;border:1px solid var(--wp-gray-20);border-radius:6px;padding:28px;box-shadow:0 1px 3px rgba(0,0,0,.08)}
.login-field{margin-bottom:18px}
.login-label{display:block;font-size:14px;font-weight:500;margin-bottom:6px}
.login-input{width:100%;padding:8px 10px;border:1px solid var(--wp-gray-50);border-radius:4px;font-size:15px}
.login-input:focus{outline:none;border-color:var(--wp-blue);box-shadow:0 0 0 1px var(--wp-blue)}
.login-error{color:#b32d2e;background:#fbdada;border:1px solid #f0a9a9;border-radius:4px;padding:10px 14px;margin-bottom:16px;font-size:14px}
.login-hint{margin-top:20px;text-align:center;font-size:13px;color:var(--wp-gray-50)}
.btn-login{width:100%;padding:10px;background:var(--wp-blue);color:#fff;border:1px solid var(--wp-blue);border-radius:4px;font-size:15px;font-weight:500;cursor:pointer}
.btn-login:hover{background:var(--wp-blue-dark)}

/* Admin */
.wp-admin{display:flex;min-height:100vh;background:var(--wp-gray-5)}
.admin-sidebar{width:260px;background:var(--wp-dark);color:#f0f0f1;position:fixed;left:0;top:0;bottom:0;overflow-y:auto;z-index:200;display:flex;flex-direction:column}
.admin-logo{padding:18px 20px;font-size:20px;font-weight:700;display:flex;align-items:center;gap:10px;color:#fff;border-bottom:1px solid #2c3338}
.admin-menu{list-style:none;margin:0;padding:12px 0;flex:1}
.admin-menu li{margin:0}
.admin-menu a{display:flex;align-items:center;gap:12px;padding:10px 20px;color:#c3c4c7;text-decoration:none;font-size:14px;transition:all .15s;border-left:3px solid transparent}
.admin-menu a:hover{color:#fff;background:#2c3338}
.admin-menu a.active{background:#2271b1;color:#fff;border-left-color:#72aee6}
.admin-menu svg{width:18px;height:18px;opacity:.8}
.admin-menu .menu-sep{height:1px;background:#2c3338;margin:12px 20px}
.admin-main{flex:1;margin-left:260px;min-width:0;display:flex;flex-direction:column}
.admin-topbar{height:60px;background:#fff;border-bottom:1px solid var(--wp-gray-20);display:flex;align-items:center;padding:0 24px;gap:16px;position:sticky;top:0;z-index:90}
.admin-burger{display:none;background:none;border:none;font-size:22px;cursor:pointer;color:var(--wp-dark);padding:4px}
.admin-title{font-size:20px;font-weight:500;color:var(--wp-dark);flex:1}
.admin-user{display:flex;align-items:center;gap:8px;font-size:13px;color:#50575e}
.admin-avatar{width:28px;height:28px;border-radius:50%;background:var(--wp-blue);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px}
.admin-content{padding:24px;flex:1}
.admin-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:150}

.wp-notice{padding:12px 16px;background:#fff;border-left:4px solid #72aee6;box-shadow:0 1px 1px rgba(0,0,0,.04);margin-bottom:20px;border-radius:0 4px 4px 0}
.dashboard-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:28px}
.stat-card{background:#fff;border:1px solid var(--wp-gray-20);border-radius:6px;padding:20px;box-shadow:0 1px 1px rgba(0,0,0,.04)}
.stat-label{font-size:13px;text-transform:uppercase;letter-spacing:.5px;color:var(--wp-gray-50);font-weight:600;margin-bottom:8px}
.stat-value{font-size:32px;font-weight:600;line-height:1;color:var(--wp-dark)}
.stat-sub{font-size:13px;color:var(--wp-gray-50);margin-top:6px}

.wp-card{background:#fff;border:1px solid var(--wp-gray-20);border-radius:4px;box-shadow:0 1px 1px rgba(0,0,0,.04);margin-bottom:20px}
.wp-card-header{padding:14px 20px;border-bottom:1px solid var(--wp-gray-10);display:flex;align-items:center;justify-content:space-between}
.wp-card-title{font-size:14px;font-weight:600;margin:0}
.wp-card-body{padding:20px}

.toolbar{display:flex;gap:12px;align-items:center;margin-bottom:16px;flex-wrap:wrap}
.filter-tabs{display:flex;gap:2px;background:var(--wp-gray-5);padding:3px;border-radius:6px}
.filter-tabs button{padding:6px 14px;border:none;background:transparent;border-radius:4px;font-size:13px;cursor:pointer;color:#50575e;font-weight:500}
.filter-tabs button.active{background:#fff;color:var(--wp-dark);box-shadow:0 1px 2px rgba(0,0,0,.08)}
.search-mini{position:relative;flex:1 1 180px;min-width:120px;max-width:260px}
.search-mini input{padding:7px 28px 7px 10px;border:1px solid var(--wp-gray-20);border-radius:4px;font-size:13px;width:100%}
.search-mini svg{position:absolute;right:8px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--wp-gray-50)}

.table-wrap{overflow-x:auto}
.wp-table{width:100%;border-collapse:collapse;font-size:14px}
.wp-table th{text-align:left;padding:10px 12px;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--wp-gray-50);border-bottom:1px solid var(--wp-gray-20);background:var(--wp-gray-0);white-space:nowrap}
.wp-table td{padding:14px 12px;border-bottom:1px solid var(--wp-gray-5);vertical-align:top}
.wp-table tr:hover td{background:#f6f7f7}
.wp-table .row-title{font-weight:600;color:var(--wp-blue);cursor:pointer}
.wp-table .row-title:hover{color:var(--wp-blue-dark)}
.row-actions{display:flex;gap:12px;margin-top:6px;font-size:13px;visibility:hidden}
.wp-table tr:hover .row-actions{visibility:visible}
.row-actions a{color:var(--wp-blue)}
.row-actions a.delete{color:#b32d2e}
.status-badge{display:inline-flex;align-items:center;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.3px}
.status-published{background:#e7f5e9;color:#1f6b2c}
.status-draft{background:#f0f0f1;color:#50575e}

.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:3px;border:1px solid;font-size:13px;font-weight:500;cursor:pointer;text-decoration:none;line-height:1.4;transition:all .15s;white-space:nowrap}
.btn-primary{background:var(--wp-blue);border-color:var(--wp-blue);color:#fff}
.btn-primary:hover{background:var(--wp-blue-dark);border-color:var(--wp-blue-dark);color:#fff}
.btn-secondary{background:#fff;border-color:var(--wp-gray-20);color:var(--wp-dark)}
.btn-secondary:hover{border-color:var(--wp-gray-50);background:#f6f7f7}
.btn-danger{background:#d63638;border-color:#d63638;color:#fff}
.btn-danger:hover{background:#b32d2e}

.form-grid{display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start}
.form-main,.form-side{display:flex;flex-direction:column;gap:20px}
.form-row{display:flex;flex-direction:column;gap:6px}
.form-label{font-size:13px;font-weight:500;color:var(--wp-dark)}
.form-hint{font-size:12px;color:var(--wp-gray-50);margin-top:4px}
.form-input,.form-select,.form-textarea{width:100%;padding:8px 10px;border:1px solid var(--wp-gray-50);border-radius:4px;font-size:14px;font-family:inherit;background:#fff}
.form-input:focus,.form-select:focus,.form-textarea:focus{outline:none;border-color:var(--wp-blue);box-shadow:0 0 0 1px var(--wp-blue)}
.form-textarea{min-height:80px;resize:vertical;line-height:1.5}
.title-input{font-size:22px!important;padding:10px 12px!important;font-weight:600}

.editor-wrap{border:1px solid var(--wp-gray-50);border-radius:4px;overflow:hidden;background:#fff}
.editor-toolbar{display:flex;flex-wrap:wrap;gap:2px;padding:6px;background:var(--wp-gray-0);border-bottom:1px solid var(--wp-gray-20)}
.editor-toolbar button{width:32px;height:28px;display:flex;align-items:center;justify-content:center;border:1px solid transparent;background:transparent;border-radius:3px;cursor:pointer;color:#2c3338;font-size:14px}
.editor-toolbar button:hover{background:#fff;border-color:var(--wp-gray-20)}
.editor-toolbar .sep{width:1px;height:20px;background:var(--wp-gray-20);margin:0 4px;align-self:center}
.editor-content{min-height:340px;padding:16px;outline:none;font-size:15px;line-height:1.7}
.editor-content:empty:before{content:attr(data-placeholder);color:#a7aaad;pointer-events:none}

.media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px}
.media-item{aspect-ratio:1;background:var(--wp-gray-5);border-radius:4px;overflow:hidden;border:1px solid var(--wp-gray-10);position:relative}
.media-item img{width:100%;height:100%;object-fit:cover;display:block;transition:filter .2s}
.media-item:hover img{filter:brightness(.65)}
.media-item-overlay{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;padding:8px;gap:5px;opacity:0;transition:opacity .2s;pointer-events:none}
.media-item:hover .media-item-overlay{opacity:1;pointer-events:auto}
.media-copy-btn{display:flex;align-items:center;gap:5px;background:rgba(0,0,0,.75);color:#fff;border:none;border-radius:4px;padding:6px 10px;font-size:12px;font-weight:600;cursor:pointer;width:100%;justify-content:center;transition:background .15s,transform .1s;backdrop-filter:blur(4px)}
.media-copy-btn:hover{background:var(--wp-blue)}
.media-copy-btn:active{transform:scale(.96)}
.media-copy-btn.copied{background:#00a32a}
.media-copy-btn svg{flex-shrink:0}
.media-delete-btn{display:flex;align-items:center;gap:5px;background:rgba(179,45,46,.82);color:#fff;border:none;border-radius:4px;padding:5px 10px;font-size:12px;font-weight:600;cursor:pointer;width:100%;justify-content:center;transition:background .15s,transform .1s;backdrop-filter:blur(4px)}
.media-delete-btn:hover{background:#b32d2e}
.media-delete-btn:active{transform:scale(.96)}
/* Media picker modal */
.media-modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9000;display:flex;align-items:center;justify-content:center;padding:20px}
.media-modal{background:#fff;border-radius:10px;width:100%;max-width:860px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.3);overflow:hidden}
.media-modal-head{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--wp-gray-10);flex-shrink:0}
.media-modal-head h3{margin:0;font-size:16px;font-weight:700;color:var(--wp-dark)}
.media-modal-close{background:none;border:none;font-size:22px;cursor:pointer;color:var(--wp-gray-50);line-height:1;padding:4px 8px;border-radius:4px;transition:background .15s,color .15s}
.media-modal-close:hover{background:var(--wp-gray-5);color:var(--wp-dark)}
.media-modal-body{overflow-y:auto;padding:20px;flex:1}
.media-modal-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px}
.media-picker-item{aspect-ratio:1;border-radius:6px;overflow:hidden;border:2px solid transparent;cursor:pointer;background:var(--wp-gray-5);transition:border-color .15s,transform .1s;position:relative}
.media-picker-item img{width:100%;height:100%;object-fit:cover;display:block;transition:filter .15s}
.media-picker-item:hover img{filter:brightness(.82)}
.media-picker-item:hover{border-color:var(--wp-blue)}
.media-picker-item.selected{border-color:var(--wp-blue);box-shadow:0 0 0 2px var(--wp-blue)}
.media-picker-item.selected::after{content:'✓';position:absolute;top:6px;right:6px;background:var(--wp-blue);color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700}
.media-picker-item.already-in{border-color:#1f6b2c;opacity:.65;cursor:default}
.picker-in-badge{position:absolute;top:6px;right:6px;background:#1f6b2c;color:#fff;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700}
.media-modal-foot{padding:14px 22px;border-top:1px solid var(--wp-gray-10);display:flex;justify-content:flex-end;gap:10px;flex-shrink:0}
/* Confirm dialog */
.confirm-dialog-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9500;display:flex;align-items:center;justify-content:center;padding:20px}
.confirm-dialog{background:#fff;border-radius:8px;width:100%;max-width:440px;box-shadow:0 8px 40px rgba(0,0,0,.22);overflow:hidden}
.confirm-dialog-head{padding:18px 22px 0;display:flex;align-items:center;gap:10px}
.confirm-dialog-head svg{flex-shrink:0}
.confirm-dialog-head h4{margin:0;font-size:15px;font-weight:700;color:var(--wp-dark)}
.confirm-dialog-body{padding:12px 22px 18px;font-size:13px;color:#50575e;line-height:1.5}
.confirm-dialog-body .usage-list{margin:8px 0 0;padding:0 0 0 14px}
.confirm-dialog-body .usage-list li{margin-bottom:2px}
.confirm-dialog-foot{padding:12px 22px;border-top:1px solid var(--wp-gray-10);display:flex;justify-content:flex-end;gap:10px}

/* Toggle switch */
.toggle-switch{position:relative;display:inline-block;width:40px;height:22px;flex-shrink:0}
.toggle-switch input{opacity:0;width:0;height:0}
.toggle-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:var(--wp-gray-20);border-radius:22px;transition:.2s}
.toggle-slider:before{position:absolute;content:"";height:16px;width:16px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.2s}
.toggle-switch input:checked + .toggle-slider{background:var(--wp-blue)}
.toggle-switch input:checked + .toggle-slider:before{transform:translateX(18px)}

/* Responsive */
@media (max-width:960px){
  .form-grid{grid-template-columns:1fr}
  .form-side{order:-1}
}
@media (max-width:782px){
  .admin-sidebar{transform:translateX(-100%);transition:transform .25s;width:280px}
  .admin-sidebar.open{transform:translateX(0);box-shadow:0 0 20px rgba(0,0,0,.3)}
  .admin-main{margin-left:0}
  .admin-burger{display:block}
  .admin-overlay.open{display:block}
  .header-inner{height:auto;padding:14px 0;flex-wrap:nowrap;gap:8px}
  .nav{display:none}
  .logo{font-size:16px;gap:8px;flex:1;min-width:0;flex-shrink:1}
  .logo-img{height:76px}
  .logo-site-title{font-size:30px;max-width:260px}
  .header-inner.search-active .logo{display:none}
  .search-box{display:none}                    /* hide desktop search — mobile toggle handles it */
  .mobile-toggle{display:flex;align-items:center}
  .mobile-search-toggle{display:flex;margin-left:auto}
  .mobile-search-wrap{display:none}
  .mobile-search-wrap.ms-open,.mobile-search-wrap.ms-closing{display:flex}
  .post-grid{grid-template-columns:1fr}
  .page-title,.article-title{font-size:36px}
  .admin-content{padding:16px}
  .dashboard-grid{grid-template-columns:1fr 1fr}
  .toolbar{flex-wrap:wrap;gap:8px}
  .search-mini{max-width:100%;flex:1 1 100%}
  .row-actions{visibility:visible}
}
@media (max-width:480px){
  .container{padding:0 16px}
  .dashboard-grid{grid-template-columns:1fr}
}
</style>
</head>
<body>
<div id="pageLoader" aria-live="polite" aria-label="Loading page" role="status">
  <div class="loader-ring"></div>
  <span class="loader-label">Loading…</span>
</div>
<div id="transition-logo-container" aria-hidden="true"><div class="tl-inner"></div></div>
<div id="page-content"></div>
<script>
// ══════════════════════════════════════════════════════════════════════════════
// API — replaces localStorage with fetch() calls to api.php
// ══════════════════════════════════════════════════════════════════════════════

const API = {
  async call(action, method = 'GET', body = null) {
    const opts = { method, headers: { 'Content-Type': 'application/json' } };
    if (body) opts.body = JSON.stringify(body);
    const res = await fetch(`api.php?action=${action}`, opts);
    return res.json();
  },

  // Posts
  async getPosts()         { const d = await this.call('posts'); return d.posts || []; },
  async savePost(post)     { return this.call('posts', post.id && post._isEdit ? 'PUT' : 'POST', post); },
  async deletePost(id)     { return this.call('posts', 'DELETE', { id }); },

  // Settings
  async getSettings()      { const d = await this.call('settings'); return d.settings || {}; },
  async saveSettings(s)    { return this.call('settings', 'POST', s); },

  // Auth
  async login(u, p)        { return this.call('login', 'POST', { username: u, password: p }); },
  async logout()           { return this.call('logout', 'POST'); },
  async getSession()       { return this.call('session'); },

  // Categories
  async getCategories()           { const d = await this.call('categories'); return d.categories || []; },
  async createCategory(name)      { return this.call('categories', 'POST', { name }); },
  async updateCategory(id, name)  { return this.call('categories', 'PUT',  { id, name }); },
  async deleteCategory(id)        { return this.call('categories', 'DELETE', { id }); },

  // Media library
  async getMedia()        { const d = await this.call('media'); return d.media || []; },
  async addMedia(url)     { return this.call('media', 'POST', { url }); },
  async deleteMedia(url)  { return this.call('media', 'DELETE', { url }); },

  // Contact
  async getContacts()           { return this.call('contact'); },
  async submitContact(data)     { return this.call('contact', 'POST', data); },
  async markContactRead(id, read) { return this.call('contact', 'PUT', { id, read }); },
  async deleteContact(id)       { return this.call('contact', 'DELETE', { id }); },
  async getContactSettings()    { return this.call('contact_settings'); },
  async saveContactSettings(s)  { return this.call('contact_settings', 'POST', s); },

  // Reset
  async reset()            { return this.call('reset', 'POST'); },
};

// ══════════════════════════════════════════════════════════════════════════════
// Utilities
// ══════════════════════════════════════════════════════════════════════════════

function escapeHtml(s) {
  return String(s || '').replace(/[&<>"']/g, m =>
    ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));
}
function slugify(s) {
  return String(s || '').toLowerCase().trim()
    .replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
}
function formatDate(iso) {
  try { return new Date(iso).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' }); }
  catch { return ''; }
}
function parseHash() {
  const hash = location.hash.slice(1) || '/';
  const [pathPart, qs] = hash.split('?');
  const parts = pathPart.split('/').filter(Boolean);
  return { path: pathPart, parts, query: new URLSearchParams(qs || '') };
}

// ══════════════════════════════════════════════════════════════════════════════
// Page loader
// ══════════════════════════════════════════════════════════════════════════════

// ══════════════════════════════════════════════════════════════════════════════
// Shared lightbox drag-sync engine
// Handles pointer (touch + mouse) dragging with live translate, spring-back,
// and commit-on-release. Used by all three gallery lightbox instances.
// ══════════════════════════════════════════════════════════════════════════════
// ── Manual scroll restoration — prevents browser scroll override on history nav
history.scrollRestoration = 'manual';

const _loader = document.getElementById('pageLoader');
const _pageContent = document.getElementById('page-content');
const _transLogo   = document.getElementById('transition-logo-container');
const _transLogoInner = _transLogo.querySelector('.tl-inner');

// Populate the transition logo once from settings (lazy, on first nav)
let _transLogoReady = false;
async function _ensureTransLogo() {
  if (_transLogoReady) return;
  _transLogoReady = true;
  try {
    const s = await API.getSettings();
    _transLogoInner.innerHTML = s.logoUrl
      ? `<img class="tl-img" src="${escapeHtml(s.logoUrl)}" alt="${escapeHtml(s.siteTitle||'')}">`
      : `<span class="tl-dot"></span><span class="tl-name">${escapeHtml(s.siteTitle||'')}</span>`;
  } catch(_) {
    // Fallback: leave inner empty — container stays invisible
  }
}

function _showTransLogo() { _transLogo.classList.add('active'); }
function _hideTransLogo() { _transLogo.classList.remove('active'); }
let _loaderTimer = null;

function showLoader() {
  clearTimeout(_loaderTimer);
  _loader.classList.remove('loader-hiding');
  _loader.classList.add('loader-visible');
}

function hideLoader() {
  _loader.classList.add('loader-hiding');
  _loaderTimer = setTimeout(() => {
    _loader.classList.remove('loader-visible', 'loader-hiding');
  }, 300);
}

// Focus the primary heading or landmark of the freshly rendered page.
// Uses tabindex="-1" so programmatic focus works without a visible ring on click.
function focusPageTitle() {
  // Small rAF delay so the DOM is fully painted before focus fires
  requestAnimationFrame(() => {
    const el =
      document.querySelector('h1.article-title') ||
      document.querySelector('h1.page-title')    ||
      document.querySelector('.admin-title')      ||
      document.querySelector('h1.login-logo');
    if (!el) return;
    el.classList.add('js-focus-target');
    el.setAttribute('tabindex', '-1');
    el.focus({ preventScroll: true });
  });
}

// ══════════════════════════════════════════════════════════════════════════════
// Router — with smooth fade transitions
// ══════════════════════════════════════════════════════════════════════════════

// Track whether this is the very first navigation (no fade-out needed)
let _isFirstNav = true;

async function router() {
  const { parts } = parseHash();
  const hash = location.hash || '#/';

  if (_isFirstNav) {
    // First load: show loader overlay, no fade needed
    _isFirstNav = false;
    showLoader();
    await _renderRoute(hash, parts);
    hideLoader();
    focusPageTitle();
    return;
  }

  // Subsequent navs: fade out current content, then swap, then fade in
  _pageContent.classList.add('page-fade-out');

  // Wait for fade-out to complete (150ms matches CSS transition)
  await new Promise(resolve => setTimeout(resolve, 155));

  // Show logo during blank phase; ensure it's populated (lazy first-time fetch)
  _ensureTransLogo();
  _showTransLogo();

  // Render new content while invisible; logo is visible in background
  await _renderRoute(hash, parts);

  // Minimum logo display time so it doesn't flash on instant local renders
  await new Promise(resolve => setTimeout(resolve, 150));

  // Reset scroll position while content is still invisible
  // Use requestAnimationFrame to ensure DOM has fully updated
  requestAnimationFrame(() => {
    window.scrollTo({ top: 0, behavior: 'instant' });

    // Begin fade-in on next frame so the browser paints the scroll first
    requestAnimationFrame(() => {
      // Hide logo just before new content appears — clean hand-off
      _hideTransLogo();

      _pageContent.classList.remove('page-fade-out');
      _pageContent.classList.add('page-fade-in');
      // Force reflow then add visible class to trigger transition
      void _pageContent.offsetWidth;
      _pageContent.classList.add('page-visible');

      setTimeout(() => {
        _pageContent.classList.remove('page-fade-in', 'page-visible');
        focusPageTitle();
      }, 200);
    });
  });
}

async function _renderRoute(hash, parts) {
  if (hash.startsWith('#/login')) { await renderLogin(); return; }
  if (hash.startsWith('#/admin')) {
    const sess = await API.getSession();
    if (!sess.loggedIn) { location.hash = '#/login'; return; }
    await renderAdmin(parts, sess);
    return;
  }
  await renderPublic(parts);
}

// ══════════════════════════════════════════════════════════════════════════════
// Shared lightbox mobile swipe — Facebook-style track model
// Call once per lightbox after building its DOM.
//
//   attachLbMobileSwipe(lbStage, ctx)
//
//   ctx = {
//     imgs        : string[]          — ordered image URL array
//     getCurIdx   : () => number      — current image index
//     setCurIdx   : (n) => void       — update curIdx in the outer scope
//     getLbImg    : () => HTMLElement — the canonical <img> element
//     setCounter  : (text) => void    — update the counter pill
//     closeNav    : () => void        — close the lightbox (for swipe-down/close)
//   }
//
// Desktop (>768 px): touch events are ignored; arrow keys / clicks drive nav.
// Mobile (≤768 px): 3-cell track slides 1:1 with the finger. Fast flick or
//   >28 % of width commits; anything less springs back.
// ══════════════════════════════════════════════════════════════════════════════
function attachLbMobileSwipe(lbStage, ctx) {
  const isMobile = () => window.innerWidth <= 768;

  let track = null, trackCells = [], trackOffset = 0;
  let tx = 0, ty = 0, tdx = 0, tStartTime = 0;
  let tLocked = false, tVertical = false;
  let _navigating = false; // local flag so we don't stomp the outer navigating

  const SW = () => lbStage.offsetWidth || window.innerWidth;

  function _buildTrack() {
    _destroyTrack();
    const imgs   = ctx.imgs;
    const curIdx = ctx.getCurIdx();
    track = document.createElement('div');
    track.className = 'lb-swipe-track';

    const indices = [
      (curIdx - 1 + imgs.length) % imgs.length,
      curIdx,
      (curIdx + 1) % imgs.length
    ];

    trackCells = indices.map(idx => {
      const cell = document.createElement('div');
      cell.className = 'lb-swipe-cell';
      const img = document.createElement('img');
      img.className = 'gallery-lightbox-img';
      img.src = imgs[idx];
      img.alt = 'Gallery image ' + (idx + 1);
      img.setAttribute('draggable', 'false');
      img.style.cssText = 'animation:none;position:relative;left:auto;top:auto;translate:none;max-width:100%;max-height:100%';
      cell.appendChild(img);
      track.appendChild(cell);
      return cell;
    });

    const w = SW();
    trackOffset = -w;
    track.style.transform = `translateX(${trackOffset}px)`;
    lbStage.appendChild(track);

    const lbImg = ctx.getLbImg();
    if (lbImg) lbImg.style.visibility = 'hidden';
  }

  function _destroyTrack() {
    if (track) { track.remove(); track = null; trackCells = []; }
    const lbImg = ctx.getLbImg();
    if (lbImg) lbImg.style.visibility = '';
  }

  function _commitSwipe(dir) {
    // dir: +1 = next, -1 = prev
    const imgs   = ctx.imgs;
    const w      = SW();
    const target = dir > 0 ? -2 * w : 0;
    const dist   = Math.abs(target - trackOffset);
    const ms     = Math.max(80, Math.round(280 * dist / w));

    track.classList.remove('lb-track-spring');
    track.classList.add('lb-track-snap');
    track.style.transition = `transform ${ms}ms cubic-bezier(.25,.46,.45,.94)`;
    track.style.transform  = `translateX(${target}px)`;
    trackOffset = target;

    const nextIdx = (ctx.getCurIdx() + dir + imgs.length) % imgs.length;
    ctx.setCurIdx(nextIdx);
    ctx.setCounter((nextIdx + 1) + ' / ' + imgs.length);

    setTimeout(() => {
      const centerCellImg = trackCells[dir > 0 ? 2 : 0]?.querySelector('img');
      const lbImg = ctx.getLbImg();
      if (centerCellImg && lbImg) {
        lbImg.src = centerCellImg.src;
        lbImg.alt = centerCellImg.alt;
      }
      _destroyTrack();
      _navigating = false;
    }, ms + 16);
  }

  function _cancelSwipe() {
    const w = SW();
    track.classList.remove('lb-track-snap');
    track.classList.add('lb-track-spring');
    track.style.transform = `translateX(${-w}px)`;
    trackOffset = -w;
    setTimeout(() => { _destroyTrack(); _navigating = false; }, 340);
  }

  function _resist(raw) {
    if (ctx.imgs.length > 1) return raw;
    return Math.sign(raw) * Math.min(Math.abs(raw) * 0.25, 40);
  }

  lbStage.addEventListener('touchstart', e => {
    if (!isMobile() || _navigating) return;
    tx = e.touches[0].clientX; ty = e.touches[0].clientY;
    tStartTime = Date.now();
    tdx = 0; tLocked = false; tVertical = false;
  }, { passive: true });

  lbStage.addEventListener('touchmove', e => {
    if (!isMobile() || _navigating) return;
    const dx = e.touches[0].clientX - tx;
    const dy = e.touches[0].clientY - ty;
    if (!tLocked) {
      if (tVertical) return;
      if (Math.abs(dy) > Math.abs(dx) + 5) { tVertical = true; return; }
      if (Math.abs(dx) < 6) return;
      tLocked = true;
      _buildTrack();
    }
    if (!track) return;
    tdx = dx;
    track.style.transform = `translateX(${-SW() + _resist(dx)}px)`;
  }, { passive: true });

  lbStage.addEventListener('touchend', () => {
    if (!isMobile() || !tLocked || !track) {
      if (track && !tLocked) _destroyTrack();
      tdx = 0; return;
    }
    const w        = SW();
    const velocity = Math.abs(tdx) / Math.max(Date.now() - tStartTime, 1);
    const commit   = (Math.abs(tdx) >= w * 0.28 || velocity >= 0.4) && ctx.imgs.length > 1;
    _navigating = true;
    if (commit) _commitSwipe(tdx < 0 ? 1 : -1);
    else        _cancelSwipe();
    tdx = 0;
  });

  // Expose so the outer closeLb can clean up a mid-swipe track
  lbStage._destroySwipeTrack = _destroyTrack;
}

// ══════════════════════════════════════════════════════════════════════════════
// Public site
// ══════════════════════════════════════════════════════════════════════════════

async function renderPublic(parts) {
  const [settings, allPosts] = await Promise.all([API.getSettings(), API.getPosts()]);
  const publishedAll = allPosts.filter(p => p.status === 'published')
    .sort((a, b) => new Date(b.date) - new Date(a.date));
  // Homepage: published + showOnHome === true only.
  // Category / search / direct post URLs use publishedAll (any published post).
  // Drafts are invisible everywhere on the public site.
  const posts = publishedAll.filter(p => p.showOnHome === true);

  document.title = settings.siteTitle + (settings.tagline ? ' — ' + settings.tagline : '');
  document.body.className = 'public-body';

  // Apply favicon dynamically
  if (settings.faviconUrl) {
    let link = document.querySelector("link[rel~='icon']");
    if (!link) { link = document.createElement('link'); link.rel = 'icon'; document.head.appendChild(link); }
    link.href = settings.faviconUrl;
  }

  // Build logo markup: image if logoUrl set, else text with dot
  const logoInner = settings.logoUrl
    ? `<img src="${escapeHtml(settings.logoUrl)}" alt="${escapeHtml(settings.siteTitle)}" class="logo-img"><span class="logo-site-title">${escapeHtml(settings.siteTitle)}</span>`
    : `<span class="logo-dot"></span>${escapeHtml(settings.siteTitle)}`;

  // Nav categories always show ALL published categories, regardless of showOnHome
  const cats = [...new Set(publishedAll.map(p => p.category).filter(Boolean))].sort();

  _pageContent.innerHTML = `
  <header class="site-header">
    <div class="container">
      <div class="header-inner">
        <a href="#/" class="logo">${logoInner}</a>
        <nav class="nav" id="mainNav">
          <a href="#/" data-nav="home">Home</a>
          <a href="#/about" data-nav="about">About</a>
          ${settings.siteGallery && settings.siteGallery.length ? `<a href="#/gallery" data-nav="gallery">Gallery</a>` : ''}
          ${cats.map(c=>`<a href="#/category/${encodeURIComponent(c.toLowerCase())}" data-nav="cat-${c.toLowerCase()}">${escapeHtml(c)}</a>`).join('')}
          ${settings.contactEnabled !== false ? `<a href="#/contact" data-nav="contact">Contact</a>` : ''}
          <a href="#/admin"></a>
        </nav>
        <form class="search-box" id="siteSearchForm" autocomplete="off">
          <input type="search" placeholder="Search..." id="siteSearchInput" aria-label="Search" autocomplete="off">
          <button type="submit" aria-label="Search">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          </button>
          <div class="search-ac-wrap" id="searchAcWrap"></div>
        </form>
        <!-- Mobile search bar (expands on tap) -->
        <div class="mobile-search-wrap" id="mobileSearchWrap">
          <form class="mobile-search-form" id="mobileSearchForm" autocomplete="off">
            <input type="search" class="mobile-search-input" id="mobileSearchInput" placeholder="Search articles…" aria-label="Search" autocomplete="off">
            <button type="button" class="mobile-search-clear" id="mobileSearchClear" aria-label="Close search">✕</button>
          </form>
        </div>
        <!-- Mobile search icon — high-contrast blue pill, acts as toggle + submit -->
        <button class="mobile-search-toggle" id="mobileSearchToggle" aria-label="Search" title="Search">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        </button>
        <button class="mobile-toggle" id="mobileToggle" aria-label="Menu" title="Menu">☰</button>
      </div>
    </div>
  </header>

  <!-- Mobile Drawer -->
  <div class="mobile-drawer" id="mobileDrawer">
    <div class="mobile-drawer-overlay" id="drawerOverlay"></div>
    <div class="mobile-drawer-panel">
      <div class="mobile-drawer-header">
        <button class="mobile-drawer-close" id="drawerClose" aria-label="Close">✕</button>
      </div>
      <nav class="mobile-drawer-nav" id="drawerNav">
        <a href="#/" data-nav="home">🏠 Home</a>
        <a href="#/about" data-nav="about">👤 About</a>
        ${settings.siteGallery && settings.siteGallery.length ? `<a href="#/gallery" data-nav="gallery">🖼️ Gallery</a>` : ''}
        ${settings.contactEnabled !== false ? `<a href="#/contact" data-nav="contact">✉️ Contact</a>` : ''}
        <div class="mobile-drawer-sep"></div>
        <div class="mobile-drawer-label">Categories</div>
        ${cats.map(c=>`<a href="#/category/${encodeURIComponent(c.toLowerCase())}" data-nav="cat-${c.toLowerCase()}">📂 ${escapeHtml(c)}</a>`).join('')}
        <div class="mobile-drawer-sep"></div>
        
      </nav>
    </div>
  </div>
  <main class="main"><div class="container" id="publicContent"></div></main>
  <footer class="site-footer"><div class="container">© ${new Date().getFullYear()} ${escapeHtml(settings.siteTitle)}</div></footer>
  `;

  const content = document.getElementById('publicContent');

  // ── Autocomplete engine ────────────────────────────────────────────────────
  // Builds a search index from title, excerpt, tags, and plain-text content.
  function buildIndex(allPosts) {
    return allPosts.map(p => {
      const plainContent = (p.content || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
      return { post: p, searchText: [p.title, p.excerpt, (p.tags||[]).join(' '), plainContent].join(' ').toLowerCase(), plainContent };
    });
  }
  const searchIndex = buildIndex(publishedAll);

  // Score a post against a query — weights: title > tags > excerpt > content
  function scorePost(entry, q) {
    const lq = q.toLowerCase().trim();
    if (!lq) return 0;
    const terms = lq.split(/\s+/);
    let score = 0;
    for (const t of terms) {
      if ((entry.post.title || '').toLowerCase().includes(t))   score += 10;
      if ((entry.post.tags  || []).join(' ').toLowerCase().includes(t)) score += 6;
      if ((entry.post.excerpt || '').toLowerCase().includes(t)) score += 4;
      if (entry.plainContent.toLowerCase().includes(t))          score += 1;
    }
    return score;
  }

  // Highlight matching text — wraps matches in <mark>
  function highlight(text, q) {
    if (!q || !text) return escapeHtml(text || '');
    const terms = q.trim().split(/\s+/).filter(Boolean).map(t => t.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'));
    if (!terms.length) return escapeHtml(text);
    const re = new RegExp('(' + terms.join('|') + ')', 'gi');
    return escapeHtml(text).replace(re, '<mark>$1</mark>');
  }

  // Extract a short snippet from content around the first match
  function snippet(plainContent, q) {
    if (!q || !plainContent) return '';
    const lc = plainContent.toLowerCase();
    const t  = q.trim().split(/\s+/)[0].toLowerCase();
    const idx = lc.indexOf(t);
    if (idx === -1) return plainContent.slice(0, 100);
    const start = Math.max(0, idx - 40);
    const end   = Math.min(plainContent.length, idx + 100);
    return (start > 0 ? '…' : '') + plainContent.slice(start, end) + (end < plainContent.length ? '…' : '');
  }

  // Hue from title (same as post grid gradient)
  function titleHue(title) { return [...(title||'')].reduce((a,c)=>a+c.charCodeAt(0),0)%360; }

  // Render autocomplete dropdown into a given wrap element
  function renderAc(wrap, q, onSelect) {
    if (!q || q.length < 2) { wrap.classList.remove('ac-open'); wrap.innerHTML=''; return; }
    const scored = searchIndex
      .map(e => ({ e, s: scorePost(e, q) }))
      .filter(x => x.s > 0)
      .sort((a, b) => b.s - a.s)
      .slice(0, 6);

    if (!scored.length) {
      wrap.innerHTML = `<div class="ac-empty">No results for "<strong>${escapeHtml(q)}</strong>"</div>`;
      wrap.classList.add('ac-open');
      return;
    }

    wrap.innerHTML = scored.map(({e}, i) => {
      const p = e.post;
      const hue = titleHue(p.title);
      const thumb = p.featuredImage
        ? `<img class="ac-item-thumb" src="${escapeHtml(p.featuredImage)}" alt="" loading="lazy">`
        : `<div class="ac-item-thumb-placeholder" style="background:linear-gradient(135deg,hsl(${hue},65%,60%),hsl(${(hue+60)%360},65%,50%))">📄</div>`;
      const snip = snippet(e.plainContent, q);
      return `<div class="ac-item" data-slug="${escapeHtml(p.slug)}" data-idx="${i}" role="option" tabindex="-1">
        ${thumb}
        <div class="ac-item-body">
          <div class="ac-item-title">${highlight(p.title, q)}</div>
          <div class="ac-item-meta">${escapeHtml(p.category||'')}${p.tags&&p.tags.length?' · '+p.tags.slice(0,3).map(t=>escapeHtml(t)).join(', '):''} · ${formatDate(p.date)}</div>
          ${snip ? `<div class="ac-item-snippet">${highlight(snip, q)}</div>` : ''}
        </div>
      </div>`;
    }).join('') +
    `<div class="ac-footer">See all results for "<strong>${escapeHtml(q)}</strong>" →</div>`;

    wrap.classList.add('ac-open');

    wrap.querySelectorAll('.ac-item').forEach(item => {
      item.addEventListener('mousedown', e => { e.preventDefault(); onSelect(item.dataset.slug); });
    });
    const footer = wrap.querySelector('.ac-footer');
    if (footer) footer.addEventListener('mousedown', e => { e.preventDefault(); onSelect(null, q); });
  }

  // ── Desktop search ─────────────────────────────────────────────────────────
  const siteSearchInput = document.getElementById('siteSearchInput');
  const searchAcWrap    = document.getElementById('searchAcWrap');
  let acActiveIdx = -1;

  function positionDesktopAc() {
    const rect = siteSearchInput.getBoundingClientRect();
    const dropW = 360;
    const vw = window.innerWidth;
    // Align right edge with input right edge, then clamp inside viewport with 12px margin
    let left = rect.right - dropW;
    if (left < 12) left = 12;
    if (left + dropW > vw - 12) left = vw - dropW - 12;
    searchAcWrap.style.top  = (rect.bottom + 6) + 'px';
    searchAcWrap.style.left = left + 'px';
    searchAcWrap.style.width = dropW + 'px';
  }

  function closeAc() { searchAcWrap.classList.remove('ac-open'); searchAcWrap.innerHTML = ''; acActiveIdx = -1; }

  function acItems() { return [...searchAcWrap.querySelectorAll('.ac-item')]; }

  function setAcActive(idx) {
    const items = acItems();
    items.forEach((el, i) => el.classList.toggle('ac-active', i === idx));
    acActiveIdx = idx;
    if (idx >= 0 && items[idx]) items[idx].scrollIntoView({ block: 'nearest' });
  }

  siteSearchInput.addEventListener('input', () => {
    acActiveIdx = -1;
    renderAc(searchAcWrap, siteSearchInput.value.trim(), (slug, q) => {
      closeAc();
      if (slug) location.hash = '#/post/' + slug;
      else if (q) location.hash = '#/search?q=' + encodeURIComponent(q);
    });
    if (searchAcWrap.classList.contains('ac-open')) positionDesktopAc();
  });

  siteSearchInput.addEventListener('focus', () => {
    if (searchAcWrap.classList.contains('ac-open')) positionDesktopAc();
  });

  siteSearchInput.addEventListener('keydown', e => {
    const items = acItems();
    if (e.key === 'ArrowDown')  { e.preventDefault(); setAcActive(Math.min(acActiveIdx + 1, items.length - 1)); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); setAcActive(Math.max(acActiveIdx - 1, -1)); }
    else if (e.key === 'Escape') { closeAc(); siteSearchInput.blur(); }
    else if (e.key === 'Enter') {
      if (acActiveIdx >= 0 && items[acActiveIdx]) {
        e.preventDefault();
        const slug = items[acActiveIdx].dataset.slug;
        closeAc(); location.hash = '#/post/' + slug;
      }
    }
  });

  siteSearchInput.addEventListener('blur', () => setTimeout(closeAc, 200));

  document.getElementById('siteSearchForm').addEventListener('submit', e => {
    e.preventDefault();
    const q = siteSearchInput.value.trim();
    closeAc();
    if (q) location.hash = '#/search?q=' + encodeURIComponent(q);
  });

  // ── Drawer open/close with slide + opacity animations ─────────────────────
  function openDrawer() {
    const drawer = document.getElementById('mobileDrawer');
    drawer.classList.remove('closing');
    drawer.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeDrawer() {
    const drawer = document.getElementById('mobileDrawer');
    if (!drawer.classList.contains('open')) return;
    drawer.classList.add('closing');
    const panel = drawer.querySelector('.mobile-drawer-panel');
    function onEnd() {
      panel.removeEventListener('animationend', onEnd);
      drawer.classList.remove('open', 'closing');
      document.body.style.overflow = '';
    }
    panel.addEventListener('animationend', onEnd);
  }
  document.getElementById('mobileToggle').addEventListener('click', openDrawer);
  document.getElementById('drawerClose').addEventListener('click', closeDrawer);
  document.getElementById('drawerOverlay').addEventListener('click', closeDrawer);
  document.getElementById('drawerNav').querySelectorAll('a').forEach(a => a.addEventListener('click', closeDrawer));

  // ── Mobile search ──────────────────────────────────────────────────────────
  const mobileSearchToggle = document.getElementById('mobileSearchToggle');
  const mobileSearchWrap   = document.getElementById('mobileSearchWrap');
  const mobileSearchInput  = document.getElementById('mobileSearchInput');
  const mobileSearchClear  = document.getElementById('mobileSearchClear');
  const mobileSearchForm   = document.getElementById('mobileSearchForm');
  let msOpen = false;
  let mobileAcActiveIdx = -1;

  const headerInner = document.querySelector('.header-inner');

  // Create the AC dropdown — appended to body so it's never clipped
  const mobileAcWrap = document.createElement('div');
  mobileAcWrap.className = 'mobile-ac-wrap';
  mobileAcWrap.id = 'mobileAcWrap';
  document.body.appendChild(mobileAcWrap);

  function positionMobileAc() {
    const rect = mobileSearchForm.getBoundingClientRect();
    const topVal = rect.bottom + 8;
    // In landscape the viewport height shrinks — clamp max-height via CSS min() already,
    // but also ensure dropdown doesn't start below the fold
    const availH = window.innerHeight - topVal - 16;
    mobileAcWrap.style.top = topVal + 'px';
    mobileAcWrap.style.left  = '12px';
    mobileAcWrap.style.right = '12px';
    mobileAcWrap.style.maxHeight = Math.max(120, Math.min(380, availH)) + 'px';
  }

  function closeMobileAc() {
    mobileAcWrap.classList.remove('ac-open');
    mobileAcWrap.style.display = 'none';
    mobileAcWrap.innerHTML = '';
    mobileAcActiveIdx = -1;
  }

  function mobileAcItems() { return [...mobileAcWrap.querySelectorAll('.ac-item')]; }

  function setMobileAcActive(idx) {
    const items = mobileAcItems();
    items.forEach((el, i) => el.classList.toggle('ac-active', i === idx));
    mobileAcActiveIdx = idx;
    if (idx >= 0 && items[idx]) items[idx].scrollIntoView({ block: 'nearest' });
  }

  function showMobileAc() {
    positionMobileAc();
    mobileAcWrap.style.display = 'block';
    mobileAcWrap.classList.add('ac-open');
  }

  mobileSearchInput.addEventListener('input', () => {
    mobileAcActiveIdx = -1;
    const q = mobileSearchInput.value.trim();
    if (!q || q.length < 2) { closeMobileAc(); return; }
    renderAc(mobileAcWrap, q, (slug, query) => {
      closeMobileAc();
      closeMobileSearch();
      if (slug)  location.hash = '#/post/' + slug;
      else if (query) location.hash = '#/search?q=' + encodeURIComponent(query);
    });
    showMobileAc();
  });

  mobileSearchInput.addEventListener('keydown', e => {
    const items = mobileAcItems();
    if (e.key === 'ArrowDown')  { e.preventDefault(); setMobileAcActive(Math.min(mobileAcActiveIdx + 1, items.length - 1)); }
    else if (e.key === 'ArrowUp') { e.preventDefault(); setMobileAcActive(Math.max(mobileAcActiveIdx - 1, -1)); }
    else if (e.key === 'Escape') { closeMobileSearch(); }
    else if (e.key === 'Enter') {
      if (mobileAcActiveIdx >= 0 && items[mobileAcActiveIdx]) {
        e.preventDefault();
        const slug = items[mobileAcActiveIdx].dataset.slug;
        closeMobileAc(); closeMobileSearch(); location.hash = '#/post/' + slug;
      }
    }
  });

  function openMobileSearch() {
    msOpen = true;
    mobileSearchWrap.style.display = 'flex';
    mobileSearchWrap.classList.remove('ms-closing');
    mobileSearchWrap.classList.add('ms-open');
    headerInner.classList.add('search-active');
    setTimeout(() => { mobileSearchInput.focus(); }, 60);
  }

  function closeMobileSearch() {
    if (!msOpen) return;
    msOpen = false;
    closeMobileAc();
    headerInner.classList.remove('search-active');
    mobileSearchWrap.classList.remove('ms-open');
    mobileSearchWrap.classList.add('ms-closing');
    mobileSearchInput.value = '';
    // Hide after slide-out animation completes
    function onAnimEnd() {
      mobileSearchWrap.removeEventListener('animationend', onAnimEnd);
      mobileSearchWrap.classList.remove('ms-closing');
      mobileSearchWrap.style.display = 'none';
    }
    mobileSearchWrap.addEventListener('animationend', onAnimEnd);
  }

  function submitMobileSearch() {
    const q = mobileSearchInput.value.trim();
    if (q) { location.hash = '#/search?q=' + encodeURIComponent(q); closeMobileSearch(); }
  }

  mobileSearchToggle.addEventListener('click', () => {
    if (msOpen) { submitMobileSearch(); }
    else        { closeDrawer(); openMobileSearch(); }
  });

  mobileSearchClear.addEventListener('click', closeMobileSearch);
  mobileSearchForm.addEventListener('submit', e => { e.preventDefault(); submitMobileSearch(); });

  // Touch on AC items: use touchend for selection, touchstart only on individual items to prevent dismiss
  mobileAcWrap.addEventListener('touchstart', e => {
    // Only block default if touch started on a tappable item — allows scroll on the list itself
    if (e.target.closest('.ac-item') || e.target.closest('.ac-footer')) {
      e.preventDefault();
    }
  }, { passive: false });

  mobileAcWrap.addEventListener('touchend', e => {
    const item   = e.target.closest('.ac-item');
    const footer = e.target.closest('.ac-footer');
    if (item) {
      e.preventDefault();
      closeMobileAc(); closeMobileSearch();
      location.hash = '#/post/' + item.dataset.slug;
    } else if (footer) {
      e.preventDefault();
      const q = mobileSearchInput.value.trim();
      if (q) { location.hash = '#/search?q=' + encodeURIComponent(q); closeMobileSearch(); }
    }
  }, { passive: false });

  // Longer blur delay — iOS fires blur before touchend; 350ms gives the tap time to register
  mobileSearchInput.addEventListener('blur', () => {
    setTimeout(() => {
      if (document.activeElement !== mobileSearchInput) {
        closeMobileAc();
        if (!mobileSearchInput.value.trim()) closeMobileSearch();
      }
    }, 350);
  });

  // ── Reposition both dropdowns on resize / orientation change ──────────────
  function onViewportChange() {
    if (searchAcWrap.classList.contains('ac-open')) positionDesktopAc();
    if (msOpen) positionMobileAc();
  }
  window.addEventListener('resize', onViewportChange);
  window.addEventListener('orientationchange', () => setTimeout(onViewportChange, 200));

  const page = parts[0] || '';

  if (!page) {
    document.querySelector('[data-nav="home"]').classList.add('active');
    document.querySelector('#drawerNav [data-nav="home"]')?.classList.add('active');
    content.innerHTML = `<div class="page-head"><h1 class="page-title">${escapeHtml(settings.homepageHeadline || settings.siteTitle)}</h1><p class="page-desc">${escapeHtml(settings.tagline)}</p>${buildCatSocialBar(settings)}</div>`;
    renderPostGrid(content, posts);
  } else if (page === 'post' && parts[1]) {
    // Direct post URL always works for any published post, regardless of showOnHome
    const post = publishedAll.find(p => p.slug === parts[1]);
    if (!post) {
      content.innerHTML = `<div class="empty-state"><h3>Post not found</h3><p><a href="#/" class="btn btn-primary">Back home</a></p></div>`;
      return;
    }
    renderSinglePost(content, post, settings);
  } else if (page === 'about') {
    document.querySelector('[data-nav="about"]')?.classList.add('active');
    document.querySelector('#drawerNav [data-nav="about"]')?.classList.add('active');
    const aboutHtml = settings.aboutContent || `<p><strong>${escapeHtml(settings.siteTitle)}</strong> is powered by MyCMS, a PHP CMS that stores all data as JSON files.</p><p>No database required. Visit <a href="#/login">the admin area</a> to manage posts.</p>`;
    const aboutGallery = Array.isArray(settings.aboutGallery) ? settings.aboutGallery.filter(Boolean) : [];
    const aboutGalleryHtml = aboutGallery.length ? `
      <div class="post-gallery" style="margin-top:40px">
        <h3>Gallery</h3>
        ${buildGalleryGridHtml(aboutGallery, 'aboutGalleryDisplay')}
      </div>` : '';
    content.innerHTML = `<div class="article-wrap"><h1 class="article-title">About</h1>${buildCatSocialBar(settings)}<div class="article-content">${aboutHtml}</div>${aboutGalleryHtml}</div>`;
    // Gallery lightbox (keyboard + swipe + animated — same as post gallery)
    if (aboutGallery.length) {
      let lb = null, lbImg = null, lbCounter = null, lbPrev = null, lbNext = null, curIdx = 0;
      let navigating = false;
      function buildAboutLb() {
        lb = document.createElement('div');
        lb.className = 'gallery-lightbox';
        lb.setAttribute('tabindex', '-1');
        lb.innerHTML = `
          <button class="gallery-lightbox-close" aria-label="Close">✕</button>
          <button class="gallery-lightbox-nav gallery-lightbox-prev" aria-label="Previous image">&#8249;</button>
          <div class="gallery-lightbox-stage">
            <img class="gallery-lightbox-img" src="" alt="Gallery image">
          </div>
          <button class="gallery-lightbox-nav gallery-lightbox-next" aria-label="Next image">&#8250;</button>
          <div class="gallery-lightbox-counter"></div>`;
        lbImg     = lb.querySelector('.gallery-lightbox-img');
        lbCounter = lb.querySelector('.gallery-lightbox-counter');
        lbPrev    = lb.querySelector('.gallery-lightbox-prev');
        lbNext    = lb.querySelector('.gallery-lightbox-next');
        const lbStageAbout = lb.querySelector('.gallery-lightbox-stage');
        if (aboutGallery.length <= 1) { lbPrev.hidden = true; lbNext.hidden = true; }
        lb.addEventListener('click', e => { if (e.target === lb || e.target === lbStageAbout) closeAboutLb(); });
        lb.querySelector('.gallery-lightbox-close').addEventListener('click', closeAboutLb);
        lbPrev.addEventListener('click', () => navAbout(-1));
        lbNext.addEventListener('click', () => navAbout(1));
        lb.addEventListener('keydown', e => {
          if (e.key === 'ArrowRight')     { e.preventDefault(); navAbout(1); }
          else if (e.key === 'ArrowLeft') { e.preventDefault(); navAbout(-1); }
          else if (e.key === 'Escape')    { e.preventDefault(); closeAboutLb(); }
        });
        // Mobile swipe — shared Facebook-style track helper
        attachLbMobileSwipe(lbStageAbout, {
          imgs:       aboutGallery,
          getCurIdx:  () => curIdx,
          setCurIdx:  (n) => { curIdx = n; },
          getLbImg:   () => lbImg,
          setCounter: (t) => { if (lbCounter) lbCounter.textContent = t; }
        });
        document.body.appendChild(lb);
        lb.focus();
      }
      function setAboutImg(idx, enterClass) {
        curIdx = ((idx % aboutGallery.length) + aboutGallery.length) % aboutGallery.length;
        lbImg.style.transition = '';
        lbImg.style.transform  = '';
        lbImg.style.opacity    = '';
        lbImg.classList.remove('lb-slide-next', 'lb-slide-prev', 'lb-exit-left', 'lb-exit-right');
        void lbImg.offsetWidth;
        lbImg.src = aboutGallery[curIdx];
        lbCounter.textContent = (curIdx + 1) + ' / ' + aboutGallery.length;
        if (enterClass) lbImg.classList.add(enterClass);
      }
      function navAbout(dir) {
        if (navigating) return; navigating = true;
        const exitClass  = dir > 0 ? 'lb-exit-left'  : 'lb-exit-right';
        const enterClass = dir > 0 ? 'lb-slide-next'  : 'lb-slide-prev';
        lbImg.style.transition = '';
        lbImg.style.transform  = '';
        lbImg.style.opacity    = '';
        lbImg.classList.remove('lb-slide-next', 'lb-slide-prev');
        void lbImg.offsetWidth;
        lbImg.classList.add(exitClass);
        setTimeout(() => { const next = (curIdx + dir + aboutGallery.length) % aboutGallery.length; setAboutImg(next, enterClass); navigating = false; }, 300);
      }
      function closeAboutLb() { navigating = false; if (lb) { const st = lb.querySelector('.gallery-lightbox-stage'); if (st && st._destroySwipeTrack) st._destroySwipeTrack(); lb.remove(); lb = null; lbImg = null; lbCounter = null; lbPrev = null; lbNext = null; } }
      content.querySelectorAll('.gallery-img').forEach(img => {
        img.addEventListener('click', () => { closeAboutLb(); buildAboutLb(); setAboutImg(+img.dataset.idx, null); });
      });
    }
  } else if (page === 'category' && parts[1]) {
    const cat = decodeURIComponent(parts[1]);
    document.querySelector(`#drawerNav [data-nav="cat-${cat.toLowerCase()}"]`)?.classList.add('active');
    document.querySelector(`.nav [data-nav="cat-${cat.toLowerCase()}"]`)?.classList.add('active');
    // Category pages show ALL published posts in that category, regardless of showOnHome
    const filtered = publishedAll.filter(p => p.category.toLowerCase() === cat.toLowerCase());
    // Use proper casing from actual post data; fallback to capitalizing the slug
    const properCat = (publishedAll.find(p => p.category.toLowerCase() === cat.toLowerCase()) || {}).category || cat;
    const catDisplay = properCat.charAt(0).toUpperCase() + properCat.slice(1);
    content.innerHTML = `<div class="page-head"><h1 class="page-title">${escapeHtml(catDisplay)}</h1><p class="page-desc">${filtered.length} post${filtered.length !== 1 ? 's' : ''}</p>${buildCatSocialBar(settings)}</div>`;
    renderPostGrid(content, filtered);
  } else if (page === 'search') {
    const q = parseHash().query.get('q') || '';
    document.getElementById('siteSearchInput').value = q;
    // Score and sort by relevance across ALL published posts
    const results = q ? searchIndex
      .map(e => ({ post: e.post, score: scorePost(e, q), snip: snippet(e.plainContent, q) }))
      .filter(x => x.score > 0)
      .sort((a, b) => b.score - a.score)
      .map(x => x.post) : [];
    content.innerHTML = `<div class="page-head"><h1 class="page-title">Search</h1><p class="page-desc">${q ? `Results for "<strong>${escapeHtml(q)}</strong>" — ${results.length} found` : 'Enter a search term above'}</p></div>`;
    renderPostGrid(content, results);
  } else if (page === 'gallery') {
    document.querySelector('[data-nav="gallery"]')?.classList.add('active');
    document.querySelector('#drawerNav [data-nav="gallery"]')?.classList.add('active');
    renderSiteGalleryPage(content, settings);
  } else if (page === 'contact') {
    document.querySelector('[data-nav="contact"]')?.classList.add('active');
    document.querySelector('#drawerNav [data-nav="contact"]')?.classList.add('active');
    renderContactPage(content, settings);
  } else {
    content.innerHTML = `<div class="empty-state"><h3>Page not found</h3><p><a href="#/" class="btn btn-primary">Go home</a></p></div>`;
  }
}

function buildCatSocialBar(settings) {
  const fb   = settings && settings.socialFacebook;
  const ig   = settings && settings.socialInstagram;
  const li   = settings && settings.socialLinkedin;
  const be   = settings && settings.socialBehance;
  const mail = settings && settings.socialMail;
  if (!fb && !ig && !li && !be && !mail) return '';
  const links = [];
  if (fb)   links.push(`<a href="${escapeHtml(fb)}" target="_blank" rel="noopener" class="cat-social-fb" title="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="#fff"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>`);
  if (ig)   links.push(`<a href="${escapeHtml(ig)}" target="_blank" rel="noopener" class="cat-social-ig" title="Instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>`);
  if (li)   links.push(`<a href="${escapeHtml(li)}" target="_blank" rel="noopener" class="cat-social-li" title="LinkedIn"><svg width="18" height="18" viewBox="0 0 24 24" fill="#fff"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg></a>`);
  if (be)   links.push(`<a href="${escapeHtml(be)}" target="_blank" rel="noopener" class="cat-social-be" title="Behance"><svg width="18" height="18" viewBox="0 0 24 24" fill="#fff"><path d="M8.5 12.5c.828 0 1.5-.672 1.5-1.5S9.328 9.5 8.5 9.5H6v3h2.5zm.25 2H6v3.5h2.75C10.26 18 11 17.26 11 16.25S10.26 14.5 8.75 14.5zM0 6v12h8.75C11.4 18 13 16.7 13 14.8c0-1.3-.7-2.4-1.8-2.9.8-.5 1.3-1.4 1.3-2.4C12.5 7.6 11 6 8.75 6H0zm15.5 1.5h5v1.25h-5V7.5zm6 6.5h-6.1c.1 1.2 1 2 2.1 2 .9 0 1.6-.5 1.9-1.2H21c-.4 1.8-2 3-3.6 3-2.2 0-4-1.8-4-4s1.8-4 4-4 3.9 1.8 3.9 4c0 .1 0 .1-.1.2h-.2zm-3.9-4c-1 0-1.9.8-2 1.8h4c-.1-1-1-1.8-2-1.8z"/></svg></a>`);
  if (mail) links.push(`<a href="mailto:${escapeHtml(mail)}" target="_blank" rel="noopener" class="cat-social-mail" title="Email"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></a>`);
  return `<div class="cat-social">${links.join('')}</div>`;
}

function renderPostGrid(container, posts) {
  const grid = document.createElement('div');
  grid.className = 'post-grid';
  if (!posts.length) {
    grid.innerHTML = `<div class="empty-state" style="grid-column:1/-1"><h3>No posts yet</h3><p>Check back soon.</p></div>`;
  } else {
    posts.forEach((p, idx) => {
      const card = document.createElement('article');
      card.className = 'post-card card-reveal';
      card.style.animationDelay = `${idx * 0.08}s`;
      card.onclick = () => location.hash = '#/post/' + p.slug;
      const hue = [...p.title].reduce((a, c) => a + c.charCodeAt(0), 0) % 360;
      const bg = `linear-gradient(135deg,hsl(${hue},70%,60%),hsl(${(hue+60)%360},70%,50%))`;
      card.innerHTML = `
        <div class="post-card-img-wrap">${p.featuredImage
          ? `<img src="${escapeHtml(p.featuredImage)}" alt="" class="post-card-img" loading="lazy">`
          : `<div style="width:100%;height:100%;background:${bg}"></div>`}</div>
        <div class="post-card-body">
          <div class="post-card-cat" onclick="event.stopPropagation();location.hash='#/category/${encodeURIComponent(p.category.toLowerCase())}'">${escapeHtml(p.category||'Uncategorized')}</div>
          <h2 class="post-card-title">${escapeHtml(p.title)}</h2>
          <p class="post-card-excerpt">${escapeHtml(p.excerpt||'')}</p>
          <div class="post-card-meta"><span>${formatDate(p.date)}</span><span>•</span><span>${escapeHtml(p.author||'admin')}</span></div>
        </div>`;
      grid.appendChild(card);
    });
  }
  container.appendChild(grid);
}

function getVideoEmbed(url, label, settings) {
  if (!url) return '';
  const esc = escapeHtml;

  // ── YouTube ──────────────────────────────────────────────────────────────
  const ytMatch = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/);
  if (ytMatch) {
    const id = ytMatch[1];
    const start = url.match(/[?&]t=(\d+)/)?.[1] || '';
    const src = `https://www.youtube.com/embed/${id}?rel=0&modestbranding=1${start ? '&start='+start : ''}`;
    return `<div class="video-embed-wrap"><iframe src="${src}" allowfullscreen allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" loading="lazy"></iframe></div>`;
  }

  // ── YouTube playlist ──────────────────────────────────────────────────────
  const ytList = url.match(/youtube\.com\/.*[?&]list=([A-Za-z0-9_-]+)/);
  if (ytList) {
    const src = `https://www.youtube.com/embed/videoseries?list=${ytList[1]}&rel=0`;
    return `<div class="video-embed-wrap"><iframe src="${src}" allowfullscreen loading="lazy"></iframe></div>`;
  }

  // ── Vimeo ─────────────────────────────────────────────────────────────────
  const vimeoMatch = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
  if (vimeoMatch) {
    return `<div class="video-embed-wrap"><iframe src="https://player.vimeo.com/video/${vimeoMatch[1]}?byline=0&portrait=0" allowfullscreen loading="lazy"></iframe></div>`;
  }

  // ── Dailymotion ───────────────────────────────────────────────────────────
  const dmMatch = url.match(/dailymotion\.com\/video\/([A-Za-z0-9]+)/);
  if (dmMatch) {
    return `<div class="video-embed-wrap"><iframe src="https://www.dailymotion.com/embed/video/${dmMatch[1]}?autoplay=0" allowfullscreen loading="lazy"></iframe></div>`;
  }

  // ── Twitch channel ────────────────────────────────────────────────────────
  const twitchChannel = url.match(/twitch\.tv\/([A-Za-z0-9_]+)\/?$/);
  if (twitchChannel) {
    const host = encodeURIComponent(window.location.hostname || 'localhost');
    return `<div class="video-embed-wrap"><iframe src="https://player.twitch.tv/?channel=${twitchChannel[1]}&parent=${host}" allowfullscreen loading="lazy"></iframe></div>`;
  }

  // ── Twitch VOD ────────────────────────────────────────────────────────────
  const twitchVod = url.match(/twitch\.tv\/videos\/(\d+)/);
  if (twitchVod) {
    const host = encodeURIComponent(window.location.hostname || 'localhost');
    return `<div class="video-embed-wrap"><iframe src="https://player.twitch.tv/?video=${twitchVod[1]}&parent=${host}" allowfullscreen loading="lazy"></iframe></div>`;
  }

  // ── Facebook video ────────────────────────────────────────────────────────
  if (/facebook\.com\/.+\/(videos|reel)\//.test(url) || /fb\.watch\//.test(url)) {
    const fbSrc = `https://www.facebook.com/plugins/video.php?href=${encodeURIComponent(url)}&show_text=false&width=734`;
    return `<div class="video-embed-wrap"><iframe src="${fbSrc}" allowfullscreen allow="autoplay;clipboard-write;encrypted-media;picture-in-picture;web-share" loading="lazy"></iframe></div>`;
  }

  // ── TikTok ────────────────────────────────────────────────────────────────
  const tiktokMatch = url.match(/tiktok\.com\/@[^/]+\/video\/(\d+)/);
  if (tiktokMatch) {
    return `<div class="video-embed-wrap" style="padding-bottom:177%"><iframe src="https://www.tiktok.com/embed/v2/${tiktokMatch[1]}" allowfullscreen allow="encrypted-media" loading="lazy"></iframe></div>`;
  }

  // ── Twitter / X ───────────────────────────────────────────────────────────
  const twitterMatch = url.match(/(?:twitter|x)\.com\/[^/]+\/status\/(\d+)/);
  if (twitterMatch) {
    return `<div class="video-embed-wrap" style="padding-bottom:75%"><iframe src="https://platform.twitter.com/embed/Tweet.html?id=${twitterMatch[1]}&theme=dark" allowfullscreen loading="lazy"></iframe></div>`;
  }

  // ── Streamable ────────────────────────────────────────────────────────────
  const streamableMatch = url.match(/streamable\.com\/([A-Za-z0-9]+)/);
  if (streamableMatch) {
    return `<div class="video-embed-wrap"><iframe src="https://streamable.com/e/${streamableMatch[1]}" allowfullscreen loading="lazy"></iframe></div>`;
  }

  // ── Rumble ────────────────────────────────────────────────────────────────
  const rumbleMatch = url.match(/rumble\.com\/embed\/([A-Za-z0-9]+)/);
  if (rumbleMatch) {
    return `<div class="video-embed-wrap"><iframe src="https://rumble.com/embed/${rumbleMatch[1]}/" allowfullscreen loading="lazy"></iframe></div>`;
  }

  // ── Google Drive video ───────────────────────────────────────────────────
  const driveMatch = url.match(/drive\.google\.com\/file\/d\/([A-Za-z0-9_-]+)/);
  if (driveMatch) {
    return `<div class="video-embed-wrap"><iframe src="https://drive.google.com/file/d/${driveMatch[1]}/preview" allowfullscreen loading="lazy"></iframe></div>`;
  }

  // ── Direct video file (mp4, webm, ogg, mov) ──────────────────────────────
  if (/\.(mp4|webm|ogg|mov)(\?.*)?$/i.test(url)) {
    return `<div class="video-embed-wrap"><video controls preload="metadata" playsinline><source src="${esc(url)}">Your browser does not support the video tag.</video></div>`;
  }

  // ── Generic / unknown URL → custom player shell with iframe ──────────────
  // Attempt to embed any remaining HTTP/HTTPS URL inside a themed player shell.
  if (/^https?:\/\//i.test(url)) {
    const logoHtml = settings && settings.logoUrl
      ? `<img src="${esc(settings.logoUrl)}" alt="${esc(settings.siteTitle||'')}" class="vp-overlay-logo">`
      : '';
    const titleHtml = settings && settings.siteTitle
      ? `<span class="vp-overlay-title">${esc(settings.siteTitle)}</span>`
      : '';
    return `
      <div class="video-player-shell">
        <div class="video-player-toolbar">
          <span class="video-player-badge">▶ Player</span>
        </div>
        <div class="video-player-frame-wrap">
          <iframe src="${esc(url)}" allowfullscreen allow="autoplay;fullscreen;picture-in-picture"
            loading="lazy" sandbox="allow-scripts allow-same-origin allow-forms allow-presentation allow-popups"></iframe>
          <div class="vp-overlay">
            ${logoHtml}
            ${titleHtml}
          </div>
        </div>
      </div>`;
  }

  // ── Absolute last resort: link ───────────────────────────────────────────
  return `<a href="${esc(url)}" target="_blank" rel="noopener" class="video-link-fallback"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>${esc(label || url)}</a>`;
}

// ══════════════════════════════════════════════════════════════════════════════
// Article scroll-reveal  (IntersectionObserver — fires once per element)
// ══════════════════════════════════════════════════════════════════════════════

function initArticleReveal(wrap) {
  if (!wrap) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);   // animate once only
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

  function watch(el, extraClass, delayS) {
    if (!el) return;
    el.classList.add('scroll-reveal');
    if (extraClass) el.classList.add(extraClass);
    if (delayS)     el.style.transitionDelay = delayS + 's';
    observer.observe(el);
  }

  // ── 1. Above-fold header: cascade in immediately (they're in viewport on load)
  watch(wrap.querySelector('.article-cat'),  null,        0);
  watch(wrap.querySelector('.article-title'),null,        0.07);
  watch(wrap.querySelector('.article-meta'), null,        0.14);

  // ── 2. Hero image or first-video block
  const hero = wrap.querySelector('.article-hero') || wrap.querySelector('.post-video-hero');
  watch(hero, 'sr-media', 0.06);

  // ── 3. Every direct child of .article-content slides up on scroll
  const contentChildren = wrap.querySelectorAll('.article-content > *');
  contentChildren.forEach((el, i) => {
    const isMedia = el.tagName === 'IMG' || el.tagName === 'FIGURE' ||
                    el.classList.contains('video-embed-wrap') ||
                    el.classList.contains('video-player-shell');
    // Stagger capped at 0.25 s so long articles don't lag
    const delay = Math.min(i * 0.06, 0.25);
    watch(el, isMedia ? 'sr-media' : 'sr-content', delay);
  });

  // ── 4. "More Videos" section heading + each video item
  const moreVideos = wrap.querySelector('.post-videos');
  if (moreVideos) {
    watch(moreVideos.querySelector('h3'), null, 0);
    moreVideos.querySelectorAll('.video-item').forEach((el, i) => {
      watch(el, 'sr-media', i * 0.1);
    });
  }

  // ── 5. Gallery heading + each image with a tight stagger
  const gallery = wrap.querySelector('.post-gallery');
  if (gallery) {
    watch(gallery.querySelector('h3'), null, 0);
    gallery.querySelectorAll('.gallery-img').forEach((el, i) => {
      watch(el, 'sr-media', Math.min(i * 0.07, 0.35));
    });
  }

  // ── 6. Back-button row
  const backRow = wrap.querySelector('div[style*="border-top"]');
  watch(backRow, null, 0);
}

// ── Shared helper: builds gallery-grid HTML with mobile odd-layout support ──
function buildGalleryGridHtml(imgs, idAttr) {
  const isOdd = imgs.length % 2 !== 0 && imgs.length > 1;
  const idPart = idAttr ? ` id="${idAttr}"` : '';
  let inner;
  if (isOdd) {
    const [first, ...rest] = imgs;
    inner = `
      <div class="gallery-first-row">
        <img src="${escapeHtml(first)}" alt="Gallery image 1" class="gallery-img" loading="lazy" data-idx="0" style="animation-delay:0s">
      </div>
      <div class="gallery-rest">
        ${rest.map((src, i) => `<img src="${escapeHtml(src)}" alt="Gallery image ${i+2}" class="gallery-img" loading="lazy" data-idx="${i+1}" style="animation-delay:${((i+1)*0.045).toFixed(3)}s">`).join('')}
      </div>`;
  } else {
    inner = imgs.map((src, i) => `<img src="${escapeHtml(src)}" alt="Gallery image ${i+1}" class="gallery-img" loading="lazy" data-idx="${i}" style="animation-delay:${(i*0.045).toFixed(3)}s">`).join('');
  }
  return `<div class="gallery-grid${isOdd ? ' gallery-grid--odd' : ''}"${idPart}>${inner}</div>`;
}

function renderSinglePost(container, post, settings) {
  const hue = [...post.title].reduce((a, c) => a + c.charCodeAt(0), 0) % 360;
  const bg = `linear-gradient(135deg,hsl(${hue},70%,60%),hsl(${(hue+60)%360},70%,50%))`;
  const videos = Array.isArray(post.videos) ? post.videos.filter(v => v && v.url) : [];
  const gallery = Array.isArray(post.gallery) ? post.gallery.filter(Boolean) : [];

  // First video placed right after title+meta (if any videos exist)
  const firstVideoHtml = videos.length ? `
    <div class="post-video-hero">
      ${videos[0].label ? `<div class="video-item-label">${escapeHtml(videos[0].label)}</div>` : ''}
      ${getVideoEmbed(videos[0].url, videos[0].label, settings)}
    </div>` : '';

  // Featured image placed after title+meta only when there are NO videos
  const featuredImageHtml = !videos.length
    ? (post.featuredImage
        ? `<img src="${escapeHtml(post.featuredImage)}" alt="" class="article-hero">`
        : '')
    : '';

  // Remaining videos (index 1+) go below the content
  const remainingVideosHtml = videos.length > 1 ? `
    <div class="post-videos">
      <h3>More Videos</h3>
      ${videos.slice(1).map(v => `<div class="video-item">${v.label ? `<div class="video-item-label">${escapeHtml(v.label)}</div>` : ''}${getVideoEmbed(v.url, v.label, settings)}</div>`).join('')}
    </div>` : '';

  const galleryHtml = gallery.length ? `
    <div class="post-gallery">
      <h3>Gallery</h3>
      ${buildGalleryGridHtml(gallery, 'postGallery')}
    </div>` : '';

  container.innerHTML = `
    <article class="article-wrap">
      <a href="#/category/${encodeURIComponent(post.category.toLowerCase())}" class="article-cat">${escapeHtml(post.category)}</a>
      <h1 class="article-title">${escapeHtml(post.title)}</h1>
      <div class="article-meta"><span>${formatDate(post.date)}</span><span>•</span><span>By ${escapeHtml(post.author||'admin')}</span></div>
      ${videos.length ? firstVideoHtml : featuredImageHtml}
      <div class="article-content">${post.content}</div>
      ${remainingVideosHtml}
      ${galleryHtml}
      <div class="share-bar" id="shareBar">
        <span class="share-bar-label">Share</span>
        <a class="share-btn share-btn-facebook" id="shareFb" href="#" target="_blank" rel="noopener" aria-label="Share on Facebook">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>Facebook
        </a>
        <a class="share-btn share-btn-twitter" id="shareX" href="#" target="_blank" rel="noopener" aria-label="Share on X (Twitter)">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>X / Twitter
        </a>
        <a class="share-btn share-btn-whatsapp" id="shareWa" href="#" target="_blank" rel="noopener" aria-label="Share on WhatsApp">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>WhatsApp
        </a>
        <a class="share-btn share-btn-messenger" id="shareMsgr" href="#" target="_blank" rel="noopener" aria-label="Share on Messenger">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.145 2 11.259c0 2.82 1.323 5.337 3.402 7.013V21l2.988-1.542c.795.207 1.637.318 2.61.318 5.523 0 10-4.144 10-9.258C21.999 6.144 17.523 2 12 2zm1.002 12.453l-2.548-2.633-4.979 2.633 5.474-5.812 2.61 2.633 4.916-2.633-5.473 5.812z"/></svg>Messenger
        </a>
        <div class="share-counter" id="shareCounter" title="Total shares">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
          <span class="share-counter-num" id="shareCountNum">0</span>
          <span>shares</span>
        </div>
      </div>
      <div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--wp-gray-10);display:flex;gap:10px;flex-wrap:wrap"><a href="#/" class="btn btn-secondary">← Home</a><a href="#/category/${encodeURIComponent(post.category.toLowerCase())}" class="btn btn-secondary">📂 ${escapeHtml(post.category)}</a></div>
    </article>`;

  // ── Scroll-reveal animations ────────────────────────────────────────────────
  initArticleReveal(container.querySelector('.article-wrap'));

  // ── Make all links in article content open in a new tab ──────────────────
  container.querySelectorAll('.article-content a').forEach(a => {
    if (!a.getAttribute('target')) a.setAttribute('target', '_blank');
    if (!a.getAttribute('rel')) a.setAttribute('rel', 'noopener');
  });

  // ── Share buttons ────────────────────────────────────────────────────────────
  (function () {
    // Build the canonical shareable post URL using ?post=SLUG (not the hash
    // fragment) so PHP can inject proper OG meta tags server-side.
    // Social crawlers never receive hash fragments, so #/post/slug would give
    // them a blank page with no OG tags.
    const base = (settings && settings.siteUrl)
      ? settings.siteUrl
      : (window.location.origin + window.location.pathname.replace(/\/$/, ''));
    const sharePermalink = base + '/?post=' + encodeURIComponent(post.slug);
    const postUrl   = encodeURIComponent(sharePermalink);
    const rawUrl    = sharePermalink;
    const postTitle = encodeURIComponent(post.title);

    // Facebook
    const fbBtn = document.getElementById('shareFb');
    if (fbBtn) fbBtn.href = `https://www.facebook.com/sharer/sharer.php?u=${postUrl}`;

    // X / Twitter
    const xBtn = document.getElementById('shareX');
    if (xBtn) xBtn.href = `https://x.com/intent/tweet?text=${postTitle}&url=${postUrl}`;

    // WhatsApp
    const waBtn = document.getElementById('shareWa');
    if (waBtn) waBtn.href = `https://wa.me/?text=${postTitle}%20${postUrl}`;

    // Messenger
    const msgrBtn = document.getElementById('shareMsgr');
    if (msgrBtn) msgrBtn.href = `https://www.facebook.com/dialog/send?link=${postUrl}&app_id=291494419107518&redirect_uri=${postUrl}`;

    // Share counter -- load current count, then increment on any share click
    const countEl = document.getElementById('shareCountNum');

    // Fetch current count from server
    async function loadShareCount() {
      try {
        const d = await API.call('posts');
        const p = (d.posts || []).find(p => p.id === post.id);
        if (countEl && p) countEl.textContent = p.shareCount || 0;
      } catch(e) {}
    }
    loadShareCount();

    // Increment share count on server and animate the number
    async function trackShare() {
      try {
        const res = await fetch('api.php?action=share', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: post.id })
        });
        const d = await res.json();
        if (countEl && d.shareCount !== undefined) {
          countEl.textContent = d.shareCount;
          countEl.classList.remove('bump');
          void countEl.offsetWidth;
          countEl.classList.add('bump');
          setTimeout(() => countEl.classList.remove('bump'), 400);
        }
      } catch(e) {}
    }

    // Wire all share buttons to increment counter
    ['shareFb','shareX','shareWa','shareMsgr'].forEach(id => {
      const btn = document.getElementById(id);
      if (btn) btn.addEventListener('click', trackShare);
    });

    // ── Dynamic Open Graph / Twitter Card meta tags ──────────────────────────
    // Updates <meta> tags in <head> so browser sharing tools (Web Share API,
    // copy-link) pick up the right data. The server-rendered OG tags (above)
    // are the canonical source for social crawlers; these JS ones keep the
    // page metadata in sync during client-side navigation.
    function setMeta(prop, content, isName) {
      const attr = isName ? 'name' : 'property';
      let el = document.querySelector(`meta[${attr}="${prop}"]`);
      if (!el) { el = document.createElement('meta'); el.setAttribute(attr, prop); document.head.appendChild(el); }
      el.setAttribute('content', content);
    }
    const canonicalUrl = sharePermalink;
    const imgUrl = post.featuredImage || '';
    const desc = post.excerpt || post.title;
    // Open Graph
    setMeta('og:type',        'article',     false);
    setMeta('og:title',       post.title,    false);
    setMeta('og:description', desc,          false);
    setMeta('og:url',         canonicalUrl,  false);
    if (imgUrl) setMeta('og:image', imgUrl,  false);
    if (settings && settings.siteTitle) setMeta('og:site_name', settings.siteTitle, false);
    // Twitter Card
    setMeta('twitter:card',        imgUrl ? 'summary_large_image' : 'summary', true);
    setMeta('twitter:title',       post.title,   true);
    setMeta('twitter:description', desc,         true);
    if (imgUrl) setMeta('twitter:image', imgUrl, true);
    // Page title
    document.title = post.title + (settings && settings.siteTitle ? ' — ' + settings.siteTitle : '');
  })();

  // ── Gallery lightbox (keyboard + swipe + animated) ──────────────────────────
  if (gallery.length) {
    let lb = null, lbImg = null, lbCounter = null, lbPrev = null, lbNext = null, curIdx = 0;
    let navigating = false;

    function buildLightbox() {
      lb = document.createElement('div');
      lb.className = 'gallery-lightbox';
      lb.setAttribute('tabindex', '-1');
      lb.innerHTML = `
        <button class="gallery-lightbox-close" aria-label="Close">✕</button>
        <button class="gallery-lightbox-nav gallery-lightbox-prev" aria-label="Previous image">&#8249;</button>
        <div class="gallery-lightbox-stage">
          <img class="gallery-lightbox-img" src="" alt="Gallery image">
        </div>
        <button class="gallery-lightbox-nav gallery-lightbox-next" aria-label="Next image">&#8250;</button>
        <div class="gallery-lightbox-counter"></div>`;
      lbImg     = lb.querySelector('.gallery-lightbox-img');
      lbCounter = lb.querySelector('.gallery-lightbox-counter');
      lbPrev    = lb.querySelector('.gallery-lightbox-prev');
      lbNext    = lb.querySelector('.gallery-lightbox-next');
      const lbStagePost = lb.querySelector('.gallery-lightbox-stage');

      if (gallery.length <= 1) { lbPrev.hidden = true; lbNext.hidden = true; }

      lb.addEventListener('click', e => { if (e.target === lb || e.target === lbStagePost) closeLb(); });
      lb.querySelector('.gallery-lightbox-close').addEventListener('click', closeLb);
      lbPrev.addEventListener('click', () => navigate(-1));
      lbNext.addEventListener('click', () => navigate(1));

      lb.addEventListener('keydown', e => {
        if (e.key === 'ArrowRight')     { e.preventDefault(); navigate(1); }
        else if (e.key === 'ArrowLeft') { e.preventDefault(); navigate(-1); }
        else if (e.key === 'Escape')    { e.preventDefault(); closeLb(); }
      });

      // Mobile swipe — shared Facebook-style track helper
      attachLbMobileSwipe(lbStagePost, {
        imgs:       gallery,
        getCurIdx:  () => curIdx,
        setCurIdx:  (n) => { curIdx = n; },
        getLbImg:   () => lbImg,
        setCounter: (t) => { if (lbCounter) lbCounter.textContent = t; }
      });

      document.body.appendChild(lb);
      lb.focus();
    }
    function setImage(idx, enterClass) {
      curIdx = ((idx % gallery.length) + gallery.length) % gallery.length;
      // clear ALL inline styles before applying animation class
      lbImg.style.transition = '';
      lbImg.style.transform  = '';
      lbImg.style.opacity    = '';
      lbImg.classList.remove('lb-slide-next', 'lb-slide-prev', 'lb-exit-left', 'lb-exit-right');
      void lbImg.offsetWidth;
      lbImg.src = gallery[curIdx];
      lbImg.alt = 'Gallery image ' + (curIdx + 1);
      lbCounter.textContent = (curIdx + 1) + ' / ' + gallery.length;
      if (enterClass) lbImg.classList.add(enterClass);
    }

    function navigate(dir) {
      if (navigating) return;
      navigating = true;
      const exitClass  = dir > 0 ? 'lb-exit-left' : 'lb-exit-right';
      const enterClass = dir > 0 ? 'lb-slide-next' : 'lb-slide-prev';
      // clear inline transform/opacity so the exit keyframe owns the motion
      lbImg.style.transition = '';
      lbImg.style.transform  = '';
      lbImg.style.opacity    = '';
      lbImg.classList.remove('lb-slide-next', 'lb-slide-prev');
      void lbImg.offsetWidth;
      lbImg.classList.add(exitClass);
      setTimeout(() => {
        const next = (curIdx + dir + gallery.length) % gallery.length;
        setImage(next, enterClass);
        navigating = false;
      }, 300);
    }

    function openLb(idx) {
      closeLb();
      buildLightbox();
      setImage(idx, null);
    }

    function closeLb() {
      navigating = false;
      if (lb) { const st = lb.querySelector('.gallery-lightbox-stage'); if (st && st._destroySwipeTrack) st._destroySwipeTrack(); lb.remove(); lb = null; lbImg = null; lbCounter = null; lbPrev = null; lbNext = null; }
    }

    container.querySelectorAll('.gallery-img').forEach(img => {
      img.addEventListener('click', () => openLb(+img.dataset.idx));
    });
  }
}

// ══════════════════════════════════════════════════════════════════════════════
// Login
// ══════════════════════════════════════════════════════════════════════════════

async function renderLogin() {
  const settings = await API.getSettings();
  document.title = 'Log In — ' + settings.siteTitle;
  document.body.className = 'login-body';
  _pageContent.innerHTML = `
    <div class="login-wrap">
      <h1 class="login-logo"><span class="logo-dot"></span>MyCMS</h1>
      <div class="login-card">
        <div id="loginError" style="display:none" class="login-error"></div>
        <form id="loginForm">
          <div class="login-field"><label class="login-label" for="user">Username</label><input class="login-input" id="user" autocomplete="username" required></div>
          <div class="login-field"><label class="login-label" for="pass">Password</label><input class="login-input" id="pass" type="password" autocomplete="current-password" required></div>
          <button class="btn-login" type="submit">Log In</button>
        </form>
        <div class="login-hint">Default: <strong>admin</strong> / <strong>admin123</strong></div>
      </div>
      <p style="text-align:center;margin-top:16px"><a href="#/" style="color:var(--wp-gray-50);font-size:13px">← Back to site</a></p>
    </div>`;

  document.getElementById('loginForm').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    btn.disabled = true; btn.textContent = 'Logging in…';
    const u = document.getElementById('user').value.trim();
    const p = document.getElementById('pass').value;
    const res = await API.login(u, p);
    if (res.ok) {
      location.hash = '#/admin';
    } else {
      const err = document.getElementById('loginError');
      err.style.display = 'block';
      err.textContent = res.error || 'Invalid username or password.';
      btn.disabled = false; btn.textContent = 'Log In';
    }
  });
}

// ══════════════════════════════════════════════════════════════════════════════
// Admin shell
// ══════════════════════════════════════════════════════════════════════════════

async function renderAdmin(parts, sess) {
  const settings = await API.getSettings();
  document.title = 'Dashboard — ' + settings.siteTitle;
  document.body.className = '';
  _pageContent.innerHTML = `
    <div class="wp-admin">
      <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-logo"><span class="logo-dot"></span>MyCMS</div>
        <ul class="admin-menu" id="adminMenu">
          <li><a href="#/admin" data-page="dashboard"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Dashboard</a></li>
          <li><a href="#/admin/posts" data-page="posts"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Posts</a></li>
          <li><a href="#/admin/posts/new" data-page="new"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Add New</a></li>
          <li><a href="#/admin/categories" data-page="categories"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>Categories</a></li>
          <li><a href="#/admin/media" data-page="media"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>Media</a></li>
          <div class="menu-sep"></div>
          <li><a href="#/admin/about" data-page="about"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>About Page</a></li>
          <li><a href="#/admin/gallery" data-page="gallery"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/><line x1="3" y1="9" x2="21" y2="9"/></svg>Gallery Page</a></li>
          <li><a href="#/admin/contacts" data-page="contacts"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>Contact Inbox<span id="sidebarUnreadBadge" style="display:none" class="unread-badge">0</span></a></li>
          <li><a href="#/admin/settings" data-page="settings"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 1 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c0 .66.26 1.3.73 1.77.47.46 1.11.73 1.77.73H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>Settings</a></li>
          <li><a href="#/" target="_blank"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>View Site</a></li>
          <div class="menu-sep"></div>
          <li><a href="#" id="logoutBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Logout</a></li>
        </ul>
      </aside>
      <div class="admin-main">
        <div class="admin-topbar">
          <button class="admin-burger" id="adminBurger">☰</button>
          <div class="admin-title" id="adminPageTitle">Dashboard</div>
          <div class="admin-user"><span>Howdy, ${escapeHtml(sess.username)}</span><div class="admin-avatar">${escapeHtml(sess.username.charAt(0).toUpperCase())}</div></div>
        </div>
        <div class="admin-content" id="adminContent"></div>
      </div>
    </div>
    <div class="admin-overlay" id="adminOverlay"></div>`;

  document.getElementById('logoutBtn').addEventListener('click', async e => {
    e.preventDefault();
    await API.logout();
    location.hash = '#/login';
  });

  const burger  = document.getElementById('adminBurger');
  const sidebar = document.getElementById('adminSidebar');
  const overlay = document.getElementById('adminOverlay');
  burger.addEventListener('click',  () => { sidebar.classList.toggle('open'); overlay.classList.toggle('open'); });
  overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('open'); });

  const page   = parts[1] || 'dashboard';
  const active = document.querySelector(`#adminMenu a[data-page="${page}"]`);
  if (parts[1] === 'posts' && parts[2] === 'new') {
    document.querySelector('[data-page="new"]')?.classList.add('active');
  } else if (active) {
    active.classList.add('active');
  }

  const content = document.getElementById('adminContent');
  const titleEl = document.getElementById('adminPageTitle');
  const posts   = await API.getPosts();

  // ── Sidebar unread badge ───────────────────────────────────────────────────
  (async () => {
    try {
      const d = await API.getContacts();
      const badge = document.getElementById('sidebarUnreadBadge');
      if (badge && d.unread > 0) { badge.textContent = d.unread; badge.style.display = 'inline-flex'; }
    } catch(e) {}
  })();

  if (!page || page === 'dashboard')  { titleEl.textContent = 'Dashboard'; renderDashboard(content, posts); }
  else if (page === 'posts') {
    if (parts[2] === 'new')                    { titleEl.textContent = 'Add New Post';   await renderPostEditor(content); }
    else if (parts[2] === 'edit' && parts[3])  { titleEl.textContent = 'Edit Post';      await renderPostEditor(content, parts[3], posts); }
    else                                        { titleEl.textContent = 'Posts';          renderPostsList(content, posts); }
  }
  else if (page === 'media')      { titleEl.textContent = 'Media Library';  await renderMedia(content, posts); }
  else if (page === 'categories') { titleEl.textContent = 'Categories';      renderCategories(content); }
  else if (page === 'about')      { titleEl.textContent = 'About Page';      renderAboutEditor(content); }
  else if (page === 'gallery')    { titleEl.textContent = 'Gallery Page';    renderGalleryEditor(content); }
  else if (page === 'contacts')   { titleEl.textContent = 'Contact Inbox';   renderContactInbox(content); }
  else if (page === 'settings')   { titleEl.textContent = 'Settings';        renderSettings(content); }
}

// ══════════════════════════════════════════════════════════════════════════════
// Dashboard
// ══════════════════════════════════════════════════════════════════════════════

function renderDashboard(container, posts) {
  const published = posts.filter(p => p.status === 'published').length;
  const drafts    = posts.filter(p => p.status === 'draft').length;
  const recent    = [...posts].sort((a, b) => new Date(b.date) - new Date(a.date)).slice(0, 5);
  const cats      = [...new Set(posts.map(p => p.category).filter(Boolean))];
  container.innerHTML = `
    <div class="wp-notice">Welcome to MyCMS! Data is saved as JSON files in the <code>data/</code> folder on the server.</div>
    <div class="dashboard-grid">
      <div class="stat-card"><div class="stat-label">Total Posts</div><div class="stat-value">${posts.length}</div><div class="stat-sub">All time</div></div>
      <div class="stat-card"><div class="stat-label">Published</div><div class="stat-value">${published}</div><div class="stat-sub">Live on site</div></div>
      <div class="stat-card"><div class="stat-label">Drafts</div><div class="stat-value">${drafts}</div><div class="stat-sub">In progress</div></div>
      <div class="stat-card"><div class="stat-label">Categories</div><div class="stat-value">${cats.length}</div><div class="stat-sub"><a href="#/admin/categories">Manage</a></div></div>
      <div class="stat-card" id="dashContactStat"><div class="stat-label">Unread Messages</div><div class="stat-value" id="dashUnreadVal">—</div><div class="stat-sub"><a href="#/admin/contacts">View Inbox</a></div></div>
    </div>
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start">
      <div class="wp-card"><div class="wp-card-header"><h2 class="wp-card-title">Recent Posts</h2><a href="#/admin/posts" class="btn btn-secondary">View all</a></div><div class="wp-card-body" style="padding:0"><div class="table-wrap"><table class="wp-table"><thead><tr><th>Title</th><th>Status</th><th>Date</th></tr></thead><tbody>${recent.map(p=>`<tr><td><a href="#/admin/posts/edit/${p.id}" class="row-title">${escapeHtml(p.title)}</a></td><td><span class="status-badge status-${p.status}">${p.status}</span></td><td>${formatDate(p.date)}</td></tr>`).join('')}</tbody></table></div></div></div>
      <div class="wp-card"><div class="wp-card-header"><h2 class="wp-card-title">Quick Actions</h2></div><div class="wp-card-body" style="display:flex;flex-direction:column;gap:10px"><a href="#/admin/posts/new" class="btn btn-primary">+ New Post</a><a href="#/admin/contacts" class="btn btn-secondary">📬 Contact Inbox</a><a href="#/admin/categories" class="btn btn-secondary">Manage Categories</a><a href="#/admin/settings" class="btn btn-secondary">Site Settings</a><a href="#/" target="_blank" class="btn btn-secondary">View Site</a></div></div>
    </div>
    <style>@media(max-width:900px){[style*="grid-template-columns:2fr 1fr"]{grid-template-columns:1fr!important}}</style>`;
  // Load unread count async
  (async () => {
    try {
      const d = await API.getContacts();
      const el = document.getElementById('dashUnreadVal');
      if (el) el.textContent = d.unread ?? 0;
    } catch(e) {}
  })();
}

// ══════════════════════════════════════════════════════════════════════════════
// Posts list
// ══════════════════════════════════════════════════════════════════════════════

function renderPostsList(container, posts) {
  let filter = 'all', search = '';

  // One-time render of the card shell + toolbar (never re-rendered)
  container.innerHTML = `
    <div class="wp-card">
      <div class="wp-card-header">
        <div class="toolbar" style="width:100%;margin:0">
          <div class="filter-tabs" id="filterTabs">
            <button data-f="all" class="active">All (${posts.length})</button>
            <button data-f="published">Published (${posts.filter(p=>p.status==='published').length})</button>
            <button data-f="draft">Drafts (${posts.filter(p=>p.status==='draft').length})</button>
          </div>
          <div class="search-mini" style="margin-left:0;flex:1 1 auto">
            <input type="search" placeholder="Search posts..." id="postSearch" autocomplete="off">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          </div>
          <a href="#/admin/posts/new" class="btn btn-primary" style="margin-left:auto">Add New</a>
        </div>
      </div>
      <div class="wp-card-body" style="padding:0">
        <div class="table-wrap">
          <table class="wp-table">
            <thead><tr><th style="width:40%">Title</th><th>Category</th><th>Status</th><th>Home</th><th>Date</th></tr></thead>
            <tbody id="postsTableBody"></tbody>
          </table>
        </div>
      </div>
    </div>`;

  function drawRows() {
    let filtered = [...posts];
    if (filter !== 'all') filtered = filtered.filter(p => p.status === filter);
    if (search) filtered = filtered.filter(p =>
      p.title.toLowerCase().includes(search) || (p.excerpt || '').toLowerCase().includes(search));
    filtered.sort((a, b) => new Date(b.date) - new Date(a.date));

    // Update filter tab active states + counts
    container.querySelectorAll('[data-f]').forEach(b => {
      b.classList.toggle('active', b.dataset.f === filter);
    });
    // Update counts
    container.querySelector('[data-f="all"]').textContent = `All (${posts.length})`;
    container.querySelector('[data-f="published"]').textContent = `Published (${posts.filter(p=>p.status==='published').length})`;
    container.querySelector('[data-f="draft"]').textContent = `Drafts (${posts.filter(p=>p.status==='draft').length})`;

    const tbody = document.getElementById('postsTableBody');
    tbody.innerHTML = filtered.map(p=>`<tr>
      <td><div class="row-title" onclick="location.hash='#/admin/posts/edit/${p.id}'">${escapeHtml(p.title)}</div>
        <div class="row-actions">
          <a href="#/admin/posts/edit/${p.id}">Edit</a>
          ${p.status==='published'?`<a href="#/post/${p.slug}" target="_blank">View</a>`:''}
          <a href="#" class="delete" data-id="${p.id}">Trash</a>
        </div></td>
      <td>${escapeHtml(p.category||'—')}</td>
      <td><span class="status-badge status-${p.status}">${p.status}</span></td>
      <td title="${p.showOnHome === true ? 'Shows on homepage' : 'Not on homepage'}" style="text-align:center;font-size:15px">${p.showOnHome === true ? '🏠' : '<span style="color:var(--wp-gray-20)">—</span>'}</td>
      <td>${formatDate(p.date)}</td>
    </tr>`).join('') || `<tr><td colspan="5" style="text-align:center;padding:40px;color:var(--wp-gray-50)">No posts found</td></tr>`;

    tbody.querySelectorAll('.delete').forEach(a => a.addEventListener('click', async e => {
      e.preventDefault();
      if (!confirm('Move this post to trash?')) return;
      const id = a.dataset.id;
      await API.deletePost(id);
      const idx = posts.findIndex(p => p.id === id);
      if (idx > -1) { posts.splice(idx, 1); drawRows(); }
    }));
  }

  // Bind filter tabs
  container.querySelectorAll('[data-f]').forEach(b => b.addEventListener('click', () => { filter = b.dataset.f; drawRows(); }));

  // Bind search — input stays in DOM, only rows re-render
  document.getElementById('postSearch').addEventListener('input', e => {
    search = e.target.value.toLowerCase();
    drawRows();
  });

  drawRows();
}

// ══════════════════════════════════════════════════════════════════════════════
// Post editor
// ══════════════════════════════════════════════════════════════════════════════

async function renderPostEditor(container, id = null, posts = []) {
  const isEdit = !!id;
  const post   = isEdit ? posts.find(p => p.id === id) : null;
  if (isEdit && !post) { container.innerHTML = '<p>Post not found</p>'; return; }
  const data = post || {
    id: '', title: '', slug: '', content: '', excerpt: '', featuredImage: '',
    category: 'Uncategorized', tags: [], status: 'draft', showOnHome: false,
    date: new Date().toISOString(), author: 'admin', videos: [], gallery: []
  };
  if (!data.videos) data.videos = [];
  if (!data.gallery) data.gallery = [];

  // Load categories + current settings (need homepageDisplay for the hint)
  const [cats, siteSettings] = await Promise.all([API.getCategories(), API.getSettings()]);
  const homepageMode = siteSettings.homepageDisplay || 'all';
  const catOptions = cats.map(c =>
    `<option value="${escapeHtml(c.name)}" ${c.name === data.category ? 'selected' : ''}>${escapeHtml(c.name)}</option>`
  ).join('');
  // Fallback: if current category not in list, add it
  const inList = cats.some(c => c.name === data.category);
  const extraOption = (!inList && data.category)
    ? `<option value="${escapeHtml(data.category)}" selected>${escapeHtml(data.category)}</option>`
    : '';

  container.innerHTML = `
    <form id="postForm"><div class="form-grid">
      <div class="form-main">
        <div class="wp-card"><div class="wp-card-body">
          <div class="form-row"><input type="text" class="form-input title-input" id="postTitle" placeholder="Add title" value="${escapeHtml(data.title)}" required></div>
          <div class="form-row" style="margin-top:12px"><div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--wp-gray-50)"><span>Permalink:</span><span style="color:var(--wp-blue)">#/post/</span><input type="text" id="postSlug" value="${escapeHtml(data.slug)}" style="border:none;border-bottom:1px dashed #ccc;padding:2px 4px;font-size:13px;width:200px;background:transparent"></div></div>
        </div></div>
        <div class="wp-card"><div class="wp-card-body" style="padding:0"><div class="editor-wrap">
          <div class="editor-toolbar">
            <button type="button" data-cmd="bold" title="Bold"><b>B</b></button>
            <button type="button" data-cmd="italic" title="Italic"><i>I</i></button>
            <button type="button" data-cmd="underline" title="Underline"><u>U</u></button><span class="sep"></span>
            <button type="button" data-cmd="formatBlock" data-val="h2" title="Heading 2">H2</button>
            <button type="button" data-cmd="formatBlock" data-val="h3" title="Heading 3">H3</button>
            <button type="button" data-cmd="formatBlock" data-val="p" title="Paragraph">P</button><span class="sep"></span>
            <button type="button" data-cmd="insertUnorderedList" title="Bullet list">• List</button>
            <button type="button" data-cmd="insertOrderedList" title="Numbered">1.</button><span class="sep"></span>
            <button type="button" data-cmd="createLink" title="Link">🔗</button>
            <button type="button" data-cmd="insertImage" title="Image">🖼️</button>
            <button type="button" data-cmd="formatBlock" data-val="blockquote" title="Quote">❝</button>
          </div>
          <div class="editor-content" id="postContent" contenteditable="true" data-placeholder="Start writing...">${data.content}</div>
        </div></div></div>
        <div class="wp-card"><div class="wp-card-header"><h2 class="wp-card-title">Excerpt</h2></div><div class="wp-card-body"><textarea class="form-textarea" id="postExcerpt" placeholder="Write a short summary...">${escapeHtml(data.excerpt)}</textarea><div class="form-hint">Appears on homepage and search results.</div></div></div>

        <div class="wp-card"><div class="wp-card-header"><h2 class="wp-card-title">📹 Video Links</h2></div><div class="wp-card-body">
          <div class="form-hint" style="margin-bottom:12px">Add YouTube, Vimeo, Dailymotion, Dropbox, or direct video links. They'll be embedded or linked in the post.</div>
          <div class="video-list-editor" id="videoListEditor">
            ${data.videos.map((v,i) => `
              <div class="video-entry" data-vidx="${i}">
                <div class="video-entry-row">
                  <input type="text" class="form-input vid-label" placeholder="Label (optional)" value="${escapeHtml(v.label||'')}">
                  <button type="button" class="btn btn-danger remove-video">✕</button>
                </div>
                <input type="url" class="form-input vid-url" placeholder="https://youtube.com/watch?v=... or Vimeo, Dropbox, etc." value="${escapeHtml(v.url||'')}">
              </div>`).join('')}
          </div>
          <button type="button" class="btn btn-secondary" id="addVideoBtn" style="margin-top:10px">+ Add Video Link</button>
        </div></div>

        <div class="wp-card"><div class="wp-card-header"><h2 class="wp-card-title">🖼️ Gallery</h2></div><div class="wp-card-body">
          <div class="form-hint" style="margin-bottom:10px">Add multiple images to display as a gallery at the bottom of the post.</div>
          ${data.gallery.length ? `<div class="gallery-drag-hint">⠿ Drag images to reorder</div>` : ''}
          <div class="gallery-editor-grid" id="galleryEditorGrid">
            ${data.gallery.map((src,i) => `
              <div class="gallery-editor-item" data-gidx="${i}">
                <img src="${escapeHtml(src)}" alt="">
                <button type="button" class="remove-gallery-img" data-src="${escapeHtml(src)}">✕</button>
              </div>`).join('')}
          </div>
          <div class="gallery-add-row">
            <label style="cursor:pointer;display:flex;align-items:center;gap:6px">
              <span class="btn btn-secondary" style="pointer-events:none">📁 Upload Image</span>
              <input type="file" id="galleryFileInput" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none">
            </label>
            <button type="button" class="btn btn-secondary" id="galleryPickerBtn">🖼️ Media Library</button>
            <span style="font-size:12px;color:var(--wp-gray-50)">— or —</span>
            <input type="url" class="form-input" id="galleryUrlInput" placeholder="Paste image URL" style="flex:1;min-width:180px">
            <button type="button" class="btn btn-secondary" id="galleryAddUrlBtn">Add</button>
          </div>
          <div id="galleryUploadStatus" style="font-size:12px;margin-top:6px;display:none;"></div>
        </div></div>
      </div>
      <div class="form-side">
        <div class="wp-card"><div class="wp-card-header"><h2 class="wp-card-title">Publish</h2></div><div class="wp-card-body">
          <div class="form-row"><label class="form-label">Status</label><select class="form-select" id="postStatus"><option value="draft" ${data.status==='draft'?'selected':''}>Draft</option><option value="published" ${data.status==='published'?'selected':''}>Published</option></select></div>
          <div class="form-row"><label class="form-label">Publish date</label><input type="datetime-local" class="form-input" id="postDate" value="${new Date(data.date).toISOString().slice(0,16)}"></div>
          <div class="form-row" style="flex-direction:row;align-items:center;gap:10px;padding:10px 0 0">
            <label class="toggle-switch" title="Show this post on the homepage">
              <input type="checkbox" id="postShowOnHome" ${data.showOnHome === true ? 'checked' : ''}>
              <span class="toggle-slider"></span>
            </label>
            <label for="postShowOnHome" class="form-label" style="margin:0;cursor:pointer">Show on Homepage</label>
          </div>
          <div id="showOnHomeHint" style="margin-top:6px;font-size:12px;padding:7px 10px;border-radius:4px;border-left:3px solid transparent;transition:all .2s"></div>
          <div style="display:flex;gap:8px;margin-top:16px"><button type="submit" class="btn btn-primary" style="flex:1">${isEdit ? 'Update' : 'Publish'}</button><a href="#/admin/posts" class="btn btn-secondary">Cancel</a></div>
        </div></div>
        <div class="wp-card"><div class="wp-card-header"><h2 class="wp-card-title">Featured Image</h2></div><div class="wp-card-body">
          <div id="imgPreview" style="margin-bottom:10px;display:none;position:relative;">
            <img id="imgPreviewImg" src="" alt="" style="width:100%;border-radius:4px;aspect-ratio:16/9;object-fit:cover;display:block;">
            <button type="button" id="imgRemove" title="Remove image" style="position:absolute;top:6px;right:6px;background:rgba(0,0,0,.55);color:#fff;border:none;border-radius:50%;width:24px;height:24px;cursor:pointer;font-size:14px;line-height:1;display:flex;align-items:center;justify-content:center;">✕</button>
          </div>
          <div id="imgError" style="display:none;margin-bottom:8px;font-size:12px;color:#b32d2e;">⚠ Could not load image — check the URL.</div>
          <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px;">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin:0;">
              <span class="btn btn-secondary" style="pointer-events:none;">📁 Upload image</span>
              <input type="file" id="imgFileInput" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none;">
            </label>
            <button type="button" class="btn btn-secondary" id="featImgPickerBtn">🖼️ Media Library</button>
          </div>
          <div style="font-size:12px;color:var(--wp-gray-50);margin-bottom:6px;">— or paste a URL —</div>
          <input type="url" class="form-input" id="postImage" placeholder="https://example.com/photo.jpg" value="${escapeHtml(data.featuredImage)}">
          <div id="imgUploadStatus" style="font-size:12px;margin-top:6px;display:none;"></div>
        </div></div>
        <div class="wp-card"><div class="wp-card-header"><h2 class="wp-card-title">Categories &amp; Tags</h2><a href="#/admin/categories" class="btn btn-secondary" style="font-size:12px">Manage</a></div><div class="wp-card-body">
          <div class="form-row"><label class="form-label">Category</label>
            <select class="form-select" id="postCategory">
              ${extraOption}${catOptions}
            </select>
          </div>
          <div class="form-row"><label class="form-label">Tags (comma separated)</label><input type="text" class="form-input" id="postTags" value="${escapeHtml((data.tags||[]).join(', '))}" placeholder="web, design"></div>
        </div></div>
      </div>
    </div></form>`;

  const titleEl   = document.getElementById('postTitle');
  const slugEl    = document.getElementById('postSlug');
  const contentEl = document.getElementById('postContent');
  const imgEl     = document.getElementById('postImage');
  const imgPrev   = document.getElementById('imgPreview');
  let slugEdited  = !!data.slug;
  slugEl.addEventListener('input',  () => slugEdited = true);
  titleEl.addEventListener('input', () => { if (!slugEdited) slugEl.value = slugify(titleEl.value); });
  function updateImgPreview(url) {
    const img = document.getElementById('imgPreviewImg');
    const err = document.getElementById('imgError');
    if (!url) { imgPrev.style.display = 'none'; err.style.display = 'none'; imgEl.value = ''; return; }
    img.onload  = () => { imgPrev.style.display = 'block'; err.style.display = 'none'; };
    img.onerror = () => { imgPrev.style.display = 'none'; err.style.display = 'block'; };
    img.src = url;
    imgEl.value = url;
  }
  if (data.featuredImage) updateImgPreview(data.featuredImage);
  imgEl.addEventListener('input',  () => updateImgPreview(imgEl.value.trim()));
  imgEl.addEventListener('change', () => updateImgPreview(imgEl.value.trim()));

  // Remove button
  document.getElementById('imgRemove').addEventListener('click', () => updateImgPreview(''));

  // Media library picker — featured image
  document.getElementById('featImgPickerBtn').addEventListener('click', () => {
    openMediaPicker(url => updateImgPreview(url), imgEl.value.trim());
  });

  // File upload
  document.getElementById('imgFileInput').addEventListener('change', async e => {
    const file = e.target.files[0];
    if (!file) return;
    const status = document.getElementById('imgUploadStatus');
    status.style.display = 'block';
    status.style.color = 'var(--wp-gray-50)';
    status.textContent = '⏳ Uploading…';
    const fd = new FormData();
    fd.append('image', file);
    try {
      const res = await fetch('api.php?action=upload', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.ok) {
        status.style.color = '#1f6b2c';
        status.textContent = '✓ Uploaded successfully';
        updateImgPreview(data.url);
        setTimeout(() => { status.style.display = 'none'; }, 3000);
      } else {
        status.style.color = '#b32d2e';
        status.textContent = '✗ ' + (data.error || 'Upload failed');
      }
    } catch {
      status.style.color = '#b32d2e';
      status.textContent = '✗ Upload failed — server error';
    }
    e.target.value = '';
  });

  // ── Video list editor ──────────────────────────────────────────────────────
  function addVideoEntry(label, url) {
    const editor = document.getElementById('videoListEditor');
    const div = document.createElement('div');
    div.className = 'video-entry';
    div.innerHTML = `
      <div class="video-entry-row">
        <input type="text" class="form-input vid-label" placeholder="Label (optional)" value="${escapeHtml(label||'')}">
        <button type="button" class="btn btn-danger remove-video">✕</button>
      </div>
      <input type="url" class="form-input vid-url" placeholder="https://youtube.com/watch?v=... or Vimeo, Dropbox, etc." value="${escapeHtml(url||'')}">`;
    div.querySelector('.remove-video').addEventListener('click', () => div.remove());
    editor.appendChild(div);
  }
  // Bind remove buttons on existing entries
  container.querySelectorAll('.remove-video').forEach(btn => {
    btn.addEventListener('click', () => btn.closest('.video-entry').remove());
  });
  document.getElementById('addVideoBtn').addEventListener('click', () => addVideoEntry('', ''));

  // ── Gallery editor ─────────────────────────────────────────────────────────
  function addGalleryItem(src) {
    if (!src) return;
    const grid = document.getElementById('galleryEditorGrid');
    const div = document.createElement('div');
    div.className = 'gallery-editor-item';
    div.draggable = true;
    div.innerHTML = `<img src="${escapeHtml(src)}" alt=""><button type="button" class="remove-gallery-img">✕</button>`;
    div.querySelector('.remove-gallery-img').addEventListener('click', () => div.remove());
    grid.appendChild(div);
  }
  // Bind existing remove buttons
  container.querySelectorAll('.remove-gallery-img').forEach(btn => {
    btn.addEventListener('click', () => btn.closest('.gallery-editor-item').remove());
  });
  // Enable drag-and-drop ordering
  makeDraggableGrid(document.getElementById('galleryEditorGrid'));

  document.getElementById('galleryAddUrlBtn').addEventListener('click', () => {
    const inp = document.getElementById('galleryUrlInput');
    const url = inp.value.trim();
    if (url) { addGalleryItem(url); inp.value = ''; }
  });
  document.getElementById('galleryUrlInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('galleryAddUrlBtn').click(); }
  });
  // Media library picker — gallery (multi-select)
  document.getElementById('galleryPickerBtn').addEventListener('click', () => {
    const current = [...container.querySelectorAll('#galleryEditorGrid .gallery-editor-item img')].map(img => img.src).filter(Boolean);
    openMediaPickerMulti(urls => { urls.forEach(addGalleryItem); }, current);
  });
  document.getElementById('galleryFileInput').addEventListener('change', async e => {
    const file = e.target.files[0];
    if (!file) return;
    const status = document.getElementById('galleryUploadStatus');
    status.style.display = 'block'; status.style.color = 'var(--wp-gray-50)';
    status.textContent = '⏳ Uploading…';
    const fd = new FormData(); fd.append('image', file);
    try {
      const res = await fetch('api.php?action=upload', { method: 'POST', body: fd });
      const d = await res.json();
      if (d.ok) {
        addGalleryItem(d.url);
        status.style.color = '#1f6b2c'; status.textContent = '✓ Added to gallery';
        setTimeout(() => { status.style.display = 'none'; }, 2500);
      } else { status.style.color = '#b32d2e'; status.textContent = '✗ ' + (d.error || 'Upload failed'); }
    } catch { status.style.color = '#b32d2e'; status.textContent = '✗ Upload failed'; }
    e.target.value = '';
  });

  container.querySelectorAll('[data-cmd]').forEach(btn => {
    btn.addEventListener('click', () => {
      const cmd = btn.dataset.cmd, val = btn.dataset.val;
      contentEl.focus();
      if (cmd === 'createLink') {
        // Save selection before the modal steals focus
        const sel = window.getSelection();
        const savedRange = (sel && sel.rangeCount) ? sel.getRangeAt(0).cloneRange() : null;
        const selectedText = savedRange ? savedRange.toString() : '';
        // Build link modal
        const bd = document.createElement('div');
        bd.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px';
        bd.innerHTML = `
          <div style="background:#fff;border-radius:8px;padding:24px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.3)">
            <h3 style="margin:0 0 16px;font-size:15px;font-weight:700">Insert Link</h3>
            <div style="margin-bottom:12px">
              <label style="font-size:13px;font-weight:600;display:block;margin-bottom:4px">URL</label>
              <input id="_linkUrl" type="url" placeholder="https://" style="width:100%;box-sizing:border-box;padding:7px 10px;border:1px solid #ddd;border-radius:4px;font-size:14px">
            </div>
            <div style="margin-bottom:16px">
              <label style="font-size:13px;font-weight:600;display:block;margin-bottom:4px">Link text${selectedText ? ' <span style="font-weight:400;color:#888">(pre-filled from selection)</span>' : ''}</label>
              <input id="_linkText" type="text" value="${selectedText.replace(/"/g,'&quot;')}" placeholder="Link label" style="width:100%;box-sizing:border-box;padding:7px 10px;border:1px solid #ddd;border-radius:4px;font-size:14px">
            </div>
            <div style="margin-bottom:18px;display:flex;align-items:center;gap:8px">
              <input type="checkbox" id="_linkBtn" style="width:15px;height:15px;cursor:pointer">
              <label for="_linkBtn" style="font-size:13px;cursor:pointer">Style as button <span style="background:var(--wp-blue);color:#fff;padding:2px 8px;border-radius:4px;font-size:12px;font-weight:600">Link ↗</span></label>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end">
              <button id="_linkCancel" style="padding:7px 16px;border:1px solid #ddd;background:#fff;border-radius:4px;cursor:pointer;font-size:14px">Cancel</button>
              <button id="_linkInsert" style="padding:7px 16px;background:var(--wp-blue);color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px;font-weight:600">Insert</button>
            </div>
          </div>`;
        document.body.appendChild(bd);
        const urlInput = bd.querySelector('#_linkUrl');
        urlInput.focus();
        bd.querySelector('#_linkCancel').addEventListener('click', () => bd.remove());
        bd.addEventListener('click', e => { if (e.target === bd) bd.remove(); });
        bd.querySelector('#_linkInsert').addEventListener('click', () => {
          const url  = bd.querySelector('#_linkUrl').value.trim();
          const text = bd.querySelector('#_linkText').value.trim();
          const asBtn = bd.querySelector('#_linkBtn').checked;
          if (!url) { urlInput.style.borderColor='#d63638'; urlInput.focus(); return; }
          bd.remove();
          // Restore selection
          if (savedRange) {
            const s2 = window.getSelection();
            s2.removeAllRanges();
            s2.addRange(savedRange);
          }
          contentEl.focus();
          const linkLabel = text || url;
          const btnClass  = asBtn ? ' class="inline-link-btn"' : '';
          const linkHtml  = `<a href="${url}" target="_blank" rel="noopener"${btnClass}>${linkLabel}${asBtn ? ' ↗' : ''}</a>`;
          document.execCommand('insertHTML', false, linkHtml);
        });
        bd.querySelector('#_linkUrl').addEventListener('keydown', e => {
          if (e.key === 'Enter') bd.querySelector('#_linkInsert').click();
        });
      } else if (cmd === 'insertImage') {
        const url = prompt('Image URL:', 'https://'); if (url) document.execCommand('insertImage', false, url);
      } else {
        document.execCommand(cmd, false, val && cmd === 'formatBlock' ? `<${val}>` : val);
      }
    });
  });

  // ── Show on Homepage live hint ─────────────────────────────────────────────
  const showOnHomeEl   = document.getElementById('postShowOnHome');
  const showOnHomeHint = document.getElementById('showOnHomeHint');
  function updateShowOnHomeHint() {
    const modeIsSelected = homepageMode === 'selected';
    if (showOnHomeEl.checked) {
      showOnHomeHint.style.background  = '#e7f5e9';
      showOnHomeHint.style.borderColor = '#1f6b2c';
      showOnHomeHint.style.color       = '#1f6b2c';
      showOnHomeHint.innerHTML = modeIsSelected
        ? '✓ <strong>ON</strong> — This post <strong>will appear on the homepage</strong> (Selected posts mode is active).'
        : '✓ <strong>ON</strong> — Noted. Homepage is currently set to <strong>"All posts"</strong>, so all published posts show anyway. Switch to "Selected posts only" in <a href="#/admin/settings">Settings</a> to use this toggle.';
    } else {
      if (modeIsSelected) {
        showOnHomeHint.style.background  = '#fff3cd';
        showOnHomeHint.style.borderColor = '#856404';
        showOnHomeHint.style.color       = '#856404';
        showOnHomeHint.innerHTML = '✕ <strong>OFF</strong> — This post <strong>will NOT appear on the homepage</strong>. It will only be visible inside its category (Selected posts mode is active).';
      } else {
        showOnHomeHint.style.background  = '#f0f0f1';
        showOnHomeHint.style.borderColor = '#8c8f94';
        showOnHomeHint.style.color       = '#50575e';
        showOnHomeHint.innerHTML = '✕ <strong>OFF</strong> — Homepage shows all published posts right now. This toggle only matters when "Selected posts only" mode is set in <a href="#/admin/settings">Settings</a>.';
      }
    }
  }
  updateShowOnHomeHint();
  showOnHomeEl.addEventListener('change', updateShowOnHomeHint);

  document.getElementById('postForm').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = e.target.querySelector('[type=submit]');
    btn.disabled = true; btn.textContent = 'Saving…';
    // Collect videos
    const videoEntries = [...container.querySelectorAll('.video-entry')].map(div => ({
      label: div.querySelector('.vid-label').value.trim(),
      url:   div.querySelector('.vid-url').value.trim()
    })).filter(v => v.url);
    // Collect gallery
    const galleryItems = [...container.querySelectorAll('#galleryEditorGrid .gallery-editor-item img')].map(img => img.src).filter(Boolean);
    const updated = {
      id:            data.id,
      _isEdit:       isEdit,
      title:         titleEl.value.trim(),
      slug:          slugEl.value.trim() || slugify(titleEl.value),
      content:       contentEl.innerHTML,
      excerpt:       document.getElementById('postExcerpt').value.trim() || contentEl.innerText.substring(0, 160),
      featuredImage: imgEl.value.trim(),
      category:      document.getElementById('postCategory').value || 'Uncategorized',
      tags:          document.getElementById('postTags').value.split(',').map(t => t.trim()).filter(Boolean),
      status:        document.getElementById('postStatus').value,
      showOnHome:    document.getElementById('postShowOnHome').checked,
      date:          new Date(document.getElementById('postDate').value).toISOString(),
      author:        data.author || 'admin',
      videos:        videoEntries,
      gallery:       galleryItems,
    };
    await API.savePost(updated);
    location.hash = '#/admin/posts';
  });
}

// ══════════════════════════════════════════════════════════════════════════════
// Categories manager
// ══════════════════════════════════════════════════════════════════════════════

async function renderCategories(container) {
  let cats = await API.getCategories();
  let editingId = null;

  async function draw() {
    cats = await API.getCategories();
    container.innerHTML = `
      <div style="display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start">
        <div class="wp-card">
          <div class="wp-card-header"><h2 class="wp-card-title">All Categories</h2></div>
          <div class="wp-card-body" style="padding:0">
            <div class="table-wrap"><table class="wp-table">
              <thead><tr><th>Name</th><th>Slug</th><th>Posts</th><th></th></tr></thead>
              <tbody id="catTableBody">
                ${cats.length ? cats.map(c => `
                  <tr id="cat-row-${c.id}">
                    <td>
                      <div id="cat-view-${c.id}">
                        <strong style="color:var(--wp-dark)">${escapeHtml(c.name)}</strong>
                        <div class="row-actions" style="visibility:visible">
                          <a href="#" class="cat-edit-btn" data-id="${c.id}" data-name="${escapeHtml(c.name)}">Edit</a>
                          <a href="#" class="cat-delete-btn delete" data-id="${c.id}" data-name="${escapeHtml(c.name)}">Delete</a>
                        </div>
                      </div>
                      <div id="cat-edit-${c.id}" style="display:none">
                        <div style="display:flex;gap:8px;align-items:center">
                          <input type="text" class="form-input cat-edit-input" data-id="${c.id}" value="${escapeHtml(c.name)}" style="flex:1">
                          <button class="btn btn-primary cat-save-btn" data-id="${c.id}" style="padding:6px 10px">Save</button>
                          <button class="btn btn-secondary cat-cancel-btn" data-id="${c.id}" style="padding:6px 10px">✕</button>
                        </div>
                      </div>
                    </td>
                    <td style="color:var(--wp-gray-50);font-size:13px">${escapeHtml(c.slug)}</td>
                    <td id="cat-count-${c.id}" style="color:var(--wp-gray-50);font-size:13px">—</td>
                    <td></td>
                  </tr>`).join('') : `<tr><td colspan="4" style="text-align:center;padding:30px;color:var(--wp-gray-50)">No categories yet</td></tr>`}
              </tbody>
            </table></div>
          </div>
        </div>

        <div class="wp-card">
          <div class="wp-card-header"><h2 class="wp-card-title">Add New Category</h2></div>
          <div class="wp-card-body">
            <div id="catAddMsg" style="display:none;margin-bottom:12px"></div>
            <div class="form-row">
              <label class="form-label">Name</label>
              <input type="text" class="form-input" id="catNewName" placeholder="e.g. Technology">
              <div class="form-hint">The name is how it appears on your site.</div>
            </div>
            <button class="btn btn-primary" id="catAddBtn" style="margin-top:8px">Add Category</button>
          </div>
        </div>
      </div>`;

    // Count posts per category
    API.getPosts().then(posts => {
      cats.forEach(c => {
        const count = posts.filter(p => p.category === c.name).length;
        const el = document.getElementById(`cat-count-${c.id}`);
        if (el) el.textContent = count;
      });
    });

    // Edit inline
    container.querySelectorAll('.cat-edit-btn').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        const id = btn.dataset.id;
        document.getElementById(`cat-view-${id}`).style.display = 'none';
        document.getElementById(`cat-edit-${id}`).style.display = 'block';
        document.querySelector(`.cat-edit-input[data-id="${id}"]`).focus();
      });
    });

    container.querySelectorAll('.cat-cancel-btn').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        const id = btn.dataset.id;
        document.getElementById(`cat-view-${id}`).style.display = 'block';
        document.getElementById(`cat-edit-${id}`).style.display = 'none';
      });
    });

    container.querySelectorAll('.cat-save-btn').forEach(btn => {
      btn.addEventListener('click', async e => {
        e.preventDefault();
        const id   = btn.dataset.id;
        const name = document.querySelector(`.cat-edit-input[data-id="${id}"]`).value.trim();
        if (!name) return;
        btn.disabled = true; btn.textContent = '…';
        const res = await API.updateCategory(id, name);
        if (res.ok) { draw(); }
        else { alert(res.error || 'Failed to update'); btn.disabled = false; btn.textContent = 'Save'; }
      });
    });

    // Delete
    container.querySelectorAll('.cat-delete-btn').forEach(btn => {
      btn.addEventListener('click', async e => {
        e.preventDefault();
        const name = btn.dataset.name;
        if (!confirm(`Delete category "${name}"? Posts using it will keep their category label.`)) return;
        await API.deleteCategory(btn.dataset.id);
        draw();
      });
    });

    // Add new
    document.getElementById('catAddBtn').addEventListener('click', async () => {
      const input = document.getElementById('catNewName');
      const msg   = document.getElementById('catAddMsg');
      const name  = input.value.trim();
      if (!name) { input.focus(); return; }
      const btn = document.getElementById('catAddBtn');
      btn.disabled = true; btn.textContent = 'Adding…';
      const res = await API.createCategory(name);
      btn.disabled = false; btn.textContent = 'Add Category';
      if (res.ok) {
        input.value = '';
        msg.style.display = 'none';
        draw();
      } else {
        msg.style.display = 'block';
        msg.innerHTML = `<div class="wp-notice" style="border-left-color:#d63638;color:#b32d2e">${escapeHtml(res.error || 'Failed to add category')}</div>`;
      }
    });
    document.getElementById('catNewName').addEventListener('keydown', e => {
      if (e.key === 'Enter') { e.preventDefault(); document.getElementById('catAddBtn').click(); }
    });
  }

  draw();
}

// ══════════════════════════════════════════════════════════════════════════════
// Media
// ══════════════════════════════════════════════════════════════════════════════

async function renderMedia(container, posts) {
  // Load persisted media library from server
  let mediaUrls = await API.getMedia();

  // Also collect URLs referenced in posts (featured + gallery) — auto-register any missing
  const postUrls = [];
  posts.forEach(p => {
    if (p.featuredImage) postUrls.push(p.featuredImage);
    if (Array.isArray(p.gallery)) p.gallery.forEach(u => { if (u) postUrls.push(u); });
  });

  // Also collect URLs from aboutGallery and siteGallery in settings
  try {
    const settings = await API.getSettings();
    if (Array.isArray(settings.aboutGallery)) settings.aboutGallery.forEach(u => { if (u) postUrls.push(u); });
    if (Array.isArray(settings.siteGallery))  settings.siteGallery.forEach(u => { if (u) postUrls.push(u); });
  } catch(e) {}

  // Register any URLs not yet in the media library (fire-and-forget)
  const mediaSet = new Set(mediaUrls);
  postUrls.forEach(u => { if (!mediaSet.has(u)) { mediaSet.add(u); API.addMedia(u); } });
  // Merge: media library first (preserves manual adds), then any new URLs
  const allUrls = [...new Set([...mediaUrls, ...postUrls])];

  // Build a usage map: url → array of post titles using it
  function buildUsageMap() {
    const map = {};
    posts.forEach(p => {
      const refs = [];
      if (p.featuredImage) refs.push(p.featuredImage);
      if (Array.isArray(p.gallery)) p.gallery.forEach(u => { if (u) refs.push(u); });
      refs.forEach(u => {
        if (!map[u]) map[u] = [];
        map[u].push(p.title || '(Untitled)');
      });
    });
    return map;
  }

  function draw(urls) {
    const usageMap = buildUsageMap();
    container.innerHTML = `
      <div class="wp-card">
        <div class="wp-card-header">
          <h2 class="wp-card-title">Media Library</h2>
          ${urls.length ? `<span style="font-size:13px;color:var(--wp-gray-50);font-weight:400">${urls.length} image${urls.length !== 1 ? 's' : ''}</span>` : ''}
        </div>
        <div class="wp-card-body">
          ${urls.length
            ? `<div class="media-grid" id="mediaGrid">
                ${urls.map((src) => `
                  <div class="media-item" data-url="${escapeHtml(src)}">
                    <img src="${escapeHtml(src)}" loading="lazy" alt="">
                    <div class="media-item-overlay">
                      <button class="media-copy-btn" data-url="${escapeHtml(src)}" title="Copy image URL">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        <span>Copy Link</span>
                      </button>
                      <button class="media-delete-btn" data-url="${escapeHtml(src)}" title="Delete image">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                        Delete
                      </button>
                    </div>
                    ${(usageMap[src] && usageMap[src].length)
                      ? `<span style="position:absolute;top:6px;left:6px;background:rgba(34,113,177,.85);color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:3px;line-height:1.4;pointer-events:none">IN USE</span>`
                      : ''}
                  </div>`).join('')}
               </div>`
            : `<div class="empty-state"><h3>No media yet</h3><p>Upload images via posts to start your library.</p></div>`}
          <div class="wp-notice" style="margin-top:20px;border-left-color:#dba617">
            Deleting from the library removes the URL entry only — it does <strong>not</strong> delete the physical file from the server. Images removed from the library but still referenced in posts remain accessible via their URL.
          </div>
        </div>
      </div>`;

    // ── Copy buttons ──────────────────────────────────────────────────────────
    container.querySelectorAll('.media-copy-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.stopPropagation();
        const url = btn.dataset.url;
        try { await navigator.clipboard.writeText(url); }
        catch {
          const ta = document.createElement('textarea');
          ta.value = url; ta.style.cssText = 'position:fixed;opacity:0';
          document.body.appendChild(ta); ta.select(); document.execCommand('copy');
          document.body.removeChild(ta);
        }
        const span = btn.querySelector('span');
        btn.classList.add('copied'); span.textContent = '✓ Copied!';
        setTimeout(() => { btn.classList.remove('copied'); span.textContent = 'Copy Link'; }, 2000);
      });
    });

    // ── Delete buttons ────────────────────────────────────────────────────────
    container.querySelectorAll('.media-delete-btn').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.stopPropagation();
        const url  = btn.dataset.url;
        const used = usageMap[url] || [];
        showDeleteConfirm(url, used, async () => {
          await API.deleteMedia(url);
          allUrls.splice(allUrls.indexOf(url), 1);
          draw(allUrls);
        });
      });
    });
  }

  draw(allUrls);
}

// ── Delete confirm dialog ─────────────────────────────────────────────────────
function showDeleteConfirm(url, usedBy, onConfirm) {
  const bd = document.createElement('div');
  bd.className = 'confirm-dialog-backdrop';
  const isActive = usedBy.length > 0;
  const filename = url.split('/').pop().split('?')[0];
  bd.innerHTML = `
    <div class="confirm-dialog">
      <div class="confirm-dialog-head">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="${isActive ? '#d63638' : '#b32d2e'}" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <h4>${isActive ? '⚠️ Image is in use' : 'Delete image?'}</h4>
      </div>
      <div class="confirm-dialog-body">
        ${isActive
          ? `<p>This image is currently used by ${usedBy.length} post${usedBy.length !== 1 ? 's' : ''}:</p>
             <ul class="usage-list">${usedBy.map(t => `<li><strong>${escapeHtml(t)}</strong></li>`).join('')}</ul>
             <p style="margin-top:10px">Removing it from the library <strong>will not</strong> remove it from those posts — the images will remain visible. The URL entry will simply no longer appear in the media library.</p>`
          : `<p>Remove <strong>${escapeHtml(filename)}</strong> from the media library?</p>
             <p style="color:#8c8f94;margin-top:4px">This only removes it from the library list. The physical file on the server is not deleted.</p>`}
      </div>
      <div class="confirm-dialog-foot">
        <button class="btn btn-secondary" id="confirmCancel">Cancel</button>
        <button class="btn btn-danger" id="confirmOk">${isActive ? 'Remove from Library Anyway' : 'Delete'}</button>
      </div>
    </div>`;
  document.body.appendChild(bd);
  bd.querySelector('#confirmCancel').addEventListener('click', () => bd.remove());
  bd.querySelector('#confirmOk').addEventListener('click', () => { bd.remove(); onConfirm(); });
  bd.addEventListener('click', e => { if (e.target === bd) bd.remove(); });
}

// ── Media picker modal (used by post editor) ──────────────────────────────────
async function openMediaPicker(onSelect, currentUrl = '') {
  const mediaUrls = await API.getMedia();
  if (!mediaUrls.length) {
    alert('No images in the media library yet. Upload images via posts first.');
    return;
  }
  const bd = document.createElement('div');
  bd.className = 'media-modal-backdrop';
  let selected = currentUrl;
  bd.innerHTML = `
    <div class="media-modal">
      <div class="media-modal-head">
        <h3>Select from Media Library</h3>
        <button class="media-modal-close" title="Close">✕</button>
      </div>
      <div class="media-modal-body">
        <div class="media-modal-grid" id="mediaPickerGrid">
          ${mediaUrls.map(src => `
            <div class="media-picker-item${src === currentUrl ? ' selected' : ''}" data-src="${escapeHtml(src)}" title="${escapeHtml(src)}">
              <img src="${escapeHtml(src)}" alt="" loading="lazy">
            </div>`).join('')}
        </div>
      </div>
      <div class="media-modal-foot">
        <button class="btn btn-secondary" id="pickerCancel">Cancel</button>
        <button class="btn btn-primary" id="pickerSelect" ${!selected ? 'disabled' : ''}>Select Image</button>
      </div>
    </div>`;
  document.body.appendChild(bd);

  bd.querySelector('.media-modal-close').addEventListener('click', () => bd.remove());
  bd.querySelector('#pickerCancel').addEventListener('click', () => bd.remove());
  bd.addEventListener('click', e => { if (e.target === bd) bd.remove(); });

  const selectBtn = bd.querySelector('#pickerSelect');
  bd.querySelectorAll('.media-picker-item').forEach(item => {
    item.addEventListener('click', () => {
      bd.querySelectorAll('.media-picker-item').forEach(i => i.classList.remove('selected'));
      item.classList.add('selected');
      selected = item.dataset.src;
      selectBtn.disabled = false;
    });
  });
  selectBtn.addEventListener('click', () => {
    if (selected) { bd.remove(); onSelect(selected); }
  });
}

// ── Drag-and-drop ordering for gallery editor grids ───────────────────────────
function makeDraggableGrid(gridEl) {
  let dragged = null;
  gridEl.addEventListener('dragstart', e => {
    const item = e.target.closest('.gallery-editor-item');
    if (!item) return;
    dragged = item;
    item.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
  });
  gridEl.addEventListener('dragend', () => {
    if (dragged) { dragged.classList.remove('dragging'); dragged = null; }
    gridEl.querySelectorAll('.gallery-editor-item').forEach(i => i.classList.remove('drag-over'));
  });
  gridEl.addEventListener('dragover', e => {
    e.preventDefault();
    const item = e.target.closest('.gallery-editor-item');
    if (!item || item === dragged) return;
    gridEl.querySelectorAll('.gallery-editor-item').forEach(i => i.classList.remove('drag-over'));
    item.classList.add('drag-over');
    // Determine insert position
    const rect  = item.getBoundingClientRect();
    const after = e.clientX > rect.left + rect.width / 2;
    if (after) gridEl.insertBefore(dragged, item.nextSibling);
    else       gridEl.insertBefore(dragged, item);
  });
  gridEl.addEventListener('dragleave', e => {
    const item = e.target.closest('.gallery-editor-item');
    if (item) item.classList.remove('drag-over');
  });
  gridEl.addEventListener('drop', e => {
    e.preventDefault();
    gridEl.querySelectorAll('.gallery-editor-item').forEach(i => i.classList.remove('drag-over'));
  });
  // Make all current and future items draggable
  function enableItems() {
    gridEl.querySelectorAll('.gallery-editor-item').forEach(el => { el.draggable = true; });
  }
  enableItems();
  // Observer so newly-added items are also made draggable
  const obs = new MutationObserver(enableItems);
  obs.observe(gridEl, { childList: true });
}

// ── Multi-select media picker (for gallery import from library) ───────────────
async function openMediaPickerMulti(onSelect, alreadyIn = []) {
  const mediaUrls = await API.getMedia();
  if (!mediaUrls.length) {
    alert('No images in the media library yet. Upload or save images first to populate the library.');
    return;
  }
  const bd = document.createElement('div');
  bd.className = 'media-modal-backdrop';
  const alreadySet = new Set(alreadyIn);
  const selected   = new Set();
  bd.innerHTML = `
    <div class="media-modal" style="max-width:760px">
      <div class="media-modal-head">
        <h3>Import from Media Library</h3>
        <button class="media-modal-close" title="Close">✕</button>
      </div>
      <div style="padding:8px 20px 0;font-size:12px;color:var(--wp-gray-50)">Click images to select. Already-added images are highlighted.</div>
      <div class="media-modal-body">
        <div class="media-modal-grid" id="multiPickerGrid">
          ${mediaUrls.map(src => `
            <div class="media-picker-item${alreadySet.has(src) ? ' already-in' : ''}" data-src="${escapeHtml(src)}" title="${escapeHtml(src)}">
              <img src="${escapeHtml(src)}" alt="" loading="lazy">
              ${alreadySet.has(src) ? '<span class="picker-in-badge">✓</span>' : ''}
            </div>`).join('')}
        </div>
      </div>
      <div class="media-modal-foot">
        <span id="multiPickerCount" style="font-size:13px;color:var(--wp-gray-50);margin-right:auto">0 selected</span>
        <button class="btn btn-secondary" id="pickerCancel">Cancel</button>
        <button class="btn btn-primary" id="pickerInsert" disabled>Add Selected</button>
      </div>
    </div>`;
  document.body.appendChild(bd);

  const countEl  = bd.querySelector('#multiPickerCount');
  const insertBtn = bd.querySelector('#pickerInsert');

  bd.querySelector('.media-modal-close').addEventListener('click', () => bd.remove());
  bd.querySelector('#pickerCancel').addEventListener('click', () => bd.remove());
  bd.addEventListener('click', e => { if (e.target === bd) bd.remove(); });

  bd.querySelectorAll('.media-picker-item').forEach(item => {
    item.addEventListener('click', () => {
      if (item.classList.contains('already-in')) return; // skip already-added
      item.classList.toggle('selected');
      const src = item.dataset.src;
      if (item.classList.contains('selected')) selected.add(src);
      else selected.delete(src);
      countEl.textContent = selected.size + ' selected';
      insertBtn.disabled  = selected.size === 0;
    });
  });

  insertBtn.addEventListener('click', () => {
    bd.remove();
    onSelect([...selected]);
  });
}

// ══════════════════════════════════════════════════════════════════════════════
// Settings
// ══════════════════════════════════════════════════════════════════════════════

async function renderSettings(container) {
  const s = await API.getSettings();
  const homeDisplay = s.homepageDisplay || 'all';
  container.innerHTML = `
    <form id="settingsForm" style="max-width:720px">

      <div class="wp-card">
        <div class="wp-card-header"><h2 class="wp-card-title">General Settings</h2></div>
        <div class="wp-card-body">
          <div class="form-row">
            <label class="form-label">Site Title</label>
            <input type="text" class="form-input" id="setTitle" value="${escapeHtml(s.siteTitle)}" required>
            <div class="form-hint">Used in the browser tab, navbar logo (when no logo image is set), and footer.</div>
          </div>
          <div class="form-row">
            <label class="form-label">Site URL</label>
            <input type="url" class="form-input" id="setSiteUrl" value="${escapeHtml(s.siteUrl || '')}" placeholder="https://example.com/mycms">
            <div class="form-hint">The full public URL of this site (no trailing slash). <strong>Required for social share links</strong> to work correctly — used when sharing posts on Facebook, X, and WhatsApp. Example: <code>https://yourdomain.com</code> or <code>https://yourdomain.com/subfolder</code>. If left blank, sharing will fall back to the browser's current address.</div>
          </div>
          <div class="form-row">
            <label class="form-label">Homepage Headline</label>
            <input type="text" class="form-input" id="setHomepageHeadline" value="${escapeHtml(s.homepageHeadline || '')}">
            <div class="form-hint">The large heading shown on the homepage. Defaults to <em>Site Title</em> if left blank.</div>
          </div>
          <div class="form-row">
            <label class="form-label">Tagline</label>
            <input type="text" class="form-input" id="setTagline" value="${escapeHtml(s.tagline)}">
            <div class="form-hint">In a few words, explain what this site is about. Shown below the homepage headline.</div>
          </div>
        </div>
      </div>

      <div class="wp-card">
        <div class="wp-card-header"><h2 class="wp-card-title">Site Branding</h2></div>
        <div class="wp-card-body">
          <div class="form-row">
            <label class="form-label">Site Logo URL</label>
            <div style="display:flex;gap:10px;align-items:center">
              <input type="url" class="form-input" id="setLogoUrl" value="${escapeHtml(s.logoUrl || '')}" placeholder="https://example.com/logo.png" style="flex:1">
              <button type="button" class="btn btn-secondary" id="previewLogoBtn">Preview</button>
            </div>
            <div class="form-hint">Link to your logo image (PNG, SVG, WebP). Replaces the text logo in the navbar. Leave blank to use the site title as text.</div>
            <div id="logoPreviewWrap" style="margin-top:10px;display:${s.logoUrl ? 'block' : 'none'}">
              <img id="logoPreview" src="${escapeHtml(s.logoUrl || '')}" alt="Logo preview" style="max-height:60px;max-width:240px;border:1px solid var(--wp-gray-10);border-radius:6px;padding:6px;background:#fff">
            </div>
          </div>
          <div class="form-row" style="margin-top:16px">
            <label class="form-label">Favicon URL</label>
            <div style="display:flex;gap:10px;align-items:center">
              <input type="url" class="form-input" id="setFaviconUrl" value="${escapeHtml(s.faviconUrl || '')}" placeholder="https://example.com/favicon.ico" style="flex:1">
              <button type="button" class="btn btn-secondary" id="previewFaviconBtn">Preview</button>
            </div>
            <div class="form-hint">Link to your favicon (.ico, .png, or .svg). Shown in browser tabs. Leave blank to use the default browser icon.</div>
            <div id="faviconPreviewWrap" style="margin-top:10px;display:${s.faviconUrl ? 'flex' : 'none'};align-items:center;gap:10px">
              <img id="faviconPreview" src="${escapeHtml(s.faviconUrl || '')}" alt="Favicon preview" style="width:32px;height:32px;object-fit:contain;border:1px solid var(--wp-gray-10);border-radius:4px;padding:3px;background:#fff">
              <span style="font-size:13px;color:var(--wp-gray-50)">Favicon preview (32×32)</span>
            </div>
          </div>
        </div>
      </div>

      <div class="wp-card">
        <div class="wp-card-header"><h2 class="wp-card-title">Homepage Display</h2></div>
        <div class="wp-card-body">
          <div class="form-row"><label class="form-label">Your homepage shows</label>
            <select class="form-select" id="setHomepage">
              <option value="all" ${homeDisplay==='all'?'selected':''}>All published posts</option>
              <option value="selected" ${homeDisplay==='selected'?'selected':''}>Selected posts only (posts with "Show on Homepage" enabled)</option>
            </select>
            <div class="form-hint">When set to "Selected posts only", only posts with the <strong>Show on Homepage</strong> toggle enabled in the post editor will appear on the home page.</div>
          </div>
        </div>
      </div>

      <div class="wp-card">
        <div class="wp-card-header"><h2 class="wp-card-title">Social Media Links</h2></div>
        <div class="wp-card-body">
          <div class="form-hint" style="margin-bottom:16px">These icons appear below the title on every category page. Leave blank to hide an icon.</div>
          <div class="form-row">
            <label class="form-label"><svg width="14" height="14" viewBox="0 0 24 24" fill="#1877f2" style="vertical-align:-2px;margin-right:5px"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>Facebook Page URL</label>
            <input type="url" class="form-input" id="setSocialFacebook" value="${escapeHtml(s.socialFacebook || '')}" placeholder="https://facebook.com/yourpage">
          </div>
          <div class="form-row">
            <label class="form-label"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d6249f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:5px"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>Instagram Profile URL</label>
            <input type="url" class="form-input" id="setSocialInstagram" value="${escapeHtml(s.socialInstagram || '')}" placeholder="https://instagram.com/yourprofile">
          </div>
          <div class="form-row">
            <label class="form-label"><svg width="14" height="14" viewBox="0 0 24 24" fill="#0a66c2" style="vertical-align:-2px;margin-right:5px"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>LinkedIn Profile / Page URL</label>
            <input type="url" class="form-input" id="setSocialLinkedin" value="${escapeHtml(s.socialLinkedin || '')}" placeholder="https://linkedin.com/in/yourprofile">
          </div>
          <div class="form-row">
            <label class="form-label"><svg width="14" height="14" viewBox="0 0 24 24" fill="#1769ff" style="vertical-align:-2px;margin-right:5px"><path d="M8.5 12.5c.828 0 1.5-.672 1.5-1.5S9.328 9.5 8.5 9.5H6v3h2.5zm.25 2H6v3.5h2.75C10.26 18 11 17.26 11 16.25S10.26 14.5 8.75 14.5zM0 6v12h8.75C11.4 18 13 16.7 13 14.8c0-1.3-.7-2.4-1.8-2.9.8-.5 1.3-1.4 1.3-2.4C12.5 7.6 11 6 8.75 6H0zm15.5 1.5h5v1.25h-5V7.5zm6 6.5h-6.1c.1 1.2 1 2 2.1 2 .9 0 1.6-.5 1.9-1.2H21c-.4 1.8-2 3-3.6 3-2.2 0-4-1.8-4-4s1.8-4 4-4 3.9 1.8 3.9 4c0 .1 0 .1-.1.2h-.2zm-3.9-4c-1 0-1.9.8-2 1.8h4c-.1-1-1-1.8-2-1.8z"/></svg>Behance Profile URL</label>
            <input type="url" class="form-input" id="setSocialBehance" value="${escapeHtml(s.socialBehance || '')}" placeholder="https://behance.net/yourprofile">
          </div>
          <div class="form-row">
            <label class="form-label"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ea4335" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:5px"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>Contact Email (Google Mail)</label>
            <input type="email" class="form-input" id="setSocialMail" value="${escapeHtml(s.socialMail || '')}" placeholder="you@gmail.com">
            <div class="form-hint">Shown as a mailto: link. Visitors tap it to open their mail client.</div>
          </div>
        </div>
      </div>

      <div class="wp-card">
        <div class="wp-card-header"><h2 class="wp-card-title">Admin Account</h2></div>
        <div class="wp-card-body">
          <div class="form-row"><label class="form-label">Username</label><input type="text" class="form-input" id="setUser" value="${escapeHtml(s.username)}" required></div>
          <div class="form-row"><label class="form-label">New Password</label><input type="password" class="form-input" id="setPass" placeholder="Leave blank to keep current"><div class="form-hint">Leave blank to keep the current password.</div></div>
        </div>
      </div>

      <div class="wp-card">
        <div class="wp-card-header"><h2 class="wp-card-title">Contact Form</h2></div>
        <div class="wp-card-body">
          <div class="form-row" style="display:flex;align-items:center;gap:14px">
            <label class="toggle-switch"><input type="checkbox" id="setContactEnabled" ${s.contactEnabled !== false ? 'checked' : ''}><span class="toggle-slider"></span></label>
            <div>
              <div style="font-size:14px;font-weight:600;color:var(--wp-dark)">Enable Contact Page</div>
              <div class="form-hint" style="margin:0">When disabled, the Contact link is hidden from public navigation.</div>
            </div>
          </div>
          <div class="form-row" style="margin-top:18px">
            <label class="form-label">Alert Email Address</label>
            <input type="email" class="form-input" id="setContactAlertEmail" value="${escapeHtml(s.contactAlertEmail || '')}" placeholder="you@example.com">
            <div class="form-hint">When someone submits the contact form, a copy of the message is emailed here. Uses PHP <code>mail()</code> — ensure your host supports it. Leave blank to disable email alerts.</div>
          </div>
          <div class="form-row">
            <label class="form-label">Privacy Policy URL</label>
            <input type="url" class="form-input" id="setContactPrivacyUrl" value="${escapeHtml(s.contactPrivacyUrl || '')}" placeholder="https://example.com/privacy">
            <div class="form-hint">Linked in the consent checkbox on the contact form. Leave blank to show a plain text consent.</div>
          </div>
          <div class="form-row" style="display:flex;align-items:center;gap:14px;margin-top:4px">
            <label class="toggle-switch"><input type="checkbox" id="setContactAutoReply" ${s.contactAutoReply !== false ? 'checked' : ''}><span class="toggle-slider"></span></label>
            <div>
              <div style="font-size:14px;font-weight:600;color:var(--wp-dark)">Send Auto-Reply Email</div>
              <div class="form-hint" style="margin:0">Automatically send a confirmation email to the person who submitted the form.</div>
            </div>
          </div>
          <div class="form-row" style="margin-top:16px" id="autoReplyMsgRow">
            <label class="form-label">Auto-Reply Message</label>
            <textarea class="form-input" id="setContactAutoReplyMsg" rows="3" style="resize:vertical">${escapeHtml(s.contactAutoReplyMsg || "Thank you for reaching out! We've received your message and will get back to you within 1-2 business days.")}</textarea>
          </div>
          <div class="form-row">
            <label class="form-label">Inquiry Subjects (one per line)</label>
            <textarea class="form-input" id="setContactSubjects" rows="6" style="resize:vertical">${(s.contactSubjects || ['General Inquiry','Support','Partnership','Feedback','Other']).join('\n')}</textarea>
            <div class="form-hint">These appear as options in the Subject dropdown on the contact form.</div>
          </div>
          <div style="margin-top:8px"><a href="#/admin/contacts" class="btn btn-secondary">📬 View Contact Inbox</a></div>
        </div>
      </div>

      <div style="display:flex;gap:10px">
        <button type="submit" class="btn btn-primary">Save Settings</button>
        <button type="button" class="btn btn-danger" id="resetData">Reset Demo Data</button>
      </div>
    </form>`;

  // Live logo preview
  document.getElementById('previewLogoBtn').addEventListener('click', () => {
    const url = document.getElementById('setLogoUrl').value.trim();
    const wrap = document.getElementById('logoPreviewWrap');
    const img  = document.getElementById('logoPreview');
    if (url) { img.src = url; wrap.style.display = 'block'; }
    else wrap.style.display = 'none';
  });

  // Live favicon preview
  document.getElementById('previewFaviconBtn').addEventListener('click', () => {
    const url  = document.getElementById('setFaviconUrl').value.trim();
    const wrap = document.getElementById('faviconPreviewWrap');
    const img  = document.getElementById('faviconPreview');
    if (url) { img.src = url; wrap.style.display = 'flex'; }
    else wrap.style.display = 'none';
  });

  document.getElementById('settingsForm').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = e.target.querySelector('[type=submit]');
    btn.disabled = true; btn.textContent = 'Saving…';
    const payload = {
      siteTitle:        document.getElementById('setTitle').value.trim(),
      siteUrl:          document.getElementById('setSiteUrl').value.trim(),
      homepageHeadline: document.getElementById('setHomepageHeadline').value.trim(),
      tagline:          document.getElementById('setTagline').value.trim(),
      logoUrl:          document.getElementById('setLogoUrl').value.trim(),
      faviconUrl:       document.getElementById('setFaviconUrl').value.trim(),
      username:         document.getElementById('setUser').value.trim(),
      homepageDisplay:  document.getElementById('setHomepage').value,
      socialFacebook:   document.getElementById('setSocialFacebook').value.trim(),
      socialInstagram:  document.getElementById('setSocialInstagram').value.trim(),
      socialLinkedin:   document.getElementById('setSocialLinkedin').value.trim(),
      socialBehance:    document.getElementById('setSocialBehance').value.trim(),
      socialMail:       document.getElementById('setSocialMail').value.trim(),
      contactEnabled:   document.getElementById('setContactEnabled').checked,
      contactAlertEmail: document.getElementById('setContactAlertEmail').value.trim(),
      contactPrivacyUrl: document.getElementById('setContactPrivacyUrl').value.trim(),
      contactAutoReply:  document.getElementById('setContactAutoReply').checked,
      contactAutoReplyMsg: document.getElementById('setContactAutoReplyMsg').value.trim(),
      contactSubjects:   document.getElementById('setContactSubjects').value.split('\n').map(s=>s.trim()).filter(Boolean),
    };
    const np = document.getElementById('setPass').value;
    if (np) payload.newPassword = np;
    await API.saveSettings(payload);
    alert('Settings saved!');
    btn.disabled = false; btn.textContent = 'Save Settings';
    location.hash = '#/admin/settings';
  });

  document.getElementById('resetData').addEventListener('click', async () => {
    if (!confirm('Reset all posts and settings to demo defaults? This cannot be undone.')) return;
    await API.reset();
    location.hash = '#/login';
    location.reload();
  });

  // Toggle auto-reply message row visibility
  const autoReplyToggle = document.getElementById('setContactAutoReply');
  const autoReplyRow    = document.getElementById('autoReplyMsgRow');
  function updateAutoReplyRow() { autoReplyRow.style.display = autoReplyToggle.checked ? 'block' : 'none'; }
  autoReplyToggle.addEventListener('change', updateAutoReplyRow);
  updateAutoReplyRow();
}

// ══════════════════════════════════════════════════════════════════════════════
// About Page Editor
// ══════════════════════════════════════════════════════════════════════════════

async function renderAboutEditor(container) {
  const s = await API.getSettings();
  const aboutContent = s.aboutContent || '';
  container.innerHTML = `
    <div style="max-width:800px">
      <div class="wp-card">
        <div class="wp-card-header">
          <h2 class="wp-card-title">Edit About Page Content</h2>
          <a href="#/about" target="_blank" class="btn btn-secondary">Preview</a>
        </div>
        <div class="wp-card-body">
          <div class="form-hint" style="margin-bottom:12px">This content appears on the public <strong>About</strong> page. You can use the toolbar to format text, add links, and insert images.</div>
          <div class="editor-wrap">
            <div class="editor-toolbar">
              <button type="button" data-cmd="bold" title="Bold"><b>B</b></button>
              <button type="button" data-cmd="italic" title="Italic"><i>I</i></button>
              <button type="button" data-cmd="underline" title="Underline"><u>U</u></button><span class="sep"></span>
              <button type="button" data-cmd="formatBlock" data-val="h2" title="Heading 2">H2</button>
              <button type="button" data-cmd="formatBlock" data-val="h3" title="Heading 3">H3</button>
              <button type="button" data-cmd="formatBlock" data-val="p" title="Paragraph">P</button><span class="sep"></span>
              <button type="button" data-cmd="insertUnorderedList" title="Bullet list">• List</button>
              <button type="button" data-cmd="insertOrderedList" title="Numbered">1.</button><span class="sep"></span>
              <button type="button" data-cmd="createLink" title="Link">🔗</button>
              <button type="button" data-cmd="insertImage" title="Image">🖼️</button>
              <button type="button" data-cmd="formatBlock" data-val="blockquote" title="Quote">❝</button>
            </div>
            <div class="editor-content" id="aboutContent" contenteditable="true" data-placeholder="Write your About page content here…" style="min-height:320px">${aboutContent}</div>
          </div>
        </div>
      </div>

      <div class="wp-card">
        <div class="wp-card-header"><h2 class="wp-card-title">About Page Gallery</h2></div>
        <div class="wp-card-body">
          <div class="form-hint" style="margin-bottom:10px">Optional: Add images to display as a gallery on the About page.</div>
          ${(s.aboutGallery||[]).length ? `<div class="gallery-drag-hint">⠿ Drag images to reorder</div>` : ''}
          <div class="gallery-editor-grid" id="aboutGalleryGrid">
            ${(s.aboutGallery||[]).map((src,i) => `
              <div class="gallery-editor-item">
                <img src="${escapeHtml(src)}" alt="">
                <button type="button" class="remove-gallery-img">✕</button>
              </div>`).join('')}
          </div>
          <div class="gallery-add-row" style="margin-top:10px">
            <button type="button" class="btn btn-secondary" id="aboutImportMediaBtn">🖼️ Import from Library</button>
            <label style="cursor:pointer;display:flex;align-items:center;gap:6px">
              <span class="btn btn-secondary" style="pointer-events:none">📁 Upload Image</span>
              <input type="file" id="aboutGalleryFile" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none">
            </label>
            <span style="font-size:12px;color:var(--wp-gray-50)">— or —</span>
            <input type="url" class="form-input" id="aboutGalleryUrl" placeholder="Paste image URL" style="flex:1;min-width:180px">
            <button type="button" class="btn btn-secondary" id="aboutGalleryAddBtn">Add</button>
          </div>
          <div id="aboutGalleryStatus" style="font-size:12px;margin-top:6px;display:none;"></div>
        </div>
      </div>

      <div style="display:flex;gap:10px;align-items:center">
        <button class="btn btn-primary" id="saveAboutBtn">Save About Page</button>
        <span id="aboutSaveMsg" style="font-size:13px;display:none;color:#1f6b2c">✓ Saved!</span>
      </div>
    </div>`;

  const aboutEl = document.getElementById('aboutContent');

  container.querySelectorAll('[data-cmd]').forEach(btn => {
    btn.addEventListener('click', () => {
      const cmd = btn.dataset.cmd, val = btn.dataset.val;
      aboutEl.focus();
      if (cmd === 'createLink') {
        const url = prompt('Enter URL:', 'https://'); if (url) document.execCommand('createLink', false, url);
      } else if (cmd === 'insertImage') {
        const url = prompt('Image URL:', 'https://'); if (url) document.execCommand('insertImage', false, url);
      } else {
        document.execCommand(cmd, false, val && cmd === 'formatBlock' ? `<${val}>` : val);
      }
    });
  });

  // About gallery helpers
  function addAboutGalleryItem(src) {
    const grid = document.getElementById('aboutGalleryGrid');
    const div = document.createElement('div');
    div.className = 'gallery-editor-item';
    div.draggable = true;
    div.innerHTML = `<img src="${escapeHtml(src)}" alt=""><button type="button" class="remove-gallery-img">✕</button>`;
    div.querySelector('.remove-gallery-img').addEventListener('click', () => div.remove());
    grid.appendChild(div);
  }
  container.querySelectorAll('#aboutGalleryGrid .remove-gallery-img').forEach(btn => {
    btn.addEventListener('click', () => btn.closest('.gallery-editor-item').remove());
  });
  // Enable drag-and-drop ordering
  makeDraggableGrid(document.getElementById('aboutGalleryGrid'));

  // Import from Media Library
  document.getElementById('aboutImportMediaBtn').addEventListener('click', () => {
    const current = [...container.querySelectorAll('#aboutGalleryGrid .gallery-editor-item img')].map(img => img.src).filter(Boolean);
    openMediaPickerMulti(urls => { urls.forEach(addAboutGalleryItem); }, current);
  });

  document.getElementById('aboutGalleryAddBtn').addEventListener('click', () => {
    const inp = document.getElementById('aboutGalleryUrl');
    const url = inp.value.trim();
    if (url) { addAboutGalleryItem(url); inp.value = ''; }
  });
  document.getElementById('aboutGalleryUrl').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('aboutGalleryAddBtn').click(); }
  });
  document.getElementById('aboutGalleryFile').addEventListener('change', async e => {
    const file = e.target.files[0]; if (!file) return;
    const st = document.getElementById('aboutGalleryStatus');
    st.style.display = 'block'; st.style.color = 'var(--wp-gray-50)'; st.textContent = '⏳ Uploading…';
    const fd = new FormData(); fd.append('image', file);
    try {
      const res = await fetch('api.php?action=upload', { method: 'POST', body: fd });
      const d = await res.json();
      if (d.ok) { addAboutGalleryItem(d.url); st.style.color = '#1f6b2c'; st.textContent = '✓ Added'; setTimeout(() => st.style.display = 'none', 2500); }
      else { st.style.color = '#b32d2e'; st.textContent = '✗ ' + (d.error || 'Failed'); }
    } catch { st.style.color = '#b32d2e'; st.textContent = '✗ Upload failed'; }
    e.target.value = '';
  });

  document.getElementById('saveAboutBtn').addEventListener('click', async () => {
    const btn = document.getElementById('saveAboutBtn');
    btn.disabled = true; btn.textContent = 'Saving…';
    const galleryItems = [...container.querySelectorAll('#aboutGalleryGrid .gallery-editor-item img')].map(img => img.src).filter(Boolean);
    await API.saveSettings({ aboutContent: aboutEl.innerHTML, aboutGallery: galleryItems });
    btn.disabled = false; btn.textContent = 'Save About Page';
    const msg = document.getElementById('aboutSaveMsg');
    msg.style.display = 'inline'; setTimeout(() => msg.style.display = 'none', 2500);
  });
}

// ══════════════════════════════════════════════════════════════════════════════
// Gallery Page Editor (Admin)
// ══════════════════════════════════════════════════════════════════════════════

async function renderGalleryEditor(container) {
  const s = await API.getSettings();
  const siteGallery = Array.isArray(s.siteGallery) ? s.siteGallery.filter(Boolean) : [];
  container.innerHTML = `
    <div style="max-width:800px">
      <div class="wp-card">
        <div class="wp-card-header">
          <h2 class="wp-card-title">Gallery Page Images</h2>
          <a href="#/gallery" target="_blank" class="btn btn-secondary">Preview</a>
        </div>
        <div class="wp-card-body">
          <div class="form-hint" style="margin-bottom:12px">Images added here appear on the public <strong>Gallery</strong> page with a dump animation and lightbox viewer. A "Gallery" link will appear in the navigation automatically once images are added.</div>
          ${siteGallery.length ? `<div class="gallery-drag-hint">⠿ Drag images to reorder</div>` : ''}
          <div class="gallery-editor-grid" id="siteGalleryGrid">
            ${siteGallery.map((src, i) => `
              <div class="gallery-editor-item">
                <img src="${escapeHtml(src)}" alt="">
                <button type="button" class="remove-gallery-img">✕</button>
              </div>`).join('')}
          </div>
          <div class="gallery-add-row" style="margin-top:12px">
            <button type="button" class="btn btn-secondary" id="siteImportMediaBtn">🖼️ Import from Library</button>
            <label style="cursor:pointer;display:flex;align-items:center;gap:6px">
              <span class="btn btn-secondary" style="pointer-events:none">📁 Upload Image</span>
              <input type="file" id="siteGalleryFile" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none">
            </label>
            <span style="font-size:12px;color:var(--wp-gray-50)">— or —</span>
            <input type="url" class="form-input" id="siteGalleryUrl" placeholder="Paste image URL" style="flex:1;min-width:180px">
            <button type="button" class="btn btn-secondary" id="siteGalleryAddBtn">Add</button>
          </div>
          <div id="siteGalleryStatus" style="font-size:12px;margin-top:6px;display:none;"></div>
        </div>
      </div>

      <div style="display:flex;gap:10px;align-items:center">
        <button class="btn btn-primary" id="saveGalleryBtn">Save Gallery</button>
        <span id="gallerySaveMsg" style="font-size:13px;display:none;color:#1f6b2c">✓ Saved!</span>
      </div>
    </div>`;

  function addSiteGalleryItem(src) {
    const grid = document.getElementById('siteGalleryGrid');
    const div = document.createElement('div');
    div.className = 'gallery-editor-item';
    div.draggable = true;
    div.innerHTML = `<img src="${escapeHtml(src)}" alt=""><button type="button" class="remove-gallery-img">✕</button>`;
    div.querySelector('.remove-gallery-img').addEventListener('click', () => div.remove());
    grid.appendChild(div);
  }

  container.querySelectorAll('#siteGalleryGrid .remove-gallery-img').forEach(btn => {
    btn.addEventListener('click', () => btn.closest('.gallery-editor-item').remove());
  });

  // Enable drag-and-drop ordering
  makeDraggableGrid(document.getElementById('siteGalleryGrid'));

  // Import from Media Library
  document.getElementById('siteImportMediaBtn').addEventListener('click', () => {
    const current = [...container.querySelectorAll('#siteGalleryGrid .gallery-editor-item img')].map(img => img.src).filter(Boolean);
    openMediaPickerMulti(urls => { urls.forEach(addSiteGalleryItem); }, current);
  });

  document.getElementById('siteGalleryAddBtn').addEventListener('click', () => {
    const inp = document.getElementById('siteGalleryUrl');
    const url = inp.value.trim();
    if (url) { addSiteGalleryItem(url); inp.value = ''; }
  });
  document.getElementById('siteGalleryUrl').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); document.getElementById('siteGalleryAddBtn').click(); }
  });
  document.getElementById('siteGalleryFile').addEventListener('change', async e => {
    const file = e.target.files[0]; if (!file) return;
    const st = document.getElementById('siteGalleryStatus');
    st.style.display = 'block'; st.style.color = 'var(--wp-gray-50)'; st.textContent = '⏳ Uploading…';
    const fd = new FormData(); fd.append('image', file);
    try {
      const res = await fetch('api.php?action=upload', { method: 'POST', body: fd });
      const d = await res.json();
      if (d.ok) { addSiteGalleryItem(d.url); st.style.color = '#1f6b2c'; st.textContent = '✓ Added'; setTimeout(() => st.style.display = 'none', 2500); }
      else { st.style.color = '#b32d2e'; st.textContent = '✗ ' + (d.error || 'Failed'); }
    } catch { st.style.color = '#b32d2e'; st.textContent = '✗ Upload failed'; }
    e.target.value = '';
  });

  document.getElementById('saveGalleryBtn').addEventListener('click', async () => {
    const btn = document.getElementById('saveGalleryBtn');
    btn.disabled = true; btn.textContent = 'Saving…';
    const imgs = [...container.querySelectorAll('#siteGalleryGrid .gallery-editor-item img')].map(img => img.src).filter(Boolean);
    await API.saveSettings({ siteGallery: imgs });
    btn.disabled = false; btn.textContent = 'Save Gallery';
    const msg = document.getElementById('gallerySaveMsg');
    msg.style.display = 'inline'; setTimeout(() => msg.style.display = 'none', 2500);
  });
}

// ══════════════════════════════════════════════════════════════════════════════
// Public Gallery Page
// ══════════════════════════════════════════════════════════════════════════════

function renderSiteGalleryPage(container, settings) {
  const imgs = Array.isArray(settings.siteGallery) ? settings.siteGallery.filter(Boolean) : [];

  if (!imgs.length) {
    container.innerHTML = `<div class="empty-state"><h3>No gallery images yet</h3><p>Visit the <a href="#/admin/gallery">admin Gallery Page</a> to add images.</p></div>`;
    return;
  }

  container.innerHTML = `
    <div class="page-head">
      <h1 class="page-title">Gallery</h1>
      <p class="page-desc">${imgs.length} image${imgs.length !== 1 ? 's' : ''}</p>
      ${buildCatSocialBar(settings)}
    </div>
    ${buildGalleryGridHtml(imgs, 'siteGalleryDisplay').replace('>', ' style="margin-bottom:60px">')}`;

  // Lightbox follows the saved display order
  const displayImgs = imgs.slice();

  let lb = null, lbImg = null, lbCounter = null, lbPrev = null, lbNext = null, curIdx = 0;
  let navigating = false;

  function buildSiteLb() {
    lb = document.createElement('div');
    lb.className = 'gallery-lightbox';
    lb.setAttribute('tabindex', '-1');
    lb.innerHTML = `
      <button class="gallery-lightbox-close" aria-label="Close">✕</button>
      <button class="gallery-lightbox-nav gallery-lightbox-prev" aria-label="Previous image">&#8249;</button>
      <div class="gallery-lightbox-stage">
        <img class="gallery-lightbox-img" src="" alt="Gallery image">
      </div>
      <button class="gallery-lightbox-nav gallery-lightbox-next" aria-label="Next image">&#8250;</button>
      <div class="gallery-lightbox-counter"></div>`;
    lbImg     = lb.querySelector('.gallery-lightbox-img');
    lbCounter = lb.querySelector('.gallery-lightbox-counter');
    lbPrev    = lb.querySelector('.gallery-lightbox-prev');
    lbNext    = lb.querySelector('.gallery-lightbox-next');
    const lbStage = lb.querySelector('.gallery-lightbox-stage');
    if (displayImgs.length <= 1) { lbPrev.hidden = true; lbNext.hidden = true; }
    lb.addEventListener('click', e => { if (e.target === lb || e.target === lbStage) closeSiteLb(); });
    lb.querySelector('.gallery-lightbox-close').addEventListener('click', closeSiteLb);
    lbPrev.addEventListener('click', () => navSite(-1));
    lbNext.addEventListener('click', () => navSite(1));
    lb.addEventListener('keydown', e => {
      if (e.key === 'ArrowRight')     { e.preventDefault(); navSite(1); }
      else if (e.key === 'ArrowLeft') { e.preventDefault(); navSite(-1); }
      else if (e.key === 'Escape')    { e.preventDefault(); closeSiteLb(); }
    });
    // Mobile swipe — shared Facebook-style track helper
    attachLbMobileSwipe(lbStage, {
      imgs:       displayImgs,
      getCurIdx:  () => curIdx,
      setCurIdx:  (n) => { curIdx = n; },
      getLbImg:   () => lbImg,
      setCounter: (t) => { if (lbCounter) lbCounter.textContent = t; }
    });
    document.body.appendChild(lb);
    lb.focus();
  }

  function setSiteImg(idx, enterClass) {
    curIdx = ((idx % displayImgs.length) + displayImgs.length) % displayImgs.length;
    lbImg.style.transition = '';
    lbImg.style.transform  = '';
    lbImg.style.opacity    = '';
    lbImg.classList.remove('lb-slide-next', 'lb-slide-prev', 'lb-exit-left', 'lb-exit-right');
    void lbImg.offsetWidth;
    lbImg.src = displayImgs[curIdx];
    lbImg.alt = 'Gallery image ' + (curIdx + 1);
    lbCounter.textContent = (curIdx + 1) + ' / ' + displayImgs.length;
    if (enterClass) lbImg.classList.add(enterClass);
  }

  function navSite(dir) {
    if (navigating) return; navigating = true;

    const ANIM_MS  = 400;
    const exitClass  = dir > 0 ? 'lb-exit-left'  : 'lb-exit-right';
    const enterClass = dir > 0 ? 'lb-slide-next'  : 'lb-slide-prev';
    const nextIdx    = (curIdx + dir + displayImgs.length) % displayImgs.length;
    const stage      = lb.querySelector('.gallery-lightbox-stage');

    // 1. Clear any leftover inline styles from a touch swipe, then kick off exit
    lbImg.style.transition = '';
    lbImg.style.transform  = '';
    lbImg.style.opacity    = '';
    lbImg.style.animation  = '';
    lbImg.classList.remove('lb-slide-next', 'lb-slide-prev', 'lb-exit-left', 'lb-exit-right');
    void lbImg.offsetWidth;
    lbImg.classList.add(exitClass);
    const outgoing = lbImg;

    // 2. Create incoming image and start entrance simultaneously
    const incoming = document.createElement('img');
    incoming.className = 'gallery-lightbox-img';
    incoming.src = displayImgs[nextIdx];
    incoming.alt = 'Gallery image ' + (nextIdx + 1);
    incoming.setAttribute('draggable', 'false');
    stage.appendChild(incoming);
    void incoming.offsetWidth;
    incoming.classList.add(enterClass);

    // 3. Update state & counter immediately
    curIdx = nextIdx;
    lbCounter.textContent = (curIdx + 1) + ' / ' + displayImgs.length;
    lbImg = incoming;

    // 4. Remove outgoing after animation completes
    setTimeout(() => {
      outgoing.remove();
      navigating = false;
    }, ANIM_MS);
  }

  function closeSiteLb() {
    navigating = false;
    if (lb) {
      // Clean up any in-progress swipe track
      const existingTrack = lb.querySelector('.lb-swipe-track');
      if (existingTrack) existingTrack.remove();
      lb.remove(); lb = null; lbImg = null; lbCounter = null; lbPrev = null; lbNext = null;
    }
  }

  container.querySelectorAll('#siteGalleryDisplay .gallery-img').forEach(img => {
    img.addEventListener('click', () => {
      closeSiteLb();
      buildSiteLb();
      setSiteImg(+img.dataset.idx, null);
    });
  });
}

// ══════════════════════════════════════════════════════════════════════════════
// Public Contact Page
// ══════════════════════════════════════════════════════════════════════════════

async function renderContactPage(container, settings) {
  if (settings.contactEnabled === false) {
    container.innerHTML = `<div class="empty-state"><h3>Page not found</h3><p><a href="#/" class="btn btn-primary">Go home</a></p></div>`;
    return;
  }

  const subjects = (settings.contactSubjects && settings.contactSubjects.length)
    ? settings.contactSubjects
    : ['General Inquiry','Support','Partnership','Feedback','Other'];

  const privacyLink = settings.contactPrivacyUrl
    ? `<a href="${escapeHtml(settings.contactPrivacyUrl)}" target="_blank" rel="noopener">Privacy Policy</a>`
    : 'Privacy Policy';

  container.innerHTML = `
  <div class="contact-wrap">
    <h1>Get in Touch</h1>
    <p class="contact-subtitle">Have a question or just want to say hello? Fill out the form below and we'll get back to you as soon as possible.</p>
    <div class="contact-card" id="contactFormCard">
      <div class="contact-alert success" id="contactSuccess">
        ✓ Your message was sent successfully. We'll be in touch soon!
      </div>
      <div class="contact-alert error" id="contactError"></div>

      <!-- Honeypot (hidden from real users, bots fill it) -->
      <div class="contact-honeypot" aria-hidden="true">
        <input type="text" name="_gotcha" id="contactHoneypot" tabindex="-1" autocomplete="off">
      </div>

      <div class="contact-row-2">
        <div>
          <div class="contact-label">Full Name <span class="req">*</span></div>
          <input type="text" class="contact-input" id="cfName" placeholder="Your full name" autocomplete="name" maxlength="120">
        </div>
        <div>
          <div class="contact-label">Email Address <span class="req">*</span></div>
          <input type="email" class="contact-input" id="cfEmail" placeholder="you@example.com" autocomplete="email" maxlength="254">
        </div>
      </div>

      <div class="contact-row-2">
        <div>
          <div class="contact-label">Subject <span class="opt">(optional)</span></div>
          <select class="contact-select" id="cfSubject">
            <option value="">— Select a subject —</option>
            ${subjects.map(s => `<option value="${escapeHtml(s)}">${escapeHtml(s)}</option>`).join('')}
          </select>
        </div>
        <div>
          <div class="contact-label">Phone Number <span class="opt">(optional)</span></div>
          <input type="tel" class="contact-input" id="cfPhone" placeholder="+63 9XX XXX XXXX" autocomplete="tel" maxlength="30">
        </div>
      </div>

      <div class="contact-row">
        <div class="contact-label">Order / Account ID <span class="opt">(optional)</span></div>
        <input type="text" class="contact-input" id="cfOrderId" placeholder="e.g. #12345 — leave blank if not applicable" maxlength="80">
      </div>

      <div class="contact-row">
        <div class="contact-label">Message <span class="req">*</span></div>
        <textarea class="contact-textarea" id="cfMessage" placeholder="Tell us what you need…" maxlength="2000"></textarea>
        <div class="char-counter" id="cfCharCounter">0 / 2000</div>
      </div>

      <div class="contact-row">
        <div class="contact-consent">
          <input type="checkbox" id="cfConsent">
          <label for="cfConsent">I agree to the ${privacyLink} and consent to having my submitted information collected and used to respond to my inquiry. <span style="color:#b32d2e">*</span></label>
        </div>
      </div>

      <button class="contact-submit" id="cfSubmit">Send Message</button>
    </div>
  </div>`;

  const nameEl    = document.getElementById('cfName');
  const emailEl   = document.getElementById('cfEmail');
  const msgEl     = document.getElementById('cfMessage');
  const consentEl = document.getElementById('cfConsent');
  const counterEl = document.getElementById('cfCharCounter');
  const submitBtn = document.getElementById('cfSubmit');
  const errBox    = document.getElementById('contactError');
  const okBox     = document.getElementById('contactSuccess');

  // Char counter
  msgEl.addEventListener('input', () => {
    const len = msgEl.value.length;
    counterEl.textContent = len + ' / 2000';
    counterEl.classList.toggle('warn', len > 1800);
  });

  // Validation helpers
  function setErr(el, msg) {
    el.classList.add('contact-field-err');
    return msg;
  }
  function clearErr(el) { el.classList.remove('contact-field-err'); }

  submitBtn.addEventListener('click', async () => {
    // Clear previous errors
    [nameEl, emailEl, msgEl, consentEl].forEach(el => clearErr(el));
    errBox.style.display = 'none';
    okBox.style.display  = 'none';

    const name    = nameEl.value.trim();
    const email   = emailEl.value.trim();
    const subject = document.getElementById('cfSubject').value;
    const phone   = document.getElementById('cfPhone').value.trim();
    const orderid = document.getElementById('cfOrderId').value.trim();
    const message = msgEl.value.trim();
    const consent = consentEl.checked;
    const honeypot = document.getElementById('contactHoneypot').value;

    const errors = [];
    if (!name)    errors.push(setErr(nameEl,    'Name is required.'));
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push(setErr(emailEl, 'Valid email is required.'));
    if (!message) errors.push(setErr(msgEl,     'Message is required.'));
    if (!consent) errors.push(setErr(consentEl, 'You must agree to the privacy policy.'));

    if (errors.length) {
      errBox.textContent = errors[0];
      errBox.style.display = 'block';
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending…';

    try {
      const res = await API.submitContact({ _gotcha: honeypot, name, email, subject, phone, orderid, message, consent: true });
      if (res.ok) {
        // Show thank-you
        document.getElementById('contactFormCard').innerHTML = `
          <div class="contact-thank-you">
            <div class="ty-icon">✉️</div>
            <h2>Message Sent!</h2>
            <p>Thank you, ${escapeHtml(name)}! We've received your message and will get back to you soon.</p>
            <a href="#/" class="btn btn-primary">Back to Home</a>
          </div>`;
      } else {
        errBox.textContent = res.error || 'Something went wrong. Please try again.';
        errBox.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Message';
      }
    } catch(e) {
      errBox.textContent = 'Network error. Please try again.';
      errBox.style.display = 'block';
      submitBtn.disabled = false;
      submitBtn.textContent = 'Send Message';
    }
  });
}

// ══════════════════════════════════════════════════════════════════════════════
// Admin Contact Inbox
// ══════════════════════════════════════════════════════════════════════════════

async function renderContactInbox(container) {
  container.innerHTML = `<div style="text-align:center;padding:40px;color:var(--wp-gray-50)">Loading messages…</div>`;

  let data;
  try {
    data = await API.getContacts();
  } catch(e) {
    container.innerHTML = `<div class="empty-state"><h3>Could not load messages</h3></div>`;
    return;
  }

  const contacts = data.contacts || [];

  function formatContactDate(iso) {
    try {
      const d = new Date(iso);
      const now = new Date();
      const diff = (now - d) / 1000;
      if (diff < 60)   return 'Just now';
      if (diff < 3600) return Math.floor(diff/60) + 'm ago';
      if (diff < 86400 && d.getDate() === now.getDate()) return d.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});
      return d.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
    } catch { return ''; }
  }

  function renderList() {
    const unread = contacts.filter(c => !c.read).length;
    container.innerHTML = `
      <div style="max-width:860px">
        <div class="wp-card">
          <div class="wp-card-header">
            <h2 class="wp-card-title">Inbox ${unread > 0 ? `<span class="unread-badge">${unread} unread</span>` : ''}</h2>
            <a href="#/admin/settings#contact" class="btn btn-secondary" style="font-size:12px">⚙ Form Settings</a>
          </div>
          ${contacts.length === 0 ? `<div class="wp-card-body"><div class="empty-state" style="padding:40px 20px"><h3>No messages yet</h3><p>When visitors submit the contact form, their messages will appear here.</p></div></div>` : ''}
          <div id="contactMsgList">
            ${contacts.map(c => `
              <div class="contact-msg-row ${!c.read ? 'unread' : ''}" data-id="${escapeHtml(c.id)}">
                <div class="contact-msg-meta">
                  <div class="contact-msg-from">${escapeHtml(c.name)} ${c.email ? `<span style="font-weight:400;color:var(--wp-gray-50)">&lt;${escapeHtml(c.email)}&gt;</span>` : ''}</div>
                  <div class="contact-msg-subject">${escapeHtml(c.subject || '(No subject)')}</div>
                  <div class="contact-msg-snippet">${escapeHtml((c.message||'').slice(0, 100))}</div>
                </div>
                <div class="contact-msg-date">${formatContactDate(c.date)}</div>
              </div>`).join('')}
          </div>
        </div>
      </div>
      <div id="msgDetailPane"></div>`;

    container.querySelectorAll('.contact-msg-row').forEach(row => {
      row.addEventListener('click', () => openMsg(row.dataset.id));
    });
  }

  async function openMsg(id) {
    const c = contacts.find(m => m.id === id);
    if (!c) return;

    // Mark as read
    if (!c.read) {
      c.read = true;
      try { await API.markContactRead(id, true); } catch(e) {}
      const row = container.querySelector(`.contact-msg-row[data-id="${id}"]`);
      if (row) row.classList.remove('unread');
      // Update badge
      const unread = contacts.filter(m => !m.read).length;
      const badge  = document.getElementById('sidebarUnreadBadge');
      if (badge) { badge.textContent = unread; badge.style.display = unread > 0 ? 'inline-flex' : 'none'; }
    }

    const pane = document.getElementById('msgDetailPane');
    pane.innerHTML = `
      <div class="contact-msg-detail">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
          <h3 style="margin:0">${escapeHtml(c.subject || '(No subject)')}</h3>
          <div style="display:flex;gap:8px">
            <a href="mailto:${escapeHtml(c.email)}?subject=Re: ${encodeURIComponent(c.subject || 'Your inquiry')}" class="btn btn-primary" style="font-size:13px">↩ Reply via Email</a>
            <button class="btn btn-danger" id="deleteMsgBtn" style="font-size:13px">🗑 Delete</button>
          </div>
        </div>
        <div class="contact-msg-field"><strong>From</strong><span>${escapeHtml(c.name)} &lt;<a href="mailto:${escapeHtml(c.email)}">${escapeHtml(c.email)}</a>&gt;</span></div>
        ${c.phone   ? `<div class="contact-msg-field"><strong>Phone</strong><span>${escapeHtml(c.phone)}</span></div>` : ''}
        ${c.orderid ? `<div class="contact-msg-field"><strong>Order/Account ID</strong><span>${escapeHtml(c.orderid)}</span></div>` : ''}
        <div class="contact-msg-field"><strong>Date</strong><span>${new Date(c.date).toLocaleString()}</span></div>
        <div class="contact-msg-body">${escapeHtml(c.message || '')}</div>
      </div>`;

    document.getElementById('deleteMsgBtn').addEventListener('click', async () => {
      if (!confirm('Delete this message? This cannot be undone.')) return;
      try {
        await API.deleteContact(id);
        const idx = contacts.findIndex(m => m.id === id);
        if (idx > -1) contacts.splice(idx, 1);
        pane.innerHTML = '';
        renderList();
      } catch(e) { alert('Delete failed.'); }
    });

    pane.scrollIntoView({ behavior:'smooth', block:'nearest' });
  }

  renderList();
}

// ══════════════════════════════════════════════════════════════════════════════
// Boot
// ══════════════════════════════════════════════════════════════════════════════

window.addEventListener('DOMContentLoaded', router);
window.addEventListener('hashchange', router);

// ── ?post=SLUG → hash redirect ────────────────────────────────────────────────
// When someone arrives via a social share link (?post=SLUG), redirect them
// to the hash-router URL (#/post/SLUG) so the SPA renders the article.
// The ?post parameter stays in the URL (PHP uses it for OG tags) but the
// hash drives which page is shown.
(function () {
  const params = new URLSearchParams(window.location.search);
  const slug   = params.get('post');
  if (slug && !location.hash.startsWith('#/post/')) {
    // Replace hash without triggering a full reload; router will fire via hashchange
    history.replaceState(null, '', window.location.pathname + window.location.search + '#/post/' + encodeURIComponent(slug));
    // Manually fire router since replaceState doesn't trigger hashchange
    router();
  }
})();

// ══════════════════════════════════════════════════════════════════════════════
// Protect images — disable right-click context menu and drag-to-save
// ══════════════════════════════════════════════════════════════════════════════
document.addEventListener('contextmenu', function(e) {
  if (e.target.tagName === 'IMG') e.preventDefault();
}, false);
document.addEventListener('dragstart', function(e) {
  if (e.target.tagName === 'IMG') e.preventDefault();
}, false);

// ══════════════════════════════════════════════════════════════════════════════
// Video overlay — keep logo/title visible during fullscreen
// When the browser fullscreens the <iframe> directly, the sibling .vp-overlay
// is not part of the fullscreen layer. We move it to <body> and apply
// .vp-fullscreen-active (position:fixed) so it floats above everything.
// On fullscreen exit we put it back inside the player frame.
// ══════════════════════════════════════════════════════════════════════════════
(function () {
  let activeOverlay = null;
  let overlayPlaceholder = null;

  function onFullscreenChange() {
    const fsEl = document.fullscreenElement
               || document.webkitFullscreenElement
               || document.mozFullScreenElement
               || document.msFullscreenElement;

    if (fsEl) {
      // Find the closest .video-player-frame-wrap ancestor (or the element itself)
      const frameWrap = fsEl.closest
        ? (fsEl.classList.contains('video-player-frame-wrap') ? fsEl : fsEl.closest('.video-player-frame-wrap'))
        : null;
      const overlay = frameWrap ? frameWrap.querySelector('.vp-overlay') : null;

      if (overlay) {
        // Insert a placeholder so we know where to put it back
        overlayPlaceholder = document.createComment('vp-overlay-placeholder');
        overlay.parentNode.insertBefore(overlayPlaceholder, overlay);
        document.body.appendChild(overlay);
        overlay.classList.add('vp-fullscreen-active');
        activeOverlay = overlay;
      }
    } else {
      // Exiting fullscreen — restore overlay to its original position
      if (activeOverlay && overlayPlaceholder && overlayPlaceholder.parentNode) {
        activeOverlay.classList.remove('vp-fullscreen-active');
        overlayPlaceholder.parentNode.insertBefore(activeOverlay, overlayPlaceholder);
        overlayPlaceholder.parentNode.removeChild(overlayPlaceholder);
      }
      activeOverlay = null;
      overlayPlaceholder = null;
    }
  }

  document.addEventListener('fullscreenchange',       onFullscreenChange);
  document.addEventListener('webkitfullscreenchange', onFullscreenChange);
  document.addEventListener('mozfullscreenchange',    onFullscreenChange);
  document.addEventListener('MSFullscreenChange',     onFullscreenChange);
})();
</script>
</body>
</html>