<?php 

require_once '../Crontab/database/postgres_test_conexion.php';
require_once '../Crontab/database/postgres_scpv3Test_conexion.php';
require_once '../Crontab/database/pg_tblog_conexion.php';

class getdata_scpv2_scpv3
{
    use conexionPostgres_QA, conexionTestPostgresdbscpv3;

    public function getDataClientescpV2()
    {
        $query = "SELECT tpj.cpersona,tpj.razonsocial,tpj.nombrecomercial,tp.abreviatura,tpa.descripcion  FROM tpersonajuridicainformacionbasica tpj
        INNER JOIN tpersona tp on tp.cpersona = tpj.cpersona
        INNER JOIN tpersonadirecciones tpd on tpd.cpersona = tp.cpersona
        INNER JOIN tpais tpa on tpa.cpais = tpd.cpais ";
        $stmt = $this->conexionpdoPostgresTest_QA()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll(); 
        return $listArray;
         //print_r($listArray);
    }


    public function getDataClientescpV3()
    {
        $queryv3 = "SELECT *  FROM scliente.t_cliente";
        $stmtv3 = $this->conexionpdoPostgresTestscpv3()->prepare($queryv3);
        $stmtv3->execute();
        $listArrayv3 = $stmtv3->fetchAll(); 
        return $listArrayv3;
       // print_r($listArrayv3);
    }


    public function getDataActualizaroRegistrar()
    {
        $datascpv2 =  $this->getDataClientescpV2();
        $datascpv3 =  $this->getDataClientescpV3();
        $cont = 0;
        $cont1 = 0;
        foreach($datascpv2 as $scpv2):
            foreach($datascpv3 as $scpv3):
                if ($scpv2['cpersona'] == $scpv3['codigocli']) {
                    $cont++;
                    continue 2;
                }
            endforeach; 
            echo ( $scpv2['cpersona']."\n");  
            $cont1++; 
        endforeach;    
        echo($cont);




        //print_r($datascpv3);

        // $pdo = $this->conexionpdoPostgresTestscpv3();
        // $result_set = $pdo->prepare("INSERT INTO scliente.t_cliente (codigocli,razon_socialcli,nombre_comercialcli,abreviaturacli,
        //                                                             id_tributariacli,fregistrocli,t_tipotrib_idtrib,t_pais_idpais,
        //                                                             t_tipoempresa_idemp,webcli) VALUES (
        //                                                             :codigocli,:razon_socialcli,:nombre_comercialcli,:abreviaturacli,
        //                                                             :id_tributariacli,:fregistrocli,:t_tipotrib_idtrib,:t_pais_idpais,
        //                                                             :t_tipoempresa_idemp:webcli)");
        //         foreach ($data as $row) {
        //             $result_set->execute(array(
        //                 'codigocli' =>$row["codigocli"],
        //                 'razon_socialcli' =>$row["razonsocial"],
        //                 'nombre_comercialcli' =>$row["nombrecomercial"],
        //                 'abreviaturacli' =>$row["abreviatura"],
        //                 'id_tributariacli' =>$row["codigocli"],
        //                 'fregistrocli' =>$row["codigocli"],
        //                 't_tipotrib_idtrib' =>$row["codigocli"],
        //                 't_pais_idpais' =>$row["codigocli"],
        //                 't_tipoempresa_idemp' =>$row["codigocli"],
        //                 'webcli' =>$row["codigocli"],
                        
        //             ));
        //         }
    }
}

$data = new getdata_scpv2_scpv3();
$data->getDataActualizaroRegistrar();
