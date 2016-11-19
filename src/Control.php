<?php

/**
 * This file is part of the Voonne platform (http://www.voonne.org)
 *
 * Copyright (c) 2016 Jan Lavička (mail@janlavicka.name)
 *
 * For the full copyright and license information, please view the file licence.md that was distributed with this source code.
 */

namespace Voonne\Controls;

use Kdyby\Autowired\AutowireComponentFactories;
use Kdyby\Autowired\AutowireProperties;
use Nette\Application\AbortException;
use Nette\ComponentModel\IComponent;
use stdClass;


abstract class Control extends \Nette\Application\UI\Control
{

	use AutowireProperties;
	use AutowireComponentFactories;


	/**
	 * Common startup method.
	 *
	 * @return void
	 */
	public function startup()
	{

	}


	/**
	 * Common render method.
	 *
	 * @return void
	 */
	public function beforeRender()
	{

	}


	/**
	 * Redirect to another presenter, action or signal.
	 *
	 * @param integer $code
	 * @param string $destination
	 * @param array|mixed $args
	 *
	 * @throws AbortException
	 */
	public function redirect($code, $destination = null, $args = [])
	{
		$parts = explode('.', $code);

		if (count($parts) == 2) {
			$arguments = is_array($destination) ? $destination : [];
			$arguments['groupName'] = $parts[0];
			$arguments['pageName'] = $parts[1];

			$this->getPresenter()->redirect('Content:default', $arguments);
		} elseif ($code == 'this') {
			parent::redirect('this');
		} else {
			parent::redirect($code, $destination, $args);
		}
	}


	/**
	 * Generates URL to presenter, action or signal.
	 *
	 * @param string $destination
	 * @param array $args
	 *
	 * @return string
	 */
	public function link($destination, $args = [])
	{
		$parts = explode('.', $destination);

		if (count($parts) == 2) {
			$args['groupName'] = $parts[0];
			$args['pageName'] = $parts[1];

			return $this->getPresenter()->link('Content:default', $args);
		} else {
			return parent::link($destination, $args);
		}
	}


	/**
	 * Saves the message to template, that can be displayed after redirect.
	 *
	 * @param string $message
	 * @param string $type
	 *
	 * @return stdClass
	 */
	public function flashMessage($message, $type = 'info')
	{
		return $this->getPresenter()->flashMessage($message, $type);
	}


	/**
	 * Adds the specified component to the IContainer.
	 *
	 * @param IComponent $component
	 * @param string $name
	 * @param string|null $insertBefore
	 *
	 * @return $this
	 */
	public function addComponent(IComponent $component, $name, $insertBefore = null)
	{
		parent::addComponent($component, $name, $insertBefore);

		if ($component instanceof Control) {
			// STARTUP
			$component->startup();

			// BEFORE RENDER
			$component->beforeRender();
		}

		return $this;
	}

}
