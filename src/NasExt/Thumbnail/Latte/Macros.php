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
		return $writer->write(
			'echo %escape(
				%modify(
					call_user_func(
							($template && method_exists($template, "imageLink") ? $template->imageLink : $this->filters->imageLink),
							[' . $args . ']
						)
				)
			)'
		);
	}
}
