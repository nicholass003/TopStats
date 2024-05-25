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

namespace nicholass003\topstats;

use nicholass003\topstats\command\TopStatsCommand;
use nicholass003\topstats\database\IDatabase;
use nicholass003\topstats\database\JsonDatabase;
use nicholass003\topstats\leaderboard\LeaderboardManager;
use nicholass003\topstats\listener\EventListener;
use nicholass003\topstats\task\UpdateTask;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use function strtolower;

class TopStats extends PluginBase{
	use SingletonTrait;

	public const MAX_LIST = 10;

	protected IDatabase $database;
	protected LeaderboardManager $leaderboardManager;

	protected function onEnable() : void{
		self::setInstance($this);
		$this->leaderboardManager = new LeaderboardManager($this);
		$this->registerCommands();
		$this->registerListeners();
		$this->registerTasks();
		$this->database = match(strtolower($this->getConfig()->get("database"))){
			"json" => new JsonDatabase($this),
			default => new JsonDatabase($this)
		};
		$this->database->loadData();
		$this->leaderboardManager->loadData();
	}

	protected function onDisable() : void{
		$this->database->saveData();
		$this->leaderboardManager->saveData();
	}

	private function registerCommands() : void{
		$commandMap = $this->getServer()->getCommandMap();
		$commandMap->register("topstats", new TopStatsCommand($this));
	}

	private function registerListeners() : void{
		$pluginManager = $this->getServer()->getPluginManager();
		$pluginManager->registerEvents(new EventListener($this), $this);
	}

	private function registerTasks() : void{
		$scheduler = $this->getScheduler();
		$scheduler->scheduleRepeatingTask(new UpdateTask($this), 20);
	}

	public function getDatabase() : IDatabase{
		return $this->database;
	}

	public function getLeaderboardManager() : LeaderboardManager{
		return $this->leaderboardManager;
	}

	public function getMaxList() : int{
		return $this->getConfig()->get("max-list") ?? self::MAX_LIST;
	}
}
