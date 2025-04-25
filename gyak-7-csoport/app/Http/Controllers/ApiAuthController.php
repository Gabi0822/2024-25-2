<?php

 namespace App\Http\Controllers;

 use App\Models\User;
 use App\Models\Ticket;
 use Illuminate\Http\Request;
 use App\Http\Requests\StoreTicketRequest;
 use App\Http\Requests\UpdateTicketRequest;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\Validator;
  use Illuminate\Validation\Rules\Password;
  use App\Http\Resources\TicketCollection;
 use App\Http\Resources\TicketResource;

 class ApiAuthController extends Controller
 {
     function register(Request $request) {
         $validator = Validator::make(
             $request->all(),
             [
                 'name' => 'required|string',
                 'email' => 'required|string|email|unique:users,email',
                 'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()],
             ],
             [
                 'required' => ':attribute mező megadása kötelező!',
                 'string' => ':attribute mezo csak szoveges lehet!',
                 'email' => ':attribute mezo csak helyesen formazott email lehet!',
                 'unique' => ':attribute cim mar foglalt!',
             ],
             [
                 'name' => 'A nev',
                 'email' => 'Az email',
                 'password' => 'A jelszo',
             ]
             );
             if($validator->fails()) {
                 return response()->json([
                     'error' => $validator->messages(),
                 ], 400);
             }

             $validated = $validator->validated();

             $user = User::create($validated);

             $token = $user->createToken('auth', $user->admin ? ['ticket:admin'] : ['ticket:user']);

             return response()->json([
                 'token' => $token->plainTextToken,
                 'raw' => $token,
             ], 201);
     }
     function login(Request $request)
     {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ],
            [
                'required' => ':attribute mező megadása kötelező!',
                'string' => ':attribute mezo csak szoveges lehet!',
                'email' => ':attribute mezo csak helyesen formazott email lehet!',
            ],
            [
                'email' => 'Az email',
                'password' => 'A jelszo',
            ]
            );
            if($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                ], 400);
            }

            $validated = $validator->validated();

            $user = User::where('email', $validated['email'])->first();

            if(!$user){
                return response()->json([
                    'error' => 'Hibas email cim',
                ], 404);
            }

            if(Auth::attempt($validated)){
                //Token generalasa
                $token = $user->createToken($user->email, $user->admin ? ['ticket:admin'] : []);

                return response()->json([
                    'token' => $token->plainTextToken,
                ]);
            } else
            {
                return response()->json([
                    'error' => 'Hibas jelszo',
                ], 401);
            }

     }

     function logout(Request $request){
        $user = Auth::user();

        $request->user()->currentAccessToken()->delete();

        return response()->json([],204);
     }

     function user(Request $request) {
        return $request->user();
     }

     //Ticket CRUD vegpontok
     public function getTickets(Request $request, string $id = null)
     {
        if(isset($id)){
            if($request->user()->tokenCan('ticket:admin')){
                return new TicketResource(Ticket::with('comments')->with('users')->with('owner')->findOrFail($id));
            }
            return new TicketResource(Auth::user()->tickets()->with('comments')->with('users')->with('owner')->findOrFail($id));
        }

        if($request->user()->tokenCan('ticket:admin')){
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
        $validated = $request->validated();
        $ticket = Ticket::create($validated);
        $ticket->users()->attach(Auth::id(),['owner' => true]);

        $ticket->comments()->create([
            'text' => $validated['text'],
            'user_id' => Auth::id(),
        ]);

        return response(new TicketResource($ticket),201);
    }

    public function update(UpdateTicketRequest $request, $id) {
        $ticket = Ticket::findOrFail($id);

        if(!$ticket->users->contains(Auth::id()) && !$request->user()->tokenCan('ticket:admin')){
            return response()->json(['error' => 'Nincs jogosultsaga ehhez'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|min:5|max:100',
            'priority' => 'required|integer|min:0|max:3',
        ]);

        $ticket->update($validated);
        return response(new TicketResource($ticket));
    }

    public function destroy(Request $request, $id) {
        $ticket = Ticket::findOrFail($id);

        if(!$ticket->users->contains(Auth::id()) && !$request->user()->tokenCan('ticket:admin')){
            return response()->json(['error' => 'Nincs jogosultsaga ehhez'], 403);
        }

        $ticket->delete();

        return response(status: 204);

    }
 }
