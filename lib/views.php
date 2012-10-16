<?php
/* LIMPIEZA DE GET Y POST */ 
if(isset($_POST)) $POST=array_map("cleanQuery",$_POST);
if(isset($_GET)) $GET=array_map("cleanQuery",$_GET);
if(isset($_SESSION)) $SESSION=array_map("cleanQuery",$_SESSION);
/* FIN DE LIMPIEZA GET Y POST  */

// Separando consultas internas
if(isset($_SERVER['PATH_INFO'])){
    if( $_SERVER['PATH_INFO'] == '/' ){
        $v[1] = 'home';
    }else{
        $v = explode('/', $_SERVER['PATH_INFO']);
    }
}else {
    $v[1] = 'home';
}

// Agregando clases para plantillas Django
Twig_Autoloader::register();
$loader     = new Twig_Loader_Filesystem(TEMPLATE);
$twig       = new Twig_Environment($loader, array('debug' => true));
$app         = new Query();

// Variable para envios a plantilla Twig
$locals = array();
$locals['app_name'] = app_name;
$locals['STATIC_URL'] = STATIC_URL;
$locals['SITE_URL'] = SITE_URL;

$locals['header'] = $app->query('bloques',array("padre=2","estado=1"),'','order by id');
$locals['footer'] = $app->query('bloques',array("padre in (2,5)","estado=1"),'','order by padre,id');
$locals['categorias'] = $app->query('categorias',array("padre=1","estado=1"),'','order by prioridad');
$locals['distritos'] = $app->query('distritos','','',
    'where  id in (select distrito from distribuidores_sedes where estado=1 group by distrito)');
/*
printQuery($locals['distritos']);
exit();
*/
if ($v[1] == 'home') {

    // PAGINA INICIAL
    $banners = $app->query('banners_categorias',array("id_categorias=1"));
    if ($banners) {
        $banners = explode( ",", $banners[0]->principal );
        $locals['banners'] = $banners;
    }

    $promociones = $app->query('promociones',array("estado=1"),'','order by prioridad');
    if ($promociones) {
        $locals['promociones'] = $promociones;
        foreach ($promociones as $k => $v) {
            $img_pro = explode( ",", $v->imagen );
            $locals['promociones'][$k]->imagen = $img_pro[1];
        }
    }

    echo $twig->render('app/home.html', $locals );
    // FIN DE PAGINA INICIAL

}else if ( $v[1] == 'contacto' ) {
    // CONTACTO
    if( isset($POST['submit-contacto']) ){

        $locals['nombre']       = $POST['nombre'];
        $locals['email']        = $POST['email'];
        $locals['mensaje']      = $POST['mensaje'];
        foreach ($POST as $k => $v) if ($v == '') $locals['errors'][$k] = true;
        if( $locals['nombre'] == 'Nombre') $locals['errors']['nombre'] = true;
        if( $locals['email'] == 'E-Mail') $locals['errors']['email'] = true;
        if( $locals['mensaje'] == 'Mensaje') $locals['errors']['mensaje'] = true;

        if( !isset($locals['errors']) ){
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "ssl";
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 465;
            $mail->Username = "mix.minds@gmail.com";  
            $mail->Password = "pueblolibre";

            $body = $twig->render('app/email-contacto-estilizado.html', $locals );
            $mail->SetFrom("mix.minds@gmail.com","Contacto - ".app_name);
            $mail->AddAddress("rbaltazarc@gmail.com","Contacto - ".app_name);
            $mail->AddBCC("rbaltazarc@gmail.com","Administrador");
            if( valid_email($locals['email']) ){
                $mail->AddBCC($locals['email'],"Cliente");
            }
            $mail->Subject    = "Contacto - ".app_name;
            $mail->AltBody    = "Para ver el mensaje, utilice un correo electrónico HTML compatible";
            $mail->MsgHTML($body);

            if(!$mail->Send()) {
                $locals['errors']['proceso'] = "Error de Envio: " . $mail->ErrorInfo;
            }else{
                $locals['exito']['pedido'] = true;
            }
        }
    }
    // printQuery($locals);
    echo $twig->render('app/contacto.html', $locals );
    // END CONTACTO

}elseif ($v[1] == 'selecciona-tu-tipo-de-comida' ) {

    // INICIO
    if( $v[2]!='' and is_numeric($v[2]) ){
        foreach ($locals['categorias'] as $val) {
            if ($val->id == $v[2]) {
                $locals['categoria'] = $val;
            }
        }
    }else{
        $locals['categoria'] = $locals['categorias'][0];
    }
    $banner = $app->query('banners_categorias',array('id_categorias='.$locals['categoria']->id));
    if($banner){
        $banner = explode(',', $banner[0]->principal);
        if(isset($banner[1])) $locals['banner_categoria'] = $banner[1];
    }
    $locals['locales'] = $app->query('distribuidores','','',
    'where  id in (select id_distribu from productos where id_categorias = '.$locals['categoria']->id.' group by id_distribu)');
    foreach ($locals['locales'] as $k => $val) {
        $imagen = explode(',', $val->imagen_listado);
        $locals['locales'][$k]->imagen = $imagen[1];
        $locals['locales'][$k]->pago = explode(",",$val->pago);
    }

    echo $twig->render('app/tipo-comida.html', $locals );
    //FIN DE PRODUCTOS REGALOS

}elseif ($v[1] == 'distribuidor' ) {

    // INICIO
    if( $v[2]!='' and is_numeric($v[2]) ){
        $query = $app->query('distribuidores',array('id='.$v[2]));
        if ($query){
            $locals['distribuidor'] = $query[0];
            if(isset($v[3]) and $v[3] == '') header('location: '.SITE_URL.'distribuidor/'.$v[2].'/inicio/');
            $query = $app->query('distribuidores_sedes',array('id_distribu='.$v[2]));
            if($query){
                $imagen = explode(',',$query[0]->imagen);
                $locals['distribuidor']->imagen = $imagen[1];
                $locals['distribuidor']->valido = '';
                foreach ($query as $val) $locals['distribuidor']->valido .= $val->descripcion.'<br>';
            }
            $locals['distribuidor']->pago = explode(",",$locals['distribuidor']->pago);
            $locals['distribuidor']->vista = $v[3];
            // printQuery($locals['distribuidor']);
            // exit();
        }
    }else{
        $locals['distribuidor'] = false;
    }
    echo $twig->render('app/distribuidor.html', $locals );
    // FIN DE LISTA
    
}elseif ($v[1] == 'busqueda' ) {

    // INICIO
    if( $GET['t']!='' and is_numeric($GET['t']) ){
        foreach ($locals['categorias'] as $val) {
            if ($val->id == $GET['t']) {
                $locals['categoria'] = $val;
            }
        }
    }

    $precio = '';
    $envio = '';
    $locals['dist'] = '';
    $banner = $app->query('banners_categorias',array('id_categorias='.$locals['categoria']->id));
    if($banner){
        $banner = explode(',', $banner[0]->principal);
        if(isset($banner[1])) $locals['banner_categoria'] = $banner[1];
    }
    if( is_numeric($GET['p']) ){
        if( $GET['p'] == 1 ) $precio = ' and precio < 21';
        if( $GET['p'] == 2 ) $precio = ' and precio > 19 and precio < 41';
        if( $GET['p'] == 3 ) $precio = ' and precio > 39 and precio < 1500';
    }
    if( is_numeric($GET['d']) ) $locals['dist'] = ' and s.distrito='.$GET['d'];
    if( is_numeric($GET['e']) ){
        if( $GET['e'] == 1 ) $envio = ' and d.llevar = 1';
        if( $GET['e'] == 2 ) $envio = ' and d.delivery = 1';
    }

    $locals['locales'] = $app->query('distribuidores','',array(' d.*'),
    ' as d inner join distribuidores_sedes as s on d.id = s.id_distribu
    where  d.id in (select id_distribu from productos where id_categorias = '.$locals['categoria']->id.$precio.
        ' group by id_distribu) '.$locals['dist'].$envio.' group by d.id');

    // echo $app->lastQuery;
    foreach ($locals['locales'] as $k => $val) {
        $imagen = explode(',', $val->imagen_listado);
        $locals['locales'][$k]->imagen = $imagen[1];
        $locals['locales'][$k]->pago = explode(",",$val->pago);
    }

    echo $twig->render('app/tipo-comida.html', $locals );
    // FIN DE LISTA

}else{
    echo $twig->render('app/grupo_det.html', $locals );
}
?>