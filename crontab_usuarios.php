<?php

require_once 'database/sqlsrv_conexion.php';
require_once 'database/postgres_conexion.php';
require_once 'database/pg_tblog_conexion.php';
require_once 'database/postgres_test_conexion.php';

class obtenerDataUsuariosFlowdesk extends conexioSQL
{
    use conexionPostgres, conexionPostgresTest, conexionPostgres_QA;

    public function dataUsuariosFlowdesk()
    {
        // Data del flowdesk empleado a actualizar 
        $queryuser_fd = "select DISTINCT(LTRIM(RTRIM(emp.documento_empleado)))  as documento_empleado,emp.ape_paterno_empl, emp.ape_materno_empl,emp.nombre,
        car.descripcion as cargo,scc.descripcion as centro_costo ,scc.codigo as codigo_sig,emp.email, emp.email_laboral,max(conn.id) as ultimocontrato,conn.fecha_fin_prog ,conn.id_tipo_contrato,conn.fecha_inicio,emp.id_nacionalidad_empl
        from empleado emp
        left join (select * from rh_contrato con where con.fecha_fin_prog is null) conn on emp.id = conn.id_empleado
        left join subcentrocosto scc on conn.id_sub_centrocosto = scc.id
        left join rh_cargo car on conn.id_ultimo_cargo = car.id
        where conn.id is  not NULL
        group by emp.documento_empleado ,emp.ape_paterno_empl, emp.ape_materno_empl,emp.nombre,car.descripcion,scc.codigo,emp.email,emp.email_laboral,emp.id,scc.descripcion,conn.fecha_fin_prog,conn.id_tipo_contrato,conn.fecha_inicio,emp.id_nacionalidad_empl
        ";
        $userfd = $this->conexionpdoSQL()->query($queryuser_fd);

       /*  $userfd->execute();
        $listArray = $userfd->fetchAll();

        var_dump($listArray); */

        // Data del SCP
        $queryscp = "select distinct(trim(identificacion)) as documento_empleado from tpersona where ctipopersona = 'NAT'";
        $pdo = $this->conexionpdoPostgresTest_QA()->query($queryscp);
        $pdoupdate_insert = $this->conexionpdoPostgresTest_QA();
        $listArrayscp = $pdo->fetchAll(PDO::FETCH_OBJ);
        $listArrayscp = array_column($listArrayscp, "documento_empleado");
        //$listArrayscp = trim($listArrayscp);

        //print_r($listArrayscp);


        $count = 0;
        $result_set = $pdoupdate_insert->prepare("UPDATE tpersona SET nombre = :nombre,
                                                                      identificacion = :identificacion,
                                                                      ctipopersona = :ctipopersona,
                                                                      abreviatura =:abreviatura,
                                                                      ctipoidentificacion = :ctipoidentificacion
                                                                      WHERE identificacion = :identificacion ");

        $result_set_insert = $pdoupdate_insert->prepare("INSERT INTO tpersona (nombre,identificacion,ctipopersona,abreviatura,ctipoidentificacion) 
                                                         VALUES (:nombre,:identificacion,:ctipopersona,:abreviatura,:ctipoidentificacion)");

        $tpersonaListadataadicional = $pdoupdate_insert->prepare("INSERT INTO tpersonadatosempleado (cpersona,carea,fingreso,estado,ctipocontrato,email,email_laboral) 
                                                    VALUES (:cpersona,:carea,:fingreso,:estado,:ctipocontrato,:email,:email_laboral)");
        
        $tpersonaListadataadicional_update = $pdoupdate_insert->prepare("UPDATE tpersonadatosempleado SET cpersona = :cpersona,
                                                                                                          carea  = :carea,
                                                                                                          fingreso = :fingreso,
                                                                                                          estado   = :estado,
                                                                                                          ctipocontrato = :ctipocontrato,
                                                                                                          email =:email,
                                                                                                          email_laboral=:email_laboral

                                                                                                         
                                                                                                        FROM (
                                                                                                        select tp.cpersona from tpersona tp
                                                                                                        inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                                                                                                        where tp.cpersona = :cpersona
                                                                                                        ) AS subquery 
                                                                                                        WHERE tpersonadatosempleado.cpersona=subquery.cpersona");

        $tpersonaListadataadicionalinforbasica = $pdoupdate_insert->prepare("INSERT INTO tpersonanaturalinformacionbasica (cpersona,apaterno,amaterno,nombres,esempleado,cnacionalidad) 
                                                                          VALUES (:cpersona,:apaterno,:amaterno,:nombres,:esempleado,:cnacionalidad)");   
                                                                          
        $tpersonaListadataadicionalinforbasica_update = $pdoupdate_insert->prepare("UPDATE tpersonanaturalinformacionbasica SET cpersona = :cpersona,
                                                                                                                apaterno = :apaterno,
                                                                                                                amaterno = :amaterno,
                                                                                                                nombres = :nombres,
                                                                                                                esempleado = :esempleado,
                                                                                                                cnacionalidad = :cnacionalidad
                                                                                                               
                                                                                                            FROM (
                                                                                                            select tp.cpersona from tpersona tp
                                                                                                            inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                                                                                                            where tp.cpersona = :cpersona
                                                                                                            ) AS subquery 
                                                                                                            WHERE tpersonanaturalinformacionbasica.cpersona=subquery.cpersona");                                                                  

        foreach ($userfd as $row) :
            if (in_array(intval($row["documento_empleado"]), $listArrayscp)) {

                $result_set->execute(array(
                    'nombre' =>  ucwords(strtolower($row["ape_paterno_empl"])).' '. ucwords(strtolower($row["ape_materno_empl"])).' '. ucwords(strtolower($row["nombre"])),
                    'ctipopersona'        => 'NAT', 
                    'ctipoidentificacion' => 2, 
                    'abreviatura' => ($row["nombre"] ? explode(" ",ucwords(strtolower($row["nombre"])))[0] : '').' '.ucwords(strtolower($row["ape_paterno_empl"])),
                    'identificacion' => $row["documento_empleado"]
                ));

                $tpersonaListadataadicional_update->execute(array(
                    'cpersona' => $this->capturarCPersona($row["documento_empleado"]),
                    'carea'     => $this->capturarCentrodeCostoSCP($this->capturarCentrodeCosto($row["documento_empleado"])),
                    'fingreso'  => $row["fecha_inicio"],
                    'estado'    =>   $row["fecha_fin_prog"] == null ? 'ACT': 'INA',
                    'ctipocontrato'    => $this->capturarTipoContrato($row["id_tipo_contrato"]),
                    'email'  => $row["email"],
                    'email_laboral'  => $row["email_laboral"]
                    
                ));

                $tpersonaListadataadicionalinforbasica_update->execute(array(
                    'cpersona' => $this->capturarCPersona($row["documento_empleado"]),
                    'apaterno' => $row["ape_paterno_empl"],
                    'amaterno' =>   $row["ape_materno_empl"],
                    'nombres' =>   $row["nombre"], 
                    'esempleado' =>  1, 
                    'cnacionalidad' => $this->capturarNacionalidadEmpleado($this->capturarPais($row["id_nacionalidad_empl"]))
                ));



               // echo $row["documento_empleado"].' '.$row["nombre"].' '.$row["ape_paterno_empl"]."\n";

                continue;
            } else {

                $result_set_insert->execute(array(
                    'identificacion'      => $row["documento_empleado"],
                    'ctipopersona'        => 'NAT', 
                    'ctipoidentificacion' => 2, 
                    'abreviatura' => ($row["nombre"] ? explode(" ",ucwords(strtolower($row["nombre"])))[0] : '').' '.ucwords(strtolower($row["ape_paterno_empl"])),
                    'nombre'          =>ucwords(strtolower($row["ape_paterno_empl"])).' '. ucwords(strtolower($row["ape_materno_empl"])).' '. ucwords(strtolower($row["nombre"]))
                ));
                //nombre,identificacion,ctipopersona,abreviatura,ctipoidentificacion   
                $lastInsertId = $pdoupdate_insert->lastInsertId();

                $tpersonaListadataadicional->execute(
                    array(
                        'cpersona' => $lastInsertId,
                        'carea'     =>  $this->capturarCentrodeCostoSCP($this->capturarCentrodeCosto($row["documento_empleado"])),
                        'fingreso' => $row["fecha_inicio"],
                        'estado' =>   $row["fecha_fin_prog"] == null ? 'ACT': 'INA',
                        'ctipocontrato'    => $this->capturarTipoContrato($row["id_tipo_contrato"]),
                        'email'  => $row["email"],
                        'email_laboral'  => $row["email_laboral"]
                    )
                );

                $tpersonaListadataadicionalinforbasica->execute(
                    array(
                        'cpersona' => $lastInsertId,
                        'apaterno' => $row["ape_paterno_empl"],
                        'amaterno' =>   $row["ape_materno_empl"],
                        'nombres' =>   $row["nombre"], 
                        'esempleado' =>  1, 
                        'cnacionalidad' =>   $this->capturarNacionalidadEmpleado($this->capturarPais($row["id_nacionalidad_empl"]))
                    )
                );

               // echo $row["documento_empleado"].' '.$row["nombre"].' '.$row["ape_paterno_empl"]."\n";
                $count++;
            }
           // array_push($array_flowdesk_scp, $row);
        endforeach;
        echo $count;
        // echo count($array_flowdesk);
       // print_r($array_flowdesk_scp);
    }

    public function empleadosInactivos()
    {

                // Usuarios con Contrato en el Flowdesk
                $queryuser_fd = "select distinct(rh.id_empleado),emp.documento_empleado,rh.fecha_fin_prog from rh_contrato rh
                inner join empleado emp on emp.id = rh.id_empleado
                where rh.fecha_fin_prog is  null;";
                $userfd = $this->conexionpdoSQL()->query($queryuser_fd);
                $listArrayfd = $userfd->fetchAll(PDO::FETCH_OBJ);
                $listArrayfd = array_column($listArrayfd, "documento_empleado");
                $trimmed_arrayfd=array_map('trim',$listArrayfd);
                sort($trimmed_arrayfd);
                //print_r($trimmed_arrayfd);
                
                // No tienenn contrato || contrato vencido dato historico
                $queryuser_fdNC = "select distinct(rh.id_empleado),max(rh.fecha_fin_prog),emp.documento_empleado from rh_contrato rh
                inner join empleado emp on emp.id = rh.id_empleado
                where rh.fecha_fin_prog is not null
                group by rh.id_empleado,emp.documento_empleado;";
                $userfdNC = $this->conexionpdoSQL()->query($queryuser_fdNC);
                $listArrayfdNC = $userfdNC->fetchAll(PDO::FETCH_OBJ);
                $listArrayfdNC = array_column($listArrayfdNC, "documento_empleado");
                $trimmed_arrayfdNC=array_map('trim',$listArrayfdNC);
                sort($trimmed_arrayfdNC);

                //print_r($trimmed_arrayfdNC);
                // Separo las personas que no tienen contrato
               $diff = array_diff($trimmed_arrayfdNC,$trimmed_arrayfd);
                //$in_values = implode(',', $diff);
                $in_values = implode("','", $diff);
                //echo "'$in_values'";

                // Pasar a inactivo los usuarios que no dejaron de tener contrato

                $query = "UPDATE tpersonadatosempleado
                SET estado='INA'
                FROM (
                select tp.cpersona,tp.identificacion,tpde.estado,tp.nombre from tpersona tp
                inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                where identificacion in ('$in_values')
                ) AS subquery
                WHERE tpersonadatosempleado.cpersona=subquery.cpersona;";
                $stmt = $this->conexionpdoPostgresTest_QA()->prepare($query);
                $stmt->execute();
                $data = $stmt->fetchAll();


                $queryuser = "UPDATE tusuarios
                SET estado='INA'
                FROM (
                select tp.cpersona,tp.identificacion,tpde.estado,tp.nombre from tpersona tp
                inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                where identificacion in ('$in_values')
                ) AS subquery
                WHERE tusuarios.cpersona=subquery.cpersona;";
                $stmtuser = $this->conexionpdoPostgresTest_QA()->prepare($queryuser);
                $stmtuser->execute();
                $datauser = $stmtuser->fetchAll();

    }

    public function capturarCPersona($documento_empleado)
    {
        $query = "SELECT cpersona FROM tpersona WHERE identificacion = '$documento_empleado'";
        $stmt = $this->conexionpdoPostgresTest_QA()->prepare($query);
        $stmt->execute();
        $listArray = $stmt->fetch();
        return $listArray['cpersona'];
    }
    public function capturarPais($idPais)
    {
        $querypais_fd = "select descripcion from nacionalidad where id = $idPais ";
        $pais_nac = $this->conexionpdoSQL()->prepare($querypais_fd);
        $pais_nac->execute();
        $pais = $pais_nac->fetch();
        return $pais['descripcion'];
    }
    public function capturarNacionalidadEmpleado($descripcionpais)
    {
        $query = "SELECT cpais FROM tpais WHERE descripcion = '$descripcionpais'";
        $stmt = $this->conexionpdoPostgresTest_QA()->prepare($query);
        $stmt->execute();
        $data = $stmt->fetch();
        //echo $data['cpais'];
        return $data['cpais'];
    }

    public function capturarCentrodeCosto($documento_empleado)
    {
        $querycc_fd = "select DISTINCT(emp.documento_empleado),scc.descripcion as centro_costo ,scc.codigo as codigo_sig,max(conn.id) as ultimocontrato
        from empleado emp
        left join (select * from rh_contrato con where con.fecha_fin_prog is null) conn on emp.id = conn.id_empleado
        left join subcentrocosto scc on conn.id_sub_centrocosto = scc.id
        left join rh_cargo car on conn.id_ultimo_cargo = car.id
        where conn.id is  not NULL and
        emp.documento_empleado = '$documento_empleado'
        group by emp.documento_empleado ,scc.descripcion ,scc.codigo";
        $cod_sig = $this->conexionpdoSQL()->prepare($querycc_fd);
        $cod_sig->execute();
        $codigo_sig_fd = $cod_sig->fetch();

        return $codigo_sig_fd['codigo_sig'];
    }

    public function capturarCentrodeCostoSCP($codigo_sig)
    {
        $query = "SELECT carea FROM tareas WHERE codigo_sig = '$codigo_sig'";
        $stmt = $this->conexionpdoPostgresTest_QA()->prepare($query);
        $stmt->execute();
        $data = $stmt->fetch();
        return $data['carea'];
    }

    public function capturarTipoContrato($codigo)
    
    {
        $querylista = "SELECT * FROM ttipocontrato";
        $stmtlista = $this->conexionpdoPostgresTest_QA()->prepare($querylista);
        $stmtlista->execute();
        $datalista = $stmtlista->fetchAll();
       
        $ar =[];
        foreach( $datalista as $dt):
            $col['pk'] = explode(",",$dt['pk_contratofd']);
            $col['tipo'] = $dt['ctipocontrato'];
            array_push($ar,$col);
        endforeach;    
       //print_r($ar);

       foreach($ar as $da):
            foreach($da['pk'] as $d => $value):
                     
                     if ($value == $codigo )
                     {
                        return $da['tipo'];
                     } 
                    continue;
            endforeach;     
       endforeach; 

       //echo($fin);

    }
}

$foo = new obtenerDataUsuariosFlowdesk();
// inserta la data de usuarios deñ FD al SCPv2
$foo->dataUsuariosFlowdesk();
// Inactiva a los usuarios tanto en personal como usuarios
$foo->empleadosInactivos();
//$foo->capturarNacionalidadEmpleado('PERÚ');

//$foo->capturarTipoContrato(4);