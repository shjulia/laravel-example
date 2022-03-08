<form method="POST" action="{{ $fromAction }}" >
    @csrf

    <div class="form-group">
        <label for="park" class="col-form-label">Where should the provider park?</label>
        <textarea id="park" class="form-control{{ $errors->has('park') ? ' is-invalid' : '' }}" name="park" rows="2">{{ old('park', $user->practice->park) }}</textarea>
        @if ($errors->has('park'))
            <span class="invalid-feedback"><strong>{{ $errors->first('park') }}</strong></span>
        @endif
    </div>

    <div class="form-group">
        <label for="door" class="col-form-label">What door should the provider use?</label>
        <textarea id="door" class="form-control{{ $errors->has('door') ? ' is-invalid' : '' }}" name="door" rows="2">{{ old('door', $user->practice->door) }}</textarea>
        @if ($errors->has('door'))
            <span class="invalid-feedback"><strong>{{ $errors->first('door') }}</strong></span>
        @endif
    </div>

    <div class="form-group">
        <label for="dress_code" class="col-form-label">What is your Practice’s dress code policy?</label>
        <textarea id="dress_code" class="form-control{{ $errors->has('dress_code') ? ' is-invalid' : '' }}" name="dress_code" rows="2">{{ old('dress_code', $user->practice->dress_code) }}</textarea>
        @if ($errors->has('dress_code'))
            <span class="invalid-feedback"><strong>{{ $errors->first('dress_code') }}</strong></span>
        @endif
    </div>

    <div class="form-group">
        <label for="info" class="col-form-label">Is there anything else a provider should know?</label>
        <textarea id="info" class="form-control{{ $errors->has('info') ? ' is-invalid' : '' }}" name="info" rows="2">{{ old('info', $user->practice->info) }}</textarea>
        @if ($errors->has('info'))
            <span class="invalid-feedback"><strong>{{ $errors->first('info') }}</strong></span>
        @endif
    </div>

    <div class="form-group row">
        @if($showLinks)
        <div class="col-6 col-sm-6">
            <a href="{{ route('practice.details.tool') }}" @click="$loading.show()" class="btn form-button skip-button">Skip</a>
        </div>
        @endif
        <div class="col-6 col-sm-6">
            <button type="submit" class="btn form-button" @click="$loading.show()">Continue</button>
        </div>
    </div>
</form>
