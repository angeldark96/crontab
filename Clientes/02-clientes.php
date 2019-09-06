<?php 

require_once '../Crontab/database/postgres_test_conexion.php';
require_once '../Crontab/database/postgres_scpv3Test_conexion.php';
//require_once '../Crontab/database/pg_tblog_conexion.php';

class getdata_scpv2_scpv3
{
    use conexionPostgres_QA, conexionTestPostgresdbscpv3;

    public function getDataClientescpV2()
    {
        $query = "SELECT DISTINCT(tpj.cpersona),tpj.razonsocial,tpj.nombrecomercial,tp.abreviatura,tpj.web,tp.identificacion,tpd.cpais  FROM tpersonajuridicainformacionbasica tpj
        LEFT JOIN tpersona tp on tp.cpersona = tpj.cpersona
        LEFT JOIN tpersonadirecciones tpd on tpd.cpersona = tpj.cpersona ORDER BY  tpj.cpersona";
        $stmt = $this->conexionpdoPostgresTest_QA()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll(); 

        // $listArray = [];
        // while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        //     array_push($listArray, $row);
        // }

        return $listArray;
        //print_r($listArray);
    }

    public function getDataClientescpV3()
    {
        $queryv3 = "SELECT *  FROM scliente.t_cliente";
        $stmtv3 = $this->conexionpdoPostgresTestscpv3()->prepare($queryv3);
        $stmtv3->execute();
        $listArrayv3 = $stmtv3->fetchAll(); 

        // $listArrayv3 = [];
        // while ($row = $stmtv3->fetch(PDO::FETCH_ASSOC)) {
        //     array_push($listArrayv3, $row);
        // }

        return $listArrayv3;

       // print_r($listArrayv3);
    }


    public function getDataActualizaroRegistrar()
    {
        $datascpv2 =  $this->getDataClientescpV2();
        $datascpv3 =  $this->getDataClientescpV3();
        $conexionSCPv3 = $this->conexionpdoPostgresTestscpv3();
        $cont = 0;
        $cont1 = 0;
        $fecha_actual = date("Y-m-d");

        $data_insertada =$conexionSCPv3->prepare("INSERT INTO scliente.t_cliente (codmigracli,razon_socialcli,nombre_comercialcli,abreviaturacli,
                                                                    id_tributariacli,fregistrocli,t_tipotrib_idtrib,t_pais_idpais,
                                                                    t_tipoempresa_idemp,webcli) VALUES (
                                                                    :codmigracli,:razon_socialcli,:nombre_comercialcli,:abreviaturacli,
                                                                    :id_tributariacli,:fregistrocli,:t_tipotrib_idtrib,:t_pais_idpais,
                                                                    :t_tipoempresa_idemp,:webcli)"); 

        $data_actualizada = $conexionSCPv3->prepare("UPDATE scliente.t_cliente  SET 
                                                                    codmigracli = :codmigracli,
                                                                    razon_socialcli = :razon_socialcli,
                                                                    nombre_comercialcli = :nombre_comercialcli,
                                                                    abreviaturacli = :abreviaturacli,
                                                                    id_tributariacli = :id_tributariacli,
                                                                    fregistrocli = :fregistrocli,
                                                                    t_tipotrib_idtrib = :t_tipotrib_idtrib,
                                                                    t_pais_idpais = :t_pais_idpais,
                                                                    t_tipoempresa_idemp = :t_tipoempresa_idemp,
                                                                    webcli = :webcli
                                                                    WHERE  codmigracli = :codmigracli

                                                ");                                                             

        foreach($datascpv2 as $scpv2):
            foreach($datascpv3 as $scpv3):
                if ($scpv2['cpersona'] == $scpv3['codmigracli']) {
                    $cont++;
                     $data_actualizada->execute(array(
                    'codmigracli' =>$scpv2["cpersona"],
                    'razon_socialcli' => ucwords(strtolower($scpv2["razonsocial"])),
                    'nombre_comercialcli' => ucwords(strtolower($scpv2["nombrecomercial"])),
                    'abreviaturacli' =>'', // Ampliar el tamaño del campo de abreviaturacli
                   // 'abreviaturacli' =>$scpv2["abreviatura"],
                    'id_tributariacli' =>$scpv2["identificacion"],  
                    'fregistrocli' => $fecha_actual,
                    't_tipotrib_idtrib' =>1, // Data de prueba => 1
                    't_pais_idpais' => $this->capturarPais($scpv2["cpais"]),
                    't_tipoempresa_idemp' =>1, // Data de prueba => 1
                    'webcli' =>$scpv2["web"]
                ));
                    continue 2;
                }
            endforeach; 

           $data_insertada->execute(array(
                    'codmigracli' =>$scpv2["cpersona"],
                    'razon_socialcli' => ucwords(strtolower($scpv2["razonsocial"])),
                    'nombre_comercialcli' => ucwords(strtolower($scpv2["nombrecomercial"])),
                    'abreviaturacli' =>'',// Ampliar el tamaño del campo de abreviaturacli
                   // 'abreviaturacli' =>$scpv2["abreviatura"],
                    'id_tributariacli' =>$scpv2["identificacion"], 
                    'fregistrocli' => $fecha_actual,
                    't_tipotrib_idtrib' => 1, // Data de prueba => 1
                    't_pais_idpais' => $this->capturarPais($scpv2["cpais"]),
                    't_tipoempresa_idemp' => 1, // Data de prueba => 1
                    'webcli' => $scpv2["web"]
                ));
                   
            $cont1++; 
        endforeach;    
        echo($cont1);

    }

    public function capturarPais($cpais)
    {
        $querypais = "SELECT idpais  FROM scliente.t_pais WHERE abrpais = '$cpais'";
        $pais = $this->conexionpdoPostgresTestscpv3()->prepare($querypais);
        $pais->execute();
        $capurarPais = $pais->fetch(); 
        $indicarPais =  $capurarPais ?  $capurarPais[0] : 1;
        return $indicarPais;
       
    }

    // public function capturarTipoEmpresa($cpais)
    // {
    //     $querypais = "SELECT abrtrib  FROM scliente.t_tipotrib WHERE t_pais_idpais = $cpais";
    //     $pais = $this->conexionpdoPostgresTestscpv3()->prepare($querypais);
    //     $pais->execute();
    //     $capurarPais = $pais->fetch(); 
    //     return $capurarPais[0];
    // }
}

$data = new getdata_scpv2_scpv3();
$data->getDataActualizaroRegistrar();
