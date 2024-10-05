<link rel="stylesheet" href="stylesheets/partials/navBar.css">
<nav class="indigoTheme <?php if ($page == 'authenticationPage') echo ' relative';
                        else echo ' absolute'; ?>">
  <div id="left-section">
    <div id="logo-wrapper">
      <img src="icons/research-svgrepo-com.svg">
      <h1 id="logo-title">EduPortal</h1>
    </div>
    <?php
    session_start();

    if ($page != 'authenticationPage') {
      echo '<script>
                      function switchClass() {
                        window.location.replace("classMessagingPage.php");
                      }

                      function switchAccountManagement() {
                        window.location.replace("accManagementPage.php");
                      }

                      function switchDashboard(){
                        window.location.replace();
                      }
                      </script>';

      if ($page == 'classTab') {
        echo '<div id="button-wrapper">
                      <button id="accountManagement-button" class="indigoTheme shadow" onclick="switchAccountManagement()">                         Account Management</button>
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
                      <button id="dashboard-button" class="indigoTheme shadow" onclick="switchDashboard()"> Dashboard </button>
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