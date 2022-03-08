@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Positions</div>
                    <div class="card-body">
                        <p><a href="{{ route('admin.data.positions.create') }}" class="btn btn-success">Add Position</a></p>

                        <form action="?" method="GET">
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="form-group">
                                        <label for="title" class="col-form-label">Position Title</label>
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
                                <th>Fee</th>
                                <th>Minimum Profit</th>
                                <th>Surge price</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($positions as $position)
                                <tr>
                                    <td>{{ $position->id }}</td>
                                    <td><a href="{{ route('admin.data.positions.show', $position) }}">{{ $position->title }}</a></td>
                                    <td>{{ $position->industry->title }}</td>
                                    <td>{{ $position->fee }}</td>
                                    <td>{{ $position->minimum_profit }}</td>
                                    <td>{{ $position->surge_price }}</td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $positions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
