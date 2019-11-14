<?php 

require_once '../Crontab/database/conexionesdb.php';


class getdata_scpv2_scpv3 extends conexioSQL
{
    

    public function getDataRolesscpV2()
    {
        $query = "select * from troldespliegue where abreviatura is not null;";
        $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll(); 
        //print_r ( $listArray);
        return $listArray;
    }

    public function getDataRolesscpV3()
    {
        $queryv3 = "SELECT *  FROM sproyecto.t_m_rolesproyectos";
        $stmtv3 = $this->conexionpdoPostgresLocalSCPv3()->prepare($queryv3);
        $stmtv3->execute();
        $listArrayv3 = $stmtv3->fetchAll();
        //print_r($listArrayv3);
       return $listArrayv3;
    }

    public function getDataActualizaroRegistrar()
    {
        $datascpv2 =  $this->getDataRolesscpV2();
       
        $datascpv3 =  $this->getDataRolesscpV3();
       // print_r($datascpv3);
        $conexionSCPv3 = $this->conexionpdoPostgresLocalSCPv3();
        $cont = 0;
        $cont1 = 0;
       

        $data_insertada =$conexionSCPv3->prepare("INSERT INTO sproyecto.t_m_rolesproyectos (nombrolproy,codmigrarol) VALUES (
                                                                    :nombrolproy,:codmigrarol)"); 

        $data_actualizada = $conexionSCPv3->prepare("UPDATE sproyecto.t_m_rolesproyectos  SET 
                                                                    nombrolproy             = :nombrolproy,
                                                                    codmigrarol= :codmigrarol
                                                                    WHERE  codmigrarol= :codmigrarol
                                                   ");                                                             

        foreach($datascpv2 as $scpv2):
            foreach($datascpv3 as $scpv3):
                if ($scpv3['codmigrarol'] == $scpv2['croldespliegue']) {
                    $cont++;
                     $data_actualizada->execute(array(
                    'codmigrarol' =>$scpv2["croldespliegue"],
                    'nombrolproy' => $scpv2["descripcionrol"], // 
                    //'nomcarg' => 'Cargo' // No puede insertarse la data por que el tamaño es 30 (limitado)
                ));
                    continue 2;
                }
            endforeach; 

           $data_insertada->execute(array(
                     'codmigrarol' =>$scpv2["croldespliegue"],
                     'nombrolproy' => $scpv2["descripcionrol"],
                   //'nomcarg' =>'Cargo'
                ));
                   
            $cont1++; 
        endforeach;    
        echo($cont1.' '.'Roles insertados')."\n";
        echo($cont.' '.'Roles actualizados');

    }
    
}

$data = new getdata_scpv2_scpv3();
//$data->getDataActualizaroRegistrar();
$data->getDataActualizaroRegistrar();
