<?php
$router->addMatchTypes(array('uuid' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}'));

// route to welcome page
$router->map('GET', '/', "WelcomeController::index", "welcome");

// routes to manage social networks
$router->map('GET', '/social/network', "SocialNetworkController::index", "social_networks");
$router->map('GET', '/social/network/add', "SocialNetworkController::add", "social_networks_addition");
$router->map('POST', '/social/network/process', "SocialNetworkController::process", "social_networks_process__create");
$router->map('POST', '/social/network/process/[uuid:id]', "SocialNetworkController::process", "social_networks_process__update");
$router->map('GET', '/social/network/modify/[uuid:id]', "SocialNetworkController::modify", "social_networks_modify");
$router->map('GET', '/social/network/delete/[uuid:id]', "SocialNetworkController::delete", "social_networks_delete");

// routes to manage roles
$router->map('GET', '/role', "RoleController::index", "roles");
$router->map('GET', '/role/add', "RoleController::add", "roles__addition");
$router->map('POST', '/role/process', "RoleController::process", "roles_process__create");
$router->map('POST', '/role/process/[uuid:id]', "RoleController::process", "roles_process__update");
$router->map('GET', '/role/modify/[uuid:id]', "RoleController::modify", "roles_modify");
$router->map('GET', '/role/delete/[uuid:id]', "RoleController::delete", "roles_delete");

// routes to manage users
$router->map('GET', '/user', "UserController::index", "users");
$router->map('GET', '/user/add', "UserController::add", "users__addition");
$router->map('POST', '/user/process', "UserController::process", "users_process__create");
$router->map('POST', '/user/process/[uuid:id]', "UserController::process", "users_process__update");
$router->map('GET', '/user/modify/[uuid:id]', "UserController::modify", "users_modify");
$router->map('GET', '/user/delete/[uuid:id]', "UserController::delete", "users_delete");

// routes to manage authentication
$router->map('GET', '/login', "AuthController::login", "login");
$router->map('POST', '/login', "AuthController::login", "login_process");