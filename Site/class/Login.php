<?php
require_once __DIR__."/../utils/autoload.php";
class Login{
    public function login($id, $nome){
        $_SESSION['login'] = true;
        $_SESSION['login_id'] = $id;
        $_SESSION['login_nome'] = $nome;
    }
    public function loginEmployee($id, $nome){
        $_SESSION['login'] = true;
        $_SESSION['employee'] = true;
        $_SESSION['login_id'] = $id;
        $_SESSION['login_nome'] = $nome;
    }
    public function loginDoctor($id, $nome){
        $_SESSION['login'] = true;
        $_SESSION['login_doctor'] = true;
        $_SESSION['login_id'] = $id;
        $_SESSION['login_nome'] = $nome;
    }
    public function logout(){
        if(isset($_SESSION['login']) && $_SESSION['login'] == true){
            unset($_SESSION['login']);
            unset($_SESSION['login_id']);
            unset($_SESSION['login_nome']);
            if(isset($_SESSION['employee']) && $_SESSION['employee'] == true){
                unset($_SESSION['employee']);
            }
            if(isset($_SESSION['doctor']) && $_SESSION['doctor'] == true){
                unset($_SESSION['doctor']);
            }
            session_destroy();
        }
    }
    public function verifyLogin(){
        if(!isset($_SESSION['login'])){                                  
            return false;
        }
        return true;
    }
    public function verifyLoginEmployee(){
        if(!isset($_SESSION['employee'])){                                  
            return false;
        }
        return true;
    }
}
?>