<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class clientesComercial extends conexioSQL
{

  public function datosExcel()
  {
    $query = "select  tc.idcon,tc.nombre , tc.apaterno, tc.amaterno,tel.* from contacto.tbl_contacto tc
        left join contacto.telefono tel on tc.idcon = tel.idcontel 
        where tc.nombre not IN ('') 
        and tel.numero is not null 
        and tel.numero != ' '
        order by 1;";
    $stmt = $this->conexionpdoLocalComercialExcel()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
    //print_r ($listArray);
    return $listArray;
  }


  public function dataActualizaroRegistrarClientes()
  {
    $datacomercial         = $this->datosExcel();
    $conexionHolistics     = $this->conexionpdoPostgresProductionHolistics();
    $contar_actualizados   = 0;
    $contar_registrados    = 0;
    $fecha_actual          = date("Y-m-d");

    $data_insertada = $conexionHolistics->prepare("INSERT INTO scliente.t_telefono (
                                                                    tipotel,
                                                                    numerotel,
                                                                    anexotel,
                                                                    t_contactos_idcont,
                                                                    maintel
                                                                    ) VALUES (
                                                                    :tipotel,
                                                                    :numerotel,
                                                                    :anexotel,
                                                                    :t_contactos_idcont,
                                                                    :maintel
                                                                     )");

    foreach ($datacomercial as $dcomercial) :

      $data_insertada->execute(array(
        'tipotel'            => trim($dcomercial["tipo"]) == 'Celular'?'1':'2',
        'numerotel'          => trim($dcomercial["numero"]),
        'anexotel'           => trim($dcomercial["anexo"]),
        't_contactos_idcont' =>  $this->contacto(trim($dcomercial["idcon"])),
        'maintel'            =>  trim($dcomercial["tipo"]) == 'Celular'?1:0,
      ));

      $contar_registrados++;
    endforeach;
    echo ($contar_registrados . ' ' . 'clientes insertados') . "\n";
    // echo ($contar_actualizados . ' ' . 'clientes actualizados');
  }


  public function contacto($pk)
  {
      $query_area = "SELECT idcont FROM scliente.t_contactos WHERE codmigracont =$pk";
      $area = $this->conexionpdoPostgresProductionHolistics()->prepare($query_area);
      $area->execute();
      $capuraarea = $area->fetch();
      return  $capuraarea[0] ?  $capuraarea[0] : null;
  }

}

$data = new clientesComercial();
$data->dataActualizaroRegistrarClientes();
//$data->contexto('');
