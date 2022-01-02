<template>

    <div class="relative text-gray-600 mr-1">

        <input class="border-solid border-2 border-gray-300 bg-white py-1 pl-2 pr-5 rounded text-sm focus:outline-none"
               type="search"
               v-model.trim="search"
               :placeholder="_.first(this.searchIn) === '*' ? 'Search' : 'Search in ' + searchIn.join(' , ')">

        <button style="top: 6px;right: 6px;"
                @click="$emit('on-search', search)"
                class="absolute bg-transparent text-lg border-none">

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
        watch: {

            search(newValue) {

                this.debounceSearch(newValue)

            }

        },
        methods: {
            debounceSearch: _.debounce(function (value) {

                this.$emit('on-search', value)

            }, 400)
        }
    };
</script>

