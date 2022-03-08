@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Create terms and conditions</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.data.terms.store') }}">
                            @csrf

                            <div class="form-group">
                                <label for="summernote" class="col-form-label">Text</label>
                                <textarea id="summernote" class="form-control{{ $errors->has('text') ? ' is-invalid' : '' }}" name="text" required>
                                    {{ old('text', $term ? $term->text : '') }}
                                </textarea>
                                @if ($errors->has('text'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('text') }}</strong></span>
                                @endif
                            </div>


                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
