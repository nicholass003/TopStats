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

use Nicholass003\TopStats\libs\_3a083282d126e516\CortexPE\Commando\BaseCommand;
use Nicholass003\TopStats\Command\SubCommand\AutoSaveSubCommand;
use Nicholass003\TopStats\Command\SubCommand\CreateSubCommand;
use Nicholass003\TopStats\Command\SubCommand\DeleteSubCommand;
use Nicholass003\TopStats\Command\SubCommand\ListSubCommand;
use Nicholass003\TopStats\Command\SubCommand\TeleportSubCommand;
use Nicholass003\TopStats\Command\SubCommand\TopStatsSubCommand;
use Nicholass003\TopStats\Command\SubCommand\TypeSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use function array_map;
use function array_unique;
use function array_values;
use function implode;

class TopStatsCommand extends BaseCommand{

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
		$subcommands = array_unique(array_values(array_map(function(TopStatsSubCommand $subCommand) : string {
			return $subCommand->getName();
		}, $this->getSubCommands())));
		$sender->sendMessage(TextFormat::RED . "Usage: /topstats <" . implode("|", $subcommands) . ">");
	}
}