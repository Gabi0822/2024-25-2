@extends('ticket.layout')

@section('title', $ticket->title)

@section('content')
<h1 class="ps-3 me-auto">
    {{$ticket->title}}
    @switch($ticket->priority)
        @case(0)
            <span class="badge rounded-pill bg-info fs-6">Alacsony</span>
            @break
        @case(1)
            <span class="badge rounded-pill bg-success fs-6">Norrmàl</span>
            @break
        @case(2)
            <span class="badge rounded-pill bg-warning text-black fs-6">Magas</span>
            @break
        @case(3)
            <span class="badge rounded-pill bg-danger fs-6">Azonnali</span>
            @break
    @endswitch
</h1>
@endsection
