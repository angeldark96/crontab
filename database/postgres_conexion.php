<?php

trait conexionPostgres
{

    function conexionpdoPostgres()
    {

        try {
            $passbd =  "11$" . "_19.06-";
            $conn = new PDO("pgsql:host=192.168.1.18;dbname=dberpProyectos", "erp", $passbd);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }
}
