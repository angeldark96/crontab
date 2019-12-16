-- Ultimo cambio de pk_flowdesk
ALTER TABLE tpersona ADD COLUMN pk_fd varchar(5);
update tpersona set pk_fd = 582 where cpersona=4122;
update tpersona set pk_fd = 615 where cpersona=4118;