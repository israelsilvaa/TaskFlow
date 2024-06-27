<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Inicio do card de busca -->
                <card-component titulo="Busca de Tasks">

                    <template v-slot:conteudo>
                        <div class="row">
                            <div class="col mb-3">
                                <input-container-component titulo="ID" id="inputId" id-help="idHelp"
                                    texto-ajuda="Opcional. Informe o ID da task">
                                    <input type="number" class="form-control" id="inputId" placeholder="55"
                                        aria-describedby="idHelp">
                                </input-container-component>
                            </div>
                            <div class="col mb-3">
                                <input-container-component titulo="Titulo" id="inputTask" id-help="tituloHelp"
                                    texto-ajuda="Informe o Titulo da task">
                                    <input type="text" class="form-control" id="inputTask" placeholder="Backup dataBase"
                                        aria-describedby="tituloHelp">
                                </input-container-component>
                            </div>
                        </div>
                    </template>
                    <template v-slot:rodape>
                        <div class="">
                            <button type="submit" class="btn btn-primary btn-sm">Buscar</button>
                        </div>
                    </template>

                </card-component>
                <!-- Fim do card de busca -->

                <!-- Inicio do card de listagem de tasks -->
                <card-component titulo="Relação de Tasks">
                    <template v-slot:conteudo>
                        <table-component :dados="tasks.data"
                            :titulos="['id', 'title', 'description', 'status', 'created_at']"></table-component>
                    </template>
                    <template v-slot:rodape>
                        <paginate-component>
                            <li v-for="link, key in tasks.links" :key="key" class="page-item" @click="paginacao(link)">
                                <a :class="link.active ? 'page-link active':'page-link'" v-html="link.label"></a>
                            </li>
                        </paginate-component>
                        <button type="button" class="btn btn-primary btn-sm m-3" data-bs-toggle="modal"
                            data-bs-target="#modalTask">Adicionar</button>
                    </template>
                </card-component>
                <!-- Fim do card de listagem de tasks -->
            </div>
        </div>

        <!-- Inicio Modal de criação de tasks -->
        <modal-component id="modalTask" titulo="Adicionar task">
            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso"
                    v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca"
                    v-if="transacaoStatus == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Titulo" id="novaTask" id-help="novaTaskHelp"
                        texto-ajuda="Informe o Titulo da task">
                        <input type="text" class="form-control" id="novaTask" placeholder="Backup dataBase"
                            v-model="titulo" aria-describedby="novaTaskHelp">
                    </input-container-component>
                </div>

                <div class="form-group">
                    <input-container-component titulo="Descrição" id="novaDescricao" id-help="novaDescricaoHelp"
                        texto-ajuda="Descrição da trask">
                        <input type="text" class="form-control" id="novaDescricao"
                            placeholder="baixar base da produção e salvar no driver" v-model="descricao"
                            aria-describedby="novaDescricaoHelp">
                    </input-container-component>
                </div>
            </template>

            <template v-slot:rodape>
                <button-component>
                    <button type="button" class="btn btn-secondary m-1" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
                </button-component>
            </template>
        </modal-component>
        <!-- Fim Modal de criação de tasks -->
    </div>
</template>

<script>
import axios from 'axios';

export default {
    computed: {
        token() {
            let token = document.cookie.split(';').find(indice => {
                return indice.includes('token=')
            })
            token = token.split('=')[1]
            token = 'Bearer ' + token
            return token
        }
    },
    data() {
        return {
            urlBase: 'http://localhost:8000/api/v1/task',
            tasks: { data: [] }, // definido como vazio, aguardar a requisição de tasks terminar_carregarListaTasks()
            titulo: '',
            descricao: '',
            transacaoStatus: '',
            transacaoDetalhes: {},
        }
    },
    methods: {
        paginacao(link){
            if(link.url){
                this.urlBase = link.url // ajustar parametro de consulta com parametro da pagina
                this.carregarListaTasks() // requisita dados para API, da pagina desejada 
            }
        },
        carregarListaTasks() {
            let config = {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }
            axios.get(this.urlBase, config)
                .then(response => {
                    this.tasks = response.data
                    console.log(response.data.data);
                })
                .catch(errors => {
                    // console.log(errors);
                });

        },
        salvar() {
            // console.log(this.titulo, this.descricao)
            let formData = new FormData();
            formData.append('title', this.titulo)
            formData.append('description', this.descricao)
            let config = {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }

            // recupera a resposta/erros de forma assincrona
            axios.post(this.urlBase, formData, config)
                .then(response => {
                    this.transacaoStatus = 'adicionado'
                    this.transacaoDetalhes = {
                        mensagem: 'ID do registro: ' + response.data.success.detail.id
                    }
                    // console.log(response);
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    if (errors.response && errors.response.data) {
                        if (errors.response.data.error) {
                            // Caso 1: Formato com 'error'
                            this.transacaoDetalhes = {
                                mensagem: errors.response.data.error.title,
                                dados: errors.response.data.error.detail
                            };
                        } else if (errors.response.data.message) {
                            // Caso 2: Formato com 'message' 
                            this.transacaoDetalhes = {
                                mensagem: errors.response.data.message,
                                // dados: errors.response.data.errors
                            };
                        }
                    }

                });
        }
    },
    mounted() {
        this.carregarListaTasks()
    }

}
</script>
