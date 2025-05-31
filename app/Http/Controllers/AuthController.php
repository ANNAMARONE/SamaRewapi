<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
   
    public function register(Request $request)
    {
        try {
            //  Validation des données
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'telephone' => 'required|string|min:9|unique:users',
                'role' => 'required|string|in:citoyen,autorite,admin',
                'adresse' => 'required|string|max:255'
            ]);
    
            //  Création de l'utilisateur
            $user = User::create([
                'nom' => $validatedData['nom'],
                'prenom' => $validatedData['prenom'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'telephone' => $validatedData['telephone'],
                'role' => $validatedData['role']?? 'citoyen',
                'adresse' => $validatedData['adresse'],
                
            ]);
            $user->assignRole($validatedData['role'] ?? 'citoyen');
            // Génération du token
            $token = JWTAuth::fromUser($user);
    
            // Retour du succès
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès.',
                'token' => $token,
                'user' => $user
            ], 201);
    
        } catch (ValidationException $e) {
            //Erreur de validation
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
    
        } catch (JWTException $e) {
            //  Erreur lors de la génération du token
            return response()->json([
                'success' => false,
                'message' => 'Impossible de générer le token'
            ], 500);
    
        } catch (\Exception $e) {
            //  Autres erreurs inattendues
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'mot_de_passe');
    
        try {
            if (! $token = JWTAuth::attempt(['email' => $credentials['email'], 'password' => $credentials['mot_de_passe']])) {
                return response()->json(['error' => 'Identifiants invalides'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Impossible de créer le token'], 500);
        }
    
        return response()->json([
            'message'=>'connexion reussit avec succes',
            'success'=>true,
            'token' => $token,
           'user'=>Auth::guard('api')->user(),
          'expires_in'=>Auth::guard('api')->factory()->getTTL()*60
        ]);
    }
    
  public function logout(){
    try{
        JWTAuth::invalidate(JWTAuth::getToken());
    }catch(JWTException  $e){
        return response()->json(['error'=>'Échec de la déconnexion, veuillez réessayer'],500);
    }
    return response()->json(['message'=>'Déconnexion réussie']);
  }
  public function getUser(){
    try{
        $user=Auth::user();
        if(!$user){
            return response()->json(['error'=>'user not found'],404);
        }
        return response()->json($user);
    }catch(JWTException $e){
        return response()->json(['error'=>'failed to fetch user profil'],500);
    }
    
  }
  public function updateUser(Request $request){
    try{
        $user=Auth::user();
        $user->update($request->only(['nom','prenom','email','password','telephone','role','adresse']));
        return response()->json($user);
        
    }catch(JWTException $e){
        return response()->json(['error'=>'failed to update user']);
    }
  }
}