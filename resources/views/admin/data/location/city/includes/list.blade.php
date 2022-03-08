<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Cities</div>
                <div class="card-body">

                    <form action="?" method="GET">
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="form-group">
                                    <label for="city" class="col-form-label">Name</label>
                                    <input id="city" class="form-control" name="city" value="{{ request('city') }}">
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
                            <th>Name</th>
                            <th>Tier</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($cities as $city)
                            <tr>
                                <td><a href="{{ route('admin.data.location.city.show', [$state, $city]) }}">{{ $city->name }}</a></td>
                                <td>{{ $city->tier }}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>

                    {{ $cities->links() }}
                </div>
            </div>
        </div>
    </div>
</div>