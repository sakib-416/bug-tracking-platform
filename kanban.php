<?php
require_once 'config.php';
check_auth();

// Action Router for Kanban Modifications
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'] ?? 'Untitled Bug';
        $description = $_POST['description'] ?? '';
        $severity = $_POST['severity'] ?? 'Medium';
        $assignee_id = !empty($_POST['assignee_id']) ? intval($_POST['assignee_id']) : 1;
        
        $stmt = $db->prepare("INSERT INTO issues (title, description, severity, status, reporter_id, assignee_id) VALUES (:t, :d, :s, 'To Do', :r, :a)");
        $stmt->execute(['t' => $title, 'd' => $description, 's' => $severity, 'r' => $_SESSION['user_id'], 'a' => $assignee_id]);
        header("Location: kanban.php");
        exit;
    }
    
    if ($_GET['action'] === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Raw JSON entry mapping for Drag & Drop requests
        $input = json_get_contents();
        if(!$input) { $input = $_POST; }
        
        $issue_id = intval($input['issue_id'] ?? 0);
        $new_status = $input['status'] ?? 'To Do';
        
        if($issue_id > 0) {
            $stmt = $db->prepare("UPDATE issues SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
            $stmt->execute(['status' => $new_status, 'id' => $issue_id]);
            echo json_encode(['success' => true]);
            exit;
        }
        echo json_encode(['success' => false]);
        exit;
    }
}

function json_get_contents() {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true);
}

// Fetch Board Elements
$issues = $db->query("SELECT issues.*, users.username as assignee FROM issues LEFT JOIN users ON issues.assignee_id = users.id")->fetchAll(PDO::FETCH_ASSOC);
$columns = ['To Do' => [], 'In Progress' => [], 'Testing' => [], 'Completed' => []];
foreach ($issues as $issue) {
    if (array_key_exists($issue['status'], $columns)) {
        $columns[$issue['status']][] = $issue;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kanban Space - GlassBug Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: radial-gradient(circle at top right, #0f172a, #020617); }
        .glass-panel { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-nav { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .kanban-col { background: rgba(255, 255, 255, 0.01); border: 1px solid rgba(255, 255, 255, 0.03); backdrop-filter: blur(8px); }
        .drag-over { background: rgba(79, 70, 229, 0.05) !important; border-color: rgba(79, 70, 229, 0.3) !important; }
    </style>
</head>
<body class="min-h-screen text-slate-100">

    <nav class="glass-nav sticky top-0 z-50 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-8">
            <span class="text-xl font-bold tracking-tight bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">GlassBug Core</span>
            <div class="flex space-x-1 text-sm font-medium text-slate-300">
                <a href="dashboard.php" class="px-4 py-2 hover:bg-white/5 rounded-xl transition">Dashboard</a>
                <a href="kanban.php" class="px-4 py-2 bg-white/5 text-white rounded-xl">Kanban Space</a>
                <a href="settings.php" class="px-4 py-2 hover:bg-white/5 rounded-xl transition">Settings</a>
            </div>
        </div>
        <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" class="w-10 h-10 rounded-full border border-white/20 object-cover">
    </nav>

    <div class="max-w-7xl mx-auto px-6 mt-8">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight">Enterprise Bug Lifecycle</h1>
                <p class="text-slate-400 text-xs">Drag and drop cards across engineering verification scopes dynamically.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
            <?php foreach($columns as $col_title => $col_issues): ?>
                <div class="kanban-col rounded-2xl p-4 min-h-[500px] flex flex-col transition-all duration-200" 
                     data-status="<?= $col_title ?>" 
                     ondragover="allowDrop(event)" 
                     ondragenter="dragEnter(event)" 
                     ondragleave="dragLeave(event)" 
                     ondrop="dropCard(event)">
                    
                    <div class="flex items-center justify-between mb-4 px-1">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400"><?= $col_title ?></span>
                        <span class="text-[11px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-bold"><?= count($col_issues) ?></span>
                    </div>

                    <div class="space-y-3 flex-1">
                        <?php foreach($col_issues as $issue): ?>
                            <div class="glass-panel p-4 rounded-xl cursor-grab active:cursor-grabbing hover:border-indigo-500/40 transition duration-150" 
                                 id="card-<?= $issue['id'] ?>" 
                                 draggable="true" 
                                 ondragstart="dragCard(event)"
                                 data-id="<?= $issue['id'] ?>">
                                
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="text-[9px] font-mono bg-slate-800 px-1.5 py-0.5 rounded text-slate-400">#BUG-<?= $issue['id'] ?></span>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded font-bold
                                        <?= $issue['severity'] === 'Critical' ? 'bg-rose-500/20 text-rose-300' : '' ?>
                                        <?= $issue['severity'] === 'High' ? 'bg-amber-500/20 text-amber-300' : '' ?>
                                        <?= $issue['severity'] === 'Medium' ? 'bg-indigo-500/20 text-indigo-300' : '' ?>
                                        <?= $issue['severity'] === 'Low' ? 'bg-slate-500/20 text-slate-300' : '' ?>
                                    "><?= $issue['severity'] ?></span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-200 line-clamp-1"><a href="bug.php?id=<?= $issue['id'] ?>" class="hover:underline"><?= htmlspecialchars($issue['title']) ?></a></h4>
                                <div class="mt-3 pt-2 border-t border-white/5 flex items-center justify-between text-[10px] text-slate-400">
                                    <span class="truncate">👤 <?= htmlspecialchars($issue['assignee'] ?? 'Unassigned') ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function allowDrop(ev) {
            ev.preventDefault();
        }

        function dragCard(ev) {
            ev.dataTransfer.setData("text/plain", ev.target.id);
        }

        function dragEnter(ev) {
            let targetCol = ev.target.closest('.kanban-col');
            if (targetCol) targetCol.classList.add('drag-over');
        }

        function dragLeave(ev) {
            let targetCol = ev.target.closest('.kanban-col');
            if (targetCol) targetCol.classList.remove('drag-over');
        }

        function dropCard(ev) {
            ev.preventDefault();
            let targetCol = ev.target.closest('.kanban-col');
            if (!targetCol) return;
            
            targetCol.classList.remove('drag-over');
            
            let cardId = ev.dataTransfer.getData("text/plain");
            let cardEl = document.getElementById(cardId);
            if (!cardEl) return;

            let issueId = cardEl.getAttribute('data-id');
            let newStatus = targetCol.getAttribute('data-status');

            // Move the element visibly in the UI
            targetCol.querySelector('.space-y-3').appendChild(cardEl);

            // Send back updates to infrastructure asynchronously
            fetch('kanban.php?action=update_status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ issue_id: issueId, status: newStatus })
            })
            .then(res => res.json())
            .then(data => { if(!data.success) alert("Failed to save state change back to database."); });
        }
    </script>
</body>
</html>
