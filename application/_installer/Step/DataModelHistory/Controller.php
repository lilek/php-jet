<?php
/**
 *
 *
 *
 * @copyright Copyright (c) 2012-2013 Miroslav Marek <mirek.marek.2m@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0.html AGPLv3
 * @author Miroslav Marek <mirek.marek.2m@gmail.com>
 * @version <%VERSION%>
 *
 * @category Jet
 * @package Installer
 */
namespace Jet;

class Installer_Step_DataModelHistory_Controller extends Installer_Step_Controller {


	public function main() {
		$main_config = new DataModel_Config(true);

		$config = DataModel_Factory::getHistoryBackendConfigInstance($main_config->getHistoryBackendType(), true);
		$form = $config->getCommonForm();

		if( $config->catchForm($form) ) {
			$config->save();

			$this->installer->goNext();
		}

		$this->view->setVar("form", $form);
		$this->view->setVar("config", $config);
		$this->render("default");
	}

	public function getLabel() {
		return Tr::_("History configuration", array(), "DataModelHistory");
	}

	public function getIsSubstep() {
		return true;
	}
}
