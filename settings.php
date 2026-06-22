<?php
require_once 'config.php';
check_auth();

$status_msg = '';
$status_type = 'success';

// CRITICAL VULNERABILITY MECHANISM (CSRF):
// The form actions update authentication states directly based solely on active cookies without checking token patterns.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $new_pass = $_POST['new_password'];
    
    if(!empty($new_pass)) {
        $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute(['password' => $new_pass, 'id' => $_SESSION['user_id']]);
        $status_msg = "Account profile authorization parameters successfully modified.";
    } else {
        $status_msg = "Invalid password input parameters specified.";
        $status_type = 'error';
    }
}

// Fetch data context
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user_context = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Settings - GlassBug Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: radial-gradient(circle at top right, #0f172a, #020617); }
        .glass-panel { background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="min-h-screen text-slate-100">

    <div class="max-w-4xl mx-auto px-6 mt-12">
        <div class="mb-6"><a href="dashboard.php" class="text-xs text-indigo-400 hover:underline">← Back to Production Dashboard Matrix</a></div>

        <?php if($status_msg): ?>
            <div class="mb-6 p-4 rounded-xl text-xs border <?= $status_type === 'success' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border-rose-500/20' ?>"><?= $status_msg ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="glass-panel p-6 rounded-3xl text-center h-fit">
                <img src="<?= htmlspecialchars($user_context['avatar']) ?>" class="w-24 h-24 rounded-full border-2 border-indigo-500/40 mx-auto object-cover mb-4">
                <h2 class="text-lg font-bold text-white"><?= htmlspecialchars($user_context['username']) ?></h2>
                <span class="text-[10px] uppercase tracking-wider font-mono px-2 py-0.5 rounded bg-white/10 text-slate-300"><?= htmlspecialchars($user_context['role']) ?> account</span>
                <p class="text-xs text-slate-400 mt-4 italic">"<?= htmlspecialchars($user_context['bio'] ?? 'No biographical metadata initialized.') ?>"</p>
            </div>

            <div class="md:col-span-2 glass-panel p-8 rounded-3xl">
                <h3 class="text-base font-bold text-white mb-6">Modify Infrastructure Account Settings</h3>
                
                <form method="POST" action="settings.php" class="space-y-4">
                    <div>
                        <label class="block text-xs uppercase font-semibold text-slate-400 tracking-wider mb-2">Update Account Password</label>
                        <input type="text" name="new_password" placeholder="Input new access key string..." required class="w-full px-4 py-3 bg-slate-900 border border-slate-700/60 rounded-xl focus:outline-none focus:border-indigo-500 text-sm text-white">
                        <span class="text-[10px] text-slate-500 mt-1 block">Submitting this action requires no confirmation tokens, leaving it vulnerable to CSRF side-channel exploits.</span>
                    </div>
                    <button type="submit" class="px-5 py-3 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-semibold text-white transition">Update Profile Security Registry</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
