<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVoteRequest;
use App\Http\Requests\UpdateVoteRequest;
use App\Models\Signalement;
use App\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateData=$request->validate([
            'signalement_id' => 'required|exists:signalements,id',
            'vote' => 'required|boolean'
        ]);
        $user=Auth::user();
        if(!$user){
            return response()->json([
                'status'=>false,
                'message'=>'utilisateur non authentifié'
            ],401);
        }
        $vote = Vote::updateOrCreate(
            [
                'users_id' => $user->id,
                'signalement_id' => $validateData['signalement_id']
            ],
            [
                'vote' => $validateData['vote']
            ]
        );
    
        return response()->json([
            'status' => true,
            'message' => 'Vote enregistré',
            'data' => $vote
        ]); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Vote $vote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vote $vote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVoteRequest $request, Vote $vote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vote $vote)
    {
        //
    }
    //Compter les votes par signalement
    public function getVotes($signalement_id)
{
    $pour =Vote::where('signalement_id','user', $signalement_id)->where('vote', true)->count();
    $contre =Vote::where('signalement_id', $signalement_id)->where('vote', false)->count();

    return response()->json([
        'status' => true,
        'signalement_id' => $signalement_id,
        'votes_pour' => $pour,
        'votes_contre' => $contre
    ]);
}
// Afficher les signalements triés par nombre de votes (priorisation)
public function signalementsPriorises()
{
    $signalements =Signalement::withCount([
        'votes as votes_pour' => function ($q) {
            $q->where('vote', true);
        }
    ])->orderByDesc('votes_pour')->get();

    return response()->json([
        'status' => true,
        'message' => 'Signalements triés par priorité',
        'data' => $signalements
    ]);
}
public function signalementsPrioritaires()
{
    $seuil = 10; // Nombre de votes requis pour être considéré comme urgent

    $signalements =Signalement::withCount([
        'votes as total_votes_pour' => function ($query) {
            $query->where('vote', true);
        }
    ])
    ->having('total_votes_pour', '>=', $seuil)
    ->with(['categorie', 'user', 'images'])
    ->orderByDesc('total_votes_pour')
    ->get();

    return response()->json([
        'status' => true,
        'message' => 'Signalements urgents',
        'data' => $signalements
    ]);
}


}