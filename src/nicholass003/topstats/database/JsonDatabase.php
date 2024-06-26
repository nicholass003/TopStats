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

namespace nicholass003\topstats\database;

use nicholass003\topstats\database\data\DataAction;
use nicholass003\topstats\database\data\DataType;
use nicholass003\topstats\TopStats;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use function in_array;

class JsonDatabase implements IDatabase{

	protected $database;

	protected array $data = [];

	public function __construct(
		private TopStats $plugin
	){
		$this->database = new Config($this->plugin->getDataFolder() . "data.json", Config::JSON);
	}

	public function getName() : string{
		return "JSON";
	}

	public function loadData() : void{
		foreach($this->database->getAll() as $xuid => $data){
			$this->data[(int) $xuid] = $data;
		}
	}

	public function getTemporaryData() : array{
		return $this->data;
	}

	public function getTemporaryDataValue(Player $player, string $type) : mixed{
		return $this->data[(int) $player->getXuid()][$type] ?? false;
	}

	public function create(Player $player) : void{
		$xuid = (int) $player->getXuid();
		if(!isset($this->data[$xuid])){
			if(!isset($this->data[$xuid]["name"])){
				$this->data[$xuid]["name"] = $player->getName();
			}
			foreach(DataType::ALL as $type){
				$this->data[$xuid][$type] = 0;
			}
		}else{
			foreach(DataType::ALL as $type){
				if(!isset($this->data[$xuid][$type])){
					$this->data[$xuid][$type] = 0;
				}
			}
		}
	}

	public function update(Player $player, array $data, int $action) : void{
		$xuid = (int) $player->getXuid();
		if(isset($this->data[$xuid])){
			foreach($data as $key => $value){
				if(!in_array($key, DataType::ALL, true)){
					throw new \InvalidArgumentException("Invalid DataType {$key}");
				}
				$this->action($xuid, $key, $value, $action);
			}
		}
	}

	private function action(int $xuid, string $key, float|int $value, int $action) : void{
		switch($action){
			case DataAction::NONE:
				$this->data[$xuid][$key] = $value;
				break;
			case DataAction::ADDITION:
				$this->data[$xuid][$key] += $value;
				break;
			case DataAction::SUBTRACTION:
				$this->data[$xuid][$key] -= $value;
				break;
			case DataAction::RESET:
				$this->data[$xuid][$key] = 0;
				break;
			default:
				throw new \InvalidArgumentException("Invalid DataAction {$key}");
		}
	}

	public function saveData() : void{
		foreach($this->data as $xuid => $data){
			$this->database->set((string) $xuid, $data);
		}
		$this->database->save();
	}
}
