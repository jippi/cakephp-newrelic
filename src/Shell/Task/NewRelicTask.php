<?php
declare(strict_types=1);

namespace NewRelic\Shell\Task;

use Cake\Console\Shell;
use NewRelic\Traits\NewRelicTrait;

class NewRelicTask extends Shell {

	use NewRelicTrait;

}
