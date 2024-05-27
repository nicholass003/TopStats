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

namespace nicholass003\topstats\listener;

use nicholass003\topstats\database\data\DataAction;
use nicholass003\topstats\database\data\DataType;
use nicholass003\topstats\model\player\PlayerModel;
use nicholass003\topstats\TopStats;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChangeSkinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerEmoteEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemEnchantEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\player\Player;
use function atan2;
use const M_PI;

class EventListener implements Listener{

	public function __construct(
		protected TopStats $plugin
	){}

	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$this->plugin->getDatabase()->create($player);
	}

	public function onPlayerDeath(PlayerDeathEvent $event) : void{
		$player = $event->getPlayer();
		$this->plugin->getDatabase()->update($player, [DataType::DEATH => 1], DataAction::ADDITION);
		$source = $player->getLastDamageCause();
		if($source instanceof EntityDamageByEntityEvent){
			$attacker = $source->getDamager() ;
			if($attacker instanceof Player){
				$this->plugin->getDatabase()->update($attacker, [DataType::KILL => 1], DataAction::ADDITION);
			}
		}
	}

	public function onBlockBreak(BlockBreakEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::BLOCK_BREAK => 1], DataAction::ADDITION);
		}
	}

	public function onBlockPlace(BlockPlaceEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::BLOCK_PLACE => 1], DataAction::ADDITION);
		}
	}

	public function onPlayerChangeSkin(PlayerChangeSkinEvent $event) : void{
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::CHANGE_SKIN => 1], DataAction::ADDITION);
		}
	}

	public function onPlayerChat(PlayerChatEvent $event) : void{
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::CHAT => 1], DataAction::ADDITION);
		}
	}

	public function onPlayerItemConsume(PlayerItemConsumeEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::CONSUME => 1], DataAction::ADDITION);
		}
	}

	public function onCraftItem(CraftItemEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::CRAFTING => 1], DataAction::ADDITION);
		}
	}

	public function onPlayerDropItem(PlayerDropItemEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::DROP_ITEM => 1], DataAction::ADDITION);
		}
	}

	public function onPlayerEmote(PlayerEmoteEvent $event) : void{
		$player = $event->getPlayer();
		if(!$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::EMOTE => 1], DataAction::ADDITION);
		}
	}

	public function onPlayerItemEnchant(PlayerItemEnchantEvent $event) : void{
		$player = $event->getPlayer();
		if($player->hasFiniteResources() && !$event->isCancelled()){
			$this->plugin->getDatabase()->update($player, [DataType::ENCHANT => 1], DataAction::ADDITION);
		}
	}

	public function onEntityItemPickup(EntityItemPickupEvent $event) : void{
		$player = $event->getEntity();
		if($player instanceof Player){
			if($player->hasFiniteResources() && !$event->isCancelled()){
				$this->plugin->getDatabase()->update($player, [DataType::ITEM_PICKUP => 1], DataAction::ADDITION);
			}
		}
	}

	public function onEntityDamage(EntityDamageEvent $event) : void{
		$entity = $event->getEntity();
		if($entity instanceof PlayerModel){
			$event->cancel();
		}
	}

	public function onPlayerMove(PlayerMoveEvent $event) : void{
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

			if($entity instanceof PlayerModel){
				$pk = MovePlayerPacket::create($entity->getId(), $entity->getPosition()->add(0, $entity->getEyeHeight(), 0), $pitch, $yaw, $yaw, MovePlayerPacket::MODE_NORMAL, $entity->onGround, 0, 0, 0, 0);
				$player->getNetworkSession()->sendDataPacket($pk);
			}
		}
	}
}
