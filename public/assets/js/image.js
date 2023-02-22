let links = document.querySelectorAll('[data-delate]');

for (let link of links) {
         
        link.addEventListener('click', function(e){
            e.preventDefault();
         
            if(confirm('voulez-vous supprimer cette l\image ?')){
               // on envoie la requette ajax
               fetch(this.getAttribute('href'),{
                method:'DELETE',
                headers:{
                    "X-Requested-width":"XMLHttpTRequest",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({"token":this.dataset.token}),

               }).then(response => (response.json()))
               .then(data =>{
                if(data.success){
                    this.parentElement.remove();
                }else{
                    alert(data.error)
                }
               })
            //    .catch(error => alert("Erreur : " + error));

            }
              
        })
        
        // alert ('voulez-vous supprimer cette image?')
   
}