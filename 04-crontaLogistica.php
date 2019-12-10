<?php

require_once 'database/conexionesdb.php';

class getdataFlowdesk extends conexioSQL
{
    // =========== Proveedores de Orden de Compras ============================================
    public function limpiardatosTablaProveedoresOC()
    {
        $sql = "DELETE FROM slogistica.tproveedoresoc";
        $stmt =  $this->conexionpdoPostgresProductionDWHAnddes()->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }
    public function CapturarDataProveedoresOC()
    {
        $query = "SELECT * FROM slogistica.vista_proveedoresOC";
        $stmt = $this->conexionpdoSQLETLInf()->query($query);
        $listArrayproveedores = $stmt->fetchAll();
         return $listArrayproveedores;
       // print_r ($listArrayproveedores);
    }
    public function registrarDataProveedoresOC()
    {
        $listArrayproveedores = $this->CapturarDataProveedoresOC();
        $pdo_conexion = $this->conexionpdoPostgresProductionDWHAnddes();
        $result_set_insert = $pdo_conexion->prepare("INSERT INTO slogistica.tproveedoresoc(ruc,razon_social,critico,condicion_contribuyente,estado_contribuyente,rubro,cargo,nombre,email,telefono,departamento,provincia,distrito,direccion,proyectos) 
            VALUES (:ruc,:razon_social,:critico,:condicion_contribuyente,:estado_contribuyente,:rubro,:cargo,:nombre,:email,:telefono,:departamento,:provincia,:distrito,:direccion,:proyectos )");

    foreach ($listArrayproveedores as $row):
        $result_set_insert->execute(array(
                'ruc'                       => $row["RUC"],
                'razon_social'              => $row["RAZON_SOCIAL"],
                'critico'                   => $row["CRITICO"],
                'condicion_contribuyente'   => $row["CONDICION_CONTRIBUYENTE"],
                'estado_contribuyente'      => $row["ESTADO_CONTRIBUYENTE"],
                'rubro'                     => $row["RUBRO"],
                'cargo'                     => $row["CARGO"],
                'nombre'                    => $row["NOMBRE"],
                'email'                     => $row["EMAIL"],
                'telefono'                  => $row["TELEFONO"],
                'departamento'              => $row["DEPARTAMENTO"],
                'provincia'                 => $row["PROVINCIA"],
                'distrito'                 => $row["DISTRITO"],
                'direccion'                 => $row["DIRECCION"],
                'proyectos'                 => $row["PROYECTO"]
        ));
    endforeach;      
    }

    public function limpiardatosBDregistroc()
    {
        $sql = "DELETE FROM slogistica.bdregistroc";
        $stmt =  $this->conexionpdoPostgresProductionDWHAnddes()->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();

    }
    public function CapturarDataBDregistroc()
    {
        $query = "SELECT * FROM slogistica.vista_bdregistrosOC";
        $stmt = $this->conexionpdoSQLETLInf()->query($query);
        $bdregistroOC = $stmt->fetchAll();
         return $bdregistroOC;
       // print_r ($listArrayproveedores);
    }

    public function registrarDatabdRegistroOC()
    {
        $listabdregistroOC = $this->CapturarDataBDregistroc();
        $pdo_conexion = $this->conexionpdoPostgresProductionDWHAnddes();
        $result_set_insert = $pdo_conexion->prepare("INSERT INTO slogistica.bdregistroc(fecha_registro,numero_orden,numero_documento,razon_social,critico,numero_proyecto,nombre_proyecto,forma_pago,tiempo_entrega,moneda,cantidad,descripcion,precio) 
            VALUES (:fecha_registro,:numero_orden,:numero_documento,:razon_social,:critico,:numero_proyecto,:nombre_proyecto,:forma_pago,:tiempo_entrega,:moneda,:cantidad,:descripcion,:precio)");

    foreach ($listabdregistroOC as $row):
        $result_set_insert->execute(array(
                'fecha_registro'        => $row["FECHA_CREACION"],
                'numero_orden'          => $row["NUMERO_ORDEN"],
                'numero_documento'      => $row["NUMERO_DOCUMENTO"],
                'razon_social'          => $row["NOMBRE_COMPLETO"],
                'critico'               => $row["CRITICO"],
                'numero_proyecto'       => $row["NUMERO_PROYECTO"],
                'nombre_proyecto'       => $row["NOMBRE_PROYECTO"],
                'forma_pago'            => $row["FORMA_PAGO"],
                'tiempo_entrega'        => $row["TIEMPO_ENTREGA"],
                'moneda'                => $row["MONEDA"],
                'cantidad'              => $row["CANTIDAD"],
                'descripcion'           => $row["DESCRIPCION"],
                'precio'                => $row["PRECIO"]
        ));
    endforeach;      
    }
     // ============================= Proveedores de Orden de Servicio ==========================
     public function limpiardatosTablaProveedoresOS()
     {
         $sql = "DELETE FROM slogistica.tproveedoresos";
         $stmt =  $this->conexionpdoPostgresProductionDWHAnddes()->prepare($sql);
         $stmt->execute();
         return $stmt->rowCount();
     }
     public function CapturarDataProveedoresOS()
     {
         $query = "SELECT * FROM slogistica.vista_proveedoresOS";
         $stmt = $this->conexionpdoSQLETLInf()->query($query);
         $listArrayproveedores = $stmt->fetchAll();
          return $listArrayproveedores;
         //print_r ($listArrayproveedores);
     }

     public function registrarDataProveedoresOS()
     {
         $listArrayproveedoresOS = $this->CapturarDataProveedoresOS();
         $pdo_conexion = $this->conexionpdoPostgresProductionDWHAnddes();
         $result_set_insert = $pdo_conexion->prepare("INSERT INTO slogistica.tproveedoresos (ruc,razon_social,critico,condicion_contribuyente,estado_contribuyente,rubro,cargo,nombre,email,telefono,departamento,provincia,distrito,direccion,proyectos) 
             VALUES (:ruc,:razon_social,:critico,:condicion_contribuyente,:estado_contribuyente,:rubro,:cargo,:nombre,:email,:telefono,:departamento,:provincia,:distrito,:direccion,:proyectos )");
 
        foreach ($listArrayproveedoresOS as $row):
            $result_set_insert->execute(array(
                    'ruc'                       => $row["RUC"],
                    'razon_social'              => $row["RAZON_SOCIAL"],
                    'critico'                   => $row["CRITICO"],
                    'condicion_contribuyente'   => $row["CONDICION_CONTRIBUYENTE"],
                    'estado_contribuyente'      => $row["ESTADO_CONTRIBUYENTE"],
                    'rubro'                     => $row["RUBRO"],
                    'cargo'                     => $row["CARGO"],
                    'nombre'                    => $row["NOMBRE"],
                    'email'                     => $row["EMAIL"],
                    'telefono'                  => $row["TELEFONO"],
                    'departamento'              => $row["DEPARTAMENTO"],
                    'provincia'                 => $row["PROVINCIA"],
                    'distrito'                 => $row["DISTRITO"],
                    'direccion'                 => $row["DIRECCION"],
                    'proyectos'                 => $row["PROYECTO"]
            ));
        endforeach;      
    // print_r( $listArrayproveedoresOS);
     }

     public function limpiardatosBDregistros()
     {
         $sql = "DELETE FROM slogistica.bdregistros";
         $stmt =  $this->conexionpdoPostgresProductionDWHAnddes()->prepare($sql);
         $stmt->execute();
         return $stmt->rowCount();

     }
     public function CapturarDataBDregistros()
     {
         $query = "SELECT * FROM slogistica.vista_bdregistrosOS";
         $stmt = $this->conexionpdoSQLETLInf()->query($query);
         $listArrayproveedores = $stmt->fetchAll();
          return $listArrayproveedores;
         //print_r ($listArrayproveedores);
     }

     public function registrarDatabdRegistroOS()
     {
         $listabdregistroOC = $this->CapturarDataBDregistros();
         $pdo_conexion = $this->conexionpdoPostgresProductionDWHAnddes();
         $result_set_insert = $pdo_conexion->prepare("INSERT INTO slogistica.bdregistros(fecha_registro,numero_orden,numero_documento,razon_social,critico,numero_proyecto,nombre_proyecto,forma_pago,tiempo_entrega,moneda,cantidad,descripcion,precio) 
             VALUES (:fecha_registro,:numero_orden,:numero_documento,:razon_social,:critico,:numero_proyecto,:nombre_proyecto,:forma_pago,:tiempo_entrega,:moneda,:cantidad,:descripcion,:precio)");
 
     foreach ($listabdregistroOC as $row):
         $result_set_insert->execute(array(
                 'fecha_registro'        => $row["FECHA_CREACION"],
                 'numero_orden'          => $row["NUMERO_ORDEN"],
                 'numero_documento'      => $row["NUMERO_DOCUMENTO"],
                 'razon_social'          => $row["NOMBRE_COMPLETO"],
                 'critico'               => $row["CRITICO"],
                 'numero_proyecto'       => $row["NUMERO_PROYECTO"],
                 'nombre_proyecto'       => $row["NOMBRE_PROYECTO"],
                 'forma_pago'            => $row["FORMA_PAGO"],
                 'tiempo_entrega'        => $row["TIEMPO_ENTREGA"],
                 'moneda'                => $row["MONEDA"],
                 'cantidad'              => $row["CANTIDAD"],
                 'descripcion'           => $row["DESCRIPCION"],
                 'precio'                => $row["PRECIO"]
         ));
     endforeach;      
     }

}

$foo = new getdataFlowdesk();
$foo->limpiardatosTablaProveedoresOC();
$foo->registrarDataProveedoresOC();
$foo->limpiardatosTablaProveedoresOS();
$foo->registrarDataProveedoresOS();

$foo->limpiardatosBDregistroc();
$foo->registrarDatabdRegistroOC();
$foo->limpiardatosBDregistros();
$foo->registrarDatabdRegistroOS();
