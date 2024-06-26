<?php
ob_start();
if(!(session_status() == PHP_SESSION_ACTIVE)){
    session_start();
}
require_once __DIR__."/../utils/RenderView.php";
require_once __DIR__."/../utils/autoload.php";

class ProximoController extends RenderView{
public function index(){
    if(empty($_SERVER['HTTP_REFERER'])){
        $clearSessions = new Validator();
        $clearSessions->destroi_sessao();
        header('Location: cadastro');
        exit();
    }
    $this->loadView(
        'proximo',
        [
            'nome' => $_POST['nome'],
            'cpf' => $_POST['cpf'],
            'data_nascimento' => $_POST['data_nascimento'],
            'telefone' => $_POST['telefone'],
            'email' => $_POST['email'],
            'senha' => $_POST['senha'],
            'sexo' => $_POST['sexo'],
            'pcd' => (isset($_POST['pcd']) && $_POST['pcd'] == 'on') ? 1 : '',
            'deficiencia' => (isset($_POST['pcd']) && $_POST['deficiencia'] != '') ? 1 : '',
            'idoso' => (isset($_POST['idoso']) && $_POST['idoso'] == 'on') ? 1 : ''
        ]
    );
}
public function validator(){
    if(empty($_SERVER['HTTP_REFERER'])){
        $clearSessions = new Validator();
        $clearSessions->destroi_sessao();
        $verifyLogin = new Login();
        if($verifyLogin->verifyLoginDoctor || $verifyLogin->verifyLoginEmployee()){
            header('Location: ../funcionario');
            exit();
        }else
        {
            header('Location: ../cadastro');
            exit();
        }
    }
    $ValidatorProx = new ValidatorPatient();
    $ValidatorProx->destroi_sessao();

    $post = ['nome', 'cpf', 'email', 'senha', 'telefone', 'data_nascimento', 'senha', 'sexo'];
    
    foreach($post as $campo){
        if(!isset($_POST[$campo])){
            header('Location: ../cadastro');
            exit();
        }
    }

    $paramPatient = [$_POST["nome"], $_POST["cpf"], $_POST["data_nascimento"], $_POST["telefone"], $_POST["email"], $_POST["senha"], $_POST["sexo"]];
    $allParameters = $paramPatient;

    if(isset($_POST['pcd']) && isset($_POST['idoso']) && isset($_POST['deficiencia']) && $_POST['deficiencia'] != '' && $_POST['pcd'] == 'on' && $_POST['idoso'] == 'on'){
        $allParameters = array_merge($paramPatient, [1, $_POST['deficiencia'], 1]);
    }else if(isset($_POST['pcd']) && isset($_POST['idoso']) && isset($_POST['deficiencia']) && $_POST['deficiencia'] == '' && $_POST['pcd'] == 'on' && $_POST['idoso'] == 'on'){
        $allParameters = array_merge($paramPatient, [1, '', 1]);
    }else if(isset($_POST['pcd']) && $_POST['deficiencia'] == '' && $_POST['pcd'] == 'on'){
        $allParameters = array_merge($paramPatient, [1, '', 0]);
    }else if(isset($_POST['pcd']) && isset($_POST['deficiencia']) && $_POST['deficiencia'] != '' && $_POST['pcd'] == 'on'){
        $allParameters = array_merge($paramPatient, [1, $_POST['deficiencia'], 0]);
    }else if(isset($_POST['idoso']) && $_POST['idoso'] == 'on'){
        $allParameters = array_merge($paramPatient, [0, '', 1]);
    }
    call_user_func_array(['Patient', 'novoPaciente'], $allParameters);

    if(!(isset($_POST['pcd']) || isset($_POST['idoso']))){
        $_SESSION['pid'] = true;
        header('Location: ../cadastro');
        exit();
    }    
    if(isset($_POST['pcd']) && $_POST['deficiencia'] == ''){
        $_SESSION['deficiencia'] = true;
        header('Location: ../cadastro');
        exit();
    }
    $FunctionsValid = new FunctionsPatient();
    $returnOrNot = $FunctionsValid->validaCampos($_POST['nome'], $_POST['cpf'], $_POST['senha'], $_POST['telefone'], $_POST['data_nascimento'], $_POST['email']);
    if(!$returnOrNot){
        header('Location: ../cadastro');
        exit();
    }
    unset($_SESSION['cad']);
    header('Location: ../proximo');
}
}
?>