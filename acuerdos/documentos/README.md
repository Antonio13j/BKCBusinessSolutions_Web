# Plantilla SMA y documentos privados

La plantilla oficial única es `PLANTILLA CESION DE ACUERDO1.docx`. La ubicación recomendada en Hostinger es **fuera de `public_html`**:

```text
/home/USUARIO/bkc_private/templates/PLANTILLA CESION DE ACUERDO1.docx
/home/USUARIO/bkc_private/generated/
```

Agregar después a `config.php`:

```php
'DOCUMENT_TEMPLATE_PATH' => '/home/USUARIO/bkc_private/templates/PLANTILLA CESION DE ACUERDO1.docx',
'DOCUMENT_OUTPUT_PATH' => '/home/USUARIO/bkc_private/generated',
```

No colocar documentos generados en una URL pública. La descarga debe pasar por PHP, sesión y autorización del acuerdo.

El proceso local auditado (`C:\VS_Code\Cesion_Derechos`) modifica siete *runs* concretos con `python-docx`: nombre, DNI, dirección, distrito, departamento, cuenta y deuda; después convierte con LibreOffice `--headless`. Ese diseño conserva imágenes, encabezado, pie y maquetación, pero **no puede trasladarse tal cual a Hostinger Web Single**, porque requiere Python, `python-docx` y un ejecutable LibreOffice persistente/disponible.

Estrategia recomendada:

- En el portal PHP: registrar el acuerdo, datos y solicitud de documento.
- En el proceso local BKC: leer solicitudes pendientes, usar el Word SMA ya validado, generar DOCX/PDF con el código existente y subir el resultado por un canal autenticado.
- En PHP: almacenar sólo una referencia aleatoria y servir el archivo mediante un controlador autorizado.

PHPWord empaquetado puede generar DOCX en Hostinger sin Composer por SSH, pero la plantilla actual usa índices físicos de párrafos/runs y debe migrarse y probarse antes. PHPWord tampoco garantiza conversión PDF fiel. La conversión definitiva debe seguir realizándose en el proceso local con LibreOffice hasta contar con un servicio de documentos controlado.

