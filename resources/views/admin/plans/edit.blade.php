@extends('admin.layout')

@section('title', 'Edit Hosting Plan')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.plans.index') }}" class="text-blue-600 hover:text-blue-800">&larr; Back to Plans</a>
</div>

<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-bold mb-4">Edit Plan: {{ $plan->name }}</h2>
    
    <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Plan Name</label>
            <input type="text" name="name" id="name" value="{{ $plan->name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
            <textarea name="description" id="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3">{{ $plan->description }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="price">Price ($)</label>
            <input type="number" step="0.01" name="price" id="price" value="{{ $plan->price }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="features_input">Features (One per line)</label>
            <textarea name="features_input" id="features_input" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="5">{{ is_array($plan->features) ? implode("\n", $plan->features) : '' }}</textarea>
        </div>

        <div class="border-t border-gray-200 pt-4 mt-4">
            <h3 class="text-lg font-semibold mb-2 text-gray-700">Discount Settings (Optional)</h3>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="discount_price">Discount Price ($)</label>
                <input type="number" step="0.01" name="discount_price" id="discount_price" value="{{ $plan->discount_price }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex gap-4">
                <div class="mb-4 w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="discount_start_date">Start Date</label>
                    <input type="datetime-local" name="discount_start_date" id="discount_start_date" value="{{ $plan->discount_start_date ? $plan->discount_start_date->format('Y-m-d\TH:i') : '' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4 w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="discount_end_date">End Date</label>
                    <input type="datetime-local" name="discount_end_date" id="discount_end_date" value="{{ $plan->discount_end_date ? $plan->discount_end_date->format('Y-m-d\TH:i') : '' }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Update Plan
            </button>
        </div>
    </form>
</div>
@endsection
