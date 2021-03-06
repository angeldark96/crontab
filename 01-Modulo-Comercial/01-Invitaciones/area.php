<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class clientesComercial extends conexioSQL
{

  public function datosExcel()
  {
    $query = "select * from invitaciones.areas;";
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

    $data_insertada = $conexionHolistics->prepare("INSERT INTO susuario.t_areas (
                                                                    nombre,
                                                                    estado,
                                                                    idarea_p,
                                                                    idareamigra
                                                                    ) VALUES (
                                                                    :nombre,
                                                                    :estado,
                                                                    :idarea_p,
                                                                    :idareamigra
                                                                     )");

    foreach ($datacomercial as $dcomercial) :
      $data_insertada->execute(array(
        'nombre'             => trim($dcomercial["areas"]),
        'estado'             => trim($dcomercial["estado"]) == 'Activo'?0:1,
        'idarea_p'           => $dcomercial["idareapadre"],
        'idareamigra'        => $dcomercial["idarea"]
        
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
