<?php
class Query{
	public $tabla;
	public $total;
	public $datos;
	public $lastQuery;

	function __construct(){
		$this->db = new DB;
	}

	public function query($tabla,$where='',$columnas='',$add=''){
		$tabla = PREFIX.$tabla;
		if($columnas == '') $columnas = '*'; else $columnas = implode(',', $columnas);

		if($where == '') $where = ''; else $where = ' where '.implode(' and ', $where);

		$consulta = $columnas.' from '.$tabla.$where.' '.$add;

		$consulta = $this->db->select($consulta);
		$this->total = count($consulta);
		$this->lastQuery = $this->db->lastQuery;
		return $consulta;
	}
}
?>