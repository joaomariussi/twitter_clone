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
            'senha' => '',
            'nome_usuario' => '',
            'data_nasc' => '',
            'localizacao' => '',

        );

        $this->view->erroCadastro = false;

        $this->render('inscreverse');
    }

    public function registrar() {

        // Cria uma instância do modelo Usuario
        $usuario = Container::getModel('Usuario');

        // Obtém os valores dos campos do formulário
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = md5($_POST['senha']) ?? '';
        $nome_usuario = ($_POST['nome_usuario']) ?? '';
        $data_nasc = $_POST['data_nasc'] ?? '';
        $localizacao = $_POST['localizacao'] ?? '';

        // Define os valores nos atributos do objeto Usuario
        $usuario->__set('nome', $nome);
        $usuario->__set('email', $email);
        $usuario->__set('senha', $senha);
        $usuario->__set('nome_usuario', $nome_usuario);
        $usuario->__set('data_nasc', $data_nasc);
        $usuario->__set('localizacao', $localizacao);

        /**
         * @var $usuario Usuario
         */

        // Valida o cadastro do usuário, verifica se já existe um usuário com o mesmo email e também verifica se já existe o nome_usuario igual do que está sendo inserido.
        if ($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0 && count($usuario->getNomeUsuario()) == 0) {

            // Salva o usuário no banco de dados
            $usuario->salvar();

            // Renderiza a página de cadastro bem-sucedido
            $this->render('cadastro');

        } else {

            // Caso haja erros de validação ou um usuário com o mesmo email já exista,
            // define os valores no objeto de visualização para exibição no formulário
            $this->view->usuario = array(
                'nome' => $nome,
                'email' => $email,
                'senha' => $senha,
                'nome_usuario' => $nome_usuario,
                'data_nasc' => $data_nasc,
                'localizacao' => $localizacao,

            );

            $this->view->erroCadastro = true;

            // Renderiza a página de inscrição novamente, exibindo mensagens de erro
            $this->render('inscreverse');
        }
    }
}
