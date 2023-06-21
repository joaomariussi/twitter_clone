<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
class AppController extends Action {

    public function timeline() {

        $this->validaAutenticacao();

        //recuperação dos tweets
        $tweet = Container::getModel('Tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweets = $tweet->getAll();

        $this->view->tweets = $tweets;

        $this->render('timeline');

    }

    public function tweet() {

        $this->validaAutenticacao();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar();

        header('Location: /timeline');
    }

    public function validaAutenticacao() {

        session_start();

        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=erro');
        }
    }

    public function quemSeguir() {

        $this->validaAutenticacao();

        $pesquisaPor = isset($_GET['pesquisaPor']) ? $_GET['pesquisaPor'] : '';

        $usuarios = array();

        if ($pesquisaPor != '') {

            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome', $pesquisaPor);
            $usuarios = $usuario->getAll();
        }

        $this->view->usuarios = $usuarios;

        $this->render('quemSeguir');
    }











}
