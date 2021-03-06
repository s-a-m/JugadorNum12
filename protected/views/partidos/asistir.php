<?php
	Helper::registerStyleFile('partido');

	/*
	Yii::app()->clientScript->registerLinkTag(
		'stylesheet/less', 'text/css', 
		Yii::app()->request->baseUrl . '/less/partido.less'
	);
	*/
	Yii::app()->clientScript->registerScriptFile(Yii::app()->BaseUrl.'/js/scriptsPartido.js');
?>
<script type="text/javascript">
	if (!window.partido) {
		window.partido = {};
	}

	if (!window.info) {
		window.info = {}
	}

	window.acciones = {
<?php
	$now = time();
	foreach ($l_acciones as $a=>$acc):
	$until = $l_desbl[$a]->fin_del_cooldown;
?>
		'<?php echo $acc->id_habilidad ?>': {
			ajax: false,
			enabled: <?php echo ($until <= $now) ? 'true' : 'false'; ?>,
			until: <?php echo $until*1000; ?>,
			cooldown: <?php echo ($until-$now)*1000; ?>
		},
<?php endforeach ?>
	};

	$.extend(partido, {
		id: <?php echo $partido->id_partido ?>,
		tiempo: <?php echo $partido->tiempoRestantePartido() ?>,
		tiempoTurno: <?php echo $partido->tiempoRestanteTurno() ?>,
		golesLocal: <?php echo $partido->goles_local ?>,
		golesVisit: <?php echo $partido->goles_visitante ?>,
		turno: <?php echo $partido->turno ?>,
		estado: <?php echo $partido->estado ?>,
		equipos: {
			local: {
				token: '<?php echo $partido->local->token ?>' 
			},
			visitante: {
				token: '<?php echo $partido->visitante->token ?>'
			}
		},
		moral_local: <?php echo $partido->moral_local ?>,
		moral_visitante: <?php echo $partido->moral_visitante ?>,

		ofensivo_local: <?php echo $partido->ofensivo_local ?>,
		ofensivo_visitante: <?php echo $partido->ofensivo_visitante ?>,

		defensivo_local: <?php echo $partido->defensivo_local ?>,
		defensivo_visitante: <?php echo $partido->defensivo_visitante ?>,
	});

	window.info.turnos = {
		inicial: <?php echo Partido::PRIMER_TURNO ?>,
		descanso: <?php echo Partido::TURNO_DESCANSO ?>,
		final: <?php echo Partido::ULTIMO_TURNO ?>
	}

	//Actualización de recursos por Ajax
	function actRecursosAj () {
		$.get(baseUrl + '/partidos/actrecursos?id_usuario=' + <?php echo Yii::app()->user->usIdent; ?>, 
	        function(data,status) {
	        	var json = JSON.parse(data);
	          	if (json.codigo == 1) {
	          		$("#progressbar-label-dinero").text(json.dinero);
	          		$("#progressbar-label-animo").text(json.animo+"/"+json.animo_max);
	          		$("#progressbar-label-influencias").text(json.influencias+"/"+json.influencias_max);
	          	}
	        }
	    );
	}
	var intervaloRec = window.setInterval(actRecursosAj, 30000);
</script>

<div id="partido-dibujo" class="inner-block">
	<img id="partido-dibujo-imagen" alt="Imagen del partido" style="display: none"/>
</div>

<!-- Marcador e información de los equipos y el estadio -->
<div id="partido-marcador" class="inner-block">
	<!-- Información general -->
	<div id="partido-marcador-general">
		<div id="partido-goles">
			<span id="partido-goles-local"><?php echo $partido->goles_local ?></span>
			&nbsp;&ndash;&nbsp;
			<span id="partido-goles-visit"><?php echo $partido->goles_visitante ?></span>
		</div>
		<!--<div id="partido-ambiente"><?php echo $partido->ambiente ?></div>-->
		<div id="partido-turnos">
<?php for ($t = Partido::PRIMER_TURNO + 1; $t <= Partido::ULTIMO_TURNO; $t++ ):
	if ( $t < Partido::TURNO_DESCANSO ) {
		$pos = -1;
	} else if ($t == Partido::TURNO_DESCANSO ) {
		$pos = 0;
	} else {
		$pos = 1;
	}
?>
			<div id="partido-turno-<?php echo $t ?>" class="turno turno-<?php
				echo ($pos < 0 ? 'pre' : ($pos > 0 ? 'post' : 'desc'))
			?> turno-<?php
				echo ($t < $partido->turno ? 'anterior' : ($t > $partido->turno ? 'siguiente' : 'actual'))
			?>"><?php
				echo ($pos < 0 ? $t : ($pos > 0 ? $t-1 : 'D'))
			?></div>
<?php endfor ?>
		</div>
		<div id="partido-tiempo"><?php
			$trp = $partido->tiempoRestantePartido();
			$s = $trp%60;
			$m = (int)($trp/60);
			echo ($m<10 ? '0'.$m : $m) . ':' . ($s<10 ? '0'.$s : $s);
		?></div>
	</div>

	<!-- Información del equipo local -->
	<div id="partido-equipo-local">
		<!--<div class="equipo-info equipo-nombre"><?php echo $eqLoc->nombre ?></div>-->
		<div class="equipo-info equipo-escudo"><img src="<?php
			echo Yii::app()->BaseUrl . '/images/escudos/96px/' . $eqLoc->token . '.png' ?>" height="96"/></div>
		<div class="equipo-info equipo-nivel"><?php echo $eqLoc->nivel_equipo ?></div>
		<!--<div class="equipo-info equipo-aforo"><?php echo $partido->aforo_local ?> asistentes</div>-->
	</div>

	<!-- Información del equipo visitante -->
	<div id="partido-equipo-visit">
		<!--<div class="equipo-info equipo-nombre"><?php echo $eqVis->nombre ?></div>-->
		<div class="equipo-info equipo-escudo"><img src="<?php
			echo Yii::app()->BaseUrl . '/images/escudos/96px/' . $eqVis->token . '.png' ?>" height="96" /></div>
		<div class="equipo-info equipo-nivel"><?php echo $eqVis->nivel_equipo ?></div>
		<!--<div class="equipo-info equipo-aforo"><?php echo $partido->aforo_visitante ?> asistentes</div>-->
	</div>

	<div class="clear"></div>
	<div id="datos-aforo" class="barra-datos-cont">
		<div class="datos-num-local textcolor-<?php echo $partido->local->token ?>"><?php echo $partido->aforo_local ?></div>
		<div class="datos-title">Aforo</div>
		<div class="datos-num-visit textcolor-<?php echo $partido->visitante->token ?>"><?php echo $partido->aforo_visitante ?></div>

		<?php
			$afl = $partido->aforo_local;
			$afv = $partido->aforo_visitante;
			$afm = $partido->local->aforo_max;
			$pl = (double) $afl / $afm;
			$pv = (double) $afv / $afm;
		?>
		<div class="datos-barra-centro"></div>
		<div class="datos-barra-local bgcolor-<?php echo $partido->local->token ?>" style="width: <?php echo $pl*100 ?>%"></div>
		<div class="datos-barra-visit bgcolor-<?php echo $partido->visitante->token ?>" style="width: <?php echo $pv*100 ?>%"></div>
	</div>

	<!-- Barras de moral -->
	<div id="datos-morales" class="barra-datos-cont">
		<div class="datos-num-local textcolor-<?php echo $partido->local->token ?>"><?php echo $partido->moral_local ?></div>
		<div class="datos-title">Moral</div>
		<div class="datos-num-visit textcolor-<?php echo $partido->visitante->token ?>"><?php echo $partido->moral_visitante ?></div>

		<?php
			$dm = $partido->moral_local - $partido->moral_visitante;
			$dmp = (double) max(min($dm / 300, 1), -1);
			$mpl = 0.5 + ($dmp*0.48);
			$mpv = 0.5 - ($dmp*0.48);
		?>

		<div class="datos-barra-centro-izq"></div><div class="datos-barra-centro-dch"></div>
		<div class="datos-barra-local bgcolor-<?php echo $partido->local->token ?>" style="width: <?php echo $mpl*100 ?>%"></div>
		<div class="datos-barra-visit bgcolor-<?php echo $partido->visitante->token ?>" style="width: <?php echo $mpv*100 ?>%"></div>
	</div>

	<!-- Barras de índice ofensivo -->
	<div id="datos-ofensivos" class="barra-datos-cont">
		<div class="datos-num-local textcolor-<?php echo $partido->local->token ?>"><?php echo $partido->ofensivo_local ?></div>
		<div class="datos-title">Ofensivo</div>
		<div class="datos-num-visit textcolor-<?php echo $partido->visitante->token ?>"><?php echo $partido->ofensivo_visitante ?></div>

		<?php
			$opl = 0.5 * $partido->ofensivo_local / 25;
			$opv = 0.5 * $partido->ofensivo_visitante / 25;
		?>
		<div class="datos-barra-centro-izq"></div><div class="datos-barra-centro-dch"></div>
		<div class="datos-barra-local bgcolor-<?php echo $partido->local->token ?>" style="width: <?php echo $opl*100 ?>%"></div>
		<div class="datos-barra-visit bgcolor-<?php echo $partido->visitante->token ?>" style="width: <?php echo $opv*100 ?>%"></div>
	</div>

	<!-- Barras de índice defensivo -->
	<div id="datos-defensivos" class="barra-datos-cont">
		<div class="datos-num-local textcolor-<?php echo $partido->local->token ?>"><?php echo $partido->defensivo_local ?></div>
		<div class="datos-title">Defensivo</div>
		<div class="datos-num-visit textcolor-<?php echo $partido->visitante->token ?>"><?php echo $partido->defensivo_visitante ?></div>

		<?php
			$dpl = 0.5 * min($partido->defensivo_local, 25) / 25;
			$dpv = 0.5 * min($partido->defensivo_visitante, 25) / 25;
		?>
		<div class="datos-barra-centro-izq"></div><div class="datos-barra-centro-dch"></div>
		<div class="datos-barra-local bgcolor-<?php echo $partido->local->token ?>" style="width: <?php echo $dpl*100 ?>%"></div>
		<div class="datos-barra-visit bgcolor-<?php echo $partido->visitante->token ?>" style="width: <?php echo $dpv*100 ?>%"></div>
	</div>
</div>

<div id="partido-info" class="inner-block">
	<div id="tabs-mensaje"></div>
	<ul id="partido-info-tabs">
		<li><a href="#partido-info-campo">Partido</a></li>
		<li><a href="#partido-info-acciones">Acciones</a></li>
		<li><a href="#partido-info-chat">Chat</a></li>
		<!--<li id="tab-datos"><a href="#partido-info-datos">Datos</a></li>-->
		<li><a href="#partido-info-cronica">Cronica</a></li>
	</ul>
	<div id="partido-info-campo" class="partido-info-content">
		<div id="js-campo"></div>
	</div>

<!--	<div id="partido-info-datos" class="partido-info-content">


		<div id="datos-titulo-local" class="barra datos-titulo">
			<?php echo $partido->local->nombre ?>
			<span class="mini">(Equipo Local)</span>
		</div>
		<div id="datos-titulo-visit" class="barra datos-titulo">
			<span class="mini">(Equipo Visitante)</span>
			<?php echo $partido->visitante->nombre ?>
		</div>

		<div class="clear"></div>

		<div id="barra-aforo" class="barra">
			Aforo
			<div id="barra-aforo-local" class="barra-aforo barra-<?php echo $partido->local->token ?>" style="width: <?php echo ((double) $partido->aforo_local / $partido->local->aforo_max)*100 ?>%"><?php echo $partido->aforo_local ?></div>
			<div id="barra-aforo-visit" class="barra-aforo barra-<?php echo $partido->visitante->token ?>" style="width: <?php echo ((double) $partido->aforo_visitante / $partido->local->aforo_max)*100 ?>%"><?php echo $partido->aforo_visitante ?></div>
		</div>

		<div id="partido-info-datos-numeritos">
			<b>Turno: </b><?php echo $partido->turno ?></br>
			<b>Estado: </b><?php echo $partido->estado ?></br>
	        <b>Ambiente: </b><?php echo $partido->ambiente ?></br>
	        <b>Nivel local: </b><?php echo $partido->nivel_local ?></br>
	  		<b>Nivel visitante: </b><?php echo $partido->nivel_visitante ?></br>
	  		<b>Indice ofensivo local: </b><?php echo $partido->ofensivo_local ?></br>
	  		<b>Indice ofensivo visitante: </b><?php echo $partido->ofensivo_visitante ?></br>
	  		<b>Indice defensivo local: </b><?php echo $partido->defensivo_local ?></br>
	  		<b>Indice defensivo visitante: </b><?php echo $partido->defensivo_visitante ?></br>
	  		<b>Aforo local: </b><?php echo $partido->aforo_local ?></br>
	  		<b>Aforo visitante: </b><?php echo $partido->aforo_visitante ?></br>
	  		<b>Moral local: </b><?php echo $partido->moral_local ?></br>
	  		<b>Moral visitante: </b><?php echo $partido->moral_visitante ?></br>
  		</div>
	</div>
-->
	<div id="partido-info-cronica" class="partido-info-content">
		<pre id="pre-c-p"><?php echo $partido->cronica ?></pre>
	</div>
	<div id="partido-info-chat" class="partido-info-content">
<?php 
    $this->widget('YiiChatWidget', array(
        'chat_id'    => $partido->id_partido,                   // a chat identificator
        'identity'   => Yii::app()->user->usIdent,   // the user
        'selector'   => '#partido-info-chat',   // were it will be inserted
        'minPostLen' => 1,                    // min and
        'maxPostLen' => 200,                   // max string size for post
        'model'      => new ChatHandler(),    // the class handler.
        'data'       => 'any data',                 // data passed to the handler
        // success and error handlers, both optionals.
        'onSuccess'  => new CJavaScriptExpression(
            "function(code, text, post_id){}"),
        'onError'    => new CJavaScriptExpression(
            "function(errorcode, info){}"),
    ));
?>
	</div>

	<div id="partido-info-acciones" class="partido-info-content">
		<!--<h2>Acciones de partido</h2> 
		<h3>(pulsa para ejecutar)</h3>-->
<?php 
	$now = time();
	foreach ($l_acciones as $a=>$acc):
	$until = $l_desbl[$a]->fin_del_cooldown;
?>		
		<div id="accion-<?php echo $acc->id_habilidad ?>" class="accion accion-<?php echo $acc->id_habilidad ?><?php if ($until > $now) echo 'disabled';?>" onclick="ejecutarAP(<?php echo $acc->id_habilidad; ?>)">
			<div class="accion-nombre"><?php echo $acc->nombre; ?></div>
			<img class="accion-icono" title="<?php echo $acc->nombre; ?>" alt="<?php echo $acc->nombre; ?>" src="<?php echo Yii::app()->BaseUrl ?>/images/habilidades/<?php echo $acc->token; ?>.png" />
			<div class="accion-recursos">
				<div class="accion-recurso">
					<div class="icon"><img class="dinero-icono" src="<?php echo Yii::app()->BaseUrl . '/images/menu/recurso_dinero.png' ?>"
					    alt="Dinero"/></div><div class="cant"><?php echo $acc['dinero'] ?></div>
				</div>
				<div class="accion-recurso">
					<div class="icon"><img class="animo-icono" src="<?php echo Yii::app()->BaseUrl . '/images/menu/recurso_animo.png' ?>"
					     alt="&Aacute;nimo"/></div><div class="cant"><?php echo $acc['animo'] ?></div>
				</div>
				<div class="accion-recurso">
					<div class="icon"><img class="influencias-icono" src="<?php echo Yii::app()->BaseUrl . '/images/menu/recurso_influencia.png' ?>"
					     alt="Influencias"/></div><div class="cant"><?php echo $acc['influencias'] ?></div>
				</div>
			</div>
			<div id="cooldown-<?php echo $acc->id_habilidad ?>" class="cooldown"><? if ($until > $now) echo ($until - $now); ?></div>
			<div class="clear"></div>
		</div>
<?php endforeach ?>
	</div>
</div>