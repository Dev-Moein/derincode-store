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
            clearErrors() {
                this.error = null
                this.errors = {}
            },

            setError(error) {
                this.errors =
                    error?.response?.data?.errors || {}

                this.error =
                    error?.response?.data?.message ||
                    'عملیات انجام نشد.'
            },

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

                    this.currentRequest = request

                    this.requests.unshift(request)

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

            async fetchMyRequests(params = {}) {
                this.loading = true
                this.clearErrors()

                try {
                    const response =
                        await api.get(
                            '/project-requests',
                            {
                                params: {
                                    per_page: 10,
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
                        this.pagination.currentPage =
                            meta.current_page ?? 1

                        this.pagination.lastPage =
                            meta.last_page ?? 1

                        this.pagination.perPage =
                            meta.per_page ?? 10

                        this.pagination.total =
                            meta.total ??
                            this.requests.length
                    }

                    return {
                        success: true,
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
        },
    }
)