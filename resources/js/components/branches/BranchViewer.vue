<template>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Добавить филиал</div>
                <div class="card-body">
                    <branch-editor @branchSaved="onBranchSaved"
                    @branchUpdated="onBranchUpdated"/>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Список филиалов</div>
                <div class="card-body">
                    <branches-table :onEditRequest="editBranch" :branches="branches"></branches-table>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
    export default {
        name: "BranchViewer",
        created(){
            this.getBranches();
        },
        components:{
            'BranchEditor': require('./BranchEditor.vue').default,
            'BranchesTable': require('./BranchesTable.vue').default
        },
        data(){
            return{
                branches:[]
            }
        },
        methods:{
            editBranch(branch){
                console.log('edit branch', branch)
            },
            onBranchSaved(branch){
                console.log('saved', branch)
                this.branches.push(branch);
            },
            onBranchUpdated(branch){
                console.log('update', branch)
            },
            async getBranches(){
                this.$bvModal.show('busyModal');
                try{
                    const response = await axios.get('/branches');
                    this.branches = response.data;
                }
                catch (e) {
                    this.$root.showErrorMsg(
                        'Сбой загрузки',
                        'Не удалось загрузить филиалы. Перезагрузите страницу и попробуйте еще раз'
                    );
                }
                this.$nextTick(()=>{
                    this.$bvModal.hide('busyModal');
                })
            }
        }
    }
</script>

<style scoped>

</style>
