<?php

require_once '../Crontab/database/postgres_conexion.php';
require_once '../Crontab/database/postgres_scpv3Test_conexion.php';
//require_once '../Crontab/database/pg_tblog_conexion.php';

class getdata_scpv2UM_scpv3
{
    use conexionPostgres, conexionTestPostgresdbscpv3;


    public function getDataClientescpV3()
    {
        $query = "SELECT * FROM scliente.t_unidadminera";
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

    public function getDataExistsUMineraindirUM($idum)
    {
        $query = "SELECT * FROM scliente.tdirum where t_unidadminera_idum = $idum ";
        $stmt = $this->conexionpdoPostgresTestscpv3()->query($query);
        $row_count = $stmt->rowCount();
        $res = ($row_count > 0) ? 'data' : 'sin_data';
        return $res;
    }

    public function getDataActualizaroRegistrar()
    {
        $dataCliente_scpv3 =  $this->getDataClientescpV3();
        $dataDirecciones_scpv3 =  $this->getDataDireccionescpV3();

        $conexionSCPv3 = $this->conexionpdoPostgresTestscpv3();
        $cont = 0;
        $cont1 = 0;


        $data_insertada = $conexionSCPv3->prepare("INSERT INTO scliente.tdirum (t_direcciones_iddir,
                                                                                 t_unidadminera_idum
                                                                                 ) VALUES (
                                                                                :t_direcciones_iddir,
                                                                                :t_unidadminera_idum
                                                                                 )");

        $data_actualizada = $conexionSCPv3->prepare("UPDATE scliente.tdirum  SET 
                                                                    t_direcciones_iddir         = :t_direcciones_iddir,
                                                                    t_unidadminera_idum        = :t_unidadminera_idum
                                                                    WHERE  t_unidadminera_idum  = :t_unidadminera_idum
                                                   ");
        foreach ($dataDirecciones_scpv3 as $scpdv3) :
            foreach ($dataCliente_scpv3 as $scpcv3) :
                if ($scpcv3['codmigraum'] == $scpdv3['codmigradirecciones']  &&  $this->getDataExistsUMineraindirUM($scpcv3["idum"]) == 'sin_data') {
                    $cont++;
                    $data_insertada->execute(array(

                        't_direcciones_iddir'   => $scpdv3["iddir"],
                        't_unidadminera_idum'   => $scpcv3["idum"]
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
       // echo($cont1.' '.'Direccion de U Minera insertados - dir_UMinera')."\n";
        echo($cont.' '.'Direccion de U Minera insertados - dir_UMinera');
    }
}

$data = new getdata_scpv2UM_scpv3();
$data->getDataActualizaroRegistrar();
