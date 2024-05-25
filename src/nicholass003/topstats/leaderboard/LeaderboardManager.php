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

use nicholass003\topstats\model\IModel;
use nicholass003\topstats\model\ModelVariant;
use nicholass003\topstats\model\text\TextModel;
use nicholass003\topstats\TopStats;
use pocketmine\utils\Config;
use pocketmine\world\Position;
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

	public function loadData() : void{
		foreach($this->leaderboardData->getAll() as $sid => $data){
			$id = (int) substr($sid, 3);
			$this->leaderboards[$id] = new Leaderboard($this->validateModel(json_decode($data, true)));
		}
	}

	public function getLeaderboardData() : Config{
		return $this->leaderboardData;
	}

	public function saveData() : void{
		foreach($this->leaderboards() as $id => $leaderboard){
			$this->leaderboardData->set("ID:{$id}", $leaderboard->toJSON());
		}
		$this->leaderboardData->save();
	}

	public function validateModel(array $data) : IModel{
		switch($data["model"]){
			case ModelVariant::PLAYER:
				break;
			case ModelVariant::TEXT:
				$textModel = new TextModel(new Position($data["position"]["x"], $data["position"]["y"], $data["position"]["z"], $this->plugin->getServer()->getWorldManager()->getWorldByName($data["position"]["world"])), $data["type"]);
				return $textModel;
			default:
				throw new \InvalidArgumentException("Invalid IModel: " . $data["model"]);
		}
	}
}
