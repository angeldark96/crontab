<?php

trait conexionPostgresTest
{

    function conexionpdoPostgresTestLog($fecharegistro,$nombrefuncion,$description)
    {

        try {
            $passbd =  "prueba$2019db";
            $conn = new PDO("pgsql:host=192.168.50.95;dbname=db_scp", "postgres", $passbd);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $result_set = $conn->prepare("INSERT INTO iemigracion.tbl_log (fecharegistro,nombrefuncion,description) VALUES (:fecharegistro,:nombrefuncion,:description)");
            $result_set->bindValue(':fecharegistro', $fecharegistro);
            $result_set->bindValue(':nombrefuncion', $nombrefuncion);
            $result_set->bindValue(':description',  $description);
            $result_set->execute();
 
            return  $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }
}


