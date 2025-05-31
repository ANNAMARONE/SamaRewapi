<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSignalementRequest;
use App\Http\Requests\UpdateSignalementRequest;
use App\Models\Signalement;
use App\Models\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\UrgentSignalementNotification;


class SignalementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $signalement=Signalement::all();
        return response()->json([
            'status'=>true,
            'data'=>$signalement
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
        try {
            $validateData = $request->validate([
                'categorie_id' => 'required|exists:categories,id',
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'adresse' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'urgent' => 'required|boolean',
                'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
    
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'utilisateur non authentifié'
                ], 401);
            }
    
            $signalement = Signalement::create([
                'user_id' => $user->id,
                'categorie_id' => $validateData['categorie_id'],
                'titre' => $validateData['titre'],
                'description' => $validateData['description'],
                'adresse' => $validateData['adresse'] ?? null,
                'latitude' => $validateData['latitude'] ?? null,
                'longitude' => $validateData['longitude'] ?? null,
                'urgent' => $validateData['urgent'],
            ]);
            if ($validateData['urgent']) {
                $autorites = User::where('role', 'autorite')->get();
            
                foreach ($autorites as $autorite) {
                    $autorite->notify(new UrgentSignalementNotification($signalement));
                }
            }
           
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('signalements', 'public');
                    $signalement->images()->create(['chemin' => $path]);
                }
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Signalement soumis avec succès',
                'data' => $signalement->load('images')
            ], 201);
    
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Problème lors de l\'ajout du signalement',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $signalement = Signalement::with(['images', 'categorie', 'user'])->find($id);
    
        if (!$signalement) {
            return response()->json([
                'status' => false,
                'message' => 'Signalement non trouvé'
            ], 404);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Détails du signalement récupérés avec succès',
            'data' => $signalement
        ]);
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Signalement $signalement)
    {
        //
    }

    /**
     * ith the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $signalement=Signalement::findOrFail($id);
        if(!$signalement){
            return response()->json([
                'status'=>false,
                'message'=>'signalement non trouver'
            ]);
        }
        // Vérification de l'utilisateur (optionnel mais recommandé)
    if (Auth::id() !== $signalement->user_id) {
        return response()->json([
            'status' => false,
            'message' => "Vous n'êtes pas autorisé à modifier ce signalement"
        ], 403);
    }

    $validated = $request->validate([
        'categorie_id' => 'nullable|exists:categories,id',
        'titre' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'adresse' => 'nullable|string',
        'latitude' => 'nullable|numeric|between:-90,90',
        'longitude' => 'nullable|numeric|between:-180,180',
        'statut' => 'nullable|in:non_traite,en_cours,resolu',
    ]);

    $signalement->update($validated);

    return response()->json([
        'status' => true,
        'message' => 'Signalement mis à jour avec succès',
        'data' => $signalement
    ]);
    }
    public function updateStatus(Request $request ,$id){
      $request->validate([
        'statut'=>'required|in:non_traite,en_cours,resolu'
      ]) ;
      $signalement=Signalement::findOrFail($id);
        if(!$signalement){
            return response()->json([
                'status'=>false,
                'message'=>'signalement non trouver'

            ]);     
    } 
            $signalement->statut=$request->statut;
            $signalement->save();
            return response()->json([
            'statut'=>true,
            'message'=>'signalement mis a jours avec succes',
            'data'=>$signalement
            ]);
    }
    public function getEnCours(){
        $signalement=Signalement::with(['images','categorie','user'])
        ->where('statut','en_cours')
        ->latest()
        ->get();
        return response()->json([
            'statut'=>true,
            'message'=>'signalements en cours recuperes avec succes ',
            'data'=>$signalement
        ]);
    }
    public function getnon_traite(){
        $signalement=Signalement::with(['images','categorie','user'])
        ->where('statut','non_traite')
        ->latest()
        ->get();
        return response()->json([
            'statut'=>true,
            'message'=>'signalements en cours recuperes avec succes ',
            'data'=>$signalement
        ]);
    }
    public function getresolut(){
        $signalement=Signalement::with(['images','categorie','user'])
        ->where('statut','resolu')
        ->latest()
        ->get();
        return response()->json([
            'statut'=>true,
            'message'=>'signalements resolut recuperes avec succes ',
            'data'=>$signalement
        ]);  
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Signalement $signalement)
    {
        try{
            $signalement->delete();
            return response()->json([
                'success' => true,
                'message' => 'signalement supprimée avec succès.'
            ], 200);
         
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Échec de la suppression de la signalement.',
            'error' => $e->getMessage()
        ], 500);
    
    }
}

public function carte(){
    $signalement=Signalement::select(
        'id',
        'titre',
        'description',
        'latitude',
        'longitude',
        'statut',
        'adresse'
    )
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->get();
    return response()->json([
        'status'=>true,
        'message'=>'Signalements géolocalisés récupérés',
        'data'=>$signalement
    ]);
}
public function filtrer(Request $request)
{
    try {
        $query = Signalement::with(['images', 'categorie', 'user']);

        if ($request->filled('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $signalements = $query->latest()->get();

        return response()->json([
            'status' => true,
            'message' => 'Signalements filtrés avec succès',
            'data' => $signalements
        ],201);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Erreur lors du filtrage des signalements',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function parCategorie($id){
try{
    $signalements=Signalement::with(['images','categorie','user'])
    ->where('categorie_id',$id)
    ->latest()
    ->get();
    return response()->json([
        'status' => true,
        'message' => "Signalements de la catégorie $id récupérés avec succès",
        'data' => $signalements
    ]);
}catch (\Exception $e) {
    return response()->json([
        'status' => false,
        'message' => 'Erreur lors de la récupération des signalements',
        'error' => $e->getMessage()
    ], 500);
}

 }
 public function statistiques()
{
    try {
        $parCategorie = Signalement::select('categorie_id', DB::raw('count(*) as total'))
            ->groupBy('categorie_id')
            ->with('categorie:id,nom')
            ->get();

        $parStatut = Signalement::select('statut', DB::raw('count(*) as total'))
            ->groupBy('statut')
            ->get();

        $parAdresse = Signalement::select('adresse', DB::raw('count(*) as total'))
            ->groupBy('adresse')
            ->get();

        $parMois = Signalement::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mois'), DB::raw('count(*) as total'))
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Statistiques récupérées',
            'data' => [
                'par_categorie' => $parCategorie,
                'par_statut' => $parStatut,
                'par_adresse' => $parAdresse,
                'par_mois' => $parMois,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Erreur lors de la génération des statistiques',
            'error' => $e->getMessage()
        ], 500);
    }
}

}