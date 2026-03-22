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

namespace Nicholass003\TopStats\Command;

use Nicholass003\TopStats\libs\_113f28876795d8c3\CortexPE\Commando\BaseCommand;
use Nicholass003\TopStats\libs\_113f28876795d8c3\dktapps\pmforms\CustomForm;
use Nicholass003\TopStats\libs\_113f28876795d8c3\dktapps\pmforms\CustomFormResponse;
use Nicholass003\TopStats\libs\_113f28876795d8c3\dktapps\pmforms\element\Input;
use Nicholass003\TopStats\libs\_113f28876795d8c3\dktapps\pmforms\element\Toggle;
use Nicholass003\TopStats\libs\_113f28876795d8c3\dktapps\pmforms\MenuForm;
use Nicholass003\TopStats\libs\_113f28876795d8c3\dktapps\pmforms\MenuOption;
use Nicholass003\TopStats\libs\_113f28876795d8c3\dktapps\pmforms\ModalForm;
use Nicholass003\TopStats\Command\SubCommand\AutoSaveSubCommand;
use Nicholass003\TopStats\Command\SubCommand\CreateSubCommand;
use Nicholass003\TopStats\Command\SubCommand\DeleteSubCommand;
use Nicholass003\TopStats\Command\SubCommand\ListSubCommand;
use Nicholass003\TopStats\Command\SubCommand\TeleportSubCommand;
use Nicholass003\TopStats\Command\SubCommand\TopStatsSubCommand;
use Nicholass003\TopStats\Command\SubCommand\TypeSubCommand;
use Nicholass003\TopStats\Database\SQLInterface;
use Nicholass003\TopStats\TopStats;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use function array_map;
use function array_unique;
use function array_values;
use function implode;
use function in_array;

class TopStatsCommand extends BaseCommand{

	/** @var TopStats */
	protected Plugin $plugin;

	protected function prepare() : void{
		$this->setPermission("topstats.command");

		$this->registerSubCommand(new AutoSaveSubCommand($this->plugin, "autosave", "AutoSave TopStats Database"));
		$this->registerSubCommand(new CreateSubCommand($this->plugin, "create", "Create or Spawn TopStats Leaderboard.", ["add", "make", "spawn"]));
		$this->registerSubCommand(new DeleteSubCommand($this->plugin, "delete", "Delete or Remove TopStats Leaderboard.", ["despawn", "destroy", "remove"]));
		$this->registerSubCommand(new ListSubCommand($this->plugin, "list", "Show TopStats Leaderboard List."));
		$this->registerSubCommand(new TeleportSubCommand($this->plugin, "teleport", "Teleport to TopStats Leaderboard by IDs.", ["tp"]));
		$this->registerSubCommand(new TypeSubCommand($this->plugin, "type", "Show TopStats Type List.", ["types"]));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$this->showForm($sender);
			return;
		}

		$subcommands = array_unique(array_values(array_map(function(TopStatsSubCommand $subCommand) : string {
			return $subCommand->getName();
		}, $this->getSubCommands())));
		$sender->sendMessage(TextFormat::RED . "Usage: /topstats <" . implode("|", $subcommands) . ">");
	}

	public function showForm(Player $player) : void{
		$form = new MenuForm(
			"TopStats Menu",
			"",
			[
				new MenuOption("AutoSave"),
				new MenuOption("Create"),
				new MenuOption("Delete"),
				new MenuOption("List"),
				new MenuOption("Teleport"),
				new MenuOption("Type")
			],
			function(Player $player, int $selectedOption) : void{
				switch($selectedOption){
					case 0:
						$toggle = new ModalForm(
							"Toggle AutoSave",
							"AutoSave TopStats Database",
							function(Player $player, bool $choice) : void{
								$database = $this->plugin->getDatabase();
								if(!$database instanceof SQLInterface){
									$player->sendMessage(TextFormat::RED . "The current Database is not a SQL Database.");
									return;
								}
								$status = $choice ? "on" : "off";
								$database->toggleAutoSave($choice);
								$player->sendMessage(TextFormat::GREEN . "AutoSave is now turned {$status}.");
							},
							"Active",
							"Deactive"
						);
						$player->sendForm($toggle);
						break;
					case 1:
						$create = new CustomForm(
							"Create TopStats Leaderboard",
							[
								new Input("model", "Enter the model of the leaderboard"),
								new Input("type", "Enter the type of the leaderboard (e.g. kills, deaths, etc.)"),
								new Input("top", "Enter the number of top entries to display (default: 1 for NPC model only)", "1", "1"),
								new Toggle("center", "Center the leaderboard (default: true)", true)
							],
							function(Player $player, CustomFormResponse $data) : void{
								$args = [
									"model" => $data->getString("model"),
									"type" => $data->getString("type"),
									"top" => (int) $data->getString("top"),
									"center" => $data->getBool("center")
								];
								$this->getSubCommand("create")?->onRun($player, "create", $args);
							}
						);
						$player->sendForm($create);
						break;
					case 2:
						$delete = new CustomForm(
							"Delete TopStats Leaderboard",
							[
								new Input("id", "Enter the ID of the leaderboard to delete")
							],
							function(Player $player, CustomFormResponse $data) : void{
								$args = [
									"id" => (int) $data->getString("id")
								];
								$this->getSubCommand("delete")?->onRun($player, "delete", $args);
							}
						);
						$player->sendForm($delete);
						break;
					case 3:
						$this->getSubCommand("list")?->onRun($player, "list", []);
						break;
					case 4:
						$teleport = new CustomForm(
							"Teleport to TopStats Leaderboard",
							[
								new Input("id", "Enter the ID of the leaderboard to teleport to")
							],
							function(Player $player, CustomFormResponse $data) : void{
								$args = [
									"id" => (int) $data->getString("id")
								];
								$this->getSubCommand("teleport")?->onRun($player, "teleport", $args);
							}
						);
						$player->sendForm($teleport);
						break;
					case 5:
						$this->getSubCommand("type")?->onRun($player, "type", []);
						break;
				}
			}
		);
		$player->sendForm($form);
	}

	private function getSubCommand(string $name) : ?TopStatsSubCommand{
		foreach($this->getSubCommands() as $subCommand){
			if($subCommand->getName() === $name || in_array($name, $subCommand->getAliases(), true)){
				return $subCommand;
			}
		}
		return null;
	}
}