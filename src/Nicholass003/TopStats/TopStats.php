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

namespace Nicholass003\TopStats;

use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\bStats\PocketmineMp\Metrics;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\CortexPE\Commando\PacketHooker;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\DaPigGuy\libPiggyEconomy\libPiggyEconomy;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\DaPigGuy\libPiggyEconomy\providers\EconomyProvider;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\JackMD\UpdateNotifier\UpdateNotifier;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\Nicholass003\Textify\Lib\TextifyFactory;
use Nicholass003\TopStats\Command\TopStatsCommand;
use Nicholass003\TopStats\Database\Data\DataType;
use Nicholass003\TopStats\Database\IDatabase;
use Nicholass003\TopStats\Database\JsonDatabase;
use Nicholass003\TopStats\Database\MySQLDatabase;
use Nicholass003\TopStats\Database\SQLInterface;
use Nicholass003\TopStats\Database\SQLiteDatabase;
use Nicholass003\TopStats\External\ExternalIntegrationRegistry;
use Nicholass003\TopStats\External\Support\TopVoterIntegration;
use Nicholass003\TopStats\Leaderboard\LeaderboardManager;
use Nicholass003\TopStats\Listener\EventListener;
use Nicholass003\TopStats\Task\UpdateTask;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
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

	private const CONFIG_VERSION = "1.0.3";

	public const MAX_LIST = 10;
	public const TIME_FORMAT = "{year}y {month}m {week}w {day}d {hour}h {minute}m {second}s";

	protected IDatabase $database;
	protected ?EconomyProvider $economyProvider = null;
	protected LeaderboardManager $leaderboardManager;

	private Config $db;

	private bool $disabledDueToInternalError = false;

	protected function onLoad() : void{
		$this->loadConfig();
		$this->saveAllResources();

		DataType::setup();
	}

	private function saveAllResources() : void{
		$this->saveResource("database.yml");
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
		if(!TextifyFactory::isRegistered()){
			TextifyFactory::register($this);
		}
		$this->leaderboardManager = new LeaderboardManager($this);
		$this->registerCommands();
		$this->registerListeners();
		$databaseType = strtolower($this->getConfig()->get("database"));
		if($databaseType !== "json"){
			if(strtolower($this->db->get("database")["type"]) !== $databaseType){
				$this->disabledDueToInternalError = true;
				$this->getLogger()->error("Database type mismatch, disable plugin.");
				$this->getServer()->getPluginManager()->disablePlugin($this);
				return;
			}
		}
		$this->database = match($databaseType){
			"json" => new JsonDatabase($this),
			"mysql" => new MySQLDatabase($this),
			"sqlite" => new SQLiteDatabase($this),
			default => new JsonDatabase($this)
		};
		if($this->checkEconomyProvider()){
			$this->economyProvider = libPiggyEconomy::getProvider($this->getConfig()->get("economy"));
		}
		$this->database->loadData();
		$this->registerExternalIntegrations();
		$this->leaderboardManager->loadData();
		$this->registerTasks();

		(new Metrics($this, 29632));
	}

	protected function onDisable() : void{
		if(!$this->disabledDueToInternalError){
			$this->database->saveData();
			TextifyFactory::getInstance()->save();
			$this->leaderboardManager->saveData();
		}
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

	private function registerExternalIntegrations() : void{
		$pluginManager = $this->getServer()->getPluginManager();
		$configIntegrations = $this->getConfig()->get("external-integrations", []);
		$registry = ExternalIntegrationRegistry::getInstance();

		$availableIntegrations = [
			"TopVoter" => TopVoterIntegration::class,
		];

		foreach($configIntegrations as $pluginName => $isActive){
			if(!$isActive || !$pluginManager->getPlugin($pluginName)){
				continue;
			}

			if(!isset($availableIntegrations[$pluginName])){
				continue;
			}

			$registry->register(new $availableIntegrations[$pluginName]());
		}
	}

	private function registerListeners() : void{
		$pluginManager = $this->getServer()->getPluginManager();
		$pluginManager->registerEvents(new EventListener($this), $this);
	}

	private function registerTasks() : void{
		$scheduler = $this->getScheduler();
		$scheduler->scheduleRepeatingTask(new UpdateTask($this), 20);

		$scheduler->scheduleRepeatingTask(new class($this->getDatabase()) extends Task{
			public function __construct(
				private readonly IDatabase $database
			){}

			public function onRun() : void{
				if($this->database instanceof SQLInterface){
					if(!$this->database->isAutoSaveActive()){
						return;
					}
					$this->database->saveData();
				}
			}
		}, SQLInterface::AUTO_SAVE_INTERVAL);
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