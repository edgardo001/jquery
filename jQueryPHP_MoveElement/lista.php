<?php
	$link = mysqli_connect('servidor', 'usuario', 'contraseña', 'bdatos');
	$consulta = "SELECT * FROM elementos ORDER BY orden";
	$resultado = mysqli_query($link, $consulta);
	$elementos = null;
	while ($datos = mysqli_fetch_assoc($resultado)){
		$elementos[$datos['id']] = $datos['nombre'];
	}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Lista editable y ordenable</title>
    <meta charset="utf-8">
    <link href='http://fonts.googleapis.com/css?family=Lilita+One' rel='stylesheet' type='text/css'>
    <link href="pagina.css" rel="stylesheet" type="text/css" media="all">    
</head>
<body>
	<div id="wrapper">
		<h1>Lista de tareas</h1>
		<ul id="lista">
			<?php 
				foreach ($elementos as $id => $nombre)
					echo '<li id="elemento-'.$id.'" contenteditable="true">'.$nombre.'</li>';
			?>
		</ul>
		<div id="form">
			<input type="radio" name="editar-ordenar" id="editar1" value="editar" checked="checked"/>
            <label for="editar1">Editar</label>
            <input type="radio" name="editar-ordenar" value="ordenar" id="ordenar1"/>
            <label for="ordenar1">Ordenar</label>
			<form id="formulario" method="post">
				<input type="text" id="campo-nombre" name="nombre" placeholder="Nuevo elemento">
				<input type="submit" value="Añadir">
			</form>			
		</div>
		
	</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
	<script>
		$(function(){
			var formulario = $('#formulario'), ordenando = false, lista = $('#lista'),
                    elementos = lista.find('li');
			lista.sortable({
                update: function(event,ui){
                    var ordenPuntos = $(this).sortable('toArray').toString();
                    $.ajax({
                        type: 'POST',
                        url: 'controlador.php',
                        dataType: 'json',
                        data: {
                            accion: 'ordenar',
                            puntos: ordenPuntos
                        }
                    });
                }
            });
            lista.sortable('disable');
            $('input[name="editar-ordenar"]').on('change', function(){
                if ($(this).val() == 'ordenar'){
                    lista.sortable('enable');
                    elementos.attr('contenteditable',false);
                    ordenando = true;
                }
                else{
                    lista.sortable('disable');
                    elementos.attr('contenteditable',true);
                    ordenando = false;
                }
            });


			formulario.on('submit',function(evento){ //Cuando el formulario se envía, vamos a insertar
				evento.preventDefault();
				var nombre = $('#campo-nombre').val();
				$('#campo-nombre').val('');
				
				$.ajax({
                    type: 'POST',
                    url: 'controlador.php',
                    dataType: 'json',
                    data: {
                        accion: 'insertar',
                        nombre: nombre,
                        orden: elementos.length + 1 // El orden es el número de elementos + 1
                    },
                    success: function (devolver){
                    	if (devolver.valor){
                    		$('<li>',{
                    			id : 'elemento-' + devolver.valor,
                    			'class': ordenando ? 'ordenable' : '',
                    			text: nombre,
                    			'contenteditable' : !ordenando
                    		}).hide().appendTo($('#lista')).fadeIn('slow');
                    	}
                    }
                });
            });
            lista.on('keydown', 'li', function(evento){
                var punto = $(this);
                var idPunto = punto.attr('id').split('-');
                idPunto = idPunto[1];

                switch(evento.keyCode){
                    case 27:{ //Escape
                        document.execCommand('undo');
                        punto.blur();
                        break;
                    }
                    case 46:{ //Suprimir
                        if (confirm('¿Seguro que quiere eliminar este elemento?')){
                            $.ajax({
                                type: 'POST',
                                data: {
                                    accion: 'eliminar',
                                    orden: punto.index(),
                                    id: idPunto
                                },
                                url: 'controlador.php',
                                success: function(e){
                                    punto.fadeOut('slow').remove();
                                }
                            });
                        }
                        break;
                    }
                    case 13:{ //Enter
                        evento.preventDefault();
                        var texto = punto.text();
                        punto.blur();
                        $.ajax({
                            type: 'POST',
                            data: {
                                accion: 'editar',
                                id: idPunto,
                                nombre: texto
                            },
                            url: 'controlador.php'
                        });
                        break;
                    }
                }
            });
		});
	</script>
</body>
</html>