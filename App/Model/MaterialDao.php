<?php 
//namespace para o autoload(carregamento automático de classes pelo PSR-4)
namespace App\Model;

class MaterialDao{
	public function create(Material $m){ //recebe a classe Material como parâmetro instanciada como $p
		$sql = 'INSERT INTO MATERIAL(nome_material, desc_material, qtde_estoque, id_prat_fk, id_forn_fk, imagem) VALUES(?, ?, ?, ?, ?, ?)';
		//as interrogações são equivalentes aos valores
		$extensao = strtolower(substr($_FILES['nome_imagem']['name'], -4)); //pega a extensao do arquivo
	    $imagem = md5($_FILES['nome_imagem']['name']) . $extensao; //define o nome do arquivo
	    $diretorio = "upload/"; //define o diretorio para onde enviaremos o arquivo
		move_uploaded_file($_FILES['nome_imagem']['tmp_name'], $diretorio.$imagem); //efetua o upload
		//Preparando o sql usando o PDO, começando com o método getConn(que é uma instância do PDO) da classe Conexao:
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->bindValue(1, $m->getnome_material());
		$stmt->bindValue(2, $m->getdesc_material());
		$stmt->bindValue(3, $m->getqtde_estoque());
		$stmt->bindValue(4, $m->getid_prat_fk());
		$stmt->bindValue(5, $m->getid_forn_fk());
		$stmt->bindValue(6, $imagem);
		//$stmt->bindValue(6, $m->getnome_imagem());
		$stmt->execute();
		//header("location:form-cadastrar.php");
	}

	public function read(){
		$sql = 'SELECT * FROM MATERIAL';
		//Preparando o sql usando o PDO, começando com o método getConn(que é uma instância do PDO) da classe Conexao:
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->execute();

		//verificando de a consulta retorna algum resultado:
		if($stmt->rowCount() > 0): //se a contagem de linhas for maior 0:
			$resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC); //retornará um array com todos os registros, que vai ser atribuído à variável $resultado
			return $resultado;
		else:
			echo "Nenhum registro";
		endif;
	}

	public function update(Material $m){//recebe a classe Material como parâmetro instanciada como $p
		$sql = 'UPDATE MATERIAL SET nome_material = ?, desc_material = ?, qtde_estoque = ?, id_prat_fk = ?, id_forn_fk = ? /*imagem = ?*/ WHERE id_material = ?';
		/* AINDA NÃO FUNCIONA O UPDATE DA IMAGEM
		$extensao = strtolower(substr($_FILES['nome_imagem_edit']['name'], -4)); //pega a extensao do arquivo
	    $imagem = $_FILES['nome_imagem_edit']['name'] . $extensao; //define o nome do arquivo
	    $diretorio = "upload/"; //define o diretorio para onde enviaremos o arquivo
		    move_uploaded_file($_FILES['nome_imagem_edit']['tmp_name'], $diretorio.$imagem); //efetua o upload
		*/
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->bindValue(1, $m->getnome_material());
		$stmt->bindValue(2, $m->getdesc_material());
		$stmt->bindValue(3, $m->getqtde_estoque());
		$stmt->bindValue(4, $m->getid_prat_fk());
		$stmt->bindValue(5, $m->getid_forn_fk());
		$stmt->bindValue(6, $m->getid_material());
		//$stmt->bindValue(7, $imagem);
		$stmt->execute();
	}

	public function delete(){
		$id = $_GET['id'];
		$sql = 'DELETE FROM MATERIAL WHERE id_material = ?';
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->bindValue(1, $id);
		$stmt->execute();
	}
	
	public function editar_material(){
		$id = $_GET['id'];
		$sql = 'SELECT * FROM MATERIAL WHERE id_material = ?';
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->bindValue(1, $id);
		$stmt->execute();
		$resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		return $resultado;
	}

	public function armazenar(Material $m){
		$id = $_GET['id'];
		$sql = 'UPDATE MATERIAL SET qtde_estoque = qtde_estoque + ? WHERE id_material = ?';
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->bindValue(1, $m->getqtde_estoque());
		$stmt->bindValue(2, $id);
		$stmt->execute();
	}

	public function devolver(Material $m){
		$sql = 'UPDATE MATERIAL SET qtde_estoque = qtde_estoque + ? WHERE id_material = ?';
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->bindValue(1, $m->getqtde_estoque());
		$stmt->bindValue(2, $m->getid_material());
		$stmt->execute();
	}

	//SUBTRAINDO QUANTIDADES DE MATERIAIS AO FAZER O PEDIDO:
	public function subtrairMaterial(Material $m){
		$sql = 'UPDATE MATERIAL SET qtde_estoque = ? WHERE id_material = ?';
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->bindValue(1, $m->getqtde_estoque());
		$stmt->bindValue(2, $m->getid_material());
		$stmt->execute();
	}

	public function relatorioMateriais(){
		$sql = 'SELECT
		M.id_material, M.nome_material, M.desc_material, M.qtde_estoque, M.imagem,
		F.nome_forn,
		P.nome_prat,
		C.nome_coluna, 
        CO.nome_corredor
        FROM MATERIAL M INNER JOIN FORNECEDOR F
        ON M.id_forn_fk = F.id_forn INNER JOIN PRATELEIRA P
        ON M.id_forn_fk = P.id_prat INNER JOIN COLUNA C
        ON P.id_coluna_fk = C.id_coluna INNER JOIN CORREDOR CO
		ON C.id_corr_fk = CO.id_corr';
		$stmt = Conexao::getConn()->prepare($sql);
		$stmt->execute();
		if($stmt->rowCount() > 0){
			$resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			foreach($resultado as $res){
				echo
					"<img src = upload/" . $res['imagem'] . " class = 'imagem-material'><br>
					ID: " . $res['id_material'] . "<br>" .
					$res['nome_material'] . " - " . $res['desc_material'] . "<br>
					Qtde em estoque: " .	$res['qtde_estoque'] . "<br>
					Fornecedor: " .	$res['nome_forn'] . "<br>
					Prateleira: " . $res['nome_prat'] . "<br>" . 
					$res['nome_coluna'] . "<br>" . 
					$res['nome_corredor'] . "<br>
					<a href = editar.php?id=".$res['id_material'] . ">
					<img src = 'icones/edit-64.png' class = 'icones'>
						</a>
					<a href = excluir.php?id=".$res['id_material'].">
						<img src = 'icones/x-mark-4-64.png' class = 'icones'>
					</a> 
					<a href = form-armazenar.php?id=".$res['id_material'].">
						<img src = 'icones/plus-8-64.png' class = 'icones'>
					</a>
					<br>____________________________<br>";
			}
			return $resultado;
		}else{
			echo "Nenhum registro";
		}
	}
}




 ?>