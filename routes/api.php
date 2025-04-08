Route::prefix('kali')->group(function () {
    Route::post('/setup', [KaliVMController::class, 'setupAttackEnvironment']);
    Route::post('/teardown/{vmid}', [KaliVMController::class, 'teardown']);
    Route::post('/create', [KaliVMController::class, 'create']);
    Route::post('/{vmid}/start', [KaliVMController::class, 'start']);
    Route::get('/{vmid}/vnc', [KaliVMController::class, 'getVNCConnection']);
}); 