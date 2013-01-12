<?php
/* @var $modeloU */
/* @var $accionesPas */

// codigo PHP 

?>

<!-- codigo HTML -->

<h1> PERFIL DE USUARIO</h1>

<h3> DATOS BÁSICOS</h3>

<table cellspacing="5px">
	<tr>
		<th>Nick</th>
		<th>eMail</th>
		<th>Personaje</th>
		<th>Nivel</th>
		<th>Equipo</th>
	</tr> 
	<tr>
		<td align="center"><?php echo $modeloU->nick ?></td>
		<td align="center"><?php echo $modeloU->email ?></td>
		<td align="center"><?php switch ($modeloU->personaje)
								{
								case Usuarios::PERSONAJE_ULTRA:
								  echo "Ultra" ;
								  break;
								case Usuarios::PERSONAJE_MOVEDORA:
								  echo "Relaciones públicas";
								  break;
								case Usuarios::PERSONAJE_EMPRESARIO:
								  echo "Empresario";
								  break;
								} ?></td>
		<td align="center"><?php echo $modeloU->nivel ?></td>
		<td align="center"><?php echo $modeloU->equipos->nombre ?></td>
	</tr>
	<tr></tr>
</table>

<h3> RECURSOS </h3>

<table cellspacing="5px">
	<tr>
		<th>Dinero</th>
		<th>Influencia</th>
		<th>Ánimo</th>
	</tr> 
	<tr>
		<td align="center"><?php echo $modeloU->recursos->dinero ?></td>
		<td align="center"><?php echo $modeloU->recursos->influencias ?></td>
		<td align="center"><?php echo $modeloU->recursos->animo ?></td>
	</tr>
	<tr></tr>
</table>

<h3> GENERACIÓN DE RECURSOS </h3>

<table cellspacing="5px">
	<tr>
		<th align="center">Generación de dinero</th>
		<th align="center">Influencias máximas</th>
		<th align="center">Generación de influencias</th>
		<th align="center">Ánimo máximo</th>
		<th align="center">Generación de ánimo</th>
	</tr> 
	<tr>
		<td align="center"><?php echo $modeloU->recursos->dinero_gen ?></td>
		<td align="center"><?php echo $modeloU->recursos->influencias_max ?></td>
		<td align="center"><?php echo $modeloU->recursos->influencias_gen ?></td>
		<td align="center"><?php echo $modeloU->recursos->animo_max ?></td>
		<td align="center"><?php echo $modeloU->recursos->animo_gen ?></td>
	</tr>
</table>

<h3> HABILIDADES PASIVAS DESBLOQUEADAS </h3>

<?php foreach ( $accionesPas as $accion ){ ?>
	<li>
    <?php echo $accion; ?>
    </li>
<?php } ?>

