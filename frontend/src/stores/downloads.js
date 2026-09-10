import { defineStore } from 'pinia'
import api from '../services/api'


export const useDownloadsStore = defineStore(
    'downloads',
    {

        state: () => ({

            downloads: [],

            loading: false,

            downloadLoading: false,

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

            hasDownloads: (state) =>
                state.downloads.length > 0,

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
                    'عملیات دانلود انجام نشد.'

            },



            /*
            |--------------------------------------------------------------------------
            | My Downloads
            |--------------------------------------------------------------------------
            */

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


                    this.downloads =
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
                                this.downloads.length,

                        }

                    } else {

                        this.pagination = {

                            currentPage: 1,

                            lastPage: 1,

                            perPage:
                                this.downloads.length ||
                                10,

                            total:
                                this.downloads.length,

                        }

                    }


                    return {

                        success: true,

                        downloads:
                            this.downloads,

                    }


                } catch (error) {

                    this.setError(error)

                    this.downloads = []

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
            | Download Project
            |--------------------------------------------------------------------------
            */

            async downloadProject(
                projectId
            ) {

                this.downloadLoading = true

                this.clearErrors()


                try {

                    if (!projectId) {

                        throw new Error(
                            'Project ID is required.'
                        )

                    }


                    const response =
                        await api.get(
                            `/downloads/projects/${projectId}`,
                            {
                                responseType: 'blob',
                            }
                        )


                    /*
                    |--------------------------------------------------------------------------
                    | Check whether the response is actually an error
                    |--------------------------------------------------------------------------
                    |
                    | Some APIs return JSON errors while axios expects a Blob.
                    | In that case we convert the Blob back to JSON.
                    |
                    */

                    const contentType =
                        response?.headers?.[
                            'content-type'
                        ] || ''


                    if (
                        contentType.includes(
                            'application/json'
                        )
                    ) {

                        const text =
                            await response.data.text()


                        const json =
                            JSON.parse(text)


                        const error =
                            new Error(
                                json?.message ||
                                'دانلود پروژه انجام نشد.'
                            )


                        error.response = {

                            status:
                                response.status,

                            data: json,

                        }


                        throw error

                    }


                    const blob =
                        new Blob(
                            [response.data],
                            {
                                type:
                                    contentType ||
                                    'application/octet-stream',
                            }
                        )


                    const url =
                        window.URL.createObjectURL(
                            blob
                        )


                    const link =
                        document.createElement('a')


                    link.href = url


                    /*
                    |--------------------------------------------------------------------------
                    | Filename
                    |--------------------------------------------------------------------------
                    */

                    const contentDisposition =
                        response?.headers?.[
                            'content-disposition'
                        ] || ''


                    const filenameMatch =
                        contentDisposition.match(
                            /filename\*?=(?:UTF-8'')?["']?([^;"']+)["']?/i
                        )


                    const filename =
                        filenameMatch?.[1]
                            ? decodeURIComponent(
                                filenameMatch[1]
                            )
                            : `project-${projectId}`


                    link.download =
                        filename


                    document.body.appendChild(
                        link
                    )


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

                } finally {

                    this.downloadLoading = false

                }

            },

        },

    }
)
