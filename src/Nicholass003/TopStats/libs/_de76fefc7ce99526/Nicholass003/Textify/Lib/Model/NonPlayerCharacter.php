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

namespace Nicholass003\TopStats\libs\_de76fefc7ce99526\Nicholass003\Textify\Lib\Model;

use Nicholass003\TopStats\libs\_de76fefc7ce99526\Nicholass003\Textify\Lib\TextifyFactory;
use Nicholass003\TopStats\libs\_de76fefc7ce99526\Nicholass003\Textify\Lib\Trait\ActorTrait;
use Nicholass003\TopStats\libs\_de76fefc7ce99526\Nicholass003\Textify\Lib\Trait\NameableTrait;
use Nicholass003\TopStats\libs\_de76fefc7ce99526\Nicholass003\Textify\Lib\Trait\PositionTrait;
use Nicholass003\TopStats\libs\_de76fefc7ce99526\Nicholass003\Textify\Lib\Utils;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\Position;
use Ramsey\Uuid\Uuid;
use function in_array;

final class NonPlayerCharacter extends Human implements Model{
	use ActorTrait;
	use NameableTrait;
	use PositionTrait;

	/** @var CompoundTag|null Custom tags for storing Textify model data */
	private ?CompoundTag $tag = null;

	public function __construct(
		Position $position,
		Skin $skin,
		?CompoundTag $nbt = null,
		int $actorRuntimeId = 0
	){
		$this->setTitle("");
		$this->setText("");
		$this->setModelPosition($position);
		$this->setActorRuntimeId($actorRuntimeId);
		$this->setVariant(Variant::PLAYER);
		$this->setActorId(Uuid::NIL);
		$this->setSkin($skin);
		$this->setCompoundTag($nbt->getCompoundTag(self::TAG_MODEL));
		parent::__construct(Location::fromObject($position, $position->getWorld()), $skin, $nbt);
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setNameTagAlwaysVisible();
		$this->setHasGravity(false);
		$this->setNoClientPredictions();
		$this->setCanSaveWithChunk(false);
	}

	public function setText(string $text) : self{
		$this->texts[Model::TEXT] = $text;
		$this->setNameTag($this->getTitle() . "\n" . $this->getText());
		return $this;
	}

	public function send(Player $player, Action $action) : void{
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

		match($action){
			Action::ADD => $this->spawnTo($player),
			Action::EDIT => $this->setNameTag($this->getTitle() . "\n" . $this->getText()),
			Action::MOVE => $this->teleport($this->getModelPosition()),
			Action::REMOVE => $this->flagForDespawn(),
		};
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
			Model::TAG_SKIN => Utils::writeTagToBase64(Utils::writeSkinNBT($this->skin)),
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