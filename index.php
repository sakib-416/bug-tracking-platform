<?php 
include 'config.php'; 

$search_query = "";
$bugs = [];

// সার্চ লজিক - ইচ্ছা করে রাখা SQL INJECTION ভালনারেবিলিটি
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    
    $sql = "SELECT * FROM faculty WHERE name = '" . $search_query . "'";
    $result = $conn->query($sql);
    
    if ($result) {
        while($row = $result->fetch_assoc()) { $bugs[] = $row; }
    } else {
        echo "<pre class='text-rose-500 bg-zinc-950 p-4 border border-zinc-900 rounded font-mono text-xs'>SQL Error: " . $conn->error . "</pre>";
    }
} else {
    $sql = "SELECT * FROM faculty";
    $result = $conn->query($sql);
    if ($result) {
        while($row = $result->fetch_assoc()) { $bugs[] = $row; }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HexaTrack — System Control</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; letter-spacing: -0.015em; background-color: #000000; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="text-[#A1A1AA] min-h-screen flex flex-col md:flex-row">

    <aside class="w-full md:w-64 border-b md:border-b-0 md:border-r border-zinc-900 p-6 flex flex-col justify-between bg-[#000000]">
        <div class="space-y-12">
            <div class="flex items-center gap-2.5 px-2">
                <div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>
                <span class="text-xs font-semibold tracking-[0.15em] text-white uppercase font-mono">HexaTrack</span>
            </div>
            <nav class="space-y-1">
                <a href="index.php" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium bg-zinc-900 text-white font-mono">
                    <span>// BOARD_VIEW</span>
                    <span class="text-[10px] bg-zinc-800 px-1.5 py-0.2 rounded text-zinc-400"><?php echo count($bugs); ?></span>
                </a>
                <a href="add-bug.php" class="w-full flex items-center justify-between px-3 py-2 rounded-lg text-xs font-medium text-zinc-500 hover:text-zinc-300 font-mono">
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
        <header class="border-b border-zinc-900 px-8 py-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-[#000000]">
            <h1 class="text-xs font-semibold tracking-wider text-zinc-400 uppercase font-mono">// WORKSPACE_ACTIVE</h1>
            
            <form method="GET" action="index.php" class="w-full sm:w-auto flex gap-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search bugs by exact title..." class="w-full sm:w-64 bg-[#050505] border border-zinc-900 rounded px-3 py-1.5 text-xs font-mono text-white focus:outline-none focus:border-zinc-700 placeholder-zinc-800 transition">
                <button type="submit" class="bg-zinc-100 hover:bg-white text-zinc-950 font-medium font-mono text-[11px] px-4 py-1.5 rounded transition uppercase tracking-wider">Find</button>
            </form>
        </header>

        <div class="p-8 max-w-7xl w-full mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                
                <div class="lg:col-span-3 bg-[#050505] border border-zinc-900 rounded-xl p-4 space-y-4">
                    <div class="flex items-center justify-between px-1 border-b border-zinc-900 pb-3">
                        <span class="text-xs font-medium text-zinc-300 tracking-tight">Active Bug Backlog</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php if (empty($bugs)): ?>
                            <div class="col-span-3 text-center py-12 text-zinc-600 text-xs font-mono border border-dashed border-zinc-900 rounded-lg">No active issues recorded</div>
                        <?php else: ?>
                            <?php foreach ($bugs as $bug): ?>
                                <div class="bg-[#000000] border border-zinc-900 p-4 rounded-xl space-y-4 hover:border-zinc-800 transition shadow-sm">
                                    <div class="space-y-1.5">
                                        <div class="flex items-start justify-between gap-2">
                                            <h4 class="text-xs font-medium text-zinc-200 tracking-tight leading-relaxed"><?php echo htmlspecialchars($bug['name']); ?></h4>
                                        </div>
                                        <p class="text-[11px] text-zinc-500 leading-relaxed"><?php echo htmlspecialchars($bug['designation']); ?></p>
                                    </div>
                                    <div class="flex items-center justify-between pt-1 border-t border-zinc-900/60">
                                        <span class="text-[9px] font-mono font-medium uppercase tracking-wider border px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-400 border-indigo-500/20"><?php echo htmlspecialchars($bug['image']); ?></span>
                                        <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-400 font-mono"><span class="w-1 h-1 rounded-full bg-emerald-400"></span> open</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
