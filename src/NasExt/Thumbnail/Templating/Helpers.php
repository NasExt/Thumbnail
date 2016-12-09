<?php
/**
 * This file is part of the NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Thumbnail\Templating;

use NasExt\Thumbnail\ImagesLoader;
use Nette\Object;

final class Helpers extends Object {

	/**
	 * @var ImagesLoader
	 */
	private $imagesLoader;

	/**
	 * @param ImagesLoader $imagesLoader
	 */
	public function __construct(ImagesLoader $imagesLoader) {
		$this->imagesLoader = $imagesLoader;
	}

	/**
	 * @return ImagesLoader
	 */
	public function imageLink() {
		return $this->imagesLoader;
	}
}
