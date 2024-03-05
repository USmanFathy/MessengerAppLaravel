<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageStoringRequest;
use App\Models\Conversation;
use App\Models\Recipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $user = auth()->user();
        $conversation = $user->conversation()->findOrFail($id);

        return $conversation->messages()->paginate();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MessageStoringRequest $request)
    {
        $user = User::find(1);
        $conversationId=$request->post('conversation_id');
        $userId=$request->post('user_id');

        DB::beginTransaction();
        try {
            if ($conversationId){
                $conversation = $user->conversation()->findOrFail($conversationId);
            }else{

                $conversation = Conversation::where('type','=','peer')
                ->whereHas('participants',function ($query) use ($userId,$user){
                    $query->join('participants as participants2','participants2.conversation_id','=','participants.conversation_id')
                        ->where('participants2.user_id', '=',$user->id)
                        ->where('participants.user_id', '=',$userId);
                })->first();

            }
            if (!$conversation){
                $conversation =Conversation::create([
                    'user_id' => $user->id,
                    'type' => 'peer',
                ]);
                $conversation->participants()->attach([
                    $user->id=>['joined_at' => now()],
                    $userId =>['joined_at' => now()]
                ]);
            }
            $message = $conversation->messages()->create([
                'user_id' => $user->id,
                'body'    => $request->post('body'),
            ]);


            DB::statement("
         INSERT INTO recipients (user_id ,message_id)
        SELECT user_id , ? FROM participants
        WHERE conversation_id = ?
        ",[$message->id , $conversation->id]);

            $conversation->update([
                'last_message_id' => $message->id
            ]);
            DB::commit();
        }catch (\Throwable $e){
            DB::rollBack();
            throw $e ;
        }
        return $message;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Recipient::where([
            'user_id' => auth()->id(),
            'message_id' => $id
        ])->delete();
        return [
            'message' => 'Deleted'
        ];
    }
}
