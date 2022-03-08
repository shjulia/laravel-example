@extends('layouts.onboarding', ['h2' => "What Practice Management software have you used in the past?"])
@section('content')
    <form method="POST" action="{{ route('provider.onboarding.toolSave') }}">
        @csrf
        <div class="onboarding-cont" style="width: 316px; margin: auto;">
            <tool-select
                inline-template
                :tools="{{ $tools }}"
                init-active="{{ $user->specialist->tool_id }}"
                v-cloak
            >
                <div class="form-group mat mt-5 text-center">
                    <label></label>
                    <div
                        v-for="tool in tools"
                        class="position"
                    >
                        <label
                            :for="tool.id"
                            :class="{'active': tool.id == active}"
                            @click="setActive(tool.id)"
                        >
                            @{{ tool.title }}
                        </label>
                        <input
                            :id="tool.id"
                            type="radio"
                            name="tool"
                            :value="tool.id"
                        >
                    </div>
                    <div class="position">
                        <label
                            :for="'other'"
                            :class="{'active': 'other' == active}"
                            @click="setOther('other')"
                        >
                            Other
                        </label>
                        <input
                            :id="'other'"
                            type="radio"
                            name="tool"
                            value=""
                        >
                    </div>
                    @if ($errors->has('tool'))
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('tool') }}</strong>
                    </span>
                    @endif
                </div>
            </tool-select>
        </div>
        <div class="text-center continue-butt">
            <button type="submit" class="btn btn-bg-grad">Continue</button>
            <br/>
            <a href="{{ route('provider.onboarding.tasks') }}" class="later">Add Later</a>
        </div>
        @include('register.provider.onboarding._progress', ['percent' => 57])
    </form>
@endsection
