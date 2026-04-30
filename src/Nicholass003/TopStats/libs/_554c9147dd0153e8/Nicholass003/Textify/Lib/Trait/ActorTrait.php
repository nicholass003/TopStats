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

namespace Nicholass003\TopStats\libs\_554c9147dd0153e8\Nicholass003\Textify\Lib\Trait;

use Nicholass003\TopStats\libs\_554c9147dd0153e8\Nicholass003\Textify\Lib\Exception\TextifyException;
use Nicholass003\TopStats\libs\_554c9147dd0153e8\Nicholass003\Textify\Lib\Model\Variant;
use pocketmine\entity\Entity;
use Ramsey\Uuid\Uuid;

trait ActorTrait{

	private int $actorRuntimeId;

	private Variant $variant;

	private string $actorId;

	public function getActorRuntimeId() : int{
		return $this->actorRuntimeId;
	}

	public function setActorRuntimeId(int $actorRuntimeId) : self{
		$this->actorRuntimeId = $actorRuntimeId === 0 ? Entity::nextRuntimeId() : $actorRuntimeId;
		return $this;
	}

	public function getVariant() : Variant{
		return $this->variant;
	}

	public function setVariant(Variant $variant) : self{
		$this->variant = $variant;
		return $this;
	}

	public function getActorId() : string{
		return $this->actorId;
	}

	public function setActorId(string $actorId) : self{
		if(Uuid::isValid($actorId)){
			$this->actorId = $actorId;
			return $this;
		}
		throw new TextifyException("Invalid UUID");
	}
}