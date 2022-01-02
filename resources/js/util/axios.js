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

        // Show the user a 500 error
        if (status >= 500) {
            Lava.alert(error.response.data.message, 'error')
        }

        // Handle Session Timeouts
        if (status === 401) {
            window.location.href = Lava.config.baseUrl
        }

        // Handle Forbidden
        if (status === 403) {
            // router.push({ name: '403' })
        }

        // Handle Token Timeouts
        if (status === 419) {
            Lava.tokenExpired()
        }

        return Promise.reject(error)
    }
)

export default instance
