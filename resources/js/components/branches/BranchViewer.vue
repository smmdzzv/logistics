<template>
    <div class="container">
        <div class="row">
            <div class="col-lg-5 col-lx-6 mb-5">
                <div class="shadow">
                    <div class="card">
                        <div class="card-header">{{branchEditTitle}}</div>
                        <div class="card-body">
                            <branch-editor :branch="branchToChange"
                                           @branchSaved="onBranchSaved"
                                           @branchUpdated="onBranchUpdated"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-lx-6">
                <div class="shadow">
                    <div class="card">
                        <div class="card-header">Список филиалов</div>
                        <div class="card-body">
                            <branches-table :branches="branches" :onEditRequest="editBranch" @branchDeleted="removeFromList"></branches-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "BranchViewer",
        created() {
            this.getBranches();
        },
        components: {
            'BranchEditor': require('./BranchEditor.vue').default,
            'BranchesTable': require('./BranchesTable.vue').default
        },
        data() {
            return {
                branches: [],
                branchToChange: {
                    name: null,
                    country: null,
                    id: null,
                    director: null
                },
                branchEditTitle:'Добавить филиал'
            }
        },
        methods: {
            editBranch(branch) {
                this.branchEditTitle = 'Редактировать филиал';
                this.branchToChange = Object.assign(this.branchToChange, branch);
                // this.$set(this.branchToChange, 'name', branch);
            },
            onBranchSaved(branch) {
                this.branches.push(branch);
            },
            onBranchUpdated(branch) {
                this.branchEditTitle = 'Добавить филиал';
                let index = this.branches.findIndex(obj => obj.id === branch.id);
                this.branches.splice(index, 1, branch);
            },
            removeFromList(branch){
                this.branches = this.branches.filter(function (item) {
                    return item.id !== branch.id
                })
            },
            async getBranches() {
                this.$bvModal.show('busyModal');
                try {
                    const response = await axios.get('/branches/all');
                    this.branches = response.data;
                } catch (e) {
                    this.$root.showErrorMsg(
                        'Сбой загрузки',
                        'Не удалось загрузить филиалы. Перезагрузите страницу и попробуйте еще раз'
                    );
                }
                this.$nextTick(() => {
                    this.$bvModal.hide('busyModal');
                })
            }
        }
    }
</script>

<style scoped>

</style>
