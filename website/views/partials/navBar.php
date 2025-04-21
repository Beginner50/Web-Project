<!-- NAVBAR MARKUP -->
<link rel="stylesheet" href="stylesheets/partials/navBar.css">

<nav class="indigoTheme <?php if ($page == 'authentication' || $page == 'messaging') echo ' relative';
                        else echo ' absolute'; ?>">
    <div id="left-section">
        <div id="logo-wrapper">
            <img src="assets/research-svgrepo-com.svg">
            <h1 id="logo-title">EduPortal</h1>
        </div>
        <?php if ($page != 'authentication'): ?>
            <div id="button-wrapper">
                <button id="accountManagement-button" class="indigoTheme shadow <?php echo $page == "account" ? "active" : "" ?>" onclick="window.location.href = '/account/<?php echo strtolower($_SESSION['UserType']) ?>/<?php echo $_SESSION['UserID'] ?>';"> Account Management</button>
                <?php if ($_SESSION['UserType'] == 'Admin'): ?>
                    <button id="adminDashboard-button" class="indigoTheme shadow <?php echo $page == "dashboard" ? "active" : "" ?>" onclick="window.location.href = '/dashboard';"> Dashboard </button>
                <?php else: ?>
                    <button id="classes-button" class="indigoTheme shadow <?php echo $page == "messaging" ? "active" : "" ?>" onclick="window.location.href = '/messaging';"> Classes </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <div id="right-section">
        <button id="contact-button" class=" indigoTheme roundBorder shadow"> Contact Us</button>
        <button id="TOC-button" class=" indigoTheme roundBorder shadow"> Terms & Conditions</button>
    </div>
</nav>