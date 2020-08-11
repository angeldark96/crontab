INSERT INTO scliente.t_tipoempresa (nomemp,abremp,t_pais_idpais) VALUES 
('Empresa Individual de Responsabilidad Limitada ','EIRL',1)
,('Sociedad Anónima','SA',1)
,('Sociedad Anónima Abierta','SAA',1)
,('Sociedad Anónima Cerrada','SAC',1)
,('Sociedad Comercial de Responsabilidad Limitada','SRL',1)
,('Sociedad Anónima','SA',4)
,('Sociedad de Responsabilidad Limitada','SRL',4)
,('Sociedad por Acciones Simplificada','SAS',4)
,('Empresa Estatal de Derecho Privado','EEDP',1)
,('Contratos Colaboracion Empresarial','CCE',1)
,('Instituciones Públicas','IP',1)
,('Sucursales o Ag. de Emp. Extranj.','SAEE',1)
,('Asociación','ASOC',1)
,('Gobierno Central','GC',1)
,('Universidad. Centros Educat. y Cult.','UCEC',1)
,('No Aplica','NA',1)
;

truncate table  scliente.t_tipoempresa restart identity cascade;