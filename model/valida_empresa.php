<?php
include_once($_SERVER['DOCUMENT_ROOT']."/controller/conecta.php");
require_once($_SERVER['DOCUMENT_ROOT']."/controller/funcoes_login.php");

require_once($_SERVER['DOCUMENT_ROOT']."/controller/funcoes_empresa.php");

//Dados do form
$idempresa = isset($_POST['idempresa']) ? $_POST['idempresa'] : '';
$razao = isset($_POST['razao']) ? $_POST['razao'] : '';
$fantasia = isset($_POST['fantasia']) ? $_POST['fantasia'] : '';
$cnpj = isset($_POST['cnpj']) ? $_POST['cnpj'] : '';
$endereco = isset($_POST['endereco']) ? $_POST['endereco'] : '';
$numero = isset($_POST['numero']) ? $_POST['numero'] : '';
$cep = isset($_POST['cep']) ? $_POST['cep'] : '';
$telefone = isset($_POST['telefone']) ? $_POST['telefone'] : '';
$email =isset($_POST['email']) ? $_POST['email'] : '';
//busca id do usuário
$idusuario = buscaIdUsuario($conexao,usuarioLogado());

// //Tem que validar se foi informado algum logo, vai ser alterado só a foto
if(isset($_FILES['logo']) || !empty($_FILES['logo'])) {
	// Lista de tipos de arquivos permitidos
	$tiposPermitidos= array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png');
	// Tamanho máximo (em bytes)
	$tamanhoPermitido = 1024 * 500; // 500 Kb
	// O nome original do arquivo no computador do usuário
	$arqName = $_FILES['logo']['name'];
	// O tipo mime do arquivo. Um exemplo pode ser "image/gif"
	$arqType = $_FILES['logo']['type'];
	// O tamanho, em bytes, do arquivo
	$arqSize = $_FILES['logo']['size'];
	// O nome temporário do arquivo, como foi guardado no servidor
	$arqTemp = $_FILES['logo']['tmp_name'];
	// O código de erro associado a este upload de arquivo
	$arqError = $_FILES['logo']['error'];


	if ($arqError == 0) {
	    // Verifica o tipo de arquivo enviado
		if (array_search($arqType, $tiposPermitidos) === false) {
			$_SESSION["Danger"] = "O tipo de arquivo enviado é inválido!:";
			header("Location: ../view/perfil.php");
		// Verifica o tamanho do arquivo enviado
		} else if ($arqSize > $tamanhoPermitido) {
			$_SESSION["Danger"] = "O tamanho do arquivo enviado é maior que o limite!";
			header("Location: ../view/perfil.php");
		// Não houveram erros, move o arquivo
		} else {
			  $pasta = '../img/uploads/empresas_logos/';
			  // Pega a extensão do arquivo enviado
			  $extensao = strtolower(end(explode('.', $arqName)));
			  // Define o novo nome do arquivo usando um UNIX TIMESTAMP
			  $logo = $idempresa . '.' . $extensao;
			  $upload = move_uploaded_file($arqTemp, $pasta . $logo);
			  
			  // Verifica se o arquivo foi movido com sucesso
			  //Grava no banco o nome do arquivo da logo do usuário
			if ($upload == true) {
			  	if(alteraLogo($conexao, $idempresa, $logo)) {
					 $_SESSION["Success"] = "logo da sua empresa alterado com sucesso!";
					 header("Location: ../view/perfil.php");
					 die();
				} else {
					$erro = mysqli_error($conexao);
					$_SESSION["Danger"] = "Os dados não foram alterado. erro:".$erro;
					header("Location: ../view/perfil.php");
					die();
				}
			}else{
					$_SESSION["Danger"] = "Ocorreu algum erro com o upload, por favor tente novamente!";
					header("Location: ../view/perfil.php");
					die();
	    	}
		}		
	}else{
		$_SESSION["Danger"] = "Ocorreu algum erro com o upload, por favor tente novamente!";
		header("Location: ../view/perfil.php");
		die();
	}
}

//Validações Obrigatórias
//se for maior vai alterar a empresa
if($idempresa > 0) {
	//Altera a empresa
	if( alteraEmpresa($conexao, $razao, $fantasia, $cnpj, $endereco, $numero, $cep, $telefone,$email,$idempresa)) {
		$_SESSION["Success"] = "Dados da sua empresa alterado com sucesso!";
			header("Location: ../view/perfil.php");
	}else {
		$erro = mysqli_error($conexao);
		$_SESSION["Danger"] = "Houve um erro ao gravar os dados da sua empresa. erro:".$erro;
		header("Location: ../view/perfil.php");
	};
}else{

	//Insere nova empresa
	if( insereEmpresa($conexao, $razao, $fantasia, $cnpj, $endereco, $numero, $cep, $telefone,$email,$idusuario["idusuario"])) {
		$_SESSION["Success"] = "Dados da sua empresa gravado com sucesso!";
			header("Location: ../view/painel.php");
	}else {
		$erro = mysqli_error($conexao);
		$_SESSION["Danger"] = "Houve um erro ao gravar os dados da sua empresa. erro:".$erro;
		header("Location: ../view/empresa.php");
	};
};
die();
?>
