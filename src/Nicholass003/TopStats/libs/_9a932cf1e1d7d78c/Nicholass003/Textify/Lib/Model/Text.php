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

namespace Nicholass003\TopStats\libs\_9a932cf1e1d7d78c\Nicholass003\Textify\Lib\Model;

use Nicholass003\TopStats\libs\_9a932cf1e1d7d78c\Nicholass003\Textify\Lib\TextifyFactory;
use Nicholass003\TopStats\libs\_9a932cf1e1d7d78c\Nicholass003\Textify\Lib\Trait\ActorTrait;
use Nicholass003\TopStats\libs\_9a932cf1e1d7d78c\Nicholass003\Textify\Lib\Trait\NameableTrait;
use Nicholass003\TopStats\libs\_9a932cf1e1d7d78c\Nicholass003\Textify\Lib\Trait\PositionTrait;
use Nicholass003\TopStats\libs\_9a932cf1e1d7d78c\Nicholass003\Textify\Lib\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\ByteMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\player\Player;
use pocketmine\world\Position;
use function in_array;

final class Text implements Model{
	use ActorTrait;
	use NameableTrait;
	use PositionTrait;

	public function __construct(
		string $actorId,
		string $text,
		Position $position,
		?CompoundTag $tag = null,
		int $actorRuntimeId = 0
	){
		$this->setTitle("");
		$this->setText($text);
		$this->setModelPosition($position);
		$this->setActorRuntimeId($actorRuntimeId);
		$this->setVariant(Variant::TEXT);
		$this->setActorId($actorId);
		$this->setCompoundTag($tag->getCompoundTag(self::TAG_MODEL));
	}

	public function send(Player $player, Action $action) : void{
		$session = $player->getNetworkSession();
		$factory = TextifyFactory::getInstance();
		switch($action){
			case Action::ADD:
				if(!$factory->hasSpawnedTo($player, $this->actorRuntimeId)){
					$factory->spawnedTo($player, $this->actorRuntimeId);
				}
				break;
			case Action::EDIT:
				if(!$factory->hasSpawnedTo($player, $this->actorRuntimeId)){
					$factory->spawnedTo($player, $this->actorRuntimeId);
					$action = Action::ADD;
				}
				break;
			case Action::REMOVE:
				if($factory->hasSpawnedTo($player, $this->actorRuntimeId)){
					$factory->despawnFrom($player, $this->actorRuntimeId);
				}
				break;
		}

		$packets = match($action){
			Action::ADD => [
				AddActorPacket::create(
					actorUniqueId: $this->actorRuntimeId,
					actorRuntimeId: $this->actorRuntimeId,
					type: EntityIds::FALLING_BLOCK,
					position: $this->modelPosition,
					motion: null,
					pitch: 0,
					yaw: 0,
					headYaw: 0,
					bodyYaw: 0,
					attributes: [],
					metadata: [
						EntityMetadataProperties::ALWAYS_SHOW_NAMETAG => new ByteMetadataProperty(1),
						EntityMetadataProperties::BOUNDING_BOX_HEIGHT => new FloatMetadataProperty(0.0),
						EntityMetadataProperties::BOUNDING_BOX_WIDTH => new FloatMetadataProperty(0.0),
						EntityMetadataProperties::FLAGS => LongMetadataProperty::buildFromFlags([
							EntityMetadataFlags::IMMOBILE => true,
							EntityMetadataFlags::FIRE_IMMUNE => true
						]),
						EntityMetadataProperties::NAMETAG => new StringMetadataProperty($this->getTitle() . "\n" . $this->getText()),
						EntityMetadataProperties::SCALE => new FloatMetadataProperty(0.0),
						EntityMetadataProperties::VARIANT => new IntMetadataProperty(TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId(VanillaBlocks::AIR()->getStateId()))
					],
					syncedProperties: new PropertySyncData([], []),
					links: []
				)
			],
			Action::EDIT => [
				SetActorDataPacket::create(
					actorRuntimeId: $this->actorRuntimeId,
					metadata: [
						EntityMetadataProperties::NAMETAG => new StringMetadataProperty($this->getTitle() . "\n" . $this->getText())
					],
					syncedProperties: new PropertySyncData([], []),
					tick: 0
				)
			],
			Action::MOVE => [
				RemoveActorPacket::create(
					actorUniqueId: $this->actorRuntimeId
				),
				AddActorPacket::create(
					actorUniqueId: $this->actorRuntimeId,
					actorRuntimeId: $this->actorRuntimeId,
					type: EntityIds::FALLING_BLOCK,
					position: $this->modelPosition,
					motion: null,
					pitch: 0,
					yaw: 0,
					headYaw: 0,
					bodyYaw: 0,
					attributes: [],
					metadata: [
						EntityMetadataProperties::ALWAYS_SHOW_NAMETAG => new ByteMetadataProperty(1),
						EntityMetadataProperties::BOUNDING_BOX_HEIGHT => new FloatMetadataProperty(0.0),
						EntityMetadataProperties::BOUNDING_BOX_WIDTH => new FloatMetadataProperty(0.0),
						EntityMetadataProperties::FLAGS => LongMetadataProperty::buildFromFlags([
							EntityMetadataFlags::IMMOBILE => true,
							EntityMetadataFlags::FIRE_IMMUNE => true
						]),
						EntityMetadataProperties::NAMETAG => new StringMetadataProperty($this->getTitle() . "\n" . $this->getText()),
						EntityMetadataProperties::SCALE => new FloatMetadataProperty(0.0),
						EntityMetadataProperties::VARIANT => new IntMetadataProperty(TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId(VanillaBlocks::AIR()->getStateId()))
					],
					syncedProperties: new PropertySyncData([], []),
					links: []
				)
			],
			Action::REMOVE => [
				RemoveActorPacket::create(
					actorUniqueId: $this->actorRuntimeId
				)
			],
		};

		foreach($packets as $packet){
			$session->sendDataPacket($packet);
		}
	}

	public function update(Action $action) : void{
		if(!in_array($action, [Action::EDIT, Action::MOVE], true)){
			return;
		}

		foreach($this->getViewers() as $player){
			$this->send($player, $action);
		}
	}

	public function destroy() : void{
		TextifyFactory::getInstance()->remove($this->actorId);
		foreach($this->getViewers() as $player){
			$this->send($player, Action::REMOVE);
		}
	}

	public function jsonSerialize() : array{
		return [
			Model::ACTOR_ID => $this->actorId,
			Model::VARIANT => $this->variant,
			Model::TITLE => $this->getTitle(),
			Model::TEXT => $this->getText(),
			Model::TAG_SKIN => null,
			Model::POSITION => [
				Model::POSITION_X => $this->modelPosition->x,
				Model::POSITION_Y => $this->modelPosition->y,
				Model::POSITION_Z => $this->modelPosition->z,
				Model::POSITION_WORLD => $this->modelPosition->getWorld()->getFolderName()
			],
			Model::TAG => Utils::writeTagToBase64($this->tag)
		];
	}
}