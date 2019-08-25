<template>
    <div class="form-group row">
        <label for="userInfo" class="col-form-label text-md-right pl-md-4">Клиент</label>
        <div class="col-md-12 dropdown">
            <input id="userInfo"
                   class="form-control"
                   v-bind:property="isEditMode"
                   :class="{'d-none': !isEditMode}"
                   v-model="userInfo"
                   v-on:click.stop.prevent.capture="findUser"
                   @input="findUser"
                   @keyup.enter="onInputEnter"
                   type="text"
                   name="userInfo"
                   autocomplete="userInfo" autofocus
                   placeholder="Введите код или ФИО клиента">
            <input id="userInfoDummy"
                   class="form-control"
                   :class="{'d-none': isEditMode}"
                   v-model:property="selectedUserDisplayInfo"
                   @click.stop.prevent.capture="editUserInfo">
            <div id="usersDropdown" class="dropdown-menu">
                <div v-bind:property="users"
                     v-on:click.stop.prevent
                     @click="selectActive(user)"
                     v-for="user in users"
                     class="dropdown-item"
                     :class="{active: user === userInFocus}" >
                    {{user.code}}  {{user.name}}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
        },
        data(){
            return{
                userInfo:"",
                users:[],
                userInFocus: null,
                selectedUser: null,
                selectedUserDisplayInfo:'',
                isEditMode: true
            }
        },
        methods:{
            findUser(event){
                console.log("find user" + event);
                if(this.userInfo.length > 0){
                    axios.get(`/search/user/`+ this.userInfo)
                        .then(result => {
                            if(result){
                                this.setUsers(result.data);
                                this.userInFocus = result.data[0];
                            }
                        });
                }
                else{
                    this.setUsers();
                }
            },
            setUsers(data){
                if(data){
                    let users = [];
                    data.forEach(function (user){
                        users.push(user);
                    });
                    this.users = users;
                }
                else
                    this.users = [];
                this.toggleDropdown()
            },
            setSelectedUser(user){
              this.selectedUser = user;
              this.selectedUserDisplayInfo = user.code + ' ' + user.name;
              this.userInfo = user.name;
              this.isEditMode = false;
              this.$emit('userSelected', this.selectedUser)
            },
            toggleDropdown(){
                if(this.users.length > 0){
                    //in case of dropdown('show')  dropdown appears at wrong position
                    return $("#userInfo").dropdown('toggle');
                }
                return $('#usersDropdown').dropdown('hide');
            },
            selectActive(user){
                console.log('select active');
                if(user){
                    this.userInFocus = user;
                    this.setSelectedUser(user);
                    $('#usersDropdown').dropdown('hide');
                }
            },
            onInputEnter(){
                this.setSelectedUser(this.users[0]);
                $('#usersDropdown').dropdown('hide');
            },
            editUserInfo(){
                this.isEditMode = true;
                let input = $("#userInfo");
                setTimeout(function () {
                    input.focus();
                }, 1);
                this.findUser();
            }
        }
    }
</script>
