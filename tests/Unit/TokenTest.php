<?php

namespace Coretrek\Idp\Tests\Unit;

use Coretrek\Idp\Token;
use Coretrek\Idp\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

class TokenTest extends TestCase
{
    /** @test */
    public function it_generates_a_valid_token()
    {
        Http::fake([
            '*/oauth/token' => Http::response(['token_type' => 'Bearer', 'access_token' => 'ABC-123', 'expires_in' => 1111], 200),
        ]);

        $token = Token::make(config('coretrek-idp.base_url'), config('coretrek-idp.client_id'), config('coretrek-idp.client_secret'), config('coretrek-idp.scopes'));

        $this->assertInstanceOf(Token::class, $token);
        $this->assertInstanceOf(Jsonable::class, $token);
        $this->assertInstanceOf(Arrayable::class, $token);

        $this->assertEquals('Bearer', $token->type);
        $this->assertEquals(1111, $token->expiresIn);
        $this->assertEquals('ABC-123', $token->accessToken);
        $this->assertEquals('Bearer ABC-123', $token->authorizationHeader());
    }
}
