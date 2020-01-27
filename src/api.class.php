<?php
	/*//////////////////////////////////
			ALESSON MARQUES DA SILVA 
			PROJETO API PHP RET JSON
				16/11/2019 - 03:50
	*///////////////////////////////////
	include 'Connection.class.php';
	include 'Funcoes.class.php';

	class API extends Funcoes
	{
		public $cnn;
		public $log;
		public $token;
		public $variaveis;

		function __construct(){
			$this->cnn = new Connection();
			$this->log = array();
		}

		public function pegarVariaveis()
		{
			switch ($_SERVER['REQUEST_METHOD']) {
				case 'GET':
					$VARS = $_GET;
				break;
				case 'POST':
					$VARS = $_POST;
				break;				
				default:
					return array('erro');
				break;
			}


			$funcao = array_shift($VARS);
			$tabela = array_shift($VARS);

			foreach($VARS as $key => $value)
			{
				switch (substr($key, 0, 1)){
					case '_':
						$sql[substr($key, 1, 999)] = $value;
					break;
					case '@':
						$token['_id'] = $value;
						unset($VARS['@TOKEN']);
					break;

					case '-':
					case '!':
					case '$':
					default:
						$parametros[$key] = $value;
					break;
				}
			}

			$sql = (OBJECT) $sql;
			$token = (OBJECT) $token;

			$parametros['token'] = $token;
			$parametros['sql'] = $sql;

			$parametros = (OBJECT) $parametros;
			$VARS = compact('funcao', 'tabela', 'parametros');
	
			$this->variaveis = (OBJECT) $VARS;
		}

		public function pegaToken()
		{

			if(!(isset($this->token) && !empty($this->token)))
			{
				$this->token = $this->variaveis->parametros->token->_id.'';
				unset($this->variaveis->parametros->token);
			}
			return $this->token;
		}

		public function geraToken($debug = 0)
		{
			$t	= md5("ALESSONMARQUESDASILVA");
			$b 	= array('ano' => (INT) date('Y'), 'mes' => (INT) date('m'), 'dia' => (INT) date('d'), 'hora' => (INT) date('H')/*, 'minuto' => (INT) date('i')*/);
			$c 	= [];
			$u	= [];

			foreach($b as $k => $v)
			{
				switch($k)
				{
					case 'ano':
						$min = 0;
						$max = 2099;
					break;
					case 'mes':
						$min = 1;
						$max = 12;
					break;
					case 'dia':
						$min = 1;
						$max = 31;
					break;
					case 'hora':
						$min = 0;
						$max = 23;
					break;
					/*
					case 'minuto':
						$min = 0;
						$max = 59;
					break;
					*/
				}
				////////////////////////////////////////////
				$b["{$k}_a"] = ($v == $min) ? $max : $v - 1;
				$b["{$k}_b"] = ($v == $max) ? $min : $v + 1;
				////////////////////////////////////////////
				$c[$k] = array(
					$b["{$k}_a"],
					$b["{$k}"  ],
					$b["{$k}_b"]
				);
			}

			foreach($c['ano'] as $a)
			{
				foreach($c['mes'] as $m)
				{
					foreach($c['dia'] as $d)
					{
						foreach($c['hora'] as $h)
						{
							$u 			= md5((STRING) (INT)$a.(INT)$m.(INT)$d.(INT)$h);
							$token[] 	= md5("{$t}{$u}");
						}
					}
				}
			}
			return $token;
		}

		private function blindSqlInjection($sql)
		{
			return addslashes($sql);
		}

		public function preparaSQL($sql)
		{
			$preparacao = isset($this->variaveis->parametros->sql) && !empty($this->variaveis->parametros->sql) ? $this->variaveis->parametros->sql : null;
			if(isset($preparacao) && !empty($preparacao))
			{
				$where = isset($preparacao->where) && !empty($preparacao->where) ? " WHERE ".$preparacao->where : '';
				$order = isset($preparacao->order) && !empty($preparacao->order) ? " ORDER BY ".$preparacao->order : '';
				$limit = isset($preparacao->limit) && !empty($preparacao->limit) ? " LIMIT ".$preparacao->limit : '';
				//$sql = $this->blindSqlInjection("{$sql}{$where}{$order}{$limit}");
				$sql = "{$sql}{$where}{$order}{$limit}";
			}
			
			return $sql;
		}

		public function selecionar($sql)
		{
			return $this->cnn->selecionar($this->preparaSQL($sql));
		}
		
		public function executar($sql)
		{
			return $this->cnn->executar($this->preparaSQL($sql));
		}

		public function criarArquivo($arquivo, $texto, $modo = 'w+')
		{
			$fopen = fopen($arquivo, "{$modo}");
			fwrite($fopen, $texto);
			fclose($fopen);
			shell_exec("chmod 777 {$arquivo};");
		}

		public function registraLog($log)
		{
			$this->log[] = array('data' => date('Y-m-d H:i:s'), 'execucao' => $log);
		}

		public function limpaLog()
		{
			$this->log = array();
		}

		public function mostraLog()
		{
			print_r($this->log);
		}		    
	}
?>