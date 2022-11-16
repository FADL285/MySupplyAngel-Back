<?php

namespace App\Http\Controllers\Api\WebSite\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function getAbout()
    {
        $about = app()->getLocale() == 'ar' ? 'about_ar' : 'about_en';
        $data = ['about' => setting($about) != false ? setting($about) : ''];
        return response()->json(['status' => true, 'data' => $data, 'message' => '']);
    }

    public function getWhyUs()
    {
        $why_us = app()->getLocale() == 'ar' ? 'why_us_ar' : 'why_us_en';
        $data = ['why_us' => setting($why_us) != false ? setting($why_us) : ''];
        return response()->json(['status' => true, 'data' => $data, 'message' => '']);
    }

    public function getTerms()
    {
        $terms = app()->getLocale() == 'ar' ? 'terms_ar' : 'terms_en';
        $data = ['terms' => setting($terms) != false ? setting($terms) : ''];
        return response()->json(['status' => true, 'data' => $data, 'message' => '']);
    }

    public function getPrivacy()
    {
        $privacy = app()->getLocale() == 'ar' ? 'privacy_ar' : 'privacy_en';
        $data = ['privacy' => setting($privacy) != false ? setting($privacy) : ''];
        return response()->json(['status' => true, 'data' => $data, 'message' => '']);
    }
}
