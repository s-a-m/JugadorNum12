<?php

/**
 * Modelo para la tabla <<acciones_grupales>>
 *
 * Columnas disponibles:
 *
 * | tipo   | nombre                    |  
 * |:-------|:--------------------------|
 * | string | $id_accion_grupal         |
 * | string | $usuarios_id_usuario      |
 * | string | $habilidades_id_habilidad |
 * | string | $equipos_id_equipo        |
 * | string | $influencias_acc          |
 * | string | $animo_acc                |
 * | string | $dinero_acc               |
 * | string | $jugadores_acc            |
 * | string | $finalizacion             |
 *
 * @package modelos
 */
class AccionesGrupales extends CActiveRecord
{
    /**     
     * Devuelve el modelo estatico de la clase "active record" especificada.
     *
     * > Funcion predeterminada de Yii.
     *
     * @static
     * @param string $className   nombre de la clase "active record". Por defecto __CLASS__
     * @return \AccionesGrupales  el modelo estatico de la clase
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className); 
    }

    /**
     * Devuelve el nombre de la tabla asociada a la clase
     *
     * > Funcion predeterminada de Yii
     * 
     * @return string   nombre de la tabla en la base de datos
     */
    public function tableName()
    {
        return 'acciones_grupales';
    }

    /**
     * Define las reglas definidas para los atributos del modelo, incluida la regla usada por "search()"
     *
     * Nota: deben definirse solo las reglas para aquellos atributos que reciban entrada del usuario
     *
     * > Funcion predeterminada de Yii
     *
     * @return array reglas de validacion para los atributos
     */
    public function rules()
    {
        return array(
            array('usuarios_id_usuario, habilidades_id_habilidad, equipos_id_equipo, influencias_acc, animo_acc, dinero_acc, jugadores_acc, finalizacion, completada', 'required'),
            array('usuarios_id_usuario, habilidades_id_habilidad, equipos_id_equipo, influencias_acc, animo_acc, dinero_acc, jugadores_acc', 'length', 'max'=>10),
            array('finalizacion', 'length', 'max'=>11),
            array('completada', 'length', 'max'=>1),
            // regla usada por "search()"
            array('id_accion_grupal, usuarios_id_usuario, habilidades_id_habilidad, equipos_id_equipo, influencias_acc, animo_acc, dinero_acc, jugadores_acc, finalizacion, completada', 'safe', 'on'=>'search'),
        );
    }

    /**
     * Define las relaciones entre la tabla <acciones_grupales> y el resto de tablas
     *
     * Relaciones definidas:
     *
     * - usuarios
     * - habilidades
     * - equipos
     * - participantes
     *
     * > Funcion predeterminada de Yii
     *
     * @return array relaciones entre <acciones_grupales> - <tabla>
     */
    public function relations()
    {
        return array(
            'usuarios'=>array(self::BELONGS_TO, 'Usuarios', 'usuarios_id_usuario'),
            'habilidades'=>array(self::BELONGS_TO, 'Habilidades', 'habilidades_id_habilidad'),
            'equipos'=>array(self::BELONGS_TO, 'Equipos', 'equipos_id_equipo'),
            'participaciones'=>array(self::HAS_MANY, 'Participaciones', 'acciones_grupales_id_accion_grupal')
        );
    }

    /**
     * Define los nombres "completos" de los atributos
     *
     * > Funcion predeterminada de Yii
     *
     * @return array nombres de los atributos 
     */
    public function attributeLabels()
    {
        return array(
            'id_accion_grupal' => 'Id Accion Grupal',
            'usuarios_id_usuario' => 'Usuarios Id Usuario',
            'habilidades_id_habilidad' => 'Habilidades Id Habilidad',
            'equipos_id_equipo' => 'Equipos Id Equipo',
            'influencias_acc' => 'Influencias Acc',
            'animo_acc' => 'Animo Acc',
            'dinero_acc' => 'Dinero Acc',
            'jugadores_acc' => 'Jugadores Acc',
            'finalizacion' => 'Finalizacion',
            'completada' => 'Completada',
        );
    }

    /**
     * Devuelve la lista de modelos con las condiciones de busqueda/filtro
     *
     * > Funcion predeterminada de Yii
     *
     * @return \CActiveDataProvider creterio definido para las busquedas/filtros
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id_accion_grupal',$this->id_accion_grupal,true);
        $criteria->compare('usuarios_id_usuario',$this->usuarios_id_usuario,true);
        $criteria->compare('habilidades_id_habilidad',$this->habilidades_id_habilidad,true);
        $criteria->compare('equipos_id_equipo',$this->equipos_id_equipo,true);
        $criteria->compare('influencias_acc',$this->influencias_acc,true);
        $criteria->compare('animo_acc',$this->animo_acc,true);
        $criteria->compare('dinero_acc',$this->dinero_acc,true);
        $criteria->compare('jugadores_acc',$this->jugadores_acc,true);
        $criteria->compare('finalizacion',$this->finalizacion,true);
        $criteria->compare('completada',$this->finalizacion,true);

        return new CActiveDataProvider($this, array('criteria'=>$criteria));
    }

    /**
     * Finaliza todas las acciones grupales no terminadas a tiempo
     *
     * @return void
     */
    public function finalizaGrupales()
    {
        try {
            //Traer acciones y Helper   
            Yii::import('application.components.Acciones.*');

            $tiempo = time();
            $busqueda=new CDbCriteria;
            $busqueda->addCondition(':bTiempo >= finalizacion');
            $busqueda->addCondition('completada = :bCompletada');
            $busqueda->params = array(':bTiempo' => $tiempo,':bCompletada' => 0);
            $transaction = Yii::app()->db->beginTransaction();

            $grupales = AccionesGrupales::model()->findAll($busqueda);

            //Iterar sobre las acciones grupales resultantes de la búsqueda
            foreach ($grupales as $gp) {       
                //Tomar participaciones
                $participantes = Participaciones::model()->findAllByAttributes(array('acciones_grupales_id_accion_grupal'=> $gp->id_accion_grupal));
                $notificacion = new Notificaciones;
				$notificacion->fecha = time();
				$notificacion->mensaje = "La habilidad " . Habilidades::model()->findByPk($gp->habilidades_id_habilidad)->nombre . " ha finalizado sin completarse."; 
                $notificacion->imagen = "images/iconos/notificaciones/finalizada_grupal.png";
				$notificacion->save();
				//Enviamos la notificación a los participantes
				foreach($participantes as $participacion){
					$usrnotif = new Usrnotif;
					$usrnotif->notificaciones_id_notificacion = $notificacion->id_notificacion;
					$usrnotif->usuarios_id_usuario = $participacion->usuarios_id_usuario;//$habilidad->id_habilidad;//
					$usrnotif->save();
				}
                //Recorro todos los participantes devolviendoles sus recursos.
                //Esto incluye el creador de la acción.
                foreach ($participantes as $participante) {
                    //Cojo el dinero,influencia y animo aportado por el usuario
                    $dinero=$participante->dinero_aportado;
                    $influencia=$participante->influencias_aportadas;
                    $animo=$participante->animo_aportado;

                    //Utilizo el helper para ingresarle al usuario los recursos
                    Recursos::aumentar_recursos($participante->usuarios_id_usuario,'dinero',$dinero);
                    Recursos::aumentar_recursos($participante->usuarios_id_usuario,'animo',$animo);
                    //Recursos::aumentar_recursos($participante->usuarios_id_usuario,'influencias',$influencia);

                    Recursos::disminuir_influencias_bloqueadas($participante->usuarios_id_usuario, $participante->influencias_aportadas);

                    //Eliminar ese modelo
                    Participaciones::model()->deleteAllByAttributes(array('acciones_grupales_id_accion_grupal'=> $gp->id_accion_grupal,'usuarios_id_usuario'=> $participante->usuarios_id_usuario));
                }

                //Borro esa accion grupal iniciada por el usuario que quiere cambiar de equipo
                AccionesGrupales::model()->deleteByPk($gp->id_accion_grupal);
            }
            //Finalizar transacción con éxito
            $transaction->commit();  
        } catch (Exception $ex) {
            //Rollback de la transacción en caso de error
            $transaction->rollback();
        }       
    }

    /** 
     * Funcion para finalizar una grupal dada y devolver los recursos a los participantes.
     * 
     * @param int $id_accion_grupal 
     * @param bool $eliminar indica si se debe borrar la accion 
     * @throws \Exception excepcion interna
     * @return void
     */
    public static function finalizaGrupal($id_accion_grupal, $eliminar = true)
    {
        try {       
            // 1. Devolver recursos a los participantes (incluido el creador)
            $participantes=Participaciones::model()->findAllByAttributes(array('acciones_grupales_id_accion_grupal'=> $id_accion_grupal));

            //Recorrer todos los participantes devolviendoles sus recursos
            $u;
            foreach ($participantes as $participante) {
                // 0. Aportar poca experencia (por el esfuerzo)
                $u = Usuarios::model()->findByPk($participante->usuarios_id_usuario);
                $u->sumarExp(Usuarios::POCA_EXP);

                // 1. Aumentar recursos a los participantes
                Recursos::aumentar_recursos($participante->usuarios_id_usuario,'dinero', $participante->dinero_aportado);
                Recursos::aumentar_recursos($participante->usuarios_id_usuario,'animo', $participante->animo_aportado);
                Recursos::disminuir_influencias_bloqueadas($participante->usuarios_id_usuario, $participante->influencias_aportadas);
                Recursos::aumentar_recursos($participante->usuarios_id_usuario,'influencias', $participante->influencias_aportadas);
                
                // 2. Eliminar ese modelo
                Participaciones::model()->deleteAllByAttributes(array('acciones_grupales_id_accion_grupal'=> $id_accion_grupal,'usuarios_id_usuario'=> $participante->usuarios_id_usuario));        
            }
            // 3. Borrar grupal si es necesario
            if ($eliminar) {
                AccionesGrupales::model()->deleteByPk($id_accion_grupal);
            }
        }
        catch (Exception $e) {
            // Dejar el try/catch para permitir posible logging de excepciones
            throw $e;
        }
    }   

    /**
     * Expulsa a un jugador de una accion grupal dada (borrarle de la lista de participantes)
     *
     * @static
     * @param int $id_accion
     * @param int $id_jugador
     * @throws \Exception 'Recursos inexistentes'
     * @throws \CHttpException 'Acción inexistente.'
     * @throws \CHttpException 'Acción completada.'
     * @throws \CHttpException 'No tienes privilegios sobre la accion.'
     * @throws \CHttpException 'No puedes expulsarte a ti mismo'
     * @throws \CHttpException 'El jugador indicado no participa en la accion'
     * @throws \Exception 'Error en la base de datos'
     * @return void
     */ 
    public static function expulsarJugador($id_accion, $id_jugador)
    {
        $acc = AccionesGrupales::model()->findByPk($id_accion);
        $rec = Recursos::model()->findByAttributes(array('usuarios_id_usuario' => $id_jugador));
        $part = Participaciones::model()->findByAttributes(array('acciones_grupales_id_accion_grupal'=>$id_accion,'usuarios_id_usuario'=>$id_jugador));
        
        //Se comprueba la coherencia de la petición
        if ($rec === null) {
            Yii::app()->user->setFlash('recursos', 'Recursos inexistentes. (actionExpulsar,AccionesController).');
            throw new Exception('Recursos inexistentes. (actionExpulsar,AccionesController)');
        }
        if ($acc === null) {
            Yii::app()->user->setFlash('accion', 'Acción inexistente.');
            throw new CHttpException('Acción inexistente.');
        }
        if ($acc->completada == 1) {
            Yii::app()->user->setFlash('expulsar', 'Acción completada.No puedes expulsar.');
            throw new CHttpException('Acción completada.No puedes expulsar.');
        }
        if ($acc['usuarios_id_usuario']!= Yii::app()->user->usIdent) {
            Yii::app()->user->setFlash('privilegios', 'No tienes privilegios sobre la acción.');
            throw new CHttpException('No tienes privilegios sobre la acción.');
        }
        if ($id_jugador == Yii::app()->user->usIdent) {
            Yii::app()->user->setFlash('error', 'No puedes expulsarte a tí mismo.');
            throw new CHttpException('No puedes expulsarte a tí mismo.');
        }
        if ($part == null) {
            Yii::app()->user->setFlash('participante', 'El jugador indicado no participa en la acción.');
            throw new CHttpException('El jugador indicado no participa en la acción.');
        }

        $actAni = $rec['animo'];
        $actInf = $rec['influencias'];
        $maxAni = $rec['animo_max'];
        $maxInf = $rec['influencias_max'];
        $partDin = $part['dinero_aportado'];
        $partAni = $part['animo_aportado'];
        $partInf = $part['influencias_aportadas'];

        $rec['dinero'] += $partDin;
        $rec['animo'] = min(($actAni + $partAni), $maxAni);
        $rec['influencias'] = min(($actInf + $partInf), $maxInf);
        $rec['influencias_bloqueadas'] = $maxInf - min(($actInf + $partInf), $maxInf);
        $rec->save();

        $acc['jugadores_acc'] -= 1;
        $acc['dinero_acc'] -= $partDin;
        $acc['animo_acc'] -= $partAni;
        $acc['influencias_acc'] -= $partInf;
        $acc->save();

        //$part->delete(); // elegante, pero no funciona
        $n = Participaciones::model()->deleteAllByAttributes(array('acciones_grupales_id_accion_grupal'=>$id_accion,'usuarios_id_usuario'=>$id_jugador));

        if($n != 1) {
            //Si salta esto es que había más de una participación del mismo usuario en la acción
            Yii::log('[DATABASE_ERROR] El usuario '.$id_jugador.' tiene '.$n.' participaciones en la acción '.$id_accion,'error');
            Yii::app()->user->setFlash('base_datos', 'Error en la base de datos. Pongase en contacto con un administrador.');
            throw new Exception('Error en la base de datos. Pongase en contacto con un administrador.');
        }
    }

    /** 
     * Funcion que agrega una participacion a una accion grupal
     *
     *  -  actualiza los recursos del usuario
     *  -  suma exp al jugador en funcion de los recursos aportados
     *  -  actualiza los recursos de la accion
     *  -  agrega la participacion (o actualiza una ya existente)
     *  -  si se completa la accion, la ejecuta
     *
     * Comprobaciones de seguridad realizadas:
     *  1. la participacion es para el equipo del usuario
     *  2. la accion no ha terminado
     *  3. el jugador tiene suficientes recursos
     *  4. la participacion no es mayor que los recursos restantes de la accion
     *  5. error al actualizar la participacion en la base de datos
     *  6. la participacion es vacia (ignorarla)
     *
     * @static
     * @param $id_accion
     * @param $recursosAportados
     * @param $accion
     * @param $habilidad
     * @param $participacion
     * @param $nuevo_participante
     *
     * @throws \Exception 'No puedes participar en esta accion'
     * @throws \Exception 'La accion ya ha acabado'
     * @throws \Exception 'No se puede obtener el modelo de recursos'
     * @throws \Exception 'No tienes suficientes recursos.'
     * @throws \Exception 'Error en la BBDD.'
     * @throws \Exception 'El usuario se ha saltado una validación.'
     * @throws \Exception 'No has aportado nada a la acción.'
     * @return void
     */
    public static function participar($id_accion, $recursosAportados, $accion, $habilidad, $participacion, $nuevo_participante)
    {
        // 1) Comprobacion: la accion es del equipo del user
        if($accion['equipos_id_equipo']!= Yii::app()->user->usAfic) {
            Yii::app()->user->setFlash('equipo', 'No puedes participar en esta acción. No es de tu equipo');
            throw new Exception('No puedes participar en esta acción.');
        }

        // 2) Comprobacion: la accion no ha terminado
        if ($accion['completada'] != 0) {
            Yii::app()->user->setFlash('acabada', 'La acción ya ha acabado.');
            throw new Exception('La acción ya ha acabado.');
        }

        // id_usuario y recursos 
        $id_user = Yii::app()->user->usIdent;
        $recursosUsuario = Recursos::model()->findByAttributes(array('usuarios_id_usuario' => $id_user));
        if ($recursosUsuario === null) { 
            //Comprobación de seguridad
            Yii::app()->user->setFlash('recursos', 'No se puede obtener el modelo de recursos. (actionParticipar,AccionesController).');
            throw new Exception('No se puede obtener el modelo de recursos.');
        }
        $dineroUsuario = $recursosUsuario['dinero'];
        $influenciasUsuario = $recursosUsuario['influencias'];
        $animoUsuario = $recursosUsuario['animo'];
        $dineroAportado = $recursosAportados['dinero_nuevo'];
        $animoAportado = $recursosAportados['animo_nuevo'];
        $influenciasAportadas = $recursosAportados['influencia_nueva'];

        // Se actualiza la participacion
        $participacion->setAttributes(array('dinero_nuevo'=>$dineroAportado, 'animo_nuevo'=>$animoAportado, 'influencia_nueva'=>$influenciasAportadas));

        // 3) Comprobacion: el usuario tiene suficientes recursos
        if ( $dineroAportado > $dineroUsuario || $animoAportado > $animoUsuario || $influenciasAportadas > $influenciasUsuario) {
            //No tiene suficientes recursos
            Yii::app()->user->setFlash('recursos', 'No tienes suficientes recursos.');
            throw new Exception('No tienes suficientes recursos.');
        }

        // 4) Comprobacion: los recursos aportados no sobrepasan los que faltan para terminar la acción
        $dineroAportado = min($dineroAportado, $habilidad['dinero_max'] - $accion['dinero_acc']);
        $animoAportado = min($animoAportado, $habilidad['animo_max'] - $accion['animo_acc']);
        $influenciasAportadas = min($influenciasAportadas, $habilidad['influencias_max'] - $accion['influencias_acc']);

        // 5) Comprobacion: error al actualizar la participacion en la base de datos
        if($dineroAportado<0 || $animoAportado<0 || $influenciasAportadas<0) {
            if($habilidad['dinero_max'] < $accion['dinero_acc']) {
                Yii::log('[DATABASE_ERROR] La accion '.$id_accion.' más dinero del maximo ('.$accion['dinero_acc'].'/'.$habilidad['dinero_max'].').','error');
                Yii::app()->user->setFlash('base_datos', 'Error en la base de datos. Pongase en contacto con un administrador.');
                throw new Exception('Error en la BBDD.');
            }
            elseif($habilidad['animo_max'] < $accion['animo_acc']) {
                Yii::log('[DATABASE_ERROR] La accion '.$id_accion.' más animo del maximo ('.$accion['animo_acc'].'/'.$habilidad['animo_max'].').','error');
                Yii::app()->user->setFlash('base_datos', 'Error en la base de datos. Pongase en contacto con un administrador.');
                throw new Exception('Error en la BBDD.');
            }
            elseif($habilidad['influencias_max'] < $accion['influencias_acc']) {
                Yii::log('[DATABASE_ERROR] La accion '.$id_accion.' más influencia del maximo ('.$accion['influencias_acc'].'/'.$habilidad['influencias_max'].').','error');
                Yii::app()->user->setFlash('base_datos', 'Error en la base de datos. Pongase en contacto con un administrador.');
                throw new Exception('Error en la BBDD.');
            }
            
            Yii::log('[MALICIOUS_REQUEST] El usuario '.$id_user.' se ha saltado una validación de seguridad, intentando robar recursos de la accion '.$id_accion, 'warning');
            Yii::app()->user->setFlash('aviso', 'Se ha registrado un intento de ataque al sistema. De no ser así, póngase en contacto con el administrador. Ten cuidado o acabarás baneado.');
            throw new Exception('El usuario se ha saltado una validación.');
        }

        //6 ) Comprobacion: la participacion es vacia (ignorarla)
        if($dineroAportado==0 && $animoAportado==0 && $influenciasAportadas==0) {
            Yii::app()->user->setFlash('aporte', 'No has aportado nada a la acción.');
            throw new Exception('No has aportado nada a la acción.');
        }

        // Actualizar los recursos del user
        $recursosUsuario['dinero'] = $dineroUsuario - $dineroAportado;
        $recursosUsuario['animo'] = $animoUsuario - $animoAportado;
        $recursosUsuario['influencias'] = $influenciasUsuario - $influenciasAportadas;
        $recursosUsuario['influencias_bloqueadas'] += $influenciasAportadas;
        $recursosUsuario->save();

        // Actualizar la experiencia acumulada del usuario
        $usuario = Usuarios::model()->findByPk($id_user);
        if ($usuario === null) { 
            //Comprobación de seguridad
            Yii::log('[DATABASE_ERROR] La accion '.$id_accion.' No se encuentra el usuario ('.$id_user.').','error');
            Yii::app()->user->setFlash('base_datos', 'Error en la base de datos. Pongase en contacto con un administrador.');
            throw new Exception('Error en la BBDD.');
        }
        $exp_ganada = (int) ($dineroAportado*Usuarios::MOD_EXP_DINERO + $animoAportado*Usuarios::MOD_EXP_ANIMO + $influenciasAportadas*Usuarios::MOD_EXP_INFLUENCIAS);
        $usuario->sumarExp($exp_ganada);

        // Actualizar la acciones_grupales
        $accion['dinero_acc'] += $dineroAportado;  
        $accion['influencias_acc'] += $influenciasAportadas;
        $accion['animo_acc'] += $animoAportado;
        if($nuevo_participante) {
            $accion['jugadores_acc'] += 1;
        }
        if ($accion['dinero_acc'] == $habilidad['dinero_max'] && $accion['influencias_acc'] == $habilidad['influencias_max'] && $accion['animo_acc'] == $habilidad['animo_max']) {
            $accion['completada'] = 1;                  
        }
        
        // Actualizar la participación
        if($nuevo_participante){
            $participacion['dinero_aportado'] = $dineroAportado;
            $participacion['influencias_aportadas'] = $influenciasAportadas;
            $participacion['animo_aportado'] = $animoAportado;
            $participacion->save();
        } else {  
            $n=$participacion->updateAll(array( 
                'dinero_aportado'=>$participacion['dinero_aportado'] + $dineroAportado,
                'influencias_aportadas'=>$participacion['influencias_aportadas'] + $influenciasAportadas,
                'animo_aportado'=>$participacion['animo_aportado'] + $animoAportado),
                "acciones_grupales_id_accion_grupal=:id_accion && usuarios_id_usuario=:id_user",
                array(':id_accion'=>$id_accion, ':id_user'=>$id_user)
            ); 
            if($n!=1) {
                //Si salta esto es que había más de una participación del mismo usuario en la acción
                Yii::log('[DATABASE_ERROR] El usuario '.$id_user.' tiene '.$n.' participaciones en la acción '.$id_accion,'error');
                Yii::app()->user->setFlash('base_datos', 'Error en la base de datos. Pongase en contacto con un administrador.');
                throw new Exception('Error en la BBDD.');
            }
        }

        // 1) Si la accion esta completada con esa aportacion
        //  - se ejecuta la accion 
        //  - bonificacion de experencia a los participantes de la accion
        // 2) Se guardas los cambios en la accion
        if($accion['completada'] == 1) {     
			//Si se ha completado creamos una notificación
			$notificacion = new Notificaciones;
			$notificacion->fecha = time();
			$notificacion->mensaje = Usuarios::model()->findByPk($id_user)->nick . " ha completado la acción " . Habilidades::model()->findByPk($habilidad->id_habilidad)->nombre;            $notificacion->imagen = "images/iconos/notificaciones/completa_grupal.png";
			$notificacion->save();
			//Enviamos la notificación a la afición
			$componentes = Usuarios::model()->findAllByAttributes(array('equipos_id_equipo'=>Usuarios::model()->findByPk($id_user)->equipos_id_equipo));
			foreach ($componentes as $componente){
				$usrnotif = new Usrnotif;
				$usrnotif->notificaciones_id_notificacion = $notificacion->id_notificacion;
				$usrnotif->usuarios_id_usuario = $componente->id_usuario;
				$usrnotif->save();
			}		

            Yii::import('application.components.Acciones.*');
            $nombreHabilidad = $habilidad->codigo;
            //Llamar al singleton correspondiente y ejecutar dicha acción
            $nombreHabilidad::getInstance()->ejecutar($id_accion);

            // actualizar la exp de todos los participantes
            $participantes = $accion->participaciones;
            $u;
            foreach ($participantes as $p) {
                $u = Usuarios::model()->findByPk($p->usuarios_id_usuario);
                $u->sumarExp(Usuarios::MUCHA_EXP);
            }
        } 
        $accion->save();

        if($accion['completada'] == 1) {
            Yii::app()->user->setFlash('completada', '¡Enhorabuena, has completado la acción¡');
        } else {
        	//si se ha aportado algo creamos notivicación

            $participaciones = Participaciones::model()->findAllByAttributes(array('acciones_grupales_id_accion_grupal'=>$id_accion));

            //Si soy el unico en participar no creamos la notificacion
            if(count($participaciones) > 1 ){

                $notificacion = new Notificaciones;
            	$notificacion->fecha = time();
            	$notificacion->mensaje = Usuarios::model()->findByPk($id_user)->nick . " ha participado en la acción " . Habilidades::model()->findByPk($habilidad->id_habilidad)->nombre;
                $notificacion->imagen = "images/iconos/notificaciones/participacion_grupal.png";
            	$notificacion->save();

            	//enviamos notificaciones a los participantes de la acción
            			
            	foreach($participaciones as $participacion){
            		if($participacion->usuarios_id_usuario != $id_user){
            			$usrnotif = new Usrnotif;
            			$usrnotif->notificaciones_id_notificacion = $notificacion->id_notificacion;
            			$usrnotif->usuarios_id_usuario = $participacion->usuarios_id_usuario;//$habilidad->id_habilidad;//
            			$usrnotif->save();
            		}
            	}
            }

            Yii::app()->user->setFlash('aporte', 'Tu equipo agradece tu generosa contribución.');
        }
    }

    /**
     * Usar una accion grupal
     *
     * @static
     * @param $usuario
     * @param $id_accion
     * @param $id_equipo
     * @param $res : recursos del jugador
     * @param $habilidad
     * @return int id de la nueva accion grupal
     */
    public static function usarGrupal($usuario, $id_accion, $id_equipo, $res, $habilidad)
    {       
        $id_usuario = $usuario->id_usuario;
        /*
            Se deberia obtener la accion grupal mediante su PK (id_accion_grupal)
            Como $id_accion equivale $id_habilidad por como se redirige desde acciones/index
            para obtener la accion grupal debo buscar por id_equipo y id_habilidad
            NOTA: no se contempla la posibilidad de en un mismo equipo haya varias acciones iguales
            pero con distinto creador (aunque dicha posibilidad existe) ya que debe arreglarse la redireccion
        */  
        $res['dinero']      -= $habilidad['dinero'];
        $res['animo']       -= $habilidad['animo'];
        $res['influencias'] -= $habilidad['influencias'];
        $res['influencias_bloqueadas'] += $habilidad['influencias'];
        
        //sumarselos al crear nueva accion grupal
        $accion_grupal = new AccionesGrupales();
        $accion_grupal->setAttributes( array(
            'usuarios_id_usuario' => $id_usuario,
            'habilidades_id_habilidad' => $id_accion,
            'equipos_id_equipo' => $id_equipo,
            'influencias_acc'   => $habilidad['influencias'],
            'animo_acc'         => $habilidad['animo'],
            'dinero_acc'        => $habilidad['dinero'],
            'jugadores_acc'     => 1,
            'finalizacion'      => $habilidad['cooldown_fin']+time(),                                                   
            'completada'        => 0
        ));
        //guardar en los modelos
        $res->save();
        $accion_grupal->save();
        
        //Crear participación del creador
        $participacion = new Participaciones();
        $participacion->acciones_grupales_id_accion_grupal = $accion_grupal->id_accion_grupal;
        $participacion->usuarios_id_usuario = $id_usuario;
        $participacion->dinero_aportado = $habilidad['dinero'];
        $participacion->influencias_aportadas = $habilidad['influencias'];
        $participacion->animo_aportado = $habilidad['animo'];
        if (!$participacion->save()) {
            Yii::app()->user->setFlash('error', 'Participación no creada. (AccionesController,actionUsar.');
            throw new Exception("Participación no creada. (AccionesController,actionUsar)");    
        }


        //Enviamos la notificación correspondiente
		$notificacion = new Notificaciones;
		$notificacion->fecha = time();
		$notificacion->mensaje = Usuarios::model()->findByPk($id_usuario)->nick . " ha abierto la acción " . Habilidades::model()->findByPk($id_accion)->nombre;
        $notificacion->imagen = "images/iconos/notificaciones/nueva_grupal.png";
		$notificacion->save();
		//Enviamos la notificación a la afición
		$componentes = Usuarios::model()->findAllByAttributes(array('equipos_id_equipo'=>$id_equipo));
		foreach ($componentes as $componente){
			$usrnotif = new Usrnotif;
			$usrnotif->notificaciones_id_notificacion = $notificacion->id_notificacion;
			$usrnotif->usuarios_id_usuario = $componente->id_usuario;
			$usrnotif->save();
		}

        // EXP: sumar experencia al usuario
        $usuario->sumarExp(Usuarios::MEDIA_EXP);
        return $accion_grupal['id_accion_grupal'];
    }
}
