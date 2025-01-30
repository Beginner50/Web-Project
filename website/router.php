<?php
if ($_SERVER['REQUEST_URI'] === '/') {
    require 'controllers/authenticationController.php';
} else {
    var_dump($_SERVER['REQUEST_URI']);
    die();
}
