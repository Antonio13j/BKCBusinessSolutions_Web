# Instalación MariaDB

1. Crear base y usuario en hPanel, asignando todos los privilegios sobre esa base.
2. Importar `001_schema.sql` y después `002_indexes.sql` desde phpMyAdmin.
3. Copiar `acuerdos/config.example.php` como `acuerdos/config.php` y completar las credenciales reales.
4. Ejecutar una sola vez `php database/create_initial_users.php` desde una terminal segura o adaptarlo temporalmente a una tarea protegida.
5. Eliminar o renombrar `create_initial_users.php` después del alta. `003_seed_demo.sql` es opcional y nunca contiene clientes reales.

