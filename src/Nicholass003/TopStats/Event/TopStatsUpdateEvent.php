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

namespace Nicholass003\TopStats\Event;

use Nicholass003\TopStats\Leaderboard\Leaderboard;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

/**
 * Fired when leaderboards of a specific type are about to be updated.
 *
 * This event can be cancelled to prevent the update process.
 */
class TopStatsUpdateEvent extends TopStatsEvent implements Cancellable{
	use CancellableTrait;

	/**
	 * @param string                  $type
	 * @param array<int, Leaderboard> $leaderboards
	 */
	public function __construct(
		private string $type,
		private array $leaderboards = []
	){}

	/**
	 * Leaderboard type being updated.
	 */
	public function getType() : string{
		return $this->type;
	}

	/**
	 * Leaderboards affected by this update.
	 *
	 * @return array<int, Leaderboard>
	 */
	public function getLeaderboards() : array{
		return $this->leaderboards;
	}
}