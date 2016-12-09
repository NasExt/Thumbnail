<?php
/**
 * This file is part of the NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Thumbnail;

use NasExt\Thumbnail\Helpers\Converters;
use NasExt\Thumbnail\Templating\Helpers;
use Nette\Application\BadRequestException;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\InvalidArgumentException;
use Nette\Object;

final class ImagesLoader extends Object {

	/** @var IStorage[] */
	private $storages = array();

	/** @var IRequest */
	private $httpRequest;

	/** @var IResponse */
	private $httpResponse;

	/** @var string */
	private $thumbsDir;

	/** @var  Validator */
	private $validator;

	/**
	 * @param           $thumbsDir
	 * @param IRequest  $httpRequest
	 * @param IResponse $httpResponse
	 * @param Validator $validator
	 */
	public function __construct($thumbsDir, IRequest $httpRequest, IResponse $httpResponse, Validator $validator) {
		$this->thumbsDir = $thumbsDir;
		$this->httpRequest = $httpRequest;
		$this->httpResponse = $httpResponse;
		$this->validator = $validator;
	}

	/**
	 * @param array $arguments
	 * @return array|string
	 */
	public function getParams(array $arguments) {

		try {
			$storageName = isset($arguments['storage']) ? $arguments['storage'] : key($this->storages);
			$size = Converters::createSizeString($arguments['size']);
			$algorithm = Converters::createAlgorithmString($arguments['algorithm']);
			$namespace = $this->getStorage($storageName)->getNamespace($arguments['namespace']);
			$file = new \SplFileInfo($arguments['filename']);
			list($width, $height) = Converters::parseSizeString($arguments['size']);

			if ($size !== 'original' && !$this->validator->validate($width, $height, $algorithm, $storageName)) {
				throw new NotAllowedImageException(sprintf('Size "%s" of image "%s" in storage "%s" is not allowed in defined rules', $size . '-' . $algorithm, (($namespace === NULL ? NULL : $namespace . DIRECTORY_SEPARATOR) . $arguments['filename']), $storageName));
			}

			$params = array(
				'storage' => $storageName,
				'namespace' => $namespace,
				'filename' => basename($file->getBasename(), '.' . $file->getExtension()),
				'extension' => $file->getExtension(),
				'size' => $size,
				'algorithm' => $algorithm,
			);

			$params = array_merge($arguments['parameters'], $params);

			return $params;
		} catch (\Exception $e) {
			return '#error: ' . $e->getMessage();
		}
	}

	/**
	 * @param ImageRequest $request
	 * @throws BadRequestException
	 */
	public function generateImage(ImageRequest $request) {
		try {
			$storageName = $request->getStorage();
			$size = $request->getSize();
			$algorithm = $request->getAlgorithm();
			$namespace = $this->getStorage($storageName)->getNamespace($request->getNamespace());
			list($width, $height) = Converters::parseSizeString($request->getSize());

			if ($size !== 'original' && !$this->validator->validate($width, $height, $algorithm, $storageName)) {
				throw new NotAllowedImageException(sprintf('Size "%s" of image "%s" in storage "%s" is not allowed in defined rules', $size . '-' . $algorithm, (($namespace === NULL ? NULL : $namespace . DIRECTORY_SEPARATOR) . $request->getFilename()), $storageName));
			}

			$storage = $this->getStorage($request->getStorage());
			$image = $storage->getImage($request);
		} catch (\Exception $e) {
			$this->httpResponse->setHeader('Content-Type', 'image/jpeg');
			$this->httpResponse->setCode(IResponse::S404_NOT_FOUND);
			exit;
		}

		$destination = $this->thumbsDir . $this->httpRequest->getUrl()->getPath();
		$dirName = dirname($destination);
		if (!is_dir($dirName)) {
			$success = @mkdir($dirName, 0777, TRUE);
			if (!$success) {
				throw new BadRequestException;
			}
		}
		$success = $image->save($destination, 90);
		if (!$success) {
			throw new BadRequestException;
		}
		$image->send();
		exit;
	}

	/**
	 * @param string $name
	 * @return IStorage
	 */
	public function getStorage($name) {
		if (isset($this->storages[$name])) {
			return $this->storages[$name];
		}

		throw new InvalidArgumentException('Thumbnail storage "' . $name . '" is not registered.');
	}

	/**
	 * @param string   $name
	 * @param IStorage $storage
	 */
	public function addStorage($name, IStorage $storage) {
		$this->storages[(string)$name] = $storage;
	}

	/**
	 * @return Helpers
	 */
	public function createTemplateHelpers() {
		return new Helpers($this);
	}
}

class NotAllowedImageException extends BadRequestException {

}
