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


const fieldLabels = {
    name: 'نام و نام خانوادگی',
    email: 'ایمیل',
    phone: 'شماره تماس',
    password: 'رمز عبور',
    password_confirmation: 'تکرار رمز عبور',
    title: 'عنوان',
    slug: 'شناسه',
    short_description: 'توضیح کوتاه',
    description: 'توضیحات',
    price: 'قیمت',
    currency: 'واحد پول',
    status: 'وضعیت',
    file: 'فایل',
    images: 'تصاویر',
    project: 'پروژه',
    project_id: 'پروژه',
    user_id: 'کاربر',
    role: 'نقش کاربر',
    budget: 'بودجه',
    admin_note: 'یادداشت مدیر',
    otp: 'کد تأیید',
}

const translateValidationMessage = (message, key) => {
    if (typeof message !== 'string' || /[\u0600-\u06FF]/.test(message)) {
        return message
    }

    const field = fieldLabels[key?.split('.')[0]] || 'این فیلد'
    const rules = [
        [/^The (.+) field is required\.?$/i, `وارد کردن ${field} الزامی است.`],
        [/^The (.+) field must be a string\.?$/i, `${field} باید متن باشد.`],
        [/^The (.+) field must be a valid email address\.?$/i, 'فرمت ایمیل واردشده معتبر نیست.'],
        [/^The (.+) field must be a number\.?$/i, `${field} باید عددی باشد.`],
        [/^The (.+) field must be an integer\.?$/i, `${field} باید عدد صحیح باشد.`],
        [/^The (.+) field must be at least (\d+) characters?\.?$/i, null],
        [/^The (.+) field must not be greater than (\d+) characters?\.?$/i, null],
        [/^The (.+) field has already been taken\.?$/i, `این ${field} قبلاً ثبت شده است.`],
        [/^The (.+) field confirmation does not match\.?$/i, 'تأیید رمز عبور با مقدار واردشده مطابقت ندارد.'],
        [/^The selected (.+) is invalid\.?$/i, `مقدار انتخاب‌شده برای ${field} معتبر نیست.`],
        [/^The (.+) field must be a file\.?$/i, `${field} باید یک فایل باشد.`],
        [/^The (.+) field must be a valid URL\.?$/i, `آدرس ${field} معتبر نیست.`],
    ]

    for (const [pattern, translated] of rules) {
        const match = message.match(pattern)
        if (!match) continue

        if (pattern.source.includes('at least')) {
            return `${field} باید حداقل ${match[2]} نویسه باشد.`
        }

        if (pattern.source.includes('not be greater')) {
            return `طول ${field} نباید بیشتر از ${match[2]} نویسه باشد.`
        }

        return translated
    }

    return translateApiMessage(message, 422)
}


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

        // Keep legacy components that read response.data.message in Persian too.
        if (error.response.data && typeof error.response.data === 'object') {
            error.response.data.message = error.apiMessage
        }

        if (status === 401) {
            localStorage.removeItem('derincode_token')
        }

        if (status === 422) {
            const rawErrors = error.response.data?.errors || {}

            error.apiErrors = Object.fromEntries(
                Object.entries(rawErrors).map(([key, messages]) => [
                    key,
                    (Array.isArray(messages) ? messages : [messages])
                        .map((message) => translateValidationMessage(message, key)),
                ])
            )
        }

        return Promise.reject(error)
    }
)


export default api
