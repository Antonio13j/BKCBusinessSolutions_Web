<?php
return [
    'DB_HOST' => 'localhost',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'bkc_acuerdos',
    'DB_USERNAME' => 'bkc_user',
    'DB_PASSWORD' => 'CAMBIAR_EN_HOSTINGER',
    'APP_ENV' => 'production',
    'APP_URL' => 'https://bkcsolution.com/acuerdos',
    // Genere un valor aleatorio de al menos 32 caracteres y elimínelo tras el setup.
    'SETUP_TOKEN' => 'CAMBIAR_POR_TOKEN_ALEATORIO_LARGO',
    // Token independiente para el sincronizador local SQL Server -> portal.
    'SYNC_TOKEN' => 'CAMBIAR_POR_OTRO_TOKEN_ALEATORIO_LARGO',
    'DOCUMENT_OUTPUT_PATH' => '/home/USUARIO/bkc_private/generated',
];

