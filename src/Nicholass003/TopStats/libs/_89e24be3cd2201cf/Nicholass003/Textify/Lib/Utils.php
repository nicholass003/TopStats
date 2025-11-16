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

namespace Nicholass003\TopStats\libs\_89e24be3cd2201cf\Nicholass003\Textify\Lib;

use Nicholass003\TopStats\libs\_89e24be3cd2201cf\Nicholass003\Textify\Lib\Exception\TextifyInvalidDataException;
use Nicholass003\TopStats\libs\_89e24be3cd2201cf\Nicholass003\Textify\Lib\Model\Model;
use Nicholass003\TopStats\libs\_89e24be3cd2201cf\Nicholass003\Textify\Lib\Model\NonPlayerCharacter;
use Nicholass003\TopStats\libs\_89e24be3cd2201cf\Nicholass003\Textify\Lib\Model\Text;
use Nicholass003\TopStats\libs\_89e24be3cd2201cf\Nicholass003\Textify\Lib\Model\Variant;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\Server;
use pocketmine\world\Position;
use Ramsey\Uuid\Uuid;
use function base64_decode;
use function base64_encode;
use function is_array;
use function json_decode;













































































final class Utils{

	public static function writeTagToBase64(CompoundTag $tag) : string{
		$stream = new BigEndianNbtSerializer();
		return base64_encode($stream->write(new TreeRoot($tag)));
	}

	public static function readTagFromBase64(string $base64) : CompoundTag{
		$decoded = base64_decode($base64, true);
		if($decoded === false){
			throw new TextifyInvalidDataException("Invalid base64 data for NBT tag");
		}
		$stream = new BigEndianNbtSerializer();
		return $stream->read($decoded)->getTag();
	}

	public static function writeSkinNBT(Skin $skin) : CompoundTag{
		$nbt = CompoundTag::create();
		return $nbt->setTag("Skin", CompoundTag::create()
			->setString(Model::TAG_SKIN_NAME, $skin->getSkinId())
			->setByteArray(Model::TAG_SKIN_DATA, $skin->getSkinData())
			->setByteArray(Model::TAG_SKIN_CAPE_DATA, $skin->getCapeData())
			->setString(Model::TAG_SKIN_GEOMETRY_NAME, $skin->getGeometryName())
			->setByteArray(Model::TAG_SKIN_GEOMETRY_DATA, $skin->getGeometryData())
		);
	}
}