<?php
class Producto
{
//--------------------------------------------------------------------------------//
//--ATRIBUTOS
	private $codBarra;
 	private $nombre;
  	private $pathFoto;
//--------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------//
//--GETTERS Y SETTERS
	public function GetCodBarra()
	{
		return $this->codBarra;
	}
	public function GetNombre()
	{
		return $this->nombre;
	}
	public function GetPathFoto()
	{
		return $this->pathFoto;
	}

	public function SetCodBarra($valor)
	{
		$this->codBarra = $valor;
	}
	public function SetNombre($valor)
	{
		$this->nombre = $valor;
	}
	public function SetPathFoto($valor)
	{
		$this->pathFoto = $valor;
	}

//--------------------------------------------------------------------------------//
//--CONSTRUCTOR
	public function __construct($codBarra=NULL, $nombre=NULL, $pathFoto=NULL)
	{
		if($codBarra !== NULL && $nombre !== NULL && $pathFoto !== NULL){
			$this->codBarra = $codBarra;
			$this->nombre = $nombre;
			$this->pathFoto = $pathFoto;
		}
	}

//--------------------------------------------------------------------------------//
//--TOSTRING	
  	public function ToString()
	{
	  	return $this->codBarra." - ".$this->nombre." - ".$this->pathFoto."\r\n";
	}
//--------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------//
//--METODOS DE CLASE
	public static function Guardar($obj)
	{
		$resultado = FALSE;
		
		//ABRO EL ARCHIVO
		$ar = fopen("archivos/productos.txt", "a");
		
		//ESCRIBO EN EL ARCHIVO
		$cant = fwrite($ar, $obj->ToString());
		
		if($cant > 0)
		{
			$resultado = TRUE;			
		}
		//CIERRO EL ARCHIVO
		fclose($ar);
		
		return $resultado;
		
	}
	public static function GuardarBaseDatos($obj)
	{	
		$valor = true;
		try
		{
			$Pdo = new PDO("mysql:host=localhost;dbname=productos","root","");
			$PdoST = $Pdo->prepare("INSERT INTO producto(codigo_barra,nombre,path_foto) VALUES(:codigo,:nombre,:foto)");
			$PdoST->bindParam(":codigo",$obj->GetCodBarra());
			$PdoST->bindParam(":nombre",$obj->GetNombre()); 
			$PdoST->bindParam(":foto",$obj->GetPathFoto());
			$PdoST->execute();
			
		}
		catch(Exception $e)
		{
			$valor = false;
			echo $e->getMessage();
		}
		return $valor;
		
	}
	public static function BorrarBaseDatos($obj)
	{	
		$valor = true;
		try
		{
			$Pdo = new PDO("mysql:host=localhost;dbname=productos","root","");
			$PdoST = $Pdo->prepare("DELETE FROM producto WHERE  codigo_barra = :codigo");
			$PdoST->bindParam(":codigo",$obj->GetCodBarra());
			$PdoST->execute();
		}
		catch(Exception $e)
		{
			$valor = false;
			echo $e->getMessage();
		}
		return $valor;
	}
	public static function ModificarBaseDatos($obj)
	{	
		$valor = true;
		try
		{
			$Pdo = new PDO("mysql:host=localhost;dbname=productos","root","");
			$PdoST = $Pdo->prepare("UPDATE producto SET nombre=:nombre,path_foto=:foto WHERE codigo_barra=:codigo");
			$PdoST->bindParam(":codigo",$obj->GetCodBarra());
			$PdoST->bindParam(":nombre",$obj->GetNombre()); 
			$PdoST->bindParam(":foto",$obj->GetPathFoto());
			$PdoST->execute();
		}
		catch(Exception $e)
		{
			$valor = false;
			echo $e->getMessage();
		}
		return $valor;
	}
	public static function TraerTodosLosProductos()
	{

		$ListaDeProductosLeidos = array();

		//leo todos los productos del archivo
		$archivo=fopen("archivos/productos.txt", "r");
		
		while(!feof($archivo))
		{
			$archAux = fgets($archivo);
			$productos = explode(" - ", $archAux);
			//http://www.w3schools.com/php/func_string_explode.asp
			$productos[0] = trim($productos[0]);
			if($productos[0] != ""){
				$ListaDeProductosLeidos[] = new Producto($productos[0], $productos[1],$productos[2]);
			}
		}
		fclose($archivo);
		var_dump($ListaDeProductosLeidos);
		die();
		return $ListaDeProductosLeidos;
		
	}
	public static function TraerTodosLosProductosBaseDatos()
	{
		$Pdo = new PDO("mysql:host=localhost;dbname=productos","root","");

		//$sql = $Pdo->query("SELECT * FROM producto WHERE 1");
		$PdoST = $Pdo->prepare("SELECT * FROM producto WHERE 1");

    	//$registros = $sql->fetchall(PDO::FETCH_ASSOC);
		$PdoST->execute();
		foreach($PdoST /*$registros*/ as $registro) //devuelve los valores de la base fila por fila
		{	
			$ListaDeProductosLeidos[] = new Producto($registro['codigo_barra'],$registro['nombre'],$registro['path_foto']);
		}
		return $ListaDeProductosLeidos;
	}

	public static function ObtenerIndice($array,$codigo)
	{	
		
		foreach($array as $valor)
		{
			if($valor->GetCodBarra() == $codigo)
			{
				$numero = array_search($valor,$array);
				break;
			}
			$numero = -1;
		}
		return $numero;
	}
//--------------------------------------------------------------------------------//
}