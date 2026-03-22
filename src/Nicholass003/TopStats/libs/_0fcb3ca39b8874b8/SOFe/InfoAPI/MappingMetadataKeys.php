<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_0fcb3ca39b8874b8\SOFe\InfoAPI;
















final class MappingMetadataKeys {
	/**
	 * Marks the name of the plugin that provides the mapping.
	 *
	 * The mapping would be unusable without the specified plugin.
	 */
	public const SOURCE_PLUGIN = "infoapi/source-plugin";

	/**
	 * Indicates that this is the non-primary alias for
	 * the mapping from the same source kind with the specified name.
	 */
	public const ALIAS_OF = "infoapi/alias-of";
}