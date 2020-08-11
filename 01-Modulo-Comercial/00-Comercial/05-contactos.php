<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class clientesComercial extends conexioSQL
{

  public function datosExcel()
  {
    $query = "select *,corre.correo  from contacto.tbl_contacto tc
        left join contacto.correo corre on tc.idcon = corre.idcon
        where tc.nombre not IN ('') order by 1;";
    $stmt = $this->conexionpdoLocalComercialExcel()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
    //print_r ($listArray);
    return $listArray;
  }

  public function datosHolistics()
  {
    $query = "select * from scliente.t_contactos";
    $stmt = $this->conexionpdoPostgresProductionHolistics()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
    // print_r ($listArray);
    return $listArray;
  }

  public function dataActualizaroRegistrarClientes()
  {
    $datacomercial         = $this->datosExcel();
    $datasclienteHolistics = $this->datosHolistics();
    $conexionHolistics     = $this->conexionpdoPostgresProductionHolistics();
    $contar_actualizados   = 0;
    $contar_registrados    = 0;
    $fecha_actual          = date("Y-m-d");

    $data_insertada = $conexionHolistics->prepare("INSERT INTO scliente.t_contactos (
                                                                    apaternocont,
                                                                    amaternocont,
                                                                    nombrescont,
                                                                    correocontac,
                                                                    fregistrocont,
                                                                    estadocont,
                                                                    delete_db,
                                                                    idcontarea,
                                                                    idcontex,
                                                                    tipocont,
                                                                    codmigracont,
                                                                    t_cargo_idcarg
                                                                    ) VALUES (
                                                                    :apaternocont,
                                                                    :amaternocont,
                                                                    :nombrescont,
                                                                    :correocontac,
                                                                    :fregistrocont,
                                                                    :estadocont,
                                                                    :delete_db,
                                                                    :idcontarea,
                                                                    :idcontex,
                                                                    :tipocont,
                                                                    :codmigracont,
                                                                    :t_cargo_idcarg
                                                                     )");

    foreach ($datacomercial as $dcomercial) :

      $data_insertada->execute(array(
        'apaternocont'  => ucwords(mb_strtolower(trim($dcomercial["apaterno"]))),
        'amaternocont'  => ucwords(mb_strtolower(trim($dcomercial["amaterno"]))),
        'nombrescont'   => ucwords(mb_strtolower(trim($dcomercial["nombre"]))),
        'correocontac'  => mb_strtolower(trim($dcomercial["correo"])),
        'fregistrocont' => $fecha_actual,
        'estadocont'    => 0,
        'delete_db'     => 0,
        'idcontarea'    => $this->area(trim($dcomercial["area_descripcion"])),
        'idcontex'      => $this->contexto(trim($dcomercial["contexto"])),
        'tipocont'      => $this->tipocontacto(trim($dcomercial["tipo"])),
        'codmigracont'  => $dcomercial["idcon"],
        't_cargo_idcarg' => $this->cargo($dcomercial["idcargo"]),
      )

      );

      $contar_registrados++;
    endforeach;
    echo ($contar_registrados . ' ' . 'clientes insertados') . "\n";
   // echo ($contar_actualizados . ' ' . 'clientes actualizados');
  }


  public function area($name)
  {
    if($name){
      $query_area = "SELECT idcontarea FROM scliente.t_contactoarea WHERE nomcontarea  ILIKE '%$name%'";
      $area = $this->conexionpdoPostgresProductionHolistics()->prepare($query_area);
      $area->execute();
      $capuraarea = $area->fetch();
      return  $capuraarea[0];
    }
    else{
       return null;
    }
   
  }
  public function contexto($name)
  {
    if ($name) {
      $query_area = "SELECT idcontex FROM scliente.t_contexto WHERE descripcion  ILIKE '%$name%'";
      $contexto = $this->conexionpdoPostgresProductionHolistics()->prepare($query_area);
      $contexto->execute();
      $capturacontexto = $contexto->fetch();
      return  $capturacontexto[0];
    } else {
      return null;
    }
  }
  public function tipocontacto($name)
  {
    $result = null;
    switch (trim($name)) {
      case 'Cliente':
        $result = 1;
        break;
      case 'No cliente':
        $result = 4;
        break;
      case 'Competencia':
        $result = 5;
        break;
      case 'Potencial':
        $result = 3;
        break;
      case 'Prospecto':
        $result = 2;
        break;
      case 'Contacto':
        $result = 0;
        break;
    }
    return  $result; 
  }
  public function cargo($pk)
  {
   
      $query_area = "SELECT idcarg FROM scliente.t_cargo WHERE codmigracargo  = $pk";
      $area = $this->conexionpdoPostgresProductionHolistics()->prepare($query_area);
      $area->execute();
      $capuraarea = $area->fetch();
      return  $capuraarea[0];
    
  }
}

$data = new clientesComercial();
$data->dataActualizaroRegistrarClientes();
//$data->contexto('');
