# Sincronización futura

SQL Server BKC seguirá siendo la fuente corporativa. Un proceso Python local hará lecturas selectivas y `UPSERT` autenticado hacia MariaDB de clientes, operaciones, saldos y campañas. No se abrirá el puerto 1433 ni se sincronizarán históricos masivos, BI, SBS, telefonía o enriquecimiento. Las tablas y columnas SQL Server deben validarse antes de implementar el conector.

