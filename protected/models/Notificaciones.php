<?php

/**
 * Modelo de la tabla <<notificaciones>>
 *
 * Columnas disponibles
 * string 	$id_notificacion
 * string 	$fecha
 * string 	$mensaje
 * string	$imagen
 */
class Notificaciones extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Notificaciones the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'notificaciones';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{ 	
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_notificacion', 'length', 'max'=>10),
			array('imagen', 'length', 'max'=>50),
			array('fecha', 'length', 'max'=>11),

			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id_notificacion, equipos_id_equipo, imagen,  fecha', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * Define las relaciones entre <notificaciones - tabla>
	 *
	 * @devuelve array de relaciones
	 */
	public function relations()
	{
		return array(
			/*Relacion entre <<usrnotif>> y <<notificaciones>>*/
			'usrnotif'=>array(self::HAS_MANY, 'Usrnotif', 'notificaciones_id_notificacion'),
		 );
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_notificacion' => 'Id Notificacion',
			'equipos_id_equipo' => 'Id Equipo',
			'fecha' => 'Fecha',
			'mensaje' => 'Mensaje',
			'imagen' => 'imagen',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id_notificacion',$this->id_notificacion,true);
		$criteria->compare('equipos_id_equipo',$this->equipos_id_equipo,true);
		$criteria->compare('fecha',$this->fecha,true);
		$criteria->compare('mensaje',$this->mensaje,true);
		$criteria->compare('imagen',$this->imagen);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function borrarNotificaciones(){
		//Borramos conexiones de notificaciones leidas
		Usrnotif::model()->deleteAllByAttributes(array('leido'=>1)); 
		//Cogemos todas las notificaciones
		$notificaciones = Notificaciones::model()->findAll();
		foreach($notificaciones as $notificacion){
			//si a alguna de las notificaciones ha sido leida por todos los usuarios se borra
			$u = Usrnotif::model()->findByAttributes(array('notificaciones_id_notificacion'=>$notificacion->id_notificacion));
			if($u === null)$notificacion->delete();
		}
	}
		
}