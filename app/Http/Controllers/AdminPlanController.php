<?php

namespace App\Http\Controllers;

use App\Models\HostingPlan;
use Illuminate\Http\Request;

class AdminPlanController extends Controller
{
    public function index()
    {
        $plans = HostingPlan::all();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'features' => 'nullable|json',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after:discount_start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        
        // Handle features input
        if ($request->has('features_input')) {
            $featuresArray = array_filter(array_map('trim', explode("\n", $request->input('features_input'))));
            $data['features'] = array_values($featuresArray);
        }

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }
        
        HostingPlan::create($data);

        return redirect()->route('admin.plans.index')->with('success', 'Hosting Plan created successfully.');
    }

    public function edit(HostingPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, HostingPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'features' => 'nullable|json',
            'discount_price' => 'nullable|numeric|min:0',
            'discount_start_date' => 'nullable|date',
            'discount_end_date' => 'nullable|date|after:discount_start_date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->has('features_input')) {
            $featuresArray = array_filter(array_map('trim', explode("\n", $request->input('features_input'))));
            $data['features'] = array_values($featuresArray);
        }

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($plan->image && file_exists(public_path('images/' . $plan->image))) {
                unlink(public_path('images/' . $plan->image));
            }
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $data['image'] = $imageName;
        }

        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('success', 'Hosting Plan updated successfully.');
    }

    public function destroy(HostingPlan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', 'Hosting Plan deleted successfully.');
    }
}
