<script>

    import Compressor from 'compressorjs';

    export default {
        data () {
            return {
                manualEnter: [],
                activeTab: this.requiredLic[0].id,
                licensesPhotos: [],
                addedLicenses: [],
                showLicenseSelector: false,
                selectedType: 0,
                anotherLicenses: this.anotherLic,
                licenseTypes: [],
                requiredLicenses: [],
            }
        },
        props: [
            'states',
            'uploadPhotoAction',
            'old',
            'user',
            'errors',
            'requiredLic',
            'anotherLic',
            'tabindex',
            'removeAction',
            'saveOneAction'
        ],
        methods: {
            changeEnter(i) {
                this.$set(this.manualEnter, i, !this.manualEnter[i]);
            },

            onChange (image, position) {
                let loader = this.$loading.show();
                this.image = true;
                let file = $('#photo' + position)[0].files[0];
                const self = this;
                new Compressor(file, {
                    quality: 0.75,
                    maxWidth: 800,
                    success(result) {
                        const formData = new FormData();
                        formData.append('photo', result, result.name);
                        formData.append('position', position);
                        self.sendPhoto(formData, loader, position);
                    },
                    error(err) {
                        alert('Something went wrong');
                    },
                });
            },

            sendPhoto (formData, loader, position) {
                axios({
                    method: 'POST',
                    url: this.uploadPhotoAction,
                    data: formData
                })
                    .then(response => {
                        this.$set(this.licensesPhotos, position, response.data);
                    })
                    .catch(response => {
                        alert('Something went wrong');
                    })
                    .finally(response => {
                        loader.hide()
                    });
            },

            saveOneLicense (data) {
                axios({
                    method: 'POST',
                    url: this.saveOneAction,
                    data: data
                })
                    .then(response => {
                    })
                    .catch(response => {
                    });
            },

            isHasPhoto(i) {
                return this.licensesPhotos.hasOwnProperty(i);
            },

            val(field, i) {
                if (this.old.hasOwnProperty(field) && this.old[field][i]) {
                    return this.old[field][i];
                }
                return (this.user.specialist.licenses && this.user.specialist.licenses.hasOwnProperty(i)) ?
                    this.user.specialist.licenses[i][field] :
                    '';
            },

            isHasError(field) {
                return this.errors.hasOwnProperty(field);
            },

            getFirstError(field) {
                return this.isHasError(field) ? this.errors[field][0] : '';
            },

            setActiveTab(tabId) {
                this.activeTab = tabId;
                this.showLicenseSelector = false;
            },

            stateVal(field, i) {
                let value = $('#state' + i).val();
                if (value) {
                    return value;
                }
                if (this.old.hasOwnProperty(field) && this.old[field][i]) {
                    return this.old[field][i];
                }
                return this.user.specialist.licenses.hasOwnProperty(i) ?
                    this.user.specialist.licenses[i][field] :
                    this.user.specialist.driver_state;
            },

            selectLicense() {

                if( +this.selectedType === 0) return;

                //get license selected license object
                let license = this.licenseTypes.find(lcs => {

                    //remove selected license from types array
                    this.licenseTypes = this.licenseTypes.filter(obj => {
                        return obj.id !== lcs.id;
                    });

                    return lcs.id === this.selectedType;
                });

                //add selected license to required licenses array
                this.requiredLicenses.push(license);
                this.activeTab = license.id;
                this.showLicenseSelector = false;
                sessionStorage.setItem('licenses', JSON.stringify(this.requiredLicenses));
                sessionStorage.setItem('types', JSON.stringify(this.licenseTypes));
                this.selectedType = 0;

                this.$nextTick(()=>{
                    $('.select2dd').select2({
                        width: '100%',
                        //minimumResultsForSearch: -1
                    });
                })
            },

            deleteLicense(id, i) {

                this.licenseTypes.push(
                    this.requiredLicenses.find(lcs => {
                        return lcs.id == id;
                    })
                );

                this.requiredLicenses = this.requiredLicenses.filter(lcs => {
                    return lcs.id !== id;
                });

                if(this.activeTab == id) {
                    this.activeTab = this.requiredLicenses[this.requiredLicenses.length - 1].id;
                }

                axios({
                    method: 'DELETE',
                    url: this.removeAction,
                    data: {'position': i}
                })
                    .then(response => {

                    })
                    .catch(response => {
                        alert('Something went wrong');
                    })

                sessionStorage.setItem('licenses', JSON.stringify(this.requiredLicenses));
                sessionStorage.setItem('types', JSON.stringify(this.licenseTypes));
            },

            showFile(i) {
                let url = this.licensesPhotos.hasOwnProperty(i) ?  this.licensesPhotos[i] : null;
                if (!url) {
                    url = this.user.specialist.licenses[i].photo_url;
                }
                window.open(url, 'file of medical license', "width=500,height=500,top=40,left=40");
            },

            licenseSelector() {
                this.showLicenseSelector = !this.showLicenseSelector;
                this.activeTab = 0;
            },

            validForm() {

                let activeTab = null;

                for (let i = 0; i < this.requiredLicenses.length; i++) {
                    let photo  = $('input[name="photo[' + i + ']"]').val();
                    let type  = $('input[name="type[' + i + ']"]').val();
                    let state  = $('select[name="state[' + i + ']"]').val();
                    let number = $('input[name="number[' + i + ']"]').val();
                    let date   = $('input[name="expiration_date[' + i + ']"]').val();

                    if (!photo && !(state && number && date) && !this.val('photo_url', i) ) {
                        activeTab = this.requiredLicenses[i].id;
                        break;
                    } else {
                        this.saveOneLicense({state: state, number: number, expiration_date: date, position: i, type: type});
                    }
                }

                if(activeTab !== null) {
                    this.activeTab = activeTab;
                    return false;
                }
                this.$loading.show();
                $('.license-form').submit();
            },

            issetData(i) {
                if( this.user.specialist.licenses[i] ) {
                    if(this.user.specialist.licenses[i].number) {
                        return true;
                    }
                }
                return false;
            },

            initManualEnterStates() {

                let licenses = this.requiredLicenses.concat(this.licenseTypes);

                licenses.forEach((item, index) => {
                    this.manualEnter[index] = false;
                });
            },
        },
        mounted() {
            this.$nextTick(() => {
                $('.select2dd').select2({
                    width: '100%',
                    //minimumResultsForSearch: -1
                });
            })
        },
        created() {
            let types = sessionStorage.getItem('types');
            types = JSON.parse(types);
            this.licenseTypes = types ? types : this.anotherLic;

            let licenses = sessionStorage.getItem('licenses');
            licenses = JSON.parse(licenses);
            this.requiredLicenses = licenses ? licenses : this.requiredLic;

            this.initManualEnterStates();
        }
    }
</script>
