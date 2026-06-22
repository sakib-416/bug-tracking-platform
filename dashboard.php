<?php
require_once 'config.php';
check_auth();

// VULNERABILITY MECHANISM (Reflected XSS):
// The search parameter is stored and reflected directly into the UI below without htmlspecialchars()
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Retrieve issues based on query
if ($search !== '') {
    // Also vulnerable to SQL injection if desired, but isolating this primarily to demonstrate clean Reflected XSS
    $query = "SELECT issues.*, u1.username as reporter, u2.username as assignee 
              FROM issues 
              LEFT JOIN users u1 ON issues.reporter_id = u1.id 
              LEFT JOIN users u2 ON issues.assignee_id = u2.id
              WHERE issues.title LIKE '%" . $search . "%' OR issues.description LIKE '%" . $search . "%'";
    $issues_res = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
} else {
    $issues_res = $db->query("SELECT issues.*, u1.username as reporter, u2.username as assignee FROM issues LEFT JOIN users u1 ON issues.reporter_id = u1.id LEFT JOIN users u2 ON issues.assignee_id = u2.id ORDER BY issues.id DESC")->fetchAll(PDO::FETCH_ASSOC);
}

// Compute metrics
$total_bugs = count($issues_res);
$critical_count = 0;
foreach($issues_res as $i) { if($i['severity'] === 'Critical' || $i['severity'] === 'High') $critical_count++; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - GlassBug Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: radial-gradient(circle at top right, #0f172a, #020617); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-nav { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="min-h-screen text-slate-100 pb-12">

    <nav class="glass-nav sticky top-0 z-50 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-8">
            <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">GlassBug Core</span>
            <div class="hidden md:flex space-x-1 text-sm font-medium text-slate-300">
                <a href="dashboard.php" class="px-4 py-2 bg-white/5 text-white rounded-xl">Dashboard</a>
                <a href="kanban.php" class="px-4 py-2 hover:bg-white/5 rounded-xl transition">Kanban Space</a>
                <a href="settings.php" class="px-4 py-2 hover:bg-white/5 rounded-xl transition">Settings</a>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <a href="admin.php" class="px-4 py-2 text-rose-400 hover:bg-rose-500/10 rounded-xl transition">Admin Portal</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-right hidden sm:block">
                <div class="text-xs text-slate-400">Authenticated as</div>
                <div class="text-sm font-semibold text-slate-200"><?= htmlspecialchars($_SESSION['username']) ?> <span class="text-[10px] uppercase tracking-widest px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 ml-1"><?= $_SESSION['role'] ?></span></div>
            </div>
            <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" class="w-10 h-10 rounded-full border border-white/20 object-cover">
            <a href="logout.php" class="p-2 bg-slate-800 hover:bg-rose-950/40 hover:text-rose-400 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 mt-8">
        
        <?php if ($search !== ''): ?>
            <div class="mb-6 p-4 bg-indigo-500/10 border border-indigo-500/20 rounded-2xl text-sm">
                Search diagnostic active. Results rendered for query string: <span class="font-mono text-cyan-400"><?= $search ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl"></div>
                <div class="text-xs uppercase font-semibold text-slate-400 tracking-wider">Total Active Issues</div>
                <div class="text-4xl font-black text-white mt-2"><?= $total_bugs ?></div>
                <div class="text-xs text-slate-500 mt-1">Across all infrastructure frameworks</div>
            </div>
            <div class="glass-panel p-6 rounded-3xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-rose-500/5 rounded-full blur-xl"></div>
                <div class="text-xs uppercase font-semibold text-rose-400 tracking-wider">Elevated Threats</div>
                <div class="text-4xl font-black text-rose-400 mt-2"><?= $critical_count ?></div>
                <div class="text-xs text-slate-500 mt-1">High/Critical scope requirements</div>
            </div>
            <div class="glass-panel p-6 rounded-3xl">
                <form method="GET" action="dashboard.php" class="h-full flex flex-col justify-between">
                    <label class="text-xs uppercase font-semibold text-slate-400 tracking-wider">Audit Lookup (Reflected XSS Lab)</label>
                    <div class="flex items-center space-x-2 mt-2">
                        <input type="text" name="search" placeholder="Query injection payload..." value="<?= htmlspecialchars($search) ?>" class="w-full text-xs px-3 py-2 bg-slate-900/60 border border-slate-700/50 rounded-xl text-white focus:outline-none focus:border-indigo-500">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-semibold shadow">Query</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="text-lg font-bold text-slate-200">System Vulnerability Reports</h2>
                    <a href="kanban.php" class="text-xs text-indigo-400 hover:underline flex items-center space-x-1">
                        <span>Launch Kanban Workspace</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <?php if(empty($issues_res)): ?>
                    <div class="glass-panel p-12 rounded-3xl text-center text-slate-500 text-sm">No records matches telemetry index vectors.</div>
                <?php else: ?>
                    <?php foreach($issues_res as $issue): ?>
                        <div class="glass-panel p-5 rounded-2xl hover:border-slate-700/60 transition group relative">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-[10px] font-mono bg-slate-800 px-2 py-1 rounded text-slate-400">#ISSUE-00<?= $issue['id'] ?></span>
                                    <h3 class="text-base font-bold text-white mt-2 group-hover:text-indigo-400 transition">
                                        <a href="bug.php?id=<?= $issue['id'] ?>"><?= htmlspecialchars($issue['title']) ?></a>
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-1 line-clamp-2"><?= htmlspecialchars($issue['description']) ?></p>
                                </div>
                                <span class="text-xs px-2.5 py-1 rounded-full font-semibold 
                                    <?= $issue['severity'] === 'Critical' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' ?>
                                    <?= $issue['severity'] === 'High' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' ?>
                                    <?= $issue['severity'] === 'Medium' ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : '' ?>
                                    <?= $issue['severity'] === 'Low' ? 'bg-slate-500/10 text-slate-400 border border-slate-500/20' : '' ?>
                                "><?= $issue['severity'] ?></span>
                            </div>
                            <div class="mt-4 pt-4 border-t border-white/5 flex items-center justify-between text-xs text-slate-500">
                                <div>Assigned To: <span class="text-slate-300 font-medium"><?= htmlspecialchars($issue['assignee'] ?? 'Unassigned') ?></span></div>
                                <div class="flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full 
                                        <?= $issue['status'] === 'Completed' ? 'bg-emerald-400' : 'bg-amber-400' ?>
                                    "></span>
                                    <span><?= $issue['status'] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="space-y-6">
                <div class="glass-panel p-6 rounded-3xl">
                    <h3 class="text-sm font-bold text-slate-200 mb-4">Report New Platform Bug</h3>
                    <form method="POST" action="kanban.php?action=create" class="space-y-4">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Title</label>
                            <input type="text" name="title" required class="w-full px-3 py-2 text-xs bg-slate-900/60 border border-slate-700/50 rounded-xl text-white focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Detailed Log Summary</label>
                            <textarea name="description" rows="3" class="w-full px-3 py-2 text-xs bg-slate-900/60 border border-slate-700/50 rounded-xl text-white focus:outline-none focus:border-indigo-500"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Severity</label>
                                <select name="severity" class="w-full p-2 text-xs bg-slate-900 border border-slate-700/50 rounded-xl text-white">
                                    <option>Low</option><option selected>Medium</option><option>High</option><option>Critical</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Assignee ID</label>
                                <input type="number" name="assignee_id" value="2" class="w-full px-3 py-2 text-xs bg-slate-900/60 border border-slate-700/50 rounded-xl text-white">
                            </div>
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-500 text-xs font-semibold text-white rounded-xl transition">File System Ticket</button>
                    </form>
                </div>

                <div class="glass-panel p-6 rounded-3xl text-xs space-y-2">
                    <span class="font-bold text-amber-400 uppercase block mb-1">Security Lab Notes</span>
                    <p class="text-slate-400">1. Try injecting <code class="text-rose-400">&lt;script&gt;alert(document.cookie)&lt;/script&gt;</code> into the search bar to demonstrate Reflected XSS.</p>
                    <p class="text-slate-400">2. Select any report above to enter the issue viewer, which is highly vulnerable to SQL Union queries.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
