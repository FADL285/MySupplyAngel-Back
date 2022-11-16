<?php

namespace App\Http\Controllers\Api\WebSite\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\WebSite\Category\CategoryResource;
use App\Http\Resources\Api\WebSite\OurClient\OurClientResource;
use App\Http\Resources\Api\WebSite\OurServices\OurServicesResource;
use App\Http\Resources\Api\WebSite\Tender\TenderResource;
use App\Models\Category;
use App\Models\MyClient;
use App\Models\OurServices;
use App\Models\Tender;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tenders = Tender::where('status', 'admin_accept')->where('expiry_date', '>', now())->latest()->take(5)->get();
        $categories = Category::latest()->take(5)->get();
        $our_services = OurServices::all();
        $our_clients = MyClient::all();

        return response()->json([
            'status'  => true,
            'data'    => [
                'tenders'      => TenderResource::collection($tenders),
                'categories'   => CategoryResource::collection($categories),
                'our_services' => OurServicesResource::collection($our_services),
                'our_clients'  => OurClientResource::collection($our_clients),
            ],
            'message' => ''
        ]);
    }
}
