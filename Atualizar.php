<?php
namespace app\models;
use app\core\Model;
use Exception;

class Atualizar extends Model{
    protected $sql = "";
    protected $where = false;
    public function concluirAula($dados){
        $sql = "INSERT INTO aulas_assistidas SET 
        idusuario= :idusuario,
        idaula= :idaula,
        idcurso= :idcurso,
        data_assistida= :data,
        hora_assistida= :hora";
        $update = $this->db->prepare($sql);
        $update->bindValue(":idusuario", $dados->idusuario);
        $update->bindValue(":idaula", $dados->idaula);
        $update->bindValue(":idcurso", $dados->idcurso);
        $update->bindValue(":data", date("Y-m-d"));
        $update->bindValue(":hora", date("H:m:i"));
        $updated = $update->execute();
        return $updated;
    }
    public function resetarAula($dados){
        $sql = "DELETE FROM aulas_assistidas WHERE idusuario= :idusuario AND idaula= :idaula AND idcurso= :idcurso";
        $delete = $this->db->prepare($sql);
        $delete->bindValue(":idusuario", $dados->idusuario);
        $delete->bindValue(":idaula", $dados->idaula);
        $delete->bindValue(":idcurso", $dados->idcurso);
        $deleted = $delete->execute();
        return $deleted;
    }
    /* FUNÇÃO DE ATUALIZAÇÃO DE REGISTROS. */
    public function update($table){
        return $this->sql = "UPDATE {$table} SET ";
    }
    /* FUNÇÃO QUE RECEBE OS CAMPOS A SEREM ATUALIZADOS */
    public function fields(array $fields){
        foreach($fields as $index => $field){
            if(array_key_last($fields) == $index){
                return $this->sql .= "{$index} = :{$index}" ;
            }
                $this->sql .= "{$index} = :{$index}, ";
            }
        return $this->sql;
    }
    /* CLÁUSULA WHERE QUE DEFINE UM REGISTRO ESPECÍFICO A SER ATUALIZADO. */
    public function where($operador, $field){
        $this->where = true;
        $this->sql .= " WHERE {$field} {$operador} :{$field} ";
        return $this->sql;
    }
    /* OPERADOR LÓGICO OR PARA A CLÁUSULA WHERE. SÓ PODE SER USADA SE A CLÁUSULA WHERE FOR DEFINIDA. */
    public function orWhere($operador, $field){
        if($this->where == true){
            $this->sql .= " OR {$field} {$operador} :{$field} ";
        }else{
            setFlash("message", "A cláusula WHERE não foi definida");
            return redirect("errorquery");
        }
        return $this->sql;
    }
    /* OPERADOR LÓGICO AND PARA A CLÁUSULA WHERE. SÓ PODE SER USADA SE A CLÁUSULA WHERE FOR DEFINIDA. */
    public function andWhere($operador, $field){
        if($this->where == true){
            $this->sql .= " AND {$field} {$operador} :{$field} ";
        }else{
            setFlash("message", "A cláusula WHERE não foi definida");
            return redirect("errorquery");
        }
        return $this->sql;
    }
    /* FUNÇÃO QUE EXECUTA A SQL. O FOREACH PERCORRE A ARRAY RECEBIDA E SUBSTITUI O FIELD PELO DADO A SER ATUALIZADO NO BD, INCLUSIVE NO WHERE.
    */
    public function execute(array $dados){
        $execute = $this->db->prepare($this->sql);
        foreach($dados as $field => $dado){
            $execute->bindValue(":{$field}", $dado);
        }
        $execute->execute();
        return $this->db->lastInsertId();        
    }
}