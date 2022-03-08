<template>
    <div>
        <a id="messagesDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-bell-o" :class="notifications.length ? 'new-notifications' : ''" aria-hidden="true"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="messagesDropdown">
            <div v-for="notification in notifications" class="alert alert-info alert-dismissible fade show notification" role="alert" >
                <div class="row d-flex align-items-center">
                    <div class="col-2 d-flex align-items-center">
                        <i class="fa notification-icon" :class="notification.icon ? notification.icon : 'fa-exclamation-circle'" aria-hidden="true"></i>
                    </div>
                    <div class="col-10">
                        <a v-if="notification.link" :href="notification.link">
                            <strong>{{ notification.title }}</strong>
                        </a>
                        <strong v-else>{{ notification.title }}</strong>
                        <div> {{ notification.text }} </div>
                        <div class="d-flex justify-content-between">
                            <div class="notification-data"> {{ showtime(notification.created_at) }}</div>
                            <div class="notification-from">from <strong>{{ notification.from ?  notification.from : 'boon' }}</strong></div>
                        </div>
                    </div>
                    <button @click="markAsRead(notification.id)" type="button" class="myclose">
                        <span>&times;</span>
                    </button>
                </div>
            </div>
            <div v-if="notifications.length == 0" class="p-1">No notifications for you.</div>
        </div>

        <div v-if="Object.keys(notify).length" aria-live="polite" aria-atomic="true" style="position: absolute; top: 80px; right: 0; min-height: 200px; width: 300px">
            <div v-for="notification in notify" class="toast" id="toast" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
                <div  class="toast-header">
                    <i class="fa notification-icon mr-1" :class="notification.icon ? notification.icon : 'fa-exclamation-circle'" aria-hidden="true"></i>
                    <strong class="mr-auto">{{ notification.from ?  notification.from : 'boon' }}</strong>
                    <small>just now</small>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    <a v-if="notification.link" :href="notification.link">
                        {{ notification.title }}
                    </a>
                    <div v-else>{{ notification.title }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {eventEmmiter} from "../../buss";

    export default {
        props: [
            'allNotifications',
            'user'
        ],
        data() {
            return {
                notifications: this.allNotifications,
                notify: []
            };
        },
        methods: {
            markAsRead(id) {
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    data: {
                        'id': id,
                    },
                    url: '/notification/mark-as-read'
                })
                    .then(response => {
                        this.notifications = _.remove(this.notifications, (n) => {
                            return n.id !== id;
                        });
                    })
                    .catch(error => {})
                    .finally(response => {
                        loader.hide();
                    });
                this.$nextTick(() => {
                    $('#messagesDropdown').dropdown('show');
                });
            },
            showtime(time) {
                let mTime = moment.tz(time, 'UTC').clone().tz(this.user.tz);
                if (mTime.format('YYYY-MM-DD') == this.today) {
                    return "Today at " + mTime.format('h:mm a');
                }
                if (moment(time).format('YYYY-MM-DD') == this.yesterday) {
                    return "Yesterday at " + mTime.format('h:mm a');
                }
                if (mTime.format('YYYY-MM-DD') > moment().subtract(7, 'days').format('YYYY-MM-DD')) {
                    return mTime.format('dddd');
                }

                return mTime.format('MMM Do');
            }
        },
        computed: {
            today() {
                return moment().format('YYYY-MM-DD');
            },
            yesterday() {
                return moment().subtract(1, 'days').format('YYYY-MM-DD');
            }
        },
        mounted() {
            eventEmmiter.$on('newJob', (notificationData) => {
                this.notifications.unshift(notificationData);
                this.notify.unshift(notificationData);
                this.$nextTick(() => {
                    $('.toast').each(function () {
                        $(this).toast('show');
                    });
                });
            });
            eventEmmiter.$on('inviteAccepted', (notificationData) => {
                this.notify.unshift(notificationData);
                this.$nextTick(() => {
                    $('.toast').each(function () {
                        $(this).toast('show');
                    });
                });
            })
        },
    }
</script>
