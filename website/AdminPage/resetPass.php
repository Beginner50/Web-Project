<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
  <?php

    require_once '../connect.php';
    session_start();

    $password=password_hash('$1lent.k',PASSWORD_DEFAULT);
    
    $stmt = $pdo -> prepare("UPDATE user SET Password = ? Where UserID = ? ");
    if($stmt -> execute([$password,$_SESSION['UserID-Clicked']])){

      $_SESSION['PassChange'] = 'Password has been reset';

    }else{
      $_SESSION['PassChange'] = 'Something went wrong';
    }

    header('Location: adminPage.php');
    exit();

    

  ?>
</body>
</html>