<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Uppercase Exception Fields
    |--------------------------------------------------------------------------
    |
    | Request fields listed here will NOT be converted to uppercase by the
    | UppercaseInput middleware. Add any new case-sensitive field names here
    | to keep logic centralized and DRY.
    |
    */

    'except' => [
        'email',
        'email_address',
        'username',
        'user_name',
        'password',
        'password_confirmation',
        'url',
        'website',
        'link',
        'callback_url',
        'redirect_uri',
        'system_id',
        'token',
        'api_key',
        '_token',
        '_method',
    ],

];
