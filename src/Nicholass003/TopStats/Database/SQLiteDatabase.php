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

use Nicholass003\TopStats\Database\Data\DataAction;
use Nicholass003\TopStats\Database\Data\DataType;
use Nicholass003\TopStats\Database\Query\DBAction;
use Nicholass003\TopStats\Database\Query\DBQuery;
use Nicholass003\TopStats\TopStats;
use pocketmine\player\Player;
use Nicholass003\TopStats\libs\_60066bca077282b2\poggit\libasynql\DataConnector;
use Nicholass003\TopStats\libs\_60066bca077282b2\poggit\libasynql\libasynql;
use Nicholass003\TopStats\libs\_60066bca077282b2\poggit\libasynql\SqlError;
use function count;
use function in_array;
use function json_encode;

class SQLiteDatabase implements SQLInterface{

	protected DataConnector $database;

	private bool $autoSaveEnabled = true;

	/** @var array<string, array> */
	protected array $data = [];

	public function __construct(
		private TopStats $plugin
	){
		$this->database = libasynql::create($plugin, $plugin->getDatabaseConfig()->get("database", []), [
			"sqlite" => "database/sqlite.sql"
		]);

		$this->autoSaveEnabled = $plugin->getConfig()->get("auto-save", true);
	}

	public function getName() : string{
		return "SQLite";
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
				$this->data[$data["xuid"]]["name"] = $data["name"];
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
		$onError ??= fn(SqlError $err) => $this->plugin->getLogger()->error("[SQLite Error] {$err->getMessage()} on query: {$query} with args: " . json_encode($args));
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
		if(count($this->data) === 0){
			return;
		}
		foreach($this->data as $xuid => $stats){
			$args = ["xuid" => (string) $xuid, "name" => $stats["name"]];
			foreach(DataType::ALL as $type){
				$args[DataType::get($type)] = $stats[$type] ?? 0;
			}
			$this->query(DBQuery::INSERT_OR_UPDATE_PLAYER_STATS, DBAction::CHANGE, $args);
		}
		$this->plugin->getLogger()->debug("Auto-saved player stats data.");
	}
}