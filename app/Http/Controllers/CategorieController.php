<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCategorieRequest;
use App\Models\Categorie;
use Exception;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $categorie=Categorie::all();
      return response()->json([
        'success'=>true,
        'data'=>$categorie
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
            // Validation des données
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255',
                'icone' => 'nullable|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
            ]);
    
            $imageIcone = null;
    
            // Traitement de l'upload de l'image
            if ($request->hasFile('icone')) {
                $image = $request->file('icone');
                $imageIcone = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('categories'), $imageIcone);
            }
    
            // Création de la catégorie
            $categorie = Categorie::create([
                'nom' => $validatedData['nom'],
                'icone' => $imageIcone,
            ]);
    
            return response()->json([
                'message' => 'Catégorie créée avec succès',
                'categorie' => $categorie
            ], 201);
    
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la création de la catégorie',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorie $categorie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorie $categorie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategorieRequest $request, Categorie $categorie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categorie $categorie)
{
    try {
        $categorie->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catégorie supprimée avec succès.'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Échec de la suppression de la catégorie.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
    

}