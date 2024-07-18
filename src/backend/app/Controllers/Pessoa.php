<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\IncomingRequest;

class Pessoa extends ResourceController 
{
    protected $format = 'json';
        
    public function index() 
    {        
        $db = \Config\Database::connect();
        $sql = "select id, nome, telefone, email, data_nascimento as datanascimento, TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) AS idade from Pessoas ";        
        $query = $db->query($sql);
        return $this->respond($query->getResult());
    }

    public function upload($id)
    {
        $request = request();
        $img = $request->getFile('arquivo');      
        if (!empty($img)) 
        {
            $data = file_get_contents($img->getTempName());
            if ($data) 
            {
                $db = \Config\Database::connect();
                $sql = 'update pessoas set imagem = ? where id = ?';
                $db->query($sql, [$data, $id]);
            }
        }        
    }

    public function show($segment=null, $term=null, $datanascimento1=null) 
    {        
        $db = \Config\Database::connect();
        $sql = "select id, nome, telefone, email, data_nascimento as datanascimento, TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) AS idade from Pessoas where 1=1 ";
        if (!empty($segment)) 
        {
            $sql .= " and id = " . $db->escapeString($segment);
        }        
        if (!empty($term))
        {
            $sql .= " and (nome like '%" . $db->escapeLikeString($term) . "%' ESCAPE '!' or email like '%" .  $db->escapeLikeString($term) . "%' ESCAPE '!')";
        }
        if (!empty($datanascimento1)) 
        {
            $sql .= " and date(data_nascimento) = '" . $db->escapeString($datanascimento1) . "'";
        }

        $query = $db->query($sql);
        return $this->respond($query->getResult());
    }
    
    public function update($id=null) 
    {
        $request = request();
        $payload = $request->getJSON();   

        if (!property_exists($payload, 'nome') || empty($payload->nome)) 
        {
            return $this->respond(self::mensagemPropRequerida('nome'));
        }

        if (!property_exists($payload, 'telefone') || empty($payload->telefone)) 
        {
            return $this->respond(self::mensagemPropRequerida('telefone'));
        }

        if (!property_exists($payload, 'email') || empty($payload->email)) 
        {
            return $this->respond(self::mensagemPropRequerida('email'));
        }

        if (!property_exists($payload, 'datanascimento') || empty($payload->datanascimento)) 
        {
            return $this->respond(self::mensagemPropRequerida('datanascimento'));
        }

        $db = \Config\Database::connect();
        
        $query = $db->query('select * from Pessoas where id = ' . $db->escapeString($id));
        if ($query->getNumRows() < 1) 
        {
            return $this->respond(['mensagem' => 'Oops.. o registro não existe.']);
        }

        $sql = "update Pessoas set nome = ?, telefone = ?, email = ?, data_nascimento = ? where id = ?";
        $db->query($sql, [$payload->nome, $payload->telefone, $payload->email, $payload->datanascimento, $id]);

        return $this->respond(['update' => 'update']);
    } 

    public function create() 
    {
        $request = request();
        $payload = $request->getJSON();        
        
        if (!property_exists($payload, 'nome') || empty($payload->nome)) 
        {
            return $this->respond(self::mensagemPropRequerida('nome'));
        }

        if (!property_exists($payload, 'telefone') || empty($payload->telefone)) 
        {
            return $this->respond(self::mensagemPropRequerida('telefone'));
        }

        if (!property_exists($payload, 'email') || empty($payload->email)) 
        {
            return $this->respond(self::mensagemPropRequerida('email'));
        }

        if (!property_exists($payload, 'datanascimento') || empty($payload->datanascimento)) 
        {
            return $this->respond(self::mensagemPropRequerida('datanascimento'));
        }            

        $db = \Config\Database::connect();
        $sql = "insert into Pessoas (nome, telefone, email, data_nascimento) values (?, ?, ?, ?)";
        $db->query($sql, [$payload->nome, $payload->telefone, $payload->email, $payload->datanascimento]);
        return $this->respond(['pessoaId' => $db->insertID()]);
    }

    public function delete($id=null) 
    {
        $db = \Config\Database::connect();
        $id = $db->escapeString($id);        
        
        $query = $db->query('select * from Pessoas where id = ' . $id);
        if ($query->getNumRows() < 1) 
        {
            return $this->respond(['mensagem' => 'Oops.. o registro não existe.']);
        }
        
        $sql = "delete from Pessoas where id = " . $id;
        $db->query($sql);
        return $this->respond(['delete' => $id]);
    } 

    private static function mensagemPropRequerida($propName) 
    {
        return ['mensagem' => 'Oops... informe a propriedade ' . $propName . ' com um valor valido'];
    }
}