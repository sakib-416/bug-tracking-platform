<?php
require_once 'config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Intentionally vulnerable to bypass strings if extended, but implementing standard loose verification for role profiling
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute(['username' => $username, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['avatar'] = $user['avatar'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials provided.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GlassBug - Secure Login Space</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: radial-gradient(circle at top left, #1e1b4b, #0f172a, #020617);
        }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-slate-100">
    <div class="glass w-full max-w-md p-8 rounded-3xl shadow-2xl">
        <div class="text-center mb-8">
            <div class="inline-flex p-3 bg-indigo-600/20 text-indigo-400 rounded-2xl mb-3 border border-indigo-500/30">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">GlassBug</h1>
            <p class="text-sm text-slate-400 mt-1">Vulnerable Enterprise Threat Tracking Platform</p>
        </div>

        <?php if($error): ?>
            <div class="mb-4 p-3 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl text-sm text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:outline-none focus:border-indigo-500 text-white transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700/50 rounded-xl focus:outline-none focus:border-indigo-500 text-white transition-all">
            </div>
            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-500 active:scale-[0.98] transition-all text-white font-semibold rounded-xl shadow-lg shadow-indigo-600/30">Authenticate</button>
        </form>
        
        <div class="mt-6 text-center text-xs text-slate-500">
            Don't have an account? <a href="register.php" class="text-indigo-400 hover:underline">Register Hub</a>
        </div>
        <div class="mt-4 p-3 bg-amber-500/5 border border-amber-500/10 rounded-xl text-[11px] text-amber-400/80">
            <strong>Lab Quick-Credentials:</strong><br>
            • Admin: <code class="text-white">admin</code> / <code class="text-white">admin123</code><br>
            • Attacker: <code class="text-white">attacker</code> / <code class="text-white">attacker</code>
        </div>
    </div>
</body>
</html>
