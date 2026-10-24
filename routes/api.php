use App\Http\Controllers\Api\PlacementController;

Route::prefix('placement')->group(function () {
Route::post('/students/{studentId}/evaluate', [PlacementController::class, 'evaluate']);
Route::get('/list', [PlacementController::class, 'list']);
Route::post('/{placementId}/apply', [PlacementController::class, 'apply']);
});