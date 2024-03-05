<?php

namespace App\Http\Controllers;

use App\Http\Requests\HandelParticipantRequest;
use App\Models\Conversation;
use App\Models\User;
use Carbon\Carbon;

class ConversationsController extends Controller
{
    public function index()
    {
        $user = User::find(1);
        return $user->conversations()->paginate();
    }

    public function show(Conversation $conversation)
    {
       return $conversation->load('participants');
    }
    public function addParticipants(Conversation $conversation, HandelParticipantRequest $request)
    {
        if (!$conversation->type == 'peer'){
            $data = $request->post('user_id');
            $conversation->participants()->attach([
                $data , [
                    'joined_at' => Carbon::now()
                ]
            ]);
            return [
                'message' => 'added'
            ];
        }
        return [
            "message" => "You Can't Added Any person to this Conversation "
        ];
    }
    public function removeParticipants(Conversation $conversation, HandelParticipantRequest $request)
    {
        if (!$conversation->type == 'peer') {
            $data = $request->post('user_id');
            $conversation->participants()->detach([
                $data, [
                    'joined_at' => Carbon::now()
                ]
            ]);
            return [
                "message" =>'deleted'
            ];
        }
        return [
            "message" => "You Can't delete Any person to this Conversation "
        ];
    }
}
