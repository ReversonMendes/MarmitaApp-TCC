<?php

function listaUsuarios($conexao) {
	$usuarios = array();
	$resultado = mysqli_query($conexao, "select * from usuario");
	while($usuario = mysqli_fetch_assoc($resultado)) {
		array_push($usuarios, $usuario);
	}
	return $usuarios;
}

function insereUsuario($conexao, $nome, $usuario, $senha, $email, $ativado) {
	$query = "insert into usuario (nome, usuario, senha, email, flaginativo) values ('{$nome}', '{$usuario}', '{$senha}','{$email}',{$ativado})";
	return mysqli_query($conexao, $query);
}

function alteraConta($conexao, $id, $usuario, $novasenha) {
	$query = "update usuario set usuario= '{$usuario}', senha = '{$novasenha}' where idusuario = '{$id}'";
	return mysqli_query($conexao, $query);
}


function buscaUsuarios($conexao, $id) {
	$query = "select * from usuario where idusuario = {$id}";
	$resultado = mysqli_query($conexao, $query) or die(mysql_error());
	return mysqli_fetch_assoc($resultado);
}

function validaUsuario($conexao, $usuario, $email) {
	$query = "select * from usuario where usuario = '{$usuario}' or email = '{$email}'";
	$resultado = mysqli_query($conexao, $query);
	return mysqli_fetch_assoc($resultado);
}

function removeUsuario($conexao, $id) {
	$query = "delete from usuarios where idusuario = {$id}";
	return mysqli_query($conexao, $query);
}
