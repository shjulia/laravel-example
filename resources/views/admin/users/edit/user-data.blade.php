@extends('admin.users.edit.edit')
@section('edit-content')
    <form method="POST" action="{{ route('admin.users.edit.userData', $user) }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}" />
        <div class="row">
            <div class="col-md-4">
                <Cinput
                    label="First Name"
                    id="first_name"
                    type="text"
                    name="first_name"
                    value="{{ old('first_name', $user->first_name) }}"
                    has-errors="{{ $errors->has('first_name') }}"
                    first-error="{{ $errors->first('first_name') }}"
                    :required="false"
                    :is-mat="true"
                    :prepend="true"
                    prepend-icon="user-o"
                ></Cinput>
            </div>
            <div class="col-md-4">
                <Cinput
                    label="Last Name"
                    id="last_name"
                    type="text"
                    name="last_name"
                    value="{{ old('last_name', $user->last_name) }}"
                    has-errors="{{ $errors->has('last_name') }}"
                    first-error="{{ $errors->first('last_name') }}"
                    :required="false"
                    :is-mat="true"
                    :prepend="true"
                    prepend-icon="user-o"
                ></Cinput>
            </div>
            <div class="col-md-4">
                <Cinput
                    label="E-mail Address"
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    has-errors="{{ $errors->has('email') }}"
                    first-error="{{ $errors->first('email') }}"
                    :required="false"
                    :is-mat="true"
                    :prepend="true"
                    prepend-icon="envelope-o"
                    @blur-input="blurEmail()"
                ></Cinput>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <Cinput
                    label="Mobile Phone"
                    id="phone"
                    type="tel"
                    name="phone"
                    value="{{ old('phone', $user->phone ?: ' ') }}"
                    has-errors="{{ $errors->has('phone') }}"
                    first-error="{{ $errors->first('phone') }}"
                    :required="false"
                    :is-mat="true"
                    :prepend="true"
                    prepend-icon="mobile big"
                    mask="(###) ###-####"
                    :number-input="true"
                    autocomplete="off"
                ></Cinput>
            </div>
            <div class="col-md-6">
                <Cinput
                    label="Password (hidden)"
                    id="password"
                    type="password"
                    name="password"
                    value="{{ old('password') }}"
                    has-errors="{{ $errors->has('password') }}"
                    first-error="{{ $errors->first('password') }}"
                    :required="false"
                    :is-mat="true"
                    :prepend="true"
                    prepend-icon="lock"
                    autocomplete="off"
                ></Cinput>
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn form-button">Edit</button>
        </div>
    </form>
@endsection
