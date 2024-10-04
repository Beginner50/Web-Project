<?php
  session_start();
  //TO CODE THE CHECKING WHETHER IT IS A STUDENT OR NOT
  
  $approve=1;
  require_once '../connect.php';

  $adminID = $_SESSION['UserID'];

  $stmt = $pdo -> prepare('UPDATE approval SET AdminID = ?,IsApproved=? WHERE UserId =?');
  if ($stmt -> execute([$adminID,$approve,$_SESSION['UserID-Clicked']])){

    $_SESSION['verifyAccStatus']='Account has been verified';
  }else{
    $_SESSION['verifyAccStatus']='Something went wrong while verifying account';
  }

  header('Location: ../adminPage.php');
  exit();
  
?>
