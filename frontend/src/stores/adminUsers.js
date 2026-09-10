import { defineStore } from 'pinia'
import api from '../services/api'


export const useAdminUsersStore = defineStore(
    'adminUsers',
    {

        state: () => ({

            users: [],

            currentUser: null,

            loading: false,

            saving: false,

            error: null,

            errors: {},


            filters: {
                search: '',
                per_page: 12,
                page: 1,
            },


            pagination: {
                currentPage: 1,
                lastPage: 1,
                perPage: 12,
                total: 0,
            }

        }),



        getters: {

            hasUsers: (state) =>
                state.users.length > 0,


            userCount: (state) =>
                state.pagination.total,

        },



        actions: {


            clearErrors(){

                this.error = null
                this.errors = {}

            },



            setError(error){

                this.errors =
                    error?.response?.data?.errors || {}

                this.error =
                    error?.response?.data?.message ||
                    'خطایی رخ داد.'

            },



            async fetchUsers(){

                this.loading = true
                this.clearErrors()


                try {

                    const response =
                        await api.get(
                            '/admin/users',
                            {
                                params:{
                                    search:
                                        this.filters.search,

                                    per_page:
                                        this.filters.per_page,

                                    page:
                                        this.filters.page,
                                }
                            }
                        )


                    /*
                    Backend:

                    {
                        data:[
                            users
                        ],
                        meta:{}
                    }

                    */


                    this.users =
                        Array.isArray(response.data.data)
                            ? response.data.data
                            : []



                    const meta =
                        response.data.meta



                    if(meta){

                        this.pagination = {

                            currentPage:
                                meta.current_page ?? 1,


                            lastPage:
                                meta.last_page ?? 1,


                            perPage:
                                meta.per_page ?? 12,


                            total:
                                meta.total ?? this.users.length,

                        }


                    } else {

                        this.pagination.total =
                            this.users.length

                    }



                    return {
                        success:true
                    }



                } catch(error){


                    this.setError(error)


                    return {
                        success:false,
                        error
                    }



                } finally {


                    this.loading = false


                }

            },




            async fetchUser(id){

                this.loading = true
                this.clearErrors()


                try {


                    const response =
                        await api.get(
                            `/admin/users/${id}`
                        )



                    this.currentUser =
                        response.data.data



                    return {
                        success:true,
                        user:this.currentUser
                    }



                } catch(error){


                    this.setError(error)


                    return {
                        success:false,
                        error
                    }



                } finally {


                    this.loading = false


                }

            },




            async updateRole(
                id,
                role
            ){

                this.saving = true
                this.clearErrors()


                try {


                    const response =
                        await api.put(
                            `/admin/users/${id}/role`,
                            {
                                role
                            }
                        )



                    const user =
                        response.data.data




                    const index =
                        this.users.findIndex(
                            item =>
                                item.id === id
                        )



                    if(index !== -1){

                        this.users[index] = user

                    }



                    return {
                        success:true,
                        user
                    }



                } catch(error){


                    this.setError(error)


                    return {
                        success:false,
                        error
                    }



                } finally {


                    this.saving = false


                }

            },




            async deleteUser(id){


                this.saving = true
                this.clearErrors()



                try {



                    await api.delete(
                        `/admin/users/${id}`
                    )



                    this.users =
                        this.users.filter(
                            user =>
                                user.id !== id
                        )



                    this.pagination.total =
                        Math.max(
                            0,
                            this.pagination.total - 1
                        )



                    return {
                        success:true
                    }



                } catch(error){


                    this.setError(error)


                    return {
                        success:false,
                        error
                    }



                } finally {


                    this.saving = false


                }

            },


        },

    }
)
