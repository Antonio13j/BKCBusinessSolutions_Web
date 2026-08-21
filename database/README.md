# Instalación MariaDB

1. Crear base y usuario en hPanel, asignando todos los privilegios sobre esa base.
2. Importar `001_schema.sql` y después `002_indexes.sql` desde phpMyAdmin.
3. Copiar `acuerdos/config.example.php` como `acuerdos/config.php` y completar las credenciales reales.
4. Con SSH, ejecutar una sola vez `php database/create_initial_users.php`. Sin SSH, configurar un `SETUP_TOKEN` aleatorio de 32+ caracteres, abrir `/acuerdos/setup.php`, crear los usuarios y eliminar inmediatamente `setup.php` y `SETUP_TOKEN`.
5. El archivo `storage/setup.lock` bloquea usos posteriores. `003_seed_demo.sql` es opcional y nunca contiene clientes reales.

