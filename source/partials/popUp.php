<aside id="popUp-window" style="display:none;">
    <div id="popUp-wrapper">
        <div id="popUp-menu" class="shadow">
            <span class="popUp indigoTheme" style="border: none; padding-bottom: 10px;">
                <?php echo ($page == 'authenticationPage') ? 'Select a subject:' : 'Members:' ?>
            </span>
            <?php if ($page == 'authenticationPage')
                include 'Authentication/getSubjects.php';
            ?>
        </div>
    </div>
</aside>