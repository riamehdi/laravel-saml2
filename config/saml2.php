<?php

return [
    // config
    'cert_path' => 'saml2',
    'sp_cert_file' => 'sp.crt',
    'sp_key_file' => 'sp.pem',
    'idp_cert_file' => 'idp.crt',
    'sp_entity_id_prod' => 'SP-' . env('APP_NNA') . '-prodn1',
    'sp_entity_id_dev' => 'SP-' . env('APP_NNA') . '-recn1',
    'idp_logout_url_prod' => 'https://mon-compte.enedis.fr:443/auth/alex-ihm/logout?spEntityId=',
    'idp_logout_url_dev' => 'https://moncompte-recn1.enedis.fr:10443/alex-ihm/logout?spEntityId=',
    'idp_login_url_prod' => 'https://mon-compte.enedis.fr:443/auth/SSOPOST/metaAlias/enedis/providerIDP',
    'idp_login_url_dev' => 'https://moncompte-recn1.enedis.fr:10443/auth/SSOPOST/metaAlias/enedis/providerIDP',
    'admin_domains' => ['enedis.fr'],
    'log_channel' => 'users_logins',
    'log_driver' => 'daily',
    'log_path' => storage_path('logs/users/logins.log'),
    'log_ignore_exceptions' => false,
    'log_level' => 'debug',
    'log_days' => 730,
];
