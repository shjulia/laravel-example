<template>

</template>

<script>
    import Swal from 'sweetalert2'

    export default {
        methods: {
            showInstallPopup() {
                const isIos = () => {
                    const userAgent = window.navigator.userAgent.toLowerCase();
                    return /iphone|ipad|ipod/.test( userAgent );
                };
                const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);
                if (isIos() && !isInStandaloneMode()) {
                    Swal.fire({
                        title: 'Would you like to install ios app?',
                        showCancelButton: true,
                        confirmButtonText: 'Install',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            window.location = 'itms-apps://itunes.apple.com/us/app/doing-boon/id1471640517';
                            sessionStorage.setItem('ios-popup-closed', 1);
                        } else {
                            sessionStorage.setItem('ios-popup-closed', 1);
                        }
                    });
                }
            }
        },
        mounted() {
            if (sessionStorage.getItem('ios-popup-closed') != 1) {
                this.showInstallPopup();
            }
        }
    }
</script>
