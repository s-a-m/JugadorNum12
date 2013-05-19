<?php

/** 
 * Mandar a un jugador rival al hospital
 *
 * Tipo : Accion grupal
 * 
 * Efectos :
 *
 * - reduce el nivel del equipo contrario
 *
 * Bonus al creador :
 * 
 * - ninguno
 *
 *
 * @package componentes\acciones
 */
class MandarJugadorHospital extends AccionGrupSingleton
{	
  /**
   * Funcion para acceder al patron Singleton
   *
   * @static
   * @return \ConstruirEstadio instancia de la accion
   */
    public static function getInstance()
    {
      if (!self::$instancia instanceof self) {
         self::$instancia = new self;
      }
      return self::$instancia;
    }

  /**
   * Ejecutar la accion
   *
   * @param int $id_usuario id del usuario que realiza la accion
   * @throws \Exception usuario no encontrado
   * @return int 0 si completada con exito ; -1 en caso contrario
   */ 
  public function ejecutar($id_accion)
  {
    // TODO
  }

  /**
   * Accion grupal: sin efecto en finalizar()
   * @return void
   */
  public function finalizar() {}

}