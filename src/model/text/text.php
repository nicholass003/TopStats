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

namespace nicholass003\topstats\model\text;

use nicholass003\topstats\model\IModel;
use nicholass003\topstats\model\ModelVariant;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class TextModel extends Entity implements IModel{

	protected int $modelID;
	protected string $type;

	public const TAG_MODEL_ID = "ModelID";
	public const TAG_TYPE = "Type";

	protected array $texts = [];

	public static function getNetworkTypeId() : string{ return EntityIds::PLAYER; }

	protected function getInitialDragMultiplier() : float{ return 0; }
	protected function getInitialGravity() : float { return 0; }

	protected function getInitialSizeInfo() : EntitySizeInfo{ return new EntitySizeInfo(0.01, 0.01); }

	public function __construct(Location $location, int $id, string $type, string $text = "", string $title = "", ?CompoundTag $nbt = null){
		parent::__construct($location, $nbt);
		$this->modelID = $id;
		$this->type = $type;
		$this->texts["text"] = $text;
		$this->texts["title"] = $title;
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
		$this->setNameTagAlwaysVisible(true);
		$this->setHasGravity(false);
		$this->setScale(0.00001);
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		$nbt->setInt(self::TAG_MODEL_ID, $this->modelID);
		$nbt->setString(self::TAG_TYPE, $this->type);
		return $nbt;
	}

	public function getModelId() : int{
		return $this->modelID;
	}

	public function setModelId(int $id) : TextModel{
		$this->modelID = $id;
		return $this;
	}

	public function getVariant() : string{
		return ModelVariant::TEXT;
	}

	public function getType() : string{
		return $this->type;
	}

	public function updateText(string $text) : TextModel{
		$this->texts["text"] = $text;
		$this->update();
		return $this;
	}

	public function updateTitle(string $title) : TextModel{
		$this->texts["title"] = $title;
		$this->update();
		return $this;
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
