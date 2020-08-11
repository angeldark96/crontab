<?php

define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class ppaComercial extends conexioSQL
{
  public function datosExcel()
  {
    $query = "select * from propuesta.ppa;";
    $stmt = $this->conexionpdoLocalComercialExcel()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
    //print_r ($listArray);
    return $listArray;
  }
  public function datosinvitacionesholistics()
  {
    $query = "select * from spropuesta.t_ppa;";
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

    $data_actualizada = $conexionHolistics->prepare("UPDATE spropuesta.t_preliminar  SET
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

    $data_insertada = $conexionHolistics->prepare("INSERT INTO spropuesta.t_preliminar (
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

    $data_insertada_clientes =   $conexionHolistics->prepare("INSERT INTO spropuesta.t_tipoclientepreliminar (
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
    $data_insertada_contactos =   $conexionHolistics->prepare("INSERT INTO spropuesta.t_contactopreliminar (
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
    $data_insertada_uminera =   $conexionHolistics->prepare("INSERT INTO spropuesta.t_uminerapreliminar (
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
           // 'estadoinv'    => $this->estadoinvitacion($dcomercial["estado"]),
            //'resultadoinv' => $this->resultadoinvitacion($dcomercial["resultado"]),
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
        //'estadoinv'    => $this->estadoinvitacion($dcomercial["estado"]),
        //'resultadoinv' => $this->resultadoinvitacion($dcomercial["resultado"]),
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

      
    endforeach;


    echo ($contar_registrados . ' ' . 'clientes insertados') . "\n";
    echo ($contar_actualizados . ' ' . 'clientes actualizados');
  }

}