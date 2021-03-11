<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class SprintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function create(Project $project)
    {
        Project::findOrFail($project->id);
        $this->authorize('create', [Sprint::class, $project]);
        return view('sprint.create', ['id' => $project->id]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Project $project)
    {
        Project::findOrFail($project->id);
        $this->authorize('create', [Sprint::class, $project]);
        $request->request->add(['project_id' => $project->id]);

        $start_date = $request->request->get('start_date');
        $end_date = $request->request->get('end_date');

        $data = $request->validate([
            'project_id' => ['required', 'numeric', 'min:0'],
            'speed' => 'required|numeric|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => ['required','date','after_or_equal:start_date', function ($attribute, $value, $fail) use ($end_date, $start_date) {
                $overlaps = Sprint::query()->whereBetween('start_date', [$start_date, $end_date])
                    ->orWhereBetween('end_date', [$start_date, $end_date])
                    ->orWhereRaw('? BETWEEN start_date and end_date', [$start_date])
                    ->orWhereRaw('? BETWEEN start_date and end_date', [$end_date]);

                if ($overlaps) {
                    $fail('The sprint overlaps with an existing sprint');
                }
            }]
        ]);

        Sprint::create($data);

        return redirect()->route('project.show', $project->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Sprint  $sprint
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project, Sprint $sprint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Sprint  $sprint
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project, Sprint $sprint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Sprint  $sprint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project, Sprint $sprint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @param  \App\Models\Sprint  $sprint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project, Sprint $sprint)
    {
        //
    }
}
