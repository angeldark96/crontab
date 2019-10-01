<?php 

require_once '../Crontab/database/postgres_conexion.php';
require_once '../Crontab/database/postgres_scpv3Test_conexion.php';
//require_once '../Crontab/database/pg_tblog_conexion.php';

class getdata_scpv2_scpv3
{
    use conexionPostgres, conexionTestPostgresdbscpv3;

    public function getDataClientescpV2()
    {
        $query = "SELECT DISTINCT(tpj.cpersona),tpj.razonsocial,tpj.nombrecomercial,tp.abreviatura,tpj.web,tp.identificacion,tpd.cpais  FROM tpersonajuridicainformacionbasica tpj
        LEFT JOIN tpersona tp on tp.cpersona = tpj.cpersona
        LEFT JOIN tpersonadirecciones tpd on tpd.cpersona = tpj.cpersona ORDER BY  tpj.cpersona";
        $stmt = $this->conexionpdoPostgres()->prepare($query);
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
                    'razon_socialcli' => ucwords(mb_strtolower($scpv2["razonsocial"])),
                    'nombre_comercialcli' => ucwords(mb_strtolower($scpv2["nombrecomercial"])),
                    'abreviaturacli' =>'', // Ampliar el tamaño del campo de abreviaturacli
                   // 'abreviaturacli' =>$scpv2["abreviatura"],
                    'id_tributariacli' =>$scpv2["identificacion"],  
                    'fregistrocli' => $fecha_actual,
                    't_tipotrib_idtrib' => $this->TipoTributo($scpv2["cpais"]),
                    't_pais_idpais' => $this->capturarPais($scpv2["cpais"]),
                    't_tipoempresa_idemp' => $this->TipoEmpresaSegunRazonSocial($this->capturarTipoEmpresa($scpv2["cpersona"])),
                    'webcli' =>$scpv2["web"]
                ));
                    continue 2;
                }
            endforeach; 

           $data_insertada->execute(array(
                    'codmigracli' =>$scpv2["cpersona"],
                    'razon_socialcli' => ucwords(mb_strtolower($scpv2["razonsocial"])),
                    'nombre_comercialcli' => ucwords(mb_strtolower($scpv2["nombrecomercial"])),
                    'abreviaturacli' =>'',// Ampliar el tamaño del campo de abreviaturacli
                   // 'abreviaturacli' =>$scpv2["abreviatura"],
                    'id_tributariacli' =>$scpv2["identificacion"], 
                    'fregistrocli' => $fecha_actual,
                    't_tipotrib_idtrib' => $this->TipoTributo($scpv2["cpais"]),
                    't_pais_idpais' => $this->capturarPais($scpv2["cpais"]),
                    't_tipoempresa_idemp' => $this->TipoEmpresaSegunRazonSocial($this->capturarTipoEmpresa($scpv2["cpersona"])),
                    'webcli' => $scpv2["web"]
                ));
                   
            $cont1++; 
        endforeach;    
        echo($cont1.' '.'clientes insertados')."\n";
        echo($cont.' '.'clientes actualizados');

    }

    public function capturarPais($cpais)
    {
        $indicarPais = 1; 
        if($cpais!=null || $cpais!='')
        {
            $querypais = "SELECT idpais  FROM scliente.t_pais WHERE abrpais = '$cpais'";
            $pais = $this->conexionpdoPostgresTestscpv3()->prepare($querypais);
            $pais->execute();
            $capurarPais = $pais->fetch(); 
            $indicarPais = $capurarPais[0];
        }
        else{
            $indicarPais = 1;
        }
        //echo $indicarPais;;
        return $indicarPais;
       
    }

    public function capturarTipoEmpresa($codmigracli)
    {
        $query_razon = "SELECT razon_socialcli  FROM scliente.t_cliente WHERE codmigracli = $codmigracli";

        // $querypais = "SELECT abremp  FROM scliente.t_tipoempresa WHERE abremp = '$razonSocial'";
        $tempresa = $this->conexionpdoPostgresTestscpv3()->prepare($query_razon);
        $tempresa->execute();
        $capurarempresa = $tempresa->fetch();
        $quitarespaciosEmpresa = trim($capurarempresa[0]);
        $conver = explode(" ",$quitarespaciosEmpresa);
        $ultimoelemento = array_pop($conver);
        $convertirMayuscula = strtoupper($ultimoelemento);
        return  $convertirMayuscula;
    }

    public function TipoEmpresaSegunRazonSocial($abre)
    {
       $query_empresa = "SELECT idemp FROM scliente.t_tipoempresa WHERE abremp = '$abre'";
       $tempresa = $this->conexionpdoPostgresTestscpv3()->prepare($query_empresa);
       $tempresa->execute();
       $capurarempresa = $tempresa->fetch();
       // echo( $capurarempresa[0]);
      return $capurarempresa[0] ? $capurarempresa[0] : 9;
       
    }

    public function TipoTributo($abrpais)
    {
       $query_pais = "SELECT idpais FROM scliente.t_pais WHERE abrpais = '$abrpais'";
       $idpais = $this->conexionpdoPostgresTestscpv3()->prepare($query_pais);
       $idpais->execute();
       $capurarpais = $idpais->fetch();
       $dataid_pais = $capurarpais[0] ? $capurarpais[0] : 7;

       $query_tipotrib = "SELECT idtrib FROM scliente.t_tipotrib WHERE t_pais_idpais = $dataid_pais";
       $abrtrib = $this->conexionpdoPostgresTestscpv3()->prepare($query_tipotrib);
       $abrtrib->execute();
       $capurartributo = $abrtrib->fetch();
       //echo($capurartributo[0]); 
      return $capurartributo[0] ?  $capurartributo[0] : 1;
       
    }
}

$data = new getdata_scpv2_scpv3();
$data->getDataActualizaroRegistrar();
//$data->capturarTipoEmpresa(4435);
//$data->TipoTributo('RUS');
