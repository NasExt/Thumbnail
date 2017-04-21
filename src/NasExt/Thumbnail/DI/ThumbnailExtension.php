<?php
/**
 * This file is part of the NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Thumbnail\DI;


use Nas\CmsModule\AdminModule\Controls\InvalidArgumentException;
use NasExt\Thumbnail\Helpers;
use NasExt\Thumbnail\LinkGenerator;
use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;

class ThumbnailExtension extends CompilerExtension {

	/** @var array */
	private $defaults = [
		'thumbsDir' => '%wwwDir%/thumbs',
		'prependRoutesToRouter' => TRUE,
		'routes' => [],
		'storages' => [],
		'rules' => [],
	];

	public function loadConfiguration() {
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$imagesLoader = $builder->addDefinition($this->prefix('imagesLoader'))
			->setClass('NasExt\Thumbnail\ImagesLoader', [$config['thumbsDir']]);

		$builder->addDefinition($this->prefix('linkGenerator'))
			->setClass(LinkGenerator::class);

		// Create validator
		$validator = $builder->addDefinition($this->prefix('validator'))
			->setClass('NasExt\Thumbnail\Validator');
		$this->registerRules($config['rules'], $validator);

		// Prepare router
		if ($config['routes']) {
			$router = $builder->addDefinition($this->prefix('router'))
				->setClass('Nette\Application\Routers\RouteList')
				->addTag($this->prefix('routeList'))
				->setAutowired(FALSE);

			$i = 0;
			foreach ($config['routes'] as $definition) {
				if (!is_array($definition)) {
					$definition = [
						'mask' => $definition,
						'defaults' => [],
						'secured' => FALSE,
					];
				} else {
					if (!isset($definition['defaults'])) {
						$definition['defaults'] = [];
					}
					if (!isset($definition['secured'])) {
						$definition['secured'] = FALSE;
					}
				}

				if (empty($definition['mask']) || is_string($definition['mask']) === FALSE) {
					throw new InvalidArgumentException('Provided route is not valid.');
				}

				$builder->addDefinition($this->prefix('route.' . $i))
					->setClass('NasExt\Thumbnail\Route', [$definition['mask'], $definition['defaults'], $definition['secured']])
					->setAutowired(FALSE)
					->addTag($this->prefix('route'))
					->setInject(FALSE);

				// Add route to router
				$router->addSetup('$service[] = ?', [$this->prefix('@route.' . $i)]);

				$i++;
			}
		}

		// Prepare storages
		if (count($config['storages']) === 0) {
			throw new \NasExt\Framework\DI\InvalidArgumentException("You have to register at least one IStorage in '" . $this->prefix('storages') . "' directive.");
		}

		foreach ($config['storages'] as $name => $storage) {
			$this->compiler->parseServices(
				$builder,
				['services' => [$this->prefix('storage.' . $name) => $storage]]
			);
			$imagesLoader->addSetup('addStorage', [$name, $this->prefix('@storage.' . $name)]);
		}

		// Register template helper for ImagesLoader
		$builder->addDefinition($this->prefix('helpers'))
			->setClass('NasExt\Thumbnail\Templating\Helpers')
			->setFactory($this->prefix('@linkGenerator') . '::createTemplateHelpers')
			->setInject(FALSE);
	}

	/**
	 * @inheritdoc
	 */
	public function beforeCompile() {
		parent::beforeCompile();

		// Get container builder
		$builder = $this->getContainerBuilder();
		// Get extension configuration
		$config = $this->getConfig($this->defaults);

		// Install extension latte macros
		$latteFactory = $builder->getDefinition($builder->getByType('\Nette\Bridges\ApplicationLatte\ILatteFactory') ?: 'nette.latteFactory');

		$latteFactory->addSetup('NasExt\Thumbnail\Latte\Macros::install(?->getCompiler())', ['@self'])
			->addSetup('addFilter', ['imageLink', [$this->prefix('@helpers'), 'imageLink']]);

		if ($config['prependRoutesToRouter']) {
			$router = $builder->getByType('Nette\Application\IRouter');
			if ($router) {
				if (!$router instanceof ServiceDefinition) {
					$router = $builder->getDefinition($router);
				}
			} else {
				$router = $builder->getDefinition('router');
			}
			$router->addSetup(
				'NasExt\Thumbnail\Route::prependRoute',
				[
					'@self',
					$this->prefix('@router'),
				]
			);
		}
	}

	/**
	 * @param array             $rules
	 * @param ServiceDefinition $validator
	 * @throws AssertionException
	 */
	private function registerRules(array $rules = [], ServiceDefinition $validator) {
		foreach ($rules as $rule) {
			// Check for valid rules values
			Validators::assert($rule['width'], 'int|null', 'Rule width');
			Validators::assert($rule['height'], 'int|null', 'Rule height');
			$validator->addSetup(
				'$service->addRule(?, ?, ?, ?)',
				[
					$rule['width'],
					$rule['height'],
					isset($rule['algorithm']) ? $rule['algorithm'] : NULL,
					isset($rule['storage']) ? $rule['storage'] : NULL,
				]
			);
		}
	}
}
