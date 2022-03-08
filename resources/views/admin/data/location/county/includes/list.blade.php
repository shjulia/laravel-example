<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Counties</div>
                <div class="card-body">
                    <form action="?" method="GET">
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <label for="county" class="col-form-label">Name</label>
                                    <input id="county" class="form-control" name="county" value="{{ request('county') }}">
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
                            <th>Title</th>
                            <th>Tier</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($counties as $county)
                            <tr>
                                <td><a href="{{ route('admin.data.location.county.show', [$state, $county]) }}">{{ $county->name }}</a></td>
                                <td>{{ $county->tier }}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $counties->links() }}
                </div>
            </div>
        </div>
    </div>
</div>