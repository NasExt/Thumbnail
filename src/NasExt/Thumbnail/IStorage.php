<?php
/**
 * This file is part of the NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Thumbnail;

use Nette\Utils\Image;

interface IStorage {

	/**
	 * @param ImageRequest $request
	 * @return Image
	 * @throws \Nette\Utils\UnknownImageFileException
	 */
	public function getImage(ImageRequest $request);

	/**
	 * @param ImageRequest $request
	 * @return bool
	 */
	public function isAllowed(ImageRequest $request);

	/**
	 * @param string $namespace
	 * @return string|NULL
	 */
	public function getNamespace($namespace);
}
