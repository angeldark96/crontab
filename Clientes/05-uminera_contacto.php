<?php

// !No migrarse

require_once '../Crontab/database/postgres_conexion.php';
require_once '../Crontab/database/postgres_scpv3Test_conexion.php';
//require_once '../Crontab/database/pg_tblog_conexion.php';

class getdata_scpv2UM_scpv3
{
    use conexionPostgres, conexionTestPostgresdbscpv3;

    public function getDataUnidadMineraContactoscpV2()
    {
        $query = "SELECT * FROM tunidadmineracontactos";
        $stmt = $this->conexionpdoPostgres()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll();
        return $listArray;
    }

    public function getDataUnidadMineraContactoscpV3()
    {
        $query = "SELECT * FROM scliente.t_umcontacto";
        $stmt = $this->conexionpdoPostgresTestscpv3()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll();
        return $listArray;
    }

    public function getDataActualizaroRegistrar()
    {
        $datascpv2 =  $this->getDataUnidadMineraContactoscpV2();
        $datascpv3 =  $this->getDataUnidadMineraContactoscpV3();

        $conexionSCPv3 = $this->conexionpdoPostgresTestscpv3();
        $cont = 0;
        $cont1 = 0;
        $fecha_actual = date("Y-m-d");

        $data_insertada = $conexionSCPv3->prepare("INSERT INTO scliente.t_umcontacto (t_contactos_idcont,
                                                                                        t_unidadminera_idum,
                                                                                        fregistroumcont,
                                                                                        estadocontum,
                                                                                        ffinumcont,
                                                                                        codmigracontacto,
                                                                                        codmigrauminera) VALUES (
                                                                    :t_contactos_idcont,
                                                                    :t_unidadminera_idum,
                                                                    :fregistroumcont,
                                                                    :estadocontum,
                                                                    :ffinumcont,
                                                                    :codmigracontacto,
                                                                    :codmigrauminera)");

        $data_actualizada = $conexionSCPv3->prepare("UPDATE scliente.t_umcontacto  SET 
                                                                    t_contactos_idcont         = :t_contactos_idcont,
                                                                    t_unidadminera_idum        = :t_unidadminera_idum,
                                                                    fregistroumcont            = :fregistroumcont,
                                                                    estadocontum               = :estadocontum,
                                                                    ffinumcont                 = :ffinumcont
                                                                    codmigracontacto           = :codmigracontacto,
                                                                    codmigrauminera            = :codmigrauminera  
                                                                    WHERE  t_contactos_idcont  = :t_contactos_idcont
                                                   ");

        foreach ($datascpv2 as $scpv2) :
            foreach ($datascpv3 as $scpv3) :
                if ($scpv2['cunidadmineracontacto'] == $scpv3['codmigracontacto'] && $scpv2['cunidadminera'] == $scpv3['codmigrauminera']) {
                    $cont++;
                    $data_actualizada->execute(array(
                        't_contactos_idcont'    => $this->capturarIdContacto($scpv2["cunidadmineracontacto"]),
                        't_unidadminera_idum'   => $this->capturarIdUnidadMinera( $scpv2["cunidadminera"]),
                        'fregistroumcont'       => $fecha_actual,
                        'estadocontum'          => 0,
                        'ffinumcont'            => null,
                        'codmigracontacto'      => $scpv2["cunidadmineracontacto"],
                        'codmigrauminera'       => $scpv2["cunidadminera"]
                    ));
                    continue 2;
                }
            endforeach;

            $data_insertada->execute(array(
                't_contactos_idcont'  => $this->capturarIdContacto( $scpv2["cunidadmineracontacto"]),
                't_unidadminera_idum' => $this->capturarIdUnidadMinera( $scpv2["cunidadminera"]),
                'fregistroumcont'     => $fecha_actual,
                'estadocontum'          => 0,
                'ffinumcont'            => null,
                'codmigracontacto'    => $scpv2["cunidadmineracontacto"],
                'codmigrauminera'     => $scpv2["cunidadminera"]
            ));

            $cont1++;
        endforeach;
        echo($cont1.' '.'Contactos de U. Minera insertados')."\n";
        echo($cont.' '.'Contactos de U. Minera  actualizados');
    }

    public function capturarIdContacto($idContacto)
    {
        $query = "SELECT * FROM scliente.t_contactos WHERE codmigracont = $idContacto ";
        $stmt = $this->conexionpdoPostgresTestscpv3()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetch();
        return $listArray['idcont'];
    }
    public function capturarIdUnidadMinera($idUnidadMinera)
    {
        $query = "SELECT * FROM scliente.t_unidadminera WHERE codmigraum = $idUnidadMinera";
        $stmt = $this->conexionpdoPostgresTestscpv3()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetch();
        return $listArray['idum'];
       
    }
}

$data = new getdata_scpv2UM_scpv3();
$data->getDataActualizaroRegistrar();
