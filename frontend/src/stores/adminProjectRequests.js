import { defineStore } from 'pinia'
import api from '../services/api'


export const useAdminProjectRequestsStore = defineStore(
    'adminProjectRequests',
    {

        state: () => ({

            requests: [],

            currentRequest: null,

            loading: false,

            saving: false,

            error: null,

            errors: {},


            filters: {

                search: '',

                status: '',

                per_page: 12,

                page: 1,

            },


            pagination: {

                currentPage: 1,

                lastPage: 1,

                perPage: 12,

                total: 0,

            },

        }),



        getters: {

            hasRequests: (state) =>
                state.requests.length > 0,


            isEmpty: (state) =>
                !state.loading &&
                state.requests.length === 0,

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
            | Fetch Admin Requests
            |--------------------------------------------------------------------------
            */

            async fetchRequests() {

                this.loading = true

                this.clearErrors()


                try {

                    const params = {

                        page:
                            this.filters.page,

                        per_page:
                            this.filters.per_page,

                    }


                    if (
                        this.filters.search
                    ) {

                        params.search =
                            this.filters.search

                    }


                    if (
                        this.filters.status
                    ) {

                        params.status =
                            this.filters.status

                    }


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
                                meta.per_page ??
                                this.filters.per_page,

                            total:
                                meta.total ??
                                this.requests.length,

                        }

                    } else {

                        this.pagination = {

                            currentPage: 1,

                            lastPage: 1,

                            perPage:
                                this.filters.per_page,

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
            | Fetch Single Request
            |--------------------------------------------------------------------------
            */

            async fetchRequest(id) {

                this.loading = true

                this.clearErrors()


                try {

                    if (!id) {

                        throw new Error(
                            'Request ID is required.'
                        )

                    }


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



            /*
            |--------------------------------------------------------------------------
            | Update Request Status
            |--------------------------------------------------------------------------
            */

            async updateStatus(
                id,
                status,
                adminNote = null
            ) {

                this.saving = true

                this.clearErrors()


                try {

                    if (!id) {

                        throw new Error(
                            'Request ID is required.'
                        )

                    }


                    if (!status) {

                        throw new Error(
                            'Request status is required.'
                        )

                    }


                    const response =
                        await api.put(
                            `/project-requests/admin/${id}/status`,
                            {

                                status,

                                admin_note:
                                    adminNote,

                            }
                        )


                    const request =
                        response?.data?.data


                    if (!request) {

                        throw new Error(
                            'Invalid updated request response.'
                        )

                    }


                    this.currentRequest =
                        request


                    const index =
                        this.requests.findIndex(
                            (item) =>
                                item.id === id
                        )


                    if (index !== -1) {

                        this.requests[index] =
                            request

                    }


                    return {

                        success: true,

                        request,

                        message:
                            response?.data?.message ||
                            'وضعیت درخواست به‌روزرسانی شد.',

                    }


                } catch (error) {

                    this.setError(error)


                    return {

                        success: false,

                        error,

                    }

                } finally {

                    this.saving = false

                }

            },


        },

    }
)
