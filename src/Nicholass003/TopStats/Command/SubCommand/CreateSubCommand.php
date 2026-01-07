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

namespace Nicholass003\TopStats\Command\SubCommand;

use Nicholass003\TopStats\libs\_809aa045474901d4\CortexPE\Commando\args\BooleanArgument;
use Nicholass003\TopStats\libs\_809aa045474901d4\CortexPE\Commando\args\IntegerArgument;
use Nicholass003\TopStats\libs\_809aa045474901d4\CortexPE\Commando\args\RawStringArgument;
use Nicholass003\TopStats\libs\_809aa045474901d4\Nicholass003\Textify\Lib\Model\Model;
use Nicholass003\TopStats\libs\_809aa045474901d4\Nicholass003\Textify\Lib\Model\Variant;
use Nicholass003\TopStats\libs\_809aa045474901d4\Nicholass003\Textify\Lib\Textify;
use Nicholass003\TopStats\Database\Data\DataType;
use Nicholass003\TopStats\External\ExternalIntegrationRegistry;
use Nicholass003\TopStats\Leaderboard\Leaderboard;
use Nicholass003\TopStats\Utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function array_unique;
use function in_array;
use const SORT_STRING;

class CreateSubCommand extends TopStatsSubCommand{

	public function prepare() : void{
		$this->setPermission("topstats.command.create");

		$this->registerArgument(0, new RawStringArgument("model"));
		$this->registerArgument(1, new RawStringArgument("type"));
		$this->registerArgument(2, new IntegerArgument("top", true));
		$this->registerArgument(3, new BooleanArgument("center", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(TextFormat::RED . "You must login to use this command.");
			return;
		}
		if(isset($args["model"])){
			if(isset($args["type"])){
				$builtInTypes = DataType::ALL;

				$externalTypes = ExternalIntegrationRegistry::getInstance()->getActiveTypes();

				$allowedTypes = array_unique([
					...$builtInTypes,
					...$externalTypes
				], SORT_STRING);

				if(!in_array($args["type"], $allowedTypes, true)){
					$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $aliasUsed . " " . $args["model"] . " <type> <top>");
					$sender->sendMessage(TextFormat::RED . "Type \"/topstats types\" to get type list");
					return;
				}
				if($args["type"] === DataType::MONEY){
					if($this->plugin->getEconomyProvider() === null){
						$sender->sendMessage(TextFormat::RED . "No EconomyProvider found, you must install the Economy plugin to enable this feature.");
						$sender->sendMessage(TextFormat::RED . "Example: \"BedrockEconomy\" or \"EconomyAPI\"");
						switch($this->plugin->getConfig()->get("economy")["provider"]){
							case "bedrockeconomy":
								if($this->plugin->getServer()->getPluginManager()->getPlugin("EconomyAPI") !== null){
									$sender->sendMessage(TextFormat::RED . "You should install BedrockEconomy instead of EconomyAPI or you can change it in config.yml and adapt it to your installed plugins.");
								}
								break;
							case "economyapi":
								if($this->plugin->getServer()->getPluginManager()->getPlugin("BedrockEconomy") !== null){
									$sender->sendMessage(TextFormat::RED . "You should install EconomyAPI instead of BedrockEconomy or you can change it in config.yml and adapt it to your installed plugins.");
								}
								break;
						}
						return;
					}
				}
				$center = $args["center"] ?? false;
				$location = $sender->getLocation();
				if($center){
					$location = Location::fromObject($location->floor()->add(0.5, 0, 0.5), $location->getWorld());
				}

				$variant = Variant::fromString($args["model"]);
				if($variant === null){
					$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $aliasUsed . " <player|text> <type> [top]" . isset($args["top"]) && $variant !== Variant::TEXT ? " top: " . $args["top"] : "");
					return;
				}

				$extraData = [
					Textify::TAG_COMPOUND => CompoundTag::create()->setTag(Model::TAG_MODEL, CompoundTag::create()->setString(Leaderboard::TAG_TYPE, $args["type"])->setByte(Leaderboard::TAG_TOP, $args["top"] ?? 0))
				];

				$source = ExternalIntegrationRegistry::getInstance()->getSource($args["type"]);
				$entries = $source !== null ? $source->getEntries() : [];
				$forceSorting = $source !== null ? $source->getIntegration()->isForceSorting() : false;
				if($variant === Variant::PLAYER){
					$extraData[Textify::TAG_SKIN] = Utils::getTopStatsPlayerSkin(DataType::get($args["type"]) !== false ? $this->plugin->getDatabase()->getTemporaryData() : $entries, $args["type"], (int) $args["top"] ?? 1);
				}elseif($variant === Variant::TEXT && $center){
					$location = Location::fromObject($location->add(0, 0.5, 0), $location->getWorld());
				}

				$leaderboard = new Leaderboard(Textify::create($variant, $location, "", "", null, $extraData));
				$this->leaderboardManager->add($leaderboard);
				$leaderboard->setForceSorting($forceSorting);
				$leaderboard->spawn();

				$sender->sendMessage(TextFormat::GREEN . "Successfully spawn TopStats with model: " . $args["model"] . " type: " . $args["type"]);
			}else{
				$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $aliasUsed . " " . $args["model"] . " <type> <top>");
			}
		}else{
			$sender->sendMessage(TextFormat::RED . "Usage: /topstats " . $aliasUsed . " <player|text> <type> <top>");
		}
	}
}