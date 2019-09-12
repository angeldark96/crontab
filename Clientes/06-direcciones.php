<?php

require_once '../Crontab/database/postgres_conexion.php';
require_once '../Crontab/database/postgres_scpv3Test_conexion.php';
//require_once '../Crontab/database/pg_tblog_conexion.php';

class getdata_scpv2UM_scpv3
{
    use conexionPostgres, conexionTestPostgresdbscpv3;

    public function getDataDireccionescpV2()
    {
        // Direcciones d los clientes
        $query = "SELECT * FROM tpersonajuridicainformacionbasica ORDER BY cpersona";
        $stmt = $this->conexionpdoPostgres()->query($query);
        $direccionesClientes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($direccionesClientes, $row['cpersona']);
        }
        $in_direccionesClientes = implode(',', $direccionesClientes);

        // direcciones de las unidades Mineras

        $queryUM = "SELECT * FROM tunidadminera ORDER BY cunidadminera";
        $stmtUM = $this->conexionpdoPostgres()->query($queryUM);
        $direccionesUM = [];
        while ($row = $stmtUM->fetch(PDO::FETCH_ASSOC)) {
            array_push($direccionesUM, $row['cunidadminera']);
        }


        // Conseguir direcion, referencia, numero , pais , cubigeo del cliente

        $query = "SELECT * FROM tpersonadirecciones where cpersona in ($in_direccionesClientes)";
        $pdo_test = $this->conexionpdoPostgres()->query($query);

        $direccionesfullCliente = [];

        while ($row1 = $pdo_test->fetch(PDO::FETCH_ASSOC)) {
            array_push($direccionesfullCliente, $row1);
        }

        return $direccionesfullCliente;

        //print_r($direccionesfullCliente);


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
        $datascpv2 =  $this->getDataDireccionescpV2();
        $datascpv3 =  $this->getDataDireccionescpV3();
        $datascpUMv2 = $this->getDataDireccionesUMcpV2();

        $conexionSCPv3 = $this->conexionpdoPostgresTestscpv3();
        $cont = 0;
        $cont1 = 0;
        $cont2 = 0;
        $cont3 = 0;


        $data_insertada = $conexionSCPv3->prepare("INSERT INTO scliente.t_direcciones ( tipodir,
                                                                                        direcciondir,
                                                                                        numerodir,
                                                                                        referenciadir,
                                                                                        t_ubigeo_idubi,
                                                                                        t_pais_idpais,
                                                                                        codmigradirecciones
                                                                                        ) VALUES (
                                                                                        :tipodir,
                                                                                        :direcciondir,
                                                                                        :numerodir,
                                                                                        :referenciadir,
                                                                                        :t_ubigeo_idubi,
                                                                                        :t_pais_idpais,
                                                                                        :codmigradirecciones
                                                                     )");

        $data_actualizada = $conexionSCPv3->prepare("UPDATE scliente.t_direcciones  SET 
                                                                    tipodir            = :tipodir,
                                                                    direcciondir       = :direcciondir,
                                                                    numerodir          = :numerodir,
                                                                    referenciadir      = :referenciadir,
                                                                    t_ubigeo_idubi     = :t_ubigeo_idubi,
                                                                    t_pais_idpais      = :t_pais_idpais,
                                                                    codmigradirecciones= :codmigradirecciones
                                                                    WHERE  codmigradirecciones  = :codmigradirecciones
                                                   ");

        foreach ($datascpv2 as $scpv2) :
            foreach ($datascpv3 as $scpv3) :
                if ($scpv2['cpersona'] == $scpv3['codmigradirecciones']) {
                    $cont++;
                    $data_actualizada->execute(array(
                        'tipodir'               => 1,
                        'direcciondir'          =>  ucwords(strtolower($scpv2["direccion"])),
                        'numerodir'             => $scpv2["numero"],
                        'referenciadir'         => $scpv2["referencia"],
                        't_ubigeo_idubi'        => $this->capturarUbigeo($scpv2["cubigeo"]),  // Ampliar el tamaño mayor a 5 y que no se null para insertar la data
                        't_pais_idpais'         => $this->capturarPais($scpv2["cpais"]), // Ampliar el tamaño a 5  y que no se null para insertar la data
                        'codmigradirecciones'   => $scpv2['cpersona']
                    ));
                    continue 2;
                }
            endforeach;

            $data_insertada->execute(array(
                'tipodir'               => 1,
                'direcciondir'          =>  ucwords(strtolower($scpv2["direccion"])),
                'numerodir'             => $scpv2["numero"],
                'referenciadir'         => $scpv2["referencia"],
                't_ubigeo_idubi'        => $this->capturarUbigeo($scpv2["cubigeo"]),   // Ampliar el tamaño mayor a 5 y que no se null para insertar la data
                't_pais_idpais'         => $this->capturarPais($scpv2["cpais"]), // Ampliar el tamaño a 5  y que no se null para insertar la data
                'codmigradirecciones'   => $scpv2['cpersona']
            ));
            $cont1++;

        endforeach;

        echo($cont1.' '.'Direcciones de Clientes insertados')."\n";
        echo($cont.' '.'Direcciones de Clientes  actualizados')."\n";


        foreach ($datascpUMv2 as $scpv2UM) :
            foreach ($datascpv3 as $scpv3) :
                if ($scpv2UM['cunidadminera'] == $scpv3['codmigradirecciones']) {
                    $cont2++;
                    $data_actualizada->execute(array(
                        'tipodir'               => 2,
                        'direcciondir'          =>  ucwords(strtolower($scpv2UM["direccion"])),
                        'numerodir'             => null,
                        'referenciadir'         => null,
                        't_ubigeo_idubi'        => $this->capturarUbigeo($scpv2UM["cubigeo"]),  // Ampliar el tamaño mayor a 5 y que no se null para insertar la data
                        't_pais_idpais'         => $this->capturarPais($scpv2UM["cpais"]), // Ampliar el tamaño a 5  y que no se null para insertar la data
                        'codmigradirecciones'   => $scpv2UM['cunidadminera']
                    ));
                    continue 2;
                }
            endforeach;

            $data_insertada->execute(array(
                'tipodir'               => 2,
                'direcciondir'          =>  ucwords(strtolower($scpv2UM["direccion"])),
                'numerodir'             => null,
                'referenciadir'         => null,
                't_ubigeo_idubi'        => $this->capturarUbigeo($scpv2UM["cubigeo"]),  // Ampliar el tamaño mayor a 5 y que no se null para insertar la data
                't_pais_idpais'         => $this->capturarPais($scpv2UM["cpais"]), // Ampliar el tamaño a 5  y que no se null para insertar la data
                'codmigradirecciones'   => $scpv2UM['cunidadminera']
            ));
            $cont3++;

        endforeach;
        echo($cont3.' '.'Direcciones de U. Minera insertados')."\n";
        echo($cont2.' '.'Direcciones de  U. Minera  actualizados');


    }

    public function capturarPais($cpais)
    {
        $querypais = "SELECT idpais  FROM scliente.t_pais WHERE abrpais = '$cpais'";
        $pais = $this->conexionpdoPostgresTestscpv3()->prepare($querypais);
        $pais->execute();
        $capurarPais = $pais->fetch();
        return $capurarPais[0];
    }


    public function capturarUbigeo($cubigeo)
    {
        $queryubigeo = "SELECT *  FROM scliente.t_ubigeo WHERE codubi = '$cubigeo'";
        $ubigeo = $this->conexionpdoPostgresTestscpv3()->prepare($queryubigeo);
        $ubigeo->execute();
        $capurarUbigeo = $ubigeo->fetch();
        return $capurarUbigeo['idubi'];
    }

    public function getDataDireccionesUMcpV2()
    {
        // Conseguir direcion, referencia, numero , pais , cubigeo del cliente
        $queryUM = "SELECT * FROM tunidadminera ORDER BY cunidadminera";
        $stmtUM = $this->conexionpdoPostgres()->query($queryUM);
        $stmtUM->execute();
        $listArray = $stmtUM->fetchAll();
        //print_r($listArray);
        return $listArray;
    }
}

$data = new getdata_scpv2UM_scpv3();
$data->getDataActualizaroRegistrar();
//$data->getDataDireccionesUMcpV2();
