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
    'alex_idp_logout_url_prod' => 'https://mon-compte.enedis.fr:443/alex-ihm/logout?spEntityId=',
    'alex_idp_logout_url_dev' => 'https://moncompte-recn1.enedis.fr:10443/alex-ihm/logout?spEntityId=',
    'alex_idp_login_url_prod' => 'https://mon-compte.enedis.fr:443/auth/SSOPOST/metaAlias/enedis/providerIDP',
    'alex_idp_login_url_dev' => 'https://moncompte-recn1.enedis.fr:10443/auth/SSOPOST/metaAlias/enedis/providerIDP',

    //  Gardian URLs
    'gardian_idp_logout_url_prod' => 'https://websso-gardian.myelectricnetwork.com/gardianwebsso/UI/Logout',
    'gardian_idp_logout_url_dev' => 'https://rec-websso-gardian.myelectricnetwork.com/gardianwebsso/UI/Logout',
    'gardian_idp_login_url_prod' => 'https://websso-gardian.myelectricnetwork.com:443/gardianwebsso/SSOPOST/metaAlias/multiauth/idp5',
    'gardian_idp_login_url_dev' => 'https://rec-websso-gardian.myelectricnetwork.com:443/gardianwebsso/SSOPOST/metaAlias/multiauth/idp5',

    // Custom consts
    'interne_domains' => ['enedis.fr'], // domaine de l'adresse e-mail qui permet de reconnaitre les users internes.
    'app_user_controller' => 'App\Http\Controllers\Api\UserController', // UserController where is location the logUserIn($attributes) function

    // Logging
    'log_channel' => 'users_logins',
    'log_driver' => 'daily',
    'log_path' => storage_path('logs/users/logins.log'),
    'log_ignore_exceptions' => false,
    'log_level' => 'debug',
    'log_days' => 730,

];
