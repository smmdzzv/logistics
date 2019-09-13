<template>
    <b-table :fields="fields"
             :items="branches"
             borderless
             primary-key="id"
             striped
             :busy="isBusy"
             responsive>
        <template v-slot:table-busy>
            <div class="text-center text-info my-2">
                <b-spinner class="align-middle"></b-spinner>
            </div>
        </template>
        <template slot="director.name" slot-scope="data">
            <span v-if="data.item.director">{{data.item.director.name}}</span>
            <span v-else>Не назначен</span>
        </template>
        <template slot="edit" slot-scope="data">
            <button @click="onEditRequest(data.item)" class="btn btn-outline-secondary">Изменить</button>
        </template>
        <template slot="remove" slot-scope="data">
            <img
                 alt="удалить тариф"
                 class="icon-btn-sm"
                 src="/svg/delete.svg">
        </template>
    </b-table>
</template>

<script>
    export default {
        name: "BranchesTable",
        props:{
            branches:{
                type:Array,
                required:true
            },
            onEditRequest:{
                type:Function,
                required:true
            }
        },
        created(){
            // this.getBranches()
        },
        methods:{
            // async getBranches(){
            //     this.isBusy = true;
            //     try{
            //         const response = await axios.get('/branches');
            //         this.items = response.data;
            //     }
            //     catch (e) {
            //         await this.$bvModal.msgBoxOk(`Не удалось загрузить филиалы. Перезагрузите страницу и попробуйте еще раз`,{
            //             centered:true,
            //             okTitle:'Закрыть',
            //             footerClass: 'border-0',
            //             title: 'Сообщение об ошибке'
            //         });
            //     }
            //     this.$nextTick(()=>{
            //         this.isBusy = false;
            //     })
            // }
        },
        data(){
            return{
                items:[],
                isBusy:false,
                fields:{
                    name:{
                        label:'Имя',
                        sortable:true
                    },
                    'director.name':{
                        label:'Директор',
                        sortable:true
                    },
                    'edit':{
                        label:'Изменить'
                    },
                    'remove':{
                        label: ''
                    }
                }
            }
        }
    }
</script>
