<?php
/**
 * This file is part of the NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Thumbnail;

use Nette\Object;
use NasExt\Thumbnail\Templating\Helpers;

final class LinkGenerator extends Object {

	/** @var  ImagesLoader */
	private $imagesLoader;

	/** @var \Nette\Application\LinkGenerator */
	private $linkGenerator;

	/**
	 * @param ImagesLoader                     $imagesLoader
	 * @param \Nette\Application\LinkGenerator $linkGenerator
	 */
	public function __construct(ImagesLoader $imagesLoader, \Nette\Application\LinkGenerator $linkGenerator) {
		$this->imagesLoader = $imagesLoader;
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public static function prepareArguments(array $params) {
		preg_match("/\\b((?P<storage>[a-zA-Z0-9]+)::)?(?:(?<namespace>[a-zA-Z0-9-_\\/\\\\\\:]+)[\\/\\\\])?(?<name>[a-zA-Z0-9-_]+).(?P<extension>[a-zA-Z]{3}+)/i", $params[0], $matches);

		$arguments = array(
			'storage' => isset($matches['storage']) && !empty($matches['storage']) ? $matches['storage'] : NULL,
			'namespace' => isset($matches['namespace']) && trim(trim($matches['namespace']), '/') ? $matches['namespace'] : NULL,
			'filename' => isset($matches['name']) && isset($matches['extension']) ? $matches['name'] . '.' . $matches['extension'] : NULL,
			'size' => (isset($params[1]) && !empty($params[1])) ? $params[1] : NULL,
			'algorithm' => (isset($params[2]) && !empty($params[2])) ? $params[2] : NULL,
		);

		unset($params[0]);

		if (array_key_exists(1, $params)) {
			unset($params[1]);
		}

		if (array_key_exists(2, $params)) {
			unset($params[2]);
		}

		$arguments['parameters'] = $params;
		return $arguments;
	}

	/**
	 * @param array  $arguments
	 * [
	 * array('someStorage::products/filename.jpg', '200x200', 'fill', array('someUrlParam' => 'someurlValue'))
	 * ]
	 * @param string $destination
	 * @return string
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function link($arguments, $destination = 'Nette:Micro:') {
		$arguments = self::prepareArguments($arguments);
		$params = $this->imagesLoader->getParams($arguments);
		return $this->linkGenerator->link($destination, $params);
	}

	/**
	 * @return Helpers
	 */
	public function createTemplateHelpers() {
		return new Helpers($this);
	}
}
