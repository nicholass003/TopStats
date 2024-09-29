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

namespace nicholass003\topstats\model\player;

use nicholass003\topstats\model\IModel;
use nicholass003\topstats\model\ModelVariant;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class PlayerModel extends Human implements IModel{

	protected int $modelID;
	protected string $type;
	protected int $top;

	public const TAG_MODEL_ID = "ModelID";
	public const TAG_TYPE = "Type";
	public const TAG_TOP = "Top";

	protected bool $spawned = false;

	protected array $texts = [];

	public function __construct(Location $location, Skin $skin, int $id, string $type, int $top, ?CompoundTag $nbt = null){
		parent::__construct($location, $skin, $nbt);
		$this->modelID = $id;
		$this->type = $type;
		$this->top = $top;
		$this->texts["text"] = "";
		$this->texts["title"] = "";
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$typeTag = $nbt->getTag(self::TAG_TYPE);
		if($typeTag instanceof StringTag){
			$this->type = $typeTag->getValue();
		}
		$modelIdTag = $nbt->getTag(self::TAG_MODEL_ID);
		if($modelIdTag instanceof IntTag){
			$this->modelID = $modelIdTag->getValue();
		}
		$topTag = $nbt->getTag(self::TAG_TOP);
		if($topTag instanceof IntTag){
			$this->top = $topTag->getValue();
		}
		$this->setNameTagAlwaysVisible(true);
		$this->setHasGravity(false);
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		$nbt->setInt(self::TAG_MODEL_ID, $this->modelID);
		$nbt->setString(self::TAG_TYPE, $this->type);
		$nbt->setInt(self::TAG_TOP, $this->top);
		return $nbt;
	}

	public function getModelId() : int{
		return $this->modelID;
	}

	public function setModelId(int $id) : PlayerModel{
		$this->modelID = $id;
		return $this;
	}

	public function getTop() : int{
		return $this->top;
	}

	public function setTop(int $top) : PlayerModel{
		$this->top = $top;
		return $this;
	}

	public function isSpawned() : bool{
		return $this->spawned === true;
	}

	public function setSpawned(bool $value) : PlayerModel{
		$this->spawned = $value;
		return $this;
	}

	public function getVariant() : string{
		return ModelVariant::PLAYER;
	}

	public function getType() : string{
		return $this->type;
	}

	public function updateText(string $text) : PlayerModel{
		$this->texts["text"] = $text;
		$this->update();
		return $this;
	}

	public function updateTitle(string $title) : PlayerModel{
		$this->texts["title"] = $title;
		$this->update();
		return $this;
	}

	public function getTexts() : array{
		return $this->texts;
	}

	public function update() : void{
		$this->setNameTag(
			$this->texts["title"] . "\n" . $this->texts["text"]
		);
	}

	public function destroy() : void{
		if(!$this->closed){
			$this->flagForDespawn();
		}
	}
}