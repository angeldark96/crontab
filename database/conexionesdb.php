<?php
class conexioSQL
{
    //? FLOWDESK
    // Conexion al FLOWDESK

    public function conexionpdoSQL()
    {

        try {

            //$conn = new PDO("sqlsrv:Server=$server_name;Database=$db_name;ConnectionPooling=0", "", "");
            $conn = new PDO("sqlsrv:Server=192.168.1.6;Database=flowdesk", "scpdemo", "scpdemo19$1");
            //$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            return  $conn;
        } catch (PDOException $e) {

            echo $e->getMessage();
        }
    }

    //? FLOWDESK-ETLInf
    public function conexionpdoSQLETLInf()
    {

        try {

            //$conn = new PDO("sqlsrv:Server=$server_name;Database=$db_name;ConnectionPooling=0", "", "");
            $conn = new PDO("sqlsrv:Server=192.168.1.6;Database=ETLInf", "scpdemo", "scpdemo19$1");
            //$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            return  $conn;
        } catch (PDOException $e) {

            echo $e->getMessage();
        }
    }

    //* SCPv2

    // Conexion  a BD de produccion SCPV2

    public function conexionpdoPostgresProductionSCPv2()
    {

        try {

            $passbd =  "dbProduction2020";
            $conn = new PDO("pgsql:host=192.168.1.186;dbname=db_scp", "postgres", $passbd);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }

    public function conexionpdoPostgresProducctionSCPv2_tbl_log($fecharegistro, $nombrefuncion, $description)
    {

        try {
            $passbd =  "dbProduction2020";
            $conn = new PDO("pgsql:host=192.168.1.186;dbname=db_scp", "postgres", $passbd);
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

    // Conexion BD de test QA -SCPV2

    public function conexionpdoPostgresTestSCPv2()
    //public function conexionpdoPostgresTest_QA()
    {

        try {

            $passbd =  "prueba$2019db";
            $conn = new PDO("pgsql:host=192.168.50.95;dbname=dbtest_scp", "postgres", $passbd);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }

    // Conexion BD Local SCPV2

    public function conexionpdoPostgresLocalSCPv2()
    {

        try {

            $passbd =  "postgres";
            $conn = new PDO("pgsql:host=127.0.0.1;dbname=db_scp", "postgres", $passbd);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }

    // Conexion BD de test QA -SCPV2 TBL_BLOG

    public function conexionpdoPostgresTestSCPv2_tbl_log($fecharegistro, $nombrefuncion, $description)
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


    //* SCPV3 
    // Conexion BD de SCPV3 local

    public function conexionpdoPostgresLocalSCPv3()
    {

        try {
          /*   $passbd =  "postgres";
            $conn = new PDO("pgsql:host=127.0.0.1;dbname=db_holistics_pro", "postgres", $passbd); */

            $passbd =  "dbProduction2020";
            //$conn = new PDO("pgsql:host=192.168.1.186;dbname=db_holistics_preprod", "postgres", $passbd);
            $conn = new PDO("pgsql:host=192.168.1.186;dbname=db_holistics_prod", "postgres", $passbd);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }

    // Conexion BD de SCPV3 Test


    public function conexionpdoPostgresTestSCPv3()
    {

        try {
            // Conexion a migrar
            /*  $passbd =  "prueba$2019db";
              $conn = new PDO("pgsql:host=192.168.50.95;dbname=db_scp", "postgres", $passbd); */
            // Conexion para hacer pruebas 
            $passbd =  "prueba$2019db";
            $conn = new PDO("pgsql:host=192.168.50.95;dbname=dbtest_ppm", "postgres", $passbd);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }

    //  Conexion BD de SCPV3 Produccion-

    public function conexionpdoPostgresProductionHolistics()
    {

        try {
            // Conexion a migrar
            /*  $passbd =  "dbProduction2020";
             $conn = new PDO("pgsql:host=192.168.1.186;dbname=db_holistics_prod", "postgres", $passbd); */

               $passbd =  "postgres";
            $conn = new PDO("pgsql:host=127.0.0.1;dbname=db_holistics_pro", "postgres", $passbd);
          

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }


    //* DWHAnddes

    public function conexionpdoPostgresProductionDWHAnddes()
    {

        try {

            $passbd =  "dbProduction2020";
            $conn = new PDO("pgsql:host=192.168.1.186;dbname=DWHAnddes", "postgres", $passbd);
           // $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }

    public function conexionpdoLocalComercialExcel()
    {

        try {

            $passbd =  "postgres";
            $conn = new PDO("pgsql:host=127.0.0.1;dbname=bd_dataexcel", "postgres", $passbd);

            return $conn;
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }
}



