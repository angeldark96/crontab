<?php
define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class clientesComercial extends conexioSQL
{

  public function datosExcel()
  {
    $query = "select tc.idcon ,tc.nombre,tc.apaterno ,tc.amaterno ,tc.cliente,cli.item   from contacto.tbl_contacto tc
        left join cliente.clientes2 cli on tc.cliente = cli.razon_social 
        where tc.nombre not IN ('') order by 1;";
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

    $data_insertada = $conexionHolistics->prepare("INSERT INTO scliente.t_clicont (
                                                                    t_cliente_idcliente,
                                                                    t_contactos_idcont,
                                                                    fregistroclicont,
                                                                    estadocontcli,
                                                                    ffinclicont
                                                                    ) VALUES (
                                                                    :t_cliente_idcliente,
                                                                    :t_contactos_idcont,
                                                                    :fregistroclicont,
                                                                    :estadocontcli,
                                                                    :ffinclicont
                                                                     )");

    foreach ($datacomercial as $dcomercial) :

      $data_insertada->execute(array(
        't_cliente_idcliente' =>  $this->cliente(trim($dcomercial["item"])),
        't_contactos_idcont'  =>  $this->contacto(trim($dcomercial["idcon"])),
        'fregistroclicont'    =>  $fecha_actual,
        'estadocontcli'       =>  0,
        'ffinclicont'         =>  $fecha_actual
      ));

      $contar_registrados++;
    endforeach;
    echo ($contar_registrados . ' ' . 'clientes insertados') . "\n";
    // echo ($contar_actualizados . ' ' . 'clientes actualizados');
  }

  public function cliente($pk)
  {
    $query_area = "SELECT idcliente FROM scliente.t_cliente WHERE codmigracli =$pk";
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
//$data->contexto('');
