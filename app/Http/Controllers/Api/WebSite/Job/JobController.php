<?php

namespace App\Http\Controllers\Api\WebSite\Job;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebSite\Job\JobRequest;
use App\Http\Resources\Api\WebSite\Employee\EmployeeResource;
use App\Http\Resources\Api\WebSite\Employee\SimpleEmployeeResource;
use App\Http\Resources\Api\WebSite\Job\JobResource;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;

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

    public function employees(Request $request)
    {
        $employees = User::where(['user_type' => 'client', 'is_need_job' => 1])
        ->whereHas('profile', function($query) use($request) {
            $query->when($request->country_id, function($query) use($request){
                $query->where('country_id', $request->country_id);
            })->when($request->city_id, function($query) use($request) {
                $query->where('city_id', $request->city_id);
            });
        })->when($request->keyword, function($query) use($request){
            $query->where(function($query) use($request){
                $query->where('fullname', 'like', '%' . $request->keyword . '%')
                ->orWhere('email', 'like', '%' . $request->keyword . '%')
                ->orWhere('phone', 'like', '%' . $request->keyword . '%');
            });
        })->when($request->start_date && $request->end_date, function($query) use($request){
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        })->latest()->paginate(10);

        return SimpleEmployeeResource::collection($employees)->additional(['status' => true, 'message' => '']);
    }

    public function showEmployee($id)
    {
        $employee = User::where(['user_type' => 'client', 'is_need_job' => 1])->findOrFail($id);
        return EmployeeResource::make($employee)->additional(['status' => true, 'message' => '']);
    }

    public function needJob()
    {
        if(auth('api')->check())
        {
            $user = auth('api')->user();
            $user->update(['is_need_job' => ! $user->is_need_job]);
            return response()->json(['status' => true, 'data' => ['is_need_job' => $user->fresh()->is_need_job], 'message' => trans('website.update.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.update.fail')], 422);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(JobRequest $request)
    {
        $job = Job::create($request->validated() + ['user_id' => auth('api')->id()]);
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

    public function myJobs(Request $request)
    {
        $jobs = Job::where('user_id', auth('api')->id())->when($request->country_id, function ($query) use ($request) {
            $query->where('country_id', $request->country_id);
        })->when($request->city_id, function ($query) use ($request) {
            $query->where('city_id', $request->city_id);
        })->get();

        return JobResource::collection($jobs)->additional(['status' => true, 'message' => '']);
    }

    public function showMyJob(Request $id)
    {
        $jobs = Job::where('user_id', auth('api')->id())->findOrFail($id);

        return JobResource::collection($jobs)->additional(['status' => true, 'message' => '']);
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
        $job = Job::where('user_id', auth('api')->id())->findOrFail($id);
        $job->update($request->validate());

        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.update.successfully')]);
    }

    public function applayOnJob($id)
    {
        $job  = Job::where('status', 'admin_accept')->findOrFail($id);

        if (auth('api')->check())
        {
            $job->users()->create(['user_id' => auth('api')->id()]);
        }

        return response()->json(['status' => true, 'data' => null, 'message' => '']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $job = Job::where('user_id', auth('api')->id())->findOrFail($id);
        if ($job->delete()) {
            return response()->json(['status' => true, 'data' => null, 'messages' => trans('dashboard.delete.successfully')]);
        }
        return response()->json(['status' => false, 'data' => null, 'messages' => trans('dashboard.delete.fail')], 422);
    }
}
