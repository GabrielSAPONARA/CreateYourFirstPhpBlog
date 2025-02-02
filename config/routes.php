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
$router->map('GET', '/register', "UserController::register", "register");
$router->map('POST', '/register/process', "UserController::processToRegister", "register__process");
$router->map('GET', '/user/profile', "UserController::profile", "profile");
$router->map('GET', '/user/profile/modify', 'UserController::modifyProfile', "profile__modify");
$router->map('POST', '/user/profile/modify', 'UserController::modifyProfile', "profile__modify__process");

// routes to manage authentication
$router->map('GET', '/login', "AuthController::login", "login");
$router->map('POST', '/login', "AuthController::login", "login_process");
$router->map('GET', '/logout', "AuthController::logout", "logout");

// routes to manage errors
$router->map('GET', '/forbidden', "BasicController::forbidden", "forbidden");

// routes to manage posts
$router->map('GET', '/post', "PostController::index", "posts");
$router->map('GET', '/post/add', "PostController::add", "posts__addition");
$router->map('POST', '/post/process', "PostController::process", "posts_process__create");
$router->map('GET', '/post/details/[uuid:postId]', "PostController::details", "posts__details");
$router->map('GET', '/post/modify/[uuid:postId]', "PostController::modify", "posts__modify");
$router->map('POST', '/post/modify/[uuid:postId]', "PostController::modify", "posts__modify__process");
$router->map('GET', '/post/byUser', "PostController::postByUser", "posts__byUser");
$router->map('GET', '/post/delete/[uuid:postId]', "PostController::delete", "posts__delete");

// routes to manage comments
$router->map('GET', '/comment/add/[uuid:postId]', "CommentController::add", "comments__addition");
$router->map('POST', '/comment/process/[uuid:postId]', "CommentController::process", "comments_process__create");
$router->map('GET', '/comment/byUser', "CommentController::commentByUser", "comments__byUser");