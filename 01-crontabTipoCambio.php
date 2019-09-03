<?php

require_once 'database/sqlsrv_conexion.php';
require_once 'database/postgres_conexion.php';
require_once 'database/pg_tblog_conexion.php';


class getdataFlowdesk extends conexioSQL
{
    use conexionPostgres, conexionPostgresTest;

    public function getTipodeCambioFlowdesk()
    {

        $fecha_actual = date("Y-m-d");
        //$fecha_actual = date("2010-02-10");
        $dia_anterior = date("Y-m-d", strtotime($fecha_actual."- 1 days"));
       //	$dia_anterior = date("Y/m/d", strtotime($fecha_actual . "- 3 days"));
       // $dia_anterior = date("Y-m-d", strtotime($fecha_actual));
        //echo  $dia_anterior;
       // $query = "SELECT * FROM dbo.moneda_tipocambio where  Convert(varchar(20), fecha_modificacion,111)  = '$dia_anterior'";
          $query = "SELECT * FROM dbo.moneda_tipocambio where  convert(date, fecha_modificacion, 120)  = '$dia_anterior'";
        $stmt = $this->conexionpdoSQL()->query($query);
        $array_flowdesk = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($array_flowdesk, $row);
        }
        return $array_flowdesk;
        // print_r($dia_anterior);
    }

    public function getTipodeCambioSCP()
    {
        // echo $this->capturardataMoneda();
        try {
            $date = new DateTime("now", new DateTimeZone('America/Lima') );
            $fechaActual =  $date->format('Y-m-d H:i:s');

            if ($this->capturardataMoneda() == 'data') {
                $data =  $this->getTipodeCambioFlowdesk();
                $pdo = $this->conexionpdoPostgres();
                $result_set = $pdo->prepare("INSERT INTO tmonedacambio (cmoneda,cmonedavalor,fecha,valorcompra,valorventa) VALUES (:cmoneda,:cmonedavalor,:fecha,:valorcompra,:valorventa)");
                foreach ($data as $row) {
                    $result_set->execute(array(
                        'cmoneda' => $this->capturarmonedaSCP($row["id_moneda"]),
                        'cmonedavalor' => 'S/.',
                        'fecha' => $row["fecha_tcambio"],
                        'valorcompra' => $row["tipocambio_cobranza"],
                        'valorventa' => $row["tipocambio_emision"]
                    ));
                }
              return  $this->conexionpdoPostgresTestLog($fechaActual, 'tipodeCambio', 'Éxito');

            } else {

              return  $this->conexionpdoPostgresTestLog($fechaActual, 'tipodeCambio', 'No se encontro data');
            }
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }

    public function capturardataMoneda()
    {

        $data = $this->getTipodeCambioFlowdesk();
        $id_monedas = [];

        foreach ($data as $value) :
            array_push($id_monedas, $value["id_moneda"]);
        endforeach;

        $in_values = implode(',', $id_monedas);
        $data_values =  $in_values == null ? 123456789  : $in_values;
        $query = "SELECT * FROM tmonedas where pk_flowdesk in ($data_values)";
        $pdo_test = $this->conexionpdoPostgres()->query($query);
        $row_count = $pdo_test->rowCount();
        $res = ($row_count > 0) ? 'data' : 'sin_data';
        return $res;
    }

    public function capturarmonedaSCP($value)
    {
       
        $query = "SELECT * FROM tmonedas where pk_flowdesk = $value";
        $stmt = $this->conexionpdoPostgres()->prepare($query);
        $stmt->execute();
        // $listArray = $stmt->fetchAll(); -- array varios registros
        $listArray = $stmt->fetch(); // unico registro
        $description = $listArray["cmoneda"];
        return  $description;
     }
}

$foo = new getdataFlowdesk();
$foo->getTipodeCambioSCP();
