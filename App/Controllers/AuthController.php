<?php

namespace App\Controllers;

//recursos do miniframework
use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {

    //Função responsável por autenticar o login do usuário.
    public function autenticar() {

        /**
         * @var $usuario Usuario
         */

        // Cria uma instância do modelo Usuario
        $usuario = Container::getModel('Usuario');

        // Define o valor do email e senha nos atributos do objeto Usuario
        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', md5($_POST['senha']));

        // Chama a função autenticar() do objeto Usuario para verificar as credenciais
        $retorno = $usuario->autenticar();

        // Verifica se a autenticação foi bem-sucedida
        if ($usuario->__get('id') != '' && $usuario->__get('nome')) {

            // Inicia a sessão
            session_start();

            // Define as variáveis de sessão com o ID e o nome do usuário autenticado
            $_SESSION['id'] = $usuario->__get('id');
            $_SESSION['nome'] = $usuario->__get('nome');

            // Redireciona para a página de timeline
            header('Location: /timeline');

        } else {

            // Caso a autenticação falhe, redireciona para a página inicial com uma mensagem de erro
            header('Location: /?login=erro');
        }
    }

    public function sair() {

        session_start();
        session_destroy();

        header('Location: /');
    }
}