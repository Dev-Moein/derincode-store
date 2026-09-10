import { defineStore } from 'pinia'
import api from '../services/api'


export const useProjectsStore = defineStore(
    'projects',
    {

        state: () => ({

            projects: [],

            loading: false,

            error: null,


            pagination: {

                currentPage: 1,

                lastPage: 1,

                perPage: 6,

                total: 0,

            },

        }),



        getters: {

            featuredProjects: (state) =>
                state.projects,

        },



        actions: {


            async fetchFeaturedProjects() {

                return await this.fetchProjects({
                    is_featured: 1,
                    per_page: 4,
                })

            },



            async fetchProjects(
                params = {}
            ) {

                this.loading = true

                this.error = null


                try {


                    const response =
                        await api.get(
                            '/projects',
                            {
                                params: {

                                    per_page:
                                        params.per_page ||
                                        this.pagination.perPage,

                                    ...params,

                                },
                            }
                        )



                    const data =
                        response?.data?.data



                    this.projects =
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
                                this.pagination.perPage,


                            total:
                                meta.total ??
                                this.projects.length,

                        }


                    } else {


                        this.pagination = {

                            currentPage: 1,

                            lastPage: 1,

                            perPage:
                                this.projects.length,

                            total:
                                this.projects.length,

                        }


                    }



                    return {

                        success: true,

                        projects:
                            this.projects,

                    }



                } catch(error) {


                    console.error(
                        'Failed to fetch projects:',
                        error
                    )



                    this.error =
                        error?.apiMessage ||
                        error?.response?.data?.message ||
                        'دریافت پروژه‌ها با مشکل مواجه شد.'



                    this.projects = []



                    return {

                        success:false,

                        error,

                    }



                } finally {


                    this.loading = false


                }

            },


        },

    }
)
