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
 * Defines a contract for integrating external data sources into TopStats.
 *
 * Implementations are responsible for:
 * - Declaring a unique type identifier
 * - Providing the event class they listen to
 * - Extracting structured leaderboard data from events
 */
interface ExternalIntegration extends ExternalTypeNames{

	/**
	 * Returns a unique identifier for this integration.
	 *
	 * Example: "vote", "kdr", "playtime"
	 */
	public function getType() : string;

	/**
	 * Returns the fully-qualified class name of the event
	 * this integration listens to.
	 *
	 * @return class-string<Event>
	 */
	public function getEventClass() : string;

	/**
	 * Extracts leaderboard data from the given event.
	 *
	 * The returned array must follow this structure:
	 *
	 * [
	 *     [
	 *         "name" => "PlayerA",
	 *         "value" => 123
	 *     ],
	 *     [
	 *         "name" => "PlayerB",
	 *         "value" => 456
	 *     ]
	 * ]
	 *
	 * @param Event $event
	 * @return list<array{name: string, value: int|float}>
	 */
	public function extractData(object $event) : array;

	/**
	 * Whether this integration already sorts its data internally.
	 *
	 * If true, TopStats will not apply additional sorting.
	 * If false, TopStats will sort the data after extraction.
	 */
	public function isForceSorting() : bool;
}

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

/**
 * Registry for managing all external integrations.
 *
 * Responsible for:
 * - Registering integrations
 * - Listening to events
 * - Dispatching leaderboard updates
 */
final class ExternalIntegrationRegistry{
	use SingletonTrait;

	/** @var array<string, ExternalSource> */
	private array $sources = [];

	/** @var array<string, ExternalIntegration> */
	private array $integrations = [];

	/**
	 * Registers a new external integration and binds its event listener.
	 *
	 * @param ExternalIntegration $integration
	 * @param int                 $eventPriority
	 */
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

	/**
	 * Unregisters an integration by type.
	 */
	public function unregister(string $type) : void{
		unset($this->sources[$type], $this->integrations[$type]);
	}

	/**
	 * Handles incoming events and updates the corresponding data source.
	 */
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

	/**
	 * Returns the data source for a given type.
	 *
	 * @return ExternalSource|null
	 */
	public function getSource(string $type) : ?ExternalSource{
		return $this->sources[$type] ?? null;
	}

	/**
	 * Returns all active integration types.
	 *
	 * @return list<string>
	 */
	public function getActiveTypes() : array{
		return array_keys($this->integrations);
	}
}
