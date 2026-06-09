<?php 
include 'config.php'; 

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $image = $_POST['image'];

    $stmt = $conn->prepare("INSERT INTO faculty (name, designation, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $designation, $image);
    
    if ($stmt->execute()) { $msg = "BUG_LOGGED_SUCCESSFULLY"; }
    else { $msg = "PIPELINE_ERROR"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Bug — HexaTrack</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; letter-spacing: -0.015em; background-color: #000000; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="text-[#A1A1AA] min-h-screen flex flex-col md:flex-row">

    <aside class="w-full md:w-64 border-b md:border-b-0 md:border-r border-zinc-900 p-6 flex flex-col justify-between bg-[#000000]">
        <div class="space-y-12">
            <div class="flex items-center gap-2.5 px-2">
                <div class="w-2 h-2 rounded-full bg-white"></div>
                <span class="text-xs font-semibold tracking-[0.15em] text-white uppercase font-mono">HexaTrack</span>
            </div>
            <nav class="space-y-1">
                <a href="index.php" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-300 font-mono">
                    <span>// BOARD_VIEW</span>
                </a>
                <a href="add-bug.php" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium bg-zinc-900 text-white font-mono">
                    <span>+ LOG_NEW_BUG</span>
                </a>
            </nav>
        </div>
        <div class="px-2 mt-8 md:mt-0 space-y-1">
            <p class="text-[10px] font-mono text-zinc-600 tracking-wider uppercase">DB // SQLI</p>
            <p class="text-[9px] font-mono text-zinc-700">USER: SAKIB</p>
        </div>
    </aside>

    <main class="flex-1 flex flex-col bg-[#000000]">
        <header class="border-b border-zinc-900 px-8 py-5 bg-[#000000]">
            <h1 class="text-xs font-semibold tracking-wider text-zinc-400 uppercase font-mono">// COMMIT_NEW_BUG</h1>
        </header>

        <div class="p-8 max-w-xl w-full mx-auto">
            <?php if ($msg): ?>
                <div class="mb-6 text-xs font-mono p-3 bg-zinc-900 border border-zinc-800 text-white rounded"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div class="space-y-2">
                    <label class="block text-[10px] font-medium font-mono uppercase tracking-wider text-zinc-400">Bug Title / Heading</label>
                    <input type="text" name="name" required placeholder="e.g., Auth token expires instantly on reload" class="w-full bg-[#050505] border border-zinc-900 rounded-lg px-4 py-3 text-xs font-mono text-white focus:outline-none focus:border-zinc-500 transition">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-medium font-mono uppercase tracking-wider text-zinc-400">Detailed Description</label>
                    <textarea name="designation" rows="4" required placeholder="Describe steps to recreate the issue..." class="w-full bg-[#050505] border border-zinc-900 rounded-lg px-4 py-3 text-xs font-mono text-white focus:outline-none focus:border-zinc-500 transition"></textarea>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-medium font-mono uppercase tracking-wider text-zinc-400">Subsystem Module</label>
                    <select name="image" class="w-full bg-[#050505] border border-zinc-900 rounded-lg px-4 py-3 text-xs font-mono text-zinc-300 focus:outline-none focus:border-zinc-500 transition">
                        <option value="Frontend UI">Frontend UI</option>
                        <option value="Backend API">Backend API</option>
                        <option value="Database Cluster">Database Cluster</option>
                        <option value="Security Architecture">Security Architecture</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-white hover:bg-zinc-200 text-black font-semibold font-mono py-3 px-4 rounded text-xs tracking-widest uppercase transition">EXECUTE_COMMIT_PIPELINE</button>
            </form>
        </div>
    </main>

</body>
</html>
