<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col" v-for="coluna, key in titulos" :key="key" class="text-uppercase">{{ coluna }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="obj in dados" :key="obj.id">
                    <template v-for="(valor, chave) in obj" :key="chave">
                        <td v-if="titulos.includes(chave)" :key="chave">{{ valor }}</td>
                    </template>
                </tr>
            </tbody>
        </table>
    </div>

</template>

<script>
export default {
    props: ['dados', 'titulos']
}
</script>
