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

		$this->setName($event->data['request']);
		$this->start();

		$controller = $event->data['controller'];
		if (isset($controller->Auth)) {
			$this->user($controller->Auth->user('id'), $controller->Auth->user('email'), '');
		}

		$this->captureParams(true);

		$this->addTracer('CakeRoute::match');
		$this->addTracer('CrudComponent::executeAction');
		$this->addTracer('Controller::render');
		$this->addTracer('View::render');
		$this->addTracer('View::element');
		$this->addTracer('View::renderLayout');
		$this->addTracer('DboSource::_execute');
		$this->addTracer('AttemptChecker::attempt');
	}

}
