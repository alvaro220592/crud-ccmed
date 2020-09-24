<?php 

namespace App\Model;

class UsuarioDao{

	public function create(Usuario $usu){
		$sql = 'INSERT INTO USUARIO (login, senha, nome, sobrenome, matricula, id_acesso_fk) VALUES(?, ?, ?, ?, ?, ?)';
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->bindValue(1, $usu->getlogin());
		$stmt->bindValue(2, $usu->getsenha());
		$stmt->bindValue(3, $usu->getnome());
		$stmt->bindValue(4, $usu->getsobrenome());
		$stmt->bindValue(5, $usu->getmatricula());
		$stmt->bindValue(6, $usu->getid_acesso_fk());
		$stmt->execute();
	}

	public function read(){
		$sql = "SELECT id_usuario, login, md5(senha) as 'senha', nome, sobrenome, matricula, id_acesso_fk FROM USUARIO";
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->execute();
		if($stmt->rowCount() > 0){
			$resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			return $resultado;
		}else{
			echo "Nenhum registro";
		}
	}

	public function update(Usuario $usu){
		$sql = 'UPDATE USUARIO SET login = ?, senha = ?, nome = ?, sobrenome = ?, matricula = ?, id_acesso_fk = ? WHERE id_usuario = ?';
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->bindValue(1, $usu->getlogin());
		$stmt->bindValue(2, $usu->getsenha());
		$stmt->bindValue(3, $usu->getnome());
		$stmt->bindValue(4, $usu->getsobrenome());
		$stmt->bindValue(5, $usu->getmatricula());
		$stmt->bindValue(6, $usu->getid_acesso_fk());
		$stmt->bindValue(7, $usu->getid_usuario());
		$stmt->execute();
	}

	public function delete($id_usuario){
		$sql = 'DELETE FROM USUARIO WHERE id_usuario = ?';
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->bindValue(1, $id_usuario);
		$stmt->execute();
	}

}

 ?>