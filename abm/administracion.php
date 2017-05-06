<?php
require_once ("clases/producto.php");

if(isset($_POST["guardar"])) 
{

	//INDICO CUAL SERA EL DESTINO DEL ARCHIVO SUBIDO
	$destino = "archivos/" . $_FILES["archivo"]["name"];

	$uploadOk = TRUE;

	$tipoArchivo = pathinfo($destino, PATHINFO_EXTENSION);

	//VERIFICO QUE EL ARCHIVO NO EXISTA
	if (file_exists($destino)) 
	{
		$uploadOk = FALSE;
		$mensaje = "El archivo ya existe. Verifique!!!";
		include("mensaje.php");
	}
	//VERIFICO EL TAMA�O MAXIMO QUE PERMITO SUBIR
	if ($_FILES["archivo"]["size"] > 500000) 
	{
		$uploadOk = FALSE;
		$mensaje = "El archivo es demasiado grande. Verifique!!!";
		include("mensaje.php");
	}

	//OBTIENE EL TAMA�O DE UNA IMAGEN, SI EL ARCHIVO NO ES UNA
	//IMAGEN, RETORNA FALSE
	$esImagen = getimagesize($_FILES["archivo"]["tmp_name"]);

	if($esImagen === FALSE) 
	{//NO ES UNA IMAGEN
		$uploadOk = FALSE;
		$mensaje = "S&oacute;lo son permitidas IMAGENES.";
		include("mensaje.php");
	}
	else {// ES UNA IMAGEN

		//SOLO PERMITO CIERTAS EXTENSIONES
		if($tipoArchivo != "jpg" && $tipoArchivo != "jpeg" && $tipoArchivo != "gif"
			&& $tipoArchivo != "png") 
		{
			$uploadOk = FALSE;
			$mensaje = "S&oacute;lo son permitidas imagenes con extensi&oacute;n JPG, JPEG, PNG o GIF.";
			include("mensaje.php");
		}
	}

	//VERIFICO SI HUBO ALGUN ERROR, CHEQUEANDO $uploadOk
	if ($uploadOk === FALSE) 
	{

		echo "<br/><br/>NO SE PUDO SUBIR EL ARCHIVO.";
	} 
	else 
	{
		//MUEVO EL ARCHIVO DEL TEMPORAL AL DESTINO FINAL
		if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $destino)) 
		{

			$p = new Producto($_POST["codBarra"],$_POST["nombre"],basename($_FILES["archivo"]["name"]));
			
			/*if(!Producto::Guardar($p))
			{
				$mensaje = "Lamentablemente ocurrio un error y no se pudo escribir en el archivo.";
				include("mensaje.php");
			}
			else
			{
				$mensaje = "El archivo fue escrito correctamente. PRODUCTO agregado CORRECTAMENTE!!!";
				include("mensaje.php");
			}*/
			if(!Producto::GuardarBaseDatos($p))
			{
				$mensaje = "Lamentablemente ocurrio un error y no se pudo escribir en el archivo.";
				include("mensaje.php");
			}
			else
			{
				$mensaje = "El archivo fue subido a la base de datos correctamente. PRODUCTO agregado CORRECTAMENTE!!!";
				include("mensaje.php");
			}

		} 
		else 
		{
			$mensaje = "Lamentablemente ocurri&oacute; un error y no se pudo subir el archivo.";
			include("mensaje.php");
		}
	}
}

if(isset($_POST["eliminar"])) 
{
	$ArrayDeProductos = Producto::TraerTodosLosProductosBaseDatos();
	$indice = Producto:: ObtenerIndice($ArrayDeProductos,$_POST["codBarra"]);
	if($indice == -1)
	{
		$mensaje = "El codigo de barra que ingreso no se encuentra";
		include("mensaje.php");
	}
	else
	{	
		if(!Producto::BorrarBaseDatos($ArrayDeProductos[$indice]))
		{
			$mensaje = "Lamentablemente ocurrio un error y no se pudo borrar el archivo.";
			include("mensaje.php");
		}
		else
		{
			unlink("archivos/".$ArrayDeProductos[$indice]->GetPathFoto());
			$mensaje = "El archivo fue borrado la base de datos correctamente. PRODUCTO borrado CORRECTAMENTE!!!";
			include("mensaje.php");
		}	
	}
}

if(isset($_POST["modificar"])) 
{
	$ArrayDeProductos = Producto::TraerTodosLosProductosBaseDatos();
	$indice = Producto:: ObtenerIndice($ArrayDeProductos,$_POST["codBarra"]);
	if($indice == -1)
	{
		$mensaje = "El codigo de barra que ingreso no se encuentra";
		include("mensaje.php");
	}
	else
	{	
		if (move_uploaded_file($_FILES["archivo"]["tmp_name"], "archivos/".$_FILES["archivo"]["name"]))
		{	
			$PRODUCTO = new Producto($_POST["codBarra"],$_POST["nombre"],basename($_FILES["archivo"]["name"]));
			if(!Producto::ModificarBaseDatos($PRODUCTO))
			{
				$mensaje = "Lamentablemente ocurrio un error y no se pudo borrar el archivo.";
				include("mensaje.php");
			}
			else
			{
				unlink("archivos/".$ArrayDeProductos[$indice]->GetPathFoto());
				$mensaje = "El archivo fue modificado la base de datos correctamente. PRODUCTO modificado CORRECTAMENTE!!!";
				include("mensaje.php");
			}
		}
		else 
		{
			$mensaje = "Lamentablemente ocurri&oacute; un error y no se pudo subir el archivo.";
			include("mensaje.php");
		}	
	}
}
?>