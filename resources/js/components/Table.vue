<template>
    <div>
        <table class="table table-hover" v-if="dados.length > 0">
            <thead>
                <tr>
                    <th scope="col" v-for="coluna, key in titulos" :key="key">{{ coluna.titulo }}</th>
                    <td colspan="2" class="text-center w-auto"><strong>Ações</strong></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="obj, chave in dadosFiltrados" :key="chave">
                    <template v-for="valor, chaveValor in obj" :key="chaveValor">
                        <td>
                            <span v-if="titulos[chaveValor].tipo == 'texto'">
                                {{ valor }}
                            </span>
                            <span v-if="titulos[chaveValor].tipo == 'status'">
                                <!-- {{ valor.name }} -->
                                <status-button-component :status="valor.name"></status-button-component>
                            </span>
                            <span v-if="titulos[chaveValor].tipo == 'array'">
                                {{ valor.map(item => item.name).join(', ') }}
                            </span>
                            <span v-if="titulos[chaveValor].tipo == 'obj'">
                                {{ valor.name }}
                            </span>
                            <span v-if="titulos[chaveValor].tipo == 'data'">
                                {{ valor }}
                            </span>
                        </td>
                    </template>
                    <td >
                        <button  class="btn btn-outline-primary btn-sm me-1"
                            data-bs-toggle="modal" :data-bs-target="visualizar.dataBsTarget"
                            @click="setStore(obj)"><i class="fa-solid fa-eye"></i></button>

                        <button  class="btn btn-outline-warning btn-sm me-1"
                            data-bs-toggle="modal" :data-bs-target="atualizar.dataBsTarget"
                            @click="setStore(obj)"><i class="fa-solid fa-pen-to-square"></i></button>

                        <button v-if="remover.userLogged.id == obj.user.id"  class="btn btn-outline-danger btn-sm"
                            data-bs-toggle="modal" :data-bs-target="remover.dataBsTarget"
                            @click="setStore(obj)"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-else class="alert alert-info" role="alert">
            <i class="fa-solid fa-bell"></i> Você não tem tasks atribuídas...
        </div>

    </div>

</template>
<script>
export default {
    methods: {
        setStore(obj) {
            this.$store.state.request.status = ''
            this.$store.state.request.detalhes = ''

            this.$store.state.item = { ...obj };

            this.$store.state.user = obj.user.name.toString();
            this.$store.state.status = obj.status;
            this.$store.state.assignedUsersNames = obj.assigned_users.map(user => user.name).join(', '); // string 'israel,maria'
            this.$store.state.assignedUsersIds = obj.assigned_users.map(user => user.id);
        }
    },
    props: ['dados','titulos', 'visualizar', 'atualizar', 'remover'],
    computed: {
        dadosFiltrados() {
            let campos = Object.keys(this.titulos)
            let dadosFiltrados = []

            this.dados.map((item, chave) => {

                let itemFiltrado = {}
                campos.forEach(campo => {
                    itemFiltrado[campo] = item[campo]

                })
                dadosFiltrados.push(itemFiltrado)
            })
            return dadosFiltrados
        }
    },
}
</script>
