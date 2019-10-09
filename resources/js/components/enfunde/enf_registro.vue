<template>

</template>

<script>
    export default{
        mounted(){
            console.log("Montado")
            var options = {
                data: ["blue", "green", "pink", "red", "yellow"]
            };

            $("#nombre-empleado").easyAutocomplete(options);
        }
    }
</script>