<?PHP 
session_save_path(realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/../tmp'));
ini_set('default_charset', 'UTF-8');
//ini_set('session.use_strict_mode', 1);
ini_set('session.cache_expire', (60 * 8)); // ANTES ESTAVA 20000 MINUTOS
ini_set('session.cache_limiter', 'nocache');
ini_set('session.cookie_lifetime', (2 * 60 * 60));
ini_set('session.cookie_secure', 1);
ini_set('session.gc_maxlifetime', (2 * 60 * 60));
ini_set('allow_url_fopen', 1);
ini_set('allow_url_include', 1);
ini_set('memory_limit', '2048M');
session_start(); 

$tokensubdom = strtolower(explode(".", $_SERVER['SERVER_NAME'])[0]); // Pegando TOKEN no Subdominio

if ($tokensubdom <> "atend" and $tokensubdom <> "wfhmlg") {
    $btntoken = false;
    $token = strtoupper($tokensubdom);
} else {
    $btntoken = true;
    $itenttoken = explode("?t.", $_SERVER['REQUEST_URI']);
    $token = strtoupper(str_replace(".php", "", $itenttoken[1]));
}

if (!empty($_SESSION['usr_mat'])) {
    header("Location: https://" . $tokensubdom . ".workfacilit.com/app/prod/");
    die();
}

session_unset();
if ($_GET['idioma'] <> "") {
    $_SESSION['usr_idioma'] = $_GET['idioma'];
} // idioma
$_SESSION["inilogin"] = "ok";
if (($_GET['pst'] == "c") or ($_GET['pst'] == "d")) {
    $_SESSION['posiclog'] = $_GET['pst'];
}
if ($_GET['pst'] == "e") {
    $_SESSION['posiclog'] = "";
}
if (($_GET['bx'] == "s") and ($_GET['pst'] == "c")) {
    $_SESSION['boxlogin'] = $_GET['bx'];
}
if ($_GET['bx'] == "n") {
    $_SESSION['boxlogin'] = "";
}
if ($_GET['wz'] == "s") {
    $_SESSION['whatzapplogin'] = $_GET['wz'];
}
if ($_GET['wz'] == "n") {
    $_SESSION['whatzapplogin'] = "";
}
$_SESSION["bdemp"] = strtoupper($_REQUEST['SNtoken'] . $_REQUEST['SZtoken'] . $_REQUEST['SNrtoken']);

if($_GET["suporte"] == "itau"){
    $_SESSION["bdemp"] = "WF";

    // echo "<script>var val = localStorage.getItem('token'); alert(val)</script>";
    // die();
}


if (isset($_GET['pricingId'])) {
    $_SESSION['pricingId'] = $_GET['pricingId'];
}
if (isset($_GET['dc'])) {
    $_SESSION['dc'] = $_GET['dc'];
}
if (isset($_GET['redir'])) {
    $_SESSION['redir'] = $_GET["redir"];
}

if (file_exists("../funcs/inifunc.php")) {
    include_once("../funcs/inifunc.php");
} else {
    include_once("funcs/utils.php");
}
if (file_exists("../conexao/conect.php")) {
    include_once("../conexao/conect.php");
} else {
    include_once("conexao/conect.php");
}
if (file_exists("../funcs/editsforms.php")) {
    include_once("../funcs/editsforms.php");
} else {
    include_once("funcs/editsforms.php");
}
if (file_exists("../funcs/utils.php")) {
    include_once("../funcs/utils.php");
} else {
    include_once("funcs/utils.php");
}
if (file_exists("../funcs/funstxt.php")) {
    include_once("../funcs/funstxt.php");
} else {
    include_once("funcs/funstxt.php");
}

$_SESSION['aplic_nome_abv'] = "Atend";
$_SESSION['aplic_versao'] = "1.8";
$_SESSION['aplic_direitos'] = "BRQ/Workfacilit";


if (($tokensubdom <> "atend" and $tokensubdom <> "wfhmlg") and ($token == "")) {
    header("Location: https://" . $tokensubdom . ".workfacilit.com/app/prod/acesso/?t." . strtoupper($tokensubdom) . "");
}

if (strpos($token, "&")) {
    $itenttoken = explode("&", $token);
    $token = strtoupper($itenttoken[0]);
}

if ($token == "") {
    strtoupper($token = $_COOKIE['usr_token']);
}

// Switch para AD
function executeSqlConfiCenter($sql, $show = false)
{
    if ($show) {
        echo $sql;
    }

    $dbms = 'pgsql';
    $db = "bd_atend_configuration_center";
    $host = "10.250.0.11";
    $port = "1701";
    $user = "dbadmin";
    $pass = "17U08KOmuNoFRBriFR";

    try {
        $dsn = "$dbms:host=$host;dbname=$db;port=$port";
        $iConn = new PDO($dsn, $user, $pass);
        $iConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $iConn->exec("SET TIME ZONE 'America/Recife'");
        $query = $iConn->prepare($sql);
        $query->execute();
        $row = $query;
        return $row;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
if ($token == 'BRK' or $token == 'BRK_WF') {

    $ad = 0;
    $rs = executeSqlConfiCenter("select ad from tb_auth where token = '" . str_replace('_WF', '', $token) . "'");
    while ($rsFetch = $rs->fetch()) {
        $ad = $rsFetch['ad'];
    }

    if ($ad == 1) {

        header("Location: https://atend.workfacilit.com/app/prod/acesso/index_ad.php?t.$token");
        die();
    }
}
if ($_GET['le'] <> "") {
    $linkexterno = true;
    $fashqrcodejsondivx = explode("&", base64_decode($_GET['le']));
    $fashqrcodejson = $fashqrcodejsondivx[0];
    $empcli = base64_decode($fashqrcodejsondivx[1]);
    $ivcrypt = "Work@facilit&LinkExternoAtendimentoWorkflowbpmn" . strtoupper($token);
    $retJson = decodeAES($fashqrcodejson, $ivcrypt);
    $JSONRecebido = json_decode($retJson, true);
    $lelgtk = cod(cod($JSONRecebido['login'], "C") . ";" . cod($JSONRecebido['codigo'], "C") . ";" . cod($JSONRecebido['email'], "C") . ";" . cod($JSONRecebido['campoemailcheck'], "C") . ";" . cod($JSONRecebido['id_demanda'], "C") . ";" . cod($JSONRecebido['tpcod'], "C"), "C");
// debug($JSONRecebido);
    //$menslog = $_SESSION['lelg'];
}

//if ($_GET['i'] == "") {

//$_SESSION['aplic_nome'] = idioma("Atend - Gestão de Serviços", "Atend - Service Management", "Atend - Gestión de servicios") ; 
if ($_SESSION['atendwhitelabel'] <> retornacodigotab("S")) {
    $_SESSION['aplic_nome'] = idioma("Plataforma de gestão de Serviços", "Atend - Service Management", "Atend - Gestión de servicios");
} else {
    $_SESSION['aplic_nome'] = idioma("Atend - Gestão de Serviços", "Atend - Service Management", "Atend - Gestión de servicios");
}

if (trim(LerTXT_BuscaParam($token, "thema_loginweb_whitelabeldesctitulo")) <> "") {
    $_SESSION['aplic_nome'] = trim(LerTXT_BuscaParam($token, "thema_loginweb_whitelabeldesctitulo"));
}


$arrayParamsToken = retorn_dados_token($token);

$mudaparamobile = true;
if (($arrayParamsToken[$token]['VersaoMobile'] == false) and ($token <> "")) {$mudaparamobile = false;}


if ($arrayParamsToken[$token]['thema_favicone'] <> "") {
    $_SESSION['aplic_favicone'] = $arrayParamsToken[$token]['thema_favicone'];
} else {
    $_SESSION['aplic_favicone'] = "favicon.png";
}

$layout_maxheight = $arrayParamsToken[$token]['layout_maxheight'];
if ($layout_maxheight == "") {
    $layout_maxheight = 300;
}

$thema_logotipo = $arrayParamsToken[$token]['thema_loginweb_logotipo'];
if ($thema_logotipo == "") {
    $thema_logotipo = "src='../imgs/" . strtolower($token) . ".png' style='max-width: ".$layout_maxheight."px'";
}

if (!strpos(trim($arrayParamsToken[$token]['thema_loginweb_fundo_latdir']), "#")) {
    if (file_exists("../imgs/bg_slider" . strtolower($token) . ".png")) {
        $thema_background = "../imgs/bg_slider" . strtolower($token) . ".png?d=" . date('dmY');
        $thema_background_video = "";
    } else {
        $thema_background = $arrayParamsToken[$token]['thema_loginweb_background'];
        $thema_background_video = $arrayParamsToken[$token]['thema_loginweb_background_video'];
    }
} else {
    $color = true;
}
$thema_corlogoatend = $arrayParamsToken[$token]['thema_loginweb_corlogoatend']; // w = BRANCO
if (trim(LerTXT_BuscaParam($token, "thema_loginweb_corlogoatend"))) {
    $thema_corlogoatend = trim(LerTXT_BuscaParam($token, "thema_loginweb_corlogoatend"));
}

$thema_position_back = $arrayParamsToken[$token]['thema_loginweb_position_back'];
if (trim(LerTXT_BuscaParam($token, "thema_loginweb_position_back"))) {
    $thema_position_back = trim(LerTXT_BuscaParam($token, "thema_loginweb_position_back"));
}

$thema_background_size = $arrayParamsToken[$token]['thema_loginweb_background_size'];
if (trim(LerTXT_BuscaParam($token, "thema_loginweb_background_size"))) {
    $thema_background_size = trim(LerTXT_BuscaParam($token, "thema_loginweb_background_size"));
}
if ($thema_background_size < 50) {
    $thema_background_size = 100;
}

$thema_corbomdia = $arrayParamsToken[$token]['thema_loginweb_corbomdia'];
if (trim(LerTXT_BuscaParam($token, "thema_loginweb_corbomdia"))) {
    $thema_corbomdia = trim(LerTXT_BuscaParam($token, "thema_loginweb_corbomdia"));
    if ($thema_corbomdia == "#000000") {
        $thema_corbomdia = "transparent";
    }
}

$thema_corhora = $arrayParamsToken[$token]['thema_loginweb_corhora'];
if (trim(LerTXT_BuscaParam($token, "thema_loginweb_corhora"))) {
    $thema_corhora = trim(LerTXT_BuscaParam($token, "thema_loginweb_corhora"));
    if ($thema_corhora == "#000000") {
        $thema_corhora = "transparent";
    }
}

$thema_corinfor = $arrayParamsToken[$token]['thema_loginweb_corinfor'];
if (trim(LerTXT_BuscaParam($token, "thema_loginweb_corinfor"))) {
    $thema_corinfor = trim(LerTXT_BuscaParam($token, "thema_loginweb_corinfor"));
}

$thema_alturahora = $arrayParamsToken[$token]['thema_loginweb_alturahora'];
if (trim(LerTXT_BuscaParam($token, "thema_loginweb_alturahora"))) {
    $thema_alturahora = trim(LerTXT_BuscaParam($token, "thema_loginweb_alturahora"));
}

$thema_loginweb_corbtncontinua = trim(LerTXT_BuscaParam($token, "thema_loginweb_corbtncontinua"));
if ($thema_loginweb_corbtncontinua == "") {
    $thema_loginweb_corbtncontinua = "success";
}
if ($_GET['i'] == "!") {
    $thema_loginweb_corbtncontinua = "dark";
}

$thema_fundo_latesq = $arrayParamsToken[$token]['thema_loginweb_fundo_latesq'];
if (($token <> "") and (trim(LerTXT_BuscaParam($token, "thema_loginweb_fundo_latesq")) <> "#000000")) {
    $thema_fundo_latesq = "background-color:" . trim(LerTXT_BuscaParam($token, "thema_loginweb_fundo_latesq")) . ";";
} else {
    $thema_fundo_latesq = "background-color:transparent;";
}

$thema_fundo_latdir = $arrayParamsToken[$token]['thema_loginweb_fundo_latdir'];
if (($token <> "") and (trim(LerTXT_BuscaParam($token, "thema_loginweb_fundo_latdir")) <> "#000000") and (trim(LerTXT_BuscaParam($token, "thema_loginweb_fundo_latdir")) <> "background-color:transparent;")) {
    $thema_fundo_latdir = "background-color:" . trim(LerTXT_BuscaParam($token, "thema_loginweb_fundo_latdir")) . ";";
    $thema_background = "";
    $thema_background_video = "";
    $color = true;
}

//Checagens

if ($arrayParamsToken[$token]['thema_loginweb_posbox'] <> "") {
    $_SESSION['posiclog'] = $arrayParamsToken[$token]['thema_loginweb_posbox'];
}
if (($token <> "") and (trim(LerTXT_BuscaParam($token, "thema_loginweb_posbox")) == "center") or (trim(LerTXT_BuscaParam($token, "thema_loginweb_posbox")) == "right")) {
    $_SESSION['posiclog'] = trim(LerTXT_BuscaParam($token, "thema_loginweb_posbox"));
}

if ($arrayParamsToken[$token]['thema_loginweb_box'] == retornacodigotab("S")) {
    $_SESSION['boxlogin'] = $arrayParamsToken[$token]['thema_loginweb_box'];
} else {
    $_SESSION['boxlogin'] = "";
}
if (($token <> "") and (trim(LerTXT_BuscaParam($token, "thema_loginweb_box")) == retornacodigotab("S"))) {
    $_SESSION['boxlogin'] = trim(LerTXT_BuscaParam($token, "thema_loginweb_box"));
}

if ($arrayParamsToken[$token]['thema_loginweb_wz'] == retornacodigotab("S")) {
    $_SESSION['whatzapplogin'] = $arrayParamsToken[$token]['thema_loginweb_wz'];
} else {
    $_SESSION['whatzapplogin'] = "";
}

if (trim(LerTXT_BuscaParam($token, "thema_loginweb_wz")) == retornacodigotab("S")) {
    $_SESSION['whatzapplogin'] = trim(LerTXT_BuscaParam($token, "thema_loginweb_wz"));
}

if (trim(LerTXT_BuscaParam($token, "thema_loginweb_0800")) == retornacodigotab("S")) {
    $_SESSION['0800login'] = trim(LerTXT_BuscaParam($token, "thema_loginweb_0800"));
}

if (trim(LerTXT_BuscaParam($token, "thema_loginweb_whitelabel")) == retornacodigotab("S")) {
    $_SESSION['atendwhitelabel'] = trim(LerTXT_BuscaParam($token, "thema_loginweb_whitelabel"));
} else {
    if ($_GET['wl'] == 1) {
        $_SESSION['atendwhitelabel'] = retornacodigotab("S");
    }
}

$mensagem = $arrayParamsToken[$token]['loginwebmensagem'];
if (trim(LerTXT_BuscaParam($token, "loginwebmensagem"))) {
    $mensagem = trim(LerTXT_BuscaParam($token, "loginwebmensagem"));
} else {
    $mensagem = "";
}
$habilitado = $arrayParamsToken[$token]['EncEmpresa'];


if (((getOS() == "iPhone") or (getOS() == "Android") or (getOS() == "iPad") or (getOS() == "Mobile")) and ($_GET['m'] == "") and ($mudaparamobile == true)) {
    echo "<script>window.location='/app/prod/app_mobile'</script>";
}
if ((strtoupper($token) <> "") and ($_GET['i'] == "") and ($habilitado == false)) {
    header("Location: https://" . $tokensubdom . ".workfacilit.com/app/prod/acesso?e=1");
}
if ($_GET['e'] == "1") {
    $retorno = "<strong>" . idioma("ERRO DE TOKEN", "ERROR TOKEN", "ERROR TOKEN") . "</strong><br>" . idioma("O TOKEN informado é inválido", "The TOKEN reported is invalid", "El TOKEN informado no es válido.");
    $cor = "danger";
}
//} 
//else {
//    $thema_logotipo = "src='../imgs/wf.png' style='width: 200px'";
//    $thema_background = "../imgs/bg_slider26.jpg";
//    $thema_corlogoatend = "blue"; // w = BRANCO
//    $thema_position_back = "0";
//    $thema_corbomdia = "black";
//    $thema_corhora = "black";
//    $thema_alturahora = "bottom: 0px";
//    $thema_fundo_latesq = "background-color: transparent; ";
//    $color = false;
//}


if (($_SESSION['posiclog'] == "center") or ($_GET['mudat'] == 1)) {
    $poslog = "display: flex; flex-direction: row; flex-wrap: wrap; justify-content: center;";
    $noexib = "display: none; right: 0px; ";
    $alinhamento = "text-center";
    $thema_alinhahora = "right";
    $alturalogin = 75;
    $larguralogin = 650;
    $poslogmob = "bottom: 30px; right: 20px;";
    $poslogatend = "bottom: 30px; left: 20px;";
    $boxlogin = "height: 100%;";
   

} else {
    $poslog = "";
    $alinhamento = "text-center";
    $alturalogin = 30;
    $larguralogin = 650;
    
    if ($_GET['mudat'] == 1) {
        $poslogmob = "bottom: 50px; left: 20px;";
        $poslogatend = "bottom: 100px; left: 20px;";
    } else {
        $poslogmob = "bottom: 50px; left: 220px;";
        $poslogatend = "bottom: 100px; left: 260px;";
    }
    $boxlogin = "height: 100%;";
    $thema_alinhahora = "right";

    if ($_SESSION['posiclog'] == "right") {
        $poslog = "display: flex; flex-direction: row; flex-wrap: wrap; justify-content: flex-end;";
        $poslogmob = "bottom: 50px; right: 150px;";
        $poslogatend = "bottom: 100px; right: 190px;";
        $thema_alinhahora = "left";
    }
}

if ($_SESSION['boxlogin'] == true) {
    $boxlogin = "box-shadow: rgba(17, 17, 26, 0.05) 0px 1px 0px, rgba(17, 17, 26, 0.1) 0px 0px 8px; margin: 50px 0px 0px 0px; border-radius: 25px; height: 87%;";
}

$colse = 6;
$colsd = 6;
?>



<!DOCTYPE html>
<html lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title><?php echo $_SESSION['aplic_nome'] ?></title>
    <link href="../imgs/<?php echo $_SESSION['aplic_favicone'] ?>" rel="icon">
    <link href="../imgs/logo_60.png" rel="apple-touch-icon">
    <!-- Meta, title, CSS, favicons, etc. -->
    <link rel="manifest" href="../manifest.json">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- Bootstrap -->
    <link href="../../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../../vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../../vendors/animate.css/animate.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="../../vendors/select-master/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="../../build/css/custom.min.css" rel="stylesheet">
    <!-- Style próprio-->
    <link href="../../prod/css/estiloproprio.css" rel="stylesheet">
    <!-- Fonts Google -->
    <link href="https://fonts.googleapis.com/css?family=Abel|Comfortaa:300,400|Dosis:200,300,400,500|Josefin+Slab|Jura:300,400|Nova+Round|Philosopher|Poiret+One|Quattrocento+Sans:400,700|Quicksand:300,400|Sacramento|Text+Me+One" rel="stylesheet" integrity="sha384-Wk/eUAOzq7OWUj3qYMYSFWZS78n9aSB3pMrrWe4vLGe/PW9VPDjT9HllL9RmqKMk" crossorigin="anonymous">
    <script src="../../vendors/jquery/dist/jq.min.js"></script>
    <script src="../funcs/funcscript.js"></script>
    <script>
        atualiza_myposicion_session('../');
    </script>
    <!-- PNotify -->
    <link href="../../vendors/pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="../../vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="../../vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">

    <?PHP
    if ((isset($_REQUEST["btnsalvar"]) != "") and ($_GET['i'] <> '!')) {
        //$retorno = aut($_REQUEST["SEemail"],$_REQUEST["SRsenha"],$_REQUEST['SNtoken']);
        $cor = "danger";
    }

    if ((isset($_REQUEST["btnrecupera"]) != "") and ($_GET['i'] == '!')) {

        if ($_SESSION['bd_conexao_status'] == true) {
            try {

                $rs = seleciona("select id, nome, apelido, email, id_empresa, senha from tb_users where email = '" . anti_injection($_REQUEST['SNemail']) . "'", 3);
                if ($rs['id'] == "") {
                    $retorno = "<strong>" . idioma("IMPOSSÍVEL AJUDAR", "UNABLE TO HELP", "NO SE PUEDE AYUDAR") . "</strong><br>" . idioma("Não identificamos o seu e-mail", "We did not identify your email", "No identificamos su correo electrónico");
                    $cor = "danger";
                } else {

                    $emp = seleciona("select * from tb_empresa", 3);

                    $arraydadosautentica['email_username'] = $emp["email_username"];
                    $arraydadosautentica['email_endereco'] = $emp["email_endereco"];
                    $arraydadosautentica['email_senha'] = $emp["email_senha"];
                    $arraydadosautentica['email_host_smtp'] = $emp["email_host_smtp"];
                    $arraydadosautentica['email_host_porta'] = $emp["email_porta_smtp"];
                    $arraydadosautentica['email_fromname'] = $emp["email_fromname"];
                    $arraydadosautentica['email_timeout'] = $emp["email_timeout"];
                    $arraydadosautentica['email_smtsecure'] = $emp["email_smtsecure"];
                    $arraydadosautentica['email_smtpauth'] = $emp["email_smtpauth"];

                    if (empty($emp["email_host_smtp"])){
                        unset($arraydadosautentica);
                    }

                    $tempo_reset = 14400;
                    if ($emp == false) {
                        $tempo_reset = 14400;
                    } else {
                        $tempo_reset = $emp['config_tempo_troca_senha'];
                    }
                    $codvalid = "A" . rand(10, 99) . "-" . rand(100, 999);
                    $texto = "Estamos enviando seus dados de acesso, conforme solicitado.<br><br><table style='width: 100%; font-family: Jura, sans-serif;' border='0' align='center' cellpadding='0' cellspacing='10'>";
                    $texto .= "<tr><td style='width: 150px;  font-size:14px; border-right: #C1CDCD solid 2px;'><strong>" . idioma("EMPRESA", "COMPANY", "EMPRESA") . "</strong></td><td>" . fpesquisar('descricao', 'tb_empresa', 'id', $rs['id_empresa']) . "</td></tr>";
                    $texto .= "<tr><td style='width: 150px;  font-size:14px; border-right: #C1CDCD solid 2px;'><strong>" . idioma("LOGIN", "LOGIN", "LOGIN") . "</strong></td><td>" . $rs['email'] . "</td></tr>";
                    $texto .= "<tr><td style='width: 150px;  font-size:14px; border-right: #C1CDCD solid 2px;'><strong>" . idioma("NOME", "Name", "Nombre") . "</strong></td><td>" . $rs['nome'] . "</td></tr>";
                    $texto .= "<tr><td style='width: 150px;  font-size:14px; border-right: #C1CDCD solid 2px;'><strong>" . idioma("CÓDIGO", "CODE", "CÓDIGO") . "</strong></td><td style='color:red'><strong>" . $codvalid . "</strong></td></tr>";
                    $texto .= "<tr><td style='width: 150px;  font-size:14px; border-right: #C1CDCD solid 2px;'><strong>" . idioma("ATENÇÃO", "ATTENTION", "ATENCIÓN") . "</strong></td><td>" . idioma("O link de troca de senha é válido por " . floor($tempo_reset / 60) . " minutos.", "The password exchange link is valid for " . floor($tempo_reset / 60) . " minutes.", "El enlace de intercambio de contraseña es válido por " . floor($tempo_reset / 60) . " minutos.") . "</td></tr>";
                    $texto .= "<tr><td style='width: 150px;  font-size:14px; border-right: #C1CDCD solid 2px;'></td></tr>";
                    $texto .= "<tr><td style='padding: 20px 0px 20px 0px;  font-family: Jura, sans-serif; text-align: left;' colspan='2'><a href='https://" . $_SERVER['SERVER_NAME'] . "/app/prod/acesso/?p1=" . cod($token, "C") . "&p2=" . cod($_REQUEST['SNemail'], "C") . "&p3=" . cod(date('d/m/Y H:i:s'), "C") . "&p4=" . cod($codvalid, "C") . "' style='margin: 0px; cursor: pointer; font-family: Arial; color: #ffffff; font-size: 14px; background: #00688B; padding: 10px 20px 10px 20px; text-decoration: none; hover{background: #3cb0fd;text-decoration: none;}'>" . idioma("Trocar senha", "Change Password", "Cambia la contraseña") . "</a><br><br></td></tr>";
                    $texto .= "</table>";

                    updatebd("tb_users", "data_validade_senha = '" . date('Y-m-d') . "', codvalid = '" . $codvalid . "'", "id", $rs['id']);

                    if (EnviarEmail("Recuperação de Acesso", $texto, $_REQUEST["SNemail"], $rs['apelido'],true,"","","",$arraydadosautentica) == True) {
                        $retorno = "<br>" . idioma("Seus dados de acesso foram enviados para o e-mail informado", "Your access data has been sent to the informed email", "Sus datos de acceso han sido enviados al correo electrónico informado");
                        $cor = "success";
                    } else {
                        $retorno = '<br>' . idioma("Falha no envio do e-mail", "Failed to send email", "Error al enviar correo electrónico");
                    }
                }
            } catch (Exception $exc) {
                $retorno = "<strong>" . idioma("ERRO DE ENVIO", "SEND ERROR", "ERROR DE ENVIO") . "</strong><br>" . idioma("Impossível conectar ao serviço de e-mail", "Unable to connect to the mail service", "No se puede conectar al servicio del e-mail");
            }
        } else {
            $retorno = "<strong>" . idioma("ERRO DE CONEXÃO", "CONNECTION ERROR", "ERROR DE CONEXIÓN") . "</strong><br>" . idioma("Impossível conectar ao serviço", "Unable to connect to the service", "No se puede conectar al servicio");
            $cor = "danger";
        }
    }
    ?>


</head>


<body style="
          overflow-y: hidden;
          overflow-x: hidden;              
          background: transparent;
    <?php if ($thema_background <> "") { ?>;
                  background-image: url('<?php echo $thema_background ?>');
                  background-repeat: no-repeat;
                  background-size: cover;
        <?php
    } else {
        echo $thema_fundo_latdir;
    }
        ?>
          background-position-x: <?php echo $thema_position_back ?>px;
          border: 0px;
          background-size: <?php echo $thema_background_size ?>%;
          ">

    <?php if ($_SESSION['atendwhitelabel'] <> retornacodigotab("S")) { ?>
        <img src='../imgs/logo_atend_<?php echo $thema_corlogoatend ?>.png' style='width: 120px; z-index: 999; position: absolute; <?php echo $poslogatend ?> ' class="center-block">
        <img src="../imgs/app_iconsh.png" style="width: 180px; position: absolute; z-index: 999; <?php echo $poslogmob ?> "><?php } ?>

    <div class="wrap" style="height: 100%;width: 100%; border: 0px; <?php echo $poslog ?>">
        <?php
        if (($thema_background == "") and ($color <> true)) {
            if ($thema_background_video == "") {
                $endVideo = "login_atend9.mp4";
            } else {
                $endVideo = $thema_background_video;
            }
        ?>
            <div class="bg-video" style="width: 100%; height: 100%">
                <video loop="true" autoplay="true" src="../imgs/<?php echo $endVideo ?>"></video>
            </div>
        <?php } 
        
        if($_GET['nc'] == "tr"){
            $thema_fundo_latesq = "background-color:white;";
            $alturalogin = 50;
            $larguralogin = 900;
        }?>



        <div class="col-md-<?php echo $colse ?> login login_form" style="<?php echo $boxlogin ?> <?php echo $thema_fundo_latesq ?>; padding: <?php echo $alturalogin ?>px; width: <?php echo $larguralogin ?>px; border: 0px">

        <?php if($_GET['nc'] <> "tr"){?>

            <form style="z-index:999999;" id="myFormaut" method="post" class="form-horizontal form-label-left" autocomplete="off" data-parsley-validate data-toggle="validator" role="form" enctype="multipart/form-data">

                <br><br><br><img <?php echo $thema_logotipo; ?> class="center-block"><br><br><br><br>


                <?PHP
                if ($retorno <> "") {
                    echo "<div class='alert alert-" . $cor . " alert-dismissible fade in' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>×</span></button><strong>" . idioma("ATENÇÃO", "ATTENTION", "ATENCIÓN") . ": </strong>  $retorno </div>";
                }
                ?>


                <?php
                if ($_GET['p3'] <> '') {
                    if ($_SESSION['bd_conexao_status'] == true) {
                        $emp = seleciona("select config_tempo_troca_senha from tb_empresa", 3);
                        if ($emp == false) {
                            $tempo_maximo = 14400;
                        } else {
                            $tempo_maximo = $emp['config_tempo_troca_senha'];
                        }
                    } else {
                        $tempo_maximo = 14400;
                    }

                    if (retorn_secs(diferencahoras(cod($_GET['p3'], "D"), date('d/m/Y H:i:s'))) < $tempo_maximo) {
                ?>

                        <div class="list-group">
                            <li class="list-group-item" style="text-align: left; padding: 1px"><?PHP echo newedit("SNemail//50//" . idioma("Digite sua credencial", "Enter your credential", "Ingrese su credencial"), "", cod($_GET['p2'], "D"), "Preencha uma credencial válida", "S", "onfocus=limpa();") ?></li>
                            <li class="list-group-item" style="text-align: left; padding: 1px"><?PHP echo newedit("SNcod//50//" . idioma("Digite o código recebido", "Enter the code received", "Ingrese el código recibido"), "", cod($_GET['p4'], "D"), "Preencha com o código recebido", "", "onfocus=limpa();") ?></li>
                            <li class="list-group-item" style="text-align: left; padding: 1px"><?PHP echo newedit("SRsenha1//25//" . idioma("Digite uma senha", "Enter a password", "Ingrese una contraseña"), "", "", "A senha deve ter no mínimo 6 caracteres", "", "onfocus=limpa();") ?></li>
                            <li class="list-group-item" style="text-align: left; padding: 1px"><?PHP echo newedit("SRsenha2//25//" . idioma("Repita a senha", "repeat the password", "repite la contraseña"), "", "", "A senha deve ter no mínimo 6 caracteres", "", "onfocus=limpa();") ?></li>

                        </div>

                        <h4><span id="divoaut" style="color: #000;" class="center-block text-center <?php echo $_SESSION['fonte_padrao'] ?>"></span></h4>

                        <div style="padding-top: 15px; padding-bottom: 50px">
                            <button id="mudasenha" name="mudasenha" style="width: 100%; height: 40px" class="btn btn-primary" type="submit"><?php echo idioma("Validar nova senha", "Validate new password", "Validar nueva contraseña") ?></button>
                        </div>

                        <script>
                            $("#SRsenha1").css({
                                'border': '0',
                                'background-color': 'white',
                                'box-shadow': 'none',
                                '-webkit-box-shadow': '0 0 0px 1000px white inset'
                            });
                            $("#SRsenha2").css({
                                'border': '0',
                                'background-color': 'white',
                                'box-shadow': 'none',
                                '-webkit-box-shadow': '0 0 0px 1000px white inset'
                            });
                            $("#SNcod").css({
                                'border': '0',
                                'background-color': 'white',
                                'box-shadow': 'none',
                                '-webkit-box-shadow': '0 0 0px 1000px white inset'
                            });
                        </script>
                    <?php
                    } else {
                        $cor = "danger";
                        $retorno = idioma("Desculpe! este link não é mais válido", "Excuse me! this link is no longer valid", "¡Perdon! este enlace ya no es válido");
                        echo "<script>setTimeout(function () {window.location='?';}, 5000);</script>";
                    }
                } else {

                    //echo recaptcha_get_html($publickey, $error);
                    echo newedit("SZlocalizacao//100/", "", "", "", "S", "");
                    echo exec('getmac');

                    if ($token <> "") {
                        echo newedit("SZtoken//50//TOKEN", "", strtoupper($token), idioma("Digite seu TOKEN", "Enter your TOKEN", "Ingrese su TOKEN"), "", "");
                    }
                    ?>
                    <div class="list-group">

                        <?php if ($token == "") { ?>

                            <li class="list-group-item" style="text-align: left; padding: 1px"><?PHP echo newedit("SNtoken//50//" . idioma("Digite seu TOKEN", "Enter your TOKEN", "Ingrese su TOKEN") . "/", "", strtoupper($token), "Digite o seu TOKEN", "", "onfocus=limpa();") ?></li>
                        <?php } else { ?>
                            <li class="list-group-item" id="idlogin" style="text-align: left; padding: 1px"><?PHP echo newedit("SNemail//50//" . idioma("Digite sua credencial", "Enter your credential", "Ingrese su credencial"), "", $JSONRecebido['login'], "Preencha uma credencial válida", "", "onfocus=limpa();") ?></li>
                            <?php if ($_GET['i'] <> '!') { ?>
                                <li class="list-group-item" id="idchave" style="text-align: left; padding: 1px"><?PHP echo newedit("SRsenha//25//" . idioma("Digite sua senha", "Type your password", "Escribe tu contraseña"), "", $JSONRecebido['pass'], "A senha deve ter no mínimo 6 caracteres", "", "onfocus=limpa();") ?></li><?php } ?>
                        <?php } ?>

                        <?php if ($linkexterno == true) { ?>
                            <li class="list-group-item text-center" style="padding: 1px; padding: 10px; background-color: #2A3F54; color: white">Você está acessando um link temporário<br><strong>Informe os dados recebidos por e-mail</strong></li>
                            <li class="list-group-item" style="text-align: left; padding: 1px"><?PHP echo newedit("SNlinkeE//51//" . idioma("Digite sua credencial ou login", "Enter your credential ou login", "Ingrese su credencial ou login"), "", "", "Preencha uma credencial válida", "", "") ?></li>
                            <li class="list-group-item" style="text-align: left; padding: 1px"><?PHP echo newedit("SNlinkeT//25//" . idioma("Digite o seu Token", "Type your token", "Escribe tu token"), "", "", "O token deve ter no mínimo 6 caracteres", "", "") ?></li>
                        <?php } ?>
                    </div>

                    <h4><span id="divoaut" style="color: #000;" class="center-block text-center <?php echo $_SESSION['fonte_padrao'] ?>"></span></h4>

                    <div style="padding-top: 5px; padding-bottom: 50px; padding: 0px 0px 0px 0px; text-align: center">

                        <?php if ($token == "") { ?>

                            <button onclick="window.location = '?t.' + $('#SNtoken').val()" id="recup" name="btndireciona" style="width: 100%; margin: 0px; height: 40px;" class="btn btn-dark alignleft" type="button"><?php echo idioma("Continuar", "Continue", "Continuar") ?></button>

                        <?php } else { ?>

                            <div class="col-md-12 col-sm-12 col-xs-12" style="margin: 0px 0px 0px 5px; padding: 0px 0px">
                                <button id="<?php
                                            if ($_GET['i'] <> '!') {
                                                echo "btnsalvar";
                                            } else {
                                                echo "btnrecupera";
                                            }
                                            ?>" name="<?php
                                                        if ($_GET['i'] <> '!') {
                                                            echo "btnsalvar";
                                                        } else {
                                                            echo "btnrecupera";
                                                        }
                                                        ?>" style="width: 100%; height: 40px; margin-bottom: 15px" class="btn btn-<?php echo $thema_loginweb_corbtncontinua ?> alignright" type="submit"><?php
                                                                                                                                                                                                    if ($_GET['i'] <> '!') {
                                                                                                                                                                                                        echo idioma("Autenticar acesso", "Authenticate access", "Autenticar para asistir");
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        echo idioma("Enviar senha", "Send password", "Enviar contraseña");
                                                                                                                                                                                                    }
                                                                                                                                                                                                    ?> <i class="fa fa-angle-right"></i></button>
                            </div>

                            <div class="col-md-6 col-sm-12 col-xs-12 <?php if ($btntoken == false) {
                                                                            echo "hidden";
                                                                        } ?>" style="margin: 0px; padding: 0px 0px">
                                <button onclick="window.location = '?mudat=1'" <?php echo tooltips_comum("bottom", idioma("Trocar o TOKEN", "Change TOKEN", "Cambiar TOKEN")) ?>id="voltar" name="btnvoltar" style="width: 100%; height: 40px" class="btn btn-transp alignleft" type="button"><i class="fa fa-angle-left"></i> Trocar o Token</button>
                            </div>
                            <div class="col-md-<?php if ($btntoken == false) {
                                                    echo "12";
                                                } else {
                                                    echo "6";
                                                } ?> col-sm-12 col-xs-12" style="margin: 0px; padding: 0px 0px 0px 5px">
                                <button onclick="window.location = '<?php
                                                                    if ($_GET['i'] <> '!') {
                                                                        echo "?t." . $token . "&i=!";
                                                                    } else {
                                                                        echo "?t." . $token;
                                                                    }
                                                                    ?>'" id="recup" name="btnrecupera" style="width: 100%; height: 40px" class="btn btn-transp alignleft" type="button"><?php
                                                                                                                                                                                        if ($_GET['i'] <> '!') {
                                                                                                                                                                                            echo idioma("Esqueceu sua senha?", "Forgot your password?", "Olvidaste tu contraseña?");
                                                                                                                                                                                        } else {
                                                                                                                                                                                            echo idioma("Voltar para Login", "Back to Login", "Atrás para sesión");
                                                                                                                                                                                        }
                                                                                                                                                                                        ?></button>
                            </div>



                        <?php } ?>
                    </div>

                <?php } ?>
                <br>

                <div class="clearfix"></div>


                <div>

                    <?php if ($mostraempresa == "S") { ?>
                        <br><br>
                        <p>
                            <i class="fa fa-globe green"></i> <span class="green">Português Brasil<?php echo $_SESSION['geolocalizacao'] ?></span>
                            <span class="right blue"> <i class="fa fa-phone blue"></i> +55 81 3037-5225</span>
                        <div><span><a target="_blank" href="http://www.workfacilit.com">www.workfacilit.com.br</a></span>
                            <span class="right"><a target="_blank" href="http://www.workfacilit.com/blog">blog.workfacilit.com.br</a></span>
                        </div>
                        <br><br>
                        <div class="text-center" style="width: 100%; font-size: 20px"><a class="facebook" href="#" style="margin-right: 10px"><i class="fa fa-facebook"></i></a> <a style="margin-right: 10px" class="twitter" href="#"><i class="fa fa-twitter"></i></a> <a class="linkedin" href="#"><i class="fa fa-linkedin"></i></a>
                            <br><img src="../imgs/logo_atend_blue.png" style="width: 140px; margin-top: 10px" class="center-block">


                        </div>
                    <?php } ?>
                    <div class="<?php echo $alinhamento ?>" style="padding-right: 5px"><br><br><br>

                        <span style="font-size: 12px; margin-left: 10px; color: <?php echo $thema_corinfor ?>"><strong><?php echo getOS() . " | " . getBrowser() . " | " . retorn_ip_client(); ?></strong></span>

                        <?php if ($token == "") { ?>
                            <div style="font-size: 13px; margin-left: 10px; color: <?php echo $thema_corinfor ?>">É necessário informar um TOKEN válido para acessar o Atend. <br>Caso não o tenha, procure o seu líder ou entre em contato com a nossa central de atendimento</div>
                        <?php } else { ?>
                            <div style="font-size: 13px; margin-left: 10px; color: <?php echo $thema_corinfor ?>">É necessário informar o seu LOGIN e SENHA. <br>Caso tenha alguma dificuldade, procure o seu líder ou entre em contato com a nossa central de atendimento<br><?php echo $menslog ?></div>
                        <?php } ?>
                        <br><br>
                        <div style="font-size: 23px; margin-left: 10px; color: <?php echo $thema_corinfor ?>"><?php if ($_SESSION['0800login'] == retornacodigotab("S")) { ?><strong><i class="fa fa-phone-square"></i> 0800-200-4008</strong> <?php } ?>
                            <?php if ($_SESSION['whatzapplogin'] == retornacodigotab("S")) { ?><span style="margin-left: 10px; margin-right: 10px"> </span> <a <?php echo tooltips_comum("top", "Clique para acessar") ?> target="_blank" href="http://api.whatsapp.com/send?1=pt_BR&phone=558130493847"><strong><i class="fa fa-whatsapp" aria-hidden="true"></i> Whatsapp</strong></a><?php } ?></div>
                        <br>
                        <?php if ($_SESSION['whatzapplogin'] == retornacodigotab("S")) { ?><div style="font-size: 13px; margin-left: 10px; color: <?php echo $thema_corinfor ?>">Atendimento através do Whatzapp entre 8h e 12h | 13h e 17h em dias úteis</div><?php } ?>
                    </div>
                </div>
        
            </form>

        <?php }else{?>

            <div class="row" style="margin-top: 0px; font-size: 30px;">Bem vindo!</div>
            <div class="row" style="margin-top: 0px; font-size: 15px;">Preencha o cadastro abaixo para ter acesso a plataforma Workfacilit</div>
            <div class="row" style="margin-top: 30px; font-size: 15px; font-weight: bold;">DADOS DA EMPRESA</div>
            <div class="row" style="margin-top: 20px"><?PHP echo newedit("SNfantasia/EMPRESA/50//" , "", "", "", "", "") ?></div>
            <div class="row" style="margin-top: 20px"><?PHP echo newedit("SJcnpj/CNPJ/50//" , "", "", "", "", "") ?></div>
            <div class="row" style="margin-top: 20px"><?PHP echo newedit("SCcep/CEP/20//" , "", "", "", "", "") ?></div>

            <div class="row" style="margin-top: 20px; font-size: 15px; font-weight: bold;">DADOS DO RESPONSÁVEL</div>
            <div class="row" style="margin-top: 20px"><?PHP echo newedit("SEemail/EMAIL CORPORATIVO/50//" , "", "", "", "", "") ?></div>
            <div class="row" style="margin-top: 10px"><?PHP echo newedit("SNnome/NOME COMPLETO/50//" , "", "", "", "", "") ?></div>
            <div class="row" style="margin-top: 10px"><?PHP echo newedit("SPcpf/CPF/30//" , "", "", "", "", "") ?></div>
            <div class="row" style="margin-top: 10px"><?PHP echo newedit("SNfuncao/FUNÇÃO/30//" , "", "", "", "", "") ?></div>
            
            <div class="row" style="margin-top: 10px">
                <button id="newcompany" name="btnnewcompany" style="width: 100%; height: 40px" class="btn btn-success alignleft" type="submit"> CADASTRAR EMPRESA <i class="fa fa-angle-right"></i> </button>
            </div>
            

        <?php }?>

        </div>
    </div>

    <div class="col-md-<?php echo $colsd ?> <?php echo $alinhamento ?>" style="
         padding-left: 480px; 
         margin-left: 50px;  
         color: #2A3F54; 
         height: 100%;
         width: 98%;
         border: 0px;
         
         ">

    </div>


    <div class="container" style="<?php echo $noexib ?> margin-top: 0px; border: 0px">
        <div class="jumbotron" style="z-index:1;background-color: transparent; color: <?php echo $thema_corbomdia ?>; text-align: <?php echo $thema_alinhahora ?>">
            <span style="font-size: 30px"><?php echo idioma("Olá!", "Hello!", "¡Hola!") ?></span>
            <p><?php echo idioma("Bem vindo", "Welcome", "Bienvenido") ?>
                <br><br><?php echo idioma($mensagem) ?>
            </p>
        </div>

        <?php if (date('m') == 100) { ?><img src="../imgs/outubro_rosa.png" style="position: absolute; top: 150px; right: 50px; width: 70px; z-index: 999"><?php } ?>
        <?php if (date('m') == 110) { ?><img src="../imgs/novembro_azul.png" style="position: absolute; top: 150px; right: 50px; width: 70px; z-index: 999"><?php } ?>
    </div>

    <div class="container" style="position: absolute; <?php echo $noexib ?> <?php echo $thema_alturahora ?>; z-index: 1">
        <div class="jumbotron" style="z-index: 1;background-color: transparent; color: <?php echo $thema_corhora ?>; text-align: <?php echo $thema_alinhahora ?>; padding-bottom: 20px; padding-right: 50px">
            <p style="font-size: 30px; margin: 0px"><span style="font-size: 50px" id="divrelogio"><?php echo date('H:i:s') ?></span></p>
            <p style="margin: 0px"><?php echo date('d/m/Y') ?></p>
        </div>
    </div>



    </div>

    <div id="loader">
        <div id="boxloader" class="text-center" style="top:250px;"><img src="../imgs/<?php
                                                                                        $load = LerTXT_BuscaParam($token, "thema_loading");
                                                                                        if ($load <> "") {
                                                                                            echo $load;
                                                                                        } else {
                                                                                            echo "load";
                                                                                        }
                                                                                        ?>.gif" style="max-height: 250px;">
            <p id="lbl_load"></p>
        </div>
    </div>

</body>

</html>
<script>
    $("#SNtoken").css({
        'border': '0',
        'background-color': 'white',
        'box-shadow': 'none',
        '-webkit-box-shadow': '0 0 0px 1000px white inset'
    });
    $("#SNemail").css({
        'border': '0',
        'background-color': 'white',
        'box-shadow': 'none',
        '-webkit-box-shadow': '0 0 0px 1000px white inset'
    });
    $("#SRsenha").css({
        'border': '0',
        'background-color': 'white',
        'box-shadow': 'none',
        '-webkit-box-shadow': '0 0 0px 1000px white inset'
    });
    $("#SNlinkeE").css({
        'border': '0',
        'background-color': 'white',
        'box-shadow': 'none',
        '-webkit-box-shadow': '0 0 0px 1000px white inset'
    });
    $("#SNlinkeT").css({
        'border': '0',
        'background-color': 'white',
        'box-shadow': 'none',
        '-webkit-box-shadow': '0 0 0px 1000px white inset'
    });
</script>

<!-- jQuery -->
<script src="../../vendors/jquery/dist/jq.min.js"></script>
<!-- Bootstrap -->
<script src="../../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Select -->
<script src="../../vendors/select-master/dist/js/bootstrap-select.min.js"></script>
<!-- FastClick -->
<script src="../../vendors/fastclick/lib/fastclick.js"></script>
<!-- NProgress -->
<script src="../../vendors/nprogress/nprogress.js"></script>
<!-- jQuery Smart Wizard -->
<script src="../../vendors/jQuery-Smart-Wizard/js/jquery.smartWizard.js"></script>
<!-- Custom Theme Scripts -->
<script src="../../build/js/custom.min.js"></script>
<!-- validator -->
<script src="../../vendors/validator/dist/validator.min.js"></script>
<!-- PNotify -->
<script src="../../vendors/pnotify/dist/pnotify.js"></script>
<script src="../../vendors/pnotify/dist/pnotify.buttons.js"></script>
<script src="../../vendors/pnotify/dist/pnotify.nonblock.js"></script>

<script>
    document.body.style.zoom = 0.8;

    $("#SEemail").keyup(function() {
        $(this).val($(this).val().toLowerCase())
    })
    $("#SNemail").keyup(function() {
        $(this).val($(this).val().toLowerCase())
    })
    $(document).ready(function() {
        $('.ui-pnotify').remove();
    });

    document.getElementById('loader').style.display = "none";

    $('#myFormaut').validator().on('submit', function(e) {
        if (e.isDefaultPrevented() == true) {
            return false;
        } else {
            if ($('#SRsenha1').length) {
                SubmitDados('#mudasenha', '#myFormaut', 'novasenha?p1=<?php echo cod($_GET['p1'], "D") ?>&p2=<?php echo $_GET['p2'] ?>&p3=<?php echo $_GET['p3'] ?>', '#divoaut', '', '');
            } else {
                SubmitDados('#btnsalvar', '#myFormaut', 'oauth?le=<?php echo $lelgtk ?>', '#divoaut', '', '');
            }
            return false;
        }
    });

    $('#SNtoken').val(localStorage.getItem('usrt'));
    <?php if (($token == "") and ($_GET['mudat'] == "")) { ?>if($('#SNtoken').val() != '') {
        //window.location = '?t.' + $('#SNtoken').val().toUpperCase();
    }
    <?php } ?>

    getLocation();
    relogio('divrelogio');


    //document.addEventListener('contextmenu', event => event.preventDefault());


    <?php if ($JSONRecebido['login'] <> "") { ?>
        $('#idlogin').addClass('hidden');
        $('#idchave').addClass('hidden');
    <?php } ?>

    function limpa(){
        $('#divoaut').empty();
    }
</script>