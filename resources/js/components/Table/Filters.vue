<template>
  <div class="relative">
    <lava-button
      @click="show"
      v-click-outside="hideFilters"
      :color="
        active_filter ||
        show_editor ||
        is_search ||
        (filters && filters.length > 0)
          ? 'info'
          : 'primary'
      "
      :no-padding="true"
    >
      <i class="ri-filter-2-line"></i>
    </lava-button>

    <div
      v-show="show_filters"
      class="absolute z-100 rounded bg-white shadow-md p-2"
      style="width: 200px"
    >
      <ul class="list-none leading-3 m-0 p-0">
        <li
          v-for="filter in resource.filters"
          :key="filter.id"
          class="flex justify-between p-2 rounded-md hover:bg-gray-200"
          :class="[
            (active_filter && active_filter.title) === filter.title
              ? 'bg-gray-200 text-gray-800'
              : '',
          ]"
        >
          <div @click="setFilter(filter)" class="cursor-pointer">
            {{ filter.title }}
          </div>

          <div>
            <span class="cursor-pointer ltr:mr-1 rtl:ml-1" @click="editFilter(filter)">
              <i class="ri-edit-line"></i>
            </span>

            <span
              class="text-danger cursor-pointer"
              @click="deleteFilter(filter)"
            >
              <i class="ri-delete-bin-line"></i>
            </span>
          </div>
        </li>

        <hr v-if="resource.filters.length > 0" />

        <li
          class="
            flex flex-col
            justify-start
            w-full
            p-2
            cursor-pointer
            hover:text-gray-800
          "
          @click="
            show_editor = true;
            show_filters = false;
          "
        >
          Create custom filter
        </li>

        <li
          v-if="resource.filters.length > 0 && active_filter"
          class="
            flex flex-col
            justify-start
            w-full
            p-2
            cursor-pointer
            hover:text-gray-800
          "
          @click="setFilter(null)"
        >
          Disable filter
        </li>
      </ul>
    </div>

    <div
      v-if="show_editor"
      class="absolute flex flex-col z-100 rounded bg-white shadow-md p-2"
      style="min-width: 560px"
    >
      <div
        v-for="(filter, i) in filters"
        class="flex justify-start text-lg p-1"
        :key="i"
      >
        <div class="flex items-start justify-between text-lg w-full">
          <span style="width: 25%; min-width: 120px; max-width: 180px">{{
            filter.name
          }}</span>

          <component
            class="w-full ltr:mr-1 rtl:ml-1"
            :key="filter.column"
            :is="filter.component + '-edit'"
            :value="filter.value"
            :data="filter"
            :resource="filter.resource"
            :disabled="filter.where.where === 'NULL'"
            @on-change="changed"
          />

        <div class="flex items-center" style="width: 200px">

            <lava-tooltip :text="filter.where.tooltip">
              <span
                v-text="filter.where.label"
                @click="changeWhere(filter.component, filter.column)"
                style="width: 32px; height: 32px"
                :class="{ 'bg-danger': filter.where.where === 'NULL' }"
                class="
                  rounded-full
                  cursor-pointer
                  bg-primary
                  ltr:mr-1
                  rtl:ml-1
                  text-white
                  flex
                  items-center
                  justify-center
                "
              >
              </span>
            </lava-tooltip>

            <lava-tooltip :text="filter.con.tooltip">
              <span
                v-text="filter.con.label"
                @click="changeC(filter)"
                style="width: 32px; height: 32px"
                class="
                  rounded-full
                  cursor-pointer
                  bg-primary
                  ltr:mr-1
                  rtl:ml-1
                  text-white
                  flex
                  items-center
                  justify-center
                "
              >
              </span>
            </lava-tooltip>

            <i
              @click="removeFilter(i)"
              style="width: 32px; height: 32px"
              class="
                ri-close-line
                rounded-full
                cursor-pointer
                bg-primary
                text-white
                flex
                items-center
                justify-center
              "
            >
            </i>

        </div>
          
        </div>
      </div>

      <lava-button
        v-if="!show_fields"
        @click="show_fields = true"
        small
        :no-padding="true"
        >+
      </lava-button>

      <VueSelect
        v-show="show_fields"
        v-model="selected_field"
        placeholder="Select a column"
        @input="addQuery"
        :options="getOptions"
        :reduce="(option) => option">
      </VueSelect>

      <div class="flex justify-end mt-2">
        <lava-button
          v-if="filters && filters.length > 0"
          @click="doFilter(filters)"
          small
          color="success"
          :disabled="show_fields"
          :no-padding="true"
        >
          Search
        </lava-button>

        <lava-button
          v-if="filters && filters.length > 0"
          @click="createFilter"
          small
          :no-padding="true"
        >
          {{ edit_mode ? "Edit" : "Save" }}
        </lava-button>

        <lava-button
          @click="cancelFilter"
          color="danger"
          small
          :no-padding="true"
        >
          Cancel
        </lava-button>
      </div>
    </div>
  </div>
</template>

<script>

    import VueSelect from "vue-select";
    export default {
        props: ['resource'],
        components: {
            VueSelect
        },
        data() {
            return {
                show_filters: false,
                show_editor: false,
                show_fields: false,
                active_filter: undefined,
                filters: [],
                is_search: false,
                edit_mode: false,
                selected_field: null,
                wheres: [
                    {
                        label: 'E',
                        where: '=',
                        tooltip: 'Equal'
                    },
                    {
                        label: 'NE',
                        where: '<>',
                        tooltip: 'Not equal'
                    },
                    {
                        label: 'GT',
                        where: '>=',
                        tooltip: 'Greater than'
                    },
                    {
                        label: 'LT',
                        where: '<=',
                        tooltip: 'Less than'
                    },
                    {
                        label: '∅',
                        where: 'NULL',
                        tooltip: 'Is empty'
                    },
                    {
                        label: 'L',
                        where: 'LIKE',
                        tooltip: 'Containse'
                    },
                    {
                        label: 'NL',
                        where: 'NOT LIKE',
                        tooltip: 'Not containse'
                    },
                    {
                        label: 'RE',
                        where: 'REGEXP',
                        tooltip: 'Regex'
                    }
                ]
            }
        },
        computed: {
            getOptions(){
                
                var fields = _.filter(this.getFields(this.resource.fields), (f) => f.component !== 'image')
                return _.map(fields, (field) => ({
                    label: field.name,
                    value: field.column
                }))
            }
        },
        methods: {
            show() {

                if (this.is_search && !this.show_editor)
                    this.show_editor = !this.show_editor
                else
                    this.show_filters = !this.show_filters

            },
            hideFilters() {

                this.show_filters = false;

            },
            hideEditor() {

                this.show_editor = false

            },
            setFilter(filter) {

                if (filter !== null && (this.active_filter?.id === filter.id)) {
                    return
                }

                if (!filter) {
                    this.is_search = false
                }

                this.active_filter = filter

                this.hideFilters();
                this.$emit('set-filter', filter)

            },
            createFilter() {
                
                Lava.showLoading(-1)
                Lava.confirm('Create filter', '', false, {
                    confirmButtonText: this.edit_mode ? 'Edit' : 'Create',
                    input: 'text',
                    inputLabel: 'Filter title',
                    inputValue: this.edit_mode ? this.active_filter.title : undefined
                }).then(res => {

                    if (res.isConfirmed) {

                        this.$http.post('/api/store-filter', {
                            resource: this.resource.resource,
                            filters: this.filters,
                            title: res.value,
                            id: this.edit_mode ? this.active_filter.id : null,
                            edit: this.edit_mode
                        }).then((response) => {

                            if (response.data.result) {

                                Lava.toast(response.data.message, 'success')
                                this.cancelFilter()
                                this.hideEditor()
                                this.updateConfig()
                                Lava.showLoading(false)

                            }

                        })

                    }

                })

            },
            changeWhere(component, column) {

                let filter = _.find(this.filters, {column})
                let wheres = filter.wheres || this.wheres

                wheres = this.arrayRotate(wheres)

                if (['select', 'badge', 'boolean'].includes(component.toLowerCase())) {

                    filter.wheres = _.filter(wheres, (where) => {
                        return !['<=', '>=', 'LIKE', 'REGEXP'].includes(where.where)
                    })

                } else if (['id', 'date', 'date-time', 'timezone', 'number', 'currency'].includes(component.toLowerCase())) {

                    filter.wheres = _.filter(wheres, (where) => {
                        return !['LIKE', 'REGEXP'].includes(where.where)
                    })

                } else if (['text', 'code', 'cke-ditor', 'text-area'].includes(component.toLowerCase())) {

                    filter.wheres = _.filter(wheres, (where) => {
                        return !['<=', '>='].includes(where.where)
                    })

                } else if (filter.relation) {

                filter.wheres = _.filter(wheres, (where) => {
                    return ['=', '<>', 'NULL'].includes(where.where)
                })

                }else {

                    filter.wheres = this.wheres

                }

                filter.where = filter.wheres[0]

            },
            changeC(filter){

                var cons = [
                    {
                        label: 'A',
                        con: 'and',
                        tooltip: 'And'
                    },
                    {
                        label: 'O',
                        con: 'or',
                        tooltip: 'Or'
                    }
                ]

                if(filter?.con.con === 'and'){
                    filter.con = cons [1]
                }else{
                    filter.con = cons [0]
                }
                
            },
            arrayRotate(array) {
                array.push(array.shift());
                return array;
            },
            doFilter(filter) {

                this.is_search = true
                this.hideEditor()
                this.$emit('set-filter', {filter})

            },
            deleteFilter(filter) {

                Lava.showLoading(-1)
                Lava.confirm('Delete filter', '', true).then(res => {

                    if (res.isConfirmed) {

                        this.$http.post('/api/delete-filter', {
                            filter_id: filter.id,
                            title: filter.title,
                        }).then((response) => {

                            if (response.data.result) {

                                Lava.toast(response.data.message, 'success')
                                this.cancelFilter()
                                this.hideEditor()
                                this.updateConfig()
                                Lava.showLoading(false)

                            }

                        })

                    }

                })

            },
            editFilter(filter) {

                this.filters = filter.filters
                this.active_filter = filter
                this.edit_mode = true
                this.show_editor = true

            },
            removeFilter(index) {

                this.filters = _.filter(this.filters, (filter, i) => i !== index)

            },
            cancelFilter() {

                this.show_filters = false
                this.show_editor = false
                this.show_fields = false
                this.is_search = false
                this.active_filter = undefined
                this.filters = []

                this.$emit('set-filter', null)

            },
            addQuery(value) {
                
                const filter = _.find(this.flattenFields(this.resource.fields), {column: value.value})

                filter.column += '__' + Math.ceil(Math.random()*1000)

                filter.where = {
                    label: 'E',
                    where: '=',
                    tooltip: 'Equal'
                }

                filter.con = {
                    label: 'A',
                    con: 'and',
                    tooltip: 'And'
                }

                this.filters.push(filter)

                this.selected_field = null

                this.show_fields = false

            },
            getFields(fields) {

                return _.filter(this.flattenFields(fields), (field) => {

                    return !['password', 'avatar'].includes(field.component)

                })

            },
            changed(data) {

                if(data?.value){ 

                    _.find(this.filters, {column: data.column}).value = data.value

                }

            }
        }
    }
</script>

<style scoped>
</style>