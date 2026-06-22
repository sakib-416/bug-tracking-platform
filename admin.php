<?php
require_once 'config.php';
check_admin(); // Restricts view exclusively to users with 'admin' privileges in the active session context

$users = $db->query("SELECT id, username, role, bio FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Portal - GlassBug Framework</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: radial-gradient(circle at top left, #450a0a, #0f172a, #020617); }
        .glass-panel { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="min-h-screen text-slate-100 pb-12">

    <div class="max-w-5xl mx-auto px-6 mt-12">
        <div class="mb-6"><a href="dashboard.php" class="text-xs text-indigo-400 hover:underline">← Exit Admin Workspace Topology</a></div>
        
        <div class="glass-panel p-8 rounded-3xl mb-8 border-rose-500/20">
            <h1 class="text-2xl font-black text-rose-400 tracking-tight mb-2">Core System Administration Infrastructure</h1>
            <p class="text-xs text-slate-400">Sensitive domain scope mapping all registered credential states.</p>
        </div>

        <div class="glass-panel rounded-3xl overflow-hidden">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/5 uppercase tracking-wider text-slate-400 font-bold">
                        <th class="p-4">ID</th>
                        <th class="p-4">Username Profile Identity</th>
                        <th class="p-4">Assigned Access Authorization Level</th>
                        <th class="p-4">Profile Metadata Bio Registry</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-300">
                    <?php foreach($users as $u): ?>
                        <tr class="hover:bg-white/[0.01]">
                            <td class="p-4 font-mono text-indigo-400">#USR-00<?= $u['id'] ?></td>
                            <td class="p-4 font-bold text-white"><?= htmlspecialchars($u['username']) ?></td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded font-bold uppercase text-[9px] <?= $u['role'] === 'admin' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-slate-800 text-slate-400' ?>">
                                    <?= $u['role'] ?>
                                </span>
                            </td>
                            <td class="p-4 text-slate-400 max-w-xs truncate"><?= htmlspecialchars($u['bio'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
