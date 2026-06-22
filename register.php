<?php
require_once 'config.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if($username && $password) {
        try {
            $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'user')");
            $stmt->execute(['username' => $username, 'password' => $password]);
            header("Location: login.php");
            exit;
        } catch (Exception $e) {
            $msg = "Username already provisioned or validation failure.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>GlassBug - Join Ecosystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body{background: radial-gradient(circle at top left, #1e1b4b, #0f172a, #020617);}.glass{background:rgba(255,255,255,0.03);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.08);}</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-slate-100">
    <div class="glass w-full max-w-md p-8 rounded-3xl shadow-2xl">
        <h1 class="text-2xl font-bold mb-2">Create Identity</h1>
        <p class="text-slate-400 text-sm mb-6">Provision access to local vulnerability sandboxing dashboards.</p>
        <?php if($msg): ?><div class="mb-4 p-3 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-xl text-xs text-center"><?= $msg ?></div><?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold tracking-wider uppercase text-slate-400 mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-2 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:outline-none focus:border-indigo-500 text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold tracking-wider uppercase text-slate-400 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:outline-none focus:border-indigo-500 text-white">
            </div>
            <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl shadow-lg transition-all">Initialize Account</button>
        </form>
        <div class="mt-4 text-center text-xs text-slate-500"><a href="login.php" class="text-indigo-400 hover:underline">Return to Login Gateway</a></div>
    </div>
</body>
</html>
