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

namespace Nicholass003\TopStats\Listener;

use Nicholass003\TopStats\libs\_8656bfc3b17ec20f\Nicholass003\Textify\Lib\Model\NonPlayerCharacter;
use Nicholass003\TopStats\libs\_8656bfc3b17ec20f\Nicholass003\Textify\Lib\Model\Text;
use Nicholass003\TopStats\Database\Data\DataAction;
use Nicholass003\TopStats\Database\Data\DataType;
use Nicholass003\TopStats\Leaderboard\LeaderboardManager;
use Nicholass003\TopStats\TopStats;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerEmoteEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemEnchantEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\player\Player;
use function atan2;
use function in_array;
use const M_PI;

class EventListener implements Listener{

	private LeaderboardManager $leaderboardManager;

	public function __construct(
		protected TopStats $plugin
	){
		$this->leaderboardManager = $plugin->getLeaderboardManager();
	}

	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$this->plugin->getDatabase()->create($player);
		foreach($this->leaderboardManager->leaderboards() as $leaderboard){
			$model = $leaderboard->getModel();
			if($model instanceof Text){
				$leaderboard->spawn();
			}
		}
	}

	public function onPlayerDeath(PlayerDeathEvent $event) : void{
		$player = $event->getPlayer();
		$this->plugin->getDatabase()->update($player, [DataType::DEATH => 1], DataAction::ADDITION);
		$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::DEATH);
		$source = $player->getLastDamageCause();
		if($source instanceof EntityDamageByEntityEvent){
			$attacker = $source->getDamager() ;
			if($attacker instanceof Player){
				$this->plugin->getDatabase()->update($attacker, [DataType::KILL => 1], DataAction::ADDITION);
				$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::KILL);
			}
		}
	}

	public function onBlockBreak(BlockBreakEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::BLOCK_BREAK => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::BLOCK_BREAK);
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::BLOCK_PLACE => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::BLOCK_PLACE);
			if(in_array($event->getItem(), [
				VanillaItems::BEETROOT_SEEDS(),
				VanillaItems::CARROT(),
				VanillaItems::MELON_SEEDS(),
				//TODO: Nether Wart ??
				VanillaItems::PITCHER_POD(),
				VanillaItems::POTATO(),
				VanillaItems::PUMPKIN_SEEDS(),
				VanillaItems::TORCHFLOWER_SEEDS(),
				VanillaItems::WHEAT_SEEDS()
			], true)){
				$this->plugin->getDatabase()->update($player, [DataType::FARM => 1], DataAction::ADDITION);
				$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::FARM);
			}
		}
	}

	public function onPlayerChangeSkin(PlayerChangeSkinEvent $event) : void{
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::CHANGE_SKIN => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::CHANGE_SKIN);
		}
	}

	public function onPlayerChat(PlayerChatEvent $event) : void{
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::CHAT => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::CHAT);
		}
	}

	public function onPlayerItemConsume(PlayerItemConsumeEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::CONSUME => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::CONSUME);
		}
	}

	public function onCraftItem(CraftItemEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::CRAFTING => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::CRAFTING);
		}
	}

	public function onPlayerDropItem(PlayerDropItemEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::DROP_ITEM => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::DROP_ITEM);
		}
	}

	public function onPlayerEmote(PlayerEmoteEvent $event) : void{
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::EMOTE => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::EMOTE);
		}
	}

	public function onPlayerItemEnchant(PlayerItemEnchantEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::ENCHANT => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::ENCHANT);
		}
	}

	public function onEntityItemPickup(EntityItemPickupEvent $event) : void{
		$player = $event->getEntity();
		if($player instanceof Player){
			if($player->hasFiniteResources() && !$event->isCancelled()){
				$this->plugin->getDatabase()->update($player, [DataType::ITEM_PICKUP => 1], DataAction::ADDITION);
				$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::ITEM_PICKUP);
			}
		}
	}

	public function onPlayerJump(PlayerJumpEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources()){
			$this->plugin->getDatabase()->update($player, [DataType::JUMP => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::JUMP);
		}
	}

	public function onPlayerKick(PlayerKickEvent $event) : void{
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::KICK => 1], DataAction::ADDITION);
			$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::KICK);
		}
	}

	public function onEntityRegainHealth(EntityRegainHealthEvent $event) : void{
		$player = $event->getEntity();
		if($player instanceof Player){
			if($player->hasFiniteResources() && !$event->isCancelled()){
				$this->plugin->getDatabase()->update($player, [DataType::HEAL => $event->getAmount()], DataAction::ADDITION);
				$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::HEAL);
			}
		}
	}

	public function onPlayerExperienceChange(PlayerExperienceChangeEvent $event) : void{
		$player = $event->getEntity();
		if($player instanceof Player){
			if($player->hasFiniteResources() && !$event->isCancelled()){
				$this->plugin->getDatabase()->update($player, [DataType::XP => $event->getNewLevel()], DataAction::ADDITION);
				$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::XP);
			}
		}
	}

	public function onEntityDamage(EntityDamageEvent $event) : void{
		$entity = $event->getEntity();
		if($entity instanceof NonPlayerCharacter){
			$event->cancel();
		}
		if($event instanceof EntityDamageByEntityEvent){
			$victim = $event->getEntity();
			if($victim instanceof Player){
				$attacker = $event->getDamager();
				if($attacker instanceof Player){
					if($victim->hasFiniteResources() && $attacker->hasFiniteResources() && !$event->isCancelled()){
						$this->plugin->getDatabase()->update($attacker, [DataType::DAMAGE_DEALT => $event->getOriginalBaseDamage()], DataAction::ADDITION);
						$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::DAMAGE_DEALT);
						$this->plugin->getDatabase()->update($victim, [DataType::DAMAGE_RECEIVED => $event->getFinalDamage()], DataAction::ADDITION);
						$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::DAMAGE_RECEIVED);
					}
				}
			}
		}elseif($event instanceof EntityDamageByChildEntityEvent){
			$victim = $event->getEntity();
			if($victim instanceof Player){
				$attacker = $event->getDamager();
				if($attacker instanceof Player){
					$child = $event->getChild();
					if($child instanceof Projectile){
						if($victim->hasFiniteResources() && $attacker->hasFiniteResources() && !$event->isCancelled()){
							$this->plugin->getDatabase()->update($attacker, [DataType::DAMAGE_DEALT => $event->getOriginalBaseDamage()], DataAction::ADDITION);
							$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::DAMAGE_DEALT);
							$this->plugin->getDatabase()->update($victim, [DataType::DAMAGE_RECEIVED => $event->getFinalDamage()], DataAction::ADDITION);
							$this->leaderboardManager->dispatchLeaderboardUpdate(DataType::DAMAGE_RECEIVED);
						}
					}
				}
			}
		}
	}

	public function onPlayerMove(PlayerMoveEvent $event) : void{
		if(!$this->plugin->getConfig()->get("rotate", true)){
			return;
		}

		$player = $event->getPlayer();
		$from = $event->getFrom();
		$to = $event->getTo();

		if($from->distance($to) < 0.1){
			return;
		}

		$maxDistance = 16;
		foreach($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance), $player) as $entity){
			if($entity instanceof Player){
				continue;
			}

			$xdiff = $player->getLocation()->x - $entity->getLocation()->x;
			$zdiff = $player->getLocation()->z - $entity->getLocation()->z;
			$angle = atan2($zdiff, $xdiff);
			$yaw = (($angle * 180) / M_PI) - 90;
			$ydiff = $player->getLocation()->y - $entity->getLocation()->y;
			$v = new Vector2($entity->getLocation()->x, $entity->getLocation()->z);
			$dist = $v->distance(new Vector2($player->getLocation()->x, $player->getLocation()->z));
			$angle = atan2($dist, $ydiff);
			$pitch = (($angle * 180) / M_PI) - 90;

			if($entity instanceof NonPlayerCharacter){
				$pk = MovePlayerPacket::create($entity->getId(), $entity->getPosition()->add(0, $entity->getEyeHeight(), 0), $pitch, $yaw, $yaw, MovePlayerPacket::MODE_NORMAL, $entity->onGround, 0, 0, 0, 0);
				$player->getNetworkSession()->sendDataPacket($pk);
			}
		}
	}
}