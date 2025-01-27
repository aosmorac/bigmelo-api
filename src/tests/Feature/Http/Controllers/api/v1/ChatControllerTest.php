<?php

namespace Tests\Feature\Http\Controllers\api\v1;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Event;

/**
 * Class ChatControllerTest
 *
 * Run these specific tests
 * php artisan test tests/Feature/Http/Controllers/api/v1/ChatControllerTest.php
 *
 * @package Tests\Feature\Http\Controllers\api\v1
 */
class ChatControllerTest extends TestApi
{
    /**
     * Message api endpoint
     */
    const ENDPOINT_CHAT = '/v1/chat';

    /**
     * Initialize migration
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $user1 = User::create([
            'role'              => 'user',
            'name'              => 'User 1',
            'email'             => 'user1@mydomain.com',
            'country_code'      => '+57',
            'phone_number'      => '3133920001',
            'full_phone_number' => '+573133920001',
            'password'          => '$2y$10$dmQmyyu./5uEb.Ti/ZeO3e80V8.mbivA4K1b43O9yvjWbvff0J7qK'
        ]);

        $user2 = User::create([
            'role'              => 'user',
            'name'              => 'User 2',
            'email'             => 'user2@mydomain.com',
            'country_code'      => '+57',
            'phone_number'      => '3133920001',
            'full_phone_number' => '+573133920002',
            'password'          => '$2y$10$dmQmyyu./5uEb.Ti/ZeO3e80V8.mbivA4K1b43O9yvjWbvff0J7qK'
        ]);

        $message1 = Message::create([
            'user_id' => $user1->id,
            'content' => $this->faker->text(300),
            'source'  => 'API'
        ]);
        $message2 = Message::create([
            'user_id' => $user1->id,
            'content' => $this->faker->text(300),
            'source'  => 'ChatGPT'
        ]);
        $message3 = Message::create([
            'user_id' => $user1->id,
            'content' => $this->faker->text(300),
            'source'  => 'WhatsApp'
        ]);
        $message4 = Message::create([
            'user_id' => $user1->id,
            'content' => $this->faker->text(300),
            'source'  => 'ChatGPT'
        ]);
        $message5 = Message::create([
            'user_id' => $user2->id,
            'content' => $this->faker->text(300),
            'source'  => 'Admin'
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function admin_can_list_all_chats_by_user_id(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->json('GET', self::ENDPOINT_CHAT);

        $response_data = json_decode($response->getContent())->data;

        $response->assertStatus(200);
        $this->assertCount(2, $response_data);
    }

    /**
     * @test
     *
     * @return void
     */
    public function admin_can_list_the_total_or_messages_by_chat(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->getToken())
            ->json('GET', self::ENDPOINT_CHAT);

        $response_data = json_decode($response->getContent())->data;

        $response->assertStatus(200);

        $this->assertEquals('User 2', $response_data[0]->name);
        $this->assertEquals('+573133920002', $response_data[0]->full_phone_number);
        $this->assertEquals(1, $response_data[0]->total_messages->total);
        $this->assertEquals(1, $response_data[0]->total_messages->admin);
        $this->assertEquals(0, $response_data[0]->total_messages->chat_gpt);
        $this->assertEquals(0, $response_data[0]->total_messages->api);
        $this->assertEquals(0, $response_data[0]->total_messages->whatsapp);

        $this->assertEquals('User 1', $response_data[1]->name);
        $this->assertEquals('+573133920001', $response_data[1]->full_phone_number);
        $this->assertEquals(4, $response_data[1]->total_messages->total);
        $this->assertEquals(0, $response_data[1]->total_messages->admin);
        $this->assertEquals(2, $response_data[1]->total_messages->chat_gpt);
        $this->assertEquals(1, $response_data[1]->total_messages->api);
        $this->assertEquals(1, $response_data[1]->total_messages->whatsapp);
    }

    /**
     * @test
     *
     * @return void
     */
    public function unauthorized_user_can_not_list_chats()
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->faker->word())
            ->json('GET', self::ENDPOINT_CHAT);

        $response->assertStatus(401);
        $response->assertJsonPath('message', 'Unauthenticated.');
    }

}
