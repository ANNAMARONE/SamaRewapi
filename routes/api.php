<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ResolutionController;
use App\Http\Controllers\SignalementController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/' ,function(){
return response()->json(['message'=>'hello word']);
});

Route::post('/register',[AuthController::class, 'register']);
Route::post('/login',[AuthController::class,'login']);
Route::apiResource('categories',CategorieController::class);
Route::get('/signalements/statut/en_cours', [SignalementController::class, 'getEnCours']);
Route::get('/signalements/statut/non_traite', [SignalementController::class, 'getnon_traite']);
Route::get('/signalements/statut/resolut', [SignalementController::class, 'getresolut']);
Route::get('/signalements',[SignalementController::class,'index']);
Route::get('/signalement/detail/{id}',[SignalementController::class,'show']);
Route::get('/signalements/carte', [SignalementController::class, 'carte']);
Route::get('/signalements/filtrer', [SignalementController::class, 'filtrer']);
Route::get('/signalements/categorie/{id}', [SignalementController::class, 'parCategorie']);
Route::get('/solution',[ResolutionController::class,'index']);
Route::get('/signalements/{id}/resolutions', [ResolutionController::class, 'getBySignalement']);
Route::get('/resolutions/{id}', [ResolutionController::class, 'show']);
// Obtenir les statistiques de vote pour un signalement
Route::get('/votes/{signalement_id}', [VoteController::class, 'getVotes']);
// Afficher les signalements triés par nombre de votes "pour"
Route::get('/signalements/priorises', [VoteController::class, 'signalementsPriorises']);
Route::get('/signalements/urgents', [VoteController::class, 'signalementsPrioritaires']);
Route::get('/signalements/{id}/commentaires', [CommentaireController::class, 'index']);
// Route::post('/categories',[CategorieController::class,'index']);
Route::middleware('jwt')->group(function(){
Route::post('/logout',[AuthController::class,'logout']);
Route::get('/user',[AuthController::class,'getUser']);
Route::post('/refresh',[AuthController::class,'UpdateUser']);
Route::post('/signalements',[SignalementController::class,'store']);
Route::post('/signalements/{id}/statut', [SignalementController::class, 'updateStatus']);
Route::delete('/categorie/{categorie}', [CategorieController::class, 'destroy']);
Route::delete('/signalement/{signalement}', [SignalementController::class, 'destroy']);
Route::post('/signalement/{id}/modifier/', [SignalementController::class, 'update']);
Route::get('/signalements/statistiques', [SignalementController::class, 'statistiques']);
Route::get('/notifications', [NotificationController::class, 'index']); // toutes
Route::get('/notifications/unread', [NotificationController::class, 'unread']); // non lues
Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']); // marquer comme lue
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
Route::post('/solution',[ResolutionController::class,'store']);
Route::post('/resolutions/{id}/changer-statut', [ResolutionController::class, 'changementStatutpro']);
Route::post('/votes', [VoteController::class, 'store']);
Route::post('/commentaires', [CommentaireController::class, 'store']);

});