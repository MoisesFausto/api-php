<?php
	/*//////////////////////////////////
			ALESSON MARQUES DA SILVA 
			PROJETO API PHP RET JSON
				16/11/2019 - 03:50
	*///////////////////////////////////
	class Connection
	{
		public $cnn;
		public $log;

		function __construct($username = '', $password = '', $host = 'alessonmarques.com.br', $port = '3306', $dbname='banco_de_dados', $drive = 'mysql') 
		{
			$this->log = array();
			if((isset($username) && !empty($username)) && (isset($password) && !empty($password))) 
			{
       			$this->cnn = $this->conectar($username, $password, $host, $port, $dbname, $drive);
			}
   		}

		public function conectar($username, $password, $host = 'alessonmarques.com.br', $port = '3306', $dbname = 'banco_de_dados', $drive = 'mysql')
		{
			try
			{
				$pdo = new PDO("{$drive}:host={$host}:{$port};dbname={$dbname}", 
							    $username, 
							    $password, 
							    array(
							        PDO::ATTR_TIMEOUT => "999",
							        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
							    )
							);
				$this->cnn = $pdo;
				$this->registraLog("Connectou: 'como {$username} em {$dbname} (({$drive}) {$host}:{$port})'");
			}catch(Exception $e){
				$this->registraLog('Algo deu errado com a conexão: {$e}');
			}
		}

		public function selecionar($sql)
		{
			//echo "\n".$sql;
			if(isset($this->cnn) && !empty($this->cnn))
				try
				{
					$stmt = $this->cnn->prepare($sql);
					$stmt->execute();
					$rs = $stmt->fetchAll(PDO::FETCH_OBJ);

					$this->registraLog("Executou: '{$sql}'");
					return $rs;
				}catch(Exception  $e){
					$this->erro = $e;
					$this->registraLog("Erro: '{$e}'");
					return array('erro' => json_encode($e));
				}
			else
			{
				$this->registraLog('Você precisa se conectar antes de usar a função '.__FUNCTION__);
			}

		}

		public function executar($sql)
		{
			//echo "\n".$sql;
			if(isset($this->cnn) && !empty($this->cnn))
				try
				{
					$stmt = $this->cnn->prepare($sql);
					$stmt->execute();

					$this->registraLog("Executou: '{$sql}'");
					return 1;
				}catch(Exception  $e){
					$this->erro = $e;
					$this->registraLog("Erro: '{$e}'");
					return 0;
				}
			else
			{
				$this->registraLog('Você precisa se conectar antes de usar a função '.__FUNCTION__);
			}
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