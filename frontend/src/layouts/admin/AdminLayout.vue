<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
</script>


<template>

<div class="admin-layout">


    <aside class="sidebar">


        <div class="logo">
            Derin<span>Code</span>
        </div>


        <div class="admin-info">

            <div class="avatar">
                A
            </div>

            <div>
                <strong>
                    {{ authStore.user?.name }}
                </strong>

                <small>
                    Admin
                </small>
            </div>

        </div>



        <nav>


            <RouterLink to="/admin">

                داشبورد

            </RouterLink>


            <RouterLink to="/admin/projects">

                پروژه‌ها

            </RouterLink>


            <RouterLink to="/admin/requests">

                درخواست‌ها

            </RouterLink>


            <RouterLink to="/admin/users">

                کاربران

            </RouterLink>


        </nav>



        <button @click="authStore.logout()">

            خروج

        </button>


    </aside>



    <main class="content">

        <slot />

    </main>



</div>

</template>



<style scoped>

.admin-layout {

    min-height:100vh;

    display:flex;

    direction:rtl;

    background:#050505;

    color:white;

}



.sidebar {

    width:270px;

    padding:30px 20px;

    background:
    linear-gradient(
        180deg,
        #111,
        #050505
    );


    border-left:

    1px solid rgba(255,114,0,.25);


}



.logo {

    font-size:30px;

    font-weight:900;

    margin-bottom:35px;

}



.logo span {

    color:#ff7200;

}



.admin-info {

    display:flex;

    align-items:center;

    gap:12px;

    padding:15px;

    background:#151515;

    border-radius:15px;

    margin-bottom:30px;

}



.admin-info small {

    display:block;

    color:#999;

}



.avatar {

    width:42px;

    height:42px;

    display:flex;

    align-items:center;

    justify-content:center;

    border-radius:50%;

    background:#ff7200;

    font-weight:bold;

}



nav {

    display:flex;

    flex-direction:column;

    gap:12px;

}



nav a {

    text-decoration:none;

    color:#aaa;

    padding:14px;

    border-radius:12px;

    transition:.3s;

}



nav a:hover {

    color:white;

    background:#1a1a1a;

}



nav a.router-link-active {

    background:

    linear-gradient(
        90deg,
        #ff7200,
        #ff4500
    );


    color:white;

}



button {

    margin-top:40px;

    width:100%;

    padding:13px;


    background:#111;

    color:white;


    border:

    1px solid #ff7200;


    border-radius:12px;

    cursor:pointer;

}



.content {

    flex:1;

    padding:40px;

}


</style>
