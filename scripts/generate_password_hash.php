<?php
if ($argc < 2) {
    echo "Uso: php generate_password_hash.php contraseña\n";
    exit(1);
}
echo password_hash($argv[1], PASSWORD_DEFAULT) . "\n";
