<?php
class conexioSQL
{

    public function conexionpdoSQL(){
    
            try
            {

            //$conn = new PDO("sqlsrv:Server=$server_name;Database=$db_name;ConnectionPooling=0", "", "");
            $conn = new PDO("sqlsrv:Server=192.168.1.6;Database=flowdesk","scpdemo","scpdemo19$1");
            //$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            return  $conn;
            
            }
            catch(PDOException $e)
            {

            echo $e->getMessage();

            }

    }        
        
}