@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'A Bit More Info',
                    'desc' => 'Please help us get to know what you do a little better.'
                ])

                <partner-details
                        inline-template
                        :user="{{ $user }}"
                        :descriptions="{{ collect($descriptions) }}"
                        v-cloak
                >
                    <form method="POST" action="{{ route('base.signup.details', ['code' => $user->tmp_token]) }}">
                        @csrf

                        <div class="form-group mat">
                            <label for="description">What best describes you?</label>
                            <select
                                    id="description"
                                    class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
                                    name="description"
                                    v-model="description"
                                    ref="select2_description"
                                    v-select2
                                    @change="change()"
                            >
                                @foreach ($descriptions as $key => $val)
                                    <option value="{{ $key }}" @if($key == old('description')) selected @endif>
                                        {{ $val['title'] }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('description'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group" v-if="description == 'provider'">
                            <div class="custom-control custom-checkbox">
                                <input class="form-check-input custom-control-input" type="checkbox" name="description_answer" id="description_answer" v-model="description_answer" value="1">
                                <label class="custom-control-label" for="description_answer">Would you like to register as a Provider so you can be hired to work?</label>
                            </div>
                        </div>
                        <div class="form-group" v-if="description == 'practice'">
                            <div class="custom-control custom-checkbox">
                                <input class="form-check-input custom-control-input" type="checkbox" name="description_answer" id="description_answer" v-model="description_answer" value="1">
                                <label class="custom-control-label" for="description_answer">Would you like to register as a Practice so you can hire providers?</label>
                            </div>
                        </div>

                        <div class="form-group" v-if="description == 'sales'">
                            <div class="form-group mat">
                                <label for="description_answer">Who do you work with?</label>
                                <select
                                        id="description_answer"
                                        class="form-control"
                                        name="description_answer"
                                        v-model="description_answer"
                                        ref="select2_description_answer"
                                        v-select2
                                >
                                    <option
                                            v-for="person in descriptions['leader']['data']"
                                            :value="person"
                                    >
                                        @{{ person }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" v-if="description == 'other'">
                            <Cinput
                                    label="Other"
                                    id="description_answer"
                                    type="text"
                                    name="description_answer"
                                    :required="false"
                                    :is-mat="true"
                                    :init-model="description_answer"
                                    init-model-attr="description_answer"
                            ></Cinput>
                        </div>
                        <div class="form-group" v-if="description_answer == 'Other'">
                            <Cinput
                                    label="Other"
                                    id="description_answer2"
                                    type="text"
                                    name="description_answer2"
                                    :required="false"
                                    :is-mat="true"
                                    :init-model="description_answer2"
                                    init-model-attr="description_answer2"
                            ></Cinput>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn form-button">Continue</button>
                        </div>
                    </form>
                </partner-details>
            </div>
        </div>
    </div>
@endsection
