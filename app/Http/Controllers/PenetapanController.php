<?php

namespace App\Http\Controllers;

use App\Models\Standar;
use Illuminate\Http\Request;

class PenetapanController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        // return nested json standar -> indikator -> target
        // dengan 'indikator.target' sebagai indikator dari nama method class standar
        // dan target dari indikator
        return Standar::with('indikator.target')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        // pending
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id) {
        //
        return Standar::with('indikator.target')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {
        // pending
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {
        // pending
    }
}