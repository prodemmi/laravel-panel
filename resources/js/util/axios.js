import axios from 'axios'
import {RouteMixin} from '../mixins'
// import router from '@/router'

const instance = axios.create()

instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
instance.defaults.headers.common['X-CSRF-TOKEN'] = $(document).attr('meta', 'csrf-token')
instance.defaults.baseURL = `/${window.baseUrl}`

instance.interceptors.response.use(
    response => {

        window.Lava.showLoading(false)

        showDumpError(response.data)

        return response

    },
    error => {
        const {status} = error.response

        window.Lava.showLoading(false)

        // Handle Session Timeouts
        if (status === 401) {
            window.location.href = window.baseUrl
        }

        // Handle Forbidden
        else if (status === 403) {
            // router.push({ name: '403' })
        }

        // Handle Token Timeouts
        else if (status === 419) {

            window.Lava.confirm("Sorry, your session has expired.", "error").then(() => {

                window.location = '/auth/logout'

            })

        }else if (status === 500){
        
            if(window.debug){

                var message = error.response.data.message;

                if(_.isString(error.response.data) && error.response.data?.includes('Sfdump')){
    
                    message = error.response.data
    
                }

                if(error.response.data?.file){

                    message = ''
                    message += 'Exception: <b>' + error.response.data?.exception + '</b> <br/>'
                    message += 'Message: <b style="color: red">' + error.response.data?.message + '</b> <br/>'
                    message += 'File: <b>' + error.response.data?.file + '</b> <br/>'
                    message += 'Line: <b>' + error.response.data?.line + '</b> <br/>'

                }

                showDumpError(_.isObject(message) ? JSON.stringify(message, null, 2) : message)

            }

        }

        return Promise.reject(error)
    }
)

function showDumpError(data){

    if(window.debug && _.isString(data) && data?.includes('Sfdump')){
        window.Lava.confirm('Dump', data, false, {
            showCancelButton: false,
            confirmButtonText: 'Ok',
            cancelButtonText: null,
            allowOutsideClick: true,
        })
        return true
    }

    return false

}

export default instance
