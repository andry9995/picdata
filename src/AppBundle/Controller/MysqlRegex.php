<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 06/10/2016
 * Time: 13:10
 */

namespace AppBundle\Controller;
use \Doctrine\ORM\Query\AST\Functions\FunctionNode;
use \Doctrine\ORM\Query\Lexer;
use \Doctrine\ORM\Query\Parser;
use \Doctrine\ORM\Query\SqlWalker;

/**
 * Class MysqlRegex
 * @package AppBundle\Controller
 */
class MysqlRegex extends FunctionNode
{
    public $regexpExpression = null;
    public $valueExpression = null;

    /**
     * @param Parser $parser
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->regexpExpression = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->valueExpression = $parser->StringExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return '(' . $this->valueExpression->dispatch($sqlWalker) . ' REGEXP ' .
        $sqlWalker->walkStringPrimary($this->regexpExpression) . ')';
    }
}