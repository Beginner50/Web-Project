<?php
require 'utilities/functions.php';

// dd($_SERVER);

if ($_SERVER['REQUEST_URI'] === '/') {
    require 'controllers/authenticationController.php';
} else {
    dd($_SERVER['REQUEST_URI']);
}
