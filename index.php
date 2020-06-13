<?php

/**
 * This is the pepper, which (along with the salt) is used for encrypting and
 * decrypting passwords. The salt for each user is generated individually and
 * stored in the database user table. The purpose of the pepper is to decouple
 * the risk of an adversary getting access to the key, of which now there are
 * two, plus the users' passwords themselves.
 * 
 * If this were a production server, this would not be defined here. One
 * possible methodology is defining the PEPPER variable in the Apache
 * configuration file (or in the configuration of whatever server you're using),
 * and then accessing it via the $_ENV superglobal.
 * 
 * @todo Move this somewhere safe in real code.
 */
define( 'PEPPER', '4Xlf526DLSMI3Ys4nuIZoxac8qGrZqkpOGfxUGH1aKnIJrX576akF39ws3D2jh47' );

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

                /**
                 * @todo Normally we would verify the user doesn't already exist
                 * by searching for the supplied email in the database. If we
                 * were really trying to get fancy, we would use a bloom filter
                 * to significantly speed up the process.
                 */
                // TODO: Check user does not already exist in the database.

                /**
                 * Verify the password and password confirmation string match.
                 */
                if (strcmp($password, $confirm)) {
                    /**
                     * Just like in C, strcmp returns 0 if the strings do match,
                     * so if we hit this block, the password did not match the
                     * confirmation string.
                     */
                    die("Password and confirmation string did not match.");
                }

                /**
                 * HMAC the user's password using the pepper. This ensures that
                 * even if the database is compromised and the salts are
                 * exposed, there is another separately-stored secret preventing
                 * the password plaintexts from being recovered without having
                 * to brute-force them.
                 */
                $password = hash_hmac('sha512', $password, PEPPER);

                /**
                 * Now, hash the hmac-ed password like we normally would.
                 */
                $password = password_hash($password, PASSWORD_ARGON2ID, [
                    'memory_cost' =>  65536,
                    'time_cost'   =>     20,
                    'threads'     =>      8
                ]);

                /**
                 * Display the original password via the confirmation string, as
                 * well as the resultant hashed password and the user email
                 * address.
                 * 
                 * @todo Remove this in actual code.
                 */
                echo "<p>Email:&nbsp;<span>$email</span></p>";
                echo "<p>Password:&nbsp;<span>$password</span></p>";
                echo "<p>Confirm:&nbsp;<span>$confirm</span></p>";

                /**
                 * Display the registration page.
                 */
                require_once('Views/Home.php');
            } break;
        } // Action Switch
    } break; // Case POST
} // Request Switch
