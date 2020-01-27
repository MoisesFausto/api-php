<?php
	/////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////
	/*//////////////////////////////////
			ALESSON MARQUES DA SILVA 
			PROJETO API PHP RET JSON
				16/11/2019 - 03:50
	*///////////////////////////////////
	/////////////////////////////////////////////////////////
	include_once('src/api.class.php');
	/////////////////////////////////////////////////////////
	header("Access-Control-Allow-Origin: *");
	header('Content-Type: application/json; charset=utf-8');
	header("Cache-Control: no-cache, no-store, must-revalidate");
	/////////////////////////////////////////////////////////
	$api = new api();
	$api->pegarVariaveis();
	////////////////////////////
	$token	= $api->geraToken();
	$_token	= $api->pegaToken();
	////////////////////////////
	// TOKEN TEM QUE SER PASSADO POR ÚLTIMO.
	/////////////////////////////////////////////////////////
	
	if(in_array($_token, $token))
	{
		/////////////////////////////////////////////////////////
		$api->cnn->conectar('usuario', 'senha');
		////////////////////////////////////////
		if(isset($api->variaveis->funcao) && !empty($api->variaveis->funcao))
		{
			$param = isset($api->variaveis->tabela) && !empty($api->variaveis->tabela) ? $api->variaveis->tabela : '';
			$rs = $api->{$api->variaveis->funcao}(isset($param) && !empty($param) ? $param : null);
			$retorno = array(	"docs" => ( $rs ? $rs : $api->cnn->erro->errorInfo[2]),
								"total" => count($rs)
			);
			//print_r($api);
		}
		else
		{
			$retorno = array("docs" => array("mensagem" => "Conexao Ok!"));
		}
		echo json_encode($retorno);
		/////////////////////////////////////////////////////////
	}
	else
	{
		///////////////////////
		//////////////////////////////////////////////////////////////////////////
		echo json_encode(array('docs' => array("mensagem" => "Hi there! # _ #")));
		//////////////////////////////////////////////////////////////////////////
		$catch = json_encode(array('No modo:' => array($_SERVER['HTTP_SEC_FETCH_MODE'], $_SERVER['HTTP_SEC_FETCH_SITE'], $_SERVER['HTTP_SEC_FETCH_USER']), 
									'usando' => $_SERVER['HTTP_USER_AGENT'],  
									'tentou_executar' => $_SERVER['QUERY_STRING'], 
									'de' => "{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']}"));
		$fopen = fopen('/home2/alesso10/public_html/api/log/catch_hacking.txt', 'a+');
		fwrite($fopen, "$catch \n");
		fclose($fopen);
		//////////////////////////////////////////////////////////////////////////
		///////////////////////
	}
	/////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////
?>