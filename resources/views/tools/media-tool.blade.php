<template>
    <div id="media-tool">

        <lava-dialog :show="current_file"
                     :danger="true"
                     width="30vw"
                     height="70vh"
                     :close-on-click-outside="true"
                     confirm-label="Delete"
                     :cancel-label="current_file && current_file.editable ? 'Edit': null"
                     @on-continue="deleteFile(current_file)"
                     @on-cancel="edit = true"
                     @on-close="current_file = null">

            <template v-slot:header>

                @{{ current_file.filename }}

            </template>

            <template v-slot:body>

                <div class="flex flex-col items-center m-auto p-2 w-full h-full">

                    <div class="mb-4">
                        <img :src="current_file.url" style="width: 100%;height: 300px" class="w-full h-full object-scale-down">
                        {{-- <audio :src="current_file.url" style="width: 100%;height: 400px" class="w-full h-full object-scale-down"></audio>
                        <video :src="current_file.url" style="width: 100%;height: 400px" class="w-full h-full object-scale-down"></video> --}}
                    </div>
    
                    <div class="w-full break-all overflow-y-auto">
                        <div class="w-full">Filename: @{{current_file.filename}}</div>
                        <div class="w-full">Path: @{{current_file.full_path}}</div>
                        <div class="w-full">Size: @{{current_file.size}}</div>
                        <div class="w-full">Extension: @{{current_file.ext}}</div>
                        <div class="w-full">
                            <a class="cursor-pointer text-blue-700 w-full" @click="copyToClipboard(current_file.url)">@{{current_file.url}} </a>
                            Click to copy.
                        </div>
                        <div>Modified: @{{current_file.modified}}</div>
                    </div>

                </div>

            </template>

            </lava-dialog>

        <div v-if="selected && selected.length > 0" class="flex items-center justify-between">

            <lava-button @click="newFolder">Compress</lava-button>
            <lava-button @click="newFile" color="red-600" >Delete</lava-button>
            <lava-button @click="newFile" >Rename</lava-button>
            
        </div>
        
        <div v-else class="flex items-center justify-between">
            <div>
                <span v-if="searching" class="px-2">@{{_.size(files)}} Items found</span>
                <lava-button v-if="!searching">
                    <lava-file-input :multiple="true" 
                                     placeholder="Uplaod file"
                                     @on-change="upload">
                    </lava-file-input>
                    
                </lava-button>
                <lava-button v-if="!searching"
                             @click="getMedia(current_path)"
                             :loading="loading"
                             :no-padding="true">
                    <i class="ri-refresh-line"></i>
                </lava-button>
            </div>
    
            <div class="flex items-center justify-between">
                <lava-search-bar :search-in="'*'" @on-search="search"></lava-search-bar>
                <lava-button v-if="!searching" @click="newFolder">New Folder</lava-button>
                <lava-button v-if="!searching" @click="newFile">New File</lava-button>
            </div>
        </div>

        <div class="flex flex-col">
        
            <div class="flex items-center">
    
                <i v-if="previus"
                   @click="goBack()" class="cursor-pointer text-lg w-fit" 
                   :class="$store.getters.getConfig.rtl ? 'ri-arrow-right-line': 'ri-arrow-left-line'"></i>

                <lava-breadcrumb :path="breadcrumb"></lava-breadcrumb>
                
            </div>
    
            <div class="flex flex-wrap">

                <div v-for="file in _.sortBy(files, {directory: false})" 
                     class="flex justify-between rounded bg-gray-300 shadow p-2 m-1 w-full cursor-pointer" 
                     style="min-width: 180px;max-width: 240px;height: 100px">

                    <div class="w-full h-full" @click="file.directory ? getMedia(file.full_path) : showDetail(file)">

                        <template v-if="file.directory">
    
                            <i class="ri-folder-line text-2xl"></i>
                            <lava-tooltip :text="file.filename">
                                <div class="text-blue-400 bold text-xl truncate overflow-hidden" v-text="file.filename"></div>
                            </lava-tooltip>
                            <div v-text="file.size"></div>
                            
                        </template>
        
                        <template v-else>
        
                            <i v-if="file.link" class="ri-link-m text-2xl"></i>
                            <i v-else class="ri-file-line text-2xl"></i>
                            <lava-tooltip :text="file.filename">
                                <div class="truncate overflow-hidden" v-text="file.filename"></div>
                            </lava-tooltip>
                            <div v-text="file.size"></div>
                            <div v-text="file.ext"></div>
        
                        </template>

                    </div>

                    <div class="flex flex-col items-center justify-between w-auto h-full">
                        <div class="flex flex-col items-center">
                            <i class="ri-delete-bin-line hover:text-danger w-fit h-fit" @click="deleteFile(file)"></i>
                            <i v-if="file.directory"
                            class="hover:text-gray-400 w-fit h-fit" 
                            :class="file.directory ? 'ri-folder-info-line' : 'ri-file-info-line'" 
                            @click="showDetail(file)"></i>
                        </div>
                        <input class="cursor-pointer" type="checkbox" v-model="selected" :value="file">
                    </div>
        
                </div>

            </div>

        </div>
        @{{selected}}
        
    </div>
</template>

<script>

    var mediaTool = CreateLavaApp('#media-tool',{
        data: () => ({
            loading: false,
            current_path: '',
            current_file: null,
            previus: false,
            searching: false,
            files: [],
            selected: [],
            breadcrumb: null
        }),
        mounted(){

            this.getMedia()

        },
        methods: {
            upload(event){

                this.uplaodFile(event.target?.files || [], {
                    path: this.current_path,
                    disk: this.getDisk()
                }).then(() => {
                    this.getMedia(this.current_path)
                })

            },
            getMedia(path = null){

                this.loading = true
                this.$http.post('/api/media/get-media', { path }).then( res => {

                        this.loading = false
                        this.current_path = res.data.path
                        this.previus = res.data.previus
                        this.files = res.data.list
                        this.breadcrumb = res.data.breadcrumb
                    })

            },
            newFile(){

                Lava.confirm('New file', '', false, {
                    confirmButtonText: 'Create',
                    input: 'text',
                    inputLabel: 'File name'
                }).then(res => {
                    if (res.isConfirmed){
                        this.loading = true
                        this.$http.post('/api/media/new-file', { path: this.current_path , filename: res.value}).then( res => {

                                this.loading = false
                                this.current_path = res.data.path
                                this.previus = res.data.previus
                                this.files = res.data.list

                        })
                    }
                });

            },
            newFolder(){

                Lava.confirm('Create folder', '', false, {
                    confirmButtonText: 'Create',
                    input: 'text',
                    inputLabel: 'Folder name'
                }).then(res => {
                    if (res.isConfirmed){
                        this.loading = true
                        this.$http.post('/api/media/new-folder', { path: this.current_path , name: res.value}).then( res => {

                                this.loading = false
                                this.current_path = res.data.path
                                this.previus = res.data.previus
                                this.files = res.data.list

                        })
                    }
                });

            },
            deleteFile(file){

                Lava.confirm('Delete file', '', true).then(res => {
                    if (res.isConfirmed){
                        this.loading = true
                        this.$http.post('/api/media/delete-media', { path: file.full_path, is_dire: file.directory }).then( res => {

                            if(res.data){
                                this.current_file = null
                                this.getMedia()
                            }

                        })
                    }
                });

            },
            search(search){

                if(search !== null && search.length > 0){
                    this.searching = true
                }else{
                    this.searching = false
                    this.getMedia(this.current_path)
                    return
                }

                this.loading = true
                this.$http.post('/api/media/search-media', { search , path: this.current_path}).then( res => {

                    this.loading = false
                    this.current_path = res.data.path
                    this.previus = res.data.previus
                    this.files = res.data.list
                    this.breadcrumb = res.data.breadcrumb

                })

            },
            goBack(){

                var last = this.current_path.split('/').reverse().splice(1).reverse().join('/')
                this.getMedia(last)

            },
            showDetail(file){
                this.current_file = file
            },
            getDisk(){

                var disk = _.get(_.trim(this.current_path || '', '/').split('/').reverse(), '0')

                if(disk == 'app'){
                    return null
                }

                return disk
            }
        }
    })

</script>