<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Auth::user()->tickets()->where('done', false)->orderByDesc(Comment::select('created_at')
        ->whereColumn('comments.ticket_id','tickets.id')->latest()->take(1))->paginate(5);

        return view('ticket.tickets', ['tickets' => $tickets]);
    }

    public function create() {}

    public function store(Request $request) {}

    public function show(string $id) {}

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}
}
