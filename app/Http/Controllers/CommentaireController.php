<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentaireRequest;
use App\Http\Requests\UpdateCommentaireRequest;
use App\Models\Commentaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class CommentaireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($signalement_id)
    {
        $commentaires = Commentaire::where('signalement_id', $signalement_id)
        ->with('user:id,nom,prenom') // pour afficher l'auteur du commentaire
        ->latest()
        ->get();

    return response()->json([
        'status' => true,
        'data' => $commentaires
    ]);
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
        $request->validate([
            'signalement_id' => 'required|exists:signalements,id',
            'contenu' => 'required|string'
        ]);
    
        $commentaire = Commentaire::create([
            'user_id' => Auth::id(),
            'signalement_id' => $request->signalement_id,
            'contenu' => $request->contenu,
        ]);
    
        return response()->json([
            'status' => true,
            'message' => 'Commentaire ajouté avec succès',
            'data' => $commentaire
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Commentaire $commentaire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commentaire $commentaire)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentaireRequest $request, Commentaire $commentaire)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commentaire $commentaire)
    {
        //
    }
}