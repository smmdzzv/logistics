<template>
    <div class="dropdown">
        <input :class="{'is-invalid': isInvalid, 'd-none': !isEditMode}"
               :placeholder="placeholder"
               :disabled="disabled"
               @blur="hideDropdown"
               @focus="showDropdown"
               @keyup.enter="onInputEnter"
               autocomplete="userInfo"
               autofocus
               class="form-control"
               id="userInfo"
               name="userInfo"
               type="text"
               v-bind:property="isEditMode"
               v-model="userInfo"
               v-on:click.stop.prevent.capture>
        <input :class="{'is-invalid': isInvalid, 'd-none': isEditMode}"
               :disabled="disabled"
               @click.stop.prevent.capture="editUserInfo"
               class="form-control dummy"
               id="userInfoDummy"
               v-model:property="selectedUserDisplayInfo">
        <span class="invalid-feedback" role="alert" v-if="errorMessages">
            <strong v-for="message in errorMessages">{{message}}</strong></span>
        <div :class="{show: showOptions && users.length > 0 && userInfo.length > 0}"
             class="dropdown-menu"
             id="usersDropdown">
            <div :class="{active: user === userInFocus}"
                 @click="selectActive(user)"
                 class="dropdown-item"
                 v-bind:property="users"
                 v-for="user in users"
                 v-on:click.stop.prevent>
                {{user.code}}
            </div>
        </div>
    </div>
</template>

<script>
    var cancel;
    var CancelToken = axios.CancelToken;
    export default {
        mounted() {
        },
        props: {
            selected: {
                type: Function,
                required: true
            },
            placeholder: {
                type: String,
                required: false,
                default: "Введите ФИО или код пользователя"

            },
            isInvalid: {
                required: false,
                default: false
            },
            preselectedUser: {
                type: Object,
                required: false
            },
            url: {
                type: String,
                required: false,
                default: "/search/user/"
            },
            errorMessages: {
                type: Array
            },
            disabled: {
                type: Boolean
            }
        },
        created() {
            if (this.preselectedUser) {
                this.selectActive(this.preselectedUser);
            }
        },
        data() {
            return {
                userInfo: "",
                users: [],
                userInFocus: null,
                selectedUser: null,
                selectedUserDisplayInfo: '',
                isEditMode: true,
                showOptions: false
            }
        },
        watch: {
            preselectedUser() {
                this.selectActive(this.preselectedUser);
            },
            userInfo() {
                if (this.selectedUser
                    && this.userInfo !== this.selectedUser.code) {
                    this.selectedUser = null;
                    this.selectedUserDisplayInfo = '';
                    this.selected(this.selectedUser);
                }
                if ((cancel != undefined)) {
                    cancel();
                }

                this.loadUsers()
            }
        },
        methods: {
            loadUsers() {
                if (this.userInfo && this.userInfo.length > 0) {
                    axios.get(this.url + this.userInfo, {
                        cancelToken: new CancelToken(function executor(c) {
                            cancel = c;
                        })
                    })
                        .catch(e => {
                        })
                        .then(result => {
                            if (result) {
                                this.setUsers(result.data);
                                this.userInFocus = result.data[0];
                            }
                        });
                } else {
                    this.setUsers();
                }
            },
            setUsers(data) {
                if (data) {
                    let users = [];
                    data.forEach(function (user) {
                        users.push(user);
                    });
                    this.users = users;
                } else
                    this.users = [];
            },
            setSelectedUser(user) {
                this.selectedUser = user;
                // this.selectedUserDisplayInfo = user.code + ' ' + user.name;
                // this.selectedUserDisplayInfo = user.code;
                this.selectedUserDisplayInfo = user? user.code : this.userInfo;
                this.userInfo = user? user.code : this.userInfo;
                this.isEditMode = false;
                this.selected(this.selectedUser);
            },
            showDropdown() {
                this.showOptions = true
            },
            hideDropdown() {
                let self = this;
                setTimeout(function () {
                    self.showOptions = false;
                }, 150);
            },
            selectActive(user) {
                if (user) {
                    this.userInFocus = user;
                    this.setSelectedUser(user);
                    this.hideDropdown()
                }
            },
            onInputEnter() {
                this.setSelectedUser(this.users[0]);
                this.hideDropdown()
            },
            editUserInfo() {
                this.isEditMode = true;
                let input = $("#userInfo");
                setTimeout(function () {
                    input.focus();
                    input.select();
                }, 1);
            },
            resetInput() {
                this.userInfo = "";
                this.users = [];
                this.userInFocus = null;
                this.selectedUser = null;
                this.selectedUserDisplayInfo = '';
                this.isEditMode = true;
            },

        },
        // directives: {
        //     debounce: {
        //         inserted: function (el, binding) {
        //             if (binding.value !== binding.oldValue) {
        //                 let debounce = function debounce(fn, delay = 300) {
        //                     let timeoutID = null;
        //
        //                     return function () {
        //                         clearTimeout(timeoutID);
        //
        //                         let args = arguments;
        //                         let that = this;
        //
        //                         timeoutID = setTimeout(function () {
        //                             fn.apply(that, args);
        //                         }, delay);
        //                     }
        //                 };
        //                 el.oninput = debounce(ev => {
        //                     el.dispatchEvent(new Event('change'));
        //                 }, parseInt(binding.value) || 300);
        //             }
        //         }
        //     }
        // }
    }
</script>
