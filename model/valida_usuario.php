<?php
include($_SERVER['DOCUMENT_ROOT']."/controller/conecta.php");
include($_SERVER['DOCUMENT_ROOT']."/controller/funcoes_login.php");

require_once($_SERVER['DOCUMENT_ROOT']."/controller/funcoes_usuario.php");

//verifica se tá logado
verificaUsuario();
$usuariologado = buscaIdUsuario($conexao,usuarioLogado());

$id = $_POST['id'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$datanascimento = $_POST['datanascimento'];

//formata a data
$datanascimento = date("Y-m-d",strtotime(str_replace('/','-',$datanascimento))); 

//Tem que validar se foi informado algum arquivo
	// Lista de tipos de arquivos permitidos
	$tiposPermitidos= array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png');
	// Tamanho máximo (em bytes)
	$tamanhoPermitido = 1024 * 500; // 500 Kb
	// O nome original do arquivo no computador do usuário
	$arqName = $_FILES['arquivo']['name'];
	// O tipo mime do arquivo. Um exemplo pode ser "image/gif"
	$arqType = $_FILES['arquivo']['type'];
	// O tamanho, em bytes, do arquivo
	$arqSize = $_FILES['arquivo']['size'];
	// O nome temporário do arquivo, como foi guardado no servidor
	$arqTemp = $_FILES['arquivo']['tmp_name'];
	// O código de erro associado a este upload de arquivo
	$arqError = $_FILES['arquivo']['error'];


	if ($arqError == 0) {
	    // Verifica o tipo de arquivo enviado
		if (array_search($arqType, $tiposPermitidos) === false) {
			$_SESSION["Danger"] = "O tipo de arquivo enviado é inválido!";
			header("Location: ../view/perfil.php");
		// Verifica o tamanho do arquivo enviado
		} else if ($arqSize > $tamanhoPermitido) {
			$_SESSION["Danger"] = "O tamanho do arquivo enviado é maior que o limite!";
			header("Location: ../view/perfil.php");
		// Não houveram erros, move o arquivo
		} else {
			  $pasta = '../img/uploads/profile/';
			  // Pega a extensão do arquivo enviado
			  $extensao = strtolower(end(explode('.', $arqName)));
			  // Define o novo nome do arquivo usando um UNIX TIMESTAMP
			  $foto = $id . '.' . $extensao;
			  $upload = move_uploaded_file($arqTemp, $pasta . $foto);
			  // Verifica se o arquivo foi movido com sucesso
			  //Grava no banco o nome do arquivo da foto do usuário
			if ($upload == true) {
			  	if(alteraFoto($conexao, $id, $foto)) {
					$_SESSION["Success"] = "Dados alterado com sucesso!";
					header("Location: ../view/perfil.php");
				} else {
					$erro = mysqli_error($conexao);
					$_SESSION["Danger"] = "Os dados não foram alterado. erro:".$erro;
					header("Location: ../view/perfil.php");
				}
			}else{
					$_SESSION["Danger"] = "Ocorreu algum erro com o upload, por favor tente novamente!";
					header("Location: ../view/perfil.php");
	    	}
		}		
	}else{
		$_SESSION["Danger"] = "Ocorreu algum erro com o upload, por favor tente novamente!";
		header("Location: ../view/perfil.php");
	}


if($id > 0){
	if(alteraUsuario($conexao, $id, $nome,$datanascimento, $email)) {
		$_SESSION["Success"] = "Dados alterado com sucesso!";
		header("Location: ../view/perfil.php");
	}
	 else {
		$erro = mysqli_error($conexao);
		$_SESSION["Danger"] = "Os dados não foram alterado. erro:".$erro;
		header("Location: ../view/perfil.php");
	}
}
// }else{
// 		if(sizeof(validaUsuario($conexao, $usuario, $email)) > 0 ) {
// 			$_SESSION["Danger"] = "Já existe um usuário cadastrado com esse email ou usuario. Por favor informe outro nome.";
// 			header("Location: ../view/perfil.php");
// 		}else {
// 			if(insereUsuario($conexao, $nome, $usuario, $senhacripto, $datanascimento, $email, $ativado, 1)) {
// 				$_SESSION["Success"] = "Usuário gravado com sucesso!";
// 				
// 			} else {
// 				$erro = mysqli_error($conexao);
// 				$_SESSION["Danger"] = "Usuário não foi gravado. erro:".$erro;
// 				header("Location: ../view/perfil.php");
// 			}
// 		}
// }
?>
