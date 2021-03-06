-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net

-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-12-2012 a las 13:37:32
-- Versión del servidor: 5.5.27
-- Versión de PHP: 5.4.7
-- Base de datos: `juego`

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- ------------------
-- ATENCION
-- ------------------
-- los usuarios por defecto comienzan en nivel 1 a 550exp para pasar a 2
-- --------------------------------------------------------------------


-- ------------------------------------------------------------------------------------
-- Vaciado de tablas
--
-- delete from <<tabla>> where 1
--  borra los n registros almacenados, el siguiente registro que guardemos sera el n+1
-- 
-- truncate <<tabla>>
--  borra los n registros almacenados, el siguiente registro que guardemos sera el 1
-- ------------------------------------------------------------------------------------

-- ---------------------
-- CONSTANTES DEFINIDAS 
-- ---------------------
-- Tipos de habilidades
--  GRUPALES        = 0
--  INDIVIDUALES    = 1
--  PARTIDO         = 2
--  PASIVAS         = 3
--
-- Tipos de personajes
--  ULTRA           = 0
--  MOVEDORA        = 1
--  EMPRESARIO      = 2
-- ---------------------

-- ------------------------------------------------------------
-- ------------ RELLENADO DE TABLAS ---------------------------
-- ------------------------------------------------------------

 -- -----------------------------------------------------
 -- Proximos partidos + seguidores con habilidades grupales
 -- -----------------------------------------------------
 -- Verdes vs. Rojos   (partido 7)
 -- Blancos vs. Negros (partido 8)
 -- -----------------------------------------------------
 -- Rojos:
 --     xaby (1)    => habilidades: 1, 2, 3
 --     arturo (3)  => habilidades: 1, 2, 3
 --     dani(4)     => habilidades: 4
 --     marcos(8)   => habilidades: 4
 -- Verdes:
 --     marina(2)   => habilidades: 3, 4, 5
 --     pedro (5)   => habilidades: 1, 2, 3
 --     rober(7)    => habilidades: 4
 --     alex(9)     => habilidades: 3, 4, 5
 -- Negros: 
 --     manu(6)     => habilidades: 3, 4, 5 
 --     samu(10)    => habilidades: 4
SET FOREIGN_KEY_CHECKS = 0; 

TRUNCATE `acciones_grupales`;


TRUNCATE `acciones_individuales`;

 
 -- ----------------------------------
 -- Rojos (1)   7 puntos    +4 goles
 -- Negros (3)  4 puntos    -1 goles
 -- Verdes (2)  2 puntos    +2 goles
 -- Blancos (4) 2 puntos    -2 goles
 -- ----------------------------------
TRUNCATE `clasificacion`;
INSERT INTO `clasificacion` (`equipos_id_equipo`, `posicion`, `puntos`, `ganados`, `empatados`, `perdidos`, `diferencia_goles`) VALUES
 -- negros
 (1, 1, 0, 0, 0, 0, 0),
 -- verdes
 (2, 3, 0, 0, 0, 0, 0),
 -- blancos
 (3, 8, 0, 0, 0, 0, 0),
 -- azules
 (4, 4, 0, 0, 0, 0, 0),
 -- rosas
 (5, 5, 0, 0, 0, 0, 0),
 -- naranjas
 (6, 6, 0, 0, 0, 0, 0),
 -- amarillos
 (7, 7, 0, 0, 0, 0, 0),
 -- rojos
 (8, 2, 0, 0, 0, 0, 0);

TRUNCATE `desbloqueadas`;

TRUNCATE `equipos`;
INSERT INTO `equipos` (`partidos_id_partido`,`nombre`,`token`,`categoria`, `aforo_max`, `aforo_base`, `nivel_equipo`, `factor_ofensivo`, `factor_defensivo`) VALUES
 (0, 'Orcas negras',   'negros',   1, 3600, 400, 10, 7, 7),
 (0, 'Perros verdes',   'verdes',   1, 3000, 500, 10, 7, 6),
 (0, 'Iguanas Moradas',  'morados',  1, 4000, 400, 9,  6, 8),
 (0, 'Tritones azules',   'azules',   1, 3500, 300, 11, 6, 7),
 (0, 'Elefantes rosas',    'rosas',    1, 4000, 450, 10, 5, 5),
 (0, 'Carpas naranjas', 'naranjas', 1, 3600, 500, 12, 7, 7),
 (0, 'Patos amarillos','amarillos',1, 3000, 350, 10, 6, 6),
 (0, 'Gorilas rojos',    'rojos',    1, 3000, 400, 12, 7, 5);

 -- ---------------------------------------------------------------------------------------------------------------------------------
 -- Tabla de costes para las habilidades 
 -- ---------------------------------------------------------------------------------------------------------------------------------
 --                                 dinero animo influencias | dinero_max animo_max influencias_max | participantes_max cooldown_fin
 -- Habilidades "empresariales" =>   500    3       1        |  10000       60          12          |   3                   200                  
 -- Habilidades "ultra"         =>   200    30      0        |  4000        600         1           |   3                   200             
 -- Habilidades "movedora"      =>   60     15      1        |  1200        300         24          |   3                   200
 -- ----------------------------------------------------------------------------------------------------------------------------------
TRUNCATE `habilidades`;
INSERT INTO `habilidades` (`codigo`, `tipo`, `nombre`, `descripcion`, `dinero`, `animo`, `influencias`, `dinero_max`, `animo_max`, `influencias_max`, `participantes_max`, `cooldown_fin`, `token`) VALUES
 -- ---------------------
 -- Tipos de habilidades
 -- ---------------------
 -- GRUPALES        0
 -- INDIVIDUALES    1
 -- PARTIDO         2
 -- PASIVAS         3
 -- ---------------------
 ('FinanciarEvento', 0, 'Financiar un evento promocional', '"El marketing lo es todo: organizar un evento promocional ayudará a caldear el ambiente del próximo partido además de atraer más espectadores al estadio"',                                                                         7500, 140,  40, 75000, 7500, 400,            6, 600, 'financiar_evento'),                            
 ('IncentivoEconomico', 0, 'Incentivo económico a los jugadores', '"Los jugadores pueden correr más... sólo necesitan un pequeño empujoncito. Aumenta el nivel del equipo para el próximo partido; el impulsor del incentivo recupera influencias que haya destinado a otras acciones"',        6000, 360,  32, 60000, 9000, 325,            6, 600, 'incentivo_economico'),                         
 ('OrganizarHomenaje', 0, 'Organizar homenaje a un jugador', '"Organiza un homenaje antes del partido a un jugador amado por la grada y conseguiras atraer a más espectadores para el próximo encuentro; el impulsor gana además influencias dentro del club si logra completar el homenaje"',  4400, 340,  12,  44000, 8500, 350,            6, 600, 'organizar_homenaje'),                          
 ('Pintarse', 0, 'Pintarse con los colores del equipo', '"Demuestra tu pasión por los colores de tu equipo. Sube el ambiente para el próximo partido"',                                                                                                                                         1000, 80,  16,  10000, 2250, 90,             6, 600, 'pintarse_colores_equipo'),                     
 ('PromoverPartido', 0, 'Promover el partido por las redes sociales', '"Comparte tu ilusión con tus amigos por internet."',                                                                                                                                                                     1000, 140,  6,  10000, 3500, 150,            6, 600, 'publicitar_internet'),                         
 ('ConseguirInversores', 0, 'Conseguir inversores extranjeros', '"Muévete por el mundillo empresarial para conseguir inversores extranjeros para tu equipo; quién sabe, quizá convezcas a algún jeque millonario para que invierta en el equipo"',                                              5200, 280, 20, 52000, 7000, 550,             6, 900, 'conseguir_inversores_extranjeros'),            
 ('ConstruirEstadio', 0, 'Construir estadio', '"Un gran equipo necesita un gran estadio. Consigue un buen arquitecto, recalifica unos terrenos un par de sobornos a políticos y bualah! Más aficionados, mejores palcos, mejores derechos en televisión..."',                                   75000, 680, 36, 750000, 17000, 900,   	   8, 15000, 'construir_estadio'),                   
 ('FicharJugador', 0, 'Fichar un jugador para el equipo', '"El fichaje de jugadores no tiene porque ser algo exclusivamente de los clubes... un buen aficionado con, digamos, las suficientes influencias sobre la directiva el club puede hacer que se fiche a ese jugador deseado"',          35000, 240, 40,  350000,  6000, 550,   	   4, 10000, 'fichar_jugador'),          
 ('FinanciarPelicula', 0, 'Financiar Pelicula sobre el equipo', '"Producir una película o un documental sobre el club, qué significan los colores del equipo, los valores de la cantera... esas chorradas que se traducen en pasta; pasta gansa"',                                              7500, 260, 16, 75000, 7500, 400,              6, 600, 'financiar_pelicula'),
 ('HackearPlataforma', 0, 'Hackear plataforma web rival', '"Organiza a unos amigos informáticos para hackear los blogs y páginas que den apoyo al equipo contrario, peudes conseguir que vayan menos hinchas de su equipo a animar al partido"',                                                2800, 400, 8, 28000, 10000, 200,            5, 300, 'hackear_plataforma_rival'),
 ('PublicarDifamaciones', 0, 'Pulicar difamaciones sobre el rival', '"La prensa sensacionalista puede ser tu aliada. Y la rosa. Y si nos ponemos los telediarios de Cuatro; un buen rumor si tiene morbo se difundirá por los medios como la polvora"',                                         2900, 220, 14, 28500, 5500, 350,              6, 600, 'publicar_difamaciones'),
 ('AlquilarBus', 0, 'Alquilar bus de hinchas para ir a animar el partido', '"Mueve a toda la peña para ir al próximo partido del equipo y llenar el estadio"',                                                                                                                                  1000, 60, 1, 10000, 1400, 30,               9, 600, 'alquilar_bus'),
 ('ObrasBeneficas', 0, 'Participar en obras benéficas', '"Mueve los hilos para que el club se plubicite con diversas obras benéficas para lavar su imagen pública y que caiga mejor a los aficioandos"',                                                                                        10000, 400, 20, 100000, 10000, 550,         5, 800, 'obras_beneficas'),
 ('MandarJugadorHospital', 0, 'Mandar a un jugador rival al hospital', '"Chicos, que parezca un accidente"',                                                                                                                                                                                    6000, 620, 20, 60000, 16000, 500,           6, 600, 'mandar_jugador_hospital'), 
 ('ConciertoRock', 0, 'Concierto de Rock', '"Toca con tus amigos un nuevo cántico para el equipo"',                                                                                                                                                                                             2400, 60, 3, 24000, 1500, 30,               4, 400, 'concierto_estadio'),

 ('Apostar', 1, 'Apostar por el partido', '"Dale esa pizca extra de emoción."',                                                                                                                                                                                                                 40,   150,  0,     NULL, NULL, NULL,         1, 200, 'apostar'),                              
 ('CrearseEspectativas', 1, 'Crearse espectativas para el próximo partido', '"Crearse espectativas para siguiente partido: obtienes inmediatamente puntos de animo"',                                                                                                                           650,  0,    0,     NULL, NULL, NULL,         1, 200, 'crear_cantico'),                          
 ('ContratarRRPP', 1, 'Contratar temporalmente a un relaciones públicas', '"Es muy duro movilizar las redes sociales tú sólo. Contrata temporalmente un ayudante."',                                                                                                                            2000, 0,    2,     NULL, NULL, NULL,         1, 200, 'recopilar_influencias'),                
 ('FalsearCuentas', 1, 'Falsear las cuentas', '"Ups, ¡vaya! error de la banca a tu favor; no, eso era del monopoli; en la vida real un banco jamás te daría dinero, en cambio los políticios... eso es otra cosa"',                                                                             0,    0,   40,     NULL, NULL, NULL,         1, 200, 'falsear_cuentas'),

 ('BeberCerveza', 2, 'Beber cerveza durante el partido', '"Recarga energías, te espera un partido largo."',                                                                                                                                                                                     1000, 0,    0,     NULL, NULL, NULL,           1, 10, 'beber_cerveza'),
 ('HablarSpeaker', 2, 'Hablar con el Speaker del estadio', '"Apoya a tu equipo a lo grande: anímalo con los altavoces del propio estadio."',                                                                                                                                                    5000, 225,  3,     NULL, NULL, NULL,           1, 10, 'hablar_speaker'),
 ('CorearEstadio', 2, 'Corear en el estadio', '"Mueve las gradas durante el partido."',                                                                                                                                                                                                         0,    150,  0,     NULL, NULL, NULL,           1, 10, 'corear_estadio'),
 ('ArrojarMechero', 2, 'Arrojar objetos al campo', '"La mejor defensa contra un lanzamiento de falta del rival. Molesta al jugador con un puntero láser o arroja mecheros al portero en un córner"',                                                                                            0,    250,  0,     NULL, NULL, NULL,           1, 10, 'arrojar_mechero'),
 ('RetransmitirRRSS', 2, 'Retransmitir el partido por las redes sociales', '"Ocúpate de que el partido tenga repercusión."',                                                                                                                                                                    0,    55,   1,     NULL, NULL, NULL,           1, 10, 'retransmitir_redes_sociales'),
 ('DoblarApuesta', 2, 'Doblar la apuesta durante el partido', '"Seguramente hoy sea tu día de suerte"',                                                                                                                                                                                         7500,  0,    0,     NULL, NULL, NULL,           1, 10, 'doblar_apuesta'), 
 ('TumultoGradas', 2, 'Participar en un tumulto en las gradas', '"¡¡¡PELEA!!! ¡¡¡PELEA!!! ¡¡¡PELEA!!!"',                                                                                                                                                                                        0,    265,  0,     NULL, NULL, NULL,           1, 10, 'pelea_aficiones'), 
 ('EntrevistaPartido', 2, 'Entrevistar el partido', '"Ser el periodista durante el partido"',                                                                                                                                                                                                   0,    200,  50,    NULL, NULL, NULL,           1, 10, 'entrevista_descanso'),

 ('ContactarYakuza', 3, 'Contactar con la Yakuza japonesa', '"¿Te creías importante? No existe ningún tiburón de los negocios fuera de su alcance."',                                                                                                                                           300000,  8000,  100,   NULL, NULL, NULL,  1, NULL, 'contactos_yakuza'),                     
 ('EscribirBlog', 3, 'Escribir blog de opinión', '"Al principio tendrás 2 visitas, la tuya y la de tu madre... pero si trabajas en él a diário con paciencia podrías convertir tu voz en un referente"',                                                                                        3500,    250,   2,     NULL, NULL, NULL,  1, NULL, 'escribir_blog'),
 ('Ascender', 3, 'Ascender en el trabajo', '"A más dinero, más acciones podrás financiar."',                                                                                                                                                                                                    0,       0,     75,    NULL, NULL, NULL,  1, NULL, 'ascender_trabajo'),
 ('EquipamientoHeroe', 3, 'Equipamiento de super héroe', '"Hata a Iron Man le gusta el futbol, ¡SEAH!!!"',                                                                                                                                                                                      1000000, 25000, 1000,  NULL, NULL, NULL,  1, NULL, 'equipamiento_super_heroe');


TRUNCATE `participaciones`;


TRUNCATE `partidos`;
/*INSERT INTO `partidos` (`equipos_id_equipo_1`, `equipos_id_equipo_2`, `hora`, `cronica`, `turno`, `ambiente`, `aforo_local`, `aforo_visitante`, `nivel_local`, `nivel_visitante`, `ofensivo_local`, `defensivo_local`, `ofensivo_visitante`, `defensivo_visitante`) VALUES
 -- Rojos vs. Verdes: ganaron los Rojos
 -- Negros vs. Blancos: ganaron los Negros
 (1, 2, 1, 'Rojos(3) - Verdes (1); Lorem ipsum dolor sit amet, Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint est laborum.', 13, 0, 100, 100, 1, 1, 1, 1, 1, 1),
 (3, 4, 1, 'Negros(1) - Blancos (0); Lorem ipsum dolor sit amet, Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint est laborum.', 13, 0, 100, 100, 1, 1, 1, 1, 1, 1),
 -- Rojos vs. Negros: ganaron los Rojos
 -- Verdes vs. Blancos: empate
 (1, 3, 50, 'Rojos(2) - Negros (1); Lorem ipsum dolor sit amet, Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint est laborum.', 13, 0, 100, 100, 1, 1, 1, 1, 1, 1),
 (2, 4, 50, 'Verdes(0) - Blancos(0); Lorem ipsum dolor sit amet, Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint est laborum.', 13, 0, 100, 100, 1, 1, 1, 1, 1, 1),
 -- Rojos vs. Blancos: empate
 -- Negros vs. Verdes: empate
 (1, 4, 100, 'Rojos (3) - Blancos (3); Lorem ipsum dolor sit amet, Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint est laborum.', 13, 0, 100, 100, 1, 1, 1, 1, 1, 1),
 (3, 2, 100, 'Negros (1) - Verdes (1); Lorem ipsum dolor sit amet, Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint est laborum.', 13, 0, 100, 100, 1, 1, 1, 1, 1, 1),
 -- Verdes vs. Rojos: PROXIMO PARTIDO
 -- Blancos vs. Negros: PROXIMO PARTIDO
 (2, 1, 150, NULL, 0, 10, 100, 1000, 1, 1, 1, 1, 1, 1),
 (4, 3, 150, NULL, 0, 55, 1000, 100, 1, 6, 6, 0, 1, 4),
 -- Negros vs. Rojos
 -- Blancos vs. Verdes
 (3, 1, 200, NULL, 0, 600, 10, 10, 6, 1, 1, 7, 1, 7),
 (4, 2, 200, NULL, 0, 7, 1000, 140, 2, 7, 1, 1, 1, 1),
 -- Blancos vs. Rojos
 -- Verdes vs. Negros
 (4, 1, 250, NULL, 0, 150, 160, 3457, 9, 9, 0, 1, 7, 0),
 (2, 3, 250, NULL, 0, 200, 125, 173, 4, 2, 4, 7, 3, 9),
 -- Partido para comprobar si los datos son reintroducidos correctamente
 (1, 2, 99999999999, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);*/

-- -------------------------------------------------------------------------------------------------------
 -- Recursos iniciales
 -- --------------------------------------------------------------------------------------------------------
 --                 dinero dinero_gen influencias influencias_max influencias_gen animo animo_max animo_gen
 -- ultras:          2000       5           2           2               1           300     400     15  
 -- movedoras:       600        2           11          12              3           150     250     9
 -- empresarios:     5000       16          6           8               2           30      50      1
 -- ---------------------------------------------------------------------------------------------------------
TRUNCATE `recursos`;

 -- ----------------------
 -- Recuento de jugadores
 -- ----------------------
 -- empresarios: 3
 -- movedoras: 3
 -- ultras: 4
 -- ----------------------
TRUNCATE `usuarios`;

 SET FOREIGN_KEY_CHECKS = 1; 

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;