<?php

trait conexionTestPostgresdbscpv3
{

    function conexionpdoPostgresTestscpv3()
    {

        try {
            $passbd =  "postgres";
            $conn = new PDO("pgsql:host=127.0.0.1;dbname=scpv3", "postgres", $passbd);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }
}
     