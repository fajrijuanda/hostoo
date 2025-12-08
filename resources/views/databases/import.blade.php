@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 5rem; margin-bottom: 5rem;">
    <div class="row" style="justify-content: center;">
        <div class="col-md-8" style="max-width: 600px; width: 100%;">
            <div class="card" style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <h2 style="margin-bottom: 1.5rem;">Import SQL</h2>
                <p>Importing into database: <strong>{{ $database->name }}</strong></p>



                <form action="{{ route('databases.process-import', $database->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="sql_file" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Choose .sql File</label>
                        <input type="file" name="sql_file" id="sql_file" class="form-control" accept=".sql,.txt" required style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px;">
                        <small class="text-muted" style="display: block; margin-top: 5px;">Max file size: 10MB</small>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <a href="{{ route('databases.index') }}" class="btn btn-secondary" style="padding: 0.7rem 1.5rem; text-decoration: none; border: 1px solid #ddd; color: #333; border-radius: 5px; font-weight: 500;">Cancel</a>
                        <button type="submit" class="btn btn-primary" style="padding: 0.7rem 1.5rem; border: none; background: #17a2b8; color: white; border-radius: 5px; font-weight: 600; cursor: pointer;">
                            <i class="fas fa-upload"></i> Upload & Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
