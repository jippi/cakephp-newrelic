<?php

App::uses('NewRelic', 'NewRelic.Lib');
App::uses('NewRelicTrait', 'NewRelic.Trait');

/**
 * New Relic Component
 *
 * @author Christian Winther
 */
class NewRelicComponent extends Component {

	use NewRelicTrait;

/**
 * Called before the Controller::beforeFilter().
 *
 * Start NewRelic and configure transaction name
 *
 * @param Controller $controller
 * @return void
 */
	public function initialize(Controller $controller) {
		$this->setName($controller->request);
		$this->start();

		if ($controller->Auth) {
			$this->user($controller->Auth->user('id'), $controller->Auth->user('email'), '');
		}

		$this->captureParams(true);
	}

}
