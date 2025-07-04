<?php

/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

try {
	require_once __DIR__ . '/../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');

	if (!isConnect()) {
		throw new Exception(__('401 - Accès non autorisé', __FILE__));
	}

	ajax::init(array('uploadImage'));

	if (init('action') == 'uploadImage') {
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}
		unautorizedInDemo();
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__));
		}
		if (init('file') == '') {
			if (!isset($_FILES['file'])) {
				throw new Exception(__('Aucun fichier trouvé. Vérifiez le paramètre PHP (post size limit)', __FILE__));
			}
			$extension = strtolower(strrchr($_FILES['file']['name'], '.'));
			if (!in_array($extension, array('.jpg', '.jpeg', '.png', '.gif', '.svg', '.webp'))) {
				throw new Exception(__('Extension du fichier non valide (autorisé .jpg .png .gif .svg .webp) :', __FILE__) . ' ' . $extension);
			}
			if (filesize($_FILES['file']['tmp_name']) > 5000000) {
				throw new Exception(__('Le fichier est trop gros (maximum 5Mo)', __FILE__));
			}
			$upfilepath = $_FILES['file']['tmp_name'];
		} else {
			$extension = strtolower(strrchr(init('file'), '.'));
			$upfilepath = init('file');
		}
		$files = ls(__DIR__ . '/../../data/eqLogic/', 'eqLogic' . $eqLogic->getId() . '-*');

		if (count($files)  > 0) {
			foreach ($files as $file) {
				unlink(__DIR__ . '/../../data/eqLogic/' . $file);
			}
		}
		$eqLogic->setConfiguration('image::type', str_replace('.', '', $extension));
		$eqLogic->setConfiguration('image::sha512', sha512(file_get_contents($upfilepath)));
		$filename = 'eqLogic' . $eqLogic->getId() . '-' . $eqLogic->getConfiguration('image::sha512') . '.' . $eqLogic->getConfiguration('image::type');
		$filepath = __DIR__ . '/../../data/eqLogic/' . $filename;
		file_put_contents($filepath, file_get_contents($upfilepath));
		if (!file_exists($filepath)) {
			throw new \Exception(__('Impossible de sauvegarder l\'image', __FILE__));
		}
		$eqLogic->save(true);
		ajax::success(array('filepath' => $eqLogic->getImage()));
	}

	if (init('action') == 'removeImage') {
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}
		unautorizedInDemo();
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('Vue inconnu. Vérifiez l\'ID', __FILE__) . ' ' . init('id'));
		}
		$eqLogic->getConfiguration('image::data', '');
		$eqLogic->getConfiguration('image::sha512', '');
		$eqLogic->save(true);
		$files = ls(__DIR__ . '/../../data/eqLogic/', 'eqLogic' . $eqLogic->getId() . '-*');
		if (count($files)  > 0) {
			foreach ($files as $file) {
				unlink(__DIR__ . '/../../data/eqLogic/' . $file);
			}
		}
		ajax::success(array('filepath' => $eqLogic->getImage()));
	}

	if (init('action') == 'getEqLogicObject') {
		$object = jeeObject::byId(init('object_id'));

		if (!is_object($object)) {
			throw new Exception(__('Objet inconnu. Vérifiez l\'ID', __FILE__));
		}
		$return = utils::o2a($object);
		$return['eqLogic'] = array();
		foreach (($object->getEqLogic()) as $eqLogic) {
			if ($eqLogic->getIsVisible() == '1') {
				$info_eqLogic = array();
				$info_eqLogic['id'] = $eqLogic->getId();
				$info_eqLogic['type'] = $eqLogic->getEqType_name();
				$info_eqLogic['object_id'] = $eqLogic->getObject_id();
				$info_eqLogic['html'] = $eqLogic->toHtml(init('version'));
				$return['eqLogic'][] = $info_eqLogic;
			}
		}
		ajax::success($return);
	}

	if (init('action') == 'byId') {
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__));
		}
		ajax::success(utils::o2a($eqLogic));
	}

	if (init('action') == 'byLogical') {
		$eqLogic = eqLogic::byLogicalId(init('logical'), init('type'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('EqLogic inconnu. Vérifiez le logicalId ou le type', __FILE__));
		}
		ajax::success(utils::o2a($eqLogic));
	}

	if (init('action') == 'toHtml') {
		if (init('ids') != '') {
			$return = array();
			foreach (json_decode(init('ids'), true) as $id => $value) {
				$eqLogic = eqLogic::byId($id);
				if (!is_object($eqLogic)) {
					continue;
				}
				$return[$eqLogic->getId()] = array(
					'html' => $eqLogic->toHtml($value['version']),
					'id' => $eqLogic->getId(),
					'type' => $eqLogic->getEqType_name(),
					'object_id' => $eqLogic->getObject_id(),
					'order' => $eqLogic->getOrder(),
					'alert' => $eqLogic->getAlert()
				);
			}
			ajax::success($return);
		} else {
			$eqLogic = eqLogic::byId(init('id'));
			if (!is_object($eqLogic)) {
				throw new Exception(__('Eqlogic inconnu. Vérifiez l\'ID', __FILE__));
			}
			$info_eqLogic = array();
			$info_eqLogic['id'] = $eqLogic->getId();
			$info_eqLogic['type'] = $eqLogic->getEqType_name();
			$info_eqLogic['object_id'] = $eqLogic->getObject_id();
			$info_eqLogic['html'] = $eqLogic->toHtml(init('version'));
			ajax::success($info_eqLogic);
		}
	}

	if (init('action') == 'htmlAlert') {
		$return = array();
		foreach ((eqLogic::all()) as $eqLogic) {
			if ($eqLogic->getAlert() == '') {
				continue;
			}
			$return[$eqLogic->getId()] = array(
				'html' => $eqLogic->toHtml(init('version')),
				'id' => $eqLogic->getId(),
				'type' => $eqLogic->getEqType_name(),
				'object_id' => $eqLogic->getObject_id(),
			);
		}
		ajax::success($return);
	}

	if (init('action') == 'htmlBattery') {
		$return = array();
		$list = array();
		foreach ((eqLogic::all()) as $eqLogic) {
			$battery_type = str_replace(array('(', ')'), array('', ''), $eqLogic->getConfiguration('battery_type', ''));
			if ($eqLogic->getIsEnable() && $eqLogic->getStatus('battery', -2) != -2) {
				$list[] = $eqLogic;
			}
		}
		usort($list, function ($a, $b) {
			return ($a->getStatus('battery') < $b->getStatus('battery')) ? -1 : (($a->getStatus('battery') > $b->getStatus('battery')) ? 1 : 0);
		});
		foreach ($list as $eqLogic) {
			$return[] = array(
				'html' => $eqLogic->batteryWidget(init('version')),
				'id' => $eqLogic->getId(),
				'type' => $eqLogic->getEqType_name(),
				'object_id' => $eqLogic->getObject_id(),
			);
		}
		ajax::success($return);
	}

	if (init('action') == 'listByType') {
		$return = array();
		foreach (eqLogic::byType(init('type')) as $eqLogic) {
			$return[$eqLogic->getId()] = utils::o2a($eqLogic);
			$return[$eqLogic->getId()]['humanName'] = $eqLogic->getHumanName();
		}
		ajax::success(array_values($return));
	}

	if (init('action') == 'listByObjectAndCmdType') {
		$object_id = (init('object_id') != -1) ? init('object_id') : null;
		ajax::success(eqLogic::listByObjectAndCmdType($object_id, init('typeCmd'), init('subTypeCmd')));
	}

	if (init('action') == 'listByObject') {
		$object_id = (init('object_id') != -1) ? init('object_id') : null;
		$getCmd = init('getCmd', false);
		$return = eqLogic::byObjectId(
			$object_id,
			init('onlyEnable', true),
			init('onlyVisible', false),
			init('eqType_name', null),
			init('logicalId', null),
			init('orderByName', false),
			is_json(init('onlyHasCmds', false), false)
		);
		if ($getCmd) {
			$fullReturn = [];
			for ($i = 0; $i < count($return); $i++) {
				$eqLogic = $return[$i];
				$cmds = $eqLogic->getCmd();
				$eq = utils::o2a($eqLogic);
				$eq['cmds'] = utils::o2a($cmds);
				$fullReturn[$i] = $eq;
			}
			ajax::success($fullReturn);
		}
		ajax::success(utils::o2a($return));
	}

	if (init('action') == 'listByTypeAndCmdType') {
		$results = eqLogic::listByTypeAndCmdType(init('type'), init('typeCmd'), init('subTypeCmd'));
		$return = array();
		foreach ($results as $result) {
			$eqLogic = eqLogic::byId($result['id']);
			$info['eqLogic'] = utils::o2a($eqLogic);
			$info['object'] = array('name' => 'Aucun');
			if (is_object($eqLogic)) {
				$object = $eqLogic->getObject();
				if (is_object($object)) {
					$info['object'] = utils::o2a($eqLogic->getObject());
				}
			}
			$return[] = $info;
		}
		ajax::success($return);
	}

	if (init('action') == 'setIsEnable') {
		unautorizedInDemo();
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__));
		}
		if (!$eqLogic->hasRight('w')) {
			throw new Exception(__('Vous n\'êtes pas autorisé à faire cette action', __FILE__));
		}
		$eqLogic->setIsEnable(init('isEnable'));
		$eqLogic->save(true);
		ajax::success();
	}

	if (init('action') == 'setOrder') {
		unautorizedInDemo();
		$eqLogics = json_decode(init('eqLogics'), true);
		foreach ($eqLogics as $eqLogic_json) {
			if (!isset($eqLogic_json['id']) || trim($eqLogic_json['id']) == '') {
				continue;
			}
			$eqLogic = eqLogic::byId($eqLogic_json['id']);
			if (!is_object($eqLogic)) {
				continue;
			}
			utils::a2o($eqLogic, $eqLogic_json);
			$eqLogic->save(true);
		}
		ajax::success();
	}

	if (init('action') == 'setGenericType') {
		unautorizedInDemo();
		$eqLogics = json_decode(init('eqLogics'), true);
		foreach ($eqLogics as $eqLogic_json) {
			if (!isset($eqLogic_json['id']) || trim($eqLogic_json['id']) == '') {
				continue;
			}
			if (!isset($eqLogic_json['generic_type'])) {
				throw new Exception(__('Pas de Type Generic fourni', __FILE__));
			}
			$eqLogic = eqLogic::byId($eqLogic_json['id']);
			if (!is_object($eqLogic)) {
				continue;
			}
			if ($eqLogic_json['generic_type'] == '') $eqLogic_json['generic_type'] = null;
			$eqLogic->setGenericType($eqLogic_json['generic_type']);
			$eqLogic->save(true);
		}
		ajax::success();
	}

	if (init('action') == 'removes') {
		unautorizedInDemo();
		$eqLogics = json_decode(init('eqLogics'), true);
		foreach ($eqLogics as $id) {
			$eqLogic = eqLogic::byId($id);
			if (!is_object($eqLogic)) {
				throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__) . ' ' . $id);
			}
			if (!$eqLogic->hasRight('w')) {
				continue;
			}
			$eqLogic->remove();
		}
		ajax::success();
	}

	if (init('action') == 'setIsVisibles') {
		unautorizedInDemo();
		$eqLogics = json_decode(init('eqLogics'), true);
		foreach ($eqLogics as $id) {
			$eqLogic = eqLogic::byId($id);
			if (!is_object($eqLogic)) {
				throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__) . ' ' . $id);
			}
			if (!$eqLogic->hasRight('w')) {
				continue;
			}
			$eqLogic->setIsVisible(init('isVisible'));
			$eqLogic->save(true);
		}
		ajax::success();
	}

	if (init('action') == 'setIsEnables') {
		unautorizedInDemo();
		$eqLogics = json_decode(init('eqLogics'), true);
		foreach ($eqLogics as $id) {
			$eqLogic = eqLogic::byId($id);
			if (!is_object($eqLogic)) {
				throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__) . ' ' . $id);
			}
			if (!$eqLogic->hasRight('w')) {
				continue;
			}
			$eqLogic->setIsEnable(init('isEnable'));
			$eqLogic->save();
		}
		ajax::success();
	}

	if (init('action') == 'simpleSave') {
		unautorizedInDemo();
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}
		$eqLogicSave = json_decode(init('eqLogic'), true);
		$eqLogic = eqLogic::byId($eqLogicSave['id']);
		if (!is_object($eqLogic)) {
			throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__) . ' ' . $eqLogicSave['id']);
		}

		if (!$eqLogic->hasRight('w')) {
			throw new Exception(__('Vous n\'êtes pas autorisé à faire cette action', __FILE__));
		}
		utils::a2o($eqLogic, $eqLogicSave);
		$eqLogic->save();
		ajax::success();
	}

	if (init('action') == 'copy') {
		unautorizedInDemo();
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__));
		}
		if (init('name') == '') {
			throw new Exception(__('Le nom de la copie de l\'équipement ne peut être vide', __FILE__));
		}
		ajax::success(utils::o2a($eqLogic->copy(init('name'))));
	}

	if (init('action') == 'getUseBeforeRemove') {
		$used = array();
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__));
		}
		$data = array('node' => array(), 'link' => array());
		$data = $eqLogic->getLinkData($data, 0, 2);
		$used = $data['node'];
		if (isset($used['eqLogic' . $eqLogic->getId()])) {
			unset($used['eqLogic' . $eqLogic->getId()]);
		}
		if (isset($used['object' . $eqLogic->getObject_id()])) {
			unset($used['object' . $eqLogic->getObject_id()]);
		}
		foreach (($eqLogic->getCmd()) as $cmd) {
			if (isset($used['cmd' . $cmd->getId()])) {
				unset($used['cmd' . $cmd->getId()]);
			}
			$cmdData = array('node' => array(), 'link' => array());
			$cmdData = $cmd->getLinkData($cmdData, 0, 2, null, false);
			if (isset($cmdData['node']['eqLogic' . $eqLogic->getId()])) {
				unset($cmdData['node']['eqLogic' . $eqLogic->getId()]);
			}
			if (isset($cmdData['node']['cmd' . $cmd->getId()])) {
				unset($cmdData['node']['cmd' . $cmd->getId()]);
			}
			if (count($cmdData['node']) > 0) {
				foreach ($cmdData['node'] as $name => $data) {
					if (cmd::byId(str_replace('cmd', '', $data['id']))->getEqLogic_id() == $eqLogic->getId()) {
						continue;
					}

					$data['sourceName'] = $cmd->getName();
					$used[$name . $cmd->getName()] = $data;
				}
			}
		}
		ajax::success($used);
	}

	if (init('action') == 'usedBy') {
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('Equipement inconnu :', __FILE__) . ' ' . init('id'), 9999);
		}
		$result = $eqLogic->getUsedBy();
		$return = array('cmd' => array(), 'eqLogic' => array(), 'scenario' => array(), 'plan' => array(), 'view' => array(), 'interactDef' => array());
		foreach ($result['cmd'] as $cmd) {
			$info = utils::o2a($cmd);
			$info['humanName'] = $cmd->getHumanName();
			$info['link'] = $cmd->getEqLogic()->getLinkToConfiguration();
			$info['linkId'] = $cmd->getId();
			$return['cmd'][] = $info;
		}
		foreach ($result['eqLogic'] as $eqLogic) {
			$info = utils::o2a($eqLogic);
			$info['humanName'] = $eqLogic->getHumanName();
			$info['link'] = $eqLogic->getLinkToConfiguration();
			$info['linkId'] = $eqLogic->getId();
			$return['eqLogic'][] = $info;
		}
		foreach ($result['scenario'] as $scenario) {
			$info = utils::o2a($scenario);
			$info['humanNameTag'] = $scenario->getHumanName(true, false, true);
			$info['humanName'] = $scenario->getHumanName();
			$info['link'] = $scenario->getLinkToConfiguration();
			$info['linkId'] = $scenario->getId();
			$return['scenario'][] = $info;
		}
		foreach ($result['plan'] as $plan) {
			$info = utils::o2a($plan);
			$info['name'] = $plan->getName();
			$info['linkId'] = $plan->getId();
			$return['plan'][] = $info;
		}
		foreach ($result['view'] as $view) {
			$info = utils::o2a($view);
			$info['name'] = $view->getName();
			$info['linkId'] = $view->getId();
			$return['view'][] = $info;
		}
		foreach ($result['interactDef'] as $interact) {
			$info = utils::o2a($interact);
			$info['humanName'] = $interact->getHumanName();
			$info['link'] = $interact->getLinkToConfiguration();
			$info['linkId'] = $interact->getId();
			$return['interactDef'][] = $info;
		}
		ajax::success($return);
	}

	if (init('action') == 'remove') {
		unautorizedInDemo();
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}
		$eqLogic = eqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__) . ' ' . init('id'));
		}
		if (!$eqLogic->hasRight('w')) {
			throw new Exception(__('Vous n\'êtes pas autorisé à faire cette action', __FILE__));
		}
		$eqLogic->remove();
		ajax::success();
	}

	if (init('action') == 'get') {
		$typeEqLogic = init('type');
		if ($typeEqLogic == '' || !class_exists($typeEqLogic)) {
			throw new Exception(__('Type incorrect (classe équipement inexistante) :', __FILE__) . ' ' . $typeEqLogic);
		}
		$eqLogic = $typeEqLogic::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('EqLogic inconnu. Vérifiez l\'ID', __FILE__) . ' ' . init('id'));
		}
		$return = utils::o2a($eqLogic);
		$return['cmd'] = array();
		foreach ($eqLogic->getCmd() as $cmd) {
			$info = utils::o2a($cmd);
			if (init('getCmdState', 0) == 1 && $cmd->getType() == 'info') {
				$state = $cmd->execCmd();
				$info['state'] = $state;
				$info['valueDate'] = $cmd->getValueDate();
				$info['collectDate'] = $cmd->getCollectDate();
			}
			$return['cmd'][] = $info;
		}
		ajax::success(jeedom::toHumanReadable($return));
	}

	if (init('action') == 'save') {
		unautorizedInDemo();
		if (!isConnect('admin')) {
			throw new Exception(__('401 - Accès non autorisé', __FILE__));
		}

		$eqLogicSaves = init('eqLogic');
		$eqLogicsSave = json_decode($eqLogicSaves, true);
		$nbrSave = count($eqLogicsSave);
		$return = array();

		foreach ($eqLogicsSave as $eqLogicSave) {

			if (!is_array($eqLogicSave)) {
				throw new Exception(__('Informations reçues incorrectes', __FILE__));
			}
			$typeEqLogic = init('type');
			$typeCmd = $typeEqLogic . 'Cmd';
			if ($typeEqLogic == '' || !class_exists($typeEqLogic) || !class_exists($typeCmd)) {
				throw new Exception(__('Type incorrect, (classe commande inexistante)', __FILE__) . $typeCmd);
			}
			$eqLogic = null;
			if (isset($eqLogicSave['id'])) {
				$eqLogic = $typeEqLogic::byId($eqLogicSave['id']);
			}
			if (!is_object($eqLogic)) {
				$eqLogic = new $typeEqLogic();
				$eqLogic->setEqType_name(init('type'));
			} else {
				if (!$eqLogic->hasRight('w')) {
					throw new Exception(__('Vous n\'êtes pas autorisé à faire cette action', __FILE__));
				}
			}
			if (method_exists($eqLogic, 'preAjax')) {
				$eqLogic->preAjax();
			}
			$eqLogicSave = jeedom::fromHumanReadable($eqLogicSave);
			utils::a2o($eqLogic, $eqLogicSave);
			$dbList = $typeCmd::byEqLogicId($eqLogic->getId());
			$eqLogic->save();
			$enableList = array();

			if (isset($eqLogicSave['cmd'])) {
				$cmd_order = 0;
				foreach ($eqLogicSave['cmd'] as $cmd_info) {
					$cmd = null;
					if (isset($cmd_info['id'])) {
						$cmd = $typeCmd::byId($cmd_info['id']);
					}
					if (!is_object($cmd)) {
						$cmd = new $typeCmd();
					}
					if (isset($cmd_info['display']['parameters'])) {
						$keys = array_map('trim', array_keys($cmd_info['display']['parameters']));
						$values = array_map('trim', array_values($cmd_info['display']['parameters']));
						$cmd_info['display']['parameters'] = array_combine($keys, $values);
					}
					$cmd->setEqLogic_id($eqLogic->getId());
					$cmd->setOrder($cmd_order);
					utils::a2o($cmd, $cmd_info);
					$cmd->save();
					$cmd_order++;
					$enableList[$cmd->getId()] = true;
				}
				foreach ($dbList as $dbObject) {
					if (!isset($enableList[$dbObject->getId()]) && !$dbObject->dontRemoveCmd()) {
						$dbObject->remove();
					}
				}
			}
			if (method_exists($eqLogic, 'postAjax')) {
				$eqLogic->postAjax();
			}
			array_push($return, utils::o2a($eqLogic));
		}
		if ($nbrSave > 1) ajax::success($return);
		ajax::success(utils::o2a($eqLogic));
	}

	if (init('action') == 'getAlert') {
		$alerts = array();
		foreach ((eqLogic::all()) as $eqLogic) {
			if ($eqLogic->getAlert() == '') {
				continue;
			}
			$alerts[] = $eqLogic->toHtml(init('version'));
		}
		ajax::success($alerts);
	}

	throw new Exception(__('Aucune méthode correspondante à :', __FILE__) . ' ' . init('action'));
	/*     * *********Catch exeption*************** */
} catch (Exception $e) {
	ajax::error(displayException($e), $e->getCode());
}
