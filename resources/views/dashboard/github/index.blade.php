@extends('layouts.app')

@section('content')
<div class="container" style="padding: 4rem 0;">
    <div class="section-title" style="text-align: left; margin-bottom: 2rem;">
        <h3 class="section-subtitle">DevOps</h3>
        <h2 class="section-heading">GitHub Integration</h2>
    </div>

    <div class="white-card" style="padding: 2rem; border-radius: 20px; box-shadow: var(--shadow);">
        @if(session('success'))
            <div style="padding: 1rem; background: #e8f5e9; color: #2e7d32; border-radius: 5px; margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
            <!-- Settings Form -->
            <div style="flex: 1; min-width: 300px;">
                <h3 style="margin-bottom: 1.5rem; color: var(--secondary);">Configuration</h3>
                
                <form action="{{ route('github.update') }}" method="POST">
                    @csrf
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #555;">Repository SSH URL</label>
                        <input type="text" name="repo_url" class="form-control" 
                               value="{{ $setting->repo_url ?? '' }}" 
                               placeholder="git@github.com:username/repository.git" 
                               style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;" required>
                        <small style="color: #999;">Use the SSH URL from GitHub.</small>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; color: #555;">Branch</label>
                        <input type="text" name="branch" class="form-control" 
                               value="{{ $setting->branch ?? 'main' }}" 
                               placeholder="main" 
                               style="width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px;" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; border-radius: 50px;">Save & Generate Keys</button>
                    <p style="margin-top: 1rem; color: #777; font-size: 0.9rem;">
                        Saving will generate a unique SSH Key Pair for this project if one doesn't exist.
                    </p>
                </form>
            </div>

            <!-- Deployment Details -->
            <div style="flex: 1; min-width: 300px; background: #f8f9fa; padding: 1.5rem; border-radius: 10px;">
                <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Integration Steps</h3>

                @if($setting && $setting->public_key)
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="margin-bottom: 0.5rem; font-size: 1rem;">1. Add Deploy Key to GitHub</h4>
                        <p style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">
                            Go to your Repo Settings > Deploy Keys > Add Deploy Key. Paste this Public Key:
                        </p>
                        <textarea readonly style="width: 100%; height: 100px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; font-family: monospace; font-size: 0.8rem; background: #fff;">{{ $setting->public_key }}</textarea>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="margin-bottom: 0.5rem; font-size: 1rem;">2. Setup Webhook</h4>
                        <p style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">
                            Go to Repo Settings > Webhooks > Add Webhook.
                        </p>
                        <div style="background: #fff; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 0.5rem;">
                            <strong style="display: block; font-size: 0.8rem; color: #999;">Payload URL:</strong>
                            <code style="word-break: break-all; color: #d63384;">{{ route('github.webhook', ['secret' => $setting->webhook_secret]) }}</code>
                        </div>
                        <div style="background: #fff; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                            <strong style="display: block; font-size: 0.8rem; color: #999;">Content type:</strong>
                            <code>application/json</code>
                        </div>
                    </div>
                @else
                    <div style="text-align: center; padding: 2rem; color: #999;">
                        <i class="fas fa-key fa-2x" style="margin-bottom: 1rem; color: #ccc;"></i>
                        <p>Configure repository settings to generate integration keys.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
