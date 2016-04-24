<?php
    // This is a shell file to allow for these global variables to be recognized by the IDE. The actual file should be named dsn.inc,
    // and should have the below values filled in.

    // Set Timezone
    date_default_timezone_set('America/Chicago');

    // Database Object
    $db = new mysqli("server", "user", "password", "database");
    $db->query("set character_set_client='utf8'");
    $db->query("set character_set_results='utf8'");
    $db->query("set collation_connection='utf8_general_ci'");

    // PayPal API Settings
    $paypalModeSetting = "live";
    $paypalUser = "user";
    $paypalPassword = "password";
    $paypalSignature = "key";

    // Stripe Settings
    $stripeKey = "secret_key";
    $stripePublicKey = "public_key";

    // Email Settings
    $smtpPass = "password";
?>