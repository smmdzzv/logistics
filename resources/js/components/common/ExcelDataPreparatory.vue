<script>
    export default {
        name: "ExcelDataPreparatory",
        data() {
            return {
                excelColumns: [],
                excelData: [],
            }
        },
        methods: {
            prepareExcelColumns() {
                this.excelColumns = Object.keys(this.fields).map(function (key) {
                    let value = {};
                    Object.assign(value, this.fields[key]);
                    value.field = key;
                    return value;
                }, this);
            },
            prepareExcelData() {
                for (let i = 0; i < this.items.length; i++) {
                    let data = {};
                    this.excelColumns.forEach(column => {
                        let keys = column.field.split('.');
                        let lastKey = this.getLastPropKey(column.field);
                        data[lastKey] = this.getValue(keys, this.items[i]);
                    }, this);
                    this.excelData.push(data);
                }
            },
            trimExcelColumnsFields() {
                this.excelColumns.forEach(column => {
                    let fullKey = column.field;
                    column.field = this.getLastPropKey(fullKey);
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
            },
            getLastPropKey(fullKey) {
                let keys = fullKey.split('.');
                return keys[keys.length - 1];
            }
        },
        watch: {
            items() {
                this.prepareExcelColumns();
                this.prepareExcelData();
                this.trimExcelColumnsFields();
            }
        }
    }
</script>
