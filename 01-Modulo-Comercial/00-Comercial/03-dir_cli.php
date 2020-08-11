<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class DireccionesClientesComercial extends conexioSQL
{


  public function dataRegistrarDireccionesClientes()
  {
    $conexionHolistics     = $this->conexionpdoPostgresProductionHolistics();
    $contar_actualizados   = 0;
    $contar_registrados    = 0;

    $data_insertada = $conexionHolistics->prepare("INSERT INTO scliente.t_clidir (
                                                                    t_direcciones_iddir,
                                                                    t_cliente_idcliente,
                                                                    fiscal
                                                                    ) VALUES (
                                                                    :t_direcciones_iddir,
                                                                    :t_cliente_idcliente,
                                                                    :fiscal)");
                                                                   


    for ($i=1; $i <= 207; $i++) {
      $data_insertada->execute(array(
        't_direcciones_iddir'        => $i,
        't_cliente_idcliente'   => $i,
        'fiscal' => true
      ));
      echo $i;

     // $contar_registrados++;
    };
   // echo ($contar_registrados . ' ' . 'Direcciones clientes insertados') . "\n";
   // echo ($contar_actualizados . ' ' . ' Direcciones clientes actualizados');
  }
 
}

$data = new DireccionesClientesComercial();
$data->dataRegistrarDireccionesClientes();
//$data->TipoEmpresa('Empresa Estatal de Derecho Privado');