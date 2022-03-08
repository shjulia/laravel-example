@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <rate
                    inline-template
                    :old="{{ collect(old())  }}"
                    :errors="{{ $errors }}"
                    :positions="{{ $positions }}"
                    :init-rows-count="{{ count(old('position', [1])) }}"
                >
                    <div class="card">
                        <div class="card-header">Create new rate</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.data.rates.store') }}">
                                @csrf

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="title" class="col-form-label">Rate title</label>
                                            <input id="title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title') }}">
                                            @if ($errors->has('title'))
                                                <span class="invalid-feedback"><strong>{{ $errors->first('title') }}</strong></span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <button class="btn btn-success" @click.prevent.stop="add()"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>

                                <div v-for="i in rowsCount">
                                    <rate-row
                                        :i="i"
                                        :init-position="val('position', i)"
                                        :init-rate="val('rate', i)"
                                        :init-minimum-profit="val('minimum_profit', i)"
                                        :init-surge-price="val('surge_price', i)"
                                        :init-max-day-rate="val('max_day_rate', i)"
                                    ></rate-row>
                                    <hr/>
                                </div>

                                <div class="form-group text-center">
                                    <button type="submit" @click="$loading.show()" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </rate>
            </div>
        </div>
    </div>
@endsection
