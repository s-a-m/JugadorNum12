<?php
/* @var $accionGrupal */
/* @var habilidad */

// codigo PHP
?>

<h1>Accion Grupal: <?php echo $habilidad[0]['nombre']; ?></h1>

<p> <?php echo "<b>RECURSOS AÑADIDOS => </b>"; ?>
			<?php printf('Dinero:%d', $accionGrupal['dinero_acc']); ?>
			&nbsp;
			<?php printf('Influencias:%d', $accionGrupal['influencias_acc']); ?>
			&nbsp;
			<?php printf('Animo:%d', $accionGrupal['animo_acc']); ?>
</p>

<p> <?php echo "<b>JUGADORES AÑADIDOS => </b>"; ?>
			<?php echo $accionGrupal['jugadores_acc']; ?>
</p>

<p> <?php echo "<b>EFECTO CONSEGUIDO => </b>"; ?>
			<?php echo $habilidad[0]['descripcion']; ?>
</p>

<p>
<?php 
$usuario = Yii::app()->user->usIdent;
$propietarioAccion = $accionGrupal['usuarios_id_usuario'];
if ($usuario == $propietarioAccion){ ?>
	<a href="<?php echo $this->createUrl(/*Aquí falta añadir la url para expulsar jugadores*/);<?>">
	<?php echo "Expulsar participantes";?> </a>
<?php } ?>
</p>