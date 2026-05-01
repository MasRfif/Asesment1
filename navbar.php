<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<nav class="navbar">
    <a href="dashboard.php" class="navbar-brand">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/></svg>
        SmartTraffic Cam
    </a>
    <ul class="navbar-nav">
        <?php if (isset($_SESSION['user_id'])): ?>
        <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="monitoring.php" class="<?= basename($_SERVER['PHP_SELF']) === 'monitoring.php' ? 'active' : '' ?>">Monitoring</a></li>
        <li><span class="navbar-user"><?= htmlspecialchars($_SESSION['nama']) ?></span></li>
        <li><a href="login.php?logout=1" class="navbar-logout">Logout</a></li>
        <?php else: ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
z