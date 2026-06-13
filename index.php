<?php
include 'config.php';

$search_query = "";
$bugs = [];

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $sql = "SELECT * FROM faculty WHERE name = '" . $search_query . "'";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bugs[] = $row;
        }
    } else {
        $sql_error = $conn->error;
    }
} else {
    $sql = "SELECT * FROM faculty";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bugs[] = $row;
        }
    }
}

$total   = count($bugs);
$critical = 0; $high = 0; $medium = 0;
foreach ($bugs as $b) {
    if ($b['severity'] === 'critical') $critical++;
    elseif ($b['severity'] === 'high') $high++;
    else $medium++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>BugVault — Security Issue Tracker</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,400&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg-base:        #060608;
    --bg-surface:     #0d0d12;
    --bg-elevated:    #13131a;
    --bg-hover:       #1a1a24;
    --border:         rgba(255,255,255,0.07);
    --border-bright:  rgba(255,255,255,0.13);
    --text-primary:   #f0f0f5;
    --text-secondary: #8888a0;
    --text-muted:     #4a4a60;
    --accent:         #7c6af7;
    --accent-glow:    rgba(124, 106, 247, 0.25);
    --accent-soft:    rgba(124, 106, 247, 0.12);
    --critical:       #ff4d6a;
    --critical-bg:    rgba(255,77,106,0.1);
    --high:           #ff9c3a;
    --high-bg:        rgba(255,156,58,0.1);
    --medium:         #f5c542;
    --medium-bg:      rgba(245,197,66,0.1);
    --low:            #34d399;
    --low-bg:         rgba(52,211,153,0.1);
    --glass:          rgba(255,255,255,0.035);
    --glass-border:   rgba(255,255,255,0.06);
    --radius-sm:      6px;
    --radius:         12px;
    --radius-lg:      18px;
}

html { scroll-behavior: smooth; }

body {
    font-family: 'Inter', -apple-system, sans-serif;
    background: var(--bg-base);
    color: var(--text-primary);
    min-height: 100vh;
    display: flex;
    overflow-x: hidden;
    font-size: 13px;
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
}

body::before {
    content: '';
    position: fixed;
    inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 20% -10%, rgba(124,106,247,0.08) 0%, transparent 60%),
        radial-gradient(ellipse 60% 50% at 80% 110%, rgba(99,198,255,0.05) 0%, transparent 60%);
    pointer-events: none;
    z-index: 0;
}

.layout { display: flex; width: 100%; position: relative; z-index: 1; }

aside {
    width: 240px;
    min-height: 100vh;
    background: var(--bg-surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    padding: 20px 0;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    flex-shrink: 0;
}

.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px 20px 20px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 8px;
}

.logo-icon {
    width: 30px; height: 30px;
    background: linear-gradient(135deg, var(--accent), #a855f7);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
    box-shadow: 0 0 20px var(--accent-glow);
}

.logo-text {
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    letter-spacing: -0.02em;
}

.logo-version {
    font-family: 'JetBrains Mono', monospace;
    font-size: 9px;
    color: var(--text-muted);
    background: var(--bg-elevated);
    padding: 1px 5px;
    border-radius: 4px;
    border: 1px solid var(--border);
}

.nav-section { padding: 0 12px; margin-bottom: 4px; }

.nav-label {
    font-size: 9px;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-muted);
    padding: 8px 8px 4px;
    font-family: 'JetBrains Mono', monospace;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 12.5px;
    font-weight: 500;
    transition: all 0.15s ease;
    margin-bottom: 1px;
}

.nav-item:hover {
    background: var(--bg-hover);
    color: var(--text-primary);
}

.nav-item.active {
    background: var(--accent-soft);
    color: var(--accent);
    border: 1px solid rgba(124,106,247,0.2);
}

.nav-item i { width: 16px; text-align: center; font-size: 12px; opacity: 0.8; }

.nav-badge {
    margin-left: auto;
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    color: var(--text-muted);
    font-size: 10px;
    padding: 1px 6px;
    border-radius: 20px;
    font-family: 'JetBrains Mono', monospace;
}

.nav-badge.critical { background: var(--critical-bg); color: var(--critical); border-color: rgba(255,77,106,0.2); }

.sidebar-footer {
    margin-top: auto;
    padding: 16px 20px 4px;
    border-top: 1px solid var(--border);
}

.user-chip {
    display: flex;
    align-items: center;
    gap: 10px;
}

.avatar {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c6af7, #a855f7);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
}

.user-info { flex: 1; min-width: 0; }
.user-name { font-size: 12px; font-weight: 600; color: var(--text-primary); }
.user-role { font-size: 10px; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; }

main {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    overflow: hidden;
}

.topbar {
    background: var(--bg-surface);
    border-bottom: 1px solid var(--border);
    padding: 12px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    position: sticky;
    top: 0;
    z-index: 100;
    backdrop-filter: blur(20px);
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-muted);
}

.breadcrumb span { color: var(--text-secondary); }
.breadcrumb .sep { color: var(--border-bright); }
.breadcrumb .current { color: var(--text-primary); font-weight: 600; }

.search-form { display: flex; gap: 8px; align-items: center; }

.search-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.search-wrap i {
    position: absolute;
    left: 11px;
    color: var(--text-muted);
    font-size: 11px;
    pointer-events: none;
}

.search-input {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 7px 12px 7px 32px;
    color: var(--text-primary);
    font-size: 12px;
    font-family: 'JetBrains Mono', monospace;
    width: 280px;
    outline: none;
    transition: all 0.2s;
}

.search-input::placeholder { color: var(--text-muted); }

.search-input:focus {
    border-color: var(--accent);
    background: var(--bg-hover);
    box-shadow: 0 0 0 3px var(--accent-glow);
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: all 0.15s;
    text-decoration: none;
    white-space: nowrap;
    font-family: inherit;
}

.btn-primary {
    background: var(--accent);
    color: white;
    box-shadow: 0 0 16px var(--accent-glow);
}

.btn-primary:hover {
    background: #8f7fff;
    box-shadow: 0 0 24px var(--accent-glow);
    transform: translateY(-1px);
}

.btn-ghost {
    background: var(--bg-elevated);
    color: var(--text-secondary);
    border: 1px solid var(--border);
}

.btn-ghost:hover {
    background: var(--bg-hover);
    color: var(--text-primary);
    border-color: var(--border-bright);
}

.content { flex: 1; padding: 28px; overflow-y: auto; }

.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 24px;
    gap: 16px;
}

.page-title { font-size: 20px; font-weight: 700; color: var(--text-primary); letter-spacing: -0.03em; margin-bottom: 4px; }
.page-sub { font-size: 12px; color: var(--text-secondary); }

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}

.stat-card {
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 18px;
    position: relative;
    overflow: hidden;
    transition: all 0.2s;
}

.stat-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: var(--glass);
    backdrop-filter: blur(10px);
    pointer-events: none;
}

.stat-card:hover {
    border-color: var(--border-bright);
    transform: translateY(-2px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
}

.stat-label {
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-muted);
    font-family: 'JetBrains Mono', monospace;
    margin-bottom: 8px;
}

.stat-value { font-size: 28px; font-weight: 700; letter-spacing: -0.04em; }
.stat-value.total { color: var(--text-primary); }
.stat-value.crit  { color: var(--critical); }
.stat-value.high  { color: var(--high); }
.stat-value.med   { color: var(--medium); }

.stat-glow-crit::after {
    content: ''; position: absolute;
    bottom: 0; left: 0; right: 0; height: 2px;
    background: var(--critical);
    box-shadow: 0 0 12px var(--critical);
    border-radius: 0 0 var(--radius) var(--radius);
}
.stat-glow-high::after {
    content: ''; position: absolute;
    bottom: 0; left: 0; right: 0; height: 2px;
    background: var(--high);
    border-radius: 0 0 var(--radius) var(--radius);
}
.stat-glow-med::after {
    content: ''; position: absolute;
    bottom: 0; left: 0; right: 0; height: 2px;
    background: var(--medium);
    border-radius: 0 0 var(--radius) var(--radius);
}

.board-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.board-title {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.count-pill {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    color: var(--text-muted);
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 20px;
    font-family: 'JetBrains Mono', monospace;
}

.bugs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 14px;
}

.bug-card {
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}

.bug-card::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: 0;
    background: linear-gradient(135deg, var(--accent-soft), transparent);
    transition: opacity 0.2s;
    pointer-events: none;
}

.bug-card:hover {
    border-color: var(--border-bright);
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.4), 0 0 0 1px var(--glass-border);
}

.bug-card:hover::before { opacity: 1; }

.bug-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
}

.bug-id {
    font-family: 'JetBrains Mono', monospace;
    font-size: 9px;
    color: var(--text-muted);
    letter-spacing: 0.05em;
}

.severity-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
    position: relative;
}

.severity-dot::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    opacity: 0.4;
}

.sev-critical { background: var(--critical); box-shadow: 0 0 8px var(--critical); }
.sev-critical::after { background: var(--critical); }
.sev-high { background: var(--high); box-shadow: 0 0 8px var(--high); }
.sev-medium { background: var(--medium); }
.sev-low { background: var(--low); }

.bug-name {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.4;
    letter-spacing: -0.015em;
}

.bug-desc {
    font-size: 11.5px;
    color: var(--text-secondary);
    line-height: 1.6;
}

.bug-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 10px;
    border-top: 1px solid var(--border);
}

.tag {
    font-family: 'JetBrains Mono', monospace;
    font-size: 10px;
    font-weight: 500;
    padding: 3px 8px;
    border-radius: 4px;
    letter-spacing: 0.03em;
}

.tag-critical { background: var(--critical-bg); color: var(--critical); border: 1px solid rgba(255,77,106,0.25); }
.tag-high     { background: var(--high-bg);     color: var(--high);     border: 1px solid rgba(255,156,58,0.25); }
.tag-medium   { background: var(--medium-bg);   color: var(--medium);   border: 1px solid rgba(245,197,66,0.25); }
.tag-low      { background: var(--low-bg);      color: var(--low);      border: 1px solid rgba(52,211,153,0.25); }

.dept-chip {
    font-size: 10px;
    color: var(--text-muted);
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    padding: 2px 8px;
    border-radius: 4px;
    font-family: 'JetBrains Mono', monospace;
}

.status-open {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    color: var(--low);
    font-family: 'JetBrains Mono', monospace;
}

.status-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
    background: var(--low);
    box-shadow: 0 0 6px var(--low);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(0.85); }
}

.empty-state {
    grid-column: 1 / -1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 64px 24px;
    border: 1px dashed var(--border);
    border-radius: var(--radius-lg);
    text-align: center;
    gap: 12px;
}

.empty-state i { font-size: 32px; color: var(--text-muted); }
.empty-state h3 { font-size: 14px; font-weight: 600; color: var(--text-secondary); }
.empty-state p { font-size: 12px; color: var(--text-muted); }

.sql-error {
    background: rgba(255,77,106,0.06);
    border: 1px solid rgba(255,77,106,0.2);
    border-radius: var(--radius);
    padding: 14px 18px;
    font-family: 'JetBrains Mono', monospace;
    font-size: 11px;
    color: var(--critical);
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.search-result-banner {
    background: var(--accent-soft);
    border: 1px solid rgba(124,106,247,0.2);
    border-radius: var(--radius-sm);
    padding: 8px 14px;
    font-size: 11.5px;
    color: var(--accent);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-family: 'JetBrains Mono', monospace;
}

@media (max-width: 900px) {
    aside { display: none; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .content { padding: 16px; }
    .topbar { padding: 10px 16px; }
    .search-input { width: 200px; }
}

@media (max-width: 580px) {
    .stats-grid { grid-template-columns: 1fr 1fr; }
    .bugs-grid { grid-template-columns: 1fr; }
    .page-header { flex-direction: column; }
    .search-input { width: 140px; }
}
</style>
</head>
<body>
<div class="layout">

<aside>
    <div class="sidebar-logo">
        <div class="logo-icon"><i class="fa-solid fa-shield-halved" style="color:white;font-size:13px"></i></div>
        <div>
            <div class="logo-text">BugVault</div>
        </div>
        <span class="logo-version">v2.1</span>
    </div>

    <div class="nav-section">
        <div class="nav-label">Workspace</div>
        <a href="index.php" class="nav-item active">
            <i class="fa-solid fa-table-columns"></i> Board
            <span class="nav-badge"><?php echo $total; ?></span>
        </a>
        <a href="add-bug.php" class="nav-item">
            <i class="fa-solid fa-plus"></i> New Issue
        </a>
        <a href="stored-xss.php" class="nav-item">
            <i class="fa-solid fa-comment-dots"></i> Comments
            <span class="nav-badge critical">XSS</span>
        </a>
    </div>

    <div class="nav-section" style="margin-top:8px">
        <div class="nav-label">Severity</div>
        <a href="?severity=critical" class="nav-item" style="color:var(--critical)">
            <i class="fa-solid fa-circle" style="font-size:7px;color:var(--critical)"></i> Critical
            <span class="nav-badge critical"><?php echo $critical; ?></span>
        </a>
        <a href="?severity=high" class="nav-item" style="color:var(--high)">
            <i class="fa-solid fa-circle" style="font-size:7px;color:var(--high)"></i> High
            <span class="nav-badge" style="color:var(--high)"><?php echo $high; ?></span>
        </a>
        <a href="?severity=medium" class="nav-item" style="color:var(--medium)">
            <i class="fa-solid fa-circle" style="font-size:7px;color:var(--medium)"></i> Medium
            <span class="nav-badge" style="color:var(--medium)"><?php echo $medium; ?></span>
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="avatar">SK</div>
            <div class="user-info">
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
            <span class="current">Issue Board</span>
        </div>
        <form method="GET" action="index.php" class="search-form">
            <div class="search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="text"
                    name="search"
                    class="search-input"
                    value="<?php echo htmlspecialchars($search_query); ?>"
                    placeholder="Search issues...">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-search" style="font-size:10px"></i> Find
            </button>
            <?php if ($search_query): ?>
            <a href="index.php" class="btn btn-ghost"><i class="fa-solid fa-xmark" style="font-size:10px"></i></a>
            <?php endif; ?>
        </form>
        <a href="add-bug.php" class="btn btn-primary">
            <i class="fa-solid fa-plus" style="font-size:10px"></i> New Issue
        </a>
    </div>

    <div class="content">
        <div class="page-header">
            <div>
                <div class="page-title">Security Issue Tracker</div>
                <div class="page-sub">Track, triage and resolve vulnerabilities across your codebase</div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Issues</div>
                <div class="stat-value total"><?php echo $total; ?></div>
            </div>
            <div class="stat-card stat-glow-crit">
                <div class="stat-label">Critical</div>
                <div class="stat-value crit"><?php echo $critical; ?></div>
            </div>
            <div class="stat-card stat-glow-high">
                <div class="stat-label">High</div>
                <div class="stat-value high"><?php echo $high; ?></div>
            </div>
            <div class="stat-card stat-glow-med">
                <div class="stat-label">Medium</div>
                <div class="stat-value med"><?php echo $medium; ?></div>
            </div>
        </div>

        <?php if (isset($sql_error)): ?>
        <div class="sql-error">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>SQL Error: <?php echo $sql_error; ?></div>
        </div>
        <?php endif; ?>

        <?php if ($search_query): ?>
        <div class="search-result-banner">
            <i class="fa-solid fa-magnifying-glass" style="font-size:10px"></i>
            Showing results for: <?php echo htmlspecialchars($search_query); ?>
            &nbsp;|&nbsp; <?php echo $total; ?> match(es) found
        </div>
        <?php endif; ?>

        <div class="board-header">
            <div class="board-title">
                <i class="fa-solid fa-layer-group" style="color:var(--accent);font-size:11px"></i>
                Active Backlog
                <span class="count-pill"><?php echo $total; ?></span>
            </div>
            <div style="display:flex;gap:8px">
                <button class="btn btn-ghost" style="font-size:11px">
                    <i class="fa-solid fa-filter" style="font-size:10px"></i> Filter
                </button>
                <button class="btn btn-ghost" style="font-size:11px">
                    <i class="fa-solid fa-sort" style="font-size:10px"></i> Sort
                </button>
            </div>
        </div>

        <div class="bugs-grid">
            <?php if (empty($bugs)): ?>
            <div class="empty-state">
                <i class="fa-solid fa-circle-check"></i>
                <h3>No issues found</h3>
                <p><?php echo $search_query ? 'No results matched your search query.' : 'This project has no open issues.'; ?></p>
            </div>
            <?php else: ?>
            <?php foreach ($bugs as $i => $bug):
                $sev = strtolower($bug['severity'] ?? 'medium');
                $dotClass = 'sev-' . $sev;
                $tagClass = 'tag-' . $sev;
            ?>
            <div class="bug-card" style="animation: fadeUp 0.3s ease <?php echo $i * 0.05; ?>s both;">
                <div class="bug-card-top">
                    <div class="bug-id">BUG-<?php echo str_pad($bug['id'], 4, '0', STR_PAD_LEFT); ?></div>
                    <div class="severity-dot <?php echo $dotClass; ?>"></div>
                </div>
                <div>
                    <div class="bug-name"><?php echo htmlspecialchars($bug['name']); ?></div>
                    <div class="bug-desc" style="margin-top:6px"><?php echo htmlspecialchars($bug['designation']); ?></div>
                </div>
                <div class="bug-card-footer">
                    <div style="display:flex;align-items:center;gap:6px">
                        <span class="tag <?php echo $tagClass; ?>"><?php echo strtoupper($sev); ?></span>
                        <span class="dept-chip"><?php echo htmlspecialchars($bug['image']); ?></span>
                    </div>
                    <div class="status-open">
                        <span class="status-dot"></span> open
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>
</div>

<style>
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
</body>
</html>
