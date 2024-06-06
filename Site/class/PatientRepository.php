<?php
if(!(session_status() == PHP_SESSION_ACTIVE)){
    session_start();
}
require_once __DIR__."/../utils/autoload.php";

class PatientRepository
{
    private $conexao;

    public function  __construct()
    {
        try 
        {
            $this->conexao = new Conexao();
        } 
        catch (Exception $e)
        {
            throw new Exception("Não foi possível conectar-se ao banco de dados: " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        if ($this->conexao) {
            $this->conexao = null;
        }
    }

    public function SelecionaPaciente($email, $password)
    {
        $pdo = $this->conexao->getConexao();
        $sql = "SELECT id, email, nome, senha FROM pacientes WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            if (password_verify($password, $result['senha'])) {
                $loginPatient = new Login();
                $loginPatient->login($result['id'], $result['nome']);
                return true;
            } else {
                $_SESSION['login_error'] = true;
                return false;
            }
        } else {
            $_SESSION['login_error'] = true;
            return false;
        }
    }
    public function SelecionaEmail($email)
    {
        $pdo = $this->conexao->getConexao();
        $sql = "SELECT email FROM pacientes WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return false;
        } else {
            return true;
        }
    }

    public function SelecionaCPF($cpf)
    {
        $pdo = $this->conexao->getConexao();
        $sql = "SELECT cpf FROM pacientes WHERE cpf = :cpf";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return false;
        } else {
            return true;
        }
    }

    public function CreateEndereco(Address $endereco)
    {
        try
        {
            $sql = "INSERT INTO enderecos (cep, estado, cidade, bairro, rua, numeroCasa, complemento) 
                    VALUES (:cep, :estado, :cidade, :bairro, :rua, :numero, :complemento)";
            $stmt = $this->conexao->getConexao()->prepare($sql);

            $cep = $endereco->getCep();
            $estado = $endereco->getEstado();
            $cidade = $endereco->getCidade();
            $bairro = $endereco->getBairro();
            $rua = $endereco->getRua();
            $numero = $endereco->getNumero();
            $complemento = $endereco->getComplemento();

            $stmt->bindParam(':cep', $cep);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':cidade', $cidade);
            $stmt->bindParam(':bairro', $bairro);
            $stmt->bindParam(':rua', $rua);
            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':complemento', $complemento);

            $stmt->execute();
            return $this->conexao->getConexao()->lastInsertId();
        }
        catch (PDOException $e)
        {
            throw new Exception("Erro ao criar paciente: ".$e->getMessage());
        }
    }

    public function CreatePaciente(Patient $patient, $id)
    {
        try
        {
            $sql = "INSERT INTO pacientes (nome, cpf, nascimento, telefone, email, necessidadeEspecial, necessidadeTipo, idoso, genero, id_endereco, senha) 
                    VALUES (:nome, :cpf, :nascimento, :telefone, :email, :necessidadeEspecial, :necessidade, :idoso, :genero, :id, :senha)";
            $stmt = $this->conexao->getConexao()->prepare($sql);
            // Bind dos parâmetros
           // Atribua os valores de retorno dos métodos às variáveis
            $nome = $patient->getNome();
            $cpf = $patient->getCpf();
            $nascimento = $patient->getNascimento();
            $telefone = $patient->getTelefone();
            $email = $patient->getEmail();
            if(isset($patient->necessidadeEspecial) && $patient->necessidadeEspecial == 1){
                $necessidadeEspecial = $patient->getNecessidadeEspecial();
                $necessidade = $patient->getNecessidade();
            }
            if(isset($patient->idoso) && $patient->idoso == 1){
                $idoso = $patient->getIdoso();
            }
            $genero = $patient->getGenero();
            $senha = $patient->getSenha();
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            // Passe as variáveis como argumentos para bindParam()
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->bindParam(':nascimento', $nascimento);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':necessidadeEspecial', $necessidadeEspecial);
            $stmt->bindParam(':necessidade', $necessidade);
            $stmt->bindParam(':idoso', $idoso);
            $stmt->bindParam(':genero', $genero);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':senha', $hash);

            // Execução da consulta
            $stmt->execute();
        }
        catch (PDOException $e)
        {
            throw new Exception("Erro ao criar paciente: ".$e->getMessage());
        }
    }
}
?>