<?php

namespace Coretrek\Idp\Tests\Unit;

use Coretrek\Idp\Sdk;
use Coretrek\Idp\Token;
use Coretrek\Idp\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class SdkTest extends TestCase
{
    /** @test */
    public function it_generates_an_sdk_instance()
    {
        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $this->assertInstanceOf(Sdk::class, $sdk);
    }

    /** @test */
    public function it_can_send_get_requests_with_the_correct_headers()
    {
        Http::fake([
            'https://www.example.com' => Http::response(['data' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $response = $sdk->get('https://www.example.com');

        $this->assertArrayHasKey('data', $response);

        Http::assertSent(fn (Request $request) => $request->method() === 'GET'
            && $request->headers()['Authorization'][0] === 'Bearer ABC-123'
        );
    }

    /** @test */
    public function list_users()
    {
        Http::fake([
            '*/api/users' => Http::response(['data' => [], 'links' => [], 'meta' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $users = $sdk->users->list();

        $this->assertArrayHasKey('data', $users);
        $this->assertArrayHasKey('meta', $users);
        $this->assertArrayHasKey('links', $users);
    }

    /** @test */
    public function list_users_with_per_page()
    {
        Http::fake([
            '*/api/users?per_page=25' => Http::response(['data' => [], 'links' => [], 'meta' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $users = $sdk->users->list(25);

        $this->assertArrayHasKey('data', $users);
        $this->assertArrayHasKey('meta', $users);
        $this->assertArrayHasKey('links', $users);
    }

    /** @test */
    public function list_users_with_filters()
    {
        Http::fake([
            '*/api/users?filter%5Bfirst_name%5D=John' => Http::response(['data' => [], 'links' => [], 'meta' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $users = $sdk->users->list(['first_name' => 'John']);

        $this->assertArrayHasKey('data', $users);
        $this->assertArrayHasKey('meta', $users);
        $this->assertArrayHasKey('links', $users);
    }

    /** @test */
    public function show_the_given_user()
    {
        Http::fake([
            '*/api/users/986b24bb-655a-4fc5-9608-8a8aba83b2dd' => Http::response(['data' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $user = $sdk->users->show('986b24bb-655a-4fc5-9608-8a8aba83b2dd');

        $this->assertArrayHasKey('data', $user);
    }

    /** @test */
    public function show_the_given_user_and_include_groups()
    {
        Http::fake([
            '*/api/users/986b24bb-655a-4fc5-9608-8a8aba83b2dd?includes=groups' => Http::response(['data' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $users = $sdk->users->show('986b24bb-655a-4fc5-9608-8a8aba83b2dd', ['groups']);

        $this->assertArrayHasKey('data', $users);
    }

    /** @test */
    public function can_create_a_new_user()
    {
        Http::fake([
            '*/api/users' => Http::response(['data' => []], 204),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $user = $sdk->users->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'mobile' => '+4795916561',
            'password' => 'Password1',
            'locale' => 'nb',
            'email_verified_at' => '2023-01-01 10:00:00',
            'two_factor_secret' => 'abc123',
            'two_factor_recovery_codes' => 'abc123 acd 321',
            'meta' => [
                'foo' => 'bar',
            ],
        ]);

        $this->assertArrayHasKey('data', $user);

        Http::assertSent(fn (Request $request) => $request['first_name'] === 'John'
            && $request['last_name'] === 'Doe'
            && $request['email'] === 'john@example.com'
            && $request['mobile'] === '+4795916561'
            && $request['password'] === 'Password1'
            && $request['locale'] === 'nb'
            && $request['email_verified_at'] === '2023-01-01 10:00:00'
            && $request['two_factor_secret'] === 'abc123'
            && $request['two_factor_recovery_codes'] === 'abc123 acd 321'
            && $request['meta'] === ['foo' => 'bar']
        );
    }

    /** @test */
    public function can_update_the_given_user()
    {
        Http::fake([
            '*/api/users/986b24bb-655a-4fc5-9608-8a8aba83b2dd' => Http::response([], 204),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $sdk->users->update('986b24bb-655a-4fc5-9608-8a8aba83b2dd', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'mobile' => '+4795916561',
            'password' => 'Password1',
            'locale' => 'nb',
            'email_verified_at' => '2023-01-01 10:00:00',
            'two_factor_secret' => 'abc123',
            'two_factor_recovery_codes' => 'abc123 acd 321',
            'meta' => [
                'foo' => 'bar',
            ],
        ]);

        Http::assertSent(fn (Request $request) => $request['first_name'] === 'John'
            && $request['last_name'] === 'Doe'
            && $request['email'] === 'john@example.com'
            && $request['mobile'] === '+4795916561'
            && $request['password'] === 'Password1'
            && $request['locale'] === 'nb'
            && $request['email_verified_at'] === '2023-01-01 10:00:00'
            && $request['two_factor_secret'] === 'abc123'
            && $request['two_factor_recovery_codes'] === 'abc123 acd 321'
            && $request['meta'] === ['foo' => 'bar']
        );
    }

    /** @test */
    public function can_delete_the_given_user()
    {
        Http::fake([
            '*/api/users/986b24bb-655a-4fc5-9608-8a8aba83b2dd' => Http::response([], 204),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $sdk->users->delete('986b24bb-655a-4fc5-9608-8a8aba83b2dd');

        Http::assertSent(fn (Request $request) => $request->url() === config('coretrek-idp.base_url').'/api/users/986b24bb-655a-4fc5-9608-8a8aba83b2dd');
    }

    /** @test */
    public function list_groups()
    {
        Http::fake([
            '*/api/groups' => Http::response(['data' => [], 'links' => [], 'meta' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $groups = $sdk->groups->list();

        $this->assertArrayHasKey('data', $groups);
        $this->assertArrayHasKey('meta', $groups);
        $this->assertArrayHasKey('links', $groups);
    }

    /** @test */
    public function list_groups_with_per_page()
    {
        Http::fake([
            '*/api/groups?per_page=25' => Http::response(['data' => [], 'links' => [], 'meta' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $groups = $sdk->groups->list(25);

        $this->assertArrayHasKey('data', $groups);
        $this->assertArrayHasKey('meta', $groups);
        $this->assertArrayHasKey('links', $groups);
    }

    /** @test */
    public function list_groups_with_filters()
    {
        Http::fake([
            '*/api/groups?filter%5Bidentifier%5D=123' => Http::response(['data' => [], 'links' => [], 'meta' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $groups = $sdk->groups->list(['identifier' => '123']);

        $this->assertArrayHasKey('data', $groups);
        $this->assertArrayHasKey('meta', $groups);
        $this->assertArrayHasKey('links', $groups);
    }

    /** @test */
    public function show_the_given_group()
    {
        Http::fake([
            '*/api/groups/986b24bb-655a-4fc5-9608-8a8aba83b2dd' => Http::response(['data' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $group = $sdk->groups->show('986b24bb-655a-4fc5-9608-8a8aba83b2dd');

        $this->assertArrayHasKey('data', $group);
    }

    /** @test */
    public function show_the_given_group_and_include_users()
    {
        Http::fake([
            '*/api/groups/986b24bb-655a-4fc5-9608-8a8aba83b2dd?includes=users' => Http::response(['data' => []], 200),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $groups = $sdk->groups->show('986b24bb-655a-4fc5-9608-8a8aba83b2dd', ['users']);

        $this->assertArrayHasKey('data', $groups);
    }

    /** @test */
    public function can_create_a_new_group()
    {
        Http::fake([
            '*/api/groups' => Http::response(['data' => []], 204),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $group = $sdk->groups->create([
            'name' => 'Group A',
            'description' => 'Description of the group A',
            'identifier' => '000000000',
            'meta' => [
                'foo' => 'bar',
            ],
        ]);

        $this->assertArrayHasKey('data', $group);

        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request['name'] === 'Group A'
            && $request['description'] === 'Description of the group A'
            && $request['identifier'] === '000000000'
            && $request['meta'] === ['foo' => 'bar']
        );
    }

    /** @test */
    public function can_update_the_given_group()
    {
        Http::fake([
            '*/api/groups/986b24bb-655a-4fc5-9608-8a8aba83b2dd' => Http::response([], 204),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $sdk->groups->update('986b24bb-655a-4fc5-9608-8a8aba83b2dd', [
            'name' => 'Group A',
            'description' => 'Description of the group A',
            'identifier' => '000000000',
            'meta' => [
                'foo' => 'bar',
            ],
        ]);

        Http::assertSent(fn (Request $request) => $request->method() === 'PATCH'
            && $request['name'] === 'Group A'
            && $request['description'] === 'Description of the group A'
            && $request['identifier'] === '000000000'
            && $request['meta'] === ['foo' => 'bar']
        );
    }

    /** @test */
    public function can_delete_the_given_group()
    {
        Http::fake([
            '*/api/groups/986b24bb-655a-4fc5-9608-8a8aba83b2dd' => Http::response([], 204),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $sdk->groups->delete('986b24bb-655a-4fc5-9608-8a8aba83b2dd');

        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE' && $request->url() === config('coretrek-idp.base_url').'/api/groups/986b24bb-655a-4fc5-9608-8a8aba83b2dd'
        );
    }

    /** @test */
    public function add_user_to_group()
    {
        Http::fake([
            '*/api/groups/457b24bb-655a-4fc5-9608-8a8aba83b3bb/users/986b24bb-655a-4fc5-9608-8a8aba83b2dd' => Http::response([], 204),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $sdk->groups->addUser('457b24bb-655a-4fc5-9608-8a8aba83b3bb', '986b24bb-655a-4fc5-9608-8a8aba83b2dd', ['foo' => 'bar']);

        Http::assertSent(fn (Request $request) => $request->method() === 'POST' && $request['meta'] === ['foo' => 'bar']
        );
    }

    /** @test */
    public function update_user_meta_in_group()
    {
        Http::fake([
            '*/api/groups/457b24bb-655a-4fc5-9608-8a8aba83b3bb/users/986b24bb-655a-4fc5-9608-8a8aba83b2dd' => Http::response([], 204),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $sdk->groups->updateUser('457b24bb-655a-4fc5-9608-8a8aba83b3bb', '986b24bb-655a-4fc5-9608-8a8aba83b2dd', ['foo' => 'bar']);

        Http::assertSent(fn (Request $request) => $request->method() === 'PATCH' && $request['meta'] === ['foo' => 'bar']
        );
    }

    /** @test */
    public function remove_the_given_user_from_the_group()
    {
        Http::fake([
            '*/api/groups/457b24bb-655a-4fc5-9608-8a8aba83b3bb/users/986b24bb-655a-4fc5-9608-8a8aba83b2dd' => Http::response([], 204),
        ]);

        $sdk = new Sdk(new Token('Bearer', 'ABC-123'), config('coretrek-idp.base_url'));

        $sdk->groups->removeUser('457b24bb-655a-4fc5-9608-8a8aba83b3bb', '986b24bb-655a-4fc5-9608-8a8aba83b2dd');

        Http::assertSent(fn (Request $request) => $request->method() === 'DELETE');
    }
}
