<?php
// Copy this file to config/config.php and edit it. config.php is ignored by git so your credentials stay private.
// Default XAMPP MySQL: user "root", empty password.
return [
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'praxia',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    // Enquiries are always saved to the database. If true, PHP mail() also emails the notify address
    // (needs a working SMTP / sendmail setup in php.ini, which XAMPP does not have by default).
    'mail_notifications' => false,
    'mail_from'          => 'no-reply@example.com',
    // Set true while developing to see PHP errors in the browser.
    'debug'              => false,
    // Max CV upload size in bytes (3 MB).
    'max_cv_bytes'       => 3 * 1024 * 1024,
];
