<?php

require_once 'database/conexionesdb.php';


class obtenerDataUsuariosFlowdesk extends conexioSQL
{
   // use conexionPostgres, conexionPostgresTest, conexionPostgres_QA;

    public function dataUsuariosFlowdesk()
    {
        // Data del flowdesk empleado a actualizar 
        $queryuser_fd = "select DISTINCT(LTRIM(RTRIM(emp.documento_empleado)))  as documento_empleado,tus.id,emp.id pk_fd,emp.ape_paterno_empl, emp.ape_materno_empl,emp.nombre,emp.sexo_empl,id_estado_civil,
        emp.email, emp.email_laboral,max(conn.id) as ultimocontrato,conn.fecha_fin_prog ,conn.id_tipo_contrato,conn.fecha_inicio,emp.id_nacionalidad_empl,tus.nombre as abreviatura
       from empleado emp
       left join (select * from rh_contrato con where con.fecha_fin_prog is null or con.fecha_fin_prog >= getdate()) conn on emp.id = conn.id_empleado
       left join usuario tus on tus.id_empleado_default = emp.id
       where conn.id is  not NULL 
       and conn.id_tipo_contrato != 11 
       group by emp.documento_empleado ,emp.ape_paterno_empl, emp.ape_materno_empl,emp.nombre,emp.sexo_empl,emp.email,emp.email_laboral,emp.id,conn.fecha_fin_prog,
       conn.id_tipo_contrato,conn.fecha_inicio,emp.id_nacionalidad_empl,tus.nombre,tus.id ,id_estado_civil;";
        $userfd = $this->conexionpdoSQL()->query($queryuser_fd);

        /*  $userfd->execute();
        $listArray = $userfd->fetchAll();

        var_dump($listArray); */

        // Data del SCP
        $queryscp = "select distinct(trim(identificacion)) as documento_empleado from tpersona where ctipopersona = 'NAT'";
        $pdo = $this->conexionpdoPostgresProductionSCPv2()->query($queryscp);
        $listArrayscp2 = $pdo->fetchAll(PDO::FETCH_OBJ);
        $listArrayscp = array_column($listArrayscp2, "documento_empleado");
        //$listArrayscp = trim($listArrayscp);

        //print_r($listArrayscp);

        $pdoupdate_insert = $this->conexionpdoPostgresProductionSCPv2();
        $countInsertados = 0;
        $countActualizados = 0;
        $result_set = $pdoupdate_insert->prepare("UPDATE tpersona SET nombre = :nombre,
                                                                      identificacion = :identificacion,
                                                                      ctipopersona = :ctipopersona,
                                                                      abreviatura =:abreviatura,
                                                                      ctipoidentificacion = :ctipoidentificacion,
                                                                      pk_fd = :pk_fd
                                                                      WHERE identificacion = :identificacion ");


        $tpersonaListadataadicional_update = $pdoupdate_insert->prepare("UPDATE tpersonadatosempleado SET cpersona = :cpersona,
                                                                                                          --carea  = :carea,
                                                                                                          --fingreso = :fingreso,
                                                                                                          estado   = :estado,
                                                                                                          ctipocontrato = :ctipocontrato,
                                                                                                          email = :email,
                                                                                                          email_laboral= :email_laboral

                                                                                                         
                                                                                                        FROM (
                                                                                                        select tp.cpersona from tpersona tp
                                                                                                        inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                                                                                                        where tp.cpersona = :cpersona
                                                                                                        ) AS subquery 
                                                                                                        WHERE tpersonadatosempleado.cpersona=subquery.cpersona");


        $tpersonaListadataadicionalinforbasica_update = $pdoupdate_insert->prepare("UPDATE tpersonanaturalinformacionbasica SET cpersona = :cpersona,
                                                                                                                apaterno = :apaterno,
                                                                                                                amaterno = :amaterno,
                                                                                                                nombres = :nombres,
                                                                                                               -- genero = :genero,
                                                                                                                --estadocivil = :estadocivil,
                                                                                                                esempleado = :esempleado,
                                                                                                                cnacionalidad = :cnacionalidad
                                                                                                               
                                                                                                            FROM (
                                                                                                            select  tp.cpersona from tpersona tp
                                                                                                            inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                                                                                                            where tp.cpersona = :cpersona
                                                                                                            ) AS subquery 
                                                                                                            WHERE tpersonanaturalinformacionbasica.cpersona=subquery.cpersona");
         $tpersonausuario =    $pdoupdate_insert->prepare("UPDATE tusuarios SET cpersona = :cpersona,
                                                                                        estado   = :estado
                                                                                        
                                                                                    FROM (
                                                                                    select tp.cpersona from tpersona tp
                                                                                    inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                                                                                    where tp.cpersona = :cpersona
                                                                                    ) AS subquery 
                                                                                    WHERE tusuarios.cpersona=subquery.cpersona");                                                                                                 



        $result_set_insert = $pdoupdate_insert->prepare("INSERT INTO tpersona (nombre,identificacion,ctipopersona,abreviatura,ctipoidentificacion,pk_fd) 
                                                                            VALUES (:nombre,:identificacion,:ctipopersona,:abreviatura,:ctipoidentificacion,:pk_fd)");

        $tpersonaListadataadicional = $pdoupdate_insert->prepare("INSERT INTO tpersonadatosempleado (cpersona,estado,ctipocontrato,email,email_laboral) 
                                                                            VALUES (:cpersona,:estado,:ctipocontrato,:email,:email_laboral)");

        $tpersonaListadataadicionalinforbasica = $pdoupdate_insert->prepare("INSERT INTO tpersonanaturalinformacionbasica (cpersona,apaterno,amaterno,nombres,esempleado,cnacionalidad) 
                                                                            VALUES (:cpersona,:apaterno,:amaterno,:nombres,:esempleado,:cnacionalidad)");


        try {

            $date = new DateTime("now", new DateTimeZone('America/Lima') );
            $fechaActual =  $date->format('Y-m-d H:i:s');

            foreach ($userfd as $row) :
                if (in_array(intval($row["documento_empleado"]), $listArrayscp)) {
                    //echo(ucwords(mb_strtolower($row["ape_paterno_empl"])).' '. ucwords(mb_strtolower($row["ape_materno_empl"])).' '. ucwords(mb_strtolower($row["nombre"]))). "\n"
                    $countActualizados++;
                    $result_set->execute(array(
                        'nombre' =>  ucwords(mb_strtolower($row["ape_paterno_empl"])) . ' ' . ucwords(mb_strtolower($row["ape_materno_empl"])) . ' ' . ucwords(mb_strtolower($row["nombre"])),
                        'ctipopersona'        => 'NAT',
                        'ctipoidentificacion' => 2,
                        'abreviatura' => $row["abreviatura"],
                        'identificacion' => $row["documento_empleado"],
                        'pk_fd' => $row["pk_fd"]
                    ));

                    $tpersonaListadataadicional_update->execute(array(
                        'cpersona' => $this->capturarCPersona($row["documento_empleado"]),
                        //'carea'     => $this->capturarCentrodeCostoSCP($this->capturarCentrodeCosto($row["documento_empleado"])),
                        //'fingreso'  => $row["fecha_inicio"],
                        'estado'    =>  'ACT',
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
                        //'genero' => '' ,
                        //'estadocivil' => '',
                        'cnacionalidad' => $this->capturarNacionalidadEmpleado($this->capturarPais($row["id_nacionalidad_empl"]))
                    ));

                    $tpersonausuario->execute(array(
                        'cpersona' => $this->capturarCPersona($row["documento_empleado"]),
                        'estado'    =>  'ACT'
                    ));

                    //  echo $row["documento_empleado"].' '.$row["nombre"].' '.$row["ape_paterno_empl"].'----------------------------------------------'."\n";
                  
                    continue;
                } 
                //else {

                    $result_set_insert->execute(array(
                        'nombre'          => ucwords(mb_strtolower($row["ape_paterno_empl"])) . ' ' . ucwords(mb_strtolower($row["ape_materno_empl"])) . ' ' . ucwords(mb_strtolower($row["nombre"])),
                        'identificacion'      => $row["documento_empleado"],
                        'ctipopersona'        => 'NAT',
                        'abreviatura' =>  $row["abreviatura"],
                        'ctipoidentificacion' => 2,
                        'pk_fd' => $row["pk_fd"]


                    ));
                    //nombre,identificacion,ctipopersona,abreviatura,ctipoidentificacion   
                    $lastInsertId = $pdoupdate_insert->lastInsertId();

                    $tpersonaListadataadicional->execute(
                        array(
                            'cpersona' => $lastInsertId,
                            //  'carea'     =>  $this->capturarCentrodeCostoSCP($this->capturarCentrodeCosto($row["documento_empleado"])),
                            //'fingreso' => $row["fecha_inicio"],
                            'estado' =>   'ACT',
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
                            //'genero' => '' ,
                            //'estadocivil' => '',
                            'cnacionalidad' =>   $this->capturarNacionalidadEmpleado($this->capturarPais($row["id_nacionalidad_empl"]))
                        )
                    );

                    //echo $row["documento_empleado"].' '.$row["nombre"].' '.$row["ape_paterno_empl"].' '.$row["codigo_sig"].' - '.$var1.' - '.$var2. "\n";
                    $countInsertados++;
                
            endforeach;
            $this->conexionpdoPostgresProducctionSCPv2_tbl_log($fechaActual, " $countActualizados Usuarios de planilla actualizados", 'Éxito');
            $this->conexionpdoPostgresProducctionSCPv2_tbl_log($fechaActual, " $countInsertados Usuarios de planilla insertados", 'Éxito');
        } catch (PDOException $e) {
            echo  $e->getMessage();
        }
    }

    public function dataUsuariosLogistica() {

        // Lista de solo usuarios por honorarios en GH
        $queryuser_fd = "select DISTINCT(LTRIM(RTRIM(emp.documento_empleado)))  as documento_empleado,tus.id,emp.id pk_fd,emp.ape_paterno_empl, emp.ape_materno_empl,emp.nombre,emp.sexo_empl,id_estado_civil,
        emp.email, emp.email_laboral,max(conn.id) as ultimocontrato,conn.fecha_fin_prog ,conn.id_tipo_contrato,conn.fecha_inicio,emp.id_nacionalidad_empl,tus.nombre as abreviatura
       from empleado emp
       left join (select * from rh_contrato con where con.fecha_fin_prog is null or con.fecha_fin_prog >= getdate()) conn on emp.id = conn.id_empleado
       left join usuario tus on tus.id_empleado_default = emp.id
       where conn.id is  not NULL 
       and conn.id_tipo_contrato = 11 
       group by emp.documento_empleado ,emp.ape_paterno_empl, emp.ape_materno_empl,emp.nombre,emp.sexo_empl,emp.email,emp.email_laboral,emp.id,conn.fecha_fin_prog,
       conn.id_tipo_contrato,conn.fecha_inicio,emp.id_nacionalidad_empl,tus.nombre,tus.id ,id_estado_civil;";
       $userfdRXH = $this->conexionpdoSQL()->query($queryuser_fd);
       
       $userfd = $userfdRXH->fetchAll();
       // Lista de Usuarios de Logistica
       $dataUserLogistica = $this->dataUserRecibosHonorarios();

       $pdoupdate_insert = $this->conexionpdoPostgresProductionSCPv2();
       $cesarusuariossincontratoRXH = 0;
       $countUserHActualizados = 0;
       $countUserHInsertados = 0;
       $result_set = $pdoupdate_insert->prepare("UPDATE tpersona SET nombre = :nombre,
                                                                     identificacion = :identificacion,
                                                                     ctipopersona = :ctipopersona,
                                                                     abreviatura =:abreviatura,
                                                                     ctipoidentificacion = :ctipoidentificacion,
                                                                     pk_fd = :pk_fd
                                                                     WHERE identificacion = :identificacion ");


       $tpersonaListadataadicional_update = $pdoupdate_insert->prepare("UPDATE tpersonadatosempleado SET cpersona = :cpersona,
                                                                                                         estado   = :estado,
                                                                                                         ctipocontrato = :ctipocontrato,
                                                                                                         email = :email,
                                                                                                         email_laboral= :email_laboral
                                                                                                       FROM (
                                                                                                       select tp.cpersona from tpersona tp
                                                                                                       inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                                                                                                       where tp.cpersona = :cpersona
                                                                                                       ) AS subquery 
                                                                                                       WHERE tpersonadatosempleado.cpersona=subquery.cpersona");


       $tpersonaListadataadicionalinforbasica_update = $pdoupdate_insert->prepare("UPDATE tpersonanaturalinformacionbasica SET cpersona = :cpersona,
                                                                                                               apaterno = :apaterno,
                                                                                                               amaterno = :amaterno,
                                                                                                               nombres = :nombres,
                                                                                                               -- genero = :genero,
                                                                                                               --estadocivil = :estadocivil,
                                                                                                               esempleado = :esempleado,
                                                                                                               cnacionalidad = :cnacionalidad
                                                                                                           FROM (
                                                                                                           select  tp.cpersona from tpersona tp
                                                                                                           inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                                                                                                           where tp.cpersona = :cpersona
                                                                                                           ) AS subquery 
                                                                                                           WHERE tpersonanaturalinformacionbasica.cpersona=subquery.cpersona");



       $result_set_insert = $pdoupdate_insert->prepare("INSERT INTO tpersona (nombre,identificacion,ctipopersona,abreviatura,ctipoidentificacion,pk_fd) 
                                                                           VALUES (:nombre,:identificacion,:ctipopersona,:abreviatura,:ctipoidentificacion,:pk_fd)");

       $tpersonaListadataadicional = $pdoupdate_insert->prepare("INSERT INTO tpersonadatosempleado (cpersona,estado,ctipocontrato,email,email_laboral) 
                                                                           VALUES (:cpersona,:estado,:ctipocontrato,:email,:email_laboral)");

       $tpersonaListadataadicionalinforbasica = $pdoupdate_insert->prepare("INSERT INTO tpersonanaturalinformacionbasica (cpersona,apaterno,amaterno,nombres,esempleado,cnacionalidad) 
                                                                           VALUES (:cpersona,:apaterno,:amaterno,:nombres,:esempleado,:cnacionalidad)");
        echo(count($userfd))."\n";
        echo(count($dataUserLogistica))."\n";
       // print_r($userfd);// documento_empleado // ape_paterno_empl // ape_materno_empl
       // print_r($dataUserLogistica);

       try {

           $date = new DateTime("now", new DateTimeZone('America/Lima') );
           $fechaActual =  $date->format('Y-m-d H:i:s');

           foreach ($userfd as $row) :
               foreach($dataUserLogistica  as $rowl):
                    // Actualizar la data de los RXH que estan registrado en LOGISTICA - GH - SCP
                    if( $row['documento_empleado'] == $rowl['documento_empleado']  && $this->existeUsuarioLogisticaenelSCP($rowl['documento_empleado']) == 'user_registrado' ){
                       
                        $countUserHActualizados++;
                        $result_set->execute(array(
                            'nombre'                 =>  ucwords(mb_strtolower($rowl["ape_paterno"])) . ' ' . ucwords(mb_strtolower($rowl["ape_materno"])) . ' ' . ucwords(mb_strtolower($rowl["nombre"])),
                            'ctipopersona'           => 'NAT',
                            'ctipoidentificacion'    => 2,
                            'abreviatura'            => $rowl["abreviatura"],
                            'identificacion'         => $rowl["documento_empleado"],
                            'pk_fd' => $row["pk_fd"]
                        ));

                        $tpersonaListadataadicional_update->execute(array(
                            'cpersona' => $this->capturarCPersona($rowl["documento_empleado"]),
                            'estado'    =>   'ACT',
                            'ctipocontrato'    => 'RXH',
                            'email'  => $rowl["email"],
                            'email_laboral'  => $rowl["email_laboral"]
                        ));
                        $tpersonaListadataadicionalinforbasica_update->execute(array(
                            'cpersona' => $this->capturarCPersona($rowl["documento_empleado"]),
                            'apaterno' => $rowl["ape_paterno"],
                            'amaterno' =>   $rowl["ape_materno"],
                            'nombres' =>   $rowl["nombre"],
                            'esempleado' =>  1,
                            'cnacionalidad' => $this->capturarNacionalidadEmpleado($this->capturarPais($rowl["id_nacionalidad_empl"]))
                        ));
                       //echo($rowl["ape_paterno"] . '))))))(((((' . ucwords(mb_strtolower($rowl["ape_materno"])) . ' ' . ucwords(mb_strtolower($rowl["nombre"])))."\n";
                        //echo ("ola----");
                        continue 2 ;
                    }

                    // INSERTAR la data de los RXH que estan registrado en LOGISTICA - GH al SCP

                     if ($row['documento_empleado'] == $rowl['documento_empleado']  && $this->existeUsuarioLogisticaenelSCP($rowl['documento_empleado']) == 'user_sin_registrar')
                    {
                        $countUserHInsertados++;
                        $result_set_insert->execute(array(
                            'nombre'          => ucwords(mb_strtolower($rowl["ape_paterno"])) . ' ' . ucwords(mb_strtolower($rowl["ape_materno"])) . ' ' . ucwords(mb_strtolower($rowl["nombre"])),
                            'identificacion'      => $rowl["documento_empleado"],
                            'ctipopersona'        => 'NAT',
                            'abreviatura' =>  $rowl["abreviatura"],
                            'ctipoidentificacion' => 2,
                            'pk_fd' => $row["pk_fd"]
                        ));
                        //nombre,identificacion,ctipopersona,abreviatura,ctipoidentificacion   
                        $lastInsertId = $pdoupdate_insert->lastInsertId();
                        $tpersonaListadataadicional->execute(
                            array(
                                'cpersona' => $lastInsertId,
                                'estado'    =>   'ACT',
                                'ctipocontrato'    => $this->capturarTipoContrato($row["id_tipo_contrato"]),
                                'email'  => $rowl["email"],
                                'email_laboral'  => $rowl["email_laboral"]
                            )
                        );
     
                        $tpersonaListadataadicionalinforbasica->execute(
                            array(
                                'cpersona' => $lastInsertId,
                                'apaterno' => $rowl["ape_paterno"],
                                'amaterno' =>   $rowl["ape_materno"],
                                'nombres' =>   $rowl["nombre"],
                                'esempleado' =>  1,
                                'cnacionalidad' =>   $this->capturarNacionalidadEmpleado($this->capturarPais($row["id_nacionalidad_empl"]))
                            )
                        );
                        //echo($rowl["ape_paterno"] . '||||||||' . ucwords(mb_strtolower($rowl["ape_materno"])) . '++++++' . ucwords(mb_strtolower($rowl["nombre"])))."\n";
                        continue 2;
                    }
                  
               endforeach; 
               // INACTIVAR A LOS USUARIOS QUE SU ORDEN DE SERVICIO ESTA VENCIDO 
               $dniperu = $row["documento_empleado"];

               $cesarusuariossincontratoRXH++;
                $query = "UPDATE tpersonadatosempleado
                SET estado='INA'
                FROM (
                select tp.cpersona,tp.identificacion,tpde.estado,tp.nombre from tpersona tp
                inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                where identificacion = '$dniperu'
                ) AS subquery
                WHERE tpersonadatosempleado.cpersona=subquery.cpersona;";
                $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
                $stmt->execute();
                $data = $stmt->fetchAll();


                $queryuser = "UPDATE tusuarios
                SET estado='INA'
                FROM (
                select tp.cpersona,tp.identificacion,tpde.estado,tp.nombre from tpersona tp
                inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                where identificacion = '$dniperu'
                ) AS subquery
                WHERE tusuarios.cpersona=subquery.cpersona;";
                $stmtuser = $this->conexionpdoPostgresProductionSCPv2()->prepare($queryuser);
                $stmtuser->execute();
                $datauser = $stmtuser->fetchAll();

               echo($row["documento_empleado"].'********'.$row["ape_paterno_empl"].' '.$row["ape_materno_empl"].' '.$row["nombre"])."\n";

           endforeach;
          /*  echo($cesarusuariossincontratoRXH)."\n";
           echo($countUserHActualizados)."\n";
           echo($countUserHInsertados)."\n"; */
           
           $this->conexionpdoPostgresProducctionSCPv2_tbl_log($fechaActual, "$countUserHActualizados Usuarios de RXH actualizados", 'Éxito');
           $this->conexionpdoPostgresProducctionSCPv2_tbl_log($fechaActual, "$countUserHInsertados Usuarios de RXH insertados", 'Éxito');
       } catch (PDOException $e) {
           echo  $e->getMessage();
       }


    }

    public function actualizarusuariosCesados()
    {
        // Data del flowdesk empleado a actualizar 
        $queryuser_fd = " SELECT DISTINCT e.id,LTRIM(RTRIM(e.documento_empleado)) as documento_empleado ,e.id pk_fd,e.nombre,e.ape_paterno_empl,e.ape_materno_empl,e.email,e.email_laboral,us.nombre as abreviatura,e.id_nacionalidad_empl,
        (SELECT MAX(rh_c.id) FROM rh_contrato as rh_c where e.id = rh_c.id_empleado)  as ultimocontrato ,
        (SELECT MAX(rh_c.id_tipo_contrato) FROM rh_contrato as rh_c where e.id = rh_c.id_empleado)  as id_tipo_contrato ,
        --(SELECT max(sub.codigo) FROM rh_contrato as rh_cd where rh_cd.id = (SELECT MAX(rh_c.id) FROM rh_contrato as rh_c where e.id = rh_c.id_empleado))  as codigocentroCosto ,
        (SELECT MAX(sub.codigo) FROM subcentrocosto as sub where sub.id = (SELECT MAX(rh_cd.id_sub_centrocosto) FROM rh_contrato as rh_cd where e.id = rh_cd.id_empleado))  as codigo_sig ,
        (SELECT MAX(sub.descripcion) FROM subcentrocosto as sub where sub.id = (SELECT MAX(rh_cd.id_sub_centrocosto) FROM rh_contrato as rh_cd where e.id = rh_cd.id_empleado))  as scc ,
        (SELECT MAX(rh_cfc.fecha_fin_prog) FROM rh_contrato as rh_cfc where rh_cfc.id =  (SELECT MAX(rh_c.id) FROM rh_contrato as rh_c where e.id = rh_c.id_empleado))   as fechafinContrato 
        from empleado as e
        left join usuario as us on e.id = us.id_empleado_default
        --where LTRIM(RTRIM(e.documento_empleado)) = '142051476'
        where e.id not in 
        (
        565,566,567,568,569,570,571,572,573,574,575,576,577,578,579,580,581,583,584,585,586,587,588,589,590,591,592,593,596,608,609,610,611,612,613,614,616,617,618,619,620,621,622
        ) and 
        (SELECT MAX(rh_cfc.fecha_fin_prog) FROM rh_contrato as rh_cfc where rh_cfc.id =  (SELECT MAX(rh_c.id) FROM rh_contrato as rh_c where e.id = rh_c.id_empleado) ) is not null 
        and (SELECT MAX(rh_cfc.fecha_fin_prog) FROM rh_contrato as rh_cfc where rh_cfc.id =  (SELECT MAX(rh_c.id) FROM rh_contrato as rh_c where e.id = rh_c.id_empleado)) <= GETDATE() 
        order by e.ape_paterno_empl DESC;";
        $userfd = $this->conexionpdoSQL()->query($queryuser_fd);

        /*  $userfd->execute();
        $listArray = $userfd->fetchAll();

        var_dump($listArray); */

        // Data del SCP
        $queryscp = "select distinct(trim(identificacion)) as documento_empleado from tpersona where ctipopersona = 'NAT'";
        $pdo = $this->conexionpdoPostgresProductionSCPv2()->query($queryscp);
        $listArrayscp2 = $pdo->fetchAll(PDO::FETCH_OBJ);
        $listArrayscp = array_column($listArrayscp2, "documento_empleado");
        //$listArrayscp = trim($listArrayscp);

        //print_r($listArrayscp);

        $pdoupdate_insert = $this->conexionpdoPostgresProductionSCPv2();
        $count = 0;
        $result_set = $pdoupdate_insert->prepare("UPDATE tpersona SET nombre = :nombre,
                                                                      identificacion = :identificacion,
                                                                      ctipopersona = :ctipopersona,
                                                                      abreviatura =:abreviatura,
                                                                      ctipoidentificacion = :ctipoidentificacion,
                                                                      pk_fd = :pk_fd
                                                                      WHERE identificacion = :identificacion ");


        $tpersonaListadataadicional_update = $pdoupdate_insert->prepare("UPDATE tpersonadatosempleado SET cpersona = :cpersona,
                                                                                                          estado   = :estado,
                                                                                                          ctipocontrato = :ctipocontrato,
                                                                                                          email = :email,
                                                                                                          email_laboral= :email_laboral

                                                                                                         
                                                                                                        FROM (
                                                                                                        select tp.cpersona from tpersona tp
                                                                                                        inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                                                                                                        where tp.cpersona = :cpersona
                                                                                                        ) AS subquery 
                                                                                                        WHERE tpersonadatosempleado.cpersona=subquery.cpersona");


        $tpersonaListadataadicionalinforbasica_update = $pdoupdate_insert->prepare("UPDATE tpersonanaturalinformacionbasica SET cpersona = :cpersona,
                                                                                                                apaterno = :apaterno,
                                                                                                                amaterno = :amaterno,
                                                                                                                nombres = :nombres,
                                                                                                                esempleado = :esempleado,
                                                                                                                cnacionalidad = :cnacionalidad
                                                                                                               
                                                                                                            FROM (
                                                                                                            select  tp.cpersona from tpersona tp
                                                                                                            inner join tpersonadatosempleado tpde on tpde.cpersona = tp.cpersona
                                                                                                            where tp.cpersona = :cpersona
                                                                                                            ) AS subquery 
                                                                                                            WHERE tpersonanaturalinformacionbasica.cpersona=subquery.cpersona");
        foreach ($userfd as $row) :
            if (in_array(intval($row["documento_empleado"]), $listArrayscp)) {

                //echo(ucwords(mb_strtolower($row["ape_paterno_empl"])).' '. ucwords(mb_strtolower($row["ape_materno_empl"])).' '. ucwords(mb_strtolower($row["nombre"]))). "\n";
                $result_set->execute(array(
                    'nombre' =>  ucwords(mb_strtolower($row["ape_paterno_empl"])) . ' ' . ucwords(mb_strtolower($row["ape_materno_empl"])) . ' ' . ucwords(mb_strtolower($row["nombre"])),
                    'ctipopersona'        => 'NAT',
                    'ctipoidentificacion' => 2,
                    'abreviatura' => $row["abreviatura"],
                    'identificacion' => $row["documento_empleado"],
                    'pk_fd'  => $row["pk_fd"]
                ));
                $tpersonaListadataadicional_update->execute(array(
                    'cpersona' => $this->capturarCPersona($row["documento_empleado"]),
                    'estado'    =>   'INA',
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

                //  echo $row["documento_empleado"].' '.$row["nombre"].' '.$row["ape_paterno_empl"].'----------------------------------------------'."\n";

                continue;
            }
        // echo(ucwords(mb_strtolower($row["ape_paterno_empl"])).' '. ucwords(mb_strtolower($row["ape_materno_empl"])).' '. ucwords(mb_strtolower($row["nombre"]))). "\n";
        //echo $row["documento_empleado"].' '.$row["nombre"].' '.$row["ape_paterno_empl"].' '.$row["codigo_sig"].' - '.$var1.' - '.$var2. "\n";
        // $count++;
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
        where rh.fecha_fin_prog is  null or rh.fecha_fin_prog >= getdate();  ";
        $userfd = $this->conexionpdoSQL()->query($queryuser_fd);
        $listArrayfd = $userfd->fetchAll(PDO::FETCH_OBJ);
        $listArrayfd = array_column($listArrayfd, "documento_empleado");
        $trimmed_arrayfd = array_map('trim', $listArrayfd);
        sort($trimmed_arrayfd);
        //print_r($trimmed_arrayfd);

        // No tienenn contrato || contrato vencido dato historico
        $queryuser_fdNC = "select distinct(rh.id_empleado),max(rh.fecha_fin_prog),emp.documento_empleado, rh.fecha_fin_prog from rh_contrato rh
        inner join empleado emp on emp.id = rh.id_empleado
        where rh.fecha_fin_prog is not null 
        group by rh.id_empleado,emp.documento_empleado, rh.fecha_fin_prog
       HAVING rh.fecha_fin_prog <= GETDATE(); ";
        $userfdNC = $this->conexionpdoSQL()->query($queryuser_fdNC);
        $listArrayfdNC = $userfdNC->fetchAll(PDO::FETCH_OBJ);
        $listArrayfdNC = array_column($listArrayfdNC, "documento_empleado");
        $trimmed_arrayfdNC = array_map('trim', $listArrayfdNC);
        sort($trimmed_arrayfdNC);

        //print_r($trimmed_arrayfdNC);
        // Separo las personas que no tienen contrato
        $diff = array_diff($trimmed_arrayfdNC, $trimmed_arrayfd);
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
        $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
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
        $stmtuser = $this->conexionpdoPostgresProductionSCPv2()->prepare($queryuser);
        $stmtuser->execute();
        $datauser = $stmtuser->fetchAll();
    }

    public function capturarCPersona($documento_empleado)
    {
        $query = "SELECT cpersona FROM tpersona WHERE identificacion = '$documento_empleado'";
        $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
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
        $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
        $stmt->execute();
        $data = $stmt->fetch();
        //echo $data['cpais'];
        return $data['cpais'];
    }

    public function capturarCentrodeCosto($documento_empleado)
    {
        $querycc_fd = "select DISTINCT(LTRIM(RTRIM(emp.documento_empleado)))  as documento_empleado,scc.descripcion as centro_costo ,scc.codigo as codigo_sig,max(conn.id) as ultimocontrato
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

        return trim($codigo_sig_fd['codigo_sig']);
    }

   /*  public function capturarCentrodeCostoSCP($codigo_sig)
    {
        $query = "SELECT carea FROM tareas WHERE codigo_sig = '$codigo_sig'";
        $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
        $stmt->execute();
        $data = $stmt->fetch();
        return $data['carea'];
    } */

    public function capturarTipoContrato($codigo)

    {
        $querylista = "SELECT * FROM ttipocontrato";
        $stmtlista = $this->conexionpdoPostgresProductionSCPv2()->prepare($querylista);
        $stmtlista->execute();
        $datalista = $stmtlista->fetchAll();

        $ar = [];
        foreach ($datalista as $dt) :
            $col['pk'] = explode(",", $dt['pk_contratofd']);
            $col['tipo'] = $dt['ctipocontrato'];
            array_push($ar, $col);
        endforeach;
        //print_r($ar);

        foreach ($ar as $da) :
            foreach ($da['pk'] as $d => $value) :

                if ($value == $codigo) {
                    return $da['tipo'];
                }
                continue;
            endforeach;
        endforeach;

        //echo($fin);

    }
    public function borrarEspaciosdocumento_empleado()
    {
        $query = "update tpersona set identificacion = trim(identificacion);";
        $stmt = $this->conexionpdoPostgresProductionSCPv2()->prepare($query);
        $stmt->execute();
    }

    public function dataUserRecibosHonorarios()
    {
        $queryuser_fd = "select pro.id,substring(pro.numerodocumento ,3,8) as documento_empleado,pro.nombre,pro.ape_paterno,pro.ape_materno,MAX(oc.id) as ultimocontrato_orden_compra,
        (select MAX(ocap.id) from ordencompra_aprobacion as ocap where MAX(oc.id) = ocap.id_ordencompra ) as estado ,
        (select (ocapultimo.id_estado) from ordencompra_aprobacion as ocapultimo where ocapultimo.id = (select MAX(ocap.id) from ordencompra_aprobacion as ocap where MAX(oc.id) = ocap.id_ordencompra ) ) as  estado_aprovacion,
        (select MAX(ocre.fecha_real_item) from ordencompra_item as ocre where MAX(oc.id) = ocre.id_ordencompra ) as fechainiciocontrato,
        (select MAX(ocit.fecha_termino_item) from ordencompra_item as ocit where MAX(oc.id) = ocit.id_ordencompra ) as fechafin,
        (select MAX(subccc.descripcion) from ordencompra_item as ocitcc inner join subcentrocosto subccc on subccc.id = ocitcc.id_sub_centrocosto  where MAX(oc.id) = ocitcc.id_ordencompra ) as id_centro_costo,
        DATEDIFF(DAY, (select MAX(ocre.fecha_real_item) from ordencompra_item as ocre where MAX(oc.id) = ocre.id_ordencompra ),(select MAX(ocit.fecha_termino_item) from ordencompra_item as ocit where MAX(oc.id) = ocit.id_ordencompra )) as diascontradatado
        from proveedor pro
        inner join (select * from ordencompra) oc on oc.id_proveedor = pro.id
        where id_tipo_comprobante in (49,60)
        group by pro.id,pro.numerodocumento,pro.nombre,pro.ape_paterno,pro.ape_materno
        HAVING 
        (select MAX(ocit.fecha_termino_item) from ordencompra_item as ocit where MAX(oc.id) = ocit.id_ordencompra ) >= getDate()  
        and
        DATEDIFF(DAY, MAX(oc.fechacreacion),(select MAX(ocit.fecha_termino_item) from ordencompra_item as ocit where MAX(oc.id) = ocit.id_ordencompra )) > 1 
        and 
        (select (ocapultimo.id_estado) from ordencompra_aprobacion as ocapultimo where ocapultimo.id = (select MAX(ocap.id) from ordencompra_aprobacion as ocap where MAX(oc.id) = ocap.id_ordencompra ) )  = 2
        order by pro.ape_paterno ASC;";
        $userrh = $this->conexionpdoSQL()->query($queryuser_fd);
        $userfdRecibosHonorarios = $userrh->fetchAll();
        $listArraydni = array_column($userfdRecibosHonorarios, "documento_empleado");
        $dnilista = implode("','", $listArraydni);

       
        $queryuserlogistica = "SELECT  RTRIM(LTRIM(em.documento_empleado)) as documento_empleado,pro.nombre,pro.ape_paterno,pro.ape_materno,em.email,em.email_laboral,em.id_nacionalidad_empl,us.nombre as abreviatura from empleado as em
        left join usuario as us on  us.id_empleado_default = em.id
        left join proveedor pro on substring(pro.numerodocumento ,3,8) = RTRIM(LTRIM(em.documento_empleado))
        where em.documento_empleado in ('$dnilista');";

        $pdo = $this->conexionpdoSQL()->query($queryuserlogistica);
        $dataRH = $pdo->fetchAll();
        return $dataRH;

    }

    public function existeUsuarioLogisticaenelSCP($dni)
    {
      
        $query = "SELECT * FROM tpersona where identificacion = '$dni'";
        $pdo_test = $this->conexionpdoPostgresProductionSCPv2()->query($query);
        $row_count = $pdo_test->rowCount();
        $res = ($row_count > 0) ? 'user_registrado' : 'user_sin_registrar';
        return $res;

    }
}


$foo = new obtenerDataUsuariosFlowdesk();
// Borrar los espacios en blanco
$foo->borrarEspaciosdocumento_empleado();

// Inserta la data de usuarios Todos los tipos de contratos a execepcion  de recibos por Honorarios
$foo->dataUsuariosFlowdesk();

// Inactiva a los usuarios tanto en personal como usuarios planilla \\ Practicas
$foo->empleadosInactivos();

//  Actualiza la data de los usuarios cesados
$foo->actualizarusuariosCesados();

// Inserta \\ actualiza \\  Inactiva del sistema que estan recibos por Honorarios
$foo->dataUsuariosLogistica();


