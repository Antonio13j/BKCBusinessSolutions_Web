# Viabilidad del Portal de Acuerdos BKC

## Resultado

Viable con PHP 8.1+ y MariaDB/MySQL en Hostinger Web Single. La aplicación no requiere Node.js, Docker, acceso root ni procesos residentes. Se implementa en `public_html/acuerdos/` sin modificar la navegación ni el `index.html` corporativo.

## Arquitectura encontrada

El repositorio de producción es `ModelWeb/WebOficialBKC`, rama `main`, remoto `Antonio13j/BKCBusinessSolutions_Web`. El sitio existente es HTML/CSS/JavaScript estático, sin build, dependencias ni backend. Su identidad usa DM Sans/Manrope, fondo azul oscuro (`#07111f`), verde (`#22c55e`) y celeste (`#38bdf8`). Logo e imágenes corporativas se sirven actualmente desde `raw.githubusercontent.com`.

Durante la auditoría existía una modificación local no confirmada en `index.html`; se preservó y no forma parte del portal.

## Arquitectura recomendada e implementada

```text
SQL Server local (fuente corporativa)
        -> sincronizador Python futuro, selectivo
MariaDB Hostinger (base operacional)
        -> PDO/prepared statements
PHP 8.1+ (sesiones, autorización, CSRF, auditoría)
        -> HTML/CSS/JS progresivo
/acuerdos/
```

MariaDB usa InnoDB, claves foráneas, `utf8mb4`, importes `DECIMAL`, índices de consulta y una tabla de secuencia anual. La combinación `INSERT ... ON DUPLICATE KEY UPDATE` + `LAST_INSERT_ID()` produce códigos únicos aun con concurrencia.

La autenticación usa `password_hash`/`password_verify`, normalización de usuario, cookies HttpOnly/SameSite y Secure bajo HTTPS, regeneración de sesión, CSRF y autorización backend. El login aplica bloqueo de 15 minutos tras cinco fallos. Errores internos se registran sin exponer datos de MariaDB. La auditoría no recibe passwords ni hashes.

## Compatibilidad y restricciones Hostinger

- Seleccionar PHP 8.1, 8.2 o 8.3 y habilitar PDO MySQL, mbstring y JSON.
- HTTPS debe estar activo; forzar redirección HTTPS desde hPanel/Apache.
- El límite de 1 CPU/1 GB es suficiente para el MVP transaccional, no para conversiones LibreOffice.
- Las credenciales viven únicamente en `acuerdos/config.php`, ignorado por Git.
- No se requiere Composer en producción para el MVP.
- Confirmar que el despliegue Git publica las carpetas nuevas bajo el mismo `public_html`.

## Documentos

El Word oficial SMA debe guardarse fuera de `public_html`, como indica `acuerdos/documentos/README.md`. Se auditó el proceso local de `Cesion_Derechos`: usa `python-docx` sobre siete runs fijos y LibreOffice headless para PDF. Hostinger Web Single no garantiza Python ni LibreOffice, por lo que esa conversión debe continuar en el proceso local y devolver el PDF al almacenamiento privado. PHPWord puede evaluarse para DOCX, pero no se debe prometer fidelidad PDF ni inventar cláusulas.

## Archivos afectados

No se modifica ningún archivo corporativo existente. Se añaden `acuerdos/`, `database/`, `sync/`, este documento y reglas de `.gitignore`.

## Riesgos y controles

- La versión/extensiones PHP y los límites exactos deben confirmarse en hPanel.
- Sin MariaDB configurada no puede ejecutarse el portal.
- La máquina de desarrollo auditada no tenía PHP instalado, por lo que la validación de sintaxis/runtime debe repetirse en Hostinger o CI antes de integrar a `main`.
- La plantilla Word se basa en posiciones de runs: cualquier edición del documento exige una prueba de regresión visual.
- La aprobación/rechazo, edición funcional de reglas/campañas, carga de pagos y entrega documental requieren una siguiente iteración antes del uso operacional completo.

## Estrategia de despliegue

Importar los SQL, crear `config.php`, crear usuarios iniciales una sola vez, probar en una URL de staging o con acceso restringido, ejecutar la matriz de pruebas y sólo entonces integrar a `main`. No se debe afirmar un despliegue exitoso sin comprobar URL, login, permisos, escritura y descarga desde Hostinger.

