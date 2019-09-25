-- Añadir codigo de migracion UM
ALTER TABLE scliente.t_unidadminera ADD COLUMN codmigraum integer;
ALTER TABLE scliente.t_contactos ADD COLUMN codmigracont integer;
ALTER TABLE scliente.t_cargo ADD COLUMN codmigracargo character(5);
ALTER TABLE scliente.t_umcontacto ADD COLUMN codmigracontacto integer;
ALTER TABLE scliente.t_umcontacto ADD COLUMN codmigrauminera integer;
ALTER TABLE scliente.t_direcciones ADD COLUMN codmigradirecciones integer;

-- Version 2
ALTER TABLE ttipocontrato ADD COLUMN pk_contratofd character varying(20);
update ttipocontrato set pk_contratofd = '10' where ctipocontrato = 'CON';
update ttipocontrato set pk_contratofd = '1,3,4,9' where ctipocontrato = 'PLA';
update ttipocontrato set pk_contratofd = '11' where ctipocontrato = 'RXH';
ALTER TABLE tpersonadatosempleado ADD COLUMN email varchar(250);
ALTER TABLE tpersonadatosempleado ADD COLUMN email_laboral varchar(250);








