<?php

namespace AppBundle\Doctrine\DBAL\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * CountOverlappingPeerGroupsFunction ::= "COUNT_OVERLAPPING_PEER_GROUPS" "(" ArithmeticPrimary "," ArithmeticPrimary ")"
 */
class CountOverlappingPeerGroupsFunction extends FunctionNode
{

    public $firstEmployee;
    public $secondEmployee;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'COUNT_OVERLAPPING_PEER_GROUPS(' .
            $this->firstEmployee->dispatch($sqlWalker) . ', ' .
            $this->secondEmployee->dispatch($sqlWalker) . ')'
        ;
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstEmployee = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->secondEmployee = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

}
