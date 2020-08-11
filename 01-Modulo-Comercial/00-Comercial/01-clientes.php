<?php


define('APP_PATH', __DIR__ . '/../../');

require_once APP_PATH . '/database/conexionesdb.php';


class clientesComercial extends conexioSQL
{
  
  public function datosclientesExcel()
  {
    $query = "select * from cliente.clientes2;";
    $stmt = $this->conexionpdoLocalComercialExcel()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
   // print_r ($listArray);
    return $listArray;
  }

  public function datosclientesHolistics()
  {
    $query = "select * from scliente.t_cliente";
    $stmt = $this->conexionpdoPostgresProductionHolistics()->prepare($query);
    $stmt->execute();
    $listArray = $stmt->fetchAll();
   // print_r ($listArray);
    return $listArray;
  }

  public function dataActualizaroRegistrarClientes()
  {
    $datacomercial         = $this->datosclientesExcel();
    $datasclienteHolistics = $this->datosclientesHolistics();
    $conexionHolistics     = $this->conexionpdoPostgresProductionHolistics();
    $contar_actualizados   = 0;
    $contar_registrados    = 0;
    $fecha_actual          = date("Y-m-d");

    $data_insertada = $conexionHolistics->prepare("INSERT INTO scliente.t_cliente (
                                                                   -- codigocli,
                                                                    razon_socialcli,
                                                                    codmigracli,
                                                                    id_tributariacli,
                                                                    fregistrocli,
                                                                    t_tipotrib_idtrib,
                                                                    t_pais_idpais,
                                                                    t_tipoempresa_idemp,
                                                                    webcli,
                                                                    estado,
                                                                    observacion
                                                                    ) VALUES (
                                                                    --:codigocli,
                                                                    :razon_socialcli,
                                                                    :codmigracli,
                                                                    :id_tributariacli,
                                                                    :fregistrocli,:t_tipotrib_idtrib,:t_pais_idpais,:t_tipoempresa_idemp,
                                                                    :webcli,:estado,:observacion)");

    $data_actualizada = $conexionHolistics->prepare("UPDATE scliente.t_cliente  SET 
                                                                          --codigocli           = :codigocli,
                                                                            --    razon_socialcli     = :razon_socialcli,
                                                                                abreviaturacli      = :abreviaturacli,
                                                                                codmigracli         = :codmigracli,
                                                                                id_tributariacli    = :id_tributariacli,
                                                                                fregistrocli        = :fregistrocli,
                                                                                t_tipotrib_idtrib   = :t_tipotrib_idtrib,
                                                                                t_pais_idpais       = :t_pais_idpais,
                                                                                t_tipoempresa_idemp = :t_tipoempresa_idemp,
                                                                                webcli              = :webcli,
                                                                                estado              = :estado,
                                                                                observacion         = :observacion,
                                                                                logo_cli            = :logo_cli
                                                                          WHERE id_tributariacli    = :id_tributariacli

                                                ");

    foreach ($datacomercial as $dcomercial) :
      foreach ($datasclienteHolistics as $dholistics) :
        if (trim($dcomercial['ruc']) == trim($dholistics['id_tributariacli'])) {
          $contar_actualizados++;
          $data_actualizada->execute(array(
            // 'razon_socialcli'     =>  $this->convertirformatorazonSocial($dcomercial["razon_social"]),
            'abreviaturacli'      => $dcomercial["abreviatura"],
            'id_tributariacli'    => trim($dcomercial["ruc"]),
            'fregistrocli'        => $fecha_actual,
            't_tipotrib_idtrib'   => 1,
            't_pais_idpais'       => 1,
            't_tipoempresa_idemp' => $this->TipoEmpresa($dcomercial["tipo_empresa"]),
            'webcli'              => $dcomercial["web"],
            'estado'              => 0,
            'observacion'         => $dcomercial["observacion"],
            'logo_cli'            => $dcomercial["logo"],
            'codmigracli'         => $dcomercial["item"]
            
          ));
          continue 2;
        }
      endforeach;

      $data_insertada->execute(array(
        'razon_socialcli'     => $this->convertirformatorazonSocial($dcomercial["razon_social"]),
        'id_tributariacli'    => trim($dcomercial["ruc"]),
        'fregistrocli'        => $fecha_actual,
        't_tipotrib_idtrib'   => 1,
        't_pais_idpais'       => 1,
        't_tipoempresa_idemp' => $this->TipoEmpresa($dcomercial["tipo_empresa"]),
        'webcli'              => $dcomercial["web"],
        'estado'              => 0,
        'observacion'         => $dcomercial["observacion"],
        'codmigracli'         => $dcomercial["item"]
      ));

      $contar_registrados++;
    endforeach;
    echo ($contar_registrados . ' ' . 'clientes insertados') . "\n";
    echo ($contar_actualizados . ' ' . 'clientes actualizados');

    
  }
  public function convertirformatorazonSocial($val)
  {
    $formato     = ucwords(mb_strtolower($val));
    $ultimapabra = substr($formato, strrpos($formato, ' ') + 1);
    $tipoempresaM = $this->convertirMayuscula($ultimapabra);
    $evaluarreemplazo = $tipoempresaM ? $tipoempresaM : $ultimapabra;
    $formatoreal = str_replace($ultimapabra, $evaluarreemplazo , $formato);
    return $formatoreal;
  }
  public function convertirMayuscula($val)
  {
    switch ($val) {
      case 'Eirl':
        return str_replace('.', '', strtoupper($val));
        break;
      case 'Sa':
       return str_replace('.','', strtoupper($val));
        break;
      case 'S.a':
       return str_replace('.','', strtoupper($val));
        break;  
      case 'S.a.':
       return str_replace('.','', strtoupper($val));
        break;
      case 'S.a.a':
       return str_replace('.','', strtoupper($val));
        break;  
      case 'S.a.a.':
       return str_replace('.','', strtoupper($val));
        break;
      case 'S.a.c':
       return str_replace('.','', strtoupper($val));
        break;
      case 'Sac':
       return str_replace('.','', strtoupper($val));
        break;
      case 'S.a.c':
       return str_replace('.','', strtoupper($val));
        break;          
      case 'S.a.c.':
       return str_replace('.','', strtoupper($val));
        break;
      case 'S.r.l.':
       return str_replace('.','', strtoupper($val));
        break;
      case 'Srl':
       return str_replace('.','', strtoupper($val));
        break;
    }

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


}

$data = new clientesComercial();
$data->dataActualizaroRegistrarClientes();
//$data->datosclientesExcel();
//$data->TipoEmpresa('Empresa Estatal de Derecho Privado');