<?php

namespace App\Http\Controllers\LiveClasses;

use App\Http\Controllers\Controller;
use App\Models\LiveClasses\LiveClass;
use App\Models\LiveClasses\LiveSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LiveSessionController extends Controller
{
    public function index(Request $request)
    {
        $liveSessions = LiveSession::select('id','live_class_id', 'date', 'timing', 'link', 'description', 'note', 'active')
            ->with('liveClass:id,title')
            ->when($request->search, function ($query, $search){
                $query->where('timing', 'like', '%'. $search . '%');
                $query->orWhere('date', 'like', '%'. $search . '%');
                $query->orWhere('link', 'like', '%'. $search . '%');
                $query->orWhere('description', 'like', '%'. $search . '%');
                $query->orWhere('note', 'like', '%'. $search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString()
            ;
        return Inertia::render('Live/Session/Index' , compact('liveSessions'));
    }

    public function create()
    {
        $liveClasses = LiveClass::whereActive(1)->get();
        return Inertia::render('Live/Session/Create', compact('liveClasses'));
    }

    public function store(Request $request)
    {
        $this->validateFull($request);
        \DB::transaction(function() use ($request) {
            LiveSession::create($request->only('live_class_id', 'date', 'timing', 'link', 'description', 'note', 'active'));
        });
        return redirect(route('liveSession.index'))->with('type', 'success')->with('message', 'Live session added successfully !!');
    }

    public function edit(LiveSession $liveSession)
    {
        $liveClasses = LiveClass::whereActive(1)->get();
        return Inertia::render('Live/Session/Create', compact('liveClasses', 'liveSession',));
    }

    public function update(Request $request, LiveSession $liveSession)
    {
        $this->validateFull($request);
        $liveSession->update($request->only('live_class_id', 'date', 'timing', 'link', 'description', 'note', 'active'));
        return redirect(route('liveSession.index'))->with('type', 'success')->with('message', 'Live session updated successfully !!');
    }

    private function validateFull($request)
    {
        $request->validate(
            [
                'live_class_id' => 'required',
                'date' => 'required|date',
                'timing' => 'required',
                'link' => 'required|url',

            ],
            [
                'live_class_id.required' => 'Live class field is required.',
                'date.required' => 'Date field is required.',
                'timing.required' => 'Timing field is required.',
                'link.required' => 'Session Link field is required.',
                'link.url' => 'Session Link should be a valid url.',
            ]
        );
    }
}
