<!-- NAVBAR FUNCTIONS -->
<?php

/**
 * generateTabButtons
 * It generates a specific version of the tab buttons based on the tab currently active
 * alongside the user type.
 * 
 * For example:
 * When an admin visits the accManagmentTab page, the buttons will appear differently
 * for the same page if a student or teacher were to visit it.
 *
 * @param  string $currentPage
 * @return void
 */
function generateTabButtons($currentPage)
{
    /* The function ensures that the buttons visible on the navbar are different for different users and
       across different web pages (tabs) */
    switch ($currentPage) {
        case 'classTab':
            // Class Tab
            echo '<div id="button-wrapper">
                  <button id="accountManagement-button" class="indigoTheme shadow" onclick=switchAccountManagement("classTab")>                         Account Management</button>
                  <button id="classes-button" class="indigoTheme shadow active"> Classes </button>
              </div>';
            break;
        case 'accountManagementTab':
            // Account Management Tab (Non-Admin)
            if ($_SESSION['UserType'] != 'Admin')
                echo '<div id="button-wrapper">
                   <button id="accountManagement-button" class="indigoTheme shadow active">                         Account Management                       </button>
                   <button id="classes-button" class="indigoTheme shadow" onclick="switchClass()">Classes</button>
              </div>';
            // Account Management Tab (Admin)
            else
                echo '<div id="button-wrapper">
                   <button id="accountManagement-button" class="indigoTheme shadow active">                         Account Management                       </button>
                   <button id="dashboard-button" class="indigoTheme shadow" onclick="switchDashboard()">Dashboard</button>
              </div>';
            break;
        case "dashboardTab":
            // Dashboard Tab
            echo '<div id="button-wrapper">
                   <button id="accountManagement-button" class="indigoTheme shadow" onclick=' . 'switchAccountManagement("dashboardTab")' . '>                         Account Management                       </button>
                   <button id="dashboard-button" class="indigoTheme shadow active"> Dashboard</button>
              </div>';
            break;
    }
}

/**
 * addTabButtonFunctionality
 * It simply creates the javascript needed to add functionality to the above
 * tab buttons
 *
 * @param  mixed $currentPage
 * @return void
 */
function addTabButtonFunctionality($currentPage)
{
    if ($currentPage != 'authenticationPage')
        echo '<script>
                       function switchClass() {
                            window.location.replace("classMessagingPage.php");
                        }

                        function switchAccountManagement(currentTab) {
                           if (currentTab == "dashboardTab")
                              window.location.replace("../accManagementPage.php");
                           else if (currentTab == "classTab")
                              window.location.replace("accManagementPage.php");
                        }

                        function switchDashboard() {
                            window.location.replace("AdminPage/adminPage.php");
                        }
              </script>';
}
?>

<!-- NAVBAR MARKUP -->
<nav class="indigoTheme <?php if ($page == 'authenticationPage') echo ' relative';
                        else echo ' absolute'; ?>">
    <div id="left-section">
        <div id="logo-wrapper">
            <img src="<?php echo ($page != 'dashboardTab')
                            ? 'assets/research-svgrepo-com.svg'
                            : '../assets/research-svgrepo-com.svg'; ?>"> 
            <h1 id="logo-title">EduPortal</h1>
        </div>
        <?php generateTabButtons($page); ?>
    </div>
    <div id="right-section">
        <button id="contact-button" class=" indigoTheme roundBorder shadow"> Contact Us</button>
        <button id="TOC-button" class=" indigoTheme roundBorder shadow"> Terms & Conditions</button>
    </div>
    <?php addTabButtonFunctionality($page); ?>
</nav>