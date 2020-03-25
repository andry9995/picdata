<?php
namespace AppBundle\Controller;

use \Doctrine\ORM\Query\AST\Functions\FunctionNode;
use \Doctrine\ORM\Query\Lexer;
use \Doctrine\ORM\Query\Parser;
use \Doctrine\ORM\Query\SqlWalker;

/**
 * Class MysqlRound
 * @package AppBundle\Controller
 */
class MysqlRound extends FunctionNode
{
    /**
     * @var
     */
    public $simpleArithmeticExpression;

    /**
     * @var
     */
    public $roundPrecission;

    /**
     * @param SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return 'ROUND(' .
        $sqlWalker->walkSimpleArithmeticExpression($this->simpleArithmeticExpression) .','.
        $sqlWalker->walkStringPrimary($this->roundPrecission) .
        ')';
    }

    /**
     * @param Parser $parser
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->simpleArithmeticExpression = $parser->SimpleArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->roundPrecission = $parser->ArithmeticExpression();
        if ($this->roundPrecission == null) {
            $this->roundPrecission = 0;
        }
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}