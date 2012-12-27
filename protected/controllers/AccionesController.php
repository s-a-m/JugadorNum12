<?php

/* Control para la funcionalidad relacionada con las acciones */
class AccionesController extends Controller
{
	/**
	 * @return array de filtros para actions
	 */
	public function filters()
	{
		return array(
			'accessControl', // Reglas de acceso
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Especifica las reglas de control de acceso.
	 * Esta función es usada por el filtro "accessControl".
	 * @return array con las reglas de control de acceso
	 */
	public function accessRules()
	{
		return array(
			array('allow', // Permite realizar a los usuarios autenticados cualquier acción
				'users'=>array('@'),
			),
			array('deny',  // Niega acceso al resto de usuarios
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Muestra todas las acciones (individuales y grupales) desbloqueadas
	 * Las acciones que el usuario no pueda hacer (por falta de recursos)
	 * aparecen remarcadas 
	 * 
	 * El id del usuario se recoge de la varibale de sesion
	 * 
	 * @ruta jugadorNum12/acciones
	 */
	public function actionIndex()
	{
		/* PEDRO */
		//Sacar una lista de las acciones desbloqueadas de un usuario
		$accionesDesbloqueadas = Desbloqueadas::model()->findAllByAttributes(array('usuarios_id_usuario'=>Yii::app()->user->usIdent));

		//Prepara los datos
		$acciones = array();
		foreach ($accionesDesbloqueadas as $habilidad){
			$acciones[] = Habilidades::model()->findAllByAttributes(array('id_habilidad' => $habilidad['habilidades_id_habilidad']));
		}

		//Envía los datos para que los muestre la vista
		$this->render('index',array('acciones'=>$acciones));
	}

	/**
	 * Ejecuta la accion (individual o grupal) pulsada 
	 *
	 * Si es una accion grupal muestra un formulario para recoger la 
	 * cantidad inicial de recursos que aporta el jugador
	 * Los datos del formulario se recogen por $_POST y se crea una 
	 * nueva accion grupal en el equipo al que pertenece el usuario
	 * Si es una accion individual se ejecuta
	 * 
	 * El id del jugador y la aficion a la que pertence se recogen de 
	 * la variable de sesion
	 *
	 * @parametro 	id de la accion que se ejecuta
	 * @ruta 		jugadorNum12/acciones/usar/{$id_accion}
	 * @redirige 	jugadorNum12/equipos/ver/{$id_equipo} 	si es accion grupal
	 * @redirige	jugadorNum12/usuarios/perfil 			si es accion individual
	 */
	public function actionUsar($id_accion)
	{
		// El parámetro $id_accion es en realidad el ID de la habilidad

		echo '<pre>'.print_r(Yii::app()->user,true).'</pre>';
		$trans = Yii::app()->db->beginTransaction();
		$habilidad = Habilidades::model()->findByPk($id_accion);

		if ( $habilidad == null ) {
			// Habilidad no encontrada
			$trans->rollback();
			throw new CHttpException(404,'Acción inexistente.');

		} else {
			// TODO Comprobar que el usuario ha desbloqueado la acción

			if ( $habilidad['tipo'] == Habilidades::TIPO_INDIVIDUAL ) {
				if ( Yii::app()->request->isPostRequest ) {
					// Petición POST: Procesar la acción
					$formDin = Yii::app()->request->getPost('dinero');
					$formAni = Yii::app()->request->getPost('animo');
					$formInf = Yii::app()->request->getPost('influencia');

					// Si no son suficientes recursos, pedir otra entrada al usuario
					if ( $formDin < $habilidad['dinero']
					  || $formAni < $habilidad['animo']
					  || $formInf < $habilidad['influencias']
					) {
						$trans->rollback();

						Yii::app()->user->setFlash('error', 'Recursos demasiado bajos');
						$this->refresh();
					}

					// Comprobar ahora que el usuario tiene recursos suficientes
					$res = Recursos::model()->findByAttributes(array('usuarios_id_usuario' => Yii::app()->user->id));
					$actDin = $res['dinero'];
					$actAni = $res['animo'];
					$actInf = $res['influencias'];

					if ($actDin < $formDin || $actAni < $formAni || $actInf < $formInf) {
						$trans->rollback();

						Yii::app()->user->setFlash('error', 'No tienes suficientes recursos');
						$this->refresh();
					}

					try {
						$res['dinero'] = $actDin - $formDin;
						$res['animo'] = $actAni - $formAni;
						$res['influencias'] = $actInf - $formInf;
						$res->save();

						$idUsuario = Yii::app()->user->id;
						$idAficion = 0;

						$accion = new AccionesGrupales();
						$accion['usuarios_id_usuario'] = $idUsuario;
						$accion['habilidades_id_habilidad'] = $habilidad['id_habilidad'];
						$accion['equipos_id_equipo'] = $idAficion;
						$accion['dinero_acc'] = $formDin;
						$accion['animo_acc'] = $formAni;
						$accion['influencias_acc'] = $formInf;
						/* TODO Resto de cosas que no sé qué son
						$accion['jugadores_acc'] = <?>;
						$accion['finalizacion'] = <?>;
						*/
						$accion->save();

						$trans->commit();

					} catch ( Exception $exc ) {
						$trans->rollback();
						throw $exc;
					}

				} else {
					// Petición GET: Mostrar formulario de recursos
					$trans->commit();
					$this->render('usar', array('habilidad'=>$habilidad));
				}

			} else if ( $habilidad['tipo'] == Habilidades::TIPO_GRUPAL ) {
				// Habilidad Grupal: TODO
				$trans->rollback();

			} else {
				// La acción no es de ningún tipo conocido
				// TODO Soltar un error de tres pares de huevos
				$trans->rollback();
			}
		}
	}

	/**
	 * Muestra la informacion relativa a una accion grupal abierta
	 *  recursos totales requeridos en la accion
	 *  jugadores que participan en ella
	 *  recursos aportados por cada jugador
	 *  efecto si se consigue la accion
	 *  
	 * Si es el usuario que la creo, muestra ademas
	 *  botones para expulsar participantes
	 * 
	 * @parametro 	id de la accion grupal que se muestra
	 * @ruta 		jugadorNum12/acciones/ver/{$id_accion}
	 */
	public function actionVer($id_accion)
	{
		/* PEDRO */
	}

	/**
	 * Permite participar en una accion grupal abierta por tu aficion.
	 * Muestra el formulario que define que recursos va a aportar a la
	 * accion que se recogen por $_POST 
	 *
	 * El id del jugador se recoge de la variable de sesion
	 *
	 * @parametro 	id de la accion en la que se va a participar
	 * @ruta 		jugadorNum12/acciones/participar/{$id}
	 * @redirige 	jugadorNum12/equipos/ver/{$id_equipo}
	 */
	public function actionParticipar($id_accion)
	{
		/* PEDRO */
	}

	/**
	 * Muestra un formulario de confirmacion para expulsar a un jugador
	 * participante en una accion grupal.
	 * Los recursos que puso el jugador le son devueltos
	 * (comprobando limite de animo e influencias)
	 * 
	 * @parametro 	id_accion de donde se va a expulsar al jugador
	 * @parametro 	id_jugador que se va a expulsar
	 * @ruta 		jugadorNum12/acciones/expulsar/{$id_accion}/{$id_jugador}
	 * @redirige 	jugadorNum12/acciones/ver/{$id_accion}
	 */
	public function actionExpulsar($id_accion, $id_jugador)
	{
		/* MARCOS */
	}
	
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=AccionesGrupales::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='acciones-grupales-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
