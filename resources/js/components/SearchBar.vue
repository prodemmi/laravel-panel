<template>

    <div class="relative text-gray-600 h-element ltr:mr-1 rtl:ml-1">

        <input class="border-solid border-2 border-gray-300 bg-white py-1 ltr:pl-5 rtl:pr-5 rounded text-sm focus:outline-none"
               type="search"
               v-model.trim="search"
               @input="doSearch(search)"
               :style="{width: `${(placeholder.length * 8) + 12}px`, minWidth: '220px'}"
               :placeholder="placeholder">

        <button @click="$emit('on-search', search)"
                class="absolute bg-transparent text-lg border-none top-0.5 ltr:left-0.5 rtl:right-0.5">

            <i class="ri-search-line" :class="[ search ? 'cursor-pointer text-black' : 'opacity-60' ]"></i>

        </button>

    </div>

</template>

<script>
    export default {
        props: ['search-in'],
        data() {

            return {
                search: undefined
            }

        },
        computed: {
            placeholder(){
                return _.first(this.searchIn) === '*' ? 'Search' : 'Search in ' + this.searchIn.join(',')
            }
        },
        methods: {
            doSearch: _.debounce(function (value) {

                this.$emit('on-search', value)

            }, 400),
            clear(){
                this.search = null
                this.doSearch(null)
            }
        }
    };
</script>

