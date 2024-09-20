    <nav class="indigoTheme">
        <div id="left-section">
            <div id="logo-wrapper">
                <img src="icons/research-svgrepo-com.svg">
                <h1>Edu Portal</h1>
            </div>
            <?php if ($page == 'classTab' ) {
                echo '<div id="button-wrapper">
                      <button id="accountManagement-button" class="indigoTheme shadow"> 
                      <a href="accManagementPage.php"> Account Management </a>
                      </button>
                      <button id="classes-button" class="indigoTheme shadow active"> Classes </button>
                      </div>';
                } else if($page == "accountManagementTab"){
                echo '<div id="button-wrapper">
                      <button id="accountManagement-button" class="indigoTheme shadow active">                         Account Management                       </button>
                      <button id="classes-button" class="indigoTheme shadow">
                      <a href="classesPage.php"> Classes </a>
                      </button>
                      </div>';

                 } ?>
        </div>
        <div id="right-section">
            <button class=" indigoTheme roundBorder shadow"> Contact Us</button>
            <button class=" indigoTheme roundBorder shadow"> Terms & Conditions</button>
        </div>
    </nav>