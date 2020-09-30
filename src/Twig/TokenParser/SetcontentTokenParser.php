<?php

declare(strict_types=1);

namespace Bolt\Twig\TokenParser;

use Bolt\Storage\Directive\EarliestDirectiveHandler;
use Bolt\Storage\Directive\LatestDirectiveHandler;
use Bolt\Storage\Directive\LimitDirective;
use Bolt\Storage\Directive\OrderDirective;
use Bolt\Storage\Directive\PageDirective;
use Bolt\Storage\Directive\PrintQueryDirective;
use Bolt\Storage\Directive\RandomDirectiveHandler;
use Bolt\Storage\Directive\ReturnMultipleDirective;
use Bolt\Storage\Directive\ReturnSingleDirective;
use Bolt\Twig\Node\SetcontentNode;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Twig {% setcontent %} token parser.
 *
 * @author Bob den Otter <bob@twokings.nl>
 * @author Xiao-Hu Tai <xiao@twokings.nl>
 */
class SetcontentTokenParser extends AbstractTokenParser
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function parse(Token $token): Node
    {
        $lineno = $token->getLine();

        $arguments = new ArrayExpression([], $lineno);
        $whereArguments = [];

        // name - the new variable with the results
        $name = $this->parser->getStream()->expect(Token::NAME_TYPE)->getValue();
        $this->parser->getStream()->expect(Token::OPERATOR_TYPE, '=');

        // ContentType, or simple expression to content.
        $contentType = $this->parser->getExpressionParser()->parseExpression();

        $counter = 0;

        do {
            // where parameter
            if ($this->parser->getStream()->test(Token::NAME_TYPE, 'where')) {
                $this->parser->getStream()->next();
                $whereArguments = [
                    'wherearguments' => $this->parser->getExpressionParser()->parseExpression(),
                ];
            }

            // limit parameter
            if ($this->parser->getStream()->test(Token::NAME_TYPE, LimitDirective::NAME)) {
                $this->parser->getStream()->next();
                $limit = $this->parser->getExpressionParser()->parseExpression();
                $arguments->addElement($limit, new ConstantExpression(LimitDirective::NAME, $lineno));
            }

            // order / orderby parameter
            if ($this->parser->getStream()->test(Token::NAME_TYPE, OrderDirective::NAME) ||
                $this->parser->getStream()->test(Token::NAME_TYPE, 'orderby')) {
                $this->parser->getStream()->next();
                $order = $this->parser->getExpressionParser()->parseExpression();
                $arguments->addElement($order, new ConstantExpression(OrderDirective::NAME, $lineno));
            }

            // page parameter
            if ($this->parser->getStream()->test(Token::NAME_TYPE, PageDirective::NAME)) {
                $this->parser->getStream()->next();
                $page = $this->parser->getExpressionParser()->parseExpression();
                $arguments->addElement($page, new ConstantExpression(PageDirective::NAME, $lineno));
            }

            // printquery parameter
            if ($this->parser->getStream()->test(Token::NAME_TYPE, PrintQueryDirective::NAME)) {
                $this->parser->getStream()->next();
                $arguments->addElement(
                    new ConstantExpression(true, $lineno),
                    new ConstantExpression(PrintQueryDirective::NAME, $lineno)
                );
            }

            // returnsingle parameter
            if ($this->parser->getStream()->test(Token::NAME_TYPE, ReturnSingleDirective::NAME)) {
                $this->parser->getStream()->next();
                $arguments->addElement(
                    new ConstantExpression(true, $lineno),
                    new ConstantExpression(ReturnSingleDirective::NAME, $lineno)
                );
            }

            // returnmultiple parameter
            if ($this->parser->getStream()->test(Token::NAME_TYPE, ReturnMultipleDirective::NAME)) {
                $this->parser->getStream()->next();
                $arguments->addElement(
                    new ConstantExpression(true, $lineno),
                    new ConstantExpression(ReturnMultipleDirective::NAME, $lineno)
                );
            }

            // latest parameter
            if ($this->parser->getStream()->test(Token::NAME_TYPE, LatestDirectiveHandler::NAME)) {
                $this->parser->getStream()->next();
                $arguments->addElement(
                    new ConstantExpression(true, $lineno),
                    new ConstantExpression(LatestDirectiveHandler::NAME, $lineno)
                );
            }

            // earliest parameter
            if ($this->parser->getStream()->test(Token::NAME_TYPE, EarliestDirectiveHandler::NAME)) {
                $this->parser->getStream()->next();
                $arguments->addElement(
                    new ConstantExpression(true, $lineno),
                    new ConstantExpression(EarliestDirectiveHandler::NAME, $lineno)
                );
            }

            // random parameter
            if ($this->parser->getStream()->test(Token::NAME_TYPE, RandomDirectiveHandler::NAME)) {
                $this->parser->getStream()->next();
                $arguments->addElement(
                    new ConstantExpression(true, $lineno),
                    new ConstantExpression(RandomDirectiveHandler::NAME, $lineno)
                );
            }

            // Make sure we don't get stuck in a loop, if a token can't be parsed.
            ++$counter;
        } while (! $this->parser->getStream()->test(Token::BLOCK_END_TYPE) && ($counter < 10));

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new SetcontentNode($name, $contentType, $arguments, $whereArguments, $lineno, $this->getTag());
    }

    /**
     * {@inheritdoc}
     */
    public function getTag(): string
    {
        return 'setcontent';
    }
}
