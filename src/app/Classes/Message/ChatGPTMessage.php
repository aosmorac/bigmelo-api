<?php

namespace App\Classes\Message;

use App\Models\Message;
use App\Models\User;

class ChatGPTMessage
{
    /**
     * @var ChatGPTMessageResponse
     */
    private ChatGPTMessageResponse $message_response;

    /**
     * @var User
     */
    private User $user;

    /**
     * @var array
     */
    private array $messages;

    /**
     * @param int $user_id
     * @param ChatGPTMessageResponse $message_response
     */
    public function __construct(int $user_id, ChatGPTMessageResponse $message_response)
    {
        $this->user = User::find($user_id);
        $this->message_response = $message_response;
    }

    /**
     * Save ChatGPT message
     *
     * @return void
     */
    public function save(): void
    {
        $text = $this->message_response->getContent();

        // Split the text into 100-word fragments
        $fragments = preg_split('/((?:\S+\s*){1,100})/', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        foreach ($fragments as $fragment) {
            $message = Message::create([
                'user_id' => $this->user->id,
                'content' => $fragment,
                'source' => 'ChatGPT'
            ]);

            \App\Models\ChatgptMessage::create([
                'message_id' => $message->id,
                'chatgpt_id' => $this->message_response->getChatgptID(),
                'object_type' => $this->message_response->getObjectType(),
                'model' => $this->message_response->getModel(),
                'role' => $this->message_response->getRole(),
                'prompt_tokens' => $this->message_response->getPromptTokens(),
                'completion_tokens' => $this->message_response->getCompletionTokens(),
                'total_tokens' => $this->message_response->getTotalTokens()
            ]);

            $this->messages[] = $message;
        }
    }

    /**
     * Return the message stored
     *
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
