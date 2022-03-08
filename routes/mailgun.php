<?php

/**
 * Mailgun endpoints
 */

Route::group([
    'prefix' => 'mailgun/events',
], function(){
    Route::post('/change-status', 'MailgunController@changeStatus');
});