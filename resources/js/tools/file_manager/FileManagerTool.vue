<template>
    <div>
         <lava-dialog :show="edit_mode"
                :danger="false"
                width="80%"
                height="auto"
                confirm-label="Edit"
                :close-on-click-outside="true"
                @on-continue="edit()"
                @on-cancel="edit_mode = null"
                @on-close="edit_mode = null">

        <template v-slot:header>

            Edit {{ edit_mode.filename }}{{ edit_mode.ext ? '.' + edit_mode.ext : null }}

        </template>

        <template v-slot:body>

            <code-edit :extenstion="edit_mode.ext" :mime-type="edit_mode.mime_type" :value="edit_mode.content" @on-change="data => edit_mode.content = data.value"></code-edit>

        </template>

    </lava-dialog>

    <lava-dialog :show="current_file"
                    :danger="true"
                    width="30vw"
                    height="auto"
                    :show-buttons="false"
                    :close-on-click-outside="true"
                    @on-close="current_file = null">

        <template v-slot:header>

            {{ current_file.filename }}

        </template>

        <template v-slot:body>

            <div class="flex flex-col items-center m-auto w-full h-full">

                <div v-if="isShowable(current_file)" class="mb-4" :style="{height: urlIsAudio(current_file.url) ? 'auto' : '40vh'}">
                    <img v-if="urlIsImage(current_file.url)" class="object-contain w-full h-full" :src="current_file.url">
                    <video v-if="urlIsVideo(current_file.url)" controls class="object-contain w-full h-full" :src="current_file.url"></video>
                    <audio v-if="urlIsAudio(current_file.url)" controls :src="current_file.url"></audio>
                </div>

                <div class="w-full break-all overflow-y-auto">
                    <div class="w-full">Name: {{current_file.filename}}</div>
                    <div class="w-full">Path: {{current_file.disk}}/{{current_file.path}}</div>
                    <div class="w-full">Size: {{human_filesize(current_file.size)}}</div>
                    <div v-if="current_file.type === 'file'" class="w-full">Extension: {{current_file.ext}}</div>
                    <div>Modified: {{current_file.modified}}</div>
                    <br>
                    <div v-if="current_file.type === 'file'" class="w-full">
                        Url: {{current_file.url}}
                        <br>
                        <a class="cursor-pointer text-blue-700 w-full" @click="copyToClipboard(current_file.url)"> Copy url</a>
                        <a :href="current_file.url" class="cursor-pointer text-blue-700 w-full" download> | Download</a>
                    </div>
                </div>

            </div>

        </template>

    </lava-dialog>

    <div class="my-2">

        <div v-if="selected && selected.length > 0" class="flex items-center justify-between">

            <div>
                <lava-button class="px-4" @click="selected = files">Select all</lava-button>
                <lava-button class="px-4" @click="selected = []">Deselect all</lava-button>
            </div>

            <div>
                <lava-button class="px-4" v-if="allAreArchive" @click="extract">Extract</lava-button>
                <lava-button class="px-4" @click="compress">Compress</lava-button>
                <lava-button class="px-4" @click="copy">Copy</lava-button>
                <lava-button class="px-4" @click="cut">Cut</lava-button>
                <lava-button class="px-4" @click="deleteMedia()" color="danger" >Delete</lava-button>
            </div>
            
        </div>
        
        <div v-else class="flex items-center justify-between">
            <div class="flex items-center">
                <lava-search-bar ref="searchBar" :search-in="'*'" @on-search="search" class="ltr:mr-1 rtl:ml-1"></lava-search-bar>
                <lava-spinner v-if="loading" color="primary" class="ltr:mr-2 rtl:ml-2"></lava-spinner>
                <lava-button class="px-4" v-if="!(searching || loading)"
                                @click="getMedia(current_path)">
                    <i class="ri-refresh-line"></i>
                </lava-button>
            </div>
    
            <div v-if="!searching" class="flex items-center justify-between">
                <lava-button class="px-4 m-1" v-if="clipboard && clipboard.length" @click="paste">Paste</lava-button>
                <lava-button class="px-4 m-1">
                    <lava-file-input :multiple="true" 
                                        placeholder="Uplaod file"
                                        @on-change="uploadMedia">
                    </lava-file-input>
                    
                </lava-button>
                <lava-button class="px-4 m-1" @click="newFolder">New Folder</lava-button>
                <lava-button class="px-4 ltr:ml-1 rtl:mr-1" @click="newFile">New File</lava-button>
            </div>
        </div>

    </div>

    <div v-if="_.isEmpty(current_path) && files.length || !_.isEmpty(current_path)" class="flex flex-col">
    
        <div class="flex items-center justify-between">

            <div>
                <i v-if="current_path && current_path.length"
                @click="goBack()" class="cursor-pointer text-lg w-fit" 
                :class="$store.getters.getConfig.rtl ? 'ri-arrow-right-line': 'ri-arrow-left-line'"></i>

                <lava-breadcrumb :path="current_path" @on-change="getMedia"></lava-breadcrumb>
            </div>

            <div class="flex items-center justify-between">
                <div style="width: 300px">
                    <lava-select label="Sort by: " 
                                    :searchable="false" 
                                    :value="sort_type"
                                    :nullable="false"
                                    class="ltr:mr-1 rtl:ml-1"
                                    @on-change="value => sort(value)"
                                    :options="['Name', 'Type', 'Modification time', 'Bigger to smaller', 'Smaller to bigger']"/>
                </div>

                <lava-button @click="layout === 'grid' ? layout = 'list' : layout = 'grid'">

                    <i :class="[layout === 'grid' ? 'text-md ri-grid-line' : 'text-md ri-list-check']"></i>

                </lava-button>
            </div>
            
        </div>

        <div class="flex justify-between w-full">

            <div class="flex w-full" :class="{'flex-col': layout === 'list', 'flex-wrap': layout === 'grid'}">

                <div v-if="layout === 'list'" class="flex items-center justify-between">
                    <div class="flex items-center justify-between">
                        <span style="width: 48px"></span>
                        <span>Name</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span style="width: 120px">Size</span>
                        <span style="width: 120px">Type</span>
                        <span style="width: 160px">Modified</span>
                        <span style="width: 24px"></span>
                    </div>
                </div>

                <div class="overflow-auto w-full" :class="{'flex content-start flex-wrap': layout === 'grid'}" style="height: 74.5vh">

                    <div v-for="file in files"
                        :key="file.path"
                        @contextmenu.prevent="handleClick($event, file)"
                        class="flex justify-between rounded bg-gray-300 shadow m-1 cursor-pointer hover:bg-gray-400 transition-all" 
                        :class="{ 'opacity-60': isInCut(file), 'w-full': layout === 'grid' }"
                        :style="layout === 'grid' ? 'min-width: 180px;max-width: 240px;height: 100px' : ''">

                        <div class="relative w-full p-2"
                            :class="{ 'flex items-center justify-between': layout === 'list'}">

                            <div @click="file.type === 'dir' ? getMedia(file.path) : showProperties(file)" 
                                class="absolute inset-0"
                                :class="{'left-5': layout === 'list'}"></div>

                            <div style="max-width: 160px" class="flex" :class="layout === 'grid' ? 'items-start' : 'items-center' ">

                                <input v-if="layout === 'list'" class="cursor-pointer" type="checkbox" v-model="selected" :value="file">

                                <div class="mx-1" :style="{'font-size': layout === 'grid' ? '38px' : '26px' }">
                                    <i v-if="file.type === 'dir'" class="ri-folder-line ltr:mr-1 rtl:ml-1"></i>
                                    <i v-else-if="file.archive" class="ri-file-zip-line ltr:mr-1 rtl:ml-1"></i>
                                    <i v-else class="ri-file-line ltr:mr-1 rtl:ml-1"></i>
                                </div>

                                <lava-tooltip :text="file.filename">
                                    <div style="max-width: 140px" class="truncate overflow-hidden" v-text="file.filename"></div>
                                    <div v-if="file.ext && layout === 'grid'" v-text="file.ext"></div>
                                    <div v-if="layout === 'grid'" v-text="human_filesize(file.size)"></div>
                                </lava-tooltip>

                            </div>

                            <div v-if="layout === 'list'" class="flex items-center justify-between">
                                <div style="width: 120px" v-text="human_filesize(file.size)"></div>
                                <div style="width: 120px" v-text="file.type === 'dir' ? 'Directory' : file.ext"></div>
                                <div style="width: 160px" v-text="file.modified"></div>
                            </div>

                            
                        </div>

                        <div v-if="layout === 'grid'" class="flex items-end h-full">
                            <input class="cursor-pointer mb-2 mr-2" type="checkbox" v-model="selected" :value="file">
                        </div>
            
                    </div>  

                </div>

            </div>
            
            <lava-card style="width: 500px" v-if="_.sumBy(statics, 'size') > 0">
                <template v-slot:header>
                    Analyze
                </template>
                <template v-slot:body>
                    <div v-for="(stat, index) in _.filter(statics, o => o.size > 0)" class="w-full">

                        <div class="flex my-1 w-full">
                            <i :class="stat.icon" style="font-size: 36px" class="mr-2" :style="{color: stat.color}"></i>
                            <div class="flex flex-col w-full">
                                <b style="font-size: 20px" :style="{color: stat.color}">{{_.capitalize(stat.label)}}</b>
                                <div class="flex items-center justify-between w-full ltr:mr-1 rtl:ml-1">
                                    <div>{{stat.count}} Files</div>
                                    <span>{{stat.human_size}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between w-full">
                            <div class="h-1 rounded" :style="{backgroundColor: stat.color, width: stat.size + '%'}"></div>
                            <div style="width: 52px" class="ltr:text-right rtl:text-left ltr:pl-1 rtl:pr-1">{{stat.size + ' %'}}</div>
                        </div>

                        <hr v-show="index < _.filter(statics, o => o.size > 0).length - 1">
                        
                    </div>
                </template>
            </lava-card>
            
        </div>

    </div>

    <span class="absolute right-0 bottom-0 p-1 bg-gray-200">
        <span v-if="selected && selected.length">
            {{selected.length}} Selected ({{sumSize()}})
        </span>
        <span v-else>
            {{_.size(files)}} Items ({{sumSize(files)}})
        </span>
    </span>

    <vue-simple-context-menu
        element-id="myUniqueId"
        :options="options(selected_file)"
        ref="vueSimpleContextMenu"
        @option-clicked="optionClicked"
    />
    </div>
</template>

<script>

export default {
    data: () => ({
            loading: false,
            current_path: '',
            current_file: null,
            previus: false,
            searching: false,
            files: [],
            selected: [],
            edit_mode: null,
            clipboard: null,
            operation: null,
            selected_file: null,
            statics: [],
            sort_type: 1,
            layout: 'grid'
        }),
        mounted(){
            
            this.getMedia()

        },
        computed: {
            allAreArchive(){
                return _.every(this.selected, {archive: true})
            }
        },
        methods: {
            options(file){

                if(file === null) {
                    return []
                }

                var options = []
                var labels  = [
                    file.type === 'dir' ? 'Open' : null,
                    file.editable ? 'Edit' : null,
                    'Copy',
                    'Cut',
                    file.archive ? 'Extract' : null,
                    'Compress',
                    'Rename',
                    'Delete',
                    'Properties'
                ]
                
                labels.filter(n => n).forEach(label => {
                    options.push({
                        name : label,
                        class: label === 'Delete' ? 'text-danger' : ''
                    })
                });

                return options

            },
            uploadMedia(event){

                this.uplaodFile(event.target?.files || [], {
                    path: this.current_path
                }).then(() => {
                    this.getMedia(this.current_path).then(() => Lava.toast('Upload completed', 'success'))
                })

            },
            getMedia(path = null){

                this.loading = true
                return this.$http.post('/api/media/get-media', { path }).then( res => {

                        setTimeout(() => this.loading = false, 300);
                        this.selected_file = null
                        this.current_path = res.data.path
                        this.files = res.data.list
                        this.sort(this.sort_type)
                        this.previus = res.data.previus
                        this.selected = []
                        if(this.$refs.searchBar.search && this.$refs.searchBar.search.length){
                            this.$refs.searchBar.clear()
                        }
                        this.searching = false
                        this.createStatics()
                        
                    }).catch(() => {
                        this.loading = false
                        this.searching = false
                    })

            },
            hideMenu(){

                this.selected_file = null

            },
            newFile(){

                Lava.confirm('New file', '', false, {
                    confirmButtonText: 'Create',
                    input: 'text',
                    inputLabel: 'File name',
                    inputPlaceholder: 'New File'
                }).then(res => {
                    if (res.isConfirmed){
                        this.$http.post('/api/media/new-file', { path: this.current_path , filename: res.value || 'New File'}).then( res => {
                            this.getMedia(this.current_path)
                        })
                    }
                });

            },
            newFolder(){

                Lava.confirm('Create folder', '', false, {
                    confirmButtonText: 'Create',
                    input: 'text',
                    inputLabel: 'Folder name',
                    inputPlaceholder: 'New Folder'
                }).then(res => {
                    if (res.isConfirmed){
                        this.$http.post('/api/media/new-folder', { path: this.current_path , name: res.value || 'New Folder'}).then( res => {
                            this.getMedia(this.current_path)
                        })
                    }
                });

            },
            deleteMedia(file = null){

                this.hideMenu()

                Lava.confirm('Delete file', '', true).then(res => {
                    if (res.isConfirmed){

                        Lava.showLoading(-1)

                        this.$http.post('/api/media/delete-media', { files: file ? [file]: this.selected , path: this.current_path}).then( res => {

                            this.current_file = null
                            this.selected = []
                            this.files    = res.data.list
                            Lava.showLoading(false)
                            this.sort(this.sort_type)

                        })
                    }
                });

            },
            renameMedia(file){

                this.hideMenu()

                Lava.confirm('New name', '', true, {
                    confirmButtonText: 'Rename',
                    input: 'text',
                    inputLabel: 'Enter new name',
                    inputPlaceholder: file.filename,
                    inputValue: file.filename + (!_.isEmpty(file.ext) ? ('.' + file.ext) : '')
                }).then(res => {
                    if (res.isConfirmed){

                        this.loading = true

                        if(res.value && res.value.length){
                            this.$http.post('/api/media/rename-media', { file, new_name: res.value , path: this.current_path}).then( res => {
                                this.files   = res.data.list
                                this.loading = false
                            })
                            return
                        }else{
                            Lava.toast('Please enter new name', 'error')
                        }
                        
                    }

                });

            },
            showEdit(file){

                this.loading = true

                this.$http.post('/api/media/get-content', { path: file.path}).then( res => {
                    file.content = res.data.content
                    this.edit_mode = file
                    this.loading = false
                })

            },
            edit(){

                Lava.showLoading(-1)

                this.$http.post('/api/media/edit-content', { file_path: this.edit_mode.path, path: this.current_path, content: this.edit_mode.content}).then( res => {
                    this.files     = res.data.list
                    this.sort(this.sort_type)
                    Lava.showLoading(false)
                    Lava.toast(this.edit_mode.filename + ' successfully edited.', "success")
                    this.edit_mode = null
                })

            },
            copy(file = null){

                this.clipboard = file ? [file] : this.selected
                this.operation = 'copy'
                this.selected = []
                this.selected_file = null
                
            },
            cut(file = null){

                this.clipboard = file ? [file] : this.selected
                this.operation = 'cut'
                this.selected = []
                this.selected_file = null

            },
            paste(){

                Lava.showLoading(-1)
                this.$http.post('/api/media/paste-media', {operation: this.operation, clipboard: this.clipboard, path: this.current_path}).then(res => {

                    this.clipboard = null
                    this.operation = null
                    this.getMedia(this.current_path)
                    Lava.showLoading(false)

                }).catch(() => {
                    this.loading = false
                    Lava.showLoading(false)
                })

            },
            sort(type = 1){

                if(type === 0) {
                    this.files = _.sortBy(this.files, ['filename'])
                }else if(type === 1){
                    this.files = _.sortBy(this.files, ['type'])
                }else if(type === 2){
                    this.files = _.sortBy(this.files, ['timestaps'])
                }else if(type === 3){
                    this.files = _.orderBy(this.files, ['size'], ['desc'])
                }else if(type === 4){
                    this.files = _.orderBy(this.files, ['size'], ['asc'])
                }

            },
            compress(file = null){
                
                this.hideMenu()

                var name = ''
                if(file){
                    name = file.filename + '_zip'
                }else{
                    name = (this.selected && this.selected.length ? 'new' : this.selected_file.filename) + '_zip'
                }

                Lava.confirm('Zip name', '', true, {
                    confirmButtonText: 'Compress',
                    input: 'text',
                    inputLabel: 'Enter zip name',
                    inputValue: name
                }).then(res => {
                    if (res.isConfirmed){

                        Lava.showLoading(-1)

                        var value = res.value

                        var hasName = value && value.length

                        if(!hasName)
                            value = name

                        this.$http.post('/api/media/compress-media', { clipboard: file ? [file] : this.selected, filename: value , path: this.current_path}).then( res => {
                            this.files    = res.data.list
                            Lava.showLoading(false)
                            this.selected = []
                        }).catch(() => {
                            Lava.showLoading(false)
                        })
                        
                    }

                });

            },
            extract(file = null){

                Lava.showLoading(-1)

                this.$http.post('/api/media/extract-media', { files: file ? [file] : this.selected, path: this.current_path}).then( res => {
                    Lava.showLoading(false)
                    this.selected = []
                    this.files    = res.data.list
                    this.sort(this.sort_type)
                }).catch(() => {
                    Lava.showLoading(false)
                })

            },
            isInCut(file){

                if(this.operation === 'cut' && _.find(this.clipboard , {full_path: file.full_path}))
                    return true

                return false
                
            },
            sumSize(files = null){

                return this.human_filesize(_.sumBy(files || this.selected, 'size'))

            },
            human_filesize(size) {
                var i = Math.floor( Math.log(size) / Math.log(1024) );

                if(i < 0){
                    i = 0
                }

                return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
            },
            search(search){

                Lava.showLoading(-1)
                if(search !== null && search.length > 0){
                    this.searching = true
                }

                this.$http.post('/api/media/search-media', { search , path: this.current_path}).then( res => {

                    Lava.showLoading(false)
                    this.current_path = res.data.path
                    this.previus = res.data.previus
                    this.files = res.data.list
                    this.sort(this.sort_type)

                }).catch(() => {
                    Lava.showLoading(false)
                })

            },
            goBack(){

                var last = this.current_path.split('/').reverse().splice(1).reverse().join('/')
                this.getMedia(last)

            },
            showProperties(file){
                this.hideMenu()
                this.current_file = file
            },
            getDisk(){

                var disk = _.get(_.trim(this.current_path || '', '/').split('/').reverse(), '0')

                if(disk == 'app'){
                    return null
                }

                return disk
            },
            createStatics(){

                this.$http.post('/api/media/get-statics').then(res => {

                    this.statics = _.map(res.data.response, stat => {
                        stat.human_size = this.human_filesize(stat.size)
                        stat.size = Math.round((stat.size / res.data.all_size) * 100) || 0
                        return stat
                    })

                })
                
            },
            handleClick (event, file) {
                if(!(this.selected && this.selected.length)){
                    this.selected_file = file
                    this.$refs.vueSimpleContextMenu.showMenu(event, file)
                }
            },
            optionClicked (event) {

                var file = event.item

                switch (event.option.name) {
                    case 'Open':
                        this.getMedia(file.path)
                        break;
                    case 'Edit':
                        this.showEdit(file)
                        break;
                    case 'Copy':
                        this.copy(file)
                        break;
                    case 'Cut':
                        this.cut(file)
                        break;
                    case 'Extract':
                        this.extract(file)
                        break;
                    case 'Compress':
                        this.compress(file)
                        break;
                    case 'Rename':
                        this.renameMedia(file)
                        break;
                    case 'Delete':
                        this.deleteMedia(file)
                        break;
                    case 'Properties':
                        this.showProperties(file)
                        break;
                }
            },
            isShowable(file){

                if(!file || file.type === 'dir'){
                    return false
                }

                return this.urlIsImage(file.url) || this.urlIsVideo(file.url) || this.urlIsAudio(file.url)

            },
            urlIsImage(url) {
                return(url.match(/\.(bmp|svg|jpg|jpeg|png|webp)$/) != null);
            },
            urlIsVideo(url) {

                return(url.match(/\.(mp4|mpg|mp2|mpeg|mov|avi|mkv|webm|flv|swf|wpd)$/) != null);

            },
            urlIsAudio(url) {

                return(url.match(/\.(mp3|aa|aiff|alac|mpc|mmf|wav|flac)$/) != null);

            }
        }
}
</script>