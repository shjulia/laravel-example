<template>
    <div class="rel">
        <div class="abs-white waiting-div" v-if="!isResult">
            <p><i class="fa fa-search"></i> Searching for a provider…</p>
            <div id="movingBallG">
                <div class="movingBallLineG"></div>
                <div id="movingBallG_1" class="movingBallG"></div>
            </div>
        </div>
        <div class="abs-white values-div container">
            <div class="row desc">
                <div class="col-sm-6 col-6">
                    <p class="title">Time</p>
                    <p class="title_val blue">{{ Math.round(shift.shift_time / 60, -1) + ' hours' }}</p>
                </div>
                <div class="col-sm-6 col-6 text-right">
                    <p class="title">Total cost</p>
                    <p class="title_val">${{ cost }}</p>
                </div>
            </div>
        </div>
        <gmap-map
                :center="findCenter"
                :zoom="zoom"
                style="width: 100%; min-height:calc(100vh - 88px)"
                :options="{gestureHandling: 'cooperative'}"
                ref="map"
        >

            <gmap-marker
                    :position="findCenter"
                    :z-index="2"
                    icon="/img/map-marker-icon.png"

            />
            <gmap-custom-marker
                    :marker="findCenter"
                    :z-index="2"
                    v-if="!isResult"
            >
                <div class="div-pulse">
                    <!--<img src="/img/pulse2.gif" class="pulsed"/>-->
                    <span class="pulse"></span>
                </div>

            </gmap-custom-marker>
        </gmap-map>
        <div class="abs-white payment-div container">
            <div class="row" v-if="!shift.parent_shift_id">
                <div class="col-8">
                    <input class="form-control" v-model="coupon" type="text" placeholder="Apply your promo">
                </div>
                <div class="col-4">
                    <button class="btn btn-secondary apply-butt" @click="applyCoupon()">Apply</button>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <img src="/img/visa.png"/>
                    <span class="personal">Personal * * * *</span>
                    <a :href="changePaymentUrl" class="change pull-right">change</a>
                </div>
            </div>
        </div>
        <button class="btn btn-cancel" @click="cancel()">Cancel</button>
    </div>
</template>

<script>
    import GmapCustomMarker from 'vue2-gmap-custom-marker';
    import Swal from 'sweetalert2'

    export default {
        props: [
            'shift',
            'provider',
            'isResult',
            'changePaymentUrl',
            'noResult',
            'applyCouponAction'
        ],
        data () {
            return {
                coupon: '',
                cost: this.shift.cost_for_practice
            }
        },
        methods: {
            cancel() {
                this.$parent.cancel();
            },
            applyCoupon() {
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    url: this.applyCouponAction,
                    data: {'coupon': this.coupon}
                })
                    .then(response => {
                        Swal.fire({
                            type: 'success',
                            title: 'Success',
                            text: response.data.text
                        });
                        this.cost = response.data.cost;
                    })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: error.response.data.error
                        });
                    })
                    .finally(response => {
                        loader.hide();
                    });
            }
        },
        components: {
            'gmap-custom-marker': GmapCustomMarker
        },
        computed: {
            zoom() {
                return 14;
            },
            findCenter() {
                return {lat: this.shift.practice_location.lat, lng: this.shift.practice_location.lng};
            }
        },
        mounted() {

        }
    }
</script>
