<?php

namespace App\Controllers;

//recursos do miniframework
use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends  Action
{

    public function index()
    {

        $this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
        $this->render('index');
    }

    public function inscreverse() {

        $this->view->usuario = array(
            'nome' => '',
            'email' => '',
            'data_nasc' => '',
            'localizacao' => '',
            'senha' => '',
        );

        $this->view->erroCadastro = false;

        $this->render('inscreverse');
    }

    public function registrar() {

        $usuario = Container::getModel('Usuario');

        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $data_nasc = $_POST['data_nasc'] ?? '';
        $localizacao = $_POST['localizacao'] ?? '';
        $senha = md5($_POST['senha']) ?? '';

        $usuario->__set('nome', $nome);
        $usuario->__set('email', $email );
        $usuario->__set('data_nasc', $data_nasc);
        $usuario->__set('localizacao', $localizacao);
        $usuario->__set('senha', $senha);

        /**
         * @var $usuario Usuario
         */

        if ($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0) {

                $usuario->salvar();

                $this->render('cadastro');

        } else {

            $this->view->usuario = array(
                'nome' => $nome,
                'email' => $email,
                'data_nasc' => $data_nasc,
                'localizacao' => $localizacao,
                'senha' => $senha
            );

            $this->view->erroCadastro = true;

            $this->render('inscreverse');
        }
    }
}
