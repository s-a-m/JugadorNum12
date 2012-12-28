<?php

/**
 * Pagina de registro  
 */
class RegistroController extends Controller
{
	/**
	 * Muestra el formulario para registrarse en la pagina
	 * Si hay datos en $_POST procesa el formulario 
	 * y guarda en la tabla <<usuarios>> el nuevo usuario 
	 *
	 * @ruta 		jugadorNum12/registro
	 * @redirige	juagdorNum12/usuarios/perfil 
	 */
	public function actionIndex()
	{
		/* ALEX */
		$animadora_status='unchecked';
		$empresario_status='unchecked';
		$ultra_status='unchecked';
		$str = 0;
		$equipos = Equipos::model()->findAll();
		$modelo = new Usuarios ;
		$modelo->scenario='registro';
		
		if(isset($_POST['ajax']) && $_POST['ajax']==='usuarios-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if (isset($_POST['Usuarios']) && isset($_POST['registro']) ) {
			
			$modelo->attributes=$_POST['Usuarios'];
			//modifico modelo con los datos del formulario
			$modelo->setAttributes(array('nick'=>$_POST['Usuarios']['nuevo_nick']));
			$modelo->setAttributes(array('pass'=>$_POST['Usuarios']['nueva_clave1']));
			$modelo->setAttributes(array('email'=>$_POST['Usuarios']['nueva_email1']));
			$modelo->setAttributes(array('nivel'=>0));

			if(isset($_POST['pers'])){
				$str = 0;
				$selected_radio = $_POST['pers'];
				if ($selected_radio === 'animadora') {
					$animadora_status = 'checked';
					$modelo->setAttributes(array('personaje'=>0));

				}else if ($selected_radio === 'empresario') {
					$empresario_status = 'checked';
					$modelo->setAttributes(array('personaje'=>1));

				}else if($selected_radio === 'ultra'){
					$ultra_status = 'checked';
					$modelo->setAttributes(array('personaje'=>2));
				}
				$modelo->setAttributes(array('equipos_id_equipo'=>$_POST['ocup']));
				if ($_POST['ocup'] != 'Elige un equipo' && $modelo->save()){
					$this->redirect(array('site/login'));
				}else $str = 1;

			}else $str = 1;
		}

		$this->render('index',array('modelo'=>$modelo , 'equipos'=>$equipos , 
			'animadora_status'=>$animadora_status , 
			'empresario_status'=>$empresario_status , 
			'ultra_status'=>$ultra_status , 'str'=>$str ) );
	}
	

	/**
	 * @return array de filtros para actions
	 */
	/*public function filters()
	{
		return array(
			'accessControl', // Reglas de acceso
			'postOnly + delete', // we only allow deletion via POST request
		);
	}*/

	/**
	 * Especifica las reglas de control de acceso.
	 * Esta función es usada por el filtro "accessControl".
	 * @return array con las reglas de control de acceso
	 */
	/*public function accessRules()
	{
		return array(
			array('allow', // Permite realizar a los usuarios autenticados cualquier acción
				'users'=>array(''),
			),
			array('deny',  // Niega acceso al resto de usuarios
				'users'=>array('*'),
			),
		);
	}*/
}