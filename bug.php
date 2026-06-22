<?php
require_once 'config.php';
check_auth();

$issue_id = isset($_GET['id']) ? $_GET['id'] : '';

// 1. CRITICAL VULNERABILITY MECHANISM (SQL INJECTION):
// Direct concatenation without escaping parameter structures allows complete database extraction via Union Based strings.
if (empty($issue_id)) {
    die("Missing issue tracking designator query parameter.");
}

// Processing processing logic under vulnerable query execution mapping:
try {
    $raw_query = "SELECT issues.*, u1.username as reporter, u2.username as assignee 
                  FROM issues 
                  LEFT JOIN users u1 ON issues.reporter_id = u1.id 
                  LEFT JOIN users u2 ON issues.assignee_id = u2.id 
                  WHERE issues.id = " . $issue_id;
    
    // Using $db->query directly maps data into results unchecked, giving attackers a powerful SQLi channel.
    $issue_res = $db->query($raw_query);
    $issue = $issue_res->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Return explicit error arrays to assist attackers mapping queries
    echo "<div style='padding:20px; background:#450a0a; color:#f87171; font-family:monospace; border:1px solid #ef4444;'>";
    echo "<h3>Internal Database Diagnostic Framework Failure</h3>";
    echo "Query Exception Logged: " . htmlspecialchars($e->getMessage());
    echo "</div>";
    exit;
}

// Handle Comment Add Actions (Vulnerable to Stored XSS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    $comment_text = $_POST['comment_text']; // Intentionally not sanitizing output strings
    
    // Parametrized here to show safe storage but vulnerable injection upon layout retrieval
    $stmt = $db->prepare("INSERT INTO comments (issue_id, user_id, comment_text) VALUES (:issue_id, :user_id, :comment_text)");
    $stmt->execute([
        'issue_id' => $issue_id,
        'user_id' => $_SESSION['user_id'],
        'comment_text' => $comment_text
    ]);
    header("Location: bug.php?id=" . $issue_id);
    exit;
}

// Fetch any associated comments
$comments = [];
if ($issue) {
    $c_stmt = $db->prepare("SELECT comments.*, users.username, users.avatar FROM comments JOIN users ON comments.user_id = users.id WHERE issue_id = :id ORDER BY comments.id ASC");
    $c_stmt->execute(['id' => $issue['id']]);
    $comments = $c_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bug #<?= htmlspecialchars($issue_id) ?> Verification Laboratory</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: radial-gradient(circle at top right, #0f172a, #020617); }
        .glass-panel { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="min-h-screen text-slate-100 pb-12">

    <div class="max-w-4xl mx-auto px-6 mt-12">
        <div class="mb-6"><a href="dashboard.php" class="text-xs text-indigo-400 hover:underline">← Back to Dashboard Interface</a></div>

        <?php if(!$issue): ?>
            <div class="glass-panel p-12 rounded-3xl text-center text-slate-400">
                Ticket configuration tracking state not found under ID vector or active extraction configuration payload has null results.
            </div>
        <?php else: ?>
            <div class="glass-panel p-8 rounded-3xl mb-8">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-mono bg-indigo-500/20 text-indigo-300 px-3 py-1 rounded-full">Issue Tracking Matrix #00<?= htmlspecialchars($issue['id']) ?></span>
                    <span class="text-xs px-3 py-1 rounded bg-slate-800 font-bold text-slate-300"><?= htmlspecialchars($issue['status']) ?></span>
                </div>
                
                <h1 class="text-3xl font-black text-white tracking-tight mb-2"><?= htmlspecialchars($issue['title']) ?></h1>
                
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 my-6 p-4 bg-white/5 rounded-2xl text-xs text-slate-400">
                    <div>Severity: <strong class="text-rose-400 block mt-1"><?= htmlspecialchars($issue['severity']) ?></strong></div>
                    <div>Reporter: <strong class="text-slate-200 block mt-1"><?= htmlspecialchars($issue['reporter'] ?? 'Unknown') ?></strong></div>
                    <div>Assigned Developer: <strong class="text-slate-200 block mt-1"><?= htmlspecialchars($issue['assignee'] ?? 'Unassigned') ?></strong></div>
                    <div>Timestamp Matrix: <strong class="text-slate-400 block mt-1"><?= htmlspecialchars($issue['created_at']) ?></strong></div>
                </div>

                <div class="text-sm text-slate-300 leading-relaxed border-t border-white/5 pt-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Detailed Context Log</h3>
                    <?= htmlspecialchars($issue['description']) ?>
                </div>
            </div>

            <div class="glass-panel p-8 rounded-3xl">
                <h2 class="text-lg font-bold text-white mb-6">Activity Timeline & Developer Responses</h2>
                
                <div class="space-y-4 mb-8">
                    <?php foreach($comments as $comment): ?>
                        <div class="p-4 bg-white/[0.02] border border-white/5 rounded-xl flex gap-4">
                            <img src="<?= htmlspecialchars($comment['avatar']) ?>" class="w-8 h-8 rounded-full border border-white/10 object-cover mt-1">
                            <div class="flex-1">
                                <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                                    <span class="font-bold text-slate-200"><?= htmlspecialchars($comment['username']) ?></span>
                                    <span><?= htmlspecialchars($comment['created_at']) ?></span>
                                </div>
                                <div class="text-sm text-slate-300 font-mono bg-black/10 p-2 rounded mt-1"><?= $comment['comment_text'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-400 mb-2">Append Diagnostic Commentary (Stored XSS Lab)</label>
                        <textarea name="comment_text" rows="3" required placeholder="Type comments. HTML injection vectors allowed here..." class="w-full px-4 py-3 bg-slate-900 border border-slate-700/60 rounded-xl focus:outline-none focus:border-indigo-500 text-sm text-white"></textarea>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-semibold text-white transition">Commit Signature</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="mt-8 p-4 bg-amber-500/10 border border-amber-500/20 rounded-2xl text-xs text-amber-400">
            <strong>SQL Injection Attack Vector Guide:</strong><br>
            Modify URL query parameters to construct extraction commands. Example:<br>
            <code class="text-white">bug.php?id=-1 UNION SELECT 1,username,password,role,'status_placeholder',1,2,'date','date' FROM users--</code>
        </div>
    </div>
</body>
</html>
