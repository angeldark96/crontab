ALTER TABLE spropuesta.t_invitacion ADD codmigrainvitacion int4 NULL;

0:En Elaboración,1:Ganada,2:Cancelada,3:Declinada,4:En Espera,5:Perdida ->tconfiguration --- resultado no va 
update tconfiguracion set valor = '0:En Elaboración,1:Ganada,2:Cancelada,3:Declinada,4:En Espera,5:Perdida'  where idconf = 19;

truncate table spropuesta.t_invitacion restart identity cascade;

UPDATE sproyecto.t_documentos_empresa SET codcorrelativo='001' WHERE iddocemp=2;
UPDATE sproyecto.t_documentos_empresa SET codcorrelativo='161' WHERE iddocemp=3;

SELECT * FROM  sproyecto.t_documentos_empresa;
UPDATE sproyecto.t_documentos_empresa SET codcorrelativo='004' WHERE iddocemp=2;


