<template>
    <div class="dropdown">
        <input :class="{'d-none': !isEditMode}"
               :placeholder="placeholder"
               @input="findUser"
               @keyup.enter="onInputEnter"
               autocomplete="userInfo"
               autofocus
               class="form-control"
               id="userInfo"
               name="userInfo"
               type="text"
               v-bind:property="isEditMode" v-model="userInfo"
               v-on:click.stop.prevent.capture="findUser">
        <input :class="{'is-invalid': isInvalid, 'd-none': isEditMode}"
               @click.stop.prevent.capture="editUserInfo"
               class="form-control"
               id="userInfoDummy"
               v-model:property="selectedUserDisplayInfo">
        <div class="dropdown-menu" id="usersDropdown">
            <div :class="{active: user === userInFocus}"
                 @click="selectActive(user)"
                 class="dropdown-item"
                 v-bind:property="users"
                 v-for="user in users"
                 v-on:click.stop.prevent>
                {{user.code}} {{user.name}}
            </div>
        </div>
    </div>

</template>

<script>
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
                type: Boolean,
                required: false,
                default: false
            }
        },
        data() {
            return {
                userInfo: "",
                users: [],
                userInFocus: null,
                selectedUser: null,
                selectedUserDisplayInfo: '',
                isEditMode: true
            }
        },
        methods: {
            findUser(event) {
                if (this.userInfo.length > 0) {
                    axios.get(`/search/user/` + this.userInfo)
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
                this.toggleDropdown()
            },
            setSelectedUser(user) {
                this.selectedUser = user;
                this.selectedUserDisplayInfo = user.code + ' ' + user.name;
                this.userInfo = user.name;
                this.isEditMode = false;
                this.selected(this.selectedUser);
                //this.$emit('userSelected', this.selectedUser)
            },
            toggleDropdown() {
                if (this.users.length > 0) {
                    //in case of dropdown('show')  dropdown appears at wrong position
                    return $("#userInfo").dropdown('toggle');
                }
                return $('#usersDropdown').dropdown('hide');
            },
            selectActive(user) {
                if (user) {
                    this.userInFocus = user;
                    this.setSelectedUser(user);
                    $('#usersDropdown').dropdown('hide');
                }
            },
            onInputEnter() {
                this.setSelectedUser(this.users[0]);
                $('#usersDropdown').dropdown('hide');
            },
            editUserInfo() {
                this.isEditMode = true;
                let input = $("#userInfo");
                setTimeout(function () {
                    input.focus();
                }, 1);
                this.findUser();
            },
            resetInput() {
                this.userInfo = "";
                this.users = [];
                this.userInFocus = null;
                this.selectedUser = null;
                this.selectedUserDisplayInfo = '';
                this.isEditMode = true;
            }
        }
    }
</script>
