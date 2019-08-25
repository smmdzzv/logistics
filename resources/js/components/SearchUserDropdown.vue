<template>
    <div class="form-group row">
        <label for="userInfo" class="col-form-label text-md-right pl-md-4">Клиент</label>
        <div class="col-md-12 dropdown">
            <input id="userInfo"
                   class="form-control"
                   v-model="userInfo"
                   v-on:click.stop.prevent
                   @keyup="findUser"
                   type="text"
                   name="userInfo"
                   autocomplete="userInfo" autofocus
                   placeholder="Введите код или ФИО клиента">
            <div id="usersDropdown" class="dropdown-menu">
                <div v-bind:property="users" v-for="user in users" class="dropdown-item">
                    {{user.code}}  {{user.name}}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data(){
            return{
                userInfo:"",
                users:[]
            }
        },
        methods:{
            findUser(){
                if(this.userInfo.length > 0){
                axios.get(`/search/user/`+ this.userInfo)
                    .then(result => {
                        if(result){
                           this.setUsers(result.data);
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
            toggleDropdown(){
                if(this.users.length > 0){
                    //in case of dropdown('show')  dropdown appears at wrong position
                    return $("#userInfo").dropdown('toggle');
                }
                return $('#usersDropdown').dropdown('hide');
            }

        }
    }
</script>
