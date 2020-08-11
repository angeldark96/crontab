\i HLPAS0000001.sql;

ALTER TABLE "susuario"."t_areadisc" ADD CONSTRAINT "fk_tareadisc_tscc" FOREIGN KEY ("idscc") REFERENCES "sfinanzas"."t_scc" ("idscc") ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE "sfinanzas"."t_scc" ADD CONSTRAINT "fk_tscc_tcc" FOREIGN KEY ("idcc") REFERENCES "sfinanzas"."t_cc" ("idcc") ON DELETE NO ACTION ON UPDATE NO ACTION;


truncate table susuario.t_areas restart identity cascade;
truncate table susuario.t_disciplinas restart identity cascade;
truncate table susuario.t_areadisc restart identity cascade;


ALTER TABLE susuario.t_areas ADD estadoarea int4 NULL;
COMMENT ON COLUMN susuario.t_areas.estadoarea IS '0:activo 1:inactivo';

INSERT INTO susuario.t_areas (idarea,nombre,codigo,idarea_p,esgt,estadoarea) VALUES 
(1,'Gerencia General',NULL,NULL,NULL,0)
,(2,'Gerencia de Ingeniería y Ambiental',NULL,1,NULL,0)
,(3,'Gerencia de Servicios Ambientales',NULL,2,'0',0)
,(4,'Gerencia de Civil e Hidráulica',NULL,2,'0',0)
,(5,'Gerencia de Geotecnia',NULL,2,'0',0)
,(6,'Gerencia de Electromecánica',NULL,2,'0',0)
,(7,'General',NULL,1,NULL,1)
,(8,'Procesos',NULL,6,NULL,1)
,(9,'Diseño Civil',NULL,4,NULL,0)
,(10,'Costos y Presupuestos',NULL,4,NULL,0)
,(11,'Estructuras',NULL,6,NULL,0)
,(12,'Mecánica',NULL,6,NULL,0)
,(13,'Tuberías',NULL,6,NULL,0)
,(14,'Electricidad',NULL,6,NULL,0)
,(15,'Instrumentación',NULL,6,NULL,0)
,(16,'Hidrología e Hidraulica',NULL,4,NULL,0)
,(17,'Hidráulica',NULL,4,NULL,1)
,(20,'Proyectistas CH',NULL,4,NULL,0)
,(22,'Transformación Digital',NULL,1,NULL,0)
,(23,'Investigación, Desarrollo e Innovación',NULL,1,NULL,0)
,(24,'no usar',NULL,NULL,NULL,1)
,(25,'Sistema Integrado de Gestión',NULL,36,NULL,0)
,(26,'Seguridad, Salud y Ambiente',NULL,1,NULL,0)
,(27,'Geoprocesos',NULL,5,NULL,0)
,(28,'Geotecnia',NULL,5,NULL,0)
,(29,'Geología',NULL,5,NULL,0)
,(30,'Geofísica',NULL,5,NULL,0)
,(31,'Geomecánica',NULL,5,NULL,0)
,(32,'Gerencia de Proyectos',NULL,2,NULL,0)
,(33,'Planeamiento y Control de Proyectos',NULL,36,NULL,0)
,(34,'Control Documentario',NULL,36,NULL,0)
,(35,'QA Planos y Mapas',NULL,32,NULL,0)
,(36,'PMO',NULL,1,NULL,0)
,(37,'Ambiental',NULL,NULL,NULL,1)
,(38,'Gerencia de Administración y Finanzas',NULL,1,NULL,0)
,(39,'Contador General',NULL,38,NULL,0)
,(40,'Finanzas',NULL,38,NULL,0)
,(41,'Tesorería',NULL,38,NULL,0)
,(42,'Contabilidad',NULL,38,NULL,0)
,(43,'Gestión Humana',NULL,1,NULL,0)
,(44,'Tecnologías de la Información',NULL,38,NULL,0)
,(45,'Logística',NULL,38,NULL,0)
,(47,'Logística de Proyectos',NULL,38,NULL,0)
,(48,'Mantenimiento e Infraestructura',NULL,38,NULL,0)
,(49,'Comunicaciones',NULL,38,NULL,0)
,(50,'Gerencia de Servicios de Construcción',NULL,1,'0',0)
,(51,'Gerencia de Supervisión',NULL,50,NULL,0)
,(52,'Gerencia de CQA',NULL,50,NULL,0)
,(53,'Construcción',NULL,51,NULL,1)
,(54,'Comercial',NULL,1,NULL,0)
,(55,'Propuestas y Licitaciones',NULL,54,NULL,0)
,(56,'Marketing',NULL,54,NULL,0)
,(57,'Gerencia Técnica',NULL,50,NULL,0)
,(58,'Proyectos campo - Geotecnia',NULL,NULL,NULL,1)
,(59,'Consultor externo',NULL,NULL,NULL,1)
,(60,'Sub Jefe de Geotécnia',NULL,28,NULL,0)
;