<?php
namespace NewRelic\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;
use NewRelic\Traits\NewRelicTrait;

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
	public function beforeFilter(Event $event) {
		$this->setName($event->getSubject()->request);
		$this->start();

		$controller = $event->getSubject();
		if (isset($controller->Auth)) {
			$this->user($controller->Auth->user('id'), $controller->Auth->user('email'), '');
		}

		$this->captureParams(true);
	}

}
