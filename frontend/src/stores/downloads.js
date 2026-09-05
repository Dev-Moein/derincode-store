import { defineStore } from 'pinia'
import api from '../services/api'

export const useDownloadsStore = defineStore(
    'downloads',
    {
        state: () => ({
            downloads: [],

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
            hasDownloads: (state) => {
                return state.downloads.length > 0
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
                    'دریافت دانلودها انجام نشد.'
            },

            async fetchMyDownloads(
                params = {}
            ) {
                this.loading = true
                this.clearErrors()

                try {
                    const response =
                        await api.get(
                            '/downloads',
                            {
                                params: {
                                    per_page: 10,
                                    ...params,
                                },
                            }
                        )

                    const data =
                        response?.data?.data

                    this.downloads =
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
                            this.downloads.length
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

            async downloadProject(
                projectId
            ) {
                this.clearErrors()

                try {
                    const response =
                        await api.get(
                            `/downloads/projects/${projectId}`,
                            {
                                responseType: 'blob',
                            }
                        )

                    const blob =
                        new Blob(
                            [response.data]
                        )

                    const url =
                        window.URL.createObjectURL(
                            blob
                        )

                    const link =
                        document.createElement('a')

                    link.href = url

                    link.download =
                        `project-${projectId}`

                    document.body.appendChild(link)

                    link.click()

                    link.remove()

                    window.URL.revokeObjectURL(
                        url
                    )

                    return {
                        success: true,
                    }
                } catch (error) {
                    this.setError(error)

                    return {
                        success: false,
                        error,
                    }
                }
            },
        },
    }
)
