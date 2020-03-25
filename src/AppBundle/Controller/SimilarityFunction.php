<?php
namespace AppBundle\Controller;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

/**
 * Class SimilarityFunction
 * @package AppBundle\Controller
 */
class SimilarityFunction extends FunctionNode
{
    /**
     * @var
     */
    protected $firstString;
    /**
     * @var
     */
    protected $secondString;
    /**
     * @var
     */
    protected $thirdString;

    public function getSql( SqlWalker $sqlWalker )
    {
        return
            'SIMILARITY (' .
            $sqlWalker->walkArithmeticExpression( $this->firstString ) .
            ',' .
            $sqlWalker->walkArithmeticExpression( $this->secondString ) .
            ',' .
            $sqlWalker->walkArithmeticExpression( $this->thirdString ) .
            ')';
    }

    public function parse( Parser $parser )
    {
        $parser->Match( Lexer::T_IDENTIFIER );
        $parser->Match( Lexer::T_OPEN_PARENTHESIS );

        $this->firstString = $parser->ArithmeticExpression();
        $parser->Match( Lexer::T_COMMA );

        $this->secondString = $parser->ArithmeticExpression();
        $parser->Match( Lexer::T_COMMA );

        $this->thirdString = $parser->ArithmeticExpression();

        $parser->Match( Lexer::T_CLOSE_PARENTHESIS );
    }
}