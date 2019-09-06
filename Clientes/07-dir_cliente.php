<?php

require_once '../Crontab/database/postgres_test_conexion.php';
require_once '../Crontab/database/postgres_scpv3Test_conexion.php';
//require_once '../Crontab/database/pg_tblog_conexion.php';

class getdata_scpv2UM_scpv3
{
    use conexionPostgres_QA, conexionTestPostgresdbscpv3;


    public function getDataClientescpV3()
    {
        $query = "SELECT * FROM scliente.t_cliente";
        $stmt = $this->conexionpdoPostgresTestscpv3()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll();
        return $listArray;
    }

    public function getDataDireccionescpV3()
    {
        $query = "SELECT * FROM scliente.t_direcciones";
        $stmt = $this->conexionpdoPostgresTestscpv3()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll();
        return $listArray;
    }

    public function getDataActualizaroRegistrar()
    {
        $dataCliente_scpv3 =  $this->getDataClientescpV3();
        $dataDirecciones_scpv3 =  $this->getDataDireccionescpV3();

        $conexionSCPv3 = $this->conexionpdoPostgresTestscpv3();
        $cont = 0;
        $cont1 = 0;


        $data_insertada = $conexionSCPv3->prepare("INSERT INTO scliente.tclidir (t_direcciones_iddir,
                                                                                 t_cliente_idcliente
                                                                                 ) VALUES (
                                                                                :t_direcciones_iddir,
                                                                                :t_cliente_idcliente
                                                                                 )");

        $data_actualizada = $conexionSCPv3->prepare("UPDATE scliente.tclidir  SET 
                                                                    t_direcciones_iddir         = :t_direcciones_iddir,
                                                                    t_cliente_idcliente        = :t_cliente_idcliente
                                                                    WHERE  t_cliente_idcliente  = :t_cliente_idcliente
                                                   ");
        foreach ($dataDirecciones_scpv3 as $scpdv3) :
            foreach ($dataCliente_scpv3 as $scpcv3) :
                if ($scpcv3['codmigracli'] == $scpdv3['codmigradirecciones']) {
                    $cont++;
                    $data_insertada->execute(array(

                        't_direcciones_iddir'   => $scpdv3["iddir"],
                        't_cliente_idcliente'   => $scpcv3["idcliente"]
                    ));
                    continue 2;
                }
            endforeach;

            // $data_insertada->execute(array(
            //     't_direcciones_iddir'   => $scpdv3["iddir"],
            //     't_cliente_idcliente'   => $scpcv3["idcliente"]
            // ));

            $cont1++;
        endforeach;
        echo ($cont1);
    }
}

$data = new getdata_scpv2UM_scpv3();
$data->getDataActualizaroRegistrar();
