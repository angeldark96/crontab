<?php

trait conexionPostgres_QA
{

    function conexionpdoPostgresTest_QA()
    {

        try {
          //  $passbd =  "prueba$2019db";
          //  $conn = new PDO("pgsql:host=192.168.50.95;dbname=test", "postgres", $passbd);
            $passbd =  "postgres";
            $conn = new PDO("pgsql:host=127.0.0.1;dbname=db_scp", "postgres",$passbd);
           /*  $passbd =  "prueba$2019db";
            $conn = new PDO("pgsql:host=192.168.50.95;dbname=db_scp", "postgres", $passbd); */
            //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }
}
