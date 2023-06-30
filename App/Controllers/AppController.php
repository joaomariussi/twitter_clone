<?php

namespace App\Controllers;

use App\Models\Tweet;
use App\Models\Usuario;
use MF\Controller\Action;
use MF\Model\Container;
class AppController extends Action {

    public function validaAutenticacao() {

        session_start();

        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=erro');
        }
    }

    public function timeline() {

        /**
         * @var $tweet Tweet
         */

        $this->validaAutenticacao();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweets = $tweet->getAll();

        $this->view->tweets = $tweets;

        /**
         * @var $usuario Usuario
         */

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']); //Seta se o id do usuário é o mesmo da sessão.

        //Seta a view e chama o getInfoUsuario da minha classe Usuario.
        $this->view->info_usuario = $usuario->getInfoUsuario();

        //Seta a view e chama o getTotalTweets da minha classe Usuario.
        $this->view->total_tweets = $usuario->getTotalTweets();

        //Seta a view e chama o getUsariosSeguindo da minha classe Usuario.
        $this->view->total_seguindo = $usuario->getUsariosSeguindo();

        //Seta a view e chama o getTotalSeguidores da minha classe Usuario.
        $this->view->total_seguidores = $usuario->getTotalSeguidores();


        //rendeniza a view timeline.
        $this->render('timeline');

    }

    public function tweet() {

        $this->validaAutenticacao();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_usuario', $_SESSION['id']);

        /**
         * @var $tweet Tweet
         */

        $tweet->salvar();

        header('Location: /timeline');

        $id_tweet = $_POST['id_tweet'];

        $tweet = Container::getModel('Tweet');
        $tweet->__set('id', $id_tweet);
        $tweet->remover();

        header('Location: /timeline');
    }

    public function quemSeguir() {

        $this->validaAutenticacao();

        /**
         * @var $usuario Tweet
         */

        $pesquisaPor = isset($_GET['pesquisaPor']) ? $_GET['pesquisaPor'] : '';

        $usuarios = array();

        if ($pesquisaPor != '') {

            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome', $pesquisaPor);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios = $usuario->getAll();
        }

        $this->view->usuarios = $usuarios;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']); //Seta se o id do usuário é o mesmo da sessão.

        /**
         * @var $usuario Usuario
         */
        //Seta a view e chama o getInfoUsuario da minha classe Usuario.
        $this->view->info_usuario = $usuario->getInfoUsuario();

        //Seta a view e chama o getTotalTweets da minha classe Usuario.
        $this->view->total_tweets = $usuario->getTotalTweets();

        //Seta a view e chama o getUsariosSeguindo da minha classe Usuario.
        $this->view->total_seguindo = $usuario->getUsariosSeguindo();

        //Seta a view e chama o getTotalSeguidores da minha classe Usuario.
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('quemSeguir');
    }

    public function acao() {

        $this->validaAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if ($acao == 'seguir') {
            $usuario->seguirUsuario($id_usuario_seguindo);

        } elseif ($acao == 'deixar_de_seguir') {
            $usuario->deixarSeguirUsuario($id_usuario_seguindo);
        }

        header('Location: /quem_seguir');
    }

    public function minhaConta() {

        $this->validaAutenticacao();

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']); //Seta se o id do usuário é o mesmo da sessão.

        /**
         * @var $usuario Usuario
         */
        //Seta a view e chama o getInfoUsuario da minha classe Usuario.
        $this->view->info_usuario = $usuario->getInfoUsuario();

        //Seta a view e chama o getTotalTweets da minha classe Usuario.
        $this->view->total_tweets = $usuario->getTotalTweets();

        //Seta a view e chama o getUsariosSeguindo da minha classe Usuario.
        $this->view->total_seguindo = $usuario->getUsariosSeguindo();

        //Seta a view e chama o getTotalSeguidores da minha classe Usuario.
        $this->view->total_seguidores = $usuario->getTotalSeguidores();


        $this->render('minhaConta');
    }

}
