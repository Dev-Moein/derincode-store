import axios from 'axios'


const api = axios.create({

    baseURL:
        import.meta.env.VITE_API_URL ||
        'http://localhost:8000/api/v1',

    headers: {

        Accept: 'application/json',

        'Content-Type': 'application/json',

    },

    timeout: 15000,

})



/*
|--------------------------------------------------------------------------
| Request Interceptor
|--------------------------------------------------------------------------
*/

api.interceptors.request.use(

    (config) => {

        const token =
            localStorage.getItem(
                'derincode_token'
            )


        if (token) {

            config.headers.Authorization =
                `Bearer ${token}`

        }


        return config

    },

    (error) => {

        return Promise.reject(error)

    }

)



/*
|--------------------------------------------------------------------------
| Response Interceptor
|--------------------------------------------------------------------------
*/

api.interceptors.response.use(

    (response) => {

        return response

    },


    async (error) => {

        /*
        |--------------------------------------------------------------------------
        | No response from server
        |--------------------------------------------------------------------------
        */

        if (!error.response) {

            error.isNetworkError = true

            error.apiMessage =
                'ارتباط با سرور برقرار نشد.'

            return Promise.reject(error)

        }


        const status =
            error.response.status


        /*
        |--------------------------------------------------------------------------
        | 401 - Unauthenticated
        |--------------------------------------------------------------------------
        */

        if (status === 401) {

            localStorage.removeItem(
                'derincode_token'
            )


            /*
            | Do not redirect automatically here.
            | Auth store / router can decide what to do.
            */

        }


        /*
        |--------------------------------------------------------------------------
        | 403 - Forbidden
        |--------------------------------------------------------------------------
        */

        if (status === 403) {

            error.apiMessage =
                error.response.data?.message ||
                'شما اجازه انجام این عملیات را ندارید.'

        }


        /*
        |--------------------------------------------------------------------------
        | 422 - Validation
        |--------------------------------------------------------------------------
        */

        if (status === 422) {

            error.apiErrors =
                error.response.data?.errors || {}


            error.apiMessage =
                error.response.data?.message ||
                'اطلاعات وارد شده صحیح نیست.'

        }


        /*
        |--------------------------------------------------------------------------
        | 429 - Too Many Requests
        |--------------------------------------------------------------------------
        */

        if (status === 429) {

            error.apiMessage =
                error.response.data?.message ||
                'تعداد درخواست‌ها بیش از حد مجاز است.'

        }


        /*
        |--------------------------------------------------------------------------
        | 500+
        |--------------------------------------------------------------------------
        */

        if (status >= 500) {

            error.apiMessage =
                error.response.data?.message ||
                'خطایی در سرور رخ داده است.'

        }


        return Promise.reject(error)

    }

)


export default api
