<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class DireccionesClientesComercial extends conexioSQL
{

  public function datosdireccionesExcel()
  {
    $query = "select d.* from cliente.clientes2 cli2
inner join cliente.direcciones d on cli2.item = d.iddir 
ORDER BY 1;";
    $stmt = $this->conexionpdoLocalComercialExcel()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
    //print_r ($listArray);
    return $listArray;
  }

  public function datosdireccionesHolistics()
  {
    $query = "select * from scliente.t_direcciones";
    $stmt = $this->conexionpdoPostgresProductionHolistics()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
   //  print_r ($listArray);
    return $listArray;
  }

  public function dataActualizaroRegistrarDireccionesClientes()
  {
    $datacomercial         = $this->datosdireccionesExcel();
    $datasclienteHolistics = $this->datosdireccionesHolistics();
    $conexionHolistics     = $this->conexionpdoPostgresProductionHolistics();
    $contar_actualizados   = 0;
    $contar_registrados    = 0;
    $fecha_actual          = date("Y-m-d");

    $data_insertada = $conexionHolistics->prepare("INSERT INTO scliente.t_direcciones (
                                                                    tipodir,
                                                                    direcciondir,
                                                                    t_ubigeo_idubi,
                                                                    t_pais_idpais
                                                                    ) VALUES (
                                                                    :tipodir,
                                                                    :direcciondir,
                                                                    :t_ubigeo_idubi,
                                                                    :t_pais_idpais)");

    $data_actualizada = $conexionHolistics->prepare("UPDATE scliente.t_direcciones  SET 
                                                                                tipodir          = :tipodir,
                                                                                direcciondir     = :direcciondir,
                                                                                t_ubigeo_idubi   = :t_ubigeo_idubi,
                                                                                t_pais_idpais    = :t_pais_idpais
                                                                          WHERE iddir = :iddir

                                                ");

    foreach ($datacomercial as $dcomercial) :
      foreach ($datasclienteHolistics as $dholistics) :
        if (intval($dcomercial['iddir']) == intval($dholistics['iddir'])) {
          $contar_actualizados++;
          $data_actualizada->execute(array(
            'tipodir'        => 1,
            'direcciondir'   => ucwords(mb_strtolower($dcomercial["direccion"])),
            't_ubigeo_idubi' =>  $this->ubigeo($dcomercial["distrito"]),
            't_pais_idpais'  => 1,
            'iddir'          => $dholistics['iddir']
          ));
          continue 2;
        }
      endforeach;

      $data_insertada->execute(array(
        'tipodir'        => 1,
        'direcciondir'   =>  ucwords(mb_strtolower($dcomercial["direccion"])),
        't_ubigeo_idubi' => $this->ubigeo($dcomercial["distrito"]),
        't_pais_idpais'  => 1,
      ));

      $contar_registrados++;
    endforeach;
    echo ($contar_registrados . ' ' . 'Direcciones clientes insertados') . "\n";
    echo ($contar_actualizados . ' ' . ' Direcciones clientes actualizados');
  }


  public function ubigeo($ubi)
  {
    $query_ubigeo = "SELECT idubi FROM scliente.t_ubigeo WHERE   upper(translate(nomubi,'áéíóúÁÉÍÓÚäëïöüÄËÏÖÜ','aeiouAEIOUaeiouAEIOU')) = '$ubi' and  codubi not ilike '%00'";
    $ubi = $this->conexionpdoPostgresProductionHolistics()->prepare($query_ubigeo);
    $ubi->execute();
    $capurarubigeo = $ubi->fetch();
    //var_dump($capurarempresa[0]);
    return $capurarubigeo[0] ? $capurarubigeo[0] : 16;
  }
}

$data = new DireccionesClientesComercial();
$data->dataActualizaroRegistrarDireccionesClientes();
//$data->TipoEmpresa('Empresa Estatal de Derecho Privado');