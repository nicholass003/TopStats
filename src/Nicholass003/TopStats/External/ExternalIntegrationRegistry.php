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