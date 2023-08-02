<?php

require_once('functions.php');

// Put your site URL and web service token here

$config['site']     = 'https://moodle.something.ac.uk';
$config['token']    = '74833e911605ee374986da0e41874371';

// If you set this to true, the entire script will fail if Moodle's web
// services reports an error. If it's false, the script will try and continue.
$config['fail']     = false;

// Used for testing. If this is false, the users will actually be deleted.
// If it's true, the script will tell you which users it wants to delete, but
// it won't actually delete them.
$config['test']    = true;

$userIDField = 0;
$usernameField = 1;
$firstnameField = 3;
$lastnameField = 4;

out('Moodle GDPR Delete Script by Alex Walker. Version 1.0.', '*');

$file = @fopen('users.csv', 'r');

if(!$file) {
    out('Cannot find users.csv', '!', 'red');
    die();
}

$count = 0;

while($csv = fgetcsv($file)) {
    if(!$config['test']) {
        out('Deleting User '.$csv[$firstnameField].' '.$csv[$lastnameField], '+', 'green');
        $response = sendToMoodle(
            'core_user_delete_users',
            array('userids' => array(
                $csv[$userIDField],
            )),
            false
        );
        
        $count++;
    } else {
        out('Test mode - would have deleted user '.$csv[$firstnameField].' '.$csv[$lastnameField], '+', 'green');
    }
}

out('Done. Deleted '.$count.' users.', '*', 'green');
