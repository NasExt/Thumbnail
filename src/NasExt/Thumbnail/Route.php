<?php
/**
 * This file is part of the NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Thumbnail;

use Nette\Application\IRouter;
use Nette\Application\Routers\RouteList;
use NetteModule\MicroPresenter;

class Route extends \Nette\Application\Routers\Route {

	/** @var  ImagesLoader */
	private $imagesLoader;

	/** @var array */
	private $defaults;

	/**
	 * @param string       $mask
	 * @param array        $defaults
	 * @param int          $flags
	 * @param ImagesLoader $imagesLoader
	 */
	public function __construct($mask, array $defaults, $flags = 0, ImagesLoader $imagesLoader) {
		$this->defaults = $defaults;
		$this->imagesLoader = $imagesLoader;

		$defaults['presenter'] = 'Nette:Micro';
		$defaults['callback'] = $this;
		parent::__construct($mask, $defaults, $flags);
	}

	/**
	 * @param RouteList $router
	 * @param IRouter   $route
	 */
	public static function prependRoute(RouteList $router, IRouter $route) {
		$router[] = $route;
		$lastKey = count($router) - 1;

		foreach ($router as $i => $route) {
			if ($i === $lastKey) {
				break;
			}

			$router[$i + 1] = $route;
		}

		$router[0] = $route;
	}

	/**
	 * @param MicroPresenter $presenter
	 */
	public function __invoke($presenter) {
		$parameters = $presenter->getRequest()->getParameters();

		$storage = $parameters['storage'];
		unset($parameters['storage']);
		$namespace = $parameters['namespace'];
		unset($parameters['namespace']);
		$filename = $parameters['filename'];
		unset($parameters['filename']);
		$extension = $parameters['extension'];
		unset($parameters['extension']);
		$size = $parameters['size'];
		unset($parameters['size']);
		$algorithm = $parameters['algorithm'];
		unset($parameters['algorithm']);
		unset($parameters['callback']);

		$imageRequest = new ImageRequest(
			$storage,
			$namespace,
			$filename,
			$extension,
			$size,
			$algorithm,
			$parameters
		);

		$this->imagesLoader->generateImage($imageRequest);
	}
}
