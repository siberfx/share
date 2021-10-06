<?php namespace Siberfx\Share\Facade;

use Illuminate\Support\Facades\Facade;

class Share extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() {
		return 'share';
	}

}