<template>

    <div class="relative">

        <i @click="doSearch(search)" class="ri-search-line search-input__icon" :class="[ search ? 'cursor-pointer text-black' : 'opacity-60' ]"></i>

        <input class="search-input__input"
               type="search"
               v-model.trim="search"
               @input="doSearch(search)"
               :style="{width: `${(placeholder.length * 8) + 12}px`, minWidth: '220px'}"
               :placeholder="placeholder">

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

                if(!this.searchIn || _.first(this.searchIn) === '*' || !_.isArray(this.searchIn)){

                    return 'Search'

                }

                return 'Search in ' + this.searchIn.join(this.searchIn.length === 2 ? ' or ' : ' ,')
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

