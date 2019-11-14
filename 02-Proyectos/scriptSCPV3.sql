
-- scpV3 - sproyecto.t_estadosproyectos
ALTER TABLE sproyecto.t_estadosproyectos ADD COLUMN codmigraestado varchar(5);
INSERT INTO sproyecto.t_estadosproyectos (nombestado,color,codmigraestado) VALUES 
('Activo','success','001')
,('Cancelado','primary','002')
,('Paralizado','danger','003')
,('Cierre-tec','warning','004')
,('Cierre-Adm','warning','005')
,('Anulado','primary','006')
,('Reapertura-AND','success','007')
,('Reapertura-SOC','success','008')
,('Cierre','warning','009')
;

-- scpV3 - sproyecto.t_portafoliosproyecto
ALTER TABLE sproyecto.t_portafoliosproyecto ADD COLUMN codmigraporta integer;

INSERT INTO sproyecto.t_portafoliosproyecto (nombreportafolio,codmigraporta) VALUES 
('Servicios de ingeníeria',1)
,('Servicios de Construcción',2)
,('Servicios Ambientales',3)
,('Otros',5)
,('Anddes',7)
;



-- scpV3 - sproyecto.t_serviciosproyectos
ALTER TABLE sproyecto.t_serviciosproyectos ADD COLUMN codmigraser integer;
INSERT INTO sproyecto.t_serviciosproyectos (nombreservicios,idportafolioproyecto,codmigraser) VALUES 
('Interno',5,1)
,('Intercompany',5,2)
,('Ingeniería',1,3)
,('Geotecnia',1,4)
,('Electromecánica',1,5)
,('Estudios Ambientales',3,6)
,('Instrumentos de Gestión Ambiental',3,7)
,('Ingeniería Ambiental',3,8)
,('Supervisión de Construcción',2,9)
,('Supervisión de CQA',2,10)
,('Supervisión de CQA/CQC',2,11)
,('MQA',2,12)
,('Detección Geoeléctrica',2,13)
;


ALTER TABLE sproyecto.t_m_rolesproyectos ADD COLUMN codmigrarol integer;
ALTER TABLE sproyecto.t_proyectos ADD COLUMN codmigraproy varchar(30);


