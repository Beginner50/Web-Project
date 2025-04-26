<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <base href="/website/">
  <link rel="stylesheet" href="stylesheets/common.css">
  <link rel="stylesheet" href="stylesheets/adminDashboard/adminPage.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="stylesheets/accountManagement/Acc_management.css">
  <link rel="stylesheet" href="stylesheets/partials/navBar.css">
  <title>Admin Page</title>
</head>

<?php include 'views/partials/navBar.php'; ?>

<body>
  <div class="main-content">
    <!-- DISPLAYING STATISTICS -->
    <div class="school-stats">
      <?php
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

    <!-- DISPLAYING USER TABLE -->
    <div class="user-information">
      <div class="search-box"></div>
      <div class="user-list-container">
        <table class="user-list">
          <tr>
            <th>ID</th>
            <th>UserType</th>
            <th>Name</th>
            <th>Authorisation</th>
          </tr>
          <?php
          foreach ($users as $user) {
            echo '<tr class="user-row" style="cursor:pointer;" data-user-id="' . $user['UserID'] . '" data-user-type="' . $user['UserType'] . '">' .
              '<td>' . $user['UserID'] . '</td>' .
              '<td>' . $user['UserType'] . '</td>' .
              '<td>' . $user['FirstName'] . " " . $user['LastName'] . '</td>' .
              '<td>' . (isset($user['IsApproved']) && $user['IsApproved'] ? 'Approved' : 'Pending') . '</td>' .
              '</tr>';
          }
          ?>
        </table>

        <!-- Dynamic Details Displayed via JSON -->
        <div class="user-details userinfo-container" style="display:none; margin-top:20px;">

          <div class="userinfo-container alter-account">

            <button class="userinfo-button resetPass indigoTheme roundBorder" onclick="redirectToresetPass()">Reset Password</button>
            <button class="userinfo-button verifyAcc indigoTheme roundBorder" onclick="redirectToverifyAcc()">Verify Account</button>
            <button class="userinfo-button deleteAcc indigoTheme roundBorder"  onclick="redirectToDeleteAcc()">Delete Account</button>

            <div class="buttoninfo">
              <div class="information">
                <div class="information-input">Reset the password to default for this user. Default Password: pass1234</div>
              </div>
              <div class="information" style="background-color: #70ecb2;">
                <div class="information-input">Verify this user as a teacher or an admin.</div>
              </div>
              <div class="information" style="background-color: palevioletred;">
                <div class="information-input">Delete the account of this user</div>
              </div>
            </div>

          </div>


          <div class="userinfo-container">
           <form id="user-specific-detail-form" class="update-subjects student-extra " style="display:none;" >
 
            <div class="information">
              <label class="sub-information">Level</label>
              <input class="information-input" type="text" id="json-level" name="level" />
            </div>
            <div class="information">
              <div class="sub-information">Class Group</div>
              <input class="information-input"type="text" id="json-classgroup" name="classgroup" />
            </div>
            <div class="information">
              <div class="sub-information">Subjects Taken</div>
              <div  class="subjectstaken" id="json-subjects-container"></div>
            </div>
            
            </form>

          </div>
        
          <div class="userinfo-container">
           <form id="personalinfo-savechanges-admin" class="update-personalinfo ">

            <div class="information">
              <div class="sub-information">UserID</div>
              <input type="text" class="information-input" id="json-userid" name="userID" readonly />
            </div>

            <div class="information">
              <label class="sub-information">User Type: </label>
              <input type="text" class="information-input" id="json-usertype" name="user-type" readonly />
            </div>

            <div class="information">
              <label class="sub-information">First Name: </label>
              <input type="text" class="information-input" id="json-firstname" name="firstname" />
            </div>

            <div class="information">
              <label class="sub-information">Last Name: </label>
              <input type="text" class="information-input" id="json-lastname" name="lastname" />
            </div>

            <div class="information">
              <label class="sub-information">Email: </label>
              <input type="email" class="information-input" id="json-email" name="email" />
            </div>

            <div class="information">
              <label class="sub-information">Gender: </label>
              <input type="text" class="information-input" id="json-gender" name="gender" />
            </div>

            <div class="information">
              <label class="sub-information">Date of Birth: </label>
              <input type="date" class="information-input" id="json-dob" name="dateofbirth" />
            </div>

            <button type="submit" id="personalinfo-savechanges-admin1" name="personalinfo-savechanges-admin1" form="personalinfo-savechanges-admin" class="indigoTheme roundBorder" style="width:150px; height:50px; margin:10px;border-width:2px;"> Save</button>
            </form>
          </div>
        
          <button onclick="goBack()" class="indigoTheme roundBorder" style="padding: 10px 20px;">Back to List</button>
        </div>
      </div>
      
    </div>
  </div>

  <script>
    document.querySelectorAll('.user-row').forEach(row => {
      row.addEventListener('click', async function () {
        const userId = this.dataset.userId;
        const userType = this.dataset.userType;

        try {
          const response = await fetch(`/website/webservices/adminDashboard/infoRetrieve.php?userID=${userId}&userType=${userType}`);
          const data = await response.json();

          if (data.error) {
            alert("Error: " + data.error);
            return;
          }

          // Show detail view
          document.querySelector('.user-list').style.display = 'none';
          document.querySelector('.user-details').style.display = 'block';

          // Populate general info
          document.getElementById('json-userid').value = data.UserID;
          document.getElementById('json-usertype').value = data.UserType;
          document.getElementById('json-firstname').value = data.FirstName;
          document.getElementById('json-lastname').value = data.LastName;
          document.getElementById('json-email').value = data.Email;
          document.getElementById('json-gender').value = data.Gender;
          document.getElementById('json-dob').value = new Date(data.DateOfBirth).toISOString().split('T')[0];

          // If student, show extra section
          if (data.UserType === 'Student') {
            document.querySelector('.student-extra').style.display = 'block';
            document.getElementById('json-level').value = data.Level;
            document.getElementById('json-classgroup').value = data.ClassGroup;

            const subjectContainer = document.getElementById('json-subjects-container');
            subjectContainer.innerHTML = '';

            if (data.Subjects && data.Subjects.length > 0) {
              data.Subjects.forEach(sub => {

                const div = document.createElement('div');
                div.className = 'subject-item';

                const userId = document.getElementById('json-userid').value;
                div.innerHTML = `
                  <input class="subject-code" type="text" value="${sub.SubjectCode}" readonly />
                  <input class="subject-name" type="text" value="${sub.Subjectname}" readonly />
                  <form action="/website/webservices/adminDashboard/subjectDelete.php" method="POST" style="display:inline;">
                    <input type="hidden" name="subjectCode" value="${sub.SubjectCode}">
                    <input type="hidden" name="userID" value="${userId}">
                    <button type="submit" class="remove-subject indigoTheme roundBorder" style="margin-left: 10px;">Delete</button>
                  </form>
                `;

                subjectContainer.appendChild(div);
              });
            } else {
              subjectContainer.innerHTML = '<div>No subjects assigned.</div>';
            }
          } else {
            document.querySelector('.student-extra').style.display = 'none';
          }
        } catch (err) {
          console.error(err);
          alert('Failed to fetch user data.');
        }
      });
    });

    function goBack() {
      document.querySelector('.user-list').style.display = 'table';
      document.querySelector('.user-details').style.display = 'none';
    }

    document.getElementById("personalinfo-savechanges-admin").addEventListener("submit", async function (e) {
      e.preventDefault();  

      const form = e.target;
      const formData = new FormData(form);  

      // For student-specific subjects (if needed)
      if (form.querySelector("#json-usertype")?.value === "Student") {
        const subjects = [];
        document.querySelectorAll(".subject-item").forEach(div => {
          const subjectCode = div.querySelector(".subject-code")?.value;
          if (subjectCode) subjects.push(subjectCode);
        });
        formData.append("subjects", JSON.stringify(subjects));
      }

      try {
        const response = await fetch("/users/edit", {
          method: "POST",
          body: formData
        });

 
        const resultText = await response.text();

        if (response.ok) {
          alert("User information updated successfully!");
          window.location.href = "/dashboard";
        } else {
          alert("Server returned an error. See console for more details.");
        }
      } catch (error) {
        console.error("Fetch error:", error);
        alert("Something went wrong while calling update API.");
      }

    });

    function redirectToresetPass() {
      const userId = document.getElementById("json-userid").value;
 
      const confirmReset = confirm("Are you sure you want to reset the password for this user?");
      if (!confirmReset) return;

      // Redirect to the reset password PHP handler
      window.location.href = `/website/webservices/adminDashboard/resetPass.php?userID=${encodeURIComponent(userId)}`;
      alert("Password changed to: pass1234");
    }

    function redirectToverifyAcc() {
      const userId = document.getElementById("json-userid").value;

      // Optional confirmation
      const confirmVerify = confirm("Are you sure you want to verify this account?");
      if (!confirmVerify) return;

      // Redirect to verification handler
      window.location.href = `/website/webservices/adminDashboard/verifyAcc.php?userID=${encodeURIComponent(userId)}`;
    }

    function redirectToDeleteAcc() {
      const userId = document.getElementById("json-userid").value;

      const confirmDelete = confirm("Are you sure you want to delete this user?");
      if (!confirmDelete) return;

      fetch(`/users/delete/${encodeURIComponent(userId)}`)
        .then(async res => {
          const text = await res.text();
          console.log("Raw response:", text); // 👀 LOG IT

          try {
            const data = JSON.parse(text);
            if (data.success) {
              alert("User successfully deleted.");
              window.location.href = "/dashboard";
            } else {
              alert("Failed to delete user: " + (data.errors || []).join(", "));
            }
          } catch (e) {
            console.error("Error parsing JSON:", e);
            alert("Something went wrong. Raw server reply:\n" + text);
          }
        })
        .catch(error => {
          console.error("Fetch error:", error);
          alert("Something went wrong during deletion.");
        });
    }




  </script>
</body>

</html>