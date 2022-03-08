@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Select Position',
                    'desc' => 'Please enter your position Information.'
                ])

                @include("register.provider._stepper", [
                    'active' => 'industry'
                ])

                <register-step2
                        inline-template
                        :industries="{{ $industries }}"
                        :positions="{{ $positions }}"
                        :user="{{ $user }}"
                        v-cloak
                >
                    <form method="POST" action="{{ route('signup.industrySave', ['code' => $user->tmp_token]) }}">
                        @csrf

                        <div class="form-group mat" v-if="!user.specialist.industry_id">
                            <label for="industry">Select Industry</label>
                            <select
                                    id="industry"
                                    class="form-control{{ $errors->has('industry') ? ' is-invalid' : '' }}"
                                    name="industry"
                                    v-model="industry"
                                    ref="select2_industry"
                                    v-select2
                            >
                                @foreach ($industries as $industry)
                                    <option value="{{ $industry->id }}" @if($industry->id == old('industry', $user->specialist->industry_id)) selected @endif>
                                        {{ $industry->title }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($errors->has('industry'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('industry') }}</strong>
                                </span>
                            @endif
                        </div>
                        <input v-else name="industry" type="hidden" :value="industry">

                        <div class="form-group mat mt-5" v-show="industry" >
                            <label style="top:-32px;">Select Position</label>
                            <div
                                v-for="pos in filteredPositions"
                                class="position"
                            >
                                <label
                                    :for="pos.id"
                                    :class="{'active': pos.id == active}"
                                    @click="!isHasChildren(pos) ? setActive(pos.id): displayChildren(pos.id)"
                                >
                                    @{{ pos.title }}
                                </label>
                                <input
                                    :id="pos.id"
                                    type="radio"
                                    name="position"
                                    :value="pos.id"
                                    :disable="isHasChildren(pos)"
                                >
                                <div
                                    v-if="isHasChildren(pos) && isDisplayChildren(pos.id)"
                                >
                                    <div
                                        v-for="ch in pos.children"
                                        class="position sub"
                                    >
                                        <label
                                            :for="ch.id"
                                            :class="{'active': ch.id == active}"
                                            @click="setActive(ch.id)"
                                        >
                                            @{{ ch.title }}
                                        </label>
                                        <input
                                            :id="ch.id"
                                            type="radio"
                                            name="position"
                                            :value="ch.id"
                                        >
                                    </div>
                                </div>
                            </div>
                            @if ($errors->has('position'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('position') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn form-button" @click="$loading.show()">Continue</button>
                        </div>
                    </form>
                </register-step2>
            </div>
        </div>
    </div>
@endsection
