<?php

/*
 * Copyright (c) 2024 - present nicholass003
 *   _______        _   _  __
 *  |__   __|      | | (_)/ _|
 *     | | _____  _| |_ _| |_ _   _
 *     | |/ _ \ \/ / __| |  _| | | |
 *     | |  __/>  <| |_| | | | |_| |
 *     |_|\___/_/\_\ __|_|_|  \__, |
 *                             __/ |
 *                            |___/
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

namespace nicholass003\topstats\libs\_2f2e26b52fbe84a5\Nicholass003\Textify\Lib;

use nicholass003\topstats\libs\_2f2e26b52fbe84a5\Nicholass003\Textify\Lib\Exception\TextifyException;
use nicholass003\topstats\libs\_2f2e26b52fbe84a5\Nicholass003\Textify\Lib\Model\Model;
use nicholass003\topstats\libs\_2f2e26b52fbe84a5\Nicholass003\Textify\Lib\Model\NonPlayerCharacter;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use pocketmine\world\World;
use function array_values;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_array;
use function json_decode;
use function json_encode;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

final class TextifyFactory{

	private static ?TextifyFactory $instance = null;
	private static ?Plugin $registrant = null;

	public static function getInstance() : TextifyFactory{
		return self::$instance ?? throw new TextifyException('');
	}

	public static function isRegistered() : bool{
		return self::$instance !== null;
	}

	public static function register(Plugin $plugin, bool $loadFromStorage = true) : void{
		if(self::$instance === null){
			self::$instance = new self();
		}
		self::$registrant = $plugin;

		$entityFactory = EntityFactory::getInstance();
		$entityFactory->register(NonPlayerCharacter::class, function(World $world, CompoundTag $nbt) : NonPlayerCharacter{
			return new NonPlayerCharacter(EntityDataHelper::parseLocation($nbt, $world)->asPosition(), Human::parseSkinNBT($nbt), $nbt);
		}, ["NonPlayerCharacter"]);

		Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $plugin);

		if($loadFromStorage){
			$path = $plugin->getDataFolder() . Textify::STORAGE_FILENAME;

			if(file_exists($path)){
				$json = file_get_contents($path);
				$data = json_decode($json, true);

				if(isset($data[Textify::STORAGE_KEY_MARKER]) && $data[Textify::STORAGE_KEY_MARKER] === Textify::STORAGE_VALUE_MARKER){
					foreach($data["models"] ?? [] as $entry){
						try{
							$model = Textify::fromString(json_encode($entry));
							self::getInstance()->add($model);
						}catch(\Throwable $e){
							$plugin->getLogger()->warning("[Textify] Failed to load model: " . $e->getMessage());
						}
					}
				}else{
					$plugin->getLogger()->warning("[Textify] Detected a storage file, but it appears to be invalid or unrelated to Textify: $path");
				}
			}
		}
	}

	public static function getRegistrant() : Plugin{
		return self::$registrant;
	}

	/** @var Model[] */
	private array $models = [];

	public function add(Model $model) : void{
		$actorId = $model->getActorId();
		if(!isset($this->models[$actorId])){
			$this->models[$actorId] = $model;
		}
	}

	public function remove(string $actorId) : void{
		unset($this->models[$actorId]);
	}

	public function get(string $actorId) : ?Model{
		return $this->models[$actorId] ?? null;
	}

	/**
	 * @return Model[]
	 */
	public function getAll() : array{
		return $this->models;
	}

	public function save() : void{
		$plugin = self::getRegistrant();
		$factory = self::getInstance();
		$path = $plugin->getDataFolder() . Textify::STORAGE_FILENAME;

		if(file_exists($path)){
			$content = file_get_contents($path);
			$json = json_decode($content, true);

			if(!is_array($json) || !isset($json[Textify::STORAGE_KEY_MARKER]) || $json[Textify::STORAGE_KEY_MARKER] !== Textify::STORAGE_VALUE_MARKER){
				throw new TextifyException("Cannot save: '" . Textify::STORAGE_FILENAME . "' exists and is not recognized as a Textify file.");
			}
		}

		$data = [
			Textify::STORAGE_KEY_MARKER => Textify::STORAGE_VALUE_MARKER,
			"models" => array_values($factory->getAll())
		];

		file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
	}

	/** @var array<string, <int, bool>> */
	private array $spawned = [];

	public function hasSpawnedTo(Player $player, int $actorRuntimeId) : bool{
		return isset($this->spawned[$player->getUniqueId()->getBytes()][$actorRuntimeId]);
	}

	public function spawnedTo(Player $player, int $actorRuntimeId) : void{
		$this->spawned[$player->getUniqueId()->getBytes()][$actorRuntimeId] = true;
	}

	public function despawnFrom(Player $player, int $actorRuntimeId) : void{
		unset($this->spawned[$player->getUniqueId()->getBytes()][$actorRuntimeId]);
	}

	public function despawnAllFrom(Player $player) : void{
		unset($this->spawned[$player->getUniqueId()->getBytes()]);
	}
}