<?php
$router->addMatchTypes(array('uuid' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}'));

// route to welcome page
$router->map('GET', '/', "WelcomeController::index");

// routes to manage social networks
$router->map('GET', '/social/network', "SocialNetworkController::index");
$router->map('GET', '/social/network/add', "SocialNetworkController::add");
$router->map('POST', '/social/network/process', "SocialNetworkController::process");
$router->map('POST', '/social/network/process/[uuid:id]', "SocialNetworkController::process");
$router->map('GET', '/social/network/modify/[uuid:id]', "SocialNetworkController::modify");
$router->map('GET', '/social/network/delete/[uuid:id]', "SocialNetworkController::delete");

// routes to manage roles
$router->map('GET', '/role', "RoleController::index");
$router->map('GET', '/role/add', "RoleController::add");
$router->map('POST', '/role/process', "RoleController::process");
$router->map('POST', '/role/process/[uuid:id]', "RoleController::process");
$router->map('GET', '/role/modify/[uuid:id]', "RoleController::modify");
$router->map('GET', '/role/delete/[uuid:id]', "RoleController::delete");