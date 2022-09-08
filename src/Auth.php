<?php

namespace Botika\Socket;

class Auth
{
	/**
	 * Auth
	 */
	protected string $username;
	protected string $password;

	/**
	 * Constructor
	 * 
	 * @param  array  $config
	 */
	public function __construct(string $username, string $password) {
		$this->username = $username;
		$this->password = $password;
	}

	/**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
	{
		return [$this->username, $this->password];
	}
}