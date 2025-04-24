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

use nicholass003\topstats\event\TopStatsUpdateEvent;
use nicholass003\topstats\model\IModel;
use nicholass003\topstats\model\ModelVariant;
use nicholass003\topstats\model\player\PlayerModel;
use nicholass003\topstats\model\text\TextModel;
use nicholass003\topstats\TopStats;
use nicholass003\topstats\utils\Utils;
use pocketmine\entity\Location;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use function array_filter;
use function count;
use function json_decode;
use function substr;

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
			fn($leaderboard) => $leaderboard->getModel()->getType() === $type
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
	 *
	 * @return bool
	 */
	public function dispatchLeaderboardUpdate(string $type) : bool{
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
			$leaderboard->update();
		}
		return true;
	}

	public function loadData() : void{
		foreach($this->leaderboardData->getAll() as $sid => $data){
			$id = (int) substr((string) $sid, 3);
			$leaderboard = new Leaderboard($this->validateModel(json_decode($data, true)));
			if($leaderboard->getModel()->getPosition()->getWorld()->isLoaded()){
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
			$data["ID:{$id}"] = $leaderboard->toJSON();
			$leaderboard->getModel()->destroy();
		}
		$this->leaderboardData->setAll($data);
		$this->leaderboardData->save();
	}

	public function validateModel(array $data) : IModel{
		$worldManager = $this->plugin->getServer()->getWorldManager();
		$world = $worldManager->getWorldByName($data["position"]["world"]);
		if($world === null){
			$worldManager->loadWorld($data["position"]["world"]);
			$world = $worldManager->getWorldByName($data["position"]["world"]);
		}
		$pos = new Position($data["position"]["x"], $data["position"]["y"], $data["position"]["z"], $world);
		switch($data["model"]){
			case ModelVariant::PLAYER:
				$playerModel = new PlayerModel(Location::fromObject($pos, $pos->getWorld()), Utils::getTopStatsPlayerSkin($this->plugin->getDatabase()->getTemporaryData(), $data["type"], $data["top"]), $data["id"], $data["type"], $data["top"]);
				return $playerModel;
			case ModelVariant::TEXT:
				$textModel = new TextModel(Location::fromObject($pos, $pos->getWorld()), $data["id"], $data["type"]);
				return $textModel;
			default:
				throw new \InvalidArgumentException("Invalid IModel: " . $data["model"]);
		}
	}
}