# Matriz de pruebas manuales

Ejecutar en staging después de importar la base y crear usuarios:

1. Login correcto con los cinco usuarios e incorrecto/inexistente/inactivo.
2. Bloqueo temporal tras cinco fallos; verificar que auditoría no contiene password/hash.
3. Cambio de contraseña, estado visible al admin y reset a la clave inicial.
4. Logout, acceso sin sesión y HTTP 403 de un agente en `admin.php`.
5. Búsqueda por documento con payload `' OR 1=1 --` sin resultados ni error SQL.
6. Crear cancelación y convenio; verificar suma exacta de cuotas en MariaDB.
7. Condición dentro de regla crea `VIGENTE`; fuera de regla crea `PENDIENTE_APROBACION`.
8. Confirmar que un agente sólo ve filas cuyo `creado_por_id` coincide con su sesión.
9. Confirmar códigos únicos en dos solicitudes simultáneas.
10. Probar desktop, laptop, tablet y móvil; revisar secretos con `git grep -nE '(DB_PASSWORD|password_hash).*(=|:).+'` antes del push.

Comandos cuando PHP esté disponible:

```bash
find acuerdos database -name '*.php' -print0 | xargs -0 -n1 php -l
php database/create_initial_users.php
```

