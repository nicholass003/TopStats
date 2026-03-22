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

namespace Nicholass003\TopStats\libs\_1a7c7f2c874275d3\Nicholass003\Textify\Lib;

use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\Nicholass003\Textify\Lib\Exception\TextifyInvalidDataException;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\Nicholass003\Textify\Lib\Model\Model;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\Nicholass003\Textify\Lib\Model\NonPlayerCharacter;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\Nicholass003\Textify\Lib\Model\Text;
use Nicholass003\TopStats\libs\_1a7c7f2c874275d3\Nicholass003\Textify\Lib\Model\Variant;
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

final class Textify{

	public const TAG_SKIN = "skin";
	public const TAG_COMPOUND = "compound";

	public const STORAGE_FILENAME = "textify_models.json";
	public const STORAGE_KEY_MARKER = "storage_textify_marker";
	public const STORAGE_VALUE_MARKER = "textify-model-storage";

	/**
	 * @param Variant     $variant
	 * @param string      $text
	 * @param Position    $position
	 * @param string|null $actorId
	 * @param array       $extraData
	 *
	 * @return Model
	 */
	public static function create(Variant $variant, Position $position, string $title = "", string $text = "", ?string $actorId = null, array $extraData = []) : Model{
		$factory = TextifyFactory::getInstance();
		$id = $actorId !== null ? $actorId : Uuid::uuid4()->toString();
		if(($model = $factory->get($id)) !== null){
			return $model;
		}

		$model = match($variant){
			Variant::NPC, Variant::PLAYER => (new NonPlayerCharacter($position, $extraData[self::TAG_SKIN], $extraData[self::TAG_COMPOUND] ?? null))
												->setActorId($id)
												->setText($text),
			Variant::TEXT => new Text($id, $text, $position, $extraData[self::TAG_COMPOUND] ?? null)
		};
		$model->setTitle($title);
		if($model->getModelPosition()->isValid()){
			$model->setModelWorldName($model->getModelPosition()->getWorld()->getFolderName());
		}

		$factory->add($model);

		return $model;
	}

	/**
	 * @param string $json
	 *
	 * @return Model
	 */
	public static function fromString(string $json) : Model{
		$data = json_decode($json, true);
		if(!is_array($data)){
			throw new TextifyInvalidDataException("Malformed JSON data: Unable to parse model information");
		}

		$requiredKeys = [Model::ACTOR_ID, Model::VARIANT, Model::TITLE, Model::TEXT, Model::POSITION];
		foreach($requiredKeys as $key){
			if(!isset($data[$key])){
				throw new TextifyInvalidDataException("Missing required key '$key'");
			}
		}

		$skinData = $data[Model::TAG_SKIN];

		$extraData = [
			self::TAG_COMPOUND => CompoundTag::create()->setTag(Model::TAG_MODEL, Utils::readTagFromBase64($data[Model::TAG]))
		];
		if($skinData !== null){
			$extraData[self::TAG_SKIN] = Human::parseSkinNBT(Utils::readTagFromBase64($skinData));
		}
		$pos = $data[Model::POSITION];
		$textify = self::create(
			Variant::fromString($data[Model::VARIANT]),
			Position::fromObject(new Vector3($pos[Model::POSITION_X], $pos[Model::POSITION_Y], $pos[Model::POSITION_Z]), Server::getInstance()->getWorldManager()->getWorldByName($pos[Model::POSITION_WORLD])),
			$data[Model::TITLE],
			$data[Model::TEXT],
			$data[Model::ACTOR_ID],
			$extraData
		);
		$textify->setModelWorldName($pos[Model::POSITION_WORLD]);
		return $textify;
	}
}