<?php
$router->addMatchTypes(array('uuid' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}'));

// route to welcome page
$router->map('GET', '/', "WelcomeController::index", "welcome");

// routes to manage roles
$router->map('GET', '/role', "RoleController::index", "roles");
$router->map('GET', '/role/add', "RoleController::add", "roles__addition");
$router->map('POST', '/role/process', "RoleController::processToCreateOrUpdateRole", "roles_process__create");
$router->map('POST', '/role/process/[uuid:id]', "RoleController::processToCreateOrUpdateRole", "roles_process__update");
$router->map('GET', '/role/modify/[uuid:id]', "RoleController::modify", "roles_modify");
$router->map('GET', '/role/delete/[uuid:id]', "RoleController::delete", "roles_delete");

// routes to manage users
$router->map('GET', '/user', "UserController::index", "users");
$router->map('GET', '/user/add', "UserController::add", "users__addition");
$router->map('POST', '/user/add', "UserController::add", "users__addition__process");
//$router->map('POST', '/user/process/[uuid:id]', "UserController::processToCreateOrUpdateUser", "users_process__update");
$router->map('GET', '/user/modify/[uuid:id]', "UserController::modify", "users_modify");
$router->map('POST', '/user/modify/[uuid:id]', "UserController::modify", "users_modify_process");
$router->map('GET', '/user/delete/[uuid:id]', "UserController::delete", "users_delete");
$router->map('GET', '/register', "UserController::register", "register");
$router->map('POST', '/register/process', "UserController::register", "register__process");
$router->map('GET', '/user/profile', "UserController::profile", "profile");
$router->map('GET', '/user/profile/modify', 'UserController::modifyProfile', "profile__modify");
$router->map('POST', '/user/profile/modify', 'UserController::modifyProfile', "profile__modify__process");
$router->map('GET', '/user/profile/set/my/password', 'UserController::setMyPassword', "set__my__password");
$router->map('POST', '/user/profile/set/my/password', 'UserController::setMyPassword', "set__my__password__process");

// routes to manage authentication
$router->map('GET', '/login', "AuthController::login", "login");
$router->map('POST', '/login', "AuthController::login", "login_process");
$router->map('GET', '/logout', "AuthController::logout", "logout");

// routes to manage errors
$router->map('GET', '/forbidden', "BasicController::forbidden", "forbidden");

// routes to manage posts
$router->map('GET', '/post', "PostController::index", "posts");
$router->map('GET', '/post/add', "PostController::add", "posts__addition");
$router->map('POST', '/post/add', "PostController::add", "posts__addition__process");
$router->map('GET', '/post/details/[uuid:postId]', "PostController::details", "posts__details");
$router->map('GET', '/post/modify/[uuid:postId]', "PostController::modify", "posts__modify");
$router->map('POST', '/post/modify/[uuid:postId]', "PostController::modify", "posts__modify__process");
$router->map('GET', '/post/byUser', "PostController::postByUser", "posts__byUser");
$router->map('GET', '/post/delete/[uuid:postId]', "PostController::delete", "posts__delete");

// routes to manage comments
$router->map('GET', '/comment/add/[uuid:postId]', "CommentController::add", "comments__addition");
$router->map('POST', '/comment/create/[uuid:postId]', "CommentController::createComment", "comments_process__create");
$router->map('GET', '/comment/byUser', "CommentController::commentByUser", "comments__byUser");
$router->map('GET', '/comment/modify/[uuid:commentId]/post/[uuid:postId]', "CommentController::modify", "comments__modify");
$router->map('POST', '/comment/modify/[uuid:commentId]/post/[uuid:postId]', "CommentController::modify", "comments__modify__process");
$router->map('GET', '/comment/delete/[uuid:commentId]', "CommentController::delete", "comments__delete");
$router->map('GET', '/comment/toValidate', "CommentController::getCommentsToValidate", "comments__toValidate");
$router->map('GET', '/comment/validateComment/[uuid:commentId]', "CommentController::validateComment", "validate__comment");
$router->map('POST', '/comment/validateComment/[uuid:commentId]', "CommentController::validateComment", "process__to__validate__comment");

// routes to contact
$router->map('GET', '/contact', "ContactController::contact", "contact");
$router->map('POST', '/contact', "ContactController::contact", "contact__process");