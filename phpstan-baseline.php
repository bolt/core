<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Api\\\\ContentDataPersister\\:\\:persist\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/ContentDataPersister.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Api\\\\ContentDataPersister\\:\\:persist\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/ContentDataPersister.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Api\\\\ContentDataPersister\\:\\:remove\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/ContentDataPersister.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Api\\\\ContentDataPersister\\:\\:remove\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/ContentDataPersister.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Api\\\\ContentDataPersister\\:\\:supports\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/ContentDataPersister.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Api\\\\ContentDataPersister\\:\\:supports\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/ContentDataPersister.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$contentType of static method Bolt\\\\Configuration\\\\Content\\\\FieldType\\:\\:factory\\(\\) expects Bolt\\\\Configuration\\\\Content\\\\ContentType, Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/ContentDataPersister.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Api\\\\Extensions\\\\ContentExtension\\:\\:applyToItem\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Extensions/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Api\\\\Extensions\\\\ContentExtension\\:\\:applyToItem\\(\\) has parameter \\$identifiers with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Extensions/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Api\\\\Extensions\\\\ContentExtension\\:\\:\\$viewlessContentTypes with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Api/Extensions/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CachingInterface\\:\\:execute\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CachingInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CachingInterface\\:\\:execute\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CachingInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CachingInterface\\:\\:setCacheKey\\(\\) has parameter \\$tokens with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CachingInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CanonicalCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CanonicalCacher\\:\\:execute\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CanonicalCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CanonicalCacher\\:\\:execute\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CanonicalCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CanonicalCacher\\:\\:generateLink\\(\\) has parameter \\$canonical with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CanonicalCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CanonicalCacher\\:\\:generateLink\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CanonicalCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CanonicalCacher\\:\\:getCacheTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CanonicalCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CanonicalCacher\\:\\:getTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CanonicalCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CanonicalCacher\\:\\:setCacheKey\\(\\) has parameter \\$tokens with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CanonicalCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\CanonicalCacher\\:\\:setCacheTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CanonicalCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Cache\\\\CanonicalCacher\\:\\:\\$cacheTags type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/CanonicalCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/ContentToArrayCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\ContentToArrayCacher\\:\\:contentToArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/ContentToArrayCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\ContentToArrayCacher\\:\\:execute\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/ContentToArrayCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\ContentToArrayCacher\\:\\:execute\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/ContentToArrayCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\ContentToArrayCacher\\:\\:getCacheTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/ContentToArrayCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\ContentToArrayCacher\\:\\:getTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/ContentToArrayCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\ContentToArrayCacher\\:\\:setCacheKey\\(\\) has parameter \\$tokens with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/ContentToArrayCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\ContentToArrayCacher\\:\\:setCacheTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/ContentToArrayCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Cache\\\\ContentToArrayCacher\\:\\:\\$cacheTags type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/ContentToArrayCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/FilesIndexCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\FilesIndexCacher\\:\\:execute\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/FilesIndexCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\FilesIndexCacher\\:\\:execute\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/FilesIndexCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\FilesIndexCacher\\:\\:get\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/FilesIndexCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\FilesIndexCacher\\:\\:getCacheTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/FilesIndexCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\FilesIndexCacher\\:\\:getTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/FilesIndexCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\FilesIndexCacher\\:\\:setCacheKey\\(\\) has parameter \\$tokens with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/FilesIndexCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\FilesIndexCacher\\:\\:setCacheTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/FilesIndexCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Cache\\\\FilesIndexCacher\\:\\:\\$cacheTags type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/FilesIndexCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/GetFormatCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\GetFormatCacher\\:\\:execute\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/GetFormatCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\GetFormatCacher\\:\\:execute\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/GetFormatCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\GetFormatCacher\\:\\:getCacheTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/GetFormatCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\GetFormatCacher\\:\\:getTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/GetFormatCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\GetFormatCacher\\:\\:setCacheKey\\(\\) has parameter \\$tokens with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/GetFormatCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\GetFormatCacher\\:\\:setCacheTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/GetFormatCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Cache\\\\GetFormatCacher\\:\\:\\$cacheTags type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/GetFormatCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/RelatedOptionsUtilityCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\RelatedOptionsUtilityCacher\\:\\:execute\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/RelatedOptionsUtilityCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\RelatedOptionsUtilityCacher\\:\\:execute\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/RelatedOptionsUtilityCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\RelatedOptionsUtilityCacher\\:\\:fetchRelatedOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/RelatedOptionsUtilityCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\RelatedOptionsUtilityCacher\\:\\:getCacheTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/RelatedOptionsUtilityCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\RelatedOptionsUtilityCacher\\:\\:getTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/RelatedOptionsUtilityCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\RelatedOptionsUtilityCacher\\:\\:setCacheKey\\(\\) has parameter \\$tokens with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/RelatedOptionsUtilityCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\RelatedOptionsUtilityCacher\\:\\:setCacheTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/RelatedOptionsUtilityCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Cache\\\\RelatedOptionsUtilityCacher\\:\\:\\$cacheTags type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/RelatedOptionsUtilityCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/SelectOptionsCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\SelectOptionsCacher\\:\\:execute\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/SelectOptionsCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\SelectOptionsCacher\\:\\:execute\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/SelectOptionsCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\SelectOptionsCacher\\:\\:getCacheTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/SelectOptionsCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\SelectOptionsCacher\\:\\:getTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/SelectOptionsCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\SelectOptionsCacher\\:\\:selectOptionsHelper\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/SelectOptionsCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\SelectOptionsCacher\\:\\:selectOptionsHelper\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/SelectOptionsCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\SelectOptionsCacher\\:\\:setCacheKey\\(\\) has parameter \\$tokens with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/SelectOptionsCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Cache\\\\SelectOptionsCacher\\:\\:setCacheTags\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/SelectOptionsCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Cache\\\\SelectOptionsCacher\\:\\:\\$cacheTags type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Cache/SelectOptionsCacher.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'host\' on array\\{scheme\\?\\: string, host\\?\\: string, port\\?\\: int\\<0, 65535\\>, user\\?\\: string, pass\\?\\: string, path\\?\\: string, query\\?\\: string, fragment\\?\\: string\\}\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'scheme\' on array\\{scheme\\?\\: string, host\\?\\: string, port\\?\\: int\\<0, 65535\\>, user\\?\\: string, pass\\?\\: string, path\\?\\: string, query\\?\\: string, fragment\\?\\: string\\}\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 3,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Canonical\\:\\:generateLink\\(\\) has parameter \\$canonical with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Canonical\\:\\:generateLink\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Canonical\\:\\:get\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Canonical\\:\\:getHost\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Canonical\\:\\:getPath\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Canonical\\:\\:getRequest\\(\\) should return Symfony\\\\Component\\\\HttpFoundation\\\\Request but returns Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Canonical\\:\\:getScheme\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Canonical\\:\\:returnPath\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Canonical\\:\\:returnPath\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Canonical\\:\\:setPath\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$name of method Symfony\\\\Component\\\\Routing\\\\Generator\\\\UrlGeneratorInterface\\:\\:generate\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$route of method Bolt\\\\Canonical\\:\\:routeRequiresParam\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$parameters of method Symfony\\\\Component\\\\Routing\\\\Generator\\\\UrlGeneratorInterface\\:\\:generate\\(\\) expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\<string\\>\\|string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Canonical.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Collection\\\\DeepCollection extends generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Collection/DeepCollection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Collection\\\\DeepCollection\\:\\:deepMake\\(\\) has parameter \\$items with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Collection/DeepCollection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Collection\\\\DeepCollection\\:\\:isKeyEmpty\\(\\) has parameter \\$key with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Collection/DeepCollection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Collection\\\\DeepCollection\\:\\:isKeyNotEmpty\\(\\) has parameter \\$key with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Collection/DeepCollection.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$message of class Symfony\\\\Component\\\\Console\\\\Exception\\\\InvalidArgumentException constructor expects string, string\\|Stringable given\\.$#',
	'identifier' => 'argument.type',
	'count' => 4,
	'path' => __DIR__ . '/src/Command/AddUserCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$plainPassword of method Symfony\\\\Component\\\\PasswordHasher\\\\Hasher\\\\UserPasswordHasherInterface\\:\\:hashPassword\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/AddUserCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 1 on list\\<string\\>\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/src/Command/CopyThemesCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Command\\\\CopyThemesCommand\\:\\:getThemes\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/CopyThemesCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$message of class Symfony\\\\Component\\\\Console\\\\Exception\\\\InvalidArgumentException constructor expects string, string\\|Stringable given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/DeleteUserCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method install\\(\\) on class\\-string\\|object\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Command\\\\ExtensionsConfigureCommand\\:\\:copyExtensionConfig\\(\\) has parameter \\$packages with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Command\\\\ExtensionsConfigureCommand\\:\\:copyExtensionRoutesAndServices\\(\\) has parameter \\$packages with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Command\\\\ExtensionsConfigureCommand\\:\\:copyRoute\\(\\) has parameter \\$oldExtensionsRoutes with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Command\\\\ExtensionsConfigureCommand\\:\\:copyService\\(\\) has parameter \\$oldExtensionsServices with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Command\\\\ExtensionsConfigureCommand\\:\\:deleteExtensionRoutesAndServices\\(\\) has parameter \\$packages with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Command\\\\ExtensionsConfigureCommand\\:\\:runExtensionInstall\\(\\) has parameter \\$packages with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of function dirname expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_map expects array, array\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$oldExtensionsRoutes of method Bolt\\\\Command\\\\ExtensionsConfigureCommand\\:\\:copyRoute\\(\\) expects array, array\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$oldExtensionsServices of method Bolt\\\\Command\\\\ExtensionsConfigureCommand\\:\\:copyService\\(\\) expects array, array\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsConfigureCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on Composer\\\\Package\\\\CompletePackageInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ExtensionsShowCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property object\\:\\:\\$scripts\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/Command/InfoCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Command\\\\InfoCommand\\:\\:unicodeString\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/InfoCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$json of function json_decode expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/InfoCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function base64_encode expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/InfoCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$message of class Symfony\\\\Component\\\\Console\\\\Exception\\\\InvalidArgumentException constructor expects string, string\\|Stringable given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ResetPasswordCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$plainPassword of method Symfony\\\\Component\\\\PasswordHasher\\\\Hasher\\\\UserPasswordHasherInterface\\:\\:hashPassword\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ResetPasswordCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$content of static method Webimpress\\\\SafeWriter\\\\FileWriter\\:\\:writeFile\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ResetSecretCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\<float\\|int\\|string\\>\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/ResetSecretCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method find\\(\\) on Symfony\\\\Component\\\\Console\\\\Application\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 7,
	'path' => __DIR__ . '/src/Command/SetupCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Command\\\\SetupCommand\\:\\:\\$errors type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/SetupCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method find\\(\\) on Symfony\\\\Component\\\\Console\\\\Application\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/WelcomeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Command\\\\WelcomeCommand\\:\\:unicodeString\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/WelcomeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function base64_encode expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Command/WelcomeCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Illuminate\\\\Support\\\\Collection\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:get\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:get\\(\\) has parameter \\$default with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:get\\(\\) has parameter \\$default with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:getCache\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:getConfig\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:getConfigFilesTimestamps\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:getContentType\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:getEnvFilesTimestamps\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:getFileTypes\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:getMediaTypes\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:getPath\\(\\) has parameter \\$additional with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:getPaths\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:getTaxonomy\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Config\\:\\:parseConfig\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$cts contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$taxos contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$size of static method Bolt\\\\Configuration\\\\Config\\:\\:convertPHPSizeToBytes\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$generalConfig of class Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser constructor expects Illuminate\\\\Support\\\\Collection, Illuminate\\\\Support\\\\Collection\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Configuration\\\\Config\\:\\:\\$data with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Config.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Content\\\\ContentType\\:\\:__call\\(\\) has parameter \\$arguments with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/ContentType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Content\\\\ContentType\\:\\:factory\\(\\) has parameter \\$contentTypesConfig with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/ContentType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:has\\(\\) expects array\\<\\(int\\|string\\)\\>\\|int\\|string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/ContentType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Configuration\\\\Content\\\\FieldType extends generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/FieldType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Content\\\\FieldType\\:\\:__construct\\(\\) has parameter \\$items with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/FieldType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Content\\\\FieldType\\:\\:__construct\\(\\) has parameter \\$items with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/FieldType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Content\\\\FieldType\\:\\:__construct\\(\\) has parameter \\$items with no value type specified in iterable type array\\|Illuminate\\\\Support\\\\Collection\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/FieldType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Content\\\\FieldType\\:\\:defaults\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/FieldType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Content\\\\FieldType\\:\\:factory\\(\\) has parameter \\$parents with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/FieldType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Content\\\\FieldType\\:\\:mock\\(\\) has parameter \\$definition with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/FieldType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Content\\\\TaxonomyType\\:\\:__call\\(\\) has parameter \\$arguments with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/TaxonomyType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Content\\\\TaxonomyType\\:\\:factory\\(\\) has parameter \\$taxonomyTypesConfig with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/TaxonomyType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),mixed\\>\\:\\:has\\(\\) expects array\\<\\(int\\|string\\)\\>\\|int\\|string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Content/TaxonomyType.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Configuration\\\\FileLocations\\:\\:\\$locations type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/FileLocations.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\BaseParser\\:\\:getFilenameLocalOverrides\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/BaseParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\BaseParser\\:\\:getInitialFilename\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/BaseParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\BaseParser\\:\\:getParsedFilenames\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/BaseParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\BaseParser\\:\\:parse\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/BaseParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\BaseParser\\:\\:parseConfigYaml\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/BaseParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:__construct\\(\\) has parameter \\$generalConfig with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:determineOrder\\(\\) has parameter \\$contentType with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parse\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parseContentType\\(\\) has parameter \\$contentType with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parseField\\(\\) has parameter \\$acceptFileTypes with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parseField\\(\\) has parameter \\$currentGroup with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parseField\\(\\) has parameter \\$field with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parseField\\(\\) has parameter \\$field with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parseField\\(\\) has parameter \\$field with no value type specified in iterable type array\\|Illuminate\\\\Support\\\\Collection\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parseFieldRepeaters\\(\\) has parameter \\$acceptFileTypes with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parseFieldRepeaters\\(\\) has parameter \\$currentGroup with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parseFieldsAndGroups\\(\\) has parameter \\$fields with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ContentTypesParser\\:\\:parseFieldsAndGroups\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function implode expects array, iterable given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ContentTypesParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\GeneralParser\\:\\:getDefaultConfig\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/GeneralParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\GeneralParser\\:\\:parse\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/GeneralParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\MenuParser\\:\\:parse\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/MenuParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\MenuParser\\:\\:parseItems\\(\\) has parameter \\$items with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/MenuParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\MenuParser\\:\\:parseItems\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/MenuParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Configuration\\\\Parser\\\\MenuParser\\:\\:\\$itemBase type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/MenuParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\PermissionsParser\\:\\:getDefaultConfig\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/PermissionsParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\PermissionsParser\\:\\:parse\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/PermissionsParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\TaxonomyParser\\:\\:parse\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/TaxonomyParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\Parser\\\\ThemeParser\\:\\:parse\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/Parser/ThemeParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\PathResolver\\:\\:defaultPaths\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/PathResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\PathResolver\\:\\:names\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/PathResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\PathResolver\\:\\:rawAll\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/PathResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\PathResolver\\:\\:resolve\\(\\) has parameter \\$additional with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/PathResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Configuration\\\\PathResolver\\:\\:resolveAll\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/PathResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Configuration\\\\PathResolver\\:\\:\\$paths type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/PathResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Configuration\\\\PathResolver\\:\\:\\$resolving type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Configuration/PathResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between array\\|bool\\|float\\|int\\|string\\|null and \'/\'\\|\'\\\\\\\\\' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/Async/UploadController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\Async\\\\UploadController\\:\\:checkJavascriptInSVG\\(\\) has parameter \\$file with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/Async/UploadController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function mb_strtolower expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/Async/UploadController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$subject of function preg_match expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/Async/UploadController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\BulkOperationsController\\:\\:findRecordsFromIds\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/BulkOperationsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\BulkOperationsController\\:\\:findRecordsFromIds\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/BulkOperationsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\<mixed\\>\\|bool\\|float\\|int\\|string supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 4,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method addField\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getContentType\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDefaultLocale\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLocale\\(\\) on Bolt\\\\Entity\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ContentEditController\\:\\:getFieldToUpdate\\(\\) has parameter \\$fieldDefinition with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ContentEditController\\:\\:getPostedLocale\\(\\) has parameter \\$post with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ContentEditController\\:\\:renderEditor\\(\\) has parameter \\$errors with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ContentEditController\\:\\:updateCollections\\(\\) has parameter \\$formData with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ContentEditController\\:\\:updateField\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ContentEditController\\:\\:updateRelation\\(\\) has parameter \\$newRelations with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ContentEditController\\:\\:updateTaxonomy\\(\\) has parameter \\$postedTaxonomy with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$contentType of method Bolt\\\\Controller\\\\Backend\\\\ContentEditController\\:\\:new\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$status of method Bolt\\\\Entity\\\\Content\\:\\:setStatus\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$time of class Carbon\\\\Carbon constructor expects Carbon\\\\Month\\|Carbon\\\\WeekDay\\|DateTimeInterface\\|float\\|int\\|string\\|null, array\\<mixed\\>\\|float\\|int\\<min, \\-1\\>\\|int\\<1, max\\>\\|string\\|true given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/Backend/ContentEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on Composer\\\\Package\\\\CompletePackageInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ExtensionsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ExtensionsController\\:\\:viewExtension\\(\\) has parameter \\$name with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ExtensionsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'dirname\' might not exist on array\\{dirname\\?\\: string, basename\\: string, extension\\?\\: string, filename\\: string\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FileEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'extension\' might not exist on array\\{dirname\\?\\: string, basename\\: string, extension\\?\\: string, filename\\: string\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/Backend/FileEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$basePath of static method Bolt\\\\Utils\\\\PathCanonicalize\\:\\:canonicalize\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/Backend/FileEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$fullFileName of method Bolt\\\\Repository\\\\MediaRepository\\:\\:findOneByFullFilename\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FileEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of method Bolt\\\\Configuration\\\\Config\\:\\:getPath\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FileEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of static method Symfony\\\\Component\\\\Filesystem\\\\Path\\:\\:getExtension\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FileEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$filename of static method Bolt\\\\Utils\\\\PathCanonicalize\\:\\:canonicalize\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/src/Controller/Backend/FileEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$location of method Bolt\\\\Repository\\\\MediaRepository\\:\\:findOneByFullFilename\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FileEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getBasepath\\(\\) on Bolt\\\\Configuration\\\\FileLocation\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 5,
	'path' => __DIR__ . '/src/Controller/Backend/FilemanagerController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method isShowAll\\(\\) on Bolt\\\\Configuration\\\\FileLocation\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FilemanagerController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\FilemanagerController\\:\\:createPaginator\\(\\) return type with generic class Pagerfanta\\\\Pagerfanta does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FilemanagerController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$locationName of method Bolt\\\\Configuration\\\\FileLocations\\:\\:get\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/Backend/FilemanagerController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$subject of static method Bolt\\\\Common\\\\Str\\:\\:endsWith\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FilemanagerController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$subject of static method Bolt\\\\Common\\\\Str\\:\\:startsWith\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FilemanagerController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$path of method Bolt\\\\Controller\\\\Backend\\\\FilemanagerController\\:\\:findFiles\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FilemanagerController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$path of method Bolt\\\\Controller\\\\Backend\\\\FilemanagerController\\:\\:findFolders\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/FilemanagerController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getBasepath\\(\\) on Bolt\\\\Configuration\\\\FileLocation\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/MediaEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on Bolt\\\\Entity\\\\Media\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/MediaEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setTitle\\(\\) on Bolt\\\\Entity\\\\Media\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/MediaEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$copyright of method Bolt\\\\Entity\\\\Media\\:\\:setCopyright\\(\\) expects string\\|null, array\\<mixed\\>\\|bool\\|float\\|int\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/MediaEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$description of method Bolt\\\\Entity\\\\Media\\:\\:setDescription\\(\\) expects string\\|null, array\\<mixed\\>\\|bool\\|float\\|int\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/MediaEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$locationName of method Bolt\\\\Configuration\\\\FileLocations\\:\\:get\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/MediaEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$object of method Doctrine\\\\Persistence\\\\ObjectManager\\:\\:persist\\(\\) expects object, Bolt\\\\Entity\\\\Media\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/MediaEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$originalFilename of method Bolt\\\\Entity\\\\Media\\:\\:setOriginalFilename\\(\\) expects string\\|null, array\\<mixed\\>\\|bool\\|float\\|int\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/MediaEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$fileLocation of method Bolt\\\\Factory\\\\MediaFactory\\:\\:createOrUpdateMedia\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/MediaEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method object\\:\\:setPassword\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ResetPasswordController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ResetPasswordController\\:\\:buildResetEmail\\(\\) has parameter \\$config with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ResetPasswordController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ResetPasswordController\\:\\:buildResetEmail\\(\\) has parameter \\$resetToken with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ResetPasswordController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\ResetPasswordController\\:\\:buildResetEmail\\(\\) has parameter \\$user with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ResetPasswordController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$user of method Symfony\\\\Component\\\\PasswordHasher\\\\Hasher\\\\UserPasswordHasherInterface\\:\\:hashPassword\\(\\) expects Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\PasswordAuthenticatedUserInterface, object given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/ResetPasswordController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\UserEditController\\:\\:getPossibleRolesForForm\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/UserEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\UserEditController\\:\\:handleEdit\\(\\) has parameter \\$submitted_data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/UserEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Backend\\\\UserEditController\\:\\:handleValidFormSubmit\\(\\) has parameter \\$form with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/UserEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$submitted_data has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/UserEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Controller\\\\Backend\\\\UserEditController\\:\\:\\$assignableRoles type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Backend/UserEditController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function mb_strtolower expects string, array\\|bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ErrorController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Frontend\\\\DetailControllerInterface\\:\\:record\\(\\) has parameter \\$slugOrId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Frontend/DetailControllerInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Frontend/ListingController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Frontend\\\\ListingController\\:\\:listing\\(\\) should return Symfony\\\\Component\\\\HttpFoundation\\\\Response but returns Symfony\\\\Component\\\\HttpFoundation\\\\Response\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Frontend/ListingController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Frontend\\\\ListingController\\:\\:parseQueryParams\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Frontend/ListingController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Frontend\\\\ListingController\\:\\:setRecords\\(\\) has parameter \\$content with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Frontend/ListingController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\Frontend\\\\ListingController\\:\\:setRecords\\(\\) return type with generic class Pagerfanta\\\\Pagerfanta does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Frontend/ListingController.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$content contains generic class Pagerfanta\\\\Pagerfanta but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/Frontend/ListingController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\ImageController\\:\\:buildImage\\(\\) should return string but returns string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ImageController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\ImageController\\:\\:getPath\\(\\) has parameter \\$additional with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ImageController.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'extension\' might not exist on array\\{dirname\\?\\: string, basename\\: string, extension\\?\\: string, filename\\: string\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ImageController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of class Symfony\\\\Component\\\\HttpFoundation\\\\Response constructor expects string\\|null, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Controller/ImageController.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Controller\\\\ImageController\\:\\:\\$parameters type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/ImageController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setMaxPerPage\\(\\) on Bolt\\\\Entity\\\\Content\\|Pagerfanta\\\\Pagerfanta\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\TwigAwareController\\:\\:createPager\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\TwigAwareController\\:\\:createPagerParams\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\TwigAwareController\\:\\:getFromRequestArray\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\TwigAwareController\\:\\:render\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\TwigAwareController\\:\\:render\\(\\) has parameter \\$template with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\TwigAwareController\\:\\:renderSingle\\(\\) has parameter \\$templates with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\TwigAwareController\\:\\:renderSingle\\(\\) should return Symfony\\\\Component\\\\HttpFoundation\\\\Response but returns Symfony\\\\Component\\\\HttpFoundation\\\\Response\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\TwigAwareController\\:\\:renderTemplate\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Controller\\\\TwigAwareController\\:\\:renderTemplate\\(\\) has parameter \\$template with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$contentType of method Bolt\\\\Controller\\\\TwigAwareController\\:\\:validLocaleForContentType\\(\\) expects Bolt\\\\Configuration\\\\Content\\\\ContentType, Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Controller/TwigAwareController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getReferences\\(\\) on Doctrine\\\\Common\\\\DataFixtures\\\\ReferenceRepository\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/DataFixtures/BaseFixture.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\BaseFixture\\:\\:getImagesIndex\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/BaseFixture.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\BaseFixture\\:\\:getRandomReference\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/BaseFixture.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\BaseFixture\\:\\:getRandomTaxonomies\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/BaseFixture.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\DataFixtures\\\\BaseFixture\\:\\:\\$referencesIndex type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/BaseFixture.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\DataFixtures\\\\BaseFixture\\:\\:\\$taxonomyIndex type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/BaseFixture.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getBasepath\\(\\) on Bolt\\\\Configuration\\\\FileLocation\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:getFieldTypeValue\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:getFixtureFormatValues\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:getPreset\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:getPresetRecords\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:getValuesforFieldType\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:loadCollectionField\\(\\) has parameter \\$fieldType with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:loadCollectionField\\(\\) has parameter \\$preset with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:loadField\\(\\) has parameter \\$fieldType with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:loadField\\(\\) has parameter \\$preset with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:loadSetField\\(\\) has parameter \\$preset with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$taxonomy of method Bolt\\\\Entity\\\\Content\\:\\:addTaxonomy\\(\\) expects Bolt\\\\Entity\\\\Taxonomy, object given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:\\$imagesIndex with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\DataFixtures\\\\ContentFixtures\\:\\:\\$presetRecords type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 2,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 2,
	'path' => __DIR__ . '/src/DataFixtures/ContentFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getBasepath\\(\\) on Bolt\\\\Configuration\\\\FileLocation\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/DataFixtures/ImageFetchFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$stream of function fclose expects resource, resource\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ImageFetchFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$stream of function fwrite expects resource, resource\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ImageFetchFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\DataFixtures\\\\ImageFetchFixtures\\:\\:\\$curlOptions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ImageFetchFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getBasepath\\(\\) on Bolt\\\\Configuration\\\\FileLocation\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ImagesFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$description of method Bolt\\\\Entity\\\\Media\\:\\:setDescription\\(\\) expects string\\|null, array\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/ImagesFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\TaxonomyFixtures\\:\\:getDefaultOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/TaxonomyFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\DataFixtures\\\\UserFixtures\\:\\:getUserData\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/UserFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\DataFixtures\\\\UserFixtures\\:\\:\\$allUsers type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/DataFixtures/UserFixtures.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$type on Doctrine\\\\Common\\\\Lexer\\\\Token\\<int, string\\>\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/Functions/Rand.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Doctrine\\\\Functions\\\\Rand\\:\\:\\$expression \\(Doctrine\\\\ORM\\\\Query\\\\AST\\\\SimpleArithmeticExpression\\) does not accept Doctrine\\\\ORM\\\\Query\\\\AST\\\\ArithmeticTerm\\|Doctrine\\\\ORM\\\\Query\\\\AST\\\\SimpleArithmeticExpression\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/Functions/Rand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\JsonHelper\\:\\:wrapJsonFunction\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/JsonHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\JsonHelper\\:\\:wrapJsonFunction\\(\\) should return array\\|string but returns bool\\|string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/JsonHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\JsonHelper\\:\\:wrapJsonFunction\\(\\) should return array\\|string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/JsonHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\JsonHelper\\:\\:wrapJsonSearch\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/JsonHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\JsonHelper\\:\\:wrapJsonSearch\\(\\) has parameter \\$slug with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/JsonHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$value on Doctrine\\\\Common\\\\Lexer\\\\Token\\<int, string\\>\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/Query/Cast.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method dispatch\\(\\) on Doctrine\\\\ORM\\\\Query\\\\AST\\\\Node\\|string\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/src/Doctrine/Query/Cast.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\TablePrefix\\:\\:__construct\\(\\) has parameter \\$tablePrefix with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/TablePrefix.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\TablePrefix\\:\\:setTablePrefix\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/TablePrefix.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\TablePrefix\\:\\:setTablePrefixes\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/TablePrefix.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\TablePrefix\\:\\:setTablePrefixes\\(\\) has parameter \\$tablePrefixes with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/TablePrefix.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'joinTable\' might not exist on array\\{cache\\?\\: array\\<mixed\\>, cascade\\: array\\<string\\>, declared\\?\\: class\\-string, fetch\\: mixed, fieldName\\: string, id\\?\\: bool, inherited\\?\\: class\\-string, indexBy\\?\\: string, \\.\\.\\.\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/TablePrefix.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getWrappedConnection\\(\\) on class\\-string\\|object\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/Version.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\Version\\:\\:__construct\\(\\) has parameter \\$tablePrefix with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/Version.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Doctrine\\\\Version\\:\\:getPlatform\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Doctrine/Version.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 11,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getAnyTitle\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getContentTypeOverviewLink\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDeleteLink\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDuplicateLink\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEditLink\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getExcerpt\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getImage\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLink\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getListFormat\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getSpecialFeature\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStatusLink\\(\\) on Bolt\\\\Twig\\\\ContentExtension\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:__call\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:__call\\(\\) has parameter \\$arguments with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getExtras\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getFieldValues\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getFieldValuesFromDefinition\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getFields\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getLocales\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getRawFields\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getRelationsFromThisContent\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getRelationsToThisContent\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getSlug\\(\\) has parameter \\$locale with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getStatuses\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getTaxonomies\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:getTaxonomyValues\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:hasField\\(\\) has parameter \\$matchTypes with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:setDefinitionFromContentTypesConfig\\(\\) has parameter \\$contentTypesConfig with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:setFieldValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:standaloneFieldFilter\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:standaloneFieldsFilter\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Content\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Content\\:\\:\\$contentType type mapping mismatch\\: property can contain string\\|null but database expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Content\\:\\:\\$createdAt type mapping mismatch\\: property can contain DateTime\\|null but database expects DateTimeInterface\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Content\\:\\:\\$status type mapping mismatch\\: property can contain string\\|null but database expects string\\.$#',
	'identifier' => 'doctrine.columnType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:__call\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:__call\\(\\) has parameter \\$arguments with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:definitionAllowsEmpty\\(\\) has parameter \\$definition with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:get\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:get\\(\\) has parameter \\$key with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:getApiValue\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:getDefaultValue\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:getDefinition\\(\\) should return Bolt\\\\Configuration\\\\Content\\\\FieldType but returns Bolt\\\\Configuration\\\\Content\\\\FieldType\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:getSanitiser\\(\\) should return Bolt\\\\Utils\\\\Sanitiser but returns Bolt\\\\Utils\\\\Sanitiser\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:getTwig\\(\\) should return Twig\\\\Environment but returns Twig\\\\Environment\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:getTwigValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:set\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:setDefinition\\(\\) has parameter \\$definition with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:setValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\:\\:shouldBeRenderedAsTwig\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Field\\:\\:\\$content type mapping mismatch\\: property can contain Bolt\\\\Entity\\\\Content\\|null but database expects Bolt\\\\Entity\\\\Content\\.$#',
	'identifier' => 'doctrine.associationType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\CheckboxField\\:\\:setValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/CheckboxField.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Entity\\\\Field\\\\CollectionField implements generic interface Iterator but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/CollectionField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\CollectionField\\:\\:getTemplates\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/CollectionField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\CollectionField\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/CollectionField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\CollectionField\\:\\:shouldThisBeTranslatable\\(\\) has parameter \\$definition with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/CollectionField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\DataField\\:\\:setValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/DataField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\EmbedField\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/EmbedField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\FileField\\:\\:getFieldBase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/FileField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\FileField\\:\\:getHost\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/FileField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\FileField\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/FileField.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Entity\\\\Field\\\\FilelistField implements generic interface Iterator but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/FilelistField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\FilelistField\\:\\:getDefaultValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/FilelistField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\FilelistField\\:\\:getJsonValue\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/FilelistField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\FilelistField\\:\\:getRawValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/FilelistField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\FilelistField\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/FilelistField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\HiddenField\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/HiddenField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\HiddenField\\:\\:setValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/HiddenField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\ImageField\\:\\:getFieldBase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/ImageField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\ImageField\\:\\:getHost\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/ImageField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\ImageField\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/ImageField.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Entity\\\\Field\\\\ImagelistField implements generic interface Iterator but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/ImagelistField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\ImagelistField\\:\\:getDefaultValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/ImagelistField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\ImagelistField\\:\\:getJsonValue\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/ImagelistField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\ImagelistField\\:\\:getRawValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/ImagelistField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\ImagelistField\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/ImagelistField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\NumberField\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/NumberField.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Entity\\\\Field\\\\SelectField implements generic interface Iterator but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SelectField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SelectField\\:\\:getDefaultValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SelectField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SelectField\\:\\:getOptions\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SelectField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SelectField\\:\\:getSelected\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SelectField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SelectField\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SelectField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SelectField\\:\\:setValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SelectField.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Entity\\\\Field\\\\SetField implements generic interface Iterator but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SetField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SetField\\:\\:getFieldsFromDefinition\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SetField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SetField\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SetField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SetField\\:\\:getValueForEditor\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SetField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SetField\\:\\:setValue\\(\\) has parameter \\$fields with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SetField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SetField\\:\\:shouldThisBeTranslatable\\(\\) has parameter \\$definition with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SetField.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SlugField.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDefinition\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SlugField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\SlugField\\:\\:setValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/SlugField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Field\\\\TemplateselectField\\:\\:setValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Field/TemplateselectField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\FieldParentInterface\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/FieldParentInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\FieldTranslation\\:\\:get\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/FieldTranslation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\FieldTranslation\\:\\:get\\(\\) has parameter \\$key with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/FieldTranslation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\FieldTranslation\\:\\:getValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/FieldTranslation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\FieldTranslation\\:\\:set\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/FieldTranslation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\FieldTranslation\\:\\:setValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/FieldTranslation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\FieldTranslation\\:\\:\\$value has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/FieldTranslation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Log\\:\\:getContext\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Log\\:\\:getExtra\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Log\\:\\:getId\\(\\) should return int but returns int\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Log\\:\\:getLocation\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Log\\:\\:getUser\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Log\\:\\:setContext\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Log\\:\\:setExtra\\(\\) has parameter \\$extra with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Log\\:\\:setLocation\\(\\) has parameter \\$location with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Log\\:\\:setUser\\(\\) has parameter \\$user with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Log\\:\\:\\$context type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Log\\:\\:\\$extra type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Log\\:\\:\\$location type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Log\\:\\:\\$user type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Log.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getContentTypeSlug\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Entity/Relation.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDefinition\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Relation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Relation\\:\\:getDefinition\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Relation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Relation\\:\\:getDefinition\\(\\) return type has no value type specified in iterable type array\\|Bolt\\\\Configuration\\\\Content\\\\ContentType\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Relation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Relation\\:\\:\\$definition type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Relation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Relation\\:\\:\\$definition type has no value type specified in iterable type array\\|Bolt\\\\Configuration\\\\Content\\\\ContentType\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Relation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Relation\\:\\:\\$fromContent type mapping mismatch\\: property can contain Bolt\\\\Entity\\\\Content\\|null but database expects Bolt\\\\Entity\\\\Content\\.$#',
	'identifier' => 'doctrine.associationType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Relation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\Relation\\:\\:\\$toContent type mapping mismatch\\: property can contain Bolt\\\\Entity\\\\Content\\|null but database expects Bolt\\\\Entity\\\\Content\\.$#',
	'identifier' => 'doctrine.associationType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Relation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\ResetPasswordRequest\\:\\:\\$user type mapping mismatch\\: property can contain object but database expects Bolt\\\\Entity\\\\User\\.$#',
	'identifier' => 'doctrine.associationType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/ResetPasswordRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\Taxonomy\\:\\:setDefinitionFromTaxonomyTypesConfig\\(\\) has parameter \\$taxonomyTypesConfig with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/Taxonomy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\User\\:\\:__unserialize\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\User\\:\\:getId\\(\\) should return int but returns int\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\User\\:\\:getUserAuthTokens\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\User\\:\\:setRoles\\(\\) has parameter \\$roles with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Entity\\\\User\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of method Cocur\\\\Slugify\\\\Slugify\\:\\:slugify\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\User\\:\\:\\$email \\(string\\) does not accept string\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Entity\\\\User\\:\\:\\$roles type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Entity/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getContentTypeSlug\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDefinition\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on Bolt\\\\Entity\\\\Field\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Listener\\\\ContentFillListener\\:\\:getParents\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Listener\\\\ContentFillListener\\:\\:getSafeSlug\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Listener\\\\ContentFillListener\\:\\:intersectFieldsAndDefinition\\(\\) has parameter \\$fields with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Listener\\\\ContentFillListener\\:\\:intersectFieldsAndDefinition\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$field of method Bolt\\\\Event\\\\Listener\\\\ContentFillListener\\:\\:getParents\\(\\) expects Bolt\\\\Entity\\\\Field, Bolt\\\\Entity\\\\Field\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$fields of method Bolt\\\\Event\\\\Listener\\\\ContentFillListener\\:\\:intersectFieldsAndDefinition\\(\\) expects array, array\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$contentType of static method Bolt\\\\Configuration\\\\Content\\\\FieldType\\:\\:factory\\(\\) expects Bolt\\\\Configuration\\\\Content\\\\ContentType, Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/ContentFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method object\\:\\:getType\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/FieldDiscriminatorListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectOrClass of class ReflectionClass constructor expects class\\-string\\<T of object\\>\\|T of object, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/FieldDiscriminatorListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Event\\\\Listener\\\\FieldDiscriminatorListener\\:\\:\\$map type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/FieldDiscriminatorListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Event\\\\Listener\\\\FieldDiscriminatorListener\\:\\:\\$tempMap type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/FieldDiscriminatorListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Listener\\\\FieldFillListener\\:\\:clean\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/FieldFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Listener\\\\FieldFillListener\\:\\:clean\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/FieldFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Listener\\\\FieldFillListener\\:\\:trimZeroWidthWhitespace\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/FieldFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDefinition\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/RelationFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setDefinitionFromContentTypesConfig\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Listener/RelationFillListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$headers on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Subscriber/AuthSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getClientIp\\(\\) on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Subscriber/AuthSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getSession\\(\\) on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Event/Subscriber/AuthSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$userAgent of method UAParser\\\\Parser\\:\\:parse\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Subscriber/AuthSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Event\\\\Subscriber\\\\ExtensionSubscriber\\:\\:\\$objects type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Subscriber/ExtensionSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method object\\:\\:executeUpdate\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/src/Event/Subscriber/TimedPublishSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Subscriber\\\\TimedPublishSubscriber\\:\\:__construct\\(\\) has parameter \\$tablePrefix with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Subscriber/TimedPublishSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Subscriber\\\\TimedPublishSubscriber\\:\\:setTablePrefix\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Subscriber/TimedPublishSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Subscriber\\\\TimedPublishSubscriber\\:\\:setTablePrefixes\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Subscriber/TimedPublishSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\Subscriber\\\\TimedPublishSubscriber\\:\\:setTablePrefixes\\(\\) has parameter \\$tablePrefixes with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Subscriber/TimedPublishSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectOrClass of class ReflectionClass constructor expects class\\-string\\<T of object\\>\\|T of object, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/Subscriber/ZoneSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\UserEvent\\:\\:__construct\\(\\) has parameter \\$roleOptions with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/UserEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\UserEvent\\:\\:getRoleOptions\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/UserEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Event\\\\UserEvent\\:\\:setRoleOptions\\(\\) has parameter \\$roleOptions with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/UserEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Event\\\\UserEvent\\:\\:\\$rolesOptions with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Event/UserEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\BaseExtension\\:\\:addListener\\(\\) has parameter \\$callback with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\BaseExtension\\:\\:getAllServiceNames\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\BaseExtension\\:\\:getConfig\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\BaseExtension\\:\\:getConfigFilenames\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\BaseExtension\\:\\:getRequest\\(\\) should return Symfony\\\\Component\\\\HttpFoundation\\\\Request but returns Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\BaseExtension\\:\\:getService\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\BaseExtension\\:\\:getTemplateFolder\\(\\) should return string\\|null but returns string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\BaseExtension\\:\\:hasConfigFilenames\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\BaseExtension\\:\\:injectObjects\\(\\) has parameter \\$objects with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of function dirname expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of function realpath expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function mb_substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Extension\\\\BaseExtension\\:\\:\\$config with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/BaseExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionCompilerPass\\:\\:addComposerPackages\\(\\) has parameter \\$packages with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionCompilerPass.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionCompilerPass\\:\\:addComposerPackages\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionCompilerPass.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionCompilerPass\\:\\:buildServices\\(\\) has parameter \\$packages with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionCompilerPass.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionCompilerPass\\:\\:createService\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionCompilerPass.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectOrClass of class ReflectionClass constructor expects class\\-string\\<T of object\\>\\|T of object, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionCompilerPass.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of function dirname expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionCompilerPass.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Extension\\\\ExtensionCompilerPass\\:\\:\\$projectDir has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionCompilerPass.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionController\\:\\:getAllServiceNames\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionController\\:\\:getConfig\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionController\\:\\:getConfigFilenames\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionController\\:\\:getRequest\\(\\) should return Symfony\\\\Component\\\\HttpFoundation\\\\Request but returns Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionController\\:\\:getService\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionController\\:\\:hasConfigFilenames\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionController\\:\\:injectObjects\\(\\) has parameter \\$objects with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of function dirname expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function mb_substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionController.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Extension\\\\ExtensionController\\:\\:\\$config with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionInterface\\:\\:injectObjects\\(\\) has parameter \\$objects with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method initialize\\(\\) on class\\-string\\|object\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method initializeCli\\(\\) on class\\-string\\|object\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionRegistry\\:\\:addCompilerPass\\(\\) has parameter \\$extensionClasses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionRegistry\\:\\:getAllRoutes\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionRegistry\\:\\:getExtensionClasses\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionRegistry\\:\\:getExtensionNames\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\ExtensionRegistry\\:\\:initializeAll\\(\\) has parameter \\$objects with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Extension\\\\ExtensionRegistry\\:\\:\\$extensionClasses type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/ExtensionRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Extension\\\\RoutesLoader\\:\\:supports\\(\\) has parameter \\$type with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Extension/RoutesLoader.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Factory\\\\ContentFactory\\:\\:upsert\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Factory/ContentFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getBasepath\\(\\) on Bolt\\\\Configuration\\\\FileLocation\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Factory/MediaFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$height of method Bolt\\\\Entity\\\\Media\\:\\:setHeight\\(\\) expects int\\|null, bool\\|int given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Factory/MediaFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$width of method Bolt\\\\Entity\\\\Media\\:\\:setWidth\\(\\) expects int\\|null, bool\\|int given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Factory/MediaFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Factory\\\\MediaFactory\\:\\:\\$allowedFileTypes with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Factory/MediaFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Form\\\\ChangePasswordFormType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/ChangePasswordFormType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Form\\\\LoginType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/LoginType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Form\\\\ResetPasswordRequestFormType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/ResetPasswordRequestFormType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Form\\\\UserType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Form/UserType.php',
];
$ignoreErrors[] = [
	'message' => '#^Generator expects value type Symfony\\\\Component\\\\HttpKernel\\\\Bundle\\\\BundleInterface, object given\\.$#',
	'identifier' => 'generator.valueType',
	'count' => 1,
	'path' => __DIR__ . '/src/Kernel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Kernel\\:\\:flattenKeys\\(\\) has parameter \\$array with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Kernel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Kernel\\:\\:flattenKeys\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Kernel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$string of function explode expects string, array\\|bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Kernel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Log\\\\RequestProcessor\\:\\:processRecord\\(\\) has parameter \\$record with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Log/RequestProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Log\\\\RequestProcessor\\:\\:processRecord\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Log/RequestProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'class\' might not exist on array\\{function\\: string, line\\?\\: int, file\\?\\: string, class\\?\\: class\\-string, type\\?\\: \'\\-\\>\'\\|\'\\:\\:\', args\\?\\: list\\<mixed\\>, object\\?\\: object\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Log/RequestProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'file\' might not exist on array\\{function\\: string, line\\?\\: int, file\\?\\: string, class\\?\\: class\\-string, type\\?\\: \'\\-\\>\'\\|\'\\:\\:\', args\\?\\: list\\<mixed\\>, object\\?\\: object\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Log/RequestProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'line\' might not exist on array\\{function\\: string, line\\?\\: int, file\\?\\: string, class\\?\\: class\\-string, type\\?\\: \'\\-\\>\'\\|\'\\:\\:\', args\\?\\: list\\<mixed\\>, object\\?\\: object\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Log/RequestProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'type\' might not exist on array\\{function\\: string, line\\?\\: int, file\\?\\: string, class\\?\\: class\\-string, type\\?\\: \'\\-\\>\'\\|\'\\:\\:\', args\\?\\: list\\<mixed\\>, object\\?\\: object\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Log/RequestProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLocale\\(\\) on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/BackendMenu.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Menu\\\\BackendMenu\\:\\:buildAdminMenu\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/BackendMenu.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Menu\\\\BackendMenu\\:\\:\\$backendUrl \\(string\\) does not accept string\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/BackendMenu.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method addChild\\(\\) on Knp\\\\Menu\\\\ItemInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 17,
	'path' => __DIR__ . '/src/Menu/BackendMenuBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Menu\\\\BackendMenuBuilder\\:\\:buildAdminMenu\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/BackendMenuBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Menu\\\\BackendMenuBuilder\\:\\:getLatestRecords\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/BackendMenuBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$record has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/BackendMenuBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Menu\\\\BackendMenuBuilderInterface\\:\\:buildAdminMenu\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/BackendMenuBuilderInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLocale\\(\\) on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/FrontendMenu.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Menu\\\\FrontendMenu\\:\\:buildMenu\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/FrontendMenu.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Menu\\\\FrontendMenuBuilder\\:\\:buildMenu\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/FrontendMenuBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Menu\\\\FrontendMenuBuilder\\:\\:generateUri\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/FrontendMenuBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Menu\\\\FrontendMenuBuilder\\:\\:setUris\\(\\) has parameter \\$item with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/FrontendMenuBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Menu\\\\FrontendMenuBuilder\\:\\:setUris\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/FrontendMenuBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, iterable given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Menu/FrontendMenuBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_map expects array, iterable given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/FrontendMenuBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Menu\\\\FrontendMenuBuilderInterface\\:\\:buildMenu\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Menu/FrontendMenuBuilderInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot use array destructuring on array\\|string\\.$#',
	'identifier' => 'offsetAccess.nonArray',
	'count' => 3,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\ContentRepository\\:\\:createPaginator\\(\\) has parameter \\$query with generic class Doctrine\\\\ORM\\\\Query but does not specify its types\\: TKey, TResult$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\ContentRepository\\:\\:createPaginator\\(\\) return type with generic class Pagerfanta\\\\Pagerfanta does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\ContentRepository\\:\\:createSortBy\\(\\) has parameter \\$contentType with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\ContentRepository\\:\\:createSortBy\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\ContentRepository\\:\\:findAdjacentBy\\(\\) has parameter \\$currentValue with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\ContentRepository\\:\\:findForTaxonomy\\(\\) has parameter \\$taxonomy with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\ContentRepository\\:\\:findLatest\\(\\) has parameter \\$contentTypes with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\ContentRepository\\:\\:findLatestQb\\(\\) has parameter \\$contentTypes with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\ContentRepository\\:\\:findOneBySlug\\(\\) has parameter \\$slug with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\ContentRepository\\:\\:searchNaive\\(\\) has parameter \\$contentTypes with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function mb_trim expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/ContentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method object\\:\\:setDefaultLocale\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method object\\:\\:setDefinition\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method object\\:\\:setLabel\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method object\\:\\:setLocale\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method object\\:\\:setName\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getMetadataFactory\\(\\) on Doctrine\\\\ORM\\\\EntityManagerInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot use array destructuring on array\\|string\\.$#',
	'identifier' => 'offsetAccess.nonArray',
	'count' => 2,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\FieldRepository\\:\\:factory\\(\\) has parameter \\$definition with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\FieldRepository\\:\\:factory\\(\\) should return Bolt\\\\Entity\\\\Field but returns object\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\FieldRepository\\:\\:findAllByParent\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\FieldRepository\\:\\:findAllBySlug\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\FieldRepository\\:\\:getQueryBuilder\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/FieldRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\LogRepository\\:\\:createPaginator\\(\\) has parameter \\$query with generic class Doctrine\\\\ORM\\\\Query but does not specify its types\\: TKey, TResult$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/LogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\RelationRepository\\:\\:findRelations\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/RelationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Repository\\\\UserRepository\\:\\:findUsers\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Security\\\\AdminApiVoter extends generic class Symfony\\\\Component\\\\Security\\\\Core\\\\Authorization\\\\Voter\\\\Voter but does not specify its types\\: TAttribute, TSubject$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/AdminApiVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Security\\\\AdminTranslateVoter extends generic class Symfony\\\\Component\\\\Security\\\\Core\\\\Authorization\\\\Voter\\\\Voter but does not specify its types\\: TAttribute, TSubject$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/AdminTranslateVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Security\\\\ContentOwnerVoter extends generic class Symfony\\\\Component\\\\Security\\\\Core\\\\Authorization\\\\Voter\\\\Voter but does not specify its types\\: TAttribute, TSubject$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/ContentOwnerVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Illuminate\\\\Support\\\\Collection\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/ContentVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Security\\\\ContentVoter extends generic class Symfony\\\\Component\\\\Security\\\\Core\\\\Authorization\\\\Voter\\\\Voter but does not specify its types\\: TAttribute, TSubject$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/ContentVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Security\\\\ContentVoter\\:\\:isGrantedAny\\(\\) has parameter \\$attributes with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/ContentVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Security\\\\ContentVoter\\:\\:isGrantedAny\\(\\) has parameter \\$subject with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/ContentVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Security\\\\ContentVoter\\:\\:\\$contenttypeBasePermissions with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/ContentVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Security\\\\ContentVoter\\:\\:\\$contenttypeDefaultPermissions with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/ContentVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Security\\\\ContentVoter\\:\\:\\$contenttypePermissions with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/ContentVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Security\\\\GlobalVoter extends generic class Symfony\\\\Component\\\\Security\\\\Core\\\\Authorization\\\\Voter\\\\Voter but does not specify its types\\: TAttribute, TSubject$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/GlobalVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Security\\\\GlobalVoter\\:\\:\\$globalPermissions with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/GlobalVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Security\\\\GlobalVoter\\:\\:\\$supportedAttributes type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/GlobalVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$login_form has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/LoginFormAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$url of class Symfony\\\\Component\\\\HttpFoundation\\\\RedirectResponse constructor expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/LoginFormAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Class Bolt\\\\Security\\\\SwitchUserVoter extends generic class Symfony\\\\Component\\\\Security\\\\Core\\\\Authorization\\\\Voter\\\\Voter but does not specify its types\\: TAttribute, TSubject$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Security/SwitchUserVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\ContentQueryParser\\:\\:fetch\\(\\) return type with generic class Pagerfanta\\\\Pagerfanta does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\ContentQueryParser\\:\\:getContentTypes\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\ContentQueryParser\\:\\:getDirective\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\ContentQueryParser\\:\\:getOperations\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\ContentQueryParser\\:\\:getParameter\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\ContentQueryParser\\:\\:getParameters\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\ContentQueryParser\\:\\:runDirectives\\(\\) has parameter \\$skipDirective with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\ContentQueryParser\\:\\:setContentTypes\\(\\) has parameter \\$contentTypes with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\ContentQueryParser\\:\\:setParameter\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\ContentQueryParser\\:\\:setParameters\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$key of function array_key_exists expects int\\|string, bool\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$str of function strtok expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\ContentQueryParser\\:\\:\\$contentTypes type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\ContentQueryParser\\:\\:\\$directives type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\ContentQueryParser\\:\\:\\$operations type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\ContentQueryParser\\:\\:\\$params type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/ContentQueryParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\DirectiveHandler\\:\\:handle\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/DirectiveHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\EarliestDirectiveHandler\\:\\:__invoke\\(\\) has parameter \\$directives with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/EarliestDirectiveHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\EarliestDirectiveHandler\\:\\:__invoke\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/EarliestDirectiveHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\LatestDirectiveHandler\\:\\:__invoke\\(\\) has parameter \\$directives with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/LatestDirectiveHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\LatestDirectiveHandler\\:\\:__invoke\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/LatestDirectiveHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\OffsetDirective\\:\\:__invoke\\(\\) has parameter \\$otherDirectives with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/OffsetDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Illuminate\\\\Support\\\\Collection\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/OrderDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\OrderDirective\\:\\:createSortBy\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/OrderDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\OrderDirective\\:\\:getOrderBys\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/OrderDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\PageDirective\\:\\:__invoke\\(\\) has parameter \\$otherDirectives with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/PageDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\RandomDirectiveHandler\\:\\:__invoke\\(\\) has parameter \\$directives with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/RandomDirectiveHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Directive\\\\RandomDirectiveHandler\\:\\:__invoke\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Directive/RandomDirectiveHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\FieldQueryUtils\\:\\:isLocalizedField\\(\\) has parameter \\$fieldname with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/FieldQueryUtils.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Filter\\:\\:getKey\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Filter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Filter\\:\\:getParameters\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Filter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Filter\\:\\:setKey\\(\\) has parameter \\$key with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Filter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Filter\\:\\:setParameter\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Filter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Filter\\:\\:setParameters\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Filter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\Filter\\:\\:\\$key type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Filter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\Filter\\:\\:\\$parameters type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Filter.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\FrontendQueryScope\\:\\:\\$orderBys type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/FrontendQueryScope.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Handler\\\\SelectQueryHandler\\:\\:__invoke\\(\\) return type with generic class Pagerfanta\\\\Pagerfanta does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Handler/SelectQueryHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Handler\\\\SelectQueryHandler\\:\\:createPaginator\\(\\) has parameter \\$query with generic class Doctrine\\\\ORM\\\\Query but does not specify its types\\: TKey, TResult$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Handler/SelectQueryHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Handler\\\\SelectQueryHandler\\:\\:createPaginator\\(\\) return type with generic class Pagerfanta\\\\Pagerfanta does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Handler/SelectQueryHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Query\\:\\:getContent\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Query.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Query\\:\\:getContent\\(\\) return type with generic class Pagerfanta\\\\Pagerfanta does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Query.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Query\\:\\:getContentByScope\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Query.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Query\\:\\:getContentByScope\\(\\) return type with generic class Pagerfanta\\\\Pagerfanta does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Query.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Query\\:\\:getContentForTwig\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Query.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\Query\\:\\:getContentForTwig\\(\\) return type with generic class Pagerfanta\\\\Pagerfanta does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Query.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\Query\\:\\:\\$scopes type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/Query.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryInterface\\:\\:build\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryInterface\\:\\:getCoreFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryInterface\\:\\:getParameter\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryInterface\\:\\:getTaxonomyFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryInterface\\:\\:setParameter\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 1 on non\\-empty\\-list\\<string\\>\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:addValueMatcher\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:booleanValueHandler\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:defaultFilterHandler\\(\\) has parameter \\$value with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:getFilter\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:incorrectQueryHandler\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:incorrectQueryHandler\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:multipleKeyAndValueHandler\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:multipleValueHandler\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:numericValueHandler\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:parseValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_diff expects array, list\\<string\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_pad expects array, list\\<string\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function end expects array\\|object, list\\<string\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$key of method Bolt\\\\Storage\\\\Filter\\:\\:setKey\\(\\) expects array\\|string, list\\<string\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$keys of function array_combine expects array\\<int\\|string\\>, list\\<string\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, list\\<string\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$value of method Bolt\\\\Storage\\\\QueryParameterParser\\:\\:defaultFilterHandler\\(\\) expects array\\|bool\\|string, float\\|int\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\QueryParameterParser\\:\\:\\$valueMatchers type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryParameterParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\QueryScopeInterface\\:\\:onQueryExecute\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/QueryScopeInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'\\:\' and array\\|string results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'content\\.\' and array\\|string results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'field_\' and array\\|string results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method andWhere\\(\\) on Doctrine\\\\ORM\\\\QueryBuilder\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method expr\\(\\) on Doctrine\\\\ORM\\\\QueryBuilder\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 6,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEntityManager\\(\\) on Doctrine\\\\ORM\\\\QueryBuilder\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getOperator\\(\\) on Doctrine\\\\ORM\\\\Query\\\\Expr\\\\Andx\\|Doctrine\\\\ORM\\\\Query\\\\Expr\\\\Comparison\\|Doctrine\\\\ORM\\\\Query\\\\Expr\\\\Func\\|Doctrine\\\\ORM\\\\Query\\\\Expr\\\\Orx\\|string\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method join\\(\\) on Doctrine\\\\ORM\\\\QueryBuilder\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method leftJoin\\(\\) on Doctrine\\\\ORM\\\\QueryBuilder\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setParameter\\(\\) on Doctrine\\\\ORM\\\\QueryBuilder\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:build\\(\\) should return Doctrine\\\\ORM\\\\QueryBuilder but returns Doctrine\\\\ORM\\\\QueryBuilder\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:getCoreFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:getDateFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:getNumberFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:getParameter\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:getQueryBuilder\\(\\) should return Doctrine\\\\ORM\\\\QueryBuilder but returns Doctrine\\\\ORM\\\\QueryBuilder\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:getTaxonomyFieldExpression\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:getTaxonomyFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:getWhereParameters\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:setContentTypeFilter\\(\\) has parameter \\$contentTypes with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:setParameter\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Storage\\\\SelectQuery\\:\\:setParameters\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function current expects array\\|object, array\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$fieldname of method Bolt\\\\Storage\\\\FieldQueryUtils\\:\\:isFieldType\\(\\) expects string, array\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$slug of static method Bolt\\\\Doctrine\\\\JsonHelper\\:\\:wrapJsonFunction\\(\\) expects bool\\|string\\|null, array\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$timestamp of function date expects int\\|null, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, array\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 4,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type array\\|string\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 3,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$coreDateFields type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$coreFields type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$fieldJoins type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$params type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$referenceFields type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$referenceJoins type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$regularFields type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$replacements type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$singleFetchMode \\(bool\\) does not accept bool\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$taxonomyFields type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Storage\\\\SelectQuery\\:\\:\\$taxonomyJoins type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Storage/SelectQuery.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method has\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forHomepage\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forListing\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forLogin\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forMaintenance\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forRecord\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forResetPasswordCheckEmail\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forResetPasswordRequest\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forResetPasswordReset\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forSearch\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forTaxonomy\\(\\) has parameter \\$taxonomy with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\TemplateChooser\\:\\:forTaxonomy\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/TemplateChooser.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$query on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:getArray\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:getArray\\(\\) has parameter \\$array with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:getSortOrder\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:length\\(\\) has parameter \\$thing with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:order\\(\\) has parameter \\$array with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:order\\(\\) has parameter \\$locale with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:order\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:paginate\\(\\) has parameter \\$array with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:paginate\\(\\) return type with generic class Pagerfanta\\\\Pagerfanta does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:shuffle\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ArrayExtension\\:\\:shuffle\\(\\) has parameter \\$array with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$timestamp of static method Carbon\\\\Carbon\\:\\:createFromTimestamp\\(\\) expects float\\|int\\|string, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Twig/ArrayExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, array\\|bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/AssetsExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, array\\|bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/AssetsExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\BackendMenuExtension\\:\\:getAdminMenuArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/BackendMenuExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\CommonExtension\\:\\:getLocale\\(\\) has parameter \\$item with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/CommonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\CommonExtension\\:\\:isCurrent\\(\\) has parameter \\$item with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/CommonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, iterable given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/CommonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$query on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 6,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDefinition\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method has\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setMaxPerPage\\(\\) on Pagerfanta\\\\Pagerfanta\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:findOneImage\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:generateLink\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:generateLink\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:getImage\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:getTaxonomies\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:getTitleFieldsNames\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:pager\\(\\) has parameter \\$records with generic class Pagerfanta\\\\Pagerfanta but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:record\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:taxonomyOptions\\(\\) has parameter \\$taxonomy with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:taxonomyOptions\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:taxonomyValues\\(\\) has parameter \\$current with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection but does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:taxonomyValues\\(\\) has parameter \\$taxonomy with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ContentExtension\\:\\:taxonomyValues\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\<string\\>\\|string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ContentExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\DebugExtension\\:\\:backtrace\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/DebugExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ExtensionExtension\\:\\:getExtensions\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ExtensionExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getCurrentPageResults\\(\\) on Bolt\\\\Entity\\\\Content\\|Pagerfanta\\\\Pagerfanta\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:fieldFactory\\(\\) has parameter \\$definition with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:getDate\\(\\) has parameter \\$date with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:getDate\\(\\) has parameter \\$format with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:getDate\\(\\) has parameter \\$timezone with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:getListTemplates\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:getSelected\\(\\) has parameter \\$returnarray with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:getSelected\\(\\) has parameter \\$returnsingle with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:getSelected\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:selectOptions\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:selectOptionsArray\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:selectOptionsContentType\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:selectOptionsHelper\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FieldExtension\\:\\:selectOptionsHelper\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$definition contains generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FieldExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLocale\\(\\) on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FrontendMenuExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPathInfo\\(\\) on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FrontendMenuExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FrontendMenuExtension\\:\\:getMenu\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FrontendMenuExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\FrontendMenuExtension\\:\\:isCurrent\\(\\) has parameter \\$item with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/FrontendMenuExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\HtmlExtension\\:\\:canonical\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/HtmlExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\HtmlExtension\\:\\:placeholders\\(\\) has parameter \\$replacements with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/HtmlExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ImageExtension\\:\\:getAlt\\(\\) has parameter \\$image with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ImageExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ImageExtension\\:\\:getFilename\\(\\) has parameter \\$image with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ImageExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ImageExtension\\:\\:getMedia\\(\\) has parameter \\$image with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ImageExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ImageExtension\\:\\:getSvg\\(\\) has parameter \\$image with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ImageExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ImageExtension\\:\\:getSvg\\(\\) should return string\\|null but returns string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ImageExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ImageExtension\\:\\:popup\\(\\) has parameter \\$image with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ImageExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ImageExtension\\:\\:showImage\\(\\) has parameter \\$image with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ImageExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\ImageExtension\\:\\:thumbnail\\(\\) has parameter \\$image with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ImageExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of method Symfony\\\\Component\\\\Asset\\\\Packages\\:\\:getUrl\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Twig/ImageExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$callable of class Twig\\\\TwigFunction constructor expects array\\{class\\-string, string\\}\\|\\(callable\\(\\)\\: mixed\\)\\|null, array\\{\\$this\\(Bolt\\\\Twig\\\\ImageExtension\\), \'media\'\\} given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/ImageExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'extras\' on array\\<mixed\\>\\|ArrayObject\\<\\(int\\|string\\), mixed\\>\\|bool\\|float\\|int\\|string\\|null\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\JsonExtension\\:\\:contentToArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\JsonExtension\\:\\:contentToArray\\(\\) should return array but returns array\\|ArrayObject\\<\\(int\\|string\\), mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\JsonExtension\\:\\:jsonDecode\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\JsonExtension\\:\\:jsonDecode\\(\\) has parameter \\$assoc with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\JsonExtension\\:\\:jsonDecode\\(\\) has parameter \\$depth with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\JsonExtension\\:\\:jsonDecode\\(\\) has parameter \\$options with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\JsonExtension\\:\\:jsonRecords\\(\\) has parameter \\$records with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\JsonExtension\\:\\:normalizeRecords\\(\\) has parameter \\$records with no value type specified in iterable type Traversable\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\JsonExtension\\:\\:normalizeRecords\\(\\) has parameter \\$records with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\JsonExtension\\:\\:normalizeRecords\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/JsonExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\LocaleExtension\\:\\:flag\\(\\) has parameter \\$localeCode with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LocaleExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\LocaleExtension\\:\\:getLocale\\(\\) has parameter \\$localeCode with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LocaleExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\LocaleExtension\\:\\:getLocale\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LocaleExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\LocaleExtension\\:\\:getLocales\\(\\) has parameter \\$localeCodes with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LocaleExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\LocaleExtension\\:\\:getLocales\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LocaleExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\LocaleExtension\\:\\:localdate\\(\\) has parameter \\$dateTime with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LocaleExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\LocaleExtension\\:\\:translate\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/LocaleExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\Node\\\\SetcontentNode\\:\\:__construct\\(\\) has parameter \\$whereArguments with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/Node/SetcontentNode.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Illuminate\\\\Support\\\\Collection\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\RelatedExtension\\:\\:checkforContent\\(\\) has parameter \\$content with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\RelatedExtension\\:\\:getFirstRelatedContent\\(\\) has parameter \\$content with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\RelatedExtension\\:\\:getRelatedContent\\(\\) has parameter \\$bidirectional with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\RelatedExtension\\:\\:getRelatedContent\\(\\) has parameter \\$content with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\RelatedExtension\\:\\:getRelatedContent\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\RelatedExtension\\:\\:getRelatedContentByType\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\RelatedExtension\\:\\:getRelatedOptions\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\RelatedExtension\\:\\:getRelatedValues\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$contentType of method Bolt\\\\Utils\\\\ListFormatHelper\\:\\:getRelated\\(\\) expects Illuminate\\\\Support\\\\Collection, Illuminate\\\\Support\\\\Collection\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#5 \\$required of method Bolt\\\\Utils\\\\RelatedOptionsUtility\\:\\:fetchRelatedOptions\\(\\) expects bool, bool\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/RelatedExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\TextExtension\\:\\:pregReplace\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/TextExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\TextExtension\\:\\:urlDecode\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/TextExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\UserExtension\\:\\:getUser\\(\\) has parameter \\$displayname with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/UserExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\UserExtension\\:\\:getUser\\(\\) has parameter \\$email with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/UserExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\UserExtension\\:\\:getUser\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/UserExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\UserExtension\\:\\:getUser\\(\\) has parameter \\$username with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/UserExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\WidgetExtension\\:\\:listwidgets\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/WidgetExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\WidgetExtension\\:\\:renderWidgetByName\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/WidgetExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Twig\\\\WidgetExtension\\:\\:renderWidgetsForTarget\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Twig/WidgetExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Utils/ContentHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\ContentHelper\\:\\:getCanonicalRouteAndParams\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ContentHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\ContentHelper\\:\\:getFieldNames\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ContentHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\ContentHelper\\:\\:getLink\\(\\) has parameter \\$record with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ContentHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\ContentHelper\\:\\:guessTitleFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ContentHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\ContentHelper\\:\\:setCanonicalPath\\(\\) has parameter \\$record with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ContentHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'singular_name\' might not exist on Bolt\\\\Configuration\\\\Content\\\\ContentType\\|null\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ContentHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$str of static method Bolt\\\\Common\\\\Str\\:\\:decode\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ContentHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\Excerpt\\:\\:determineSnipLocation\\(\\) has parameter \\$locations with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Excerpt.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\Excerpt\\:\\:extractLocations\\(\\) has parameter \\$words with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Excerpt.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\Excerpt\\:\\:extractLocations\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Excerpt.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\Excerpt\\:\\:extractRelevant\\(\\) has parameter \\$words with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Excerpt.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\Excerpt\\:\\:extractRelevant\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Excerpt.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\Excerpt\\:\\:getExcerpt\\(\\) has parameter \\$focus with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Excerpt.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function mb_substr expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Excerpt.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|string supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 3,
	'path' => __DIR__ . '/src/Utils/FakeContent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\FakeContent\\:\\:generateHTML\\(\\) has parameter \\$loops with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/FakeContent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\FakeContent\\:\\:generateMarkdown\\(\\) has parameter \\$loops with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/FakeContent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\FilesIndex\\:\\:get\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/FilesIndex.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\Html\\:\\:decorateTT\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Html.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\Html\\:\\:trimText\\(\\) should return string but returns string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Html.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function mb_strlen expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Html.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function mb_substr expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/src/Utils/Html.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'COUNT\\(id\\)\' on array\\<string, mixed\\>\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ListFormatHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setListFormat\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ListFormatHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\ListFormatHelper\\:\\:getMenuLinks\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ListFormatHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\ListFormatHelper\\:\\:getRelated\\(\\) has parameter \\$contentType with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ListFormatHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\ListFormatHelper\\:\\:getRelated\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ListFormatHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\ListFormatHelper\\:\\:getSelect\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ListFormatHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\ListFormatHelper\\:\\:getSelect\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ListFormatHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$object of method Doctrine\\\\Persistence\\\\ObjectManager\\:\\:persist\\(\\) expects object, Bolt\\\\Entity\\\\Content\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/ListFormatHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on list\\<string\\>\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:getCodetoCountry\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:getContentLocales\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:getCurrentLocale\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:getFlagCodes\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:getLink\\(\\) has parameter \\$locale with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:getLink\\(\\) has parameter \\$routeParams with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:getLocales\\(\\) has parameter \\$localeCodes with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:getLocales\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:isCurrentLocale\\(\\) has parameter \\$localeCodes with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:localeInfo\\(\\) has parameter \\$localeCode with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\LocaleHelper\\:\\:localeInfo\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Utils\\\\LocaleHelper\\:\\:\\$codeToCountry with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Utils\\\\LocaleHelper\\:\\:\\$currentLocale with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Utils\\\\LocaleHelper\\:\\:\\$flagCodes with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Utils\\\\LocaleHelper\\:\\:\\$localeCodes with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/LocaleHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Utils\\\\MomentFormatConverter\\:\\:\\$formatConvertRules type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/MomentFormatConverter.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'file\' might not exist on array\\{function\\: string, line\\?\\: int, file\\?\\: string, type\\?\\: \'\\-\\>\'\\|\'\\:\\:\', args\\?\\: list\\<mixed\\>, object\\?\\: object\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Recursion.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'type\' might not exist on array\\{function\\: string, line\\?\\: int, file\\?\\: string, class\\: class\\-string, type\\?\\: \'\\-\\>\'\\|\'\\:\\:\', args\\?\\: list\\<mixed\\>, object\\?\\: object\\}\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Recursion.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setMaxPerPage\\(\\) on Bolt\\\\Entity\\\\Content\\|Pagerfanta\\\\Pagerfanta\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/RelatedOptionsUtility.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\RelatedOptionsUtility\\:\\:fetchRelatedOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/RelatedOptionsUtility.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method addAttribute\\(\\) on HTMLPurifier_HTMLDefinition\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 7,
	'path' => __DIR__ . '/src/Utils/Sanitiser.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method addElement\\(\\) on HTMLPurifier_HTMLDefinition\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Utils/Sanitiser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\Sanitiser\\:\\:createNonSupportedElements\\(\\) has parameter \\$allowedTags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Sanitiser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$definition of method Bolt\\\\Utils\\\\Sanitiser\\:\\:createNonSupportedElements\\(\\) expects HTMLPurifier_HTMLDefinition, HTMLPurifier_HTMLDefinition\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/Sanitiser.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Bolt\\\\Configuration\\\\Config\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/src/Utils/ThumbnailHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on Bolt\\\\Entity\\\\Field\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 7,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\TranslationsManager\\:\\:__construct\\(\\) has parameter \\$collections with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection but does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\TranslationsManager\\:\\:__construct\\(\\) has parameter \\$keys with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\TranslationsManager\\:\\:applyTranslations\\(\\) has parameter \\$orderId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\TranslationsManager\\:\\:getFieldChildrenTranslations\\(\\) has parameter \\$translations with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\TranslationsManager\\:\\:getTranslations\\(\\) has parameter \\$orderId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\TranslationsManager\\:\\:getTranslations\\(\\) return type with generic interface Doctrine\\\\Common\\\\Collections\\\\Collection does not specify its types\\: TKey, T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\TranslationsManager\\:\\:hasTranslations\\(\\) has parameter \\$orderId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Utils\\\\TranslationsManager\\:\\:populateTranslations\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Utils\\\\TranslationsManager\\:\\:\\$translations type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Utils/TranslationsManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Validator\\\\ContentTypeConstraintLoader\\:\\:parseNodes\\(\\) has parameter \\$nodes with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Validator/ContentTypeConstraintLoader.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Validator\\\\ContentTypeConstraintLoader\\:\\:parseNodes\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Validator/ContentTypeConstraintLoader.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getContentType\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Validator/ContentValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on Bolt\\\\Entity\\\\Content\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/src/Validator/ContentValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Validator\\\\ContentValidator\\:\\:getFieldConstraints\\(\\) has parameter \\$contentType with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Validator/ContentValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Validator\\\\ContentValidator\\:\\:getFieldConstraints\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Validator/ContentValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Validator\\\\ContentValidator\\:\\:relationsToMap\\(\\) return type has no value type specified in iterable type list\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Validator/ContentValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Validator\\\\ContentValidatorInterface\\:\\:validate\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Validator/ContentValidatorInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\BaseWidget\\:\\:__invoke\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/BaseWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\BaseWidget\\:\\:getExtension\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/BaseWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\BaseWidget\\:\\:getTemplateFolder\\(\\) should return string\\|null but returns string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 3,
	'path' => __DIR__ . '/src/Widget/BaseWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\BaseWidget\\:\\:run\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/BaseWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of function dirname expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/src/Widget/BaseWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\BoltHeaderWidget\\:\\:__invoke\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/BoltHeaderWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\CanonicalLinkWidget\\:\\:run\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/CanonicalLinkWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\FlocOptOutHeader\\:\\:__invoke\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/FlocOptOutHeader.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\Injector\\\\HtmlInjector\\:\\:getMap\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/Injector/HtmlInjector.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\Injector\\\\HtmlInjector\\:\\:injectSnippet\\(\\) has parameter \\$functionMap with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/Injector/HtmlInjector.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\Injector\\\\QueueProcessor\\:\\:pregCallback\\(\\) has parameter \\$c with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/Injector/QueueProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\Injector\\\\QueueProcessor\\:\\:process\\(\\) has parameter \\$queue with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/Injector/QueueProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\<float\\|int\\|string\\>\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/Injector/QueueProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function preg_replace_callback expects array\\<float\\|int\\|string\\>\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/Injector/QueueProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Widget\\\\Injector\\\\QueueProcessor\\:\\:\\$matchedComments type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/Injector/QueueProcessor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\Injector\\\\Target\\:\\:listAll\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/Injector/Target.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\MaintenanceModeWidget\\:\\:run\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/MaintenanceModeWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\RequestAwareInterface\\:\\:setRequest\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/RequestAwareInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\ResponseAwareInterface\\:\\:setResponse\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/ResponseAwareInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\SnippetWidget\\:\\:__construct\\(\\) has parameter \\$targets with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/SnippetWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\SnippetWidget\\:\\:run\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/SnippetWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Widget\\\\SnippetWidget\\:\\:\\$type has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/SnippetWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\StopwatchAwareInterface\\:\\:startStopwatch\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/StopwatchAwareInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\StopwatchAwareInterface\\:\\:stopStopwatch\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/StopwatchAwareInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\TwigAwareInterface\\:\\:setTwig\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/TwigAwareInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widget\\\\WidgetInterface\\:\\:__invoke\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widget/WidgetInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widgets\\:\\:filteredWidgets\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Widgets.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widgets\\:\\:invokeWidget\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widgets.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widgets\\:\\:listWidgetsForTarget\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Widgets.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widgets\\:\\:renderWidgetByName\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widgets.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Bolt\\\\Widgets\\:\\:renderWidgetsForTarget\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widgets.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$request of method Bolt\\\\Widget\\\\RequestAwareInterface\\:\\:setRequest\\(\\) expects Symfony\\\\Component\\\\HttpFoundation\\\\Request, Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Widgets.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$request of method Bolt\\\\Widget\\\\Injector\\\\QueueProcessor\\:\\:process\\(\\) expects Symfony\\\\Component\\\\HttpFoundation\\\\Request, Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Widgets.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Widgets\\:\\:\\$queue with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/src/Widgets.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Bolt\\\\Widgets\\:\\:\\$rendered type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/src/Widgets.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
