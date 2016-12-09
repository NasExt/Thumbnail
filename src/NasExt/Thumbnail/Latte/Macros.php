<?php
/**
 * This file is part of the NasExt extensions of Nette Framework
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Thumbnail\Latte;

use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

class Macros extends MacroSet {

	/**
	 * @param Compiler $compiler
	 * @return static
	 */
	public static function install(Compiler $compiler) {
		$me = new static($compiler);
		self::registerMacro('src', $me);
		self::registerMacro('img', $me);
		return $me;
	}

	/**
	 * @param array $macro
	 * @return array
	 */
	public static function prepareArguments(array $macro) {
		preg_match("/\\b((?P<storage>[a-zA-Z0-9]+)::)?(?:(?<namespace>[a-zA-Z0-9-_\\/\\\\\\:]+)[\\/\\\\])?(?<name>[a-zA-Z0-9-_]+).(?P<extension>[a-zA-Z]{3}+)/i", $macro[0], $matches);

		$arguments = array(
			'storage' => isset($matches['storage']) && !empty($matches['storage']) ? $matches['storage'] : NULL,
			'namespace' => isset($matches['namespace']) && trim(trim($matches['namespace']), '/') ? $matches['namespace'] : NULL,
			'filename' => isset($matches['name']) && isset($matches['extension']) ? $matches['name'] . '.' . $matches['extension'] : NULL,
			'size' => (isset($macro[1]) && !empty($macro[1])) ? $macro[1] : NULL,
			'algorithm' => (isset($macro[2]) && !empty($macro[2])) ? $macro[2] : NULL,
		);

		unset($macro[0]);

		if (array_key_exists(1, $macro)) {
			unset($macro[1]);
		}

		if (array_key_exists(2, $macro)) {
			unset($macro[2]);
		}

		$arguments['parameters'] = $macro;
		return $arguments;
	}

	/**
	 * @param string $name
	 * @param Macros $macros
	 */
	private static function registerMacro($name, Macros $macros) {
		$macros->addMacro(
			$name, function (MacroNode $node, PhpWriter $writer) use ($macros) {
			return $macros->macroSrc($node, $writer);
		}, NULL, function (MacroNode $node, PhpWriter $writer) use ($macros) {
			return ' ?> ' . ($node->htmlNode->name === 'a' ? 'href' : 'src') . '="<?php ' . $macros->macroSrc($node, $writer) . ' ?>"<?php ';
		}
		);
	}

	/**
	 * @param MacroNode $node
	 * @param PhpWriter $writer
	 * @return string
	 */
	public function macroSrc(MacroNode $node, PhpWriter $writer) {
		$absolute = substr($node->args, 0, 2) === '//' ? '//' : '';
		$args = $absolute ? substr($node->args, 2) : $node->args;
		return $writer->write('echo %escape(%modify($_presenter->link("' . $absolute . ':Nette:Micro:", $template->imageLink()->getParams(NasExt\Thumbnail\Latte\Macros::prepareArguments([' . $args . '])))))');
	}
}
