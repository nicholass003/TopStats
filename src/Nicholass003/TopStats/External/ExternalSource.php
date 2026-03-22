<?php

/*
 * Copyright (c) 2024 - present nicholass003
 *        _      _           _                ___   ___ ____
 *       (_)    | |         | |              / _ \ / _ \___ \
 *  _ __  _  ___| |__   ___ | | __ _ ___ ___| | | | | | |__) |
 * | '_ \| |/ __| '_ \ / _ \| |/ _` / __/ __| | | | | | |__ <
 * | | | | | (__| | | | (_) | | (_| \__ \__ \ |_| | |_| |__) |
 * |_| |_|_|\___|_| |_|\___/|_|\__,_|___/___/\___/ \___/____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  nicholass003
 * @link    https://github.com/nicholass003/
 *
 *
 */

declare(strict_types=1);

namespace Nicholass003\TopStats\External;

use Nicholass003\TopStats\TopStats;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use function array_keys;
























































/**
 * Represents a data source produced by an ExternalIntegration.
 *
 * Stores the latest extracted entries for a specific type.
 */
class ExternalSource{

	/**
	 * @param string                                      $type
	 * @param ExternalIntegration                         $integration
	 * @param list<array{name: string, value: int|float}> $entries
	 */
	public function __construct(
		private string $type,
		private ExternalIntegration $integration,
		private array $entries = []
	){}

	public function getType() : string{
		return $this->type;
	}

	public function getIntegration() : ExternalIntegration{
		return $this->integration;
	}

	/**
	 * @return list<array{name: string, value: int|float}>
	 */
	public function getEntries() : array{
		return $this->entries;
	}

	/**
	 * @param list<array{name: string, value: int|float}> $entries
	 */
	public function setEntries(array $entries) : void{
		$this->entries = $entries;
	}
}