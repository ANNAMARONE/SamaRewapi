<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResolutionRequest;
use App\Http\Requests\UpdateResolutionRequest;
use App\Models\Resolution;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resolution=Resolution::all();
        return response()->json([
            'status'=>true,
            'data'=>$resolution
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
       try{
        $validateData=$request->validate([
            'signalement_id' => 'required|exists:signalements,id',
            'proposition' => 'required|string',
        ]);
        $user=Auth::user();
        if(!$user){
            return response()->json([
                'status'=>false,
                'message'=>'utilisateur non authentifié'
            ],401);
        }
        $resolution=Resolution::create([
            'signalement_id' => $validateData['signalement_id'],
            'users_id' =>$user->id,
            'proposition' => $validateData['proposition'],
            'statut' => 'proposee',
            'date_resolution' => now(), 
        ]);
        return response()->json([
            'status'=>true,
            'message'=>'Proposition enregistrée',
            'data'=>$resolution
        ]);
       }catch(Exception $e){
        return response()->json([
            'status'=>false,
            'message'=>'problemes lors de la soumission de la proposition',
            'error'=>$e->getMessage()
        ]);
       }
        
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
{
    $resolution =Resolution::with(['user', 'signalement'])->find($id);

    if (!$resolution) {
        return response()->json([
            'status' => false,
            'message' => 'Résolution non trouvée'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'Détails de la résolution',
        'data' => $resolution
    ]);
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resolution $resolution)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResolutionRequest $request, Resolution $resolution)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resolution $resolution)
    {
        try{
            $resolution->delete();
            return response()->json([
                'success' => true,
                'message' => 'proposition supprimée avec succès.'
            ], 200);
         
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Échec de la suppression de la propositon.',
            'error' => $e->getMessage()
        ], 500);
    
    }
    }
    public function changementStatutpro(Request $request,$id){
        $request->validate([
            'statut' => 'required|in:proposee,en_cours,terminee'
        ]);
        $resolution=Resolution::findOrFail($id);
        if (!$resolution) {
            return response()->json([
                'status' => false,
                'message' => 'Résolution non trouvée'
            ], 404);
        }
        $resolution->statut = $request->statut;
        $resolution->date_resolution = now(); 
        $resolution->save();   
        return response()->json([
            'status' => true,
            'message' => 'Statut de la résolution mis à jour',
            'data' => $resolution
        ]);
     
    }
    public function getBySignalement($signalement_id)
{
    $resolutions =Resolution::with('user')
        ->where('signalement_id', $signalement_id)
        ->orderByDesc('created_at')
        ->get();

    return response()->json([
        'status' => true,
        'message' => 'Liste des propositions pour ce signalement',
        'data' => $resolutions
    ]);
}

}