<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketCollection;
 use App\Http\Resources\TicketResource;
 use App\Models\Ticket;
 use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;

class ApiController extends Controller
{
    public function getTickets(Request $request, string $id = null)
    {
        if (isset($id)){
            if($request->user()->tokenCan('ticket:admin')) {
                return new TicketResource(Ticket::with('comments')->with('users')->with('owner')->findOrFail($id));
            }
            return new TicketResource(Auth::user()->tickets()->with('comments')->with('users')->with('owner')->findOrFail($id));
        }

        if($request->user()->tokenCan('ticket:admin')) {
            return TicketResource::collection(Ticket::with('comments')->with('users')->with('owner')->get());
        }

        return TicketResource::collection(Auth::user()->tickets()->with('comments')->with('users')->with('owner')->get());
    }

    public function getTicketsPaginated(Request $request) {
        if($request->user()->tokenCan('ticket:admin')) {
            return new TicketCollection(Ticket::with('comments')->with('users')->with('owner')->paginate(5));
         }

        return new TicketCollection(Auth::user()->tickets()->with('comments')->with('users')->with('owner')->paginate(5));

    }

    public function store(StoreTicketRequest $request) {
            //validation
            $validated = $request->validated();

            //send the data
            $ticket = Ticket::create($validated);

            //Attach user
            $ticket->users()->attach(Auth::id(),['owner' => true]);

            $ticket->comments()->create([
                    'text' => $validated['text'],
                    'user_id' => Auth::id(),
                ]);


            return response(new TicketResource($ticket), 201);
    }

    public function update(UpdateTicketRequest $request, string $id) {
        $ticket = Ticket::findOrFail($id);

        if(!$ticket->users->contains(Auth::id()) && !$request->user()->tokenCan('ticket:admin')){
            return response()->json([
                'error' => 'No permission!'
            ],403);
        }

        $validated = $request->validate([
            'title' => 'required|string|min:5|max:100',
            'priority' => 'required|integer|min:0|max:3',
        ]);

        $ticket->update($validated);

        return (new TicketResource($ticket));
    }

    public function destroy(Request $request, $id) {
        $ticket = Ticket::findOrFail($id);

        if(!$ticket->users->contains(Auth::id()) && !$request->user()->tokenCan('ticket:admin')){
            return response()->json([
                'error' => 'No permission!'
            ],403);
        }

        $ticket->delete();

        return response(status: 204);

    }
}
