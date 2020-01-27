<?php
	/*//////////////////////////////////
			ALESSON MARQUES DA SILVA 
			PROJETO API PHP RET JSON
				16/11/2019 - 03:50
	*///////////////////////////////////
	class Funcoes
	{
		/////////////////////////
		function __construct(){}
		/////////////////////////////////////////////////////////////////////////////////////
		/////////////////////////
		public function pega($tabela)
		{
			$sql = $this->select("SELECT ||CAMPOS|| FROM {$tabela}");
			return $this->selecionar($sql);
		}
		public function insere($tabela)
		{
			$sql = $this->insert("INSERT INTO {$tabela} (||CAMPOS||) VALUES (||VALORES||)");
			return $this->executar($sql);
		}
		public function atualiza($tabela)
		{
			$sql = $this->update("UPDATE {$tabela} SET ||AJUSTES||");
			return $this->executar($sql);	
		}
		public function deleta($tabela)
		{
			$sql = $this->update("DELETE FROM {$tabela}");
			return $this->executar($sql);	
		}
		/////////////////////////
		/////////////////////////////////////////////////////////////////////////////////////
		

		/////////////////////////
		private function select($sql)
		{
			$parametros = $this->pegaParametros();
			////////////////////////
			$fld = array();
			foreach($parametros as $key => $value)
			{
				$fld[] = $key;
			}
			$fld = implode(', ', $fld);
			if(!(isset($fld) && !empty($fld)))
				$fld = '*';
			$sql = str_replace(array("||CAMPOS||"), array($fld), $sql);
			return $sql;
		}

		private function insert($sql)
		{
			$parametros = $this->pegaParametros();
			////////////////////////
			$fld = array();
			$vlr = array();
			foreach($parametros as $key => $value)
			{
				$fld[] = $key;
				$vlr[] = $this->trataValor($value);
			}
			$fld = implode(', ', $fld);
			$vlr = implode(', ', $vlr);

			$sql = str_replace(array("||CAMPOS||", "||VALORES||"), array($fld, $vlr), $sql);
			return $sql;
		}

		private function update($sql)
		{
			$parametros = $this->pegaParametros();
			////////////////////////
			$set = array();
			foreach($parametros as $key => $value)
			{
				$set[] = "{$key} = ".$this->trataValor($value);
			}
			$set = implode(', ', $set);

			$sql = str_replace(array("||AJUSTES||"), array($set), $sql);
			return $sql;
		}
		/////////////////////////
		private function pegaParametros()
		{
			$parametros = array();
			$naoEParametro = array('sql');

			foreach($this->variaveis->parametros as $key => $par)
			{
				if(!in_array($key, $naoEParametro))
				$parametros[$key] = $par;
			}
			return $parametros;
		}

		private function trataValor($valor)
		{
			switch(gettype($valor)){
				case 'string':
					return "'{$valor}'";
				break;
				case 'int':
					return $valor;
				break;
				default:
					return "'{$valor}'";
				break;
			}
		}
	}
?>