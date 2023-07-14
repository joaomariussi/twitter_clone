<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model {

    private $id;
    private $id_usuario;
    private $tweet;
    private $data;

    public  function __get($atributo) {
        return $this->$atributo;
    }

    public function __set($atributo, $valor) {
        return $this->$atributo = $valor;
    }

    //salva o tweet no banco de dados.
    public function salvar(): static
    {

        $tweet = trim($this->__get('tweet')); // Remove espaços em branco do início e do final do tweet

        if (empty($tweet)) { // Verifica se o tweet está vazio após remover os espaços em branco
            return $this; // Retorna o objeto Tweet sem salvar no banco de dados
        }

        $query = "insert into tweets(id_usuario, tweet) values(:id_usuario, :tweet)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':tweet', $this->__get('tweet'));
        $stmt->execute();

        return $this;
    }

    public function remover(): Tweet
    {
        $query = "DELETE FROM tweets WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();

        return $this;
    }


    //recupera os registro de tweets dos usuários.
    public function getAll()
    {

        $query = "select
                    t.id, t.id_usuario, u.nome, t.tweet, date_format(t.data, '%d/%m/%Y %H:%i') as data
                from tweets as t
                    left join usuarios as u on (t.id_usuario = u.id)
                where 
                    t.id_usuario = :id_usuario
                or t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores where id_usuario = :id_usuario)
                order by 
                    t.data desc";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
