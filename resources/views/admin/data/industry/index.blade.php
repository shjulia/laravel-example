@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Industries</div>
                    <div class="card-body">
                        <p><a href="{{ route('admin.data.industries.create') }}" class="btn btn-success">Add Industry</a></p>

                        <form action="?" method="GET">
                            <div class="row">
                                <div class="col-sm-10">
                                    <div class="form-group">
                                        <label for="industry" class="col-form-label">Industry Title</label>
                                        <input id="industry" class="form-control" name="industry" value="{{ request('industry') }}">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="col-form-label">&nbsp;</label><br />
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Alias</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($industries as $industry)
                                <tr>
                                    <td>{{ $industry->id }}</td>
                                    <td><a href="{{ route('admin.data.industries.show', $industry) }}">{{ $industry->title }}</a></td>
                                    <td>{{ $industry->alias }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $industries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
