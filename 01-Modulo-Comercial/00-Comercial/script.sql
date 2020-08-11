select * from clientes;
truncate table  scliente.t_cliente restart identity cascade;

create schema cliente;

CREATE TABLE cliente.clientes (
  item  smallserial NOT NULL,
  razon_social varchar null,
	abreviatura varchar null,
	pais varchar NULL,
	tipo_empresa varchar NULL,
	tipo_tributo varchar NULL,
	ruc varchar NULL,
	web varchar NULL,
	holding varchar NULL,
	estado varchar NULL,
	observacion varchar NULL,
	logo varchar null,
	CONSTRAINT t_item_pk PRIMARY KEY (item)
);

copy cliente.clientes from 'D:/migra_excel/migra_clientes3.csv' using delimiters ';' csv header;


CREATE TABLE cliente.direcciones (
    iddir smallserial NOT NULL,
    direccion varchar null,
	departamento varchar null,
	provincia varchar NULL,
	distrito varchar NULL,
	numerodir varchar NULL,
	referenciadir varchar NULL,
	pais varchar NULL
);

copy cliente.direcciones from 'D:/migra_excel/migra_direcciones.csv' using delimiters ';'  csv header;

CREATE TABLE cliente.tipoempresa (
    idtipoempresa varchar null,
    nombretipoempresa varchar null,
	abreviatura varchar null,
	pais varchar NULL
);

copy cliente.tipoempresa from 'D:/migra_excel/migra_tipoempresa.csv' using delimiters ';';

CREATE TABLE cliente.tipotributo (
    idtipotributo varchar null,
    nombretipotributo varchar null,
	abreviatura varchar null
);

copy cliente.tipotributo from 'D:/migra_excel/migra_tipotributo.csv' using delimiters ';';
----------------------------------------------------------------------------------------
CREATE TABLE contacto.tbl_contacto (
    idcon smallserial not null,
    nombre varchar null,
	apaterno varchar null,
	amaterno varchar NULL,
	cargo varchar NULL,
	idcargo int NULL,
	area_descripcion varchar NULL,
	contexto varchar NULL,
	tipo varchar null,
	estado varchar null,
	cliente varchar null,
	uminera varchar null,
	CONSTRAINT t_idcon_pk PRIMARY KEY (idcon)
);

--truncate table  contacto.tbl_contacto restart identity cascade;
copy contacto.tbl_contacto from 'D:/migra_excel/migra_contactos.csv' using delimiters ';' csv header;

CREATE TABLE contacto.telefono (
  idcontel  smallserial not null,
  numero varchar null,
	tipo varchar null,
	anexo varchar NULL,
	principal varchar null
	--CONSTRAINT t_idcon_tel_pk PRIMARY KEY (idcontel)
);

copy contacto.telefono from 'D:/migra_excel/migra_telefonos.csv' using delimiters ';' csv header;

CREATE TABLE contacto.correo (
    idcon smallserial not null,
    correo  varchar null
);

copy contacto.correo from 'D:/migra_excel/migra_correos.csv' using delimiters ';' csv header;

CREATE TABLE contacto.cargo (
    idcon smallserial not null,
    nombrecargo  varchar null,
    idcargopk int  null  
);

copy scliente.t_cargo from 'D:/migra_excel/migra_cargos.csv' using delimiters ';' csv header;


CREATE TABLE contacto.tipocli (
    tipocli varchar null
);

copy contacto.tipocli from 'D:/migra_excel/migra_tipocli.csv' using delimiters ';';

-----------------------------------------------------------------------------------------------------

CREATE TABLE uminera.uminera (
    idcon smallserial not null,
    nombre_um varchar null,
	estado_um varchar null,
	pais varchar NULL,
	direccion varchar NULL,
	referencia varchar NULL,
	cliente varchar NULL,
	logo varchar NULL,
	departamento varchar NULL,
	provincia varchar NULL,
	distrito varchar NULL
);

copy uminera.uminera from 'D:/migra_excel/migra_uminera.csv' using delimiters ';'csv header;

CREATE TABLE uminera.pais (
    id varchar null,
	pais varchar NULL
);

copy uminera.pais from 'D:/migra_excel/migra_pais.csv' using delimiters ';';


----------------------------------------------------------------------------

CREATE TABLE invitaciones.disciplina (
  iddiscip  int null,
  nombDiscip varchar null,
	idscc int,
	estDiscip varchar NULL
);

copy invitaciones.disciplina from 'D:/migra_excel/migra_dis.csv' using delimiters ';' csv header;