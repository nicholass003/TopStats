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

namespace Nicholass003\TopStats\libs\_809aa045474901d4\Nicholass003\Textify\Lib\Model;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\Position;

/**
 * Interface Model
 *
 * Represents a Textify model entity such as FloatingText or NonPlayerCharacter.
 * Provides structure for serialization, behavior, and entity display control.
 */
interface Model extends \JsonSerializable{

	// === NBT Key ===
	public const TAG_MODEL = "Model";

	// === JSON Serialization Keys ===
	public const ACTOR_ID = "actor_id";
	public const TITLE = "title";
	public const TEXT = "text";
	public const VARIANT = "variant";
	public const TAG = "tag";

	public const POSITION = "position";
	public const POSITION_X = "x";
	public const POSITION_Y = "y";
	public const POSITION_Z = "z";
	public const POSITION_WORLD = "world";

	public const TAG_SKIN = "Skin"; //TAG_Compound
	public const TAG_SKIN_NAME = "Name"; //TAG_String
	public const TAG_SKIN_DATA = "Data"; //TAG_ByteArray
	public const TAG_SKIN_CAPE_DATA = "CapeData"; //TAG_ByteArray
	public const TAG_SKIN_GEOMETRY_NAME = "GeometryName"; //TAG_String
	public const TAG_SKIN_GEOMETRY_DATA = "GeometryData"; //TAG_ByteArray

	/**
	 * Returns the runtime ID used in packets.
	 *
	 * @return int
	 */
	public function getActorRuntimeId() : int;

	/**
	 * Sets the runtime ID used in packets.
	 *
	 * @param int $actorRuntimeId
	 *
	 * @return $this
	 */
	public function setActorRuntimeId(int $actorRuntimeId) : self;

	/**
	 * Returns the internal actor ID (unique model identifier).
	 *
	 * @return string
	 */
	public function getActorId() : string;

	/**
	 * Sets the internal actor ID (unique model identifier).
	 *
	 * @param string $actorId
	 *
	 * @return $this
	 */
	public function setActorId(string $actorId) : self;

	/**
	 * Gets the variant @see Variant.
	 *
	 * @return Variant
	 */
	public function getVariant() : Variant;

	/**
	 * Sets the variant @see Variant.
	 *
	 * @param Variant $variant
	 *
	 * @return $this
	 */
	public function setVariant(Variant $variant) : self;

	/**
	 * Gets the display title of the entity.
	 *
	 * @return string
	 */
	public function getTitle() : string;

	/**
	 * Sets the display title of the entity.
	 *
	 * @param string $title
	 *
	 * @return $this
	 */
	public function setTitle(string $title) : self;

	/**
	 * Gets the main display text below the title.
	 *
	 * @return string
	 */
	public function getText() : string;

	/**
	 * Sets the main display text below the title.
	 *
	 * @param string $text
	 *
	 * @return $this
	 */
	public function setText(string $text) : self;

	/**
	 * Gets the position of the model in the world.
	 *
	 * @return Position
	 */
	public function getModelPosition() : Position;

	/**
	 * Sets the position of the model in the world.
	 *
	 * @param Position $position
	 *
	 * @return $this
	 */
	public function setModelPosition(Position $position) : self;

	/**
	 * Gets the world name of the model.
	 *
	 * @return string
	 */
	public function getModelWorldName() : string;

	/**
	 * Sets the world name of the model.
	 *
	 * @param string $worldName
	 *
	 * @return $this
	 */
	public function setModelWorldName(string $worldName) : self;

	/**
	 * Gets all players currently viewing the model's position in the world.
	 *
	 * @return Player[]
	 */
	public function getViewers() : array;

	/**
	 * Sends the model packet to a player.
	 *
	 * @param Player $player
	 * @param Action $action
	 *
	 * @return void
	 */
	public function send(Player $player, Action $action) : void;

	/**
	 * Updates the model with a specified action.
	 *
	 * @param Action $action
	 *
	 * @return void
	 */
	public function update(Action $action) : void;

	/**
	 * @return void
	 */
	public function destroy() : void;

	/**
	 * Gets the NBT tag of the model.
	 *
	 * @return CompoundTag
	 */
	public function getCompoundTag() : CompoundTag;

	/**
	 * Sets the NBT tag of the model.
	 *
	 * @param CompoundTag|null $tag
	 *
	 * @return self
	 */
	public function setCompoundTag(?CompoundTag $tag) : self;
}