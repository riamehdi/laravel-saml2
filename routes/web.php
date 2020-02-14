<?php

Route::get('/saml2/login', 'LaravelSaml2Controller@login')->name('saml2-login');
Route::post('/saml2/acs', 'LaravelSaml2Controller@acs')->name('saml2-acs');
Route::get('/saml2/logout', 'LaravelSaml2Controller@logout')->name('saml2-logout');
Route::get('/saml2/metadata', 'LaravelSaml2Controller@getMetadata')->name('saml2-metadata');
