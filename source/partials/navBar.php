<nav class="indigoTheme <?php if ($page == 'authenticationPage') echo ' relative';
                        else echo ' absolute'; ?>">
  <div id="left-section">
    <div id="logo-wrapper">
      <img src="<?php echo !isset($_SESSION['UserType']) || $_SESSION['UserType'] != 'Admin' ? 'icons/research-svgrepo-com.svg' : '../icons/research-svgrepo-com.svg'; ?>">
      <h1 id="logo-title">EduPortal</h1>
    </div>
    <?php
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
    error_reporting(-1);

    if ($page != 'authenticationPage') {
      echo '<script>
                      function switchClass() {
                        window.location.replace("classMessagingPage.php");
                      }

                      function switchAccountManagement(currentTab) {
                        if(currentTab == "dashboardTab")
                          window.location.replace("../accManagementPage.php");
                        else if(currentTab == "classTab")
                          window.location.replace("accManagementPage.php");
                      }

                      function switchDashboard(){
                        window.location.replace("AdminPage/adminPage.php");
                      }
                      </script>';

      if ($page == 'classTab') {
        echo '<div id="button-wrapper">
                      <button id="accountManagement-button" class="indigoTheme shadow" onclick=switchAccountManagement("classTab")>                         Account Management</button>
                      <button id="classes-button" class="indigoTheme shadow active"> Classes </button>
              </div>';
      } else if ($page == "accountManagementTab" && $_SESSION['UserType'] != 'Admin') {
        echo '<div id="button-wrapper">
                      <button id="accountManagement-button" class="indigoTheme shadow active">                         Account Management                       </button>
                      <button id="classes-button" class="indigoTheme shadow" onclick="switchClass()">Classes</button>
                      </div>';
      } else if ($page == "accountManagementTab" && $_SESSION['UserType'] == 'Admin') {
        echo '<div id="button-wrapper">
                      <button id="accountManagement-button" class="indigoTheme shadow active">                         Account Management                       </button>
                      <button id="dashboard-button" class="indigoTheme shadow" onclick="switchDashboard()">Dashboard</button>
                      </div>';
      } else if ($page == "dashboardTab" && $_SESSION['UserType'] == 'Admin') {
        echo '<div id="button-wrapper">
                      <button id="accountManagement-button" class="indigoTheme shadow" onclick=' . 'switchAccountManagement("dashboardTab")' . '>                         Account Management                       </button>
                      <button id="dashboard-button" class="indigoTheme shadow active"> Dashboard</button>
                      </div>';
      }
    }
    ?>
  </div>
  <div id="right-section">
    <button id="contact-button" class=" indigoTheme roundBorder shadow"> Contact Us</button>
    <button id="TOC-button" class=" indigoTheme roundBorder shadow"> Terms & Conditions</button>
  </div>
</nav>