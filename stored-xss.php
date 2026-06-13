<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $reported_by = $_POST['reported_by'] ?? 'anonymous';
    $severity    = $_POST['severity'] ?? 'medium';

    if (!empty($title)) {
        $sql = "INSERT INTO bugs_stored (title, description, reported_by, severity, status) VALUES ('" . $title . "', '" . $description . "', '" . $reported_by . "', '" . $severity . "', 'open')";
        $conn->query($sql);
    }
    header("Location: stored-xss.php");
    exit;
}

$comments = [];
$result = $conn->query("SELECT * FROM bugs_stored ORDER BY created_at DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) $comments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BugVault — Issue Comments</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --bg-base: #060608; --bg-surface: #0d0d12; --bg-elevated: #13131a; --bg-hover: #1a1a24;
    --border: rgba(255,255,255,0.07); --border-bright: rgba(255,255,255,0.13);
    --text-primary: #f0f0f5; --text-secondary: #8888a0; --text-muted: #4a4a60;
    --accent: #7c6af7; --accent-glow: rgba(124,106,247,0.25); --accent-soft: rgba(124,106,247,0.12);
    --critical: #ff4d6a; --high: #ff9c3a; --medium: #f5c542; --low: #34d399;
    --radius-sm: 6px; --radius: 12px; --radius-lg: 18px;
}
body {
    font-family: 'Inter', sans-serif; background: var(--bg-base); color: var(--text-primary);
    min-height: 100vh; display: flex; font-size: 13px; line-height: 1.5; -webkit-font-smoothing: antialiased;
}
body::before {
    content: ''; position: fixed; inset: 0;
    background: radial-gradient(ellipse 80% 60% at 20% -10%, rgba(124,106,247,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 110%, rgba(99,198,255,0.05) 0%, transparent 60%);
    pointer-events: none; z-index: 0;
}
.layout { display: flex; width: 100%; position: relative; z-index: 1; }
aside {
    width: 240px; min-height: 100vh; background: var(--bg-surface); border-right: 1px solid var(--border);
    display: flex; flex-direction: column; padding: 20px 0; position: sticky; top: 0; height: 100vh; flex-shrink: 0;
}
.sidebar-logo { display: flex; align-items: center; gap: 10px; padding: 4px 20px 20px; border-bottom: 1px solid var(--border); margin-bottom: 8px; }
.logo-icon { width: 30px; height: 30px; background: linear-gradient(135deg, var(--accent), #a855f7); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; box-shadow: 0 0 20px var(--accent-glow); }
.logo-text { font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 600; color: var(--text-primary); }
.logo-version { font-family: 'JetBrains Mono', monospace; font-size: 9px; color: var(--text-muted); background: var(--bg-elevated); padding: 1px 5px; border-radius: 4px; border: 1px solid var(--border); }
.nav-section { padding: 0 12px; margin-bottom: 4px; }
.nav-label { font-size: 9px; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-muted); padding: 8px 8px 4px; font-family: 'JetBrains Mono', monospace; }
.nav-item { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: var(--radius-sm); color: var(--text-secondary); text-decoration: none; font-size: 12.5px; font-weight: 500; transition: all 0.15s ease; margin-bottom: 1px; }
.nav-item:hover { background: var(--bg-hover); color: var(--text-primary); }
.nav-item.active { background: var(--accent-soft); color: var(--accent); border: 1px solid rgba(124,106,247,0.2); }
.nav-item i { width: 16px; text-align: center; font-size: 12px; opacity: 0.8; }
.nav-badge { margin-left: auto; background: var(--bg-elevated); border: 1px solid var(--border); color: var(--text-muted); font-size: 10px; padding: 1px 6px; border-radius: 20px; font-family: 'JetBrains Mono', monospace; }
.nav-badge.critical { background: rgba(255,77,106,0.1); color: var(--critical); border-color: rgba(255,77,106,0.2); }
.sidebar-footer { margin-top: auto; padding: 16px 20px 4px; border-top: 1px solid var(--border); }
.user-chip { display: flex; align-items: center; gap: 10px; }
.avatar { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg, #7c6af7, #a855f7); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: white; }
.user-name { font-size: 12px; font-weight: 600; color: var(--text-primary); }
.user-role { font-size: 10px; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; }
main { flex: 1; display: flex; flex-direction: column; }
.topbar { background: var(--bg-surface); border-bottom: 1px solid var(--border); padding: 12px 28px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; backdrop-filter: blur(20px); }
.breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--text-muted); }
.breadcrumb span { color: var(--text-secondary); }
.breadcrumb .sep { color: var(--border-bright); }
.breadcrumb .current { color: var(--text-primary); font-weight: 600; }
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: var(--radius-sm); font-size: 12px; font-weight: 600; cursor: pointer; border: none; transition: all 0.15s; text-decoration: none; font-family: inherit; }
.btn-primary { background: var(--accent); color: white; box-shadow: 0 0 16px var(--accent-glow); }
.btn-primary:hover { background: #8f7fff; transform: translateY(-1px); }
.btn-ghost { background: var(--bg-elevated); color: var(--text-secondary); border: 1px solid var(--border); }
.btn-ghost:hover { background: var(--bg-hover); color: var(--text-primary); }
.content { flex: 1; padding: 32px; max-width: 800px; width: 100%; margin: 0 auto; }
.page-title { font-size: 20px; font-weight: 700; letter-spacing: -0.03em; margin-bottom: 4px; }
.page-sub { font-size: 12px; color: var(--text-secondary); margin-bottom: 28px; }
.submit-form {
    background: var(--bg-surface); border: 1px solid var(--border);
    border-radius: var(--radius-lg); padding: 24px; margin-bottom: 28px;
    display: flex; flex-direction: column; gap: 16px;
}
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-label { font-size: 11px; font-weight: 600; color: var(--text-secondary); letter-spacing: 0.03em; }
.form-input, .form-select, .form-textarea {
    background: var(--bg-elevated); border: 1px solid var(--border);
    border-radius: var(--radius-sm); padding: 8px 12px;
    color: var(--text-primary); font-size: 13px; font-family: 'Inter', sans-serif;
    outline: none; transition: all 0.2s; width: 100%;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
    border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow);
}
.form-input::placeholder, .form-textarea::placeholder { color: var(--text-muted); }
.form-textarea { resize: vertical; min-height: 80px; }
.comment-list { display: flex; flex-direction: column; gap: 12px; }
.comment-card {
    background: var(--bg-surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 16px; transition: all 0.2s;
}
.comment-card:hover { border-color: var(--border-bright); }
.comment-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.comment-meta { display: flex; align-items: center; gap: 10px; }
.comment-avatar { width: 26px; height: 26px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: white; flex-shrink: 0; }
.comment-author { font-size: 12px; font-weight: 600; color: var(--text-primary); }
.comment-time { font-size: 10px; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; }
.comment-title { font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 5px; }
.comment-body { font-size: 12px; color: var(--text-secondary); line-height: 1.6; }
.tag { font-family: 'JetBrains Mono', monospace; font-size: 10px; font-weight: 500; padding: 2px 8px; border-radius: 4px; }
.tag-critical { background: rgba(255,77,106,0.1); color: var(--critical); border: 1px solid rgba(255,77,106,0.25); }
.tag-high { background: rgba(255,156,58,0.1); color: var(--high); border: 1px solid rgba(255,156,58,0.25); }
.tag-medium { background: rgba(245,197,66,0.1); color: var(--medium); border: 1px solid rgba(245,197,66,0.25); }
.tag-low { background: rgba(52,211,153,0.1); color: var(--low); border: 1px solid rgba(52,211,153,0.25); }
.section-label { font-size: 11px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px; font-family: 'JetBrains Mono', monospace; }
@media (max-width: 900px) { aside { display: none; } .content { padding: 16px; } }
</style>
</head>
<body>
<div class="layout">
<aside>
    <div class="sidebar-logo">
        <div class="logo-icon"><i class="fa-solid fa-shield-halved" style="color:white;font-size:13px"></i></div>
        <div><div class="logo-text">BugVault</div></div>
        <span class="logo-version">v2.1</span>
    </div>
    <div class="nav-section">
        <div class="nav-label">Workspace</div>
        <a href="index.php" class="nav-item"><i class="fa-solid fa-table-columns"></i> Board</a>
        <a href="add-bug.php" class="nav-item"><i class="fa-solid fa-plus"></i> New Issue</a>
        <a href="stored-xss.php" class="nav-item active">
            <i class="fa-solid fa-comment-dots"></i> Comments
            <span class="nav-badge critical">XSS</span>
        </a>
    </div>
    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="avatar">SK</div>
            <div>
                <div class="user-name">Sakib</div>
                <div class="user-role">// ADMIN</div>
            </div>
        </div>
    </div>
</aside>

<main>
    <div class="topbar">
        <div class="breadcrumb">
            <i class="fa-solid fa-shield-halved" style="color:var(--accent);font-size:11px"></i>
            <span>BugVault</span>
            <span class="sep">/</span>
            <span class="current">Comments Feed</span>
        </div>
        <a href="index.php" class="btn btn-ghost">
            <i class="fa-solid fa-arrow-left" style="font-size:10px"></i> Board
        </a>
    </div>

    <div class="content">
        <div class="page-title">Issue Comments</div>
        <div class="page-sub">Community-submitted issue reports and comments — stored without sanitization</div>

        <div class="submit-form">
            <div class="section-label">// POST_COMMENT</div>
            <form method="POST" action="stored-xss.php">
                <div style="display:flex;flex-direction:column;gap:14px">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Issue Title</label>
                            <input type="text" name="title" class="form-input" placeholder="Issue title or comment heading..." required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reported By</label>
                            <input type="text" name="reported_by" class="form-input" placeholder="Your name / handle">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-textarea" placeholder="Detailed description, proof of concept, or notes..."></textarea>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <select name="severity" class="form-select" style="width:160px">
                            <option value="critical">Critical</option>
                            <option value="high">High</option>
                            <option value="medium" selected>Medium</option>
                            <option value="low">Low</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane" style="font-size:10px"></i> Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="section-label">// STORED_COMMENTS (<?php echo count($comments); ?>)</div>
        <div class="comment-list">
            <?php if (empty($comments)): ?>
            <div style="text-align:center;padding:40px;color:var(--text-muted);font-size:12px;border:1px dashed var(--border);border-radius:var(--radius)">No comments yet</div>
            <?php else: ?>
            <?php foreach ($comments as $c):
                $sev = strtolower($c['severity'] ?? 'medium');
                $initial = strtoupper(substr($c['reported_by'] ?? 'A', 0, 1));
            ?>
            <div class="comment-card">
                <div class="comment-header">
                    <div class="comment-meta">
                        <div class="comment-avatar"><?php echo $initial; ?></div>
                        <div>
                            <div class="comment-author"><?php echo $c['reported_by']; ?></div>
                            <div class="comment-time"><?php echo date('Y-m-d H:i', strtotime($c['created_at'])); ?></div>
                        </div>
                    </div>
                    <span class="tag tag-<?php echo $sev; ?>"><?php echo strtoupper($sev); ?></span>
                </div>
                <div class="comment-title"><?php echo $c['title']; ?></div>
                <?php if ($c['description']): ?>
                <div class="comment-body"><?php echo $c['description']; ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>
</div>
</body>
</html>
