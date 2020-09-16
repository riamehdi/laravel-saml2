<?php

return [

    // ALEX ou GARDIAN
    'mode' => env('SAML2_MODE', 'alex'),

    // Certs files
    'cert_path' => 'saml2',
    'sp_cert_file' => 'sp.crt',
    'sp_key_file' => 'sp.pem',
    'idp_cert_file' => 'idp.crt',

    // Configuration entity id
    'sp_entity_id_prod' => 'SP-' . env('APP_NNA') . '-prodn1',
    'sp_entity_id_dev' => 'SP-' . env('APP_NNA') . '-recn1',

    //  Alex URLs
    'alex_idp_logout_url_prod' => env('SAML2_LOGOUT_URL_PROD', ''),
    'alex_idp_logout_url_dev' => env('SAML2_LOGOUT_URL_DEV', ''),
    'alex_idp_login_url_prod' => env('SAML2_LOGIN_URL_PROD', ''),
    'alex_idp_login_url_dev' => env('SAML2_LOGIN_URL_DEV', ''),

    //  Gardian URLs
    'gardian_idp_logout_url_prod' => env('GARDIAN_LOGOUT_URL_PROD', ''),
    'gardian_idp_logout_url_dev' => env('GARDIAN_LOGOUT_URL_DEV', ''),
    'gardian_idp_login_url_prod' => env('GARDIAN_LOGIN_URL_PROD', ''),
    'gardian_idp_login_url_dev' => env('GARDIAN_LOGIN_URL_DEV', ''),

    // Custom consts
    'interne_domains' => [env('SAML2_INTERNE_DOMAIN', 'test.fr')], // domaine de l'adresse e-mail qui permet de reconnaitre les users internes.
    'app_user_controller' => 'App\Http\Controllers\Api\UserController', // UserController where is location the logUserIn($attributes) function

    // Logging
    'log_channel' => 'users_logins',
    'log_driver' => 'daily',
    'log_path' => storage_path('logs/users/logins.log'),
    'log_ignore_exceptions' => false,
    'log_level' => 'debug',
    'log_days' => 730,

];
