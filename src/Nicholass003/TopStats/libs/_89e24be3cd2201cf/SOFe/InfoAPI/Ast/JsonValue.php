<?php

declare(strict_types=1);

namespace Nicholass003\TopStats\libs\_89e24be3cd2201cf\SOFe\InfoAPI\Ast;

use JsonException;
use Shared\SOFe\InfoAPI\Mapping;
use Nicholass003\TopStats\libs\_89e24be3cd2201cf\SOFe\InfoAPI\QualifiedRef;
use Nicholass003\TopStats\libs\_89e24be3cd2201cf\SOFe\InfoAPI\StringParser;
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