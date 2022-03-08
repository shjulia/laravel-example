<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">States</div>
                <div class="card-body">

                    <form action="?" method="GET">
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <label for="state" class="col-form-label">Title</label>
                                    <input id="state" class="form-control" name="state" value="{{ request('state') }}">
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
                            <th>Short title</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($states as $state)
                            <tr>
                                <td>{{ $state->id }}</td>
                                <td><a href="{{ route('admin.data.location.state.show', $state) }}">{{ $state->title }}</a></td>
                                <td>{{ $state->short_title }}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>