<register-step4
    inline-template
    :states="{{ $states }}"
    upload-photo-action="{{ $uploadPhotoUrl }}"
    :old="{{ collect( old() ) }}"
    :errors="{{ $errors }}"
    :user="{{ $user }}"
    :required-lic="{{ collect($types['requiredLicense']) }}"
    :another-lic="{{ collect($types['anotherLicense']) }}"
    remove-action="{{ $removeLicenseUrl }}"
    save-one-action="{{ $saveOneAction }}"
    v-cloak
>
    <div class="row">
        <form method="POST"
            class="license-form"
            @submit.prevent="validForm()"
            action="{{ $action }}"
        >
            @csrf

            {{--Tabs--}}
            <ul class="licenses">

                {{--Required Licenses--}}
                <li
                    class="license-tab"
                    v-for="(license, i) in requiredLicenses"
                    :class="{ active: license.id == activeTab }"
                >
                    <span
                        class="tab-name"
                        @click="setActiveTab(license.id)"
                    >
                        @{{ license.title }}
                    </span>

                    <span
                        class="delete-license-button"
                        v-if="!license.required"
                        @click="deleteLicense(license.id, i)"
                    >
                        <i class="fa fa-close"></i>
                    </span>

                    <span
                        v-if="isHasError('number.' + i) ||
                          isHasError('type.' + i) ||
                          isHasError('expiration_date.' + i)"
                        class="tab-error"
                        role="alert"
                    >
                        <strong>Errors</strong>
                    </span>
                </li>
                {{--!--}}

                {{--Add Another License--}}
                <li
                    class="license-tab plus"
                    :class="{active: activeTab == 0}"
                    v-if="licenseTypes.length"
                    @click="licenseSelector"
                >
                    <span class="tab-name">
                        <i class="fa fa-plus"></i>
                    </span>
                </li>
                {{--!--}}

            </ul>
            {{--!--}}

            <div
                class="tab-content"
                v-show="showLicenseSelector"
            >
                <select
                    class="form-control select2dd"
                    v-select2
                    @change="selectLicense()"
                    v-model="selectedType"
                >
                    <option value="0">Select License Type</option>
                    <option
                        v-for="type in licenseTypes"
                        :value="type.id"
                    >
                        @{{ type.title }}
                    </option>
                </select>
            </div>

            {{--Required Licenses Tabs Content--}}
            <div
                v-for="(license, i) in requiredLicenses"
                class="tab-content"
                v-show="(activeTab == license.id) && (showLicenseSelector == false)"
            >
                <input
                    type="hidden"
                    :name="'type[' + i + ']'"
                    v-model="license.id"
                >

                {{--Photo Input--}}
                <div class="input-group">
                    <div class="custom-file">
                        <input
                            type="file"
                            :id="'photo' + i"
                            :name="'photo[' + i + ']'"
                            accept="image/*"
                            class="custom-file-input"
                            @change="onChange($event, i)"
                        >
                        <label class="custom-file-label little-custom-file-label">
                            Choose a photo of license
                        </label>
                    </div>
                    <div
                        class="input-group-append fileinput-button"
                        v-if="val('photo_url', i) || isHasPhoto(i)"
                    >
                        <button @click="showFile(i)" class="btn btn-outline-primary" type="button">
                            Show photo
                        </button>
                    </div>
                </div>
                <span v-if="isHasError('photo.' + i)" class="invalid-feedback" role="alert">
                        <strong>@{{ getFirstError('photo.' + i) }}</strong>
                </span>

                <span class="manual-enter" @click="changeEnter(i)">or manually enter number</span>

                {{--License State Field--}}
                <div v-show="manualEnter[i] || issetData(i)" class="license-data form-group mat">
                    <label class="label_up">License state</label>
                    <select
                        :id="'state' + i"
                        class="form-control select2dd"
                        v-select2
                        :name="'state[' + i + ']'"
                    >
                        <option
                            v-for="state in states"
                            :value="state.short_title"
                            :selected="stateVal('state', i) == state.short_title ? true : false"
                        >
                            @{{ state.title }}
                        </option>
                    </select>
                    <span v-if="isHasError('state.' + i)" class="invalid-feedback" role="alert">
                        <strong>@{{ getFirstError('state.' + i) }}</strong>
                    </span>
                </div>
                {{--!--}}

                {{--License Number Field--}}
                <Cinput
                    :id="'number' + i"
                    v-show="manualEnter[i] || issetData(i)"
                    label="License Number"
                    type="text"
                    :is-mat="true"
                    :name="'number[' + i + ']'"
                    :value="val('number', i)"
                    :has-errors="isHasError('number.' + i)"
                    :first-error="getFirstError('number.' + i)"
                ></Cinput>
                {{--!--}}

                {{--License Expiration Field--}}
                <Cinput
                    :id="'expiration_date' + i"
                    v-show="manualEnter[i] || issetData(i)"
                    label="License Expiration"
                    type="date"
                    :is-mat="true"
                    :name="'expiration_date[' + i + ']'"
                    :value="val('expiration_date', i) ? val('expiration_date', i) : ' '"
                    :has-errors="isHasError('expiration_date.' + i)"
                    :first-error="getFirstError('expiration_date.' + i)"
                ></Cinput>
                {{--!--}}

                <input type="hidden" :value="i" :name="'position[' + i + ']'">
            </div>
            {{--!--}}

            <div class="form-group">
                <button type="submit" class="btn form-button">
                    Submit
                </button>
            </div>

        </form>
    </div>
</register-step4>
