<regions inline-template
         :saved-states="{{ isset($region) ? $region->states->pluck('id')->toJson() : '[]' }}"
         v-cloak>
    <form method="POST" action="{{ isset($region) ? route('admin.data.location.region.update', [$region]) : route('admin.data.location.region.store') }}">
        @csrf
        @isset($region) @method('PUT') @endif
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="name" class="col-form-label">Name</label>
                    <input id="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', $region->name ?? '') }}" required>
                    @if ($errors->has('name'))
                        <span class="invalid-feedback"><strong>{{ $errors->first('name') }}</strong></span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="cities" class="col-form-label">States</label>
                    <select multiple v-select2 v-model="states" name="states[]" class="select2 form-control{{ $errors->has('states') ? ' is-invalid' : '' }}" ref="states">
                        @foreach($states as $state)
                            <option value="{{ $state->id }}"> {{ $state->title }} </option>
                        @endforeach
                    </select>
                    @if ($errors->has('states'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('states') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</regions>

