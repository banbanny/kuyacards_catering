use App\Http\Controllers\AuthController;

Route::get('/auth', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signup']);
