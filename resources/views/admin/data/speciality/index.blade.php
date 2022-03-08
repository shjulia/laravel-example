@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Specialities</div>
                    <div class="card-body">
                        <p><a href="{{ route('admin.data.specialities.create') }}" class="btn btn-success">Add Speciality</a></p>

                        <form action="?" method="GET">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label for="title" class="col-form-label">Speciality Title</label>
                                        <input id="title" class="form-control" name="title" value="{{ request('title') }}">
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label for="industry" class="col-form-label">Industry</label>
                                        <select id="industry" class="form-control" name="industry">
                                            <option value=""></option>
                                            @foreach ($industries as $industry)
                                                <option value="{{ $industry->id }}"{{ $industry->id == request('industry') ? ' selected' : '' }}>{{ $industry->title }}</option>
                                            @endforeach;
                                        </select>
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
                                <th>Industry</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($specialities as $speciality)
                                <tr>
                                    <td>{{ $speciality->id }}</td>
                                    <td><a href="{{ route('admin.data.specialities.show', $speciality) }}">{{ $speciality->title }}</a></td>
                                    <td>{{ $speciality->industry->title }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $specialities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
