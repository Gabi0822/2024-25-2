<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index() {
        $tickets = Auth::user()->tickets()->where('done',false)->paginate(5);

        return view('ticket.tickets', ['tickets' => $tickets]);
    }

    public function create() {}

    public function store(Request $request) {}

    public function show(string $id) {}

    public function edit(string $id) {}

    public function update(Request $request, string $id) {}

    public function destroy(string $id) {}

    public function newComment(Request $request, string $id) {}
}
