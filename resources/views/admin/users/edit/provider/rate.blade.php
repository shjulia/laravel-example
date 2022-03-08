@extends('admin.users.edit.edit')
@section('edit-content')
    <form method="POST" action="{{ route('admin.users.edit.rate', $user) }}">
        @csrf
        <div class="form-group">
            <Cinput
                label="Rate"
                id="rate"
                type="text"
                name="rate"
                value="{{ old('rate', $user->specialist->min_rate ?: $positionRate) }}"
                has-errors="{{ $errors->has('rate') }}"
                first-error="{{ $errors->first('rate') }}"
            ></Cinput>
        </div>

        <div class="form-group">
            <button type="submit" class="btn form-button">Edit</button>
        </div>
    </form>
@endsection
