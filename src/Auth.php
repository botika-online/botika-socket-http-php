<?php

namespace Botika\Socket;

use InvalidArgumentException;

class Auth
{
	protected array $config;

	/**
	 * Constructor
	 * 
	 * @param  array  $config
	 */
	public function __construct(array $config = []) {
		if (! isset($config['key']) || empty($config['key'])) {
			throw new InvalidArgumentException('Auth Key is required');
		}

		$this->config = $config;
	}

	/**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
	{
		return $this->config;
	}
}