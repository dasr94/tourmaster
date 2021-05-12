
<form action="https://theoutdoortrip.com/?tourmaster-payment" method="POST" id="data">
<!-- <form action="http://localhost/bookdev/?tourmaster-payment" method="POST" id="data"> -->
    <!-- <input type="hidden" name="tour-id" value="30734">x     -->
    <input type="hidden" name="tour-id" value="<?php echo $_GET['tid'] ?>">
    <!-- <input type="hidden" name="tour-date" value="2021-01-05"> -->
    <input type="hidden" name="tour-valid" value="0">
    <!-- <input type="submit"> -->
</form>
<script>
const data = new FormData(document.getElementById("data"));

function redireccion(id){
    document.getElementById(id).submit();
}

function enviarData(){
    fetch("http://localhost/bookdev/?tourmaster-payment", {
        method: 'POST',
        body: data
    })
    .then(function(response){
        if(response.ok){
            return response.text()
        } else {
            throw "error en la llamada ajax";
        }
    })
    .then(function(texto){
        console.log(texto);
    })
    .catch(function(err) {
        console.log(err);
    });
}
// window.onload = enviarData();
window.onload = document.getElementById("data").submit();
console.log(window.location.hostname + "/bookdev/?tourmaster-payment");
</script>
<?php


?>