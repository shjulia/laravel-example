<areas inline-template
       :saved-cities="{{ isset($area) ? json_encode($area->cities->pluck('id')) : '[]' }}"
       :saved-zips="{{ isset($area) ? json_encode($area->zipCodes->pluck('id')) : '[]' }}"
       :all-cities="{{ json_encode($cities) }}"
       :all-zips="{{ json_encode($zipCodes) }}"
       :saved-tier="{{  isset($area) ? $area->tier : 1}}"
       v-cloak
>

    <form method="POST" action="{{ isset($area) ? route('admin.data.location.area.update', [$state, $area]) : route('admin.data.location.area.store', [$state]) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="name" class="col-form-label">Name</label>
                    <input id="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name', $area->name ?? '') }}" required>
                    @if ($errors->has('name'))
                        <span class="invalid-feedback"><strong>{{ $errors->first('name') }}</strong></span>
                    @endif
                </div>
                <div class="form-group">
                    <div class="form-group">
                        <label for="tier">Tier</label>
                        <select v-model="tier" class="form-control{{ $errors->has('tier') ? ' is-invalid' : '' }}" name="tier" required>
                            @foreach($tiers as $tier)
                                <option value="{{ $tier->id }}">{{ $tier->multiplier }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('tier'))
                            <span class="invalid-feedback"><strong>{{ $errors->first('tier') }}</strong></span>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="form-group">
                        <label for="cities" class="col-form-label">Cities</label>
                        <select @change="cityChange()" multiple v-select2 v-model="cities" name="cities[]" class="select2 form-control{{ $errors->has('cities') ? ' is-invalid' : '' }}" ref="cities">
                            @foreach($cities as $city)
                                <option value="{{ $city['id'] }}"> {{ $city['name'] }} </option>
                            @endforeach
                        </select>
                        @if ($errors->has('cities'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cities') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="zip_codes" class="col-form-label">Zip Codes</label>
                        <select @change="zipChange()" multiple v-select2 v-model="zips" name="zip_codes[]" class="select2 form-control{{ $errors->has('zip_codes') ? ' is-invalid' : '' }}" ref="zip">
                            @foreach($zipCodes as $zipCode)
                                <option value="{{ $zipCode->id }}"> {{ $zipCode->zip }} ({{ $zipCode->place_name }}) </option>
                            @endforeach
                        </select>
                        @if ($errors->has('zip_codes'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('zip_codes') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="custom-control custom-checkbox mb-4">
                    <input type="checkbox" class="custom-control-input" id="is_open" name="is_open" value="1" @if(isset($area) && $area->is_open) checked="checked" @endif>
                    <label class="custom-control-label" for="is_open">Open</label>
                </div>
            </div>

            <!-- Map -->
            <div class="col-6">
                <gmap-map
                        :center="getCenter()"
                        :zoom="6"
                        style="width: 100%; height: 400px"
                        ref="map"
                >
                    <template
                            v-if="Object.keys(cityMarkers).length"
                    >
                        <gmap-marker
                                v-for="marker in cityMarkers"
                                :position="getLocation(marker)"
                                :clickable="true"
                                :draggable="false"
                        />
                    </template>

                    <template
                            v-if="Object.keys(zipMarkers).length"
                    >
                        <gmap-marker
                                v-for="marker in zipMarkers"
                                :position="getLocation(marker)"
                                :clickable="true"
                                :draggable="false"
                                :icon="markerIcon('blue')"
                        />
                    </template>



                </gmap-map>
            </div>
        </div>

        <div class="form-group col-6">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>

    </form>
</areas>