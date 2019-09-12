<?php

require_once '../Crontab/database/postgres_conexion.php';
require_once '../Crontab/database/postgres_scpv3Test_conexion.php';
//require_once '../Crontab/database/pg_tblog_conexion.php';

class getdata_scpv2UM_scpv3
{
    use conexionPostgres, conexionTestPostgresdbscpv3;

    public function getDataUMscpV2()
    {
        $query = "SELECT * FROM tunidadminera  ORDER BY cpersona";
        $stmt = $this->conexionpdoPostgres()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll();
        return $listArray;
    }

    public function getDataUMscpV3()
    {
        $queryv3 = "SELECT *  FROM scliente.t_unidadminera";
        $stmtv3 = $this->conexionpdoPostgresTestscpv3()->prepare($queryv3);
        $stmtv3->execute();
        $listArrayv3 = $stmtv3->fetchAll();
        return $listArrayv3;
    }

    public function getDataUMActualizaroRegistrar()
    {
        $datascpv2 =  $this->getDataUMscpV2();
        $datascpv3 =  $this->getDataUMscpV3();
        $conexionSCPv3 = $this->conexionpdoPostgresTestscpv3();
        $cont = 0;
        $cont1 = 0;
        $fecha_actual = date("Y-m-d");

        $data_insertada = $conexionSCPv3->prepare("INSERT INTO scliente.t_unidadminera (nombre_um,
                                                                                       estado_um,
                                                                                       codigo_um,
                                                                                       logo_um,
                                                                                       t_cliente_idcliente,
                                                                                       fregistroum,
                                                                                       codmigraum) VALUES (
                                                                                        :nombre_um,
                                                                                        :estado_um,
                                                                                        :codigo_um,
                                                                                        :logo_um,
                                                                                        :t_cliente_idcliente,
                                                                                        :fregistroum,
                                                                                        :codmigraum
                                                                                        )");

        $data_actualizada = $conexionSCPv3->prepare("UPDATE scliente.t_unidadminera  SET 
                                                                    nombre_um           = :nombre_um,
                                                                    estado_um           = :estado_um,
                                                                    codigo_um           = :codigo_um,
                                                                    logo_um             = :logo_um,
                                                                    t_cliente_idcliente = :t_cliente_idcliente,
                                                                    fregistroum         = :fregistroum,
                                                                    codmigraum         = :codmigraum
                                                                    WHERE  codmigraum    = :codmigraum

                                                ");

        foreach ($datascpv2 as $scpv2) :
            foreach ($datascpv3 as $scpv3) :
                if ($scpv2['cunidadminera'] == $scpv3['codmigraum']) {
                    $cont++;
                    $data_actualizada->execute(array(
                        'nombre_um' => $scpv2['nombre'],
                        'estado_um' => $scpv2['cumineraestado'] == '001' ? 0 : 1,
                        'codigo_um' => '', // la data del SCP supera los 6 digitos que es el rango de la SCPv3
                        'logo_um' => '',   // no lo guardan en el campo logo_unidadminera 
                        't_cliente_idcliente' => $this->getDataClienteUM($scpv2["cpersona"]),
                        'fregistroum' => $fecha_actual,
                        'codmigraum' => $scpv2['cunidadminera']
                    ));
                    continue 2;
                }
            endforeach;

            $data_insertada->execute(array(
                'nombre_um' => $scpv2['nombre'],
                'estado_um' => $scpv2['cumineraestado'] == '001' ? 0 : 1,
                'codigo_um' => '', // la data del SCP supera los 6 digitos que es el rango de la SCPv3
                'logo_um' => '', // no lo guardan en el campo logo_unidadminera 
                't_cliente_idcliente' => $this->getDataClienteUM($scpv2["cpersona"]),
                'fregistroum' => $fecha_actual,
                'codmigraum' => $scpv2['cunidadminera']
            ));

            $cont1++;
        endforeach;
        echo($cont1.' '.'U. Minera insertados')."\n";
        echo($cont.' '.'U. Minera actualizados');
    }

    public function getDataClienteUM($idcliente)
    {

        $queryv3 = "SELECT *  FROM scliente.t_cliente where codmigracli = $idcliente";
        $stmtv3 = $this->conexionpdoPostgresTestscpv3()->prepare($queryv3);
        $stmtv3->execute();
        $listArrayv3 = $stmtv3->fetch();

        return $listArrayv3[0];
    }
}

$data = new getdata_scpv2UM_scpv3();
$data->getDataUMActualizaroRegistrar();
