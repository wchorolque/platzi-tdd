<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;

use App\Http\Requests\RepositoryRequest;

class RepositoryController extends Controller
{
    public function index(Request $request)
    {
        return view('repositories.index', [
            'repositories' => $request->user()->repositories
        ]);
    }

    public function show(Request $request, Repository $repository)
    {
        $this->authorize('pass', $repository);

        return view('repositories.show', compact('repository'));
    }

    public function edit(Request $request, Repository $repository)
    {
        $this->authorize('pass', $repository);

        return view('repositories.edit', compact('repository'));
    }

    public function create()
    {
        return view('repositories.create');
    }

    public function store(RepositoryRequest $request)
    {
        $request->user()->repositories()->create($request->all());

        return redirect()->route('repositories.index');
    }

    public function update(RepositoryRequest $request, Repository $repository)
    {
        $this->authorize('pass', $repository);

        $repository->update($request->all());

        return redirect()->route('repositories.edit', $repository);
    }

    public function destroy(Request $request, Repository $repository)
    {
        $this->authorize('pass', $repository);

        $repository->delete();

        return redirect()->route('repositories.index');
    }
}
