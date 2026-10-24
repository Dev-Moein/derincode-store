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


const translateApiMessage = (message, status) => {
    const knownMessages = {
        'Validation failed.': 'اطلاعات واردشده معتبر نیست. لطفاً موارد مشخص‌شده را بررسی کنید.',
        'Unauthenticated.': 'برای انجام این عملیات باید وارد حساب کاربری شوید.',
        'This project is not available for purchase.': 'این پروژه در حال حاضر برای خرید در دسترس نیست.',
        'You do not have permission to perform this action.': 'شما اجازه انجام این عملیات را ندارید.',
        'Resource not found.': 'مورد درخواستی پیدا نشد.',
        'Too many requests. Please try again later.': 'تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.',
        'Something went wrong.': 'خطایی در سرور رخ داده است. لطفاً کمی بعد دوباره تلاش کنید.',
        'The given data was invalid.': 'اطلاعات واردشده معتبر نیست. لطفاً موارد مشخص‌شده را بررسی کنید.',
        'The project is not available for purchase.': 'این پروژه در حال حاضر برای خرید در دسترس نیست.',
        'Invalid authentication response.': 'پاسخ ورود از سرور معتبر نیست. لطفاً دوباره تلاش کنید.',
        'Invalid registration response.': 'پاسخ ثبت‌نام از سرور معتبر نیست. لطفاً دوباره تلاش کنید.',
        'Invalid profile response.': 'دریافت اطلاعات حساب با مشکل مواجه شد.',
        'User is not authenticated.': 'برای انجام این عملیات ابتدا وارد حساب کاربری شوید.',
        'Request failed with status code 401': 'نشست شما منقضی شده است؛ دوباره وارد حساب شوید.',
        'Network Error': 'ارتباط با سرور برقرار نشد. اتصال اینترنت و وضعیت سرور را بررسی کنید.',
    }

    if (typeof message === 'string' && knownMessages[message]) {
        return knownMessages[message]
    }

    if (status === 401) {
        return 'نشست شما معتبر نیست یا منقضی شده است. لطفاً دوباره وارد حساب شوید.'
    }

    if (status === 403) {
        return 'شما اجازه انجام این عملیات را ندارید.'
    }

    if (status === 404) {
        return 'مورد درخواستی پیدا نشد یا دیگر در دسترس نیست.'
    }

    if (status === 409) {
        return 'این درخواست با وضعیت فعلی اطلاعات سازگار نیست. صفحه را تازه‌سازی و دوباره تلاش کنید.'
    }

    if (status === 422) {
        return 'اطلاعات واردشده معتبر نیست. لطفاً موارد مشخص‌شده را بررسی کنید.'
    }

    if (status === 429) {
        return 'تعداد درخواست‌ها بیش از حد مجاز است. لطفاً کمی بعد دوباره تلاش کنید.'
    }

    if (status >= 500) {
        return 'خطایی در سرور رخ داده است. لطفاً کمی بعد دوباره تلاش کنید.'
    }

    return typeof message === 'string' && /[\u0600-\u06FF]/.test(message)
        ? message
        : 'انجام عملیات با مشکل مواجه شد. لطفاً دوباره تلاش کنید.'
}


/*
|--------------------------------------------------------------------------
| Request Interceptor
|--------------------------------------------------------------------------
*/
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('derincode_token')

        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }

        return config
    },
    (error) => Promise.reject(error)
)


/*
|--------------------------------------------------------------------------
| Response Interceptor
|--------------------------------------------------------------------------
*/
api.interceptors.response.use(
    (response) => response,
    async (error) => {
        if (!error.response) {
            error.isNetworkError = true
            error.apiMessage = translateApiMessage(error.message)
            return Promise.reject(error)
        }

        const status = error.response.status
        const responseMessage = error.response.data?.message

        error.apiMessage = translateApiMessage(responseMessage, status)

        if (status === 401) {
            localStorage.removeItem('derincode_token')
        }

        if (status === 422) {
            error.apiErrors = error.response.data?.errors || {}
        }

        return Promise.reject(error)
    }
)


export default api
