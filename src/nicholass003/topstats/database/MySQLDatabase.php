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
use nicholass003\topstats\database\query\DBAction;
use nicholass003\topstats\database\query\DBQuery;
use nicholass003\topstats\TopStats;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use nicholass003\topstats\libs\_84622ff2cf681b8b\poggit\libasynql\DataConnector;
use nicholass003\topstats\libs\_84622ff2cf681b8b\poggit\libasynql\libasynql;
use nicholass003\topstats\libs\_84622ff2cf681b8b\poggit\libasynql\SqlError;
use function in_array;
use function json_encode;

class MySQLDatabase implements SQLInterface{

	private const AUTO_SAVE_INTERVAL = 20 * 60 * 5; //5 minutes

	protected DataConnector $database;

	private bool $autoSaveEnabled = true;

	/** @var array<string, array> */
	protected array $data = [];

	public function __construct(
		private TopStats $plugin
	){
		$this->database = libasynql::create($plugin, $plugin->getDatabaseConfig()->get("database", []), [
			"mysql" => "database/mysql.sql"
		]);

		$this->autoSaveEnabled = $plugin->getConfig()->get("auto-save", true);
		if($this->autoSaveEnabled){
			$plugin->getScheduler()->scheduleRepeatingTask(new class($plugin) extends Task{
				public function __construct(
					private readonly TopStats $plugin
				){}

				public function onRun() : void{
					if(($database = $this->plugin->getDatabase()) instanceof SQLInterface){
						if(!$database->isAutoSaveActive()){
							return;
						}
						$database->saveData();
					}
				}
			}, self::AUTO_SAVE_INTERVAL);
		}
	}

	public function getName() : string{
		return "MySQL";
	}

	public function toggleAutoSave(bool $value) : void{
		$this->autoSaveEnabled = $value;
	}

	public function isAutoSaveActive() : bool{
		return $this->autoSaveEnabled;
	}

	public function loadData() : void{
		$this->query(DBQuery::INIT_DATABASE, DBAction::GENERIC);
		$this->query(DBQuery::SELECT_ALL_STATS, DBAction::SELECT, [], function(array $rows){
			foreach($rows as $data){
				if(!isset($data["xuid"])){
					continue;
				}
				foreach(DataType::ALL as $type){
					$this->data[$data["xuid"]][$type] = $data[DataType::get($type)] ?? 0;
				}
			}
		});
		$this->database->waitAll();
	}

	/**
	 * @return array<string, array>
	 */
	public function getTemporaryData() : array{
		return $this->data;
	}

	public function getTemporaryDataValue(Player $player, string $type) : mixed{
		return $this->data[$player->getXuid()][$type] ?? false;
	}

	public function create(Player $player) : void{
		$xuid = $player->getXuid();
		if(!isset($this->data[$xuid])){
			$this->data[$xuid] = ["name" => $player->getName()];
			$this->query(DBQuery::INSERT_PLAYER_STATS, DBAction::INSERT, [
				"xuid" => $xuid,
				"name" => $player->getName()
			]);
		}
		foreach(DataType::ALL as $type){
			$this->data[$xuid][$type] ??= 0;
		}
	}

	public function update(Player $player, array $data, int $action) : void{
		$xuid = $player->getXuid();
		if(!isset($this->data[$xuid])){
			return;
		}
		foreach($data as $key => $value){
			if(!in_array($key, DataType::ALL, true)){
				throw new \InvalidArgumentException("Invalid DataType {$key}");
			}
			$this->applyAction($xuid, $key, $value, $action);
		}
	}

	private function query(string $query, DBAction $action, array $args = [], ?callable $onInserted = null, ?callable $onError = null) : void{
		$onError ??= fn(SqlError $err) => $this->plugin->getLogger()->error("[MySQL Error] {$err->getMessage()} on query: {$query} with args: " . json_encode($args));
		switch($action){
			case DBAction::CHANGE:
				$this->database->executeChange($query, $args, $onInserted, $onError);
				break;
			case DBAction::GENERIC:
				$this->database->executeGeneric($query, $args, $onInserted, $onError);
				break;
			case DBAction::INSERT:
				$this->database->executeInsert($query, $args, $onInserted, $onError);
				break;
			case DBAction::SELECT:
				$this->database->executeSelect($query, $args, $onInserted, $onError);
				break;
		}
	}

	private function applyAction(string $xuid, string $key, float|int $value, int $action) : void{
		match($action){
			DataAction::NONE => $this->data[$xuid][$key] = $value,
			DataAction::ADDITION => $this->data[$xuid][$key] += $value,
			DataAction::SUBTRACTION => $this->data[$xuid][$key] -= $value,
			DataAction::RESET => $this->data[$xuid][$key] = 0,
			default => throw new \InvalidArgumentException("Invalid DataAction {$action}")
		};
	}

	public function saveData() : void{
		foreach($this->data as $xuid => $stats){
			$args = ["xuid" => $xuid];
			foreach(DataType::ALL as $type){
				$args[DataType::get($type)] = $stats[$type] ?? 0;
			}
			$this->query(DBQuery::INSERT_OR_UPDATE_PLAYER_STATS, DBAction::CHANGE, $args);
		}
		$this->plugin->getLogger()->debug("Auto-saved player stats data.");
	}
}