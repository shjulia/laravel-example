@extends('admin.users.edit.edit')
@section('edit-content')
    <form method="POST" action="{{ route('admin.users.edit.check', $user) }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}" />
        <div class="row">
            <div class="col-md-12">
                <Cinput
                    label="Social Security Number"
                    id="ssn"
                    type="tel"
                    name="ssn"
                    value="{{ old('ssn', $user->specialist->ssn) }}"
                    has-errors="{{ $errors->has('ssn') }}"
                    first-error="{{ $errors->first('ssn') }}"
                    mask="###-##-####"
                    :number-input="true"
                    :required="false"
                    :is-mat="true"
                ></Cinput>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn form-button">Edit</button>
        </div>
    </form>
@endsection
