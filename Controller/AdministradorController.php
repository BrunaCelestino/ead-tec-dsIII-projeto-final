<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AdministradorController
{
    public function login($cpf, $senha)
    {
        require_once __DIR__ . '/../Model/Administrador.php';

        $administrador = new Administrador();
        $administrador->carregarAdministrador($cpf);

        if ($administrador->getSenha() == $senha) {
            $_SESSION['Administrador'] = serialize($administrador);
            return true;
        }

        return false;
    }

    public function gerarLista()
    {
        require_once '../Model/Administrador.php';
        $u = new Administrador();
        return $results = $u->listaCadastrados();
    }
}
