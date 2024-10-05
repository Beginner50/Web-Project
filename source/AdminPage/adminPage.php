<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">


<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


  <link rel="stylesheet" href="../stylesheets/common.css">
  <link rel="stylesheet" href="../stylesheets/adminPage/adminPage.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="../stylesheets/accountManagementPage/Acc_management.css">
  <title>Admin Page</title>
</head>

<body>
  <div class="main-content">

    <!-- DISPLAYING STATISTICS -->
    <div class="school-stats">
      <?php

      require_once '../connect.php';
      $stmt = $pdo->prepare('SELECT COUNT(StudentID) from student');
      $stmt->execute();
      $_SESSION['StudentCount'] = $stmt->fetch(PDO::FETCH_NUM)[0];

      $stmt = $pdo->prepare('SELECT COUNT(TeacherID) from teacher');
      $stmt->execute();
      $_SESSION['TeacherCount'] = $stmt->fetch(PDO::FETCH_NUM)[0];

      $stmt = $pdo->prepare('SELECT COUNT(UserID) FROM approval WHERE IsApproved=0');
      $stmt->execute();
      $_SESSION['UnapprovedCount'] = $stmt->fetch(PDO::FETCH_NUM)[0];

      $stmt = $pdo->prepare('SELECT COUNT(UserID) from user');
      $stmt->execute();
      $_SESSION['UserCount'] = $stmt->fetch(PDO::FETCH_NUM)[0];
      ?>

      <div class="stats-container total-unapproved">
        <i class="fa-regular fa-circle-xmark fa-2xl" style="color:#f25356;"></i>
        <div>
          <div><?php echo $_SESSION['UnapprovedCount'] ?></div>
          <div class="stats-information" style="font-size:18px;">Total Unapproved Users</div>
        </div>
      </div>

      <div class="stats-container total-students">
        <i class="fa-solid fa-graduation-cap fa-2xl" style="color:#7bc7ed;"></i>
        <div>
          <div><?php echo $_SESSION['StudentCount'] ?></div>
          <div class="stats-information" style="font-size:20px;">Total Students</div>
        </div>

      </div>

      <div class="stats-container total-teachers">
        <i class="fa-solid fa-chalkboard-user fa-2xl" style="color:#ecdd70;"></i>
        <div>
          <div><?php echo $_SESSION['TeacherCount'] ?></div>
          <div class="stats-information" style="font-size:20px;">Total Teachers</div>
        </div>
      </div>

      <div class="stats-container total-staffs">
        <i class="fa-regular fa-user fa-2xl" style="color:#70ecb2;"></i>
        <div>
          <div><?php echo $_SESSION['UserCount'] ?></div>
          <div class="stats-information" style="font-size:20px;">Total Users</div>
        </div>
      </div>

    </div>
    <!-- DISPLAYING USER CLICKED INFORMATION -->
    <div class="user-information">

      <div class="search-box"></div>
      <div class="user-list">

        <button class="backButton indigoTheme roundBorder "> Back</button>

        <div class="userinfo-container alter-account">

          <button class="userinfo-button resetPass indigoTheme roundBorder" onclick="redirectToresetPass()">Reset Password</button>
          <button class="userinfo-button verifyAcc indigoTheme roundBorder" onclick="redirectToverifyAcc()">Verify Account</button>
          <button class="userinfo-button deleteAcc indigoTheme roundBorder">Delete Account</button>

          <div class="buttoninfo">

            <div class="information">
              <div class="information-input">Reset the password to default for this user. Default Password: $1lent.k</div>
            </div>

            <div class="information" style="background-color: #70ecb2; ">
              <div class="information-input">Verify this user as a teacher or an admin.</div>
            </div>

            <div class="information" style="background-color: palevioletred; ">
              <div class="information-input">Delete the account of this user</div>
            </div>

          </div>

          <?php

          if (isset($_SESSION['PassChange'])) {

            echo '<div class="success-container" >' . $_SESSION['PassChange'] . '</div>';
            unset($_SESSION['PassChange']);
          }


          if (isset($_SESSION['verifyAccStatus'])) {

            echo '<div class="success-container" >' . $_SESSION['verifyAccStatus'] . '</div>';
            unset($_SESSION['verifyAccStatus']);
          }

          ?>

        </div>
        <!-- DISPLAYING USER SUBJECTS -->
        <div class="userinfo-container ">
          <form class="update-subjects" id="subjectchange-admin" action="AdminPage/subjectChange.php" method="POST">
            <div class="information">
              <label class="sub-information">Level</label>
              <input class="information-input" type="text" id="level" name="level" value="<?php echo $_SESSION['Level']; ?>">
            </div>

            <div class="information">
              <div class="sub-information">Class Group</div>
              <input class="information-input" type="text" id="classgroup" name="classgroup" value="<?php echo $_SESSION['ClassGroup']; ?>">
            </div>

            <div class="information studentinfo-grid">
              <div class="sub-information">Subjects Taken</div>

              <div class="subjectstaken">
                <?php
                foreach ($_SESSION['Subjects'] as $Subjects) {
                  echo '<div class="subject-item">';


                  echo '<input class="subject-code" type="text" name= "' . $Subjects['SubjectCode'] . '" id="' . $Subjects['SubjectCode'] . '" value="' . $Subjects['SubjectCode'] . '">';
                  echo '<input class="subject-name" type="text" name="' . $Subjects['Subjectname'] . '" id="' . $Subjects['Subjectname'] . '" value="' . $Subjects['Subjectname'] . '">';
                }
                ?>
                <form id="subjectDeleteForm" action="subjectDelete.php" method="POST">

                  <input type="hidden" name="delete-subjectCode" value="<?php echo $Subjects['SubjectCode']; ?>">
                  <div><?php echo $Subjects['SubjectCode']; ?></div>
                  <button type="submit" name="deleteButton" class="remove-subject indigoTheme roundBorder" form="subjectDeleteForm"> Delete </button>

                </form>
                <?php
                echo '</div>';


                if (isset($_SESSION['subjectDeletStatus'])) {
                  if (isset($_SESSION['subjectDeletStatus'])) {

                    echo '<div class="">' . $_SESSION['subjectDeletStatus'] . ' </div>';
                    echo '<div class="">' . $_SESSION['subjectDeletStatus'] . ' </div>';
                    unset($_SESSION['subjectDeletStatus']);
                  }
                }
                ?>


              </div>

            </div>
          </form>
          </form>
        </div>
        <!-- DISPLAYING GENERAL INFORMATION-->
        <div class="userinfo-container">
          <form id="admin-update-personalinfo" class="update-personalinfo" method="POST" action="../AccountManagement/personalinfo.php">

            <div class="information">
              <div class="sub-information">UserID</div>
              <div class="information-input"> <?php echo $_SESSION['UserID-Clicked']; ?> </div>
            </div>

            <div class="information">
              <label class="sub-information">First Name: </label>
              <input class="information-input" type="text" id="firstname" name="firstname" value="<?php echo $_SESSION['FirstName']; ?>">
            </div>

            <div class="information">
              <label class="sub-information" for="lastname">Last Name: </label>
              <input class="information-input" type="text" id="lastname" name="lastname" value="<?php echo $_SESSION['LastName']; ?>">
            </div>

            <div class="information">
              <label class="sub-information" for="gender">Gender: </label>
              <input class="information-input" type="text" id="gender" name="gender" value="<?php echo $_SESSION['Gender']; ?>"> <!-- must do a dropdown menu like in register -->

            </div>

            <div class="information">
              <label class="sub-information" for="dateofbirth">Date Of Birth: </label>
              <input class="information-input" type="date" id="dateofbirth" name="dateofbirth" value="<?php echo date('Y-m-d', strtotime($_SESSION['DateOfBirth'])); ?>"> <!-- Formats date in proper format -->

            </div>

            <div class="information personalinfo-email">
              <label class="sub-information" for="email">Email: </label>
              <input class="information-input" type="email" id="email" name="email" value="<?php echo $_SESSION['Email']; ?>" style="width:300px;">
            </div>


            <button type="submit" id="personalinfo-savechanges-admin" name="personalinfo-savechanges-admin" form="admin-update-personalinfo" class="indigoTheme roundBorder" style="width:150px; height:50px; margin:10px;border-width:2px;"> Save</button>


          </form>
          <?php

          // Check if there are any errors in the session
          if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {

            foreach ($_SESSION['errors'] as $error) {
              echo '<div class="error-container">' . $error . '</div>'; // Display each error
            }

            // Unset the errors after displaying them
            unset($_SESSION['errors']);
          }

          if (isset($_SESSION['Success'])) {

            echo '<div class="success-container" >' . $_SESSION['Success'] . '</div>';
            unset($_SESSION['Success']);
          }
          ?>
        </div>
      </div>
    </div>

  </div>

  <script src="../scripts/adminPage.js"></script>
</body>

</html>