<?php

namespace App\Http\Controllers\Site\Planner;

use Illuminate\Http\Request;

use App\Models\Site\Planner\Task;
use App\Http\Requests;
use App\Http\Requests\Site\Planner\TaskRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller {

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        if ($request->ajax()) {
            return Task::where('trade_id', '=', $id)->where('company_id', Auth::user()->company_id)->get();
        }

        return view('errors/404');
    }

    /**
     * Store a newly created resource in storage via ajax.
     */
    public function store(TaskRequest $request)
    {
        if ($request->ajax()) {
            return Task::create($request->all());
        }

        return view('errors/404');
    }

    /**
     * Update the specified resource in storage via ajax.
     */
    public function update(TaskRequest $request, $id)
    {
        if ($request->ajax()) {
            $task = Task::findOrFail($id);
            $task->update($request->all());

            return $task;
        }

        return view('errors/404');
    }
}