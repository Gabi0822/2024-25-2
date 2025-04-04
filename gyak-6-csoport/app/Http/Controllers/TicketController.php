<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Auth::user()->tickets()->where('done', false)->orderByDesc(Comment::select('updated_at')->whereColumn('comments.ticket_id','tickets.id')->latest()->take(1))->paginate(3);

        //$tickets = Ticket::all();

        return view('ticket.tickets', ['tickets' => $tickets]);
    }

    public function create() {
        return view('ticket.ticketform');
    }

    public function store(Request $request) {
        //validation
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:100',
            'priority' => 'required|integer|min:0|max:3',
            'text' => 'required|string|max:1000',
            'file' =>  'nullable|file'
        ]);

        //send the data
        $ticket = Ticket::create($validated);

        /*
        $t = new Ticket();
        $t->title = $validated['title'];
        $t->save()
        */

        //Attach user
        $ticket->users()->attach(Auth::id(),['owner' => true]);

        /*
        Comment::create([
            'text' => $validated['text'],
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
        ]);
        */

        //With file management
        if($request->hasFile('file')) {
            $filename = $request->file('file')->store();
            $ticket->comments()->create([
                'text' => $validated['text'],
                'user_id' => Auth::id(),
                'filename' => $request->file('file')->getClientOriginalName(),
                'filename_hash' => $filename,
            ]);
        } else {
            $ticket->comments()->create([
                'text' => $validated['text'],
                'user_id' => Auth::id(),
            ]);
        }


        return redirect()->route('tickets.show', ['ticket' => $ticket->id]);
    }

    public function show(string $id) {

        /*
        $ticket = Ticket::find($id);
        if(!$ticket){
            abort(404);
        }
        */
        $ticket = Ticket::findOrFail($id);

        if(!$ticket->users->contains(Auth::id()) && !Auth::user()->admin)
        {
            abort(401);
        }

        return view('ticket.ticket', ['ticket' => $ticket]);

    }

    public function edit(string $id) {
        $ticket = Ticket::findOrFail($id);

        if(!$ticket->users->contains(Auth::id()) && !Auth::user()->admin){
            abort(401);
        }

        return view('ticket.ticketform', ['ticket' => $ticket]);
    }

    public function update(Request $request, string $id) {
        $ticket = Ticket::findOrFail($id);

        if(!$ticket->users->contains(Auth::id()) && !Auth::user()->admin){
            abort(401);
        }

        $validated = $request->validate([
            'title' => 'required|string|min:5|max:100',
            'priority' => 'required|integer|min:0|max:3',
        ]);

        $ticket->update($validated);

        return redirect()->route('tickets.show', ['ticket' => $ticket->id]);
    }

    public function destroy(string $id) {
        $ticket = Ticket::findOrFail($id);

        if(!$ticket->users->contains(Auth::id()) && !Auth::user()->admin){
            abort(401);
        }

        $ticket->comments()->delete();
        $ticket->delete();

        return redirect()->route('tickets.index');

    }
}
