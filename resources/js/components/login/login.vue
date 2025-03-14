<template>
    <div class="h-screen w-screen flex">
        <Toast />

        <!-- Bagian Kiri: Gambar -->
        <div
            class="hidden md:flex items-center justify-center w-1/2 bg-blue-400"
        >
            <Image
                src="/image/logo_itats.webp"
                alt="Login Image"
                class="w-50"
            />
            <span class="text-2xl text-center font-bold">LPMI - Lembaga Penjaminan Mutu Internal</span>
        </div>

        <!-- Bagian Kanan: Form Login -->
        <div
            class="w-full md:w-1/2 flex items-center justify-center p-10 bg-gray-100"
        >
            <div class="w-full max-w-md">
                <h2 class="text-4xl font-bold text-gray-700 text-center">
                    Login
                </h2>
                <p class="text-center text-gray-500 text-sm mb-8">
                    Masukkan username dan password Anda
                </p>

                <Form
                    v-slot="$form"
                    :initialValues="initialValues"
                    :resolver="resolver"
                    @submit="login"
                    class="flex flex-col gap-5"
                >
                    <div class="flex flex-col gap-1">
                        <label class="text-gray-600 font-semibold"
                            >Username</label
                        >
                        <InputText
                            name="username"
                            type="text"
                            placeholder="Masukkan username"
                            class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <Message
                            v-if="$form.username?.invalid"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ $form.username.error.message }}
                        </Message>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-gray-600 font-semibold"
                            >Password</label
                        >
                        <Password
                            name="password"
                            placeholder="Masukkan password"
                            :feedback="false"
                            toggleMask
                            fluid
                        />
                        <Message
                            v-if="$form.password?.invalid"
                            severity="error"
                            size="small"
                            variant="simple"
                        >
                            {{ $form.password.error.message }}
                        </Message>
                    </div>

                    <Button
                        type="submit"
                        label="Masuk"
                        size="small"
                        class=" bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition-all duration-300"
                    />
                </Form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { z } from "zod";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import { Form } from "@primevue/forms";
import Button from "primevue/button";
import Message from "primevue/message";

const router = useRouter();
const loading = ref(false);
const toast = useToast();

const initialValues = ref({
    username: "",
    password: "",
});

const login = async (e) => {
    if (e.valid) {
        try {
            loading.value = true;
            const response = await axios.post("/api/login", {
                email: e.values.username,
                password: e.values.password,
            });
            loading.value = false;
            localStorage.setItem("name", response.data.name);
            localStorage.setItem("token", response.data.token);
            localStorage.setItem("userRole", response.data.userRole);

            axios.defaults.headers.common[
                "Authorization"
            ] = `Bearer ${response.data.token}`;
            await router.push("/");
        } catch (error) {
            toast.add({
                severity: "error",
                summary: "Email atau Password Salah.",
                life: 3000,
            });
            loading.value = false;
        }
    }
};

const resolver = zodResolver(
    z.object({
        username: z.string().min(1, { message: "Username harus diisi." }),
        password: z.string().min(1, { message: "Password harus diisi." }),
    })
);
</script>
