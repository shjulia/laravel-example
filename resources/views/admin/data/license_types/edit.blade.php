@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <license-types
                        inline-template
                        :states="{{ $states }}"
                        :old="{{ old() ? collect(old()) : collect($licenseTypeArray) }}"
                        :errors="{{ $errors }}"
                        :positions="{{ $positions }}"
                        :init-rows-count="{{ count(old('position', $licenseTypeArray['position'] ?? [1])) }}"
                >
                    <div class="card">
                        <div class="card-header">Edit type {{ $licenseType->id }}</div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.data.license_types.update', $licenseType) }}">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="title" class="col-form-label">License type title</label>
                                            <input id="title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" value="{{ old('title', $licenseType->title) }}">
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
                                    <license-row
                                            :i="i"
                                            :init-position="val('position', i)"
                                            :init-states="arrVal('states', i)"
                                            :init-required="val('required', i)"
                                    ></license-row>
                                    <hr/>
                                </div>

                                <div class="form-group text-center">
                                    <button type="submit" @click="$loading.show()" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </license-types>
            </div>
        </div>
    </div>
@endsection