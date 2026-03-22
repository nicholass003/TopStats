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

namespace Nicholass003\TopStats\Database;

use pocketmine\player\Player;

/**
 * Defines a database driver for TopStats.
 *
 * Implementations handle loading, storing, and updating
 * player statistics data.
 */
interface IDatabase{

	/**
	 * Database driver name.
	 */
	public function getName() : string;

	/**
	 * Load all data into memory.
	 */
	public function loadData() : void;

	/**
	 * Get all cached player data.
	 *
	 * @return array<string, array<string, int|float|string>>
	 */
	public function getTemporaryData() : array;

	/**
	 * Get a specific value for a player.
	 *
	 * @param Player $player
	 * @param string $type
	 * @return int|float|string|false
	 */
	public function getTemporaryDataValue(Player $player, string $type) : mixed;

	/**
	 * Persist all data to storage.
	 */
	public function saveData() : void;

	/**
	 * Create a new data entry for a player.
	 */
	public function create(Player $player) : void;

	/**
	 * Update player data with a specific action.
	 *
	 * @param Player                   $player
	 * @param array<string, int|float> $data
	 * @param int                      $action One of DataAction constants
	 */
	public function update(Player $player, array $data, int $action) : void;
}