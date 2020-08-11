<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class clientesComercial extends conexioSQL
{

  public function datosExcel()
  {
    $query = "select * from invitaciones.invitaciones where idinvitacion is not null order by idinvitacion ;";
    $stmt = $this->conexionpdoLocalComercialExcel()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
    //print_r ($listArray);
    return $listArray;
  }
  public function datosinvitacionesholistics()
  {
    $query = "select * from spropuesta.t_invitacion ;";
    $stmt = $this->conexionpdoPostgresLocalSCPv3()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
    //print_r ($listArray);
    return $listArray;
  }


  public function dataActualizaroRegistrarClientes()
  {
    $datacomercial      = $this->datosExcel();
    $datacomercialholis = $this->datosinvitacionesholistics();
    $conexionHolistics     = $this->conexionpdoPostgresLocalSCPv3();
    $contar_actualizados   = 0;
    $contar_registrados    = 0;
    $fecha_actual          = date("Y-m-d");

    $data_actualizada = $conexionHolistics->prepare("UPDATE spropuesta.t_invitacion  SET
                                                                           nombreinv          = :nombreinv,
                                                                           fechainv           = :fechainv,
                                                                           estadoinv          = :estadoinv,
                                                                         resultadoinv       = :resultadoinv,
                                                                           fechadecinv        = :fechadecinv,
                                                                           idareagt           = :idareagt,
                                                                           idmoneda           = :idmoneda,
                                                                           codigoand          = :codigoand,
                                                                           codmigrainvitacion = :codmigrainvitacion,
                                                                           montoinv           = :montoinv,
                                                                           montodecinv        = :montodecinv,
                                                                           motivodecinv       = :motivodecinv,
                                                                           codigoinv          = :codigoinv  
                                                                  WHERE codmigrainvitacion = :codmigrainvitacion
                                                   "); 

    $data_insertada = $conexionHolistics->prepare("INSERT INTO spropuesta.t_invitacion (
                                                                    nombreinv,
                                                                    fechainv,
                                                                    estadoinv,
                                                                    resultadoinv,
                                                                    fechadecinv,
                                                                    idareagt,
                                                                    idmoneda,
                                                                    codmigrainvitacion,
                                                                    codigoand,
                                                                    montoinv,
                                                                    montodecinv,
                                                                    motivodecinv,
                                                                    codigoinv
                                                                    ) VALUES (
                                                                    :nombreinv,
                                                                    :fechainv,
                                                                    :estadoinv,
                                                                    :resultadoinv,
                                                                    :fechadecinv,
                                                                    :idareagt,
                                                                    :idmoneda,
                                                                    :codmigrainvitacion,
                                                                    :codigoand,
                                                                     :montoinv,
                                                                     :montodecinv,
                                                                     :motivodecinv,
                                                                     :codigoinv
                                                                     )");

    $data_insertada_clientes =   $conexionHolistics->prepare("INSERT INTO spropuesta.t_tipoclienteinvitacion (
                                                                    idinv,
                                                                    idcliente,
                                                                    idtipocli,
                                                                    fregaud,
                                                                    fmodaud
                                                                    ) VALUES (
                                                                    :idinv,
                                                                    :idcliente,
                                                                    :idtipocli,
                                                                    :fregaud,
                                                                    :fmodaud
                                                                     )");
    $data_insertada_contactos =   $conexionHolistics->prepare("INSERT INTO spropuesta.t_contactoinvitacion (
                                                                    idinv,
                                                                    idcont,
                                                                    estadocontinv,
                                                                    fregaud,
                                                                    fmodaud
                                                                    ) VALUES (
                                                                    :idinv,
                                                                    :idcont,
                                                                    :estadocontinv,
                                                                    :fregaud,
                                                                    :fmodaud
                                                                     )");                                                                 
    $data_insertada_uminera =   $conexionHolistics->prepare("INSERT INTO spropuesta.t_uminerainvitacion (
                                                                    idtipocliinv,
                                                                    idum,
                                                                    fregaud,
                                                                    fmodaud
                                                                    ) VALUES (
                                                                    :idtipocliinv,
                                                                    :idum,
                                                                    :fregaud,
                                                                    :fmodaud
                                                                     )");                                                                                                                                 

    foreach ($datacomercial as $dcomercial) :
      foreach ($datacomercialholis as $dcomercialholi) :
        if ($dcomercial['idinvitacion'] == $dcomercialholi['codmigrainvitacion']) {
         
          $contar_actualizados++;
          $data_actualizada->execute(array(
            //'nombreinv'    => trim(ucwords(mb_strtolower($dcomercial["serviciosolicitado"], 'UTF-8'))),
            //'nombreinv'    => mb_strtolower($dcomercial["serviciosolicitado"], 'UTF-8'),
            'nombreinv'    => mb_convert_case(mb_strtolower($dcomercial["serviciosolicitado"]), MB_CASE_TITLE, "UTF-8"),
            'fechainv'     => $dcomercial["invitacion"],
            'estadoinv'    => $this->estadoinvitacion($dcomercial["estado"]),
            'resultadoinv' => $this->resultadoinvitacion($dcomercial["resultado"]),
            'fechadecinv'  => $dcomercial["invitacion"],
            'idmoneda'     => 2,
            'idareagt'     => $dcomercial["esgt"],
            'codmigrainvitacion'     => $dcomercial["idinvitacion"],
            'codigoand'     => $dcomercial["preliminar"],
            'montoinv'      => $dcomercial["monto"],
            'montodecinv' => $dcomercial["monto_declinado"],
            'motivodecinv' => $dcomercial["motivo"],
            'codigoinv' => $dcomercial["cod"]
          ));
          continue 2;
        }
      endforeach;
      $data_insertada->execute(array(
        'nombreinv'    => mb_convert_case(mb_strtolower($dcomercial["serviciosolicitado"]), MB_CASE_TITLE, "UTF-8"),
        'fechainv'     => $dcomercial["invitacion"],
        'estadoinv'    => $this->estadoinvitacion($dcomercial["estado"]),
        'resultadoinv' =>$this->resultadoinvitacion($dcomercial["resultado"]),
        'fechadecinv'  => $dcomercial["invitacion"],
        'idmoneda'     => 2,
        'idareagt'     => $dcomercial["esgt"],
        'codmigrainvitacion'     => $dcomercial["idinvitacion"],
        'codigoand'     => $dcomercial["preliminar"],
        'montoinv'      => $dcomercial["monto"],
        'montodecinv' => $dcomercial["monto_declinado"],
        'motivodecinv' => $dcomercial["motivo"],
        'codigoinv' => $dcomercial["cod"]
      ));
      $contar_registrados++;

      $lastInsertId = $conexionHolistics->lastInsertId();
      $data_insertada_clientes->execute(array(
        'idinv'     => $lastInsertId,
        'idcliente' =>  $this->obtenercliente($dcomercial["idcli"]),
        'idtipocli' => 1,
        'fregaud'   =>  $fecha_actual,
        'fmodaud'   =>  $fecha_actual
        )
      );
      $data_insertada_contactos->execute(array(
        'idinv'     => $lastInsertId,
        'idcont' =>  $this->obtenercontacto(trim($dcomercial["contacto"]),$dcomercial["idcli"], trim($dcomercial["unidad"])),
        'estadocontinv' => 0,
        'fregaud'   =>  $fecha_actual,
        'fmodaud'   =>  $fecha_actual
      ));

      $lastInsertumId = $conexionHolistics->lastInsertId();
      $data_insertada_uminera->execute(array(
        'idtipocliinv'     => $lastInsertumId,
        'idum' =>  $this->obteneruminera($dcomercial["idcli"], $dcomercial["unidad"]),
        'fregaud'   =>  $fecha_actual,
        'fmodaud'   =>  $fecha_actual
      ));
    endforeach;
      
  
    echo ($contar_registrados . ' ' . 'clientes insertados') . "\n";
    echo ($contar_actualizados . ' ' . 'clientes actualizados');
  }

  
  public function estadoinvitacion($name)
  {
    $result = null;
    switch (trim($name)) {
      case 'Aprobada':
        $result = 1;
        break;
      case 'Rechazada':
        $result = 2;
        break;
      case 'En espera de respuesta':
        $result = 0;
        break;
    }
    return  $result;
  }
  public function resultadoinvitacion($name)
  {
    $result = null;
    switch (strtoupper(trim($name))) {
      case 'DECLINADA':
        $result = 3;
        break;
      case 'EN ESPERA':
        $result = 4;
        break;
      case 'GANADA':
        $result = 1;
        break;
      case 'PERDIDA':
        $result = 5;
        break;
      case 'CANCELADA':
        $result = 2;
        break;
      case 'EN ELABORACIóN':
        $result = 0;  
        break;
      case 'SUPERADA':
        $result = 6;
        break;
      case 'MIGRACIóN':
        $result = 7;
        break;         
    }
    return  $result;
  }
  public function obtenercliente($idcli)
  {
    $cli = null;
    if($idcli){
      $query_empresa = "SELECT idcliente FROM scliente.t_cliente WHERE idcliente =$idcli ";
      $tempresa = $this->conexionpdoPostgresLocalSCPv3()->prepare($query_empresa);
      $tempresa->execute();
      $capurarempresa = $tempresa->fetch();
      $cli = $capurarempresa[0];
    }
   
    //var_dump($capurarempresa[0]);
    return $cli;
  }
  public function obteneruminera($idcliente, $uminera)
  {
    $cli = null;
    if($idcliente){
      $query_area = "SELECT idum FROM scliente.t_unidadminera WHERE t_cliente_idcliente =$idcliente and  nombre_um ilike '%$uminera%'";
      $area = $this->conexionpdoPostgresLocalSCPv3()->prepare($query_area);
      $area->execute();
      $capuraarea = $area->fetch();
      $cli =  $capuraarea[0];
    }
   
    return   $cli;
  }
  public function obtenercontacto($nombrecopleto,$idcli,$nomum){

    if(!$idcli){
       $query_area = "select null as nulo;";
    }
   else if($nomum){
      $query_area = "select tc.idcont ,trim(upper(concat(nombrescont,' ',apaternocont,' ',amaternocont ))) as nombrecompleto,
    tclicon.t_cliente_idcliente,tumcon.t_unidadminera_idum ,tum.nombre_um 
    from scliente.t_contactos tc
    left join scliente.t_clicont tclicon on idcont = tclicon.t_contactos_idcont
    left join scliente.t_umcontacto tumcon on idcont = tumcon.t_contactos_idcont
    left join scliente.t_unidadminera tum on tumcon.t_unidadminera_idum = tum.idum
    where trim(concat(nombrescont,' ',apaternocont,' ',amaternocont )) ilike  upper('%$nombrecopleto%')
    and tclicon.t_cliente_idcliente = $idcli
    and tum.nombre_um ilike '%$nomum%'
    ;";
    }
    else{
      $query_area = "select tc.idcont ,trim(upper(concat(nombrescont,' ',apaternocont,' ',amaternocont ))) as nombrecompleto,
    tclicon.t_cliente_idcliente,tumcon.t_unidadminera_idum ,tum.nombre_um 
    from scliente.t_contactos tc
    left join scliente.t_clicont tclicon on idcont = tclicon.t_contactos_idcont
    left join scliente.t_umcontacto tumcon on idcont = tumcon.t_contactos_idcont
    left join scliente.t_unidadminera tum on tumcon.t_unidadminera_idum = tum.idum
    where trim(concat(nombrescont,' ',apaternocont,' ',amaternocont )) ilike  upper('%$nombrecopleto%')
    and tclicon.t_cliente_idcliente = $idcli
    ;";
    }
    
    $area = $this->conexionpdoPostgresLocalSCPv3()->prepare($query_area);
    $area->execute();
    $capuraarea = $area->fetch();
    return  $capuraarea[0] ?  $capuraarea[0] : null;
    
  }
}

$data = new clientesComercial();
$data->dataActualizaroRegistrarClientes();
//$data->obtenercontacto('Rafael Guillen Llerena',151, 'El Brocal');
