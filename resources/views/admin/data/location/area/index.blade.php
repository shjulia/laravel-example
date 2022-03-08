@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Areas</div>
                    <div class="card-body">
                        <div class="d-flex flex-row mb-3">
                            <a href="{{ route('admin.data.location.state.show', [$state]) }}" class="btn btn-success mr-1">Back</a>
                            <a href="{{ route('admin.data.location.area.create', $state) }}" class="btn btn-primary">Create Area</a>

                        </div>

                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Tier</th>
                                <th>Open</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($areas as $area)
                                <tr>
                                    <td><a href="{{ route('admin.data.location.area.edit', [$state, $area]) }}">{{ $area->name }}</a></td>
                                    <td>{{ $area->tier }}</td>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="is_open" @if($area->is_open) checked @endif>
                                            <label class="custom-control-label" for="customCheck1">Open</label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        {{ $areas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
