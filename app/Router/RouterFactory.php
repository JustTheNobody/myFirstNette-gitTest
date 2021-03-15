<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
		$router->addRoute('<presenter>/calculator<action>[/<id>]', 'Calculator:default');
		//Article
		$router->addRoute('<presenter>/article<action>[/<id>]', 'Article:default');
		$router->addRoute('<presenter>/add<action>[/<id>]', 'Article:add');
		$router->addRoute('<presenter>/detele<action>[/<id>]', 'Article:delete');
		$router->addRoute('<presenter>/edit<action>[/<id>]', 'Article:edit');
		//Auth
		$router->addRoute('<presenter>/login<action>[/<id>]', 'Login:default');
		$router->addRoute('<presenter>/register<action>[/<id>]', 'Register:default');
		return $router;
	}
}