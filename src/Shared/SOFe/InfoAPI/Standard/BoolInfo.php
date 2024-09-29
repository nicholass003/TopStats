<?php

declare(strict_types=1);

/**
 * Constants for standard kinds shared between instances of the shaded virion.
 *
 * Each kind has its own class to allow adding new standard kinds in the future.
 */
namespace Shared\SOFe\InfoAPI\Standard;
























/** An info of type `bool`, representing a generic bool. */
final class BoolInfo {
	public const KIND = "infoapi/bool";
}