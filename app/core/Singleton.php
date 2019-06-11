<?php
namespace Core;

/**
 * Singleton class
 *
 */
class Singleton
{
	private static $_instances = [];

	/**
	 * Call this method to get singleton
	 */
	public static function getInstance()
	{
		self::$_instances[static::class] = self::$_instances[static::class] ?? new static();
		return self::$_instances[static::class];
	}

	/**
	 * Make constructor private, so nobody can call "new Class".
	 */
	private function __construct()
	{}
}
