<?php
require_once '../connect.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sGetListUsers = $pdo->prepare('CALL sp_getListUsers();');
$sGetListUsers->execute();
$users = $sGetListUsers->fetchAll(PDO::FETCH_ASSOC);
