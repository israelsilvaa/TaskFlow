<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Entrar</div>
                    <div class="card-body">
                        <form method="POST" action="" @submit.prevent="login($event)">
                            <input type="hidden" name="_token" :value="csrf_token">
                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" :class="'form-control ' + error_classe" name="email"
                                        required autocomplete="email" autofocus v-model="email">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">Senha</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" :class="'form-control ' + error_classe"
                                        name="password" required autocomplete="current-password" v-model="password">

                                    <span v-if="error_login" class="invalid-feedback" role="alert">
                                        <strong>{{ error_login }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Entrar
                                    </button>

                                    <!-- <a class="btn btn-link" href="">
                                        Esqueci a senha
                                    </a> -->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['csrf_token'],
    data() {
        return {
            email: '',
            password: '',
            error_email: '',
            error_password: '',
            error_classe: '',
            error_login: '',
        }
    },
    methods: {
        login(e) {
            let url = 'http://localhost:8000/api/login'
            let configuracao = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                },
                body: new URLSearchParams({
                    'email': this.email,
                    'password': this.password
                })
            }
            fetch(url, configuracao)
                .then(response => {
                    // Verifique se a resposta é realmente JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new TypeError("A resposta da API não é JSON");
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.cookie = 'token=' + data.success.detail.Token + ';SameSite=Lax'
                        e.target.submit()
                    } else {
                        // Lidar com erros de login aqui
                        console.error('Erro ao logar:', data);
                        this.email = ''
                        this.password = ''
                        this.error_classe = 'is-invalid'
                        this.error_login = data.error.detail
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição:', error);
                    this.error_login = 'Erro na requisição: ' + error.message;
                });
        },
    }
}
</script>
