<a href="{{ $backUrl ?? url('/') }}" class="back" @click="$loading.show()"><i class="fa fa-chevron-left"></i> BACK</a>
<p class="sitename"><img src="{{ asset('/img/boon-logo.svg') }}"></p>
<h1 @if(isset($h1Class)) class="{{ $h1Class }}" @endif>{{ $h1 }}</h1>
<p class="h1_subtitle">{{ $desc }}</p>
