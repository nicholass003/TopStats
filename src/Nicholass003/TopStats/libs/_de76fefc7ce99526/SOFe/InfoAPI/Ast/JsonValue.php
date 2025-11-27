<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_de76fefc7ce99526\SOFe\InfoAPI\Ast;

use JsonException;
use Shared\SOFe\InfoAPI\Mapping;
use Nicholass003\TopStats\libs\_de76fefc7ce99526\SOFe\InfoAPI\QualifiedRef;
use Nicholass003\TopStats\libs\_de76fefc7ce99526\SOFe\InfoAPI\StringParser;
use function is_numeric;
use function is_string;
use function json_decode;
use function strlen;






































































/** A value in JSON format to be interpreted based on the type. */
final class JsonValue {
	public function __construct(
		public string $asString,
		public string $json,
	) {
	}
}