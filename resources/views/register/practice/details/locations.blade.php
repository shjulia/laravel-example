@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('practice.details.tool') }}" @click="$loading.show()" class="back"><i class="fa fa-chevron-left"></i> BACK</a>
                        <h2 class="detailsh2">Locations</h2>
                        <locations
                            inline-template
                            autocomplete-action="{{ route('practice.signup.autocomplete') }}"
                            place-action="{{ route('practice.signup.placeData') }}"
                            :locations="{{ $locations }}"
                            create-action="{{ route('practice.details.locations.create') }}"
                            edit-action="{{ route('practice.details.locations.edit', '_') }}"
                            edit-current-action="{{ route('practice.details.locations.edit-current') }}"
                            :practice="{{ $practice }}"
                            v-cloak
                        >
                            <div>
                                <p>
                                    <b>Main address: </b>
                                    <span>{{ $practice->full_address }}</span>
                                    <button class="btn btn-link ml-1" @click="editCurrent()"><i class="fa fa-pencil"></i></button>
                                </p>
                                <button class="btn btn-primary" @click="create()">Add address</button>
                                <p></p>
                                <div v-if="isShowForm">

                                    <Cinput
                                        label="Practice Name"
                                        id="name"
                                        type="text"
                                        name="name"
                                        :init-model="name"
                                        init-model-attr="name"
                                        :has-errors="!!server_errors.name"
                                        :first-error="server_errors.name"
                                        :required="false"
                                        :is-mat="true"
                                        autocomplete="off"
                                        @blur-input="blurName()"
                                    ></Cinput>
                                    <div class="autocomplete" v-if="Object.keys(names).length">
                                        <div v-for="(value, key) in names">
                                            <a
                                                href="#"
                                                @click.prevent="selectPlace(key, value)"
                                                v-html="formatedName(value)"
                                            ></a>
                                            <hr/>
                                        </div>
                                    </div>

                                    <Cinput
                                        label="Address"
                                        id="address"
                                        type="text"
                                        name="address"
                                        :init-model="address"
                                        init-model-attr="address"
                                        :has-errors="!!server_errors.address"
                                        :first-error="server_errors.address"
                                        :required="false"
                                        :is-mat="true"
                                    ></Cinput>

                                    <Cinput
                                        label="City"
                                        id="city"
                                        type="text"
                                        name="city"
                                        :init-model="city"
                                        init-model-attr="city"
                                        :has-errors="!!server_errors.city"
                                        :first-error="server_errors.city"
                                        :required="false"
                                        :is-mat="true"
                                    ></Cinput>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group mat">
                                                <label for="state">State</label>
                                                <select
                                                    id="state"
                                                    class="form-control"
                                                    name="state"
                                                    v-model="state"
                                                    v-select2
                                                    ref="select2_state"
                                                >
                                                    <option></option>
                                                    @foreach ($states as $state)
                                                        <option value="{{ $state->short_title }}"
                                                        >{{ $state->title }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" v-if="server_errors.state">@{{ server_errors.state }}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <Cinput
                                                label="Zip"
                                                id="zip"
                                                type="tel"
                                                name="zip"
                                                :init-model="zip"
                                                init-model-attr="zip"
                                                :has-errors="!!server_errors.zip"
                                                :first-error="server_errors.zip"
                                                :required="false"
                                                :number-input="true"
                                                :is-mat="true"
                                                mask="#########"
                                            ></Cinput>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <Cinput
                                                label="Practice URL"
                                                id="url"
                                                type="text"
                                                name="url"
                                                :init-model="url"
                                                init-model-attr="url"
                                                :has-errors="!!server_errors.url"
                                                :first-error="server_errors.url"
                                                :required="false"
                                                :is-mat="true"
                                            ></Cinput>
                                        </div>
                                        <div class="col-md-6">
                                            <Cinput
                                                label="Practice Phone"
                                                id="phone"
                                                type="text"
                                                name="phone"
                                                :init-model="phone"
                                                init-model-attr="phone"
                                                :has-errors="!!server_errors.phone"
                                                :first-error="server_errors.phone"
                                                :required="false"
                                                :number-input="true"
                                                mask="(###) ###-####"
                                                :is-mat="true"
                                            ></Cinput>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success" @click="submit()">Save</button>
                                    </div>
                                </div>
                                @php $i = 1; @endphp
                                @foreach($locations as $location)
                                    <hr/>
                                    <div class="row">
                                        <div class="col-8">
                                            <b>Address {{ $i }}: </b>
                                            <span>{{ $location->full_address }}</span>
                                        </div>
                                        <div class="col-2">
                                            <button class="btn btn-link ml-1" @click="edit({{ $location->id }})"><i class="fa fa-pencil"></i></button>
                                        </div>
                                        <div class="col-2">
                                            <form class="form-inline" action="{{ route('practice.details.locations.delete', $location) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-link ml-2"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                    @php $i++; @endphp
                                @endforeach
                            </div>
                        </locations>
                        <div class="form-group row">
                            <div class="col-6 col-sm-6">
                                <a href="{{ route('practice.details.team') }}" @click="$loading.show()" class="btn form-button skip-button">Skip</a>
                            </div>
                            <div class="col-6 col-sm-6">
                                <a href="{{ route('practice.details.team') }}" class="btn form-button" @click="$loading.show()">Continue</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
