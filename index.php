<?php

$request_method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);

switch ($request_method) {
    case 'GET': {
        /**
         * Display home page.
         */
        require_once('Views/Home.php');
    } break; // Case GET

    case 'POST': {
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

        switch ($action) {
            case 'register': {
                $email = filter_input(INPUT_POST, 'user-email', FILTER_SANITIZE_STRING);
                $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
                $confirm = filter_input(INPUT_POST, 'password-confirmation', FILTER_SANITIZE_STRING);

                echo "<p>Email:&nbsp;<span>$email</span></p>";
                echo "<p>Password:&nbsp;<span>$password</span></p>";
                echo "<p>Confirm:&nbsp;<span>$confirm</span></p>";
            } break;
        } // Action Switch
    } break; // Case POST
} // Request Switch
