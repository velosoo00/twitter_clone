<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {


	public function timeline() {
		
		$this->validaAutenticacao();
		
		//recuperação dos tweets
		$tweet = Container::getModel('Tweet');

		$tweet->__set('id_usuario', $_SESSION['id']);
		
		//variáveis de paginação
		$total_registros_paginas = 7;//limit
		$pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
		$deslocamento = ($pagina - 1) * $total_registros_paginas;//offset
		
		/*
		$total_registros_paginas = 10;//limit
		$deslocamento = 10;//offset
		$pagina = 2;

		$total_registros_paginas = 10;//limit
		$deslocamento = 20;//offset
		$pagina = 3;
		*/

		//$tweets = $tweet->getAll();
		$tweets = $tweet->getPorPagina($total_registros_paginas, $deslocamento);
		$total_tweets = $tweet->getTotalRegistros();
		
		$this->view->total_de_paginas = ceil($total_tweets['total'] / $total_registros_paginas);

		$this->view->pagina_ativa = $pagina;

		$this->view->tweets = $tweets;

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();

		$this->render('timeline');

	}

	public function tweet(){

		$this->validaAutenticacao();
			
		$tweet = Container::getModel('Tweet');

		$tweet->__set('tweet', $_POST['tweet']);
		$tweet->__set('id_usuario', $_SESSION['id']);

		$tweet->salvar();

		header('location: /timeline');

	}

	public function validaAutenticacao(){

		session_start();

		if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {

			header('Location: /?login=erro');

		}

	}

	public function quemSeguir(){

		$this->validaAutenticacao();

		$pesquisar = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '';

		$usuarios = array();

		if($pesquisar != ''){

			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisar);
			$usuario->__set('id', $_SESSION['id']);
			$usuarios = $usuario->getAll();

		}

		$this->view->usuarios = $usuarios;

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();

		$this->render('quemSeguir');

	}

	public function acao(){

		$this->validaAutenticacao();

		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		if($acao == 'seguir'){

			$usuario->seguirUsuario($id_usuario_seguindo);

		}elseif($acao == 'deixar_de_seguir'){

			$usuario->deixarSeguirUsuario($id_usuario_seguindo);

		}

		header('location: /quem_seguir');

	}

	public function remover(){

		$this->validaAutenticacao();

		$id = isset($_GET['id']) ? $_GET['id'] : '';

		$tweet = Container::getModel('Tweet');

		$tweet->__set('id', $id);

		$tweet->delete();

		header('location: /timeline');

	}

}

?>