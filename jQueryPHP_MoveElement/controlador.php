<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') && isset($_POST['accion'])){   	
    $link = mysqli_connect('servidor', 'usuario', 'contraseña', 'bdatos');
	
	$devolver = null;
	$consulta = '';
	$accion = $_POST['accion'];
	switch($accion){
		case 'insertar':{ // Inserción de un nuevo elemento
			$nombre = mysqli_real_escape_string($link, $_POST['nombre']);
			$orden = mysqli_real_escape_string($link, $_POST['orden']);
			$consulta = "INSERT INTO elementos (nombre, orden) VALUES ('".$nombre."', ".$orden.") ";
			if (mysqli_query($link, $consulta)){
				$devolver = array ('valor' => mysqli_insert_id($link));
			}
			break;
		}
		case 'eliminar':{ // Eliminación de un nuevo elemento
			$id = mysqli_real_escape_string($link, $_POST['id']);
            $orden = mysqli_real_escape_string($link, $_POST['orden']);

			$consulta = 'DELETE FROM elementos WHERE id = '.$id;
			if (mysqli_query($link, $consulta)){
                $consulta = "UPDATE elementos SET orden = orden -1 WHERE orden > ".$orden;
                mysqli_query($link,$consulta);
				$devolver = array ('realizado' => true);
			}			
			break;
		}
		case 'editar':{ // Edición de un elemento
			$id = mysqli_real_escape_string($link, $_POST['id']);
			$nombre = mysqli_real_escape_string($link, $_POST['nombre']);
			$consulta = "UPDATE elementos SET nombre = '".$nombre."' WHERE id = ".$id;
			if (mysqli_query($link, $consulta)){
				$devolver = array ('realizado' => true);
			}
			break;
		}
		case 'ordenar':{ // Ordenar los elementos
			$puntos = explode(',',$_POST['puntos']);
            $consulta = 'UPDATE elementos SET orden = CASE id '.PHP_EOL;
        	foreach ($puntos as $index => $id){
            	$idPunto = explode('-', $id);
            	$idPunto = mysqli_real_escape_string($link,$idPunto[1]);
            	$orden = mysqli_real_escape_string($link, ($index + 1));
                $consulta .= 'WHEN '.$idPunto.' THEN '.$orden.''.PHP_EOL;
        	}
            $consulta .= 'ELSE orden'.PHP_EOL.'END';
            echo $consulta;
        	if (mysqli_query($link, $consulta)){
				$devolver = array ('realizado' => true);
			}			
			break;
		}
	}
	if ($devolver)
		echo json_encode($devolver);
}
else {
	die('No se está accediendo correctamente');
}
?>