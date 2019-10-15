<?php

trait conexionPostgres
{

    function conexionpdoPostgres()
    {

        try {

            // Conexion a migrar 

           /*  $passbd =  "11$" . "_19.06-";
            $conn = new PDO("pgsql:host=192.168.1.18;dbname=dberpProyectos", "erp", $passbd); */

            // Conexion para hacer pruebas 
            
            $passbd =  "postgres";
            $conn = new PDO("pgsql:host=127.0.0.1;dbname=dberpProyectos", "postgres",$passbd);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }
}
