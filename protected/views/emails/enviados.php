<?php
	$form = $this->beginWidget('CActiveForm', array(
				'id'=>'emails-form',
			    'enableAjaxValidation'=>false,
			    'enableClientValidation'=>true,
			    'clientOptions'=>array(
					'validateOnSubmit'=>true,),
			    ));
 ?>

<h1> Bandeja de Salida</h1>

<table class="bandejas-mensajeria">
	<tr> <th>Enviado a </th> <th> Asunto </th> <th> Fecha </th> <th> &nbsp; </th> <th> &nbsp; </th> </tr> </table>

<table  class="bandejas-mensajeria">
<?php foreach ( $emails as $i=>$email ){ ?>
	
	<tr> 
		<td> <p><?php echo $niks[$i]; ?></p> </td> 
		<td> <p><?php echo $email['asunto']; ?></p> </td> 
		<td> <p><?php echo Yii::app()->dateFormatter->formatDateTime($email['fecha'], 'medium', 'short'); ?></p> </td>  
		<td> <?php echo CHtml::button('Leer', array('submit' => array('emails/leerEmail', 'id'=>$email['id_email']),'class'=>"button small black")); ?> </td> 
		<td> <?php echo CHtml::button('Borrar', array('submit' => array('emails/eliminarEmails', 'id'=>$email['id_email'], 'borrado'=>'borrado_from'),'class'=>"button small black")); ?> </td> 
	</tr>
		
<?php }?>

</table>


<br>
<table> 
<tr> 
	<td><?php echo CHtml::button('Redactar mensaje', array('submit' => array('emails/redactar', 'destinatario'=>"" , 'tema'=>"" ,'equipo'=>false),'class'=>"button small black")); ?> </td>
	<td> &nbsp; &nbsp; &nbsp; </td>
	<td><?php echo CHtml::button('Bandeja de entrada', array('submit' => array('emails/'),'class'=>"button small black")); ?> </td>
	<td> &nbsp; &nbsp; &nbsp; </td>
	<td><?php echo CHtml::button('Borrar mensajes', array('submit' => array('emails/eliminarEmails', 'id'=>0, 'borrado'=>"borrado_from"),'class'=>"button small black")); ?> </td>
</tr>
</table>

 <?php $this->endWidget(); ?>