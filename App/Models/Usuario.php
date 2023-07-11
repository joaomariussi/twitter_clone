<?php

namespace App\Models;

use MF\Model\Model;

use Carbon\Carbon;
class Usuario extends Model {

    private $id;
    private $nome;
    private $email;
    private $senha;
    private $nome_usuario;
    private $data_nasc;
    private $localizacao;

    public  function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        return $this->$atributo = $valor;
    }

    //função responsável por salvar o usuário no banco de dados.
    public function salvar(): static
    {

        $data_nasc = date('Y-m-d', strtotime($this->__get('data_nasc')));

        $query = "insert into usuarios(nome, email, senha, nome_usuario, data_nasc, localizacao) values (:nome, :email, :senha, :nome_usuario, :data_nasc, :localizacao)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->bindValue(':nome_usuario', $this->__get('nome_usuario'));
        $stmt->bindValue(':data_nasc', $data_nasc);
        $stmt->bindValue(':localizacao', $this->__get('localizacao'));

        $stmt->execute();
        return $this;
    }

    //função responsável por validar o cadastro do usuário.
    public function validarCadastro(): bool
    {

        $valido = true;

        if (strlen($this->__get('nome')) < 3) {
            $valido = false;
        }

        if (strlen($this->__get('email')) < 3) {
            $valido = false;
        }

        if (strlen($this->__get('senha')) < 3) {
            $valido = false;
        }

        if (strlen($this->__get('nome_usuario')) <3) {
            $valido = false;
        }

        $dataNasc = $this->__get('data_nasc');
        if (strlen($dataNasc) == 0 || !strtotime($dataNasc)) {
            $valido = false;
        }

        if (strlen($this->__get('localizacao')) < 3) {
            $valido = false;
        }

        return $valido;
    }

    //recuperar um usuário por email e faz as validações
    public function getUsuarioPorEmail(): false|array
    {

        $query = "select nome, email from usuarios where email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getNomeUsuario() {

        $query = "Select nome, nome_usuario from usuarios where nome_usuario = :nome_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome_usuario', $this->__get('nome_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Verifica os dados do usuário ao tentar logar.
    public function autenticar(): static
    {

        $query = "select id, nome, email from usuarios where email = :email  and senha = :senha";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if(!empty($usuario['id']) && !empty($usuario['nome'])) {
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);

        }

        return $this;
    }

    //Faz a busca por todos os usuários cadastrados no banco de dados e fazendo a validação se um usuário segue o outro.
    public function getAll() {

        $query =
            "select 
                u.id, 
                u.nome, 
                u.email,
                u.nome_usuario,
                u.data_nasc,
                u.localizacao,
                (
                    select 
                        count(*)
                    from 
                        usuarios_seguidores as us 
                    where 
                        us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id
                ) as seguir_sn
            from 
                usuarios as u 
            where 
                u.nome like :nome and u.id != :id_usuario";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Insere o id_usuario na coluna id_usuario_seguindo da tabela usuarios_seguidores.
    public function seguirUsuario($id_usuario_seguindo) {

        $query = "insert into usuarios_seguidores(id_usuario, id_usuario_seguindo) values(:id_usuario, :id_usuario_seguindo)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();

        return true;
    }

    //Deleta o usuário da tabela usuarios_seguidores.
    public function deixarSeguirUsuario($id_usuario_seguindo) {

        $query = "delete from usuarios_seguidores where id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);
        $stmt->execute();

        return true;
    }

    //Busca o nome do usuário dentro do banco de dados.
    public function getInfoUsuario() {

        $query = "Select nome from usuarios where id = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    //Faz a busca e conta, todos os tweets feito pelo usuário.
    public function getTotalTweets() {

        $query = "Select count(*) as total_tweets from tweets where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    //Faz a busca e conta, todos os usuários seguindo pelo usuário da sessão.
    public function getUsariosSeguindo() {

        $query = "Select count(*) as total_seguindo  from usuarios_seguidores where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    //Faz a busca e conta, todos os seguidores que seguem o usuário da sessão.
    public function getTotalSeguidores() {

        $query = "Select count(*) as total_seguidores  from usuarios_seguidores where id_usuario_seguindo = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getDataNasc(): string
    {
        $query = "Select data_nasc from usuarios where id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();

        $dataNasc = $stmt->fetch(\PDO::FETCH_ASSOC)['data_nasc'];

        //retorna a data já formatada em nome do mês, exemplo: 03 de julho de 2001.
        return Carbon::parse($dataNasc)->locale('pt_BR')->isoFormat('D [de] MMMM [de] YYYY');
    }

    public function getLocalizacao() {

        $query = "Select localizacao as localidade from usuarios where id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}