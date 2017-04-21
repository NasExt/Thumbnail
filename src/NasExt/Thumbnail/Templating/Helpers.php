<?php
/**
 * This file is part of the NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Thumbnail\Templating;

use NasExt\Thumbnail\LinkGenerator;
use Nette\Object;

final class Helpers extends Object {

	/** @var LinkGenerator */
	private $linkGenerator;

	/**
	 * @param LinkGenerator $linkGenerator
	 */
	public function __construct(LinkGenerator $linkGenerator) {
		$this->linkGenerator = $linkGenerator;
	}

	/**
	 * @param array $arguments
	 * @return string
	 */
	public function imageLink($arguments) {
		return $this->linkGenerator->link($arguments);
	}
}
