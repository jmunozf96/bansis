<template>
    <div><h1 v-text="title"></h1><p v-html="message"></p></div>
</template>
<script>
    export default{
        data(){
            return {
                title: 'Hola mundo 2',
                message: '<strong>Directiva</strong>'
            }
        }
    }
</script>