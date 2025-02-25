<!-- NAVBAR MARKUP -->
<link rel="stylesheet" href="stylesheets/partials/navBar.css">

<nav class="indigoTheme <?php if ($page == 'authenticationPage') echo ' relative';
                        else echo ' absolute'; ?>">
    <div id="left-section">
        <div id="logo-wrapper">
            <img src="assets/research-svgrepo-com.svg">
            <h1 id="logo-title">EduPortal</h1>
        </div>
        <?php if ($page != 'authenticationPage'): ?>
            <div id="button-wrapper">
                <form action="/portal" method="GET">
                    <input type="hidden" id="page-input" name="page" value="account-management">
                    <button id="accountManagement-button" class="indigoTheme shadow <?php echo $page == "accountManagementPage" ? "active" : "" ?>" onclick="document.getElementById('page-input').value = 'account-management'" type="submit"> Account Management</button>
                    <?php if ($_SESSION['UserType'] == 'Admin'): ?>
                        <button id="adminDashboard-button" class="indigoTheme shadow <?php echo $page == "adminDashboardPage" ? "active" : "" ?>" onclick="document.getElementById('page-input').value = 'admin-dashboard'" type="submit"> Dashboard </button>
                    <?php else: ?>
                        <button id="classes-button" class="indigoTheme shadow <?php echo $page == "classMessagingPage" ? "active" : "" ?>" onclick="document.getElementById('page-input').value = 'class-messaging'" type="submit"> Classes </button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <div id="right-section">
        <button id="contact-button" class=" indigoTheme roundBorder shadow"> Contact Us</button>
        <button id="TOC-button" class=" indigoTheme roundBorder shadow"> Terms & Conditions</button>
    </div>
</nav>