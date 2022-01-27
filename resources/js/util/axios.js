import axios from 'axios'
// import router from '@/router'

const instance = axios.create()

instance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
instance.defaults.headers.common['X-CSRF-TOKEN'] = $(document).attr('meta', 'csrf-token')
instance.defaults.baseURL = `/${window.config.baseUrl}`

instance.interceptors.response.use(
    response => response,
    error => {
        const {status} = error.response

        // Handle Session Timeouts
        if (status === 401) {
            window.location.href = Lava.config.baseUrl
        }

        // Handle Forbidden
        else if (status === 403) {
            // router.push({ name: '403' })
        }

        // Handle Token Timeouts
        else if (status === 419) {

            Lava.confirm("Sorry, your session has expired.", "error").then(() => {

                window.location = '/auth/logout'

            })

        }

        return Promise.reject(error)
    }
)

export default instance
