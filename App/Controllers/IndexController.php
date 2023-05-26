<?php

namespace App\Controllers;

//recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends  Action
{

    public function index()
    {

        $this->render('index');
    }

    public function inscreverse() {

        $this->view->usuario = array(
            'nome' => '',
            'email' => '',
            'senha' => '',
        );

        $this->view->erroCadastro = false;

        $this->render('inscreverse');
    }

    public function registrar() {

        $usuario = Container::get_Model('Usuario');

        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        $usuario->__set('nome', $nome);
        $usuario->__set('email', $email );
        $usuario->__set('senha', $senha);

        if ($usuario->validarCadastro() && count($usuario->recuperarUsuarioPorEmail()) == 0) {

                $usuario->salvar();

                $this->render('cadastro');

        } else {

            $this->view->usuario = array(
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha
            );

            $this->view->erroCadastro = true;

            $this->render('inscreverse');
        }
    }
}
