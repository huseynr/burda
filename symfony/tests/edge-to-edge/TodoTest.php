<?php

namespace App\Test\EdgeToEdge;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class TodoTest extends ApiTestCase
{
    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/todos');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            '@context'         => '/api/contexts/Todo',
            '@id'              => '/api/todos',
        ]);

        $this->assertCount(10, $response->toArray()['hydra:member']);
    }

    public function testPagination(): void
    {
        static::createClient()->request('GET', '/api/todos?page=2');

        $this->assertJsonContains([
            'hydra:view'       => [
                '@id'            => '/api/todos?page=2',
            ],
        ]);
    }

    public function testCreateTodo(): void
    {
        static::createClient()->request('POST', '/api/todos', [
            'json' => [
                'title'       => 'title',
                'description' => "description",
                'status'      => 'pending'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);

        $this->assertResponseHeaderSame(
            'content-type', 'application/ld+json; charset=utf-8'
        );

        $this->assertJsonContains([
            'title'       => 'title',
            'description' => "description",
            'status'      => 'pending'
        ]);
    }
}










