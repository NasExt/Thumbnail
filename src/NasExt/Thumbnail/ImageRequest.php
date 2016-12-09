<?php
/**
 * This file is part of the NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Thumbnail;

use Nette\Object;

class ImageRequest extends Object
{
	/** @var string */
	private $storage;

	/** @var string */
	private $namespace;

	/** @var string */
	private $filename;

	/** @var string */
	private $extension;

	/** @var string */
	private $size;

	/** @var string */
	private $algorithm;

	/** @var array */
	private $parameters;

	/**
	 * @param string $storage
	 * @param string $namespace
	 * @param string $filename
	 * @param string $extension
	 * @param string $size
	 * @param string $algorithm
	 * @param array  $parameters
	 */
	public function __construct($storage, $namespace, $filename, $extension, $size, $algorithm, array $parameters)
	{
		$this->storage = $storage;
		$this->namespace = $namespace;
		$this->filename = $filename;
		$this->size = $size;
		$this->algorithm = $algorithm;
		$this->parameters = $parameters;
		$this->extension = $extension;
	}

	/**
	 * @return string
	 */
	public function getStorage()
	{
		return $this->storage;
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * @return string
	 */
	public function getExtension()
	{
		return $this->extension;
	}

	/**
	 * @return string
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return string
	 */
	public function getAlgorithm()
	{
		return $this->algorithm;
	}

	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}
}
