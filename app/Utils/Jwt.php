<?php

namespace App\Utils;

use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Encoding\JoseEncoder;

class Jwt 
{
    public static function parseUrn($jwt)
    {
      try {
        $parser = new Parser(new JoseEncoder());
        $token = $parser->parse($jwt);

        // User's ID on LinkedIn
        return $token->claims()->get('sub');
      } catch (\Exception $e) {
        return 'Oh no, an error: ' . $e->getMessage();
      }
    }
}