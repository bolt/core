<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\GraphQL\Language;

use GraphQL\Error\SyntaxError;
use GraphQL\Language\Lexer as BaseLexer;
use GraphQL\Language\Token;
use GraphQL\Utils\BlockString;
use GraphQL\Utils\Utils;

class Lexer extends BaseLexer
{
    /**
     * @var int
     */
    private $position;

    /**
     * @var int
     */
    private $byteStreamPosition = 0;

    public function lookahead()
    {
        $token = $this->token;
        if ($token->kind !== Token::EOF) {
            do {
                $token = $token->next ?: ($token->next = $this->readToken($token));
            } while ($token->kind === Token::COMMENT);
        }

        return $token;
    }

    /**
     * @throws SyntaxError
     */
    private function readToken(Token $prev): Token
    {
        $bodyLength = $this->source->length;

        $this->positionAfterWhitespace();
        $position = $this->position;

        $line = $this->line;
        $col = 1 + $position - $this->lineStart;

        if ($position >= $bodyLength) {
            return new Token(Token::EOF, $bodyLength, $bodyLength, $line, $col, $prev);
        }

        // Read next char and advance string cursor:
        [, $code, $bytes] = $this->readChar(true);

        switch ($code) {
            case 33: // !
                return new Token(Token::BANG, $position, $position + 1, $line, $col, $prev);
            case 35: // #
                $this->moveStringCursor(-1, -1 * $bytes);

                return $this->readComment($line, $col, $prev);
            case 36: // $
                return new Token(Token::DOLLAR, $position, $position + 1, $line, $col, $prev);
            case 38: // &
                return new Token(Token::AMP, $position, $position + 1, $line, $col, $prev);
            case 40: // (
                return new Token(Token::PAREN_L, $position, $position + 1, $line, $col, $prev);
            case 41: // )
                return new Token(Token::PAREN_R, $position, $position + 1, $line, $col, $prev);
            case 46: // .
                [, $charCode1] = $this->readChar(true);
                [, $charCode2] = $this->readChar(true);

                if ($charCode1 === 46 && $charCode2 === 46) {
                    return new Token(Token::SPREAD, $position, $position + 3, $line, $col, $prev);
                }
                break;
            case 58: // :
                return new Token(Token::COLON, $position, $position + 1, $line, $col, $prev);
            case 61: // =
                return new Token(Token::EQUALS, $position, $position + 1, $line, $col, $prev);
            case 64: // @
                return new Token(Token::AT, $position, $position + 1, $line, $col, $prev);
            case 91: // [
                return new Token(Token::BRACKET_L, $position, $position + 1, $line, $col, $prev);
            case 93: // ]
                return new Token(Token::BRACKET_R, $position, $position + 1, $line, $col, $prev);
            case 123: // {
                return new Token(Token::BRACE_L, $position, $position + 1, $line, $col, $prev);
            case 124: // |
                return new Token(Token::PIPE, $position, $position + 1, $line, $col, $prev);
            case 125: // }
                return new Token(Token::BRACE_R, $position, $position + 1, $line, $col, $prev);
            // A-Z
            case 42: // *
            case 65:
            case 66:
            case 67:
            case 68:
            case 69:
            case 70:
            case 71:
            case 72:
            case 73:
            case 74:
            case 75:
            case 76:
            case 77:
            case 78:
            case 79:
            case 80:
            case 81:
            case 82:
            case 83:
            case 84:
            case 85:
            case 86:
            case 87:
            case 88:
            case 89:
            case 90:
                // _
            case 95:
                // a-z
            case 97:
            case 98:
            case 99:
            case 100:
            case 101:
            case 102:
            case 103:
            case 104:
            case 105:
            case 106:
            case 107:
            case 108:
            case 109:
            case 110:
            case 111:
            case 112:
            case 113:
            case 114:
            case 115:
            case 116:
            case 117:
            case 118:
            case 119:
            case 120:
            case 121:
            case 122:
            case 126: // ~
                return $this->moveStringCursor(-1, -1 * $bytes)
                    ->readName($line, $col, $prev);
            // -
            case 45:
                // 0-9
            case 48:
            case 49:
            case 50:
            case 51:
            case 52:
            case 53:
            case 54:
            case 55:
            case 56:
            case 57:
                return $this->moveStringCursor(-1, -1 * $bytes)
                    ->readNumber($line, $col, $prev);
            // "
            case 34:
                [, $nextCode] = $this->readChar();
                [, $nextNextCode] = $this->moveStringCursor(1, 1)->readChar();

                if ($nextCode === 34 && $nextNextCode === 34) {
                    return $this->moveStringCursor(-2, (-1 * $bytes) - 1)
                        ->readBlockString($line, $col, $prev);
                }

                return $this->moveStringCursor(-2, (-1 * $bytes) - 1)
                    ->readString($line, $col, $prev);
        }

        throw new SyntaxError(
            $this->source,
            $position,
            $this->unexpectedCharacterMessage($code)
        );
    }

    private function positionAfterWhitespace(): void
    {
        while ($this->position < $this->source->length) {
            [, $code, $bytes] = $this->readChar();

            // Skip whitespace
            // tab | space | comma | BOM
            if ($code === 9 || $code === 32 || $code === 44 || $code === 0xFEFF) {
                $this->moveStringCursor(1, $bytes);
            } elseif ($code === 10) { // new line
                $this->moveStringCursor(1, $bytes);
                $this->line++;
                $this->lineStart = $this->position;
            } elseif ($code === 13) { // carriage return
                [, $nextCode, $nextBytes] = $this->moveStringCursor(1, $bytes)->readChar();

                if ($nextCode === 10) { // lf after cr
                    $this->moveStringCursor(1, $nextBytes);
                }
                $this->line++;
                $this->lineStart = $this->position;
            } else {
                break;
            }
        }
    }

    private function readChar($advance = false, $byteStreamPosition = null)
    {
        if ($byteStreamPosition === null) {
            $byteStreamPosition = $this->byteStreamPosition;
        }

        $code = null;
        $utf8char = '';
        $bytes = 0;
        $positionOffset = 0;

        if (isset($this->source->body[$byteStreamPosition])) {
            $ord = ord($this->source->body[$byteStreamPosition]);

            if ($ord < 128) {
                $bytes = 1;
            } elseif ($ord < 224) {
                $bytes = 2;
            } elseif ($ord < 240) {
                $bytes = 3;
            } else {
                $bytes = 4;
            }

            $utf8char = '';
            for ($pos = $byteStreamPosition; $pos < $byteStreamPosition + $bytes; $pos++) {
                $utf8char .= $this->source->body[$pos];
            }
            $positionOffset = 1;
            $code = $bytes === 1 ? $ord : Utils::ord($utf8char);
        }

        if ($advance) {
            $this->moveStringCursor($positionOffset, $bytes);
        }

        return [$utf8char, $code, $bytes];
    }

    private function moveStringCursor($positionOffset, $byteStreamOffset)
    {
        $this->position += $positionOffset;
        $this->byteStreamPosition += $byteStreamOffset;

        return $this;
    }

    private function readComment($line, $col, Token $prev)
    {
        $start = $this->position;
        $value = '';
        $bytes = 1;

        do {
            [$char, $code, $bytes] = $this->moveStringCursor(1, $bytes)->readChar();
            $value .= $char;
        } while ($code &&
        // SourceCharacter but not LineTerminator
        ($code > 0x001F || $code === 0x0009)
        );

        return new Token(
            Token::COMMENT,
            $start,
            $this->position,
            $line,
            $col,
            $prev,
            $value
        );
    }

    private function readName($line, $col, Token $prev)
    {
        $value = '';
        $start = $this->position;
        [$char, $code] = $this->readChar();

        while ($code && (
                $code === 95 || // _
                $code >= 48 && $code <= 57 || // 0-9
                $code >= 65 && $code <= 90 || // A-Z
                $code >= 97 && $code <= 122 || // a-z
                $code === 126 || // ~
                $code === 42 // *
            )) {
            $value .= $char;
            [$char, $code] = $this->moveStringCursor(1, 1)->readChar();
        }

        return new Token(
            Token::NAME,
            $start,
            $this->position,
            $line,
            $col,
            $prev,
            $value
        );
    }

    /**
     * Reads a number token from the source file, either a float
     * or an int depending on whether a decimal point appears.
     *
     * Int:   -?(0|[1-9][0-9]*)
     * Float: -?(0|[1-9][0-9]*)(\.[0-9]+)?((E|e)(+|-)?[0-9]+)?
     *
     * @param int $line
     * @param int $col
     *
     * @throws SyntaxError
     */
    private function readNumber($line, $col, Token $prev): Token
    {
        $value = '';
        $start = $this->position;
        [$char, $code] = $this->readChar();

        $isFloat = false;

        if ($code === 45) { // -
            $value .= $char;
            [$char, $code] = $this->moveStringCursor(1, 1)->readChar();
        }

        // guard against leading zero's
        if ($code === 48) { // 0
            $value .= $char;
            [$char, $code] = $this->moveStringCursor(1, 1)->readChar();

            if ($code >= 48 && $code <= 57) {
                throw new SyntaxError(
                    $this->source,
                    $this->position,
                    'Invalid number, unexpected digit after 0: ' . Utils::printCharCode($code)
                );
            }
        } else {
            $value .= $this->readDigits();
            [$char, $code] = $this->readChar();
        }

        if ($code === 46) { // .
            $isFloat = true;
            $this->moveStringCursor(1, 1);

            $value .= $char;
            $value .= $this->readDigits();
            [$char, $code] = $this->readChar();
        }

        if ($code === 69 || $code === 101) { // E e
            $isFloat = true;
            $value .= $char;
            [$char, $code] = $this->moveStringCursor(1, 1)->readChar();

            if ($code === 43 || $code === 45) { // + -
                $value .= $char;
                $this->moveStringCursor(1, 1);
            }
            $value .= $this->readDigits();
        }

        return new Token(
            $isFloat ? Token::FLOAT : Token::INT,
            $start,
            $this->position,
            $line,
            $col,
            $prev,
            $value
        );
    }

    private function readDigits()
    {
        [$char, $code] = $this->readChar();

        if ($code >= 48 && $code <= 57) { // 0 - 9
            $value = '';

            do {
                $value .= $char;
                [$char, $code] = $this->moveStringCursor(1, 1)->readChar();
            } while ($code >= 48 && $code <= 57); // 0 - 9

            return $value;
        }

        if ($this->position > $this->source->length - 1) {
            $code = null;
        }

        throw new SyntaxError(
            $this->source,
            $this->position,
            'Invalid number, expected digit but got: ' . Utils::printCharCode($code)
        );
    }

    private function readString($line, $col, Token $prev)
    {
        $start = $this->position;

        // Skip leading quote and read first string char:
        [$char, $code, $bytes] = $this->moveStringCursor(1, 1)->readChar();

        $chunk = '';
        $value = '';

        while ($code !== null &&
            // not LineTerminator
            $code !== 10 && $code !== 13
        ) {
            // Closing Quote (")
            if ($code === 34) {
                $value .= $chunk;

                // Skip quote
                $this->moveStringCursor(1, 1);

                return new Token(
                    Token::STRING,
                    $start,
                    $this->position,
                    $line,
                    $col,
                    $prev,
                    $value
                );
            }

            $this->assertValidStringCharacterCode($code, $this->position);
            $this->moveStringCursor(1, $bytes);

            if ($code === 92) { // \
                $value .= $chunk;
                [, $code] = $this->readChar(true);

                switch ($code) {
                    case 34:
                        $value .= '"';
                        break;
                    case 47:
                        $value .= '/';
                        break;
                    case 92:
                        $value .= '\\';
                        break;
                    case 98:
                        $value .= chr(8);
                        break; // \b (backspace)
                    case 102:
                        $value .= "\f";
                        break;
                    case 110:
                        $value .= "\n";
                        break;
                    case 114:
                        $value .= "\r";
                        break;
                    case 116:
                        $value .= "\t";
                        break;
                    case 117:
                        $position = $this->position;
                        [$hex] = $this->readChars(4, true);
                        if (! preg_match('/[0-9a-fA-F]{4}/', $hex)) {
                            throw new SyntaxError(
                                $this->source,
                                $position - 1,
                                'Invalid character escape sequence: \\u' . $hex
                            );
                        }
                        $code = hexdec($hex);
                        $this->assertValidStringCharacterCode($code, $position - 2);
                        $value .= Utils::chr($code);
                        break;
                    default:
                        throw new SyntaxError(
                            $this->source,
                            $this->position - 1,
                            'Invalid character escape sequence: \\' . Utils::chr($code)
                        );
                }
                $chunk = '';
            } else {
                $chunk .= $char;
            }

            [$char, $code, $bytes] = $this->readChar();
        }

        throw new SyntaxError(
            $this->source,
            $this->position,
            'Unterminated string.'
        );
    }

    /**
     * Reads a block string token from the source file.
     *
     * """("?"?(\\"""|\\(?!=""")|[^"\\]))*"""
     */
    private function readBlockString($line, $col, Token $prev)
    {
        $start = $this->position;

        // Skip leading quotes and read first string char:
        [$char, $code, $bytes] = $this->moveStringCursor(3, 3)->readChar();

        $chunk = '';
        $value = '';

        while ($code !== null) {
            // Closing Triple-Quote (""")
            if ($code === 34) {
                // Move 2 quotes
                [, $nextCode] = $this->moveStringCursor(1, 1)->readChar();
                [, $nextNextCode] = $this->moveStringCursor(1, 1)->readChar();

                if ($nextCode === 34 && $nextNextCode === 34) {
                    $value .= $chunk;

                    $this->moveStringCursor(1, 1);

                    return new Token(
                        Token::BLOCK_STRING,
                        $start,
                        $this->position,
                        $line,
                        $col,
                        $prev,
                        BlockString::value($value)
                    );
                }
                // move cursor back to before the first quote
                $this->moveStringCursor(-2, -2);
            }

            $this->assertValidBlockStringCharacterCode($code, $this->position);
            $this->moveStringCursor(1, $bytes);

            [, $nextCode] = $this->readChar();
            [, $nextNextCode] = $this->moveStringCursor(1, 1)->readChar();
            [, $nextNextNextCode] = $this->moveStringCursor(1, 1)->readChar();

            // Escape Triple-Quote (\""")
            if ($code === 92 &&
                $nextCode === 34 &&
                $nextNextCode === 34 &&
                $nextNextNextCode === 34
            ) {
                $this->moveStringCursor(1, 1);
                $value .= $chunk . '"""';
                $chunk = '';
            } else {
                $this->moveStringCursor(-2, -2);
                $chunk .= $char;
            }

            [$char, $code, $bytes] = $this->readChar();
        }

        throw new SyntaxError(
            $this->source,
            $this->position,
            'Unterminated string.'
        );
    }

    private function unexpectedCharacterMessage($code)
    {
        // SourceCharacter
        if ($code < 0x0020 && $code !== 0x0009 && $code !== 0x000A && $code !== 0x000D) {
            return 'Cannot contain the invalid character ' . Utils::printCharCode($code);
        }

        if ($code === 39) {
            return "Unexpected single quote character ('), did you mean to use " .
                'a double quote (")?';
        }

        return 'Cannot parse the unexpected character ' . Utils::printCharCode($code) . '.';
    }

    private function assertValidStringCharacterCode($code, $position): void
    {
        // SourceCharacter
        if ($code < 0x0020 && $code !== 0x0009) {
            throw new SyntaxError(
                $this->source,
                $position,
                'Invalid character within String: ' . Utils::printCharCode($code)
            );
        }
    }

    private function readChars($charCount, $advance = false, $byteStreamPosition = null)
    {
        $result = '';
        $totalBytes = 0;
        $byteOffset = $byteStreamPosition ?: $this->byteStreamPosition;

        for ($i = 0; $i < $charCount; $i++) {
            [$char, , $bytes] = $this->readChar(false, $byteOffset);
            $totalBytes += $bytes;
            $byteOffset += $bytes;
            $result .= $char;
        }
        if ($advance) {
            $this->moveStringCursor($charCount, $totalBytes);
        }

        return [$result, $totalBytes];
    }

    private function assertValidBlockStringCharacterCode($code, $position): void
    {
        // SourceCharacter
        if ($code < 0x0020 && $code !== 0x0009 && $code !== 0x000A && $code !== 0x000D) {
            throw new SyntaxError(
                $this->source,
                $position,
                'Invalid character within String: ' . Utils::printCharCode($code)
            );
        }
    }
}
