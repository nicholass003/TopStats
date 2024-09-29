<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_9bd0966c4414a90a\SOFe\InfoAPI;

final class KindMetadataKeys {
	/** Marks a kind as root-compatible. */
	public const IS_ROOT = "infoapi/is-root";
	/** Sets the template name for a root kind in the mapping browser. */
	public const BROWSER_TEMPLATE_NAME = "infoapi:browser/template-name";

	/**
	 * Marks the name of the plugin that manages the kind.
	 *
	 * This means the kind would be unreasonable or impossible to use without the specified plugin.
	 * Only for display purpose.
	 */
	public const SOURCE_PLUGIN = "infoapi/source-plugin";
}