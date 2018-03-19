<?php
/**
 * This file is part of the NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Thumbnail;

use Nette\InvalidArgumentException;
use Nette\SmartObject;

class Validator {

	use SmartObject;

	/** @var array */
	private $rules = [];

	/**
	 * @param int|NULL        $width
	 * @param int|NULL        $height
	 * @param int|string|NULL $algorithm
	 * @param string|NULL     $storage
	 */
	public function addRule($width = NULL, $height = NULL, $algorithm = NULL, $storage = NULL) {
		if ($width === NULL && $height === NULL) {
			throw new InvalidArgumentException('Width or height have to be defined!');
		}

		$this->rules[] = [
			'width' => $width ? (int)$width : NULL,
			'height' => $height ? (int)$height : NULL,
			'algorithm' => $algorithm === NULL ? NULL : (string)$algorithm,
			'storage' => $storage === NULL ? NULL : $storage,
		];
	}

	/**
	 * @param int|NULL    $width
	 * @param int|NULL    $height
	 * @param int         $algorithm
	 * @param string|NULL $storage
	 * @return bool
	 */
	public function validate($width = NULL, $height = NULL, $algorithm = NULL, $storage = NULL) {
		if (!count($this->rules)) {
			return TRUE;
		}

		foreach ($this->rules as $rule) {
			if ($rule['storage'] !== NULL && $rule['storage'] !== $storage) {
				continue;
			}

			if (($width === $rule['width'] || $rule['width'] === NULL) && ($height === $rule['height'] || $rule['height'] === NULL)) {
				if ($rule['algorithm'] !== NULL && $rule['algorithm'] !== $algorithm) {
					continue;
				}

				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Returns all added rules
	 * @return array
	 */
	public function getRules() {
		return $this->rules;
	}
}
