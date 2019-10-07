<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" v-text="title"></div>
                    <div class="card-body">
                       <a v-text="link.text" :title="link.title" :href="link.href"></a>
                    </div>
                </div>
            </div>
        </div>
        <directiva-html></directiva-html>
    </div>
</template>

<script>
    import directiva from './directiva.vue';
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data(){
            return {
                title: 'Hola mundo',
                link: {
                    text: 'VueJS Home',
                    href: 'https://vuejs.org/v2/guide/',
                    title: 'Documentacion Vue JS'
                }
            }
        },
        components: {
            'directiva-html': directiva,
        }
    }
</script>
