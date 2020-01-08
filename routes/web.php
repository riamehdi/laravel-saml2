<?php

Route::get('/saml2/login', 'Saml2Controller@login')->name('saml2-login');
Route::post('/saml2/acs', 'Saml2Controller@acs')->name('saml2-acs');
Route::get('/saml2/logout', 'Saml2Controller@logout')->name('saml2-logout');
Route::get('/saml2/metadata', 'Saml2Controller@getMetadata')->name('saml2-metadata');

// TEMP !
Route::get('/saml2/logadminin', 'Saml2Controller@logAdminIn')->name('saml2-log-admin-in'); // bypass connexion and log Admin in
Route::get('/saml2/loguserbyid/{user}', 'Saml2Controller@logUserById')->name('saml2-log-user-by-id'); // bypass connexion and log User by ID
