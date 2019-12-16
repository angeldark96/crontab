-- Clientes
/*
Anddes ARG
Anddes CHL
*/
update tpersona set identificacion = '3071422823' where cpersona = 4431;
update tpersona set identificacion = '764931661' where cpersona = 4432;
update tpersona set identificacion = '20205467603' where cpersona = 4443;
update tpersona set identificacion = '20100147514' where cpersona = 4493;
update tpersona set identificacion = 'RUC004' where cpersona = 4496;

update tpersonadirecciones set direccion = 'Jr. Manuel del Águila Nro. 667 (Oficina 1) San Martin - Moyobamba - Moyobamba'  where cpersona = 4498
update tpersonadirecciones set direccion = 'N. Graig Street-pittsburgh, Pa 15213 - Usa'  where cpersona = 4537 and ctipodireccion  = 'COM'


-- client 1 a eliminar
delete from tpersonadirecciones as t2  
where t2.cpersona=4551;

delete from tpersonajuridicainformacionbasica as t3  
where t3.cpersona=4551;

delete from tpersona as t 
where t.cpersona=4551;

-- client 2 a eliminar 
delete from tpersonadirecciones as t2  
where t2.cpersona=4551;

delete from tpersonajuridicainformacionbasica as t3  
where t3.cpersona=4551;

delete from tpersona as t 
where t.cpersona=4551;


-- Ultimo cambio de pk_flowdesk
ALTER TABLE tpersona ADD COLUMN pk_fd varchar(5);
update tpersona set pk_fd = 582 where cpersona=4122;
update tpersona set pk_fd = 615 where cpersona=4118;


