<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

$repos = update::listRepo();
$keys = array('api', 'apipro', 'apitts', 'dns::token', 'market::allowDNS', 'ldap::enable', 'apimarket');
foreach ($repos as $key => $value) {
	$keys[] = $key . '::enable';
}
global $JEEDOM_INTERNAL_CONFIG;
$configs = config::byKeys($keys);
user::isBan();
$productName = config::byKey('product_name');
?>

<div class="row row-overflow">
	<div class="col-xs-12" id="config">
		<div class="input-group" style="margin-bottom:5px;">
			<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchConfig">
			<div class="input-group-btn">
				<a id="bt_resetConfigSearch" class="btn" style="width:30px"><i class="fas fa-times"></i> </a>
			</div>
			<div class="input-group-btn">
				<a id="bt_saveGeneraleConfig" class="btn btn-success roundedRight" type="button"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a>
			</div>
		</div>
		<div>
			<form class="form-horizontal">
				<div id="searchResult"></div>
			</form>
		</div>

		<ul class="nav nav-tabs nav-primary" role="tablist" id="tablist">
			<li role="presentation" class="active"><a data-target="#generaltab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-wrench" title="{{Général}}"></i><span> {{Général}}</span></a></li>
			<li role="presentation"><a data-target="#interfacetab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-laptop" title="{{Interface}}"></i><span> {{Interface}}</span></a></li>
			<li role="presentation"><a id="bt_networkTab" data-target="#networktab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-rss" title="{{Réseaux}}"></i><span> {{Réseaux}}</span></a></li>
			<li role="presentation"><a data-target="#logtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="far fa-file" title="{{Logs}}"></i><span> {{Logs}}</span></a></li>
			<li role="presentation"><a data-target="#summarytab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-table" title="{{Résumés}}"></i><span> {{Résumés}}</span></a></li>
			<li role="presentation"><a data-target="#eqlogictab" aria-controls="profile" role="tab" data-toggle="tab"><i class="icon divers-svg" title="{{Equipements}}"></i><span> {{Equipements}}</span></a></li>
			<li role="presentation"><a data-target="#repporttab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-newspaper" title="{{Rapports}}"></i><span> {{Rapports}}</span></a></li>
			<li role="presentation"><a data-target="#graphlinktab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-sitemap" title="{{Liens}}"></i><span> {{Liens}}</span></a></li>
			<li role="presentation"><a data-target="#interacttab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-microphone" title="{{Interactions}}"></i><span> {{Interactions}}</span></a></li>
			<li role="presentation"><a data-target="#securitytab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-shield-alt" title="{{Securité}}"></i><span> {{Securité}}</span></a></li>
			<li role="presentation"><a data-target="#updatetab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-credit-card" title="{{Mises à jour}}"></i><span> {{Mises à jour/Market}}</span></a></li>
			<li role="presentation"><a data-target="#cachetab" aria-controls="profile" role="tab" data-toggle="tab"><i class="far fa-hdd" title="{{Cache}}"></i><span> {{Cache}}</span></a></li>
			<li role="presentation"><a data-target="#apitab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-key" title="{{API}}"></i><span> {{API}}</span></a></li>
			<li role="presentation"><a data-target="#ostab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fas fa-terminal" title="{{OS/DB}}"></i><span> {{OS/DB}}</span></a></li>
		</ul>

		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="generaltab">
				<br>
				<form class="form-horizontal col-lg-6">
					<fieldset>
						<legend>{{Général}}</legend>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Nom de votre}} <?php echo $productName; ?>
								<sup><i class="fas fa-question-circle" tooltip="{{Utilisé notamment par le market}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="text" class="configKey form-control" data-l1key="name">
								<label class="checkbox-inline"><input type="checkbox" class="configKey form-control" data-l1key="display_name_login">{{Afficher sur la page de connexion}}</label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Langue}}
								<sup><i class="fas fa-question-circle" tooltip="{{Sélection de la langue d'affichage}}"></i></sup></label>
							<div class="col-md-6 col-xs-8">
								<select class="form-control configKey" data-l1key="language" data-reload="1">
									<option value="fr_FR">{{Français}}</option>
									<option value="en_US">{{Anglais}}</option>
									<option value="de_DE">{{Allemand}}</option>
									<option value="es_ES">{{Espagnol}}</option>
									<option value="it_IT">{{Italien (pas de support)}}</option>
									<option value="pt_PT">{{Portugais (pas de support)}}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Moteur TTS}}
								<sup><i class="fas fa-question-circle" tooltip="{{Sélection du moteur text-to-speech}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<select class="form-control configKey" data-l1key="tts::engine">
									<option value="pico">Pico</option>
									<option value="espeak">Espeak</option>
									<?php
									foreach ((plugin::listPlugin(true)) as $plugin) {
										if (!$plugin->getHasTtsEngine()) {
											continue;
										}
										echo '<option value="plugin::' . $plugin->getId() . '">{{Plugin}} ' . $plugin->getName() . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<hr class="hrPrimary">
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Version}}
								<sup><i class="fas fa-question-circle" tooltip="{{Version de}} <?php echo $productName; ?>"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<span class="label label-info"><?php echo jeedom::version(); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Système}}
								<sup><i class="fas fa-question-circle" tooltip="{{Type de matériel utilisé}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<span class="label label-info"><?php echo jeedom::getHardwareName() ?></span>
								<a class="btn btn-sm btn-default pull-right" id="bt_resetHardwareType" tooltip="{{Rafraîchir}}"><i class="fas fa-sync"></i></a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Clé d'installation}}
								<sup><i class="fas fa-question-circle" tooltip="{{Identifie votre}} <?php echo $productName; ?> {{sur le market}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<span class="label label-info" style="width:calc(100% - 40px);"><?php echo jeedom::getHardwareKey() ?></span>
								<a class=" btn btn-sm btn-default pull-right" id="bt_resetHwKey" tooltip="{{Remise à zéro}}"><i class=" fas fa-undo-alt"></i></a>
							</div>
						</div>
						<hr class="hrPrimary">
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Fuseau horaire}}
								<sup><i class="fas fa-question-circle" tooltip="{{Sélection du fuseau horaire}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<select class="form-control configKey roundedLeft" data-l1key="timezone">
										<option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
										<option value="Pacific/Tahiti">(GMT-10:00) Pacific/Tahiti</option>
										<option value="America/Adak">(GMT-10:00) Hawaii-Aleutian</option>
										<option value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
										<option value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands</option>
										<option value="Pacific/Gambier">(GMT-09:00) Gambier Islands</option>
										<option value="America/Anchorage">(GMT-09:00) Alaska</option>
										<option value="America/Ensenada">(GMT-08:00) Tijuana, Baja California</option>
										<option value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
										<option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
										<option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
										<option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
										<option value="America/Dawson_Creek">(GMT-07:00) Arizona</option>
										<option value="America/Belize">(GMT-06:00) Saskatchewan, Central America</option>
										<option value="America/Cancun">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
										<option value="Chile/EasterIsland">(GMT-06:00) Easter Island</option>
										<option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
										<option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
										<option value="America/Havana">(GMT-05:00) Cuba</option>
										<option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
										<option value="America/Caracas">(GMT-04:30) Caracas</option>
										<option value="America/Santiago">(GMT-04:00) Santiago</option>
										<option value="America/La_Paz">(GMT-04:00) La Paz</option>
										<option value="Atlantic/Stanley">(GMT-04:00) Faukland Islands</option>
										<option value="America/Campo_Grande">(GMT-04:00) Brazil</option>
										<option value="America/Goose_Bay">(GMT-04:00) Atlantic Time (Goose Bay)</option>
										<option value="America/Glace_Bay">(GMT-04:00) Atlantic Time (Canada)</option>
										<option value="America/Guadeloupe">(GMT-04:00) Guadeloupe</option>
										<option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
										<option value="America/Araguaina">(GMT-03:00) UTC-3</option>
										<option value="America/Montevideo">(GMT-03:00) Montevideo</option>
										<option value="America/Miquelon">(GMT-03:00) Miquelon, St. Pierre</option>
										<option value="America/Godthab">(GMT-03:00) Greenland</option>
										<option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
										<option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
										<option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
										<option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
										<option value="Atlantic/Azores">(GMT-01:00) Azores</option>
										<option value="Europe/Belfast">(GMT) Greenwich Mean Time : Belfast</option>
										<option value="Europe/Dublin">(GMT) Greenwich Mean Time : Dublin</option>
										<option value="Europe/Lisbon">(GMT) Greenwich Mean Time : Lisbon</option>
										<option value="Europe/London">(GMT) Greenwich Mean Time : London</option>
										<option value="Africa/Abidjan">(GMT) Monrovia, Reykjavik</option>
										<option value="Africa/Casablanca">(GMT) Greenwich Mean Time : Casablanca</option>
										<option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
										<option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
										<option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
										<option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
										<option value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
										<option value="Asia/Beirut">(GMT+02:00) Beirut</option>
										<option value="Africa/Cairo">(GMT+02:00) Cairo</option>
										<option value="Asia/Gaza">(GMT+02:00) Gaza</option>
										<option value="Africa/Blantyre">(GMT+02:00) Harare, Pretoria</option>
										<option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
										<option value="Europe/Minsk">(GMT+02:00) Minsk</option>
										<option value="Asia/Damascus">(GMT+02:00) Syria</option>
										<option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
										<option value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
										<option value="Asia/Tehran">(GMT+03:30) Tehran</option>
										<option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
										<option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
										<option value="Asia/Kabul">(GMT+04:30) Kabul</option>
										<option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option>
										<option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
										<option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
										<option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
										<option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
										<option value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk</option>
										<option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
										<option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
										<option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
										<option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
										<option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
										<option value="Australia/Perth">(GMT+08:00) Perth</option>
										<option value="Australia/Eucla">(GMT+08:45) Eucla</option>
										<option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
										<option value="Asia/Seoul">(GMT+09:00) Seoul</option>
										<option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
										<option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
										<option value="Australia/Broken_Hill">(GMT+09:30) Broken Hill</option>
										<option value="Australia/Darwin">(GMT+09:30) Darwin</option>
										<option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
										<option value="Australia/Hobart">(GMT+10:00) Hobart</option>
										<option value="Australia/Lindeman">(GMT+10:00) Lindeman</option>
										<option value="Australia/Melbourne">(GMT+10:00) Melbourne</option>
										<option value="Australia/Sydney">(GMT+10:00) Sydney</option>
										<option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
										<option value="Australia/Lord_Howe">(GMT+10:30) Lord Howe Island</option>
										<option value="Etc/GMT-11">(GMT+11:00) Solomon Is., New Caledonia</option>
										<option value="Asia/Magadan">(GMT+11:00) Magadan</option>
										<option value="Pacific/Norfolk">(GMT+11:30) Norfolk Island</option>
										<option value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka</option>
										<option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
										<option value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
										<option value="Pacific/Chatham">(GMT+12:45) Chatham Islands</option>
										<option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
										<option value="Pacific/Kiritimati">(GMT+14:00) Kiritimati</option>
									</select>
									<span class="input-group-btn">
										<a class="btn btn-primary form-control roundedRight" id="bt_forceSyncHour" tooltip="{{Forcer la synchronisation de l'heure}}"><i class="fas fa-clock"></i></a>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Serveur de temps optionnel}}
								<sup><i class="fas fa-question-circle" tooltip="{{Permet d'ajouter un serveur de temps pour la synchronisation de l'heure}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="text" class="configKey form-control" data-l1key="ntp::optionalServer">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Ignorer la vérification de l'heure}}
								<sup><i class="fas fa-question-circle" tooltip="{{Cochez la case pour ne pas prendre en compte l'heure du système}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="checkbox" class="configKey" data-l1key="ignoreHourCheck">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Dernière date connue}}
								<sup><i class="fas fa-question-circle" tooltip="{{Dernière date système connue par}} <?php echo $productName; ?>"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<span class="label label-info"><?php echo cache::byKey('hour')->getDatetime() ?></span>
								<a class="btn btn-sm btn-default pull-right" id="bt_resetHour" tooltip="{{Remise à zéro}}"><i class=" fas fa-undo-alt"></i></a>
							</div>
						</div>

						<hr class="hrPrimary">
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Core js (dev)}}
								<sup><i class="fas fa-question-circle warning" tooltip="{{Ne charge pas jQuery/Boostrap et leurs librairies (Attention : Les plugins installés doivent supporter ce mode)}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="checkbox" class="configKey form-control" data-l1key="core::jqueryless">
							</div>
						</div>
						<br>
					</fieldset>
				</form>

				<form class="form-horizontal col-lg-6">
					<fieldset>
						<legend>{{Coordonnées}}</legend>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{GPS}}
								<sup><i class="fas fa-question-circle" tooltip="{{Coordonnées GPS du bâtiment. De nombreux sites internet permettent de connaitre les coordonnées GPS d'une adresse}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Latitude}}</span>
									<input type="number" class="configKey form-control" data-l1key="info::latitude">
									<span class="input-group-addon">{{Longitude}}</span>
									<input type="number" class="configKey form-control roundedRight" data-l1key="info::longitude">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Postales}}
								<sup><i class="fas fa-question-circle" tooltip="{{Adresse postale du bâtiment}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Adresse}}</span>
									<input type="text" class="configKey form-control roundedRight" data-l1key="info::address">
								</div>
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{CP}}</span>
									<input type="text" class="configKey form-control" data-l1key="info::postalCode">
									<span class="input-group-addon">{{Ville}}</span>
									<input type="text" class="configKey form-control roundedRight" data-l1key="info::city">
								</div>
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Pays}}</span>
									<input type="text" class="configKey form-control roundedRight" data-l1key="info::stateCode" placeholder="FR">
								</div>
							</div>
						</div>

						<legend>{{Informations diverses}}</legend>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Altitude}}
								<sup><i class="fas fa-question-circle" tooltip="{{Altitude du bâtiment en mètres}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="number" class="configKey form-control" data-l1key="info::altitude">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Surface habitable}}
								<sup><i class="fas fa-question-circle" tooltip="{{Surface habitable du bâtiment en mètres carrés}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="number" class="configKey form-control" data-l1key="info::livingSpace">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Nombre d'occupants}}
								<sup><i class="fas fa-question-circle" tooltip="{{Nombre d'occupants du bâtiment}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="number" class="configKey form-control" data-l1key="info::nbOccupant">
							</div>
						</div>
					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="interfacetab">
				<br>
				<form class="form-horizontal col-lg-6">
					<fieldset>
						<legend>{{Thème}}</legend>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Desktop}}</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Principal}}</span>
									<select class="form-control configKey" data-l1key="jeedom_theme_main" data-reload="1">
										<?php
										foreach ((ls(__DIR__ . '/../../core/themes')) as $dir) {
											if (is_dir(__DIR__ . '/../../core/themes/' . $dir . '/desktop')) {
												echo '<option value="' . trim($dir, '/') . '">' . ucfirst(str_replace('core2019_', ' ', trim($dir, '/'))) . '</option>';
											}
										}
										?>
									</select>
									<span class="input-group-addon">{{Alternatif}}</span>
									<select class="form-control configKey roundedRight" data-l1key="jeedom_theme_alternate" data-reload="1">
										<?php
										foreach ((ls(__DIR__ . '/../../core/themes')) as $dir) {
											if (is_dir(__DIR__ . '/../../core/themes/' . $dir . '/desktop')) {
												echo '<option value="' . trim($dir, '/') . '">' . ucfirst(str_replace('core2019_', ' ', trim($dir, '/'))) . '</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Mobile}}</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Principal}}</span>
									<select class="form-control configKey" data-l1key="mobile_theme_color">
										<?php
										foreach ((ls(__DIR__ . '/../../core/themes')) as $dir) {
											if (is_dir(__DIR__ . '/../../core/themes/' . $dir . '/mobile')) {
												echo '<option value="' . trim($dir, '/') . '">' . ucfirst(str_replace('core2019_', ' ', trim($dir, '/'))) . '</option>';
											}
										}
										?>
									</select>
									<span class="input-group-addon">{{Alternatif}}</span>
									<select class="form-control configKey roundedRight" data-l1key="mobile_theme_color_night">
										<?php
										foreach ((ls(__DIR__ . '/../../core/themes')) as $dir) {
											if (is_dir(__DIR__ . '/../../core/themes/' . $dir . '/mobile')) {
												echo '<option value="' . trim($dir, '/') . '">' . ucfirst(str_replace('core2019_', ' ', trim($dir, '/'))) . '</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
						</div>
						<hr class="hrPrimary">
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Basculer de thème}}
							</label>
							<div class="col-md-6 col-xs-8">
								<label class="checkbox-inline"><input type="checkbox" class="configKey" data-l1key="theme_changeAccordingTime">{{Selon l'heure}}
									<sup><i class="fas fa-question-circle" tooltip="{{Basculer automatiquement de thème en fonction des horaires définis ci-dessous}}"></i></sup>
								</label>
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Principal de}}</span>
									<input type="text" class="configKey form-control in_timepicker" data-l1key="theme_start_day_hour">
									<span class="input-group-addon">{{à}}</span>
									<input type="text" class="configKey form-control in_timepicker" data-l1key="theme_end_day_hour">
									<span class="input-group-btn">
										<a id="bt_resetThemeCookie" class="btn btn-sm btn-primary form-control roundedRight" tooltip="{{Supprimer le cookie de thème}}"><i class="fas fa-eraser"></i></a>
									</span>
								</div>
								<label class="checkbox-inline"><input type="checkbox" class="configKey" data-l1key="mobile_theme_useAmbientLight">{{Selon la luminosité}}
									<sup><i class="fas fa-question-circle" tooltip="{{Basculer automatiquement de thème en fonction du capteur de luminosité sur mobile}}"></i></sup>
								</label>
							</div>
						</div>

						<legend>{{Affichage}}</legend>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Nombre de colonnes}}
								<sup><i class="fas fa-question-circle" tooltip="{{Nombre de colonnes selon la taille de l'écran (1 colonne = 1 objet)}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<select class="form-control configKey" data-l1key="dahsboard::column::size" data-reload="1">
									<option value="col-lg-3 col-md-4 col-sm-12">{{Grand écran = 4 colonnes, moyen = 3 colonnes}}</option>
									<option value="col-lg-4 col-md-6 col-sm-12">{{Grand écran = 3 colonnes, moyen = 2 colonnes}}</option>
									<option value="col-lg-6 col-md-12">{{Grand écran = 2 colonnes}}</option>
									<option value="col-sm-12">{{1 colonne}}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Options}}</label>
							<div class="col-md-6 col-xs-8">
								<label class="checkbox-inline"><input type="checkbox" class="configKey form-control" data-l1key="theme_displayAsTable" data-reload="1">{{Affichage tableau}}
									<sup><i class="fas fa-question-circle" tooltip="{{Affiche les pages du menu Outils et des plugins supportés en mode tableau}}"></i></sup>
								</label>
								<label class="checkbox-inline"><input type="checkbox" class="configKey form-control" data-l1key="interface::advance::coloredIcons" data-reload="1">{{Icônes colorées}}
									<sup><i class="fas fa-question-circle" tooltip="{{Coloration des icônes (modifiable par scénario: setColoredIcon => Coloration des icônes)}}"></i></sup>
								</label>
								<label class="checkbox-inline"><input type="checkbox" class="configKey form-control" data-l1key="disableMobileUi">{{Désactiver la version mobile}}
									<sup><i class="fas fa-question-circle" tooltip="{{La version mobile sera la même que la version desktop}}"></i></sup>
								</label>
								<label class="checkbox-inline"><input type="checkbox" class="configKey form-control" data-l1key="scenario::disableAutocomplete">{{Désactiver l'auto-complétion des scénarios}}</label>
							</div>
						</div>
						<hr class="hrPrimary">
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Notifications}}
								<sup><i class="fas fa-question-circle" tooltip="{{Position et durée d'affichage des notifications en secondes (0 = infini)}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Position}}</span>
									<select class="form-control configKey" data-l1key="interface::toast::position" data-reload="1">
										<option value="toast-top-left">{{En haut à gauche}}</option>
										<option value="toast-top-center">{{En haut au centre}}</option>
										<option value="toast-top-right">{{En haut à droite}}</option>
										<option value="toast-bottom-right">{{En bas à droite}}</option>
										<option value="toast-bottom-left">{{En bas à gauche}}</option>
									</select>
									<span class="input-group-addon">{{Durée}}</span>
									<input type="number" min="0" max="30" step="1" class="configKey form-control ispin roundedRight" data-l1key="interface::toast::duration" data-reload="1">
								</div>
							</div>
						</div>

						<legend>{{Personnalisation}}</legend>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Basique}}</label>
							<div class="col-md-6 col-xs-8">
								<label class="checkbox-inline"><input type="checkbox" class="configKey form-control" data-l1key="interface::advance::enable">{{Activer}}
									<sup><i class="fas fa-question-circle" tooltip="{{Cocher la case pour modifier les paramètres d'interface ci-dessous}}"></i></sup>
								</label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Transparence}}
								<sup><i class="fas fa-question-circle" tooltip="{{Transparence (Opacité) des tuiles et de certains éléments d'interface}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="number" min="0" max="1" step="0.1" class="configKey form-control ispin" data-l1key="css::background-opacity" data-reload="1">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Arrondi}}
								<sup><i class="fas fa-question-circle" tooltip="{{Arrondi des éléments d'interface (0 = pas d'arrondi)}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="number" min="0" max="1" step="0.1" class="configKey form-control ispin" data-l1key="css::border-radius" data-reload="1">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Désactiver les ombres}}</label>
							<div class="col-md-6 col-xs-8">
								<input type="checkbox" class="configKey form-control" data-l1key="widget::shadow" data-reload="1">
							</div>
						</div>
						<hr class="hrPrimary">
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Avancée}}</label>
							<div class="col-md-6 col-xs-8">
								<label class="checkbox-inline"><input type="checkbox" class="configKey form-control" data-l1key="enableCustomCss">{{Activer}}
									<sup><i class="fas fa-question-circle" tooltip="{{Cocher la case pour activer la personnalisation avancée}}"></i></sup>
								</label>
								<a class="btn btn-sm btn-warning pull-right" href="index.php?v=d&p=editor&type=custom"><i class="fas fa-pencil-alt"></i> {{Personnalisation avancée}}</a>
							</div>
						</div>
						<br>
					</fieldset>
				</form>
				<form class="form-horizontal col-lg-6">
					<fieldset>
						<legend>{{Tuiles}}</legend>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Pas}}
								<sup><i class="fas fa-question-circle" tooltip="{{Contraint la hauteur et la largeur des tuiles tous les x pixels}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Hauteur}}</span>
									<input type="number" min="1" step="1" max="300" class="configKey form-control ispin" data-l1key="widget::step::height" data-reload="1" placeholder="60">
									<span class="input-group-addon ">{{Largeur}}</span>
									<input type="number" min="1" step="1" max="300" class="configKey form-control ispin roundedRight" data-l1key="widget::step::width" data-reload="1" placeholder="80">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Marge}}
								<sup><i class="fas fa-question-circle" tooltip="{{Espace entre les tuiles en pixels}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="number" min="0" step="1" max="50" class="configKey form-control ispin" data-l1key="widget::margin" data-reload="1">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Options}}</label>
							<div class="col-md-6 col-xs-8">
								<label class="checkbox-inline"><input type="checkbox" class="configKey form-control" data-l1key="interface::advance::vertCentering" data-reload="1">{{Centrage vertical}}
									<sup><i class="fas fa-question-circle" tooltip="{{Centre verticalement le contenu des tuiles}}"></i></sup>
								</label>
								<label class="checkbox-inline"><input type="checkbox" class="configKey form-control" data-l1key="interface::advance::coloredcats" data-reload="1">{{Catégories colorées}}
									<sup><i class="fas fa-question-circle" tooltip="{{Colore le titre des tuiles en fonction de la catégorie}}"></i></sup>
								</label>
								<label class="checkbox-inline"><input type="checkbox" class="configKey form-control" data-l1key="interface::mobile::onecolumn" data-reload="1">{{Pleine largeur sur mobile}}
									<sup><i class="fas fa-question-circle" tooltip="{{Les tuiles prennent toute la largeur de l'écran en version mobile}}"></i></sup>
								</label>
							</div>
						</div>
						<hr class="hrPrimary">
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Réorganisation automatique}}</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Actions humaines}}
										<sup><i class="fas fa-question-circle" tooltip="{{Poids des actions humaines dans la réorganisation automatique des tuiles}}"></i></sup>
									</span>
									<input type="number" min="0" step="1" max="10" class="configKey form-control ispin roundedRight" data-l1key="autoreorder::weight_human_action">
								</div>
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Actions automatiques}}
										<sup><i class="fas fa-question-circle" tooltip="{{Poids des actions automatiques dans la réorganisation automatique des tuiles}}"></i></sup>
									</span>
									<input type="number" min="0" step="1" max="10" class="configKey form-control ispin roundedRight" data-l1key="autoreorder::weight_automation_action">
								</div>
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Consultation historique}}
										<sup><i class="fas fa-question-circle" tooltip="{{Poids des actions de consultation de l'historique dans la réorganisation automatique des tuiles}}"></i></sup>
									</span>
									<input type="number" min="0" step="1" max="10" class="configKey form-control ispin roundedRight" data-l1key="autoreorder::weight_history">
								</div>
							</div>
						</div>

						<legend>{{Images de fond}}</legend>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Afficher}}
								<sup><i class="fas fa-question-circle" tooltip="{{Cocher la case pour afficher les images de fond}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="checkbox" class="configKey" data-l1key="showBackgroundImg" data-reload="1">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Dashboard}}
								<sup><i class="fas fa-question-circle" tooltip="{{Image de fond pour les pages du Dashboard (En fonction des options de l'objet)}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="btn btn-sm btn-success btn-file roundedLeft">
										<i class="fas fa-file-upload"></i> {{Envoyer}}<input class="bt_uploadImage" type="file" name="file" accept="image/*" data-page="dashboard">
									</span>
									<a class="btn btn-sm btn-warning bt_removeBackgroundImage roundedRight" data-page="dashboard"><i class="fas fa-trash-alt"></i> {{Supprimer}}</a>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Analyse}}
								<sup><i class="fas fa-question-circle" tooltip="{{Image de fond pour les pages du menu Analyse}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="btn btn-sm btn-success btn-file roundedLeft">
										<i class="fas fa-file-upload"></i> {{Envoyer}}<input class="bt_uploadImage" type="file" name="file" accept="image/*" data-page="analysis">
									</span>
									<a class="btn btn-sm btn-warning bt_removeBackgroundImage roundedRight" data-page="analysis"><i class="fas fa-trash-alt"></i> {{Supprimer}}</a>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Outils}}
								<sup><i class="fas fa-question-circle" tooltip="{{Image de fond pour les pages du menu Outils}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="btn btn-sm btn-success btn-file roundedLeft">
										<i class="fas fa-file-upload"></i> {{Envoyer}}<input class="bt_uploadImage" type="file" name="file" accept="image/*" data-page="tools">
									</span>
									<a class="btn btn-sm btn-warning bt_removeBackgroundImage roundedRight" data-page="tools"><i class="fas fa-trash-alt"></i> {{Supprimer}}</a>
								</div>
							</div>
						</div>
						<hr class="hrPrimary">
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Opacité thème}}
								<sup><i class="fas fa-question-circle" tooltip="{{Opacité des images de fond en fonction du thème}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Light}}</span>
									<input type="number" min="0.1" max="1" step="0.05" class="configKey form-control ispin" data-l1key="interface::background::opacitylight" data-reload="1">
									<span class="input-group-addon">{{Dark}}</span>
									<input type="number" min="0.1" max="1" step="0.05" class="configKey form-control ispin roundedRight" data-l1key="interface::background::opacitydark" data-reload="1">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Flou}}
								<sup><i class="fas fa-question-circle" tooltip="{{Valeur de flou pour les images de fond sur les pages Dashboard}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="number" min="0" step="0.25" max="20" class="configKey form-control ispin" data-l1key="css::objectBackgroundBlur" data-reload="1">
							</div>
						</div>

					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="networktab">
				<br>
				<form class="form-horizontal col-xs-12">
					<fieldset>
						<legend>{{Accès interne}}</legend>
						<div class="form-group">
							<label class="col-lg-2 col-xs-4 control-label">{{Gestion automatique}}</label>
							<div class="col-xs-8">
								<label class="checkbox-inline"><input type="checkbox" class="configKey" data-l1key="network::disableInternalAuto">{{Désactiver}}
									<sup><i class="fas fa-question-circle" tooltip="{{Désactiver la gestion automatique de l'adresse d'accès interne}}"></i></sup>
								</label>
								<div class="input-group">
									<span class="input-group-addon roundedLeft">{{Interface}}</span>
									<span class="configKey hidden" data-l1key="network::internalAutoInterface"></span>
									<select class="roundedRight configKey form-control">
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 col-xs-4 control-label">{{Adresse}}
								<sup><i class="fas fa-question-circle" tooltip="{{Uniquement utilisée pour la communication interne avec}} <?php echo $productName; ?>"></i></sup>
							</label>
							<div class="col-xs-8">
								<div class="input-group">
									<select class="roundedLeft configKey form-control" data-l1key="internalProtocol">
										<option value="http://">{{HTTP}}</option>
									</select>
									<span class="input-group-addon">://</span>
									<input type="text" class="configKey form-control" data-l1key="internalAddr">
									<span class="input-group-addon">:</span>
									<input type="text" class="configKey form-control" data-l1key="internalPort">
									<span class="input-group-addon">/</span>
									<input type="text" class="configKey form-control roundedRight" data-l1key="internalComplement">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 col-xs-4 control-label">{{Interfaces}}
								<sup><i class="fas fa-question-circle" tooltip="{{Liste des interfaces d'accès interne}}"></i></sup>
							</label>
							<div class="col-xs-8">
								<table id="networkInterfacesTable" class="table table-condensed" style="margin-bottom: unset;">
									<thead>
										<tr>
											<th>{{Nom}}</th>
											<th>{{IP}}</th>
											<th>{{MAC}}</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>

						<legend>{{Accès externe}}</legend>
						<div class="form-group">
							<label class="col-lg-2 col-xs-4 control-label">{{Gestion automatique}}
							</label>
							<div class="col-xs-8">
								<label class="checkbox-inline"><input type="checkbox" class="configKey" data-l1key="network::disableMangement">{{Désactiver}}
									<sup><i class="fas fa-question-circle" tooltip="{{Désactiver la gestion automatique de l'adresse d'accès externe}}"></i></sup>
								</label>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 col-xs-4 control-label">{{Adresse}}</label>
							<div class="col-xs-8">
								<div class="input-group">
									<select class="roundedLeft configKey form-control" data-l1key="externalProtocol">
										<option value="http://">{{HTTP}}</option>
										<option value="https://">{{HTTPS}}</option>
									</select>
									<span class="input-group-addon">://</span>
									<input type="text" class="configKey form-control" data-l1key="externalAddr">
									<span class="input-group-addon">:</span>
									<input type="text" class="configKey form-control" data-l1key="externalPort">
									<span class="input-group-addon">/</span>
									<input type="text" class="configKey form-control roundedRight" data-l1key="externalComplement">
								</div>
							</div>
						</div>
						<?php
						foreach ($repos as $key => $value) {
							if (!isset($value['scope']['proxy']) || $value['scope']['proxy'] === false) {
								continue;
							}
							if ($configs[$key . '::enable'] == 0) {
								continue;
							}
							$div = '<div class="form-group">';
							$div .= '<label class="col-lg-2 col-xs-4 control-label">{{DNS}} ' . $value['name'] . '</label>';
							$div .= '<div class="col-xs-8">';
							if ($configs['dns::token'] == '') {
								$div .= '<div class="alert alert-warning">{{Cette fonctionnalité n\'est pas incluse avec votre service Pack (voir profil Market)}}</div>';
								$div .= '</div>';
								$div .= '</div>';
								echo $div;
								continue;
							}
							$div .= '<label class="checkbox-inline"><input type="checkbox" class="configKey" data-l1key="' . $key . '::allowDNS">{{Activer DNS}} ' . $productName . '</label>';
							$div .= '<div class="input-group">';
							$div .= '<span class="input-group-addon roundedLeft">{{Mode}}</span>';
							$div .= '<select class="configKey form-control roundedRight" data-l1key="dns::mode">';
							$div .= '<option value="openvpn">{{Openvpn (standard)}}</option>';
							$div .= '</select>';
							$div .= '</div>';
							if ($configs['market::allowDNS'] == 1 && network::dns_run()) {
								$div .= '<span class="label label-success">{{Démarré :}} <a href="' . network::getNetworkAccess('external') . '" target="_blank" style="text-decoration: underline;">' . network::getNetworkAccess('external') . '</a></span> ';
							} else {
								$div .= '<span class="label label-warning">{{Arrêté}}</span> ';
							}
							$div .= '<span>';
							$div .= '<a class="btn btn-sm btn-success" id="bt_restartDns"><i class=\'fas fa-play\'></i> {{(Re)démarrer}}</a> ';
							$div .= '<a class="btn btn-sm btn-danger" id="bt_haltDns"><i class=\'fas fa-stop\'></i> {{Arrêter}}</a>';
							$div .= '</span>';
							$div .= '</div>';
							$div .= '</div>';
							echo $div;
						}
						?>

						<legend>{{Accès Docker}}</legend>
						<div class="form-group">
							<label class="col-lg-2 col-xs-4 control-label">{{Masque IP locales}}
								<sup><i class="fas fa-question-circle" tooltip="{{Uniquement pour les installations sous Docker (format: 192.168.1.*)}}"></i></sup>
							</label>
							<div class="col-xs-8">
								<input type="text" class="configKey form-control" data-l1key="network::localip">
							</div>
						</div>

						<hr class="hrPrimary">
					</fieldset>
				</form>

				<form class="form-horizontal col-lg-6 col-xs-12">
					<fieldset>
						<legend>{{Proxy Market}}</legend>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Activer le proxy}}
								<sup><i class="fas fa-question-circle" tooltip="{{Utiliser un proxy pour accéder au Market}}"></i></sup>
							</label>
							<div class="col-md-6 col-xs-8">
								<input type="checkbox" data-l1key="proxyEnabled" class="configKey">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Adresse du proxy}}</label>
							<div class="col-md-6 col-xs-8">
								<input class="configKey form-control" type="text" data-l1key="proxyAddress">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Port du proxy}}</label>
							<div class="col-md-6 col-xs-8">
								<input class="configKey form-control" data-l1key="proxyPort" type="text">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Nom d'utilisateur}}</label>
							<div class="col-md-6 col-xs-8">
								<input class="configKey form-control" type="text" data-l1key="proxyLogins">
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-4 control-label">{{Mot de passe}}</label>
							<div class="col-md-6 col-xs-8">
								<div class="input-group">
									<input class="inputPassword configKey form-control roundedLeft" type="text" data-l1key="proxyPassword">
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_showPass roundedRight" data-plugin="core"><i class="fas fa-eye"></i></a>
									</span>
								</div>
							</div>
						</div>
						<br>
					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="logtab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<legend>{{Timeline}}</legend>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-4 col-xs-8 control-label">{{Nombre maximum d'évènements sur chaque Timeline}}</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-4">
								<input type="text" class="configKey form-control" data-l1key="timeline::maxevent">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-8 control-label">{{Evènements actuels}} : </label>
							<div class="col-lg-3 col-md-2 col-sm-1 col-xs-4">
								<span id="timelineEvents" class="label label-sm label-primary"><?php echo timeline::getLength(); ?></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-4 col-xs-8 control-label">{{Supprimer tous les évènements de la Timeline qui sont dans le futur}}</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-4">
								<a type="text" class="btn btn-sm btn-warning" id="bt_removeTimelineFuturEvent"><i class="fas fa-trash"></i> {{Supprimer}}</a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-4 col-xs-8 control-label">{{Supprimer tous les évènements de la Timeline}}</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-4">
								<a type="text" class="btn btn-sm btn-danger" id="bt_removeTimelineEvent"><i class="fas fa-trash"></i> {{Supprimer}}</a>
							</div>
						</div>
						<legend>{{Messages}}</legend>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-4 col-xs-8 control-label">{{Ajouter un message à chaque erreur dans les logs}}</label>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
								<input type="checkbox" class="configKey" data-l1key="addMessageForErrorLog" checked>
							</div>
						</div>
						<div class="form-group" data-channel="">
							<label class="col-lg-4 col-md-4 col-sm-4 col-xs-8 control-label">{{Action sur message}}</label>
							<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
								<a class="btn btn-sm btn-success bt_addActionOnMessage" data-channel=""><i class="fas fa-plus-circle"></i> {{Ajouter}}</a>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-2 hidden-768"></div>
							<div class="col-sm-10 col-xs-12"></div>
						</div>
						<div id="div_actionOnMessage"></div>
						<hr>
						<?php
						foreach ($JEEDOM_INTERNAL_CONFIG['messageChannel'] as $k => $v) {
							echo '<div class="form-group" data-channel="' . $k . '">';
							echo '<label class="col-lg-4 col-md-4 col-sm-4 col-xs-8 control-label">' . $v['icon'] . ' {{Action sur message, channel}} ' . $v['name'] . '</label>';
							echo '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';
							echo '<a class="btn btn-sm btn-success bt_addActionOnMessage" data-channel="' . $k . '"><i class="fas fa-plus-circle"></i> {{Ajouter}}</a>';
							echo '</div>';
							echo '</div>';
							echo '<div class="form-group">';
							echo '<div class="col-sm-2 hidden-768"></div>';
							echo '<div class="col-sm-10 col-xs-12"></div>';
							echo '</div>';
							echo '<div id="div_actionOnMessage' . $k . '"></div>';
							echo '<hr>';
						}
						?>
					</fieldset>
				</form>
				<hr>

				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a data-target="#log_alertes" role="tab" data-toggle="tab"><i class="fas fa-bell"></i> {{Action sur alertes}}</a></li>
					<li role="presentation"><a data-target="#log_log" role="tab" data-toggle="tab"><i class="fas fa-file"></i> {{Niveau de Logs}}</a></li>
				</ul>

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="log_alertes">
						<form class="form-horizontal">
							<fieldset>
								<br>
								<?php
								$div = '';
								foreach ($JEEDOM_INTERNAL_CONFIG['alerts'] as $level => $value) {
									$div .= '<div class="form-group">';
									$div .= '<label class="col-lg-4 col-md-4 col-sm-4 col-xs-8 control-label">{{Ajouter un message à chaque}} ' . $value['name'] . '</label>';
									$div .= '<div class="col-sm-1">';
									$div .= '<input type="checkbox" class="configKey" data-l1key="alert::addMessageOn' . ucfirst($level) . '"/>';
									$div .= '</div>';
									$div .= '</div>';
									$div .= '<div class="form-group">';
									$div .= '<label class="col-lg-4 col-md-4 col-sm-4 col-xs-4 control-label">{{Commande sur}} ' . $value['name'] . '</label>';
									$div .= '<div class="col-lg-4 col-md-6 col-sm-6 col-xs-8">';
									$div .= '<div class="input-group">';
									$div .= '<input type="text"  class="configKey form-control roundedLeft" data-l1key="alert::' . $level . 'Cmd">';
									$div .= '<span class="input-group-btn">';
									$div .= '<a class="btn btn-default cursor bt_selectAlertCmd roundedRight" tooltip="{{Rechercher une commande}}" data-type="' . $level . '"><i class="fas fa-list-alt"></i></a>';
									$div .= '</span>';
									$div .= '</div>';
									$div .= '</div>';
									$div .= '</div>';
									$div .= '<hr/>';
								}
								echo $div;
								?>
							</fieldset>
						</form>
					</div>

					<div role="tabpanel" class="tab-pane" id="log_log">
						<form class="form-horizontal">
							<fieldset>
								<br>
								<div class="form-group">
									<label class="col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label">{{Nombre maximal de lignes dans un fichier de log}}</label>
									<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
										<input type="text" class="configKey form-control" data-l1key="maxLineLog">
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label">{{Niveau de log par défaut}}</label>
									<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
										<select class="form-control configKey" data-l1key="log::level">
											<option value="100">{{Debug}}</option>
											<option value="200">{{Info}}</option>
											<option value="300">{{Warning}}</option>
											<option value="400">{{Erreur}}</option>
										</select>
									</div>
								</div>
								<?php
								$other_log = array('scenario', 'plugin', 'market', 'api', 'connection', 'interact', 'tts', 'report', 'event');
								$div = '<div id="logsForms"';
								foreach ($other_log as $name) {
									$div .= '<form class="form-horizontal">';
									$div .= '<div class="form-group">';
									$div .= '<label class="col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label">' . ucfirst($name) . '</label>';
									$div .= '<div class="col-sm-8">';
									$div .= '<label class="radio-inline"><input type="radio" data-context="1000" name="rd_logupdate' . $name . '" class="configKey checkContext" data-l1key="log::level::' . $name . '" data-l2key="1000"> {{Aucun}}</label>';
									$div .= '<label class="radio-inline"><input type="radio" data-context="default" name="rd_logupdate' . $name . '" class="configKey checkContext" data-l1key="log::level::' . $name . '" data-l2key="default"> {{Défaut}}</label>';
									$div .= '<label class="radio-inline"><input type="radio" data-context="100" name="rd_logupdate' . $name . '" class="configKey checkContext" data-l1key="log::level::' . $name . '" data-l2key="100"> {{Debug}}</label>';
									$div .= '<label class="radio-inline"><input type="radio" data-context="200" name="rd_logupdate' . $name . '" class="configKey checkContext" data-l1key="log::level::' . $name . '" data-l2key="200"> {{Info}}</label>';
									$div .= '<label class="radio-inline"><input type="radio" data-context="300" name="rd_logupdate' . $name . '" class="configKey checkContext" data-l1key="log::level::' . $name . '" data-l2key="300"> {{Warning}}</label>';
									$div .= '<label class="radio-inline"><input type="radio" data-context="400" name="rd_logupdate' . $name . '" class="configKey checkContext"  data-l1key="log::level::' . $name . '" data-l2key="400"> {{Erreur}}</label>';
									$div .= '</div>';
									$div .= '</div>';
								}
								if ($div != '') echo $div;
								if (init('rescue', 0) == 0) {
									$div = '';
									foreach ((plugin::listPlugin(true)) as $plugin) {
										$div .= '<form class="form-horizontal">';
										$div .= '<div class="form-group">';
										$div .= '<label class="col-lg-4 col-md-4 col-sm-4 col-xs-3 control-label">' . $plugin->getName() . '</label>';
										$div .= '<div class="col-sm-8">';
										$div .= '<label class="radio-inline"><input type="radio" data-context="1000" name="rd_logupdate' . $plugin->getId() . '" class="configKey checkContext" data-l1key="log::level::' . $plugin->getId() . '" data-l2key="1000"> {{Aucun}}</label>';
										$div .= '<label class="radio-inline"><input type="radio" data-context="default" name="rd_logupdate' . $plugin->getId() . '" class="configKey checkContext" data-l1key="log::level::' . $plugin->getId() . '" data-l2key="default"> {{Défaut}}</label>';
										$div .= '<label class="radio-inline"><input type="radio" data-context="100" name="rd_logupdate' . $plugin->getId() . '" class="configKey checkContext" data-l1key="log::level::' . $plugin->getId() . '" data-l2key="100"> {{Debug}}</label>';
										$div .= '<label class="radio-inline"><input type="radio" data-context="200" name="rd_logupdate' . $plugin->getId() . '" class="configKey checkContext" data-l1key="log::level::' . $plugin->getId() . '" data-l2key="200"> {{Info}}</label>';
										$div .= '<label class="radio-inline"><input type="radio" data-context="300" name="rd_logupdate' . $plugin->getId() . '" class="configKey checkContext" data-l1key="log::level::' . $plugin->getId() . '" data-l2key="300"> {{Warning}}</label>';
										$div .= '<label class="radio-inline"><input type="radio" data-context="400" name="rd_logupdate' . $plugin->getId() . '" class="configKey checkContext" data-l1key="log::level::' . $plugin->getId() . '" data-l2key="400"> {{Erreur}}</label>';
										$div .= '</div>';
										$div .= '</div>';
									}
									$div .= '<br></div>';
									if ($div != '') echo $div;
								}
								?>
							</fieldset>
						</form>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane" id="summarytab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<a id="bt_addObjectSummary" class="btn btn-sm btn-success pull-right"><i class="fas fa-plus-circle"></i> {{Ajouter un type de résumé}}</a>
						<table class="table table-condensed" id="table_objectSummary">
							<thead>
								<tr>
									<th>{{Clé}}</th>
									<th>{{Nom}}</th>
									<th>{{Calcul}}</th>
									<th style="min-width: 100px">{{Icône}}</th>
									<th style="min-width: 100px">{{Icône si nul}}</th>
									<th style="min-width:60px">{{Unité}}</th>
									<th style="min-width:90px">{{Masquer le nombre}}
										<sup><i class="fas fa-question-circle" tooltip="{{Ne jamais afficher le numéro à coté de l'icône.}}"></i></sup>
									</th>
									<th style="min-width:120px">{{Masquer le nombre si nul}}
										<sup><i class="fas fa-question-circle" tooltip="{{Ne pas afficher le numéro à coté de l'icône seulement si nul.}}"></i></sup>
									</th>
									<th>{{Méthode de comptage}}</th>
									<th style="min-width:70px">{{Si nul}}
										<sup><i class="fas fa-question-circle" tooltip="{{Afficher même si le résumé est nul.}}"></i></sup>
									</th>
									<th style="min-width:70px">{{Ignorer si}}
										<sup><i class="fas fa-question-circle" tooltip="{{Ignorer commande si pas d'update depuis plus de (min).}}"></i></sup>
									</th>
									<th style="min-width:1px">{{Lier à un virtuel}}</th>
									<th></th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
						<span><i>{{Pour utiliser des icônes colorées, l'option Interface / Tuiles / Icônes widgets colorées doit être activée.}}</i></span>
					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="eqlogictab">
				<br>
				<legend>{{Equipements}}</legend>
				<form class="form-horizontal">
					<fieldset>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label">{{Échecs avant désactivation}}
								<sup><i class="fas fa-question-circle" tooltip="{{Nombre d'échecs avant désactivation de l'équipement (0: pas de désactivation).}}"></i></sup>
							</label>
							<div class="col-lg-1 col-sm-1 col-xs-4">
								<input type="text" class="configKey form-control" data-l1key="numberOfTryBeforeEqLogicDisable">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label">{{Seuil des piles}}<i class="warning jeedom-batterie1" style="font-size:36px;vertical-align: middle;"></i> {{Inférieur à}}
								<sup><i class="fas fa-question-circle" tooltip="{{Si le niveau de charge passe en dessous de (%)}}"></i></sup>
								<sub>%</sub>
							</label>
							<div class="col-lg-1 col-sm-1 col-xs-4">
								<input class="configKey form-control" data-l1key="battery::warning">
							</div>
							<label class="col-lg-1 col-sm-4 col-xs-12 eqLogicAttr label label-warning">{{Warning}}</label>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-12 control-label">{{Seuil des piles}}<i class="danger jeedom-batterie0" style="font-size:36px;vertical-align: middle;"></i> {{Inférieur à}}
								<sup><i class="fas fa-question-circle" tooltip="{{Si le niveau de charge passe en dessous de (%)}}"></i></sup>
								<sub>%</sub>
							</label>
							<div class="col-lg-1 col-sm-1 col-xs-4">
								<input class="configKey form-control" data-l1key="battery::danger">
							</div>
							<label class="col-lg-1 col-sm-4 col-xs-12 eqLogicAttr label label-danger">{{Danger}}</label>
						</div>
					</fieldset>
				</form>

				<legend>{{Historique des commandes}}</legend>
				<form class="form-horizontal">
					<fieldset>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Afficher les statistiques sur les widgets}}</label>
							<div class="col-lg-1 col-md-2 col-sm-5 col-xs-6">
								<input type="checkbox" class="configKey" data-l1key="displayStatsWidget">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Période de calcul pour min, max, moyenne}}
								<sub>h</sub>
							</label>
							<div class="col-lg-1 col-md-2 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="historyCalculPeriod">
							</div>
							<label class="col-lg-3 col-md-4 col-sm-6 col-xs-6 control-label">{{Période de calcul pour la tendance}}
								<sub>h</sub>
							</label>
							<div class="col-lg-1 col-md-2 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="historyCalculTendance">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Délai avant archivage}}
								<sub>h</sub>
							</label>
							<div class="col-lg-1 col-md-2 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="historyArchiveTime">
							</div>
							<label class="col-lg-3 col-md-4 col-sm-6 col-xs-6 control-label">{{Archiver par paquet de}}
								<sub>h</sub>
							</label>
							<div class="col-lg-1 col-md-2 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="historyArchivePackage">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Seuil de calcul de tendance bas}}</label>
							<div class="col-lg-1 col-md-2 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="historyCalculTendanceThresholddMin">
							</div>
							<label class="col-lg-3 col-md-4 col-sm-6 col-xs-6 control-label">{{Seuil de calcul de tendance haut}}</label>
							<div class="col-lg-1 col-md-2 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="historyCalculTendanceThresholddMax">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Limiter à une valeur toutes les}}
							<sup><i class="fas fa-question-circle" title="{{Limite le nombre de valeurs historisées par les commandes en temps réel (avant le lissage de la nuit). Attention un mode de lissage doit absolument être défini.}}"></i></sup>
							</label>
							<div class="col-lg-2 col-md-2 col-sm-5 col-xs-6">
							<select class="form-control configKey" data-l1key="history::smooth">
								<option value="-2">{{Aucun}}</option>
								<option value="60">{{1 min}}</option>
								<option value="300">{{5 min}}</option>
								<option value="600">{{10 min}}</option>
							</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Période d'affichage des graphiques par défaut}}</label>
							<div class="col-lg-2 col-md-2 col-sm-5 col-xs-6">
								<select class="form-control configKey" data-l1key="history::defautShowPeriod">
									<option value="-6 month">{{6 mois}}</option>
									<option value="-3 month">{{3 mois}}</option>
									<option value="-1 month">{{1 mois}}</option>
									<option value="-1 week">{{1 semaine}}</option>
									<option value="-1 day">{{1 jour}}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Purger l'historique si plus vieux que}}</label>
							<div class="col-lg-2 col-md-2 col-sm-5 col-xs-6">
								<select class="form-control configKey" data-l1key="historyPurge">
									<option value="">{{Jamais}}</option>
									<option value="-1 day">{{1 jour}}</option>
									<option value="-7 days">{{7 jours}}</option>
									<option value="-1 month">{{1 mois}}</option>
									<option value="-3 month">{{3 mois}}</option>
									<option value="-6 month">{{6 mois}}</option>
									<option value="-1 year">{{1 an}}</option>
									<option value="-2 years">{{2 ans}}</option>
									<option value="-3 years">{{3 ans}}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Autoriser les dates dans le futur}}
								<sup><i class="fas fa-question-circle" tooltip="{{Autorise l'affichage d'historique avec des dates dans le futur.}}"></i></sup>
							</label>
							<div class="col-lg-1 col-md-2 col-sm-5 col-xs-6">
								<input type="checkbox" class="configKey" data-l1key="history::allowFuture">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-4 col-xs-8 control-label">{{Supprimer tous les historiques qui sont dans le futur}}</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-4">
								<a type="text" class="btn btn-sm btn-warning" id="bt_removeHistoryInFutur"><i class="fas fa-trash"></i> {{Supprimer}}</a>
							</div>
						</div>
					</fieldset>
				</form>

				<legend>{{Widgets par défaut}}</legend>
				<form class="form-horizontal">
					<fieldset>
						<?php
						$widgets_list = cmd::availableWidget('dashboard');
						foreach ($JEEDOM_INTERNAL_CONFIG['cmd']['type'] as $type => $subtypes) { //info or action
							$icon = '';
							if ($type == 'info') $icon = '<i class="info fas fa-info-circle"></i> ';
							if ($type == 'action') $icon = '<i class="warning fas fa-terminal"></i> ';
							foreach ($subtypes['subtype'] as $subtype => $value) { //Each subtype per info or action
								$div = '';
								$div .= '<div class="form-group">';
								$div .= '<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">' . $icon . $JEEDOM_INTERNAL_CONFIG['cmd']['type'][$type]['name'] . ' ' . $value['name'] . '</label>';
								$div .= '<div class="col-lg-1 col-md-2 col-sm-5 col-xs-6">';
								$div .= '<select class="configKey form-control" data-l1key="widget::default::cmd::' . $type . '::' . $subtype . '" >';
								$div .= cmd::getSelectOptionsByTypeAndSubtype($type, $subtype, 'dashboard', $widgets_list);
								$div .= '</select>';
								$div .= '</div>';
								$div .= '</div>';
								echo $div;
							}
						}
						?>
					</fieldset>
				</form>

				<legend>{{Push}}</legend>
				<form class="form-horizontal">
					<fieldset>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{URL de push globale}}
								<sup><i class="fas fa-question-circle" tooltip="{{Mettez ici l'URL à appeler lors d'une mise à jour de la valeur des commandes.<br>Vous pouvez utiliser les tags suivants :<br>#value# (valeur de la commande), #cmd_id# (id de la commande) et #cmd_name# (nom de la commande)}}"></i></sup>
							</label>
							<div class="col-lg-5 col-md-6 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="cmdPushUrl">
							</div>
						</div>
					</fieldset>
				</form>

				<legend>{{InfluxDB}}</legend>
				<form class="form-horizontal">
					<fieldset>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{URL du serveur InfluxDB}}</label>
							<div class="col-lg-3 col-md-3 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="cmdInfluxURL">
							</div>
							<label class="col-lg-2 col-md-3 col-sm-6 col-xs-6 control-label">{{Port du serveur InfluxDB}}</label>
							<div class="col-lg-1 col-md-2 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="cmdInfluxPort">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Nom de la base}}</label>
							<div class="col-lg-3 col-md-3 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="cmdInfluxTable">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Utilisateur de la base}}</label>
							<div class="col-lg-3 col-md-3 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control" data-l1key="cmdInfluxUser">
							</div>
							<label class="col-lg-2 col-md-3 col-sm-6 col-xs-6 control-label">{{Mot de passe de la base}}</label>
							<div class="col-lg-2 col-md-2 col-sm-5 col-xs-6">
								<input type="text" class="configKey form-control inputPassword" data-l1key="cmdInfluxPass">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-6 col-xs-6 control-label">{{Actions}}</label>
							<div class="col-xs-6">
								<a class="btn btn-default btn-sm" id="bt_influxDelete"><i class="fas fa-trash"></i> {{Supprimer}}</a>
								<a class="btn btn-default btn-sm" id="bt_influxHistory"><i class="fas fas fa-history"></i> {{Envoyer Historique}}</a>
							</div>
						</div>
					</fieldset>
				</form>


				<legend>{{Spécial}}</legend>
				<form class="form-horizontal">
					<fieldset>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Guillemets automatiques}}
								<sup><i class="fas fa-question-circle warning" tooltip="{{Gérer automatiquement les guillemets des chaines de caractères dans les expressions (activé par défaut)}}."></i></sup>
							</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
								<input type="checkbox" class="configKey form-control" data-l1key="expression::autoQuote">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Ne pas exécuter la commande si l’équipement est déjà dans le bon état (alpha)}}</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
								<input type="checkbox" class="configKey form-control" data-l1key="cmd::allowCheckState">
							</div>
						</div>
					</fieldset>
				</form>
				<br>
			</div>

			<div role="tabpanel" class="tab-pane" id="repporttab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Délai d'attente après génération de la page}}
								<sub>ms</sub>
							</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
								<input class="configKey form-control" data-l1key="report::delay">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Nettoyer les rapports plus anciens de}}
								<sub>j</sub>
							</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
								<input class="configKey form-control" data-l1key="report::maxdays">
							</div>
						</div>
					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="graphlinktab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Profondeur pour les scénarios}}
								<sup><i class="fas fa-question-circle" tooltip="{{Nombre maximum de niveaux d’éléments affichés dans les graphiques de liens de scénario}}"></i></sup>
							</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
								<input class="configKey form-control" data-l1key="graphlink::scenario::drill">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Profondeur pour les objets}}
								<sup><i class="fas fa-question-circle" tooltip="{{Nombre maximum de niveaux d’éléments affichés dans les graphiques de liens d'objet}}"></i></sup>
							</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
								<input class="configKey form-control" data-l1key="graphlink::jeeObject::drill">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Profondeur pour les équipements}}
								<sup><i class="fas fa-question-circle" tooltip="{{Nombre maximum de niveaux d’éléments affichés dans les graphiques de liens d'équipement}}"></i></sup>
							</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
								<input class="configKey form-control" data-l1key="graphlink::eqLogic::drill">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Profondeur pour les commandes}}
								<sup><i class="fas fa-question-circle" tooltip="{{Nombre maximum de niveaux d’éléments affichés dans les graphiques de liens de commande}}"></i></sup>
							</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
								<input class="configKey form-control" data-l1key="graphlink::cmd::drill">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Profondeur pour les variables}}
								<sup><i class="fas fa-question-circle" tooltip="{{Nombre maximum de niveaux d’éléments affichés dans les graphiques de liens de variable}}"></i></sup>
							</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
								<input class="configKey form-control" data-l1key="graphlink::dataStore::drill">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Paramètre de prerender}}
								<sup><i class="fas fa-question-circle" tooltip="{{Permet d’agir sur la disposition du graphique (défaut 3)}}"></i></sup>
							</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
								<input class="configKey form-control" data-l1key="graphlink::prerender">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Paramètre de render}}
								<sup><i class="fas fa-question-circle" tooltip="{{Permet d’agir sur la disposition du graphique selon les relations entre éléments (défaut 3000)}}"></i></sup>
							</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
								<input class="configKey form-control" data-l1key="graphlink::render">
							</div>
						</div>
					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="interacttab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<legend>{{Général}}</legend>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Sensibilité}}
								<sup><i class="fas fa-question-circle" tooltip="{{Plus la sensibilité est basse (de 1 à 99), plus la correspondance doit être exacte.}}"></i></sup>
							</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<div class="input-group">
									<span class="input-group-addon roundedLeft" style="width:90px">{{1 mot}}</span>
									<input type="text" class="configKey form-control" data-l1key="interact::confidence1">
									<span class="input-group-addon" style="width:90px">{{2 mots}}</span>
									<input type="text" class="configKey form-control" data-l1key="interact::confidence2">
									<span class="input-group-addon" style="width:90px">{{3 mots}}</span>
									<input type="text" class="configKey form-control" data-l1key="interact::confidence3">
									<span class="input-group-addon" style="width:90px">> {{3 mots}}</span>
									<input type="text" class="configKey form-control roundedRight" data-l1key="interact::confidence">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Réduire le poids de}}
								<sup><i class="fas fa-question-circle" tooltip="{{Distance de Levenshtein pour le calcul de correspondance<br>Nombre de différences entre les deux chaines en fonction du nombre de mots.}}"></i></sup>
							</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<div class="input-group">
									<span class="input-group-addon roundedLeft" style="width:90px">{{1 mot}}</span>
									<input type="text" class="configKey form-control" data-l1key="interact::weigh1">
									<span class="input-group-addon" style="width:90px">{{2 mots}}</span>
									<input type="text" class="configKey form-control" data-l1key="interact::weigh2">
									<span class="input-group-addon" style="width:90px">{{3 mots}}</span>
									<input type="text" class="configKey form-control" data-l1key="interact::weigh3">
									<span class="input-group-addon" style="width:90px">{{4 mots}}</span>
									<input type="text" class="configKey form-control roundedRight" data-l1key="interact::weigh4">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Ne pas répondre si l'interaction n'est pas comprise}}
								<sup><i class="fas fa-question-circle" tooltip="{{Par défaut Jeedom répond “je n’ai pas compris” si aucune interaction ne correspond.}}"></i></sup>
							</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
								<input type="checkbox" class="configKey" data-l1key="interact::noResponseIfEmpty">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Regex générale d'exclusion pour les interactions}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<textarea type="text" class="configKey form-control" data-l1key="interact::regexpExcludGlobal"></textarea>
							</div>
						</div>


						<legend>{{Interaction automatique, contextuelle & avertissement}}</legend>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Activer les interactions automatiques}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input type="checkbox" class="configKey" data-l1key="interact::autoreply::enable">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Activer les réponses contextuelles}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input type="checkbox" class="configKey" data-l1key="interact::contextual::enable">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Réponse contextuelle prioritaire si la phrase commence par}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input class="configKey form-control" data-l1key="interact::contextual::startpriority">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Découper une interaction en deux si elle contient}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input class="configKey form-control" data-l1key="interact::contextual::splitword">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Activer les interactions "préviens moi"}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input type="checkbox" class="configKey" data-l1key="interact::warnme::enable">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Réponse de type "préviens moi" si la phrase commence par}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input class="configKey form-control" data-l1key="interact::warnme::start">
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Commande de retour par défaut}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<div class="input-group">
									<input type="text" class="configKey form-control roundedLeft" data-l1key="interact::warnme::defaultreturncmd">
									<span class="input-group-btn">
										<a class="btn btn-default cursor bt_selectWarnMeCmd roundedRight" tooltip="{{Rechercher une commande}}"><i class="fas fa-list-alt"></i></a>
									</span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Synonymes pour les objets}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input class="configKey form-control" data-l1key="interact::autoreply::object::synonym">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Synonymes pour les équipements}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input class="configKey form-control" data-l1key="interact::autoreply::eqLogic::synonym">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Synonymes pour les commandes}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input class="configKey form-control" data-l1key="interact::autoreply::cmd::synonym">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Synonymes pour les résumés}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input class="configKey form-control" data-l1key="interact::autoreply::summary::synonym">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Synonyme commande slider maximum}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input class="configKey form-control" data-l1key="interact::autoreply::cmd::slider::max">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-3 col-md-4 col-sm-4 col-xs-6 control-label">{{Synonyme commande slider minimum}}</label>
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
								<input class="configKey form-control" data-l1key="interact::autoreply::cmd::slider::min">
							</div>
						</div>

						<legend>{{Couleurs}}<i class="fas fa-plus-circle pull-right cursor" id="bt_addColorConvert"></i></legend>

						<table class="table table-condensed" id="table_convertColor">
							<thead>
								<tr>
									<th>{{Nom}}</th>
									<th>{{Code HTML}}</th>
									<th></th>
								</tr>
								<tr class="filter" style="display : none;">
									<td class="color"><input class="filter form-control" filterOn="color"></td>
									<td class="codeHtml"><input class="filter form-control" filterOn="codeHtml"></td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="securitytab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<legend>{{Connexion}}</legend>
						<div class="form-group">
							<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Durée de vie des sessions}}
								<sup><i class="fas fa-question-circle" tooltip="{{Durée de vie de votre connexion, en heure<br>(si vous n'avez pas coché la case enregistrer cet ordinateur)}}"></i></sup>
								<sub>h</sub>
							</label>
							<div class="col-md-3 col-sm-4 col-xs-12">
								<input type="text" class="configKey form-control" data-l1key="session_lifetime">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Nombre d'échecs tolérés}}
								<sup><i class="fas fa-question-circle" tooltip="{{Passé ce nombre, l'IP sera bannie.}}"></i></sup>
							</label>
							<div class="col-md-3 col-sm-4 col-xs-12">
								<input type="text" class="configKey form-control" data-l1key="security::maxFailedLogin">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Temps maximum entre les échecs}}
								<sup><i class="fas fa-question-circle" tooltip="{{Temps en secondes}}"></i></sup>
								<sub>s</sub>
							</label>

							<div class="col-md-3 col-sm-4 col-xs-12">
								<input type="text" class="configKey form-control" data-l1key="security::timeLoginFailed">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Durée du bannissement}}
								<sup><i class="fas fa-question-circle" tooltip="{{Durée en secondes.<br> -1 : bannissement infini}}"></i></sup>
								<sub>s</sub>
							</label>
							<div class="col-md-3 col-sm-4 col-xs-12">
								<input type="text" class="configKey form-control" data-l1key="security::bantime">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Liste blanche}}
								<sup><i class="fas fa-question-circle" tooltip="{{IPs ou masques séparés par ;<br>ex: 127.0.0.1;192.168.*.*}}"></i></sup>
							</label>
							<div class="col-md-3 col-sm-4 col-xs-12">
								<input type="text" class="configKey form-control" data-l1key="security::whiteips">
							</div>
						</div>

						<legend>{{LDAP}}</legend>
						<?php if (function_exists('ldap_connect')) { ?>
							<div class="form-group">
								<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Activer l'authentification LDAP}}</label>
								<div class="col-sm-1">
									<input type="checkbox" class="configKey" data-l1key="ldap:enable">
								</div>
							</div>
							<div id="div_config_ldap">
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Samba4}}</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="checkbox" class="configKey form-control" data-l1key="ldap:samba4">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{tls}}</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="checkbox" class="configKey form-control" data-l1key="ldap:tls">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Hôte}}
										<sup><i class="fas fa-question-circle" tooltip="{{URL utilisée pour contacter la base, en précisant le type de connexion (e.g ldap(s)://URL)}}"></i></sup>
									</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="text" class="configKey form-control" data-l1key="ldap:host">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Port}}
										<sup><i class="fas fa-question-circle" tooltip="{{Port à utiliser (par défaut, LDAP : 389, LDAPS : 636)}}"></i></sup>
									</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="text" class="configKey form-control" data-l1key="ldap:port">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Domaine}}
										<sup><i class="fas fa-question-circle" tooltip="{{FQDN de la base}}"></i></sup>
									</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="text" class="configKey form-control" data-l1key="ldap:domain">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Base DN des utilisateurs}}
										<sup><i class="fas fa-question-circle" tooltip="{{Base DN correspondant à l'emplacement des utilisateurs (e.g cn=users,dc=ldap,dc=local)}}"></i></sup>
									</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="text" class="configKey form-control" data-l1key="ldap:basedn">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Nom d'utilisateur}}
										<sup><i class="fas fa-question-circle" tooltip="{{Utilisateur ayant accès à la base (si possible, utiliser un compte en lecture seul et pas root pour optimiser la sécurité)}}"></i></sup>
									</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="text" class="configKey form-control" data-l1key="ldap:username">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Mot de passe}}
										<sup><i class="fas fa-question-circle" tooltip="{{Mot de passe de l'utilisateur}}"></i></sup>
									</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<div class="input-group">
											<input type="text" class="inputPassword configKey form-control" data-l1key="ldap:password">
											<span class="input-group-btn">
												<a class="btn btn-default form-control bt_showPass roundedRight"><i class="fas fa-eye"></i></a>
											</span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Champs recherche utilisateur}}
										<sup><i class="fas fa-question-circle" tooltip="{{Champ à utiliser comme identifiant utilisateur (e.g uid)}}"></i></sup>
									</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="text" class="configKey form-control" data-l1key="ldap::usersearch">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Filtre administrateurs (optionnel)}}
										<sup><i class="fas fa-question-circle" tooltip="{{Filtre permettant d'identifier les administrateurs dans la base. Si vide, tous les utilisateurs en base seront administrateurs par défaut}}"></i></sup>
									</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="text" class="configKey form-control" data-l1key="ldap:filter:admin">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Filtre utilisateurs (optionnel)}}
										<sup><i class="fas fa-question-circle" tooltip="{{Filtre permettant d'identifier les utilisateurs dans la base. Si vide, tous les utilisateurs en base seront administrateurs par défaut}}"></i></sup>
									</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="text" class="configKey form-control" data-l1key="ldap:filter:user">
									</div>
								</div>
								<div class="form-group">
									<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Filtre utilisateurs limités (optionnel)}}
										<sup><i class="fas fa-question-circle" tooltip="{{Filtre permettant d'identifier les utilisateurs limités dans la base. Si vide, tous les utilisateurs en base seront administrateurs par défaut}}"></i></sup>
									</label>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<input type="text" class="configKey form-control" data-l1key="ldap:filter:restrict">
									</div>
								</div>
								<div class="form-group">
									<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6"></div>
									<div class="col-md-3 col-sm-4 col-xs-12">
										<a class="btn btn-default" id="bt_testLdapConnection"><i class="fas fa-cube"></i> Tester</a>
									</div>
								</div>
							</div>
						<?php } else {
							echo '<div class="alert alert-info">{{Librairie LDAP non trouvée. Merci de l\'installer avant de pouvoir utiliser la connexion LDAP}}</div>';
						} ?>

						<legend>{{Single Sign On}}</legend>
						<div class="form-group">
							<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Activer l'authentification SSO}}</label>
							<div class="col-md-3 col-sm-4 col-xs-12">
								<input type="checkbox" class="configKey" data-l1key="sso:allowRemoteUser">
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 col-sm-4 col-xs-12 control-label">{{Configurer Entête HTTP}}
								<sup><i class="fas fa-question-circle" tooltip="{{Champ à utiliser pour déterminer l'entête HTTP contenant l'identifiant utilisateur (e.g HTTP_REMOTE_USER)}}"></i></sup>
							</label>
							<div class="col-md-3 col-sm-4 col-xs-12">
								<input type="text" class="configKey form-control" data-l1key="sso:remoteUserHeader">
							</div>
						</div>

						<legend>{{Dépendance et démon}}</legend>
						<div class="form-group">
							<label class="col-lg-6 col-sm-8 col-xs-12 control-label">{{Autoriser l'installation des dépendances d'un même plugin à moins de 45s d'intervalle}}
								<sup><i class="fas fa-question-circle" tooltip="{{Autoriser l'installation des dépendances d'un même plugin à moins de 45s d'intervalle}}"></i></sup>
							</label>
							<div class="col-sm-1">
								<input type="checkbox" class="configKey" data-l1key="dontProtectTooFastLaunchDependancy">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-6 col-sm-8 col-xs-12 control-label">{{Autoriser le lancement du démon d'un même plugin à moins de 45s d'intervalle}}
								<sup><i class="fas fa-question-circle" tooltip="{{Autoriser le lancement du démon d'un même plugin à moins de 45s d'intervalle}}"></i></sup>
							</label>
							<div class="col-sm-1">
								<input type="checkbox" class="configKey" data-l1key="dontProtectTooFastLaunchDeamony">
							</div>
						</div>
					</fieldset>
				</form>
				<form class="form-horizontal">
					<fieldset>
						<legend>{{IPs bannies}} <a class="btn btn-danger btn-xs pull-right" id="bt_removeBanIp"><i class="fas fa-trash"></i> {{Supprimer}}</a></legend>
						<table class="table table-condensed">
							<thead>
								<tr>
									<th>{{IP}}</th>
									<th>{{Date}}</th>
									<th>{{Date de fin}}</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$ban_ips = json_decode(cache::byKey('security::banip')->getValue('[]'), true);
								if (!is_array($ban_ips)) {
									$ban_ips = array();
								}
								if (count($ban_ips) != 0) {
									$div = '';
									foreach ($ban_ips as $ip => $datetime) {
										$div .= '<tr>';
										$div .= '<td>' . $ip . '</td>';
										$div .= '<td>' . date('Y-m-d H:i:s',(int) $datetime) . '</td>';
										if (config::byKey('security::bantime') == -1) {
											$div .= '<td>{{Jamais}}</td>';
										} else {
											$div .= '<td>' . date('Y-m-d H:i:s',(int) ($datetime + config::byKey('security::bantime'))) . '</td>';
										}
										$div .= '</tr>';
									}
									echo $div;
								}
								?>
							</tbody>
						</table>
					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="updatetab">
				<br>
				<div class="row">
					<div class="col-sm-12">
						<form class="form-horizontal">
							<fieldset>
								<legend>{{Mise à jour de}} <?php echo $productName; ?></legend>
								<div class="form-group">
									<label class="col-lg-3 col-md-4 col-xs-6 control-label">{{Source de mise à jour du core}}</label>
									<div class="col-lg-3 col-md-4 col-xs-5">
										<select class="form-control configKey" data-l1key="core::repo::provider">
											<option value="default">{{Défaut}}</option>
											<?php
											foreach ($repos as $key => $value) {
												if (!isset($value['scope']['core']) || $value['scope']['core'] === false) {
													continue;
												}
												if ($configs[$key . '::enable'] == 0) {
													continue;
												}
												echo '<option value="' . $key . '">' . $value['name'] . '</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-3 col-md-4 col-xs-6 control-label">{{Version du core}}
										<sup><i class="fas fa-question-circle" tooltip="{{Version installée du core, pour la vérification de mise à jour disponible.}}"></i></sup>
									</label>
									<div class="col-lg-3 col-md-4 col-xs-5">
										<div class="input-group">
											<select class="form-control configKey" data-l1key="core::branch">
												<optgroup label="{{Defaut (support)}}">
													<option value="master">{{Stable}}</option>
												</optgroup>
												<?php 
												if(config::byKey('core::repo::provider') == 'default'){
													$lists = cache::byKey('core::branch::default::list')->getValue(array());
													if(!isset($lists['branchs']) || !is_array($lists['branchs'])){
														$request_http = new com_http('https://api.github.com/repos/jeedom/core/branches');
														$request_http->setHeader(array('User-agent: jeedom'));
														try {
															$lists['branchs'] = json_decode($request_http->exec(10, 1), true);
														} catch (\Exception $e) {
														}
														cache::set('core::branch::default::list',$lists,86400);
													}
													if(!isset($lists['tags']) || !is_array($lists['tags'])){
														$request_http = new com_http('https://api.github.com/repos/jeedom/core/tags');
														$request_http->setHeader(array('User-agent: jeedom'));
														try {
															$lists['tags'] = json_decode($request_http->exec(10, 1), true);
														} catch (\Exception $e) {
														}
														cache::set('core::branch::default::list',$lists,86400);
												  }
												if(isset($lists['branchs']) && is_array($lists['branchs'])){
													echo '<optgroup label="{{Branches (Pas de support)}}">';
													foreach ($lists['branchs'] as $branch) {
														if(!is_array($branch) || !isset($branch['name'])){
															continue;
														}
														if(in_array($branch['name'],array('V4-stable','master'))){
															continue;
														}
														echo '<option value="'.$branch['name'].'">'.$branch['name'].'</option>';
													}
													echo '</optgroup>';
												}
												if(isset($lists['tags']) && is_array($lists['tags'])){
													echo '<optgroup label="{{Tags (Pas de support)}}">';
													foreach ($lists['tags'] as $tag) {
														if(!is_array($tag) || !isset($tag['name'])){
															continue;
														}
														echo '<option value="tag::'.$tag['name'].'">'.$tag['name'].'</option>';
													}
													echo '</optgroup>';
												}
											}
											?>
											</select>
											<span class="input-group-btn">
												<a class="btn btn-default form-control" id="bt_refreshListBranch"><i class="fas fa-sync"></i></a>
											</span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-3 col-md-4 col-xs-6 control-label">{{Vérification automatique des mises à jour}}</label>
									<div class="col-sm-1">
										<input type="checkbox" class="configKey" data-l1key="update::autocheck">
									</div>
								</div>
								<div class="form-group">
									<label class="col-lg-3 col-md-4 col-xs-6 control-label">{{[DANGER] Mettre à jour les dépendances PHP (composer) après chaque mise à jour du core}}</label>
									<div class="col-sm-1">
										<input type="checkbox" class="configKey" data-l1key="update::composerUpdate">
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<br><br>
				<div class="row">
					<div class="col-sm-12">
						<form class="form-horizontal">
							<fieldset>
								<legend>{{Configuration des dépôts}}</legend>
								<ul class="nav nav-tabs" role="tablist">
									<?php
									foreach ($repos as $key => $value) {
										$name = (isset($value['configuration']['translate_name'])) ? $value['configuration']['translate_name'] : $value['name'];
										$active = ($key == 'market') ? 'active' : '';
										echo '<li role="presentation" class="' . $active . '"><a data-target="#tab' . $key . '" aria-controls="tab' . $key . '" role="tab" data-toggle="tab">' . $name . '</a></li>';
									}
									?>
								</ul>
								<div class="tab-content">
									<?php
									foreach ($repos as $key => $value) {
										$div = '';

										$name = (isset($value['configuration']['translate_name'])) ? $value['configuration']['translate_name'] : $value['name'];
										$active = ($key == 'market') ? 'active' : '';

										$div .= '<div role="tabpanel" class="tab-pane ' . $active . '" id="tab' . $key . '">';
										$div .= '<br/>';
										$div .= '<div class="form-group">';
										$div .= '<label class="col-lg-3 col-xs-6 control-label">{{Activer}} ' . $name . '</label>';
										$div .= '<div class="col-sm-1">';
										$div .= '<input type="checkbox" class="configKey enableRepository" data-repo="' . $key . '" data-l1key="' . $key . '::enable"/>';
										$div .= '</div>';
										$div .= '</div>';
										if ($value['scope']['hasConfiguration'] === false) {
											$div .= '</div>';
											echo $div;
											continue;
										}
										$div .= '<div class="repositoryConfiguration' . $key . '" style="display:none;">';
										foreach ($value['configuration']['configuration'] as $pKey => $parameter) {
											$div .= '<div class="form-group">';
											$div .= '<label class="col-lg-3 col-md-4 col-xs-6 control-label">';
											$div .= $parameter['name'];
											$div .= '</label>';
											$div .= '<div class="col-lg-3 col-md-4 col-xs-5">';
											$default = (isset($parameter['default'])) ? $parameter['default'] : '';
											switch ($parameter['type']) {
												case 'checkbox':
													$div .= '<input type="checkbox" class="configKey" data-l1key="' . $key . '::' . $pKey . '" value="' . $default . '">';
													break;
												case 'input':
													$div .= '<input class="configKey form-control" data-l1key="' . $key . '::' . $pKey . '" value="' . $default . '">';
													break;
												case 'number':
													$div .= '<input type="number" class="configKey form-control ispin" data-l1key="' . $key . '::' . $pKey . '" value="' . $default . '">';
													break;
												case 'password':
													$div .= '<div class="input-group">';
													$div .= '<input type="text" class="inputPassword configKey form-control" data-l1key="' . $key . '::' . $pKey . '" value="' . $default . '">';
													$div .= '<span class="input-group-btn"><a class="btn btn-default form-control bt_showPass roundedRight"><i class="fas fa-eye"></i></a></span>';
													$div .= '</div>';
													break;
												case 'password_noshow':
													$div .= '<div class="input-group">';
													$div .= '<input type="text" class="inputPassword configKey form-control" data-l1key="' . $key . '::' . $pKey . '" value="' . $default . '">';
													$div .= '</div>';
													break;
												case 'select':
													$div .= '<select class="form-control configKey" data-l1key="' . $key . '::' . $pKey . '">';
													foreach ($parameter['values'] as $optkey => $optval) {
														$div .= '<option value="' . $optkey . '">' . $optval . '</option>';
													}
													$div .= '</select>';
													break;
											}
											$div .= '</div>';
											$div .= '</div>';
										}
										if (isset($value['scope']['test']) && $value['scope']['test']) {
											$div .= '<div class="form-group">';
											$div .= '<label class="col-lg-3 col-md-4 col-xs-6 control-label">{{Tester/Synchroniser}}</label>';
											$div .= '<div class="col-lg-3 col-md-4 col-xs-5">';
											$div .= '<a class="btn btn-default testRepoConnection" data-repo="' . $key . '"><i class="fas fa-check"></i> {{Tester}}</a>';
											$div .= '</div>';
											$div .= '</div>';
										}
										$div .= '</div>';
										$div .= '</div>';
										echo $div;
									}
									?>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>

			<div role="tabpanel" class="tab-pane" id="cachetab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<div class="alert alert-info">
							{{Attention : toute modification du moteur de cache nécessite un redémarrage et vous fera perdre temporairement les informations sur la valeur des commandes et toute autre information en cache le temps que tout soit renvoyé.}}
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Moteur de cache}}</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
								<select class="form-control configKey" data-l1key="cache::engine">
									<option value="FileCache">{{Fichier}}</option>
									<?php if (class_exists('redis')) { ?>
										<option value="RedisCache">{{Redis}}</option>
									<?php } ?>
									<option value="MariadbCache">{{Mysql}}</option>
								</select>
							</div>
						</div>
						<div class="cacheEngine RedisCache">
							<div class="form-group">
								<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Adresse Redis}}</label>
								<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
									<input type="text" class="configKey form-control" data-l1key="cache::redisaddr">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Port redis}}</label>
								<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
									<input type="text" class="configKey form-control" data-l1key="cache::redisport">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Temps de pause pour le long polling}}
								<sup><i class="fas fa-question-circle" tooltip="{{Fréquence de vérification des événements en attente.}}"></i></sup>
								<sub>s</sub>
							</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
								<input class="configKey form-control" data-l1key="event::waitPollingTime">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Nettoyer le cache}}
								<sup><i class="fas fa-question-circle" tooltip="{{Force la suppression des objets qui ne sont plus utiles.}}<br>{{Exécuté automatiquement toutes les nuits.}}"></i></sup>
							</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
								<a class="btn btn-warning" id="bt_cleanCache" style="width:80px"><i class="fas fa-magic"></i> {{Nettoyer}}</a>
							</div>
						</div>
						<hr>
						<div class="form-group">
							<label class="col-lg-4 col-md-5 col-sm-6 col-xs-6 control-label">{{Vider toutes les données en cache}}
								<sup><i class="fas fa-question-circle" tooltip="{{Vide complètement le cache.<br>Attention cela peut faire perdre des données.}}"></i></sup>
							</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-6">
								<a class="btn btn-danger" id="bt_flushCache" style="width:80px"><i class="fas fa-trash"></i> {{Vider}}</a>
							</div>
						</div>
					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="apitab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<div class="form-group">
							<label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label">{{Clé API}}
								<sup><i class="fas fa-question-circle" tooltip="{{Clé API globale}}"></i></sup>
							</label>
							<div class="col-lg-5 col-md-5 col-sm-7 col-xs-12">
								<div class="input-group">
									<input class="inputPassword span_apikey roundedLeft form-control" readonly value="<?php echo $configs['api']; ?>">
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_regenerate_api" data-plugin="core"><i class="fas fa-sync"></i></a>
									</span>
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_showPass"><i class="fas fa-eye"></i></a>
									</span>
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_copyPass roundedRight"><i class="far fa-copy"></i></a>
									</span>
								</div>
							</div>
							<label class="col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label">{{Accès API}}</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
								<select class="form-control configKey" data-l1key="api::core::mode">
									<option value="enable">{{Activé}}</option>
									<option value="whiteip">{{IP blanche}}</option>
									<option value="disable">{{Désactivé}}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label">{{Accès API TTS}}</label>
							<div class="col-lg-5 col-md-5 col-sm-7 col-xs-12">
								<div class="input-group">
									<input class="inputPassword span_apikey roundedLeft form-control" readonly value="<?php echo $configs['apitts']; ?>">
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_regenerate_api" data-plugin="apitts"><i class="fas fa-sync"></i></a>
									</span>
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_showPass"><i class="fas fa-eye"></i></a>
									</span>
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_copyPass roundedRight"><i class="far fa-copy"></i></a>
									</span>
								</div>
							</div>
							<label class="col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label">{{Accès API}}</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
								<select class="form-control configKey" data-l1key="api::apitts::mode">
									<option value="enable">{{Activé}}</option>
									<option value="whiteip">{{IP blanche}}</option>
									<option value="localhost">{{Localhost}}</option>
									<option value="disable">{{Désactivé}}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label">{{Clé API Pro}}
								<sup><i class="fas fa-question-circle" tooltip="{{Clé API Pro}}"></i></sup>
							</label>
							<div class="col-lg-5 col-md-5 col-sm-7 col-xs-12">
								<div class="input-group">
									<input class="inputPassword span_apikey roundedLeft form-control" readonly value="<?php echo $configs['apipro']; ?>">
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_regenerate_api" data-plugin="apipro"><i class="fas fa-sync"></i></a>
									</span>
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_showPass"><i class="fas fa-eye"></i></a>
									</span>
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_copyPass roundedRight"><i class="far fa-copy"></i></a>
									</span>
								</div>
							</div>
							<label class="col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label">{{Accès API}}</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
								<select class="form-control configKey" data-l1key="api::apipro::mode">
									<option value="enable">{{Activé}}</option>
									<option value="disable">{{Désactivé}}</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label">{{Clé Market}}
								<sup><i class="fas fa-question-circle" tooltip="{{Clé Market}}"></i></sup>
							</label>
							<div class="col-lg-5 col-md-5 col-sm-7 col-xs-12">
								<div class="input-group">
									<input class="inputPassword span_apikey roundedLeft form-control" readonly value="<?php echo $configs['apimarket']; ?>">
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_regenerate_api" data-plugin="apimarket"><i class="fas fa-sync"></i></a>
									</span>
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_showPass"><i class="fas fa-eye"></i></a>
									</span>
									<span class="input-group-btn">
										<a class="btn btn-default form-control bt_copyPass roundedRight"><i class="far fa-copy"></i></a>
									</span>
								</div>
							</div>
							<label class="col-lg-2 col-md-2 col-sm-4 col-xs-12 control-label">{{Accès API}}</label>
							<div class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
								<select class="form-control configKey" data-l1key="api::apimarket::mode">
									<option value="enable">{{Activé}}</option>
									<option value="disable">{{Désactivé}}</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label">{{Interdire les méthodes api (regexp)}}</label>
							<div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
								<input type="text" class="configKey form-control" data-l1key="api::forbidden::method">
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-2 col-md-3 col-sm-4 col-xs-12 control-label">{{N'autoriser que les méthodes api (regexp)}}</label>
							<div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
								<input type="text" class="configKey form-control" data-l1key="api::allow::method">
							</div>
						</div>

						<hr class="hrPrimary">
						<?php
						if (init('rescue', 0) == 0) {
							$div = '';
							foreach ((plugin::listPlugin(true)) as $plugin) {
								$div .=  '<div class="form-group">';
								$div .= '<label class="col-xs-12 control-label pull-left">{{Clé API}} : ' . $plugin->getName() . '</label>';
								$div .= '<div class="col-lg-4 col-md-3 col-sm-4 col-xs-12">';
								$div .= '<div class="input-group">';
								$div .= '<input class="inputPassword span_apikey roundedLeft form-control" readonly value="' . jeedom::getApiKey($plugin->getId(), 'disable') . '">';
								$div .= '<span class="input-group-btn">';
								$div .= '<a class="btn btn-default form-control bt_regenerate_api" data-plugin="' . $plugin->getId() . '"><i class="fas fa-sync"></i></a>';
								$div .= '</span>';
								$div .= '<span class="input-group-btn">';
								$div .= '<a class="btn btn-default form-control bt_showPass"><i class="fas fa-eye"></i></a>';
								$div .= '</span>';
								$div .= '<span class="input-group-btn">';
								$div .= '<a class="btn btn-default form-control bt_copyPass roundedRight"><i class="far fa-copy"></i></a>';
								$div .= '</span>';
								$div .= '</div>';
								$div .= '</div>';
								$div .= '<label class="col-lg-2 col-md-2 col-sm-2 col-xs-6 control-label">{{Accès API}}</label>';
								$div .= '<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">';
								$div .= '<select class="form-control configKey" data-l1key="api::' . $plugin->getId() . '::mode">';
								$div .= '<option value="enable">{{Activé}}</option>';
								$div .= '<option value="whiteip">{{IP blanche}}</option>';
								$div .= '<option value="localhost">{{Localhost}}</option>';
								$div .= '<option value="disable">{{Désactivé}}</option>';
								$div .= '</select>';
								$div .= '</div>';
								$div .= '<label class="col-lg-2 col-md-3 col-sm-3 col-xs-6 control-label">{{Accès restreint}}';
								$div .= '<sup> <i class="fas fa-question-circle" tooltip="{{Les appels API seront bloqués pour les méthodes du Core.}}"></i></sup>';
								$div .= '</label>';
								$div .= '<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6">';
								$div .= '<input type="checkbox" class="form-control configKey checkContext" data-context="api::restricted" data-l1key="api::' . $plugin->getId() . '::restricted">';
								$div .= '</select>';
								$div .= '</div>';
								$div .= '<div class="visible-xs col-xs-12"><br/></div>';
								$div .= '</div>';
							}
							echo $div;
						}
						?>
					</fieldset>
				</form>
			</div>

			<div role="tabpanel" class="tab-pane" id="ostab">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<legend><i class="fas fa-hospital-symbol"></i> {{Vérifications Système}}</legend>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-5 col-xs-8 control-label"><i class="fas fa-recycle"></i> {{Vérification générale}}
								<sup><i class="fas fa-question-circle" tooltip="{{Permet d'exécuter le test de consistence}}"></i></sup>
							</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-4">
								<a class="btn btn-info" id="bt_consistency" style="width:50%;"><i class="fas fa-recycle"></i> {{Vérifier}}</a>
								<a id="bt_logConsistency" class="btn btn-success" target="_blank" title="{{Ouvrir le log Consistency.}}"><i class="far fa-file"></i> {{Log}}</a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-5 col-xs-8 control-label"><i class="fas fa-terminal"></i> {{Rétablissement des droits des dossiers et fichiers}}
								<sup><i class="fas fa-question-circle" tooltip="{{Permet de réappliquer les bons droits sur les fichiers.}}"></i></sup>
							</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-4">
								<a class="btn btn-info" id="bt_cleanFileSystemRight" style="width:50%;"><i class="fas fa-terminal"></i> {{Vérifier}}</a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-5 col-xs-8 control-label"><i class="fas fa-box-open"></i> {{Vérification des packages système}}
								<sup><i class="fas fa-question-circle" tooltip="{{Vérifie que les packages nécessaires sont bien installés.}}"></i></sup>
							</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-4">
								<a class="btn btn-info" id="bt_checkPackage" style="width:50%;"><i class="fas fa-box-open"></i> {{Vérifier}}</a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-5 col-xs-8 control-label"><i class="fas fa-database"></i> {{Vérification de la base de données}}
								<sup><i class="fas fa-question-circle" tooltip="{{Vérifie que la base de données est conforme à ce qui est attendu.}}"></i></sup>
							</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-4">
								<a class="btn btn-info" id="bt_checkDatabase" style="width:50%;"><i class="fas fa-database"></i> {{Vérifier}}</a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 col-md-4 col-sm-5 col-xs-8 control-label"><i class="fas fa-database"></i> {{Nettoyage de la base de données}}<sup><i class="fas fa-question-circle" tooltip="{{Nettoie la base de données (objets, commandes, historiques et autres informations non valides).}}"></i></sup>
							</label>
							<div class="col-lg-3 col-md-4 col-sm-5 col-xs-4">
								<a class="btn btn-warning" id="bt_cleanDatabase" style="width:50%;"><i class="fas fa-database"></i> {{Nettoyer}}</a>
							</div>
						</div>

						<legend><i class="fas fa-tools"></i> {{Outils Système}}</legend>
						<div class="form-group">
							<div class="row">
								<div class="alert alert-danger">
									{{ATTENTION : ces opérations sont risquées, vous pouvez perdre l'accès à votre système et à}} <?php echo $productName; ?>. <br>
									{{L'équipe}} <?php echo $productName; ?> {{se réserve le droit de refuser toute demande de support en cas de mauvaise manipulation.}}
								</div>
								<div class="form-group">
									<label class="col-md-4 col-xs-6 control-label"><i class="fas fa-indent"></i> {{Editeur de fichiers}}</label>
									<div class="col-md-5 col-xs-6">
										<a class="btn btn-danger" href="index.php?v=d&p=editor" style="width:50%;"><i class="fas fa-indent"></i> {{Ouvrir}}</a>
										<span class="small italic"> (Shift click)</span>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 col-xs-6 control-label"><i class="fas fa-terminal"></i> {{Administration Système}}
										<sup><i class="fas fa-question-circle" tooltip="{{Interface d’administration système.}}"></i></sup>
									</label>
									<div class="col-md-5 col-xs-6">
										<a class="btn btn-danger" href="index.php?v=d&p=system" style="width:50%;"><i class="fas fa-terminal"></i> {{Ouvrir}}</a>
										<span class="small italic"> (Ctrl click)</span>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 col-xs-6 control-label"><i class="fas fa-fill-drip"></i> {{Editeur en masse}}
										<sup><i class="fas fa-question-circle" tooltip="{{Edition multiple de paramètres d'équipements, commandes...}}"></i></sup>
									</label>
									<div class="col-md-5 col-xs-6">
										<a class="btn btn-danger" href="index.php?v=d&p=massedit" style="width:50%;"><i class="fas fa-fill-drip"></i> {{Ouvrir}}</a>
										<span class="small italic"> (Ctrl Alt click)</span>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 col-xs-6 control-label"><i class="fas fa-database"></i> {{Administration Base de données}}
										<sup><i class="fas fa-question-circle" tooltip="{{Interface d’administration de la base de données.}}"></i></sup>
									</label>
									<div class="col-md-5 col-xs-6">
										<a class="btn btn-danger" href="index.php?v=d&p=database" style="width:50%;"><i class="fas fa-database"></i> {{Ouvrir}}</a>
										<span class="small italic"> (Alt click)</span>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-4 col-xs-6 control-label">
										<i class="fas fa-database"></i> {{Utilisateur}} / {{Mot de passe}}
									</label>
									<div class="col-md-5 col-xs-6">
										<?php
										global $CONFIG;
										echo $CONFIG['db']['username'];
										?>
										<div class="input-group">
											<input class="inputPassword roundedLeft form-control" readonly value="<?php echo $CONFIG['db']['password']; ?>">
											<span class="input-group-btn">
												<a class="btn btn-default form-control bt_showPass roundedRight"><i class="fas fa-eye"></i></a>
											</span>
										</div>
										</span>
									</div>
								</div>

							</div>
					</fieldset>
					<br>
				</form>
			</div>
		</div>
	</div>
</div>

<?php include_file("desktop", "administration", "js"); ?>
