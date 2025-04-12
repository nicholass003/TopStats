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

use nicholass003\topstats\libs\_95ef3006793f2228\CortexPE\Commando\PacketHooker;
use nicholass003\topstats\libs\_95ef3006793f2228\DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use nicholass003\topstats\libs\_95ef3006793f2228\DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use nicholass003\topstats\libs\_95ef3006793f2228\JackMD\UpdateNotifier\UpdateNotifier;
use nicholass003\topstats\command\TopStatsCommand;
use nicholass003\topstats\database\data\DataType;
use nicholass003\topstats\database\IDatabase;
use nicholass003\topstats\database\JsonDatabase;
use nicholass003\topstats\database\MySQLDatabase;
use nicholass003\topstats\leaderboard\LeaderboardManager;
use nicholass003\topstats\listener\EventListener;
use nicholass003\topstats\model\player\PlayerModel;
use nicholass003\topstats\model\text\TextModel;
use nicholass003\topstats\task\UpdateTask;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use function array_column;
use function array_pop;
use function array_unshift;
use function end;
use function explode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function implode;
use function ltrim;
use function str_repeat;
use function strlen;
use function strpos;
use function strtolower;
use function trim;

class TopStats extends PluginBase{
	use SingletonTrait;

	private const CONFIG_VERSION = "1.0.0";

	public const MAX_LIST = 10;
	public const TIME_FORMAT = "{year}y {month}m {week}w {day}d {hour}h {minute}m {second}s";

	protected IDatabase $database;
	protected ?EconomyProvider $economyProvider = null;
	protected LeaderboardManager $leaderboardManager;

	private Config $db;

	protected function onLoad() : void{
		$this->loadConfig();
		$this->saveAllResources();

		DataType::setup();
	}

	private function saveAllResources() : void{
		$this->saveResource($this->getDataFolder() . "database.yml");
		$this->db = new Config($this->getDataFolder() . "database.yml", Config::YAML);
	}

	private function loadConfig() : void{
		$configPath = $this->getDataFolder() . "config.yml";
		$defaultConfigPath = $this->getFile() . "resources/config.yml";

		if(!file_exists($configPath)){
			$this->getLogger()->warning("⚠️ No config found! Generating default config...");
			$this->saveDefaultConfig();
			return;
		}

		try{
			$userConfigContent = file_get_contents($configPath);
			$defaultConfigContent = file_get_contents($defaultConfigPath);
			if($userConfigContent === false || $defaultConfigContent === false){
				throw new \Exception("Failed to read config file.");
			}
		}catch(\Exception $e){
			$this->getLogger()->error("❌ {$e->getMessage()}");
			return;
		}

		$userConfigLines = explode("\n", $userConfigContent);
		$defaultConfigLines = explode("\n", $defaultConfigContent);

		$userConfigMap = [];
		$configVersionKey = "config-version";
		$configVersionFound = false;
		$needUpdate = false;

		$context = [];

		foreach($userConfigLines as $line){
			$trimmedLine = ltrim($line);
			$indent = strlen($line) - strlen($trimmedLine);

			if(strpos($trimmedLine, ":") !== false){
				[$key] = explode(":", $trimmedLine, 2);
				$key = trim($key);

				while(!empty($context) && end($context)['indent'] >= $indent){
					array_pop($context);
				}

				$fullKey = implode("->", array_column($context, 'key')) . "->" . $key;
				$fullKey = ltrim($fullKey, "->");

				$userConfigMap[$fullKey] = $line;
				$context[] = ['key' => $key, 'indent' => $indent];

				if($key === $configVersionKey){
					$configVersionFound = true;
					$value = trim(explode(":", $trimmedLine, 2)[1]);
					if($value !== '"' . self::CONFIG_VERSION . '"'){
						$needUpdate = true;
						$userConfigMap[$fullKey] = "$configVersionKey: \"" . self::CONFIG_VERSION . "\"";
					}
				}
			}
		}

		if(!$configVersionFound){
			$needUpdate = true;
			array_unshift($userConfigLines, "$configVersionKey: \"" . self::CONFIG_VERSION . "\"");
		}

		$updatedConfigLines = [];
		$context = [];

		foreach($defaultConfigLines as $line){
			$trimmedLine = ltrim($line);
			$indent = strlen($line) - strlen($trimmedLine);

			if(strpos($trimmedLine, ":") !== false){
				[$key] = explode(":", $trimmedLine, 2);
				$key = trim($key);

				while(!empty($context) && end($context)['indent'] >= $indent){
					array_pop($context);
				}

				$fullKey = implode("->", array_column($context, 'key')) . "->" . $key;
				$fullKey = ltrim($fullKey, "->");

				if(isset($userConfigMap[$fullKey])){
					$updatedConfigLines[] = str_repeat(" ", $indent) . ltrim($userConfigMap[$fullKey]);
				}else{
					$updatedConfigLines[] = $line;
					$needUpdate = true;
				}

				$context[] = ['key' => $key, 'indent' => $indent];
			}else{
				$updatedConfigLines[] = $line;
			}
		}

		if($needUpdate){
			file_put_contents($configPath, implode("\n", $updatedConfigLines));
			$this->getLogger()->notice("🚀 Config updated successfully!");
		}else{
			$this->getLogger()->notice("✅ Config is already up to date.");
		}
	}

	protected function onEnable() : void{
		self::setInstance($this);
		UpdateNotifier::checkUpdate($this->getDescription()->getName(), $this->getDescription()->getVersion());
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
			"mysql" => new MySQLDatabase($this),
			default => new JsonDatabase($this)
		};
		if($this->checkEconomyProvider()){
			$this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));
		}
		$this->database->loadData();
		$this->leaderboardManager->loadData();
	}

	protected function onDisable() : void{
		$this->database->saveData();
		$this->leaderboardManager->saveData();
	}

	private function checkEconomyProvider() : bool{
		/** @var EconomyProvider $provider */
		$provider = libPiggyEconomy::$economyProviders[strtolower($this->getConfig()->get("economy")["provider"])];
		return $provider::checkDependencies();
	}

	private function registerCommands() : void{
		$commandMap = $this->getServer()->getCommandMap();
		$commandMap->register("topstats", new TopStatsCommand($this, "topstats", "TopStats Command"));
	}

	private function registerEntities() : void{
		$entityFactory = EntityFactory::getInstance();
		$getTagValue = function(CompoundTag $nbt, string $tagName, string $tagClass) : mixed{
			$tag = $nbt->getTag($tagName);
			if($tag instanceof $tagClass){
				return $tag->getValue();
			}else{
				throw new SavedDataLoadingException("Expected \"{$tagName}\" NBT tag of type {$tagClass} not found");
			}
		};
		$entityFactory->register(PlayerModel::class, function(World $world, CompoundTag $nbt) use($getTagValue) : PlayerModel{
			$type = $getTagValue($nbt, PlayerModel::TAG_TYPE, StringTag::class);
			$modelID = $getTagValue($nbt, PlayerModel::TAG_MODEL_ID, IntTag::class);
			$top = $getTagValue($nbt, PlayerModel::TAG_TOP, IntTag::class);
			return new PlayerModel(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $modelID, $type, $top, $nbt);
		}, ["PlayerModel"]);
		$entityFactory->register(TextModel::class, function(World $world, CompoundTag $nbt) use($getTagValue) : TextModel{
			$type = $getTagValue($nbt, TextModel::TAG_TYPE, StringTag::class);
			$modelID = $getTagValue($nbt, TextModel::TAG_MODEL_ID, IntTag::class);
			return new TextModel(EntityDataHelper::parseLocation($nbt, $world), $modelID, $type, "", "", $nbt);
		}, ["TextModel"]);
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

	public function getDatabaseConfig() : Config{
		return $this->db;
	}

	public function getEconomyProvider() : ?EconomyProvider{
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