<?php

require_once '../Crontab/database/conexionesdb.php';

class getdata_scpv2UM_scpv3 extends conexioSQL

{
    


    public function getDataClientescpV3()
    {
        $query = "SELECT * FROM scliente.t_cliente";
        $stmt = $this->conexionpdoPostgresLocalSCPv3()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll();
        return $listArray;
    }

    public function getDataDireccionescpV3()
    {
        $query = "SELECT * FROM scliente.t_direcciones";
        $stmt = $this->conexionpdoPostgresLocalSCPv3()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetchAll();
        return $listArray;
    }

    public function getDataExistsClienteinClidir($idCliente)
    {
        $query = "SELECT * FROM scliente.t_clidir where t_cliente_idcliente = $idCliente ";
        $stmt = $this->conexionpdoPostgresLocalSCPv3()->query($query);
        $row_count = $stmt->rowCount();
        $res = ($row_count > 0) ? 'data' : 'sin_data';
        return $res;
    }

    public function getDataActualizaroRegistrar()
    {
        $dataCliente_scpv3 =  $this->getDataClientescpV3();
        $dataDirecciones_scpv3 =  $this->getDataDireccionescpV3();

        $conexionSCPv3 = $this->conexionpdoPostgresLocalSCPv3();
        $cont = 0;
        $cont1 = 0;


        $data_insertada = $conexionSCPv3->prepare("INSERT INTO scliente.t_clidir (t_direcciones_iddir,
                                                                                 t_cliente_idcliente
                                                                                 ) VALUES (
                                                                                :t_direcciones_iddir,
                                                                                :t_cliente_idcliente
                                                                                 )");

        $data_actualizada = $conexionSCPv3->prepare("UPDATE scliente.t_clidir  SET 
                                                                    t_direcciones_iddir         = :t_direcciones_iddir,
                                                                    t_cliente_idcliente        = :t_cliente_idcliente
                                                                    WHERE  t_cliente_idcliente  = :t_cliente_idcliente
                                                   ");
                                                   
        foreach ($dataDirecciones_scpv3 as $scpdv3) :
            foreach ($dataCliente_scpv3 as $scpcv3) :
                if ($scpcv3['codmigracli'] == $scpdv3['codmigradirecciones'] &&  $this->getDataExistsClienteinClidir($scpcv3["idcliente"]) == 'sin_data'  ) {
                    $cont++;
                      $data_insertada->execute(array(
                        't_direcciones_iddir'   => $scpdv3["iddir"],
                        't_cliente_idcliente'   => $scpcv3["idcliente"]
                    )); 

                    continue 2;
                }
               
            endforeach;


          
        endforeach;
        
        echo ($cont . ' ' . 'Direccion de Clientes insertados - dir_cliente')."\n";
      //  echo($cont1.' '.'Direccion de Clientes actualizados - dir_cliente');
    }
}

$data = new getdata_scpv2UM_scpv3();
$data->getDataActualizaroRegistrar();

//echo($data->getDataExistsClienteinClidir(68));
