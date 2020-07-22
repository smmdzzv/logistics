<script>
    export default {
        name: "ExcelDataPreparatory",
        props: {
            excelSheetName: {
                type: String,
                default: 'Лист 1'
            },
            excelFileName: {
                type: String,
                default: 'Coded Logistics Export'
            }
        },
        data() {
            return {
                excelColumns: [],
                excelData: [],
            }
        },
        methods: {
            prepareExcelColumns() {
                this.excelColumns = this.fields.filter(f => f.key !== 'index').map(function (field) {
                    return  {
                        field: field.key,
                        label: field.label
                    };
                }, this);
            },
            prepareExcelData() {
                if (this.items)
                    for (let i = 0; i < this.items.length; i++) {
                        let data = {};
                        this.excelColumns.forEach(column => {
                            let keys = column.field.split('.');
                            data[column.field.replace('.', '')] = this.getValue(keys, this.items[i]);
                        }, this);
                        this.excelData.push(data);
                    }
            },
            trimExcelColumnsFields() {
                this.excelColumns.forEach(column => {
                    column.field = column.field.replace('.', '');
                }, this)
            },
            getValue(keys, obj) {
                let value = obj;
                keys.forEach(key => {
                    if (!value)
                        return;
                    value = value[key];
                }, this);

                return value;
            }
        },
        watch: {
            items() {
                this.excelData = [];
                this.prepareExcelColumns();
                this.prepareExcelData();
                this.trimExcelColumnsFields();
            }
        }
    }
</script>
