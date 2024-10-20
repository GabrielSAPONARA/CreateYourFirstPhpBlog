<?php
$router->addMatchTypes(array('uuid' => '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}'));
$router->map('GET', '/', "WelcomeController::index");
$router->map('GET', '/social/network', "SocialNetworkController::index");
$router->map('GET', '/social/network/add', "SocialNetworkController::add");
$router->map('POST', '/social/network/process', "SocialNetworkController::process");
$router->map('POST', '/social/network/process/[uuid:id]', "SocialNetworkController::process");
$router->map('GET', '/social/network/modify/[uuid:id]', "SocialNetworkController::modify");
$router->map('GET', '/social/network/delete/[uuid:id]', "SocialNetworkController::delete");