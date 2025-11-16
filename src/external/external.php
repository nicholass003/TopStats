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

interface ExternalIntegration extends ExternalTypeNames{

	/**
	 * Return unique type string (ex: "vote", "kdr", "playtime")
	 */
	public function getType() : string;

	/**
	 * Class of events to be handled
	 */
	public function getEventClass() : string;

	/**
	 * Extract data from event → format:
	 * [
	 *   "PlayerA" => value,
	 *   "PlayerB" => value,
	 * ]
	 */
	public function extractData(object $event) : array;

	/**
	 * Indicates whether the integration already performs its own sorting
	 * inside `extractData()`, meaning TopStats should NOT sort the data again.
	 *
	 * If this returns **true**, the integration guarantees that the array returned
	 * by `extractData()` is already properly sorted (usually in descending order),
	 * and therefore TopStats must use the data as-is without applying any
	 * additional sorting.
	 *
	 * If this returns **false**, TopStats will apply its internal sorting logic
	 * to the extracted data.
	 *
	 * @return bool  True if integration handles sorting; false if TopStats should sort.
	 */
	public function isForceSorting() : bool;
}

class ExternalSource{

	/**
	 * @param array<string, int|float> $entries
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

	public function getEntries() : array{
		return $this->entries;
	}

	public function setEntries(array $entries) : void{
		$this->entries = $entries;
	}
}

final class ExternalIntegrationRegistry{
	use SingletonTrait;

	/** @var array<string, ExternalSource> */
	private array $sources = [];

	/** @var array<string, ExternalIntegration> */
	private array $integrations = [];

	public function register(ExternalIntegration $integration, int $eventPriority = EventPriority::NORMAL) : void{
		$type = $integration->getType();
		$eventClass = $integration->getEventClass();

		$this->integrations[$type] = $integration;

		$this->sources[$type] = new ExternalSource($type, $integration);

		Server::getInstance()
			->getPluginManager()
			->registerEvent(
				$eventClass,
				function(Event $ev) use($type, $integration) : void{
					$this->handleEvent($type, $integration, $ev);
				},
				$eventPriority,
				TopStats::getInstance()
			);
	}

	public function unregister(string $type) : void{
		unset($this->sources[$type]);
	}

	private function handleEvent(string $type, ExternalIntegration $integration, Event $ev) : void{
		$data = $integration->extractData($ev);

		$source = $this->getSource($type);
		if($source === null){
			return;
		}

		$source->setEntries($data);

		TopStats::getInstance()
			->getLeaderboardManager()
			->dispatchLeaderboardUpdate($type, $source->getEntries());
	}

	public function getSource(string $type) : ?ExternalSource{
		return $this->sources[$type] ?? null;
	}

	/**
	 * Return list of active external types (ex: ["vote", "kdr", ...])
	 */
	public function getActiveTypes() : array{
		return array_keys($this->integrations);
	}
}
