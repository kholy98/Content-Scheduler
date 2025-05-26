<template>
   <div class="flex items-center justify-between p-4">
        <h1 class="text-3xl">Dashboard</h1>
        <button
        @click="logout"
        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition"
        >
        Logout
        </button>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-lg"> Welcome back,  {{ user?.name }}</p>
            <p class="text-sm"> {{ user?.email }}</p>
        </div>
    </div>
</template>

<script setup lang="ts">
    import axiosInstance from "@/lib/axios";
    import { ref } from "vue";
    import router from "@/router";

    const user = ref({
        name: "",
        email: "",
    });

    
    const fetchUserData = async () => {
        try {
            const response = await axiosInstance.get('/user');
            user.value = response.data;
        } catch (error) {
            console.error('Error fetching user data:', error);
        }
    };

    const logout = async () => {
        try {
            await axiosInstance.post('/logout');
            localStorage.removeItem('access_token');
            user.value = {
                name: "",
                email: "",
            };

            router.push('/login');
        } catch (error) {
            console.error('Logout failed:', error);
        }
    };

    fetchUserData();

</script>

<style lang="scss" scoped>


</style>