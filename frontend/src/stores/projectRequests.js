import { defineStore } from 'pinia'
import api from '../services/api'


export const useProjectRequestsStore = defineStore(
    'projectRequests',
    {

        state: () => ({

            requests: [],

            currentRequest: null,

            loading: false,

            error: null,

            errors: {},


            pagination: {

                currentPage: 1,

                lastPage: 1,

                perPage: 10,

                total: 0,

            },

        }),



        getters: {

            pendingRequests: (state) => {

                return state.requests.filter(
                    (request) =>
                        request.status === 'pending'
                )

            },


            hasRequests: (state) => {

                return state.requests.length > 0

            },


            requestCount: (state) => {

                return state.pagination.total

            },

        },



        actions: {


            /*
            |--------------------------------------------------------------------------
            | Errors
            |--------------------------------------------------------------------------
            */

            clearErrors() {

                this.error = null

                this.errors = {}

            },


            setError(error) {

                this.errors =
                    error?.apiErrors ||
                    error?.response?.data?.errors ||
                    {}


                this.error =
                    error?.apiMessage ||
                    error?.response?.data?.message ||
                    error?.message ||
                    'عملیات انجام نشد.'

            },



            /*
            |--------------------------------------------------------------------------
            | Create Request
            |--------------------------------------------------------------------------
            */

            async createRequest(payload) {

                this.loading = true

                this.clearErrors()


                try {

                    const response =
                        await api.post(
                            '/project-requests',
                            payload
                        )


                    const request =
                        response?.data?.data


                    if (!request) {

                        throw new Error(
                            'Invalid project request response.'
                        )

                    }


                    this.currentRequest =
                        request


                    this.requests.unshift(
                        request
                    )


                    this.pagination.total += 1


                    return {

                        success: true,

                        request,

                        message:
                            response?.data?.message ||
                            'درخواست پروژه با موفقیت ثبت شد.',

                    }


                } catch (error) {

                    this.setError(error)


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
            | My Requests
            |--------------------------------------------------------------------------
            */

            async fetchMyRequests(
                params = {}
            ) {

                this.loading = true

                this.clearErrors()


                try {

                    const response =
                        await api.get(
                            '/project-requests',
                            {
                                params: {

                                    per_page:
                                        params.per_page ??
                                        this.pagination.perPage,

                                    page:
                                        params.page ??
                                        this.pagination.currentPage,

                                    ...params,

                                },
                            }
                        )


                    const data =
                        response?.data?.data


                    this.requests =
                        Array.isArray(data)
                            ? data
                            : []


                    const meta =
                        response?.data?.meta


                    if (meta) {

                        this.pagination = {

                            currentPage:
                                meta.current_page ?? 1,

                            lastPage:
                                meta.last_page ?? 1,

                            perPage:
                                meta.per_page ?? 10,

                            total:
                                meta.total ??
                                this.requests.length,

                        }

                    } else {

                        this.pagination = {

                            currentPage: 1,

                            lastPage: 1,

                            perPage:
                                this.requests.length ||
                                10,

                            total:
                                this.requests.length,

                        }

                    }


                    return {

                        success: true,

                        requests:
                            this.requests,

                    }


                } catch (error) {

                    this.setError(error)


                    this.requests = []


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
            | Admin Requests
            |--------------------------------------------------------------------------
            */

            async fetchAdminRequests(
                params = {}
            ) {

                this.loading = true

                this.clearErrors()


                try {

                    const response =
                        await api.get(
                            '/project-requests/admin',
                            {
                                params,
                            }
                        )


                    const data =
                        response?.data?.data


                    this.requests =
                        Array.isArray(data)
                            ? data
                            : []


                    const meta =
                        response?.data?.meta


                    if (meta) {

                        this.pagination = {

                            currentPage:
                                meta.current_page ?? 1,

                            lastPage:
                                meta.last_page ?? 1,

                            perPage:
                                meta.per_page ?? 10,

                            total:
                                meta.total ??
                                this.requests.length,

                        }

                    } else {

                        this.pagination.total =
                            this.requests.length

                    }


                    return {

                        success: true,

                        requests:
                            this.requests,

                    }


                } catch (error) {

                    this.setError(error)


                    this.requests = []


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
            | Update Request Status - Admin
            |--------------------------------------------------------------------------
            */

            async updateRequestStatus(
                id,
                payload
            ) {

                this.loading = true

                this.clearErrors()


                try {

                    const response =
                        await api.put(
                            `/project-requests/admin/${id}/status`,
                            payload
                        )


                    const updated =
                        response?.data?.data


                    const index =
                        this.requests.findIndex(
                            (item) =>
                                item.id === id
                        )


                    if (
                        index !== -1 &&
                        updated
                    ) {

                        this.requests[index] =
                            updated

                    }


                    if (
                        this.currentRequest?.id === id &&
                        updated
                    ) {

                        this.currentRequest =
                            updated

                    }


                    return {

                        success: true,

                        request: updated,

                        message:
                            response?.data?.message ||
                            'وضعیت درخواست با موفقیت تغییر کرد.',

                    }


                } catch (error) {

                    this.setError(error)


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
            | Fetch Single Request
            |--------------------------------------------------------------------------
            */

            async fetchRequest(id) {

                this.loading = true

                this.clearErrors()


                try {

                    const response =
                        await api.get(
                            `/project-requests/admin/${id}`
                        )


                    const request =
                        response?.data?.data


                    if (!request) {

                        throw new Error(
                            'Invalid project request response.'
                        )

                    }


                    this.currentRequest =
                        request


                    return {

                        success: true,

                        request,

                    }


                } catch (error) {

                    this.setError(error)


                    this.currentRequest = null


                    return {

                        success: false,

                        error,

                    }

                } finally {

                    this.loading = false

                }

            },

        },

    }
)
