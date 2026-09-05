import { defineStore } from 'pinia'
import api from '../services/api'

const TOKEN_KEY = 'derincode_token'
const USER_KEY = 'derincode_user'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(
            localStorage.getItem(USER_KEY) || 'null'
        ),

        token:
            localStorage.getItem(TOKEN_KEY) || null,
        purchasedProjects: [],
        loading: false,
        initialized: false,

        errors: {},
        errorMessage: null,

        forgotEmail: '',
        verifiedOtp: '',
    }),

    getters: {
        isAuthenticated: (state) => {
            return Boolean(state.token)
        },

        isAdmin: (state) => {
            return (
                Array.isArray(state.user?.roles) &&
                state.user.roles.includes('admin')
            )
        },

        userName: (state) => {
            return state.user?.name || ''
        },

        userEmail: (state) => {
            return state.user?.email || ''
        },
    },

    actions: {
        /*
        |--------------------------------------------------------------------------
        | Persistence
        |--------------------------------------------------------------------------
        */

        persistAuth() {
            if (this.token) {
                localStorage.setItem(
                    TOKEN_KEY,
                    this.token
                )
            } else {
                localStorage.removeItem(TOKEN_KEY)
            }

            if (this.user) {
                localStorage.setItem(
                    USER_KEY,
                    JSON.stringify(this.user)
                )
            } else {
                localStorage.removeItem(USER_KEY)
            }
        },

        setAuth(user, token) {
            this.user = user
            this.token = token

            this.persistAuth()
        },

        /*
        |--------------------------------------------------------------------------
        | Errors
        |--------------------------------------------------------------------------
        */

        clearErrors() {
            this.errors = {}
            this.errorMessage = null
        },

        setErrors(error) {
            this.errors =
                error?.response?.data?.errors || {}

            this.errorMessage =
                error?.response?.data?.message ||
                null
        },

        /*
        |--------------------------------------------------------------------------
        | Password Recovery
        |--------------------------------------------------------------------------
        */

        setPasswordRecovery(email) {
            this.forgotEmail = email
            this.verifiedOtp = ''
        },

        setVerifiedOtp(otp) {
            this.verifiedOtp = otp
        },

        clearPasswordRecovery() {
            this.forgotEmail = ''
            this.verifiedOtp = ''
        },

        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        async login(credentials) {
            this.loading = true
            this.clearErrors()

            try {
                const response = await api.post(
                    '/auth/login',
                    credentials
                )

                const data =
                    response?.data?.data

                if (
                    !data?.token ||
                    !data?.user
                ) {
                    throw new Error(
                        'Invalid authentication response.'
                    )
                }

                this.setAuth(
                    data.user,
                    data.token
                )

                return {
                    success: true,
                    message:
                        response?.data?.message ||
                        'ورود با موفقیت انجام شد.',
                }
            } catch (error) {
                this.setErrors(error)

                return {
                    success: false,
                    error,
                }
            } finally {
                this.loading = false
            }
        },

        /*
        |--------------------------------------------------------------------------
        | Register
        |--------------------------------------------------------------------------
        */

        async register(payload) {
            this.loading = true
            this.clearErrors()

            try {
                const response = await api.post(
                    '/auth/register',
                    payload
                )

                const data =
                    response?.data?.data

                if (
                    !data?.token ||
                    !data?.user
                ) {
                    throw new Error(
                        'Invalid registration response.'
                    )
                }

                this.setAuth(
                    data.user,
                    data.token
                )

                return {
                    success: true,
                    message:
                        response?.data?.message ||
                        'ثبت‌نام با موفقیت انجام شد.',
                }
            } catch (error) {
                this.setErrors(error)

                return {
                    success: false,
                    error,
                }
            } finally {
                this.loading = false
            }
        },

        /*
        |--------------------------------------------------------------------------
        | Current Auth User
        |--------------------------------------------------------------------------
        */

        async fetchUser() {
            if (!this.token) {
                this.initialized = true
                return false
            }

            try {
                this.clearErrors()

                const response = await api.get(
                    '/auth/me'
                )

                const user =
                    response?.data?.data?.user

                if (!user) {
                    throw new Error(
                        'Invalid user response.'
                    )
                }

                this.user = user

                this.persistAuth()

                return true
            } catch (error) {
                this.clearAuth()

                return false
            } finally {
                this.initialized = true
            }
        },

        /*
        |--------------------------------------------------------------------------
        | Profile
        |--------------------------------------------------------------------------
        */

        async fetchProfile() {
            if (!this.token) {
                return {
                    success: false,
                    error: new Error(
                        'User is not authenticated.'
                    ),
                }
            }

            this.loading = true
            this.clearErrors()

            try {
                const response = await api.get(
                    '/profile'
                )

                const data =
    response?.data?.data

const user = data?.user

if (!user) {
    throw new Error(
        'Invalid profile response.'
    )
}

this.user = user

this.purchasedProjects =
    Array.isArray(data?.purchased_projects)
        ? data.purchased_projects
        : []

                this.persistAuth()

                return {
    success: true,
    user,
    purchasedProjects:
        this.purchasedProjects,
    message:
        response?.data?.message ||
        'پروفایل دریافت شد.',
}
            } catch (error) {
                this.setErrors(error)

                /*
                 * If token is no longer valid,
                 * clear local authentication.
                 */
                if (
                    error?.response?.status === 401
                ) {
                    this.clearAuth()
                }

                return {
                    success: false,
                    error,
                }
            } finally {
                this.loading = false
            }
        },

        async updateProfile(payload) {
            this.loading = true
            this.clearErrors()

            try {
                const response = await api.put(
                    '/profile',
                    payload
                )

                const user =
                    response?.data?.data?.user

                if (!user) {
                    throw new Error(
                        'Invalid profile response.'
                    )
                }

                this.user = user

                this.persistAuth()

                return {
                    success: true,
                    user,
                    message:
                        response?.data?.message ||
                        'پروفایل با موفقیت به‌روزرسانی شد.',
                }
            } catch (error) {
                this.setErrors(error)

                return {
                    success: false,
                    error,
                }
            } finally {
                this.loading = false
            }
        },

        async changePassword(payload) {
            this.loading = true
            this.clearErrors()

            try {
                const response = await api.put(
                    '/profile/password',
                    payload
                )

                return {
                    success: true,
                    message:
                        response?.data?.message ||
                        'رمز عبور با موفقیت تغییر کرد.',
                }
            } catch (error) {
                this.setErrors(error)

                return {
                    success: false,
                    error,
                }
            } finally {
                this.loading = false
            }
        },

        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        async logout() {
            try {
                if (this.token) {
                    await api.post(
                        '/auth/logout'
                    )
                }
            } catch (error) {
                console.error(
                    'Logout request failed:',
                    error
                )
            } finally {
                this.clearAuth()
            }
        },

clearAuth() {
    this.user = null
    this.token = null
    this.purchasedProjects = []

    this.clearErrors()
    this.clearPasswordRecovery()

    localStorage.removeItem(TOKEN_KEY)
    localStorage.removeItem(USER_KEY)
},

        /*
        |--------------------------------------------------------------------------
        | Forgot Password
        |--------------------------------------------------------------------------
        */

        async forgotPassword(email) {
            this.loading = true
            this.clearErrors()

            try {
                const response = await api.post(
                    '/auth/forgot-password',
                    {
                        email,
                    }
                )

                this.setPasswordRecovery(email)

                return {
                    success: true,
                    message:
                        response?.data?.message ||
                        'در صورت وجود ایمیل، کد ارسال شد.',
                }
            } catch (error) {
                this.setErrors(error)

                return {
                    success: false,
                    error,
                }
            } finally {
                this.loading = false
            }
        },

        /*
        |--------------------------------------------------------------------------
        | Verify OTP
        |--------------------------------------------------------------------------
        */

        async verifyOtp(email, otp) {
            this.loading = true
            this.clearErrors()

            try {
                const response = await api.post(
                    '/auth/verify-otp',
                    {
                        email,
                        otp,
                    }
                )

                this.forgotEmail = email
                this.setVerifiedOtp(otp)

                return {
                    success: true,
                    message:
                        response?.data?.message ||
                        'کد تأیید شد.',
                }
            } catch (error) {
                this.setErrors(error)

                return {
                    success: false,
                    error,
                }
            } finally {
                this.loading = false
            }
        },

        /*
        |--------------------------------------------------------------------------
        | Reset Password
        |--------------------------------------------------------------------------
        */

        async resetPassword(payload) {
            this.loading = true
            this.clearErrors()

            try {
                const response = await api.post(
                    '/auth/reset-password',
                    payload
                )

                this.clearPasswordRecovery()

                return {
                    success: true,
                    message:
                        response?.data?.message ||
                        'رمز عبور با موفقیت تغییر کرد.',
                }
            } catch (error) {
                this.setErrors(error)

                return {
                    success: false,
                    error,
                }
            } finally {
                this.loading = false
            }
        },
    },
})
