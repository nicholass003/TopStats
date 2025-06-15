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

namespace nicholass003\topstats\leaderboard;

use Exception;
use nicholass003\topstats\libs\_949d26e1f7364fbc\nicholass003\Textify\Lib\TextifyFactory;
use nicholass003\topstats\event\TopStatsUpdateEvent;
use nicholass003\topstats\TopStats;
use pocketmine\utils\Config;
use function array_filter;
use function count;
use function is_array;
use function json_decode;
use function json_encode;
use function substr;
use const JSON_PRETTY_PRINT;

class LeaderboardManager{

	/** @var array<int, Leaderboard> */
	protected array $leaderboards = [];

	protected Config $leaderboardData;

	public function __construct(
		protected TopStats $plugin
	){
		$this->leaderboardData = new Config($this->plugin->getDataFolder() . "leaderboards.json", Config::JSON);
	}

	public function add(Leaderboard $leaderboard) : LeaderboardManager{
		$this->leaderboards[$leaderboard->getId()] = $leaderboard;
		return $this;
	}

	public function remove(int $id) : LeaderboardManager{
		if(isset($this->leaderboards[$id])){
			unset($this->leaderboards[$id]);
		}
		return $this;
	}

	public function get(int $id) : ?Leaderboard{
		return $this->leaderboards[$id] ?? null;
	}

	/**
	 * @return array<int, Leaderboard>
	 */
	public function leaderboards() : array{
		return $this->leaderboards;
	}

	/**
	 * @return array<int, Leaderboard>
	 */
	public function getLeaderboardFromType(string $type) : array{
		return array_filter(
			$this->leaderboards,
			fn($leaderboard) => $leaderboard->getModel()->getCompoundTag()->getString("TopStatsType") === $type
		);
	}

	/**
	 * Dispatches a leaderboard update event for the specified type.
	 *
	 * This function retrieves all leaderboards that match the provided type,
	 * fires a {@see TopStatsUpdateEvent}, and updates each leaderboard unless
	 * the event is cancelled.
	 *
	 * @param string $type The leaderboard type identifier (e.g., "block_break", "kills").
	 * @param array  $data Optional external data used from other plugins.
	 *
	 * @return bool
	 */
	public function dispatchLeaderboardUpdate(string $type, array $data = []) : bool{
		$leaderboards = $this->getLeaderboardFromType($type);

		if(count($leaderboards) === 0){
			return false;
		}

		$ev = new TopStatsUpdateEvent($type, $leaderboards);
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}

		foreach($leaderboards as $id => $leaderboard){
			$leaderboard->update($data);
		}
		return true;
	}

	public function loadData() : void{
		foreach($this->leaderboardData->getAll() as $sid => $raw){
			$id = (int) substr((string) $sid, 3);

			$data = json_decode($raw, true);
			if(!is_array($data)){
				throw new Exception("Invalid leaderboard data format for ID: $id");
			}

			$model = TextifyFactory::getInstance()->get($data["id"] ?? null);
			if($model === null){
				throw new Exception("Model not found for leaderboard ID: $id");
			}

			$leaderboard = new Leaderboard($model);
			if($leaderboard->getModel()->getModelPosition()->getWorld()->isLoaded()){
				$leaderboard->update();
			}

			$this->leaderboards[$id] = $leaderboard;
		}
	}

	public function getLeaderboardData() : Config{
		return $this->leaderboardData;
	}

	public function saveData() : void{
		$data = [];
		foreach($this->leaderboards() as $id => $leaderboard){
			$data["ID:{$id}"] = json_encode($leaderboard);
			$leaderboard->getModel()->destroy();
		}
		$this->leaderboardData->setAll($data);
		$this->leaderboardData->save();
	}
}