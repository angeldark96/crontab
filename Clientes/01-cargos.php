<?php 

require_once '../Crontab/database/postgres_test_conexion.php';
require_once '../Crontab/database/postgres_scpv3Test_conexion.php';
//require_once '../Crontab/database/pg_tblog_conexion.php';

class getdata_scpv2_scpv3
{
    use conexionPostgres_QA, conexionTestPostgresdbscpv3;

    public function getDataCargoscpV2()
    {
        $query = "SELECT * FROM tcontactocargo";
        $stmt = $this->conexionpdoPostgresTest_QA()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll(); 
        return $listArray;
    }

    public function getDataCargoscpV3()
    {
        $queryv3 = "SELECT *  FROM scliente.t_cargo";
        $stmtv3 = $this->conexionpdoPostgresTestscpv3()->prepare($queryv3);
        $stmtv3->execute();
        $listArrayv3 = $stmtv3->fetchAll(); 
        return $listArrayv3;
    }


    public function getDataActualizaroRegistrar()
    {
        $datascpv2 =  $this->getDataCargoscpV2();
        $datascpv3 =  $this->getDataCargoscpV3();
        $conexionSCPv3 = $this->conexionpdoPostgresTestscpv3();
        $cont = 0;
        $cont1 = 0;
        $fecha_actual = date("Y-m-d");

        $data_insertada =$conexionSCPv3->prepare("INSERT INTO scliente.t_cargo (nomcarg,codmigracargo) VALUES (
                                                                    :nomcarg,:codmigracargo)"); 

        $data_actualizada = $conexionSCPv3->prepare("UPDATE scliente.t_cargo  SET 
                                                                    nomcarg             = :nomcarg,
                                                                    codmigracargo       = :codmigracargo
                                                                    WHERE  codmigracargo= :codmigracargo
                                                   ");                                                             

        foreach($datascpv2 as $scpv2):
            foreach($datascpv3 as $scpv3):
                if ($scpv2['ccontactocargo'] == $scpv3['codmigracargo']) {
                    $cont++;
                     $data_actualizada->execute(array(
                    'codmigracargo' =>$scpv2["ccontactocargo"],
                   // 'nomcarg' => $scpv2["descripcion"], // 
                    'nomcarg' => 'Cargo', // No puede insertarse la data por que el tamaño es 30 (limitado)
                ));
                    continue 2;
                }
            endforeach; 

           $data_insertada->execute(array(
                    'codmigracargo' =>$scpv2["ccontactocargo"],
                   // 'nomcarg' => $scpv2["descripcion"],
                   'nomcarg' =>'Cargo'
                ));
                   
            $cont1++; 
        endforeach;    
        echo($cont1);

    }

   
    
}

$data = new getdata_scpv2_scpv3();
$data->getDataActualizaroRegistrar();
