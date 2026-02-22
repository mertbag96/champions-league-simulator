<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamStoreRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $teams = Team::query()
            ->orderBy('id')
            ->get();

        return Inertia::render('Teams/Index', [
            'teams' => $teams,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Teams/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamStoreRequest $request): RedirectResponse
    {
        Team::create($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Team was successfully created!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team): Response
    {
        return Inertia::render('Teams/Edit', [
            'team' => $team,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamUpdateRequest $request, Team $team): RedirectResponse
    {
        $team->update($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Team was successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();

        return redirect()
            ->back()
            ->with('success', 'Team was successfully deleted.');
    }
}
