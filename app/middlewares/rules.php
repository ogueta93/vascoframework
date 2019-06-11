<?php
/**
 * Rules file
 **/

return [
    'Home' => [
        'global' => 'demo'
    ],

    'Test' => [
        'global' => 'demo',
        'login , loginUser' => 'checkGuest',
        'users, resume , getAllUsers , updateUser' => 'checkLogged'
    ]
];
