<!-- coded by Efe -->
<?php

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}


function generateVerificationCode() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}
?>
