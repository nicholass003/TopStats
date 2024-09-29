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

namespace nicholass003\topstats\command\subcommand;

use nicholass003\topstats\libs\_49be177cdacfd2f0\CortexPE\Commando\BaseSubCommand;
use nicholass003\topstats\leaderboard\LeaderboardManager;
use nicholass003\topstats\TopStats;
use pocketmine\plugin\Plugin;

abstract class TopStatsSubCommand extends BaseSubCommand{

	protected LeaderboardManager $leaderboardManager;

	/** @var TopStats */
	protected Plugin $plugin;

	public function __construct(
		TopStats $plugin,
		string $name,
		string $description = "",
		array $aliases = []
	){
		parent::__construct($plugin, $name, $description, $aliases);
		$this->leaderboardManager = $plugin->getLeaderboardManager();
	}
}