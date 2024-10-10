<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_8d917a7f9d1fddb3\SOFe\InfoAPI\Ast;

use JsonException;
use Shared\SOFe\InfoAPI\Mapping;
use nicholass003\topstats\libs\_8d917a7f9d1fddb3\SOFe\InfoAPI\QualifiedRef;
use nicholass003\topstats\libs\_8d917a7f9d1fddb3\SOFe\InfoAPI\StringParser;
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