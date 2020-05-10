<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, Content-Type, Authorization, Accept, X-Requested-With, x-xsrf-token");
    header('Content-Type: application/json; charset=utf-8');

    include "config.php";

    $postjson = json_decode(file_get_contents('php://input'), true);

    //REGISTRO USUARIO
    if ($postjson['aksi'] == "proses_register") {

        $cekemail = mysqli_fetch_array(mysqli_query($mysqli, "CALL ValidarEmail ('$postjson[correo]');"));

        //mysqli_free_result($cekemail);  
        mysqli_next_result($mysqli); 
        if ($cekemail['correo'] == $postjson['correo']) {
            $result = json_encode(array('success'=>false, 'msg'=>'Correo ya registrado'));
        } else {
            
            $password = md5($postjson['contrasena']);
            $insert = mysqli_query($mysqli, "CALL CrearUsuario(
                '$postjson[pApellido]',
                '$postjson[sApellido]',
                '$postjson[pNombre]',
                '$postjson[sNombre]',
                '$postjson[codigo]',
                '$password',
                '$postjson[correo]',
                '$postjson[direccion]',
                '$postjson[fechaNacimiento]',
                '$postjson[departamento]',
                '$postjson[genero]',
                '$postjson[municipio]',
                '$postjson[tipDoc]',
                '$postjson[numDoc]');
            ");

            //$id = mysqli_insert_id($mysqli);
            if ($insert) {
                $result = json_encode(array('success'=>true, 'msg'=>'Registro existoso'));
            } else {
                $result = json_encode(array('success'=>false, 'msg'=>'No se realizó el registro'));
            }
        }
        echo $result;
    }
    //LOGIN
    else if ($postjson['aksi'] == "proses_login") {
        $password = md5($postjson['contrasena']);
        $logindata = mysqli_fetch_array(mysqli_query($mysqli, "CALL Login('$postjson[correo]', '$password')"));

        $data = array(
            'idPersona'               =>    $logindata['id_persona'],
            'pApellido'               =>    $logindata['primer_apellido'],
            'sApellido'               =>    $logindata['segundo_apellido'],
            'pNombre'                 =>    $logindata['primer_nombre'],
            'sNombre'                 =>    $logindata['segundo_nombre'],
            'tipDoc'                  =>    $logindata['FK_id_tipo_documento'],
            'numDoc'                  =>    $logindata['numero_documento'],
            'codigo'                  =>    $logindata['codigo'],
            'correo'                  =>    $logindata['correo'],
            'departamento'            =>    $logindata['FK_id_departamento'],
            'municipio'               =>    $logindata['FK_id_municipio'],
            'direccion'               =>    $logindata['direccion'],
            'fechaNacimiento'         =>    $logindata['fecha_nacimiento'],
            'genero'                  =>    $logindata['FK_id_genero'],
            'rol'                     =>    $logindata['FK_id_rol'],
            'estadoCuenta'            =>    $logindata['estado_cuenta'],
            'puntaje'                 =>    $logindata['puntaje']
        );
        if ($logindata) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "load_departamentos") {
        $data = array();

        $query = mysqli_query($mysqli, "CALL Departamentos();");

        while ($rows = mysqli_fetch_array($query)) {
            $data[] = array(
                'nombreDepartamentos' => $rows['nombre_departamento'],
                'idDepartamento' => $rows['id_departamento']
            );
        }
        if ($query) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "load_municipios") {

        $query = mysqli_query($mysqli, "CALL Municipios('$postjson[idDepartamento]')");

        while ($rows = mysqli_fetch_array($query)) {
            $data[] = array(
                'nombreMunicipios' => $rows['nombre_municipio'],
                'idMunicipio' => $rows['id_municipio']
            );
        }
        if ($query) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "load_tipoDocumento") {
        $data = array();

        $query = mysqli_query($mysqli, "CALL TipoDocumentos();");


        while ($rows = mysqli_fetch_array($query)) {
            $data[] = array(
                'tipoDocumentos' => $rows['nombre_tipo_documento'],
                'idTipoDocumentos' => $rows['id_tipo_documento']
            );
        }
        if ($query) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "load_reciduos") {
        $data = array();

        $query = mysqli_query($mysqli, "CALL Residuos();");


        while ($rows = mysqli_fetch_array($query)) {
            $data[] = array(
                'id_residuo' => $rows['id_residuo'],
                'nombre_residuo' => $rows['nombre_residuo'],
                'descripcion_residuo' => $rows['descripcion_residuo'],
                'imagen' => $rows['imagen'],
                'puntos' => $rows['puntos']
            );
        }
        if ($query) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    }
     else if ($postjson['aksi'] == "proses_update") {

        $password = md5($postjson['contrasena']);
        $cekpassword = mysqli_fetch_array(mysqli_query($mysqli, "CALL SelectContrasena('$postjson[id]');"));
        mysqli_next_result($mysqli); 

        if ($postjson['correo'] != $postjson['auxCorreo']) {
            $cekemail = mysqli_fetch_array(mysqli_query($mysqli, "CALL ValidarEmail ('$postjson[correo]');"));
            mysqli_next_result($mysqli); 
            if ($cekemail['correo'] == $postjson['correo']) {
                $result = json_encode(array('success'=>false, 'msg'=>'Correo ya registrado'));
            } else {
                if ($postjson['contrasena'] == "") {
                    $password = $cekpassword['contrasena'];
                } else {
                    $password = md5($postjson['contrasena']);
                }
                    
                $update = mysqli_query($mysqli, "CALL ActualizarUsuario( 
                    '$postjson[pApellido]',
                    '$postjson[sApellido]',
                    '$postjson[pNombre]',
                    '$postjson[sNombre]',
                    '$postjson[codigo]',
                    '$password',
                    '$postjson[correo]',
                    '$postjson[direccion]',
                    '$postjson[fechaNacimiento]',
                    '$postjson[departamento]',
                    '$postjson[genero]',
                    '$postjson[municipio]',
                    '$postjson[tipDoc]',
                    '$postjson[numDoc]',
                    '$postjson[id]');
                ");
                if ($update) {
                    $result = json_encode(array('success'=>true, 'msg'=>'Actualización existosa'));
                } else {
                    $result = json_encode(array('success'=>false, 'msg'=>'No se realizó la actualización'));
                }
            }
        } else {
            if ($postjson['contrasena'] == "") {
                $password = $cekpassword['contrasena'];
            } else {
                $password = md5($postjson['contrasena']);
            }
                
            $update = mysqli_query($mysqli, "CALL ActualizarUsuario( 
                '$postjson[pApellido]',
                '$postjson[sApellido]',
                '$postjson[pNombre]',
                '$postjson[sNombre]',
                '$postjson[codigo]',
                '$password',
                '$postjson[correo]',
                '$postjson[direccion]',
                '$postjson[fechaNacimiento]',
                '$postjson[departamento]',
                '$postjson[genero]',
                '$postjson[municipio]',
                '$postjson[tipDoc]',
                '$postjson[numDoc]',
                '$postjson[id]');
            ");
            if ($update) {
                $result = json_encode(array('success'=>true, 'msg'=>'Actualización existosa'));
            } else {
                $result = json_encode(array('success'=>false, 'msg'=>'No se realizó la actualización'));
            }
        }
        echo $result;
    } else if ($postjson['aksi'] == "load_data") {

        $query = mysqli_query($mysqli, "CALL CargarDatos('$postjson[id]');");
        
        while ($rows = mysqli_fetch_array($query)) {
            $data = array(
                'pApellido'               =>    $rows['primer_apellido'],
                'sApellido'               =>    $rows['segundo_apellido'],
                'pNombre'                 =>    $rows['primer_nombre'],
                'sNombre'                 =>    $rows['segundo_nombre'],
                'tipDoc'                  =>    $rows['FK_id_tipo_documento'],
                'numDoc'                  =>    $rows['numero_documento'],
                'codigo'                  =>    $rows['codigo'],
                'correo'                  =>    $rows['correo'],
                'departamento'            =>    $rows['FK_id_departamento'],
                'municipio'               =>    $rows['FK_id_municipio'],
                'direccion'               =>    $rows['direccion'],
                'fechaNacimiento'         =>    $rows['fecha_nacimiento'],
                'genero'                  =>    $rows['FK_id_genero']
            );
        }

        if ($query) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "proses_sendData") {
        
        $logindata = mysqli_fetch_array(mysqli_query($mysqli, "CALL EnviarDatos('$postjson[id]');"));

        $data = array(
            'idPersona'               =>    $logindata['id_persona'],
            'pApellido'               =>    $logindata['primer_apellido'],
            'sApellido'               =>    $logindata['segundo_apellido'],
            'pNombre'                 =>    $logindata['primer_nombre'],
            'sNombre'                 =>    $logindata['segundo_nombre'],
            'tipDoc'                  =>    $logindata['FK_id_tipo_documento'],
            'numDoc'                  =>    $logindata['numero_documento'],
            'codigo'                  =>    $logindata['codigo'],
            'correo'                  =>    $logindata['correo'],
            'departamento'            =>    $logindata['FK_id_departamento'],
            'municipio'               =>    $logindata['FK_id_municipio'],
            'direccion'               =>    $logindata['direccion'],
            'fechaNacimiento'         =>    $logindata['fecha_nacimiento'],
            'genero'                  =>    $logindata['FK_id_genero'],
            'rol'                     =>    $logindata['FK_id_rol'],
            'estadoCuenta'            =>    $logindata['estado_cuenta'],
            'puntaje'                 =>    $logindata['puntaje']
        );
        if ($logindata) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "disable_count") {
        $query = mysqli_query($mysqli, "CALL DeshabilitarCuenta('$postjson[id]');");
        if ($query) {
            $result = json_encode(array('success'=>true));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "email_auth") {

        $cekemail = mysqli_fetch_array(mysqli_query($mysqli, "CALL ValidarEmail ('$postjson[email]');"));

        mysqli_next_result($mysqli); 

        $tokenMail = mysqli_query($mysqli, "CALL TokenCorreo ('$postjson[email]', '$postjson[token]');");

        $data = array(
            'id' => $cekemail['id_persona']
        );

        if ($cekemail['correo'] == $postjson['email']) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "validate_tokenEmail") {

        $id = mysqli_fetch_array(mysqli_query($mysqli, "CALL ValidarToken('$postjson[id]','$postjson[token]');"));

        if ($id['FK_id_persona'] == $postjson['id']) {
            $result = json_encode(array('success'=>true));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "update_Password") {
        $password = md5($postjson['contrasena']);

        $changePass = mysqli_query($mysqli, "CALL CambioContrasena('$postjson[id]','$password');");

        if ($changePass) {
            $result = json_encode(array('success'=>true));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "generate_code") {

        $generateCode = mysqli_query($mysqli, "CALL GenerarCodigo('$postjson[id]','$postjson[documento]','$postjson[codigo]','$postjson[residuo]');");

        if ($generateCode) {
            $result = json_encode(array('success'=>true));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "enter_code") {

        $enterCode = mysqli_fetch_array(mysqli_query($mysqli, "CALL IngresarCodigo('$postjson[id]','$postjson[codigo]');"));

        if ($enterCode) {
            $result = json_encode(array('success'=>true));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "generate_code_amount") {

        $generateCode = mysqli_query($mysqli, "CALL GenerarCodigoCantidad('$postjson[codigo]','$postjson[id]','$postjson[incentivo]');");

        if ($generateCode) {
            $result = json_encode(array('success'=>true));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "load_incentivos") {
        $data = array();

        $query = mysqli_query($mysqli, "CALL Incentivos();");


        while ($rows = mysqli_fetch_array($query)) {
            $data[] = array(
                'id_incentivo' => $rows['id_incentivo'],
                'nombre_incentivo' => $rows['nombre_incentivo'],
                'descripsion_incentivo' => $rows['descripsion_incentivo'],
                'valor' => $rows['valor'],
                'imagen' => $rows['imagen']
            );
        }
        if ($query) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "enter_code_cant") {

        $enterCode = mysqli_query($mysqli, "CALL IngresarCodigoCantidad('$postjson[codigo]','$postjson[documento]', '$postjson[id]');");

        if ($enterCode) {
            $result = json_encode(array('success'=>true));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "load_usuarios") {
        $data = array();

        $query = mysqli_query($mysqli, "CALL Usuarios();");


        while ($rows = mysqli_fetch_array($query)) {
            $data[] = array(
                'Usuario' => $rows['Usuario'],
                'Num_Documento' => $rows['Num. Documento'],
                'Codigo' => $rows['Codigo'],
                'Correo' => $rows['Correo'],
                'Actividad' => $rows['Actividad'],
                'fecha_modificacion' => $rows['fecha_modificacion'],
            );
        }
        if ($query) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "load_codigosCanjeo") {
        $data = array();

        $query = mysqli_query($mysqli, "CALL LogCodigosCanjeo();");


        while ($rows = mysqli_fetch_array($query)) {
            $data[] = array(
                'usuario' => $rows['usuario'],
                'numero_documento_usuario' => $rows['numero_documento_usuario'],
                'administrativo' => $rows['administrativo'],
                'numero_documento_administrativo' => $rows['numero_documento_administrativo'],
                'token' => $rows['token'],
                'nombre_incentivo' => $rows['nombre_incentivo'],
                'fecha_hora' => $rows['fecha_hora'],
                'actividad' => $rows['actividad']
            );
        }
        if ($query) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "load_codigosSumaPuntos") {
        $data = array();

        $query = mysqli_query($mysqli, "CALL LogCodigosSumaPuntos();");


        while ($rows = mysqli_fetch_array($query)) {
            $data[] = array(
                'usuario' => $rows['usuario'],
                'numero_documento_usuario' => $rows['numero_documento_usuario'],
                'administrativo' => $rows['administrativo'],
                'numero_documento_administrativo' => $rows['numero_documento_administrativo'],
                'token' => $rows['token'],
                'nombre_residuo' => $rows['nombre_residuo'],
                'fecha_hora' => $rows['fecha_hora'],
                'actividad' => $rows['actividad']
            );
        }
        if ($query) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "proses_register_administrativo") {

        $cekemail = mysqli_fetch_array(mysqli_query($mysqli, "CALL ValidarEmail ('$postjson[correo]');"));

        //mysqli_free_result($cekemail);  
        mysqli_next_result($mysqli); 
        if ($cekemail['correo'] == $postjson['correo']) {
            $result = json_encode(array('success'=>false, 'msg'=>'Correo ya registrado'));
        } else {
            
            $password = md5($postjson['contrasena']);
            $insert = mysqli_query($mysqli, "CALL CrearAdministrativo(
                '$postjson[pApellido]',
                '$postjson[sApellido]',
                '$postjson[pNombre]',
                '$postjson[sNombre]',
                '$postjson[codigo]',
                '$password',
                '$postjson[correo]',
                '$postjson[direccion]',
                '$postjson[fechaNacimiento]',
                '$postjson[departamento]',
                '$postjson[genero]',
                '$postjson[municipio]',
                '$postjson[tipDoc]',
                '$postjson[numDoc]');
            ");

            //$id = mysqli_insert_id($mysqli);
            if ($insert) {
                $result = json_encode(array('success'=>true, 'msg'=>'Registro existoso'));
            } else {
                $result = json_encode(array('success'=>false, 'msg'=>'No se realizó el registro'));
            }
        }
        echo $result;
    } else if ($postjson['aksi'] == "load_administrativos") {
        $data = array();

        $query = mysqli_query($mysqli, "CALL Administrativos();");


        while ($rows = mysqli_fetch_array($query)) {
            $data[] = array(
                'administrativo' => $rows['administrativo'],
                'numero_documento' => $rows['numero_documento'],
                'codigo' => $rows['codigo'],
                'correo' => $rows['correo'],
                'actividad' => $rows['actividad'],
                'fecha_hora_modificacion' => $rows['fecha_hora_modificacion'],
            );
        }
        if ($query) {
            $result = json_encode(array('success'=>true, 'result'=>$data));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    } else if ($postjson['aksi'] == "proses_update_administrador") {

        $password = md5($postjson['contrasena']);
        $cekpassword = mysqli_fetch_array(mysqli_query($mysqli, "CALL SelectContrasena('$postjson[id]');"));
        mysqli_next_result($mysqli); 

        if ($postjson['correo'] != $postjson['auxCorreo']) {
            $cekemail = mysqli_fetch_array(mysqli_query($mysqli, "CALL ValidarEmail ('$postjson[correo]');"));
            mysqli_next_result($mysqli); 
            if ($cekemail['correo'] == $postjson['correo']) {
                $result = json_encode(array('success'=>false, 'msg'=>'Correo ya registrado'));
            } else {
                if ($postjson['contrasena'] == "") {
                    $password = $cekpassword['contrasena'];
                } else {
                    $password = md5($postjson['contrasena']);
                }
                    
                $update = mysqli_query($mysqli, "CALL ActualizarAdministrador( 
                    '$postjson[pApellido]',
                    '$postjson[sApellido]',
                    '$postjson[pNombre]',
                    '$postjson[sNombre]',
                    '$postjson[codigo]',
                    '$password',
                    '$postjson[correo]',
                    '$postjson[direccion]',
                    '$postjson[fechaNacimiento]',
                    '$postjson[departamento]',
                    '$postjson[genero]',
                    '$postjson[municipio]',
                    '$postjson[tipDoc]',
                    '$postjson[numDoc]',
                    '$postjson[id]');
                ");
                if ($update) {
                    $result = json_encode(array('success'=>true, 'msg'=>'Actualización existosa'));
                } else {
                    $result = json_encode(array('success'=>false, 'msg'=>'No se realizó la actualización'));
                }
            }
        } else {
            if ($postjson['contrasena'] == "") {
                $password = $cekpassword['contrasena'];
            } else {
                $password = md5($postjson['contrasena']);
            }
                
            $update = mysqli_query($mysqli, "CALL ActualizarAdministrador( 
                '$postjson[pApellido]',
                '$postjson[sApellido]',
                '$postjson[pNombre]',
                '$postjson[sNombre]',
                '$postjson[codigo]',
                '$password',
                '$postjson[correo]',
                '$postjson[direccion]',
                '$postjson[fechaNacimiento]',
                '$postjson[departamento]',
                '$postjson[genero]',
                '$postjson[municipio]',
                '$postjson[tipDoc]',
                '$postjson[numDoc]',
                '$postjson[id]');
            ");
            if ($update) {
                $result = json_encode(array('success'=>true, 'msg'=>'Actualización existosa'));
            } else {
                $result = json_encode(array('success'=>false, 'msg'=>'No se realizó la actualización'));
            }
        }
        echo $result;
    } else if ($postjson['aksi'] == "proses_update_administrativo") {

        $password = md5($postjson['contrasena']);
        $cekpassword = mysqli_fetch_array(mysqli_query($mysqli, "CALL SelectContrasena('$postjson[id]');"));
        mysqli_next_result($mysqli); 

        if ($postjson['correo'] != $postjson['auxCorreo']) {
            $cekemail = mysqli_fetch_array(mysqli_query($mysqli, "CALL ValidarEmail ('$postjson[correo]');"));
            mysqli_next_result($mysqli); 
            if ($cekemail['correo'] == $postjson['correo']) {
                $result = json_encode(array('success'=>false, 'msg'=>'Correo ya registrado'));
            } else {
                if ($postjson['contrasena'] == "") {
                    $password = $cekpassword['contrasena'];
                } else {
                    $password = md5($postjson['contrasena']);
                }
                    
                $update = mysqli_query($mysqli, "CALL ActualizarAdministrativo( 
                    '$postjson[pApellido]',
                    '$postjson[sApellido]',
                    '$postjson[pNombre]',
                    '$postjson[sNombre]',
                    '$postjson[codigo]',
                    '$password',
                    '$postjson[correo]',
                    '$postjson[direccion]',
                    '$postjson[fechaNacimiento]',
                    '$postjson[departamento]',
                    '$postjson[genero]',
                    '$postjson[municipio]',
                    '$postjson[tipDoc]',
                    '$postjson[numDoc]',
                    '$postjson[id]');
                ");
                if ($update) {
                    $result = json_encode(array('success'=>true, 'msg'=>'Actualización existosa'));
                } else {
                    $result = json_encode(array('success'=>false, 'msg'=>'No se realizó la actualización'));
                }
            }
        } else {
            if ($postjson['contrasena'] == "") {
                $password = $cekpassword['contrasena'];
            } else {
                $password = md5($postjson['contrasena']);
            }
                
            $update = mysqli_query($mysqli, "CALL ActualizarAdministrativo( 
                '$postjson[pApellido]',
                '$postjson[sApellido]',
                '$postjson[pNombre]',
                '$postjson[sNombre]',
                '$postjson[codigo]',
                '$password',
                '$postjson[correo]',
                '$postjson[direccion]',
                '$postjson[fechaNacimiento]',
                '$postjson[departamento]',
                '$postjson[genero]',
                '$postjson[municipio]',
                '$postjson[tipDoc]',
                '$postjson[numDoc]',
                '$postjson[id]');
            ");
            if ($update) {
                $result = json_encode(array('success'=>true, 'msg'=>'Actualización existosa'));
            } else {
                $result = json_encode(array('success'=>false, 'msg'=>'No se realizó la actualización'));
            }
        }
        echo $result;
    } else if ($postjson['aksi'] == "disable_count_administrativo") {
        $query = mysqli_query($mysqli, "CALL DeshabilitarCuentaAdministrativo('$postjson[id]');");
        if ($query) {
            $result = json_encode(array('success'=>true));
        } else {
            $result = json_encode(array('success'=>false));
        }
        echo $result;
    }
?>