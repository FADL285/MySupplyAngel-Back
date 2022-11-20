<?php

namespace App\Http\Controllers\Api\Dashboard\Job;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Dashboard\ChangeStatus\ChangeStatusRequest;
use App\Http\Requests\Api\Dashboard\Job\JobChangeRequest;
use App\Http\Requests\Api\Dashboard\Job\JobRequest;
use App\Http\Resources\Api\Dashboard\Job\JobResource;
use App\Models\Job;
use App\Notifications\Dashboard\ChangeStatus\AdminChangeStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $jobs = Job::when($request->country_id, function ($query) use ($request) {
            $query->where('country_id', $request->country_id);
        })->when($request->city_id, function ($query) use ($request) {
            $query->where('city_id', $request->city_id);
        })->get();

        return JobResource::collection($jobs)->additional(['status' => true, 'message' => '']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JobRequest $request)
    {
        $job = Job::create($request->validated() + ['status' => 'admin_accept']);
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.create.successfully')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $job = Job::findOrFail($id);

        return (new JobResource($job))->additional(['status' => true, 'message' => '']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(JobRequest $request, $id)
    {
        $job = Job::findOrFail($id);
        $job->update($request->validate());

        return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.update.successfully')]);
    }

    public function changeStatus(ChangeStatusRequest $request, Job $job)
    {
        $job->update(['status' => $request->status]);
        if ($job->user)
        {
            Notification::send($job->user, new AdminChangeStatusNotification($job->id, 'job', $request->status, ['database', 'broadcast']));
        }
        return response()->json(['status' => true, 'data' => null, 'message' => trans('dashboard.update.successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        if ($job->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
