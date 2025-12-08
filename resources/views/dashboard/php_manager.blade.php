@extends('layouts.app')

@section('content')
<div style="padding: 1.5rem 0;">
    <div class="section-title" style="text-align: left; margin-bottom: 2rem;">
        <h3 class="section-subtitle">Configuration</h3>
        <h2 class="section-heading">PHP Environment</h2>
    </div>

    <style>
        .custom-checkbox input[type="checkbox"] {
            accent-color: #ffc107; 
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
    </style>

    <div class="white-card" style="padding: 2rem; border-radius: 20px; box-shadow: var(--shadow);">
        
        @if(session('success'))
            <div class="alert" style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('php.update') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 3rem;">
                <h3 style="margin-bottom: 1rem; color: var(--secondary); font-size: 1.2rem;">PHP Version</h3>
                <div style="max-width: 400px; position: relative;">
                    <i class="fas fa-code-branch" style="position: absolute; left: 15px; top: 15px; color: #999;"></i>
                    <select name="version" class="form-control" style="width: 100%; padding: 12px 15px 12px 45px; border: 1px solid #ddd; border-radius: 10px; font-size: 1rem; cursor: pointer; -webkit-appearance: none;">
                        @foreach($availableVersions as $ver)
                            <option value="{{ $ver }}" {{ $currentVersion == $ver ? 'selected' : '' }}>PHP {{ $ver }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down" style="position: absolute; right: 15px; top: 15px; color: #999; pointer-events: none;"></i>
                </div>
                <p style="color: #999; font-size: 0.9rem; margin-top: 10px;">Select the PHP version best suited for your application.</p>
            </div>

            <div style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1rem; color: var(--secondary); font-size: 1.2rem;">PHP Extensions</h3>
                <div class="custom-checkbox" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
                    @foreach($extensions as $ext => $enabled)
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 12px; border: 1px solid #eee; border-radius: 12px; transition: all 0.3s;" 
                           onmouseover="this.style.borderColor='var(--primary)'; this.style.background='#fcfcfc';" 
                           onmouseout="this.style.borderColor='#eee'; this.style.background='transparent';">
                        <input type="checkbox" name="extensions[]" value="{{ $ext }}" {{ $enabled ? 'checked' : '' }}>
                        <span style="font-weight: 500; color: #555;">{{ $ext }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <hr style="border: none; border-top: 1px solid #eee; margin: 2rem 0;">

            <div style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 30px; border-radius: 50px; font-weight: 600;">
                    <i class="fas fa-save" style="margin-right: 8px;"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
