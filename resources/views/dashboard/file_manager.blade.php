@extends('layouts.app')

@section('content')
<section class="file-manager" style="padding: 4rem 0;">
    <style>
        /* Custom Checkbox Color to match folder icons */
        .file-manager input[type="checkbox"] {
            accent-color: #ffc107; 
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <div class="section-title" style="text-align: left; margin-bottom: 2rem;">
        <h3 class="section-subtitle">Management</h3>
        <h2 class="section-heading">File Manager</h2>
        <a href="{{ route('dashboard') }}" style="font-size: 0.9rem; color: var(--text-color);"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>

    <!-- Components -->
    @include('dashboard.partials.file-manager-toolbar')

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Hostoo.alert({title:'Success', text:"{{ session('success') }}", type:'success'});
            });
        </script>
    @endif
    @if(session('error'))
        <script>
             document.addEventListener('DOMContentLoaded', () => {
                Hostoo.alert({title:'Error', text:"{{ session('error') }}", type:'error'});
            });
        </script>
    @endif

    @include('dashboard.partials.file-manager-grid')
</section>

@include('dashboard.partials.file-manager-modals')
@include('dashboard.partials.file-manager-scripts')
@endsection


