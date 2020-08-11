<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class clientesComercial extends conexioSQL
{

  public function datosExcel()
  {
    $query = "select tc.idcon ,tc.nombre,tc.apaterno ,tc.amaterno ,tc.cliente,cli.item,tc.uminera   from contacto.tbl_contacto tc
        left join cliente.clientes2 cli on tc.cliente = cli.razon_social 
        where tc.nombre not IN ('') 
        and tc.uminera is not null and  tc.uminera != '-'
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

    $data_insertada = $conexionHolistics->prepare("INSERT INTO scliente.t_umcontacto (
                                                                    t_unidadminera_idum,
                                                                    t_contactos_idcont,
                                                                    fregistroumcont,
                                                                    estadocontum,
                                                                    ffinumcont
                                                                    ) VALUES (
                                                                    :t_unidadminera_idum,
                                                                    :t_contactos_idcont,
                                                                    :fregistroumcont,
                                                                    :estadocontum,
                                                                    :ffinumcont
                                                                     )");

    foreach ($datacomercial as $dcomercial) :

      $data_insertada->execute(array(
        't_unidadminera_idum' =>  $this->uminera(trim($dcomercial["item"]), $dcomercial["uminera"]),
        't_contactos_idcont'  =>  $this->contacto(trim($dcomercial["idcon"])),
        'fregistroumcont'    =>  $fecha_actual,
        'estadocontum'       =>  0,
        'ffinumcont'         =>  $fecha_actual
      ));

      $contar_registrados++;
    endforeach;
    echo ($contar_registrados . ' ' . 'clientes insertados') . "\n";
    // echo ($contar_actualizados . ' ' . 'clientes actualizados');
  }

  public function uminera($idcliente,$uminera)
  {
    $query_area = "SELECT idum FROM scliente.t_unidadminera WHERE t_cliente_idcliente =$idcliente and  nombre_um ilike '%$uminera%'";
    $area = $this->conexionpdoPostgresProductionHolistics()->prepare($query_area);
    $area->execute();
    $capuraarea = $area->fetch();
    return  $capuraarea[0] ?  $capuraarea[0] : null;
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
//$data->uminera(1, 'Amsac');
