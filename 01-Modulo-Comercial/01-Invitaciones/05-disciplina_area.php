<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class clientesComercial extends conexioSQL
{

  public function datosExcel()
  {
    $query = "select * from invitaciones.disciplina_area;";
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

    $data_insertada = $conexionHolistics->prepare("INSERT INTO susuario.t_areadisc (
                                                                    idarea,
                                                                    iddisc,
                                                                    idscc
                                                                    ) VALUES (
                                                                    :idarea,
                                                                    :iddisc,
                                                                    :idscc
                                                                     )");

    foreach ($datacomercial as $dcomercial) :
      $data_insertada->execute(array(
        'idarea' => $this->area($dcomercial["idarea"]),
        'iddisc' => $this->disciplina($dcomercial["iddisciplina"]),
        'idscc' => $this->scc($dcomercial["idscc"])
      ));

      $contar_registrados++;
    endforeach;
    echo ($contar_registrados . ' ' . 'clientes insertados') . "\n";
    // echo ($contar_actualizados . ' ' . 'clientes actualizados');
  }
  public function area($idarea)
  {
    $query_empresa = "SELECT idarea FROM susuario.t_areas WHERE idarea =$idarea ";
    $tempresa = $this->conexionpdoPostgresLocalSCPv3()->prepare($query_empresa);
    $tempresa->execute();
    $capurarempresa = $tempresa->fetch();
    //var_dump($capurarempresa[0]);
    return $capurarempresa[0];
  }
  public function disciplina($iddisc)
  {
    $query_empresa = "SELECT iddisc FROM susuario.t_disciplinas WHERE iddisc =$iddisc ";
    $tempresa = $this->conexionpdoPostgresLocalSCPv3()->prepare($query_empresa);
    $tempresa->execute();
    $capurarempresa = $tempresa->fetch();
    //var_dump($capurarempresa[0]);
    return $capurarempresa[0];
  }

  public function scc($idscc)
  {
    $query_empresa = "SELECT idscc FROM sfinanzas.t_scc WHERE idscc =$idscc ";
    $tempresa = $this->conexionpdoPostgresLocalSCPv3()->prepare($query_empresa);
    $tempresa->execute();
    $capurarempresa = $tempresa->fetch();
    //var_dump($capurarempresa[0]);
    return $capurarempresa[0];
  }
}

$data = new clientesComercial();
$data->dataActualizaroRegistrarClientes();
//$data->contexto('');
