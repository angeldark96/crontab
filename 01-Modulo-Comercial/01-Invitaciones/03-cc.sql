truncate table sfinanzas.t_cc restart identity cascade;
truncate table sfinanzas.t_scc restart identity cascade;

INSERT INTO sfinanzas.t_cc (nombcc,codcc) VALUES 
('Operaciones','91')
,('Administración','94')
,('Ventas','95')
;

ALTER TABLE sfinanzas.t_scc ADD estadoscc int4 NULL;
COMMENT ON COLUMN sfinanzas.t_scc.estadoscc IS '0:activo 1:inactivo';