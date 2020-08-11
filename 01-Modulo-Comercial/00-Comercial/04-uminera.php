<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class clientesComercial extends conexioSQL
{
  
  public function datosumExcel()
  {
    $query = "select distinct nombre_um ,estado_um,umimm.pais, cliente,cli.item as idcliente ,departamento,provincia, distrito from uminera.uminera2 umimm
          left join cliente.clientes cli on umimm.cliente = cli.razon_social
          where umimm.nombre_um  not in ('','-')  
          and umimm.nombre_um not in ('Corporativo','General') 
          order by 5;";
    $stmt = $this->conexionpdoLocalComercialExcel()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
    //print_r ($listArray);
    return $listArray;
  }

  public function datosumHolistics()
  {
    $query = "select * from scliente.t_unidadminera";
    $stmt = $this->conexionpdoPostgresProductionHolistics()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
   // print_r ($listArray);
    return $listArray;
  }

  public function dataActualizaroRegistrarClientes()
  {
    $datacomercial         = $this->datosumExcel();
    $datasclienteHolistics = $this->datosumHolistics();
    $conexionHolistics     = $this->conexionpdoPostgresProductionHolistics();
    $contar_actualizados   = 0;
    $contar_registrados    = 0;
    $fecha_actual          = date("Y-m-d");

    $data_insertada = $conexionHolistics->prepare("INSERT INTO scliente.t_unidadminera (
                                                                    nombre_um,
                                                                    estado_um,
                                                                    t_cliente_idcliente,
                                                                    fregistroum
                                                                    ) VALUES (
                                                                    :nombre_um,
                                                                    :estado_um,
                                                                    :t_cliente_idcliente,
                                                                    :fregistroum)");

    foreach ($datacomercial as $dcomercial) :

      $data_insertada->execute(array(
        'nombre_um'           => ucwords(mb_strtolower($dcomercial["nombre_um"])),
        'estado_um'           => 0,
        't_cliente_idcliente' => $this->cliente($dcomercial["idcliente"]),
        'fregistroum'         =>  $fecha_actual
       
      ));

      $contar_registrados++;
    endforeach;
    echo ($contar_registrados . ' ' . 'clientes insertados') . "\n";
    echo ($contar_actualizados . ' ' . 'clientes actualizados');

    
  }


  public function TipoEmpresa($abre)
  {
    $query_empresa = "SELECT idemp FROM scliente.t_tipoempresa WHERE   upper(translate(nomemp,'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ','aeiouAEIOUaeiouAEIOU')) ILIKE '%$abre%' and t_pais_idpais IN (1)";
    $tempresa = $this->conexionpdoPostgresProductionHolistics()->prepare($query_empresa);
    $tempresa->execute();
    $capurarempresa = $tempresa->fetch();
    //var_dump($capurarempresa[0]);
    return $capurarempresa[0] ? $capurarempresa[0] : 16;
  }

  public function cliente($pk)
  {

    $query_area = "SELECT idcliente FROM scliente.t_cliente WHERE codmigracli  = $pk";
    $area = $this->conexionpdoPostgresProductionHolistics()->prepare($query_area);
    $area->execute();
    $capuraarea = $area->fetch();
    return  $capuraarea[0];
  }
}

$data = new clientesComercial();
$data->dataActualizaroRegistrarClientes();
//$data->TipoEmpresa('Empresa Estatal de Derecho Privado');