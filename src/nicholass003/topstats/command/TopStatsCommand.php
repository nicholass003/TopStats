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

namespace nicholass003\topstats\command;

use nicholass003\topstats\leaderboard\Leaderboard;
use nicholass003\topstats\leaderboard\LeaderboardManager;
use nicholass003\topstats\model\IModel;
use nicholass003\topstats\model\ModelType;
use nicholass003\topstats\model\ModelVariant;
use nicholass003\topstats\model\text\TextModel;
use nicholass003\topstats\TopStats;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use function array_keys;
use function count;
use function ctype_digit;
use function in_array;
use function strtolower;

class TopStatsCommand extends Command implements PluginOwned{

	private LeaderboardManager $leaderboardManager;

	public function __construct(
		private TopStats $plugin
	){
		parent::__construct("topstats", "TopStats Command", "Usage: /topstats <subcommand>");
		$this->setPermission("topstats.command");
		$this->leaderboardManager = $plugin->getLeaderboardManager();
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "You must login to use this command.");
			return;
		}
		if(isset($args[0])){
			switch(strtolower($args[0])){
				case "add":
				case "create":
				case "make":
				case "spawn":
					if(isset($args[1])){
						if(isset($args[2])){
							if(!in_array($args[2], ModelType::ALL, true)){
								$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $args[0] . " " . $args[1] . " <type>");
								$sender->sendMessage(TextFormat::RED . "Type \"/topstats types\" to get type list");
								return;
							}
							switch(strtolower($args[1])){
								case ModelVariant::PLAYER:
									break;
								case ModelVariant::TEXT:
									$leaderboard = new Leaderboard(new TextModel($sender->getPosition(), $args[2]));
									$leaderboard->spawn();
									$this->leaderboardManager->add($leaderboard);
									$sender->sendMessage(TextFormat::GREEN . "Successfully spawn TopStats with model: " . $args[1] . " type: " . $args[2]);
									break;
								default:
									$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $args[0] . " <player|text>");
									break;
							}
						}else{
							$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $args[0] . " " . $args[1] . " <type>");
						}
					}else{
						$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $args[0] . " <player|text>");
					}
					break;
				case "delete":
				case "despawn":
				case "destroy":
				case "remove":
					$leaderboards = $this->leaderboardManager->leaderboards();
					foreach($leaderboards as $id => $leaderboard){
						if($id === (int) $args[1]){
							if($leaderboard instanceof Leaderboard){
								$this->leaderboardManager->remove($id);
								$leaderboard->getModel()->destroy();
								$sender->sendMessage(TextFormat::GREEN . "TopStats with id {$id} successfully removed");
							}
						}
					}
					break;
				case "list":
					if(count($this->leaderboardManager->leaderboards()) < 1){
						$sender->sendMessage(TextFormat::RED . "There are no TopStats Model available.");
					}else{
						foreach($this->leaderboardManager->leaderboards() as $id => $leaderboard){
							if(!$leaderboard->getModel() instanceof IModel) continue;
							$sender->sendMessage(TextFormat::GREEN . "- ModelID: {$id} ModelVariant: " . $leaderboard->getModel()->getVariant());
						}
					}
					break;
				case "teleport":
				case "tp":
					$leaderboards = $this->leaderboardManager->leaderboards();
					if(isset($args[1])){
						if(ctype_digit((string) $args[1])){
							if(in_array((int) $args[1], array_keys($leaderboards), true)){
								foreach($leaderboards as $id => $leaderboard){
									if($leaderboard->getModel() instanceof IModel && $id === (int) $args[1]){
										$sender->teleport($leaderboard->getModel()->getPosition());
										$sender->sendMessage(TextFormat::GREEN . "Success teleported to TopStats position with id: {$id}");
										break;
									}
								}
							}else{
								$sender->sendMessage(TextFormat::RED . "Failed to teleported, there are no TopStats with id: " . $args[1]);
							}
						}else{
							$sender->sendMessage(TextFormat::RED . "ModelID should be a number.");
							$sender->sendMessage(TextFormat::RED . "To get list of TopStats data, type \"/topstats list\"");
						}
					}else{
						$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $args[0] . " <id>");
						$sender->sendMessage(TextFormat::RED . "To get list of TopStats data, type \"/topstats list\"");
					}
					break;
				case "type":
				case "types":
					$sender->sendMessage(TextFormat::YELLOW . "Type List");
					foreach(ModelType::ALL as $type){
						$sender->sendMessage(TextFormat::GREEN . " - {$type}");
					}
					break;
			}
		}else{
			$sender->sendMessage(TextFormat::RED . $this->usageMessage);
		}
	}

	public function getOwningPlugin() : Plugin{
		return $this->plugin;
	}
}
