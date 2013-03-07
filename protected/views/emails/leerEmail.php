<?php
	$form = $this->beginWidget('CActiveForm', array(
				'id'=>'emails-form',
			    'enableAjaxValidation'=>false,
			    'enableClientValidation'=>true,
			    'clientOptions'=>array(
					'validateOnSubmit'=>true,),
			    ));
 ?>


<table>
	<tr> <th>Remitente </th> <th> Destinatario </th> <th> Asunto </th> <th> Fecha </th> <th> &nbsp; </th> </tr>

	<tr> 
		<td> <?php echo $from; ?> </td> 
		<td> <?php echo $to; ?> </td> 
		<td> <?php echo $email->asunto; ?> </td> 
		<td> <?php echo Yii::app()->dateFormatter->formatDateTime($email->fecha, 'medium', 'short'); ?> </td> 
	</tr>

	<tr>
		<th>Contenido :  </th>
		<th> <?php echo $email->contenido; ?> </th>
	</tr>
</table>

<?php echo CHtml::button('Borrar', array('submit' => array('emails/eliminarEmail', 'id'=>$email->id_email,'antes'=>'entrada'),'class'=>"button small black")); ?> <br> <br>
<?php echo CHtml::button('Redactar mensaje', array('submit' => array('emails/redactar'),'class'=>"button small black")); ?> <br> <br>
<?php echo CHtml::button('Bandeja de entrada', array('submit' => array('emails/'),'class'=>"button small black")); ?> <br> <br>
<?php echo CHtml::button('Enviados', array('submit' => array('emails/enviados'),'class'=>"button small black")); ?> <br> <br>

 <?php $this->endWidget(); ?>