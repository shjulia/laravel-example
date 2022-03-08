<form method="POST" action="{{ $fromAction }}" >
    @csrf
    <tool-select
        inline-template
        :tools="{{ $tools }}"
        init-active="{{ $user->practice->tool_id }}"
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

    <div class="form-group row">
        @if($showLinks)
            <div class="col-6 col-sm-6">
                <a href="{{ route('practice.details.locations') }}" @click="$loading.show()" class="btn form-button skip-button">Skip</a>
            </div>
        @endif
        <div class="col-6 col-sm-6">
            <button type="submit" class="btn form-button" @click="$loading.show()">Continue</button>
        </div>
    </div>
</form>
