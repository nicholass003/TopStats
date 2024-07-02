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

use CortexPE\Commando\PacketHooker;
use DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use nicholass003\topstats\command\TopStatsCommand;
use nicholass003\topstats\database\IDatabase;
use nicholass003\topstats\database\JsonDatabase;
use nicholass003\topstats\leaderboard\LeaderboardManager;
use nicholass003\topstats\listener\EventListener;
use nicholass003\topstats\model\player\PlayerModel;
use nicholass003\topstats\task\UpdateTask;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use function strtolower;

class TopStats extends PluginBase{
	use SingletonTrait;

	public const MAX_LIST = 10;
	public const TIME_FORMAT = "{year}y {month}m {day}d {hour}h {minute}m {second}s";

	protected IDatabase $database;
	protected ?EconomyProvider $economyProvider = null;
	protected LeaderboardManager $leaderboardManager;

	protected function onLoad() : void{
		$this->saveDefaultConfig();
	}

	protected function onEnable() : void{
		self::setInstance($this);
		libPiggyEconomy::init();
		if(!PacketHooker::isRegistered()){
			PacketHooker::register($this);
		}
		$this->leaderboardManager = new LeaderboardManager($this);
		$this->registerCommands();
		$this->registerEntities();
		$this->registerListeners();
		$this->registerTasks();
		$this->database = match(strtolower($this->getConfig()->get("database"))){
			"json" => new JsonDatabase($this),
			default => new JsonDatabase($this)
		};
		$this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));
		$this->database->loadData();
		$this->leaderboardManager->loadData();
	}

	protected function onDisable() : void{
		$this->database->saveData();
		$this->leaderboardManager->saveData();
	}

	private function registerCommands() : void{
		$commandMap = $this->getServer()->getCommandMap();
		$commandMap->register("topstats", new TopStatsCommand($this, "topstats", "TopStats Command"));
	}

	private function registerEntities() : void{
		$entityFactory = EntityFactory::getInstance();
		$entityFactory->register(PlayerModel::class, function(World $world, CompoundTag $nbt) : PlayerModel{
			$typeTag = $nbt->getTag(PlayerModel::TAG_TYPE);
			$modelIdTag = $nbt->getTag(PlayerModel::TAG_MODEL_ID);
			$topTag = $nbt->getTag(PlayerModel::TAG_TOP);
			if($typeTag instanceof StringTag){
				$type = $typeTag->getValue();
			}else{
				throw new SavedDataLoadingException("Expected \"" . PlayerModel::TAG_TYPE . "\" NBT tag not found");
			}
			if($modelIdTag instanceof IntTag){
				$modelID = $modelIdTag->getValue();
			}else{
				throw new SavedDataLoadingException("Expected \"" . PlayerModel::TAG_MODEL_ID . "\" NBT tag not found");
			}
			if($topTag instanceof IntTag){
				$top = $topTag->getValue();
			}else{
				throw new SavedDataLoadingException("Expected \"" . PlayerModel::TAG_TOP . "\" NBT tag not found");
			}
			return new PlayerModel(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $modelID, $type, $top, $nbt);
		}, ["PlayerModel"]);
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

	public function getEconomyProvider() : EconomyProvider{
		return $this->economyProvider;
	}

	public function getLeaderboardManager() : LeaderboardManager{
		return $this->leaderboardManager;
	}

	public function getMaxList() : int{
		return $this->getConfig()->get("max-list") ?? self::MAX_LIST;
	}

	public function getTimeFormat() : string{
		return $this->getConfig()->get("time-format") ?? self::TIME_FORMAT;
	}
}
