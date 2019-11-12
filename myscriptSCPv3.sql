-- Añadir codigo de migracion UM
ALTER TABLE scliente.t_cliente ADD COLUMN codmigracli integer;
ALTER TABLE scliente.t_unidadminera ADD COLUMN codmigraum integer;
ALTER TABLE scliente.t_contactos ADD COLUMN codmigracont integer;
ALTER TABLE scliente.t_cargo ADD COLUMN codmigracargo character(5);
ALTER TABLE scliente.t_umcontacto ADD COLUMN codmigracontacto integer;
ALTER TABLE scliente.t_umcontacto ADD COLUMN codmigrauminera integer;
ALTER TABLE scliente.t_direcciones ADD COLUMN codmigradirecciones integer;









