<?php 

//require_once '../database/conexionesdb.php';
require_once '../Crontab/database/conexionesdb.php';


class getdata_scpv2_scpv3 extends conexioSQL
{
    

    public function getDataProyectosscpV2()
    {
        $query = "select tp.cproyecto,tp.codigo,tp.nombre,tp.ctipoproyecto,tp.tipoproy,tp.fproyecto,tp.cestadoproyecto,tc.descripcion generaingreso from tproyecto tp
        left join tcatalogo tc on tp.tipoproy = tc.digide  and tc.codtab='00056'
        where cestadoproyecto != '000';";
        $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll(); 
        //print_r ( $listArray);
        return $listArray;
    }

    public function getDataProyectosscpV3()
    {
        $queryv3 = "SELECT *  FROM sproyecto.t_proyectos";
        $stmtv3 = $this->conexionpdoPostgresLocalSCPv3()->prepare($queryv3);
        $stmtv3->execute();
        $listArrayv3 = $stmtv3->fetchAll();
        //print_r($listArrayv3);
       return $listArrayv3;
    }

    public function getDataActualizaroRegistrar()
    {
        $datascpv2 =  $this->getDataProyectosscpV2();

        //print_r($datascpv2);
       
        $datascpv3 =  $this->getDataProyectosscpV3();
       // print_r($datascpv3);
        $conexionSCPv3 = $this->conexionpdoPostgresLocalSCPv3();
        $cont = 0;
        $cont1 = 0;

        $proyectosContractuales   = $this->proyectosContractuales();
        $proyectosSOC             = $this->proyectosSOC();
        $proyectosSubproyectos    = $this->proyectosSubProyectos();
    

        $data_insertada =$conexionSCPv3->prepare("INSERT INTO sproyecto.t_proyectos (
                                                                            nomproy,
                                                                            codproy,
                                                                            tipoproyecto,
                                                                            generaingreso,
                                                                            fechacreacion,
                                                                            idserviciosproyecto,
                                                                           /*  
                                                                           
                                                                           
                                                                            */
                                                                            codmigraproy) VALUES (
                                                                            :nomproy,
                                                                            :codproy,
                                                                            :tipoproyecto,
                                                                            :generaingreso,
                                                                            :fechacreacion,
                                                                            :idserviciosproyecto,
                                                                           /*  
                                                                           
                                                                           
                                                                            :idserviciosproyecto, */
                                                                            :codmigraproy
                                                                            )"); 

        $data_actualizada = $conexionSCPv3->prepare("UPDATE sproyecto.t_proyectos  SET 
                                                                    nomproy         = :nomproy,
                                                                    codproy         = :codproy,
                                                                    tipoproyecto    = :tipoproyecto,
                                                                    generaingreso   = :generaingreso,
                                                                    fechacreacion   = :fechacreacion,
                                                                    idserviciosproyecto = :idserviciosproyecto,
                                                                   /* 
                                                                    
                                                                   
                                                                    idserviciosproyecto = :idserviciosproyecto */
                                                                    codmigraproy        = :codmigraproy
                                                                    WHERE  codmigraproy= :codmigraproy
                                                   ");                                                             

        foreach($datascpv2 as $scpv2):
            foreach($datascpv3 as $scpv3):
                if ($scpv3['codmigraproy'] == $scpv2['cproyecto']) {
                    $cont++;
                     $data_actualizada->execute(array(
                    'nomproy'     => $scpv2["nombre"], 
                    'codproy'     => $scpv2["codigo"],
                    'tipoproyecto'=> $this->tipoProyecto($scpv2["codigo"],$proyectosContractuales,$proyectosSOC,$proyectosSubproyectos),
                    'generaingreso'=> $scpv2["generaingreso"] == 'Si'? 0 : 1, 
                    'idserviciosproyecto'=> $this->tipoServicio($scpv2['ctipoproyecto']),
                    'fechacreacion'=> $scpv2["fproyecto"],
                   /*  
                    'idserviciosproyecto'=> $scpv2["descripcionrol"],   */
                    'codmigraproy'=> $scpv2["cproyecto"]
                    //'nomcarg' => 'Cargo' // No puede insertarse la data por que el tamaño es 30 (limitado)
                ));
                    continue 2;
                }
            endforeach; 

           $data_insertada->execute(array(
                    'nomproy'     => $scpv2["nombre"], 
                    'codproy'     => $scpv2["codigo"],
                    'tipoproyecto'=> $this->tipoProyecto($scpv2["codigo"],$proyectosContractuales,$proyectosSOC,$proyectosSubproyectos), 
                    'generaingreso'=> $scpv2["generaingreso"] == 'Si'? 0 : 1, 
                    'fechacreacion'=> $scpv2["fproyecto"],
                    'idserviciosproyecto'=>$this->tipoServicio($scpv2['ctipoproyecto']),
                    'codmigraproy'=> $scpv2["cproyecto"]
                   //'nomcarg' =>'Cargo'
                ));
                   
            $cont1++; 
        endforeach;    
        echo($cont1.' '.'Proyectos insertados')."\n";
        echo($cont.' '.'Proyectos actualizados');

    }

    

    public function proyectosContractuales()
    {
        $query = "select codigo from tproyecto
        where codigo not ilike '%-%'
        and cestadoproyecto != '000'";
        $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll(); 
        $listArrayfdNC = array_column($listArray, "codigo");
        $trimmed_arrayfdNC = array_map('trim', $listArrayfdNC);
        sort($trimmed_arrayfdNC);
        //print_r($trimmed_arrayfdNC);
        return $trimmed_arrayfdNC;
    }

    public function proyectosSubProyectos()
    {
        $query = "select codigo, substring(codigo,strpos(codigo,'-')+1) as subp_soc from tproyecto
        where codigo  ilike '%-%' 
        and  substring(substring(codigo,strpos(codigo,'-')+1),1,2) != '00' 
        and cestadoproyecto != '000';";
        $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll(); 
        $listArrayfdNC = array_column($listArray, "codigo");
        $trimmed_arrayfdNC = array_map('trim', $listArrayfdNC);
        sort($trimmed_arrayfdNC);

        return $trimmed_arrayfdNC;
    }

    public function proyectosSOC()
    {
        $query = "select codigo, substring(codigo,strpos(codigo,'-')+1) as subp_soc from tproyecto
        where codigo  ilike '%-%' 
        and  substring(substring(codigo,strpos(codigo,'-')+1),1,2) = '00' 
        and cestadoproyecto != '000'; ";
        $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll(); 
        $listArrayfdNC = array_column($listArray, "codigo");
        $trimmed_arrayfdNC = array_map('trim', $listArrayfdNC);
        sort($trimmed_arrayfdNC);

        return $trimmed_arrayfdNC;
    }

    public function tipoProyecto($codigo,$prycontractuales,$prySOC,$prySubproyectos)
    { 
        $codigo2 = str_replace(' ', '', $codigo);
        if(array_search($codigo2, $prycontractuales))
        {
            return '1';
        }
        if(array_search($codigo2, $prySOC))
        {
            return '2';
        }
        if(array_search($codigo2, $prySubproyectos))
        {
            return '3';
        }
    }

    public function tipoServicio($codmigraservicios)
    { 
        $codmigraser = $codmigraservicios == null ? 50 : $codmigraservicios;
        $query_servicio = "SELECT idserviciosproyecto FROM sproyecto.t_serviciosproyectos WHERE codmigraser = $codmigraser";
        $servicio = $this->conexionpdoPostgresLocalSCPv3()->prepare($query_servicio);
        $servicio->execute();
        $capurarservicio = $servicio->fetch();
        //$ola = $capurarservicio ? $capurarservicio : null;
        //print_r($capurarservicio);
        return $capurarservicio ? $capurarservicio[0] : null;
    }
}

$data = new getdata_scpv2_scpv3();
//$data->tipoProyecto2('1010.20.05-001',$data->proyectosSOC());
$data->getDataActualizaroRegistrar();
//$data->getDataActualizaroRegistrar();

//$data->tipoServicio(null);


