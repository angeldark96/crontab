<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class clientesComercial extends conexioSQL
{

  public function datosExcel()
  {
    $query = "select * from invitaciones.scc;";
    $stmt = $this->conexionpdoLocalComercialExcel()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
    //print_r ($listArray);
    return $listArray;
  }


  public function dataActualizaroRegistrarClientes()
  {
    $datacomercial         = $this->datosExcel();
    $conexionHolistics     = $this->conexionpdoPostgresLocalSCPv3();
    $contar_actualizados   = 0;
    $contar_registrados    = 0;
    $fecha_actual          = date("Y-m-d");

    $data_insertada = $conexionHolistics->prepare("INSERT INTO sfinanzas.t_scc (
                                                                    nombscc,
                                                                    codscc,
                                                                    idcc,
                                                                    estadoscc
                                                                    ) VALUES (
                                                                    :nombscc,
                                                                    :codscc,
                                                                    :idcc,
                                                                    :estadoscc
                                                                     )");

    foreach ($datacomercial as $dcomercial) :
      $data_insertada->execute(array(
        'nombscc' => trim($dcomercial["nombscc"]),
        'codscc'  =>$dcomercial["codscc"], 
        'idcc'    => $dcomercial["idcc"],
        'estadoscc'  => trim($dcomercial["estado"]) == 'Activo' ? 0 : 1

      ));

      $contar_registrados++;
    endforeach;
    echo ($contar_registrados . ' ' . 'clientes insertados') . "\n";
    // echo ($contar_actualizados . ' ' . 'clientes actualizados');
  }
}

$data = new clientesComercial();
$data->dataActualizaroRegistrarClientes();
//$data->contexto('');
