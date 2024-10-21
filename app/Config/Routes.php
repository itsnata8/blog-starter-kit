<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('admin', static function ($routes) {
    $routes->group('', ['filter' => 'cifilter:auth'], static function ($routes) {
        // $routes->view('example-page', 'example-page');
        $routes->get('home', 'AdminController::index', ['as' => 'admin.home']);
        $routes->get('logout', 'AdminController::logoutHandler', ['as' => 'admin.logout']);
        $routes->get('profile', 'AdminController::profile', ['as' => 'admin.profile']);
        $routes->post('update-personal-details', 'AdminController::updatePersonalDetails', ['as' => 'admin.update-personal-details']);
        $routes->post('update-picture-profile', 'AdminController::updatePictureProfile', ['as' => 'admin.update-picture-profile']);
        $routes->post('change-password', 'AdminController::changePassword', ['as' => 'admin.change-password']);
        $routes->get('settings', 'AdminController::settings', ['as' => 'admin.settings']);
        $routes->post('update-general-settings', 'AdminController::updateGeneralSettings', ['as' => 'admin.update-general-settings']);
        $routes->post('update-blog-logo', 'AdminController::updateBlogLogo', ['as' => 'admin.update-blog-logo']);
        $routes->post('update-blog-favicon', 'AdminController::updateBlogFavicon', ['as' => 'admin.update-blog-favicon']);
        $routes->post('update-social-media', 'AdminController::updateSocialMedia', ['as' => 'admin.update-social-media']);
        $routes->get('categories', 'AdminController::categories', ['as' => 'admin.categories']);
        $routes->post('add-category', 'AdminController::addCategory', ['as' => 'admin.add-category']);
        $routes->get('get-categories', 'AdminController::getCategories', ['as' => 'admin.get-categories']);
        $routes->get('get-category', 'AdminController::getCategory', ['as' => 'admin.get-category']);
        $routes->post('update-category', 'AdminController::updateCategory', ['as' => 'admin.update-category']);
        $routes->get('delete-category', 'AdminController::deleteCategory', ['as' => 'admin.delete-category']);
    });
    $routes->group('', ['filter' => 'cifilter:guest'], static function ($routes) {
        // $routes->view('example-auth', 'example-auth');
        $routes->get('login', 'AuthController::loginForm', ['as' => 'admin.login.form']);
        $routes->post('login', 'AuthController::loginHandler', ['as' => 'admin.login.handler']);
        $routes->get('forgot-password', 'AuthController::forgotForm', ['as' => 'admin.forgot.form']);
        $routes->post('send-password-reset-link', 'AuthController::sendPasswordResetLink', ['as' => 'send_password_reset_link']);
        $routes->get('password/reset/(:any)', 'AuthController::resetPassword/$1', ['as' => 'admin.reset-password']);
        $routes->post('reset-password-handler/(:any)', 'AuthController::resetPasswordHandler/$1', ['as' => 'reset-password-handler']);
    });
});
