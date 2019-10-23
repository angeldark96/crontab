<?php

//! No se migra

require_once '../Crontab/database/postgres_conexion.php';
require_once '../Crontab/database/postgres_scpv3Test_conexion.php';

class getdata_scpv2Contactos_scpv3
{
    use conexionPostgres, conexionTestPostgresdbscpv3;

    public function getDataContactoscpV2()
    {
        $query = "SELECT * FROM tunidadmineracontactos";
        $stmt = $this->conexionpdoPostgres()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll();
        return $listArray;
    }

    public function getDataContactoscpV3()
    {
        $queryv3 = "SELECT *  FROM scliente.t_contactos";
        $stmtv3 = $this->conexionpdoPostgresTestscpv3()->prepare($queryv3);
        $stmtv3->execute();
        $listArrayv3 = $stmtv3->fetchAll();
        return $listArrayv3;
    }

    public function getDataActualizaroRegistrarContactos()
    {
        $datascpv2 =  $this->getDataContactoscpV2();
        $datascpv3 =  $this->getDataContactoscpV3();
        $conexionSCPv3 = $this->conexionpdoPostgresTestscpv3();
        $cont = 0;
        $cont1 = 0;
        $fecha_actual = date("Y-m-d");

        $data_insertada = $conexionSCPv3->prepare("INSERT INTO scliente.t_contactos (apaternocont,amaternocont,nombrescont,t_cargo_idcarg,
                                                                    correocontac,fregistrocont,estadocont,codmigracont) VALUES (
                                                                    :apaternocont,:amaternocont,:nombrescont,:t_cargo_idcarg,
                                                                    :correocontac,:fregistrocont,:estadocont,:codmigracont)");

        $data_actualizada = $conexionSCPv3->prepare("UPDATE scliente.t_contactos  SET 
                                                                    apaternocont = :apaternocont,
                                                                    amaternocont = :amaternocont,
                                                                    nombrescont = :nombrescont,
                                                                    t_cargo_idcarg = :t_cargo_idcarg,
                                                                    correocontac = :correocontac,
                                                                    fregistrocont = :fregistrocont,
                                                                    estadocont = :estadocont,
                                                                    codmigracont = :codmigracont
                                                                    WHERE  codmigracont = :codmigracont
                                                ");

        foreach ($datascpv2 as $scpv2) :
            foreach ($datascpv3 as $scpv3) :
                if ($scpv2['cunidadmineracontacto'] == $scpv3['codmigracont']) {
                    $cont++;
                    $data_actualizada->execute(array(
                        'apaternocont' => ucwords(mb_strtolower($scpv2["apaterno"])),
                        'amaternocont' => ucwords(mb_strtolower($scpv2["amaterno"])),
                        'nombrescont' =>  ucwords(mb_strtolower($scpv2["nombres"])),
                        't_cargo_idcarg' => $this->capturarCargo($scpv2["ccontactocargo"]),
                        'correocontac' => mb_strtolower($scpv2["email"]),
                        'fregistrocont' => $fecha_actual,
                        'estadocont' => 0,
                        'codmigracont' => $scpv2['cunidadmineracontacto']

                    ));
                    continue 2;
                }
            endforeach;

            $data_insertada->execute(array(
                'apaternocont' => ucwords(mb_strtolower($scpv2["apaterno"])),
                'amaternocont' => ucwords(mb_strtolower($scpv2["amaterno"])),
                'nombrescont' => ucwords(mb_strtolower($scpv2["nombres"])),
                't_cargo_idcarg' => $this->capturarCargo($scpv2["ccontactocargo"]),
                'correocontac' => mb_strtolower($scpv2["email"]),
                'fregistrocont' => $fecha_actual,
                'estadocont' => 0,
                'codmigracont' => $scpv2['cunidadmineracontacto']
            ));
            $cont1++;
        endforeach;
        echo($cont1.' '.'contactos insertados')."\n";
        echo($cont.' '.'contactos actualizados');
    }

    public function capturarCargo($idcargo)
    {
        $queryv3 = "SELECT *  FROM scliente.t_cargo where codmigracargo = '$idcargo'";
        $stmtv3 = $this->conexionpdoPostgresTestscpv3()->prepare($queryv3);
        $stmtv3->execute();
        $listArrayv3 = $stmtv3->fetch(); 
        
        return $listArrayv3[0];
    }
}

$data = new getdata_scpv2Contactos_scpv3();
$data->getDataActualizaroRegistrarContactos();
