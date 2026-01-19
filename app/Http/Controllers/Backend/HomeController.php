<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function AllFeature()
    {
        $feature = Feature::latest()->get();
        return view('admin.backend.feature.all_feature', compact('feature'));
    }

    public function AddFeature()
    {
        return view('admin.backend.feature.add_feature');
    }

    public function StoreFeature(Request $request)
    {
        Feature::create([
                'title' => $request->title,
                'icon' => $request->icon,
                'description' => $request->description,
            ]);

        $notification = array(
            'message' => 'Feature Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.feature')->with($notification);
    }
}
